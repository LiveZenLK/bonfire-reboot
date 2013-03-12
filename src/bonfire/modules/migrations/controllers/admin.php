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

	/**
	 * Displays the various available migrations and their versions for
	 * the App, Bonfire core, and the modules, and provides methods to
	 * migrate each of them.
	 */
	public function index()
	{
		$this->load->helper('form');

		$data = array(
			'app_installed'			=> $this->migrations->get_schema_version('app_'),
			'app_latest'			=> $this->migrations->get_latest_version('app_'),
			'core_installed'		=> $this->migrations->get_schema_version('core'),
			'core_latest'			=> $this->migrations->get_latest_version('core'),
			'mod_migrations'		=> $this->get_module_versions(),
			'app_migrations'		=> $this->migrations->get_available_versions('app_'),
			'core_migrations'		=> $this->migrations->get_available_versions('core')
		);

		Template::set($data);

		$this->render();
	}

	//--------------------------------------------------------------------

	public function migrate_to($version, $type='')
	{
		$result = $this->migrations->version($version, $type);

		if ($result !== FALSE && strlen($this->migrations->error) == 0)
		{
			if ($result === 0)
			{
				BF_Notices::set(lang('mig_migrate_uninstalled'), 'success');

				redirect(ADMIN_PATH .'/migrations');
			}
			else
			{
				BF_Notices::set(lang('mig_migrated_to'). $result, 'success');

				redirect(ADMIN_PATH .'/migrations');
			}
		}
		else
		{
			$msg = lang('mig_error_migrating');
			logit($msg . "\n" . $this->migrations->error, 'error');
			$msg = $msg . '<strong>' . $this->migrations->error . '</strong>';
			BF_Notices::set($msg, 'error');
		}//end if

		BF_Notices::set(lang('mig_no_version'), 'error');
		redirect(ADMIN_PATH .'/migrations');
	}

	//--------------------------------------------------------------------


	/**
	 * Runs the migration for a single module.
	 *
	 * @param  string $module The name of the module to migrate.
	 */
	public function migrate_module($module=null)
	{
		$file = $this->input->post('version');

		$version = $file != 'uninstall' ? (int)(substr($file, 0, 3)) : 0;

		$path = module_path($module, 'migrations');

		// Reset the migrations path for this run only.
		$this->migrations->set_path($path);

		// Do the migration
		$this->migrate_to($version, $module .'_');

		die(var_dump($module));
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