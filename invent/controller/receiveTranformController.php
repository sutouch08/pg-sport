<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";

if( isset( $_GET['save_edit'] ) && isset( $_POST['id_receive_tranform'] ) )
{
	$rd	= new receive_tranform();
	$rs	= $rd->save_add($_POST['id_receive_tranform']);
	if($rs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}

if( isset( $_GET['save_add'] ) && isset( $_POST['id_receive_tranform'] ) )
{
	$rd	= new receive_tranform();
	$rs	= $rd->save_add($_POST['id_receive_tranform']);
	if($rs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}

//////////////// ลบเอกสาร /////////////////
if( isset( $_GET['delete_doc'] ) && isset( $_GET['id_receive_tranform'] ) )
{
	$id = $_GET['id_receive_tranform'];
	$rd	= new receive_tranform();
	$rs 	= $rd->delete_doc($id);
	if($rs)
	{
		echo "success";
	}else{
		echo "fail";
	}
}


if( isset( $_GET['delete_item'] ) && isset( $_POST['id_receive_tranform_detail'] ) )
{
	$rd 	= new receive_tranform();
	$rs	= $rd->delete_item($_POST['id_receive_tranform_detail']);
	if($rs)
	{
		echo "success";
	}else{ 
		echo "fali";
	}
}


if( isset( $_GET['sum_item'] ) && isset( $_POST['id_receive_tranform'] ) )
{
	$rd 	= new receive_tranform();
	$qs	= $rd->get_items($_POST['id_receive_tranform']);
	if(dbNumRows($qs) > 0 )
	{
		$data = array();
		$n = 1;
		while($rs = dbFetchArray($qs) )
		{
			$arr = array(
						"id"						=>$rs['id_receive_tranform_detail'],
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


if( isset( $_GET['add_item'] ) && isset( $_POST['id_receive_tranform'] ) )
{
	$product		= new product();
	$arr			= $product->check_barcode(trim($_POST['barcode']));
	if($arr['id_product_attribute'] != 0 )
	{
		$id_pro		= $product->getProductId($arr['id_product_attribute']);
		$qty			= $_POST['qty'] * $arr['qty'];
		$rd			= new receive_tranform();
		$data			= array(
								"id_receive_tranform"	=> $_POST['id_receive_tranform'],
								"id_product"				=> $id_pro,
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
			$datax = array(
							"product_code" 	=> get_product_reference($arr['id_product_attribute']),
							"product_name" 	=> get_product_name($id_pro),
							"zone_name"		=> get_zone($_POST['id_zone']),
							"qty"					=> $qty
							);
			echo json_encode($datax);
		}else{
			echo "fail";
		}
	}else{
		echo "barcode_fail";
	}
}

if( isset( $_GET['update'] ) && isset( $_GET['id_receive_tranform'] ) )
{
	$id		= $_GET['id_receive_tranform'];
	$rd	= new receive_tranform();
	$data	= array(
				"order_reference"	=> $_POST['order_reference'],
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


if( isset( $_GET['add_new']) && isset( $_POST['id_employee'] ) )
{
	$rd	= new receive_tranform();
	$data	= array(
				"reference"			=> $rd->get_new_reference(dbDate($_POST['date_add'])),
				"order_reference"	=> $_POST['order_reference'],
				"id_employee"		=> $_POST['id_employee'],
				"date_add"			=> dbDate($_POST['date_add']),
				"remark"				=> $_POST['remark']
				);
	$rs	= $rd->add($data);
	if($rs)
	{
		echo $rs;
	}else{
		echo "fail";
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

if( isset( $_GET['print'] ) && isset( $_GET['id_receive_tranform'] ) )
{
	$id		= $_GET['id_receive_tranform'];
	$ro = new receive_tranform($id);
	$print = new printer();
	echo $print->doc_header();
	$print->add_title("เอกสารรับเข้าสินค้าจากการแปรรูป");
	$header	= array("เลขที่เอกสาร"=>$ro->reference, "เลขที่อ้างอิง"=>$ro->order_reference, "ผู้ทำรายการ"=>employee_name($ro->id_employee), "วันที่"=>thaiDate($ro->date_add));
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
						array("รหัส", "width:15%; text-align:center; border-left: solid 1px #ccc; border-top:0px;"),
						array("สินค้า", "width:50%; text-align:center;border-left: solid 1px #ccc; border-top:0px;"),
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
					if(count($rs) != 0) :
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

if( isset( $_GET['clear_filter'] ) )
{
	setcookie("receive_from_date","",time()-3600,"/");	
	setcookie("receive_to_date","",time()-3600,"/");	
	setcookie("receive_filter","",time()-3600,"/");	
	setcookie("receive_search_text","",time()-3600,"/");	
	echo "success";
}

?>