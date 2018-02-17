<?php 
include "../../library/config.php";
include "../../library/functions.php";
include "../function/tools.php";
function checked_qty($id_order, $id_product_attribute){
		 $sql = dbQuery("SELECT SUM(qty) FROM tbl_qc WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND valid =1");
		 $sqr =dbQuery("SELECT SUM(qty) FROM tbl_temp WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND status IN(1,2,3,4,6)");
		 $sqm = dbQuery("SELECT product_qty FROM tbl_order_detail WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order");
		 $sqa = dbQuery("SELECT id_temp FROM tbl_temp WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND status = 1");
		 list($re) = dbFetchArray($sql);
		 list($rs) = dbFetchArray($sqr);
		 list($rm) = dbFetchArray($sqm);
		 list($ra) = dbFetchArray($sqa);
		 if($re <1){ $result['current'] = 0; }else{ $result['current'] = $re; } // ยอด qc
		$result['prepare_qty'] = $rs; // ยอดจัด
		$result['order_qty'] = $rm; //ยอดสั่ง
		$result['id_temp'] = $ra; //id_temp
		return $result;
	 }
	 
if( isset( $_GET['save_qc'] ) && isset( $_GET['id_order'] ) )
{
	$id_order 		= $_GET['id_order'];
	$id_employee 	= $_GET['id_user'];
	$barcode		= $_POST['barcode'];
	$time				= $_POST['time'];
	$box 				= $_POST['id_box'];
	$product 		= new product();
	foreach($barcode as $i => $code) :
		$code = trim($code);
		$arr = $product->check_barcode($code);
		$id_product_attribute = $arr['id_product_attribute'];
		$date_upd = date("Y-m-d H:i:s", strtotime($time[$i]));
		$id_box   = $box[$i];
		$qty = $arr['qty'];
		if( $id_product_attribute != "" ) :
			$check = checked_qty($id_order, $id_product_attribute); ///ตรวจสอบยอดจัด กับยอดที่ qc แล้ว
			$order_qty = $check['order_qty']; // ยอดสั่ง
			$current_qty = $check['current'];// ยอด qc
			$prepare_qty = $check['prepare_qty'];	// ยอดจัด
			$id_temp = $check['id_temp'];
			// ถ้าไม่มีอะไรผิดพลาด บันทึกรายการปกติ
			$qc = dbQuery("INSERT INTO tbl_qc (id_employee, id_order, id_product_attribute, qty, date_upd, valid, error_id, id_box, id_temp) VALUES ($id_employee, $id_order, $id_product_attribute, $qty, '$date_upd', 1, 0, $id_box, $id_temp)");
			if($qc)
			{
				dbQuery("UPDATE tbl_order_detail SET date_upd = NOW() WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
				dbQuery("UPDATE tbl_temp SET status = 6 WHERE id_temp = $id_temp");
		
				if(($current_qty+$qty) == $order_qty)
				{ /// ถ้ายอดตรวจครบตามจำนวนที่จัดมา อัพเดตสถานะใน tbl_temp ให้เป็น QC แล้ว
					dbQuery("UPDATE tbl_temp SET status = 2 WHERE id_order =$id_order AND id_product_attribute = $id_product_attribute");
				}
			}
		endif;
	endforeach;
	echo "success";
}


if(isset($_GET['checked'])&&isset($_GET['id_order'])){
	$id_order 					= $_GET['id_order'];
	$id_employee 				= $_GET['id_user'];
	$barcode 					= trim($_GET['barcode_item']);
	$id_box 						= $_GET['id_box'];
	$product 					= new product();
	$arr 							= $product->check_barcode($barcode); ///ดึง id_product_attribute และ จำนวน จากบาร์โค้ด คืนค่ามาเป็น array [id_product_attribute] และ [qty] ตามลำดับ
	$id_product_attribute 	= $arr['id_product_attribute'];
	$qty 							= $arr['qty'];
	if($id_product_attribute == "" )
	{
		$message = "บาร์โค้ดผิด หรือ ไม่มีรายการสินค้านี้ในระบบ";
		echo "erreo :".$message;
	}else if(check_product_in_order($id_product_attribute, $id_order)){ ///ถ้ายอดจัดผิด บันทึก error ลง tbl_qc แล้วส่ง error กลับ
		$check 			= checked_qty($id_order, $id_product_attribute); ///ตรวจสอบยอดจัด กับยอดที่ qc แล้ว
		$order_qty 		= $check['order_qty']; // ยอดสั่ง
		$current_qty 	= $check['current'];// ยอด qc
		$prepare_qty 	= $check['prepare_qty'];	// ยอดจัด
		$id_temp 		= $check['id_temp'];
		if($current_qty+$qty > $prepare_qty || $current_qty+$qty >$order_qty){ ///ถ้ายอดจัดเกิน บันทึก error ลง tbl_qc แล้วส่ง error กลับ
			$message = "สินค้าเกิน";
			dbQuery("INSERT INTO tbl_qc (id_employee, id_order, id_product_attribute, qty, date_upd, valid, error_id) VALUES ($id_employee, $id_order, $id_product_attribute, $qty, NOW(), 0, 2)");
			echo "erreo :".$message;
		}else{
			// ถ้าไม่มีอะไรผิดพลาด บันทึกรายการปกติ
			
			$qc = dbQuery("INSERT INTO tbl_qc (id_employee, id_order, id_product_attribute, qty, date_upd, valid, error_id, id_box, id_temp) VALUES ($id_employee, $id_order, $id_product_attribute, $qty, NOW(), 1, 0, $id_box, $id_temp)");
			
			if($qc)
			{
				dbQuery("UPDATE tbl_order_detail SET date_upd = NOW() WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
				dbQuery("UPDATE tbl_temp SET status = 6 WHERE id_temp = $id_temp");
		
				if(($current_qty+$qty) == $order_qty)
				{ /// ถ้ายอดตรวจครบตามจำนวนที่จัดมา อัพเดตสถานะใน tbl_temp ให้เป็น QC แล้ว
					dbQuery("UPDATE tbl_temp SET status = 2 WHERE id_order =$id_order AND id_product_attribute = $id_product_attribute");
				}
			$qc_qty = $current_qty+$qty;
			echo "ok:".$id_product_attribute.":".$qc_qty;
			}else{
				$message = " QC ไม่ผ่านลองใหม่อีกครั้ง";
				echo "erreo :".$message;
			}
		}
	}else{
		$message = "จัดสินค้าผิด";
		dbQuery("INSERT INTO tbl_qc (id_employee, id_order, id_product_attribute, qty, date_upd, valid, error_id) VALUES ($id_employee, $id_order, $id_product_attribute, $qty, NOW(), 0, 1)");
		echo "erreo :".$message;
	}
}


////// ปิดการตรวจเมื่อตรวจสินค้าครบแล้ว ///////
if(isset($_GET['close_job'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$id_employee = $_GET['id_employee'];
	$order = new order($id_order);
	if($order->current_state != 11){
		header("location: ../index.php?content=qc");
	}else{
		dbQuery("UPDATE tbl_temp SET status = 3 WHERE id_order = $id_order AND (status = 2 OR status = 6)");	
		if($order->state_change($order->id_order, 10, $id_employee)){
			header("location: ../index.php?content=qc");
		}else{
			$message = "ปิดการจัดไม่สำเร็จ";
			header("location: ../index.php?content=qc&process&id_order=$id_order&error=$message");
		}
	}
}

////// ปิดการตรวจเมื่อตรวจสินค้าครบแล้ว ///////
if(isset($_GET['close_job2'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$id_employee = $_GET['id_employee'];
	$order = new order($id_order);
	if($order->current_state != 11){
		header("location: ../index.php?content=qc2");
	}else{
		dbQuery("UPDATE tbl_temp SET status = 3 WHERE id_order = $id_order AND (status = 2 OR status = 6)");	
		if($order->state_change($order->id_order, 10, $id_employee)){
			header("location: ../index.php?content=qc2");
		}else{
			$message = "ปิดการจัดไม่สำเร็จ";
			header("location: ../index.php?content=qc2&process&id_order=$id_order&error=$message");
		}
	}
}

if(isset($_GET['over_order'])&&isset($_GET['id_zone'])&&isset($_GET['id_product_attribute'])){
	$id_zone = $_GET['id_zone'];
	$id_product_attribute = $_GET['id_product_attribute'];
	$product = new product();
	$id_product = $product->getProductId($id_product_attribute);
	$id_order = $_GET['id_order'];
	$id_warehouse = get_warehouse_by_zone($id_zone);
	$id_employee = $_COOKIE['user_id'];
	list($old_qty) = dbFetchArray(dbQuery("SELECT qty FROM tbl_stock WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute"));
	$new_qty = -1;
	$qty = 1;
	//echo $new_qty;
	cancle_product($qty, $id_product, $id_product_attribute, $id_order, $id_zone, $id_warehouse, $id_employee);
	//update_buffer_zone($qty, $id_product_attribute);
	update_stock_zone($new_qty, $id_zone, $id_product_attribute);
	header("location: ../index.php?content=qc&process&id_order=$id_order");
}

if(isset($_GET['getData'])&&isset($_GET['id_product_attribute'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$id_product_attribute = $_GET['id_product_attribute'];
	$qs = dbQuery("SELECT id_temp FROM tbl_temp WHERE id_product_attribute = ".$id_product_attribute." AND id_order = ".$id_order);
	$rs = dbNumRows($qs);
	$in = "";
	if($rs>0){
		$i = 0;
		while($ro = dbFetchArray($qs) ){
			$in .= $ro['id_temp'];
			$i++;
			if($i < $rs){ $in .= ", "; }
		}
	}
			
	$sql = dbQuery("SELECT id_qc, id_product_attribute, qty FROM tbl_qc WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order AND valid =1 AND id_temp NOT IN(".$in.") ");
	$order_qty = check_qty_in_order($id_product_attribute, $id_order);
	$product = new product();
	$product->product_attribute_detail($id_product_attribute);
	$header = "<table class='table table-striped'><tr><td colspan='3' >".$product->reference." จำนวนสั่ง |  $order_qty  ตัว </td></tr>
					<tr><td colspan='3' align='center'>สินค้าที่ต้องเอาออก</td></tr>
				<tr><td style='width:60%;'>สินค้า</td><td style='width:20%; text-align:center;'>จำนวน</td><td style='width:20%; text-align:center;'>เอาออก</td></tr>";
	$body = "";
	$end = "</table>";
	while($row = dbFetchArray($sql)){
		$id_qc = $row['id_qc'];
		$qty = $row['qty'];
		$body .="<tr><td>".$product->reference."</td><td align='center'>$qty</td>
		<td align='center'>
		<input type='hidden' name='id_qc[]' value='$id_qc' />
		<input type='hidden' name='order_qty[$id_qc]' value='$qty' />
		<input type='checkbox' class='from-control' name='edit[$id_qc]' /></td>";
	}
	$result = $header.$body.$end;
	echo $result;
}
if(isset($_GET['edit_qc'])&&isset($_POST['id_qc'])){
	$id_qc = $_POST['id_qc'];
	$edit_qty = $_POST['edit'];	
	$id_order = $_POST['id_order'];
	foreach($id_qc as $id){
		$edit = $edit_qty[$id];
		if($edit =="on"){ 
		dbQuery("DELETE FROM tbl_qc WHERE id_qc = $id");
		}
	}
	header("location: ../index.php?content=qc&process=y&id_order=$id_order");
}
if(isset($_GET['reload'])){
	echo "<table class='table'>
		<thead>
			<th style='width: 5%; text-align:center;'>ลำดับ</th><th style='width: 20%; text-align:center;'>เลขที่เอกสาร</th>
			<th style='width: 15%; text-align:center;'>ลูกค้า</th><th style='width: 15%; text-align:center;'>รูปแบบ</th><th style='width: 15%; text-align:center;'>วันที่สั่ง</th>
			<th style='width: 15%; text-align:center;'>พนักงานจัด</th><th style='width: 15%; text-align:center;'>&nbsp;</th>
		</thead>";
		$sql = dbQuery("SELECT id_order FROM tbl_order WHERE current_state = 5");
		$n = 1;
		while($row = dbFetchArray($sql)){
			$order = new order($row['id_order']);
			$customer = new customer($order->id_customer);
			list($id_employee) = dbFetchArray(dbQuery("SELECT id_employee FROM tbl_temp WHERE id_order =".$order->id_order." AND status = 1 GROUP BY id_employee"));
			$employee = new employee($id_employee);
			echo"
			<tr>
					<td align='center'>$n</td>
					<td align='center'>".$order->reference."</td>
					<td align='center'>".$customer->full_name."</td>
					<td align='center'>".$order->role_name."</td>
					<td align='center'>".thaiDate($order->date_add)."</td>
					<td align='center'>".$employee->full_name."</td>
					<td align='center'><a href='index.php?content=qc&process=y&id_order=".$order->id_order."'><span class='btn btn-default'>ตรวจสินค้า</span></a></td>
			</tr>";
			$n++;
		}
echo"		</table>";
}
if(isset($_GET['delete'])){
	$id_product_attribute = $_GET['id_product_attribute'];
	$id_order = $_GET['id_order'];
	dbQuery("DELETE FROM tbl_qc WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order");
	header("location: ../index.php?content=qc&process=y&id_order=$id_order");
}

if( isset($_GET['add_box']) && isset($_GET['id_order']) && isset($_GET['barcode_box']) )
{
	$d_box = "";
	$id_order = $_GET['id_order'];
	$barcode = $_GET['barcode_box'];
	$qs = dbQuery("SELECT id_box, valid FROM tbl_box WHERE barcode = '".$barcode."'");
	if(dbNumRows($qs) > 0){
		list($id, $valid) = dbFetchArray($qs);
		$id_box = $id;	
		if($valid == 0 ){
			echo "success : ".$id_box;
		}else{
			echo "closed : ".$id_box;
		}
	}else{
		$qr = dbQuery("INSERT INTO tbl_box (barcode, id_order) VALUES ('".$barcode."', ".$id_order.")");
		if($qr){
			$id_box = dbInsertId();
			echo "success : ".$id_box;
		}else{
			echo "fail : xx";
		}
	}
}


//// Print Packing List
if(isset($_GET['print_packing_list'])&&isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	$employee = employee_name($order->id_employee);
	$customer = new customer($order->id_customer);
	$remark = $order->comment;
	$title = "Packing List";
	$number = $_GET['number'];
	$id_box = $_GET['id_box'];
	$total_qty = "";/////วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$row = 17;
	//$sql = dbQuery("SELECT id_order_detail, id_product_attribute, barcode, product_reference, product_name, product_price, product_qty, reduction_percent, reduction_amount, discount_amount, total_amount FROM tbl_order_detail WHERE id_order = $id_order ORDER BY barcode ASC");
	//echo "SELECT id_product_attribute, SUM(qty) as qty FROM tbl_qc WHERE id_order = ".$id_order." AND id_box = ".$id_box." AND valid = 1 GROUP BY id_product_atttribute";
	$sql = dbQuery("SELECT id_product_attribute, SUM(qty) as qty FROM tbl_qc WHERE id_order = ".$id_order." AND id_box = ".$id_box." AND valid = 1 GROUP BY id_product_attribute");;
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
					<title>แพ็คกิ้งสิส</title>
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
				<button  class='btn btn-primary pull-right' onClick=\"print();\" type='button' />พิมพ์</button></div> ";
				
	function doc_head($order,$company, $customer, $number, $title, $page, $total_page){
	$result = "
	<h4>$title</h4><p class='pull-right'>หน้า ".$page." / ".$total_page."</p>
	<table align='center' style='width:100%; table-layout:fixed;'>
		<tr>
			<td style='width:70%;'>
			
				<div style='width:99.5%; height:30mm; margin-right:0.5%; border: 1px solid #AAA;'>
					
					<table width='100%'>
						<tr style='font-size: 16px;'>
							<td style='width:50%; padding:10px; height:15mm; vertical-align:text-top;'>เอกสาร : ".$order->reference."</td>
							<td style='padding:10px; vertical-align:text-top; height:15mm;'>วันที่ : ".thaiDate($order->date_add,"/")."</td>
						</tr>
						<tr style='font-size: 16px;'>
							<td colspan='2' style='width:20%; padding:10px; vertical-align:text-top;'>ลูกค้า : ".$customer->full_name."</td>
						</tr>
				</table>	
				
			  </div>
			  
			</td>
			
			
			
			<td style='width:30%;'>
				<div style='width:99.5%; height:30mm; margin-left:0.5%; border: 1px solid #AAA;'>
					<table width='100%'>
						<tr>
							<td align='center' style='padding:10px; vertical-align:text-top;'>กล่องที่</td>
						</tr>
						<tr>
							<td align='center' style='font-size: 48px; font-weight:bold; vertical-align:text-top;'>".$number."</td>
						</tr>
				</table>	
			</div>
		</td>
	</tr>
	</table>
	
	<table class='table table-striped' align='center' style='width:100%; table-layout:fixed; margin-top:5px; ' id='order_detail'>
	<tr>
				<td style='width:10%; text-align:center; border:solid 1px #AAA; padding:10px;'>ลำดับ</td>
				<td style='width:70%; border:solid 1px #AAA; text-align:center; padding:10px'>สินค้า</td>
			   <td style='width:20%; text-align:center; border:solid 1px #AAA;  padding:10px'>จำนวน</td> 
	</tr>"; return $result; }
	function page_summary($employee, $total_qty=""){
		echo"	
		<tr style='height:12mm; font-size: 18px;'>
			<td colspan='3' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px; vertical-align:text-top; text-align:right;'>รวม $total_qty หน่วย</td>
		</tr>
		<tr style='height:12mm;'>
			<td rowspan='2' colspan='3' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px; vertical-align:text-top;'>หมายเหตุ :  </td>
		</tr>
		<tr style='height:12mm;'></tr>
		<tr style='height:12mm;'>
			<td colspan='3' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px; vertical-align:text-top; text-align:right;'>ผู้จัดทำ : ".$employee."</td>
		</tr>
		</table>";
	}
	
	if($rs>0){
		echo $html.$doc_body_top.doc_head($order, $company, $customer, $number, $title,$page, $total_page);
	while($i<$rs){
		list($id_product_attribute, $qty)= dbFetchArray($sql);
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product);
		$product->product_attribute_detail($id_product_attribute);
		echo"
		<tr style='height:12mm;  font-size: 16px;'>
			<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA;'>$n</td>
			<td style='vertical-align:middle; padding:3px; border: solid 1px #AAA;'>".$product->reference." : ".$product->product_name."</td>
			<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA;'>".number_format($qty)."</td>
		</tr>";
				$total_qty += $qty;
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
								<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 10px;'>&nbsp;</td>
				</tr>";
						$ba++; $count++;
					}
				}
				$total_all_qty = $total_qty;
				page_summary($employee, $total_all_qty);
				}else{
				if($count>$row){  $page++; echo "</table><div style='page-break-after:always;'></div>".doc_head($order, $company, $customer, $number, $title, $page, $total_page); $count = 1;  }
				}
				$n++; 
	}
	}else{
		echo $html.$doc_body_top.doc_head($order, $company, $customer, $number, $title,$page, $total_page);
		echo"<tr><td colspan='8' align='center' style='border: solid 1px #AAA;'><h3>---- ยังไม่มีรายการสินค้า  ----</h3></td></tr>";
	}
	echo "</div></body></html>";
	 }

if(isset($_GET['update_box']) && isset($_GET['id_order'])  )
{
	$id_order = $_GET['id_order'];
	$id_box 	= $_GET['id_box'];
	$qs = dbQuery("SELECT id_box FROM tbl_box WHERE id_order = ".$id_order);    
	$data = array();
	if(dbNumRows($qs) > 0 ) : 
		$i = 1; 
		while($ro = dbFetchArray($qs)) : 
			$qo = dbQuery("SELECT SUM(qty) AS qty FROM tbl_qc WHERE id_order = ".$id_order." AND id_box = ".$ro['id_box']." AND valid = 1"); 
			$rs = dbFetchArray($qo) ; 
			$qty = $rs['qty'];
			if($qty <1){ $qty = 0 ; }
			$active = "btn-success";
			if($id_box == $ro['id_box']){ $active = "btn-danger"; }
			$arr = array("i"=>$i, "id_box"=>$ro['id_box'], "qty"=>$qty, "class"=>$active);
			array_push($data, $arr);          
            $i++;   
 		endwhile; 
 	else : 
			$arr = array("nocontent"=>"ยังไม่มีการตรวจสินค้า");
 endif; 	
 
 echo json_encode($data);
	
}

if(isset($_GET['get_total']) &&isset($_GET['id_order'] ))
{
	$qty = 0;
	$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_qc WHERE id_order = ".$_GET['id_order']." AND valid = 1 ");
	if(dbNumRows($qs) == 1 )
	{
		list($qty) = dbFetchArray($qs);
	}
	echo $qty;
}

?>