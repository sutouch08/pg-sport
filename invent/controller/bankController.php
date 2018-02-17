<?php
require "../../library/config.php";
require "../../library/functions.php";
require '../function/tools.php';
require '../function/bank_helper.php';


if( isset( $_GET['addNewAccount'] ) )
{
	$sc = 'fail';
	$id		= $_POST['id_account'] == '' ? FALSE : $_POST['id_account'];
	if( $id !== FALSE )
	{
		$qr = "UPDATE tbl_bank_account SET bankcode = '".$_POST['bank']."', bank_name = '".getBankName($_POST['bank'])."', branch = '".$_POST['branch']."', ";
		$qr .= "acc_name = '".$_POST['acName']."', acc_no = '".$_POST['acNo']."', active = ".$_POST['active']." WHERE id_account = ".$id;
		$qs = dbQuery($qr);
	}
	else
	{
		$qr = "INSERT INTO tbl_bank_account (bankcode, bank_name, branch, acc_name, acc_no, active) ";
		$qr .= "VALUES ('".$_POST['bank']."', '".getBankName($_POST['bank'])."', '".$_POST['branch']."', '".$_POST['acName']."', '".$_POST['acNo']."', ".$_POST['active'].")";
		$qs = dbQuery($qr);
	}
	if( $qs )
	{
		$sc = 'success';	
	}
	echo $sc;
}


if( isset( $_GET['removeBankAccount'] ) )
{
	$sc 	= 'fail';
	$id 	= $_POST['id_account'];	
	$qs 	= dbQuery("DELETE FROM tbl_bank_account WHERE id_account = ".$id);
	if( $qs )
	{
		$sc = 'success';
	}
	echo $sc;
}

if( isset( $_GET['getBankAccountTable'] ) )
{
	$sc = 'fail';
	$qs = dbQuery("SELECT * FROM tbl_bank_account");
	if( dbNumRows($qs) > 0 )
	{
		$n = 1; 
		$ds = array();
		while( $rs = dbFetchArray($qs) )
		{
			$arr = array(
								'id'				=> $rs['id_account'],
								'bankName'	=> $rs['bank_name'],
								'accName'	=> $rs['acc_name'],
								'accNo'		=> $rs['acc_no'],
								'branch'		=> $rs['branch'],
								'actived'		=> isActived($rs['active']),
								'active'		=> $rs['active'],
								'logo'			=> '<img src="'.bankLogoUrl($rs['bankcode']).'" height="20px" width="20px;" />'
							);
			array_push($ds, $arr);
			$n++;	
		}
		$sc = json_encode($ds);
	}
	echo $sc;
}

if( isset( $_GET['getBankAccount'] ) )
{
	$sc = 'fail';
	$qs = dbQuery("SELECT * FROM tbl_bank_account WHERE id_account = ".$_POST['id_account']);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$ds = $rs['id_account'].' | '.$rs['bankcode'].' | '.$rs['acc_name'].' | '.$rs['acc_no'].' | '.$rs['branch'].' | '.$rs['active'];
		$sc = $ds;						
	}
	echo $sc;
}

if( isset( $_GET['getAccountDetail'] ) )
{
	$sc = 'fail';
	$qs = dbQuery("SELECT * FROM tbl_bank_account WHERE id_account = ".$_POST['id_account']);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$ds = bankLogoUrl($rs['bankcode']).' | '.$rs['bank_name'].' สาขา '.$rs['branch'].'<br/>เลขที่บัญชี '.$rs['acc_no'].'<br/> ชื่อบัญชี '.$rs['acc_name'];
		$sc = $ds;						
	}
	echo $sc;
}

if( isset( $_GET['toggleActive'] ) )
{
	$sc 		= 'fail';
	$active 	= $_POST['active'] == 1 ? 0 : 1 ; // สลับสถานะ 
	$id			= $_POST['id_account'];	
	$qs 		= dbQuery("UPDATE tbl_bank_account SET active = ".$active." WHERE id_account = ".$id);
	if( $qs )
	{
		$sc = 'success';
	}
	echo $sc;
}

?>