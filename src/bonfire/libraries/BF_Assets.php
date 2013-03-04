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
	protected static $asset_url;

	/**
	 * Fingerprints are md5 hashes of hte contents of the file in most cases.
	 * Protects filenames from far-future expires.
	 */
	protected static $fingerprint_assets = FALSE;

	/**
	 * Maps the file extensions to default folders in our
	 * assets paths.
	 */
	protected static $folder_map = array(
			'css'		=> 'css',
			'js'		=> 'js',
			'bmp'		=> 'img',
			'gif'		=> 'img',
			'png'		=> 'img',
			'jpg'		=> 'img',
			'jpeg'		=> 'img',
			'jpe'		=> 'img',
			'tiff'		=> 'img',
			'tif'		=> 'img',
			'swf'		=> 'flash',
			'mid'		=> 'audio',
			'midi'		=> 'audio',
			'mp3'		=> 'audio',
			'ogg'		=> 'audio',
			'wav'		=> 'audio',
			'aif'		=> 'audio',
			'aiff'		=> 'audio',
			'aifc'		=> 'audio',
			'mpga'		=> 'audio',
			'mp2'		=> 'audio',
			'ram'		=> 'audio',
			'rm'		=> 'audio',
			'rpm'		=> 'audio',
			'ra'		=> 'audio',
			'rv'		=> 'video',
			'mpeg'		=> 'video',
			'mpe'		=> 'video',
			'mpg'		=> 'video',
			'qt'		=> 'video',
			'mov'		=> 'video',
			'avi'		=> 'video',
			'movie'		=> 'video'
		);

	/**
	 * Folder Types that can be compressed and combined.
	 */
	public static $can_combine 		= array('css', 'js');
	public static $can_compress 	= array('css', 'js');

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

		self::$asset_url = '/'. BF_ASSET_PATH .'/';
		self::$pipeline_enabled = self::$ci->config->item('assets.enabled');
		self::$fingerprint_assets = self::$ci->config->item('assets.fingerprint');

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
			$files = self::$css_files;
		}
		else
		{
			$files 		= func_get_args();
		}
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
			$attr = array(
				'rel'	=> 'stylesheet',
				'type'	=> 'text/css',
				'href'	=> self::compute_public_path($file, 'css', 'css', false),
				'media'	=> $media
			);

			$final .= '<link'. self::attributes($attr) ." />\n";
		}

		return $final;
	}

	//--------------------------------------------------------------------


	//--------------------------------------------------------------------
	// JS Methods
	//--------------------------------------------------------------------

	/**
	 * Adds multiple javascript files to the list to be rendered out later.
	 */
	public function add_js()
	{
		if (!func_num_args())
		{
			return;
		}

		$files = func_get_args();

		if (is_array($files))
		{
			self::$js_files = array_merge(self::$js_files, $files);
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Renders out the links to the appropriate JS files.
	 *
	 * If we are in debug mode, or 'assets.enabled' is FALSE, then we create
	 * separate link tags for each file
	 *
	 * @return [type] [description]
	 */
	public static function js_tag()
	{
		if (!func_num_args())
		{
			$files = self::$js_files;
		}
		else
		{
			$files 		= func_get_args();
		}
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
			$files = self::get_filenames_from_folder(FCPATH .'assets/js/', 'js', $recursive);
		}

		// Is the pipeline enabled?
		if (self::$pipeline_enabled)
		{

		}
		else
		{
			// Render out individual links
			return self::debug_js($files);
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Builds individual links for all JS files in the system.
	 *
	 * Note that we do NOT check the files exist, etc here. Instead, we
	 * let the bf_pipeline controller handle that.
	 *
	 * @param  array $files  The files to create links to.
	 * @return string        The links to all files.
	 */
	private static function debug_js($files)
	{
		if (!is_array($files))
		{
			return '';
		}

		$final = '';

		foreach ($files as $file)
		{
			$attr = array(
				'type'	=> 'text/javascript',
				'src'	=> self::compute_public_path($file, 'js', 'js', false),
			);

			$final .= '<script'. self::attributes($attr) ."></script>\n";
		}

		return $final;
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Images
	//--------------------------------------------------------------------

	/**
	 * Creates an image tag for any image in public/assets/img folder.
	 *
	 * @param  [type] $file    [description]
	 * @param  array  $options [description]
	 * @return [type]          [description]
	 */
	public static function img_tag($file=null, $options=array())
	{
		$attr = array(
			'src'	=> self::compute_public_path($file, 'img', null, false)
		);

		// Size
		if (isset($options['size']))
		{
			list($width, $height) = explode('x', $options['size']);

			if (is_numeric($width))
			{
				$attr['width'] = $width;
			}

			if (is_numeric($height))
			{
				$attr['height'] = $height;
			}

			unset($options['size']);
		}

		// Width
		if (isset($options['width']))
		{
			$attr['width'] = $options['width'];
			unset($options['width']);
		}

		// Height
		if (isset($options['height']))
		{
			$attr['height'] = $options['height'];
			unset($options['height']);
		}

		// Alt Tag
		if (isset($options['alt']))
		{
			$attr['alt'] = $options['alt'];
			unset($options['alt']);
		}
		else
		{
			$info = pathinfo($file);
			$attr['alt'] =  ucwords( str_replace('_', ' ', basename($file,'.'.$info['extension']) ));
		}

		// Merge in any remaining options
		$attr = array_merge($attr, $options);

		return '<img'. self::attributes($attr) ." />\n";
	}

	//--------------------------------------------------------------------

	public static function audio_tag()
	{
		if (!func_num_args())
		{
			$files = self::$js_files;
		}
		else
		{
			$files 		= func_get_args();
		}
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

		// Content to show if browser doesn't support this tag.
		$inner_content = 'Your browser does not support the audio tag.';
		if (isset($options['inner_content']))
		{
			$inner_content = $options['inner_content'];
			unset($options['inner_content']);
		}

		// the Attributes apply only to the audio tag itself, not the stuff inside.
		$attrs = array();

		foreach ($options as $key => $value)
		{
			// Is it a core audio param?
			if (in_array(strtolower($key), array('autoplay', 'controls', 'loop', 'muted')))
			{
				$attrs[$key] = $key;
				unset($options[$key]);
			}
		}

		// Include any additional attributes.
		$attrs = array_merge($attrs, $options);

		$output = '<audio'. self::attributes($attrs) .">\n";

		foreach ($files as $file)
		{
			$src = self::compute_public_path($file, 'audio', null, false);

			$type = '';
			$type = stripos($src, '.mp3') !== FALSE ? 'audio/mpeg' : $type;
			$type = stripos($src, '.wav') !== FALSE ? 'audio/wav' : $type;
			$type = stripos($src, '.ogg') !== FALSE ? 'audio/ogg' : $type;

			$output .='    <source src="'. $src .'" type="'. $type .'">'. "\n";
		}

		$output .= '    '. $inner_content ."\n";

		$output .= "</audio>\n";

		return $output;
	}

	//--------------------------------------------------------------------

	/**
	 * Creates the RSS-feed auto-discovery-link.
	 *
	 * Possible options:
	 * 		'rel'
	 * 		'title'
	 * 		'href'
	 *
	 * @param  array  $options An array of potential options. See description.
	 * @return string          The auto discovery link
	 */
	public function auto_discovery_tag($options=array())
	{
		if (!function_exists('current_url'))
		{
			$this->ci->load->helper('url');
		}

		$attr = array();

		$attr['rel'] = isset($options['rel']) ? $options['rel'] : 'alternate';

		$type = isset($options['type']) ? $options['type'] : 'rss';
		$attr['type'] = "application/{$type}+xml";

		$attr['title'] = isset($options['title']) ? $options['title'] : 'RSS';

		$attr['href'] = isset($options['url']) ? self::compute_public_path($options['url'], null, null, true, false) : current_url();

		return '<link'. self::attributes($attr) ." />\n";
	}

	//--------------------------------------------------------------------


	//--------------------------------------------------------------------
	// Path Methods
	//--------------------------------------------------------------------

	/**
	 * Computes the path to an asset in the public assets/css directory. If
	 * the source has no extension, .css will be added unless it is a full
	 * URI. Full paths from the document root will be passed through.
	 *
	 * @param  string $source The source of the asset location.
	 *
	 * @return string         The computed path
	 */
	public static function css_path($source=null)
	{
		return compute_public_path($source, 'css', 'css');
	}

	//--------------------------------------------------------------------

	/**
	 * Builds the correct URI path to an asset. Adds the extension if not present.
	 * Returns full URLs untouched. Prefix with assets_path var.
	 *
	 * @param  string $source The source asset file to build out.
	 * @param  string $folder The folder (within assets) to inlcude if necessary
	 * @param  string $ext    The file extension to add.
	 * @param  bool   $include_host [description]
	 *
	 * @return string         The computed path relative to the site.
	 */
	public static function compute_public_path($source, $folder=null, $ext=null, $include_host=true, $include_asset_folder=true)
	{
		// Standardize to include extension
		if (!empty($ext))
		{
			$ext = '.'. trim($ext, '.');
			$source = rtrim($source, $ext) . $ext;
		}

		// Add our assets path to it unless it starts with '/'
		if ($source[0] != '/' && !self::is_uri($source))
		{
			$temp = $include_asset_folder == true ? self::$asset_url : '';
			$temp .= !empty($folder) ? $folder .'/'. $source : $source;

			$source = $temp;
		}

		// Fingerprint the asset name for cache-busting
		if (self::$fingerprint_assets)
		{
			// Make sure the file is compiled into the public path
			$orig_path 	= self::ensure_file($source);
			$new_path 	= '';

			// Generate our fingerprinted filename...
			$dir = dirname($source) .'/';
			$source 	= $dir . self::fingerprint($orig_path, $folder, $new_path);

			if (is_file($orig_path))
			{
				rename($orig_path, $new_path);
			}
		}

		if ($include_host)
		{
			$source = base_url($source);
		}

		return $source;
	}

	//--------------------------------------------------------------------

	/**
	 * Generates the fingerprinted filename based on the contents of the
	 * file (or combination of files). This should be used for generating
	 * pre-compiled filenames, not necessarily on live files.
	 *
	 * @param  string $source The name of the source file to fingerprint.
	 * @return string         The revised source name, including the fingerprint.
	 */
	public static function fingerprint($source, $folder_type, &$new_path=null)
	{
		if (!self::is_uri($source) && is_file($source))
		{
			$hash = md5_file($source);

			$ext = '.'. pathinfo($source, PATHINFO_EXTENSION);
			$name = str_replace($ext, '', basename($source));

			$name .= '-'. $hash . $ext;

			// Build out our new path
			$new_path = FCPATH . BF_ASSET_PATH .'/'. $folder_type .'/'. $name;

			return $name;
		}

		// File doesn't exist, or is a full URL, so we don't mess with it.
		return $source;
	}

	//--------------------------------------------------------------------

	public static function enable_fingerprinting($enable=true)
	{
		self::$enable_fingerprinting = $enable;
	}

	//--------------------------------------------------------------------

	/**
	 * Checks to make sure a file exists and, if not, runs through the
	 * typical pipeline to build the file out. This is most useful
	 * so that we can fingerprint the final file.
	 *
	 * @param  string $source The source file name from the URL.
	 * @param bool $compile_Asset If TRUE, will compile the asset into the public asset folder.
	 * @return [type]         [description]
	 */
	public function ensure_file($source, $compile_asset=TRUE)
	{
		$server_path = FCPATH . BF_ASSET_PATH . str_ireplace(BF_ASSET_PATH .'/', '', $source);

		if (is_file($server_path))
		{
			return $server_path;
		}

		// Stores where we actually find the file at.
		$found_path = null;

		$was_compressed	= false;
		$was_joined		= false;

		// Folder name based on file type (css, img, js, audio, video, flash)
		$folder_type = self::determine_folder_type($source);

		$contents = self::get_asset_contents($source, $folder_type, $found_path);

		/*
			Compression
		 */
		if (in_array($folder_type, self::$can_compress) && self::$ci->config->item('assets.compress') )
		{
			$contents = self::compress_string($contents, $folder_type);
			$was_compressed = true;
		}

		/*
			If Compiling is enabled, copy to public/assets so that
			we have a static asset to serve next time.
		 */
		if ($compile_asset)
		{
			self::compile_asset($contents, str_ireplace(BF_ASSET_PATH .'/', '', $source), $found_path, $was_compressed, $was_joined);
		}

		return $server_path;
	}

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
		if (!class_exists('CI_Cache'))
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
		if (count(self::$asset_paths))
		{
			return self::$asset_paths;
		}

		$new_paths = array();

		// First place to look is our application/assets folder
		$new_paths[] = realpath(APPPATH) .'/';

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
	 * Compresses a string. Used for compressing CSS and JS files.
	 *
	 * @param  string $contents    The file contents to compress
	 * @param  string $folder_type Either 'css', or 'js'
	 */
	public static function compress_string($contents, $folder_type)
	{
		$vendor_path = str_replace('bonfire/', '', BFPATH) .'vendor/';

		switch ($folder_type)
		{
			case 'css':
				list($comp_path, $method) = explode('::', self::$ci->config->item('assets.css_compressor'));
				list($f, $class) = explode('/', $comp_path);

				require ($vendor_path . $comp_path .'.php');
				$contents = $class::$method($contents);
				$was_compressed = true;
				break;
			case 'js':
				list($comp_path, $method) = explode('::', self::$ci->config->item('assets.js_compressor'));
				list($f, $class) = explode('/', $comp_path);

				require ($vendor_path . $comp_path .'.php');
				$contents = $class::$method($contents);
				$was_compressed = true;
				break;
		}

		return $contents;
	}

	//--------------------------------------------------------------------

	/**
	 * Compiles a single asset and saves the copy to the public assets folder
	 * so that we have a static file we can deal with.
	 *
	 * @param  string $contents       The contents of the file to work with.
	 * @param  string $path           The path to the original file.
	 * @param  bool $was_compressed IF TRUE, the file has been compressed.
	 * @param  boolean $was_joined     If TRUE, multiple files have been joined.
	 */
	public static function compile_asset($contents, $path, $found_path, $was_compressed, $was_joined)
	{
		// $path = final path to file (within /assets folder)
		// $found_path = original source destination
		//$path = str_replace(  realpath(APPPATH) .'/'. BF_ASSET_PATH, '', $path);
		$final_path = str_replace('//', '/', FCPATH . BF_ASSET_PATH .'/'. $path);

		// If it has been compressed or joined, we need to
		// use the $content var and write out to file. These should
		// always be text-based files so we should be good here.
		if ($was_joined || $was_compressed)
		{
			self::$ci->load->helper('file');

			if (!write_file($final_path, $contents))
			{
				show_error('Unable to write to file: '. $final_path);
			}
		}

		// Otherwise we simply copy the file...
		else if (!empty($found_path))
		{
			if (!copy($found_path, $final_path))
			{
				show_error('Unable to copy file: '. $final_path);
			}
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves the contents of an asset. Used for CSS and JS, primarily, but
	 * also valid for some other content types.
	 *
	 * @param  string  $path        The path to the original file as provided to the URL
	 * @param  string  $folder_type The asset type, like 'css' or 'js'
	 * @param  string $found_path   The final path where the asset was actually found at.
	 * @return string               The contents of the asset.
	 */
	public function get_asset_contents($path, $folder_type, &$found_path)
	{
		$paths = self::init_paths();
		$contents = '';

		foreach ($paths as $asset_path)
		{
			$file = str_replace('//', '/', $asset_path . $path);

			if (is_file($file))
			{
				$found_path = $file;

				switch ($folder_type)
				{
					case 'css':
					case 'js':
						$contents = file_get_contents($file);
						break;
					case 'flash':
					case 'audio':
					case 'video':
						break;
					case 'img':
						// Potentially expensive process
						// that helps when we're not compiling
						// so images can still be dipslayed.
						$contents = file_get_contents($file);
						break;
				}

				break;
			}
		}

		return $contents;
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

	/**
	 * Attempts to determine the filetype that should be used. The method
	 * returns the folder name that we want to check in for each of our paths.
	 *
	 * @param  [type] $filename [description]
	 * @return string           The folder name to look in (css/js/img/etc)
	 */
	public static function determine_folder_type($filename)
	{
		// Get our file extension
		$ext = pathinfo($filename, PATHINFO_EXTENSION);

		if (isset(self::$folder_map[$ext]))
		{
			return self::$folder_map[$ext];
		}

		return NULL;
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

	/**
	 * Determines if a file is any type of URI (http, https, ftp, etc).
	 *
	 * @param  string  $source The value to interpret
	 * @return boolean
	 */
	public static function is_uri($path)
	{
		return (bool)preg_match('/^[a-z]+:\/\//', $path);
	}

	//--------------------------------------------------------------------

}