<?php
defined('BASEPATH') or exit('No direct script access allowed');

class mdl_calendar extends CI_Model

{

    private $allowedFileType;
    private $path_dir;


    public function __construct()
    {
        parent::__construct();

        $this->allowedFileType = array('jpg', 'png', 'jpeg', 'JPG', 'gif', 'GIF');
        $this->path_dir = FCPATH . 'asset/image/';
    }

    // private $path = 'asset/image';


    public function get_data_leave_type()
    {
        $query = $this->db->select('*')
            ->get('leave_type');

        return $query->result();
    }

    public function get_data_time_s_e()
    {
        $query = $this->db->select('*')
            ->get('time');

        return $query->result();
    }


    public function get_data_emp()
    {
        $query = $this->db->select('*')
            ->get('emp_type');

        return $query->result();
    }

    public function get_data_leave()
    {
        $query = $this->db->select('*')
            ->get('leave');

        return $query->result();
    }

    public function get_data_calendar()
    {
        $show = 0;
        $id = $this->session->userdata('user_code');
        $emp_id = $this->session->userdata('user_emp');

            $sql = $this->db->select('*')
                ->from('calendar')
                ->where('calendar.status', 1);
                
                if($this->session->userdata('role') != 'admin'){
                    $sql->where('(calendar.user_staff_id ='.$id.' or calendar.owner1='.$emp_id.' or calendar.owner2='.$emp_id.' or calendar.owner3='.$emp_id.')');
                }

                $query = $sql->get();

            $data = [];
            foreach ($query->result() as $row) {
                $data_img = [];
                $query_img = $this->db->select('*')
                    ->from('calendar_img')
                    ->where('calendar_img.calendar_id', $row->ID)
                    ->get();

                foreach ($query_img->result() as $rows) {


                    $data_img[] = array(
                        'id' => $rows->ID,
                        'name' => base_url('/asset/image/' . $rows->IMAGE),
                    );
                }

                $item = [];
                // $data[] = (object) array('id' => '1');
                $item['ID'] = $row->ID;
                $item['EMP_ID'] = $row->EMP_ID;
                $item['LEAVE_ID'] = $row->LEAVE_ID;
                $item['DESCRIPTION'] = $row->DESCRIPTION;
                $item['DATE_START'] = $row->DATE_START;
                $item['DATE_END'] = $row->DATE_END;
                $item['LEAVE_TYPE_ID'] = $row->LEAVE_TYPE_ID;
                $item['TIME_S_ID'] = $row->TIME_S_ID;
                $item['TIME_E_ID'] = $row->TIME_E_ID;       
                $item['IMAGE'] = $data_img;


                $item['DATE_END_LESS'] = date('Y-m-d', strtotime($row->DATE_END . "-1 days"));


                $data[] = (object) $item;
            }
            $object =  $data;

            return $object;
        
    }

    public function insert_data_calendar()
    {

        $time_start_hour = $this->input->post('time_start_hour');
        $time_end_hour = $this->input->post('time_end_hour');

        // $tomorrow = date('Y-m-d',strtotime($time_end_hour . "+1 days"));
        // echo $tomorrow;die;

        $sql = $this->db->select('*')
            ->from('time')
            ->order_by('id', 'DESC')
            ->where('ID', $time_start_hour);

        $query = $sql->get();
        $row_s =  $query->row();


        $sql = $this->db->select('*')
            ->from('time')
            ->order_by('id', 'DESC')
            ->where('ID', $time_end_hour);

        $query = $sql->get();
        $row_e =  $query->row();

        $start =  $row_s->START;
        $end =  $row_e->END;

        $dateEnd = $this->input->post('date_end');
        $date_end = date('Y-m-d', strtotime($dateEnd . "+1 days"));

        // คำนวนจำนวนวันหยุด
        $date1 = date_create($this->input->post('date_start'));
        $date2 = date_create($date_end);
        $diff = date_diff($date1, $date2);

        // เช็คข้อมูล owner
        $sql_staff = $this->db->select('owner1,owner2,owner3')
        ->from('staff')
        ->where('id',$this->session->userdata('user_code'));        
        $q_staff = $sql_staff->get();
        $r_staff = $q_staff->row();

        $data_array = array(
            'EMP_ID ' => $this->input->post('emp_name'),
            'LEAVE_ID ' => $this->input->post('leave'),
            'DESCRIPTION' => $this->input->post('description'),
            'DATE_START' => $this->input->post('date_start'),
            'DATE_END' => $date_end,
            'TOTAL' => $diff->format('%a'),
            'LEAVE_TYPE_ID ' => $this->input->post('leave_type_name'),
            'TIME_S_ID' => $start,
            'TIME_E_ID' => $end,
            'OWNER1' => $r_staff->owner1 ? $r_staff->owner1 : null,
            'OWNER2' => $r_staff->owner2 ? $r_staff->owner2 : null,
            'OWNER3' => $r_staff->owner3 ? $r_staff->owner3 : null,
            'USER_STAFF_ID' =>  $this->session->userdata('user_code'),
        );
        $this->db->insert('calendar', $data_array);
        $new_id = $this->db->insert_id();

        $file = $_FILES['files'];
        if ($file) {

            // $uploadsDir = base_url('asset/image/');
            $uploadsDir = $this->path_dir;
            $allowedFileType = $this->allowedFileType;
            $optional = [
                'width' => 300
            ];
            $img_array = [];

            foreach ($file['name'] as $key => $val) {
                //	check file for upload
                if ($file['name'][$key]) {
                    // create array FILE

                    $imagefile = array(
                        'type'        => $file['type'][$key],
                        'name'        => $file['name'][$key],
                        'tmp_name'    => $file['tmp_name'][$key],
                        'error'        => $file['error'][$key],
                        'size'        => $file['size'][$key]
                    );

                    $new_image_name = time() . '.' . explode("/", $file['type'][$key])[1];

                    $img_array[] = $new_image_name;

                    $targetPath = $uploadsDir . $new_image_name;


                    $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
                    if (in_array($fileType, $allowedFileType)) {
                        $check_upload = $this->image->uploadimage($targetPath, $imagefile, $optional);
                    } else {
                        $text_formatfile = implode(',', $allowedFileType);
                        $result = array(
                            'error_code' => 2,
                            'txt'        => 'รองรับเฉพาะ ' . $text_formatfile
                        );

                        //return $result;
                    }
                    sleep(1);
                }

                $result = array(
                    'error_code' => 0,
                    'txt'        => 'อัพโหลดรูปสำเร็จ'
                );

                //return $result;
            }
            foreach ($img_array as $key => $val) {
                $data_img_array = array(
                    'IMAGE ' => $val,

                    'CALENDAR_ID ' => $new_id,
                );

                $this->db->insert('calendar_img', $data_img_array);
            }
        }



        if ($new_id) { //ถ้าค้นหาไอดีเจอแล้ว เป็น จริง

            $query = $this->db->select('*')
                ->where('id', $new_id)
                ->get('calendar');
            $row = $query->row();

            $query_img = $this->db->select('*')
                ->where('calendar_id', $new_id)
                ->get('calendar_img');




            $item = [];
            $data_img = [];

            foreach ($query_img->result() as $row_img) {

                $data_img[] = array(
                    'id' => $row_img->ID,
                    'name' => base_url('/asset/image/' . $row_img->IMAGE),
                );
            }
            $item['ID'] = $row->ID;
            $item['EMP_ID'] = $row->EMP_ID;
            $item['LEAVE_ID'] = $row->LEAVE_ID;
            $item['DESCRIPTION'] = $row->DESCRIPTION;
            $item['DATE_START'] = $row->DATE_START;
            $item['DATE_END'] = $row->DATE_END;
            $item['LEAVE_TYPE_ID'] = $row->LEAVE_TYPE_ID;
            $item['TIME_S_ID'] = $row->TIME_S_ID;
            $item['TIME_E_ID'] = $row->TIME_E_ID;
            $item['IMAGE'] = $data_img;

            $item['DATE_END_LESS'] = date('Y-m-d', strtotime($row->DATE_END . "-1 days"));
            $data[] = $item;

            $result = [
                // $query->result()
                'query' => $data,

            ];
        } else { //ถ้าหาไอดีไม่เจอ แสดง 'no success'
            $result = [
                'status' => 'no success'
            ];
        }


        $result = json_encode($result);
        return $result;
    }

    public function update_data_calendar()
    {

        $file = isset($_FILES['files']) ? $_FILES['files'] : null;

        $uploadsDir = $this->path_dir;
        $allowedFileType = $this->allowedFileType;
        $optional = [
            'width' => 1920
        ];
        $img_array = [];
        if (isset($file)) {


            foreach ($file['name'] as $key => $val) {
                //	check file for upload
                if ($file['name'][$key]) {
                    // create array FILE

                    $imagefile = array(
                        'type'        => $file['type'][$key],
                        'name'        => $file['name'][$key],
                        'tmp_name'    => $file['tmp_name'][$key],
                        'error'        => $file['error'][$key],
                        'size'        => $file['size'][$key]
                    );

                    $new_image_name = time() . '.' . explode("/", $file['type'][$key])[1];

                    $img_array[] = $new_image_name;

                    $targetPath = $uploadsDir . $new_image_name;


                    $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
                    if (in_array($fileType, $allowedFileType)) {

                        $check_upload = $this->image->uploadimage($targetPath, $imagefile, $optional);
                    } else {
                        $text_formatfile = implode(',', $allowedFileType);
                        $result = array(
                            'error_code' => 2,
                            'txt'        => 'รองรับเฉพาะ ' . $text_formatfile
                        );

                        //return $result;
                    }
                    sleep(1);
                }

                $result = array(
                    'error_code' => 0,
                    'txt'        => 'อัพโหลดรูปสำเร็จ'
                );

                //return $result;
            }
            //     echo "<pre>";
            //    print_r($img_array);
        }
        // echo $this->path;
        //die;


        $time_start_hour_up = $this->input->post('time_start_hour_up');
        $time_end_hour_up = $this->input->post('time_end_hour_up');

        // $tomorrow = date('Y-m-d',strtotime($time_end_hour . "+1 days"));
        // echo $tomorrow;die;

        $sql = $this->db->select('*')
            ->from('time')
            ->order_by('id', 'DESC')
            ->where('ID', $time_start_hour_up);

        $query = $sql->get();
        $row_s =  $query->row();


        $sql = $this->db->select('*')
            ->from('time')
            ->order_by('id', 'DESC')
            ->where('ID', $time_end_hour_up);

        $query = $sql->get();
        $row_e =  $query->row();

        $start_up =  $row_s->START;
        $end_up =  $row_e->END;

        $dateEnd = $this->input->post('date_end_up');
        $date_end = date('Y-m-d', strtotime($dateEnd . "+1 days"));

        // คำนวนจำนวนวันหยุด
        $date1 = date_create($this->input->post('date_start_up'));
        $date2 = date_create($date_end);
        $diff = date_diff($date1, $date2);
      
        $data_array = array(
            'ID' => $this->input->post('user_id_up'),

            'EMP_ID ' => $this->input->post('emp_name_up'),
            'LEAVE_ID ' => $this->input->post('leave_up'),
            'DESCRIPTION' => $this->input->post('description_up'),
            'DATE_START' => $this->input->post('date_start_up'),
            'DATE_END' => $date_end,
            'TOTAL' => $diff->format('%a'),
            'LEAVE_TYPE_ID ' => $this->input->post('leave_type_name_up'),
            'TIME_S_ID' => $start_up,
            'TIME_E_ID' => $end_up,
            'USER_STAFF_ID' =>  $this->session->userdata('user_code'),
        );

        $id = $data_array['ID'];
        // foreach($data_array as $row){
        //     print_r($row->id);
        // }
        $this->db->where('id', $id);
        $this->db->update('calendar', $data_array);



        foreach ($img_array as $key => $val) {
            $data_img_array = array(
                'IMAGE ' => $val,

                'CALENDAR_ID ' => $id
            );

            $this->db->insert('calendar_img', $data_img_array);
        }


        if ($id) { //ถ้าค้นหาไอดีเจอแล้ว เป็น จริง

            $query = $this->db->select('*')
                ->where('id', $id)
                ->get('calendar');
            $row = $query->row();

            $query_img = $this->db->select('*')
                ->where('calendar_id', $id)
                ->get('calendar_img');




            $item = [];
            $data_img = [];

            foreach ($query_img->result() as $row_img) {

                $data_img[] = array(
                    'id' => $row_img->ID,
                    'name' => base_url('/asset/image/' . $row_img->IMAGE),
                );
            }
            // $data[] = (object) array('id' => '1');
            $item['ID'] = $row->ID;
            $item['EMP_ID'] = $row->EMP_ID;
            $item['LEAVE_ID'] = $row->LEAVE_ID;
            $item['DESCRIPTION'] = $row->DESCRIPTION;
            $item['DATE_START'] = $row->DATE_START;
            $item['DATE_END'] = $row->DATE_END;
            $item['LEAVE_TYPE_ID'] = $row->LEAVE_TYPE_ID;
            $item['TIME_S_ID'] = $row->TIME_S_ID;
            $item['TIME_E_ID'] = $row->TIME_E_ID;
            $item['IMAGE'] = $data_img;

            $item['DATE_END_LESS'] = date('Y-m-d', strtotime($row->DATE_END . "-1 days"));
            $data[] = $item;

            $result = [
                // $query->result()
                'query' => $data,

            ];
        } else { //ถ้าหาไอดีไม่เจอ แสดง 'no success'
            $result = [
                'status' => 'no success'
            ];
        }


        $result = json_encode($result);
        return $result;
        //print_r($this->input->post('user_name'));die;
    }

    public function delete_image_data()
    {
        $dataResult = [];
        if ($this->input->post('id')) {
            $id = $this->input->post('id');

            $this->db->where('id', $id);
            $this->db->delete('calendar_img');

            $dataResult['status'] = 1;
        }

        $result = json_encode($dataResult);
        return $result;
    }
    public function delete_data_calendar()
    {
        $data_array = array(
            'ID' => $this->input->post('user_id_up'),
            'EMP_ID' => $this->input->post('emp_name_up'),
            'LEAVE_ID' => $this->input->post('leave_up'),
            'DESCRIPTION' => $this->input->post('description_up'),
            'DATE_START' => $this->input->post('date_start_up'),
            'DATE_END' => $this->input->post('date_end_up'),
            'LEAVE_TYPE_ID' => $this->input->post('leave_type_name_up'),
            'TIME_S_ID' => $this->input->post('time_start_hour_up'),
            'TIME_E_ID' => $this->input->post('time_end_hour_up'),
            'STATUS' => '0',
        );

        $id = $data_array['ID'];
        // foreach($data_array as $row){
        //     print_r($row->id);
        // }
        $this->db->where('id', $id);
        $this->db->update('calendar', $data_array);

        $query = $this->db->select('*')
            ->where('id', $id)
            ->order_by('id', 'DESC')
            ->get('calendar');

        $result = [
            // $query->result()
            'query' => $query->result(),
            // 'id' => $this->db->insert_id(),
            // 'id' => $this->db->insert_id(),
            // 'status' => 'success id ='
        ];

        $result = json_encode($result);
        return $result;
        //print_r($this->input->post('user_name'));die;
    }
}
