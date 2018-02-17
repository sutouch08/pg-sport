<?php 
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
$user_id = getCookie('user_id');

if( isset( $_GET['empLogin'] ) )
{
	$userName 	= $_POST['userName'];
	$password	= $_POST['password'];
	$sc = 'fail';
	$qs = dbQuery("SELECT * FROM tbl_employee WHERE email = '".$userName."' AND password = '".$password."' AND active = 1 ");
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		createCookie('user_id', $rs['id_employee']);
		createCookie('UserName', $rs['first_name']);
		createCookie('profile_id', $rs['id_profile']);
		$sc = 'success';
	}
	echo $sc;
}
///**************************** check duplicate name and sur name  **************//
if( isset($_GET['check_name']) && isset($_POST['first_name']) )
{
	$rs = 0;
	if($_POST['id_employee'] != 0 ) :
		$qs = dbQuery("SELECT id_employee FROM tbl_employee WHERE first_name = '".$_POST['first_name']."' AND last_name = '".$_POST['last_name']."' AND id_employee != ".$_POST['id_employee']);
	else :
		$qs = dbQuery("SELECT id_employee FROM tbl_employee WHERE first_name = '".$_POST['first_name']."' AND last_name = '".$_POST['last_name']."'");
	endif;
	if(dbNumRows($qs) > 0 )
	{
		$rs = 1;
	}
	echo $rs;
}
///**************************** check duplicate email or user name  **************//

if( isset( $_GET['check_email'] ) && isset( $_POST['email'] ) )
{
	$rs = 0;
	if( $_POST['id_employee'] != 0 )
	{
		$qs = dbQuery("SELECT id_employee FROM tbl_employee WHERE email = '".$_POST['email']."' AND id_employee != ".$_POST['id_employee']);
	}
	else
	{
		$qs = dbQuery("SELECT id_employee FROM tbl_employee WHERE email = '".$_POST['email']."'");
	}
	if(dbNumRows($qs) > 0 )
	{
		$rs = 1;	
	}
	echo $rs;
}

/********************************* check s_key ****************************/
if(isset( $_GET['check_s_key']) && isset( $_POST['s_key'] ) )
{
	$rs = 0;
	$s_key = md5($_POST['s_key']);
	if( $_POST['id_employee'] != 0 )
	{
		$qs = dbQuery("SELECT id_employee FROM tbl_employee WHERE s_key = '".$s_key."' AND id_employee != ".$_POST['id_employee']);
	}
	else
	{
		$qs = dbQuery("SELECT id_employee FROM tbl_employee WHERE s_key = '".$s_key."'");
	}
	if( dbNumRows($qs) > 0 )
	{
		$rs = 1; 
	}
	echo $rs;
}

//****************************  เพิ่มพนักงานใหม่ ******************************//
if(isset($_GET['add'])){
	$s_key = $_POST['s_key'] == ""? md5($_POST['s_key']) : "";
	$data = array(
					"id_profile" 	=> $_POST['id_profile'], 
					"first_name" 	=> $_POST['first_name'], 
					"last_name" 	=> $_POST['last_name'], 
					"email" 		=> $_POST['email'], 
					"password" 	=> md5($_POST['password']), 
					"s_key" 		=> $s_key, 
					"active" 		=> $_POST['active']
					);
	$employee = new employee();
	$rs = $employee->addEmployee($data);
	if($rs)
	{
		echo $rs;
	}
	else
	{
		echo "fail";
	}
}

//****************************  เแก้ไขข้อมูลพนักงาน ******************************//
if( isset( $_GET['edit'] ) && isset( $_POST['id_employee'] ) )
{
	$data 		= array("id_employee" => $_POST['id_employee'], "id_profile" => $_POST['id_profile'], "first_name" => $_POST['first_name'], "last_name" => $_POST['last_name'], "email" => $_POST['email'], "active" => $_POST['active']);
	$employee 	= new employee();
	$rs 			= $employee->editEmployee($data);
	if($rs)
	{
		echo "success";
	}
	else
	{
		echo "fail";
	}
	
}

//****************************  ลบพนักงาน ******************************//
if(isset($_GET['drop'])){
	$id_employee = $_GET['id_employee'];
	$employee = new employee();
	if($employee->deleteEmployee($id_employee)){
		$message = "ลบพนักงานเรียบร้อยแล้ว";
		header("location: ../index.php?content=Employee&message=$message");
	}else{
		$message = $employee->error_message;
		header("location: ../index.php?content=Employee&error=$message");
	}
}


//***********************  reset password  ************************************//
if(isset($_GET['reset_password'])){
	$id_employee = $_POST['id_employee'];
	$password = md5($_POST['password']);
	$data = array($id_employee, $password);
	$employee = new employee();
	$rs = $employee->reset_password($data);
	if($rs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}

if( isset( $_GET['reset_s_key'] ) && isset( $_POST['id_employee'] ) )
{
	$employee 	= new employee();
	$data			= array("id_employee"=>$_POST['id_employee'], "s_key"=>$_POST['s_key']);
	$rs 			= $employee->reset_s_key($data);
	if($rs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}

//*******************  Active / Disactive พนักงาน  ************************//
if(isset($_GET['active'])&&isset($_GET['id_employee'])){
	$employee = new employee();
	if($employee->change_status($_GET['id_employee'], $_GET['active'])){
		header("location: ../index.php?content=Employee");	
	}else{
		$message = $employee->error_message;
		header("location: ../index.php?content=Employee&error=$message");
	}
}

if(isset($_GET['clear_filter']))
{
	setcookie("employee_search_text","", time()-3600,"/");
	header("location: ../index.php?content=Employee");	
}
?>