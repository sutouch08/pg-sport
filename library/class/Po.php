<?php 
class po
{

public function __construct($id = "")
{
	if($id != "" )
	{
		$qs = dbQuery("SELECT * FROM tbl_po WHERE id_po = ".$id);
		if( dbNumRows($qs) == 1 )
		{
			$rs 						= dbFetchArray($qs);
			$this->id_po			= $rs['id_po'];
			$this->reference		= $rs['reference'];
			$this->id_supplier		= $rs['id_supplier'];
			$this->id_employee	= $rs['id_employee'];
			$this->bill_discount	= $rs['bill_discount'];
			$this->due_date		= $rs['due_date'];
			$this->date_add		= $rs['date_add'];
			$this->date_upd		= $rs['date_upd'];
			$this->status			= $rs['status'];
			$this->valid				= $rs['valid'];
			$this->remark			= $rs['remark'];
			$this->role				= $rs['role'];
		}
	}
}

public function check_reference($reference, $id_po = "")
{
	if($id_po != "")
	{
		$qs = dbQuery("SELECT id_po FROM tbl_po WHERE reference = '".$reference."' AND id_po != ".$id_po." LIMIT 1");
	}
	else
	{
		$qs = dbQuery("SELECT id_po FROM tbl_po WHERE reference = '".$reference."' LIMIT 1");
	}
	return dbNumRows($qs);
}

public function cancle_close($id_po)
{
	startTransection();
	$qs 	= dbQuery("UPDATE tbl_po_detail SET valid = 0 WHERE id_po = ".$id_po);
	$qr 	= dbQuery("UPDATE tbl_po SET valid = 0 WHERE id_po = ".$id_po);
	if($qs && $qr)
	{
		commitTransection();
		return true;
	}
	else
	{
		dbRollback();
		return false;	
	}		
}

public function close_po($id_po)
{
	startTransection();
	$qs 	= dbQuery("UPDATE tbl_po_detail SET valid = 1 WHERE id_po = ".$id_po);
	$qr 	= dbQuery("UPDATE tbl_po SET valid = 1 WHERE id_po = ".$id_po);
	if($qs && $qr)
	{
		commitTransection();
		return true;
	}
	else
	{
		dbRollback();
		return false;	
	}
}

public function delete_po($id_po)
{
	return dbQuery("DELETE FROM tbl_po WHERE id_po = ".$id_po);	
}

public function drop_all_detail($id_po)
{
	return dbQuery("DELETE FROM tbl_po_detail WHERE id_po = ".$id_po);
}

public function drop_detail($id_po_detail)
{
	return dbQuery("DELETE FROM tbl_po_detail WHERE id_po_detail = ".$id_po_detail);	
}

public function drop_different($id_po, array $detail)
{
	$in = "";
	$i	= 1;
	$c = count($detail);
	$error = "";
	foreach($detail as $id=>$val)
	{
		$in .= $id;
		if($i<$c){ $in .= ", "; }
		$i++;
	}
	$qs = dbQuery("SELECT * FROM tbl_po_detail WHERE id_po = ".$id_po." AND id_product_attribute NOT IN(".$in.")");
	
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs))
		{
			if($rs['received'] == 0 )
			{
				$this->drop_detail($rs['id_po_detail']);
			}else{
				$error .= get_product_reference($rs['id_product_attribute'])." , ";
			}
		}
		if($error != "")
		{
			return $error;
		}else{
			return "success";
		}
	}else{
		return "success";
	}
}

public function get_detail($id_po)
{
	$qs = "SELECT id_po_detail, id_po, tbl_po_detail.id_product, tbl_po_detail.id_product_attribute, reference, tbl_po_detail.price, qty, discount_percent, discount_amount, total_discount, total_amount, tbl_po_detail.date_upd, received, valid";
	$qs .= " FROM tbl_po_detail LEFT JOIN tbl_product_attribute ON tbl_po_detail.id_product_attribute = tbl_product_attribute.id_product_attribute ";
	$qs .= "LEFT JOIN tbl_size ON tbl_size.id_size = tbl_product_attribute.id_size LEFT JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute ";
	$qs .= "WHERE id_po = ".$id_po." ORDER BY tbl_po_detail.id_product ASC, tbl_product_attribute.id_color ASC, tbl_size.position ASC, tbl_attribute.position ASC";
	$qs = dbQuery($qs);
	return $qs;	
}

public function add_item(array $data)
{
	$qr  = dbQuery("SELECT id_po_detail FROM tbl_po_detail WHERE id_po = ".$data['id_po']." AND id_product_attribute = ".$data['id_product_attribute']);
	$rows = dbNumRows($qr);
	if( $rows == 1 )
	{
		list($id_po_detail) = dbFetchArray($qr);
		$qs = "UPDATE tbl_po_detail SET price = ".$data['price'].", qty = ".$data['qty'].", discount_percent = ".$data['discount_percent'].", discount_amount = ".$data['discount_amount'].", total_discount = ".$data['total_discount'].", total_amount = ".$data['total_amount'];
		$qs .= " WHERE id_po_detail = ".$id_po_detail;
		return  dbQuery($qs);
		
	}else{
		$qs  = "INSERT INTO tbl_po_detail (id_po, id_product, id_product_attribute, price, qty, discount_percent, discount_amount, total_discount, total_amount) ";
		$qs .= "VALUES (".$data['id_po'].", ".$data['id_product'].", ".$data['id_product_attribute'].", ".$data['price'].", ".$data['qty'].", ".$data['discount_percent'].", ".$data['discount_amount'].", ".$data['total_discount'].", ".$data['total_amount'].")";
		$rs  = dbQuery($qs);
		if($rs)
		{
			return dbInsertId();
		}else{
			return false;
		}
	}
}

public function get_items($id)
{
	return dbQuery("SELECT * FROM tbl_po_detail WHERE id_po = ".$id);	
}

public function get_item($id)
{
	return dbFetchArray( dbQuery("SELECT * FROM tbl_po_detail WHERE id_po_detail = ".$id." LIMIT 1") );
}

public function add(array $data)
{
	$sql = "INSERT INTO tbl_po ( reference, id_supplier, id_employee, due_date, date_add, remark, role ) VALUES ";
	$sql .= "('".$data['reference']."', ".$data['id_supplier'].", ".$data['id_employee'].", '".$data['due_date']."', '".$data['date_add']."', '".$data['remark']."', ".$data['role'].")";
	$qs = dbQuery($sql);
	if( $qs )
	{	
		return dbInsertId(); 
	}else{ 
		return false; 
	}
}

public function update($id, array $data)
{
	$qs = dbQuery("UPDATE tbl_po SET reference = '".$data['reference']."', id_supplier = ".$data['id_supplier'].", id_employee = ".$data['id_employee'].", due_date = '".$data['due_date']."', date_add = '".$data['date_add']."', remark = '".$data['remark']."', role = ".$data['role']." WHERE id_po = ".$id);
	return $qs;
}

public function update_bill_discount($id, $discount)
{
	$qs = dbQuery("UPDATE tbl_po SET bill_discount = ".$discount." WHERE id_po = ".$id);
	return $qs;	
}

public function update_status($id, $status)
{
	$qs = dbQuery("UPDATE tbl_po SET status = ".$status." WHERE id_po = ".$id);
	return $qs;	
}

public function getDiscount($d_percent, $d_amount)
{
	$discount = array();
	if($d_percent != 0)
	{
		$discount['value'] 	= $d_percent;
		$discount['unit']		= "%";	
	}else if($d_amount != 0){
		$discount['value']	= $d_amount;
		$discount['unit']		= "THB";
	}else{
		$discount['value']	= 0;
		$discount['unit']		= "%";
	}
	return $discount;
}

public function total_qty($id)
{
	$qty = 0;
	$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_po_detail WHERE id_po = ".$id);
	if( dbNumRows($qs) == 1 )	
	{
		list($qty)	= dbFetchArray($qs);
	}
	return $qty;
}

public function po_received_qty($id_po)
{
	$qty = 0;
	$qs = dbQuery("SELECT SUM(received) AS received FROM tbl_po_detail WHERE id_po = ".$id_po);
	if( dbNumRows($qs) == 1 )
	{
		list($qty) = dbFetchArray($qs);
	}
	return $qty;
}

public function receive_item($id_po, $id_product_attribute, $qty)
{
	return dbQuery("UPDATE tbl_po_detail SET received = received + ".$qty." WHERE id_po = ".$id_po." AND id_product_attribute = ".$id_product_attribute);	
}

public function supplier_code($id)
{
	$code = "";
	$qs = dbQuery("SELECT code FROM tbl_supplier WHERE id = ".$id);
	if(dbNumRows($qs) == 1 )
	{
		list($code) = dbFetchArray($qs);
	}
	return $code;		
}

public function supplier_name($id)
{
	$name = "";
	$qs = dbQuery("SELECT name FROM tbl_supplier WHERE id = ".$id);
	if(dbNumRows($qs) == 1 )
	{
		list($name) = dbFetchArray($qs);
	}
	return $name;
}

public function get_id_po_by_reference($reference)
{
	$id_po = 0;
	$qs = dbQuery("SELECT reference FROM tbl_po WHERE reference = '".$reference."' LIMIT 1");
	if( dbNumRows($qs) == 1 )
	{
		list($id_po) = dbFetchArray($qs);
	}
	return $id_po;
}
}/// end class
?>
