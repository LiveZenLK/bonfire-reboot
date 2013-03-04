<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bf_pipeline extends BF_Controller {

	protected $cache_type = 'dummy';

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
	private $can_combine 	= array('css', 'js');
	private $can_compress 	= array('css', 'js');
	private $can_cache		= array('css', 'js');

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
		$path = str_ireplace(BF_ASSET_PATH .'/', '', $this->uri->uri_string());

		// Stores where we actually find the file at.
		$found_path = null;

		$was_compressed	= false;
		$was_joined		= false;

		// Folder name based on file type (css, img, js, audio, video, flash)
		$folder_type = $this->determine_folder_type($path);

		/*
			Get the file contents
		 */
		if (!$contents = $this->cache->get(str_replace('/', '\\', $path)))
		{
			$contents = BF_Assets::get_asset_contents($path, $folder_type, $found_path);

			/*
				Compression
			 */
			if (in_array($folder_type, $this->can_compress) && $this->config->item('assets.compress') )
			{
				$contents = BF_Assets::compress_string($contents, $folder_type);
				$was_compressed = true;
			}

			/*
				If Compiling is enabled, copy to public/assets so that
				we have a static asset to serve next time.
			 */
			if ($this->config->item('assets.compile'))
			{
				BF_Assets::compile_asset($contents, $path, $was_compressed, $was_joined);
			}

			/*
				Save to Cache, if we are css or js
			 */
			if (in_array($folder_type, $this->can_cache) && !$this->config->item('assets.compile'))
			{
				$this->cache->save( str_replace('/', '\\', $path), $contents, 300);
			}
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