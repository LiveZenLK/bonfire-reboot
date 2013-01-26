<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class BF_Controller extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
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
		if (!class_exists('Template'))
		{
			//$this->load->library('Template');
		}

		// Make sure to pass along any data if we have it.
		if (!is_null($data))
		{
			Template::set($data);
		}
		Template::render($layout, $return_output);
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
