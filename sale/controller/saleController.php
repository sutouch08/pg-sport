<?php 
require "../../library/config.php";
require "../../library/functions.php";
require "../../invent/function/tools.php";
//***********************  reset password  ************************************//
if(isset($_GET['reset_password'])){
	$id_employee = $_POST['id_employee'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$checked = dbNumRows(dbQuery("SELECT email FROM tbl_employee WHERE email = '$email' and id_employee != $id_employee"));
	if($checked > 0){
		$message = "อีเมล์หรือชื่อผู้ใช้ซ้ำ มีอีเมล์หรือชื่อผู้ใช้นี้ในระบบแล้ว";
		header("location: ../index.php?content=Employee&reset_password=y&id_employee=$id_employee&error=$message");
	}else{
		if($password == ""){
			$pass = "";
		}else{
			$pass = ", password ='".md5($password)."'";
		}
		//echo $pass;
		dbQuery("UPDATE tbl_employee SET email = '$email' $pass WHERE id_employee = '$id_employee'");
		$message = "แก้ไขข้อมูลเรียบร้อยแล้ว";
		header("location: ../index.php?content=Employee&reset_password=y&id_employee=$id_employee&message=$message");
	}
}