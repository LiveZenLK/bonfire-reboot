<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Help Module - Admin Controller
 */
class Admin extends BF_Controller {

	protected $theme = 'admin';

	//--------------------------------------------------------------------

	public function index()
	{
		$this->render();
	}

	//--------------------------------------------------------------------

}