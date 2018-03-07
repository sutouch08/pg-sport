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

	//--- รายงาน
	case 'sale_summary' :
		$content = 'report/sale_summary.php';
		$pageTitle = 'รายงานสรุปยอดขาย';
		break;

	case 'sale_by_item' :
		$content = 'report/sale_by_item.php';
		$pageTitle = 'รายงานยอดขายแยกตามสินค้า';
		break;

	default:
		$content = 'main.php';
		$pageTitle = COMPANY;
		$id_tab = 1;
		break;
}

require_once 'template.php';
}
else
{
	require '../invent/maintenance.php';
}
?>
