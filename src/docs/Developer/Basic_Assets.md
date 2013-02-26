# Basic Asset Handling

Bonfire provides a helper library that assists in building link tags for many different types of assets. It also can work with the [Asset Pipeline](/admin/help/Developer.Assets_Pipeline) to combine and minify your assets.

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

