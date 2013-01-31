<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bonfire
 *
 * An open source project to allow developers get a jumpstart their development of CodeIgniter applications
 *
 * @package   Bonfire
 * @author    Bonfire Dev Team
 * @copyright Copyright (c) 2011 - 2012, Bonfire Dev Team
 * @license   http://guides.cibonfire.com/license.html
 * @link      http://cibonfire.com
 * @since     Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Template
 *
 * The Template class makes the creation of consistently themed web pages across your
 * entire site simple and as automatic as possible.
 *
 * It supports controller-named automatic overrides, and more.
 *
 * @package    Bonfire
 * @subpackage Libraries
 * @category   Libraries
 * @author     Bonfire Dev Team
 * @version    3.0
 * @link       http://cibonfire.com/docs/guides/views.html
 *
 */
class Template
{

	/**
	 * Set the debug mode on the template to output messages
	 *
	 * @access public
	 * @static
	 *
	 * @var bool
	 */
	public static $debug = false;


	/**
	 * Stores the name of the active theme (folder) with a trailing slash.
	 *
	 * @access protected
	 * @static
	 *
	 * @var string
	 */
	protected static $active_theme = '';


	/**
	 * Stores the default theme from the config file for a slight performance increase.
	 *
	 * @access protected
	 * @static
	 *
	 * @var string
	 */
	protected static $default_theme = '';


	/**
	 * The view to load. Normally not set unless you need to bypass the automagic.
	 *
	 * @access protected
	 * @static
	 *
	 * @var string
	 */
	protected static $current_view;


	/**
	 * The layout to render the views into.
	 *
	 * @access public
	 * @static
	 *
	 * @var string
	 */
	public static $layout;


	/**
	 * If TRUE, CodeIgniter's Template Parser will be used to
	 * parse the view. If FALSE, the view is displayed with
	 * no parsing. Used by the yield() and block()
	 *
	 * @access public
	 * @static
	 *
	 * @var bool
	 */
	public static $parse_views = FALSE;


	/**
	 * The data to be passed into the views. The keys are the names of the variables
	 * and the values are the values.
	 *
	 * @access protected
	 * @static
	 *
	 * @var array
	 */
	protected static $data = array();


	/**
	 * An array of blocks. The key is the name to reference it by, and the value is the file.
	 * The class will loop through these, parse them, and push them into the layout.
	 *
	 * @access public
	 * @static
	 *
	 * @var array
	 */
	public static $blocks;


	/**
	 * Holds a simple array to store the status Message
	 * that gets displayed using the message() function.
	 *
	 * @access protected
	 * @static
	 *
	 * @var string
	 */
	protected static $messages = array();

	/**
	 * The openging HTML tag that each item within a message
	 * is opened with.
	 *
	 * @access protected
	 * @static
	 *
	 * @var string
	 */
	protected static $message_tag_start = '<br/>';

	/**
	 * The openging HTML tag that each item within a message
	 * is opened with.
	 *
	 * @access protected
	 * @static
	 *
	 * @var string
	 */
	protected static $message_tag_end 	= '';

	/**
	 * An array of paths to look for themes.
	 *
	 * @access protected
	 * @static
	 *
	 * @var array
	 */
	protected static $theme_paths = array();

	/**
	 * Stores CI's default view path.
	 *
	 * @access protected
	 * @static
	 *
	 * @var string
	 */
	protected static $orig_view_path;

	/**
	 * Stores the data for the various yields.
	 *
	 * @access  protected
	 * @static
	 *
	 * @var object
	 */
	protected static $yields;

	/**
	 * An instance of the CI super object.
	 *
	 * @access private
	 * @static
	 *
	 * @var object
	 */
	private static $ci;

	//--------------------------------------------------------------------

	/**
	 * This constructor is here purely for CI's benefit, as this is a static class.
	 *
	 * @return void
	 */
	public function __construct()
	{
		self::$ci =& get_instance();

		self::init();

	}//end __construct()

	//--------------------------------------------------------------------

	/**
	 * Grabs an instance of the CI superobject, loads the Ocular config
	 * file, and sets our default layout.
	 *
	 * @access public
	 * @static
	 *
	 * @return void
	 */
	public static function init()
	{
		// If the application config file hasn't been loaded, do it now
		if (!self::$ci->config->item('template.theme_paths'))
		{
			self::$ci->config->load('application');
		}

		// Store our settings
		self::$theme_paths 		= self::$ci->config->item('template.theme_paths');
		self::$layout 			= self::$ci->config->item('template.default_layout');
		self::$default_theme 	= self::$ci->config->item('template.default_theme');
		self::$active_theme		= self::$default_theme;
		self::$parse_views		= self::$ci->config->item('template.parse_views');

		self::$yields = new stdClass();
		self::$blocks = new stdClass();

		// Store our orig view path, so we can reset it
		//self::$orig_view_path = self::$ci->load->_ci_view_path;

		log_message('debug', 'Template library loaded');

	}//end init()

	//--------------------------------------------------------------------


	/**
	 * Renders out the specified layout, which starts the process
	 * of rendering the page content. Also determines the correct
	 * view to use based on the current controller/method.
	 *
	 * @access public
	 * @static
	 *
	 * @global object $OUT Core CodeIgniter Output object
	 * @param  string $layout The name of the a layout to use. This overrides any current or default layouts set.
	 *
	 * @return void
	 */
	public static function render($layout=NULL)
	{
		$output = '';
		$controller = self::$ci->router->class;

		// We need to know which layout to render
		$layout = empty($layout) ? self::$layout : $layout;

		// Grab our current view name, based on controller/method
		// which routes to views/controller/method.

		if (empty(self::$current_view))
		{
			self::$current_view =  self::$ci->router->class . '/' . self::$ci->router->method;
		}

		// We load our view first so that we can support multiple yields and
		// perform some sort of template inheritance voodoo here.
		if (self::$debug) { echo 'Current View = '. self::$current_view; }

		self::$yields->tpl_content = new stdClass();
		self::load_view(self::$current_view, NULL, self::$ci->router->class .'/'. self::$ci->router->method, FALSE, self::$yields->tpl_content);

		//
		// Time to render the layout
		//
		$output = '';
		self::load_view($layout, self::$data, $controller, TRUE, $output);

		if (empty($output)) { show_error('Unable to find theme layout: '. $layout); }

		//Events::trigger('after_layout_render', $output);

		global $OUT;
		$OUT->set_output($output);

		unset($output);
		self::$yields = new stdClass();
	}//end render()

	//--------------------------------------------------------------------

	/**
	 * Renders the current page into the layout.
	 *
	 * Uses a view based on the controller/function being run. (See __constructor).
	 *
	 * @access public
	 * @static
	 *
	 * @return string A string containing the output of the render process.
	 */
	public static function yield($name=null)
	{
		if (empty($name))
		{
			$name = 'tpl_content';
		}

		// Grab the data to return, if exists...
		if (isset(self::$yields->$name))
		{
			return self::$yields->$name;
		}

		return '';

	}//end yield()

	//--------------------------------------------------------------------

	/**
	 * Saves the content between the tag and the end_content_for() so that
	 * it can be inserted into the layouts via a yield($name) method.
	 *
	 * @param  string $name The name to reference this content by.
	 * @return voic
	 */
	public static function content_for($name)
	{
		// Make sure it's not a reserved name!
		if ($name == 'tpl_content')
		{
			show_error('content_for method cannot use "tpl_content" as its name.');
		}

		// Create a placeholder in the yields array
		self::$yields->$name = '';

		ob_start();
	}

	//--------------------------------------------------------------------

	/**
	 * Ends the content_for block and stores the information for the layout.
	 *
	 * @param  string $name The name to match with the opening content_for tag.
	 * @return void
	 */
	public static function end_content_for($name)
	{
		// We can only save if we've started it before.
		// Not 100% why I'm choosing to do it this way,
		// but I guess it's providing another level of security checks?
		if (isset(self::$yields->$name))
		{
			// Grab the output so that we don't screw up our buffering levels.
			$buffer = ob_get_contents();
			ob_end_clean();

			self::$yields->$name = $buffer;
		}


	}

	//--------------------------------------------------------------------


	//--------------------------------------------------------------------
	// !BLOCKS
	//--------------------------------------------------------------------

	/**
	 * Stores the block named $name in the blocks array for later rendering.
	 * The $current_view variable is the name of an existing view. If it is empty,
	 * your script should still function as normal.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $block_name The name of the block. Must match the name in the block() method.
	 * @param string $view_name  The name of the view file to render.
	 *
	 * @return void
	 */
	public static function set_block($block_name='', $view_name='')
	{
		if (!empty($block_name))
		{
			self::$blocks->$block_name = $view_name;
		}

	}//end set_block()

	//--------------------------------------------------------------------

	/**
	 * Renders a "block" to the view.
	 *
	 * A block is a partial view contained in a view file in the
	 * application/views folder. It can be used for sidebars,
	 * headers, footers, or any other recurring element within
	 * a site. It is recommended to set a default when calling
	 * this function within a layout. The default will be rendered
	 * if no methods override the view (using the set_block() method).
	 *
	 * @access public
	 * @static
	 *
	 * @param string $block_name   The name of the block to render.
	 * @param string $default_view The view to render if no other view has been set with the set_block() method.
	 * @param array  $data         An array of data to pass to the view.
	 * @param bool   $themed       Whether we should look in the themes or standard view locations.
	 *
	 * @return void
	 */
	public static function block($block_name='', $default_view='', $data=array(), $themed=FALSE)
	{
		if (empty($block_name))
		{
			logit('[Template] No block name provided.');
			return;
		}

		if (empty($block_name) && empty($default_view))
		{
			logit('[Template] No default block provided for `' . $block_name . '`');
			return;
		}

		// If a block has been set previously use that
		if (isset(self::$blocks->$block_name))
		{
			$block_name = self::$blocks->$block_name;
		}
		// Otherwise, use the default view.
		else
		{
			$block_name = $default_view;
		}

		if (self::$debug) { echo "Looking for block: <b>{$block_name}</b>."; }

		self::load_view($block_name, $data, FALSE, $themed, $output);

		$block_data = array('block'=>$block_name, 'output'=>$output);
		Events::trigger('after_block_render', $block_data );

		echo $output;

	}//end block()

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// !THEME PATHS
	//--------------------------------------------------------------------

	/**
	 * Theme paths allow you to have multiple locations for themes to be
	 * stored. This might be used for separating themes for different sub-
	 * applications, or a core theme and user-submitted themes.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $path A new path where themes can be found.
	 *
	 * @return bool
	 */
	public static function add_theme_path($path=NULL)
	{
		if (empty($path) || !is_string($path))
		{
			return FALSE;
		}

		// Make sure the path has a '/' at the end.
		if (substr($path, -1) != '/')
		{
			$path .= '/';
		}

		// If the path already exists, we're done here.
		if (isset(self::$theme_paths[$path]))
		{
			return TRUE;
		}

		// Make sure the folder actually exists
		if (is_dir(FCPATH . $path))
		{
			array_push(self::$theme_paths, $path);
			return FALSE;
		}
		else
		{
			logit("[Template] Cannot add theme folder: $path does not exist");
			return FALSE;
		}

	}//end add_theme_path()

	//--------------------------------------------------------------------

	/**
	 * Remove the theme path
	 *
	 * @access public
	 * @static
	 *
	 * @param string $path The path to remove from the theme paths.
	 *
	 * @return void
	 */
	public static function remove_theme_path($path=NULL)
	{
		if (empty($path) || !is_string($path))
		{
			return;
		}

		if (isset(self::$theme_paths[$path]))
		{
			unset(self::$theme_paths[$path]);
		}

	}//end remove_theme_path()

	//--------------------------------------------------------------------

	/**
	 * Stores the name of the active theme to use. This theme should be
	 * relative to one of the 'template.theme_paths' folders.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $theme         The name of the active theme.
	 *
	 * @return void
	 */
	public static function set_theme($theme=NULL)
	{
		if (empty($theme) || !is_string($theme))
		{
			return;
		}

		// Make sure a trailing slash is there
		if (substr($theme, -1) !== '/')
		{
			$theme .= '/';
		}

		self::$active_theme = $theme;
	}//end set_theme()

	//--------------------------------------------------------------------

	/**
	 * Returns the active theme.
	 *
	 * @access public
	 * @static
	 *
	 * @return string The name of the active theme.
	 */
	public static function theme()
	{
		return ( ! empty(self::$active_theme)) ? self::$active_theme : self::$default_theme;
	}//end theme()

	//--------------------------------------------------------------------

	/**
	 * Returns the full url to a file in the currently active theme.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $resource Path to a resource in the theme
	 *
	 * @return string The full url (including http://) to the resource.
	 */
	public static function theme_url($resource='')
	{
		$url = base_url();

		// Add theme path
		$url .= self::$theme_paths[0] .'/';

		// Add theme
		$url .= empty(self::$active_theme) ? self::$default_theme : self::$active_theme;

		// Cleanup, just to be safe
		$url = str_replace('//', '/', $url);
		$url = str_replace(':/', '://', $url);

		return $url . $resource;

	}//end theme_url()

	//--------------------------------------------------------------------


	/**
	 * Set the current view to render.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $view The name of the view file to render as content.
	 *
	 * @return void
	 */
	public static function set_view($view=NULL)
	{
		if (empty($view) || !is_string($view))
		{
			return;
		}

		self::$current_view = $view;
	}//end set_view()

	//--------------------------------------------------------------------


	/**
	 * Makes it easy to save information to be rendered within the views.
	 *
	 * @param string $var_name The name of the variable to set
	 * @param mixed  $value    The value to set it to.
	 *
	 * @return void
	 */
	public static function set($var_name='', $value='')
	{
		// Added by dkenzik
		// 20101001
		// Easier migration when $data is scaterred all over your project
		//
		if(is_array($var_name) && $value=='')
		{
			foreach($var_name as $key => $value)
			{
				self::$data[$key] = $value;
			}
		}
		else
		{
			self::$data[$var_name] = $value;
		}//end if

	}//end set()

	//--------------------------------------------------------------------

	/**
	 * Returns a variable that has been previously set, or FALSE if not exists.
	 * As of 3.0, will also return class properties.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $var_name The name of the data item to return.
	 *
	 * @return mixed The value of the class property or view data.
	 */
	public static function get($var_name=NULL)
	{
		if (empty($var_name))
		{
			return FALSE;
		}

		// First, is it a class property?
		if (isset(self::$$var_name))
		{
			return self::$$var_name;
		}
		else if (isset(self::$data[$var_name]))
		{
			return self::$data[$var_name];
		}

		return FALSE;
	}//end get()

	//--------------------------------------------------------------------

	/**
	 * Set whether or not the views will be passed through CI's parser.
	 *
	 * @access public
	 *
	 * @param bool $parse Boolean value. Should we parse views?
	 */
	public function parse_views($parse = FALSE)
	{
		self::$parse_views = (bool) $parse;

	}//end parse_views()

	//--------------------------------------------------------------------


	/**
	 * Sets a status message (for displaying small success/error messages).
	 * This function is used in place of the session->flashdata function,
	 * because you don't always want to have to refresh the page to get the
	 * message to show up.
	 *
	 *  NOTE: This feature is deprecated as of version 1.0.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $message A string with the message to save.
	 * @param string $type    A string to be included as the CSS class of the containing div.
	 *
	 * @return void
	 */
	public static function set_message($message='', $type='info')
	{
		if (!empty($message))
		{
			if (isset(self::$ci->session))
			{
				// Grab any existing messages, if they exist, so we can
				// add it to the new one.
				$messages = self::$ci->session->flashdata('message');
				if ($messages)
				{
					$messages = unserialize($messages);
				}
				else
				{
					$messages = array();
				}

				$messages[] = $type .'::'. $message;

				self::$ci->session->set_flashdata('message', $messages);
			}

			self::$messages[] = array('type'=>$type, 'message'=>$message);
		}

	}//end set_message()

	//---------------------------------------------------------------

	/**
	 * Displays a status message (small success/error messages).
	 * If data exists in 'message' session flashdata, that will
	 * override any other messages. The renders the message based
	 * on the template provided in the config file ('OCU_message_template').
	 *
	 * NOTE: This feature is deprecated as of version 1.0.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $message A string to be the message. (Optional) If included, will override any other messages in the system.
	 * @param string $type    The class to attached to the div. (i.e. 'information', 'attention', 'error', 'success')
	 *
	 * @return string A string with the results of inserting the message into the message template.
	 */
	public static function message($message='', $type='information')
	{
		$output = '';

		// Does session data exist?
		if (empty($message) && class_exists('CI_Session'))
		{
			$messages = self::$ci->session->flashdata('message');
			if (!empty($messages))
			{
				$messages = unserialize($messages);
			}
			else
			{
				$messages = array();
			}

			if (!empty($messages))
			{
				foreach ($messages as $m)
				{
					// Split out our message parts
					$temp_message = explode('::', $m);
					$type = $temp_message[0];
					$output .= self::$message_tag_start . $temp_message[1] . self::$message_tag_end;

					unset($temp_message);
				}
			}
		}//end if

		// If message is empty, we need to check our own storage.
		if (empty($output))
		{
			if (!count(self::$messages))
			{
				return '';
			}

			foreach (self::$messages as $m)
			{
				$output .= $m['message'];
				$type 	 = $m['type'];
			}
		}

		// Grab out message template and replace the placeholders
		$template = str_replace('{type}', $type, self::$ci->config->item('template.message_template'));
		$template = str_replace('{message}', $output, $template);

		// Clear our session data so we don't get extra messages.
		// (This was a very rare occurence, but clearing should resolve the problem.
		if (class_exists('CI_Session'))
		{
			self::$ci->session->set_flashdata('message', '');
		}

		return $template;

	}//end message()

	//---------------------------------------------------------------

	/**
	 * Like CodeIgniter redirect(), but uses javascript if needed
	 * to redirect out of an ajax request.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $url The url to redirect to. If not a full url, will wrap it in site_url().
	 *
	 * @return void
	 */
	public static function redirect($url=NULL)
	{
		if ( ! preg_match('#^https?://#i', $url))
		{
			$url = site_url($url);
		}

		if (!self::$ci->input->is_ajax_request())
		{
			header("Location: ".$url);

			// A full HTML document requires certain elements to
			// be considered valid.  We don't return any content,
			// so override the default header which specifies HTML.
			header("Content-Type: text/plain");
		}
		else
		{
			// Output URL somewhere where we know how to escape it safely
			echo '<div id="url" data-url="';
			e($url);
			echo '"></div>';

			// then JS can grab it
			echo <<<EOF
<script>
window.location = document.getElementById('url').getAttribute('data-url');
</script>
EOF;
		}

		exit();

	}//end redirect()

	//--------------------------------------------------------------------

	/**
	 * Loads a view based on the current themes.
	 *
	 * @access public
	 * @static
	 *
	 * @param string $view      The view to load.
	 * @param array  $data      An array of data elements to be made available to the views
	 * @param string $override  The name of a view to check for first (used for controller-based layouts)
	 * @param bool   $is_themed Whether it should check in the theme folder first.
	 * @param object $output    A pointer to the variable to store the output of the loaded view into.
	 *
	 * @return void
	 */
	public static function load_view($view=NULL, $data=NULL, $override='', $is_themed=TRUE, &$output)
	{
		if (empty($view))	return '';

		// If no active theme is present, use the default theme.
		$theme = empty(self::$active_theme) ? self::$default_theme : self::$active_theme;

		if ($is_themed)
		{
			// First check for the overriden file...
			$output = self::find_file($override, $data, $theme);

			// If we didn't find it, try the standard view
			if (empty($output))
			{
				$output = self::find_file($view, $data, $theme);
			}
		}

		// Just a normal view (possibly from a module, though.)
		else
		{
			// First check within our themes...
			$output = self::find_file($view, $data, $theme);

			// if $output is empty, no view was overriden, so go for the default
			if (empty($output))
			{
				self::$ci->load->_ci_view_path = self::$orig_view_path;

				if (self::$parse_views === TRUE)
				{

					if (!class_exists('CI_Parser'))
					{
						self::$ci->load->library('parser');
					}

//					$output = self::$ci->load->_ci_load(array('_ci_path' => $view.'.php','_ci_vars' => $data,'_ci_return' => TRUE));

					if (count($data) > 0)
					{
						$data = array_merge((array)$data,self::$ci->load->_ci_cached_vars);

						$temp = array();
						foreach($data as $key => $value)
						{
							if (count($value) > 0)
							{
								$value = (array) $value;
							}
							$temp[$key] = $value;
						}
						$data = array();

						$data = $temp;
						unset($temp);
					}
					else
					{
						$data = self::$ci->load->_ci_cached_vars;
					}

					//$output = self::$ci->load->view($view, $data, TRUE);
					$output = self::$ci->parser->parse($view, $data, TRUE);
				}
				else
				{
					$output = self::$ci->load->view($view, $data, TRUE);
				}
			}
			self::$ci->load->_ci_view_path = self::$orig_view_path;
		}//end if

		// Put our ci view path back to normal
		//self::$ci->load->_ci_view_path = self::$orig_view_path;
		unset($theme, $orig_view_path);

	}//end load_view()

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// !PRIVATE METHODS
	//--------------------------------------------------------------------

	/**
	 * Searches through the the active theme and the default theme to try to find
	 * a view file. If found, it returns the rendered view.
	 *
	 * @access private
	 *
	 * @param string $view The name of the view to find.
	 * @param array  $data An array of key/value pairs to pass to the views.
	 *
	 * @return string The content of the file, if found, else empty.
	 */
	private function find_file($view=NULL, $data=NULL)
	{
		if (empty($view))
		{
			return FALSE;
		}

		$output = '';		// Stores the final output
		$view_path = '';	// Used to store the location of the file.

		if (!empty($data))
		{
			$data = (array)$data;
		}

		// If there are multiple theme locations, we need to search through all of them.
		foreach (self::$theme_paths as $path)
		{
			/*
				Check the active theme
			*/
			if (self::$debug) { echo "[Find File] Looking for view in active theme: <b>". FCPATH. $path .'/'. self::$active_theme . $view .'.php</b><br/>'; }

			if (!empty(self::$active_theme) && is_file(FCPATH. $path .'/'. self::$active_theme . $view .'.php'))
			{
				if (self::$debug) { echo 'Found <b>'. $view .'</b> in Active Theme.<br/>'; }
				$view_path = FCPATH. $path .'/'. self::$active_theme;
			}

		}

		// If the view was found, it's path is stored in the $view_path var. So parse or render it
		// based on user settings.
		if (!empty($view_path))
		{
			$view_path = str_replace('//', '/', $view_path);

			// Add it to the locations that the Loader will search for.
			self::$ci->load->add_view_path($view_path);

			if (self::$debug) { echo '[Find File] Rendering file at: '. $view_path . $view .'.php<br/><br/>'; }

			// Grab the output of the view.
			if (self::$parse_views === TRUE)
			{

				$data = array_merge((array)$data,self::$ci->load->_ci_cached_vars);
				$output = self::$ci->load->_ci_load(array('_ci_path' => $view_path . $view .'.php', '_ci_vars' => $data, '_ci_return' => TRUE));

				//Parser dies on looping, better then before but not fixed.
				//$output = self::$ci->parser->parse($view_path.$view, $data, TRUE, TRUE);
			} else
			{
				//$output = self::$ci->load->_ci_load(array('_ci_path' => $view_path . $view .'.php', '_ci_vars' => $data, '_ci_return' => TRUE));
				$output = self::$ci->load->view($view, $data, true);
			}

			// Put CI's view path back to the original
			self::$ci->load->remove_view_path($view_path);
		}//end if

		return $output;

	}//end find_file()

	//--------------------------------------------------------------------

}//end class


//--------------------------------------------------------------------

/**
 * A shorthand method that allows views (from the current/default themes)
 * to be included in any other view.
 *
 * This function also allows for a very simple form of mobile templates. If being
 * viewed from a mobile site, it will attempt to load a file whose name is prefixed
 * with 'mobile_'. If that file is not found it will load the regular view.
 *
 * @access  public
 * @example Rendering a view named 'index', the mobile version would be 'mobile_index'.
 *
 * @param string $view          The name of the view to render.
 * @param array  $data          An array of data to pass to the view.
 * @param bool   $ignore_mobile If TRUE, will not change the view name based on mobile viewing. If FALSE, will attempt to load a file prefixed with 'mobile_'
 *
 * @return string
 */
function theme_view($view=NULL, $data=NULL, $ignore_mobile=FALSE)
{
	if (empty($view)) return '';

	$ci =& get_instance();

	$output ='';

	// If we're allowed, try to load the mobile version
	// of the file.
	if (!$ignore_mobile)
	{
		$ci->load->library('user_agent');

		if ($ci->agent->is_mobile())
		{
			Template::load_view('mobile_'. $view, $data, NULL, TRUE, $output);
		}
	}

	// If output is empty, then either no mobile file was found
	// or we weren't looking for one to begin with.
	if (empty($output))
	{
		Template::load_view($view, $data, NULL, TRUE, $output);
	}

	return $output;

}//end theme_view()

//--------------------------------------------------------------------

/**
 * A simple helper method for checking menu items against the current
 * class that is running.
 *
 * <code>
 *   <a href="<?php echo site_url(SITE_AREA . '/content'); ?>" <?php echo check_class(SITE_AREA . '/content'); ?> >
 *    Admin Home
 *  </a>
 *
 * </code>
 * @access public
 *
 * @param string $item       The name of the class to check against.
 * @param bool   $class_only If TRUE, will only return 'active'. If FALSE, will return 'class="active"'.
 *
 * @return string Either <b>class="active"</b> or an empty string.
 */
function check_class($item='', $class_only=FALSE)
{
	$ci =& get_instance();

	if (strtolower($ci->router->fetch_class()) == strtolower($item))
	{
		return $class_only ? 'active' : 'class="active"';
	}

	return '';

}//end check_class()

//--------------------------------------------------------------------

/**
 * A simple helper method for checking menu items against the current
 * class' method that is being executed (as far as the Router knows.)
 *
 * @access public
 *
 * @param string $item The name of the method to check against. Can be an array of names.
 *
 * @return string Either <b>class="active"</b> or an empty string.
 */
function check_method($item)
{
	$ci =& get_instance();

	$items = array();

	if (!is_array($item))
	{
		$items[] = $item;
	}
	else
	{
		$items = $item;
	}

	if (in_array($ci->router->fetch_method(), $items))
	{
		return 'class="active"';
	}

	return '';

}//end check_method()

//--------------------------------------------------------------------

/**
 * Will create a breadcrumb from either the uri->segments or
 * from a key/value paired array passed into it.
 *
 * @access public
 *
 * @param array $my_segments (optional) Array of Key/Value to make Breadcrumbs from
 * @param bool  $wrap        (boolean)  Set to TRUE to wrap in un-ordered list
 * @param bool  $echo        (boolean)  Set to TRUE to echo the output, set to FALSE to return it.
 *
 * @return string A Breadcrumb of your page structure.
 */
function breadcrumb($my_segments=NULL, $wrap=FALSE, $echo=TRUE)
{
	$ci =& get_instance();

	$output = '';

	if (!class_exists('CI_URI'))
	{
		$ci->load->library('uri');
	}


	if ( $ci->config->item('template.breadcrumb_symbol') == '' )
	{
		$seperator = '/';
	}
	else
	{
		$seperator = $ci->config->item('template.breadcrumb_symbol');
	}

	if ($wrap === TRUE)
	{
		$seperator = '<span class="divider">' . $seperator . '</span>' . PHP_EOL;
	}


	if (empty($my_segments) || !is_array($my_segments))
	{
		$segments = $ci->uri->segment_array();
		$total    = $ci->uri->total_segments();
	}
	else
	{
		$segments = $my_segments;
		$total    = count($my_segments);
	}

	$in_admin = (bool) (is_array($segments) && in_array(SITE_AREA, $segments));

	if ( $in_admin == TRUE )
	{
		$home_link = site_url(SITE_AREA);
	}
	else
	{
		$home_link = site_url();
	}

	if ($wrap === TRUE)
	{
		$output  = '<ul class="breadcrumb">' . PHP_EOL;
		$output .= '<li><a href="'.$home_link.'"><i class="icon-home">&nbsp;</i></a> '.$seperator.'</li>' . PHP_EOL;
	}
	else
	{
		$output  = '<a href="'.$home_link.'">home</a> '.$seperator;
	}

	$url = '';
	$count = 0;

	// URI BASED BREADCRUMB
	if (empty($my_segments) || !is_array($my_segments))
	{
		foreach ($segments as $segment)
		{
			$url .= '/'. $segment;
			$count += 1;

			if ($count == $total)
			{
				if ($wrap === TRUE)
				{
					$output .= '<li class="active">' . ucfirst(str_replace('_', ' ', $segment)) . '</li>' . PHP_EOL;
				}
				else
				{
					$output .= ucfirst(str_replace('_', ' ', $segment)) . PHP_EOL;
				}
			}
			else
			{
				if ($wrap === TRUE)
				{
					$output .= '<li><a href="'. $url .'">'. str_replace('_', ' ', ucfirst(mb_strtolower($segment))) .'</a>' . $seperator . '</li>' . PHP_EOL;
				}
				else
				{
					$output .= '<a href="'. $url .'">'. str_replace('_', ' ', ucfirst(mb_strtolower($segment))) .'</a>' . $seperator . PHP_EOL;
				}
			}
		}
	}
	else
	{
		// USER-SUPPLIED BREADCRUMB
		foreach ($my_segments as $title => $uri)
		{
			$url .= '/'. $uri;
			$count += 1;

			if ($count == $total)
			{
				if ($wrap === TRUE)
				{
					$output .= '<li class="active">' . str_replace('_', ' ', $title) . '</li>' . PHP_EOL;
				}
				else
				{
					$output .= str_replace('_', ' ', $title);
				}

			}
			else
			{

				if ($wrap === TRUE)
				{
					$output .= '<li><a href="'. $url .'">'. str_replace('_', ' ', ucfirst(mb_strtolower($title))) .'</a>' . $seperator . '</li>' . PHP_EOL;
				}
				else
				{
					$output .= '<a href="'. $url .'">'. str_replace('_', ' ', ucfirst(mb_strtolower($title))) .'</a>' . $seperator . PHP_EOL;
				}

			}
		}
	}

	if ($wrap === TRUE)
	{
		$output .= PHP_EOL . '</ul>' . PHP_EOL;
	}

	unset($in_admin, $seperator, $url, $wrap);

	if ($echo === TRUE)
	{
		echo $output;
		unset ($output);
	}
	else
	{
		return $output;
	}

}//end breadcrumb()

//---------------------------------------------------------------

/* End of file template.php */
/* Location: ./application/libraries/template.php */
