# The Asset Pipeline

Bonfire implements a version of Ruby on Rails' Asset Pipeline to help you create faster sites, well, faster. When we refer to _asset_ we refer to any filetypes that Bonfire has to serve, but is typically css files, javascript files, and images.

The Asset Pipeline really exists to serve three primary purposes: precompile, concatenate, and minify assets into one central location. In other words, it takes your stylesheets, javascript files, images, and other files you want, joins them together when possible, and places them in the public/assets folder. This is all done to make your site faster by not requiring any more hits on the server than necessary, and serving the files up as static files in production.

## Asset Paths

By default, Bonfire will look for your assets in one of three possible asset paths:

<tt>application/assets</tt> is for assets that belong to your application as a whole, or is common to multiple custom modules that you don't want to duplicate across modules.

<tt>{module}/assets</tt> is for assets that a module might want to package with it. Each module can have its own set of assets that can be combined with all of the rest.

<tt>theme/{theme}/assets</tt> is for assets specific to a single theme.

While you can still place your assets within the <tt>public/assets</tt> folder, if the pipeline is enabled there is a possibility that this file will be overwritten during the compilation and caching process. If you intend to use the Pipeline, then your assets should stay within one of these 4 locations. Leave the <tt>public/assets</tt> for Bonfire to manage.

## Enabling the Asset Pipeline

To start using the Asset Pipeline you must first ensure that it is turned on in your <tt>application/config/application.php</tt> config file.

    $config['assets.enabled'] = TRUE;

## Configuring the Asset Location

Two elements control the location of your Assets in the system. By default, the path to access assets at is <tt>http://yoursite.com/assets</tt>. This folder name can be changed by follow these two steps;

- Rename the <tt>/public/assets</tt> folder to the desired path.
- Modify your <tt>config/routes.php</tt> file to set <tt>Route::create('assets/(:any)', 'bf_pipeline/$1');</tt> to the same as you renamed the folder in step one.

## Customizing the Compressors Used

You can change the compressors used within the Asset Pipeline by modifying the <tt>application.php</tt> config file.

    # Compressors to use
    $config['assets.js_compressor'] 	= 'Minify/JSMin::minify';
    $config['assets.css_compressor']	= 'Minify/Minify_CSS::minify';

To use your own compressor, you must follow these steps:

- Your libraries must be under the <tt>vendor/</tt> folder
- The name of the class must match the name of the file, excluding the file extension.
- After the :: is the name of the method to call. This must be a static method.

Using the css_compressor from above as an example. The file is located at <tt>vendors/Minify/Minify_Css</tt>. Inside that file is a class named <tt>Minify_CSS</tt> that we will call the <tt>minify</tt> static method of with the only parameter being the contents to minify.