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

When the <tt>css_tag</tt> method is used without any parameters, it will use all of the CSS files that have been added via the <tt>add_css()</tt> and <tt>add_module_css()</tt> tags.

	BF_Assets::add_css('style.css', 'alt_style.css');

    echo BF_Assets::css_tag();
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css" media="screen">
    <link rel="stylesheet" type="text/css" href="/assets/css/alt_style.css" media="screen">

### Adding CSS Files

You can add CSS files that should have links rendered them with the <tt>add_css()</tt> method.