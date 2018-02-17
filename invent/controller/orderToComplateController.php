<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
include '../function/order_helper.php';

if( isset( $_GET['prepareOrder']) && isset( $_GET['id_order'] ) )
{
	$sc = 'success';
	$id_order	= $_GET['id_order'];
	if( orderExists($id_order) === TRUE && tempExists($id_order) === FALSE )
	{
		$id_zone		= $_GET['id_zone'];
		$id_wh		= get_warehouse_by_zone($id_zone);
		$id_emp		= getCookie('user_id');
		$date			= getOrderDateAdd($id_order);
		$sumQty		= getOrderQty($id_order);
		$prepared	= 0;
		addState($id_order, 3, $id_emp);
		addState($id_order, 4, $id_emp);
		$qs = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = ".$_GET['id_order']);
		if( dbNumRows($qs) > 0 )
		{
			//---- Start Prepared
			set_time_limit(150);
			while($rs = dbFetchObject($qs) )
			{
				$qty 			= $rs->product_qty;
				$id_pa 		= $rs->id_product_attribute;
				$i 				= 1;
				while($i <= $qty)
				{
					$qr = "INSERT INTO tbl_temp (id_order, id_product_attribute, qty, id_warehouse, id_zone, status, id_employee, date) VALUES ";
					$qr .= "(".$id_order.", ".$id_pa.", 1, ".$id_wh.", ".$id_zone.", 10,".$id_emp.", '".$date."')";
					if( dbQuery($qr) )
					{
						$prepared++;	
					}
					$i++;	
				}
			}
			addState($id_order, 5, $id_emp);			
		}
		if( $prepared == 0 )
		{
			$sc = 'fail';
		}
		echo $sc.' | '.$sumQty.' | '.$prepared;
	}
	else
	{
		echo 	"tempExists | 0 | 0";
	}
}


if( isset( $_GET['qcOrder'] ) && isset( $_GET['id_order'] ) )
{
	$sc = 'success';
	$id_order = $_GET['id_order'];
	if( qcExists($id_order) == FALSE )
	{
		if( tempExists($id_order) === TRUE )
		{
			$id_zone		= $_GET['id_zone'];
			$id_wh		= get_warehouse_by_zone($id_zone);
			$id_emp		= getCookie('user_id');
			$date			= getOrderDateAdd($id_order);
			$temp			= tempOrderQty($id_order, 10);
			addState($id_order, 11, $id_emp);
			$id_box 		= getIdBox($id_order, '001');
			$qs = dbQuery("SELECT * FROM tbl_temp WHERE id_order = ".$id_order." AND status = 10");
			if( dbNumRows($qs) > 0 )
			{
				set_time_limit(150);
				$n = 0;
				while($rs = dbFetchObject($qs) )
				{
					$qr = "INSERT INTO tbl_qc (id_employee, id_order, id_product_attribute, qty, date_upd, valid, error_id, id_box, id_temp) VALUES ";
					$qr .= "(".$id_emp.", ".$id_order.", ".$rs->id_product_attribute.", ".$rs->qty.", '".$rs->date."', 1, 0, ".$id_box.", ".$rs->id_temp.")";
					dbQuery($qr);
					$n++;
				}
				if($temp != $n)
				{
					$sc = "checked ".$n."/".$temp;
				}
				addState($id_order, 10, $id_emp);
			}
			else
			{
				$sc = "temp is empty";	
			}
		}
		else
		{
			$sc = "temp not exists";	
		}
	}
	else
	{
		$sc = "ออเดอร์ถูกตรวจไปแล้ว";	
	}
	echo $sc;
}

//----- เปิดบิล
if( isset( $_GET['billOrder'] ) && isset( $_GET['id_order'] ) )
{
	$sc = 'success';
	$id_order = $_GET['id_order'];
	
	if( soldExists($id_order) === FALSE )
	{
		$id_zone	= $_GET['id_zone'];
		$id_wh	= get_warehouse_by_zone($id_zone);
		$order 	= new order($id_order);
		$product	= new product();
		$ref		= $order->reference;
		$id_cus	= $order->id_customer;
		$id_sale	= get_id_sale_by_customer($id_cus);
		$role		= $order->role;
		$id_emp	= $order->id_employee;
		$qs = dbQuery('SELECT id_product_attribute, SUM(qty) AS qty FROM tbl_qc WHERE id_order = '.$id_order.' AND valid = 1 GROUP BY id_product_attribute');
		$row		= dbNumRows($qs);
		if( $row > 0 )
		{
			$n 	= 0;
			while( $rs = dbFetchObject($qs) )
			{
				$id_pa 	= $rs->id_product_attribute;
				$qty	 	= $rs->qty;
				$ds		= get_detail_from_order($id_order, $id_pa);
				if( $ds !== FALSE )
				{
					$id_pd 		= $ds['id_product'];
					$p_name		= $ds['product_name'];	
					$p_ref		= $ds['product_reference'];
					$order_qty	= $ds['product_qty'];
					$barcode	= $ds['barcode'];
					$price		= $ds['product_price'];
					$p_dis		= $ds['reduction_percent'];
					$a_dis		= $ds['reduction_amount'];
					$dis_amount	= $p_dis > 0 ? $qty * ($price * ($p_dis * 0.01)) : $qty * $a_dis;
					$final_price	= $p_dis > 0 ? $price - ($price * ($p_dis * 0.01)) : $price - $a_dis;
					$total_amount	= $qty * $final_price;
					$cost 		= $product->get_product_cost($id_pa);
					$total_cost	= $qty * $cost;	
				
					$qr = "INSERT INTO tbl_order_detail_sold ";
					$qr .= "(id_order, reference, id_role, id_customer, id_employee, id_sale, id_product, ";
					$qr .= "id_product_attribute, product_name, product_reference, barcode, product_price, ";
					$qr .= "order_qty, sold_qty, reduction_percent, reduction_amount, discount_amount, final_price, total_amount, date_upd, cost, total_cost)";
					$qr .= " VALUES ";
					$qr .= "(".$id_order.", '".$ref."', ".$role.", ".$id_cus.", ".$id_emp.", ".$id_sale.", ".$id_pd.", ".$id_pa.", '".$p_name."', '".$p_ref."', '".$barcode."', ";
					$qr .= $price.", ".$order_qty.", ".$qty.", ".$p_dis.", ".$a_dis.", ".$dis_amount.", ".$final_price.", ".$total_amount.", '".$order->date_add."', ".$cost.", ".$total_cost.")";
					
					if( dbQuery($qr) )
					{
						$rb = stock_movement("out", 2, $id_pa, $id_wh, $qty, $order->reference, $order->date_add, $id_zone);
						updateTempStatus($id_order, $id_pa, 10, 4);
						$n++;
					}
					
				} // end if
			}// end while
			
			if( $n != $row )
			{
				$sc = "บันทึกขาย ".$n."/".$row." รายการ";
			}
			addState($id_order, 9, $id_emp);
			changeOrderState($id_order, 9);
			changeValidOrder($id_order,0);
			changeValidOrderDetail($id_order, 1);
		}		
	}
	else
	{
		$sc = "มีการบันทึกขายไปแล้ว";	
	}
	echo $sc;
}


if( isset( $_GET['checkOrder'] ) && isset( $_GET['id_order'] ) )
{
	$sc = 'success';
	$id_order 	= $_GET['id_order'];
	$id_zone		= $_GET['id_zone'];
	$order		= orderExists($id_order);
	$temp			= tempExists($id_order);
	$zone			= zoneExists($id_zone);
	if( isOrderCancled($id_order) === FALSE )
	{
		if( $order === FALSE OR $temp === TRUE OR $zone === FALSE )
		{
			$od = $order === FALSE ? 'ไม่พบออเดอร์ <br/>' : '';
			$tm = $temp === TRUE ? 'ออเดอร์ถูกจัดไปแล้ว <br/>' : '';
			$zn = $zone === FALSE ? 'ไม่พบโซน <br/>' : '';
			$sc = $od . $tm . $zn;
		}
	}
	else
	{
		$sc = "ออเดอร์ถูกยกเลิกไปแล้ว<br/>";	
	}
	echo $sc;		
}

function orderExists($id_order)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_order FROM tbl_order WHERE id_order = " . $id_order);
	if( dbNumRows($qs) == 1 )
	{
		$sc = TRUE;	
	}
	return $sc;
}

function tempExists($id_order)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_temp FROM tbl_temp WHERE id_order = " . $id_order);
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;
	}
	return $sc;
}

function qcExists($id_order)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_qc FROM tbl_qc WHERE id_order = ".$id_order);
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;	
	}
	return $sc;
}

function soldExists($id_order)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_order_detail_sold FROM tbl_order_detail_sold WHERE id_order = ".$id_order);
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;
	}
	return $sc;
}

function zoneExists($id_zone)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_zone FROM tbl_zone WHERE id_zone = " . $id_zone);
	if( dbNumRows($qs) == 1 )
	{
		$sc = TRUE;	
	}
	return $sc;
}
function getOrderDateAdd($id)
{
	$sc = date('Y-m-d H:i:s');
	$qs = dbQuery("SELECT date_add FROM tbl_order WHERE id_order = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		list($sc) = dbFetchArray($qs);
	}
	return $sc;		
}

function getOrderQty($id)
{
	$sc = 0;
	$qs = dbQuery("SELECT SUM(product_qty) AS qty FROM tbl_order_detail WHERE id_order = ".$id);
	list($qty) = dbFetchArray($qs);
	if( ! is_null($qty) )
	{
		$sc = $qty;
	}
	return $sc;
}

function tempOrderQty($id_order, $status)
{
	$sc = 0;
	$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_temp WHERE id_order= ".$id_order." AND status = ".$status);
	list( $qty ) = dbFetchArray($qs);
	if( ! is_null($qty) )
	{
		$sc = $qty;	
	}
	return $sc;
}

function updateTempStatus($id_order, $id_pa, $f_status, $t_status)
{
	return dbQuery("UPDATE tbl_temp SET status = ".$t_status." WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa." AND status = ".$f_status);
}

function addState($id_order, $state, $id_emp)
{
	return dbQuery("INSERT INTO tbl_order_state_change (id_order, id_order_state, id_employee) VALUES (".$id_order.", ".$state.", ".$id_emp.")");	
}

function getIdBox($id_order, $barcode)
{
	$sc = 0;
	$qs = dbQuery("SELECT id_box FROM tbl_box WHERE id_order = ".$id_order." AND barcode = '".$barcode."'");
	if( dbNumRows($qs) > 0 )
	{
		list($sc) = dbFetchArray($qs);
	}
	else
	{
		$qr = dbQuery("INSERT INTO tbl_box (barcode, id_order) VALUES ('".$barcode."', ".$id_order.")");
		$sc = dbInsertId();	
	}
	return $sc;
}

function changeValidOrder($id_order, $valid)
{
	return dbQuery("UPDATE tbl_order SET valid = ".$valid." WHERE id_order = ".$id_order);	
}

function changeValidOrderDetail($id_order, $valid)
{
	return dbQuery("UPDATE tbl_order_detail SET valid_detail = ".$valid." WHERE id_order = ".$id_order);
}

function changeOrderState($id_order, $state)
{
	return dbQuery("UPDATE tbl_order SET current_state = ".$state." WHERE id_order = ".$id_order);	
}

function isOrderCancled($id_order)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_order FROM tbl_order WHERE id_order = ".$id_order." AND current_state = 8");
	if( dbNumRows($qs) == 1 )
	{
		$sc = TRUE;	
	}
	return $sc;
}
?>