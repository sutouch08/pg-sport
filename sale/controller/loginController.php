<?php
	require "../../library/config.php";
	if(isset($_GET['email'])&&isset($_GET['password'])){
		$email = $_GET['email'];
		$password = md5($_GET['password']);
		$sql = dbQuery("SELECT id_customer FROM tbl_customer WHERE email = '$email' AND password = '$password'");
		if(dbNumRows($sql)==1){
			$pass = "true";
		}else{
			$pass = "false";
		}
		echo $pass;
	}
?>