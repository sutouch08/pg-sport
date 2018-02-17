<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";


if( isset( $_GET['updateReceivedQty'] ) )
{
	$qr = "SELECT id_po_detail, id_po, id_product_attribute FROM tbl_po_detail";
	$qs = dbQuery($qr);
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchObject($qs) )
		{
			set_time_limit(30);
			$received = getReceived($rs->id_po, $rs->id_product_attribute);
			$qa = dbQuery("UPDATE tbl_po_detail SET received = ".$received." WHERE id_po_detail = ".$rs->id_po_detail);
		}
	}
	echo 'updated';
}

function getReceived($id_po, $id_pa)
{
	$qty = 0;
	$qr = "SELECT SUM(rd.qty) AS qty FROM ";
	$qr .= "tbl_receive_product_detail AS rd JOIN tbl_receive_product r USING(id_receive_product) ";
	$qr .= "WHERE r.id_po = ".$id_po." AND rd.id_product_attribute = ".$id_pa." AND rd.status = 1";
	$qs = dbQuery($qr);
	list( $qty ) = dbFetchArray($qs);
	$qty = is_null( $qty ) ? 0 : $qty;
	return $qty;
}

if( isset( $_GET['get_role_detail'] ) && isset( $_POST['id_po_role'] ) )
{
	$qs = dbQuery("SELECT * FROM tbl_po_role WHERE id_po_role = ".$_POST['id_po_role']);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$data = $rs['id_po_role']." | ".$rs['role_name']." | ".$rs['active']." | ".$rs['is_default'];
		echo $data;
	}
	else
	{
		echo "fail";	
	}
}

if( isset( $_GET['set_default_role'] ) && isset( $_POST['id_po_role'] ) )
{
	startTransection();
	$qs = dbQuery("UPDATE tbl_po_role SET is_default = 1 WHERE id_po_role = ".$_POST['id_po_role']);
	$qr = dbQuery("UPDATE tbl_po_role SET is_default = 0 WHERE is_default = 1 AND id_po_role != ".$_POST['id_po_role']);
	if( $qs && $qr )
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

if( isset( $_GET['is_default'] ) && isset( $_POST['id_po_role'] ) )
{
	$qs = dbQuery("SELECT id_po_role FROM tbl_po_role WHERE id_po_role = ".$_POST['id_po_role']." AND is_default = 1 ");
	echo dbNumRows($qs);	
}

if( isset( $_GET['active_po_role'] ) && isset( $_POST['id_po_role'] ) )
{
	$id = $_POST['id_po_role'];
	$active = $_POST['active'];
	$qs = dbQuery("UPDATE tbl_po_role SET active = ".$active." WHERE id_po_role = ".$id);
	if( $qs ){ echo "success"; }else{ echo "fail"; }	
}

if( isset( $_GET['update_po_role'] ) && isset( $_POST['id_po_role'] ) )
{
	$id 		= $_POST['id_po_role'];
	$name 	= $_POST['role_name'];
	$active	= $_POST['active'];	
	$qs = dbQuery("UPDATE tbl_po_role SET role_name = '".$name."', active = ".$active." WHERE id_po_role = ".$id);
	if( $qs )
	{
		echo "success";
	}
	else
	{
		echo "fail";	
	}
}

if( isset( $_GET['add_po_role'] ) && isset( $_POST['role_name'] ) )
{
	$name = $_POST['role_name'];
	$active = $_POST['active'] == 0 ? 0 : 1;
	list($no) = dbFetchArray(dbQuery("SELECT COUNT(*) FROM tbl_po_role"));
	$default = $no == 0 ? 1 : 0 ;
	$qs = dbQuery("INSERT INTO tbl_po_role (role_name, is_default, active) VALUES ('".$name."', ".$default.", ".$active.")");
	if( $qs )
	{
		$id = dbInsertId();
		$no += 1;
		$data = array("no" => $no, "id" => $id, "role" => $name, "actived" => $active, "default" => $default);
		echo json_encode($data);
	}
	else
	{
		echo "fail";	
	}
}
/////////////// check new reference ////////////
if( isset($_GET['check_new_ref']) && isset($_POST['reference']) )
{
	$reference = $_POST['reference'];
	$id_po		= $_POST['id_po'];
	$po = new po();
	$rs = $po->check_reference($reference, $id_po); //// ถ้า ซ้ำ ส่ง 1 กลับมา ถ้าไม่ซ้ำ ส่ง 0 กลับมา
	echo $rs;
}

////////////////  Cancle close PO ////////////////////
if( isset( $_GET['cancle_close_po'] ) && isset( $_GET['id_po'] ) )
{
	$po = new po();
	$rs = $po->cancle_close($_GET['id_po']);
	if($rs)
	{
		echo "success";
	}
	else
	{
		echo "fail";
	}
}
///////////////  Close PO  /////////////////////////

if( isset( $_GET['close_po'] ) && isset( $_GET['id_po'] ) )
{
	$po = new po();
	$rs = $po->close_po($_GET['id_po']);
	if($rs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}

//*************************  รับรายการสินค้ามาเพื่อหาข้อมูลแล้วส่งกลับไปแสดงผลใน form ยังไม่มีการบันทึกลงฐานข้อมูล  ******************/
if( isset( $_GET['insert_item'] ) )
{
	$id_product	= $_POST['id_product'];
	$p_data		= $_POST['qty'];
	$no			= $_GET['no'];
	$price		= $_POST['cost'];
	$discount	= $_POST['discount'];
	$unit			= $_POST['unit'];
	if($unit == "percent"){ $d_price = $price - ($price * ($discount*0.01)); }else{ $d_price = $price - $discount; }
	$result		= array();
	$po			= new po();
	foreach($p_data as $id => $qty)
	{
		if($qty != "" )
		{
			$no++;
			$arr = array(
						"no"						=> $no,
						"id"							=> $id,
						"code"					=> get_product_reference($id),
						"product_name"			=> get_product_name($id_product),
						"price"					=> $price,
						"qty"						=> $qty,
						"discount"				=> $discount,
						"unit"						=> unit_selected($unit),
						"total_amount"			=> number_format($d_price * $qty,2)
						);
			array_push($result, $arr);
		}
	}
	echo $no." | ".json_encode($result);
}

//*********************************** แก้ไขรายการสั่งซื้อ  *****************************//

		///////////////// ลดข้อมูลทั้งหมดแล้วเพิ่มใหม่  //////////////////
if( isset($_GET['save_edit']) && isset($_GET['id_po']) )
{
	// $_POST['bill_discount']    $_POST['qty'] = array key = id_product_attribute  value = qty
	///  update bill_discount ;
	$id_po		= $_GET['id_po'];
	$qtys			= $_POST['qty'];
	$price		= $_POST['price'];
	$dis_c		= $_POST['discount'];
	$unit			= $_POST['unit'];
	$po	= new po($id_po);
	if( $_POST['bill_discount'] > 0 )
	{ 
		$po->update_bill_discount($_GET['id_po'], $_POST['bill_discount']);
	}
	$c 	= count($qtys);
	$s 	= 0;
	/// Insert item to database;
	foreach( $qtys as $id =>$qty)
	{
		$product = new product();
		$id_product 	= $product->getProductId($id);
		$dis				= discount($unit[$id], $dis_c[$id]);
		$total_discount	= total_discount($dis, $price[$id], $qty);
		$data 			= array(
								"id_po"=>$id_po,
								"id_product"=>$id_product,
								"id_product_attribute"=>$id,
								"price"=>$price[$id],
								"qty"=>$qty,
								"discount_percent"=>$dis['percent'],
								"discount_amount"=>$dis['amount'],
								"total_discount"=> $total_discount,
								"total_amount"=> ($qty * $price[$id]) - $total_discount
								);
		$rd = $po->add_item($data);
		if($rd){ $s++; }
	}
	if($c == $s)
	{
		if($po->status == 0 ){ $po->update_status($id_po, 1); }
		$de = $po->drop_different($id_po, $qtys);	
		echo $de;
	}else{
		echo "fail";	
	}
}


//************************************  บันทึกรายการสั่งซื้อลงฐานข้อมูล  **************************//
if( isset($_GET['save_add']) && isset($_GET['id_po']) )
{
	// $_POST['bill_discount']    $_POST['qty'] = array key = id_product_attribute  value = qty
	///  update bill_discount ;
	$id_po		= $_GET['id_po'];
	$qtys			= $_POST['qty'];
	$price		= $_POST['price'];
	$dis_c		= $_POST['discount'];
	$unit			= $_POST['unit'];
	$po	= new po();
	if( $_POST['bill_discount'] > 0 )
	{ 
		$po->update_bill_discount($_GET['id_po'], $_POST['bill_discount']);
	}
	/// Insert item to database;
	foreach( $qtys as $id =>$qty)
	{
		$product = new product();
		$id_product 	= $product->getProductId($id);
		$dis				= discount($unit[$id], $dis_c[$id]);
		$total_discount	= total_discount($dis, $price[$id], $qty);
		$data 			= array(
								"id_po"=>$id_po,
								"id_product"=>$id_product,
								"id_product_attribute"=>$id,
								"price"=>$price[$id],
								"qty"=>$qty,
								"discount_percent"=>$dis['percent'],
								"discount_amount"=>$dis['amount'],
								"total_discount"=> $total_discount,
								"total_amount"=> ($qty * $price[$id]) - $total_discount
								);
		$rd = $po->add_item($data);
	}
	$po->update_status($id_po, 1);
	echo "success";
}


if( isset( $_GET['get_product'] ) && isset( $_POST['product_code'] ) ) 
{
	$id_product = get_id_product_by_product_code($_POST['product_code']);
	if( $id_product)
	{
		$product = new product();
		$product->product_detail($id_product);
		$config = "size";
		$sqr = dbQuery("SELECT id_$config FROM tbl_product_attribute WHERE id_product = $id_product AND id_$config !=0 GROUP BY id_$config");
		$colums = dbNumRows($sqr);
		$sqm = dbQuery("SELECT id_color, id_size, id_attribute FROM tbl_product_attribute WHERE id_product = $id_product LIMIT 1");
		list($co, $si, $at) = dbFetchArray($sqm);
		if($co !=0){ $co =1;}
		if($si !=0){ $si = 1;}
		if($at !=0){ $at = 1;}
		$count = $co+$si+$at;
		if($count >1){	$table_w = (85*($colums+1)+100); }else if($count ==1){ $table_w = 400; }
		if($table_w < 400){ $table_w = 400; }
		$dataset = $product->request_attribute_grid($id_product);
		$dataset .= "|".$table_w;
		$dataset .= "|".$_POST['product_code'];
		$dataset .= "|".$id_product;
		$dataset .= "|".$product->product_cost;
	}else{
		$dataset = "fail";
	}
	echo $dataset;
}

if( isset($_GET['update_po']) && isset($_POST['id_po']) ) 
{
	$data	= array(
					"reference" 		=> $_POST['reference'],
					"id_supplier"		=>$_POST['s_id'],
					"id_employee"	=>$_POST['id_employee'],
					"due_date"		=>dbDate($_POST['due_date']),
					"date_add"		=>dbDate($_POST['date_add'], true),
					"remark"			=>$_POST['remark'],
					"role"				=>$_POST['role']
					);
	$po 	= new po();
	$rs 	= $po->update($_POST['id_po'], $data);
	if( $rs )
	{
		echo "success";
	}else{
		echo "fail";
	}
}

if( isset($_GET['add_po']) && isset($_POST['s_id']) )
{
	$data = array(
					"reference"		=>$_POST['reference'],
					"id_supplier"		=>$_POST['s_id'],
					"id_employee"	=>$_POST['id_employee'],
					"due_date"		=>dbDate($_POST['due_date']),
					"date_add"		=>dbDate($_POST['date_add'], true),
					"remark"			=>$_POST['remark'],
					"role"				=> $_POST['role']
				);	
	$po = new po();
	$rs = $po->add($data);
	if($rs)
	{
		echo $rs;
	}else{
		echo "fail";
	}
}


if( isset( $_GET['delete_po'] ) && isset( $_GET['id_po'] ) )
{
	$po 	= new po($_GET['id_po']);
	if($po->valid != 1 && $po->status != 2 )
	{
		$rd = $po->drop_all_detail($po->id_po);
		if($rd)
		{
			$rs = $po->delete_po($po->id_po);
			if($rs)
			{
				echo "success";
			}
		}
	}
}

if( isset( $_GET['print_po']) && isset( $_GET['id_po'] ) )
{
	$id_po			= $_GET['id_po'];
	$po 				= new po($id_po);
	$print 			= new printer();
	echo $print->doc_header();
	$print->add_title("PO/ใบสั่งซื้อ");
	$header			= array("เลขที่เอกสาร"=>$po->reference, "วันที่เอกสาร"=>thaiDate($po->date_add), "ผู้ขาย"=>supplier_code($po->id_supplier)." : ".supplier_name($po->id_supplier), "กำหนดรับ"=>thaiDate($po->due_date));
	$print->add_header($header);
	$detail			= $po->get_detail($id_po);
	$total_row 		= dbNumRows($detail);
	$config 			= array("total_row"=>$total_row, "font_size"=>10, "sub_total_row"=>4);
	$print->config($config);
	$row 				= $print->row;
	$total_page 		= $print->total_page;
	$total_qty 		= 0;
	$total_price		= 0;
	$total_amount 	= 0;
	$total_discount = 0;
	$bill_discount	= $po->bill_discount;
	//**************  กำหนดหัวตาราง  ******************************//
	$thead	= array(
						array("ลำดับ", "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
						array("รหัส", "width:15%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("สินค้า", "width:35%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("จำนวน", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("ราคา", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("ส่วนลด", "width:15%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("มูลค่า", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
						);
	$print->add_subheader($thead);
	
	//***************************** กำหนด css ของ td *****************************//
	$pattern = array(
							"text-align: center; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
							"text-align:right; border-left: solid 1px #ccc; border-top:0px;"
							);					
	$print->set_pattern($pattern);	
	
	//*******************************  กำหนดช่องเซ็นของ footer *******************************//
	$footer	= array( 
						array("ผู้จัดทำ", "","วันที่............................."), 
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
						$id_product 		= $product->getProductId($rs['id_product_attribute']);
						$product_code 	= $product->product_reference($rs['id_product_attribute']);
						$product_name 	= "<input type='text' style='border:0px; width:100%;' value='".$product->product_name($id_product)."' />";
						$dis					= $po->getDiscount($rs['discount_percent'], $rs['discount_amount']); // หาส่วนลด
						$discount			= number_format($dis['value'],2)." ".$dis['unit'];
						$data 				= array($n, $product_code, $product_name, number_format($rs['qty']), number_format($rs['price'], 2), $discount, number_format($rs['total_amount'], 2) );
						$total_qty 			+= $rs['qty'];
						$total_price 		+= $rs['qty'] * $rs['price'];
						$total_amount 		+= $rs['total_amount'];
						$total_discount 	+= $rs['total_discount'];
					else :
						$data = array("", "", "", "","", "","");
					endif;
					echo $print->print_row($data);
					$n++; $i++;  	
				endwhile;
				echo $print->table_end();
				if($print->current_page == $print->total_page)
				{ 
					$qty = number_format($total_qty);
					$amount = number_format($total_price,2); 
					$total_discount_amount = number_format($total_discount+$bill_discount,2);
					$net_amount = number_format($total_price - ($total_discount + $bill_discount) ,2);
					$remark = $po->remark;
				}else{ 
					$qty = ""; 
					$amount = ""; 
					$total_discount_amount = "";
					$net_amount = "";
					$remark = ""; 
				}
				$sub_total = array(
						array("<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px; border-left:0px; width:60%; text-align:center;'>**** ส่วนลดท้ายบิล : ".number_format($bill_discount,2)." ****</td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc;'><strong>จำนวนรวม</strong></td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; text-align:right;'>".$qty."</td>"),
						array("<td rowspan='3' style='height:".$print->row_height."mm; border-top: solid 1px #ccc; border-bottom-left-radius:10px; width:55%; font-size:10px;'><strong>หมายเหตุ : </strong>".$remark."</td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc;'><strong>ราคารวม</strong></td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; text-align:right;'>".$amount."</td>"),
						array("<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px;'><strong>ส่วนลดรวม</strong></td>
						<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; border-bottom:0px; border-bottom-right-radius:10px; text-align:right;'>".$total_discount_amount."</td>"),
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

if( isset( $_GET['print_barcode']) && isset( $_GET['id_po'] ) )
{
	$id_po			= $_GET['id_po'];
	$po 				= new po($id_po);
	$print 			= new printer();
	echo $print->doc_header();
	$print->add_title("PO/ใบสั่งซื้อ (ใช้สำหรับยิงเข้า formula เท่านั้น)");
	$header			= array("เลขที่เอกสาร"=>$po->reference, "วันที่เอกสาร"=>thaiDate($po->date_add), "ผู้ขาย"=>supplier_code($po->id_supplier)." : ".supplier_name($po->id_supplier), "กำหนดรับ"=>thaiDate($po->due_date));
	$print->add_header($header);
	$detail			= $po->get_detail($id_po);
	$total_row 		= dbNumRows($detail);
	$config 			= array("total_row"=>$total_row, "font_size"=>10, "sub_total_row"=>4, "footer"=>false);
	$print->config($config);
	$row 				= $print->row;
	$total_page 		= $print->total_page;
	$total_qty 		= 0;
	$total_price		= 0;
	$total_amount 	= 0;
	$total_discount = 0;
	$bill_discount	= $po->bill_discount;
	//**************  กำหนดหัวตาราง  ******************************//
	$thead	= array(
						array("ลำดับ", "width:4%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
						array("บาร์โค้ด", "width:15%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("รหัส", "width:18%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("สินค้า", "width:27%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("จำนวน", "width:8%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("ราคา", "width:8%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("ส่วนลด", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("มูลค่า", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
						);
	$print->add_subheader($thead);
	
	//***************************** กำหนด css ของ td *****************************//
	$pattern = array(
							"text-align: center; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px; text-align:center;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
							"text-align:right; border-left: solid 1px #ccc; border-top:0px;"
							);					
	$print->set_pattern($pattern);	
	
	//*******************************  กำหนดช่องเซ็นของ footer *******************************//
	$footer	= array( 
						array("ผู้จัดทำ", "","วันที่............................."), 
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
						$product->product_attribute_detail($rs['id_product_attribute']);
						$id_product 		= $product->id_product; //$product->getProductId($rs['id_product_attribute']);
						$product_code 	= $product->product_reference($rs['id_product_attribute']);
						$product_name 	= "<input type='text' style='border:0px; width:100%;' value='".$product->product_name($id_product)."' />";
						$barcode			= $print->print_barcode($product->barcode, "width:100%; height:21.8px;");
						$dis					= $po->getDiscount($rs['discount_percent'], $rs['discount_amount']); // หาส่วนลด
						$discount			= number_format($dis['value'],2)." ".$dis['unit'];
						$data 				= array($n, $barcode, $product_code, $product_name, number_format($rs['qty']), number_format($rs['price'], 2), $discount, number_format($rs['total_amount'], 2) );
						$total_qty 			+= $rs['qty'];
						$total_price 		+= $rs['qty'] * $rs['price'];
						$total_amount 		+= $rs['total_amount'];
						$total_discount 	+= $rs['total_discount'];
					else :
						$data = array("", "", "", "","", "","","");
					endif;
					echo $print->print_row($data);
					$n++; $i++;  	
				endwhile;
				echo $print->table_end();
				if($print->current_page == $print->total_page)
				{ 
					$qty = number_format($total_qty);
					$amount = number_format($total_price,2); 
					$total_discount_amount = number_format($total_discount+$bill_discount,2);
					$net_amount = number_format($total_price - ($total_discount + $bill_discount) ,2);
					$remark = $po->remark;
				}else{ 
					$qty = ""; 
					$amount = ""; 
					$total_discount_amount = "";
					$net_amount = "";
					$remark = ""; 
				}
				$sub_total = array(
						array("<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px; border-left:0px; width:60%; text-align:center;'>**** ส่วนลดท้ายบิล : ".number_format($bill_discount,2)." ****</td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc;'><strong>จำนวนรวม</strong></td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; text-align:right;'>".$qty."</td>"),
						array("<td rowspan='3' style='height:".$print->row_height."mm; border-top: solid 1px #ccc; border-bottom-left-radius:10px; width:55%; font-size:10px;'><strong>หมายเหตุ : </strong>".$remark."</td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc;'><strong>ราคารวม</strong></td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; text-align:right;'>".$amount."</td>"),
						array("<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px;'><strong>ส่วนลดรวม</strong></td>
						<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; border-bottom:0px; border-bottom-right-radius:10px; text-align:right;'>".$total_discount_amount."</td>"),
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

if( isset($_GET['get_po_detail']) && isset($_POST['id_po']) )  //// ใช้กับรายงาน
{
	$id_po	= $_POST['id_po'];
	$po 		=	new po($id_po);
	$data 	= array();
	$arr 		= array(
							"reference"  => $po->reference,
							"date_add" 	=> thaiDate($po->date_add),
							"sup_code"	=> supplier_code($po->id_supplier),
							"supplier"		=> supplier_name($po->id_supplier),
							"due_date"	=> thaiDate($po->due_date)
							);
	array_push($data, $arr);							
	$qs	= $po->get_detail($id_po);
	$no	= 1;
	$total_qty = 0; $total_discount = 0; $total_amount = 0; $total_price = 0;
	while( $rs = dbFetchArray($qs) )
	{
		$id 	= $rs['id_product_attribute'];
		$dis	= $po->getDiscount($rs['discount_percent'], $rs['discount_amount']);
		$arr = array(
						"no"						=> $no,
						"product_reference" 	=> get_product_reference($id),
						"product_name"			=> get_product_name($rs['id_product']),
						"qty"						=> number_format($rs['qty']),
						"price"					=> number_format($rs['price'],2),
						"discount"				=> number_format($dis['value'],2),
						"unit"						=> $dis['unit'],
						"total_amount"			=> number_format($rs['total_amount'],2),
						"received"				=> number_format($rs['received'])
						);
		$no++; $total_qty += $rs['qty']; $total_discount += $rs['total_discount'];  $total_amount += $rs['total_amount']; $total_price += $rs['qty']*$rs['price'];
		array_push($data, $arr);
	}
	$arr = array(
					"bill_discount"		=> $po->bill_discount,
					"total_qty"			=> number_format($total_qty),
					"total_price"			=> number_format($total_price, 2),
					"total_discount"		=> number_format($total_discount + $po->bill_discount, 2),
					"net_amount"			=> number_format($total_amount - $po->bill_discount,2)
					);
	array_push($data, $arr);
	echo json_encode($data);
}

function unit_selected($unit)
{
	$rs  = "<option value='percent' ".isSelected("percent", $unit)." >%</option>";
	$rs .= "<option value='amount' ".isSelected("amount", $unit)." >THB</option>";
	return $rs;
}

function discount($unit, $discount)
{
	if( $unit == "percent" )
	{
		$discount_percent = $discount;
		$discount_amount 	= 0.00;
	}else if($unit == "amount"){
		$discount_percent = 0.00;
		$discount_amount 	= $discount;
	}else{
		$discount_percent = 0.00;
		$discount_amount 	= 0.00;
	}
	$dis['percent']	= $discount_percent;
	$dis['amount']	= $discount_amount;
	return $dis;
}

function get_id_product_by_product_code($code)
{
	$id = 0;
	$qs = dbQuery("SELECT id_product FROM tbl_product WHERE product_code = '".$code."'");
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$id = $rs['id_product'];
	}
	return $id;
}

function total_discount(array $dis, $price, $qty)
{
	if($dis['percent'] != 0)
	{
		$discount = (($dis['percent'] * 0.01) * $price) *$qty;
	}else if( $dis['amount'] != 0 ){
		$discount = $qty * $dis['amount'];
	}else{
		$discount = 0.00;
	}
	return $discount;
}

if( isset( $_GET['get_product_info'] ) && isset( $_POST['product_code'] ) )
{
	$code 		= trim($_POST['product_code']);
	$data 		= array();
	$arr 			= array("title" => $code);
	array_push($data, $arr);
	$id_product	= get_id_product_by_product_code($_POST['product_code']);
	if($id_product)
	{
		$from 		= fromDate(date("Y-01-01"));
		$to 			= toDate(date("Y-m-d"));
		$qs			= "SELECT tbl_po.id_po, date_add, reference, name, SUM(qty) AS qty, SUM(received) AS received, tbl_po.valid ";
		$qs 			.= "FROM tbl_po JOIN tbl_supplier ON tbl_po.id_supplier = tbl_supplier.id JOIN tbl_po_detail ON tbl_po.id_po = tbl_po_detail.id_po ";
		$qs 			.= "WHERE id_product = ".$id_product." AND (date_add BETWEEN '".$from."' AND '".$to."') GROUP BY tbl_po.id_po ORDER BY date_add DESC";
		$qr 			= dbQuery($qs);
		if( dbNumRows($qr) > 0 )
		{
			$total_qty = 0;
			$total_received = 0;
			while( $rs = dbFetchArray($qr) )
			{
				$arr = array(
							"id_po"		=> $rs['id_po'],
							"date"			=> thaiDate($rs['date_add'],"/"),
							"po"			=> $rs['reference'],
							"sup"			=> $rs['name'],
							"qty"			=> number_format($rs['qty']),
							"received"	=> number_format($rs['received']),
							"status"		=> $rs['valid'] == 1 ? "<span style='color:green'>ปิดแล้ว</span>" : "<span style='color:red'>ยังไม่ปิด</span>"
							);	
				array_push($data, $arr);		
				$total_qty += $rs['qty'];		
				$total_received += $rs['received'];			
			}
			$arr = array("id_po" => "", "date" =>"", "po" =>"", "qty" => number_format($total_qty), "received" => number_format($total_received), "status" =>"");
			array_push($data, $arr);
		}
		else
		{
			$arr = array("nocontent" => "ไม่พบรายการใดๆ");	
			array_push($data, $arr);
		}
	}
	else
	{
		$arr = array("nocontent" => "ไม่มีรหัสสินค้าที่กำหนด กรุณาตรวจสอบ");
		array_push($data, $arr);	
	}
	echo json_encode($data);
}

/////// แสดงยอดสั่งซื้อรวมภายในเดือน ////
/*if( isset( $_GET['get_monthly_amount'] ) )
{
	$m 	= date("n", strtotime("this month"));  /// ได้ตัวเลขกลับมา 1 - 12 โดยไม่มี 0 นำหน้า
	$data = array();
	$total_amount = 0;
	while($m > 0 )
	{
		if($m < 10){ $n = "0".$m; }else{ $n = $m; }
		$from = fromDate(date("Y-$n-01"));
		$to	= toDate(date("Y-$n-t"));
		$qs 	= dbQuery("SELECT SUM(total_amount) AS amount FROM tbl_po_detail JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po WHERE status IN(1,2) AND (date_add BETWEEN '".$from."' AND '".$to."')");
		list($amount) = dbFetchArray($qs);
		if( is_null($amount) ){ $amount = 0.00; }
		$arr = array("month" => getThaiMonthName($from), "amount" => number_format($amount,2));
		array_push($data, $arr);
		$total_amount += $amount;
		$m--;
	}
	$arr = array("last" => "รวม", "total_amount" => number_format($total_amount, 2));
	array_push($data, $arr);
	echo json_encode($data);
}*/

if( isset( $_GET['get_monthly_amount'] ) )
{
	$m 	= date("n", strtotime("this month"));  /// ได้ตัวเลขกลับมา 1 - 12 โดยไม่มี 0 นำหน้า
	$col_amount = array();
	$total_amount = 0;
	$data = '<table class="table table-striped table-bordered">';
	$data .= '<thead><th style="width:150px; text-align:center;">เดือน</th>';
	$qr 	= dbQuery("SELECT * FROM tbl_po_role WHERE active = 1 ORDER BY id_po_role ASC");
	while($rs = dbFetchArray($qr) )
	{
		$data .= '<th style="width:150px; text-align:center;">'.$rs['role_name'].'</th>';
		$col_amount[$rs['id_po_role']] = 0;
	}
	$data .= '<th style="width:150px; text-align:center;">รวม</th>';
	$data .= '</thead>';
	
	while($m > 0 )
	{
		if($m < 10){ $n = "0".$m; }else{ $n = $m; }
		$from = fromDate(date("Y-$n-01"));
		$to	= toDate(date("Y-$n-t"));
		$data .= '<tr><td>'.getThaiMonthName($from).'</td>';
		$qr 	= dbQuery("SELECT * FROM tbl_po_role WHERE active = 1 ORDER BY id_po_role ASC");
		$sum_amount = 0;
		while($rs = dbFetchArray($qr) )
		{
			$qs 	= dbQuery("SELECT SUM(total_amount) AS amount FROM tbl_po_detail JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po WHERE status IN(1,2) AND (date_add BETWEEN '".$from."' AND '".$to."') AND role = ".$rs['id_po_role']);
			list($amount) = dbFetchArray($qs);
			if( is_null($amount) ){ $amount = 0.00; }
			$data .= '<td align="right">'.number_format($amount,2).'</td>';
			$sum_amount += $amount;
			$col_amount[$rs['id_po_role']] += $amount;
		}
		$data .= '<td align="right">'.number_format($sum_amount, 2).'</td></tr>';
		$total_amount += $sum_amount;
		$m--;
	}
	$data .= '<tr/><td align="right"><strong>รวม</strong></td>';
	$qr 	= dbQuery("SELECT * FROM tbl_po_role WHERE active = 1 ORDER BY id_po_role ASC");
	while( $rs = dbFetchArray($qr) )
	{
		$data .= '<td align="right"><strong>'.number_format($col_amount[$rs['id_po_role']], 2).'</strong></td>';
	}
	$data .= '<td align="right"><strong>'.number_format($total_amount, 2).'</strong></td></tr>';
	$data .= '</table>';
	echo $data;
}

if( isset( $_GET['clear_filter'] ) )
{
	setcookie("po_filter", "", time()-3600, "/");
	setcookie("po_search_text", "", time()-3600, "/");
	setcookie("po_from_date", "", time()-3600, "/");
	setcookie("po_to_date", "", time()-3600, "/");
	echo "success";		
}

?>