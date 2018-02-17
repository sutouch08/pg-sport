<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
///////////////////  AutoComplete //////////////////////
if(isset($_GET['product'])&&isset($_REQUEST['term'])){
	if($_REQUEST['term'] =="*"){
		$qstring = "SELECT id_product_attribute AS id, reference FROM tbl_product_attribute";
	}else{
		$qstring = "SELECT id_product_attribute AS id, reference FROM tbl_product_attribute WHERE reference LIKE '%".$_REQUEST['term']."%'";
	}
	$result = dbQuery($qstring);//query the database for entries containing the term
if ($result->num_rows>0)
	{
		$data= array();
	while($row = $result->fetch_array())//loop through the retrieved values
		{
				$data[]=$row['reference'];
		}
		echo json_encode($data);//format the array into json data
	}else {
		echo "error";
	}

}
///////////////////  AutoComplete //////////////////////
if(isset($_GET['product_attribute'])&&isset($_REQUEST['term'])){
	if($_REQUEST['term'] =="*"){
		$qstring = "SELECT reference, product_name FROM tbl_product_attribute LEFT JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product";
	}else{
	$qstring = "SELECT reference, product_name FROM tbl_product_attribute LEFT JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product WHERE reference LIKE '%".$_REQUEST['term']."%' OR product_name LIKE '%".$_REQUEST['term']."%' ORDER BY reference ASC";
	}
	$result = dbQuery($qstring);//query the database for entries containing the term
if ($result->num_rows>0)
	{
		$data= array();
	while($row = $result->fetch_array())//loop through the retrieved values
		{
				$data[]=$row['reference'].":".$row['product_name'];
		}
		echo json_encode($data);//format the array into json data
	}else {
		echo "error";
	}

}
///////////////////  AutoComplete //////////////////////
if(isset($_GET['product_name'])&&isset($_REQUEST['term'])){
	if($_REQUEST['term'] =="*"){
		$qstring = "SELECT id_product, product_code, product_name FROM tbl_product ";
	}else{
		$qstring = "SELECT id_product, product_code, product_name FROM tbl_product WHERE product_code LIKE '%".$_REQUEST['term']."%' OR product_name LIKE'%".$_REQUEST['term']."%' ORDER BY product_code ASC";
	}
	$result = dbQuery($qstring);//query the database for entries containing the term
if ($result->num_rows>0)
	{
		$data= array();
	while($row = $result->fetch_array())//loop through the retrieved values
		{
				$data[]=$row['id_product'].":".$row['product_code'].":".$row['product_name'];
		}
		echo json_encode($data);//format the array into json data
	}else {
		echo "error";
	}

}
///////////////////  AutoComplete //////////////////////
if(isset($_GET['customer_name'])&&isset($_REQUEST['term'])){
	if($_REQUEST['term'] =="*"){
		$qstring = "SELECT id_customer, customer_code, first_name, last_name FROM tbl_customer";
	}else{
	$qstring = "SELECT id_customer, customer_code, first_name, last_name FROM tbl_customer WHERE customer_code LIKE '%".$_REQUEST['term']."%' OR first_name LIKE '%".$_REQUEST['term']."%' OR last_name LIKE '%".$_REQUEST['term']."%' ORDER BY customer_code ASC";
	}
	$result = dbQuery($qstring);//query the database for entries containing the term
if ($result->num_rows>0)
	{ 
		$data= array();
	while($row = $result->fetch_array())//loop through the retrieved values
		{
				$data[] = $row['customer_code'].":".$row['first_name']." ".$row['last_name'].":".$row['id_customer'];
		}
		echo  json_encode($data);//format the array into json data
	}else {
		echo "error";
	}
}
///////////////////  AutoComplete //////////////////////
if(isset($_GET['product_code'])&&isset($_REQUEST['term'])){
	if($_REQUEST['term']=="*"){
		$qstring = "SELECT reference FROM tbl_product_attribute";
	}else{
		$qstring = "SELECT reference FROM tbl_product_attribute WHERE reference LIKE '%".$_REQUEST['term']."%'";
	}
	$result = dbQuery($qstring);//query the database for entries containing the term
if ($result->num_rows>0)
	{ 
		$data= array();
	while($row = $result->fetch_array())//loop through the retrieved values
		{
				$data[]=$row['reference'];
		}
		echo  json_encode($data);//format the array into json data
	}else {
		echo "error";
	}
}
//************************************* add request order *********************************************//
if(isset($_GET['add_request'])&&isset($_POST['id_customer'])){
	$id_customer = $_POST['id_customer'];
	$date = dbDate($_POST['doc_date']);
	$id_employee = $_COOKIE['user_id'];
	$employee = new employee();
	$id_sale = $employee->get_id_sale($id_employee);
	$reference = get_max_request_reference("PREFIX_REQUEST_ORDER");
	if(dbQuery("INSERT INTO tbl_request_order (reference, id_customer, id_employee, id_sale) VALUES ('$reference', $id_customer, $id_employee, $id_sale)")){
		list($id_request_order) = dbFetchArray(dbQuery("SELECT id_request_order FROM tbl_request_order WHERE reference ='$reference'"));
		header("location: ../index.php?content=request&add=y&id_request_order=$id_request_order");
	}else{
		$message = "เพิ่มรายการไม่สำเร็จ";
		header("location: ../index.php?content=request&add=y&error=$message");
	}		
}
//************************************** Delete Request Detail  *******************************************//
if(isset($_GET['delete'])&&isset($_GET['id_request_order_detail'])){
	$id_request_order_detail = $_GET['id_request_order_detail'];
	list($id_request_order)=dbFetchArray(dbQuery("SELECT id_request_order FROM tbl_request_order_detail WHERE id_request_order_detail = $id_request_order_detail"));
	if(dbQuery("DELETE FROM tbl_request_order_detail WHERE id_request_order_detail = $id_request_order_detail")){
		$message = "ลบรายการเรียบร้อยแล้ว";
		header("location: ../index.php?content=request&add=y&id_request_order=$id_request_order&message=$message");	
	}else{
		$message = "ลบรายการไม่สำเร็จ";
		header("location: ../index.php?content=request&add=y&id_request_order=$id_request_order&error=$message");
	}
}
//***************************************** Delete Request Order ******************************************//
if(isset($_GET['delete_request'])&&isset($_GET['id_request_order'])){
	$id_request_order = $_GET['id_request_order'];
	if(dbQuery("DELETE FROM tbl_request_order_detail WHERE id_request_order = $id_request_order")){
		dbQuery("DELETE FROM tbl_request_order WHERE id_request_order = $id_request_order");
		$message = "ลบรายการเรียบร้อยแล้ว";
		header("location: ../index.php?content=request&message=$message");	
	}else{
		$message = "ลบรายการไม่สำเร็จ";
		header("location: ../index.php?content=request&error=$message");
	}
}
//**************************************  Save Request Order  ****************************************//
if(isset($_GET['save_request_order'])&&isset($_GET['id_request_order'])){
	$id_request_order = $_GET['id_request_order'];
	dbQuery("UPDATE tbl_request_order SET status = 1 WHERE id_request_order = $id_request_order");
	$message = "บันทึกเรียบร้อยเเล้ว";
	header("location: ../index.php?content=request&message=$message");
}




//************************************* chech stock ******************************************//

if(isset($_GET['check_stock'])&&isset($_GET['product_code'])){
	$reference = $_GET['product_code'];
	list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE reference = '$reference'"));
	$product = new product();
	$product->product_attribute_detail($id_product_attribute);
	list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
	$qty = ($product->available_qty($id_product_attribute) + $qty_moveing) - $product->order_qty($id_product_attribute);
	$data = $id_product_attribute.":".$qty;
	echo $data;
}


if(isset($_GET['check_stock'])&&isset($_GET['barcode'])){
	$reference = $_GET['barcode'];
	list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE barcode = '$reference'"));
	$product = new product();
	$id_product = $product->getProductId($id_product_attribute);
	$product->product_detail($id_product);
	$product->product_attribute_detail($id_product_attribute);
	$product_code = $product->reference;
	list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse = 1"));
	$qty = ($product->available_qty($id_product_attribute) + $qty_moveing) - $product->order_qty($id_product_attribute);
	$data = trim($id_product_attribute).":".$qty.":".$product_code;
	echo $data;
}

//// add order
if(isset($_GET['add'])&&isset($_POST['id_customer'])){
	$id_customer = $_POST['id_customer'];
	$customer = new customer($id_customer);
	$reference = get_max_role_reference("PREFIX_ORDER",1);
	$payment = $_POST['payment'];
	$role = 1; 
	$id_employee = $_POST['id_employee'];
	$id_cart = 0;
	if($customer->id_address !=""){ $id_address = $customer->id_address; }else{ $id_address = 0; } 
	$current_state = 1;
	$shipping_no = 0;
	$invoice_no = 0;
	$delivery_no = 0;
	$delivery_date = "";
	$comment = $_POST['comment'];
	$valid = 0;
	$date_add = dbDate($_POST['doc_date'], true);
	$date_upd = date('Y-m-d');
	if(isset($_POST['id_order'])&&$_POST['id_order'] !=""){
		$id_order = $_POST['id_order'];
		dbQuery("UPDATE tbl_order SET id_customer = $id_customer, id_address_delivery = $id_address, payment = '$payment', comment = '$comment', date_add = '$date_add', date_upd = '$date_upd' WHERE id_order = $id_order");
		header("location: ../index.php?content=order&add=y&id_order=$id_order&id_customer=$id_customer");
	}else{
	if(dbQuery("INSERT INTO tbl_order(reference, id_customer, id_employee, id_cart, id_address_delivery, current_state, payment, shipping_number, invoice_number, delivery_number, delivery_date, comment, valid, role, date_add, date_upd,order_status) VALUES ('$reference', $id_customer, $id_employee, $id_cart, $id_address, $current_state, '$payment', $shipping_no, $invoice_no, $delivery_no, '$delivery_date', '$comment', $valid, $role, '$date_add', NOW(),0)")){
		list($id_order) = dbFetchArray(dbQuery("SELECT id_order FROM tbl_order WHERE reference = '$reference' AND id_customer = $id_customer"));
		header("location: ../index.php?content=order&add=y&id_order=$id_order&id_customer=$id_customer");
	}else{
		$message = "ไม่สามารถเพิ่มออเดอร์ใหม่ในฐานข้อมูลได้";
		header("location: ../index.php?content=order&add=y&error=$message");
	}
	}
	
}

if(isset($_GET['reference'])){
	$reference = $_GET['reference'];
	$id_customer = $_GET['id_customer'];
	$sql = dbQuery("SELECT id_product_attribute, id_product FROM tbl_product_attribute WHERE reference ='$reference'");
	list($id_product_attribute, $id_product) = dbFetchArray($sql);
	$product = new product();
	$product->product_detail($id_product, $id_customer);
	$product->product_attribute_detail($id_product_attribute);
	echo $product->id_product_attribute.":".$product->product_sell.":".$product->available_qty();
}

if(isset($_GET['edit'])&&isset($_GET['state_change'])){
	$id_order = $_POST['id_order'];
	$id_employee = $_POST['id_employee'];
	$id_order_state = $_POST['order_state'];
	if($id_order_state != 0){
		if(order_state_change($id_order, $id_order_state, $id_employee)){
			$message = "เปลี่ยนสถานะเรียบร้อยแล้ว";
			header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&message=$message");
		}else{
			$message = "เปลี่ยนสถานะไม่สำเร็จ";
			header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&error=$message");
		}
		}else{
			$message = "คุณไม่ได้เลือกสถานะ";
			header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&error=$message");
		}
}
if(isset($_GET['edit_order'])&&isset($_POST['new_qty'])&&$_POST['new_qty'] !=""){
	$id_order = $_POST['id_order'];
	$id_product_attribute = $_POST['id_product_attribute'];
	$qty = $_POST['new_qty'];
	list($old_total_amount) = dbFetchArray(dbQuery("SELECT total_amount FROM tbl_order_detail WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute"));
	list($old_qty) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_temp WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute"));
	$order = new order($id_order);
	$customer = new customer($order->id_customer);
	if($customer->credit_amount != 0.00){
		$product = new product();
		$id_prodcut = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product, $order->id_customer);
		$product->product_attribute_detail($id_product_attribute);
		$total_amount = $qty * $product->product_sell; 
		$new_total_amount = $total_amount - $old_total_amount;
		if($qty<$old_qty){
			if($order->changeQty($id_product_attribute, $qty)){
					$message = "แก้ไขเรียบร้อยแล้ว";
					header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&message=$message");
			}else{
						$message = "แก้ไขไม่สำเร็จ";
						header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&error=$message");
						exit;
			}
		}else if($qty>$old_qty){
			if($order->changeQty($id_product_attribute, $qty)){
					dbQuery("UPDATE tbl_order_detail SET valid_detail = 0 WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
					$message = "แก้ไขเรียบร้อยแล้ว";
					header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&message=$message");
			}else{
						$message = "แก้ไขไม่สำเร็จ";
						header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&error=$message");
						exit;
			}
			
		}else{
			if($order->check_credit($new_total_amount)){
				if($order->changeQty($id_product_attribute, $qty)){
					dbQuery("UPDATE tbl_order_detail SET valid_detail = 1 WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
					$message = "แก้ไขเรียบร้อยแล้ว";
					header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&message=$message");
				}else{
						$message = "แก้ไขไม่สำเร็จ";
						header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&error=$message");
						exit;
				}
			}else{
				$message = "แก้ไขไม่สำเร็จ เนื่องจากเคดิตไม่พอ เครดิตคงเหลือ : ".$customer->credit_balance;
				header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&error=$message");
				exit;
			}
		}
	}else if($order->changeQty($id_product_attribute, $qty)){
			if($qty>$old_qty){ 	
				dbQuery("UPDATE tbl_order_detail SET valid_detail = 0 WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute"); 
			}else{
				dbQuery("UPDATE tbl_order_detail SET valid_detail = 1 WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
			}
					$message = "แก้ไขเรียบร้อยแล้ว";
					header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&message=$message");
	}else{
						$message = "แก้ไขไม่สำเร็จ";
					header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&error=$message");
						exit;
	}
}

if(isset($_GET['check_stock'])&&isset($_GET['id_product_attribute'])){
		$id_product_attribute = $_GET['id_product_attribute'];
		$old_qty = $_GET['qty'];
		$product = new product();
		$qty = $product->available_order_qty($id_product_attribute) + $old_qty;
		echo $qty;
}
if(isset($_GET['edit_order'])&&isset($_GET['insert_detail'])){
	$id_order = $_POST['id_order'];
	$id_product_attribute = trim($_POST['id_product_attribute']);
	$qty = $_POST['qty'];
	$order = new order($id_order);
	$product = new product();
	$id_prodcut = $product->getProductId($id_product_attribute);
	$product->product_detail($id_product, $order->id_customer);
	$product->product_attribute_detail($id_product_attribute);
	if($order->insertDetail($id_product_attribute, $qty)){
		$message = "เพิ่มสินค้าเรียบร้อยแล้ว";
		header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&message=$message");

	}else{
		$message = "เพิ่มสินค้าไม่สำเร็จ";
		header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&error=$message");
	}	
}

if(isset($_GET['add'])&&isset($_GET['insert_detail'])){
	$id_order = $_POST['id_order'];
	$id_product_attribute = trim($_POST['id_product_attribute']);
	$qty = $_POST['qty'];
	$order = new order($id_order);
	$product = new product();
	$id_prodcut = $product->getProductId($id_product_attribute);
	$product->product_detail($id_product, $order->id_customer);
	$product->product_attribute_detail($id_product_attribute);
	if($order->insertDetail($id_product_attribute, $qty)){
		$message = "เพิ่มสินค้าเรียบร้อยแล้ว";
			header("location: ../index.php?content=order&add=y&id_order=$id_order&message=$message");
	}else{
		$message = "เพิ่มสินค้าไม่สำเร็จ";
		header("location: ../index.php?content=order&add=y&id_order=$id_order&error=$message");
	}	
}

if( isset($_GET['addx']) && isset( $_GET['insert_detailx'] ) )
{
		
}

function check_edit_qty($id_product_attribute, $qty, $new_qty)
{
	$product = new product();
	$a_qty = $product->available_order_qty($id_product_attribute) + $qty;
	if($a_qty > $new_qty)
	{
		return $a_qty;
	}else{
		return false;
	}
}
if( isset($_GET['edit_order']) && isset($_GET['edit_qty']) )
{
	$err = "";
	$id_order = $_POST['id_order'];
	$id_order_detail = $_POST['id_order_detail'];
	$id_product_attribute = trim($_POST['id_product_attribute']);
	$qty = $_POST['qty'];
	$new_qty = $_POST['edit_qty'];
	$p_qty = $new_qty - $qty;
	$order = new order($id_order);
	$customer = new customer($order->id_customer);
	$product = new product();
	$id_product = $product->getProductId($id_product_attribute);
	$product->product_detail($id_product, $order->id_customer);
	$qr = dbQuery("SELECT product_price, reduction_percent, reduction_amount, final_price FROM tbl_order_detail WHERE id_order_detail = ".$id_order_detail);
	if(dbNumRows($qr) == 1 )
	{
		list($product_price, $reduction_percent, $reduction_amount, $final_price) = dbFetchArray($qr);
	}else{
		$product_price = 0;
		$reduction_percent = 0;
		$reduction_amount = 0;
		$final_price = 0;
	}
	$discount = $reduction_percent > 0 ? $p_qty * ($product_price * ($reduction_percent*0.01)) : $p_qty * $reduction_amount;
	$total_amount = $p_qty * $final_price;
	$new_discount = $reduction_percent > 0 ? $new_qty * ($product_price* ($reduction_percent*0.01)) : $new_qty * $reduction_amount;
	$new_total_amount = $new_qty * $final_price;
	if( check_edit_qty($id_product_attribute, $qty, $new_qty) ) /// ถ้ามียอดคงเหลือเพียงพอ
	{
		if($customer->credit_amount != 0.00 )
		{
			$cr = $order->check_credit($total_amount);
		}else{
			$cr = true;
		}
		if($cr)
		{
			$qs = dbQuery("UPDATE tbl_order_detail SET product_qty = ".$new_qty.", discount_amount = ".$new_discount.", total_amount = ".$new_total_amount." WHERE id_order_detail = ".$id_order_detail);
			$err = number_format($new_total_amount, 2);
		}else{
			$err = "x0";  /// x0 = วงเงินคงเหลือไม่พอ  x1 = สินค้าคงเหลือไม่พอ  xx = สำเร็จ
		}
	}else{
		$err = "x1";
	}
	echo $err;
}

if(isset($_GET['edit_order'])&&isset($_GET['add_detail'])&& $_POST['qty']!=""){
	$id_order = $_POST['id_order'];
	$id_product_attribute = trim($_POST['id_product_attribute']);
	$qty = $_POST['qty'];
	$order = new order($id_order);
	$customer = new customer($order->id_customer);
	$product = new product();
	$id_prodcut = $product->getProductId($id_product_attribute);
	$product->product_detail($id_product, $order->id_customer);
	$product->product_attribute_detail($id_product_attribute);
	$total_amount = $qty * $product->product_sell;
	if($customer->credit_amount != 0.00){
		if($order->check_credit($total_amount)){
			if($order->insertDetail($id_product_attribute, $qty)){
				$message = "เพิ่มสินค้าเรียบร้อยแล้ว";
				header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&message=$message");
		
			}else{
				$message = "เพิ่มสินค้าไม่สำเร็จ";
				header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&error=$message");
				exit;
			}
		}else{
			$message = "ไม่สามารถเพิ่มรายการได้เนื่องจากเครดิตคงเหลือไม่พอ  เครดิตคงเหลือ : ".$customer->credit_balance." ฿";
			header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&error=$message");
			exit;
		 }
	}else{
		if($order->insertDetail($id_product_attribute, $qty)){
				$message = "เพิ่มสินค้าเรียบร้อยแล้ว";
				header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&message=$message");
		
			}else{
				$message = "เพิ่มสินค้าไม่สำเร็จ";
				header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&error=$message");
				exit;
			}
	}
		
}
/// ลบในหน้า แก้ไข
if(isset($_GET['delete'])&&isset($_GET['id_order'])&&isset($_GET['id_product_attribute'])){
	$id_order = $_GET['id_order'];
	$id_product_attribute = $_GET['id_product_attribute'];
	if(dbQuery("DELETE FROM tbl_order_detail WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute")){
		$message = "ลบรายการเรียบร้อยแล้ว";
		header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&message=$message");
	}else{
		$message = "ลบรายการไม่สำเร็จ";
		header("location: ../index.php?content=order&edit=y&id_order=$id_order&view_detail=y&error=$message");
	}	
}

/// ลบในหน้า เพิ่ม //
if(isset($_GET['delete'])&&isset($_GET['id_order_detail'])){
	$id_order_detail = $_GET['id_order_detail'];
	list($id_order)=dbFetchArray(dbQuery("SELECT id_order FROM tbl_order_detail WHERE id_order_detail = $id_order_detail"));
	if(dbQuery("DELETE FROM tbl_order_detail WHERE id_order_detail = $id_order_detail")){
		$message = "ลบรายการเรียบร้อยแล้ว";
		header("location: ../index.php?content=order&add=y&id_order=$id_order&message=$message");	
	}else{
		$message = "ลบรายการไม่สำเร็จ";
		header("location: ../index.php?content=order&add=y&id_order=$id_order&error=$message");
	}
}
//********************************* test  ************************//
if(isset($_GET['add_to_order'])){
	$id_order= $_POST['id_order'];
	$order= new order($id_order);
	$id_customer = $order->id_customer;
	$order_qty = $_POST['qty'];
	$i = 0;
	$n = 0;
	$missing = "";
	foreach ($order_qty as $id=>$qty ){	
		if($qty !=""){
			$product = new product();
			$customer = new customer($id_customer);
			$id_product = $product->getProductId($id);
			$product->product_detail($id_product, $order->id_customer);
			$product->product_attribute_detail($id);
			$total_amount = $qty*$product->product_sell;		
			if(!ALLOW_UNDER_ZERO){
				list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id' AND id_warehouse = 1"));
				$instock = $product->available_order_qty($id); 
				if($qty>$instock){
					$missing .= $product->reference." มียอดคงเหลือไม่เพียงพอ &nbsp;<br/>";
				}else{
					if($order->insertDetail($id, $qty)){
						$n++;
							}else{
						$message = $order->error_message;
						header("location: ../index.php?content=order&add=y&id_order=$id_order&id_customer=$id_customer&error=$message");
						exit;
							}
					}
				}else{
					if($order->insertDetail($id, $qty)){
					$n++;
						}else{
					$message = $order->error_message;
					header("location: ../index.php?content=order&add=y&id_order=$id_order&id_customer=$id_customer&error=$message");
					exit;
						}
				}
		}else{
		}
	}
	if($missing ==""){
	$message = "เพิ่ม $n รายการเรียบร้อย";
	header("location: ../index.php?content=order&add=y&id_order=$id_order&id_customer=$id_customer&message=$message");
	}else{
	$message = "เพิ่ม $n รายการเรียบร้อย";
	header("location: ../index.php?content=order&add=y&id_order=$id_order&id_customer=$id_customer&message=$message&missing=$missing");
	}
		
}

//********************************  เพิ่มรายการในหน้า request_product *************************************//
if(isset($_GET['add_to_request_order'])){
	$id_request_order= $_POST['id_request_order'];
	$order_qty = $_POST['qty'];
	foreach ($order_qty as $id=>$qty ){	
		if($qty !=""){
			$product = new product();
			$id_product = $product->getProductId($id);
			$product->product_detail($id_product);
			$product->product_attribute_detail($id);
			$id_product = $product->id_product;
			$sql = dbQuery("SELECT id_request_order_detail, qty FROM tbl_request_order_detail WHERE id_request_order = $id_request_order AND id_product_attribute = $id");
			$row = dbNumRows($sql);
			if($row>0){
				list($id_request_order_detail, $old_qty) = dbFetchArray($sql);
				$new_qty = $qty+$old_qty;
				dbQuery("UPDATE tbl_request_order_detail SET qty = $new_qty WHERE id_request_order_detail = $id_request_order");
			}else{
				dbQuery("INSERT INTO tbl_request_order_detail (id_request_order, id_product, id_product_attribute, qty) VALUES ($id_request_order, $id_product, $id, $qty)");
			}
		}else{
			// do nothing
		}
	}
		header("location: ../index.php?content=request&add=y&id_request_order=$id_request_order");			
}
//// ปริ๊นออเดอร์ไปนำเข้า  formula

function show_discount($percent, $amount)
{
	 $unit 	= " %";
	 $dis	= 0.00;
	if($percent != 0.00){ $dis = $percent; }else{ $dis = number_format($amount, 2); $unit = ""; }
	return $dis.$unit;
}

if( isset( $_GET['print_order']) && isset( $_GET['id_order'] ) )
{
	$id_order	= $_GET['id_order'];
	$order 		= new order($id_order);
	$print 		= new printer();
	$doc			= doc_type($order->role);
	echo $print->doc_header();
	$print->add_title($doc['title']);
	$header		= array("ลูกค้า"=>customer_name($order->id_customer), "วันที่"=>thaiDate($order->date_add), "พนักงานขาย"=>sale_name($order->id_sale), "เลขที่เอกสาร"=>$order->reference);
	$print->add_header($header);
	$detail 		= dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = ".$id_order);
	$total_row 	= dbNumRows($detail);
	$config 		= array("footer"=>false, "total_row"=>$total_row, "font_size"=>10, "sub_total_row"=>4);
	$print->config($config);
	$row 			= $print->row;
	$total_page 	= $print->total_page;
	$total_qty 	= 0;
	$total_price	= 0;
	$total_amount 		= 0;
	$total_discount 	= 0;
	$bill_discount		= bill_discount($id_order);
	//**************  กำหนดหัวตาราง  ******************************//
	$thead	= array(
						array("ลำดับ", "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
						array("บาร์โค้ด", "width:20%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("สินค้า", "width:30%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("ราคา", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("จำนวน", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("ส่วนลด", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("มูลค่า", "width:15%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
						);
	$print->add_subheader($thead);
	
	//***************************** กำหนด css ของ td *****************************//
	$pattern = array(
							"text-align: center; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px; padding:0px; text-align:center; vertical-align:middle;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
							"text-align:right; border-left: solid 1px #ccc; border-top:0px;"
							);					
	$print->set_pattern($pattern);	
	
	//*******************************  กำหนดช่องเซ็นของ footer *******************************//
	$footer	= array( 
						array("ผู้รับของ", "ได้รับสินค้าถูกต้องตามรายการแล้ว","วันที่............................."), 
						array("ผู้ส่งของ", "","วันที่............................."),
						array("ผู้ตรวจสอบ", "","วันที่............................."),
						array("ผู้อนุมัติ", "","วันที่.............................")
						);						
	$print->set_footer($footer);		
	
	$n = 1;
	while($total_page > 0 )
	{
		echo $print->page_start();
			echo $print->top_page();
			echo $print->content_start();
				echo $print->table_start();
				$i = 0;
				$product = new product();
				while($i<$row) : 
					$rs = dbFetchArray($detail);
					if(count($rs) != 0) :
						
						$barcode			= "<img src='".WEB_ROOT."library/class/barcode/barcode.php?text=".$rs['barcode']."' style='height:8mm;' />";
						$product_name 	= "<input type='text' style='border:0px; width:100%;' value='".$product->product_reference($rs['id_product_attribute'])." : ".$product->product_name($rs['id_product'])."' />";
						$amount				= $rs['total_amount'];
						$discount_amount = $rs['discount_amount'];
						$price 				= $rs['product_price'] * $rs['product_qty'];
						$discount 			= show_discount($rs['reduction_percent'], $rs['reduction_amount']);
						$data 				= array($n, $barcode, $product_name, number_format($rs['product_price'],2), number_format($rs['product_qty']), $discount, number_format($amount, 2));
						$total_qty 			+= $rs['product_qty'];
						$total_price			+= $price;
						$total_amount 		+= $amount;
						$total_discount 	+= $discount_amount;
					else :
						$data = array("", "", "", "", "", "","");
					endif;
					echo $print->print_row($data);
					$n++; $i++;  	
				endwhile;
				echo $print->table_end();
				if($print->current_page == $print->total_page)
				{ 
					$qty 			= number_format($total_qty); 
					$amount 		= number_format($total_price,2); 
					$all_disc		= number_format($total_discount+$bill_discount,2);
					$net_amount	= number_format($total_amount - $bill_discount ,2);
					$remark 		= $order->comment; 
				}else{ 
					$qty 			= ""; 
					$amount		= "";
					$all_disc		= "";
					$net_amount	= "";
					$remark 		= ""; 
				}
				$sub_total = array(
						array("<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px; border-left:0px; width:60%; text-align:center;'>**** ส่วนลดท้ายบิล : ".number_format($bill_discount,2)." ****</td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc;'><strong>จำนวนรวม</strong></td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; text-align:right;'>".$qty."</td>"),
						array("<td rowspan='3' style='height:".$print->row_height."mm; border-top: solid 1px #ccc; border-bottom-left-radius:10px; width:55%; font-size:10px;'><strong>หมายเหตุ : </strong>".$remark."</td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc;'><strong>ราคารวม</strong></td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; text-align:right;'>".$amount."</td>"),
						array("<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px;'><strong>ส่วนลดรวม</strong></td>
						<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; border-bottom:0px; border-bottom-right-radius:10px; text-align:right;'>".$all_disc."</td>"),
						array("<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px;'><strong>ยอดเงินสุทธิ</strong></td>
						<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; border-bottom:0px; border-bottom-right-radius:10px; text-align:right;'>".$net_amount."</td>")
						);
			echo $print->print_sub_total($sub_total);				
			echo $print->content_end();
			echo $print->footer;
		echo $print->page_end();
		$total_page --; $print->current_page++;
	}
	echo $print->doc_footer();
}

	
//// ปริ๊นออเดอร์ไปนำจัดสินค้า
if(isset($_GET['print_prepare'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	$customer = new customer($order->id_customer);
	$remark = $order->comment;
	$content = "order";
	$title = "ใบสั่งจัดสินค้า";
	$total_order = ""; ///เก็บยอดรวมเพิ่มทีละรายการเวลาวนลูป
	$total_order_amount = "";///วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$total_discount_order =""; //เก็บยอดเงินส่วนลดตอนวนลูป
	$total_discount_amount = ""; //วนลูปจบเอายอดเงินส่วนลดมาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$net_total =""; //มูลค่าสินค้าหลังหักส่วนลด
	echo"<!DOCTYPE html>
				<html>
				<head>
					<meta charset='utf-8'>
					<meta name='viewport' content='width=device-width, initial-scale=1.0'>
					<link rel='icon' href='../favicon.ico' type='image/x-icon' />
					<title>ออเดอร์</title>
					<!-- Core CSS - Include with every page -->
					<link href='/invent/library/css/bootstrap.css' rel='stylesheet'>
					<link href='/invent/library/css/font-awesome.css' rel='stylesheet'>
					<link href='/invent/library/css/bootflat.min.css' rel='stylesheet'>
					 <link rel='stylesheet' href='/invent/library/css/jquery-ui-1.10.4.custom.min.css' />
					 <script src='/invent/library/js/jquery.min.js'></script>
					<script src='/invent/library/js/jquery-ui-1.10.4.custom.min.js'></script>
					<script src='/invent/library/js/bootstrap.min.js'></script>  
					<!-- SB Admin CSS - Include with every page -->
					<link href='/invent/library/css/sb-admin.css' rel='stylesheet'>
					<link href='/invent/library/css/template.css' rel='stylesheet'>
				</head>
				<body style='padding-top:10px;'><div style='width:180mm; margin-right:auto; margin-left:auto; padding:10px'>
				<div class=\"hidden-print\">
				<button  class='btn btn-primary pull-right' onClick=\"print();\" type='button' />พิมพ์</button>
				<a href='../index.php?content=$content&edit=y&id_order=$id_order&view_detail=y' ><button  class='btn btn-primary pull-right' type='button' style='margin-right:20px;' />ยกเลิก</button></a>
</div> 
				
	<h4 style='float:left'>$title</h4>
	<table style='width:100%; border:solid 1px #CCC;'>
		<tr>
			<td colspan='2' style='padding:10px;vertical-align:text-top; text-align:center;'><h3>".$order->reference."</h3></td>
		</tr>
		<tr>
			<td style='width:20%px; padding:10px; vertical-align:text-top; text-align:right;'><h4>ลูกค้า :</h4></td>
			 <td style='padding:10px; vertical-align:text-top; text-align:left;'><h4>".$customer->full_name."</h4></td>
		</tr>
		<tr>
		<td style='width:20%px; padding:10px; vertical-align:text-top; text-align:right;'><h4>วันที่ : </h4></td>
		<td style='padding:10px; text-align:left; vertical-align:text-top;'><h4>".thaiDate($order->date_add)."</h4></td>
		</tr>
	</table>	
	</div></body></html>";
	 }
if(isset($_GET['getData'])&&isset($_GET['id_product'])){
			$id_product = $_GET['id_product'];
			$id_cus = $_GET['id_customer'];
			$product = new product();
			$product->product_detail($id_product, $id_cus);
			$config = getConfig("ATTRIBUTE_GRID_HORIZONTAL");
			$sqr = dbQuery("SELECT id_$config FROM tbl_product_attribute WHERE id_product = $id_product AND id_$config !=0 GROUP BY id_$config");
			$colums = dbNumRows($sqr);
			$sqm = dbQuery("SELECT id_color, id_size, id_attribute FROM tbl_product_attribute WHERE id_product = $id_product LIMIT 1");
			list($co, $si, $at) = dbFetchArray($sqm);
			if($co !=0){ $co =1;}
			if($si !=0){ $si = 1;}
			if($at !=0){ $at = 1;}
			$count = $co+$si+$at;
			if($count >1){	$table_w = (70*($colums+1)+100); }else if($count ==1){ $table_w = 800; }
			$dataset = $product->order_attribute_grid($product->id_product);
			$dataset .= "|".$table_w;
			$dataset .= "|".$product->product_code;
			echo $dataset;
}
if(isset($_GET['get_request_data'])&&isset($_GET['id_product'])){
			$id_product = $_GET['id_product'];
			$id_cus = $_GET['id_customer'];
			$product = new product();
			$product->product_detail($id_product, $id_cus);
			$config = getConfig("ATTRIBUTE_GRID_HORIZONTAL");
			$sqr = dbQuery("SELECT id_$config FROM tbl_product_attribute WHERE id_product = $id_product AND id_$config !=0 GROUP BY id_$config");
			$colums = dbNumRows($sqr);
			$sqm = dbQuery("SELECT id_color, id_size, id_attribute FROM tbl_product_attribute WHERE id_product = $id_product LIMIT 1");
			list($co, $si, $at) = dbFetchArray($sqm);
			if($co !=0){ $co =1;}
			if($si !=0){ $si = 1;}
			if($at !=0){ $at = 1;}
			$count = $co+$si+$at;
			if($count >1){	$table_w = (70*($colums+1)+100); }else if($count ==1){ $table_w = 800; }
			$dataset = $product->request_attribute_grid($product->id_product);
			$dataset .= "|".$table_w;
			$dataset .= "|".$product->product_code;
			echo $dataset;
}
if(isset($_GET['view_stock_data'])&&isset($_GET['id_product'])){
			$id_product = $_GET['id_product'];
			
			$product = new product();
			$product->product_detail($id_product);
			$config = getConfig("ATTRIBUTE_GRID_HORIZONTAL");
			$sqr = dbQuery("SELECT id_$config FROM tbl_product_attribute WHERE id_product = $id_product AND id_$config !=0 GROUP BY id_$config");
			$colums = dbNumRows($sqr);
			$sqm = dbQuery("SELECT id_color, id_size, id_attribute FROM tbl_product_attribute WHERE id_product = $id_product LIMIT 1");
			list($co, $si, $at) = dbFetchArray($sqm);
			if($co !=0){ $co =1;}
			if($si !=0){ $si = 1;}
			if($at !=0){ $at = 1;}
			$count = $co+$si+$at;
			if($count >1){	$table_w = (70*($colums+1)+100); }else if($count ==1){ $table_w = 800; }
			$dataset = $product->order_report_attribute_grid($id_product);
			$dataset .= "|".$table_w;
			$dataset .= "|".$product->product_code;
			echo $dataset;
}
if(isset($_GET['check_password'])){
	$sc = 0;
	$password = md5($_GET['password']);
	list($id_employee) = dbFetchArray(dbQuery("SELECT id_employee FROM tbl_employee LEFT JOIN tbl_access ON tbl_employee.id_profile = tbl_access.id_profile WHERE id_tab = '35' AND s_key = '$password'"));
	if( $id_employee != '' )
	{
		$qs = dbQuery("SELECT tbl_access.add, tbl_access.edit, tbl_access.delete FROM tbl_access JOIN tbl_employee ON tbl_access.id_profile = tbl_employee.id_profile WHERE id_employee = ".$id_employee." AND id_tab = 35");
		if( dbNumRows($qs) == 1 )
		{
			list( $add, $edit, $delete ) = dbFetchArray($qs);
			if( $add || $edit || $delete )
			{
				$sc = $id_employee;
			}
		}
	}
	echo $sc;
}

if(isset($_GET['edit_discount']))
{
	$id_employee 				= $_COOKIE['user_id'];
	$id_order 					= $_POST['id_order'];
	$id_order_detail_array 	= $_POST['id_order_detail_array'];
	$reduction_array  		= $_POST['reduction_array'];
	$unit_array 					= $_POST['unit_array'];
	$em_approve				= $_POST['id_employee'];
	dbQuery("INSERT INTO tbl_discount_edit(id_order,id_employee, em_approve) VALUES (".$id_order.", ".$id_employee.", ".$em_approve.")");
	$id_discount_edit = dbInsertId();
	$i = 0;
	foreach ($id_order_detail_array as $id ) :
		$unit			= $unit_array[$i];
		list($product_qty, $product_price, $id_product_attribute) = dbFetchArray(dbQuery("SELECT product_qty,product_price,id_product_attribute FROM tbl_order_detail WHERE id_order_detail = '$id'"));
		if($unit == "percent") :
			$reduction_percent = $reduction_array[$i];
			$reduction_amount = 0.00;
			$rate = $reduction_array[$i]/100;			
			$discount = $product_price * $rate;
			$discount_amount = ($product_price * $product_qty) * $rate;
			$final_price = $product_price - $discount;
			$total_amount = $product_qty * $final_price;
		elseif($unit == "amount") :
			$reduction_percent = 0.00;
			$reduction_amount = $reduction_array[$i];		
			$discount = $reduction_amount;
			$discount_amount = $discount * $product_qty;
			$final_price = $product_price - $discount;
			$total_amount = $product_qty * $final_price;
		endif;
		$qs = dbQuery("SELECT reduction_percent, reduction_amount FROM tbl_order_detail WHERE id_order_detail = ".$id);
		if(dbNumRows($qs) > 0 ) :
			list($old_percent, $old_amount) = dbFetchArray($qs);
			if($old_percent != 0.00){ $old_dis = $old_percent; $old_unit = "percent"; }else{ $old_dis = $old_amount; $old_unit = "amount"; }
			if($unit != $old_unit || $reduction_amount != $old_amount || $reduction_percent != $old_percent ) :
				$sql = "UPDATE tbl_order_detail SET reduction_percent = ".$reduction_percent.", reduction_amount = ".$reduction_amount.", discount_amount = ".$discount_amount.", final_price = ".$final_price.", total_amount = ".$total_amount." WHERE id_order_detail = ".$id;
				$rs = dbQuery($sql);
				if($rs) :
					dbQuery("INSERT INTO tbl_discount_edit_detail(id_discount_edit, id_order_detail, dis_before, dis_after, old_unit, new_unit) VALUES (".$id_discount_edit.", ".$id.", ".$old_dis.", ".$reduction_array[$i].", '".$old_unit."', '".$unit."')");
				endif;
			endif;
		endif;
		$i++;
	endforeach;
	echo "1";
}
/******************************  เพิ่มส่วนลดท้ายบิล  ***********************************/
if( isset($_GET['insert_bill_discount']) )
{
	$discount_amount = $_POST['discount'];
	$id_order 			= $_POST['id_order'];
	$id_approve 		= $_POST['id_approve'];
	$id_employee 		= $_COOKIE['user_id'];
	$em_add				= employee_name($id_employee);
	$em_app				= employee_name($id_approve);
	$order 				= new order($id_order);
	$role					= $order->role;
	$id_customer 		= $order->id_customer;
	$id_sale				= $order->id_sale;
	$qr = dbQuery("SELECT id_order_discount FROM tbl_order_discount WHERE id_order = ".$id_order);
	if( dbNumRows($qr) > 0 )
	{
		$qs = dbQuery("UPDATE tbl_order_discount SET discount_amount = ".$discount_amount.", em_add = '".$em_add."', em_approve = '".$em_app."' WHERE id_order = ".$id_order);
	}else{
		$qs = dbQuery("INSERT INTO tbl_order_discount ( id_order, role, id_sale, id_customer, discount_amount, em_add, date_add, em_approve ) VALUES ( ".$id_order.", ".$role.", ".$id_sale.", ".$id_customer.", ".$discount_amount.", '".$em_add."', NOW(), '".$em_app."')");
	}
	if($qs)
	{
		echo "success"; 
	}else{
		echo "fail";
	}
}

if( isset($_GET['update_bill_discount']) )
{
	$discount_amount = $_POST['discount'];
	$id_order 			= $_POST['id_order'];
	$id_approve 		= $_POST['id_approve'];
	$id_employee 		= $_COOKIE['user_id'];
	$em_add				= employee_name($id_employee);
	$em_app				= employee_name($id_approve);
	$qs = dbQuery("UPDATE tbl_order_discount SET discount_amount = ".$discount_amount.", em_add = '".$em_add."', em_approve = '".$em_app."' WHERE id_order = ".$id_order);
	if($qs)
	{
		echo "success"; 
	}else{
		echo "fail";
	}
}

if( isset($_GET['delete_bill_discount']) && isset($_GET['id_order']) )
{
	$id_order = $_GET['id_order'];	
	$qs = dbQuery("DELETE FROM tbl_order_discount WHERE id_order = ".$id_order);
	if($qs)
	{
		 $mess="ลบส่วนลดท้ายบิลเรียบร้อยแล้ว";
		 header("location: ../index.php?content=order&edit=y&id_order=".$id_order."&view_detail&message=".$message);
	}else{
		$error = "ลบส่วนลดท้ายบิลไม่สำเร็จ";
		header("location: ../index.php?content=order&edit=y&id_order=".$id_order."&view_detail&error=".$error);
	}
}

if(isset($_GET['save_order'])){
	$id_order = $_GET['id_order'];
	dbQuery("UPDATE tbl_order SET order_status = 1 WHERE id_order = $id_order");
	$message = "บันทึกเรียบร้อยเเล้ว";
	header("location: ../index.php?content=order&message=$message");
}
if(isset($_GET['check_add'])){
	$user_id = $_COOKIE['user_id'];
	list($id_order) = dbFetchArray(dbQuery("SELECT id_order FROM tbl_order WHERE id_employee = $user_id AND order_status = 0 AND role = 1"));
	if($id_order == ""){
		header("location: ../index.php?content=order&add=y");
	}else{
		$message = "ยังไม่ได้บันทึกออร์เดอร์นี้";
		header("location: ../index.php?content=order&add=y&id_order=$id_order&message1=$message");
	}
}

if(isset($_GET['search-text'])&&isset($_GET['filter'])){
	$text = $_GET['search-text'];
	$filter = $_GET['filter'];
	$html = "";
	//$paginator = new paginator();
	if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
	switch($filter){
		case "customer" :
		$in_cause = "";
		$qs = dbQuery("SELECT id_customer FROM tbl_customer WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%' GROUP BY id_customer");
		$rs = dbNumRows($qs);
		$i=0;
		if($rs>0){
		while($i<$rs){
			list($in) = dbFetchArray($qs);
			$in_cause .="$in";
			$i++;
			if($i<$rs){ $in_cause .=","; 	}
		}
		$where = "WHERE id_customer IN($in_cause) AND role IN(1,4) AND order_status = 1 ORDER BY id_order DESC" ; 
		}else{
			$where = "WHERE id_order = NULL";
		}
		break;
		case "sale" :
		$in_cause = "";
		$qs = dbQuery("SELECT id_sale FROM tbl_sale LEFT JOIN tbl_employee ON tbl_sale.id_employee = tbl_employee.id_employee WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%'");
		$rs = dbNumRows($qs);
		$i=0;
		$in ="";
		if($rs>0){
		while($i<$rs){
			list($id_sale) = dbFetchArray($qs);
			$in .="$id_sale";
			$i++;
			if($i<$rs){ $in .=","; }
		}
		$sq = dbQuery("SELECT id_customer FROM tbl_customer WHERE id_sale IN($in)");
		$rs = dbNumRows($sq);
		$n =0;
		while($n<$rs){
			list($id_customer) = dbFetchArray($sq);
			$in_cause .= "$id_customer";
			$n++;
			if($n<$rs){ $in_cause .= ","; }
		}
		$where = "WHERE id_customer IN($in_cause) AND role IN(1,4) AND order_status = 1 ORDER BY id_order DESC";
		}else{
			$where = "WHERE id_order = NULL";
		}
		break;
		case "reference" :
			$where = "WHERE reference LIKE'%$text%' AND role IN(1,4) AND order_status = 1 ORDER BY id_order DESC ";
		break;
		
	}
		//$paginator->Per_Page("tbl_order",$where,$get_rows);
		//$limit = "LIMIT ".$paginator->Page_Start." , ".$paginator->Per_Page; 
		//$paginator->display($get_rows,"index.php?content=order");

$html .="
	<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12' id='search-table'>
	<table class='table'>
    	<thead style='color:#FFF; background-color:#48CFAD;'>
        	<th style='width:5%; text-align:center;'>ID</th><th style='width:10%;'>เลขที่อ้างอิง</th><th style='width:20%;'>ลูกค้า</th>
            <th style='width:10%;'>พนักงาน</th><th style='width:10%; text-align:center;'>ยอดเงิน</th>
			<th style='width:15%; text-align:center;'>การชำระเงิน</th><th style='width:10%; text-align:center;'>สถานะ</th>
			<th style='width:10%; text-align:center;'>วันที่เพิ่ม</th><th style='width:10%; text-align:center;'>วันที่ปรับปรุง</th>
        </thead>";
		//$result = getOrderTable($view,$from, $to,$paginator->Page_Start,$paginator->Per_Page);
		
		$result = dbQuery("SELECT id_order,reference,id_customer,id_employee,payment,tbl_order.date_add,current_state,tbl_order.date_upd FROM tbl_order $where");
		$i=0;
		$row = dbNumRows($result);
		if($row>0){
		while($i<$row){
			list($id_order, $reference,$id_customer,$id_employee,  $payment,   $date_add,$current_state,$date_upd)=dbFetchArray($result);
			list($cus_first_name, $cus_last_name) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_customer WHERE id_customer = '$id_customer'"));
			list($employee_name) = dbFetchArray(dbQuery("SELECT first_name FROM tbl_employee WHERE id_employee = '$id_employee'"));
			list($amount) = dbFetchArray(dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail WHERE id_order = $id_order"));
			list($status) = dbFetchArray(dbQuery("SELECT state_name FROM tbl_order_state WHERE id_order_state = '$current_state'"));
	$html .="<tr style='color:#FFF; background-color:".state_color($current_state).";'>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$id_order</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$reference</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$cus_first_name &nbsp; $cus_last_name</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$employee_name</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">".number_format($amount)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$payment</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">$status</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">".thaiDate($date_add)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&edit=y&id_order=$id_order&view_detail=y'\">".thaiDate($date_upd)."</td>
			</tr>";
			$i++;
		}
		}else if($row==0){
			$html .="<tr><td colspan='9' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;ไม่มีรายการในช่วงนี้</h3></td></tr>";
		}
		$html .= "</table>";
		//$html .= $paginator->display_pages();
		$html .= "<br><br></div>";
		echo $html;
}

if(isset($_GET['clear_filter'])){
		setcookie("order_from_date","",time()-3600,"/");
		setcookie("order_to_date","",time()-3600,"/");
		setcookie("order_search-text", $text, time() - 3600, "/");
		setcookie("order_filter",$filter, time() - 3600,"/");
		header("location: ../index.php?content=order");
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
			$title = "รายการอภินันทนาการ / Sponsor Order";
			break;
		case 5 :
			$content = "consignment";
			$title = "ใบส่งของ / ใบแจ้งหนี้";
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

	
?>
