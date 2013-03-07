<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Migrations Module - Admin Controller
 */
class Admin extends Admin_Controller {

	protected $language_file	= 'migrations';

	//--------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		$this->load->library('Migrations');
	}

	//--------------------------------------------------------------------


	//--------------------------------------------------------------------

	public function index()
	{
		$this->load->helper('form');

		$data = array(
			'app_installed'			=> $this->migrations->get_schema_version('app_'),
			'app_latest'			=> $this->migrations->get_latest_version('app_'),
			'core_installed'		=> $this->migrations->get_schema_version('core'),
			'core_latest'			=> $this->migrations->get_latest_version('core'),
			'mod_migraions'			=> $this->get_module_versions(),
			'app_migrations'		=> $this->migrations->get_available_versions('app_'),
			'core_migrations'		=> $this->migrations->get_available_versions('core')
		);

		Template::set($data);

		$this->render();
	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Private Methods
	//--------------------------------------------------------------------

	/**
	 * Get all versions available for the modules
	 *
	 * @access private
	 *
	 * @return array Array of available versions for each module
	 */
	private function get_module_versions()
	{
		$mod_versions = array();

		$modules = module_files(null, 'migrations');

		if ($modules === false)
		{
			return false;
		}

		// Sort Module Migrations in Reverse Order instead of Randomness.
		foreach ($modules as &$mod)
		{
			if ( ! array_key_exists('migrations', $mod))
			{
				continue;
			}

			arsort($mod['migrations']);
		}

		foreach ($modules as $module => $migrations)
		{
			$mod_versions[$module] = array(
				'installed_version'	=> $this->migrations->get_schema_version($module .'_'),
				'latest_version'	=> $this->migrations->get_latest_version($module .'_'),
				'migrations'		=> $migrations['migrations']
			);
		}

		return $mod_versions;
	}//end get_module_versions()

	//--------------------------------------------------------------------
}