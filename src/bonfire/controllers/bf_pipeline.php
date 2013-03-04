<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bf_pipeline extends BF_Controller {

	protected $cache_type = 'dummy';

	private $asset_paths = array();

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

		$folder_type = BF_Assets::determine_folder_type($path);

		/*
			Get the file contents
		 */
		if (!$contents = $this->cache->get(str_replace('/', '\\', $path)))
		{
			$contents = BF_Assets::get_asset_contents($path, $folder_type, $found_path);

			/*
				Compression
			 */
			if (in_array($folder_type, BF_Assets::$can_compress) && $this->config->item('assets.compress') )
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

}