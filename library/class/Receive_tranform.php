<?php 
class receive_tranform
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
	$qs = dbQuery("SELECT * FROM tbl_receive_tranform WHERE id_receive_tranform = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$this->id_receive_tranform	= $rs['id_receive_tranform'];
		$this->reference				= $rs['reference'];
		$this->order_reference		= $rs['order_reference'];
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
		while($rs = dbFetchArray($qs) ) :
			if($rs['status'] == 0 ) :
				$rd = update_stock_zone($rs['qty'], $rs['id_zone'], $rs['id_product_attribute']);
				if($rd)
				{
					$rm = $this->insert_movement("in", 1, $rs['id_product_attribute'],$rs['id_warehouse'], $rs['qty'], $this->reference, dbDate($this->date_add, true), $rs['id_zone']);
					$this->change_item_status($rs['id_receive_tranform_detail'], 1);
					$i++;
				}
			endif;
		endwhile;
	}
	if($i == $rows){ $this->change_status($id, 1); }
	return $i; 
}


public function add_item(array $data)
{
	$qs = dbQuery("SELECT qty FROM tbl_receive_tranform_detail WHERE id_receive_tranform = ".$data['id_receive_tranform']." AND id_product_attribute = ".$data['id_product_attribute']." AND id_zone = ".$data['id_zone']." AND status = 0");
	if(dbNumRows($qs) == 1 )
	{ 
		return dbQuery("UPDATE tbl_receive_tranform_detail SET qty = qty + ".$data['qty']." WHERE id_receive_tranform = ".$data['id_receive_tranform']." AND id_product_attribute = ".$data['id_product_attribute']." AND id_zone = ".$data['id_zone']." AND status = 0");
	}else{
		return dbQuery("INSERT INTO tbl_receive_tranform_detail (id_receive_tranform, id_product, id_product_attribute, qty, id_warehouse, id_zone, id_employee, date_add) VALUES (".$data['id_receive_tranform'].", ".$data['id_product'].", ".$data['id_product_attribute'].", ".$data['qty'].", ".$data['id_warehouse'].", ".$data['id_zone'].", ".$data['id_employee'].", '".$data['date_add']."')");
	}
}

public function delete_doc($id)
{
	$qs = $this->get_items($id);
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$this->delete_item($rs['id_receive_tranform_detail']);	
		}
	}
	return dbQuery("DELETE FROM tbl_receive_tranform WHERE id_receive_tranform = ".$id);
}

public function delete_item($id)
{
	$rs = $this->isSaved($id);
	if($rs)
	{
		$rd = $this->roll_back_action($id);	
		if($rd)
		{
			return dbQuery("DELETE FROM tbl_receive_tranform_detail WHERE id_receive_tranform_detail = ".$id);	
		}else{
			return false;
		}
	}else{
		return dbQuery("DELETE FROM tbl_receive_tranform_detail WHERE id_receive_tranform_detail = ".$id);	
	}
}

///// Check if item is saved return id_product_attribute, id_zone and qty in array  else return false ///
public function isSaved($id)
{
	$qs = dbQuery("SELECT qty FROM tbl_receive_tranform_detail WHERE id_receive_tranform_detail = ".$id." AND status = 1");
	return dbNumRows($qs);
}

public function roll_back_action($id)
{
	$rs = $this->get_item($id);
	if($rs)
	{
		if(!isset($this->reference) )
		{
			$this->get_data($rs['id_receive_tranform']);	
		}
		$rd = $this->delete_movement($this->reference, $rs['id_product_attribute'], $rs['qty'], $rs['id_zone']);
		if($rd)
		{
			return update_stock_zone($rs['qty']*-1, $rs['id_zone'], $rs['id_product_attribute']);
		}else{
			return false;
		}
	}		
}

public function get_items($id)
{
	return dbQuery("SELECT * FROM tbl_receive_tranform_detail WHERE id_receive_tranform = ".$id." ORDER BY status ASC");
}

public function get_item($id)
{
	$qs = dbQuery("SELECT * FROM tbl_receive_tranform_detail WHERE id_receive_tranform_detail = ".$id);
	if(dbNumRows($qs) == 1 )
	{
		return dbFetchArray($qs);
	}else{
		return false;
	}
}

public function get_saved_items($id)
{
	return dbQuery("SELECT * FROM tbl_receive_tranform_detail WHERE id_receive_tranform = ".$id." AND status = 1");
}

public function add(array $data)
{
	$qs = dbQuery("INSERT INTO tbl_receive_tranform (reference, order_reference, id_employee, date_add, remark) VALUES ('".$data['reference']."', '".$data['order_reference']."', ".$data['id_employee'].", '".$data['date_add']."', '".$data['remark']."')");
	return dbInsertId();
}

public function update($id, array $data)
{
	return dbQuery("UPDATE tbl_receive_tranform SET order_reference = '".$data['order_reference']."', id_employee = ".$data['id_employee'].", date_add = '".$data['date_add']."', remark = '".$data['remark']."' WHERE id_receive_tranform = ".$id);
}

public function change_status($id, $status)
{
	return dbQuery("UPDATE tbl_receive_tranform SET status = ".$status." WHERE id_receive_tranform = ".$id);	
}

public function change_item_status($id, $status)
{
	return dbQuery("UPDATE tbl_receive_tranform_detail SET status = ".$status." WHERE id_receive_tranform_detail = ".$id);	
}

public function total_qty($id)
{
	$qty = 0;
	$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_receive_tranform_detail WHERE id_receive_tranform = ".$id);
	if(dbNumRows($qs) == 1 )
	{ 
		list($qty)	= dbFetchArray($qs);
	}
	return $qty;
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

public function get_new_reference($date = "")
{
	$prefix = getConfig("PREFIX_RECEIVE_TRANFORM");
	if($date == ''){ $date = date("Y-m-d"); }
	$year = date("y", strtotime($date));
	$month = date("m", strtotime($date));
	$qs = dbQuery("SELECT MAX(reference) AS reference FROM tbl_receive_tranform WHERE reference LIKE '%".$prefix."-".$year.$month."%'");
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

}/// end class
?>
