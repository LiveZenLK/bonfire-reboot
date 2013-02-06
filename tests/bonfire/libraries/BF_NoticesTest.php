<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class BF_NoticesTest extends CI_UnitTestCase {

	public function setUp()
	{
		BF_Notices::clear_all();
	}

	//--------------------------------------------------------------------

	public function test_it_loads()
	{
		$this->assertTrue(class_exists('BF_Notices'));
	}

	//--------------------------------------------------------------------

	public function test_set_stores()
	{
		BF_Notices::set('My first message.', 'success', 'tests');

		$msg = BF_Notices::all('tests');

		$this->assertEqual($msg[0]['msg'], 'My first message.');
		$this->assertEqual($msg[0]['status'], 'success');
		$this->assertEqual($msg[0]['group'], 'tests');
	}

	//--------------------------------------------------------------------

	public function test_group_gets_only_group()
	{
		BF_Notices::set('One', 'success', 'group1');
		BF_Notices::set('Two', 'success', 'group2');

		$msgs = BF_Notices::group('group1');

		$this->assertEqual(count($msgs), 1);
		$this->assertEqual($msgs[0]['msg'], 'One');
	}

	//--------------------------------------------------------------------

	public function test_status_gets_only_status()
	{
		BF_Notices::set('One', 'success', 'group1');
		BF_Notices::set('Two', 'failure', 'group2');

		$msgs = BF_Notices::status('success');

		$this->assertEqual(count($msgs), 1);
		$this->assertEqual($msgs[0]['msg'], 'One');
	}

	//--------------------------------------------------------------------

	public function test_all_gets_all()
	{
		BF_Notices::set('One', 'success', 'group1');
		BF_Notices::set('Two', 'failure', 'group2');

		$msgs = BF_Notices::all();

		$this->assertEqual(count($msgs), 2);
		$this->assertEqual($msgs[0]['msg'], 'One');
	}

	//--------------------------------------------------------------------

	public function test_groups_gives_count()
	{
		BF_Notices::set('One', 'success', 'group1');
		BF_Notices::set('Two', 'failure', 'group2');
		BF_Notices::set('Three', 'info', 'group2');

		$groups = BF_Notices::groups();

		$this->assertEqual(count($groups), 2);
		$this->assertEqual($groups['group2'], 2);
		$this->assertEqual($groups['group1'], 1);
		$this->assertIsA($groups['group2'], 'int');
	}

	//--------------------------------------------------------------------

	public function test_sort_by_time()
	{
		BF_Notices::set('One', 'success', 'group1');
		sleep(1);
		BF_Notices::set('Two', 'failure', 'group2');
		sleep(1);
		BF_Notices::set('Three', 'info', 'group2');

		BF_Notices::sort('time', 'asc');

		$msgs = BF_Notices::all();

		$this->assertEqual($msgs[0]['msg'], 'One');

		BF_Notices::sort('time', 'desc');

		$msgs = BF_Notices::all();

		$this->assertEqual($msgs[0]['msg'], 'Three');
	}

	//--------------------------------------------------------------------

	public function test_sort_by_group()
	{
		BF_Notices::set('One', 'success', 'group1');
		BF_Notices::set('Two', 'failure', 'group2');
		BF_Notices::set('Three', 'info', 'group2');

		BF_Notices::sort('group', 'asc');

		$msgs = BF_Notices::all();

		$this->assertEqual($msgs[0]['msg'], 'One');

		BF_Notices::sort('group', 'desc');

		$msgs = BF_Notices::all();

		$this->assertEqual($msgs[0]['msg'], 'Three');
		$this->assertEqual($msgs[1]['msg'], 'Two');
	}

	//--------------------------------------------------------------------

	public function test_sort_by_status()
	{
		BF_Notices::set('One', 'success', 'group1');
		BF_Notices::set('Two', 'failure', 'group2');
		BF_Notices::set('Three', 'info', 'group2');

		BF_Notices::sort('status', 'asc');

		$msgs = BF_Notices::all();

		$this->assertEqual($msgs[0]['status'], 'failure');

		BF_Notices::sort('status', 'desc');

		$msgs = BF_Notices::all();

		$this->assertEqual($msgs[0]['status'], 'success');
	}

	//--------------------------------------------------------------------
}