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
|---------------------------------------------------------------------
| THEME PATHS
|---------------------------------------------------------------------
| An array of folders to look in for themes. There must be at least
| one folder path at all times, to serve as the fall-back for when
| a theme isn't found. Paths are relative to the FCPATH.
*/
$config['template.template_paths'] = array('themes');

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
$config['template.default_theme']	= 'default';




//--------------------------------------------------------------------
// ASSETS
//--------------------------------------------------------------------

/*
|--------------------------------------------------------------------
| Asset Pipeline
|--------------------------------------------------------------------
| When TRUE the full Asset Pipeline will be in play, which includes
| compiling, joining, and minifying your assets, like CSS, JS and images.
|
| When FALSE, the assets will be served up one at time with no precompilation.
| This may be more appropriate for smaller or less complex sites, but large
| applications will benefit from the speed and simplicity of using the Pipeline.
|
*/

# Should we compress assets?
$config['assets.compress'] 			= FALSE;

# Diplay files as single files?
$config['assets.debug'] 			= TRUE;

# If the static asset file is not foudnd, but the file is found
# in one of the asset paths, should we compile it to a static asset?
$config['assets.compile'] 			= FALSE;

# Fingerprint asset names to work with far future expires
$config['assets.fingerprint'] 		= FALSE;

# Compressors to use
$config['assets.js_compressor'] 	= 'Minify/JSMin::minify';
$config['assets.css_compressor']	= 'Minify/Minify_CSS::minify';
