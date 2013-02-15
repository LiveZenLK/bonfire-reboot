<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Help Module - Admin Controller
 */
class Admin extends BF_Controller {

	protected $theme = 'admin';

	//--------------------------------------------------------------------

	public function _remap($method=null)
	{
		if (method_exists($this, $method))
		{
			$this->$method();
		}
		else
		{
			// Shove it all to the index method to parse. :)
			$this->index($method);
		}
	}

	//--------------------------------------------------------------------

	public function index($page=null)
	{
		if (empty($page))
		{
			$page = 'index';
		}

		$content = $this->read_page($page);
		Template::set('page_content', $content);

		// Table of Contents
		$toc = $this->build_toc();

		Template::set('toc', $toc);

		// Display the page
		Template::set_view('admin/index');
		$this->render();
	}

	//--------------------------------------------------------------------

	public function styles()
	{
		$this->render();
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Utility Methods
	//--------------------------------------------------------------------

	public function read_page($page)
	{
		// Make sure to substitute any '.' folder separators in the URL.
		if (!empty($page))
		{
			$page = str_replace('.', '/', $page);
		}

		$base = str_ireplace('bonfire/', '', BFPATH) .'docs/';

		$output = '';

		if (is_file($base .$page .'.md'))
		{
			$output = file_get_contents($base .$page .'.md');

			$this->load->helper('markdown');
			$output = Markdown($output);
		}

		// Determine out page name
		$ar = explode('/', $page);

		Template::set('page_content_title', end($ar));

		return $output;
	}

	//--------------------------------------------------------------------

	private function build_toc()
	{
		$this->load->helper('directory');

		$base = str_ireplace('bonfire/', '', BFPATH) .'docs/';

		// Read in our first level TOC if exists.
		$toc = $this->read_toc($base);

		// Read through first-level folders to get their toc files.
		$map = directory_map($base, 2);

		foreach ($map as $folder => $file)
		{
			if (is_array($file))
			{
				$toc .= "<h2>$folder</h2>\n";
				$toc .= "<div class='toc-wrap' markdown=1 data-section='$folder'>\n";
				$toc .= $this->read_toc($base . $folder);
				$toc .= "</div>\n";
			}
		}

		// Parse the entire thing...
		$this->load->helper('markdown');

		$toc = Markdown($toc);

		return $toc;
	}

	//--------------------------------------------------------------------

	/**
	 * Simply reads the TOC file from a specified folder.
	 *
	 * @param  string $folder The name of the folder to look in for a _toc.md file.
	 * @return string         The unparsed file contents.
	 */
	private function read_toc($folder)
	{
		$folder = rtrim($folder, '/') .'/';

		if (file_exists($folder .'_toc.md'))
		{
			$file = file_get_contents($folder .'_toc.md');
			return $file;
		}

		return '';
	}

	//--------------------------------------------------------------------

}