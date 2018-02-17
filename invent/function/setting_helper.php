<?php

function updateConfig( $configName, $value = '', $id_employee = 0 )
{
	$sc = FALSE;
	$id_emp  = $id_employee == 0 ? $_COOKIE['user_id'] : $id_employee;	
	if( $value !== '' )
	{
		$se = dbQuery("UPDATE tbl_config SET value = '".$value."', id_employee = ".$id_emp." WHERE config_name = '".$configName."'");
	}
	return $sc;	
}


function selectInterval($se = '')
{
	$sc = '';
	$sc .= '<option value="0" '.isSelected(0, $se).'>ตลอดเวลา</option>';
	$sc .= '<option value="3600" '.isSelected(3600, $se).'>ทุกชั่วโมง</option>';
	$sc .= '<option value="10800" '.isSelected(10800, $se).'>ทุก 3 ชั่วโมง</option>';
	$sc .= '<option value="21600" '.isSelected(21600, $se).'>ทุก 6 ชั่วโมง</option>';
	$sc .= '<option value="43200" '.isSelected(43200, $se).'>ทุก 12 ชั่วโมง</option>';
	$sc .= '<option value="86400" '.isSelected(86400, $se).'>ทุก 24 ชั่วโมง</option>';
	
	return $sc;
}

?>