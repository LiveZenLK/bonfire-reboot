# Modifications to CodeIgniter

## index.php

A few small changes have been made to the main <tt>index.php</tt> file that ships with CodeIgniter and is used as the base for launching your application.

### <tt>BFPATH</tt>

A new constant, <tt>BFPATH</tt>, is available that points to the root of Bonfire's specific code. By default this is <tt>/bonfire/</tt>.

## Codeigniter.php

The <tt>BF_Controller</tt> file is loaded just before the MY_Controller class would be loaded so that it's available to be extended by application files. Around line 236

    // start BONFIRE modifications
	if (file_exists(BFPATH.'core/BF_Controller.php'))
	{
		require BFPATH.'core/BF_Controller.php';
	}
	// end BONFIRE modifications

## Common.php

The <tt>load_class()</tt> method has been modified to allow it to search for core files not only in the Codeigniter folder and the application folder, but also in the bonfire folder. To allow graceful overriding in the application, the path is first searched in APPPATH, then BFPATH, then BASEPATH.

We also search in the bonfire/ folder for BF_ files. These files are like MY_Model, etc, but are provided with class names prefixed with BF_ so that your MY_ files can extend them.

## Router.php

The Router class has been completely replaced with a modified version that includes ideas and code from segersjens/CodeIgniter-HMVC-Modules and Laravels Router. By replacing the file, we get a slight performance increase, and increased functionality.