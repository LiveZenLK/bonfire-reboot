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
 * BF_Command class
 *
 * Provides a base class for CLI-commands to extend from in order
 * to get the basic functionality like writing to the CLI, getting iput, etc.
 *
 */
class BF_Command {

	//--------------------------------------------------------------------
	// Arguments and Options
	//--------------------------------------------------------------------
	// These methods are intended to be overridden by the child classes
	// to let the system know of available arguments and options for your class.
	//

	/**
	 * Lets the CLI know what
	 * @return [type] [description]
	 */
	public function get_arguments()
	{

	}

	//--------------------------------------------------------------------

	public function get_options()
	{

	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Helper methods
	//--------------------------------------------------------------------

	/**
	 * Should be overridden by the child class to display help text
	 * for their command.
	 *
	 */
	public function help()
	{
		BF_CLI::display('No help available.', 'info');
	}

	//--------------------------------------------------------------------


	//--------------------------------------------------------------------
	// Input Methods
	//--------------------------------------------------------------------

	/**
	 * Retrieves the value of one or more CLI arguments.
	 *
	 * @param  string $name The name of argument to retrieve
	 * @return mixed        The value of the argument, or all arguments, or NULL
	 */
	public function argument($name=null)
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Retrieves the value of one or more CLI options.
	 *
	 * @param  string $name The name of the option to retrieve.
	 * @return mixed        The value of the option, all options, or NULL.
	 */
	public function option($name=null)
	{

	}

	//--------------------------------------------------------------------

	//--------------------------------------------------------------------
	// Ouput methods
	//--------------------------------------------------------------------

	/**
	 * Write normal text to the screen.
	 *
	 * Text appears the default color in your terminal.
	 *
	 * @param  [type] $msg [description]
	 * @return [type]      [description]
	 */
	public function write($msg)
	{

	}

	//--------------------------------------------------------------------


	/**
	 * Write an informational message to the screen.
	 * These should be short alerts to the users about possible caveats, etc
	 * with your command.
	 *
	 * Text will appear in a blue color.
	 *
	 * @param  string $msg The message to display
	 * @return [type]      [description]
	 */
	public function info($msg)
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Displays an error on the screen and halts the system.
	 *
	 * @param  string $msg The message to display
	 * @return [type]      [description]
	 */
	public function error($msg)
	{

	}

	//--------------------------------------------------------------------

	/**
	 * Asks the user a question and waits for the answer.
	 *
	 * @param  string $question The text of the question to ask.
	 * @return [type]           [description]
	 */
	public function ask($question, $options=null)
	{

	}

	//--------------------------------------------------------------------

}