<?php 
class receive_product
{

public function __construct($id = "")
{
	if($id != "" )
	{
		$this->get_data($id);
	}
}

public function get_data($id)
{
	$qs = dbQuery("SELECT * FROM tbl_receive_product WHERE id_receive_product = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$this->id_receive_product	= $rs['id_receive_product'];
		$this->reference				= $rs['reference'];
		$this->invoice					= $rs['invoice'];
		$this->po_reference			= $rs['po_reference'];
		$this->id_po					= $rs['id_po'];
		$this->id_employee			= $rs['id_employee'];
		$this->date_add				= $rs['date_add'];
		$this->date_upd				= $rs['date_upd'];
		$this->remark					= $rs['remark'];
		$this->status					= $rs['status'];
	}
}

public function save_add($id)
{
	$i 			= 0;
	$qs 		= $this->get_items($id);
	$rows 	= dbNumRows($qs);
	if($rows > 0 )
	{
		if(!isset($this->reference))
		{
			$this->get_data($id);
		}
		$po = new po($this->id_po);
		if($po->valid != 1 ){ $po->update_status($this->id_po, 2); }
		while($rs = dbFetchArray($qs) ) :
			if($rs['status'] == 0 )
			{
				$rd = update_stock_zone($rs['qty'], $rs['id_zone'], $rs['id_product_attribute']);
				if($rd)
				{
					$rm = $this->insert_movement("in", 1, $rs['id_product_attribute'],$rs['id_warehouse'], $rs['qty'], $this->reference, dbDate($this->date_add, true), $rs['id_zone']);
					$this->receive_item($this->id_po, $rs['id_product_attribute'], $rs['qty']);
					$rx = $this->valid_qty_with_po($this->id_po,$rs['id_product_attribute']);
					if($rx){ $this->change_valid_po_detail($rx); }
					$this->change_item_status($rs['id_receive_product_detail'], 1);
					$i++;
				}
			}
			else if($rs['status'] == 1)
			{
				$rx = $this->valid_qty_with_po($this->id_po,$rs['id_product_attribute']);
				if($rx){ $this->change_valid_po_detail($rx); }
				$i++;
			}
		endwhile;
	}
	$this->valid_po($this->id_po);
	if($i == $rows){ $this->change_status($id, 1); }
	return $i; 
}


public function inPO($id_po, $id_product_attribute)
{
	$qty = 0;
	$qs = dbQuery("SELECT qty FROM tbl_po_detail WHERE id_po = ".$id_po." AND id_product_attribute = ".$id_product_attribute);	
	if( dbNumRows($qs) == 1 )
	{
		list($qty) = dbFetchArray($qs);	
	}
	return $qty;
}

public function add_item(array $data)
{
	$rs = $this->inPO($data['id_po'], $data['id_product_attribute']);
	$qr = 0;
	if($rs)
	{
		$qs = dbQuery("SELECT qty FROM tbl_receive_product_detail WHERE id_receive_product = ".$data['id_receive_product']." AND id_product_attribute = ".$data['id_product_attribute']." AND id_zone = ".$data['id_zone']." AND status = 0");
		if(dbNumRows($qs) == 1 )
		{ 
			$qr = dbQuery("UPDATE tbl_receive_product_detail SET qty = qty + ".$data['qty']." WHERE id_receive_product = ".$data['id_receive_product']." AND id_product_attribute = ".$data['id_product_attribute']." AND id_zone = ".$data['id_zone']." AND status = 0");
		}else{
			$qr = dbQuery("INSERT INTO tbl_receive_product_detail (id_receive_product, id_product, id_product_attribute, qty, id_warehouse, id_zone, id_employee, date_add) VALUES (".$data['id_receive_product'].", ".$data['id_product'].", ".$data['id_product_attribute'].", ".$data['qty'].", ".$data['id_warehouse'].", ".$data['id_zone'].", ".$data['id_employee'].", '".$data['date_add']."')");
		}
	}
	if($qr == 0)
	{
		return "xx";
	}else{
		return "aa";
	}	
}

public function delete_doc($id)
{
	$qs = $this->get_items($id);
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$this->delete_item($rs['id_receive_product_detail']);	
		}
	}
	return dbQuery("DELETE FROM tbl_receive_product WHERE id_receive_product = ".$id);
}

public function delete_item($id)
{
	$rs = $this->isSaved($id);
	if($rs)
	{
		$rd = $this->roll_back_action($id);	
		if($rd)
		{
			return dbQuery("DELETE FROM tbl_receive_product_detail WHERE id_receive_product_detail = ".$id);	
		}else{
			return false;
		}
	}else{
		return dbQuery("DELETE FROM tbl_receive_product_detail WHERE id_receive_product_detail = ".$id);	
	}
}

///// Check if item is saved return id_product_attribute, id_zone and qty in array  else return false ///
public function isSaved($id)
{
	$qs = dbQuery("SELECT id_product_attribute FROM tbl_receive_product_detail WHERE id_receive_product_detail = ".$id." AND status = 1");
	return dbNumRows($qs);
}

public function roll_back_action($id)
{
	$rs = $this->get_item($id);
	if($rs)
	{
		if(!isset($this->reference) )
		{
			$this->get_data($rs['id_receive_product']);	
		}
		$rd = $this->delete_movement($this->reference, $rs['id_product_attribute'], $rs['qty'], $rs['id_zone']);
		if($rd)
		{
			$rx = update_stock_zone($rs['qty']*-1, $rs['id_zone'], $rs['id_product_attribute']);
			if($rx)
			{
				return $this->receive_item($this->id_po, $rs['id_product_attribute'], $rs['qty']*-1);	
			}
		}else{
			return false;
		}
	}		
}

public function get_items($id)
{
	$qs 	= "SELECT rd.id_receive_product_detail, rd.id_receive_product, rd.id_product, rd.id_product_attribute, rd.qty, rd.id_warehouse, rd.id_zone, rd.id_employee, rd.date_add, ";
	$qs	.= "rd.date_upd, rd.status ";
	$qs	.= "FROM tbl_receive_product_detail AS rd ";
	$qs 	.= "LEFT JOIN tbl_product_attribute AS p ON rd.id_product_attribute = p.id_product_attribute ";
	$qs 	.= "LEFT JOIN tbl_size AS s ON p.id_size = s.id_size ";
	$qs	.= "LEFT JOIN tbl_attribute AS a ON p.id_attribute = a.id_attribute ";
	$qs 	.= "WHERE rd.id_receive_product = ".$id." ORDER BY rd.id_product ASC, p.id_color ASC, s.position ASC, a.position ASC";
	$qs 	= dbQuery($qs);
	return $qs;
}

public function get_item($id)
{
	$qs = dbQuery("SELECT * FROM tbl_receive_product_detail WHERE id_receive_product_detail = ".$id);
	if(dbNumRows($qs) == 1 )
	{
		return dbFetchArray($qs);
	}else{
		return false;
	}
}

public function get_saved_items($id)
{
	return dbQuery("SELECT * FROM tbl_receive_product_detail WHERE id_receive_product = ".$id." AND status = 1");
}

public function get_not_save_items($id)
{
	return dbQuery("SELECT * FROM tbl_receive_product_detail WHERE id_receive_product = ".$id." AND status = 0");	
}

public function add(array $data)
{
	$qs = dbQuery("INSERT INTO tbl_receive_product (reference, invoice, po_reference, id_po, id_employee, date_add, remark) VALUES ('".$data['reference']."', '".$data['invoice']."', '".$data['po_reference']."', ".$data['id_po'].", ".$data['id_employee'].", '".$data['date_add']."', '".$data['remark']."')");
	return dbInsertId();
}

public function update($id, array $data)
{
	return dbQuery("UPDATE tbl_receive_product SET invoice = '".$data['invoice']."', po_reference = '".$data['po_reference']."', id_po = ".$data['id_po'].", id_employee = ".$data['id_employee'].", date_add = '".$data['date_add']."', remark = '".$data['remark']."' WHERE id_receive_product = ".$id);
}

public function change_status($id, $status)
{
	return dbQuery("UPDATE tbl_receive_product SET status = ".$status." WHERE id_receive_product = ".$id);	
}

public function change_item_status($id, $status)
{
	return dbQuery("UPDATE tbl_receive_product_detail SET status = ".$status." WHERE id_receive_product_detail = ".$id);	
}

public function total_qty($id)
{
	$qty = 0;
	$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_receive_product_detail WHERE status = 1 AND id_receive_product = ".$id);
	if(dbNumRows($qs) == 1 )
	{ 
		list($qty)	= dbFetchArray($qs);
	}
	return $qty;
}

public function total_amount($id)
{
	$sc = 0;
	$qs = dbQuery("SELECT tbl_receive_product_detail.qty, tbl_product_attribute.cost FROM tbl_receive_product_detail JOIN tbl_product_attribute ON tbl_receive_product_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE tbl_receive_product_detail.status = 1 AND id_receive_product = ".$id);
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchObject($qs) )
		{
			$sc += ($rs->qty * $rs->cost);	
		}
	}
	return $sc;
}


public function insert_movement($move, $reason, $id_product_attribute, $id_warehouse, $qty, $reference, $date_upd, $id_zone = 0 )
{
	if( $move == "in" )
	{
		return dbQuery("INSERT INTO tbl_stock_movement (id_reason, id_product_attribute, id_warehouse, move_in, reference, date_upd, id_zone) VALUES (".$reason.", ".$id_product_attribute.", ".$id_warehouse.", ".$qty.", '".$reference."', '".$date_upd."', ".$id_zone.")");
	}
	else if( $move == "out" )
	{
		return dbQuery("INSERT INTO tbl_stock_movement (id_reason, id_product_attribute, id_warehouse, move_out, reference, date_upd, id_zone) VALUES (".$reason.", ".$id_product_attribute.", ".$id_warehouse.", ".$qty.", '".$reference."', '".$date_upd."', ".$id_zone.")");
	}
}

function delete_movement($reference, $id_product_attribute, $qty, $id_zone)
{
	$rs = 0;
	$qs = dbQuery("SELECT id_stock_movement FROM tbl_stock_movement WHERE reference = '".$reference."' AND id_product_attribute = ".$id_product_attribute." AND id_zone = ".$id_zone." AND move_in = ".$qty);
	if( dbNumRows($qs) > 0)
	{
		list($id) = dbFetchArray($qs);
		$rs = dbQuery("DELETE FROM tbl_stock_movement WHERE id_stock_movement = ".$id);
	}
	return $rs;
}

public function get_po_item_qty($id_po, $id_product_attribute)
{
	$qty = 0;
	$qs = dbQuery("SELECT qty FROM tbl_po_detail WHERE id_po = ".$id_po." AND id_product_attribute =".$id_product_attribute);
	if( dbNumRows($qs) == 1 )
	{
		list($qty) = dbFetchArray($qs);
	}
	return $qty;
}

public function get_po_received_qty($id_po, $id_product_attribute)
{
	$qty = 0;
	$qs = dbQuery("SELECT received FROM tbl_po_detail WHERE id_po = ".$id_po." AND id_product_attribute = ".$id_product_attribute);
	if(dbNumRows($qs) == 1 )
	{
		list($qty) = dbFetchArray($qs);
	}
	return $qty;
}

public function get_new_reference($date = "")
{
	$prefix = getConfig("PREFIX_RECIEVE");
	if($date == ''){ $date = date("Y-m-d"); }
	$year = date("y", strtotime($date));
	$month = date("m", strtotime($date));
	$qs = dbQuery("SELECT MAX(reference) AS reference FROM tbl_receive_product WHERE reference LIKE '%".$prefix."-".$year.$month."%'");
	$rs = dbFetchArray($qs);
	$str = $rs['reference'];
	if($str !="")
	{
		$ra = explode('-', $str, 2);
		$num = $ra[1];
		$run_num = $num + 1;
		$reference = $prefix."-".$run_num;		
	}else{
		$reference = $prefix."-".$year.$month."00001";
	}
	return $reference;		
}


public function receive_item($id_po, $id_product_attribute, $qty)
{
	return dbQuery("UPDATE tbl_po_detail SET received = received + ".$qty." WHERE id_po = ".$id_po." AND id_product_attribute = ".$id_product_attribute);	
}

public function valid_qty_with_po($id_po, $id_product_attribute)
{
	$qs = dbQuery("SELECT id_po_detail FROM tbl_po_detail WHERE id_po = ".$id_po." AND id_product_attribute = ".$id_product_attribute." AND (received > qty OR received = qty) AND valid = 0");	
	if(dbNumRows($qs) == 1 )
	{
		list($id) = dbFetchArray($qs);
		return $id;
	}else{
		return false;
	}
}

public function change_valid_po_detail($id_po_detail, $i = 1)
{
	return dbQuery("UPDATE tbl_po_detail SET valid = ".$i." WHERE id_po_detail = ".$id_po_detail );	
}

public function valid_po($id_po)
{
	$qs = dbQuery("SELECT id_po_detail FROM tbl_po_detail WHERE id_po = ".$id_po." AND (received < qty)");
	if(dbNumRows($qs) == 0 )
	{
		dbQuery("UPDATE tbl_po SET valid = 1 WHERE id_po = ".$id_po);	
	}
}

}/// end class
?>
