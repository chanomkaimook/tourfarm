<?php
error_reporting(E_ALL & ~E_NOTICE);

/**
 * check data name duplicate
 *
 * @param Array|null $data     = array key=column on table, value=data to search
 * @param String|null $table    = table name
 * @return boolean
 */
function check_dup(array $array = [], String $table = null)
{
  $ci = &get_instance();
  $ci->load->database();

  # code...
  $result = true;
  if (isset($array) && $table) {
    $sql = $ci->db->from($table);

    foreach ($array as $column => $value) {
      $sql->where($column, $value);
    }

    $count = $sql->count_all_results(null, false);
    $q = $sql->get();

    return $count;
    if (!$count) {
      $result = false;
    }
  }

  return $result;
}

/**
 * user name
 *
 * @param String|null $column = column table
 * @param String|null $value = value
 * @return String name
 */
function whois(String $column = null, String $value = null)
{
  $ci = &get_instance();
  $ci->load->database();

  # code...
  $result = "";
  if ($column && $value) {
    $sql = $ci->db->select('NAME,LASTNAME')
      ->from('staff')
      ->where($column, $value);
    $count = $sql->count_all_results(null, false);
    $q = $sql->get();
    if ($count) {
      $row = $q->row();
      $result = $row->NAME . " " . $row->LASTNAME;
    }
  }

  return $result;
}

/**
 * delete file
 *
 * @param array $array = array(key=>path file name eg. asset/images/12345.jpg)
 * @param String|null $table = table name
 * @return void
 */
function delete_file(array $array = [], String $table = null)
{
  $ci = &get_instance();
  $ci->load->database();

  if ($table && $array) {

    foreach ($array as $key => $value) {

      // check path file
      if (file_exists($value)) {
        @unlink($value);
      }
    }
  }
}

/**
 * status alias
 *
 * @param String|null $key = item id or key (if key wont to alias)
 * @param String|null $alias = column status alias
 * @return void
 */
function status_alias(String $key = null, String $alias = null)
{
  $ci = &get_instance();
  $ci->load->database();

  # code...
  $result = "";

  if ($key) {
    $sql = $ci->db->select('ID,NAME')
      ->from('status_alias');

    if ($alias) {
      $sql->where($alias, $key);
    } else {
      $sql->where('id', $key);
    }

    $count = $sql->count_all_results(null, false);
    $q = $sql->get();
    if ($count) {
      $result = $q->row();
    }
  }

  return $result;
}

/**
 * query column status alias
 *
 * @param String|null $key = item id or key (if key wont to alias)
 * @param String|null $alias = column status alias
 * @param string $column = column to show
 * @return void
 */
function get_status_alias(String $key = null, String $alias = null,String $column = "NAME")
{
  $ci = &get_instance();
  $ci->load->database();

  # code...
  $result = "";

  if ($key) {
    $sql = (object) status_alias($key,$alias);
    // print_r($sql);
    $result = $sql->NAME;
  }

  return $result;
}
