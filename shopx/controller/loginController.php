<?php
	require "../../library/config.php";
	require "../../library/functions.php";
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
	
	if(isset($_GET['EMAIL'])){
		$email = $_GET['EMAIL'];
		$sql = dbQuery("SELECT id_customer FROM tbl_customer WHERE email = '$email'");
		if(dbNumRows($sql)==1){
			$pass = "false";
		}else{
			$pass = "true";
		}
		echo $pass;
	}
	if(isset($_GET['new_user'])){
		if(isset($_POST['gender'])){ $gender = $_POST['gender'];}else{ $gender = 0; }
			$id_default_group = "1";
			$first_name = $_POST['first_name'];
			$last_name = $_POST['last_name'];
			$email = $_POST['EMAIL'];
			$password = md5($_POST['PASSWORD']);
			$birthday = dbDate($_POST['day']."-".$_POST['month']."-".$_POST['year']);
			$active = $company->default_active;
			$date_add = date('Y-m-d');
			$date_upd = date("Y-m-d");
			$group_checked = 1;
			if(dbQuery("INSERT INTO tbl_customer(id_default_group, id_sale, id_gender, first_name, last_name, email, password, birthday, credit_amount, credit_term, active, date_add, date_upd) VALUES ($id_default_group, 0, $gender, '$first_name', '$last_name', '$email', '$password', '$birthday', '0', '0', '$active', '$date_add', '$date_upd')")){
				$sql = dbQuery("SELECT id_customer FROM tbl_customer WHERE first_name = '$first_name' AND last_name = '$last_name' AND email = '$email'");
				list($id_customer) = dbFetchArray($sql); 
				setcookie("id_customer",$id_customer,time()+(3600*24*30),'/');
				setcookie("customer_name",$first_name."&nbps".$last_name,time()+(3600*24*30),'/');
				dbQuery("INSERT INTO tbl_customer_group(id_customer, id_group) VALUES ($id_customer, 1)");
				header("location: ../index.php?content=account");
			}
	}
?>