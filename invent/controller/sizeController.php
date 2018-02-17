<?php
require "../../library/config.php";
//**** เพิ่มรายการสี****//
if(isset($_GET['add'])&&isset($_POST['size_name'])){
	$size_name = $_POST['size_name'];
	$qr = dbFetchArray(dbQuery("SELECT max(position) as max FROM tbl_size"));
	$position = $qr['max']+1;
	dbQuery("INSERT INTO tbl_size (size_name, position) VALUES ('$size_name', $position)");
	header("location: ../index.php?content=size");
}
//**** ลบรายการสี****//
if(isset($_GET['delete'])&&isset($_GET['id_size'])){
	$id_size = $_GET['id_size'];
	dbQuery("DELETE FROM tbl_size WHERE id_size = $id_size");
	header("location: ../index.php?content=size");
}
///****** แก้ไขรายการสี *****///
if(isset($_GET['edit'])&&isset($_POST['id_size'])){
	$id_size=$_POST['id_size'];
	$size_name = $_POST['size_name'];
	$position = $_POST['position'];
	dbQuery("UPDATE tbl_size SET size_name = '$size_name', position = $position WHERE id_size = $id_size");
	header("location: ../index.php?content=size");
}
//******************** เปลี่ยนตำแหน่ง ***************************//
if(isset($_GET['move'])&&isset($_GET['id_size'])&&isset($_GET['position'])){
	$move = $_GET['move'];
	$id_size = $_GET['id_size'];
	$position = $_GET['position'];
	switch($move){
		case "up":
			list($above) = dbFetchArray(dbQuery("SELECT id_size FROM tbl_size WHERE position = ($position-1)"));
			dbQuery("UPDATE tbl_size SET position = $position WHERE id_size = $above");
			dbQuery("UPDATE tbl_size SET position = ($position-1) WHERE id_size = $id_size");
			break;
		case "down":
			list($below) = dbFetchArray(dbQuery("SELECT id_size FROM tbl_size WHERE position = ($position+1)"));
			dbQuery("UPDATE tbl_size SET position = $position WHERE id_size = $below");
			dbQuery("UPDATE tbl_size SET position = ($position+1) WHERE id_size = $id_size");
			break;
		default:
			break;
	}
	header("location: ../index.php?content=size");
}			


?>