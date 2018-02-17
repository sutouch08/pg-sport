<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
require LIB_ROOT."class/Order.php";

if( isset( $_GET['clearFilter'] ) )
{
	deleteCookie('sReference');
	deleteCookie('sCustomer');
	deleteCookie('from_date');
	deleteCookie('to_date');
	echo 'success';	
}
///////////////////  AutoComplete //////////////////////
if(isset($_GET['product'])&&isset($_REQUEST['term'])){
	$qstring = "SELECT id_product_attribute AS id, reference FROM product_attribute_table WHERE reference LIKE '%".$_REQUEST['term']."%'";
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
if(isset($_GET['employee_name'])&&isset($_REQUEST['term'])){
	$qstring = "SELECT id_employee, first_name, last_name FROM tbl_employee WHERE first_name LIKE '%".$_REQUEST['term']."%' OR last_name LIKE '%".$_REQUEST['term']."%'";
	$result = dbQuery($qstring);//query the database for entries containing the term
if ($result->num_rows>0)
	{ 
		$data= array();
	while($row = $result->fetch_array())//loop through the retrieved values
		{
				$data[]=$row['id_employee'].":".$row['first_name']." ".$row['last_name'];
		}
		echo  json_encode($data);//format the array into json data
	}else {
		echo "error";
	}
}
///////////////////  AutoComplete //////////////////////
if(isset($_GET['product_code'])&&isset($_REQUEST['term'])){
	$qstring = "SELECT reference FROM tbl_product_attribute WHERE reference LIKE '%".$_REQUEST['term']."%'";
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
//chech stock

if(isset($_GET['check_stock'])&&isset($_GET['product_code'])){
	$reference = $_GET['product_code'];
	list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE reference = '$reference'"));
	$product = new product();
	$product->product_attribute_detail($id_product_attribute);
	$qty = $product->available_qty() - $product->order_qty();
	$data = $id_product_attribute.":".$qty;
	echo $data;
}
if(isset($_GET['check_stock'])&&isset($_GET['barcode'])){
	$reference = $_GET['barcode'];
	list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE barcode = '$reference'"));
	$product = new product();
	$product->product_attribute_detail($id_product_attribute);
	$product_code = $product->reference;
	$qty = $product->available_qty() - $product->order_qty();
	$data = trim($id_product_attribute).":".$qty.":".$product_code;
	echo $data;
}
//// add order
if(isset($_GET['add'])&&isset($_POST['employee'])){
	$id_employee = $_POST['employee'];
	$employee = new employee($id_employee);
	$id_customer = $_POST['id_customer'];
	$role = $_POST['role'];
	$reference = get_max_role_reference("PREFIX_REQUISITION", $role);
	$id_cart = 0;
	$id_address = 0;
	$current_state = 1;
	$shipping_no = 0;
	$invoice_no = 0;
	$delivery_no = 0;
	$delivery_date = "";
	$comment = $_POST['comment'];
	$valid = 0;
	$date_add = dbDate($_POST['doc_date']);
	$date_upd = date('Y-m-d');
	if(dbQuery("INSERT INTO tbl_order(reference, id_customer, id_employee, id_cart, current_state, payment, comment, valid, role, date_add, date_upd,order_status) VALUES ('$reference', '$id_customer', $id_employee, $id_cart, $current_state, '$payment', '$comment', $valid, $role, '$date_add', NOW(),0)")){
		list($id_order) = dbFetchArray(dbQuery("SELECT id_order FROM tbl_order WHERE reference = '$reference' AND id_employee = $id_employee"));
		header("location: ../index.php?content=requisition&add=y&id_order=$id_order&id_employee=$id_employee");
	}else{
		$message = "ไม่สามารถเพิ่มออเดอร์ใหม่ในฐานข้อมูลได้";
		header("location: ../index.php?content=requisition&add=y&error=$message");
	}
	
}

if(isset($_GET['reference'])){
	require LIB_ROOT."class/category.php";
	$reference = $_GET['reference'];
	$id_customer = $_GET['id_customer'];
	$sql = dbQuery("SELECT id_product_attribute, id_product FROM tbl_product_attribute WHERE reference ='$reference'");
	list($id_product_attribute, $id_product) = dbFetchArray($sql);
	$product = new product();
	$id_prodcut = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product, $order->id_customer);
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
			header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&message=$message");
		}else{
			$message = "เปลี่ยนสถานะไม่สำเร็จ";
			header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&error=$message");
		}
		}else{
			$message = "คุณไม่ได้เลือกสถานะ";
			header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&error=$message");
		}
}
if(isset($_GET['edit_order'])&&isset($_POST['new_qty'])&&$_POST['new_qty'] !=""){
	$id_order = $_POST['id_order'];
	$id_product_attribute = $_POST['id_product_attribute'];
	$qty = $_POST['new_qty'];
	list($old_qty, $old_total_amount) = dbFetchArray(dbQuery("SELECT product_qty, total_amount FROM tbl_order_detail WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute"));
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
					header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&message=$message");
					}else{
						$message = "แก้ไขไม่สำเร็จ";
						header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&error=$message");
						exit;
					}
		}else{
			if($order->check_credit($new_total_amount)){
				if($order->changeQty($id_product_attribute, $qty)){
					$message = "แก้ไขเรียบร้อยแล้ว";
					header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&message=$message");
					}else{
						$message = "แก้ไขไม่สำเร็จ";
						header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&error=$message");
						exit;
					}
			}else{
				$message = "แก้ไขไม่สำเร็จ เนื่องจากเคดิตไม่พอ เครดิตคงเหลือ : ".$customer->credit_balance;
				header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&error=$message");
				exit;
			}
		}
		}else if($order->changeQty($id_product_attribute, $qty)){
					$message = "แก้ไขเรียบร้อยแล้ว";
					header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&message=$message");
					}else{
						$message = "แก้ไขไม่สำเร็จ";
						header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&error=$message");
						exit;
					}
}
if(isset($_GET['check_stock'])&&isset($_GET['id_product_attribute'])){
		$id_product_attribute = $_GET['id_product_attribute'];
		$product = new product();
		$product->product_attribute_detail($id_product_attribute);
		$qty = $product->available_qty() - $product->order_qty();
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
	if($order->insert_detail($id_product_attribute, $qty)){
		$message = "เพิ่มสินค้าเรียบร้อยแล้ว";
		header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&message=$message");

	}else{
		$message = "เพิ่มสินค้าไม่สำเร็จ";
		header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&error=$message");
	}	
}

if(isset($_GET['add'])&&isset($_GET['insert_detail'])){
	$id_order = $_POST['id_order'];
	$id_product_attribute = trim($_POST['id_product_attribute']);
	$qty = $_POST['qty'];
	$order = new order($id_order);
	$product = new product();
	$id_prodcut = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product, "");
		$product->product_attribute_detail($id_product_attribute);
	if($order->insert_detail($id_product_attribute, $qty)){
		$message = "เพิ่มสินค้าเรียบร้อยแล้ว";
		header("location: ../index.php?content=requisition&add=y&id_order=$id_order&message=$message");
	}else{
		$message = "เพิ่มสินค้าไม่สำเร็จ";
		header("location: ../index.php?content=requisition&add=y&id_order=$id_order&error=$message");
	}	
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
			if($order->insert_detail($id_product_attribute, $qty)){
				$message = "เพิ่มสินค้าเรียบร้อยแล้ว";
				header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&message=$message");
		
			}else{
				$message = "เพิ่มสินค้าไม่สำเร็จ";
				header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&error=$message");
				exit;
			}
		}else{
			$message = "ไม่สามารถเพิ่มรายการได้เนื่องจากเครดิตคงเหลือไม่พอ  เครดิตคงเหลือ : ".$customer->credit_balance." ฿";
			header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&error=$message");
			exit;
		 }
	}else{
		if($order->insert_detail($id_product_attribute, $qty)){
				$message = "เพิ่มสินค้าเรียบร้อยแล้ว";
				header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&message=$message");
		
			}else{
				$message = "เพิ่มสินค้าไม่สำเร็จ";
				header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&error=$message");
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
		header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&message=$message");
	}else{
		$message = "ลบรายการไม่สำเร็จ";
		header("location: ../index.php?content=requisition&edit=y&id_order=$id_order&view_detail=y&error=$message");
	}	
}

/// ลบในหน้า เพิ่ม //
if(isset($_GET['delete'])&&isset($_GET['id_order_detail'])){
	$id_order_detail = $_GET['id_order_detail'];
	list($id_order)=dbFetchArray(dbQuery("SELECT id_order FROM tbl_order_detail WHERE id_order_detail = $id_order_detail"));
	if(dbQuery("DELETE FROM tbl_order_detail WHERE id_order_detail = $id_order_detail")){
		$message = "ลบรายการเรียบร้อยแล้ว";
		header("location: ../index.php?content=requisition&add=y&id_order=$id_order&message=$message");	
	}else{
		$message = "ลบรายการไม่สำเร็จ";
		header("location: ../index.php?content=requisition&add=y&id_order=$id_order&error=$message");
	}
}
if(isset($_GET['add_to_order'])){
	$id_order= $_POST['id_order'];
	$order= new order($id_order);
	$id_customer = $order->id_customer;
	$order_qty = $_POST['qty'];
	$i = 0;
	$n = 0;
	$missing = "";
	foreach ($order_qty as $id_color => $items ){	
		foreach($items as $id => $qty )
		{
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
			}
		}
	}
	if($missing ==""){
	$message = "เพิ่ม $n รายการเรียบร้อย";
	header("location: ../index.php?content=requisition&add=y&id_order=$id_order&id_customer=$id_customer&message=$message");
	}else{
	$message = "เพิ่ม $n รายการเรียบร้อย";
	header("location: ../index.php?content=requisition&add=y&id_order=$id_order&id_customer=$id_customer&message=$message&missing=$missing");
	}
		
}
if(isset($_GET['print_order'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	$employee = new employee($order->id_employee);
	echo "
	<!DOCTYPE html>
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
<body>
<div class='container'>
	<div class='row'>
	<div class='row'>
        	<div class='col-lg-12'><h4>".$order->reference." - เบิกสินค้า<p class='pull-right'>ผู้เบิก : &nbsp;".$employee->full_name."</p></h4></div>
        </div>
	<table class='table' id='order_detail'>
	<thead>
				<th style='width:5%; text-align:center;'>ลำดับ</th><th style='text-align:center;'>บาร์โค้ด</th><th style='width:30%;'>สินค้า</th>
			   <th style='width:10%; text-align:center;'>ราคา</th><th style='width:10%; text-align:center;'>จำนวน</th>
			   <th style='width:10%; text-align:center;'>ส่วนลด</th><th style='width:10%; text-align:center;'>มูลค่า</th>
	</thead>";
	$sql = dbQuery("SELECT id_order_detail, id_product_attribute, barcode, product_reference, product_name, product_price, product_qty, reduction_percent, reduction_amount, discount_amount, total_amount FROM tbl_order_detail WHERE id_order = $id_order");
	$row = dbNumRows($sql);
	$n = 1;
	$i = 0;
	if($row>0){
	while($i<$row){
		list($id_order_detail, $id_product_attribute, $barcode, $product_reference, $product_name, $product_price, $product_qty, $discount_percent, $discount_amount, $total_discount, $total_amount)= dbFetchArray($sql);
		$product = new product();
		$id_prodcut = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product);
		$product->product_attribute_detail($id_product_attribute);
		if($discount_percent !== 0.00){ $discount = $discount_percent ."%";}else if($discount_amount != 0.00){ $discount = $discount_amount . "฿" ;}
		echo"<tr><td style='text-align:center; vertical-align:middle;'>$n</td>
		<td style='text-align:center; vertical-align:middle;'><img src='".WEB_ROOT."library/class/barcode/barcode.php?text=".$barcode."' style='width:150px;' /></td>
		<td style='vertical-align:middle;'>$product_reference : $product_name</td>
		<td style='text-align:center; vertical-align:middle;'>".number_format($product_price,2)."</td>
		<td style='text-align:center; vertical-align:middle;'>".number_format($product_qty)."</td>
		<td style='text-align:center; vertical-align:middle;'>$discount</td>
		<td style='text-align:center; vertical-align:middle;'>".number_format($total_amount,2)."</td>
				</td></tr>";
				$i++;
				$n++;
	}
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr>";
	}
	echo"		
	</table>	
	";
	echo "</div></div></body></html>";
	 }
	 if(isset($_GET['save_order'])){
	$id_order = $_GET['id_order'];
	$now = date("Y-m-d H:i:s");
	dbQuery("UPDATE tbl_order SET order_status = 1, date_add = '$now'  WHERE id_order = $id_order");
	$message = "บันทึกเรียบร้อยเเล้ว";
	header("location: ../index.php?content=requisition&message=$message");
}
if(isset($_GET['check_add'])){
	$user_id = $_COOKIE['user_id'];
	list($id_order) = dbFetchArray(dbQuery("SELECT id_order FROM tbl_order WHERE id_employee = $user_id AND order_status = 0 AND role IN (2,6)"));
	if($id_order == ""){
		header("location: ../index.php?content=requisition&add=y");
	}else{
		$message = "ยังไม่ได้บันทึกออร์เดอร์นี้";
		header("location: ../index.php?content=requisition&add=y&id_order=$id_order&message1=$message");
	}
}
?>