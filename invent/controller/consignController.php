<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
require "../function/consign_helper.php";

//////////////////////  Save Edit Discount  //////////////////////////
if( isset( $_GET['saveEditDiscount'] ) && isset( $_GET['id'] ) && isset( $_GET['apv'] ) )
{
	$id 		= $_GET['id'];   // id_order_consign
	$apv		= $_GET['apv'];  // id_employee of approver
	$id_emp 	= getCookie('user_id'); /// id_employee of current user
	$cs 		= new consign($id);
	$prices 	= $_POST['productPrice'];
	$p_disx 	= $_POST['p_dis'];
	$a_disx 	= $_POST['a_dis'];
	
	$sc 		= true;
	startTransection();
	foreach($prices as $id_cd => $price)
	{
		$p_dis = $p_disx[$id_cd];
		$a_dis = $a_disx[$id_cd];
		$ds = $cs->getConsignItemArray($id_cd);
		$rs = $cs->updatePriceAndDiscount($id_cd, $price, $p_dis, $a_dis);
		$rd = $cs->createDiscountLogs($ds, $id_emp, $apv, $price, $p_dis, $a_dis); 
		if( !$rs || !$rd ){ $sc = false; }
	}
	if( $sc )
	{
		commitTransection();
		echo 'success';
	}
	else
	{
		dbRollback();
		echo 'fail';	
	}
}

///////////////////// Valid Discount Permission /////////////////////////
if( isset( $_GET['validEditDiscountPermission'] ) && isset( $_POST['password'] ) )
{
	$id_emp = 0;
	$password = $_POST['password'];
	$qs = dbQuery("SELECT id_employee FROM tbl_employee JOIN tbl_access ON tbl_employee.id_profile = tbl_access.id_profile WHERE id_tab = '35' AND s_key = '".$password."'");	
	if( dbNumRows($qs) == 1 )
	{
		list( $id_emp ) = dbFetchArray($qs);
	}
	echo $id_emp;
}

////////////////////  เปิดบิล ตัดสต็อก บันทึก movement และ บันทึกยอดขาย ///////////////
if( isset( $_GET['saveConsign'] ) && isset( $_POST['id_order_consign'] ) )
{
	$id				= $_POST['id_order_consign'];
	$consign 	= new consign($id);
	$ref			= $consign->reference;
	$id_cus		= $consign->id_customer;
	$id_emp		= getCookie('user_id');
	$id_sale		= getIdSaleByCustomer($id_cus);
	$id_zone		= $consign->id_zone;
	$date_upd	= $consign->date_add;
	$product		= new product();
	$qs			= $consign->getConsignItems($id);
	$sc			= true;

	startTransection();

	// Update status to Saved
	$consign->changeStatus($id, 1);

	while( $rs = dbFetchArray($qs) )
	{
		set_time_limit(150);
		$id_pa 	= $rs['id_product_attribute'];
		$id_pd	= $product->get_id_product($id_pa);
		$usz		= update_stock_zone($rs['qty'] *-1, $id_zone, $id_pa);
		$sm 		= stock_movement('out', 3, $id_pa, 2, $rs['qty'], $ref, $date_upd, $id_zone);
		$data		= array(
						'reference'		=> $ref,
						'id_cus'			=> $id_cus,
						'id_emp'			=> $id_emp,
						'id_sale'			=> $id_sale,
						'id_pd'				=> $id_pd,
						'id_pa'				=> $id_pa,
						'p_name'			=> $product->product_name($id_pd),
						'p_reference'	=> $product->product_reference($id_pa),
						'barcode'			=> get_barcode($id_pa),
						'price'				=> $rs['product_price'],
						'cost'				=> $product->get_product_cost($id_pa),
						'order_qty'		=> $rs['qty'],
						'sold_qty'		=> $rs['qty'],
						'red_percent'	=> $rs['reduction_percent'],
						'red_amount'	=> $rs['reduction_amount'],
						'date_upd'		=> $date_upd
				);
		$rd	= $consign->consignSold($data);
		if( !$rd || !$usz || !$sm )
		{
			$sc	= false;
		}
	}

	if( $sc )
	{
		commitTransection();
		echo 'success';
	}
	else
	{
		dbRollback();
		echo 'fail';
	}

}

////////////////////  ยกเลิกการเปิดบิล คืนสต็อกเข้าระบบ ลบ movement และ ยอดขาย //////////////
if( isset( $_GET['rollBackConsign'] ) && isset( $_POST['id_order_consign'] ) )
{
	$id 			= $_POST['id_order_consign'];
	$consign 	= new consign($id);
	$sc 			= true;
	// ตรวจสอบว่าสถานะเปิดบิลแล้วหรือไม่  ถ้าไม่ไม่ต้องทำอะไร
	if( $consign->consign_status == 1 )
	{
		$qs	= $consign->getConsignSold($consign->reference);
		startTransection();
		while( $rs = dbFetchArray($qs) )
		{
			$id_pa 	= $rs['id_product_attribute'];
			$qty 		= $rs['sold_qty'];
			$usz		= update_stock_zone($qty, $consign->id_zone, $id_pa);
			$sm		= $consign->dropItemMovement($consign->reference, $id_pa);
			$ods		= $consign->dropItemSold($consign->reference, $id_pa);

			if( !$usz || !$sm || !$ods ){ $sc = false; }

		}

		if( !$consign->changeStatus($id, 0))
		{
			$sc = false;
		}
		if( $sc )
		{
			commitTransection();
			echo 'success';
		}
		else
		{
			dbRollback();
			echo 'fail';
		}
	}
}


////////////////////  ลบสินค้าในรายการ  //////////////////////////////////////////
if( isset( $_GET['deleteConsignDetail'] ) )
{
	$id_cd 		= $_POST['id_cd'];
	$consign 	= new consign();
	$rs 			= $consign->deleteConsignDetail($id_cd);
	if( $rs )
	{
		echo 'success';
	}
	else
	{
		echo 'fail';
	}
}

if( isset( $_GET['cancleConsign'] ) )
{
	$id_cd = $_POST['id_order_consign'];
	$consign = new consign($id_cd);
	$rs		= $consign->changeStatus($id_cd, 2);	
	if( $consign->id_consign_check != 0 )
	{
		$ck = new consignCheck();
		$rd = $ck->changeStatus($consign->id_consign_check, 0);	
	}
	if( $rs )
	{
		echo 'success';
	}
	else
	{
		echo 'fail';
	}
}


///////////////////////  เพิ่มสินค้าในรายการ  /////////////////////////
if( isset( $_GET['insertConsignDetail'] ) )
{
	$id				= $_POST['id_order_consign'];
	$id_pa 		= $_POST['id_pa'];
	$qty 			= $_POST['qty'];
	$price		= $_POST['price'] == '' ? get_product_price($id_pa) : $_POST['price'];
	$p_dis 		= $_POST['p_dis'] == '' ? 0.00 : $_POST['p_dis'];
	$a_dis		= $_POST['a_dis'] == '' ? 0.00 : $_POST['a_dis'];
	$prefix		= "";
	$data 		= array(
						'id'			=> $id,
						'id_pa'	=> $id_pa,
						'price'		=> $price,
						'p_dis'	=> $p_dis,
						'a_dis'	=> $a_dis,
						'qty'		=> $qty
			);

	$consign 	= new consign();
	$isExists		= $consign->isExactlyExists($id, $id_pa, $price, $p_dis, $a_dis);
	if( $isExists )
	{
		$rs 	= $consign->updateConsignDetail($isExists, $data);
	}
	else
	{
		$rs 	= $consign->insertConsignDetail($data);
	}
	if( $rs && $isExists )
	{
		$id_cd 	= $isExists;
		$qr 		= dbQuery("SELECT * FROM tbl_order_consign_detail WHERE id_order_consign_detail = ".$id_cd);
		$rd = dbFetchArray($qr);
		$res = array(
						'id'				=> $id_cd,
						'no'			=> 0,
						'barcode'	=> get_barcode($rd['id_product_attribute']),
						'item_code'	=> get_product_reference($rd['id_product_attribute']),
						'price'			=> number_format($rd['product_price'], 2),
						'p_dis'		=> number_format($rd['reduction_percent'], 2),
						'a_dis'		=> number_format($rd['reduction_amount'], 2),
						'qty'			=> number_format($rd['qty']),
						'amount'		=> number_format(getConsignSumAmount($rd['qty'], $rd['product_price'], $rd['reduction_percent'], $rd['reduction_amount']), 2)
			);
		$prefix 	= $id_cd.' | ';
		echo $prefix. json_encode($res);
	}
	else if( $rs && !$isExists )
	{
		$id_cd 	= dbInsertId();
		$qr 		= dbQuery("SELECT * FROM tbl_order_consign_detail WHERE id_order_consign_detail = ".$id_cd);
		$rd = dbFetchArray($qr);
		$res = array(
						'id'				=> $id_cd,
						'no'			=> 0,
						'barcode'	=> get_barcode($rd['id_product_attribute']),
						'item_code'	=> get_product_reference($rd['id_product_attribute']),
						'price'			=> number_format($rd['product_price'], 2),
						'p_dis'		=> number_format($rd['reduction_percent'], 2),
						'a_dis'		=> number_format($rd['reduction_amount'], 2),
						'qty'			=> number_format($rd['qty']),
						'amount'		=> number_format(getConsignSumAmount($rd['qty'], $rd['product_price'], $rd['reduction_percent'], $rd['reduction_amount']), 2)
			);
		$prefix 	= 'add | ';
		echo $prefix. json_encode($res);
	}
	else
	{
		echo 'fail';
	}
}
//////////////////////////  เพิ่มออเดอร์ใหม่  /////////////////////////////
if( isset( $_GET['new_consign'] ) )
{
	$id_customer 	= $_POST['id_customer'];
	$id_zone			= $_POST['id_zone'];
	$remark			= $_POST['remark'];
	$date_add		= dbDate($_POST['date_add'], true);
	$data				= array(
									"reference" 			=> newConsignReference("PREFIX_CONSIGN", $date_add),
									"id_customer" 		=> $id_customer,
									"date_add" 			=> $date_add,
									"comment" 			=> $remark,
									"consign_status"	=> 0,
									"id_zone"				=> $id_zone,
									"id_consign_check" => 0,
									"id_employee"		=> getCookie('user_id')
									);
	$consign			= new consign();
	$rs				= $consign->addNewConsign($data);
	if($rs)
	{
		echo $rs;
	}
	else
	{
		echo "fail";
	}
}


if( isset( $_GET['update_consign'] ) )
{
	$id				= $_POST['id_order_consign'];
	$consign 	= new consign($id);
	$date_add	= thaiDate($consign->date_add);
	$rs			= false;
	if( $consign->consign_status == 0 )
	{
		if( $date_add != $_POST['date_add'] ){
			$data	= array(
								"id_customer" 	=> $_POST['id_customer'],
								"date_add" 		=> dbDate($_POST['date_add'], true),
								"comment" 		=> $_POST['remark'],
								"id_zone" 		=> $_POST['id_zone'],
								"id_employee" 	=> getCookie('user_id')
							);
		}else{
			$data	= array(
								"id_customer" 	=> $_POST['id_customer'],
								"comment" 		=> $_POST['remark'],
								"id_zone" 		=> $_POST['id_zone'],
								"id_employee" 	=> getCookie('user_id')
							);
		}
		$rs		= $consign->updateConsign($id, $data);
	}
	if( $rs )
	{
		echo "success";
	}
	else
	{
		echo 'fail';
	}
}

//////////////////// ลบเอกสารตัดยอดฝากขาย  ////////////////////////

if( isset( $_GET['deleteConsign'] ) && isset( $_POST['id_order_consign'] ) )
{
	$id = $_POST['id_order_consign'];
	$cs = new consign($id);
	if( $cs->consign_status == 1 )
	{
		echo $cs->consign_status;
	}
	else
	{
		startTransection();
		if( $cs->id_consign_check != 0 )
		{
			$ck = new consignCheck();
			$re = $ck->changeStatus($cs->id_consign_check, 0);	
		}
		else
		{
			$re = true;	
		}
		$rs = $cs->dropConsignDetail($id);
		$rd = $cs->dropConsign($id);
		

		if( $rs && $rd && $re)
		{
			commitTransection();
			echo 'success';
		}
		else
		{
			dbRollback();
			echo 'fail';
		}
	}
}

///////////////////  ตรวจสอบบาร์โค้ดที่ยิงเข้ามา ///////////////////////
if( isset( $_GET['check_barcode'] ) && isset( $_POST['barcode'] ) )
{
	$item 			= "fail";
	$barcode 	= trim($_POST['barcode']);
	$id_zone		= $_POST['id_zone'];
	$consign 	= new consign();
	$rs 			= $consign->checkBarcode($barcode);
	if( $rs )
	{
		$item = $rs->id_product_attribute.' | '.$rs->reference.' | '.$consign->stockConsignZone($rs->id_product_attribute, $id_zone).' | '.$rs->price;
	}
	echo $item;
}
////////////////////////  ต้องการข้อมูลสินค้า  ///////////////
if( isset( $_GET['getItemData'] ) )
{
	$item 			= "fail";
	$reference	= $_POST['reference'];
	$id_zone		= $_POST['id_zone'];
	$consign		= new consign();
	$rs 			= $consign->getItemByReference($reference);
	if( $rs )
	{
		$item = $rs->id_product_attribute.' | '.$consign->stockConsignZone($rs->id_product_attribute, $id_zone).' | '.$rs->price;
	}
	echo $item;
}

//////////////////////////////////   ตรวจสอบสถานะ   //////////////////////////////
if( isset( $_GET['getConsignStatus'] ) && isset( $_GET['id_order_consign'] ) )
{
	$consign = new consign();
	echo $consign->getConsignStatus($_GET['id_order_consign']);
}

///////////////////////////////  ดึงรายการยอดต่างจากการกระทบยอดมา เพิ่มตัดยอดฝากขาย //////////////////

if( isset( $_GET['add_consign_diff'] ) )
{
	$id_cc	= $_GET['id_consign_check'];
	$ck		= new consignCheck($id_cc);
	$qs 		= $ck->getCheckDiff($id_cc);
	$sc 		= true;
	
	if( dbNumRows($qs) > 0 )
	{
		$data	= array(
					"reference" 			=> newConsignReference("PREFIX_CONSIGN"),
					"id_customer" 		=> $ck->id_customer,
					"date_add" 			=> dbDate(date('Y-m-d'), true),
					"comment" 			=> '',
					"consign_status"	=> 0,
					"id_zone"				=> $ck->id_zone,
					"id_consign_check" => $id_cc,
					"id_employee"		=> getCookie('user_id')
									);
		$consign	= new consign();
		startTransection();
		$id			= $consign->addNewConsign($data); /// ได้ id_order_consign กลับมา (ถ้าสำเร็จ)
		if( $id )
		{
			
			while( $rs = dbFetchArray($qs) )
			{
				$id_pa 	= $rs['id_product_attribute'];
				$qty 		= $rs['qty_stock'] - $rs['qty_check'];
				
				$item = array(
							'id'			=> $id,
							'id_pa'	=> $id_pa,
							'price'		=> get_product_price($id_pa),
							'p_dis'	=> 0.00,
							'a_dis'	=> 0.00,
							'qty'		=> $qty
					);
				$rd 	= $consign->insertConsignDetail($item);
				if( !$rd ){ $sc = false; }
			}
			$rc = $ck->changeStatus($id_cc, 1);
			
			if( !$rc ){ $sc = false; }
		}
	}
	
	if( $sc )
	{
		commitTransection();
		$mes = 'เพิ่มรายการตัดยอดฝากขายเรียบร้อยแล้ว';
		header("location: ../index.php?content=consign&id_order_consign=".$id."&edit&message=".$mes);	
	}
	else
	{
		dbRollback();
		$mes = 'ไม่สามารถเพิ่มรายการตัดยอดฝากได้เพราะไม่มีรายการสินค้าที่จะนำมาตัดยอด';
		header("location: ../index.php?content=consign_check&edit=y&id_consign_check=".$id_cc."&error=".$mes);
	}
}

//////////////////////////////////  Print /////////////////////////////////
if( isset( $_GET['printConsign'] ) && isset( $_GET['id'] ) )
{
	$id 	= $_GET['id'];
	$cs 	= new consign($id);
	$print	= new printer();
	$pd 	= new product();

	echo $print->doc_header();

	$print->add_title('ตัดยอดฝากขาย (ใช้สำหรับยิงเข้า Formula เท่านั้น)');
	$header			= array(
							"เลขที่เอกสาร"		=> $cs->reference,
							"วันที่เอกสาร"		=>	 thaiDate($cs->date_add),
							"ลูกค้า"				=> customer_name($cs->id_customer),
							"โซน" 				=> get_zone($cs->id_zone),
							"พนักงาน"			=> employee_name($cs->id_employee)
							);
	$print->add_header($header);
	$detail			= $cs->getConsignItems($id);
	$total_row 		= dbNumRows($detail);
	$config 			= array("total_row"=>$total_row, "font_size"=>10, "sub_total_row"=>4, "footer"=>false);
	$print->config($config);
	$row 				= $print->row;
	$total_page 		= $print->total_page;

	$total_qty 		= 0;
	$total_price		= 0;
	$total_amount 	= 0;
	$total_discount = 0;
	//**************  กำหนดหัวตาราง  ******************************//
	$thead	= array(
						array("ลำดับ", "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
						array("บาร์โค้ด", "width:15%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("สินค้า", "width:30%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("ราคา", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("ส่วนลด(%)", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("ส่วนลด(฿)", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("จำนวน", "width:8%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
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
	$footer	= array( 	array("ผู้จัดทำ", "","วันที่............................."), 	array("ผู้ตรวจสอบ", "","วันที่............................."),	array("ผู้อนุมัติ", "","วันที่.............................")	);
	$print->set_footer($footer);

	$n = 1;
	while($total_page > 0 )
	{
		echo $print->page_start();
		echo $print->top_page();
		echo $print->content_start();
		echo $print->table_start();
		$i 						= 0;
		while($i<$row)
		{
			$rs = dbFetchArray($detail);
			if(count($rs) != 0)
			{
				$id_pa 				= $rs['id_product_attribute'];
				$id_pd		 		= $pd->get_id_product($id_pa);
				$product_code 	= $pd->product_reference($id_pa);
				$product_name 	= "<input type='text' style='border:0px; width:100%;' value='".$pd->product_name($id_pd)."' />";
				$barcode			= $print->print_barcode(get_barcode($id_pa), "width:100%; height:21.8px;");
				$p_dis				= $rs['reduction_percent'] > 0 ? $rs['reduction_percent'] . ' %' : '-' ;
				$a_dis				= $rs['reduction_amount'] > 0 ? number_format($rs['reduction_amount'], 2) : '-';
				$discount			= getConsignSumDiscount($rs['qty'], $rs['product_price'], $rs['reduction_percent'], $rs['reduction_amount']);
				$amount 				= getConsignSumAmount($rs['qty'], $rs['product_price'], $rs['reduction_percent'], $rs['reduction_amount']);

				$data 				= array($n, $barcode, $product_code, number_format($rs['product_price'], 2), $p_dis, $a_dis,  number_format($rs['qty']), number_format($amount, 2) );
				$total_qty 			+= $rs['qty'];
				$total_price 		+= $rs['qty'] * $rs['product_price'];
				$total_amount 		+= $amount;
				$total_discount 	+= $discount;

			}
			else
			{
				$data = array("", "", "", "","", "","","");
			}
			echo $print->print_row($data);
			$n++; $i++;
		} /// end while
		echo $print->table_end();
		if($print->current_page == $print->total_page)
		{
			$qty		= number_format($total_qty);
			$amount 	= number_format($total_price,2);
			$total_discount_amount = number_format($total_discount,2);
			$net_amount = number_format($total_price - ($total_discount) ,2);
			$remark = $cs->comment;
		}else{
			$qty = "";
			$amount = "";
			$total_discount_amount = "";
			$net_amount = "";
			$remark = "";
		}
		$sub_total = array(
						array("<td rowspan='4' style='height:".$print->row_height."mm; border-top: solid 1px #ccc; border-bottom-left-radius:10px; width:55%; font-size:10px;'><strong>หมายเหตุ : </strong>".$remark."</td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc;'><strong>จำนวนรวม</strong></td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; text-align:right;'>".$qty."</td>"),
						array("<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc;'><strong>ราคารวม</strong></td>
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

///////////////////////////////////////////////////////
if(isset($_GET['print_diff'])){
	$id_consign_check = $_GET['id_consign_check'];
	list($reference,$id_customer,$remark,$date_add) = dbFetchArray(dbQuery("SELECT reference,id_customer,comment,date_add FROM tbl_consign_check WHERE id_consign_check = $id_consign_check"));
	$company = new company();
	$customer = new customer($id_customer);
			$title = "ใบแจ้งรายการขายจากการฝากขาย";
	$total_qty = ""; /// เก็บยอดสินค้าตอนวนลูป
	$total_all_qty =""; ///วนเสร็จแล้วเอาค่ามาใส่ตัวนี้
	$total_order = ""; ///เก็บยอดรวมเพิ่มทีละรายการเวลาวนลูป
	$total_order_amount = "";///วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$total_discount_order =""; //เก็บยอดเงินส่วนลดตอนวนลูป
	$total_discount_amount = ""; //วนลูปจบเอายอดเงินส่วนลดมาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
	$net_total =""; //มูลค่าสินค้าหลังหักส่วนลด
	$row = 22;
	$sql = dbQuery("SELECT tbl_consign_check_detail.id_product_attribute, barcode, reference, qty_stock, qty_check FROM tbl_consign_check_detail LEFT JOIN tbl_product_attribute ON tbl_consign_check_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_consign_check = $id_consign_check AND qty_stock > qty_check ORDER BY barcode DESC");
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
				<a href='../index.php?content=consign&consign_balance&id_consign_check=$id_consign_check' ><button  class='btn btn-primary pull-right' type='button' style='margin-right:20px;' />ยกเลิก</button></a>
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
				list($id_product_attribute,$barcode,$reference1,$qty_stock,$qty_check)= dbFetchArray($sql);
				$product = new product();
				$id_product = $product->getProductId($id_product_attribute);
				$product->product_detail($id_product);
				$product->product_attribute_detail($id_product_attribute);
				$product_price = $product->product_price;
				$diff = $qty_stock - $qty_check;
				$price = $diff * $product_price;
				$sumdiff = $sumdiff + $diff;
				$total = $total +$price;
				if($count+1 >$row){  $css_row ="border-bottom: solid 1px #AAA; border-top: 0px;";  }else{ $css_row ="border-top: 0px;";}
				echo"<tr style='height:9mm;'>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$n</td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$barcode</td>
				<td style='vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$reference1 </td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$product_price</td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 10px;'>$diff</td>
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

if(isset($_GET['print_balance'])){
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
/***********************************************************************************************************************************************************************************************************/




if(	isset(	$_GET['clear_filter']	 ) )
{
	deleteCookie('consignFromDate');
	deleteCookie('consignToDate');
	deleteCookie('consign_filter');
	deleteCookie('consign_search_text');
	echo 'done';
}

?>
