<?php 
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
$user_id = $_COOKIE['user_id'];
if(isset($_GET['add'])){
	$profile_name = $_POST['profile_namee'];
	$checked = dbNumRows(dbQuery("SELECT profile_name FROM tbl_profile WHERE profile_name = '$profile_name'"));
	if($checked > 0){
		$message = "อีเมล์ซ้ำ มีอีเมล์นี้ในระบบแล้ว";
		header("location: ../index.php?content=Profile&add=y&error=$message");
	}else{
		dbQuery("INSERT INTO tbl_profile (profile_name) VALUES ('$profile_name')");
		list($id_profile) = dbFetchArray(dbQuery("SELECT id_profile FROM tbl_profile WHERE profile_name = '$profile_name'"));
		$sql = dbQuery("SELECT id_tab FROM tbl_tab");
		while($row=dbFetchArray($sql)){
			$id_tab = $row['id_tab'];
			dbQuery("INSERT INTO tbl_access (id_profile, id_tab, tbl_access.view, tbl_access.add, tbl_access.edit, tbl_access.delete) VALUES ( $id_profile, $id_tab, 0, 0, 0, 0) ");
		}
		$message = "เพิ่มพนักงานเรียบร้อย";
		header("location: ../index.php?content=Profile&message=$message");
		//echo $id_profile;
	}
}
if(isset($_GET['edit'])){
	$profile_namee = $_POST['profile_namee'];
	$profile_name = $_POST['profile_name'];
	$id_profile = $_POST['id_profile'];
	$checked = dbNumRows(dbQuery("SELECT profile_name FROM tbl_profile WHERE profile_name = '$profile_namee' and profile_name != '$profile_name'"));
	if($checked > 0){
		$message = "โปรไฟร์ซ้ำ มีโปรไฟร์ในระบบแล้ว";
		header("location: ../index.php?content=Profile&edit=y&id_profile=$id_profile&error=$message");
	}else{
		dbQuery("UPDATE tbl_profile SET profile_name = '$profile_namee' WHERE id_profile = '$id_profile'");
		$message = "แก้ไขโปรไฟร์เรียบร้อยแล้ว";
		header("location: ../index.php?content=Profile&edit=y&id_profile=$id_profile&message=$message");
	}
}
if(isset($_GET['drop'])){
	$id_profile = $_GET['id_profile'];
	dbQuery("DELETE FROM tbl_profile WHERE id_profile =$id_profile");
	$message = "ลบโปรไฟร์เรียบร้อย";
	header("location: ../index.php?content=Profile&message=$message");
}