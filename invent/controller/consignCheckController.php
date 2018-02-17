<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
require "../function/consign_helper.php";

if( isset($_GET['cancle_consign_check']) && isset( $_GET['id_consign_check'] ) )
{
	$id = $_GET['id_consign_check'];
	$success = true;
	startTransection();
	$qs = dbQuery("DELETE FROM tbl_consign_box_detail WHERE id_consign_check = ".$id);
	if(!$qs){ $success = false; }
	$qs = dbQuery("DELETE FROM tbl_consign_box WHERE id_consign_check = ".$id);
	if(!$qs){ $success = false; }
	$qs = dbQuery("DELETE FROM tbl_consign_check_detail WHERE id_consign_check = ".$id);
	if(!$qs){ $success = false; }
	$qr = dbQuery("DELETE FROM tbl_consign_check WHERE id_consign_check = ".$id);
	if(!$qs){ $success = false; }
	
	if( $success )
	{
		commitTransection();
		echo "success";
	}
	else
	{
		dbRollback();
		echo "fail";
	}
}


if( isset($_GET['check_diff']) && isset($_GET['id_consign_check']) )
{
	$data = array();
	$qs = dbQuery("SELECT * FROM tbl_consign_check_detail WHERE id_consign_check = ".$_GET['id_consign_check']);
	if(dbNumRows($qs) > 0 )
	{
		$n = 1;
		$total_qty = 0;
		$total_amount = 0;
		while($rs = dbFetchArray($qs))
		{
			$product = new product();
			$product->product_attribute_detail($rs['id_product_attribute']);
			$diff 	= $rs['qty_stock'] - $rs['qty_check'];
			if($diff != 0 )
			{
				$amount = $diff * $product->product_price;
				$arr = array(
							"no" 		=> $n,
							"barcode" 	=> $product->barcode,
							"reference"	=> $product->reference,
							"price"		=> number_format($product->product_price, 2),
							"qty"			=> number_format($diff),
							"amount"		=> number_format($amount,2)
							);
				array_push($data, $arr);
				$total_qty += $diff;
				$total_amount += $amount;						
				$n++;	
			}
		}
		$arr = array("total_qty" => number_format($total_qty), "total_amount" => number_format($total_amount));
		array_push($data, $arr);
	}
	else
	{
		$arr = array("nocontent"=>"nocontent");
		array_push($data, $arr);
	}
	echo json_encode($data);
}

if( isset($_GET['check_balance']) && isset($_GET['id_consign_check']) )
{
	$data = array();
	$qs = dbQuery("SELECT * FROM tbl_consign_check_detail WHERE id_consign_check = ".$_GET['id_consign_check']." AND qty_check != 0");
	if(dbNumRows($qs) > 0 )
	{
		$n = 1;
		$total_qty = 0;
		$total_amount = 0;
		while($rs = dbFetchArray($qs))
		{
			$product = new product();
			$product->product_attribute_detail($rs['id_product_attribute']);
			$qty 	= $rs['qty_check'];
			if( $qty != 0 )
			{
				$amount = $qty * $product->product_price;
				$arr = array(
							"no" 		=> $n,
							"barcode" 	=> $product->barcode,
							"reference"	=> $product->reference,
							"price"		=> number_format($product->product_price, 2),
							"qty"			=> number_format($qty),
							"amount"		=> number_format($amount,2)
							);
				array_push($data, $arr);
				$total_qty += $qty;
				$total_amount += $amount;						
				$n++;	
			}
		}
		$arr = array("total_qty" => number_format($total_qty), "total_amount" => number_format($total_amount));
		array_push($data, $arr);
	}
	else
	{
		$arr = array("nocontent"=>"nocontent");
		array_push($data, $arr);
	}
	echo json_encode($data);
}

if( isset($_GET['check_error']) && isset($_GET['id_consign_check']) )
{
	$data = array();
	$qs = dbQuery("SELECT * FROM tbl_consign_check_error WHERE id_consign_check = ".$_GET['id_consign_check']);
	if(dbNumRows($qs) > 0 )
	{
		$n = 1;
		$total_qty = 0;
		$total_amount = 0;
		while($rs = dbFetchArray($qs))
		{
			$product = new product();
			if($rs['id_product_attribute'] != 0 )
			{
				$product->product_attribute_detail($rs['id_product_attribute']);
				$reference = $product->reference;
			}
			else
			{
				$reference = "";
			}
			$qty 	= $rs['qty'];
			if( $qty != 0 )
			{
				$amount = $qty * $product->product_price;
				$err = consign_check_error_type($rs['error_type']);
				$arr = array(
							"no" 			=> $n,
							"barcode" 	=> $rs['barcode'],
							"reference"	=> $reference,
							"qty"			=> number_format($qty),
							"error"		=> $err,
							"time"			=> thaiDateTime($rs['date_upd']),
							"box"			=> "กล่องที่ ".box_no($rs['id_box'])
							);
				array_push($data, $arr);
				$total_qty += $qty;
				$total_amount += $amount;						
				$n++;	
			}
		}
		$arr = array("total_qty" => number_format($total_qty));
		array_push($data, $arr);
	}
	else
	{
		$arr = array("nocontent"=>"nocontent");
		array_push($data, $arr);
	}
	echo json_encode($data);
}


if( isset( $_GET['remove_checked'] ) && isset( $_POST['id_consign_box_detail'] ) && isset( $_POST['id_consign_check_detail'] ) )
{
	$rs = "fail";
	$qs = dbQuery("SELECT qty FROM tbl_consign_box_detail WHERE id_consign_box_detail = ".$_POST['id_consign_box_detail']);
	if(dbNumRows($qs) == 1 )
	{
		list($qty) = dbFetchArray($qs);
		$qr = dbQuery("UPDATE tbl_consign_check_detail SET qty_check = qty_check - ".$qty." WHERE id_consign_check_detail = ".$_POST['id_consign_check_detail']);
		if( $qr )
		{
			$qa = dbQuery("DELETE FROM tbl_consign_box_detail WHERE id_consign_box_detail = ".$_POST['id_consign_box_detail']);
			if($qa)
			{
				$rs = $qty;
			}
		}
	}
	echo $rs;
}

if( isset( $_GET['get_checked_detail'] ) && isset( $_POST['id_consign_check'] ) && isset( $_POST['id_product_attribute'] ) )
{
	$data = array();
	$arr 	= array("reference"=> $_POST['reference']);
	array_push($data, $arr);
	$qs = dbQuery("SELECT id_consign_box_detail, box_no, qty, date_upd FROM tbl_consign_box_detail JOIN tbl_consign_box ON tbl_consign_box_detail.id_consign_box = tbl_consign_box.id_box WHERE tbl_consign_box_detail.id_consign_check = ".$_POST['id_consign_check']." AND id_product_attribute = ".$_POST['id_product_attribute']." ORDER BY date_upd DESC");
	while($rs = dbFetchArray($qs) )
	{
		$arr = array("id" => $rs['id_consign_box_detail'], "box" => "กล่องที่ ".$rs['box_no'], "qty" => $rs['qty'], "time" => thaiDateTime($rs['date_upd']), "id_check_detail" => $_POST['id_consign_check_detail'], "id_pda" => $_POST['id_product_attribute'] );
		array_push($data, $arr);
	}
	
	echo json_encode($data);
}

if( isset( $_GET['print_box'] ) && isset( $_GET['id_box'] ) && isset( $_GET['id_consign_check'] ) )
{
	$id_box			= $_GET['id_box'];
	$id_c				= $_GET['id_consign_check'];
	//$reference 		= get_consign_check_reference($id_c);
	$box_no 		= box_no($id_box);
	$print 			= new printer();
	echo $print->doc_header();
	$print->add_title("รายการสินค้าในกล่องที่ ".$box_no);
	$detail			= get_consign_box_detail($id_box);
	$total_row 		= dbNumRows($detail);
	if($total_row == 0 )
	{
		echo "<center><h4>-----  ไม่พบรายการใดๆในกล่อง -----</h4></center>";
	}
	$config 			= array("total_row"=>$total_row, "font_size"=>14, "sub_total_row"=>1, "header_rows"=>0, "header"=>false);
	$print->config($config);
	$row 				= $print->row;
	$total_page 		= $print->total_page;
	$total_qty 		= 0;
	
	//**************  กำหนดหัวตาราง  ******************************//
	$thead	= array(
						array("ลำดับ", "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
						array("รหัส", "width:25%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("สินค้า", "width:50%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("จำนวน", "width:20%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px;")
						);
	$print->add_subheader($thead);
	
	//***************************** กำหนด css ของ td *****************************//
	$pattern = array(
							"text-align: center; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;"
							);					
	$print->set_pattern($pattern);	
	
	//*******************************  กำหนดช่องเซ็นของ footer *******************************//
	$footer	= array( 
						array("ผู้จัดทำ", "","วันที่............................."), 
						array("ผู้ตรวจสอบ", "","วันที่.............................")
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
						$id_product 		= $product->getProductId($rs['id_product_attribute']);
						$product_code 	= $product->product_reference($rs['id_product_attribute']);
						$product_name 	= "<input type='text' style='border:0px; width:100%;' value='".$product->product_name($id_product)."' />";
						$data 				= array($n, $product_code, $product_name, number_format($rs['qty']));
						$total_qty 			+= $rs['qty'];
					else :
						$data = array("", "", "", "");
					endif;
					echo $print->print_row($data);
					$n++; $i++;  	
				endwhile;
				echo $print->table_end();
				if($print->current_page == $print->total_page)
				{ 
					$qty = number_format($total_qty);
				}else{ 
					$qty = ""; 
					$amount = ""; 
					$total_discount_amount = "";
					$net_amount = "";
					$remark = ""; 
				}
				$sub_total = array(
						array("<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px; border-bottom-left-radius: 10px; border-left:0px; width:80%; text-align:right;'><strong>จำนวนรวม</strong></td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; border-bottom:0px; border-bottom-right-radius: 10px; text-align:center; '>".$qty."</td>")						
						);
			echo $print->print_sub_total($sub_total);				
			echo $print->content_end();
			echo $print->footer;
		echo $print->page_end();
		$total_page --; $print->current_page++;
	}
	echo $print->doc_footer();
}


/***********************************************************************************************************************************************************/
if( isset( $_GET['print_balance'] ) )
{
	$id = $_GET['id_consign_check'];
	
	$id_consign_check = $_GET['id_consign_check'];
	list($reference,$id_customer,$remark,$date_add) = dbFetchArray(dbQuery("SELECT reference,id_customer,comment,date_add FROM tbl_consign_check WHERE id_consign_check = $id_consign_check"));
	$company = new company();
	$customer = new customer($id_customer);
			$title = "ใบแจ้งรายการคงเหลือจากการฝากขาย";
	$total_qty = ""; /// เก็บยอดสินค้าตอนวนลูป
	$total_all_qty =""; ///วนเสร็จแล้วเอาค่ามาใส่ตัวนี้
	$total_order = ""; ///เก็บยอดรวมเพิ่มทีละรายการเวลาวนลูป
	$total_order_amount = "";///วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$total_discount_order =""; //เก็บยอดเงินส่วนลดตอนวนลูป
	$total_discount_amount = ""; //วนลูปจบเอายอดเงินส่วนลดมาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$net_total =""; //มูลค่าสินค้าหลังหักส่วนลด
	$row = 22;
	$sql = dbQuery("SELECT tbl_consign_check_detail.id_product_attribute, barcode, reference,qty_check FROM tbl_consign_check_detail LEFT JOIN tbl_product_attribute ON tbl_consign_check_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_consign_check = $id_consign_check AND qty_check != 0  ORDER BY barcode DESC");
	$rs = dbNumRows($sql);
	
	$total_page = ceil($rs/$row);
	$page = 1;
	$count = 1;
	$n = 1;
	$i = 0;
	$sumdiff = 0;
	$total = 0;
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
				$doc_body_top = "<body style='padding-top:0px; margin-top:-15px;'><div style='width:180mm; margin-right:auto; margin-left:auto; padding:10px'>
				<div class='hidden-print' style='margin-bottom:0px; margin-top:10px;'>
				<button  class='btn btn-primary pull-right' onClick=\"print();\" type='button' />พิมพ์</button>
				<a href='../index.php?content=consign&consign_balance_check&id_consign_check=$id_consign_check' ><button  class='btn btn-primary pull-right' type='button' style='margin-right:20px;' />ยกเลิก</button></a>
</div> ";
				function doc_head($reference,$company, $customer, $title, $page, $total_page,$date_add){
					$result = "<!--
	<div style='width:100%; height:25mm; margin-right:0.5%;'>
		<table width='100%' border='0px'><tr>
			<td style='width:20%; padding:10px; text-align:center; vertical-align:top;'><img src='../../img/company/logo.png' style='width:100px; padding-right:10px;' /></td>
			<td style='width:40%; padding:10px; vertical-align:text-top;'>
				<h4 style='margin-top:0px; margin-bottom:5px;'>".$company->full_name."</h4>
				<p style='font-size:12px'>".$company->address." &nbsp; ".$company->post_code."</p>
				<p style='font-size:12px'>โทร. ".$company->phone." &nbsp;แฟกซ์. ".$company->fax."</p>
				<p style='font-size:12px'>เลขประจำตัวผู้เสียภาษี ".$company->tax_id."</p></td>
				<td style='vertical-align:text-top; text-align:right; padding-bottom:10px;'><strong>$title</strong><br/> หน้า $page / $total_page</td></tr>
			</table></div>-->
	<h4>$title</h4><p class='pull-right'>หน้า $page / $total_page</p>
	<table align='center' style='width:100%; table-layout:fixed;'>
		<tr><td style='width:50%;'>
			<div style='width:99.5%; height:20mm; margin-right:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:20%; padding:10px; height:5mm; vertical-align:text-top;'>ลูกค้า :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$customer->full_name."</td></tr>
				<!--<tr><td style='width:20%; padding:10px; vertical-align:text-top;'>ที่อยู่ :</td>
				<td style='padding:10px; height:30mm; vertical-align:text-top;'>".$customer->address1." ".$customer->address2." ".$customer->city."<br/>เบอร์โทร ".$customer->phone."</td></tr>-->
				</table>	</div>
				</td>
			<td style='width:50%;'><div style='width:99.5%; height:20mm; margin-left:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>วันที่ :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".showDate($date_add)."</td></tr>
				<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>เลขที่เอกสาร :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>$reference</td></tr>
				<!--<tr><td style='width:40%; padding:10px; height:5mm; vertical-align:text-top;'>เครดิตเทอม :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".$customer->credit_term." วัน</td></tr>-->
				</table>	</div></td></tr>
	</table>
	
	<table class='table table-striped' align='center' style='width:100%; table-layout:fixed; margin-top:5px; ' id='order_detail'>
	<tr>
				<td style='width:10%; text-align:center; border:solid 1px #AAA; padding:10px;'>ลำดับ</td><td style='width:20%; text-align:center; border:solid 1px #AAA;  padding:10px'>บาร์โค้ด</td>
				<td style='width:35%; border:solid 1px #AAA; text-align:center; padding:10px'>สินค้า</td>
			   <td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>ราคา</td><td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>จำนวน</td>
			   <td style='width:15%; text-align:center; border:solid 1px #AAA;  padding:10px'>มูลค่า</td>
	</tr>"; return $result; }
	function footer($total_qty=""){
				$result = "</table>
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
	function page_summary($total_order_amount, $remark, $total_all_qty){
		if($total_order_amount !=""){ $total_order_amount = number_format($total_order_amount,2);}
		echo"	<tr style='height:9mm;'><td colspan='7' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px; vertical-align:text-top; text-align:right;'>รวม $total_all_qty หน่วย</td></tr>
				<tr style='height:9mm;'><td colspan='3' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px; vertical-align:text-top;'>หมายเหตุ : $remark </td>
					<td colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>ราคารวม</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding-left:10px; padding-right:10px;'>".$total_order_amount."</td></tr>
				</table>";
	}
	
	if($rs>0){
		echo $html.$doc_body_top.doc_head($reference,$company, $customer, $title,$page, $total_page,$date_add);
			while($i<$rs){
				list($id_product_attribute,$barcode,$reference1,$qty_check)= dbFetchArray($sql);
				$product = new product();
				$id_product = $product->getProductId($id_product_attribute);
				$product->product_detail($id_product);
				$product->product_attribute_detail($id_product_attribute);
				$product_price = $product->product_price;
				$price = $qty_check * $product_price;
				$sumdiff = $sumdiff + $qty_check;
				$total = $total +$price;
				if($count+1 >$row){  $css_row ="border-bottom: solid 1px #AAA; border-top: 0px;";  }else{ $css_row ="border-top: 0px;";}
				echo"<tr style='height:9mm;'>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$n</td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$barcode</td>
				<td style='vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$reference1 </td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$product_price</td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$qty_check</td>
				<td  style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; border-right: solid 1px #AAA; font-size: 10px;'>".number_format($price,2)."</td></tr>";
				$i++;
				$count++;
				if($n==$rs){ 
					$ba_row = $row - $count -7; 
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
							<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
							<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; border-right: solid 1px #AAA; font-size: 8px;'></td>
							</tr>";
							$ba++; $count++;
						}
					}
					$total_all_qty = $sumdiff;
					$total_order_amount = $total;
					page_summary($total_order_amount, $remark, $total_all_qty);
					echo footer($total_all_qty);
				}else{
					if($count>$row){  $page++; echo "</table><div style='page-break-after:always;'></div>".doc_head($reference,$company, $customer, $title,$page, $total_page,$date_add); $count = 1;  }
				}
				$n++;
			}
		echo "</table>	";
	}
	echo "</div></body></html>";
}
/**********************************************************************************************************************************************************/



if( isset( $_GET['get_box_list'] ) && isset( $_GET['id_consign_check'] ) )
{
	$data = array();
	$qs = dbQuery("SELECT id_box, barcode, id_consign_check, box_no FROM tbl_consign_box WHERE id_consign_check = ".$_GET['id_consign_check']);
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$arr = array("id_box" => $rs['id_box'], "barcode" => $rs['barcode'], "id_consign_check" => $rs['id_consign_check'], "box_no" => $rs['box_no']);
			array_push($data, $arr);	
		}
	}
	else
	{
		$arr = array("nocontent" => "nocontent");
		array_push($data, $arr);	
	}
	echo json_encode($data);
}


if( isset( $_GET['check_item'] ) && isset( $_POST['barcode'] ) )
{
	$barcode 	= trim($_POST['barcode']);
	$id_c			= $_POST['id_consign_check'];
	$id_box		= $_POST['id_box'];
	$qty			= $_POST['qty'];
	$pd			= new product();
	$arr			= $pd->check_barcode($barcode);
	$id_pda		= $arr['id_product_attribute'];
	$qtyx			= $arr['qty'];	
	if($id_pda)
	{
		$qty 	= $qty * $qtyx;
		$rs = insert_consign_check($id_c, $barcode, $id_pda, $qty, $id_box);
		if($rs)
		{
			echo $id_pda." | ".$qty;
		}
		else
		{
			echo "wrong"; /// ไม่มีสินค้านี้ในโซน
		}
	}
	else
	{
		$error_type = 2; /// 1 = ไม่มีสินค้านี้ในโซน  2 = ไม่มีสินค้าในฐานข้อมูล
		insert_consign_check_error($id_c, $error_type, $barcode, 0, $qty, $id_box);
		echo "fail";	
	}
}

if( isset( $_GET['get_box'] ) && isset( $_POST['barcode'] ) )
{
	$id 			= $_POST['id_consign_check'];
	$barcode 	= trim($_POST['barcode']);	
	$id_box		= get_consign_box($barcode, $id);
	if(!$id_box)
	{
		$id_box = add_consign_box($barcode, $id);
	}
	echo $id_box;
}


if( isset( $_GET['add_new'] ) && isset( $_POST['id_customer'] ) && isset( $_POST['id_zone'] ) )
{
	$id_customer 	= $_POST['id_customer'];
	$id_zone 		= $_POST['id_zone'];
	$date_add		= dbDate($_POST['date_add'], true);
	$remark			= $_POST['remark'];
	$reference 		= get_max_role_reference_consign_check("PREFIX_CONSIGN_CHECK");
	$id 				= "fail";
	$qs = dbQuery("INSERT INTO tbl_consign_check (reference, id_customer, id_zone, date_add, comment) VALUES ('".$reference."', ".$id_customer.", ".$id_zone.", '".$date_add."', '".$remark."')");
	if($qs)
	{
		$qr = dbQuery("SELECT id_consign_check FROM tbl_consign_check WHERE reference = '".$reference."'");
		list($id) = dbFetchArray($qr);
		$qr = dbQuery("SELECT * FROM tbl_stock WHERE id_zone = ".$id_zone);
		if(dbNumRows($qr) > 0 )
		{
			while($rs = dbFetchArray($qr) )
			{
				$qa = dbQuery("INSERT INTO tbl_consign_check_detail (id_consign_check, id_product_attribute, qty_stock, qty_check) VALUES ( ".$id.", ".$rs['id_product_attribute'].", ".$rs['qty'].", 0)");
			}
		}
	}
	echo $id;
}

if( isset( $_GET['update'] ) && isset( $_POST['id_consign_check'] ) )
{
	$id 				= $_POST['id_consign_check'];
	$id_customer 	= $_POST['id_customer'];
	$date_add		= dbDate($_POST['date_add'], true);
	$remark			= $_POST['remark'];
	$qs = dbQuery("UPDATE tbl_consign_check SET id_customer = ".$id_customer.", date_add = '".$date_add."', comment = '".$remark."' WHERE id_consign_check = ".$id);
	if($qs)
	{
		echo "success";
	}
	else
	{
		echo "fail";
	}
}

if( isset( $_GET['get_qty_in_box']) && isset( $_GET['id_box']) && isset( $_GET['id_consign_check']) )
{
	$id_box = $_GET['id_box'];
	$id_cc	= $_GET['id_consign_check'];
	$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_consign_box_detail WHERE id_consign_check = ".$id_cc." AND id_consign_box = ".$id_box);
	list($qty) = dbFetchArray($qs);
	if( is_null($qty) ){ $qty = 0; }
	echo number_format($qty);
}

if( isset( $_GET['delete_consign_box'] ) && isset( $_GET['id_consign_box']) && isset( $_GET['id_consign_check']) )
{
	$id_box 	= $_GET['id_consign_box'];
	$id_cc	= $_GET['id_consign_check'];
	$ss = true;
	$qs = dbQuery("SELECT id_product_attribute, SUM(qty) AS qty FROM tbl_consign_box_detail WHERE id_consign_box = ".$id_box." AND id_consign_check = ".$id_cc." GROUP BY id_product_attribute");
	if( dbNumRows($qs) > 0 )
	{
		startTransection();
		while($rs = dbFetchArray($qs))
		{
			$qr = dbQuery("UPDATE tbl_consign_check_detail SET qty_check = (qty_check - ".$rs['qty'].") WHERE id_product_attribute = ".$rs['id_product_attribute']." AND id_consign_check = ".$id_cc);
			if( !$qr){ $ss = false; }
		}
		if( $ss )
		{
			$qr = dbQuery("DELETE FROM tbl_consign_box_detail WHERE id_consign_box = ".$id_box." AND id_consign_check = ".$id_cc);
			if( !$qr){ $ss = false; }
		}
		if($ss)
		{
			commitTransection();
			echo "success";
		}
		else
		{
			dbRollback();
			echo "fail";	
		}			
	}
	else
	{
		echo "no_item";
	}
}


if( isset($_GET['clear_filter'] ) )
{
	setcookie("consign_search_text", "", time()-3600, "/");
	setcookie("consign_filter", "", time()-3600, "/");
	setcookie("from_date", "", time()-3600, "/");
	setcookie("to_date", "", time()-3600, "/");
	echo "success";	
}
?>