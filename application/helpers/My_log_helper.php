<?php 
error_reporting(E_ALL & ~E_NOTICE);

/**
 * log message
 * @param array @array = 
 *      type = event controller [login,logout,select,insert,update,delete,load] 
 *      event = event from database [select,insert,update,delete,truncate]  
 *      query = query data
 */
function log_data(array $array = array())
{
    $ci =& get_instance();
    $ci->load->database();
      # code...
      $type = $array[0]? $array[0] : "";
      $event = $array[1] ? $array[1] : "";
      $query = $array[2] ? $array[2] : "";
      
      $log_data = '{"user_id": "'.$ci->session->userdata('user_code').'","user_ip": "'.$_SERVER['REMOTE_ADDR'].'","user_data": {"type": "'.$type.'","event": "'.$event.'","url": "'.current_url().'","query": "'.$query.'"},"user_agent": "'.$ci->input->user_agent().'"}';
      log_message('all', $log_data);  
}
