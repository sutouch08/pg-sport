<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
require "../function/lend_helper.php";

if( isset( $_GET['delete_order'] ) && isset( $_POST['id_order'] ) )
{
	$id_order 	= $_POST['id_order'];
	$lend 			= new lend();
	$rs 			= $lend->delete_order($id_order);
	if($rs)
	{
		echo "success";
	}
	else
	{
		echo "fail";
	}
}


if( isset( $_GET['return_product'] ) && isset( $_GET['id_order'] ) )
{
	$id_order 	= $_GET['id_order'];
	$lend		= new lend();
	$id_lend 	= get_lend_id_by_order($id_order);
	$id_zone 	= $_POST['id_zone'];
	$id_wh 		= get_warehouse_by_zone($id_zone);
	$reference 	= get_order_reference($id_order);
	$qty			= $_POST['qty'];
	$barcode 	= trim($_POST['barcode']);
	$res 			= "";   //// ไว้เก็บคำตอบสำหรับส่งกลับ
	$id_pa 		= get_id_product_attribute_by_barcode($barcode);  /// ตรวจสอบบาร์โค้ด ได้ค่ากลับคืนมาเป็น id_product_attribute ถ้าไม่มีจะได้ 0 คืนมา  function อยู่ใน  tools
	echo $id_pa;
	if($id_pa)   //// ถ้ามีสินค้านี้อยู่ในฐานข้อมูล
	{
		$id_ld		= 	$lend->isExists($id_lend, $id_pa);  //// ตรวจสอบว่า มีสินค้าที่ยิงคืนมามีอยู่ในเอกสารนี้หรือไม่
		if($id_ld)  /// ถ้ามี
		{
			$is = $lend->isValid($id_ld);  //// ตรวจสอบว่าคืนครบแล้วหรือไม่
			if(!$is) //// ถ้ายังไม่ครบ
			{
				startTransection();
				$re = $lend->return_product($id_lend, $id_pa, $qty); /// ทำการคืนสินค้า
				$ra = update_stock_zone($qty, $id_zone, $id_pa);
				$rb = stock_movement("in", 5, $id_pa, $id_wh, $qty, $reference, date("Y-m-d H:i:s"), $id_zone);
				if($re) /// ถ้าคืนสำเร็จ
				{
					$res = $id_pa;
					$lend->change_lend_detail_valid($id_ld, 0); /////// เปลี่ยนสถานะของรายการ ***  ถ้าพารามิเตอร์ ตัวที่ 2 = 0 จะทำการตรวจสอบยอดคือกับยอดยืมว่าครบหรือไม่ ถ้าครบแล้วจะเปลี่ยน เป็น 1
					$lend->change_lend_status($id_lend, 2); /// // เปลี่ยนสถานะของเอกสาร *** ถ้าพารามิเตอร์ตัวที่ 2 = 2 จะทำการตรวจสอบว่าคืนสินค้าครบทุกรายการแล้วหรือไม่ ถ้าครบแล้วจะเปลี่ยนสถานะเป็น 2 ( close ) ถ้าไม่จะ เป็น 1
					commitTransection();
				}
				else  /// ถ้าคืนไม่สำเร็จ
				{
					dbRollback();
					$res = "fail";
				}
			}
			else if( $qty < 0 ) /// ถ้าคืนครบแล้ว แต่จำนวนที่ส่งเข้ามาติดลบ หมายถึงต้องการเอายอดคืนออก
			{
				$ro = $lend->return_qty($id_order, $id_pa);  /// ดูยอดคืนก่อนว่ามีการคืนมาเท่าไร
				if( $ro >= ($qty * -1))  /// ถ้ายอดที่คืนมา มากกว่ายอดที่จะเอาออก อนุญาติให้เอาออกได้
				{
					startTransection();
					$re = $lend->return_product($id_lend, $id_pa, $qty); /// ทำการคืนสินค้า
					$ra = update_stock_zone($qty, $id_zone, $id_pa);
					$rb = stock_movement("in", 5, $id_pa, $id_wh, $qty, $reference, date("Y-m-d H:i:s"), $id_zone);
					if($re) /// ถ้าคืนสำเร็จ
					{
						$res = $id_pa;
						$lend->change_lend_detail_valid($id_ld, 0); /////// เปลี่ยนสถานะของรายการ ***  ถ้าพารามิเตอร์ ตัวที่ 2 = 0 จะทำการตรวจสอบยอดคือกับยอดยืมว่าครบหรือไม่ ถ้าครบแล้วจะเปลี่ยน เป็น 1
						$lend->change_lend_status($id_lend, 2); /// // เปลี่ยนสถานะของเอกสาร *** ถ้าพารามิเตอร์ตัวที่ 2 = 2 จะทำการตรวจสอบว่าคืนสินค้าครบทุกรายการแล้วหรือไม่ ถ้าครบแล้วจะเปลี่ยนสถานะเป็น 2 ( close ) ถ้าไม่จะ เป็น 1
						commitTransection();
					}
					else  /// ถ้าคืนไม่สำเร็จ
					{
						dbRollback();
						$res = "fail";
					}
				}
				else
				{
					$res = "over_return";
				}
			}
			else
			{
				$res = "returned";
			}
		}
		else  /// ถ้าไม่มีในเอกสาร
		{
			$res = "product_not_in";
		}
	}
	else  /// ถ้าไม่มีสินค้าในฐานข้อมูล
	{
		$res = "no_product";
	}
	echo $res;
}

if( isset( $_GET['change_state'] ) && isset( $_GET['id_order'] ) && isset( $_GET['user_id'] ) )
{
	$id_order	= $_GET['id_order'];
	$id_emp		= $_GET['user_id'];
	$id_state 	= $_GET['order_state'];
	$rs 		= order_state_change($id_order, $id_state, $id_emp);
	if($rs)
	{
		echo "success";
	}
	else
	{
		echo "fail";
	}
}

if( isset( $_GET['save_order'] ) && isset( $_GET['id_order'] ) )
{
	$id_order = $_GET['id_order'];
	$id_lend 		= get_lend_id_by_order($id_order);
	$lend			= new lend();
	startTransection();
	$rs 			= $lend->change_lend_status($id_lend, 1);
	$rd 			= $lend->change_order_status($id_order, 1);
	if($rs && $rd)
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


if( isset( $_GET['delete_row'] ) && isset( $_GET['id_order'] ) && isset( $_GET['id_product_attribute'] ) )
{
	$id_order 	= $_GET['id_order'];
	$id_pa 		= $_GET['id_product_attribute'];	
	$id_lend 		= get_lend_id_by_order($id_order);
	$lend 			= new lend();
	startTransection();
	$rs = $lend->delete_detail($id_order, $id_pa);
	$rd = $lend->delete_lend_detail($id_lend, $id_pa);
	if( $rs && $rd )
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

if( isset($_GET['update_order']) && isset($_GET['id_order']))
{
	$id_order 	= $_GET['id_order'];
	$data 		= array("id_employee" => $_POST['id_employee'], "date_add" => dbDate($_POST['date_add'], true), "remark" => $_POST['remark'], "user_id" => $_POST['user_id']);
	$lend 			= new lend();
	$rs 			= $lend->update_order($id_order, $data);
	if($rs)
	{
		echo "success";
	}
	else
	{
		echo "fail";	
	}
}
//**********  โหลดข้อมูลรายการเพื่ออัพเดตรายการใหม่  *********************//
if( isset( $_GET['get_lend_table'] ) && isset( $_GET['id_order'] ) )
{
	$id_order 	= $_GET['id_order'];
	$edit			= $_GET['edit'];
	$data 		= array();
	$qs 			= dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = ".$id_order);
	$rows 		= dbNumRows($qs);
	if($rows > 0 )
	{
		$total_qty = 0;
		$total_amount = 0;
		$n = 1; 
		while($rs = dbFetchArray($qs) ) : 
			$id = $rs['id_product_attribute']; 
			$product = new product(); 
			$arr = array(
					"id"				=> $id,
					"no"			=> $n,
					"img"			=> $product->get_product_attribute_image($id, 1),
					"barcode"	=> $rs['barcode'],
					"product"		=> $rs['product_reference']." : ".$rs['product_name'],
					"price"		=> number_format($rs['product_price'], 2),
					"qty"			=> number_format($rs['product_qty']),
					"amount"		=> number_format($rs['total_amount'],2),
					"btn"			=> $edit == 1 ? '<button type="button" class="btn btn-sm btn-danger" onclick="delete_row('.$id.')"><i class="fa fa-trash"></i></button>' : ''
					);
			array_push($data, $arr);
			$total_qty 		+= $rs['product_qty'];
			$total_amount 	+= $rs['product_qty'] * $rs['product_price'];
			$n++;
		endwhile;		
		$arr = array( "total_qty" => number_format($total_qty), "total_amount" => number_format($total_amount, 2));
		array_push($data, $arr);
	}
	else
	{
		$arr = array( "total_qty" => 0, "total_amount" => 0);
		array_push($data, $arr);
	}
	
	echo json_encode($data);
}


if( isset( $_GET['add_to_order'] ) && isset( $_GET['id_order'] ) )
{
	$id_order 	= $_GET['id_order'];
	$qtys			= $_POST['qty'];
	$lend 			= new lend();
	$sc 			= 0;
	$fa			= 0;
	$rc			= 0;
	foreach($qtys as $color => $items)
	{
		foreach($items as $id => $qty)
		{
			if($qty != "")
			{
				$rc++;
				$rs = $lend->add_to_order($id_order, $id, $qty);	
				if($rs)
				{
					$sc++;
				}
				else
				{
					$fa++;
				}
			}
		}
	}
	
	if($rc == $sc && $rc != 0)
	{
		echo $sc;
	}
	else
	{	
		echo "เพิ่ม $rc รายการ<br/>สำเร็จ $sc รายการ <br/>ไม่สำเร็จ $fa รายการ";	
	}	
}


if( isset( $_GET['add_new'] ) && isset( $_POST['id_employee'] ) )
{
	$data		= array(
						"reference" 		=> get_max_role_reference("PREFIX_LEND", 3, dbDate($_POST['date_add'])), 
						"id_employee" 	=> $_POST['id_employee'], 
						"comment"		=> $_POST['remark'], 
						"date_add" 		=> dbDate($_POST['date_add'], true)
						);
	$lend		= new lend();
	startTransection();
	$rs 	= $lend->add_order($data);
	if($rs)
	{
		$datax = array("id_order" => $rs, "id_employee" => $_POST['id_employee'], "date_add" => dbDate($_POST['date_add'], true), "id_user"=> $_POST['id_user']);
		$rd = $lend->add_lend($datax);
	}
	else
	{
		$rd = false;
	}
	
	if($rs && $rd)
	{
			commitTransection();
			echo $rs;
	}
	else
	{
		dbRollback();
		echo "fail";
	}
}

if( isset( $_GET['check_order_is_saved'] ) && isset($_GET['id_user']) )
{
	$lend = new lend();
	$rs = $lend->get_order_not_save($_GET['id_user']);
	if($rs)
	{
		header("location: ../index.php?content=lend&add=y&id_order=".$rs."&error=คุณมีเอกสารยืมสินค้าที่ยังไม่ได้บันทึก กรุณาบันทึกเอกสารเก่าก่อนเพิ่มใหม่");
	}
	else
	{
		header("location: ../index.php?content=lend&add=y");	
	}
			
}

if(isset($_GET['getData'])&&isset($_GET['id_product']))
{
	$id_product = $_GET['id_product'];
	$product = new product();
	$product->product_detail($id_product, 0);
	$lend = new lend();
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
	$dataset = $product->order_attribute_grid($id_product); //$lend->lend_attribute_grid($id_product);
	$dataset .= "|".$table_w;
	$dataset .= "|".$product->product_code;
	echo $dataset;
}

if( isset( $_GET['get_lend_detail'] ) && isset( $_GET['id_order'] ) && isset( $_GET['id_product_attribute'] ) )
{
	$id_order = $_GET['id_order'];
	$id_pa = $_GET['id_product_attribute'];
	$id_lend 	= get_lend_id_by_order($id_order);
	$lend 	= new lend();
	$qs 	= dbQuery("SELECT * FROM tbl_order_detail_sold WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa);
	$data = array();
	if(dbNumRows($qs) == 1 )
	{
		$rs 		= dbFetchArray($qs);
		$product = new product();
		$return_qty = $lend->return_qty($id_order, $id_pa);
		$balance		= $rs['sold_qty'] - $return_qty;
		$data = array(
						"id"			=> $rs['id_product_attribute'],
						"img"		=> '<img src="'.$product->get_product_attribute_image($rs['id_product_attribute'], 1) .'" width="35px" height="35px" />',
						"product" => $rs['product_reference']." : ".$rs['product_name'],
						"price"	=> number_format($rs['product_price'], 2),
						"lend_qty"	=> number_format($rs['sold_qty']),
						"return_qty"	=> number_format($return_qty),
						"balance_qty"	=> number_format($balance),
						"amount"			=> number_format($balance * $rs['product_price'], 2)
						);										
	}
	echo json_encode($data);
}

if( isset( $_GET['get_zone'] ) && isset( $_GET['barcode'] ) )
{
	$barcode = trim($_GET['barcode']);
	$zone = "fail";
	$qs = dbQuery("SELECT id_zone, zone_name FROM tbl_zone WHERE barcode_zone = '".$barcode."'");
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$zone = $rs['id_zone']." | ".$rs['zone_name'];
	}
	echo $zone;
}

if( isset( $_GET['print_lend']) && isset( $_GET['id_order'] ) )
{
	$id_order		= $_GET['id_order'];
	$lend 				= new lend();
	$id_lend 			= $lend->get_id_lend_by_order($id_order);
	$lend				= new lend($id_lend);
	$print 			= new printer();
	echo $print->doc_header();		
	$print->add_title("สรุปการยืมสินค้า");
	$header			= array(
									"เลขที่เอกสาร"		=>$lend->reference, 
									"วันที่เอกสาร"		=>thaiDate($lend->date_add), 
									"ผู้ยืม"				=> employee_name($lend->id_employee) , 
									"ผู้ทำรายการ"		=>	employee_name($lend->id_user),
									"วันที่ออกเอกสาร"	=> date("d-m-Y")
									);
	$print->add_header($header);
	$detail			= $lend->get_detail($id_lend);
	$total_row 		= dbNumRows($detail);
	$config 			= array(
									"total_row"			=>$total_row, 
									"font_size"			=>10, 
									"sub_total_row"		=>1
									);
	$print->config($config);
	$row 				= $print->row;
	$total_page 		= $print->total_page;
	$total_qty 		= 0;
	$total_return	= 0;
	$total_balance	= 0;
	$total_amount 	= 0;
	
	//**************  กำหนดหัวตาราง  ******************************//
	$thead	= array(
						array("ลำดับ", "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
						array("สินค้า", "width:45%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("ราคา", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("ยืม", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("คืนแล้ว", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("คงค้าง", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
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
						array("ผู้ยืม", "","วันที่............................."), 
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
						$id_pa 	= $rs['id_product_attribute'];
						$product->product_attribute_detail($id_pa);
						$id_pd 	= $product->id_product;
						$p_detail = $product->product_reference($id_pa).' | '.$product->product_name($id_pd);
						$p_detail = "<input type='text' style='border:0px; width:100%;' value='".$p_detail."' />";
						$price 	= $product->product_price;
						$qty 		= $rs['qty'];
						$return	= $rs['return_qty'];
						$bl_qty	= $qty - $return;
						$bl_amount = $bl_qty * $price;
						$data		= array($n, $p_detail, number_format($price, 2), number_format($qty), number_format($return), number_format($bl_qty), number_format($bl_amount, 2) );
						$total_qty 			+= $qty;
						$total_return 		+= $return;
						$total_balance		+= $bl_qty;
						$total_amount 		+= $bl_amount;
					else :
						$data = array("", "", "", "","", "","");
					endif;
					echo $print->print_row($data);
					$n++; $i++;  	
				endwhile;
				echo $print->table_end();
				if($print->current_page == $print->total_page)
				{ 
					$qty 		= number_format($total_qty);
					$return	= number_format($total_return);
					$balance = number_format($total_balance);
					$amount = number_format($total_amount,2); 
					$remark = get_order_remark($id_order);
				}else{ 
					$qty 		= '';
					$return	= '';
					$balance = '';
					$amount = '';
					$remark = '';
				}
				$sub_total = array(
						array("<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px; border-left:0px; width:60%; text-align:center;'><strong>รวม</strong></td>
								<td style='width:10%; height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px;'>".$qty."</td>
								<td style='width:10%; height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px; border-right:0px; text-align:right;'>".$return."</td>
								<td style='width:10%; height:".$print->row_height."mm; border: solid 1px #ccc;  border-bottom:0px;border-right:0px; text-align:right;'>".$balance."</td>
								<td style='width:10%; height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; border-bottom:0px; border-bottom-right-radius:10px; text-align:right;'>".$amount."</td>")
						);
			echo $print->print_sub_total($sub_total);				
			echo $print->content_end();
			echo $print->footer;
		echo $print->page_end();
		$total_page --; $print->current_page++;
	}
	echo $print->doc_footer();
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