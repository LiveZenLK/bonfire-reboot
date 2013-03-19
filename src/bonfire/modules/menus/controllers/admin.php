<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Menus Module - Admin Controller
 */
class Admin extends Admin_Controller {

	//--------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        $this->lang->load('menus');
    }

    //--------------------------------------------------------------------

	public function index()
	{
        $this->load->helper('form');

		$this->render();
	}

	//--------------------------------------------------------------------


}