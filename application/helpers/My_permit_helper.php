<?php
error_reporting(E_ALL & ~E_NOTICE);

/**
 * 
 */
function check_session(string $module_name = null)
{

  $ci = &get_instance();
  $ci->load->database();
  # code...

  $role = $ci->session->userdata('role');

  $module = $module_name ? $module_name : $ci->uri->segment(1);
  $result = false;

  if ($ci->session->userdata('role') == 'admin') {
    $result = true;
  } else {
    if (is_numeric(array_search($module,$ci->session->userdata('set_role')[$role])) && $ci->session->userdata('set_role')[$role]) {
      $result = true;
    }
  }



  return $result;
}

function check_userlive()
{
  $ci = &get_instance();
  $ci->load->database();
  # code...
  $userid = $ci->session->userdata('user_code');

  $sql = $ci->db->from('staff')
  ->where('id',$userid)
  ->where('(status !=1 or verify is null)',null,false)
  ->get();
  $num = $sql->num_rows();
  if($num){
    redirect(site_url('error_permit'));
  }
  
}

function check_permit()
{
  $ci = &get_instance();
  $ci->load->database();
  # code...

  $result = check_session();

  if (!$result) {
    redirect(site_url('error_permit'));
  }
}

function check_permit_menu(string $module = null)
{
  $result = check_session($module);
  $css_name = '';

  if (!$result) {
    $css_name = 'd-none';
  }

  return $css_name;
}
