
<?php 
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
require "../function/setting_helper.php";

if( isset( $_GET['updateConfig'] ) )
{
	$sc = '';
	$config = $_POST;
	foreach($config as $configName => $val )
	{
		updateConfig($configName, $val);
	}
	echo $sc;
}

if( isset( $_GET['activeSystem'] ) )
{
	$sc = 'fail';
	$rs = updateConfig('CLOSED', 0);
	if( $rs )
	{
		$sc = 'success';
	}
	echo $sc;
}

if( isset( $_GET['updatePopup'] ) )
{
	$sc = 'fail';
	$pop_on 	= $_POST['pop_on'];
	$data			= array( 'delay' => $_POST['delay'], 'start' => $_POST['start'], 'end' => $_POST['end'], 'content' => $_POST['content'], 'width' => $_POST['width'], 'height' => $_POST['height'], 'active' => $_POST['active']);
	$set			= '';
	$i = 1;
	$c = 7;
	foreach( $data as $key => $val )
	{
		if( $val != '' )
		{
			if( $key == 'start' ){ $val = fromDate($val); }
			if( $key == 'end' ){ $val = toDate($val); }
			$set .= $key ." = '".$val."'";
			if( $i < $c ){ $set .= ", "; }
		}
		$i++;
	}
	if( isset( $_POST['updateAll'] ) )
	{
		$qs = dbQuery("UPDATE tbl_popup SET ".$set." WHERE pop_on IN('front', 'back', 'sale')");
	}
	else
	{
		$qs = dbQuery("UPDATE tbl_popup SET ".$set." WHERE pop_on = '".$pop_on."'");
	}
	if( $qs )
	{
		$sc = 'success';
	}
	
	//header("location: ../index.php?content=popup");
	echo $sc;
}


?>