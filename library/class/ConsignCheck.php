<?php 
class consignCheck {
public $id_consign_check;
public $reference;
public $id_customer;
public $id_zone;
public $date_add;
public $date_upd;
public $consign_valid;
public $comment;

public function __construct($id = '')
{
	if( $id != '')
	{
		$qs = dbQuery("SELECT * FROM tbl_consign_check WHERE id_consign_check = ".$id);
		$rs = dbFetchObject($qs);
		$this->id_consign_check		= $rs->id_consign_check;
		$this->reference				= $rs->reference;
		$this->id_customer			= $rs->id_customer;
		$this->id_zone					= $rs->id_zone;
		$this->date_add				= $rs->date_add;
		$this->date_upd				= $rs->date_upd;
		$this->consign_valid			= $rs->consign_valid;
		$this->comment				= $rs->comment;	
	}
	
}

public function getCheckDiff($id)
{
	return dbQuery("SELECT id_product_attribute, qty_stock, qty_check FROM tbl_consign_check_detail  WHERE id_consign_check = ".$id." AND qty_stock > qty_check");	
}

public function changeStatus($id, $status)
{
	return dbQuery("UPDATE tbl_consign_check SET consign_valid = ".$status." WHERE id_consign_check = ".$id);
}
	
}
