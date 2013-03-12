<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class BF_Controller extends CI_Controller {

	/**
	 * The type of caching to use. By default it's 'dummy',
	 * which doesn't actually do anything. :)
	 */
	protected $cache_type = 'dummy';
	protected $backup_cache = 'file';

	// What theme should we use? If blank, will use the 'default' theme
	protected $theme = NULL;

	// If set, this language file will automatically be loaded.
	protected $language_file = NULL;

	// All supported output formats.
	// Used by the render_as() method.
	private $supported_formats = array(
		'json'		=> 'application/json',
		'xml'		=> 'application/xml',
		'extjson'	=> 'application/json',
		'jsonp'		=> 'application/javascript',
		'serialized'	=> 'application/vnd.php.serialized',
		'php'		=> 'text/plain',
		'html'		=> 'text/html',
		'csv'		=> 'application/csv'
	);

	//--------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		// Make sure that caching is ALWAYS available throughout the app
		// though it defaults to 'dummy' which won't actually cache.
		$this->load->driver('cache', array('adapter' => $this->cache_type, 'backup' => $this->backup_cache));

		if (!is_null($this->language_file))
		{
			$this->lang->load($this->language_file);
		}
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Rendering functions
	//--------------------------------------------------------------------

	/**
	 * Renders the view through a template layout file using the Template library.
	 *
	 * @param  [type]  $layout      [description]
	 * @param  boolean $return_data [description]
	 * @return [type]               [description]
	 */
	protected function render($layout=null, $data=null, $return_output=false)
	{
		// Make sure to pass along any data if we have it.
		if (!is_null($data))
		{
			Template::set($data);
		}

		// Are we specifying a theme other than the default?
		if (!empty($this->theme))
		{
			Template::set_theme($this->theme);
		}

		// Render it!
		$this->output->set_output( Template::render($layout, $return_output) );
	}

	//--------------------------------------------------------------------

	/**
	 * Renders the contents of a specific file. This is intended for rendering
	 * view files in other locations, like if you're sharing views between
	 * several Bonfire applications.
	 *
	 * @param  string $filename The full system path to the file to render.
	 * @param  boolean $use_layout 	If TRUE, will wrap the file contents in
	 *                              the current layout file as the $tpl_content variable.
	 *                              If FALSE, will render the file as is.
	 * @return [type]           [description]
	 */
	public function render_file($filename, $use_layout=false)
	{
		if (!class_exists('Template'))
		{
			$this->load->library('Template');
		}

		// TODO Finish it!
	}

	//--------------------------------------------------------------------

	/**
	 * Renders a string of aribritrary text. This is best used during an AJAX
	 * call or web service request that are expecting something other then
	 * proper HTML.
	 *
	 * @param  string $text The text to render.
	 * @param  bool $typography If TRUE, will run the text through 'Auto_typography'
	 *                          before outputting to the browser.
	 * @return [type]       [description]
	 */
	public function render_text($text, $typography=false)
	{
		// Note that, for now anyway, we don't do any cleaning of the text
		// and leave that up to the client to take care of.

		// However, we can auto_typogrify the text if we're asked nicely.
		if ($typography === true)
		{
			$this->load->helper('typography');
			$text = auto_typography($text);
		}

		$this->output->set_content_type('text/plain')
					 ->set_output($text);
	}

	//--------------------------------------------------------------------

	/**
	 * Converts the provided array or object to JSON, sets the proper MIME type,
	 * and outputs the data.
	 *
	 * Do NOT do any further actions after calling this action.
	 *
	 * @param  mixed $json 	The data to be converted to JSON.
	 * @return [type]       [description]
	 */
	public function render_json($json)
	{
		if (is_resource($json))
		{
			throw new RenderException('Resources can not be converted to JSON data.');
		}

		$this->output->set_content_type('application/json')
					 ->set_output(json_encode($json));
	}

	//--------------------------------------------------------------------

	/**
	 * Render data out as a specific type of data, like json, extjson, xml, etc.
	 * Built for AJAX power.
	 *
	 * @param  strign $format    The output format to use
	 * @param  array  $data      The data to output
	 * @param  int    $http_code The HTTP code to send along with it
	 */
	public function render_as($format, $data=array(), $http_code=NULL)
	{
		$output = '';

		// If the data is empty and no code provided, error and bail!
		if (empty($data) && is_null($http_code))
		{
			$http_code = 404;
		}

		// Otherwise (if no data but 200 provided...) or some data, carry on!
		else
		{
			// Default to a 200 (OK) HTTP code
			is_numeric($http_code) OR $http_code = 200;

			// Load the BF_Format library (thanks Phil!)
			$this->load->library('BF_Format', null, 'format');

			// Make sure we have a valid formatter
			// If it does, return the formatted output
			if (method_exists($this->format, 'to_'. $format))
			{
				$callback = $this->input->get('callback', TRUE);

				// HTML
				if ($format == 'html')
				{
					$output = $this->format->factory( $data['data'] )->to_html();
				}
				// JSONP
				else if (!empty($callback) && substr($format, 0, 4) == 'json')
				{
					$output = $this->format->factory( $data, NULL, $callback )->to_jsonp();
				}
				// All other formats
				else
				{
					$output = $this->format->factory( $data )->{'to_'. $format}();
				}
			}
			// format not supported? Dump it directly
			else
			{
				$output = @$data['data'];
			}
		}

		// Make sure the AJAX call isn't cached and set our content type/length
		$this->output->set_header('HTTP/1.1: '. $http_code);
		$this->output->set_header('Status: '. $http_code);
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('cache-Control: post-check=0, pre-check=0');
		$this->output->set_header('Pragma: non-cache');
		$this->output->set_header('Content-Type: '. $this->supported_formats[$output_format]);
		$this->output->set_header('Content-Length: '. @strlen($output));

		$this->output->set_output($output);
	}

	//--------------------------------------------------------------------


	/**
	 * Sends the supplied string to the browser with a MIME type of text/javascript.
	 *
	 * Do NOT do any further processing after this command or you may receive a
	 * Headers already sent error.
	 *
	 * @param  mixed $js 	The javascript to output.
	 * @return [type]       [description]
	 */
	public function render_js($js=null)
	{
		if (!is_string($js))
		{
			throw new RenderException('No javascript passed to the render_js() method.');
		}

		$this->output->set_content_type('application/x-javascript')
					 ->set_output($js);
	}

	//--------------------------------------------------------------------

	/**
	 * Breaks us out of any output buffering so that any content echo'd out
	 * will echo out as it happens, instead of waiting for the end of all
	 * content to echo out. This is especially handy for long running
	 * scripts like might be involved in cron scripts.
	 *
	 * @return void
	 */
	public function render_realtime()
	{
		if (ob_get_level() > 0)
		{
			end_end_flush();
		}
		ob_implicit_flush(true);
	}

	//--------------------------------------------------------------------


	//--------------------------------------------------------------------
	// Parameter Helper
	//--------------------------------------------------------------------

	/**
	 * Attempts to get any information from php://input and return it
	 * as JSON data. This is useful when your javascript is sending JSON data
	 * to the application.
	 *
	 * @param  strign $format 	The type of element to return, either 'object' or 'array'
	 * @param  int   $depth 	The number of levels deep to decode
	 *
	 * @return mixed 	The formatted JSON data, or NULL.
	 */
	public function get_json($format='object', $depth=512)
	{
		$as_array	= $format == 'array' ? true : false;

		return json_decode( file_get_contents('php://input'), $as_array, $depth);
	}

	//--------------------------------------------------------------------

}
