<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends BF_Controller {}

//--------------------------------------------------------------------

class Front_Controller extends BF_Controller {}

//--------------------------------------------------------------------

class Admin_Controller extends BF_Controller {

	protected $theme = 'admin';
}
// End Admin Controller

//--------------------------------------------------------------------