<?php

//--------------------------------------------------------------------
// Module Locations
//--------------------------------------------------------------------
// These paths are checked in the order listed whenever a module is
// attempting to be located, whether it's loading a library, helper,
// or routes file.
//
	$config['modules_locations'] = array(
		APPPATH .'modules/', 		// application/modules
		BFPATH .'modules/'			// bonfire/modules
	);

//--------------------------------------------------------------------
// !TEMPLATE
//--------------------------------------------------------------------

/*
|--------------------------------------------------------------------
| SITE PATH
|--------------------------------------------------------------------
| The path to the root folder that holds the application. This does
| not have to be the site root folder, or even the folder defined in
| FCPATH.
|
*/
$config['template.site_path']	= FCPATH;

/*
|---------------------------------------------------------------------
| THEME PATHS
|---------------------------------------------------------------------
| An array of folders to look in for themes. There must be at least
| one folder path at all times, to serve as the fall-back for when
| a theme isn't found. Paths are relative to the FCPATH.
*/
$config['template.theme_paths'] = array('themes');

/*
|--------------------------------------------------------------------
| DEFAULT LAYOUT
|--------------------------------------------------------------------
| This is the name of the default layout used if no others are
| specified.
|
| NOTE: do not include an ending ".php" extension.
|
*/
$config['template.default_layout'] = "index";

/*
|--------------------------------------------------------------------
| DEFAULT THEME
|--------------------------------------------------------------------
| This is the folder name that contains the default theme to use
| when 'template.use_mobile_themes' is set to TRUE.
|
*/
$config['template.default_theme']	= 'default/';

/*
|--------------------------------------------------------------------
| PARSE VIEWS
|--------------------------------------------------------------------
| If set to TRUE, views will be parsed via CodeIgniter's parser.
| If FALSE, views will be considered PHP views only.
|
*/
$config['template.parse_views']		= FALSE;

/*
|--------------------------------------------------------------------
| ALLOW CONTROLLER BASED VIEWS
|--------------------------------------------------------------------
| If set to TRUE, Bonfire will search for theme layouts in files
| with names that match the name of the controller, before trying
| the index.php layout.
|
| IE - The Books controller would look in the theme for a layout file
| named books.php.
|
*/
$config['template.allow_controller_layouts'] = TRUE;

/*
|--------------------------------------------------------------------
| MESSAGE TEMPLATE
|--------------------------------------------------------------------
| This is the template that Ocular will use when displaying messages
| through the message() function.
|
| To set the class for the type of message (error, success, etc),
| the {type} placeholder will be replaced. The message will replace
| the {message} placeholder.
|
*/
$config['template.message_template'] =<<<EOD
 <div class="alert alert-block alert-{type} fade in notification">
		<a data-dismiss="alert" class="close" href="#">&times;</a>
		<div>{message}</div>
	</div>
EOD;

