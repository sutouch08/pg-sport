<?php

function get_po_backlog_data($id_po)
{
	$total_qty 						= 0;
	$total_received_qty 			= 0;
	$total_backlog_qty 			= 0;
	$total_po_amount				= 0;
	$total_received_amount		= 0;
	$total_backlog_amount		= 0;
	$qs = dbQuery("SELECT * FROM tbl_po_detail WHERE id_po = ".$id_po);
	while( $rs = dbFetchArray($qs) )
	{
		$qty 			= $rs['qty'];
		$price 		= $rs['price'];
		$received	= $rs['received'];
		$backlog		= $qty - $received;
		$dis			= $rs['discount_percent'] > 0 ? $price * ($rs['discount_percent'] * 0.01) : $rs['discount_amount'] > 0 ? $rs['discount_amount'] : 0 ;
		
		$total_qty					+= $qty;
		$total_received_qty		+= $received;
		$total_backlog_qty 		+= $backlog;
		$total_po_amount 			+= $qty * ($price - $dis);
		$total_received_amount	+= $received * ($price - $dis);
		$total_backlog_amount	+= $backlog * ($price - $dis);
	}
	$data 	= array(
							"po_qty" 					=> $total_qty, 
							"po_amount" 			=> $total_po_amount, 
							"received_qty"			=> $total_received_qty, 
							"received_amount" 	=> $total_received_amount, 
							"backlog_qty"			=> $total_backlog_qty,
							"backlog_amount" 		=> $total_backlog_amount
							);
	return $data;
}

function sum_backlog($id, $id_sup, $from, $to)
{
	$backlog = 0;
	if( $id_sup != 0 )
	{
		$qs = dbQuery("SELECT SUM(qty) AS qty, SUM(received) AS received FROM tbl_po_detail JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po WHERE id_product_attribute = ".$id." AND id_supplier = ".$id_sup." AND tbl_po_detail.valid = 0 AND ( tbl_po.date_add BETWEEN '".$from."' AND '".$to."')");
	}
	else
	{
		$qs = dbQuery("SELECT SUM(qty) AS qty, SUM(received) AS received FROM tbl_po_detail JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po WHERE id_product_attribute = ".$id." AND tbl_po_detail.valid = 0 AND ( tbl_po.date_add BETWEEN '".$from."' AND '".$to."')");
	}
	
	while($rs = dbFetchArray($qs) )
	{
		if(!is_null($rs['qty']))
		{
			if($rs['qty'] > $rs['received'])
			{
				$backlog += $rs['qty'] - $rs['received'];
			}
		}
	}
	return $backlog;	
}

function get_item_po_backlog($id_po, $id_product_attribute)
{
	$backlog = 0;
	$qs = dbQuery("SELECT qty, received FROM tbl_po_detail WHERE id_po = ".$id_po." AND id_product_attribute = ".$id_product_attribute." AND tbl_po_detail.valid = 0");
	if( dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			if($rs['qty'] > $rs['received'])
			{
				$backlog += $rs['qty'] - $rs['received'];
			}
		}
	}
	return $backlog;	
}

function select_po_role($se ='')
{
	$option = '';
	$qs = dbQuery("SELECT * FROM tbl_po_role WHERE active = 1");
	if( $se !='')
	{
		while( $rs = dbFetchArray($qs) )
		{
			$option .= '<option value="'.$rs['id_po_role'].'" '.isSelected($se, $rs['id_po_role']).'>'.$rs['role_name'].'</option>';
		}
	}
	else
	{
		while( $rs = dbFetchArray($qs) )
		{
			$option .= '<option value="'.$rs['id_po_role'].'" '.isSelected(1, $rs['is_default']).'>'.$rs['role_name'].'</option>';	
		}
	}
	return $option;
}

function role_name($id)
{
	$name = '';
	$qs = dbQuery("SELECT role_name FROM tbl_po_role WHERE id_po_role = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		list( $name ) = dbFetchArray($qs);
	}
	return $name;
}
function count_role()
{
	list($c) = dbFetchArray(dbQuery("SELECT COUNT(*) FROM tbl_po_role WHERE active = 1 "));
	return $c;	
}

function getSupplierNameByPO($id_po)
{
	$sup = "";
	$qs 	= dbQuery("SELECT name FROM tbl_supplier JOIN tbl_po ON tbl_supplier.id = tbl_po.id_supplier WHERE id_po = ".$id_po);
	if( dbNumRows($qs) == 1 )
	{
		list($sup) = dbFetchArray($qs);	
	}
	return $sup;
}

function getPoPriceItem($id_po, $id_pa)
{
	$sc = 0;
	$qs = dbQuery("SELECT price FROM tbl_po_detail WHERE id_po = ".$id_po." AND id_product_attribute = ". $id_pa);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);	
	}
	return $sc;
}
?>