<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends BF_Controller {}

//--------------------------------------------------------------------

class Front_Controller extends BF_Controller {}

//--------------------------------------------------------------------

class Admin_Controller extends BF_Controller {

	protected $theme = 'admin';

    //--------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        // Load the minimum amount of helpers, libs and models to
        // make the admin work.
        $this->load->model('menus/menu_model');

    }

    //--------------------------------------------------------------------

}
// End Admin Controller

//--------------------------------------------------------------------