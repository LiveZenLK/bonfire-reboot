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
		$this->assertTrue(1);
	}

	//--------------------------------------------------------------------

}