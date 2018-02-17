<?php
require "../../library/config.php";
require "../../library/functions.php";
if(isset($_GET['warehouse_name'])){
	$warehouse_name = $_GET['warehouse_name'];
	$result = dbQuery("select warehouse_name from tbl_warehouse where warehouse_name = '$warehouse_name'");
	$row = dbNumRows($result);
	if($row >0){
		$message ="1";
		echo $message;
	}else{
		$message ="0";
		echo $message;
	}
}

if(isset($_GET['add'])&&isset($_POST['warehouse_name'])){
	$warehouse_name = $_POST['warehouse_name'];
	$active = $_POST['active'];
	dbQuery("INSERT INTO tbl_warehouse (warehouse_name, active) VALUES ('$warehouse_name', $active)");
	header("location:../index.php?content=warehouse");
}

if(isset($_GET['edit'])&&isset($_POST['id_warehouse'])){
	$id_warehouse = $_POST['id_warehouse'];
	$warehouse_name = $_POST['warehouse_name'];
	$active = $_POST['active'];
	dbQuery("UPDATE tbl_warehouse SET warehouse_name = '$warehouse_name', active = $active WHERE id_warehouse = $id_warehouse");
	header("location: ../index.php?content=warehouse");
}

if(isset($_GET['delete'])&&isset($_GET['id_warehouse'])){
	
	$id_warehouse = $_GET['id_warehouse'];
	$check = dbNumRows(dbQuery("SELECT qty FROM product_zone WHERE id_warehouse = $id_warehouse AND qty >0"));
	if($check <1 ){
	dbQuery("DELETE FROM tbl_warehouse WHERE id_warehouse = $id_warehouse");
	dbQuery("DELETE FROM tbl_zone WHERE id_warehouse = $id_warehouse");
	header("location: ../index.php?content=warehouse");
	}else{
		$error_message = "คุณไม่สามารถลบคลังนี้ได้เนื่องจากมีรายการสินค้าคงเหลือในคลังนี้";
		header("location: ../index.php?content=warehouse&error=$error_message");
	}
}



?>