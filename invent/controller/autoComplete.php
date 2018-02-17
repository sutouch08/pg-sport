<?php
require "../../library/config.php";
require"../../library/functions.php";
require "../function/tools.php";


if( isset( $_GET['get_order_reference'] ) && isset( $_REQUEST['term'] ) && isset( $_GET['id_customer'] ) )
{
	$txt = $_REQUEST['term'];
	if( $txt == "*" )
	{
		$qs = dbQuery("SELECT reference FROM tbl_order WHERE id_customer = ".$_GET['id_customer']." AND role = 1 AND current_state = 9 ");	
	}
	else
	{
		$qs = dbQuery("SELECT reference FROM tbl_order WHERE id_customer = ".$_GET['id_customer']." AND role = 1 AND current_state = 9 AND reference LIKE '%".$txt."%'");	
	}
	$data = array();
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$data[] = $rs['reference'];	
		}
	}
	else
	{
		$data[] = "ไม่พบข้อมูล";
	}
	echo json_encode($data);
}

if( isset( $_GET['getOrderReference'] ) && isset( $_REQUEST['term'] ) )
{
	$qs = dbQuery("SELECT id_order, reference FROM tbl_order WHERE reference LIKE '%".$_REQUEST['term']."%'");
	$data = array();
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchObject($qs) )
		{
			$data[] = $rs->reference.' | '.$rs->id_order;
		}
	}
	else
	{
		$data[] = 'ไม่พบข้อมูล';	
	}
	echo json_encode($data);
}

/****************  Tranform Receive Reference *****************/
if( isset( $_GET['get_tranform_reference'] ) && isset( $_REQUEST['term'] ) )
{
	if( $_REQUEST['term'] == "*" )
	{
		$qs = dbQuery("SELECT order_reference FROM tbl_receive_tranform GROUP BY order_reference ORDER BY order_reference ASC")	;
	}
	else
	{
		$qs = dbQuery("SELECT order_reference FROM tbl_receive_tranform WHERE order_reference LIKE '%".$_REQUEST['term']."%' GROUP BY order_reference ORDER BY order_reference ASC");	
	}
	$data = array();
	if( dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$data[] = $rs['order_reference'];
		}
	}
	else
	{
		$data[] = "ไม่พบข้อมูล";
	}
	echo json_encode($data);
}
/****************   PO And Supplier ****************/
if( isset( $_REQUEST['term'] ) && isset( $_GET['get_po'] ) )
{
	if( $_REQUEST['term'] == "*" )
	{
		$qs = dbQuery("SELECT reference, name FROM tbl_po JOIN tbl_supplier ON tbl_po.id_supplier = tbl_supplier.id WHERE tbl_po.status != 0 ORDER BY date_add DESC");	
	}
	else
	{
		$qs = dbQuery("SELECT reference, name FROM tbl_po JOIN tbl_supplier ON tbl_po.id_supplier = tbl_supplier.id WHERE tbl_po.status != 0 AND (reference LIKE '%".$_REQUEST['term']."%' OR name LIKE '%".$_REQUEST['term']."%') ORDER BY reference DESC");	
	}
	$data = array();
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) ) :
			$data[] = $rs['reference']." | ".$rs['name'];
		endwhile;
	}
	else
	{
		$data = "ไม่พบข้อมูล";
	}
	echo json_encode($data);
}

/*****************  PO And Supplier  ************/
if( isset( $_REQUEST['term'] ) && isset( $_GET['get_active_po'] ) )
{
	if( $_REQUEST['term'] == "*")
	{
		$qs = dbQuery("SELECT id_po, reference, name FROM tbl_po JOIN tbl_supplier ON tbl_po.id_supplier = tbl_supplier.id WHERE tbl_po.status != 0 AND tbl_po.valid = 0");	
	}else{
		$qs = dbQuery("SELECT id_po, reference, name FROM tbl_po JOIN tbl_supplier ON tbl_po.id_supplier = tbl_supplier.id WHERE (reference LIKE '%".$_REQUEST['term']."%' OR name LIKE '%".$_REQUEST['term']."%') AND tbl_po.status != 0 AND tbl_po.valid = 0");
	}
	$data = array();
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$data[] = $rs['id_po']." | ".$rs['reference']." | ".$rs['name'];
		}
	}else{
		$data[] = "ไม่พบข้อมูล";
	}
	echo json_encode($data);
}

/************************* Supplier Code And Name  ***************/

if( isset( $_REQUEST['term'] ) && isset( $_GET['get_supplier'] ) )
{
	if( $_REQUEST['term'] == "*" )
	{
		$qs = dbQuery("SELECT id, code, name FROM tbl_supplier");
	}
	else
	{
		$qs = dbQuery("SELECT id, code, name FROM tbl_supplier WHERE code LIKE '%".$_REQUEST['term']."%' OR name LIKE '%".$_REQUEST['term']."%'");	
	}
	$data = array();
	if( dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs)) :
			$data[] = $rs['code']." | ".$rs['name']." | ".$rs['id'];
		endwhile;	
	}
	else
	{
		$data[] = "ไม่พบข้อมูล";
	}
	echo json_encode($data);
}

/***************  Supplier Code ******************/

if( isset( $_REQUEST['term'] ) && isset( $_GET['get_supplier_code'] ) ) 
{
	if( $_REQUEST['term'] == "*")
	{	
		$qs = dbQuery("SELECT id, code, name FROM tbl_supplier");
	}else{
		$qs = dbQuery("SELECT id, code, name FROM tbl_supplier WHERE code LIKE '%".$_REQUEST['term']."%'");
	}
	$data = array();
	if( dbNumRows($qs) >0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$data[] = $rs['code']." : ".$rs['name']." : ".$rs['id'];	
		}
	}else{
		$data[] = "ไม่พบข้อมูล";
	}
	echo json_encode($data);
}

/***************  Supplier Name ******************/

if( isset( $_REQUEST['term'] ) && isset( $_GET['get_supplier_name'] ) ) 
{
	if( $_REQUEST['term'] == "*")
	{	
		$qs = dbQuery("SELECT id, code, name FROM tbl_supplier");
	}else{
		$qs = dbQuery("SELECT id, code, name FROM tbl_supplier WHERE name LIKE '%".$_REQUEST['term']."%'");
	}
	$data = array();
	if( dbNumRows($qs) >0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$data[] = $rs['code']." : ".$rs['name']." : ".$rs['id'];	
		}
	}else{
		$data[] = "ไม่พบข้อมูล";
	}
	echo json_encode($data);
}


/************* Products ***************/

if( isset($_REQUEST['term']) && isset( $_GET['getProductReferenceOnly'] ) )
{
	$t = $_REQUEST['term'];
	if( $t == "*")
	{
		$qs = dbQuery("SELECT reference FROM tbl_product_attribute ORDER BY id_product ASC");	
	}
	else
	{
		$qs = dbQuery("SELECT reference FROM tbl_product_attribute WHERE reference LIKE '%".$t."%' ORDER BY reference ASC");
	}
	$data = array();
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$data[] = $rs['reference'];
		}
	}
	else
	{
		$data[] = "ไม่พบข้อมูล";	
	}
	echo json_encode($data);	
}

if( isset($_REQUEST['term']) && isset( $_GET['get_product_attribute'] ) )
{
	$t = $_REQUEST['term'];
	if( isset( $_GET['no_visual'] ) )
	{
		
	}
	if( $t == "*")
	{
		$qs = dbQuery("SELECT id_product_attribute, reference FROM tbl_product_attribute ORDER BY id_product ASC");	
	}
	else
	{
		$qs = dbQuery("SELECT id_product_attribute, reference FROM tbl_product_attribute WHERE reference LIKE '%".$t."%' ORDER BY reference ASC");
	}
	$data = array();
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$data[] = $rs['reference']." | ".$rs['id_product_attribute'];	
		}
	}
	else
	{
		$data[] = "ไม่พบข้อมูล";	
	}
	echo json_encode($data);
}



if( isset( $_REQUEST['term'] ) && isset( $_GET['getProductCode'] ) )
{
	$data = array();
	$qs = dbQuery("SELECT id_product, product_code, product_name FROM tbl_product WHERE product_code LIKE '%".$_REQUEST['term']."%' OR product_name LIKE '%".$_REQUEST['term']."%' ORDER BY product_code ASC");
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchObject($qs) )
		{
			$data[] = $rs->product_code.' | '.$rs->product_name.' | '.$rs->id_product;
		}
		$data = json_encode($data);
	}
	else
	{
		$data = 'nodata';	
	}
	echo $data;
}

if(isset($_REQUEST['term']) && isset($_GET['get_product_id'])){
	$sql = dbQuery("SELECT id_product, product_code, product_name FROM tbl_product WHERE product_code LIKE'%".$_REQUEST['term']."%' OR product_name LIKE '%".$_REQUEST['term']."%'  ORDER BY id_product ASC");
	$data = array();
	while($row = dbFetchArray($sql)){
		$data[] = $row['product_code']." : ".$row['product_name']." : ".$row['id_product'];
	}
	echo json_encode($data);
}

if(isset($_REQUEST['term']) && isset($_GET['product_code']))
{
	if($_REQUEST['term'] == "*")
	{
		$sql = dbQuery("SELECT product_code FROM tbl_product ORDER BY product_code ASC");
	}
	else
	{
		$sql = dbQuery("SELECT product_code FROM tbl_product WHERE product_code LIKE'%".$_REQUEST['term']."%' ORDER BY product_code ASC");
	}
	$data = array();
	while($row = dbFetchArray($sql)){
		$data[] = $row['product_code'];
	}
	echo json_encode($data);
}

if(isset($_GET['all_product_code']))
{
	$qs = dbQuery("SELECT product_code FROM tbl_product");
	$data = array();
	while($row = dbFetchArray($qs)){
		$data[] = $row['product_code'];
	}
	echo json_encode($data);
}

if(isset($_REQUEST['term']) && isset($_GET['get_product_code'])){
	if($_REQUEST['term'] == "*")
	{
		$sql = dbQuery("SELECT product_code, product_name FROM tbl_product ORDER BY product_code ASC");
	}
	else
	{
		$sql = dbQuery("SELECT product_code, product_name FROM tbl_product WHERE product_code LIKE'%".$_REQUEST['term']."%' OR product_name LIKE '%".$_REQUEST['term']."%'  ORDER BY product_code ASC");
	}
	$data = array();
	while($row = dbFetchArray($sql)){
		$data[] = $row['product_code']." : ".$row['product_name'];
	}
	echo json_encode($data);
}


/****************** Employee  *******************/
if( isset($_REQUEST['term']) && isset($_GET['get_employee_id']) ){
	$sql = dbQuery("SELECT id_employee, first_name, last_name FROM tbl_employee WHERE first_name LIKE '%".$_REQUEST['term']."%' OR last_name LIKE '%".$_REQUEST['term']."%' ORDER BY first_name ASC");
	$data = array();
	while( $rs = dbFetchArray($sql) ){
		$data[] = $rs['id_employee']." : ".$rs['first_name']." ".$rs['last_name'];
	}
	echo json_encode($data);
}

if( isset($_REQUEST['term']) && isset($_GET['get_employee']) )
{
	$t = $_REQUEST['term'];
	if( $t == '*' )
	{
		$qs = dbQuery("SELECT id_employee, first_name, last_name FROM tbl_employee ORDER BY first_name ASC");
	}
	else
	{
		$qs = dbQuery("SELECT id_employee, first_name, last_name FROM tbl_employee WHERE first_name LIKE '%".$t."%' OR last_name LIKE '%".$t."%' ORDER BY first_name ASC");	
	}
	$data = array();
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$data[] = $rs['first_name']." ".$rs['last_name']." | ".$rs['id_employee'];	
		}
	}
	else
	{
		$data[] = "ไม่พบข้อมูล";	
	}
	echo json_encode($data);
}

/****************** Sponsor customer  *******************/
if( isset($_REQUEST['term']) && isset($_GET['get_sponsor_id']) ){
	$sql = dbQuery("SELECT tbl_sponsor.id_customer, first_name, last_name FROM tbl_sponsor JOIN tbl_customer ON tbl_sponsor.id_customer = tbl_customer.id_customer WHERE first_name LIKE '%".$_REQUEST['term']."%' OR last_name LIKE '%".$_REQUEST['term']."%' ORDER BY first_name ASC");
	$data = array();
	while( $rs = dbFetchArray($sql) ){
		$data[] = $rs['id_customer']." : ".$rs['first_name']." ".$rs['last_name'];
	}
	echo json_encode($data);
}

/*********************** Support Employee  *****************/

if( isset($_REQUEST['term']) && isset($_GET['get_support_id']) ){
	$sql = dbQuery("SELECT tbl_support.id_employee, first_name, last_name FROM tbl_support JOIN tbl_employee ON tbl_support.id_employee = tbl_employee.id_employee WHERE first_name LIKE '%".$_REQUEST['term']."%' OR last_name LIKE '%".$_REQUEST['term']."%' ORDER BY first_name ASC");
	$data = array();
	while($rs = dbFetchArray($sql) ){
		$data[] = $rs['id_employee']." : ".$rs['first_name']." ".$rs['last_name'];
	}
	echo json_encode($data);
}

/**********************  Customer ***********************/
if( isset($_REQUEST['term']) && isset($_GET['get_customer_id']) ){
	$sql = dbQuery("SELECT id_customer, customer_code, first_name, last_name FROM tbl_customer WHERE first_name LIKE '%".$_REQUEST['term']."%' OR last_name LIKE '%".$_REQUEST['term']."%' ");
	$data = array();
	while( $rs = dbFetchArray($sql) ){
		$data[] = $rs['id_customer']." : ".$rs['first_name']." ".$rs['last_name'];
	}
	echo json_encode($data);
}

if( isset($_REQUEST['term']) && isset( $_GET['get_customer'] ) )
{
	$text = trim($_REQUEST['term']);
	if( $text == "*" )	
	{ 
		$qs = dbQuery("SELECT id_customer, customer_code, first_name, last_name FROM tbl_customer ORDER BY customer_code");
	}
	else
	{
		$qs = dbQuery("SELECT id_customer, customer_code, first_name, last_name FROM tbl_customer WHERE customer_code LIKE '%".$text."%' OR first_name LIKE '%".$text."%' OR last_name LIKE '%".$text."%'");
	}
	if(dbNumRows($qs) > 0 )
	{
		$data = array();
		while($rs = dbFetchArray($qs) )
		{
			$data[] = $rs['customer_code']." | ".$rs['first_name']." ".$rs['last_name']." | ".$rs['id_customer'];
		}
	}
	else
	{
		$data = array("ไม่พบข้อมูล");	
	}
	echo json_encode($data);
}

if( isset($_REQUEST['term']) && isset($_GET['consign_zone']) )
{
	$text = trim($_REQUEST['term']);
	if( $text == "*" )
	{
		$qs = dbQuery("SELECT id_zone, zone_name FROM tbl_zone WHERE id_warehouse = 2");
	}
	else
	{
		$qs = dbQuery("SELECT id_zone, zone_name FROM tbl_zone WHERE id_warehouse = 2 AND zone_name LIKE '%".$text."%'");
	}
	if( dbNumRows($qs) > 0 )
	{
		$data = array();
		while($rs = dbFetchArray($qs) )
		{
			$data[] = $rs['zone_name']." | ".$rs['id_zone'];
		}
	}
	else
	{
		$data = array("ไม่พบข้อมูล");
	}
	echo json_encode($data);	
}

/********************  Lend Reference  **************/
if( isset($_REQUEST['term']) && isset($_GET['get_lend_id']) ){
	$sql = dbQuery("SELECT id_order, reference FROM tbl_order WHERE reference LIKE '%".$_REQUEST['term']."%' AND role = 3 ");
	$data = array();
	while( $rs = dbFetchArray($sql) ){
		$data[] = $rs['id_order']." : ".$rs['reference'];
	}
	echo json_encode($data);
}

/***********************  รายชื่อพนักงานที่ได้มีงบประมาณในการเบิกอภินันทนาการ  ***********************/
if( isset($_REQUEST['term']) && isset($_GET['get_support_employee_id']) )
{
	$sql = dbQuery("SELECT tbl_employee.id_employee, first_name, last_name FROM tbl_employee JOIN tbl_support ON tbl_employee.id_employee = tbl_support.id_employee WHERE first_name LIKE '%".$_REQUEST['term']."%' OR last_name LIKE '%".$_REQUEST['term']."%' "); 	
	$data = array();
	while( $rs = dbFetchArray($sql) )
	{
		$data[] = $rs['id_employee']." : ".$rs['first_name']." ".$rs['last_name'];
	}
	echo json_encode($data);
}

/********************************* ชื่อและไอดีโซน *************************************/
if( isset($_GET['get_zone_name']) && isset($_REQUEST['term']) )
{
	$text = $_REQUEST['term'];
	$qs = dbQuery("SELECT id_zone, zone_name FROM tbl_zone WHERE zone_name LIKE '%".$text."%' ");
	$data = array();
	while($rs = dbFetchArray($qs))
	{
		$data[] = $rs['id_zone']." : ".$rs['zone_name'];
	}
	echo json_encode($data);
}

if( isset( $_GET['get_zone'] ) && isset( $_REQUEST['term'] ) )
{
	$id_wh = $_GET['id_warehouse'];
	if( $id_wh )
	{
		$qs = dbQuery("SELECT zone_name FROM tbl_zone WHERE id_warehouse = ".$id_wh." AND zone_name LIKE '%".$_REQUEST['term']."%' ORDER BY zone_name ASC");
	}
	else
	{
	$qs = dbQuery("SELECT zone_name FROM tbl_zone WHERE zone_name LIKE '%".$_REQUEST['term']."%' ORDER BY zone_name ASC");
	}
	$data = array();
	if( dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$data[] = $rs['zone_name'];	
		}
	}
	else
	{
		$data[] = "ไม่มีข้อมูล";
	}
	echo json_encode($data);
}


if( isset( $_GET['getZone'] ) && isset( $_REQUEST['term'] ) )
{
	$qs = dbQuery("SELECT id_zone, id_warehouse, zone_name FROM tbl_zone WHERE zone_name LIKE '%".$_REQUEST['term']."%' ORDER BY zone_name ASC");
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$ds[] = $rs['zone_name'].' | '.$rs['id_zone'].' | '.$rs['id_warehouse'];
		}
	}
	else
	{
		$ds[] = 'ไม่พบข้อมูล';
	}
	echo json_encode($ds);
}


//------------- Transfer
if( isset( $_GET['getTransferZone'] ) && isset( $_REQUEST['term'] ) )
{
	$id_wh = $_GET['id_warehouse'];
	$qs = dbQuery("SELECT id_zone, zone_name FROM tbl_zone WHERE id_warehouse = ".$id_wh." AND (zone_name LIKE '%".$_REQUEST['term']."%' OR barcode_zone LIKE '%".$_REQUEST['term']."%') ORDER BY zone_name ASC");
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchObject($qs) )
		{
			$ds[] = $rs->zone_name.' | '.$rs->id_zone;	
		}
	}
	else
	{
		$ds[] = 'ไม่พบข้อมูล';	
	}
	
	echo json_encode($ds);
}


/********************* รายชื่อผู้จัดส่ง *********************/
if( isset( $_GET['get_sender'] ) )
{
	$txt = $_REQUEST['term'];
	$qs = dbQuery("SELECT id_sender, name FROM tbl_sender WHERE name LIKE '%".$txt."%'");
	$data = array();
	while( $rs = dbFetchArray($qs) )
	{
		$data[] = $rs['id_sender']." | ".$rs['name'];	
	}
	echo json_encode($data);
}

?>