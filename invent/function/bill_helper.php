<?php


function get_current_state($id_order)
{
	$qs = dbQuery("SELECT current_state FROM tbl_order WHERE id_order = ".$id_order);
	list($state) = dbFetchArray($qs);
	return $state;	
}






function show_discount($percent, $amount)
{
	 $unit 	= " %";
	 $dis	= 0.00;
	if($percent != 0.00){ $dis = $percent; }else{ $dis = number_format($amount, 2); $unit = ""; }
	return $dis.$unit;
}







function get_temp_qty($id_order, $id_product_attribute)
{
	$qty = 0;
	$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_temp WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_product_attribute);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$qty = $rs['qty'];
	}
	return $qty;
}	







function get_sold_data($id_order, $id_product_attribute)
{
	$rs = false;
	$role = order_role($id_order);
	if($role == 5 )
	{
		$qr = "SELECT SUM(qty) AS sold_qty, reduction_percent, reduction_amount ";
		$qr .= "FROM tbl_qc JOIN tbl_order_detail ON tbl_qc.id_order = tbl_order_detail.id_order AND tbl_qc.id_product_attribute = tbl_order_detail.id_product_attribute ";
		$qr .= "WHERE tbl_qc.id_order = ".$id_order." AND tbl_qc.id_product_attribute = ".$id_product_attribute." AND tbl_qc.valid = 1";
		$qs = dbQuery($qr);
	}
	else
	{
		$qs = dbQuery("SELECT * FROM tbl_order_detail_sold WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_product_attribute);
	}
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);	
	}
	return $rs;
}







function order_role($id_order)
{
	$role = 1;
	$qs = dbQuery("SELECT role FROM tbl_order WHERE id_order = ".$id_order);
	if(dbNumRows($qs) == 1 )
	{
		list($role) = dbFetchArray($qs);
	}
	return $role;
}






//-----------------  Id Zone ในคลังฝากขาย จากออเดอร์ฝากขาย
function getConsignmentIdZone($id_order)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_zone FROM tbl_order_consignment WHERE id_order = ".$id_order);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray( $qs );	
	}
	return $sc;
}




//----------------------------  เปลี่นสถานะใน temp ใช้ในการเปิดบิล
function updateTemp($status, $current_status, $id_order, $id_pa, $limit)
{
	return dbQuery("UPDATE tbl_temp SET status = ".$status." WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa." AND status = ".$current_status." LIMIT ".$limit);
}






function createErrorLog($functionName, $id_order, $reference, $id_pa)
{
	return dbQuery("INSERT INTO tbl_error_log ( function_name, id_order, reference, id_product_attribute ) VALUES ('".$functionName."', ".$id_order.", '".$reference."', ".$id_pa.")");	
}







function doc_type($role)
{
	switch($role){
		case 1 :
			$content="order";
			$title = "Packing List";
			break;
		case 2 : 
			$content = "requisition";
			$title = "ใบเบิกสินค้า / Requisition Product";
			break;
		case 3 :
			$content = "lend";
			$title = "ใบยืมสินค้า / Lend Product";
			break;
		case 4 :
			$content = "sponsor";
			$title = "รายการสปอนเซอร์สโมสร / Sponsor Order";
			break;
		case 5 :
			$content = "consignment";
			$title = "ใบส่งของ / ใบแจ้งหนี้  สินค้าฝากขาย";
			break;
		case 6 :
			$content = "requisition";
			$title = "ใบส่งของ / ใบเบิกสินค้าเพื่อแปรรูป";
			break;
		case 7 :
			$content = "order_support";
			$title = "รายการเบิกอภินันทนาการ / Support Order";
			break;
		default :
			$content = "order";
			$title = "ใบส่งของ / ใบแจ้งหนี้";
			break;
	}	
	$type = array("content"=>$content, "title"=>$title);
	return $type;
}







function get_header($order)
{
	if($order->role == 3 )
	{
				$header		= array(
								"เลขที่เอกสาร"=>$order->reference, 
								"วันที่"=>thaiDate($order->date_add), 
								"ผู้ยืม"=>employee_name($order->id_employee), 
								"ผู้ทำรายการ" => employee_name(get_lend_user_id($order->id_order)),
								"เลขที่อ้างอิง"	=> getInvoice($order->id_order)
							);
	}
	else if( $order->role == 2 || $order->role == 6 )
	{
		$header		= array(
									"ลูกค้า"=>customer_name($order->id_customer), 
									"วันที่"=>thaiDate($order->date_add), 
									"ผู้เบิก"=>employee_name($order->id_employee), 
									"เลขที่เอกสาร"=>$order->reference,
									"เลขที่อ้างอิง"	=> getInvoice($order->id_order)
									
									);	
	}
	else if( $order->role == 7)
	{
		$header	= array(
									"ผู้รับ"=>customer_name($order->id_customer), 
									"วันที่"=>thaiDate($order->date_add), 
									"ผู้เบิก"=>employee_name($order->id_employee), 
									"เลขที่เอกสาร"=>$order->reference, 
									"ผู้ดำเนินการ" => employee_name(get_id_user_support($order->id_order)),
									"เลขที่อ้างอิง"	=> getInvoice($order->id_order)
									);		
	}
	else if( $order->role == 4 )
	{
		$header	= array(
								"ผู้รับ"=>customer_name($order->id_customer), 
								"วันที่"=>thaiDate($order->date_add), 
								"ผู้เบิก"=>employee_name($order->id_employee), 
								"เลขที่เอกสาร"=>$order->reference, 
								"ผู้ดำเนินการ" => employee_name(get_id_user_sponsor($order->id_order)),
								"เลขที่อ้างอิง"	=> getInvoice($order->id_order)
								);
	}
	else
	{
		$header	= array(
							"ลูกค้า"=>customer_name($order->id_customer), 
							"วันที่"=>thaiDate($order->date_add), 
							"พนักงานขาย"=>sale_name($order->id_sale), 
							"เลขที่เอกสาร"=>$order->reference,
							"เลขที่อ้างอิง"	=> getInvoice($order->id_order)
							);
	}	
	
	return $header;
}


?>