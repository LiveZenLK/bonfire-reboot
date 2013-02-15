<a name="intro"></a>
## 1 How Bonfire Extends CodeIgniter

CodeIgniter provides a <tt>CI_Controller</tt> that is meant to be used as the basis for all of your own controllers. It handles the behind-the-scenes work of assigning class vars and the Loader so that you can access them. Bonfire extends this concept and provides 4 additional Controllers that can be used as base classes throughout your project. This helps you to keep from repeating code any more than necessary by providing a central place for many site-wide code to sit. For example, it makes a user object available that can be accessed from any controller, library, or view to know details about the current user. You can use it to set a custom theme for all of your public pages. And much more.

<a name="controllers"></a>
## 2 The Controllers

Each controller type is meant to server a specific purpose, but they are all easily adaptable to fit your needs. This file is meant to be customized for your application! Don't be afraid to edit it. That said, however, please be sure to back the file up during any upgrades of Bonfire.

<a name="base"></a>
### 2.1 Base_Controller

All of the custom controllers extend from the <tt>Base_Controller</tt>.  This class extends the MX_Controller which gives you all of the power of WireDesign’s [HMVC](https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc/wiki/Home) available to all of your classes.  That allows for a different way of working, but also a very powerful one, and one that is not necessary to use.

This controller is the place that you want to setup anything that should happen for every page of your site, like:

* Setup environment-specific settings, like turning the profiler on for development and off for production and testing.
* Get the cache setup correctly.  This is currently setup to only use a file-based cache, but you can easily tell it to use APC if available, and fallback to the file system if not.
* This controller also sets up [System Events](system_events.html) that will get executed just before and just after the Base_Controller’s constructor runs.

Some of the things that would normally be auto-loaded are handled here so that any AJAX controllers you may write don't need to process any of these other settings.

<a name="front"></a>
### 2.2 Front_Controller

The <tt>Front_Controller</tt> is intended to be used as the base for any public-facing controllers.  As such, anything that needs to be done for the front-end can be done here.

Currently, it simply sets the active theme.  You could also set the default theme here, if you create a parent theme ‘framework’ to use with all of your sites that you extend with child themes.


<a name="auth"></a>
### 2.3 Authenticated_Controller

This controller forms the base for the Admin Controller.  It was broken into two parts in case you needed to create a front-end area that was only accessible to your users, but that was not part of the Admin area and didn’t share the same themes, etc.  All changes you make here will affect your Admin Controller’s, though, so use with care.  If you need to, reset the values in the Admin Controller.

This controller currently...

* Loads in all of the user-associated models, like the user_model, permission_model, etc
* Restricts access to only logged in users
* Gets form_validation setup and working correctly with HMVC.


<a name="admin"></a>
### 2.4 Admin_Controller

The final controller sets things up even more for use within the Admin area of your site.  That is, the area that Bonfire has setup for you as a base of operations.  It currently...

* Sets the pagination settings for a consistent user experience.
* Gets the admin theme loaded and makes sure that some consistent CSS files are loaded so we don’t have to worry about it later.


<a name="create"></a>
## 3 Creating Controllers

Creating controllers in Bonfire is nearly identical to creating controllers in straight CodeIgniter. The only difference is the naming of some of the classes when you're dealing with the Administration side of Bonfire and [Contexts](contexts.html).

## 4 Rendering from Controllers

While the Template library handles most of the heavy-lifting of rendering your views and data, the BF_Controller does come with a few methods to help you render data out of your script.

### 4.1 <tt>render()</tt>

This method will load the Template library, if it's not already loaded, and take care of rendering your view, wrapped in the theme's layouts, out to the browser. It has the same effect as calling <tt>Template::set($data)</tt> followed by <tt>Template::render($layout)</tt>.

```html+php
class Blog extends Front_Controller {

	public function index()
	{
		$this->render();
	}
}
```

The first parameter is the name of the layout to use, if you want to use something other than <tt>default</tt>. The second parameter is the <tt>$data</tt> array. This is just a convenience factor for those accustomerd to working in CodeIgniter. The third parameter, if TRUE, will return the content to your script instead of rendering it out to the screen.


### 4.2 <tt>render_file()</tt>

This allows you to render an arbitrary view from anywhere on the file system. Primarily handy if you are running multiple applications on the same server and want to share some views, but you might find other uses, too.

```html+php
$this->render_file($path);
```

The first parameter is the full server path to the file you want to display in the browser. If you pass TRUE in the second parameter, it will wrap the file within the theme's index layout file.


### 4.3 <tt>render_text()</tt>

Renders a string of aribritrary text. This is best used during an AJAX call or web service request that are expecting something other then proper HTML. This sets the Content Type to <tt>text/plain</tt> and sends the text to the output class. This should be the last thing called in your script.

```html+php
$this->render_text($str);
```

The first parameter is the string of text to render to the screen. The second parameter, if TRUE, will use CodeIgniter's <tt>auto_typography</tt> function on the string of text before outputting it.


### 4.4 <tt>render_js()</tt>

Sends the supplied string to the browser with a MIME type of <tt>text/javascript</tt>. Do NOT do any further processing after this command or you may receive a Headers already sent error.

```html+php
$this->render_json($script);
```

The first parameter is a string with the javascript to render out.


### 4.5 <tt>render_realtime()</tt>

During the normal execution of a CodeIgniter script, your output is buffered. This is done for speed, enabling post-processing of your output and caching of your whole_page output. However, there are times when you do want your script to output information to the browser as it happens, not waiting for the script to end. This is most helpful for long-running scripts that you need to be able to provide immediate feedback on so that people know it's actually doing something, like cronjobs.

Within your controllers you can call <tt>render_realtime()</tt> method. Any output after that (echo, sprintf, etc) will be output directly to the browser.

```html+php
$this->render_realtime();
echo "My great content.";
```


### 4.6 <tt>render_json()</tt>

Converts the provided array or object to JSON, sets the proper MIME type, and outputs the data. This should be the last thing called in your script.

```html+php
$this->render_json($array);
```


The only parameter is the data that will be converted to JSON.



### 4.7 <tt>get_json()</tt>

While not a render helper, the <tt>get_json()</tt> method works as the counterpart to <tt>render_json</tt> method.

When you pass data to your application from a javascript method, such as jQuery's <tt>.post()</tt> method, and specify the content type as JSON, this method provides a handy way to get at that data, by reading the <tt>php://input</tt> stream, decoding the json data and handing it over to you.

```html+php
$this->get_json();
```

The first parameter allows you to specify the format of the data you would like returned. Valid options are 'object' (default), or 'array'. The second parameter allows you to limit the number of levels deep within the object you wish to decode. Default is the maximum value of 512 levels deep.

## 5 Caching

BF_Controller will always load up the cache driver so it's ready for use throughout your application. You should consider using the cache driver where appropriate throughout your application, even if you don't think you need to worry about caching at this point. That way, should you find a need for caching down the line, it will already be in place and you only need to change the caching type in your controller.

Bonfire defaults to loading the <tt>dummy</tt> cache driver, with a <tt>file</tt> cache backup. This means that, throughout development, the cache driver will be loaded and ready for use, but nothing will ever be cached. When you find that you need caching, you have two simple class properties that you can specify in your controllers to set it up for you.

```html+php
class Some_Controller extends BF_Controller {

	protected $cache_type  	= 'apc';
	protected $backup_cache = 'file';

}
```

The <tt>$cache_type</tt> var specifies the primary cache driver to use. The <tt>$backup_cache</tt> specifies which driver to use when that driver is not available (like memcached crashed, etc.).
