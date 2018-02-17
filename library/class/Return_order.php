<?php 
class return_order{
public $id_return_order;
public $reference;
public $id_return_reason;
public $order_reference;
public $role;
public $id_customer;
public $id_sale;
public $id_employee;
public $date_add;
public $date_upd;
public $remark;
public $status;
public $id_return_order_detail;
public $id_product_attribute;
public $qty;
public $id_zone;
public $detail_date_add;
public $detail_status;
public $error_message;
public function __construct($id_return_order=""){
	if($id_return_order ==""){
		return true;
	}else{
		$sql 							= dbQuery("SELECT * FROM tbl_return_order WHERE id_return_order ='".$id_return_order."'");
		$ro 							= dbFetchArray($sql);
		$this->id_return_order 	= $ro['id_return_order'];
		$this->reference 			= $ro['reference'];
		$this->order_reference	= $ro['order_reference'];
		$this->role					= $ro['role'];
		$this->id_customer 		= $ro['id_customer'];
		$this->id_sale 				= $ro['id_sale'];
		$this->id_employee 		= $ro['id_employee'];
		$this->date_add 			= $ro['date_add'];
		$this->date_upd 			= $ro['date_upd'];
		$this->remark 				= $ro['remark'];
		$this->status 				= $ro['status'];
	}
}

public function get_data($id)
{
	$sql 							= dbQuery("SELECT * FROM tbl_return_order WHERE id_return_order ='".$id."'");
	$ro 							= dbFetchArray($sql);
	$this->id_return_order 	= $ro['id_return_order'];
	$this->reference 			= $ro['reference'];
	$this->order_reference	= $ro['order_reference'];
	$this->role					= $ro['role'];
	$this->id_customer 		= $ro['id_customer'];
	$this->id_sale 				= $ro['id_sale'];
	$this->id_employee 		= $ro['id_employee'];
	$this->date_add 			= $ro['date_add'];
	$this->date_upd 			= $ro['date_upd'];
	$this->remark 				= $ro['remark'];
	$this->status 				= $ro['status'];	
}

public function add(array $data){
	$qs = "INSERT INTO tbl_return_order (reference, order_reference, role, id_customer, id_sale, id_employee, date_add, remark, status) ";
	$qs .= "VALUES ('".$data['reference']."', '".$data['order_reference']."', ".$data['role'].", ".$data['id_customer'].", ".$data['id_sale'].", ".$data['id_employee'].", '".$data['date_add']."', '".$data['remark']."', ".$data['status'].")";
	$rs = dbQuery($qs);
	if($rs)
	{
		return dbInsertId();
	}else{
		return false;
	}
}


public function add_item($id_return_order, array $data)
{
	$this->get_data($id_return_order);
	$id_product_attribute	= $data['id_product_attribute'];
	$order_reference		= $data['order_reference'];
	$qty						= $data['qty'];
	$id_zone					= $data['id_zone'];
	$date_add				= $data['date_add'];
	$status					= $data['status'];
	$id_customer			= $this->id_customer;
	$id_sale					= $this->id_sale;
	$id_employee			= $this->id_employee;
	$qs = dbQuery("SELECT id_return_order_detail FROM tbl_return_order_detail WHERE id_return_order = ".$id_return_order." AND id_product_attribute = ".$id_product_attribute." AND order_reference = '".$order_reference."' AND  id_zone = ".$id_zone." AND status = 0 ");
	if( dbNumRows($qs) == 1	)
	{
		$id		= dbFetchArray($qs);
		$qr = dbQuery("UPDATE tbl_return_order_detail SET qty = qty + ".$qty." WHERE id_return_order_detail = ".$id['id_return_order_detail']);
	}else{
		$qrs 	= "INSERT INTO tbl_return_order_detail (id_return_order, order_reference, id_product_attribute, qty, id_customer, id_sale, id_employee, id_zone, date_add, status)";
		$qrs 	.= " VALUES (".$id_return_order.", '".$order_reference."', ".$id_product_attribute.", ".$qty.", ".$id_customer.", ".$id_sale.", ".$id_employee.", ".$id_zone.", NOW(), ".$status.")";
		$qr = dbQuery($qrs);
	}
	if($qr)
	{
		$qa = dbQuery("SELECT * FROM tbl_return_order_detail WHERE id_return_order = ".$id_return_order." AND id_product_attribute = ".$id_product_attribute." AND order_reference = '".$order_reference."' AND  id_zone = ".$id_zone." AND status = 0");
		$rs = dbFetchArray($qa);
		$product	= new product();
		$id_product = $product->getProductId($rs['id_product_attribute']);
		$product_name = $product->product_reference($rs['id_product_attribute'])." : ".$product->product_name($id_product);
		$rd = array(
						"id"=>$rs['id_return_order_detail'], 
						"product"=>$product_name, 
						"qty"=>$qty,
						"zone"=>get_zone($rs['id_zone']), 
						"order_reference"=>$rs['order_reference'], 
						"date_add"=>thaiDateTime($rs['date_add']),
						"status"=>$rs['status']
						);
	}else{
		$rd = false;	
	}
	return $rd;
}

public function add_item2($id_return_order, array $data)
{
	$this->get_data($id_return_order);
	$id_product_attribute	= $data['id_product_attribute'];
	$order_reference		= $data['order_reference'];
	$qty						= $data['qty'];
	$id_zone					= $data['id_zone'];
	$date_add				= $data['date_add'];
	$status					= $data['status'];
	$id_customer			= $this->id_customer;
	$id_sale					= $this->id_sale;
	$id_employee			= $this->id_employee;
	$product = new product();
	$product->product_attribute_detail($id_product_attribute);
	$product_price 		= $product->product_price;
	$final_price				= $product_price;
	$total_amount			= $product_price * $qty;
	$qs = dbQuery("SELECT id_return_order_detail FROM tbl_return_order_detail WHERE id_return_order = ".$id_return_order." AND id_product_attribute = ".$id_product_attribute." AND order_reference = '".$order_reference."' AND  id_zone = ".$id_zone." AND status = 0 ");
	if( dbNumRows($qs) == 1	)
	{
		$id		= dbFetchArray($qs);
		$qr = dbQuery("UPDATE tbl_return_order_detail SET qty = qty + ".$qty.", total_amount = total_amount + ".$total_amount." WHERE id_return_order_detail = ".$id['id_return_order_detail']);
	}else{
		$qrs 	= "INSERT INTO tbl_return_order_detail (id_return_order, order_reference, id_product_attribute, qty, product_price, final_price, total_amount, id_customer, id_sale, id_employee, id_zone, date_add, status)";
		$qrs 	.= " VALUES (".$id_return_order.", '".$order_reference."', ".$id_product_attribute.", ".$qty.", ".$product_price.", ".$final_price.", ".$total_amount.", ".$id_customer.", ".$id_sale.", ".$id_employee.", ".$id_zone.", NOW(), ".$status.")";
		$qr = dbQuery($qrs);
	}
	if($qr)
	{
		$qa = dbQuery("SELECT * FROM tbl_return_order_detail WHERE id_return_order = ".$id_return_order." AND id_product_attribute = ".$id_product_attribute." AND order_reference = '".$order_reference."' AND  id_zone = ".$id_zone." AND status = 0");
		$rs = dbFetchArray($qa);
		$product	= new product();
		$id_product = $product->getProductId($rs['id_product_attribute']);
		$product_name = $product->product_reference($rs['id_product_attribute'])." : ".$product->product_name($id_product);
		$rd = array(
						"id"=>$rs['id_return_order_detail'], 
						"product"=>$product_name, 
						"qty"=>$qty,
						"price"=>$rs['product_price'],
						"percent"=>$rs['reduction_percent'],
						"amount"=>$rs['reduction_amount'],
						"total_amount"=>$rs['total_amount'],
						"zone"=>get_zone($rs['id_zone']), 
						"order_reference"=>$rs['order_reference'], 
						"date_add"=>thaiDate($rs['date_add']),
						"status"=>$rs['status']
						);
	}else{
		$rd = false;	
	}
	return $rd;
}


public function isSaved($id_return_order_detail)
{
	$st = 0;
	$qs = dbQuery("SELECT status FROM tbl_return_order_detail WHERE id_return_order_detail = ".$id_return_order_detail);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$st = $rs['status'];
	}
	return $st;
}

public function delete_row($id_return_order_detail)
{
	if( $this->isSaved($id_return_order_detail) ) //// ถ้าบันทึกแล้ว
	{
		$item = $this->get_item($id_return_order_detail);
		$this->get_data($item['id_return_order']);
		$stock	= $this->insert_stock($item['id_product_attribute'], $item['id_zone'], $item['qty']*(-1));  ////  ลดสต็อก
		if($stock)
		{
			/// ลบ movement
			$movement = dbQuery("DELETE FROM tbl_stock_movement WHERE reference = '".$this->reference."' AND id_product_attribute = ".$item['id_product_attribute']." AND id_zone = ".$item['id_zone']);
			if($movement)
			{	
				return dbQuery("DELETE FROM tbl_return_order_detail WHERE id_return_order_detail = ".$id_return_order_detail);
			}
		}else{
			return false;
		}
	}else{
		return dbQuery("DELETE FROM tbl_return_order_detail WHERE id_return_order_detail = ".$id_return_order_detail);	
	}
}


public function update($id_return_order, array $data)
{
	$sql = "";
	$n = count($data);
	$i = 1;
	foreach($data as $key=>$val)
	{
		$sql .= $key." = '".$val."'";
		if($i<$n){ $sql .= ", "; }
		$i++;
	}
	return dbQuery("UPDATE tbl_return_order SET ".$sql." WHERE id_return_order = ".$id_return_order);	
}

public function total_return($id_return_order)
{
	$qty = 0;
	$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_return_order_detail WHERE id_return_order = ".$id_return_order);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$qty = $rs['qty'];
	}
	return $qty;
}

public function get_item($id_return_order_detail)
{
	$qs = dbQuery("SELECT * FROM tbl_return_order_detail WHERE id_return_order_detail = ".$id_return_order_detail);
	return dbFetchArray($qs);
}

public function return_detail($id_return_order, $status = '')
{
	if($status !='')
	{
		return dbQuery("SELECT * FROM tbl_return_order_detail WHERE id_return_order = ".$id_return_order." AND status = ".$status);
	}else{
		return dbQuery("SELECT * FROM tbl_return_order_detail WHERE id_return_order = ".$id_return_order);	
	}
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
	drop_zero();
	return $qr;
}

public function set_item_status($id_return_order_detail, $status)
{
	$qs = dbQuery("UPDATE tbl_return_order_detail SET status = ".$status." WHERE id_return_order_detail = ".$id_return_order_detail);
	return $qs;
}

public function set_return_status($id_return_order, $status)
{
	$qs = dbQuery("UPDATE tbl_return_order SET status = ".$status." WHERE id_return_order = ".$id_return_order);
	return $qs;	
}

public function drop_data($id_return_order, $status = 1)
{
	$this->get_data($id_return_order);
	$qs	= $this->return_detail($id_return_order, $status);  //// เอาเฉพาะรายการที่ 1 = บันทึกแล้ว 0 = ยังไม่บันทึก  '' = ทุกรายการ
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )	
		{
			////// ลดสต็อก
			$stock = $this->insert_stock($rs['id_product_attribute'], $rs['id_zone'], $rs['qty']*(-1));
			if($stock)
			{
				/// ลบ movement
				$movement = dbQuery("DELETE FROM tbl_stock_movement WHERE reference = '".$this->reference."' AND id_product_attribute = ".$rs['id_product_attribute']." AND id_zone = ".$rs['id_zone']);
			}// end if $stock
		}// end while
	}//end if num_rows
	return true;
}

public function drop_all_detail($id_return_order)
{
	return dbQuery("DELETE FROM tbl_return_order_detail WHERE id_return_order = ".$id_return_order);
}

public function drop_return($id_return_order)
{
	return dbQuery("DELETE FROM tbl_return_order WHERE id_return_order = ".$id_return_order);	
}


public function save_add()
{
	$result = true;
	$qs = $this->return_detail($this->id_return_order);
	if(dbNumrows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$product = new product();
			$id_product = $product->getProductId($rs['id_product_attribute']);
			$product->product_detail($id_product);
			$product->product_attribute_detail($rs['id_product_attribute']);
			$detail = $this->get_sold_detail($rs['order_reference'], $rs['id_product_attribute']);
			$total_amount = $rs['qty']*$detail['final_price'];
			$sql = "UPDATE tbl_return_order_detail SET product_price = ".$detail['product_price'].", reduction_percent = ".$detail['reduction_percent'].", reduction_amount = ".$detail['reduction_amount'].", final_price = ".$detail['final_price'];
			$sql .= ", total_amount = ".$total_amount.", id_customer = ".$this->id_customer.", id_sale = ".$this->id_sale.", id_employee = ".$this->id_employee." WHERE id_return_order_detail = ".$rs['id_return_order_detail']; 
			if(dbQuery($sql) )  //// 
			{
				if( $this->insert_stock($rs['id_product_attribute'], $rs['id_zone'], $rs['qty']) )
				{
					$sm = stock_movement("in", 1, $rs['id_product_attribute'], get_warehouse_by_zone($rs['id_zone']), $rs['qty'], $this->reference, $this->date_add, $rs['id_zone']);	
					if($sm)
					{
						$this->set_item_status($rs['id_return_order_detail'], 1); //// update_status
					}else{
						$result = false;
					}
				}else{
					$result = false;
				}
			}else{
				$result = false;
			}			
		}/// endwhile;
		if( $result ){ 	$this->set_return_status($this->id_return_order, 1); }
	}/// endif;
	return $result;
}

public function save_add2()
{
	$result = true;
	$qs = $this->return_detail($this->id_return_order);
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
					$this->set_item_status($rs['id_return_order_detail'], 1); //// update_status
				}else{
					$result = false;
				}
			}else{
				$result = false;
			}		
		}/// endwhile;
		if( $result ){ 	$this->set_return_status($this->id_return_order, 1); }
	}/// endif;
	return $result;
}
public function item_returned($id_product_attribute, $order_reference)
{
	$qty = 0;
	$qr = dbQuery("SELECT sum(qty) as qty FROM tbl_return_order_detail WHERE id_product_attribute = ".$id_product_attribute." AND order_reference = '".$order_reference."' AND status = 1");
	if(dbNumRows($qr) == 1 )
	{
		$rs = dbFetchArray($qr);
		$qty	= $rs['qty'];
	}
	return $qty;
}

public function returned_qty($id_return_order_detail)
{
	$qty = 0;
	$qs = dbQuery("SELECT qty FROM tbl_return_order_detail WHERE id_return_order_detail = ".$id_return_order_detail);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$qty = $rs['qty'];
	}
	return $qty;
}

public function select_last_item_order($id_product_attribute, $id_customer, $reference = '', $id_return_order_detail = '', $limit = 10)
{
	$option = "";
	$qs = dbQuery("SELECT reference, sold_qty, reduction_percent, reduction_amount FROM tbl_order_detail_sold WHERE id_customer = ".$id_customer." AND id_product_attribute = ".$id_product_attribute." AND id_role = 1 AND sold_qty > 0 ORDER BY reference DESC LIMIT ".$limit);
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{	
			$returned = $this->item_returned($id_product_attribute, $rs['reference']);
			$qty	= $rs['sold_qty'] - $returned;
			if( $rs['reduction_percent'] > 0 ){ $discount = $rs['reduction_percent']." %"; }else if($rs['reduction_amount'] > 0){ $discount = $rs['reduction_amount']." ฿"; }else{ $discount = 0.00; }
			if($reference == $rs['reference']){ 
				$qty += $this->returned_qty($id_return_order_detail);
				$option .= "<option value='".$rs['reference']." : ".$qty."' selected >".$rs['reference']." : ".$qty." : ".$discount."</option>";
			}else{
				$option .= "<option value='".$rs['reference']." : ".$qty."' >".$rs['reference']." : ".$qty." : ".$discount."</option>";
			}
		}
	}else{
		$option .= "<option value='none : 0'>--- ไม่มีเอกสารที่ตรงกัน  ---</option>";
	}
	return $option;
}

public function get_final_price($reference, $id_product_attribute)
{
	$price = 0;
	$qs = dbQuery("SELECT final_price FROM tbl_order_detail_sold WHERE reference = '".$reference."' AND id_product_attribute = ".$id_product_attribute);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$price = $rs['final_price'];
	}else{
		$product = new product();
		$product->product_attribute_detail($id_product_attribute);
		$price 	= $product->product_price;
	}
	return $price;
}

public function get_sold_detail($reference, $id_product_attribute)
{
	$d_sold = array();
	$qs = dbQuery("SELECT * FROM tbl_order_detail_sold WHERE reference = '".$reference."' AND id_product_attribute = ".$id_product_attribute);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$d_sold['product_price']			= $rs['product_price'];
		$d_sold['reduction_percent']		= $rs['reduction_percent'];
		$d_sold['reduction_amount']		= $rs['reduction_amount'];
		$d_sold['final_price']				= $rs['final_price'];
	}else{
		$product = new product();
		$product->product_attribute_detail($id_product_attribute);
		$price 	= $product->product_price;
		$d_sold['product_price']			= $price;
		$d_sold['reduction_percent']		= 0;
		$d_sold['reduction_amount']		= 0;
		$d_sold['final_price']				= $price;
	}
	return $d_sold;
}

public function get_return_amount_by_sale($id_sale, $from, $to)
{
	$rs = 0;
	$qs = dbQuery("SELECT SUM(total_amount) FROM tbl_return_order_detail  WHERE id_sale = ".$id_sale." AND (date_add BETWEEN '".fromDate($from)."' AND '".toDate($to)."') AND status = 1");
	list($amount) = dbFetchArray($qs);
	if( !is_null($amount) )
	{
		$rs = $amount;
	}
	return $rs;
}

public function get_return_amount_by_customer($id_customer, $from, $to)
{
	$rs = 0;
	$qs = dbQuery("SELECT SUM(total_amount) FROM tbl_return_order_detail WHERE id_customer = ".$id_customer." AND (date_add BETWEEN '".fromDate($from)."' AND '".toDate($to)."')");
	list($amount) = dbFetchArray($qs);
	if(!is_null($amount))
	{
		$rs = $amount;
	}
	return $rs;
}

public function get_return_amount_by_customer_group($id_group, $from, $to)
{
	$rs = 0;
	$qs = dbQuery("SELECT SUM(total_amount) FROM tbl_return_order_detail JOIN tbl_customer ON tbl_return_order_detail.id_customer = tbl_customer.id_customer WHERE id_default_group = ".$id_group." AND (tbl_return_order_detail.date_add BETWEEN '".fromDate($from)."' AND '".toDate($to)."') AND status = 1");
	list($amount) = dbFetchArray($qs);
	if(!is_null($amount))
	{
		$rs = $amount;
	}
	return $rs;
}

}
?>
