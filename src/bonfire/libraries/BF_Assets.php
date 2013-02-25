<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The BF_Assets class handles creating link tags for most asset types, but
 * specifically focusing on CSS stylesheets, Javascript files and images.
 *
 * This library has two duties:
 *
 * 		1. Simplifies creation of the link tags for our assets without needing
 * 		   different methods for the various states of the pipeline.
 * 		2. Providing methods that can be used by the Pipeline itself
 * 		   to combine, minify, etc.
 */
class BF_Assets {

	/**
	 * Pointer to CI superglobal.
	 */
	protected static $ci;

	/**
	 * CSS and JS file holder arrays.
	 */
	protected static $css_files = array();
	protected static $js_files	= array();

	/**
	 * If TRUE, the Asset Pipeline is enabled which combines,
	 * processes and minifies the assets where appropriate.
	 * If FALSE, will simply create links to the /public/assets files.
	 */
	protected static $pipeline_enabled = FALSE;

	/**
	 * If TRUE, the css() and js() methods will always render links to
	 * individual files, no matter the other settings for combining,
	 * minifying, etc.
	 *
	 * Debug mode can be set either through the BF_Assets::debug(); method
	 * or by add ?debug=1
	 */
	protected static $debug	= FALSE;

	/**
	 * The folders that assets should be looked in when the pipeline is enabled.
	 * When enabled, these folders will be browsed in the order they are listed here.
	 * The active module and active theme will be added at render time.
	 */
	protected static $asset_paths = array();

	/**
	 * Stores whether we have initialized our paths yet. Since we are browsing
	 * multiple folders finding all of our Modules and Themes folders, we don't
	 * want to do that at first, especially since we may be caching information.
	 */
	private static $paths_initialized = FALSE;

	/**
	 * The string to be added to the assets when their links are rendered out.
	 * In order for the asset pipeline to work, a route must exist that funnels
	 * these calls to the bf_pipeline controller.
	 */
	protected static $asset_url = '/assets/';

	//--------------------------------------------------------------------

	public function __construct()
	{
		self::$ci =& get_instance();

		self::init();
	}

	//--------------------------------------------------------------------

	public static function init()
	{
		// Make sure our application config file is loaded.
		if (!self::$ci->config->item('assets.js_compressor'))
		{
			self::$ci->config->load('application');
		}

		self::$pipeline_enabled = self::$ci->config->item('assets.enabled');

		// Is debug mode enabled from the URL?
		self::$debug = isset($_GET['debug']);
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// CSS Methods
	//--------------------------------------------------------------------

	/**
	 * Adds multiple stylesheet links to the list to be rendered out later.
	 */
	public function add_css()
	{
		if (!func_num_args())
		{
			return;
		}

		$files = func_get_args();

		if (is_array($files))
		{
			self::$css_files = array_merge(self::$css_files, $files);
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Renders out the links to the appropriate CSS files.
	 *
	 * If we are in debug mode, or 'assets.enabled' is FALSE, then we create
	 * separate link tags for each file
	 *
	 * @param string $media The media type for these files to be linked to.
	 * @return [type] [description]
	 */
	public static function css_tag()
	{
		if (!func_num_args())
		{
			return '';
		}

		$files 		= func_get_args();
		$options 	= array();

		// The only one that would be an array should be the 'options' array,
		// so grab it out of the files list.
		for ($i=0; $i < count($files); $i++)
		{
			if (is_array($files[$i]))
			{
				$options = $files[$i];
				unset($files[$i]);
				break;
			}
		}

		// Was the 'all' keyword passed in?
		if (in_array('all', $files))
		{
			$recursive = isset($options['recursive']) ? $options['recursive'] : false;
			$files = self::get_filenames_from_folder(FCPATH .'assets/css/', 'css', $recursive);
		}

		// Was media set in the options? If not, default to screen.
		if (!isset($options['media']))
		{
			$options['media'] = 'screen';
		}

		// Is the pipeline enabled?
		if (self::$pipeline_enabled)
		{

		}
		else
		{
			// Render out individual links
			return self::debug_css($files, $options['media']);
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Builds individual links for all CSS files in the system.
	 *
	 * Note that we do NOT check the files exist, etc here. Instead, we
	 * let the bf_pipeline controller handle that.
	 *
	 * @param  array $files  The files to create links to.
	 * @param string $media The media to attach to the link
	 * @return string        The links to all files.
	 */
	private static function debug_css($files, $media='screen')
	{
		if (!is_array($files))
		{
			return '';
		}

		$final = '';

		foreach ($files as $file)
		{
			// Make sure we have a consistent filename to deal with.
			$file = rtrim($file, '.css') .'.css';

			if (strpos('http', $file) === FALSE)
			{
				$file = self::$asset_url .'css/'. $file;
			}

			$attr = array(
				'rel'	=> 'stylesheet',
				'type'	=> 'text/css',
				'href'	=> $file,
				'media'	=> $media
			);

			$final .= '<link '. self::attributes($attr) ." />\n";
		}

		return $final;
	}


	//--------------------------------------------------------------------
	// JS Methods
	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Utility Methods
	//--------------------------------------------------------------------

	/**
	 * Can either set the debug flag or report on it's current state.
	 *
	 * @param  boolean $debug If !NULL, we set the class var to this value.
	 */
	public static function debug($debug=null)
	{
		if (is_null($debug))
		{
			return self::$debug;
		}

		if (is_bool($debug))
		{
			self::$debug = $debug;
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Ensures that the cache engine is loaded and loads it if it isn't.
	 * This makes sure that we can cache files after the heavy processing
	 * that combining and minifying create.
	 */
	public static function enable_cache()
	{
		// If the app isn't using the cache, we'll load up and use the
		// file based cache since we know that will always be present.
		if (!isset(self::$ci->cache))
		{
			self::$ci->load->driver('cache', array('adapter' => 'file'));
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Handles setting up our array of asset paths that the asset pipeline
	 * should look in for the requested file.
	 */
	public static function init_paths()
	{
		$new_paths = array();

		// First place to look is our application/assets folder
		$new_paths[] = realpath(APPPATH) .'/assets/';

		// Add our active module to the search paths.
		$module = self::$ci->router->fetch_module();

		if (!empty($module))
		{
			$p = APPPATH .'modules/'. rtrim($module, '/') .'/assets/';

			if (is_dir($p))
			{
				$new_paths[] = realpath($p);
			}
		}

		self::$ci->load->helper('directory');

		// Add our current theme to the paths...
		foreach (Template::$template_paths as $path)
		{
			$folders = directory_map(FCPATH . $path, 1);

			foreach ($folders as $folder)
			{
				$new_paths[] = FCPATH . $path .'/'. rtrim($folder, '/') .'/';
			}
		}

		self::$asset_paths = array_merge(self::$asset_paths, $new_paths);

		return self::$asset_paths;
	}

	//--------------------------------------------------------------------

	/**
	 * Clears all storage vars to the default, clean postiion.
	 *
	 * @return void
	 */
	public function clear_all()
	{
		self::$css_files 	= array();
		self::$js_files		= array();
	}

	//--------------------------------------------------------------------


	//--------------------------------------------------------------------
	// Private Methods
	//--------------------------------------------------------------------

	/**
	 * Converts an array of attributes into a string.
	 *
	 * @author Dan Horrigan (Stuff Library)
	 *
	 * @param array $attributes An array of value pairs representing the attributes.
	 *
	 * @return string A string containing the rendered attributes.
	 */
	private static function attributes($attributes=null)
	{
		if (empty($attributes))
		{
			return '';
		}

		$final = '';
		if (is_array($attributes))
		{
			foreach ($attributes as $key => $value)
			{
				if ($value === NULL)
				{
					continue;
				}

				$final .= ' '. $key .'="'. htmlspecialchars($value, ENT_QUOTES) .'"';
			}
		}

		return $final;
	}

	//--------------------------------------------------------------------

	/**
	 * Scans a folder for files and returns a list of them.
	 *
	 * @param  [type]  $folder    [description]
	 * @param  boolean $recursive [description]
	 * @return [type]             [description]
	 */
	private static function get_filenames_from_folder($folder, $ext, $recursive=false)
	{
		if (!is_dir($folder))
		{
			return NULL;
		}

		// Standardize our extension
		$ext = '.'. trim($ext, '.');

		if (!function_exists('directory_map'))
		{
			self::$ci->load->helper('directory');
		}

		$files = array();

		$map = directory_map($folder);

		foreach ($map as $dir => $file)
		{
			// Is this another folder?
			if (is_array($file) && $recursive)
			{
				$temp = self::get_filenames_from_folder(rtrim($folder, '/') .'/'. $dir, $ext, $recursive);

				if (is_array($temp))
				{
					foreach ($temp as $t)
					{
						$files[] = rtrim($dir, '/') .'/'. $t;
					}
				}
			}

			// Or is it a single file with a valid extension?
			else if (is_string($file) )
			{
				if (substr($file, -strlen($ext)) == $ext)
				{
					$files[] = $file;
				}
			}

		}

		unset($map);

		return $files;
	}

	//--------------------------------------------------------------------

}