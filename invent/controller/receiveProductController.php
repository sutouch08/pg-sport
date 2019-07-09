<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";

/*********************   New Code ***********************/
if(isset($_GET['updateHeader']))
{
	$id = $_POST['id_receive_product'];
	$invoice = $_POST['invoice'];
	$remark = $_POST['remark'];
	$sc = TRUE;

	$arr = array(
		'invoice' => $invoice,
		'remark' => $remark
	);

	$cs = new receive_product();
	if($cs->update($id, $arr) !== TRUE)
	{
		$sc = FALSE;
		$message = 'Update Not successfull';
	}

	echo $sc === TRUE ? 'success' : $message;
}





if(isset($_GET['receiveItems']) && isset($_POST['id_receive_product']))
{
	$id_rp = $_POST['id_receive_product'];
	$id_po = $_POST['id_po'];
	$id_zone = $_POST['id_zone'];
	$details = json_decode($_POST['detail']);
	$id_warehouse = get_warehouse_by_zone($id_zone);
	$id_emp = getCookie('user_id');
	$date = date('Y-m-d');
	$sc = TRUE;
	$message = '';

	if(!empty($details))
	{
		$rd = new receive_product();

		startTransection();

		foreach($details as $item)
		{
			if($sc === FALSE)
			{
				break;
			}

			$data = array(
				'id_receive_product' => $id_rp,
				'id_product' => $item->id_product,
				'id_product_attribute' => $item->id_product_attribute,
				'qty' => $item->qty,
				'id_warehouse' => $id_warehouse,
				'id_zone' => $id_zone,
				'id_employee' => $id_emp,
				'date_add' => $date
			);


			if($rd->isExists($id_rp, $item->id_product_attribute) === TRUE)
			{
				if($rd->updateItem($data) === FALSE)
				{
					$sc = FALSE;
					$message = 'ปรับปรุงข้อมูลไม่สำเร็จ';
				}
			}
			else
			{
				if($rd->addNewItem($data) === FALSE)
				{
					$sc = FALSE;
					$message = 'เพิ่มข้อมูลไม่สำเร็จ';
				}
			}

		} //--- end foreach

		if($sc === TRUE)
		{
			commitTransection();
		}
		else
		{
			dbRollback();
		}
	}
	else
	{
		$sc = FALSE;
		$message = 'ไม่พบข้อมูลรับเข้ากรุณาตรวจสอบรายการ';
	}

	echo $sc === TRUE ? 'success' : $message;

}


if(isset($_GET['receiveItem']) && isset($_POST['id_receive_product']))
{
	$id_rp = $_POST['id_receive_product'];
	$id_po = $_POST['id_po'];
	$id_zone = $_POST['id_zone'];
	$id_pd = $_POST['id_product'];
	$id_pa = $_POST['id_product_attribute'];
	$qty   = $_POST['qty'];

	$rd = new receive_product();

	$data = array(
		'id_receive_product' => $id_rp,
		'id_product' => $id_pd,
		'id_product_attribute' => $id_pa,
		'qty' => $qty,
		'id_warehouse' => get_warehouse_by_zone($id_zone),
		'id_zone' => $id_zone,
		'id_employee' => getCookie('user_id'),
		'date_add' => date('Y-m-d')
	);

	if($rd->isExists($id_rp, $id_pa) === TRUE)
	{
		$rs = $rd->updateItem($data);
	}
	else
	{
		$rs = $rd->addNewItem($data);
	}

	echo $rs === TRUE ? 'success' : 'รับเข้าโซนไม่สำเร็จ';

}




if( isset( $_GET['cancleReceiveItem']) && isset($_POST['id_receive_product']))
{
	$id_rp = $_POST['id_receive_product'];
	$id_pa = $_POST['id_product_attribute'];

	$qr  = "DELETE FROM tbl_receive_product_detail ";
	$qr .= "WHERE id_receive_product = ".$id_rp." " ;
	$qr .= "AND id_product_attribute = ".$id_pa." ";
	$qr .= "AND status = 0 ";

	$qs = dbQuery($qr);

	echo dbAffectedRows();
}




if( isset($_GET['unSaveRecieved']) && isset($_POST['id_receive_product']))
{
	$sc = TRUE;
	$message = '';
	$id = $_POST['id_receive_product'];
	$rd = new receive_product();
	$qs = $rd->getSavedDetails($id);

	if(dbNumRows($qs) > 0)
	{
		startTransection();
		while($rs = dbFetchObject($qs))
		{
			if($sc === FALSE)
			{
				break;
			}

			if($rd->roll_back_action($rs->id_receive_product_detail) === FALSE)
			{
				$sc = FALSE;
				$message = 'ย้อนรายการไม่สำเร็จ';
			}

			if($rd->change_item_status($rs->id_receive_product_detail, 0) === FALSE)
			{
				$sc = FALSE;
				$message = 'เปลี่ยนสถานะรายการไม่สำเร็จ';
			}
		}

		if($sc === TRUE)
		{
			if($rd->change_status($rd->id_receive_product, 0) === FALSE)
			{
				$sc = FALSE;
				$message = 'เปลี่ยนสถานะเอกสารไม่สำเร็จ';
			}
		}

		if($sc === TRUE)
		{
			commitTransection();
		}
		else
		{
			dbRollback();
		}

		endTransection();
	}

	echo $sc === TRUE ? 'success' : $message;
}





/****************** End New code  *********************/



if( isset( $_GET['check_approve'] ) && isset( $_POST['password'] ) )
{
	$rs = "fail";
	$pass = md5(trim($_POST['password']));
	$qs = dbQuery("SELECT id_employee FROM tbl_employee JOIN tbl_access ON tbl_employee.id_profile = tbl_access.id_profile WHERE s_key = '".$pass."' AND id_tab = 49 AND ( tbl_access.add =1 OR tbl_access.edit = 1 OR tbl_access.delete = 1)");
	if(dbNumRows($qs) == 1 )
	{
		$rs = "success";
	}
	echo $rs;
}


if( isset( $_GET['save_edit'] ) && isset( $_POST['id_receive_product'] ) )
{
	$rd	= new receive_product();
	$rs	= $rd->save_add($_POST['id_receive_product']);
	if($rs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}

if( isset( $_GET['save_add'] ) && isset( $_POST['id_receive_product'] ) )
{
	$rd	= new receive_product();
	$rs	= $rd->save_add($_POST['id_receive_product']);
	if($rs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}

//////////////// ลบเอกสาร /////////////////
if( isset( $_GET['delete_doc'] ) && isset( $_GET['id_receive_product'] ) )
{
	$id = $_GET['id_receive_product'];
	$rd	= new receive_product();
	$rs 	= $rd->delete_doc($id);
	if($rs)
	{
		echo "success";
	}else{
		echo "ลบเอกสารไม่สำเร็จ";
	}
}




if( isset( $_GET['delete_item'] ) && isset( $_POST['id_receive_product_detail'] ) )
{
	$rd 	= new receive_product();
	$rs	= $rd->delete_item($_POST['id_receive_product_detail']);
	if($rs)
	{
		echo "success";
	}else{
		echo "fali";
	}
}


if( isset( $_GET['sum_item'] ) && isset( $_POST['id_receive_product'] ) )
{
	$rd 	= new receive_product();
	$qs 	= $rd->get_items($_POST['id_receive_product']);
	if(dbNumRows($qs) > 0 )
	{
		$data = array();
		$n = 1;
		while($rs = dbFetchArray($qs) )
		{
			$arr = array(
						"id"						=>$rs['id_receive_product_detail'],
						"no"					=> $n,
						"product_code" 	=> get_product_reference($rs['id_product_attribute']),
						"product_name" 	=> get_product_name($rs['id_product']),
						"zone_name"		=> get_zone($rs['id_zone']),
						"qty"					=> $rs['qty'],
						"status"				=> isActived($rs['status'])
						);
			array_push($data, $arr);
			$n++;
		}
		echo json_encode($data);
	}else{
		echo "fail";
	}
}





if( isset( $_GET['add_item'] ) && isset( $_POST['id_receive_product'] ) )
{
	$product		= new product();
	$arr			= $product->check_barcode(trim($_POST['barcode']));
	if($arr['id_product_attribute'] != 0 )
	{
		$id_pro		= $product->getProductId($arr['id_product_attribute']);
		$qty			= $_POST['qty'] * $arr['qty'];
		$rd			= new receive_product();
		$data			= array(
								"id_receive_product"	=> $_POST['id_receive_product'],
								"id_product"				=> $id_pro,
								"id_po"					=> $_POST['id_po'],
								"id_product_attribute"	=> $arr['id_product_attribute'],
								"qty"						=> $qty,
								"id_warehouse"			=> get_warehouse_by_zone($_POST['id_zone']),
								"id_zone"					=> $_POST['id_zone'],
								"id_employee"			=> $_COOKIE['user_id'],
								"date_add"				=> date("Y-m-d")
							);
		$rs 	= $rd->add_item($data);
		if($rs)
		{
			if($rs =="aa")
			{
				$datax = array(
								"product_code" 	=> get_product_reference($arr['id_product_attribute']),
								"product_name" 	=> get_product_name($id_pro),
								"zone_name"		=> get_zone($_POST['id_zone']),
								"qty"					=> $qty
								);
				echo json_encode($datax);
			}
			else
			{
				echo "not_in_po";
			}
		}
		else
		{
			echo "fail";
		}
	}
	else
	{
		echo "barcode_fail";
	}
}

if( isset( $_GET['update'] ) && isset( $_GET['id_receive_product'] ) )
{
	$id		= $_GET['id_receive_product'];
	$rd	= new receive_product();
	$data	= array(
				"invoice"				=> $_POST['invoice'],
				"po_reference"		=> $_POST['po_reference'],
				"id_po"				=> $_POST['id_po'],
				"id_employee"		=> $_POST['id_employee'],
				"date_add"			=> dbDate($_POST['date_add']),
				"remark"				=> $_POST['remark']
				);
	$rs	= $rd->update($id, $data);
	if($rs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}


if( isset( $_GET['add_new']) && isset( $_POST['id_po'] ) )
{
	$rd	= new receive_product();
	$data	= array(
				"reference"			=> $rd->get_new_reference(dbDate($_POST['date_add'])),
				"invoice"				=> $_POST['invoice'],
				"po_reference"	=> $_POST['po_reference'],
				"id_po"				  => $_POST['id_po'],
				"id_employee"		=> getCookie('user_id'),
				"date_add"			=> dbDate($_POST['date_add']),
				"remark"				=> $_POST['remark']
				);

	$rs	= $rd->add($data);
	if($rs)
	{
		echo $rs;
	}else{
		echo "เพิ่มเอกสารไม่สำเร็จ";
	}
}



if( isset($_GET['get_zone'] ) && isset( $_POST['barcode'] ) )
{
	$barcode = trim($_POST['barcode']);
	$qs = dbQuery("SELECT id_zone, zone_name FROM tbl_zone WHERE barcode_zone = '".$barcode."' LIMIT 1 ");
	if(dbNumRows($qs) == 1 )
	{
		list($id, $name) = dbFetchArray($qs);
		echo $id." : ".$name;
	}else{
		echo "fail";
	}
}

if( isset( $_GET['print'] ) && isset( $_GET['id_receive_product'] ) )
{
	$id		= $_GET['id_receive_product'];
	$ro = new receive_product($id);
	$po = new po($ro->id_po);
	$print = new printer();
	echo $print->doc_header();
	$print->add_title("เอกสารรับสินค้าเข้าตามใบสั่งซื้อ");
	$header	= array(
								"เลขที่เอกสาร"=>$ro->reference,
								"เลขที่ใบสั่งซื้อ"=>$ro->po_reference,
								"ผู้ทำรายการ"=>employee_name($ro->id_employee),
								"เลขที่ใบส่งของ"=>$ro->invoice,
								"Supplier"=> supplier_name($po->id_supplier),
								"วันที่"=>thaiDate($ro->date_add)
							);
	$print->add_header($header);
	$detail = $ro->get_saved_items($id);
	$total_row = dbNumRows($detail);
	$config = array("total_row"=>$total_row, "font_size"=>10, "sub_total_row"=>2);
	$print->config($config);
	$row = $print->row;
	$total_page = $print->total_page;
	$total_qty = 0;
	//**************  กำหนดหัวตาราง  ******************************//
	$thead	= array(
						array("ลำดับ", "width:5%; text-align:center; border-top:0px; border-top-left-radius:10px;"),
						array("รหัส", "width:20%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("สินค้า", "width:45%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
						array("จำนวน", "width:10%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("โซน", "width:20%; text-align:center; border-left: solid 1px #ccc; border-top:0px; border-top-right-radius:10px")
						);
	$print->add_subheader($thead);

	//***************************** กำหนด css ของ td *****************************//
	$pattern = array(
							"text-align: center; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px;",
							"border-left: solid 1px #ccc; border-top:0px; text-align:center;",
							"border-left: solid 1px #ccc; border-top:0px; text-align:center"
							);
	$print->set_pattern($pattern);

	//*******************************  กำหนดช่องเซ็นของ footer *******************************//
	$footer	= array(
						array("ผู้ทำรายการ", "","วันที่............................."),
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
					if(!empty($rs)) :
						$data = array( $n, get_product_reference($rs['id_product_attribute']), get_product_name($rs['id_product']), $rs['qty'], get_zone($rs['id_zone']) );
						$total_qty += $rs['qty'];
					else :
						$data = array("", "", "", "","");
					endif;
					echo $print->print_row($data);
					$n++; $i++;
				endwhile;
				echo $print->table_end();
				if($print->current_page == $print->total_page){ $qty = number_format($total_qty); $remark = $ro->remark; }else{ $qty = ""; $remark = ""; }
				$sub_total = array(
						array("<td rowspan='2' style='height:".$print->row_height."mm; border-top: solid 1px #ccc; border-bottom-left-radius:10px; width:60%; font-size:10px;'><strong>หมายเหตุ : </strong>".$remark."</td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc;'><strong>จำนวนรวม</strong></td>
								<td style='width:20%; height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; text-align:right;'>".$qty."</td>"),
						array("<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-bottom:0px;'><strong>มูลค่ารวม</strong></td>
						<td style='height:".$print->row_height."mm; border: solid 1px #ccc; border-right:0px; border-bottom:0px; border-bottom-right-radius:10px; text-align:right;'> - </td>")
						);
			echo $print->print_sub_total($sub_total);
			echo $print->content_end();
			echo $print->footer;
		echo $print->page_end();
		$total_page --; $print->current_page++;
	}
	echo $print->doc_footer();
}

if( isset( $_GET['get_received_product'] ) && isset( $_POST['id_received_product'] ) )
{
	$id 	= $_POST['id_received_product'];
	$re 	= new receive_product($id);
	$data = array();
	$arr 	= array(
						"id"				=> $re->id_receive_product,
						"reference" 	=> $re->reference,
						"date_add"	=> thaiDate($re->date_add),
						"invoice"		=> $re->invoice,
						"po_reference"	=> $re->po_reference,
						"employee"		=> employee_name($re->id_employee),
						"remark"			=> $re->remark
						);
	array_push($data, $arr);

	$no = 1;
	$total_qty = 0;
	$qs = $re->get_items($id);
	while($rs = dbFetchArray($qs) )
	{
		$arr = array(
						"no"						=> $no,
						"product_reference" 	=> get_product_reference($rs['id_product_attribute']),
						"product_name"			=> get_product_name($rs['id_product']),
						"zone"						=> get_zone($rs['id_zone']),
						"qty"						=> number_format($rs['qty']),
						"status"					=> isActived($rs['status'])
						);
		array_push($data, $arr);
		$total_qty += $rs['qty'];
		$no++;
	}
	$arr = array("total_qty"	=> number_format($total_qty));
	array_push($data, $arr);

	echo json_encode($data);
}


if( isset( $_GET['clear_filter'] ) )
{
	deleteCookie('sCode');
	deleteCookie('sInvoice');
	deleteCookie('sPo');
	deleteCookie('sSup');
	deleteCookie('fromDate');
	deleteCookie('toDate');
	echo "done";
}

?>
