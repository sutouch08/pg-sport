<?php 
include "../../library/config.php";
include "../../library/functions.php";
include "../function/tools.php";
if(isset($_GET['open'])){
	$id_employee = $_POST['id_employee'];
	$name_check = $_POST['name_check'];
	$date_start = date('Y-m-d');
	$id_warehouse = $_POST['id_warehouse'];
	dbQuery("INSERT INTO tbl_check (id_employee_open,name_check,id_warehouse,status,date_start)value('$id_employee','$name_check','$id_warehouse','1','$date_start')");
	$check_stock = new checkstock();
	$id_check = $check_stock->get_id_check();
	//echo "INSERT INTO tbl_stock_check (id_check,id_zone,id_product_attribute,qty_before) SELECT $id_check,tbl_stock.id_zone,id_product_attribute,qty FROM tbl_zone LEFT JOIN tbl_stock ON tbl_zone.id_zone = tbl_stock.id_zone WHERE id_warehouse = $id_warehouse";
	dbQuery("INSERT INTO tbl_stock_check (id_check,id_zone,id_product_attribute,qty_before) SELECT $id_check,tbl_zone.id_zone,id_product_attribute,qty FROM tbl_zone LEFT JOIN tbl_stock ON tbl_zone.id_zone = tbl_stock.id_zone WHERE id_warehouse = $id_warehouse");
	dbQuery("UPDATE tbl_stock_check SET status = '-1' WHERE id_product_attribute = 0 AND id_check = '$id_check'");
	$message = "เปิดการตรวจนับเรียบร้อยแล้ว";
	header("location: ../index.php?content=OpenCheck&message=$message");
}
if(isset($_GET['close'])){
	$id_employee = $_POST['id_employee'];
	$id_check = $_POST['id_check'];
	$date_stop = date('Y-m-d');
	dbQuery("UPDATE tbl_check SET status = '2' , date_stop = '$date_stop' , id_employee_close = '$id_employee' WHERE id_check = $id_check");
	$message = "ปิดการตรวจนับเรียบร้อยแล้ว";
	header("location: ../index.php?content=OpenCheck&message=$message");
}
?>