<?php 
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
require "../function/support_helper.php";


/*************************************  ส่งกลับข้อมูลเพื่อแก้ไข budget  **********************/

if( isset($_GET['get_budget']) && isset($_GET['id_support']) && isset($_GET['id_support_budget']) )
{
	$id_support = $_GET['id_support'];
	$id_support_budget = $_GET['id_support_budget'];
	$data = "";
	$sql = dbQuery("SELECT * FROM tbl_support_budget WHERE id_support_budget = ".$id_support_budget." AND id_support = ".$id_support." LIMIT 1");
	while($rs = dbFetchArray($sql))
	{	
		$data = $id_support_budget." : ".$id_support." : ".$rs['reference']." : ".$rs['limit_amount']." : ".thaiDate($rs['start'])." : ".thaiDate($rs['end'])." : ".$rs['remark']." : ".$rs['active']." : ".$rs['year'];
	}
	echo $data;
}

//************************************  เปลี่ยนปีงบประมาณที่ใช้  ******************//

if( isset($_GET['set_year']) && isset($_GET['id_support']) && isset($_GET['year']) )
{
	$id_support = $_GET['id_support'];
	$year = $_GET['year'];
	$current_year = get_support_current_year($id_support);
	$name = employee_name(get_id_employee_by_id_support($id_support));
	$rs = "false";
	$qs =dbQuery("SELECT limit_amount, start, end, year FROM tbl_support_budget WHERE id_support = ".$id_support." AND year = '".$year."'");
	$rw = dbNumRows($qs);
	if($rw >0){
		$sql = dbQuery("UPDATE tbl_support SET year = '".$year."' WHERE id_support = ".$id_support);
		if($sql){ 
			add_support_log($id_support, 0, "edit", "เปลี่ยนปีงบประมาณของ $name", "ปี ".$current_year, "ปี ".$year);
			while($rd = dbFetchArray($qs)){
				$rs = number_format($rd['limit_amount'],2)." : ".thaiDate($rd['start'], "/")." - ".thaiDate($rd['end'], "/")." : ".$rd['year'];
				}
		}
	}else{
		$rs = "noyear";
	}
	echo $rs;			
}

/***************************  ตรวจสอบปีงบประมาณซ้ำหรือไม่ ก่อนเพิ่มใหม่  ******************/
if( isset($_GET['check_valid_year']) && isset($_GET['id_support']) && isset($_GET['year']) )
{
	$id_support = $_GET['id_support'];
	$year = $_GET['year'];
	$rs = 0;
	$rw = dbNumRows(dbQuery(	"SELECT year FROM tbl_support_budget WHERE id_support = ".$id_support." AND year = '".$year."'"));
	if( $rw >0 ){ $rs = 1; }
	echo $rs;
}

/**************************  ตรวจสอบรายชื่อสปอนเซอร์ซ้ำ *****************************/

if( isset($_GET['valid_duplicate']) && isset($_GET['id_employee']) ){
	$rs = 0;
	$row = dbNumRows(dbQuery("SELECT id_support FROM tbl_support WHERE id_employee = ".$_GET['id_employee']));
	if($row > 0){ $rs = 1; }
	echo $rs;	
}

/***********************  add member ********************************/
if(isset($_GET['add_member'])&&isset($_POST['id_employee'])){
	$id_employee = $_POST['id_employee'];
	$name = employee_name($id_employee);
	$reference = $_POST['reference'];
	$limit_amount = $_POST['budget'];
	$start_date = dbDate($_POST['from_date']);
	$end_date = dbDate($_POST['to_date']);
	$remark = $_POST['remark'];
	$active = $_POST['active'];
	$year = $_POST['year'];
	$sql = dbQuery("INSERT INTO tbl_support (id_employee, active, year) VALUES (".$id_employee.", ".$active.", '".$year."')");
	$id_support = dbInsertId();
	$qr = dbQuery("INSERT INTO tbl_support_budget (id_support, reference, id_employee, limit_amount, start, end, remark, active, year, balance) VALUES (".$id_support.", '".$reference."', ".$id_employee.", ".$limit_amount.", '".$start_date."', '".$end_date."', '".$remark."', ".$active.", '".$year."', ".$limit_amount.")");
	$id_budget = dbInsertId();
	if($sql && $qr){
		$budget = get_support_budget($id_budget);
		$from_value = '-';
		add_support_log($id_support, $id_budget, "add", "เพิ่มผู้รับอภินันท์ $name และ เพิ่มวงเงินใหม่", $from_value, $budget);
	}
	if($sql && $qr){
		$message = "เพิ่มรายชื่อเรียบร้อยแล้ว";
		header("location: ../index.php?content=support&message=$message");
	}else{
		$message = "เพิ่มรายชื่อไม่สำเร็จ";
		header("location: ../index.php?content=support&add=y&error=$message");
	}
}

/****************************************  Add New Budget  *****************************/
if( isset($_GET['add_budget']) && isset($_GET['id_support']) && isset($_POST['budget']) )
{
	$id_support = $_GET['id_support'];
	$reference = $_POST['reference'];
	$budget = $_POST['budget'];
	$from = dbDate($_POST['from_date']);	
	$to = dbDate($_POST['to_date']);
	$remark = $_POST['remark'];
	$active = $_POST['active'];
	$year = $_POST['year'];
	$id_employee = get_id_employee_by_id_support($id_support);
	$name = employee_name($id_employee);
	
	$qr = dbQuery("INSERT INTO tbl_support_budget (id_support, reference, id_employee, limit_amount, start, end, remark, active, year, balance) VALUES (".$id_support.", '".$reference."', ".$id_employee.", ".$budget.", '".$from."', '".$to."', '".$remark."', ".$active.", '".$year."', ".$budget.")");
	$id_support_budget = dbInsertId();
	if($qr)
	{
		$budget_amount = get_support_budget($id_support_budget);
		add_support_log($id_support, $id_support_budget, "add", "เพิ่มงบประมาณใหม่ ปี $year ของ $name", "-", $budget_amount);
		$message = "เพิ่มงบประมาณเรียบร้อยแล้ว";
		header("location: ../index.php?content=support&edit&id_support=".$id_support."&message=".$message);
	}else{
		$message = "เพิ่มงบประมาณไม่สำเร็จ";
		header("location: ../index.php?content=support&edit&id_support=".$id_support."&error=".$message);
	}
}


/***************************************** Edit Budget  *********************************/
if( isset($_GET['edit_budget']) && isset($_POST['id_support_budget']) )
{
	$id_support = $_POST['id_support'];
	$id_support_budget = $_POST['id_support_budget'];
	$reference = $_POST['reference'];
	$budget = $_POST['budget'];
	$start_date = dbDate($_POST['from_date']);
	$end_date = dbDate($_POST['to_date']);
	$rank = get_current_support_rank($id_support_budget);
	$remark = $_POST['remark'];
	$active = $_POST['active'];
	$year = $_POST['year'];
	$balance = get_support_balance($id_support_budget);
	$old_budget = get_support_budget($id_support_budget);
	$new_balance = $balance + ($budget - $old_budget);
	$budget_year = get_current_support_budget_year($id_support_budget);
	$name = employee_name(get_id_employee_by_id_support($id_support));
	$qs = dbQuery("UPDATE tbl_support_budget SET reference = '".$reference."', limit_amount = ".$budget.", start = '".$start_date."', end = '".$end_date."', remark = '".$remark."', active = ".$active.", year = '".$year."', balance = ".$new_balance." WHERE id_support_budget = ".$id_support_budget);
	if($qs){
		$action = "";
		if( $year != $budget_year ){ $action .= "เปลี่ยนปีของงบประมาณของ $name จาก $budget_year เป็น $year "; $sep = "/"; }else{ $sep = ''; }
		if( $rank['start'] != $start_date || $rank['end'] != $end_date){ $action .= " $sep เปลี่ยนแปลงวันที่ในงบประมาณของ $name เริ่มต้น จาก ".thaiDate($rank['start']) ." เป็น ".thaiDate($start_date)." และ สิ้นสุด จาก ".thaiDate($rank['end'])." เป็น ".thaiDate($end_date)." "; $sep = "/"; }else{ $sep = ''; }
		if( $budget != $old_budget ){ $action .= " $sep เปลี่ยนแปลงงบประมาณของ $name";  $sep = '/';}else{ $sep = ''; }
		add_support_log($id_support, $id_support_budget, "edit", $action, $old_budget, $budget);
		
		$message = "ปรับปรุงข้อมูลเรียบร้อยแล้ว";
		header("location: ../index.php?content=support&edit=y&id_support=".$id_support."&message=".$message);
	}else{
		$message = "ไม่สามารถปรับปรุงข้อมูลได้";
		header("location: ../index.php?content=support&edit=y&id_support=".$id_support."&error=".$message);
	}
}

/******************************************  DELETE Budget  ************************************/
if( isset($_GET['delete_budget']) && isset($_GET['id_support']) && isset($_GET['id_support_budget']) )
{
	$id_support = $_GET['id_support'];
	$id_support_budget = $_GET['id_support_budget'];
	$name = employee_name(get_id_employee_by_id_support($id_employee));
	$budget = get_support_budget($id_support_budget);
	$qs = dbQuery("DELETE FROM tbl_support_budget WHERE id_support = ".$id_support." AND id_support_budget = ".$id_support_budget);
	if($qs){
		add_support_log($id_support, $id_support_budget, "delete", "ลบงบประมาณ ของ $name", $budget, 0.00);
		$message = "ลบงบประมาณเรียบร้อยแล้ว";
		header("location: ../index.php?content=support&edit=y&id_support=".$id_support."&message=".$message);
	}else{
		$message = "ลบงบประมาณไม่สำเร็จ";
		header("location: ../index.php?content=support&edit=y&id_support=".$id_support."&error=".$message);
	}
}

/***************************************** edit member  ****************************************/
if(isset($_GET['edit_member']) && isset($_GET['id_support']) && isset($_GET['id_employee']) ){
	$id_support = $_GET['id_support'];
	$id_employee = $_GET['id_employee'];
	$name = employee_name($id_employee);
	$old_id_employee = get_id_employee_by_id_support($id_support);
	$old_name = employee_name($old_id_employee);
	$rs = "false";
	$qs = dbQuery("UPDATE tbl_support SET id_employee = ".$id_employee." WHERE id_support = ".$id_support);
	if($qs){
		add_support_log($id_support, 0, "edit", "เปลี่ยนชื่อผู้รับงบประมาณ", $old_name, $name);
		$qr = dbQuery("UPDATE tbl_support_budget SET id_employee = ".$id_employee." WHERE id_support = ".$id_support);
		if($qr){ $rs = $name; }
	}
	echo $rs;
}

/********************************************* DELETE MEMBER ******************************/
if( isset($_GET['delete_member']) && isset($_GET['id_support']) )
{
	$id_support = $_GET['id_support'];
	$old_id_employee = get_id_employee_by_id_support($id_support);
	$old_name = employee_name($old_id_employee);
	$rw = dbNumRows(dbQuery("SELECT id_support_budget FROM tbl_support_budget WHERE id_support = ".$id_support));
	if($rw > 0)
	{
		$message = "ไม่สารมารถลบรายการนี้ได้ เนื่องจากยังมีรายการ งบประมาณค้างอยู่";
		header("location: ../index.php?content=support&error=".$message);
	}else{
		$qs = dbQuery("DELETE FROM tbl_support WHERE id_support = ".$id_support);
		if($qs){
			add_support_log($id_support, 0, "delete", "ลบผู้รับงบประมาณ", $old_name, "");
			$message = "ลบรายการเรียบร้อยแล้ว";
			header("location: ../index.php?content=support&message=".$message);			
		}else{
			$message = "ลบรายการไม่สำเร็จ";
			header("location: ../index.php?content=support&error=".$message);
		}
	}		
	
}


/******************************************************************************************************   ORDER  ***********************************************************************/

//*********************************  เพิ่มออเดอร์  ***************************//
if( isset($_GET['add_order']) && isset($_POST['id_employee']) )
{
	$id_employee 	= $_POST['id_employee'];
	$id_customer 	= $_POST['id_customer'];
	$date_add 		= dbDate($_POST['doc_date'], true);
	$reference = get_max_role_reference("PREFIX_SUPPORT",7);
	$payment 		= "เบิกอภินันท์";
	$role 				= 7;
	$id_cart			= 0;
	$current_state = 1;
	$comment = $_POST['remark'];
	$valid = 0;
	$status = 0;
	$amount = 0.00;
	$id_user = $_COOKIE['user_id'];
	list($id_support, $year) = dbFetchArray(dbQuery("SELECT id_support, year FROM tbl_support WHERE id_employee = ".$id_employee));
	list($id_budget) = dbFetchArray(dbQuery("SELECT id_support_budget FROM tbl_support_budget WHERE id_support = ".$id_support." AND year = '".$year."' AND active = 1"));
	$qr = dbQuery("INSERT INTO tbl_order (reference, id_customer, id_employee, id_cart, current_state, payment, comment, valid, role, date_add, order_status) VALUES ('".$reference."', ".$id_customer.", ".$id_employee.", ".$id_cart.", ".$current_state.", '".$payment."', '".$comment."', ".$valid.", ".$role.", '".$date_add."', ".$status.")");
	if($qr){
		$id_order = dbInsertId();
		$qs = dbQuery("INSERT INTO tbl_order_support(id_order, id_customer, id_employee, id_support, id_budget, year, amount, status, date_add, id_user) VALUES (".$id_order.", ".$id_customer.", ".$id_employee.", ".$id_support.", ".$id_budget.", '".$year."', ".$amount.", ".$status.", '".$date_add."', ".$id_user.")");
		$id_order_support = dbInsertId();
		order_state_change($id_order, $current_state, $id_user);
		header("location: ../index.php?content=order_support&edit=y&id_order=".$id_order."&id_order_support=".$id_order_support);
	}else{
		header("location: ../index.php?content=order_support&error=เพิ่มออเดอร์ไม่สำเร็จ");
	}
}


//*************************ajax  แก้ไขออเดอร์  ajax ***************************//
if( isset($_GET['edit_order']) && isset($_GET['id_order']) && isset($_GET['id_order_support']) )
{
	$id_order = $_GET['id_order'];
	$id_order_support = $_GET['id_order_support'];
	$id_employee = $_POST['id_employee'];
	$id_customer = $_POST['id_customer'];
	$date_add = dbDate($_POST['doc_date'],true);
	$remark = $_POST['remark'];
	$id_user = $_COOKIE['user_id'];
	$old_id_budget = $_GET['id_budget'];
	$order = new order($id_order);
	list($id_support, $year) = dbFetchArray(dbQuery("SELECT id_support, year FROM tbl_support WHERE id_employee = ".$id_employee));
	list($id_budget) = dbFetchArray(dbQuery("SELECT id_support_budget FROM tbl_support_budget WHERE id_support = ".$id_support." AND year = '".$year."' AND active = 1"));
	$order_amount = $order->getCurrentOrderAmount($id_order);
	$budget_balance = get_support_balance($id_budget);
	if($order_amount > $budget_balance){
		echo "over_budget : ".$id_budget;
	}else{
		$qs = dbQuery("UPDATE tbl_order SET id_customer = ".$id_customer.", id_employee = ".$id_employee.", comment = '".$remark."', date_add = '".$date_add."' WHERE id_order = ".$id_order);
		if($qs){
			$qr = dbQuery("UPDATE tbl_order_support SET id_customer = ".$id_customer.", id_employee = ".$id_employee.", id_support = ".$id_support." , id_budget = ".$id_budget.", year = '".$year."', id_user=".$id_user." WHERE id_order_support = ".$id_order_support);
			if($qr){
				$balance = $budget_balance - $order_amount;
				update_support_balance($id_budget, $balance);
				$old_balance = get_support_balance($old_id_budget);
				$old_balance += $order_amount;
				update_support_balance($old_id_budget, $old_balance);
				echo "success : ".$id_budget;
			}else{
				echo "false : ".$id_budget;
			}
		}else{
			echo "fail : ".$id_budget;
		}
	}
}


///***********************************************  เพิ่มรายการสั่งสินค้า  ************************************//
if(isset($_GET['add_to_order'])){
	$id_order= $_POST['id_order'];
	$order= new order($id_order);
	$id_employee = $order->id_employee;
	$id_order_support = $_POST['id_order_support'];
	$id_budget = $_POST['id_budget'];
	$order_qty = $_POST['qty'];
	$n = 0;
	$missing = "";
	foreach ($order_qty as $id_clolr =>$items ){	
		foreach($items as $id => $qty)
		{
			if($qty !=""){
				$product = new product();
				$id_product = $product->getProductId($id);
				$product->product_attribute_detail($id);
				$total_amount = $qty*$product->product_sell;		
				$balance = get_support_balance($id_budget);	
				if($total_amount <= $balance){
					if(!ALLOW_UNDER_ZERO)
					{
								$instock = $product->available_order_qty($id); 
								if($qty>$instock)
								{
									$missing .= $product->reference." : มียอดคงเหลือไม่เพียงพอ &nbsp;<br/>";
								}
								else
								{
											if($order->insert_support_detail($id, $qty))
											{
												$amount = $balance - $total_amount;
												update_support_balance($id_budget, $amount);
												$n++;
											}
											else
											{
												$missing .= $product->reference. " : ".$order->error_message. "<br/>";
											}
									}
						}
						else
						{
								if($order->insert_support_detail($id, $qty))
								{
									$amount = $balance - $total_amount;
									update_support_balance($id_budget, $amount);
									$n++;
								}
								else
								{
									$missing .= $product->reference. " : ".$order->error_message. "<br/>";
								}
						}
				}
				else
				{
					$missing .= 	$product->reference." : งบประมาณคงเหลือไม่เพียงพอ";
				}//if($order_amount <= $balance)
			}// if qty !=0
		}// foreach
	}// foreach
	if($missing ==""){
		$message = "เพิ่ม $n รายการเรียบร้อย";
		header("location: ../index.php?content=order_support&edit=y&id_order=".$id_order."&id_order_support=".$id_order_support."&message=$message");
	}else{
		$message = $missing;
		header("location: ../index.php?content=order_support&edit=y&id_order=".$id_order."&id_order_support=".$id_order_support."&error=$message");
	}
}

/// ลบในหน้า แก้ไข
if( isset($_GET['delete_item']) && isset($_GET['id_order_detail']) && isset($_GET['id_budget']) )
{
	$id_order = $_GET['id_order'];
	$id_order_detail = $_GET['id_order_detail'];
	$id_order_support = $_GET['id_order_support'];
	$id_budget = $_GET['id_budget'];
	$amount = $_GET['amount'];
	$balance = get_support_balance($id_budget);
	$qr = dbQuery("DELETE FROM tbl_order_detail WHERE id_order_detail = ".$id_order_detail." AND id_order = ".$id_order);
	if($qr){
		$balance += $amount;
		$rs = update_support_balance($id_budget, $balance);
		if($rs)
		{
			$message = "ลบรายการเรียบร้อยแล้ว";
			header("location: ../index.php?content=order_support&edit=y&id_order=".$id_order."&id_order_support=".$id_order_support."&message=".$message);
		}else{
			$message = "ลบรายการสำเร็จแต่ปรับปรุงงบคงเหลือไม่สำเร็จ";
			header("location: ../index.php?content=order_support&edit=y&id_order=".$id_order."&id_order_support=".$id_order_support."&error=".$message);
		}
	}else{
		$message = "ลบรายการไม่สำเร็จ";
		header("location: ../index.php?content=order_support&edit=y&id_order=".$id_order."&id_order_support=".$id_order_support."&error=".$message);
	}	
}



//***************************** เปลี่ยนสถานะออเดอร์ในหน้ารายละเอียด  **********************//
if( isset($_GET['state_change']) && isset($_GET['id_order']) &&  isset($_POST['id_state']) )
{
	$id_order = $_GET['id_order'];
	$id_order_state = $_POST['id_state'];
	$id_user = $_POST['id_user'];
	$rs = false;
	if($id_order_state != 0)
	{
		$rs = order_state_change($id_order, $id_order_state, $id_user);			
	}
	if($rs)
	{
		header("location: ../index.php?content=order_support&id_order=".$id_order."&view_detail");
	}else{
		$message = "เปลี่ยนสถานะไม่สำเร็จ";
		header("location: ../index.php?content=order_support&id_order=".$id_order."&view_detail&error=".$message);
	}
}


//*****************************  Save order  **********************//
if( isset($_GET['save_order']) && isset($_GET['id_order']) )
{
	$qr = dbQuery("UPDATE tbl_order SET order_status = 1 WHERE id_order =".$_GET['id_order']);
	header("location: ../index.php?content=order_support");
}


if(isset($_GET['check_add'])){
	$user_id = $_COOKIE['user_id'];
	$qs = dbQuery("SELECT tbl_order_support.id_order FROM tbl_order_support JOIN tbl_order ON tbl_order_support.id_order = tbl_order.id_order WHERE id_user = ".$user_id." AND order_status = 0 AND role = 7 LIMIT 1");
	if(dbNumRows($qs) < 1) {
		header("location: ../index.php?content=order_support&add=y");
	}else{
		$rs = dbFetchArray($qs);
		$id_order = $rs['id_order'];
		$id_order_support = get_id_order_support($id_order);
		$message = "ยังไม่ได้บันทึกออร์เดอร์นี้";
		header("location: ../index.php?content=order_support&edit=y&id_order=".$id_order."&id_order_support=".$id_order_support."&warning=".$message);
	}
}



if(isset($_GET['clear_filter'])){
		setcookie("support_from_date","",time()-3600,"/");
		setcookie("support_to_date","",time()-3600,"/");
		setcookie("support_search-text", $text, time() - 3600, "/");
		setcookie("support_filter",$filter, time() - 3600,"/");
		header("location: ../index.php?content=order_support");
}

//// ปริ๊นออเดอร์ไปนำเข้า  formula
if(isset($_GET['print_order'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	$employee = employee_name($order->id_employee);
	$id_order_support = $_GET['id_order_support'];
	$customer = new customer($order->id_customer);
	$remark = $order->comment;
	$title = "ใบเบิกอภินันท์/Sponsored Order";
	$qty = ""; ///เก็บยอดรวมเพิ่มทีละรายการเวลาวนลูป
	$total_qty = "";/////วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$total_order = ""; ///เก็บยอดรวมเพิ่มทีละรายการเวลาวนลูป
	$total_order_amount = "";///วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$total_discount_order =""; //เก็บยอดเงินส่วนลดตอนวนลูป
	$total_discount_amount = ""; //วนลูปจบเอายอดเงินส่วนลดมาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$net_total =""; //มูลค่าสินค้าหลังหักส่วนลด
	$row = 17;
	$sql = dbQuery("SELECT id_order_detail, id_product_attribute, barcode, product_reference, product_name, product_price, product_qty, reduction_percent, reduction_amount, discount_amount, total_amount FROM tbl_order_detail WHERE id_order = $id_order ORDER BY barcode ASC");
	$rs = dbNumRows($sql);
	$total_page = ceil($rs/$row);
	$page = 1;
	$count = 1;
	$n = 1;
	$i = 0;
	$html = "	<!DOCTYPE html>
				<html>
				<head>
					<meta charset='utf-8'>
					<meta name='viewport' content='width=device-width, initial-scale=1.0'>
					<link rel='icon' href='../favicon.ico' type='image/x-icon' />
					<title>ออเดอร์</title>
					<!-- Core CSS - Include with every page -->
					<link href='../../library/css/bootstrap.css' rel='stylesheet'>
					<link href='../../library/css/font-awesome.css' rel='stylesheet'>
					<link href='../../library/css/bootflat.min.css' rel='stylesheet'>
					 <link rel='stylesheet' href='../../library/css/jquery-ui-1.10.4.custom.min.css' />
					 <script src='../../library/js/jquery.min.js'></script>
					<script src='../../library/js/jquery-ui-1.10.4.custom.min.js'></script>
					<script src='../../library/js/bootstrap.min.js'></script>  
					<!-- SB Admin CSS - Include with every page -->
					<link href='../../library/css/sb-admin.css' rel='stylesheet'>
					<link href='../../library/css/template.css' rel='stylesheet'>
				</head>";
				$doc_body_top = "<body style='padding-top:0px; margin-top:-15px;'><div style='width:180mm; margin-right:auto; margin-left:auto; padding:10px; '>
				<div class='hidden-print' style='margin-bottom:0px; margin-top:10px;'>
				<button  class='btn btn-primary pull-right' onClick=\"print();\" type='button' />พิมพ์</button>
				<a href='../index.php?content=order_support&id_order=".$id_order."&id_order_support=".$id_order_support."&view_detail=y' ><button  class='btn btn-primary pull-right' type='button' style='margin-right:20px;' />ยกเลิก</button></a>
</div> ";
				function doc_head($order,$company, $customer, $employee, $title, $page, $total_page){
					$result = "
	<h4>$title</h4><p class='pull-right'>หน้า $page / $total_page</p>
	<table align='center' style='width:100%; table-layout:fixed;'>
		<tr><td style='width:50%;'>
			<div style='width:99.5%; height:20mm; margin-right:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr>
					<td align='right' style='width:20%; padding:10px; height:5mm; vertical-align:text-top;'>ผู้เบิก :</td>
					<td style='padding:10px; vertical-align:text-top; height:5mm;'>".$employee."</td>
				</tr>
				<tr>
					<td align='right' style='width:20%; padding:10px; vertical-align:text-top;'>ผู้รับ :</td>
					<td style='padding:10px; height:30mm; vertical-align:text-top;'>".$customer->full_name."</td>
				</tr>
				</table>	</div>
				</td>
			<td style='width:50%;'><div style='width:99.5%; height:20mm; margin-left:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td align='right' style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>วันที่ :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".thaiDate($order->date_add,"/")."</td></tr>
				<tr><td align='right' style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>เลขที่เอกสาร :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$order->reference."</td></tr>
				</table>	</div></td></tr>
	</table>
	
	<table class='table table-striped' align='center' style='width:100%; table-layout:fixed; margin-top:5px; ' id='order_detail'>
	<tr>
				<td style='width:10%; text-align:center; border:solid 1px #AAA; padding:10px;'>ลำดับ</td><td style='text-align:center; border:solid 1px #AAA;  padding:10px'>บาร์โค้ด</td>
				<td style='width:30%; border:solid 1px #AAA; text-align:center; padding:10px'>สินค้า</td>
			   <td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>ราคา</td><td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>จำนวน</td>
			   <td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>ส่วนลด</td><td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>มูลค่า</td>
	</tr>"; return $result; }
	function page_summary($total_order_amount, $total_discount_amount, $net_total, $remark, $total_qty=""){
		if($total_order_amount !=""){ $total_order_amount = number_format($total_order_amount,2);}
		if($total_discount_amount !=""){ $total_discount_amount = number_format($total_discount_amount,2); }
		if($net_total !=""){ $net_total = number_format($net_total,2); }
		echo"	<tr style='height:12mm;'><td colspan='7' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px; vertical-align:text-top; text-align:right;'>รวม $total_qty หน่วย</td></tr>
				<tr style='height:12mm;'><td rowspan='3' colspan='3' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px; vertical-align:text-top;'>หมายเหตุ : $remark </td>
					<td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ราคารวม</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>".$total_order_amount."</td></tr>
				<tr style='height:12mm;'><td colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>ส่วนลด</td>
					<td align='right' colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>".$total_discount_amount."</td></tr>
				<tr style='height:12mm;'><td colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>ยอดเงินสุทธิ</td>
					<td align='right' colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>".$net_total."</td></tr>
				</table>";
	}
	
	if($rs>0){
		echo $html.$doc_body_top.doc_head($order, $company, $customer, $employee, $title,$page, $total_page);
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
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>".number_format($total_amount,2)."</td>
				</tr>";
				$total_qty += $product_qty;
				$total_order += $total;
				$total_discount_order += $total_discount;
				$i++; $count++;
				if($n==$rs){ 
				$ba_row = $row - $count -4; 
				$ba = 0;
				if($ba_row >0){
					while($ba <= $ba_row){
						if($count+1 >$row){  $css_ba_row ="border-bottom: solid 1px #AAA; border-top: 0px;";  }else{ $css_ba_row ="border-top: 0px;";}
						echo"<tr style='height:12mm;'>
								<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>&nbsp;</td>
								<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>&nbsp;</td>
								<td style='vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>&nbsp;</td>
								<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>&nbsp;</td>
								<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>&nbsp;</td>
								<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>&nbsp;</td>
								<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>&nbsp;</td>
				</tr>";
						$ba++; $count++;
					}
				}
				$total_all_qty = $total_qty;
				$total_order_amount = $total_order;
				$total_discount_amount = $total_discount_order;
				$net_total = $total_order_amount - $total_discount_amount;
				page_summary($total_order_amount, $total_discount_amount, $net_total, $remark, $total_all_qty);
				}else{
				if($count>$row){  $page++; echo "</table><div style='page-break-after:always;'></div>".doc_head($order, $company, $customer, $title, $page, $total_page); $count = 1;  }
				}
				$n++; 
	}
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr>";
	}
	echo "</div></body></html>";
	 }
	
?>