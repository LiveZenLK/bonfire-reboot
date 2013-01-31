<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

define('BONFIRE_VERSION', '1.0-dev');

//--------------------------------------------------------------------
// Simple Autoloader
//--------------------------------------------------------------------
// This autoloader is not intended to provide huge amounts of flexibility.
// Instead, it is geared toward allowing some of the Bonfire core
// libraries to be autoloaded as needed for convenience.
//
function bf_autoloader($class)
{
	$supported_libs = array('Template', 'Assets', 'BF_Notices');

	if (!in_array($class, $supported_libs))
	{
		return;
	}

	$ci =& get_instance();
	$ci->load->library($class);
}

spl_autoload_register('bf_autoloader');

//--------------------------------------------------------------------
// Exceptions
//--------------------------------------------------------------------

class RenderException extends Exception {}