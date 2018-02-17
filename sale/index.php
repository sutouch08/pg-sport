<?php
require_once '../library/config.php';
require_once '../library/functions.php';
require_once "../invent/function/tools.php";

function check_login()
{
	if( !isset($_COOKIE['user_id']) && !isset($_COOKIE['sale_id']) )
	{
		header('Location: login.php');
		exit;
	}else{
		$active = isActive($_COOKIE['user_id']);
		if(!$active){
			header('Location: login.php');
			exit;
		}
	}
}

if( !getConfig("CLOSED") )
{
$content = '';

check_login();
if(isset($_GET['logout'])) {
		doLogout();
}


$page = (isset($_GET['content'])&& $_GET['content'] !='')?$_GET['content']:'';
switch($page){
	case "order":
		$content = "order.php";
		$pageTitle = "สั่งสินค้า";
		break;
	case 'category':
		$content = 'category.php';
		$pageTitle = 'สินค้า';
		break;
	case 'product':
		$content = 'product.php';
		$pageTitle = 'สินค้า';
		break;
	case "cart":
		$content = "cart.php";
		$pageTitle = "สรุปรายการ";
		break;
	case "dashboard":
		$content = "dashboard.php";
		$pageTitle = "Sale Dash Board";
		break;
	case "tracking":
		$content = "order_tracking.php";
		$pageTitle = "ติดตามออเดอร์";
		break;
	case "request" :
		$content = "request/index.php";
		$pageTitle = "สั่งจองสินค้า";
		break;
	case "Employee":
		$content= "employee.php";
		$pageTitle = "reset password";
		break;
	default:
		$content = "order.php"; //'home.php';
		$pageTitle = COMPANY;
		break;
}
require_once 'template.php';
}
else
{
	require '../invent/maintenance.php';	
}
?>

