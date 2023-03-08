<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_login extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url', 'form');
        $this->load->model('mdl_login');

    }

    public function index()
    {
		if ($this->session->userdata('user_code'))
        {
            // User is logged in.  Do something.
            redirect(site_url('dashboard/ctl_dashboard'));
        }

        $this->load->view('login');
    }

    /**
     * 
     * * 
     * login staff
     * 
     */

    public function check_login()
    {
        $result = array();
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $result = $this->mdl_login->check_login();
            if ($result['error'] == 0) {
                if ($result['data']) {
                    $session = array(
                        'user_code' => $result['data']->ID,
                        'user_emp' => $result['data']->EMPLOYEE_ID,
                        'user_name' => $result['data']->NAME . " " . $result['data']->LASTNAME,
                        'permit' => '',
                        'role' => $result['data']->ROLE,
                        'set_role' => $result['set_role']
                    );
                    $this->session->set_userdata($session);

                    // keep log
                    log_data(array('login','',''));
                }
            }
        }
            
        echo json_encode($result);
    }

}
