<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Error_permit extends MY_Controller
{
    public function __construct()
	{
		parent::__construct();
	}

    public function index()
	{
        // $this->load->view('error_404');
        $this->template->set_layout('lay_main');
        $this->template->title('ใบลาออนไลน์');	
        $this->template->build('error_permit');
    }
}