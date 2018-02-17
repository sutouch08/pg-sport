<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";

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
if(isset($_GET['employee_name1'])&&isset($_REQUEST['term'])){
	$qstring = "SELECT id_employee, first_name, last_name FROM tbl_employee WHERE first_name LIKE '%".$_REQUEST['term']."%' OR last_name LIKE '%".$_REQUEST['term']."%'";
	$result = dbQuery($qstring);//query the database for entries containing the term
if ($result->num_rows>0)
	{ 
		$data= array();
	while($row = $result->fetch_array())//loop through the retrieved values
		{
				$data[]=$row['first_name'];
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
	$id_product = $product->getProductId($id_product_attribute);
	$product->product_detail($id_product);
	$product->product_attribute_detail($id_product_attribute);
	$qty = $product->available_qty() - $product->order_qty();
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
	$qty = $product->available_qty() - $product->order_qty();
	$data = trim($id_product_attribute).":".$qty.":".$product_code;
	echo $data;
}
//// add order
if(isset($_GET['add'])&&isset($_POST['employee'])){
	$id_employee = $_POST['employee'];
	$employee = new employee($id_employee);
	$role = $_POST['role'];
	$reference = get_max_role_reference("PREFIX_LEND", $role);
	$payment = $_POST['payment'];
	$id_customer = 0;
	$id_cart = 0;
	$id_address = 0;
	$current_state = 3;
	$shipping_no = 0;
	$invoice_no = 0;
	$delivery_no = 0;
	$delivery_date = "";
	$comment = $_POST['comment'];
	$valid = 0;
	$date_add = dbDate($_POST['doc_date']);
	$date_upd = date('Y-m-d');
	if(dbQuery("INSERT INTO tbl_order(reference, id_customer, id_employee, id_cart, id_address_delivery, current_state, payment, shipping_number, invoice_number, delivery_number, delivery_date, comment, valid, role, date_add, date_upd,order_status) VALUES ('$reference', $id_customer, $id_employee, $id_cart, $id_address, $current_state, '$payment', $shipping_no, $invoice_no, $delivery_no, '$delivery_date', '$comment', $valid, $role, '$date_add', NOW(),0)")){
		list($id_order) = dbFetchArray(dbQuery("SELECT id_order FROM tbl_order WHERE reference = '$reference' AND id_employee = $id_employee"));
		header("location: ../index.php?content=lend&add=y&id_order=$id_order&id_employee=$id_employee");
	}else{
		$message = "ไม่สามารถเพิ่มออเดอร์ใหม่ในฐานข้อมูลได้";
		header("location: ../index.php?content=lend&add=y&error=$message");
	}
	
}

if(isset($_GET['reference'])){
	require LIB_ROOT."class/category.php";
	$reference = $_GET['reference'];
	$id_customer = $_GET['id_customer'];
	$sql = dbQuery("SELECT id_product_attribute, id_product FROM tbl_product_attribute WHERE reference ='$reference'");
	list($id_product_attribute, $id_product) = dbFetchArray($sql);
	$product = new product();
			$id_product = $product->getProductId($id_product_attribute);
			$product->product_detail($id_product,$id_customer);
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
			header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&message=$message");
		}else{
			$message = "เปลี่ยนสถานะไม่สำเร็จ";
			header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&error=$message");
		}
		}else{
			$message = "คุณไม่ได้เลือกสถานะ";
			header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&error=$message");
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
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product, $order->id_customer);
		$product->product_attribute_detail($id_product_attribute);
		$total_amount = $qty * $product->product_sell; 
		$new_total_amount = $total_amount - $old_total_amount;
		if($qty<$old_qty){
			if($order->changeQty($id_product_attribute, $qty)){
					$message = "แก้ไขเรียบร้อยแล้ว";
					header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&message=$message");
					}else{
						$message = "แก้ไขไม่สำเร็จ";
						header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&error=$message");
						exit;
					}
		}else{
			if($order->check_credit($new_total_amount)){
				if($order->changeQty($id_product_attribute, $qty)){
					$message = "แก้ไขเรียบร้อยแล้ว";
					header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&message=$message");
					}else{
						$message = "แก้ไขไม่สำเร็จ";
						header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&error=$message");
						exit;
					}
			}else{
				$message = "แก้ไขไม่สำเร็จ เนื่องจากเคดิตไม่พอ เครดิตคงเหลือ : ".$customer->credit_balance;
				header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&error=$message");
				exit;
			}
		}
		}else if($order->changeQty($id_product_attribute, $qty)){
					$message = "แก้ไขเรียบร้อยแล้ว";
					header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&message=$message");
					}else{
						$message = "แก้ไขไม่สำเร็จ";
						header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&error=$message");
						exit;
					}
}
if(isset($_GET['check_stock'])&&isset($_GET['id_product_attribute'])){
		$id_product_attribute = $_GET['id_product_attribute'];
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product);
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
	$id_product = $product->getProductId($id_product_attribute);
	$product->product_detail($id_product, $order->id_customer);
	$product->product_attribute_detail($id_product_attribute);
	if($order->insertDetail($id_product_attribute, $qty)){
		$message = "เพิ่มสินค้าเรียบร้อยแล้ว";
		header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&message=$message");

	}else{
		$message = "เพิ่มสินค้าไม่สำเร็จ";
		header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&error=$message");
	}	
}

if(isset($_GET['add'])&&isset($_GET['insert_detail'])){
	$id_order = $_POST['id_order'];
	$id_product_attribute = trim($_POST['id_product_attribute']);
	$qty = $_POST['qty'];
	$order = new order($id_order);
	$product = new product();
	$id_product = $product->getProductId($id_product_attribute);
	$product->product_detail($id_product);
	$product->product_attribute_detail($id_product_attribute);
	if($order->insert_detail($id_product_attribute, $qty)){
		$message = "เพิ่มสินค้าเรียบร้อยแล้ว";
		header("location: ../index.php?content=lend&add=y&id_order=$id_order&message=$message");
	}else{
		$message = "เพิ่มสินค้าไม่สำเร็จ";
		header("location: ../index.php?content=lend&add=y&id_order=$id_order&error=$message");
	}	
}

if(isset($_GET['edit_order'])&&isset($_GET['add_detail'])&& $_POST['qty']!=""){
	$id_order = $_POST['id_order'];
	$id_product_attribute = trim($_POST['id_product_attribute']);
	$qty = $_POST['qty'];
	$order = new order($id_order);
	$customer = new customer($order->id_customer);
	$product = new product();
	$id_product = $product->getProductId($id_product_attribute);
	$product->product_detail($id_product, $order->id_customer);
	$product->product_attribute_detail($id_product_attribute);
	$total_amount = $qty * $product->product_sell;
	if($customer->credit_amount != 0.00){
		if($order->check_credit($total_amount)){
			if($order->insertDetail($id_product_attribute, $qty)){
				$message = "เพิ่มสินค้าเรียบร้อยแล้ว";
				header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&message=$message");
		
			}else{
				$message = "เพิ่มสินค้าไม่สำเร็จ";
				header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&error=$message");
				exit;
			}
		}else{
			$message = "ไม่สามารถเพิ่มรายการได้เนื่องจากเครดิตคงเหลือไม่พอ  เครดิตคงเหลือ : ".$customer->credit_balance." ฿";
			header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&error=$message");
			exit;
		 }
	}else{
		if($order->insertDetail($id_product_attribute, $qty)){
				$message = "เพิ่มสินค้าเรียบร้อยแล้ว";
				header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&message=$message");
		
			}else{
				$message = "เพิ่มสินค้าไม่สำเร็จ";
				header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&error=$message");
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
		header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&message=$message");
	}else{
		$message = "ลบรายการไม่สำเร็จ";
		header("location: ../index.php?content=lend&edit=y&id_order=$id_order&view_detail=y&error=$message");
	}	
}

/// ลบในหน้า เพิ่ม //
if(isset($_GET['delete'])&&isset($_GET['id_order_detail'])){
	$id_order_detail = $_GET['id_order_detail'];
	list($id_order)=dbFetchArray(dbQuery("SELECT id_order FROM tbl_order_detail WHERE id_order_detail = $id_order_detail"));
	if(dbQuery("DELETE FROM tbl_order_detail WHERE id_order_detail = $id_order_detail")){
		$message = "ลบรายการเรียบร้อยแล้ว";
		header("location: ../index.php?content=lend&add=y&id_order=$id_order&message=$message");	
	}else{
		$message = "ลบรายการไม่สำเร็จ";
		header("location: ../index.php?content=lend&add=y&id_order=$id_order&error=$message");
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
	header("location: ../index.php?content=lend&add=y&id_order=$id_order&id_customer=$id_customer&message=$message");
	}else{
	$message = "เพิ่ม $n รายการเรียบร้อย";
	header("location: ../index.php?content=lend&add=y&id_order=$id_order&id_customer=$id_customer&message=$message&missing=$missing");
	}
		
}
if(isset($_GET['print_order'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	$employee = new employee($order->id_employee);
	$remark = $order->comment;
	$role = $order->role;
	switch($role){
		case 1 :
			$content="order";
			$title = "ใบสั่งขาย/Sale Order";
			break;
		case 2 : 
			$content = "requisition";
			$title = "ใบเบิกสินค้า/Requisition Product";
			break;
		case 3 :
			$content = "lend";
			$title = "ใบยืมสินค้า/Lend Product";
			break;
		case 4 :
			$content = "sponsor";
			$title = "รายการอภินันทนาการ/Sponsor Order";
			break;
		case 5 :
			$content = "consignment";
			$title = "รายการฝากขาย/Consignment Order";
			break;
		default :
			$content = "order";
			$title = "ใบสั่งขาย/Sale Order";
			break;
	}
	$total_order = ""; ///เก็บยอดรวมเพิ่มทีละรายการเวลาวนลูป
	$total_order_amount = "";///วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$total_discount_order =""; //เก็บยอดเงินส่วนลดตอนวนลูป
	$total_discount_amount = ""; //วนลูปจบเอายอดเงินส่วนลดมาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$net_total =""; //มูลค่าสินค้าหลังหักส่วนลด
	$html = "	<!DOCTYPE html>
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
				</head>";
				$doc_body_top = "<body style='padding-top:10px;'><div style='width:180mm; margin-right:auto; margin-left:auto; padding:10px'>
				<div class=\"hidden-print\">
				<button  class='btn btn-primary pull-right' onClick=\"print();\" type='button' />พิมพ์</button>
				<a href='../index.php?content=$content&edit=y&id_order=$id_order&view_detail=y' ><button  class='btn btn-primary pull-right' type='button' style='margin-right:20px;' />ยกเลิก</button></a>
</div> ";
				$doc_head = "
	<h4 style='float:left'>$title</h4>
	<table align='center' style='width:100%; table-layout:fixed;'>
		<tr><td style='width:50%;'>
			<div style='width:99.5%; height:40mm; margin-right:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:20%; padding:10px; height:5mm; vertical-align:text-top;'>ผู้ยืม :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$employee->full_name."</td></tr>
				<tr><td style='width:20%; padding:10px; vertical-align:text-top;'></td>
				<td style='padding:10px; height:30mm; vertical-align:text-top;'></td></tr>
				</table>	</div>
				</td>
			<td style='width:50%;'><div style='width:99.5%; height:40mm; margin-left:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>วันที่ :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".thaiDate($order->date_add)."</td></tr>
				<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>เลขที่เอกสาร :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$order->reference."</td></tr>
				</table>	</div></td></tr>
	</table>
	
	<table class='table table-striped' align='center' style='width:100%; table-layout:fixed; margin-top:5px;' id='order_detail'>
	<tr>
				<td style='width:10%; text-align:center; border:solid 1px #AAA; padding:10px;'>ลำดับ</td><td style='text-align:center; border:solid 1px #AAA;  padding:10px'>บาร์โค้ด</td>
				<td style='width:30%; border:solid 1px #AAA; text-align:center; padding:10px'>สินค้า</td>
			   <td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>ราคา</td><td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>จำนวน</td>
			   <td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>ส่วนลด</td><td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>มูลค่า</td>
	</tr>";
	function page_summary($total_order_amount, $total_discount_amount, $net_total, $remark){
		if($total_order_amount !=""){ $total_order_amount = number_format($total_order_amount,2);}
		if($total_discount_amount !=""){ $total_discount_amount = number_format($total_discount_amount,2); }
		if($net_total !=""){ $net_total = number_format($net_total,2); }
		echo"	<tr><td rowspan='3' colspan='3' style='border:solid 1px #AAA;  padding:10px; vertical-align:text-top;'>หมายเหตุ : $remark </td>
				<td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ราคารวม</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding:10px'>".$total_order_amount."</td></tr>
				<tr><td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ส่วนลด</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding:10px'>".$total_discount_amount."</td></tr>
				<tr><td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ยอดเงินสุทธิ</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding:10px'>".$net_total."</td></tr>
				</table>";
	}
	$row = 13;
	$sql = dbQuery("SELECT id_order_detail, id_product_attribute, barcode, product_reference, product_name, product_price, product_qty, reduction_percent, reduction_amount, discount_amount, total_amount FROM tbl_order_detail WHERE id_order = $id_order");
	$rs = dbNumRows($sql);
	$count = 1;
	$n = 1;
	$i = 0;
	if($rs>0){
		echo $html.$doc_body_top.$doc_head;
	while($i<$rs){
		list($id_order_detail, $id_product_attribute, $barcode, $product_reference, $product_name, $product_price, $product_qty, $discount_percent, $discount_amount, $total_discount, $total_amount)= dbFetchArray($sql);
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product);
		$product->product_attribute_detail($id_product_attribute);
		$total = $product_price * $product_qty;
		if($discount_percent !== 0.00){ $discount = $discount_percent ."%";}else if($discount_amount != 0.00){ $discount = $discount_amount . "฿" ;}
		echo"<tr style='height:12mm;'><td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>$n</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'><img src='".WEB_ROOT."library/class/barcode/barcode.php?text=".$barcode."' style='width:100px;' /></td>
		<td style='vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>$product_reference : $product_name</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>".number_format($product_price,2)."</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>".number_format($product_qty)."</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>$discount</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>".number_format($total,2)."</td>
				</tr>";
				$total_order += $total;
				$total_discount_order += $total_discount;
				$i++; $count++;
				if($n==$rs){ 
				$total_order_amount = $total_order;
				$total_discount_amount = $total_discount_order;
				$net_total = $total_order_amount - $total_discount_amount;
				page_summary($total_order_amount, $total_discount_amount, $net_total, $remark);
				}else{
				if($count>$row){ page_summary($total_order_amount, $total_discount_amount, $net_total, $remark); echo $doc_head; $count = 1; }
				}
				$n++; 
	}
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr>";
	}
	echo "</div></body></html>";
	 }
	
	if(isset($_GET['return_product'])){
		$id_order = $_POST['id_order'];
		$id_warehouse = 0;
		$id_zone = 0;
		$id_employee = $_POST['id_employee'];
		$date_upd = date('Y-m-d');
		$reference = $_POST['reference'];
		 for($i=0;$i<count($_POST["id_product_attribute"]);$i++){
			 $qty = $_POST['qty'][$i];
			if($qty >= "1"){
				$id_product_attribute = $_POST["id_product_attribute"][$i];
				insert_to_temp($id_order, $id_product_attribute, $qty, $id_warehouse, $id_zone, 5, $id_employee);
				update_buffer_zone($qty, $id_product_attribute);
				stock_movement("in", 5, $id_product_attribute,1, $qty, $reference,$date_upd);
			}
		}
		echo $id_order;
		$message = "บันทึกการคืนสินค้าเรียบร้อยแล้ว";
		header("location: ../index.php?content=lend&return_detail=y&id_order=$id_order&view_detail=y&message=$message");
	 }
if(isset($_GET['save_order'])){
	$id_order = $_GET['id_order'];
	$now = date("Y-m-d H:i:s");
	dbQuery("UPDATE tbl_order SET order_status = 1, date_add='$now' WHERE id_order = $id_order");
	$message = "บันทึกเรียบร้อยเเล้ว";
	header("location: ../index.php?content=lend&message=$message");
}
if(isset($_GET['check_add'])){
	$user_id = $_COOKIE['user_id'];
	list($id_order) = dbFetchArray(dbQuery("SELECT id_order FROM tbl_order WHERE id_employee = $user_id AND order_status = 0 AND role = 3"));
	if($id_order == ""){
		header("location: ../index.php?content=lend&add=y");
	}else{
		$message = "ยังไม่ได้บันทึกออร์เดอร์นี้";
		header("location: ../index.php?content=lend&add=y&id_order=$id_order&message1=$message");
	}
}

if(isset($_GET['clear_filter']))
{
	setcookie("lend_search_text","", -1, "/");
	setcookie("lend_filter", "", -1, "/");
	setcookie("lend_from_date", "", -1, "/");
	setcookie("lend_to_date", "", -1, "/");
	echo "cleared";	
}
?>