<?php
defined('BASEPATH') or exit('No direct script access allowed');

class mdl_login extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function check_login()
    {
        if (trim($this->input->post('user_name')) && trim($this->input->post('user_password'))) {
            $user_name = trim($this->input->post('user_name'));
            $user_password = md5(trim($this->input->post('user_password')));

            //trim เช็คห้ามมีช่องว่างคำ

            $sql = $this->db->from('staff')
                ->where('username', $user_name)
                ->where('password', $user_password)
                ->where('verify', 1)
                ->where('status', 1)
                ->get();

            $number = $sql->num_rows();  //num_rows() นับจำนวนแถว

            if ($number == 1) {
                $row = $sql->row();
                if (strnatcmp($user_name, $row->USERNAME) == 0) {
                    $set_role = [];

                    $sql_role = $this->db->from('roles')
                        ->where('del is null')
                        ->get();
                    if($sql_role){
                        foreach($sql_role->result() as $row_role){
                            $php_array = (array) json_decode($row_role->PAGES);
                            $set_role[$row_role->NAME] = $php_array['data'];
                        }
                    }   

                    $result = array(
                        'error' => 0,
                        'data' => $sql->row(),
                        'set_role' => $set_role,
                    );
                } else {
                    $result = array(
                        'error' => 1,
                        'text' => 'ชื่อผู้ใช้ ไม่ถูกต้อง',
                        'data' => ''
                    );
                }
            } else {
                $result = array(
                    'error' => 1,
                    'text' => 'ไม่พบข้อมูล',
                    'data' => ''
                );
            }
        } else {
            $result = array(
                'error' => 1,
                'text' => 'กรุณากรอกข้อมูลให้ครบ',
                'data' => ''
            );
        }

        return $result;
    }
}
