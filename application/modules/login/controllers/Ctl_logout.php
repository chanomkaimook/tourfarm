<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_logout extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    public function index()
    {

        // keep log
        log_data(array('logout','',''));

        $this->session->sess_destroy();
        redirect(site_url('login/ctl_login/'));
    }

}
