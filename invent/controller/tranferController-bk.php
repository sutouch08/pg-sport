<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
if(isset($_GET['add'])){
	$reference = get_max_role_reference_tranfer("PREFIX_TRANFER");
	$comment = $_POST['comment'];
	$doc_date = dbDate($_POST['doc_date']);
	$warehouse_from = $_POST['warehouse_from'];
	$warehouse_to = $_POST['warehouse_to'];
	$id_employee = $_POST['id_employee'];
	//echo "INSERT INTO tbl_tranfer (reference,warehouse_from,warehouse_to,id_employee,date_add)VALUES('$reference','$warehouse_from','$warehouse_to','$id_employee','$doc_date')";
	dbQuery("INSERT INTO tbl_tranfer (reference,warehouse_from,warehouse_to,id_employee,date_add,comment)VALUES('$reference','$warehouse_from','$warehouse_to','$id_employee','$doc_date','$comment')");
	list($id_tranfer) = dbFetchArray(dbQuery("SELECT id_tranfer FROM tbl_tranfer WHERE reference = '$reference'"));
	header("location: ../index.php?content=tranfer&add=y&id_tranfer=$id_tranfer");
}
if(isset($_GET['check_zone'])){
	$sc 			= '';
	$zone 		= $_GET['zone'];
	$id_tranfer 	= $_GET['id_tranfer'];
	list($warehouse_from) = dbFetchArray(dbQuery("SELECT warehouse_from FROM tbl_tranfer WHERE id_tranfer = '$id_tranfer'"));
	list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_zone WHERE (barcode_zone LIKE '%$zone%' OR zone_name LIKE '%$zone%') AND id_warehouse = '$warehouse_from'"));
	if($id_zone != "")
	{
		$sc .= "ok!$id_zone!<table class='table table-bordered'><thead id='head'><th style='width:5%;'>ลำดับ</th><th style='width:15%;'>บาร์โค้ด</th><th style='width:30%;'>รหัสสินค้า</th><th style='width:10%;'>จำนวน</th></thead>";
		$sql = dbQuery("SELECT id_stock, tbl_stock.id_product_attribute, qty, barcode, reference FROM tbl_stock JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_zone = $id_zone");
		$n = 1;
		if(dbNumRows($sql) > 0 ){
			while( $rs = dbFetchObject($sql) )
			{
				set_time_limit(60);
				$sc .= '<tr id="row' . $rs->id_product_attribute .'">';
				$sc .= 	'<td class="text-center; middle;">' .$n.'</td>';
				$sc .= 	'<td class="text-center; middle;">' . $rs->barcode .'</td>';
				$sc .= 	'<td class="text-center; middle;">' . $rs->reference .'</td>';
				$sc .= 	'<td class="text-center; middle;">' . $rs->qty .'</td>';
				$sc .= '</tr>';
						$n++;
			} //end while
		}
		else
		{
			$sc .="<tr><td colspan='6' align='center'><h3>ไม่มีสินค้าในโซนนี้</h3></td></tr>";
		}
		$sc .= "</table>	";
	}
	else
	{
		$sc .= "fales!ไม่มีโซนนี้";
	}
	echo $sc;
}

if( isset( $_GET['moveout'] ) )
{
	$sc	= '';
	$id_zone 				= $_GET['id_zone'];
	$id_tranfer 				= $_GET['id_tranfer'];
	$barcode_item 		= $_GET['barcode_item'];
	$qty 						= $_GET['qty'];
	$allow_under_zero 	= $_GET['under_zero'];
	$date_upd 				= date("Y-m-d");	
	list($id_product_attribute, $reference) = dbFetchArray(dbQuery("SELECT id_product_attribute, reference FROM tbl_product_attribute WHERE barcode = '$barcode_item'"));
	if($id_product_attribute != "")
	{
		list($id_stock,$qty_stock) = dbFetchArray(dbQuery("SELECT id_stock, qty FROM tbl_stock WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone"));
		if( $id_stock == "" && $allow_under_zero)
		{
			$qs = dbQuery("INSERT INTO tbl_stock (id_zone, id_product_attribute, qty) VALUES ( $id_zone, $id_product_attribute, 0)");
			if($qs)
			{
				$id_stock 	= dbInsertId();
				$qty_stock	= 0;
			}
			else
			{
				$id_stock = "";
			}
		}
		
		if($id_stock != ""){
			if($qty <= "$qty_stock" || $allow_under_zero ){
				list($reference_tranfer, $warehouse_from) = dbFetchArray(dbQuery("SELECT reference,warehouse_from FROM tbl_tranfer WHERE id_tranfer = $id_tranfer"));
				list($id_tranfer_detail,$tranfer_qty) = dbFetchArray(dbQuery("SELECT id_tranfer_detail,tranfer_qty FROM tbl_tranfer_detail WHERE id_tranfer = $id_tranfer AND id_product_attribute = $id_product_attribute"));
				$qty_balance = $qty_stock - $qty;
				dbQuery("UPDATE tbl_stock SET qty = '$qty_balance' WHERE id_stock = $id_stock");
				if($id_tranfer_detail != ""){
					$sumqty = $qty + $tranfer_qty;
					dbQuery("UPDATE tbl_tranfer_detail SET tranfer_qty = '$sumqty' WHERE id_tranfer_detail = $id_tranfer_detail");
				}else{
					dbQuery("INSERT INTO tbl_tranfer_detail (id_tranfer,id_product_attribute,id_zone_from,tranfer_qty)VALUES('$id_tranfer','$id_product_attribute','$id_zone','$qty')");
				}
				stock_movement("out", 2, $id_product_attribute, $warehouse_from, $qty, $reference_tranfer, $date_upd, $id_zone);
				$sc .= 'ok!' . $id_zone . '!<table class="table table-bordered">';
				$sc .= '<thead id="head">';
				$sc .= 	'<th style="width:5%;">ลำดับ</th>';
				$sc .= 	'<th style="width:15%;">บาร์โค้ด</th>';
				$sc .= 	'<th style="width:30%;">รหัสสินค้า</th>';
				$sc .= 	'<th style="width:10%;">จำนวน</th>';
				$sc .= '</thead>';
				$sql = dbQuery("SELECT id_stock, tbl_stock.id_product_attribute, qty, reference, barcode FROM tbl_stock JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_zone = $id_zone");
				$n = 1;
				if( dbNumRows($sql) > 0)
				{
					while( $rs = dbFetchObject($sql) )
					{
						$sc .= '<tr id="row' . $rs->id_product_attribute . '">';
						$sc .= 	'<td class="text-center; middle;">' . $n . '</td>';
						$sc .= 	'<td class="middle;">' . $rs->barcode . '</td>';
						$sc .= 	'<td class="middle;">' . $rs->reference . '</td>';
						$sc .= 	'<td class="text-center; middle;">' . $rs->qty . '</td>';
						$sc .= '</tr>';
						$n++;
					}
				}
				else
				{
					$sc .= "<tr><td colspan='6' align='center'><h3>ไม่มีสินค้าในโซนนี้</h3></td></tr>";
				}
				
				$sc .= "</table>";
				
			}
			else
			{
				$sc .=  "fales!จำนวนสินค้าเกิน";
			}
		}
		else
		{
			
			$sc .= "fales!ไม่มี $reference ในโซนนี้";
		}
	}
	else
	{
		$sc .= "false!ไม่มีบาร์โค้ดสินค้านี้";
	}
	
	echo $sc;
}




if(isset($_GET['item_move'])){
	$id_tranfer = $_GET['id_tranfer'];
	echo "<table class='table table-bordered'><thead id='head'><th style='width:5%;' >ลำดับ</th><th style='width:15%;'>บาร์โค้ด</th><th style='width:30%;'>รหัสสินค้า</th><th style='width:10%;text-align:center;'>ย้ายจาก</th><th style='width:10%;text-align:center;'>ไปที่</th><th style='width:10%;text-align:center;'>จำนวน</th><th  style='width:10%; text-align:center;'>การกระทำ</th></thead>";
				$sql = dbQuery("SELECT id_tranfer_detail,id_product_attribute,id_zone_from,id_zone_to,tranfer_qty,valid FROM tbl_tranfer_detail WHERE id_tranfer = '$id_tranfer' ORDER BY id_product_attribute ASC");
				$row = dbNumRows($sql);
				$n = 1;
				$i = 0;
				if($row>0){
				while($i<$row){
					list($id_tranfer_detail,$id_product_attribute,$id_zone_from,$id_zone_to,$tranfer_qty,$valid)= dbFetchArray($sql);
					$product = new product();
					$product->product_attribute_detail($id_product_attribute);
					$reference = $product->reference;
					$barcode = $product->barcode;
					list($name_zone_from) = dbFetchArray(dbQuery("SELECT zone_name FROM tbl_zone WHERE id_zone = $id_zone_from"));
					if($valid == "0"){
						$name_zone_to = "<button type='button' class='btn btn-link' onclick=\"click_move_in($id_product_attribute,'$reference',$tranfer_qty)\"><span class='glyphicon glyphicon-log-in' style='color:#5cb85c; font-size:16px;'></span></button>";	
					}else{
						list($name_zone_to) = dbFetchArray(dbQuery("SELECT zone_name FROM tbl_zone WHERE id_zone = $id_zone_to"));
					}
					echo"<tr id='row$id_product_attribute'><td style='text-align:center; vertical-align:middle;'>$n</td><td style='vertical-align:middle;'>$barcode</td><td style='vertical-align:middle;'>$reference</td><td style='text-align:center; vertical-align:middle;'>$name_zone_from</td><td style='text-align:center; vertical-align:middle;'>$name_zone_to</td><td style='text-align:center; vertical-align:middle;'>$tranfer_qty</td>
					
			<td align='center'>
					<button class='btn btn-danger btn-xs' onclick='delete_detail($id_tranfer_detail)'>
						<span class='glyphicon glyphicon-trash' style='color: #fff;'></span>
					</button>
			</td></tr>";
					$i++;
					$n++;	}
				}else{
					echo"<tr id='row'><td style='text-align:center; vertical-align:middle;' colspan='7' align='center'><h4>ไม่มีรายการสินค้าที่ย้าย</h4></td></tr>";
				}

}
if(isset($_GET['move_in'])){
	$id_tranfer = $_GET['id_tranfer'];
	$id_product_attribute = $_GET['id_product_attribute'];
	$zone_in = $_GET['zone_in'];
	$qty_in = $_GET['qty_in'];
	$date_upd = date('Y-m-d');
	list($reference_tranfer,$id_warehouse) = dbFetchArray(dbQuery("SELECT reference,warehouse_to FROM tbl_tranfer WHERE id_tranfer = $id_tranfer"));
	list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_zone WHERE (barcode_zone LIKE '%$zone_in%' OR zone_name LIKE '%$zone_in%') AND id_warehouse = $id_warehouse"));
	if($id_zone != ""){
		list($id_stock,$stock_qty) = dbFetchArray(dbQuery("SELECT id_stock,qty FROM tbl_stock WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone"));
		if($id_stock != ""){
			$sumstock = $qty_in + $stock_qty;
			dbQuery("UPDATE tbl_stock SET qty = '$sumstock' WHERE id_stock = $id_stock");
		}else{
			dbQuery("INSERT INTO tbl_stock (id_zone,id_product_attribute,qty)VALUES($id_zone,$id_product_attribute,'$qty_in')");
		}
		dbQuery("UPDATE tbl_tranfer_detail SET id_zone_to = $id_zone , valid = 1 WHERE id_tranfer = $id_tranfer AND id_product_attribute = $id_product_attribute");
		stock_movement("in",'1',$id_product_attribute,$id_warehouse,$qty_in,$reference_tranfer,$date_upd, $id_zone);
		echo "ok!<table class='table table-bordered'><thead id='head'><th style='width:5%;' >ลำดับ</th><th style='width:15%;'>บาร์โค้ด</th><th style='width:30%;'>รหัสสินค้า</th><th style='width:10%;text-align:center;'>ย้ายจาก</th><th style='width:10%;text-align:center;'>ไปที่</th><th style='width:10%;text-align:center;'>จำนวน</th><th  style='width:10%; text-align:center;'>การกระทำ</th></thead>";
				$sql = dbQuery("SELECT id_tranfer_detail,id_product_attribute,id_zone_from,id_zone_to,tranfer_qty,valid FROM tbl_tranfer_detail WHERE id_tranfer = '$id_tranfer' ORDER BY id_product_attribute ASC");
				$row = dbNumRows($sql);
				$n = 1;
				$i = 0;
				if($row>0){
				while($i<$row){
					list($id_tranfer_detail,$id_product_attribute,$id_zone_from,$id_zone_to,$tranfer_qty,$valid)= dbFetchArray($sql);
					$product = new product();
					$product->product_attribute_detail($id_product_attribute);
					$reference = $product->reference;
					$barcode = $product->barcode;
					list($name_zone_from) = dbFetchArray(dbQuery("SELECT zone_name FROM tbl_zone WHERE id_zone = $id_zone_from"));
					if($valid == "0"){
						$name_zone_to = "<button type='button' class='btn btn-link' onclick=\"click_move_in($id_product_attribute,'$reference',$tranfer_qty)\"><span class='glyphicon glyphicon-log-in' style='color:#5cb85c; font-size:16px;'></span></button>";	
					}else{
						list($name_zone_to) = dbFetchArray(dbQuery("SELECT zone_name FROM tbl_zone WHERE id_zone = $id_zone_to"));
					}
					echo"<tr id='row$id_product_attribute'><td style='text-align:center; vertical-align:middle;'>$n</td><td style='vertical-align:middle;'>$barcode</td><td style='vertical-align:middle;'>$reference</td><td style='text-align:center; vertical-align:middle;'>$name_zone_from</td><td style='text-align:center; vertical-align:middle;'>$name_zone_to</td><td style='text-align:center; vertical-align:middle;'>$tranfer_qty</td>
					
			<td align='center'>
					<button class='btn btn-danger btn-xs' onclick='delete_detail($id_tranfer_detail)'>
						<span class='glyphicon glyphicon-trash' style='color: #fff;'></span>
					</button>
			</td></tr>";
					$i++;
					$n++;	}
				}else{
					echo"<tr id='row'><td style='text-align:center; vertical-align:middle;' colspan='7' align='center'><h4>ไม่มีรายการสินค้าที่ย้าย</h4></td></tr>";
				}
	}else{
		echo "fales!ไม่มีโซนนี้";
	}
}
if(isset($_GET['move_in_all'])){
	$zone_in_all = $_GET['zone_in_all'];
	$id_tranfer = $_GET['id_tranfer'];
	$date_upd = date('Y-m-d');
	list($reference_tranfer,$id_warehouse) = dbFetchArray(dbQuery("SELECT reference,warehouse_to FROM tbl_tranfer WHERE id_tranfer = $id_tranfer"));
	list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_zone WHERE (barcode_zone LIKE '%$zone_in_all%' OR zone_name LIKE '%$zone_in_all%') AND id_warehouse = $id_warehouse"));
	if($id_zone != ""){
		$sql = dbQuery("SELECT id_tranfer_detail,id_product_attribute,tranfer_qty FROM tbl_tranfer_detail WHERE id_tranfer = $id_tranfer AND valid = 0");
				$row = dbNumRows($sql);
				$i = 0;
				while($i<$row){
					list($id_tranfer_detail,$id_product_attribute,$qty_in)= dbFetchArray($sql);
					list($id_stock,$stock_qty) = dbFetchArray(dbQuery("SELECT id_stock,qty FROM tbl_stock WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone"));
					if($id_stock != ""){
						$sumstock = $qty_in + $stock_qty;
						dbQuery("UPDATE tbl_stock SET qty = '$sumstock' WHERE id_stock = $id_stock");
					}else{
						dbQuery("INSERT INTO tbl_stock (id_zone,id_product_attribute,qty)VALUES($id_zone,$id_product_attribute,'$qty_in')");
					}
					dbQuery("UPDATE tbl_tranfer_detail SET id_zone_to = $id_zone , valid = 1 WHERE id_tranfer = $id_tranfer AND id_product_attribute = $id_product_attribute");
					stock_movement("in",'1',$id_product_attribute,$id_warehouse,$qty_in,$reference_tranfer,$date_upd, $id_zone);
					$i++;
				}
		echo "ok!<table class='table table-bordered'><thead id='head'><th style='width:5%;' >ลำดับ</th><th style='width:15%;'>บาร์โค้ด</th><th style='width:30%;'>รหัสสินค้า</th><th style='width:10%;text-align:center;'>ย้ายจาก</th><th style='width:10%;text-align:center;'>ไปที่</th><th style='width:10%;text-align:center;'>จำนวน</th><th  style='width:10%; text-align:center;'>การกระทำ</th></thead>";
				$sql = dbQuery("SELECT id_tranfer_detail,id_product_attribute,id_zone_from,id_zone_to,tranfer_qty,valid FROM tbl_tranfer_detail WHERE id_tranfer = '$id_tranfer' ORDER BY id_product_attribute ASC");
				$row = dbNumRows($sql);
				$n = 1;
				$i = 0;
				if($row>0){
				while($i<$row){
					list($id_tranfer_detail,$id_product_attribute,$id_zone_from,$id_zone_to,$tranfer_qty,$valid)= dbFetchArray($sql);
					$product = new product();
					$product->product_attribute_detail($id_product_attribute);
					$reference = $product->reference;
					$barcode = $product->barcode;
					list($name_zone_from) = dbFetchArray(dbQuery("SELECT zone_name FROM tbl_zone WHERE id_zone = $id_zone_from"));
					if($valid == "0"){
						$name_zone_to = "<button type='button' class='btn btn-link' onclick=\"click_move_in($id_product_attribute,'$reference',$tranfer_qty)\"><span class='glyphicon glyphicon-log-in' style='color:#5cb85c; font-size:16px;'></span></button>";	
					}else{
						list($name_zone_to) = dbFetchArray(dbQuery("SELECT zone_name FROM tbl_zone WHERE id_zone = $id_zone_to"));
					}
					echo"<tr id='row$id_product_attribute'><td style='text-align:center; vertical-align:middle;'>$n</td><td style='vertical-align:middle;'>$barcode</td><td style='vertical-align:middle;'>$reference</td><td style='text-align:center; vertical-align:middle;'>$name_zone_from</td><td style='text-align:center; vertical-align:middle;'>$name_zone_to</td><td style='text-align:center; vertical-align:middle;'>$tranfer_qty</td>
					
			<td align='center'>
					<button class='btn btn-danger btn-xs' onclick='delete_detail($id_tranfer_detail)'>
						<span class='glyphicon glyphicon-trash' style='color: #fff;'></span>
					</button>
			</td></tr>";
					$i++;
					$n++;	}
				}else{
					echo"<tr id='row'><td style='text-align:center; vertical-align:middle;' colspan='7' align='center'><h4>ไม่มีรายการสินค้าที่ย้าย</h4></td></tr>";
				}
	}else{
		echo "fales!ไม่มีโซนนี้";
	}
}
if(isset($_GET['moveout_all'])){
	$id_tranfer = $_GET['id_tranfer'];
	$id_zone = $_GET['id_zone'];
	$date_upd = date('Y-m-d');
	list($reference_tranfer,$id_warehouse) = dbFetchArray(dbQuery("SELECT reference,warehouse_from FROM tbl_tranfer WHERE id_tranfer = $id_tranfer"));
	$sql = dbQuery("SELECT id_stock,id_product_attribute,qty FROM tbl_stock WHERE id_zone = $id_zone");
	$row = dbNumRows($sql);
	$i = 0;
	if($row>0){
	while($i<$row){
		list($id_stock,$id_product_attribute,$qty)= dbFetchArray($sql);
		list($id_tranfer_detail,$tranfer_qty) = dbFetchArray(dbQuery("SELECT id_tranfer_detail,tranfer_qty FROM tbl_tranfer_detail WHERE id_tranfer = $id_tranfer AND id_product_attribute = $id_product_attribute"));
				dbQuery("DELETE FROM tbl_stock WHERE id_stock = $id_stock");
				if($id_tranfer_detail != ""){
					$sumqty = $qty + $tranfer_qty;
					dbQuery("UPDATE tbl_tranfer_detail SET tranfer_qty = '$sumqty' WHERE id_tranfer_detail = $id_tranfer_detail");
				}else{
					dbQuery("INSERT INTO tbl_tranfer_detail (id_tranfer,id_product_attribute,id_zone_from,tranfer_qty)VALUES('$id_tranfer','$id_product_attribute','$id_zone','$qty')");
				}
				stock_movement("out", 2, $id_product_attribute, $id_warehouse, $qty, $reference_tranfer, $date_upd, $id_zone);
	$i++;
	}
	echo "ok!<table class='table table-bordered'><thead id='head'><th style='width:5%;'>ลำดับ</th><th style='width:15%;'>บาร์โค้ด</th><th style='width:30%;'>รหัสสินค้า</th><th style='width:10%;'>จำนวน</th></thead>";
	$sql = dbQuery("SELECT id_stock,id_product_attribute,qty FROM tbl_stock WHERE id_zone = $id_zone");
	$row = dbNumRows($sql);
	$n = 1;
	$i = 0;
	if($row>0){
	while($i<$row){
		list($id_stock,$id_product_attribute,$qty)= dbFetchArray($sql);
		$product = new product();
		$product->product_attribute_detail($id_product_attribute);
		$reference = $product->reference;
		$barcode = $product->barcode;
		echo"<tr id='row$id_product_attribute'><td style='text-align:center; vertical-align:middle;'>$n</td><td style='vertical-align:middle;'>$barcode</td><td style='vertical-align:middle;'>$reference</td><td style='text-align:center; vertical-align:middle;'>$qty</td></tr>";
				$i++;
				$n++;
	}
	}else{
		echo"<tr><td colspan='6' align='center'><h3>ไม่มีสินค้าในโซนนี้</h3></td></tr>";
	}
	echo"</table>	";
	}else{
		echo "fales!โซนนี้ไม่มีสินค้า";
	}
}
if(isset($_GET['print'])&&isset($_GET['id_tranfer'])){
	$id_tranfer = $_GET['id_tranfer']; 
	$company = new company();
	$sql = dbQuery("SELECT * FROM tbl_tranfer WHERE id_tranfer = '$id_tranfer'");
	$data = dbFetchArray($sql);
	$reference = $data['reference'];
	$date_add = $data['date_add'];
	$id_employee = $data['id_employee'];
	$warehouse_from = $data['warehouse_from'];
	$warehouse_to = $data['warehouse_to'];
	$employee = new employee($id_employee);
	$employee_name = $employee->full_name;
	$row = 18;
	$sqr = dbQuery("SELECT id_product_attribute, tranfer_qty,id_zone_from,id_zone_to FROM tbl_tranfer_detail WHERE id_tranfer = '$id_tranfer' ORDER BY id_product_attribute ASC");
	$rs = dbNumRows($sqr);
	$count = 1;
	$total_page = ceil($rs/$row);
	$page = 1;
	$total_qty = 0;
	$n = 1;
	$i = 0;
	$html = "	<!DOCTYPE html>
				<html>
				<head>
					<meta charset='utf-8'>
					<meta name='viewport' content='width=device-width, initial-scale=1.0'>
					<link rel='icon' href='../favicon.ico' type='image/x-icon' />
					<title>โอนคลัง</title>
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
				$doc_body_top = "<body style='padding-top:0px; margin-top:-15px;'><div style='width:180mm; margin-right:auto; margin-left:auto; padding-left:10px; padding-right:10px; padding-top:0px;'>
				<div class=\"hidden-print\" style='margin-bottom:0px;'>
				<button  class='btn btn-primary pull-right' onClick=\"print();\" type='button' />พิมพ์</button>
				<a href='../index.php?content=tranfer&add=y&id_tranfer=$id_tranfer' ><button  class='btn btn-primary pull-right' type='button' style='margin-right:20px;' />ยกเลิก</button></a>
</div>";
			/*	$doc_head = "
			<!--<div style='width:100%; height:40mm; margin-right:0.5%;'>
			<table width='100%' border='0px'><tr>
				<td style='width:20%; padding:10px; text-align:center; vertical-align:top;'><img src='../../img/company/logo.png' style='width:100px; padding-right:10px;' /></td>
				<td style='width:40%; padding:10px; vertical-align:text-top;'>
				<h4 style='margin-top:0px; margin-bottom:5px;'>".$company->full_name."</h4>
				<p style='font-size:12px'>".$company->address." &nbsp; ".$company->post_code."</p>
				<p style='font-size:12px'>โทร. ".$company->phone." &nbsp;แฟกซ์. ".$company->fax."</p>
				<p style='font-size:12px'>เลขประจำตัวผู้เสียภาษี ".$company->tax_id."</p></td>
				<td style='vertical-align:text-top; text-align:right; padding-bottom:10px;'><strong>รายการรับสินค้าเข้าคลัง</strong></td></tr>
			</table>
			</div>-->";*/
			function doc_head($date_add, $reference, $employee_name, $page, $total_page,$warehouse_from,$warehouse_to){
				$result ="
		<h4>โอนคลัง</h4><p class='pull-right'>หน้า $page / $total_page</p>
		<table align='center' style='width:100%; table-layout:fixed;'>
		<tr><td style='width:50%;'>
			<div style='width:99.5%; height:30mm; margin-right:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:35%; padding:10px; height:5mm; vertical-align:text-top;'>เลขที่เอกสาร :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'> $reference </td></tr>
				</table>
				<table width='100%'>
				<tr><td style='width:50%; padding:10px; vertical-align:text-top;'>โอนจาก : ".get_warehouse_name_by_id($warehouse_from)."</td><td style='padding:10px; height:30mm; vertical-align:text-top;'>ไป :".get_warehouse_name_by_id($warehouse_to)."  </td></tr>
				</table>	</div>
				</td>
			<td style='width:50%;'><div style='width:99.5%; height:30mm; margin-left:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:35%; padding:10px; height:5mm; vertical-align:text-top;'>วันที่ :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".thaiDate($date_add)."</td></tr>
				<tr><td style='width:35%; padding:10px; height:5mm; vertical-align:text-top;'>พนักงาน :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>$employee_name</td></tr>
				</table>	</div></td></tr>
	</table>
	
		<table class='table table-striped' align='center' style='width:100%; table-layout:fixed; margin-top:5px;' id='order_detail'>
			<tr>
				<td style='width:10%; text-align:center; border:solid 1px #AAA; padding:10px;'>ลำดับ</td>
				<td style='width:15%; text-align:center; border:solid 1px #AAA;  padding:10px'>บาร์โค้ด</td>
				<td style='width:30%; border:solid 1px #AAA; text-align:center; padding:10px'>สินค้า</td>
			   <td style='width:15%; text-align:center; border:solid 1px #AAA;  padding:10px'>จำนวน</td>
			   <td style='width:15%; text-align:center; border:solid 1px #AAA;  padding:10px'>ย้ายจากโซน</td>
			   <td style='width:15%; text-align:center; border:solid 1px #AAA;  padding:10px'>ไปที่โซน</td>
			</tr>"; return $result; }
			function footer($total_qty=""){
				$result = "<tr style='height:9mm;'><td colspan='6' style='text-align:right; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 14px;'>รวม $total_qty หน่วย</td></tr></table>
				<div style='page-break-after:always'>
				<table style='width:100%; border:0px;'>
				<tr><td>	<div class='col-lg-12' style='text-align:center;'>ผู้รับของ</div></td>
					<td><div class='col-lg-12' style='text-align:center;'>ผู้ส่งของ</div></td>
					<td><div class='col-lg-12' style='text-align:center;'>ผู้ตรวจสอบ</div></td>
					<td><div class='col-lg-12' style='text-align:center;'>ผู้อนุมัติ</div></td>
				</tr>
				<tr><td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>ได้รับสินค้าถูกต้องแล้ว</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div></td>
					<td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>&nbsp;</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div></td>
					<td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>&nbsp;</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div></td>
					<td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>&nbsp;</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div>
				</td></tr></table></div>
				"; return $result; }
	
	if($rs>0){
		echo $html.$doc_body_top.doc_head($date_add, $reference, $employee_name, $page, $total_page,$warehouse_from,$warehouse_to);
	while($i<$rs){
		list($id_product_attribute, $tranfer_qty,$id_zone_from,$id_zone_to)= dbFetchArray($sqr);
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product);
		$product->product_attribute_detail($id_product_attribute);	
		if($count+1 >$row){  $css_row ="border-bottom: solid 1px #AAA; border-top: 0px;";  }else{ $css_row ="border-top: 0px;";}
		echo"<tr style='height:9mm;'>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>$n</td>
				<td style='vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>".$product->barcode."</td>
				<td style='vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>".$product->reference." : ".$product->product_name."</td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'> $tranfer_qty </td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>".get_zone($id_zone_from)."</td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; border-right: solid 1px #AAA; font-size: 8px;'>".get_zone($id_zone_to)."</td>
				</tr>";
				$total_qty = $total_qty+$tranfer_qty;//get_warehouse_name_by_id("")get_zone($id_zone)
				$i++; $count++;
				if($n==$rs){ 
				$ba_row = $row - $count; 
				$ba = 0;
				if($ba_row >0){
					while($ba <= $ba_row){
						if($count+1 >$row){  $css_ba_row ="border-bottom: solid 1px #AAA; border-top: 0px;";  }else{ $css_ba_row ="border-top: 0px;";}
						echo"<tr style='height:9mm;'>
						<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
						<td style='vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
						<td style='vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
						<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
						<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
						<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; border-right: solid 1px #AAA; font-size: 8px;'></td>
						</tr>";
						$ba++; $count++;
					}
				}
				echo footer($total_qty);
				
				}else{
				if($count>$row){  $page++; echo footer().doc_head($date_add, $reference, $employee_name, $page, $total_page,$warehouse_from,$warehouse_to); $count = 1; }
				}
				$n++; 
	}
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr></table>";
	}
	echo "</div></body></html>";	
}
if(isset($_GET['delete'])){
	$id_tranfer = $_GET['id_tranfer'];
	$id_tranfer_detail = $_GET['id_tranfer_detail'];
	list($reference) = dbFetchArray(dbQuery("SELECT reference FROM tbl_tranfer WHERE id_tranfer = $id_tranfer"));
	list($id_product_attribute,$id_zone_from,$id_zone_to,$tranfer_qty,$valid) = dbFetchArray(dbQuery("SELECT id_product_attribute,id_zone_from,id_zone_to,tranfer_qty,valid FROM tbl_tranfer_detail WHERE id_tranfer_detail = $id_tranfer_detail"));
	list($id_stock, $qty) = dbFetchArray(dbQuery("SELECT id_stock, qty FROM tbl_stock WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone_from"));
	list($id, $qty_stock) = dbFetchArray(dbQuery("SELECT id_stock, qty FROM tbl_stock WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone_to"));
	$qty_balance = $qty_stock - $tranfer_qty;
	if($tranfer_qty <= "$qty_stock" || !$valid)
	{
		if($id_stock != ""){
			$sumqty = $qty + $tranfer_qty;
			dbQuery("UPDATE tbl_stock SET qty = '$sumqty' WHERE id_stock = $id_stock");
			
		}else{
			dbQuery("INSERT INTO tbl_stock(id_zone,id_product_attribute,qty)VALUES($id_zone_from,$id_product_attribute,'$tranfer_qty')");
		}
		if($valid == 1)
		{	
			dbQuery("UPDATE tbl_stock SET qty = '$qty_balance' WHERE id_stock = $id");
			dbQuery("DELETE FROM tbl_stock WHERE id_stock = $id AND qty < 1");
		}
		dbQuery("DELETE FROM tbl_stock_movement WHERE reference = '$reference' AND id_product_attribute = '$id_product_attribute'");
		dbQuery("DELETE FROM tbl_tranfer_detail WHERE id_tranfer_detail = $id_tranfer_detail");
		echo "ok!<table class='table table-bordered'><thead id='head'><th style='width:5%;' >ลำดับ</th><th style='width:15%;'>บาร์โค้ด</th><th style='width:30%;'>รหัสสินค้า</th><th style='width:10%;text-align:center;'>ย้ายจาก</th><th style='width:10%;text-align:center;'>ไปที่</th><th style='width:10%;text-align:center;'>จำนวน</th><th  style='width:10%; text-align:center;'>การกระทำ</th></thead>";
				$sql = dbQuery("SELECT id_tranfer_detail,id_product_attribute,id_zone_from,id_zone_to,tranfer_qty,valid FROM tbl_tranfer_detail WHERE id_tranfer = '$id_tranfer' ORDER BY id_product_attribute ASC");
				$row = dbNumRows($sql);
				$n = 1;
				$i = 0;
				if($row>0)
				{
					while($i<$row)
					{
						list($id_tranfer_detail,$id_product_attribute,$id_zone_from,$id_zone_to,$tranfer_qty,$valid)= dbFetchArray($sql);
						$product = new product();
						$product->product_attribute_detail($id_product_attribute);
						$reference = $product->reference;
						$barcode = $product->barcode;
						list($name_zone_from) = dbFetchArray(dbQuery("SELECT zone_name FROM tbl_zone WHERE id_zone = $id_zone_from"));
						if($valid == "0")
						{
							$name_zone_to = "<button type='button' class='btn btn-link' onclick=\"click_move_in($id_product_attribute,'$reference',$tranfer_qty)\"><span class='glyphicon glyphicon-log-in' style='color:#5cb85c; font-size:16px;'></span></button>";	
						}
						else
						{
							list($name_zone_to) = dbFetchArray(dbQuery("SELECT zone_name FROM tbl_zone WHERE id_zone = $id_zone_to"));
						}
						echo"<tr id='row$id_product_attribute'><td style='text-align:center; vertical-align:middle;'>$n</td><td style='vertical-align:middle;'>$barcode</td><td style='vertical-align:middle;'>$reference</td><td style='text-align:center; vertical-align:middle;'>$name_zone_from</td><td style='text-align:center; vertical-align:middle;'>$name_zone_to</td><td style='text-align:center; vertical-align:middle;'>$tranfer_qty</td>
						
				<td align='center'>
						<button class='btn btn-danger btn-xs' onclick='delete_detail($id_tranfer_detail)'>
							<span class='glyphicon glyphicon-trash' style='color: #fff;'></span>
						</button>
				</td></tr>";
						$i++;
						$n++;	
						}
				}
				else
				{
					echo"<tr id='row'><td style='text-align:center; vertical-align:middle;' colspan='7' align='center'><h4>ไม่มีรายการสินค้าที่ย้าย</h4></td></tr>";
				}
		}
		else
		{
			echo "fales!สินค้าตัวนี้มีการเคลื่อนไหวแล้วไม่สามารถลบได้";
		}
}
if(isset($_GET['delete_tranfer'])){
	$id_tranfer = $_GET['id_tranfer'];
	list($reference) = dbFetchArray(dbQuery("SELECT reference FROM tbl_tranfer WHERE id_tranfer = $id_tranfer"));
	$sql = dbQuery("SELECT id_tranfer_detail,id_product_attribute,id_zone_from,id_zone_to,tranfer_qty,valid FROM tbl_tranfer_detail WHERE id_tranfer = $id_tranfer");
				$row = dbNumRows($sql);
				$i = 0;
				$n = 0;
				while($i<$row){
					list($id_tranfer_detail,$id_product_attribute,$id_zone_from,$id_zone_to,$tranfer_qty,$valid)= dbFetchArray($sql);
						list($id_stock,$qty) = dbFetchArray(dbQuery("SELECT id_stock,qty FROM tbl_stock WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone_from"));
							list($id,$qty_stock) = dbFetchArray(dbQuery("SELECT id_stock,qty FROM tbl_stock WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone_to"));
							$qty_balance = $qty_stock - $tranfer_qty;
							if($tranfer_qty <= "$qty_stock"){
								if($id_stock != ""){
									$sumqty = $qty + $tranfer_qty;
									dbQuery("UPDATE tbl_stock SET qty = '$sumqty' WHERE id_stock = $id_stock");
									
								}else{
									dbQuery("INSERT INTO tbl_stock(id_zone,id_product_attribute,qty)VALUES($id_zone_from,$id_product_attribute,'$tranfer_qty')");
								}
								if($valid == 1){
									dbQuery("UPDATE tbl_stock SET qty = '$qty_balance' WHERE id_stock = $id");
										dbQuery("DELETE FROM tbl_stock WHERE id_stock = $id AND qty < 1");
								}
							dbQuery("DELETE FROM tbl_stock_movement WHERE reference = '$reference' AND id_product_attribute = '$id_product_attribute'");
							dbQuery("DELETE FROM tbl_tranfer_detail WHERE id_tranfer_detail = $id_tranfer_detail");
							}else{
								$n++;
							}
					$i++;
				}
				if($n > 0){
					echo "fales!มีสินค้าบางรายการไม่สามารลบได้เพราะมีการเคลื่อนไหวแล้ว";
				}else{
					dbQuery("DELETE FROM tbl_tranfer WHERE id_tranfer = $id_tranfer");
					echo "ok";
				}
}
if(isset($_GET['autozone'])){
	$zone_name = $_GET['zone_name'];
	$qstring = "SELECT id_zone AS id, zone_name FROM tbl_zone WHERE zone_name LIKE '%$zone_name%'";
	$result = dbQuery($qstring);//query the database for entries containing the term
if ($result->num_rows>0)
	{
		$data= array();
	while($row = $result->fetch_array())//loop through the retrieved values
		{
				$data[]=$row['zone_name'];
		}
		echo json_encode($data);//format the array into json data
	}else {
		echo "error";
	}

}
?>