<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class BF_AssetsTest extends CI_UnitTestCase {

	public function setUp()
	{
		BF_Assets::clear_all();
	}

	//--------------------------------------------------------------------

	public function test_is_loaded()
	{
		$this->assertTrue(class_exists('BF_Assets'));
	}

	//--------------------------------------------------------------------

	public function test_css_tag()
	{
		$test = BF_Assets::css_tag('style');
		$str = '<link rel="stylesheet" type="text/css" href="/assets/css/style.css" media="screen" />';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::css_tag('style.css');
		$str = '<link rel="stylesheet" type="text/css" href="/assets/css/style.css" media="screen" />';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::css_tag('http://cibonfire.com/assets/css/style.css');
		$str = '<link rel="stylesheet" type="text/css" href="http://cibonfire.com/assets/css/style.css" media="screen" />';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::css_tag('style.css', array('media' => 'all'));
		$str = '<link rel="stylesheet" type="text/css" href="/assets/css/style.css" media="all" />';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::css_tag('style.css', array('media' => 'print'));
		$str = '<link rel="stylesheet" type="text/css" href="/assets/css/style.css" media="print" />';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::css_tag('style.css', 'alt_style.css');
		$str = '<link rel="stylesheet" type="text/css" href="/assets/css/style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="/assets/css/alt_style.css" media="screen" />';
		$this->assertEqual(trim($test), trim($str));

	}

	//--------------------------------------------------------------------

	public function test_add_css()
	{
		BF_Assets::add_css('style.css', 'alt_style.css');

		$test = BF_Assets::css_tag();
		$str = '<link rel="stylesheet" type="text/css" href="/assets/css/style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="/assets/css/alt_style.css" media="screen" />';
		$this->assertEqual(trim($test), trim($str));
	}

	//--------------------------------------------------------------------

	public function test_js_tag()
	{
		$test = BF_Assets::js_tag('xmlhr');
		$str = '<script type="text/javascript" src="/assets/js/xmlhr.js"></script>';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::js_tag('xmlhr.js');
		$str = '<script type="text/javascript" src="/assets/js/xmlhr.js"></script>';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::js_tag('xmlhr.js', 'common.js');
		$str = '<script type="text/javascript" src="/assets/js/xmlhr.js"></script>
<script type="text/javascript" src="/assets/js/common.js"></script>';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::js_tag('module/xmlhr.js');
		$str = '<script type="text/javascript" src="/assets/js/module/xmlhr.js"></script>';
		$this->assertEqual(trim($test), trim($str));
	}

	//--------------------------------------------------------------------

	public function test_add_js()
	{
		BF_Assets::add_js('xmlhr.js', 'common.js');

		$test = BF_Assets::js_tag();
		$str = '<script type="text/javascript" src="/assets/js/xmlhr.js"></script>
<script type="text/javascript" src="/assets/js/common.js"></script>';
		$this->assertEqual(trim($test), trim($str));
	}

	//--------------------------------------------------------------------

	public function test_img_tag()
	{
		$test = BF_Assets::img_tag('icon');
		$str = '<img src="/assets/img/icon" alt="Icon" />';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::img_tag('icon.png');
		$str = '<img src="/assets/img/icon.png" alt="Icon" />';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::img_tag('icon.png', array('size' => '16x10', 'alt' => 'Edit Entry'));
		$str = '<img src="/assets/img/icon.png" width="16" height="10" alt="Edit Entry" />';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::img_tag('icons/icon.gif', array('size' => '16x16'));
		$str = '<img src="/assets/img/icons/icon.gif" width="16" height="16" alt="Icon" />';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::img_tag('icons/icon.gif', array('height' => '32', 'width' => 32));
		$str = '<img src="/assets/img/icons/icon.gif" width="32" height="32" alt="Icon" />';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::img_tag('icons/icon.gif', array('class' => 'menu_icon'));
		$str = '<img src="/assets/img/icons/icon.gif" alt="Icon" class="menu_icon" />';
		$this->assertEqual(trim($test), trim($str));
	}

	//--------------------------------------------------------------------

	public function test_is_uri()
	{
		$this->assertTrue(BF_Assets::is_uri('http://mysite.com'));
		$this->assertTrue(BF_Assets::is_uri('https://mysite.com'));
		$this->assertTrue(BF_Assets::is_uri('ftp://mysite.com'));
		$this->assertTrue(BF_Assets::is_uri('file://mysite.com'));
		$this->assertFalse(BF_Assets::is_uri('mysite.com'));
		$this->assertFalse(BF_Assets::is_uri('/mysite.com'));
	}

	//--------------------------------------------------------------------

	public function test_audio_tag()
	{
		$test = BF_Assets::audio_tag('sound.wav');
		$str = '<audio>
    <source src="/assets/audio/sound.wav" type="audio/wav">
    Your browser does not support the audio tag.
</audio>';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::audio_tag('/audio/sound.wav');
		$str = '<audio>
    <source src="/audio/sound.wav" type="audio/wav">
    Your browser does not support the audio tag.
</audio>';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::audio_tag('sound.wav', 'sound.mp3', 'sound.ogg');
		$str = '<audio>
    <source src="/assets/audio/sound.wav" type="audio/wav">
    <source src="/assets/audio/sound.mp3" type="audio/mpeg">
    <source src="/assets/audio/sound.ogg" type="audio/ogg">
    Your browser does not support the audio tag.
</audio>';
		$this->assertEqual(trim($test), trim($str));

		$test = BF_Assets::audio_tag('sound.wav', array('controls' => true, 'class' => 'audio'));
		$str = '<audio controls="controls" class="audio">
    <source src="/assets/audio/sound.wav" type="audio/wav">
    Your browser does not support the audio tag.
</audio>';
		$this->assertEqual(trim($test), trim($str));
	}

	//--------------------------------------------------------------------


}