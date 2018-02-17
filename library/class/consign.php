<?php
class consign{

	public function __construct($id = ''){
		if( $id != '')
		{
			$sql 								= dbQuery("SELECT * FROM tbl_order_consign WHERE id_order_consign = ".$id);
			$consign 						= dbFetchArray($sql);
			$this->id_order_consign 	= $consign['id_order_consign'];
			$this->date_add 				= $consign['date_add'];
			$this->id_customer 			= $consign['id_customer'];
			$this->reference 				= $consign['reference'];
			$this->comment 				= $consign['comment'];
			$this->consign_status 		= $consign['consign_status'];
			$this->id_zone 				= $consign['id_zone'];
			$this->id_consign_check 	= $consign['id_consign_check'];
			$this->id_employee			= $consign['id_employee'];
			$this->date_upd 				= $consign['date_upd'];
		}
	}
	
	public function addNewConsign(array $data)
	{
		$qs = "INSERT INTO tbl_order_consign (reference, id_customer, date_add, comment, consign_status, id_zone, id_consign_check, id_employee) VALUES ";
		$qs .= "('".$data['reference']."', ".$data['id_customer'].", '".$data['date_add']."', '".$data['comment']."', ".$data['consign_status'].", ".$data['id_zone'].", ".$data['id_consign_check'].", ".$data['id_employee'].")";
		$rs = dbQuery($qs);
		if( $rs )
		{
			return dbInsertId();
		}
		else
		{
			return false;	
		}
	}
	
	public function updateConsign($id, array $data)
	{
		if( isset($data['date_add']) ){
			$rs = dbQuery("UPDATE tbl_order_consign SET id_customer = ".$data['id_customer'].", date_add = '".$data['date_add']."', id_zone = ".$data['id_zone'].", id_employee = ".$data['id_employee']." WHERE id_order_consign = ".$id);
		}else{
			$rs = dbQuery("UPDATE tbl_order_consign SET id_customer = ".$data['id_customer'].", id_zone = ".$data['id_zone'].", id_employee = ".$data['id_employee']." WHERE id_order_consign = ".$id);
		}
		return $rs;
	}
	
	public function dropConsign($id)
	{
		return dbQuery("DELETE FROM tbl_order_consign WHERE id_order_consign = ".$id);	
	}
	
	public function insertConsignDetail(array $data)
	{	
		$qs = "INSERT INTO tbl_order_consign_detail (id_order_consign, id_product_attribute, product_price, reduction_percent, reduction_amount, qty) VALUES ";
		$qs .= "(".$data['id'].", ".$data['id_pa'].", ".$data['price'].", ".$data['p_dis'].", ".$data['a_dis'].", ".$data['qty'].")";
		return dbQuery($qs);
	}
	
	public function updateConsignDetail($id_cd, array $data)
	{
		return dbQuery("UPDATE tbl_order_consign_detail SET qty = qty + ".$data['qty']." WHERE id_order_consign_detail = ".$id_cd);
	}
	
	public function updatePriceAndDiscount($id_cd, $price, $p_dis, $a_dis)
	{
		return dbQuery("UPDATE tbl_order_consign_detail SET product_price = ".$price.", reduction_percent = ".$p_dis.", reduction_amount = ".$a_dis." WHERE id_order_consign_detail = ".$id_cd);
	}
	
	public function deleteConsignDetail($id_cd)
	{
		return dbQuery("DELETE FROM tbl_order_consign_detail WHERE id_order_consign_detail = ".$id_cd);	
	}
	
	public function dropConsignDetail($id)
	{
		return dbQuery("DELETE FROM tbl_order_consign_detail WHERE id_order_consign = ".$id);	
	}
	
	public function isExactlyExists($id, $id_pa, $price, $p_dis, $a_dis)
	{
		$id_cd = 0;
		$qs = dbQuery("SELECT id_order_consign_detail FROM tbl_order_consign_detail WHERE id_order_consign = ". $id ." AND id_product_attribute = ". $id_pa ." AND product_price = ". $price ." AND reduction_percent = ". $p_dis ." AND reduction_amount = ". $a_dis);
		if( dbNumRows($qs) == 1 )
		{
			list( $id_cd ) = dbFetchArray($qs);
		}
		return $id_cd;
	}
	
	public function get_zone($id_customer){
		list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_order_consignment WHERE id_customer = $id_customer"));
		return $id_zone;
	}
	public function order_qty($id_product_attribute,$id_zone){
		list($qty) = dbFetchArray(dbQuery("select qty from tbl_stock WHERE id_zone = '$id_zone' AND id_product_attribute = '$id_product_attribute'"));
		return $qty;
	}
	
	public function changeStatus($id, $status)
	{
		return dbQuery("UPDATE tbl_order_consign SET consign_status = ".$status." WHERE id_order_consign = ".$id);
	}
	
	public function getConsignItems($id)
	{
		return dbQuery("SELECT * FROM tbl_order_consign_detail WHERE id_order_consign = ".$id);
	}
	
	
	public function consignSold($ds)
	{
		$discount_amount = $this->discountAmount($ds['sold_qty'], $ds['price'], $ds['red_percent'], $ds['red_amount']);
		$final_price			= $this->finalPrice($ds['price'], $ds['red_percent'], $ds['red_amount']);
		$total_amount 		= $final_price * $ds['sold_qty'];
		$total_cost			= $ds['sold_qty'] * $ds['cost'];
		$qs = "INSERT INTO tbl_order_detail_sold ";
		$qs .= "(id_order, reference, id_role, id_customer, id_employee, id_sale, id_product, id_product_attribute, product_name, product_reference, barcode, product_price, order_qty, sold_qty, ";
		$qs .= "reduction_percent, reduction_amount, discount_amount, final_price, total_amount, date_upd, cost, total_cost)";
		$qs .= " VALUES ";
		$qs .= "(0, '".$ds['reference']."', 5, ".$ds['id_cus'].", ".$ds['id_emp'].", ".$ds['id_sale'].", ".$ds['id_pd'].", ".$ds['id_pa'].", '".$ds['p_name']."', '".$ds['p_reference']."', '".$ds['barcode']."', ".$ds['price'].", ";
		$qs .= $ds['order_qty'].", ".$ds['sold_qty'].", ".$ds['red_percent'].", ".$ds['red_amount'].", ".$discount_amount.", ";
		$qs .= $final_price.", ".$total_amount.", '".$ds['date_upd']."', ".$ds['cost'].", ".$total_cost.")";
		
		return dbQuery($qs);
	}
	
	public function getConsignSold($reference)
	{
		return dbQuery("SELECT * FROM tbl_order_detail_sold WHERE reference = '".$reference."' AND id_role = 5");
	}
	
	public function getConsignItemArray($id_cd)
	{
		$qs = dbQuery("SELECT * FROM tbl_order_consign_detail WHERE id_order_consign_detail = ".$id_cd);
		return dbFetchArray($qs);
	}
	
	public function dropItemMovement($reference, $id_pa)
	{
		return dbQuery("DELETE FROM tbl_stock_movement WHERE reference = '".$reference."' AND id_product_attribute = ".$id_pa);
	}
	
	public function dropItemSold($reference, $id_pa)
	{
		return dbQuery("DELETE FROM tbl_order_detail_sold WHERE reference = '".$reference."' AND id_product_attribute = ".$id_pa." AND id_role = 5 ");	
	}
	
	public function discountAmount($qty, $price, $re_percent, $re_amount)
	{
		$dis = 0;
		if( $re_percent != 0 )
		{
			$dis = $qty * ($price * ($re_percent * 0.01) );
		}
		else if( $re_amount != 0)
		{
			$dis = $qty * $re_amount;
		}
		return $dis;
	}
	
	public function finalPrice($price, $re_percent, $re_amount)
	{
		$dis = $price;
		if( $re_percent != 0 )
		{
			$dis = $price - ( $price * ($re_percent * 0.01) );
		}
		else if( $re_amount != 0)
		{
			$dis = $price - $re_amount;
		}
		return $dis;
	}
	
	public function checkBarcode($barcode)
	{	
		$qs = dbQuery("SELECT id_product_attribute, reference, price FROM tbl_product_attribute WHERE barcode = '".$barcode."'");
		if( dbNumRows($qs) == 1 )
		{
			return dbFetchObject($qs);	
		}
		else
		{
			return false;
		}
	}
	
	public function getItem($id_pa)
	{	
		$qs = dbQuery("SELECT id_product_attribute, reference, price FROM tbl_product_attribute WHERE id_product_attribute = '".$id_pa."'");
		if( dbNumRows($qs) == 1 )
		{
			return dbFetchObject($qs);
		}
		else
		{
			return false;
		}
	}
	
	public function getItemByReference($reference)
	{	
		$qs = dbQuery("SELECT id_product_attribute, reference, price FROM tbl_product_attribute WHERE reference = '".$reference."'");
		if( dbNumRows($qs) == 1 )
		{
			return dbFetchObject($qs);
		}
		else
		{
			return false;
		}
	}
	
	public function stockConsignZone($id_pa, $id_zone)
	{
		$qty = 0;
		$qs = dbQuery("SELECT qty FROM tbl_stock WHERE id_product_attribute = ".$id_pa." AND id_zone = ".$id_zone);
		if( dbNumRows($qs) == 1 )
		{
			list($qty) = dbFetchArray($qs);
		}
		return $qty;
	}
	
	public function getProductPrice($id_pa)
	{
		$price = 0.00;
		$qs = dbQuery("SELECT price FROM tbl_product_attribute WHERE id_product_attribute = ".$id_pa);
		if( dbNumRows($qs) == 1 )
		{
			list($price) = dbFetchArray($qs);
		}
		return $price;
	}
	
	public function getConsignStatus($id)
	{
		$cst = 1;
		$qs = dbQuery("SELECT consign_status FROM tbl_order_consign WHERE id_order_consign = ".$id);
		if( dbNumRows($qs) == 1 )
		{
			list( $cst ) = dbFetchArray($qs);
		}
		return $cst;
	}
	
	public function getConsignReference($id)
	{
		$ref = '';
		$qs = dbQuery("SELECT reference FROM tbl_order_consign WHERE id_order_consign = ".$id);
		if( dbNumRows($qs) == 1 )
		{
			list( $ref ) = dbFetchArray($qs);
		}
		return $ref;
	}
	
	public function createDiscountLogs(array $ds, $id_emp, $id_apv, $price, $p_dis, $a_dis)
	{
		$dif = 0;
		if( $price != $ds['product_price'] ){ $dif++; }
		if( $p_dis != $ds['reduction_percent'] ){ $dif++; }
		if( $a_dis != $ds['reduction_amount'] ){ $dif++; }
		if( $dif )
		{
			$qs = "INSERT INTO tbl_discount_consign_edit ( id_product_attribute, reference, original_price, new_price, original_p_dis, new_p_dis, original_a_dis, new_a_dis, id_employee, id_approver) ";
			$qs .= "VALUES (".$ds['id_product_attribute'].", '".$this->getConsignReference($ds['id_order_consign'])."' , ".$ds['product_price'].", ".$price.", ".$ds['reduction_percent'].", ".$p_dis.", ".$ds['reduction_amount'].", ".$a_dis.", ".$id_emp.", ".$id_apv.")";
			return dbQuery($qs);
		}
		else
		{
			return true;
		}
	}
}//end class


?>
