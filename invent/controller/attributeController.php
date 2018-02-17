<?php
require "../../library/config.php";
//**** เพิ่มรายการคุณลักษณะ****//
if(isset($_GET['add'])&&isset($_POST['attribute_name'])){
	$attribute_name = $_POST['attribute_name'];
	$qr = dbFetchArray(dbQuery("SELECT max(position) as max FROM tbl_attribute"));
	$position = $qr['max']+1;
	dbQuery("INSERT INTO tbl_attribute (attribute_name, position) VALUES ('$attribute_name', $position)");
	header("location: ../index.php?content=attribute&add=y");
}
//**** ลบรายการคุณลักษณะ****//
if(isset($_GET['delete'])&&isset($_GET['id_attribute'])){
	$id_attribute = $_GET['id_attribute'];
	dbQuery("DELETE FROM tbl_attribute WHERE id_attribute = $id_attribute");
	header("location: ../index.php?content=attribute");
}
///****** แก้ไขรายการคุณลักษณะ *****///
if(isset($_GET['edit'])&&isset($_POST['id_attribute'])){
	$id_attribute=$_POST['id_attribute'];
	$attribute_name = $_POST['attribute_name'];
	$position = $_POST['position'];
	dbQuery("UPDATE tbl_attribute SET attribute_name = '$attribute_name', position = $position WHERE id_attribute = $id_attribute");
	header("location: ../index.php?content=attribute");
}
//******************** เปลี่ยนตำแหน่ง ***************************//
if(isset($_GET['move'])&&isset($_GET['id_attribute'])&&isset($_GET['position'])){
	$move = $_GET['move'];
	$id_attribute = $_GET['id_attribute'];
	$position = $_GET['position'];
	switch($move){
		case "up":
			list($above) = dbFetchArray(dbQuery("SELECT id_attribute FROM tbl_attribute WHERE position = ($position-1)"));
			dbQuery("UPDATE tbl_attribute SET position = $position WHERE id_attribute = $above");
			dbQuery("UPDATE tbl_attribute SET position = ($position-1) WHERE id_attribute = $id_attribute");
			break;
		case "down":
			list($below) = dbFetchArray(dbQuery("SELECT id_attribute FROM tbl_attribute WHERE position = ($position+1)"));
			dbQuery("UPDATE tbl_attribute SET position = $position WHERE id_attribute = $below");
			dbQuery("UPDATE tbl_attribute SET position = ($position+1) WHERE id_attribute = $id_attribute");
			break;
		default:
			break;
	}
	header("location: ../index.php?content=attribute");
}			


?>