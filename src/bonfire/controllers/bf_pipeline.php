<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bf_pipeline extends BF_Controller {

	private $asset_paths = array();

	/**
	 * Maps the file extensions to default folders in our
	 * assets paths.
	 */
	private $folder_map = array(
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
	private $can_combine = array('css', 'js');
	private $can_compress = array('css', 'js');

	/**
	 * Everything simply needs to go to the index method to make the magic happen.
	 */
	public function _remap($method)
	{
		$this->index($method);
	}

	//--------------------------------------------------------------------

	/**
	 * Does the main work of building out our
	 */
	public function index($type)
	{
		// Clean up the requested asset name.
		$path = str_ireplace('assets/', '', $this->uri->uri_string());

		// Folder name based on file type (css, img, js, audio, video, flash)
		$folder_type = $this->determine_folder_type($path);

		// Load our asset library and start our cache, if the app isn't using one.
		BF_Assets::enable_cache();

		/*
			Get the file contents
		 */
		if (!$contents = $this->cache->get($path))
		{
			$paths = BF_Assets::init_paths();

			foreach ($paths as $asset_path)
			{
				$file = $asset_path .'/'. $path;

				if (is_file($file))
				{
					switch ($folder_type)
					{
						case 'css':
						case 'js':
							$contents = file_get_contents($file);
							break;
						case 'flash':
						case 'audio':
						case 'video':
						case 'img':
							break;
					}

					break;
				}
			}

			/*
				Compression
			 */
			if (in_array($folder_type, $this->can_compress) && $this->config->item('assets.compress') )
			{
				$vendor_path = str_replace('bonfire/', '', BFPATH) .'vendor/';

				switch ($folder_type)
				{
					case 'css':
						list($comp_path, $method) = explode('::', $this->config->item('assets.css_compressor'));
						list($f, $class) = explode('/', $comp_path);

						require ($vendor_path . $comp_path .'.php');
						$contents = $class::$method($contents);
						break;
					case 'js':
						list($comp_path, $method) = explode('::', $this->config->item('assets.js_compressor'));
						list($f, $class) = explode('/', $comp_path);

						require ($vendor_path . $comp_path .'.php');
						$contents = $class::$method($contents);
						break;
				}
			}

			$this->cache->save($path, $contents, 300);
		}


		/*
			Display the Output with correct mime type.
		 */
		switch ($folder_type)
		{
			case 'css':
				$mime = 'text/css';
				break;
			case 'js':
				$mime = 'text/javascript';
				break;
			default:
				$this->load->helper('file');
				$mime = get_mime_by_extension($path);
				break;
		}


		// If we have the mime type we'll set the content type
		// here, otherwise, we'll let the browser default it for now.
		if ($mime)
		{
			$this->output->set_content_type($mime)->set_output($contents);
		}
		else
		{
			$this->output->set_output($contents);
		}
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Private Methods
	//--------------------------------------------------------------------

	/**
	 * Pulls together all of our possible asset paths into a single
	 * array. Since this must hit the filesystem numerous times, it tries
	 * to cache the results using your application's current Cache engine.
	 *
	 * @return void
	 */
	private function build_asset_paths()
	{
		$paths = array();

		$this->config->load('application');
		$this->load->helper('directory');

		// Application path will override everything else!
		$paths[] = realpath(APPPATH) .'/assets/';

		// Theme paths
		$template_paths = $this->config->item('template.template_paths');
		foreach ($template_paths as $tp)
		{
			$themes = directory_map(FCPATH .$tp, 1);

			if (is_array($themes))
			{
				foreach ($themes as $theme)
				{
					$paths[] = FCPATH . $tp .'/'. $theme .'/assets/';
				}
			}
		}
		unset($template_paths);

		// Save it for everyone else.
		$this->asset_paths = $paths;
	}

	//--------------------------------------------------------------------

	/**
	 * Attempts to determine the filetype that should be used. The method
	 * returns the folder name that we want to check in for each of our paths.
	 *
	 * @param  [type] $filename [description]
	 * @return string           The folder name to look in (css/js/img/etc)
	 */
	private function determine_folder_type($filename)
	{
		// Get our file extension
		$ext = pathinfo($filename, PATHINFO_EXTENSION);

		if (isset($this->folder_map[$ext]))
		{
			return $this->folder_map[$ext];
		}

		return NULL;
	}

	//--------------------------------------------------------------------

}