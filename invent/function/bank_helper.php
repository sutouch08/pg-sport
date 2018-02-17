<?php

function getActiveBank()
{
	return dbQuery("SELECT * FROM tbl_bank_account WHERE active = 1 ");	
}

function select_bank($se = '' )
{
	$sc = '';
	$qs = dbQuery("SELECT * FROM tbl_bank");
	while( $rs = dbFetchArray($qs) )
	{
		$sc .= '<option value="'.$rs['code'].'" '.isSelected($se, $rs['code']).'>'.$rs['bank_name'].'</option>';
	}
	return $sc;
}

function bankLogoUrl($code)
{
	$link	= WEB_ROOT.'img/bank/'.$code.'.png';
	$file = realpath(DOC_ROOT.$link);
	if( ! file_exists($file) )
	{
		$link = WEB_ROOT.'img/bank/noimg.png';
	}
	return $link;
}

function getBankName($code)
{
	$sc = '';
	$qs = dbQuery("SELECT bank_name FROM tbl_bank WHERE code = '".$code."'");
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}

function getAccountNo($id_account)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT acc_no FROM tbl_bank_account WHERE id_account = ".$id_account);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}
function getBankAccount($id_acc)
{
	$sc = '';
	$qs = dbQuery("SELECT * FROM tbl_bank_account WHERE id_account = ".$id_acc);
	return dbFetchArray($qs);	
}
?>
