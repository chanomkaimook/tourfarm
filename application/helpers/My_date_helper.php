<?php
error_reporting(E_ALL & ~E_NOTICE);

//	convert thai date
//	@param	date	@date = date yyyy-mm-dd
//	@param	typereturn	@text = [date , datetime]
//	return datetime TH
//
function toThaiDateTimeString($date, $typereturn)
{
  
  $thai_day_arr = array("อา", "จ", "อ", "พ", "พฤ", "ศ", "ส");
  $thai_month_arr = array(
    "00" => "",
    "01" => "ม.ค",
    "02" => "ก.พ",
    "03" => "มี.ค",
    "04" => "เม.ย",
    "05" => "พ.ค",
    "06" => "มิ.ย",
    "07" => "ก.ค",
    "08" => "ส.ค",
    "09" => "ก.ย",
    "10" => "ต.ค",
    "11" => "พ.ย",
    "12" => "ธ.ค"
  );

  $time = strtotime($date);
  $time_day = date("j", $time);
  $time_month = date("m", $time);
  $time_year = date("Y", $time);

  $thai_date_return = $time_day . " " . $thai_month_arr[$time_month] . " " . $time_year;
  $thai_time_return = date('H:i:s',$time);

  if($typereturn == "datetime"){
    $result = $thai_date_return." ".$thai_time_return;
  }else{
    $result = $thai_date_return;
  }
  
  return $result;
}
