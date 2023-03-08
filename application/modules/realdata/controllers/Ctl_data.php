<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ctl_data extends MY_Controller
{

    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('mdl_catagory','mdl_docnode'));
        $this->load->library('image');

        // $this->middleware();

        // setting
        $this->model = $this->mdl_catagory;
    }

    public function get_doc_waite()
    {
        $total = $this->mdl_docnode->get_count_waite();
        $returns = array(
            'total' => $total
        );

        echo json_encode($returns);
    }
}
