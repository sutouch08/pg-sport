<?php
require_once '../../library/config.php';
require_once '../../library/functions.php';
require "../../invent/function/tools.php";
$content = '';
if(!isset($_COOKIE['user_id'])){
		header('Location: login.php');
		exit;
		}
		if (isset($_GET['logout'])) {
		doLogout();
	}
$page = (isset($_GET['content'])&& $_GET['content'] !='')?$_GET['content']:'';
switch($page){
	case "order":
		$content = "request_order.php";
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
		$content = "request/request_order.php";
		$pageTitle = "สั่งจองสินค้า";
		break;
	
	default:
		$content = "main.php"; //'home.php';
		$pageTitle = COMPANY;
		break;
}
require_once 'template.php';
?>

