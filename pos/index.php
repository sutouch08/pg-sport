<?php
require_once '../library/config.php';
require_once '../library/functions.php';
require_once '../invent/function/tools.php';

function check_login()
{
	if( !isset($_COOKIE['user_id']) )
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

if( !getConfig('CLOSED') )
{
$content = '';

check_login();
if(isset($_GET['logout'])) {
		doLogout();
}


$page = (isset($_GET['content'])&& $_GET['content'] !='')?$_GET['content']:'';
$id_tab = 0;
$id_profile = getCookie('profile_id');

switch($page){
	case 'order':
		$content = 'order.php';
		$pageTitle = 'ขายสินค้า';
		$id_tab = 14;
		break;

	case 'return_order' :
		$content = 'return_order.php';
		$pageTitle = 'รับคืนสินค้า';
		$id_tab = 40;
		break;

	default:
		$content = 'main.php'; //'home.php';
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
