<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/* load the MX_Router class */
require APPPATH . "third_party/MX/Controller.php";

class MY_Controller extends MX_Controller
{	

	function __construct() 
	{
		parent::__construct();
		$this->_hmvc_fixes();
	}

	public function middleware()
    {
        $this->is_logged_in();

		$this->is_permit_in();

		$this->is_alive_in();
    }
	
	public function is_logged_in()
    {
        $user = $this->session->userdata('user_code');
		if (!isset($user))
        {
            // User is logged in.  Do something.
            redirect(site_url('login/ctl_login'));
        }
    }


	public function is_permit_in()
	{
		$this->load->helper('My_permit');
		return check_permit();
	
	}
	public function is_alive_in()
	{
		$this->load->helper('My_permit');
		return check_userlive();
	}

	function _hmvc_fixes()
	{		
		//fix callback form_validation		
		//https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc
		$this->load->library('form_validation');
		$this->form_validation->CI =& $this;
	}

}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */
