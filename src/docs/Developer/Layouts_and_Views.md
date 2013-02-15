## 1 Overview: How the Pieces Fit Together

This guide covers how Bonfire handles rendering views within your application. It presents the relationship between standard CodeIgniter views and how they fit into the overall Theme.

In a straight CodeIgniter application you might be used to having a view for your header and a view for your footer. Then, in each view file, you would use <tt>$this->load->view('header')</tt> to keep a consistent layout across your site. This works well, and is one of the fastest options available to you. But it's not the most efficient or flexible from a developer's viewpoint.

Bonfire uses Themes, which you'll be accustomed to using in nearly any CMS out there. Each theme contains one or more layouts (one-column, two-column, blog, etc) as well as any assets (like CSS or Javascript files) that the theme might need. A single command in your controller is all that is needed to make this work.

Views are exactly what you are accustomed to working with, but follow a simple convention that keeps things consistent among all of the developers on your team, and keeps things simple and organized.


<a name="organize"></a>
## 2 Views

Views in Bonfire are the same views that are available within stock CodeIgniter. They are just organized to take advantage of conventions that make keeping which view belongs to which method much simpler and consistent across projects and between co-workers. Since most of the work that you will be doing will be focused within modules, all of the examples will use a <tt>blog</tt> module as an example. However, these same principles apply directly to the <tt>application/views</tt> folder itself.

<a name="render"></a>
### 2.1 Using <tt>render</tt>

Wherever you would have used CodeIgniter's <tt>$this->load->view()</tt> previously, you should use <tt>Template::render()</tt> instead. It displays a view, wrapped within your themed layout files, checks if it's an AJAX or mobile request, and handles it all without you having to do much. At its simplest, all you have to do to display a fully-themed page from within your controller is:

```html+php
class Blog extends Front_Controller {

	public function index()
	{
		Template::render();
	}

}
```

If your class extends from BF_Controller, or any of the other custom child classes that are provided (like Base_Controller or Authenticated Controller), you can use the following shortcut to render a view and it’s layout:

```html+php
class Blog extends Front_Controller {

	public function index()
	{
		$this->render();
	}
}
```

The first parameter is the name of the layout to use, if you want to use something other than <tt>default</tt>. The second parameter is the <tt>$data</tt> array. This is just a convenience factor for those accustomerd to working in CodeIgniter. The third parameter, if TRUE, will return the content to your script instead of rendering it out to the screen.

See the Controllers documentation for more <tt>render</tt> related utility methods.

<a name="render-method"></a>
### 2.2 Rendering a Method's View

Bonfire's template system favors certain conventions to make your job easier. When you use the <tt>render()</tt> method it looks for a view with the same name as the active method.

All public context views (those front-facing pages) will be found in your module's <tt>/view</tt> folder. All other contexts (content, settings, etc) should have a folder matching the context name, and the view would be inside of that folder.

```html+php
/*
	Public Context

	View would be found at:
		module_name/
			views/
				index.php
*/
class Blog extends Front_Controller {

	public function index()
	{
		Template::render();
	}

}
```

```html+php
/*
	Settings Context

	View would be found at:
		module_name/
			views/
				settings/
					index.php
*/
class Settings extends Admin_Controller {

	public function index()
	{
		Template::render();
	}

}
```

<a name="render-view"></a>
### 2.3 Rendering an Arbitrary View

Sometimes you will need to use a different view than what Bonfire defaults to, such as when you want to share a form between your <tt>create</tt> and <tt>edit</tt> methods. This can be handled by using the <tt>set_view()</tt> method of the Template library. The only parameter is the name of the view file to display. The view name is relative to the module's view folder.

```html+php
class Settings extends Admin_Controller {

	public function create()
	{
		Template::set_view('settings/post_form');
		Template::render();
	}

}
```

<a name="theme-views"></a>
### 2.4 Rendering Theme Views

Often you will want to display one of the files from your theme within one of your view files. This is most often done within one of your layouts (see below) but can be done from within any view. You would accomplish this by using the <tt>theme_view()</tt> helper function. You don't need to load any helpers to make this happen, it is ready for you when your Template library is loaded.

The first parameter is the name of the view to display. This view is relative to your active theme's folder.

```html+php
echo theme_view('recent_posts');
```

The second parameter is an optional array of data to pass to the view. This functions exactly like the second parameter of CodeIgniter's <tt>$this->load->view()</tt> method. Many times this won't be necessary, as any view data that is already available should be usable by any theme views also.

```html+php
echo theme_view('recent_posts', array('posts' => $posts));
```

By default, this method will check to see if the user is viewing this from a mobile device (like a smartphone or tablet). If they are, then it will first look for a view that is prefixed with <tt>mobile_</tt>.

```html+php
/*
	Would look for a file called: mobile_recent_posts
	in the active theme folder.
*/
echo theme_view('recent_posts');
```

If you want to ignore the mobile version (like when they have said they want to view the full site anyway) you may pass <tt>TRUE</tt> in as the third parameter.

```html+php
/*
	Would look for a file called: recent_posts
	in the active theme folder, even if they
	were viewing on a mobile device.
*/
echo theme_view('recent_posts', null, true);
```

<a name="parsing"></a>
### 2.5 Parsing Views

Bonfire assumes that you are using PHP as your template language. However, you can still use CodeIgniter's built-in <tt>parser</tt> library to display your views instead of letting it parse straight PHP. This can be turned on and off with the Template library's <tt>parse_views()</tt> method.

```html+php
Template::parse_views(TRUE);
```

Once this command has been called, all views from this point on would use the parser.

<a name="themes"></a>
## 3 Themes

Bonfire supports a very flexible theme engine that allows for common layouts to wrap around your views, as well as more complex parent/child relationships between themes. Themes can include their own assets (CSS, Javascript and images), as well as multiple layout files. They can also providing layout switching based on the controller that's currently being ran.

<a name="layouts"></a>
### 3.1 Layouts

A layout is a view file that is contains common content that you want displayed on each page. This is commonly used to create consistent headers and footers across your site.

You can also have layouts for two- and three-column (or more!) formats that are ready to be used within any controller in your site.

<a name="layouts-default"></a>
### 3.2 Default Layouts

When you display a view using <tt>Template::render()</tt> Bonfire will use a layout called <tt>index.php</tt>. This is the base layout that is used across your site.

    themes/
    	my_theme/
    		index.php

<a name="layouts-other"></a>
### 3.3 Using Arbitrary Layouts

If a page requires a different layout than the default ones, say to display a two-column layout, you can easily choose a different layout to use with the <tt>render()</tt> method.

```html+php
class Blog extends Front_Controller {

	public function index()
	{
		Template::render('two_column');
	}

}
```

You can also set a layout to be used by an entire controller by directly tapping into the Template library's class variables.

```html+php
class Blog extends Front_Controller {

	public function __construct()
	{
		Template::layout = 'two_column';
	}

}
```

This is best used when you want this controller to use a layout that is shared with other controllers.

<a name="controllers"></a>
### 3.4 Controller-Based Layouts

If you have a controller that needs it's own layout all you need to do is to create a new layout in your theme folder with the same name as your controller.

```html+php
/*
	Uses the blog layout file at:

		themes/
			my_theme/
				blog.php
*/
class Blog extends Front_Controller {

	public function index()
	{
		Template::render();
	}

}
```

<a name="parent-child"></a>
### 3.5 set_theme()

The first parameter should be the name of the active (or primary) theme to use. This is the one that will be looked into first when trying to display any themed files.  If you have not specified a theme to use, Bonfire will look for a theme named <tt>default</tt>.  This default theme can be changed in the application config file.

```html+php
Template::set_theme('my_theme', 'default');
```


<a name="yield"></a>
### 3.6 Understanding Yield

In your theme's layout files, you can specify where the controller's views are set to display by using the <tt>Template::yield()</tt> method.

```html+php
<?php echo Template::block('header') ?>

	<?php echo Template::yield() ?>

<?php echo Template::block('header') ?>
```

### 3.7 The <tt>content_for()</tt> Method

Yields can also be used to assign content in your view file that will be made available to the layout files. For example, you might want to reserve a portion of your footer template that allows each view to insert new script tags. Using <tt>yield()</tt> and <tt>content_for()</tt> makes this simple.

In your footer.php layout:

    <?php echo Template::yield('foot_scripts'); ?>

By providing a name as the first parameter to the yield() method, you instruct the Template class to look for content that has been passed from your views. You would do that with the <tt>content_for</tt> method in your view file:

    <?php Template::content_for('foot_scripts'); ?>
    	. . . Your script tag would go here . . .
    <?php Template::end_content_for('foot_scripts'); ?>

Note that the name used in the <tt>yield</tt> method must match the name provided in the <tt>content_for</tt> and <tt>end_content_for</tt> tags.


<a name="blocks"></a>
## 4 Blocks

Blocks allow you to set aside room for content within your layouts. It can be used as a placeholder for content that varies across layouts, like a sidebar. On a two-column layout you can set aside an area for the sidebar content, but then define what content is used there in each controller or method, allowing each page to be customized without needing additional layouts.

```html+php
Template::block('block_name', 'default_view');
```

The first parameter is a name that the block can be referenced by when you set the view to use in your controller. The second optional parameter is the name of a view to use as a default, in case other content has not been set.

If you need to pass a specific set of data to the block's view, you can pass an array of key/value pairs as the third parameter.

```html+php
Template::block('block_name', 'default_view', $data);
```

Sometimes you will want to keep all of the various files that can be used in this block within your theme. In this case, you can pass TRUE as the fourth parameter. Instead of looking within your module's view folder, it will look within the theme folder.

```html+php
Template::block('block_name', 'default_view', $data, true);
```

To set a different view to use in that block, you would use the <tt>Template::set_block()</tt> method within your controller. The first parameter is the name of the block to set the content for. The second parameter is the name of the view file to use. By default, this will look within your module's view folder.

```html+php
Template::set_block('sidebar', 'blog_sidebar');
```


<a name="helpers"></a>
## 5 Helper Functions

The Template library contains several other, smaller, functions and methods that change the way you might be used to using standard CodeIgniter features, as well as some to aid with building out your navigation.

<a name="data"></a>
### 5.1 View Data

Instead of using a $data array to pass content to your view, or using <tt>$this->load->vars()</tt>, you can use <tt>Template::set()</tt> to make the data usable within the view. This should follow the same format that you are used to using, namely being an array of key/value pairs.

```html+php
// Instead of...
$data = array(
	'title'	=> 'Page title'
);
$this->load->view('index', $data);

// You should use...
Template::set('title', 'Page title');
Template::render();
```

In order to make the transition from a familiar CodeIgniter practice to Bonfire simpler, you can pass an array in as the only parameter.

```html+php
$data = array(
	'title'	=> 'Page title'
);
Template::set($data);
```

Within your views, you can access the variables with a name that matches the key of the array pairs.

<a name="messages"></a>
### 5.2 Flash Messages

CodeIgniter's Session library provides flash messages that allow you to set short status messages in your views. This is commonly used for small notifications when your object saved successfully. The only limitation this has is that it only is available on a page reload, which can soon become costly due to the sheer number of server hits it creates.

Bonfire provides a variation on this that is available both to the current page as well as the next page load. To use it you would call the <tt>set_message()</tt> method within your controllers. The first parameter is the text to be displayed. The second parameter is the class name that will be given to the output string.

```html+php
Template::set_message('Your message displayed just fine.', 'success');
```

To display the message, use the <tt>message()</tt> method within your view.

```html+php
Template::message();
```

The code is wrapped within HTML that you can define in the <tt>application.php</tt> config file. It defaults to a Bootstrap compatible alert message.

```html+php
$config['template.message_template'] =<<<EOD
 <div class="alert alert-block alert-{type} fade in notification">
		<a data-dismiss="alert" class="close" href="#">&times;</a>
		<div>{message}</div>
	</div>
EOD;
```

If multiple messages are passed into the system, they are combined and spit out as one, using the <tt>type</tt> of the last message passed in.

<a name="redirect"></a>
### 5.3 Redirect

When you are using AJAX a lot in your application, you will find times when CodeIgniter's stock <tt>redirect()</tt> method will not work for you, since it redirects within the AJAX call. What happens when you want to completely break out of the AJAX call and do a complete page refresh? You can use <tt>Template::redirect('some/other/page')</tt> instead. This returns an empty file that simply has a Javascript redirect code inserted.

The only parameter is a URL (either absolute or relative) to redirect the user to.

```html+php
Template::redirect('users/profile');
```

<a name="paths"></a>
### 5.4 Theme Paths

By default, all of your application's themes are stored within <tt>bonfire/themes</tt>. There may be times, though, when you need to have multiple locations to store your theme files. This might be handy when you ship a forums module and want to provide themes specifically for your forums in their own location.

You can add a new path to look for themes with the <tt>add_theme_path()</tt> method. The only parameter is the path to your theme folder. This must be relative to your application's web root (FCPATH).

```html+php
Template::add_theme_path('user_themes');
```

If you only need that path there temporarily, you can provide a slight performance increase by removing the path when you're done with it.

```html+php
Template::remove_theme_path('user_themes');
```

If you want to permanently add that path to your application, you can set this in the <tt>application.php</tt> config file.

```html+php
$config['template.theme_paths'] = array('user_themes', 'bonfire/themes');
```

<a name="menus"></a>
### 5.5 Working With Menus

The template library contains two functions to ease working with navigation items and setting the active link within the menu.

**<tt>check_class()</tt>**

The <tt>check_class()</tt> function checks the passed in url segments against the controller that is running. The first parameter is the name of the class to check against. If you wanted to highlight the Blog link in your main application and your controller was named 'blog' you could use:

```html+php
<a href="/blog" <?php echo check_class('blog') ?>>Blog</a>

// Outputs:
<a href="/blog" class="active">Blog</a>
```

If you already have other classes applied to the link and just want the word <tt>active</tt> output, you can pass TRUE as the second parameter.

```html+php
<a href="/blog" class="dropdown <?php echo check_class('blog', TRUE) ?>">Blog</a>

// Outputs:
<a href="/blog" class="dropdown active">Blog</a>
```


**<tt>check_method()</tt>**

Check method works the same as <tt>check_class()</tt> but compares it to the active method running within your controller. The parameters are identical.