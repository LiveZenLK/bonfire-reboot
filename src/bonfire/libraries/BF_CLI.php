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

//--------------------------------------------------------------------

/**
 * BF_CLI class
 *
 * Provides a wrapper class that implements the basics needed for the
 * CLI functionality.
 *
 * This class will locate commands in the following order:
 * 		- {module}/commands
 * 		- application/commands
 * 		- bonfire/commands
 */

class BF_CLI {

	public static $styles = array(
			'normal'	=> '',
			'info'		=> '',
			'warning'	=> '',
			'error'		=> ''
		);

	public function __construct()
	{
		// Make sure our errors are known.
		error_reporting(E_ALL | E_STRICT);

		ini_set("error_log", NULL);
		ini_set("log_errors", 1);
		ini_set("html_errors", 0);
		ini_set("display_errors", 0);

		// Make sure the output buffer is off and allowing things
		// to flow easily through the system.
		while (ob_get_level())
		{
			ob_end_clean();
		}
		ob_implicit_flush(true);
	}

	//--------------------------------------------------------------------

	/**
	 * Gets the ball rolling and does the actual work here. Or at least pretends
	 * that it does, since it's really just handing the job off to it's
	 * command files...
	 *
	 * @return [type] [description]
	 */
	public function start()
	{
		// Display Bonfire info
		self::write(sprintf(
				"\nBonfire %s - PHP %s [%s]\n",
				BONFIRE_VERSION,
				phpversion(),
				PHP_OS
			)
		);
	}

	//--------------------------------------------------------------------

	/**
	 * Returns a list of all available commands, both built into Bonfire
	 * and included with any of the modules.
	 *
	 */
	public function commands()
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Returns help for a single specific
	 * @return [type] [description]
	 */
	public function help()
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Displays a text string out to the command line.
	 *
	 * @param  string $msg  The string to display on the screen.
	 * @param  string $style One of 'normal', 'info', 'warning', 'error'
	 */
	public static function write($msg, $style='normal')
	{
		echo $msg ."\n";
	}

	//--------------------------------------------------------------------

}