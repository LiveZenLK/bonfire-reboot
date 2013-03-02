# Basic Asset Handling

Assets in Bonfire work hard to help you take advantage of as much front-end performance techniques as we can, yet still keep a very flexible system. You can combine files into a single file, minify javascript or CSS files, and even fingerprint the name so you can use far-future expires headers to take advantage of browser caching.

The basic use of assets assumes that all assets are stored in the <tt>public/assets</tt> folder under the folder of their specific type.

    public/
        assets/
            css/
            js/
            img/
            audio/
            video/

When you use one of the assets tag methods (like <tt>css_tag()</tt>, <tt>js_tag()</tt>, <tt>img_tag()</tt>, etc) it will automatically build the filename to be in the correct folder based on file type (as determined by the file extension or the tag method used). You should note that this does NOT check that the file is actually there. That is completely your responsibility.


### The Assets Path

To change the folder that it will use for the asset base folder ('assets' by default), you can edit the <tt>config/constants.php</tt> file and change the value of the <tt>BF_ASSET_PATH</tt> value to be a folder within the <tt>/public</tt> folder. This constant is then used in the routes file to redirect assets and as the URL used to find assets at.

    define('BF_ASSET_PATH', 'assets');

### Backup Asset Paths

If your asset does not exist in the <tt>public/assets</tt> folder, then they will be looked for in several different locations. By default, Bonfire will look for your assets in one of three possible asset paths:

<tt>application/assets</tt> is for assets that belong to your application as a whole, or is common to multiple custom modules that you don't want to duplicate across modules.

<tt>{module}/assets</tt> is for assets that a module might want to package with it. Each module can have its own set of assets that can be combined with all of the rest.

<tt>theme/{theme}/assets</tt> is for assets specific to a single theme.

Any files found in the <tt>public/assets</tt> folder will take precedence over any of the other locations. The entire point of the backup folders is to provide a simple way to package assets in modules, your application, etc, while still providing the benefits of compressing and combining files. However, compressing and combining takes time so static assets are always preferred for performance. For this reason, in production environments, the system is set to automatically compile those files into the public assets folder so that you have a static file that can be served as fast as possible.

## The Pipeline

When a static asset is not found, the system goes through the Asset Pipeline it will go through several possible steps. All of these steps can be turned on or off in the application config file.

1. Search for the file in one of the Asset Paths
2. Parse the file for any files to combine
3. Combines the files into one
4. Minifies (compresses) the file for faster transfer
5. Compiles the file (with optional fingerprint) to the public assets folder.

### Customizing the Compressors Used

You can change the compressors used within the Asset Pipeline by modifying the <tt>application.php</tt> config file.

    # Compressors to use
    $config['assets.js_compressor']     = 'Minify/JSMin::minify';
    $config['assets.css_compressor']    = 'Minify/Minify_CSS::minify';

To use your own compressor, you must follow these steps:

- Your libraries must be under the <tt>vendor/</tt> folder
- The name of the class must match the name of the file, excluding the file extension.
- After the :: is the name of the method to call. This must be a static method.

Using the css_compressor from above as an example. The file is located at <tt>vendors/Minify/Minify_Css</tt>. Inside that file is a class named <tt>Minify_CSS</tt> that we will call the <tt>minify</tt> static method of with the only parameter being the contents to minify.

## Stylesheets

### <tt>css_tag()</tt>

This method creates links to css files in your <tt>public/assets/css</tt> folder. You can pass in as many stylesheet names as you wish. You can also include an array of name/value pairs to set different options for how it will handle your CSS file.

    echo BF_Assets::css_tag('style');	// Creates...
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css" media="screen">

    echo BF_Assets::css_tag('style.css');
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css" media="screen">

    echo BF_Assets::css_tag('http://cibonfire.com/assets/css/style.css');
    <link rel="stylesheet" type="text/css" href="http://cibonfire.com/assets/css/style.css" media="screen">

    echo BF_Assets::css_tag('style.css', array('media' => 'all'));
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css" media="all">

    echo BF_Assets::css_tag('style.css', array('media' => 'print'));
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css" media="print">

    echo BF_Assets::css_tag('style.css', 'alt_style.css');
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/assets/css/alt_style.css" media="screen">

You can also create links for all css files in your <tt>assets/css</tt> folder by passing in 'all' as the only file name. This will only grab the stylesheets in the roots of the <tt>css</tt> folder. Any subfolders will not be scanned.

    echo BF_Assets::css_tag('all');
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/assets/css/alt_style.css" media="screen">

If you want to include all of the stylesheets within the subfolders, also, then pass a <tt>'recursive' => true</tt> in the options array.

    echo BF_Assets::css_tag('all', array('recursive' => true));

When the <tt>css_tag</tt> method is used without any parameters, it will use all of the CSS files that have been added via the <tt>add_css()</tt> tag.

	BF_Assets::add_css('style.css', 'alt_style.css');

    echo BF_Assets::css_tag();
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/assets/css/alt_style.css" media="screen">

### Adding CSS Files

You can add CSS files that should have links rendered them with the <tt>add_css()</tt> method. You can provide as many file names as you'd like in a comma-delimited list. These can then have links rendered later by using the <tt>css_tag()</tt> with no parameters.

    BF_Assets::add_css('style.css', 'alt_style.css');

## Javascript

### js_tag()

Creates links to the Javascript files in your <tt>public/assets/js</tt> folder. You can pass in as many script names as you wish.

    echo BF_Assets::js_tag('xmlhr');  // Creates....
    <script type="text/javascript" src="/assets/js/xmlhr.js"></script>

    echo BF_Assets::js_tag('xmlhr.js');
    <script type="text/javascript" src="/assets/js/xmlhr.js"></script>

    echo BF_Assets::js_tag('xmlhr.js', 'common.js');
    <script type="text/javascript" src="/assets/js/xmlhr.js"></script>
    <script type="text/javascript" src="/assets/js/common.js"></script>

    echo BF_Assets::js_tag('module/xmlhr.js');
    <script type="text/javascript" src="/assets/js/module/xmlhr.js"></script>

You can create links for all javascript files in your <tt>assets/js</tt> folder by passing in 'all' as the only file name. This will only grab script files in the root of the <tt>js</tt> folder. Any subfolders will not be scanned.

    echo BF_Assets::js_tag('all');
    <script type="text/javascript" src="/assets/js/xmlhr.js"></script>
    <script type="text/javascript" src="/assets/js/common.js"></script>

If you want to include all of the script files within the subfolders also, then pass a <tt>'recursive' => true</tt> in the options array.

    echo BF_Assets::js_tag('all', array('recursive' => true));

When the <tt>js_tag()</tt> method is used without any parameters, it will use all of the JS files that have been added via the <tt>add_js()</tt> tag.

    BF_Assets::add_js('xmlhr.js', 'common.js');

    echo BF_Assets::js_tag();
    <script type="text/javascript" src="/assets/js/xmlhr.js"></script>
    <script type="text/javascript" src="/assets/js/common.js"></script>

### Adding Javascript Files

You can add javascript files that should have links rendered them with the <tt>add_js()</tt> method. You can provide as many file names as you'd like in a comma-delimited list. These can then have links rendered later by using the <tt>js_tag()</tt> with no parameters.

    BF_Assets::add_js('xmlhr.js', 'common.js');

## Images

You can use the <tt>img_tag()</tt> method to create links to any images that current reside in your public <tt>assets/img</tt> folder.

You can provide an array of key/value pairs to pass along several options to the image tag, including:

'alt' If no alt text is given, the file name part of the source is used (capitalized and without the extension)

'size' Supplied as "{width}x{height}" format, so "30x45" becomes <tt>width="30" height="45"</tt>. Size will be ignored if the value is not in the correct format.


     echo BF_Assets::img_tag('icon');
     <img src="/assets/img/icon" alt="Icon" />

     echo BF_Assets::img_tag('icon.png');
     <img src="/assets/img/icon.png" alt="Icon" />

     echo BF_Assets::img_tag('icon.png', array('size' => '16x10', 'alt' => 'Edit Entry'));
     <img src="/assets/img/icon.png" width="16" height="10" alt="Edit Entry" />

    echo BF_Assets::img_tag('icons/icon.gif', array('size' => '16x16'));
    <img src="/assets/img/icons/icon.gif" width="16" height="16" alt="Icon" />

    echo BF_Assets::img_tag('icons/icon.gif', array('height' => '32', 'width' => 32));
    <img src="/assets/img/icons/icon.gif" width="32" height="32" alt="Icon" />

    echo BF_Assets::img_tag('icons/icon.gif', array('class' => 'menu_icon'));
    <img src="/assets/img/icons/icon.gif" class="menu_icon" alt="Icon" />


## Audio

You can create HTML5 audio file links within your site with the <tt>audio_tag()</tt> method. By default this will look within the <tt>/assets/audio</tt> folder, though this can be overridden on a per-call basis by simply providing a full relatvie URl path in the call.

Any additional elements can be passed in the tag and will be included on the tag. If they have a value of 'true' then they will be displayed as 'key'='key' for backward compatibility.

    echo BF_Assets::audio_tag('sound.wav');
    <audio>
        <source src="/assets/audio/sound.wav" type="audio/wav">
        Your browser does not support the audio tag.
    </audio>

    echo BF_Assets::audio_tag('/assets/audio/sound.wav', 'sound.mp3');
    <audio>
        <source src="sound.wav" type="audio/wav">
        <source src="sound.mp3" type="audio/mpeg">
        Your browser does not support the audio tag.
    </audio>

    echo BF_Assets::audio_tag('/audio/sound.wav');
    <audio>
        <source src="/audio/sound.wav" type="audio/wav">
        Your browser does not support the audio tag.
    </audio>

    echo BF_Assets::audio_tag('sound.wav' array( 'autoplay' => true, 'controls' => true, 'class' => 'song',  ));
    <audio controls>
        <source src="/audio/sound.wav" type="audio/wav">
        Your browser does not support the audio tag.
    </audio>

By default, the string 'Your browser does not support the audio tag.' will be displayed for browsers that don't understand the HTML5 tag. You may customize this by passing in a string with additional content (like a flash-based player) as the <tt>inner_content</tt> key of the options array.

    echo BF_Assets::audio_tag('sound.wav' array( 'inner_content' => '...' ));
    <audio controls>
        <source src="/audio/sound.wav" type="audio/wav">
        ...
    </audio>


## RSS Feed Auto-Discovery

You can create a link that browsers and news readers can use to auto-detect an RSS or ATOM feed. The 'type' can be either 'rss' (default) or 'atom'. If no 'url' is provided in the options array, the current url is used instead.

    echo BF_Assets::auto_discovery_tag();
    <link rel="alternate" type="application/rss+xml" title="RSS" href="{current_url}" />

     echo BF_Assets::auto_discovery_tag( array('type' => 'atom') );
    <link rel="alternate" type="application/atom+xml" title="RSS" href="{current_url}" />

     echo BF_Assets::auto_discovery_tag( array('url'=>'/feed', 'title' => 'My RSS Feed') );
    <link rel="alternate" type="application/rss+xml" title="My RSS Feed" href="http://localhost/feed" />
