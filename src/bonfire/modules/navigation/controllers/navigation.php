<?php

class Navigation extends CI_Controller {

	public function index()
	{
		$this->load->helper('url');

		redirect_to_route('profile');

		echo '<br/>Index';
	}

	//--------------------------------------------------------------------


}