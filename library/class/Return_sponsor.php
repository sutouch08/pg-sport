<?php
class return_sponsor
{
public $id_return_sponsor;
public $reference;
public $order_reference;
public $id_customer;
public $id_employee;
public $date_add;
public $date_upd;
public $remark;
public $status;

public function __construct($id = "")
{
	if($id != "" )
	{
		$qs = dbQuery("SELECT * FROM tbl_return_sponsor WHERE id_return_sponsor = ".$id);
		if(dbNumRows($qs) == 1 )
		{
			$rs = dbFetchArray($qs);
			$this->id_return_sponsor	= $id;
			$this->reference 				= $rs['reference'];
			$this->order_reference		= $rs['order_reference'];
			$this->id_customer			= $rs['id_customer'];
			$this->id_employee			= $rs['id_employee'];
			$this->date_add				= $rs['date_add'];
			$this->date_upd				= $rs['date_upd'];
			$this->remark					= $rs['remark'];
			$this->status					= $rs['status'];
		}
	}
	return true;
}

public function get_data($id)
{
	$qs = dbQuery("SELECT * FROM tbl_return_sponsor WHERE id_return_sponsor = ".$id);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$this->id_return_sponsor	= $id;
		$this->reference 				= $rs['reference'];
		$this->order_reference		= $rs['order_reference'];
		$this->id_customer			= $rs['id_customer'];
		$this->id_employee			= $rs['id_employee'];
		$this->date_add				= $rs['date_add'];
		$this->date_upd				= $rs['date_upd'];
		$this->remark					= $rs['remark'];
		$this->status					= $rs['status'];
	}
	return true;
}

public function add(array $data)
{
	$sql = "INSERT INTO tbl_return_sponsor (reference, order_reference, id_customer, id_employee, date_add, remark, status ) VALUES ";
	$sql .= "('".$data['reference']."', '".$data['order_reference']."', ".$data['id_customer'].", ".$data['id_employee'].", '".$data['date_add']."', '".$data['remark']."', ".$data['status'].")";	
	$qs = dbQuery($sql);
	if($qs)
	{
		return dbInsertId();
	}else{
		return false;
	}
}

public function update($id_return_sponsor, array $data)
{
	$res = false;
	$this->get_data($id_return_sponsor);
	$total_amount 	= $this->total_return_amount($this->reference) * (-1);  ///  ยอดเงินรวมใน order_detail_sold * -1 เพื่อทำให้เป็น บวก
	$id_budget		= $this->get_id_budget_by_id_order($this->get_id_order_by_reference($this->order_reference, 4));
	$qm				= dbQuery("UPDATE tbl_order_detail_sold SET id_customer = ".$data['id_customer']." WHERE reference = '".$this->reference."'");
	if($qm)
	{
		$qr	= dbQuery("UPDATE tbl_sponsor_budget SET balance = balance - ".$total_amount." WHERE id_sponsor_budget = ".$id_budget);
		if($qr)
		{
			$new_id_budget 	= $this->get_id_budget_by_id_order($this->get_id_order_by_reference($data['order_reference'], 4));
			$qa	= dbQuery("UPDATE tbl_sponsor_budget SET balance = balance + ".$total_amount." WHERE id_sponsor_budget = ".$new_id_budget);
			if($qa)
			{
				$qs = "UPDATE tbl_return_sponsor SET order_reference = '".$data['order_reference']."', id_customer = ".$data['id_customer'].", ";
				$qs .= "date_add = '".$data['date_add']."', remark = '".$data['remark']."' WHERE id_return_sponsor = ".$id_return_sponsor;	
				$rs = dbQuery($qs);
				if($rs)
				{
					$res = true;
				}
			}
		}
	}
	return $res;
}	

public function drop_return($id_return_sponsor)
{
	$qs = dbQuery("DELETE FROM tbl_return_sponsor WHERE id_return_sponsor = ".$id_return_sponsor);
	return $qs;	
}


public function drop_all_detail($id_return_sponsor)
{
	$res	= true;
	$qs	= dbQuery("SELECT id_return_sponsor_detail FROM tbl_return_sponsor_detail WHERE id_return_sponsor = ".$id_return_sponsor);
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$rd = $this->drop_detail($rs['id_return_sponsor_detail']);
			if(!$rd){ $res		= false; }
		}
	}
	return $res;
}

public function drop_detail($id_return_sponsor_detail)
{
	$qs = dbQuery("DELETE FROM tbl_return_sponsor_detail WHERE id_return_sponsor_detail = ".$id_return_sponsor_detail);
	return $qs;	
}

public function add_item($id_return_sponsor, array $data)
{
	$id_product_attribute 	= $data['id_product_attribute'];
	$qty 							= $data['qty'];
	$id_zone 					= $data['id_zone'];
	$date_add 					= $data['date_add'];
	$status						= $data['status'];
	$qs = dbQuery("SELECT qty FROM tbl_return_sponsor_detail WHERE id_product_attribute = ".$id_product_attribute." AND id_return_sponsor = ".$id_return_sponsor." AND id_zone = ".$id_zone." AND status= 0");
	if(dbNumRows($qs) == 1 )
	{
		$qr = dbQuery("UPDATE tbl_return_sponsor_detail SET qty = qty + ".$qty." WHERE id_return_sponsor = ".$id_return_sponsor." AND id_product_attribute = ".$id_product_attribute." AND id_zone = ".$id_zone);
	}else{
		$qr = dbQuery("INSERT INTO tbl_return_sponsor_detail ( id_return_sponsor, id_product_attribute, qty, id_zone, date_add, status) VALUES (".$id_return_sponsor.", ".$id_product_attribute.", ".$qty.", ".$id_zone.", NOW(), 0 )");	
	}
	if($qr)
	{
		$qa = dbQuery("SELECT * FROM tbl_return_sponsor_detail WHERE id_return_sponsor = ".$id_return_sponsor." AND id_product_attribute = ".$id_product_attribute." AND id_zone = ".$id_zone." AND status = 0");
		$rs = dbFetchArray($qa);
		$product		= new product();
		$id_product = $product->getProductId($rs['id_product_attribute']);
		$product_name = $product->product_reference($rs['id_product_attribute'])." : ".$product->product_name($id_product);
		$rs = array(
						"id"=>$rs['id_return_sponsor_detail'], 
						"product"=>$product_name, 
						"qty"=>$qty,
						"zone"=>get_zone($rs['id_zone']), 
						"wh"=>get_warehouse_name_by_id(get_warehouse_by_zone($rs['id_zone'])), 
						"date_add"=>thaiDateTime($rs['date_add']),
						"status"=>$rs['status']
						);
	}else{
		$rs = false;
	}
	return $rs;	
}

public function sum_item($id_return_sponsor)
{
	$qs = dbQuery("SELECT * FROM tbl_return_sponsor_detail WHERE id_return_sponsor = ".$id_return_sponsor);
	$data = array();
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$product = new product();
			$id_product	= $product->getProductId($rs['id_product_attribute']);
			$product_name = $product->product_reference($rs['id_product_attribute'])." : ".$product->product_name($id_product);
			$arr = array(
						"id"=>$rs['id_return_sponsor_detail'],
						"product"=>$product_name,
						"qty"=>$rs['qty'],
						"zone"=>get_zone($rs['id_zone']),
						"wh"=>get_warehouse_name_by_id(get_warehouse_by_zone($rs['id_zone'])), 
						"date_add"=>thaiDateTime($rs['date_add']),
						"status"=>$rs['status'] == 1 ? true : false
						);
			array_push($data, $arr);
		}
	}else{
		$data = false;
	}
	
	return $data;
}

public function delete_item($id_return_sponsor_detail)
{
	$qs = dbQuery("DELETE FROM tbl_return_sponsor_detail WHERE id_return_sponsor_detail = ".$id_return_sponsor_detail);
	return $qs;	
}

public function total_return($id_return_sponsor)
{
	$qty 	= 0;
	$qs 	= dbQuery("SELECT SUM(qty) AS qty FROM tbl_return_sponsor_detail WHERE id_return_sponsor = ".$id_return_sponsor);
	if(dbNumRows($qs) == 1 )
	{
		$rs 	= dbFetchArray($qs);
		$qty 	= $rs['qty'];
	}
	return $qty;
}


public function return_detail($id_return_sponsor)
{
	$qs = dbQuery("SELECT * FROM tbl_return_sponsor_detail WHERE id_return_sponsor = ".$id_return_sponsor);
	return $qs;	
}

public function item_not_save($id_return_sponsor)
{
	$qs = dbQuery("SELECT * FROM tbl_return_sponsor_detail WHERE id_return_sponsor = ".$id_return_sponsor." AND status = 0");
	return $qs;
}

public function item_saved($id_return_sponsor)
{
	$qs = dbQuery("SELECT * FROM tbl_return_sponsor_detail WHERE id_return_sponsor = ".$id_return_sponsor." AND status = 1");
	return $qs;	
}

public function set_item_status($id_return_sponsor_detail, $status)
{
	$qs = dbQuery("UPDATE tbl_return_sponsor_detail SET status = ".$status." WHERE id_return_sponsor_detail = ".$id_return_sponsor_detail);
	return $qs;
}

public function set_return_status($id_return_sponsor, $status)
{
	$qs = dbQuery("UPDATE tbl_return_sponsor SET status = ".$status." WHERE id_return_sponsor = ".$id_return_sponsor);
	return $qs;	
}

/////////  เพิ่มรายการลงใน order_detail_sold //////
public function save_item(array $data)
{
	$qs = dbQuery("INSERT INTO tbl_order_detail_sold (id_order, reference, id_role, id_customer, id_employee, id_sale, id_product, id_product_attribute,  product_name, product_reference, barcode, product_price, order_qty, sold_qty, reduction_percent, reduction_amount, discount_amount, final_price, total_amount) VALUES (".$data['id_order'].", '".$data['reference']."', ".$data['id_role'].", ".$data['id_customer'].", ".$data['id_employee'].", ".$data['id_sale'].", ".$data['id_product'].", ".$data['id_product_attribute'].", '".$data['product_name']."', '".$data['product_reference']."', '".$data['barcode']."', ".$data['product_price'].", ".$data['order_qty'].", ".$data['sold_qty'].", ".$data['reduction_percent'].", ".$data['reduction_amount'].", ".$data['discount_amount'].", ".$data['final_price'].", ".$data['total_amount']." )");
	return $qs;
}

public function insert_stock($id_product_attribute, $id_zone, $qty)
{
	$qs = dbQuery("SELECT qty FROM tbl_stock WHERE id_product_attribute = ".$id_product_attribute." AND id_zone = ".$id_zone);
	if(dbNumRows($qs) == 1 )
	{
		$qr	= dbQuery("UPDATE tbl_stock SET qty = qty + ".$qty." WHERE id_product_attribute = ".$id_product_attribute." AND id_zone = ".$id_zone);	
	}else{
		$qr	= dbQuery("INSERT INTO tbl_stock (id_zone, id_product_attribute, qty) VALUES (".$id_zone.", ".$id_product_attribute.", ".$qty.")");
	}
	return $qr;
}

public function save_add()
{
	$result = true;
	$qs = $this->return_detail($this->id_return_sponsor);
	$id_order	= $this->get_id_order_by_reference($this->order_reference, 4);
	$id_budget	= $this->get_id_budget_by_id_order($id_order);
	if(dbNumrows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$product = new product();
			$id_product = $product->getProductId($rs['id_product_attribute']);
			$product->product_detail($id_product);
			$product->product_attribute_detail($rs['id_product_attribute']);
				if( $this->insert_stock($rs['id_product_attribute'], $rs['id_zone'], $rs['qty']) )
				{
					$sm = stock_movement("in", 1, $rs['id_product_attribute'], get_warehouse_by_zone($rs['id_zone']), $rs['qty'], $this->reference, $this->date_add, $rs['id_zone']);	
					if($sm)
					{
						$this->set_item_status($rs['id_return_sponsor_detail'], 1); //// update_status
						$amount = $product->product_price * $rs['qty'];
						$qm = dbQuery("UPDATE tbl_sponsor_budget SET balance = balance + ".$amount." WHERE id_sponsor_budget = ".$id_budget);
						if(!$qm)
						{
							$result = false;
						}
					}else{
						$result = false;
					}
				}else{
					$result = false;
				}		
		}/// endwhile;
		if( $result ){ 	$this->set_return_status($this->id_return_sponsor, 1); }
	}/// endif;
	return $result;
}

public function drop_data()
{
	///  1. ลด budget	
	$amount = $this->total_return_amount($this->reference);
	$id_order	= $this->get_id_order_by_reference($this->order_reference, 4);
	$id_budget 	= $this->get_id_budget_by_id_order($id_order);
	$qm = dbQuery("UPDATE tbl_sponsor_budget SET balance = balance + ".$amount." WHERE id_sponsor_budget = ".$id_budget);
	
	///  2. ลบ movement
	if($qm)
	{
		$qr = $this->return_movement($this->reference);
		while($rm = dbFetchArray($qr))
		{
			///  3. ลดสต็อก
			$qty 	= $rm['move_in'] * (-1);
			$ra = $this->insert_stock($rm['id_product_attribute'], $rm['id_zone'], $qty);
			if($ra)
			{ 
				$ro = dbQuery("DELETE FROM tbl_stock_movement WHERE id_stock_movement = ".$rm['id_stock_movement']); 
			}
		}
		return true;	
	}else{
		return false;
	}	
}

public function return_movement($reference)
{
	$qs = dbQuery("SELECT id_stock_movement, id_product_attribute, move_in, id_zone FROM tbl_stock_movement WHERE reference = '".$reference."'");
	return $qs;	
}

//////////////////  ยอดรวมสินค้าที่คืนที่ถูกเพิ่มเข้า order_detail_sold (ได้ยอดติดลบกลับมา)
private function total_return_amount($reference)
{
	$amount = 0;
	$qs = dbQuery("SELECT SUM(total_amount) AS amount FROM tbl_order_detail_sold WHERE reference = '".$reference."'");
	$rs = dbFetchArray($qs);
	if($rs['amount'] != NULL )
	{
		$amount	= $rs['amount'];
	}
	return $amount;
}

public function get_id_order_by_reference($reference, $role)
{
	$id		= 0;
	$qs = dbQuery("SELECT id_order FROM tbl_order WHERE reference = '".$reference."' AND role =".$role);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$id = $rs['id_order'];
	}
	return $id;
}

public function get_id_budget_by_id_order($id_order)
{
	$id_budget = 0;
	$qs = dbQuery("SELECT id_budget FROM tbl_order_sponsor WHERE id_order = ".$id_order);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$id_budget	= $rs['id_budget'];
	}
	return $id_budget;
}
	
}
?>