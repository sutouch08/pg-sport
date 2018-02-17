<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
require "../function/sponsor_helper.php";
require "../function/support_helper.php";

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////		รับคืนจาก อภินันท์			//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////		บันทึกรายการคืน		////////////////////////////////////////////////
if( isset($_GET['save_add']) && isset($_GET['id_return_support']) )
{
	$res = "fail";
	$id_return_support 	= $_GET['id_return_support'];
	$rs	= new return_support($id_return_support);
	$ro	= $rs->save_add();
	if($ro)
	{
		$res = "success";
	}
	echo $res;
}

if( isset($_GET['save_edit']) && isset($_GET['id_return_support']) )
{
	$res = "fail";
	$id_return_support 	= $_GET['id_return_support'];
	$rs	= new return_support($id_return_support);
	$rd	= $rs->drop_data();
	if($rd)
	{
		$ro	= $rs->save_add();
		if($ro)
		{
			$res = "success";
		}
	}
	echo $res;
}

///////////////////////////////////////////// 		แก้ไขเอกสาร 		/////////////////////////////////////////////
if( isset($_GET['update']) && isset($_POST['id_return_support']) )
{
	$id_return_support	= $_POST['id_return_support'];
	$data		= array(
						"order_reference"=>$_POST['reference'],
						"id_employee"=>$_POST['id_employee'],
						"date_add"=>dbDate($_POST['date_add']),
						"remark"=>$_POST['remark']
					);
	$ro		= new return_support();
	$rs 		= $ro->update($id_return_support, $data);
	if($rs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}

/////////////////////////////////////		 ลบรายการ 1 บรรทัด 		/////////////////////////////////////////
if( isset($_GET['delete_row']) && isset($_GET['id_return_support_detail']) )
{
	$id 	= $_GET['id_return_support_detail'];
	$rs	= new return_support();
	$qs	= $rs->delete_item($id);
	if($qs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}

///////////////////////////		ลบเอกสารคืนสินค้าอภินันท์	/////////////////////////////////////////////////
if( isset($_GET['delete_return']) && isset($_GET['id_return_support']) )
{
	$id_return_support	= $_GET['id_return_support'];
	$res 	= "fail";
	$rs 	= new return_support($id_return_support);
	$rd	= $rs->drop_data();
	if($rd)
	{
		$rm 	= $rs->drop_all_detail($id_return_support);
		if($rm)
		{
			$ro 	= $rs->drop_return($id_return_support);
			if($ro)
			{
				$res = "success";
			}
		}
	}
	echo $res;
}

////////////////////////////////////////		รวมยอดรายการ		/////////////////////////////////////////
if( isset($_GET['sum_item']) && isset($_GET['id_return_support']) )
{
	$id_return_support = $_GET['id_return_support'];
	$rs	= new return_support();
	$data = $rs->sum_item($id_return_support);
	if($data)
	{
		echo json_encode($data);
	}else{
		echo "fail";
	}
}

//////////////////////////		ยิงบาร์โค้ดรับคืนทีละรายการ สำหรับอภินันท์เท่านั้น		////////////////////////////////////
if( isset($_GET['add_item']) && isset($_POST['qty']) && isset($_POST['barcode']) && isset($_POST['id_return_support']) )
{
	$id_return_support	= $_POST['id_return_support'];
	$qty					= $_POST['qty'];
	$barcode			= $_POST['barcode'];
	$id_zone				= $_POST['id_zone'];
	if($qty == ""){ $qty = 1; }
	$product		= new product();
	$arr			=  $product->check_barcode($barcode); ///ดึง id_product_attribute และ จำนวน จากบาร์โค้ด คืนค่ามาเป็น array [id_product_attribute] และ [qty] ตามลำดับ
	$id_product_attribute = $arr['id_product_attribute'];
	$qty 	= $arr['qty'] * $qty;
	$rs = new return_support();

	if( $id_product_attribute !="" )
	{
		$data['id_product_attribute']	 	= $id_product_attribute;
		$data['qty']							= $qty;
		$data['id_zone']						= $id_zone;
		$data['date_add']					= date("Y-m-d H:i:s");
		$data['status']						= 0;
		$ro		= $rs->add_item($id_return_support, $data);
		if($ro)
		{
			echo json_encode($ro);
		}else{
			echo "fail";
		}
	}else{
		echo "fail";
	}
}

////////////////////////////////////////		เพิ่มเอกสารรับคืนจากอภินันท์		//////////////////////////////////////
if( isset($_GET['add']) && isset($_GET['role']) && $_GET['role'] == 7 )
{
	$data 						= array();
	$data['reference']			= get_max_return_support_reference(dbDate($_POST['date_add']));
	$data['order_reference']	= $_POST['order_reference'];
	$data['id_employee']		= $_POST['id_employee'];
	$data['date_add']			= dbDate($_POST['date_add']);
	$data['remark']				= $_POST['remark'];
	$data['status']				= 0;
	$rs	= new return_support();
	$ro	= $rs->add($data);
	if($ro)
	{
		header("location: ../index.php?content=support_return&add=y&id_return_support=".$ro);
	}else{
		$message("ไม่สามารถเพิ่มรายการได้");
		header("location: ../index.php?content=support_return&add=y&error=".$message);
	}
}


//////////////////////////////////////		ตรวจสอบเลขที่อ้างอิง		////////////////////////////////////
if( isset($_GET['check_support_order']) )
{
	$id_employee 	= $_POST['id_employee'];
	$reference		= $_POST['reference'];
	$qs = dbQuery("SELECT tbl_order_support.id_order FROM tbl_order_support JOIN tbl_order ON tbl_order_support.id_order = tbl_order.id_order WHERE reference = '".$reference."' AND tbl_order_support.id_employee = ".$id_employee." AND role = 7 AND current_state = 9");
	if(dbNumRows($qs) == 1 )
	{
		echo "ok";
	}else{
		echo "fail";
	}
}

////////////////////////////////////////////////////// 	จบรับคืนจากอภินันท์		//////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////		  รับคืนจาก สปอนเซอร์  		/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////		บันทึกรายการคืน		////////////////////////////////////////////////
if( isset($_GET['save_add']) && isset($_GET['id_return_sponsor']) )
{
	$id_return_sponsor 	= $_GET['id_return_sponsor'];
	$rs	= new return_sponsor($id_return_sponsor);
	$ro	= $rs->save_add();
	if($ro)
	{
		echo "success";
	}else{
		echo "fail";
	}
}

if( isset($_GET['save_edit']) && isset($_GET['id_return_sponsor']) )
{
	$id_return_sponsor 	= $_GET['id_return_sponsor'];
	$rs	= new return_sponsor($id_return_sponsor);
	$rd	= $rs->drop_data();
	if($rd)
	{
		$ro	= $rs->save_add();
		if($ro)
		{
			echo "success";
		}else{
			echo "fail";
		}
	}else{
		echo "fail";
	}
}

///////////////////////////		ลบเอกสารคืนสินค้าจากสปอนเซอร์		/////////////////////////////////////////////////
if( isset($_GET['delete_return']) && isset($_GET['id_return_sponsor']) )
{
	$id_return_sponsor	= $_GET['id_return_sponsor'];
	$res 	= "fail";
	$rs 	= new return_sponsor($id_return_sponsor);
	$rd	= $rs->drop_data();
	if($rd)
	{
		$rm 	= $rs->drop_all_detail($id_return_sponsor);
		if($rm)
		{
			$ro 	= $rs->drop_return($id_return_sponsor);
			if($ro)
			{
				$res = "success";
			}
		}
	}
	echo $res;
}


//////////////////////////		ยิงบาร์โค้ดรับคืนทีละรายการ สำหรับสปอนเซอร์เท่านั้น		////////////////////////////////////
if( isset($_GET['add_item']) && isset($_POST['qty']) && isset($_POST['barcode']) && isset($_POST['id_return_sponsor']) )
{
	$id_return_sponsor	= $_POST['id_return_sponsor'];
	$qty					= $_POST['qty'];
	$barcode			= $_POST['barcode'];
	$id_zone				= $_POST['id_zone'];
	if($qty == ""){ $qty = 1; }
	$product		= new product();
	$arr			=  $product->check_barcode($barcode); ///ดึง id_product_attribute และ จำนวน จากบาร์โค้ด คืนค่ามาเป็น array [id_product_attribute] และ [qty] ตามลำดับ
	$id_product_attribute = $arr['id_product_attribute'];
	$qty 	= $arr['qty'] * $qty;
	$rs = new return_sponsor();

	if( $id_product_attribute !="" )
	{
		$data['id_product_attribute']	 	= $id_product_attribute;
		$data['qty']							= $qty;
		$data['id_zone']						= $id_zone;
		$data['date_add']					= date("Y-m-d H:i:s");
		$data['status']						= 0;
		$ro		= $rs->add_item($id_return_sponsor, $data);
		if($ro)
		{
			echo json_encode($ro);
		}else{
			echo "fail";
		}
	}else{
		echo "fail";
	}
}

////////////////////////////////////////		รวมยอดรายการ		/////////////////////////////////////////
if( isset($_GET['sum_item']) && isset($_GET['id_return_sponsor']) )
{
	$id_return_sponsor = $_GET['id_return_sponsor'];
	$rs	= new return_sponsor();
	$data = $rs->sum_item($id_return_sponsor);
	if($data)
	{
		echo json_encode($data);
	}else{
		echo "fail";
	}
}

/////////////////////////////////////		 ลบรายการ 1 บรรทัด 		/////////////////////////////////////////
if( isset($_GET['delete_row']) && isset($_GET['id_return_sponsor_detail']) )
{
	$id 	= $_GET['id_return_sponsor_detail'];
	$rs	= new return_sponsor();
	$qs	= $rs->delete_item($id);
	if($qs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}

////////////////////////////////////////		เพิ่มเอกสารรับคืนจากสปอนเซอร์		//////////////////////////////////////
if( isset($_GET['add']) && isset($_GET['role']) && $_GET['role'] == 4 )
{
	$data 						= array();
	$data['reference']			= get_max_return_sponsor_reference(dbDate($_POST['date_add']));
	$data['order_reference']	= $_POST['order_reference'];
	$data['id_customer']		= $_POST['id_customer'];
	$data['id_employee']		= $_COOKIE['user_id'];
	$data['date_add']			= dbDate($_POST['date_add']);
	$data['remark']				= $_POST['remark'];
	$data['status']				= 0;
	$rs	= new return_sponsor();
	$ro	= $rs->add($data);
	if($ro)
	{
		header("location: ../index.php?content=sponsor_return&add=y&id_return_sponsor=".$ro);
	}else{
		$message("ไม่สามารถเพิ่มรายการได้");
		header("location: ../index.php?content=sponsor_return&add=y&error=".$message);
	}
}


///////////////////////////////////////////// 		แก้ไขเอกสาร 		/////////////////////////////////////////////
if( isset($_GET['update']) && isset($_POST['id_return_sponsor']) )
{
	$id_return_sponsor	= $_POST['id_return_sponsor'];
	$data		= array(
						"order_reference"=>$_POST['reference'],
						"id_customer"=>$_POST['id_customer'],
						"id_employee"=>$_COOKIE['user_id'],
						"date_add"=>dbDate($_POST['date_add']),
						"remark"=>$_POST['remark']
					);
	$ro		= new return_sponsor();
	$rs 		= $ro->update($id_return_sponsor, $data);
	if($rs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}

//////////////////////////////////////		ตรวจสอบเลขที่อ้างอิง		////////////////////////////////////
if( isset($_GET['check_sponsor_order']) )
{
	$id_customer 	= $_POST['id_customer'];
	$reference		= $_POST['reference'];
	$qs = dbQuery("SELECT id_order FROM tbl_order WHERE reference = '".$reference."' AND id_customer = ".$id_customer." AND role = 4 AND current_state = 9");
	if(dbNumRows($qs) == 1 )
	{
		echo "ok";
	}else{
		echo "fail";
	}
}
/////////////////////////////////////////////////////////////////		จบรับคืนจากสปอนเซอร์		/////////////////////////////////////////////////////////////






/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////					รับคืนจากการขาย					//////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////  บันทึกรายการลดหนี้  ////////////////////////////
if( isset($_GET['save_add']) && isset($_GET['id_return_order']) )
{
	$id_return_order 	= $_GET['id_return_order'];
	if( isset( $_POST['id_detail']))
	{
		$id_detail			= $_POST['id_detail'];
		$o_ref				= $_POST['reference'];
		foreach($id_detail as $id)
		{
			$arr = explode(" : ", $o_ref[$id]);
			$order_reference = $arr[0];
			$qs	= dbQuery("UPDATE tbl_return_order_detail SET order_reference = '".$order_reference."' WHERE id_return_order_detail = ".$id);
		}
		$rs	= new return_order($id_return_order);
		$ro	= $rs->save_add();
		if($ro)
		{
			echo "success";
		}else{
			echo "fail";
		}
	}
	else
	{
		echo "must_total";
	}

}

//////////////////////////////////  บันทึกรายการลดหนี้2  ////////////////////////////
if( isset($_GET['save_add2']) && isset($_GET['id_return_order']) )
{
	$id_return_order 	= $_GET['id_return_order'];
	$reduction_percent = $_POST['reduction_percent'];
	$reduction_amount = $_POST['reduction_amount'];
	$rs	= new return_order($id_return_order);
	foreach($reduction_percent as $id=>$val)
	{
		dbQuery("UPDATE tbl_return_order_detail SET reduction_percent = ".$val.", reduction_amount = ".$reduction_amount[$id].", final_price = product_price - ((".($val*0.01)." * product_price) + ".$reduction_amount[$id]."),
		total_amount = qty * (product_price - (".$val*0.01." * product_price) + ".$reduction_amount[$id].") WHERE id_return_order_detail = ".$id);
	}
	$rs	= new return_order($id_return_order);
	$ro	= $rs->save_add2();
	if($ro)
	{
		echo "success";
	}else{
		echo "fail";
	}
}

if( isset($_GET['save_edit']) && isset($_GET['id_return_order']) )
{
	$id_return_order	= $_GET['id_return_order'];
	if( isset( $_POST['id_detail']))
	{
		$id_detail			= $_POST['id_detail'];
		$o_ref				= $_POST['reference'];
		$rs 					= new return_order($id_return_order);
		$ro					= $rs->drop_data($id_return_order);
		if($ro)
		{
			foreach($id_detail as $id)
			{
				$arr = explode(" : ", $o_ref[$id]);
				$order_reference = $arr[0];
				$qs	= dbQuery("UPDATE tbl_return_order_detail SET order_reference = '".$order_reference."' WHERE id_return_order_detail = ".$id);
			}
			$rn	= $rs->save_add();
			if($rn)
			{
				echo "success";
			}else{
				echo "fail";
			}
		}else{
			echo "fail";
		}
	}
	else
	{
		echo "must_total";
	}
}


if( isset($_GET['save_edit2']) && isset($_GET['id_return_order']) )
{
	$id_return_order 	= $_GET['id_return_order'];
	$reduction_percent = $_POST['reduction_percent'];
	$reduction_amount = $_POST['reduction_amount'];
	$rs	= new return_order($id_return_order);
	$ro	= $rs->drop_data($id_return_order);
	if($ro)
	{
		foreach($reduction_percent as $id=>$val)
		{
			dbQuery("UPDATE tbl_return_order_detail SET reduction_percent = ".$val.", reduction_amount = ".$reduction_amount[$id].", final_price = product_price - ((".($val*0.01)." * product_price) + ".$reduction_amount[$id]."),
			total_amount = qty * (product_price - (".$val*0.01." * product_price) + ".$reduction_amount[$id].") WHERE id_return_order_detail = ".$id);
		}
		$rn	= $rs->save_add2();
		if($rn)
		{
			echo "success";
		}else{
			echo "fail";
		}
	}else{
		echo "fail";
	}
}

/*********************************  แก้ไขเอกสาร  ********************************/
if( isset($_GET['update']) && isset($_POST['id_return_order']) )
{
	$id_return_order	= $_POST['id_return_order'];
	$data		= array(
						"remark"=>$_POST['remark']
					);
	$ro		= new return_order();
	$rs 		= $ro->update($id_return_order, $data);
	if($rs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}

/*********************************  แก้ไขเอกสาร2  ********************************/
if( isset($_GET['update2']) && isset($_POST['id_return_order']) )
{
	$id_return_order	= $_POST['id_return_order'];
	$data		= array(
						"date_add"=>dbDate($_POST['date_add'], true),
						"id_customer"=>$_POST['id_customer'],
						"order_reference"=>$_POST['order_reference'],
						"remark"=>$_POST['remark']
					);
	$ro		= new return_order($id_return_order);
	if( dbDate($ro->date_add) != dbDate($_POST['date_add']) )
	{
		$qs = dbQuery("UPDATE tbl_stock_movement SET date_upd = '".dbDate($_POST['date_add'], true)."' WHERE reference = '".$ro->reference."'");
	}
	if( $_POST['order_reference'] != $ro->order_reference)
	{
		$qs = dbQuery("UPDATE tbl_return_order_detail SET order_reference = '".$_POST['order_reference']."' WHERE id_return_order = ".$id_return_order);
	}
	$rs 		= $ro->update($id_return_order, $data);
	if($rs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}


/********************************* ลบรายการ 1 บรรทัด  *************************/
if( isset($_GET['delete_row']) && isset($_GET['id_return_order_detail']) )
{
	$id 	= $_GET['id_return_order_detail'];
	$ro	= new return_order();
	$qs 	= $ro->delete_row($id);
	if($qs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}


/*********************************  ยิงบาร์โค้ดรับคืนทีละรายการ  *********************/
if( isset($_GET['add_item']) && isset($_POST['qty']) && isset($_POST['barcode']) && isset($_POST['id_return_order']) )
{
	$id_return_order	= $_POST['id_return_order'];
	$qty					= $_POST['qty'];
	$barcode			= trim($_POST['barcode']);
	$id_zone				= $_POST['id_zone'];
	if($qty == ""){ $qty = 1; }
	$product		= new product();
	$arr			=  $product->check_barcode($barcode); ///ดึง id_product_attribute และ จำนวน จากบาร์โค้ด คืนค่ามาเป็น array [id_product_attribute] และ [qty] ตามลำดับ
	$id_product_attribute = $arr['id_product_attribute'];
	$qty 	= $arr['qty'] * $qty;

	$data['id_product_attribute']		= $id_product_attribute;
	$data['order_reference']			= '';//$_POST['order_reference'];
	$data['qty']							= $qty;
	$data['id_zone']						= $id_zone;
	$data['date_add']					= dbDate($_POST['date_add']);
	$data['status']						= 0;
	$ro	= new return_order();
	$rs	= $ro->add_item($id_return_order, $data);
	if($rs)
	{
		echo json_encode($rs);
	}else{
		echo "fail";
	}
}

/*********************************  ยิงบาร์โค้ดรับคืนทีละรายการ2  *********************/
if( isset($_GET['add_item2']) && isset($_POST['qty']) && isset($_POST['barcode']) && isset($_POST['id_return_order']) )
{
	$id_return_order	= $_POST['id_return_order'];
	$qty					= $_POST['qty'];
	$barcode			= trim($_POST['barcode']);
	$id_zone				= $_POST['id_zone'];
	if($qty == ""){ $qty = 1; }
	$product		= new product();
	$arr			=  $product->check_barcode($barcode); ///ดึง id_product_attribute และ จำนวน จากบาร์โค้ด คืนค่ามาเป็น array [id_product_attribute] และ [qty] ตามลำดับ
	$id_product_attribute = $arr['id_product_attribute'];
	$qty 	= $arr['qty'] * $qty;

	$data['id_product_attribute']		= $id_product_attribute;
	$data['order_reference']			= $_POST['order_reference'];
	$data['qty']							= $qty;
	$data['id_zone']						= $id_zone;
	$data['date_add']					= dbDate($_POST['date_add']);
	$data['status']						= 0;
	$ro	= new return_order();
	$rs	= $ro->add_item2($id_return_order, $data);
	if($rs)
	{
		echo json_encode($rs);
	}else{
		echo "fail";
	}
}

/******************************* รวมยอดรายการ  *********************************/
if( isset($_GET['sum_item']) && isset($_GET['id_return_order']) )
{
	$id_return_order 	= $_GET['id_return_order'];
	$id_customer		= $_GET['id_customer'];
	$qs = dbQuery("SELECT * FROM tbl_return_order_detail WHERE id_return_order = ".$id_return_order);
	$data = array();
	$ro	= new return_order();
	while($rs = dbFetchArray($qs) )
	{
		$product 		= new product();
		$id_product		= $product->getProductId($rs['id_product_attribute']);
		$product_name	= $product->product_reference($rs['id_product_attribute'])." : ".$product->product_name($id_product);
		$list				= "<select name='reference[".$rs['id_return_order_detail']."]' id='reference_".$rs['id_return_order_detail']."' style='width:100%; font-size:12px;'>";
		$list				.= $ro->select_last_item_order($rs['id_product_attribute'], $id_customer, $rs['order_reference'], $rs['id_return_order_detail'],10);
		$list				.= "</select>";
		$arr = array(
					"id"=>$rs['id_return_order_detail'],
					"product"=>$product_name,
					"qty"=>$rs['qty'],
					"zone"=>get_zone($rs['id_zone']),
					"date_add"=>thaiDateTime($rs['date_add']),
					"status"=>isActived($rs['status']),
					"list"=>$list
					);
		array_push($data, $arr);
	}
	echo json_encode($data);
}

/******************************* รวมยอดรายการ2  *********************************/
if( isset($_GET['sum_item2']) && isset($_GET['id_return_order']) )
{
	$id_return_order 	= $_GET['id_return_order'];
	$id_customer		= $_GET['id_customer'];
	$qs = dbQuery("SELECT * FROM tbl_return_order_detail WHERE id_return_order = ".$id_return_order);
	$data = array();
	$ro	= new return_order();
	while($rs = dbFetchArray($qs) )
	{
		$product 		= new product();
		$id_product		= $product->getProductId($rs['id_product_attribute']);
		$product_name	= $product->product_reference($rs['id_product_attribute'])." : ".$product->product_name($id_product);
		$arr = array(
					"id"=>$rs['id_return_order_detail'],
					"product"=>$product_name,
					"qty"=>$rs['qty'],
					"price"=>$rs['product_price'],
					"percent"=>$rs['reduction_percent'],
					"amount"=>$rs['reduction_amount'],
					"final_price"=>$rs['final_price'],
					"total_amount"=>$rs['total_amount'],
					"zone"=>get_zone($rs['id_zone']),
					"date_add"=>thaiDate($rs['date_add']),
					"status"=>isActived($rs['status']),
					"list"=>$rs['order_reference']
					);
		array_push($data, $arr);
	}
	echo json_encode($data);
}


/*********************************  รับเข้าจากการขาย  **************************/
if( isset($_GET['add']) && isset($_GET['role']) && $_GET['role'] == 1 )
{
	$data 						= array();
	$data['reference']			= get_max_return_reference(dbDate($_POST['date_add']));
	$data['order_reference']	= $_POST['order_reference'];
	$data['role']					= 1;
	$data['id_customer']		= $_POST['id_customer'];
	$data['id_sale']				= get_id_sale_by_customer($_POST['id_customer']);
	$data['id_employee']		= $_COOKIE['user_id'];
	$data['date_add']			= dbDate($_POST['date_add'], true);
	$data['remark']				= $_POST['remark'];
	$data['status']				= 0;
	$rs	= new return_order();
	$ro	= $rs->add($data);
	if($ro)
	{
		echo $ro;
	}else{
		echo "fail";
	}
}

///////////////////////////		ลบเอกสารคืนสินค้าจากการขาย	/////////////////////////////////////////////////
if( isset($_GET['delete_return']) && isset($_GET['id_return_order']) )
{
	$id_return_order	= $_GET['id_return_order'];
	$res 	= "fail";
	$status = 1; ///// 1 = บันทึกแล้ว  0 = ยังไม่บันทึก  '' = ทุกรายการ
	$rs 	= new return_order($id_return_order);
	$rd	= $rs->drop_data($id_return_order, $status); //// ลบข้อมูลที่มีการบันทึกไปแล้วในตารางต่างๆออก
	if($rd)
	{
		$rm 	= $rs->drop_all_detail($id_return_order);
		if($rm)
		{
			$ro 	= $rs->drop_return($id_return_order);
			if($ro)
			{
				$res = "success";
			}
		}
	}
	echo $res;
}

/////////////////////////////////////////////////////////////////////////////////////////////
if( isset($_GET['get_zone']) && isset($_POST['barcode']) )
{
	$barcode = $_POST['barcode'];
	$qs = dbQuery("SELECT id_zone, zone_name FROM tbl_zone WHERE barcode_zone = '".$barcode."'");
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		echo $rs['id_zone']." : ".$rs['zone_name'];
	}else{
		echo "fail";
	}
}


if( isset($_GET['check_order']) )
{
	$id_customer 	= $_POST['id_customer'];
	$reference		= $_POST['reference'];
	$qs = dbQuery("SELECT id_order FROM tbl_order WHERE reference = '".$reference."' AND id_customer = ".$id_customer." AND role = 1");
	if(dbNumRows($qs) == 1 )
	{
		echo "ok";
	}else{
		echo "fail";
	}
}

if( isset($_GET['print_return']) && isset($_GET['id_return_order']) )
{
	$id_return_order	= $_GET['id_return_order'];
	$ro = new return_order($id_return_order);
	$print = new printer();
	echo $print->doc_header();
	$print->add_title("เอกสารรับคืนสินค้าจากการขาย");
	$header	= array("เลขที่เอกสาร"=>$ro->reference, "เลขที่อ้างอิง"=>$ro->order_reference, "ลูกค้า"=>customer_name($ro->id_customer), "วันที่"=>thaiDate($ro->date_add));
	$print->add_header($header);
	$detail = $ro->return_detail($id_return_order, 1);
	$total_row = dbNumRows($detail);
	$config = array("total_row"=>$total_row, "font_size"=>10, "sub_total_row"=>2);
	$print->config($config);
	$row = $print->row;
	$total_page = $print->total_page;
	$total_qty = 0;
	$total_amount = 0;
	//**************  กำหนดหัวตาราง  ******************************//
	$thead	= array(
						array("ลำดับ", "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
						array("สินค้า", "width:40%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("เลขที่อ้างอิง", "width:15%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("ราคา", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("ส่วนลด", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("จำนวน", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("มูลค่า", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
						);
	$print->add_subheader($thead);

	//***************************** กำหนด css ของ td *****************************//
	$pattern = array(
							"text-align: center; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
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
						$id_product = $product->getProductId($rs['id_product_attribute']);
						$product_name = $product->product_reference($rs['id_product_attribute'])." : ".$product->product_name($id_product);
						if($rs['reduction_percent'] != 0 ){ $discount = $rs['reduction_percent']." %"; }else if($rs['reduction_amount'] != 0){ $discount = number_format($rs['reduction_amount'],2)." ฿"; }else{ $discount = 0.00; }
						$data = array($n, $product_name, $rs['order_reference'], $rs['product_price'], $discount, number_format($rs['qty']), number_format($rs['total_amount'], 2));
						$total_qty += $rs['qty'];
						$total_amount += $rs['total_amount'];
					else :
						$data = array("", "", "", "","", "","");
					endif;
					echo $print->print_row($data);
					$n++; $i++;
				endwhile;
				echo $print->table_end();
				if($print->current_page == $print->total_page){ $qty = number_format($total_qty); $amount = number_format($total_amount,2); $remark = $ro->remark; }else{ $qty = ""; $amount = ""; $remark = ""; }
				$sub_total = array(
						array("<td rowspan='2' style='height:".$print->row_height."mm; border-top: solid 1px #ccc; border-bottom-left-radius:10px; width:60%; font-size:10px;'><strong>หมายเหตุ : </strong>".$remark."</td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc;'><strong>จำนวนรวม</strong></td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; text-align:right;'>".$qty."</td>"),
						array("<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px;'><strong>มูลค่ารวม</strong></td>
						<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; border-bottom:0px; border-bottom-right-radius:10px; text-align:right;'>".$amount."</td>")
						);
			echo $print->print_sub_total($sub_total);
			echo $print->content_end();
			echo $print->footer;
		echo $print->page_end();
		$total_page --; $print->current_page++;
	}
	echo $print->doc_footer();
}

if( isset($_GET['print_return_barcode']) && isset($_GET['id_return_order']) )
{
	$id_return_order	= $_GET['id_return_order'];
	$ro = new return_order($id_return_order);
	$print = new printer();
	echo $print->doc_header();
	$print->add_title("เอกสารรับคืนสินค้าจากการขาย");
	$header	= array("เลขที่เอกสาร"=>$ro->reference, "เลขที่อ้างอิง"=>$ro->order_reference, "ลูกค้า"=>customer_name($ro->id_customer), "วันที่"=>thaiDate($ro->date_add));
	$print->add_header($header);
	$detail = $ro->return_detail($id_return_order, 1);
	$total_row = dbNumRows($detail);
	$config = array("total_row"=>$total_row, "font_size"=>10, "sub_total_row"=>2);
	$print->config($config);
	$row = $print->row;
	$total_page = $print->total_page;
	$total_qty = 0;
	$total_amount = 0;
	//**************  กำหนดหัวตาราง  ******************************//
	$thead	= array(
						array("ลำดับ", "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
						array("บาร์โค้ด", "width:15%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("สินค้า", "width:25%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("เลขที่อ้างอิง", "width:15%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("ราคา", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("ส่วนลด", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("จำนวน", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("มูลค่า", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
						);
	$print->add_subheader($thead);

	//***************************** กำหนด css ของ td *****************************//
	$pattern = array(
							"text-align: center; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px; padding:0px; text-align:center;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"text-align:center; border-left: solid 1px #ccc; border-top:0px;",
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
						$id_product = $product->getProductId($rs['id_product_attribute']);
						$product_name = $product->product_reference($rs['id_product_attribute']);
						$barcodex = get_barcode($rs['id_product_attribute']);
						if( $barcodex ){	$barcode = "<img src='".WEB_ROOT."library/class/barcode/barcode.php?text=".$barcodex."' style='height:8mm;' />"; }else{ $barcode = ""; }
						if($rs['reduction_percent'] != 0 ){ $discount = $rs['reduction_percent']." %"; }else if($rs['reduction_amount'] != 0){ $discount = number_format($rs['reduction_amount'],2)." ฿"; }else{ $discount = 0.00; }
						$data = array($n, $barcode, $product_name, $rs['order_reference'], $rs['product_price'], $discount, number_format($rs['qty']), number_format($rs['total_amount'], 2));
						$total_qty += $rs['qty'];
						$total_amount += $rs['total_amount'];
					else :
						$data = array("", "", "", "", "","", "","");
					endif;
					echo $print->print_row($data);
					$n++; $i++;
				endwhile;
				echo $print->table_end();
				if($print->current_page == $print->total_page){ $qty = number_format($total_qty); $amount = number_format($total_amount,2); $remark = $ro->remark; }else{ $qty = ""; $amount = ""; $remark = ""; }
				$sub_total = array(
						array("<td rowspan='2' style='height:".$print->row_height."mm; border-top: solid 1px #ccc; border-bottom-left-radius:10px; width:60%; font-size:10px;'><strong>หมายเหตุ : </strong>".$remark."</td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc;'><strong>จำนวนรวม</strong></td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; text-align:right;'>".$qty."</td>"),
						array("<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px;'><strong>มูลค่ารวม</strong></td>
						<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; border-bottom:0px; border-bottom-right-radius:10px; text-align:right;'>".$amount."</td>")
						);
			echo $print->print_sub_total($sub_total);
			echo $print->content_end();
			echo $print->footer;
		echo $print->page_end();
		$total_page --; $print->current_page++;
	}
	echo $print->doc_footer();
}


if( isset($_GET['clear_filter']))
{
	setcookie("return_filter", "", time() - 3600, "/");
	setcookie('return_search-text', '', time() -3600, "/");
	setcookie("return_from_date", "", time() -3600, "/");
	setcookie("return_to_date", "", time() - 3600, "/");
	echo "success";
}

?>
