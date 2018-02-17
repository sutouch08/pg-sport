<?php
require '../library/config.php';
require '../library/functions.php';
require "../library/class/php-excel.class.php";
require "function/tools.php";
require "function/sponsor_helper.php";
require "function/support_helper.php";

if( isset($_GET['update_zone']) && isset( $_GET['id_zone'] ) && isset( $_GET['reference'] ) )
{
	$ref = $_GET['reference'];
	$id_zone = $_GET['id_zone'];
	$qs = dbQuery("UPDATE tbl_stock_movement SET id_zone = ".$id_zone." WHERE id_zone = 0 AND reference = '".$ref."'");
	if( $qs )
	{
		echo "success";
	}else{
		echo "fail";
	}
}

  ?>	

