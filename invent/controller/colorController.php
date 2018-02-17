<?php
require "../../library/config.php";
//**** เพิ่มรายการสี****//
if(isset($_GET['add'])&&isset($_POST['color_code'])){
	$color_code = $_POST['color_code'];
	$color_name = $_POST['color_name'];
	$qr = dbFetchArray(dbQuery("SELECT max(position) as max FROM tbl_color"));
	$position = $qr['max']+1;
	dbQuery("INSERT INTO tbl_color (color_code, color_name, position) VALUES ('$color_code', '$color_name', $position)");
	header("location: ../index.php?content=color");
}
//**** ลบรายการสี****//
if(isset($_GET['delete'])&&isset($_GET['id_color'])){
	$id_color = $_GET['id_color'];
	dbQuery("DELETE FROM tbl_color WHERE id_color = $id_color");
	header("location: ../index.php?content=color");
}
///****** แก้ไขรายการสี *****///
if(isset($_GET['edit'])&&isset($_POST['id_color'])){
	$id_color=$_POST['id_color'];
	$color_code = $_POST['color_code'];
	$color_name = $_POST['color_name'];
	$position = $_POST['position'];
	dbQuery("UPDATE tbl_color SET color_code = '$color_code', color_name = '$color_name', position = $position WHERE id_color = $id_color");
	header("location: ../index.php?content=color");
}



?>