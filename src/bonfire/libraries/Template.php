<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Template {

	/**
	 * The name of the theme to use.
	 */
	protected static $theme = NULL;

	/**
	 * The name of the layout to call.
	 */
	protected static $layout = NULL;

	/**
	 * The folders that we should look in for template-related files.
	 */
	protected static $template_paths = array();

	/**
	 * Stores the yields as they are built.
	 */
	protected static $yields;

	/**
	 * Stores the blocks to be displayed.
	 */
	protected static $blocks;

	/**
	 * Stores the data that should be made available to the view.
	 */
	protected static $data = array();

	/**
	 * Hooking into the CI superobject.
	 */
	private static $ci;

	//--------------------------------------------------------------------

	/**
	 * Primarily a workaround for CI loading of the file.
	 *
	 * Sets the CI object reference and calls the init() method.
	 */
	public function __construct()
	{
		self::$ci =& get_instance();

		self::init();
	}

	//--------------------------------------------------------------------

	/**
	 * Reads our config and stores our variables for faster access.
	 */
	public static function init()
	{
		// If the application config file hasn't been loaded, do it now
		if (!self::$ci->config->item('template.theme_paths'))
		{
			self::$ci->config->load('application');
		}

		self::$layout			= self::$ci->config->item('template.default_layout');
		self::$theme  			= self::$ci->config->item('template.default_theme');
		self::$template_paths  	= self::$ci->config->item('template.template_paths');

		self::$yields	= new stdClass();
		self::$blocks	= new stdClass();
	}

	//--------------------------------------------------------------------

	public static function render($layout=null)
	{
		$output = '';

		$view = self::$ci->router->fetch_class() . '/' . self::$ci->router->fetch_method();
		$layout = empty($layout) ? self::$layout : $layout;

		// Grab our main view first so that we can support multiple yields
		self::$yields->tpl_content = self::load_view($view);

		// Try to load our template
		$output = self::load_view($layout, true);

		return $output;
	}

	//--------------------------------------------------------------------

	/**
	 * Renders the current page into the layout.
	 *
	 * Uses a view based on the controller/function being run. (See render method).
	 *
	 * @return string A string containing the output of the render process.
	 */
	public function yield($name=null)
	{
		// If no name is passed in, then this is our main view.
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
	}

	//--------------------------------------------------------------------



	//--------------------------------------------------------------------
	// Utility Methods
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
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Sets the name of the theme to use.
	 *
	 * @param string $theme The name of the theme.
	 */
	public function set_theme($theme)
	{
		if (!empty($theme))
		{
			self::$theme = $theme;
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Sets the name of the layout to use.
	 *
	 * @param string $theme The name of the layout.
	 */
	public function set_layout($layout)
	{
		if (!empty($layout))
		{
			self::$layout = $layout;
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Handles the simple task of loading in a view file. If it is not themed
	 * we will simply use CI's built in load->view() method to take advantage of
	 * the module locations. If it's themed, though, we'll load it ourselves
	 * for maximum performance.
	 *
	 * @param  string  $filename The name of the view file (without extension)
	 * @param  boolean $themed   if TRUE, will look in the theme file.
	 * @return string  			 The rendered view file.
	 */
	public static function load_view($filename, $themed=false)
	{
		if (!$themed)
		{
			return self::$ci->load->view($filename, null, true);
		}

		foreach (self::$template_paths as $path)
		{
			$file = realpath(FCPATH . $path .'/'. self::$theme) .'/'. $filename .'.php';

			// We found it, so load that bad boy in and send
			// it back to the caller!
			if (is_file($file))
			{
				// Make the data available to the views
				extract(self::$data);

				ob_start();

					include($file);

					$buffer = ob_get_contents();
				ob_end_clean();
				return $buffer;
			}
		}

		// If we're here, then we didn't find the file. Throw an error, CI Style.
		show_error('Unable to load the requested file: '. $filename);
	}

	//--------------------------------------------------------------------

}