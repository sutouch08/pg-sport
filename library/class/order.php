<?php
class order{
	public $id_order;
	public $reference;
	public $id_customer;
	public $customer_name;
	public $id_employee;
	public $id_sale;
	public $id_cart;
	public $current_state;
	public $current_state_name;
	public $payment;
	public $comment;
	public $valid;
	public $role;
	public $role_name;
	public $date_add;
	public $date_upd;
	public $order_status;
	public $total_product = 0;
	public $total_qty = 0;
	public $total_amount = 0;
	public $state_color;
	public $error_message;
	public $id_order_detail ;
	public $id_product;
	public $id_product_attribute;
	public $product_name;
	public $product_qty;
	public $product_reference;
	public $barcode;
	public $product_price;
	public $reduction_precent;
	public $reduction_amount;
	public $discount_amount;
	public $final_price;

	public function __construct($id_order="")
	{
		if( $id_order != '' )
		{
			$sql = dbQuery("SELECT * FROM tbl_order WHERE id_order=$id_order");
			$order 					= dbFetchArray($sql);
			$this->id_order 		= $order['id_order'];
			$this->reference 		= $order['reference'];
			$this->id_customer 	= $order['id_customer'];
			$this->id_employee 	= $order['id_employee'];
			$this->id_sale 			= $this->getIdSale();
			$this->id_cart 			= $order['id_cart'];
			$this->current_state 	= $order['current_state'];
			$this->payment 		= $order['payment'];
			$this->comment 		= $order['comment'];
			$this->valid 				= $order['valid'];
			$this->role 				= $order['role'];
			list($role_name) 		= dbFetchArray(dbQuery("SELECT role_name FROM tbl_order_role WHERE id_role =".$this->role));
			$this->role_name 		= $role_name;
			list($current_state_name) 	= dbFetchArray(dbQuery("SELECT state_name FROM tbl_order_state WHERE id_order_state =".$this->current_state));
			$this->current_state_name 	= $current_state_name;
			$this->date_add 		= $order['date_add'];
			$this->date_upd 		= $order['date_upd'];
			$this->order_status 	= $order['order_status'];
			$this->getTotalOrder($id_order);
			$this->state_color 	= $this->state_color();
		}
	}

	public function updateDeliveryFee($id, $amount)
	{
		if( ! $this->isFeeExists($id) )
		{
			$qs = dbQuery("INSERT INTO tbl_delivery_fee ( id_order, amount ) VALUES (".$id.", ".$amount.")");
		}
		else
		{
			if( $amount == 0 )
			{
				$qs = dbQuery("DELETE FROM tbl_delivery_fee WHERE id_order = ".$id);
			}
			else
			{
				$qs = dbQuery("UPDATE tbl_delivery_fee SET amount = ".$amount." WHERE id_order = ".$id);
			}
		}
		return $qs;
	}

	public function isFeeExists($id)
	{
		$qs = dbQuery("SELECT id FROM tbl_delivery_fee WHERE id_order = ".$id);
		return dbNumRows($qs);
	}
	//------- ตรวจสอบว่าพนักงานมีการเปิดออเดอร์ค้างไว้หรือไม่----//
	public function checkOrderNotSave($id_employee)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT id_order FROM tbl_order WHERE id_employee = ".$id_employee." AND order_status = 0 AND valid != 2 AND role = 1");
		if( dbNumRows($qs) > 0 )
		{
			list( $sc ) = dbFetchArray($qs);
		}
		return $sc;
	}

	//--------- Add new order ------//
	public function add(array $ds)
	{
		$fields 	= '';
		$values 	= '';
		$n 		= count($ds);
		$i 			= 1;
		foreach( $ds as $key => $val )
		{
			$fields .=	 $key;
			if( $i < $n ){ $fields .= ', '; }
			$values .= "'".$val."'";
			if( $i < $n ){ $values .= ', '; }
			$i++;
		}

		$qs = dbQuery("INSERT INTO tbl_order (".$fields.") VALUES (".$values.")");
		if( $qs )
		{
			return dbInsertId();
		}
		else
		{
			return FALSE;
		}
	}

	//-------------------- Update order -----------------------//
	public function updateOrder($id, array $ds)
	{
		$set = '';
		$n = count($ds);
		$i = 1;
		foreach( $ds as $key => $val )
		{
			$set .= $key." = '".$val."'";
			if( $i < $n ){ $set .= ", "; }
			$i++;
		}
		return dbQuery("UPDATE tbl_order SET ".$set." WHERE id_order = ".$id);
	}

	//---------------------  Add OR Update online order  customer reference --------------//
	public function updateOnlineOrderCustomer ($id_order, $customer)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT id FROM tbl_order_online WHERE id_order = ".$id_order);
		if( dbNumRows($qs) == 1 )
		{
			$rs = dbFetchArray( $qs );
			$sc = dbQuery("UPDATE tbl_order_online SET customer = '".addslashes($customer)."' WHERE id = ".$rs['id']);
		}
		else
		{
			$sc = dbQuery("INSERT INTO tbl_order_online ( id_order, customer ) VALUES (".$id_order.", '".addslashes($customer)."')");
		}
		return $sc;
	}

	//-------------------- Save Order -------------------//
	public function saveOrder($id)
	{
		return dbQuery("UPDATE tbl_order SET order_status = 1 WHERE id_order = ".$id);
	}

	//---------------------- Delete 1 Order detail  ---------------------//
	public function deleteOrderDetail($id)
	{
		return dbQuery("DELETE FROM tbl_order_detail WHERE id_order_detail = ".$id);
	}

	//----------------------- สรุปยอดท้ายบิล ---------------//
	public function getOrderSummary($id)
	{
		$qs = dbQuery("SELECT SUM( discount_amount ) AS disc, SUM( total_amount ) AS amount FROM tbl_order_detail WHERE id_order = ".$id);
		$rs = dbFetchArray($qs);
		$sc = array( "total_price" => $rs['amount'] + $rs['disc'], "total_discount" => $rs['disc'], "total_amount" => $rs['amount'], "bill_discount" => bill_discount($id) );
		return $sc;
	}

	public function qc_qty( $id_order = "")
	{
		$qty = 0;
		if($id_order == ""){ $id_order = $this->id_order; }
		$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_qc WHERE id_order = ".$id_order." AND valid = 1");
		if(dbNumRows($qs) > 0 ){
			$rs = dbFetchArray($qs);
			$qty += $rs['qty'];
		}
		return $qty;
	}

	public function addOnlineAddress($ds)
	{
		$fields 	= '';
		$values 	= '';
		$n 		= count($ds);
		$i 			= 1;
		foreach( $ds as $key => $val )
		{
			$fields .=	 $key;
			if( $i < $n ){ $fields .= ', '; }
			$values .= "'".$val."'";
			if( $i < $n ){ $values .= ', '; }
			$i++;
		}

		$qs = dbQuery("INSERT INTO tbl_address_online (".$fields.") VALUES (".$values.")");
		if( $qs )
		{
			return dbInsertId();
		}
		else
		{
			return FALSE;
		}
	}

	public function updateOnlineAddress($id, $ds)
	{
		$set = '';
		$n = count($ds);
		$i = 1;
		foreach( $ds as $key => $val )
		{
			$set .= $key." = '".$val."'";
			if( $i < $n ){ $set .= ", "; }
			$i++;
		}
		return dbQuery("UPDATE tbl_address_online SET ".$set." WHERE id_address = ".$id);
	}

	public function deleteOnlineAddress($id)
	{
		return dbQuery("DELETE FROM tbl_address_online WHERE id_address = ".$id);
	}

	public function get_detail_total_amount($id_product_attribute, $id_order="")
	{
		$amount = 0;
		if($id_order == ""){ $id_order = $this->id_order; }
		$qs = dbQuery("SELECT total_amount FROM tbl_order_detail WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_product_attribute);
		if(dbNumRows($qs) > 0 )
		{
			$rs = dbFetchArray($qs);
			$amount = $rs['total_amount'];
		}
		return $amount;
	}


	public function isExists($id_order, $id_pa)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT id_order_detail FROM tbl_order_detail WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa);
		if( dbNumRows($qs) == 1 )
		{
			list( $sc ) = dbFetchArray($qs);
		}
		return $sc;
	}

	//---------------------------------  get a state name  ---------------------//
	public function stateName($id)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT state_name FROM tbl_order_state WHERE id_order_state = ".$id);
		if( dbNumRows($qs) == 1 )
		{
			list( $sc ) = dbFetchArray($qs);
		}
		return $sc;
	}

	public function validOrder($id)
	{
		return dbQuery("UPDATE tbl_order SET valid = 1 WHERE id_order = ".$id);
	}

	//-------------------------------------- END NEW CODE  -----------------------------------------------------------//
	public function getDetailOrder($id_order){
		$sql = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = '".$id_order."'");
		return $sql;
	}



	public function productDetail($id_product_attribute, $field){
		$id_order = $this->id_order;
		$sql = dbQuery("SELECT $field FROM tbl_order_detail WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
		$data = dbFetchArray($sql);
		return $data[$field];
	}




	public function getTotalOrder($id_order){
		$sql = dbQuery("SELECT product_qty, discount_amount, total_amount FROM tbl_order_detail WHERE id_order = $id_order");
		$row = dbNumRows($sql);
		$total_qty = 0;
		$total_discount = 0;
		$total_amount = 0;
		while($i = dbFetchArray($sql)){
			$total_qty += $i['product_qty'];
			$total_discount += $i['discount_amount'];
			$total_amount += $i['total_amount'];
		}
		$this->total_product = $row;
		$this->total_qty = $total_qty;
		$this->total_amount = $total_amount;
	}


	public function getIdSale(){
		$sql = dbQuery("SELECT id_sale FROM tbl_customer WHERE id_customer ='".$this->id_customer."'");
		list($id_sale) = dbFetchArray($sql);
		if($id_sale !=""){ $result = $id_sale; }else{ $result = 0; }
		return $result;
	}


	public function currentState(){
		$id_order = $this->id_order;
		$sql = dbQuery("SELECT state_name FROM tbl_order LEFT JOIN tbl_order_state ON tbl_order.current_state = tbl_order_state.id_order_state WHERE id_order = $id_order");
		list($current_state) = dbFetchArray($sql);
		return $current_state;
	}


	public function orderState(){
		$id_order = $this->id_order;
		$sql = dbQuery("SELECT tbl_order_state_change.id_order_state, state_name, first_name, last_name, date_add FROM tbl_order_state_change LEFT JOIN tbl_order_state ON tbl_order_state_change.id_order_state = tbl_order_state.id_order_state LEFT JOIN tbl_employee ON tbl_order_state_change.id_employee = tbl_employee.id_employee WHERE tbl_order_state_change.id_order = $id_order ORDER BY tbl_order_state_change.date_add DESC");
		return $sql;
	}



	public function state_change($id_order, $id_order_state, $id_employee){
	if($id_order_state == 2){
		dbQuery("UPDATE tbl_order SET current_state = $id_order_state, valid = 1 WHERE id_order = $id_order");
	}else if($id_order_state==1){
		dbQuery("UPDATE tbl_order SET current_state = $id_order_state, valid = 0 WHERE id_order = $id_order");
	}else{
		dbQuery("UPDATE tbl_order SET current_state = $id_order_state WHERE id_order = $id_order");
	}
	dbQuery("INSERT INTO tbl_order_state_change SET id_order = $id_order, id_order_state = $id_order_state, id_employee = $id_employee");
	return true;
}


	public function changeQty($id_product_attribute, $qty){
		$id_order = $this->id_order;
		$id_customer = $this->id_customer;
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product, $id_customer);
		$product->product_attribute_detail($id_product_attribute);
		$reduction_percent = $this->productDetail($id_product_attribute,"reduction_percent");
		$reduction_amount = $this->productDetail($id_product_attribute, "reduction_amount");
		$total_amount = $product->product_sell * $qty;
		if($product->discount_type =="percentage"){
		$discount_amount = $qty * ($product->product_price * ($reduction_percent/100)) ;
			}else if($product->discount_type=="cus_percentage"){
						$discount_amount = $qty * ($product->product_price * ($product->dis/100)) ;
			}else{
						$discount_amount = $qty * $reduction_amount;
		}
		$sql = dbQuery("UPDATE tbl_order_detail SET product_qty = $qty, reduction_percent = $reduction_percent, reduction_amount = $reduction_amount, discount_amount = $discount_amount, total_amount = $total_amount WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order");
		return true;
	}
	//-------------------  เพิ่มสินค้าในออเดอร์ขาย -----------------//
	private function insertOrderDetail($id_pa, $qty)
	{
		$sc 		= FALSE;
		$id_order	= $this->id_order;
		$customer 	= new customer($this->id_customer);
		$product	= new product();
		$id_pd		= $product->getProductId($id_pa);
		$product->product_detail($id_pd, $this->id_customer);
		$product->product_attribute_detail($id_pa);
		$barcode 	= $product->barcode;
		$dis_type	= $product->discount_type;
		$p_dis		= $dis_type == 'percentage' ? $product->product_discount1 : ( $dis_type == 'cus_percentage' ? $product->product_discount1 : 0.00 ) ;
		$a_dis		= $dis_type == 'amount'	? $product->product_discount1 : 0.00 ;

		$id			= $this->isExists($this->id_order, $id_pa);  //// ตรวจสอบว่ามีสินค้านี้อยู่ในออเดอร์แล้วหรือยัง ถ้ามีจะได้ id_order_detail กลับมา ถ้าไม่มีจะได้ FALSE;
		if( $id !== FALSE ) //------- มีอยู่แล้ว Update ---//
		{
			$qs = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order_detail = ".$id);
			if( dbNumRows($qs) == 1 )
			{
				//-------------------  คำนวนใหม่ -------//
				$rs 			= dbFetchArray($qs);
				$qty			= $qty + $rs['product_qty'];  //----- เอาของเก่ารวมกับของใหม่
				$total_dis		= $qty * $product->discount;
				$final_price	= $product->product_sell;
				$total_amount 	= $qty * $final_price;
				$q = "UPDATE tbl_order_detail SET product_qty = ".$qty.", reduction_percent = ".$p_dis.", reduction_amount = ".$a_dis.", discount_amount = ".$total_dis.", final_price = ".$final_price.", total_amount = ".$total_amount;
				$q .= " WHERE id_order_detail = ".$id;
				$sc = dbQuery($q);
			}
		}
		else		//-------  ยังไม่มี Insert ใหม่ ----//
		{
			$pName 		= $product->product_name;
			$reference 	= $product->reference;
			$barcode	= $product->barcode;
			$price		= $product->product_price;
			$total_dis	= $qty * $product->discount;
			$final_price	= $product->product_sell;
			$total_amount = $qty * $final_price;
			$q = "	INSERT INTO tbl_order_detail ";
			$q .= "(id_order, id_product, id_product_attribute, product_name, product_qty, product_reference, barcode, product_price, reduction_percent, reduction_amount, discount_amount, final_price, total_amount, valid_detail ) ";
			$q .= "VALUES";
			$q .= " (". $id_order .", ". $id_pd .", ". $id_pa .", '". $pName ."', ". $qty .", '". $reference ."', '". $barcode ."', ". $price .", ". $p_dis .", ". $a_dis .", ".$total_dis.", ".$final_price.", ". $total_amount .", 0 )";
			$sc = dbQuery($q);
		}

		return $sc;
	}




	//-----------------------------  เพิ่มสินค้าในออเดอร์ฝากขาย  ---------------------//
	private function insertConsignmentDetail($id_pa, $qty)
	{
		$sc 			= 	FALSE;
		$id_order	= $this->id_order;
		$customer 	= new customer($this->id_customer);
		$product		= new product();
		$id_pd		= $product->getProductId($id_pa);
		$product->product_detail($id_pd);
		$product->product_attribute_detail($id_pa);
		$barcode 	= $product->barcode;
		$p_dis		= 0.00;
		$a_dis		= 0.00;

		$id				= $this->isExists($this->id_order, $id_pa);  //// ตรวจสอบว่ามีสินค้านี้อยู่ในออเดอร์แล้วหรือยัง ถ้ามีจะได้ id_order_detail กลับมา ถ้าไม่มีจะได้ FALSE;
		if( $id !== FALSE ) //------- มีอยู่แล้ว Update ---//
		{
			$qs = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order_detail = ".$id);
			if( dbNumRows($qs) == 1 )
			{
				//-------------------  คำนวนใหม่ -------//
				$rs 			= dbFetchArray($qs);
				$qty			= $qty + $rs['product_qty'];  //----- เอาของเก่ารวมกับของใหม่
				$total_dis	= $qty * $product->discount;
				$final_price	= $product->product_sell;
				$total_amount = $qty * $final_price;
				$q = "UPDATE tbl_order_detail SET product_qty = ".$qty.", reduction_percent = ".$p_dis.", reduction_amount = ".$a_dis.", discount_amount = ".$total_dis.", final_price = ".$final_price.", total_amount = ".$total_amount;
				$q .= " WHERE id_order_detail = ".$id;
				$sc = dbQuery($q);
			}
		}
		else		//-------  ยังไม่มี Insert ใหม่ ----//
		{
			$pName 		= $product->product_name;
			$reference 	= $product->reference;
			$barcode	= $product->barcode;
			$price		= $product->product_price;
			$total_dis	= $qty * $product->discount;
			$final_price	= $product->product_sell;
			$total_amount = $qty * $final_price;
			$q = "	INSERT INTO tbl_order_detail ";
			$q .= "(id_order, id_product, id_product_attribute, product_name, product_qty, product_reference, barcode, product_price, reduction_percent, reduction_amount, discount_amount, final_price, total_amount, valid_detail ) ";
			$q .= "VALUES";
			$q .= " (". $id_order .", ". $id_pd .", ". $id_pa .", '". $pName ."', ". $qty .", '". $reference ."', '". $barcode ."', ". $price .", ". $p_dis .", ". $a_dis .", ".$total_dis.", ".$final_price.", ". $total_amount .", 0 )";
			$sc = dbQuery($q);
		}

		return $sc;
	}

	/****************************  สำหรับเพิ่มรายการสินค้าในออเดอร์ที่มีลูกค้าเท่านั้น ***************************/
	public function insertDetail($id_pa, $qty)
	{
		switch( $this->role )
		{
			//----------  กรณีขายสินค้า  ------//
			case 1 :
				$rs = $this->insertOrderDetail($id_pa, $qty);
			break;
			//---------  กรณีสปอนเซอร์สโมสร  -------//
			case 4 :
				$rs = $this->insert_support_detail($id_pa, $qty);
			break;
			//----------  กรณีฝากขาย  -----------//
			case 5 :
				$rs = $this->insertConsignmentDetail($id_pa, $qty);
			break;
			//------------  กรณีอภินันท์ของบุคคลภายใน  -----------//
			case 7 :
				$rs = $this->insert_support_detail($id_pa, $qty);
			break;
			default :
				$rs = $this->insertOrderDetail($id_pa, $qty);
			break;
		}
		return $rs;
	}

	//---------------------  สำหรับ เบิก อภินันทนาการ และ สปอนเซอร์สโมสร  --------------------//
	public function insert_support_detail($id_product_attribute, $qty)
	{
		$id_order 		= $this->id_order;
		$product 		= new product();
		$id_product 	= $product->getProductId($id_product_attribute);
		$product->product_attribute_detail($id_product_attribute);
		$product_name	= $product->product_name($id_product);
		$barcode 		= $product->barcode;
		$product_price	= $product->product_price;
		$final_price 		= $product->product_sell;
		$reference 		= $product->reference;
		$total_amount 	= $product->product_sell * $qty;
		$reduction_percent 	= 0.00;
		$reduction_amount 	= 0.00;
		$discount_amount 		= 0.00;
		$qr = dbQuery("SELECT product_qty, discount_amount, total_amount FROM tbl_order_detail WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
		$row = dbNumRows($qr);
		if($row>0)
		{
			list($old_qty, $old_discount_amount, $old_total_amount) = dbFetchArray($qr);
			$qty = $qty + $old_qty;
			$discount_amount = $discount_amount+$old_discount_amount;
			$total_amount = $total_amount+$old_total_amount;
			$qs = dbQuery("UPDATE tbl_order_detail SET product_qty = $qty, reduction_percent = $reduction_percent, reduction_amount = $reduction_amount, discount_amount = $discount_amount, total_amount = $total_amount WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order");
		}
		else
		{
			$qs = dbQuery("INSERT INTO tbl_order_detail
			(id_order, id_product, id_product_attribute, product_name, product_qty, product_reference, barcode, product_price, reduction_percent, reduction_amount, discount_amount, final_price, total_amount, valid_detail ) VALUES
			($id_order, $id_product, $id_product_attribute, '$product_name', $qty, '$reference', '$barcode', $product_price, $reduction_percent, $reduction_amount, $discount_amount, $final_price, $total_amount, 0 )");
		}
		if($qs){
			return true;
		}
		else
		{
			$this->error_message = "เพิ่ม หรือปรับปรุงฐานข้อมูลไม่สำเร็จ";
			return false;
		}
	}


	//---------------------- สำหรับ เบิก ยืม ที่ไม่ไมีลูกค้า  ---------------------------------//
	public function insert_detail($id_product_attribute, $qty){
		$id_order = $this->id_order;
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product);
		$product->product_attribute_detail($id_product_attribute);
		$product_name = $product->product_name;
		$barcode = $product->barcode;
		$product_price = $product->product_price;
		$final_price = $product->product_sell;
		$reference = $product->reference;
		$total_amount = $product->product_sell * $qty;
		$reduction_percent = 0.00;
		$reduction_amount = 0.00;
		$discount_amount = 0.00;
		$qr = dbQuery("SELECT product_qty, discount_amount, total_amount FROM tbl_order_detail WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
				$row = dbNumRows($qr);
				if($row>0){
						list($old_qty, $old_discount_amount, $old_total_amount) = dbFetchArray($qr);
						$qty = $qty + $old_qty;
						$discount_amount = $discount_amount+$old_discount_amount;
						$total_amount = $total_amount+$old_total_amount;
					dbQuery("UPDATE tbl_order_detail SET product_qty = $qty, reduction_percent = $reduction_percent, reduction_amount = $reduction_amount, discount_amount = $discount_amount, total_amount = $total_amount WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order");
					return true;
					}else{
					dbQuery("INSERT INTO tbl_order_detail
					(id_order, id_product, id_product_attribute, product_name, product_qty, product_reference, barcode, product_price, reduction_percent, reduction_amount, discount_amount, final_price, total_amount, valid_detail ) VALUES
					($id_order, $id_product, $id_product_attribute, '$product_name', $qty, '$reference', '$barcode', $product_price, $reduction_percent, $reduction_amount, $discount_amount, $final_price, $total_amount, 0 )");
					return true;
					}
			}

	public function orderProductTable($can_edit = "",$can_delete=""){ //แสดงรายการสินค้า
		 if($this->valid==1 || $this->current_state !=1 && $this->current_state !=3){ $active = "disabled='disabled'";}else{$active = ""; }
		$id_order = $this->id_order;
		$role = $this->role;
		switch($role){
		case 1 :
			$content = "order";
			break;
		case 2 :
			$content = "requisition";
			break;
		case 3 :
			$content = "lend";
			break;
		case 4 :
			$content = "sponsor";
			break;
		case 5 :
			$content = "consignment";
			break;
		case 6 :
			$content = "tranfromation";
			break;
		default :
			$content = "order";
			break;
		}
		$field = "tbl_order_detail.id_order, id_product_attribute, product_reference, product_name, barcode, product_price, product_qty, discount_amount, total_amount,reduction_percent,reduction_amount,id_order_detail";
		$sql = dbQuery("SELECT $field FROM tbl_order_detail LEFT JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order WHERE tbl_order_detail.id_order = $id_order ORDER BY barcode ASC");
		$row = dbNumRows($sql);
		echo"
		<button type='button' id='edit_reduction' class='btn btn-default' $can_edit>แก้ไขส่วนลด</button><a href='#'  data-toggle='modal' data-target='#ModalLogin' $can_edit> <button type='button' id='save_reduction' class='btn btn-default' disabled='disabled' $can_edit>บันทึกส่วนลด</button></a>
		<br>";
		echo "
		<br>
		<table id='product_table' class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'><thead><th style='width:10%'>รูปภาพ</th><th>สินค้า</th><th style='width:10%; text-align:center;'>ราคา</th><th style='width:10%; text-align:center;'>จำนวน</th><th style='width:10%; text-align:center;'>ส่วนลด</th><th style='width:10%; text-align:center;'>จำนวนเงิน</th><th style='width:10% text-align:center;'>การกระทำ</th></thead>";
		if($row>0){
			$discount ="0.00";
			$amount = "0.00";
			$total_amount = "";
			$n = 1;
			while($i = dbFetchArray($sql)){
				$product = new product();
				$total = $i['total_amount'];
				$id_order_detail = $i['id_order_detail'];
				$id_product_attribute = $i['id_product_attribute'];
				$reduction_percent = $i['reduction_percent'];
				$reduction_amount = $i['reduction_amount'];
				$total_price = $i['product_price']*$i['product_qty'];
				if($reduction_amount != "0.00"){
					$reduction = $reduction_amount;
					$unit = "฿";
				}else if($reduction_percent != "0.00"){
					$reduction = $reduction_percent;
					$unit = "%";
				}else{
					$reduction = 0;
					$unit = "%";
				}
				$input_reduction = "<div class='input_reduction' style='display:none;'><div class='input-group input-group-sm'  ><input type='text' class='form-control' id='reduction$n' value='$reduction' /><span class='input-group-addon'>$unit</span>
</div></div>";
				echo"<tr>
				<td style='text-align:center; vertical-align:middle;'><img src='".$product->get_product_attribute_image($i['id_product_attribute'],1)."' /></td>
				<td style='vertical-align:middle;'>".$i['product_reference']." : ".$i['product_name']." : ".$i['barcode']."</td>
				<td style='text-align:center; vertical-align:middle;'>".number_format($i['product_price'],2)."</td>
				<td style='text-align:center; vertical-align:middle;'><p id='qty".$i['id_order'].$i['id_product_attribute']."'>".number_format($i['product_qty'])."</p><input type='text' id='edit_qty".$i['id_order'].$i['id_product_attribute']."' style='display:none;' />
				<input type='hidden' class='form-control' id='id_order_detail$n' name='id_order_detail' value='$id_order_detail' /></td>
				<td style='text-align:center; vertical-align:middle;'><p class='reduction' id='reduction' >$reduction $unit</p>$input_reduction </td>

				<td style='text-align:center; vertical-align:middle;'>".number_format($total,2)." </td>
				<td style='text-align:center; vertical-align:middle;'>";
				if($this->current_state == 1 || $this->current_state == 3 ){
				echo "
				<button type='button' id='edit".$i['id_order'].$i['id_product_attribute']."' class='btn btn-warning' onclick='edit_product(".$i['id_order'].",".$i['id_product_attribute'].") ' $active $can_edit><i class='fa fa-pencil'></i></button>
				<button type='button' id='update".$i['id_order'].$i['id_product_attribute']."' onclick='update(".$i['id_order'].",".$i['id_product_attribute'].")' class='btn btn-default' style='display:none;' $active>Update</button>
				<a href='controller/".$content."Controller.php?delete=y&id_order=".$i['id_order']."&id_product_attribute=".$i['id_product_attribute']."'>
					<button type='button' id='delete".$i['id_order'].$i['id_product_attribute']."' class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ ".$i['product_reference']." : ".$i['product_name']." : ".$i['barcode']." ? '); \" $active $can_delete><i class='fa fa-trash'></i></button>
				</a>"; }
				echo "</td><tr>";
					$discount += $i['discount_amount'];
					$total_amount += $total_price;
					$amount += $i['total_amount'];
					$n++;
			}
			echo" <input type='hidden' id='loop' value='".($n-1)."'><input type='hidden' id='id_order' value='$id_order'>
			<tr id='new_row' style='display:none;'>
			<td>เลือกสินค้า : &nbsp;</td>
			<td><input type='text' id='product' name='product'  style='width:50%;' /><input type='hidden' name='id_product_attribute' id='id_product_attribute' /></td>
			<td id='available' style='text-align:center; vertical-align:middle;'></td><input type='hidden' id='stock_qty' />
			<td style='text-align:center; vertical-align:middle;'><input type='text' name='qty' id='qty' autocomplete='off'/><input type='hidden' id='price' name='price' /><input type='hidden' name='id_cus' id='id_cus' value='".$this->id_customer."' /></td>
			<td id='total' style='text-align:center; vertical-align:middle;'><input type='hidden' name='total_amount' id='total_amount' /></td><td><button type='button' id='add' class='btn btn-default' onclick='add_detail()' $active>เพิ่ม</button></td>
			</tr>
			<tr><input type='hidden' name='new_qty' id='new_qty' /><input type='hidden' name='id_order' id='id_order' value='$id_order'/>
			<td rowspan='3' colspan='4'><button type='button' id='add_product' class='btn btn-success' $active $can_edit><span class='glyphicon glyphicon-plus'></span>เพิ่มสินค้า</button></td>
			<td style='border-left:1px solid #ccc'><b>สินค้า</b></td><td colspan='2' align='right'><b>".number_format($total_amount,2)." </b></td></tr>
			<tr><td style='border-left:1px solid #ccc'><b>ส่วนลด</b></td><td colspan='2' align='right'><b>".number_format($discount,2)." </b></td></tr>
			<tr><td style='border-left:1px solid #ccc'><b>สุทธิ </b></td><td colspan='2' align='right'><b>".number_format($total_amount - $discount,2)." </b></td></tr></table>";

		}else{
			echo" <tr id='new_row' style='display:none;'>
			<td>เลือกสินค้า : &nbsp;</td><input type='hidden' name='id_order' id='id_order' value='$id_order' />
			<td><input type='text' id='product' name='product'  style='width:50%;' /><input type='hidden' name='id_product_attribute' id='id_product_attribute' /></td>
			<td id='available' style='text-align:center; vertical-align:middle;'></td><input type='hidden' id='stock_qty' />
			<td style='text-align:center; vertical-align:middle;'><input type='text' name='qty' id='qty' autocomplete='off'/><input type='hidden' id='price' name='price' /><input type='hidden' name='id_cus' id='id_cus' value='".$this->id_customer."' /></td>
			<td id='total' style='text-align:center; vertical-align:middle;'><input type='hidden' name='total_amount' id='total_amount' /></td><td><button type='button' id='add' class='btn btn-default' onclick='add_detail()' $active>เพิ่ม</button></td>
			</tr>
			<tr><td><button type='button' id='add_product' class='btn btn-success' $active ><span class='glyphicon glyphicon-plus'></span>เพิ่มสินค้า</button></td><td colspan='5' align='center'><h4>ไม่มีรายการสินค้า</h4></td></tr></table>";
		}

	}
	///**********************************  Sale Order Tracking  *****************************************//
		public function saleOrderProductTable(){ //แสดงรายการสินค้า
		 if($this->valid==1 || $this->current_state !=1 && $this->current_state !=3){ $active = "disabled='disabled'";}else{$active = ""; }
		$id_order = $this->id_order;
		$role = $this->role;
		switch($role){
		case 1 :
			$content = "order";
			break;
		case 2 :
			$content = "requisition";
			break;
		case 3 :
			$content = "lend";
			break;
		case 4 :
			$content = "sponsor";
			break;
		case 5 :
			$content = "consignment";
			break;
		default :
			$content = "order";
			break;
		}
		$field = "tbl_order_detail.id_order, id_product_attribute, product_reference, product_name, barcode, product_price, product_qty, discount_amount, total_amount";
		$sql = dbQuery("SELECT $field FROM tbl_order_detail LEFT JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order WHERE tbl_order_detail.id_order = $id_order");
		$row = dbNumRows($sql);
		echo"
		<table id='product_table' class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'><thead><th style='width:10%'>รูปภาพ</th><th>สินค้า</th><th style='width:10%; text-align:center;'>ราคา</th><th style='width:10%; text-align:center;'>จำนวน</th><th style='width:20%; text-align:center;'>จำนวนเงิน</th></thead>";
		if($row>0){
			$discount ="";
			$amount = "";
			$total_amount = "";
			while($i = dbFetchArray($sql)){
				$product = new product();
				$total = $i['product_price']*$i['product_qty'];
				echo"<tr>
				<td style='text-align:center; vertical-align:middle;'><img src='".$product->get_product_attribute_image($i['id_product_attribute'],1)."' /></td>
				<td style='vertical-align:middle;'>".$i['product_reference']." : ".$i['product_name']." : ".$i['barcode']."</td>
				<td style='text-align:center; vertical-align:middle;'>".number_format($i['product_price'],2)."</td>
				<td style='text-align:center; vertical-align:middle;'><p id='qty".$i['id_order'].$i['id_product_attribute']."'>".number_format($i['product_qty'])."</p><input type='text' id='edit_qty".$i['id_order'].$i['id_product_attribute']."' style='display:none;' /></td>
				<td style='text-align:center; vertical-align:middle;'>".number_format($total,2)." </td>
				<tr>";
					$discount += $i['discount_amount'];
					$total_amount += $total;
					$amount += $i['total_amount'];
			}
			echo"
			<tr>
			<td rowspan='3' colspan='3'>&nbsp;</td>
			<td style='border-left:1px solid #ccc'><b>สินค้า</b></td><td colspan='2' align='right'><b>".number_format($total_amount,2)." </b></td></tr>
			<tr><td style='border-left:1px solid #ccc'><b>ส่วนลด</b></td><td colspan='2' align='right'><b>".number_format($discount,2)." </b></td></tr>
			<tr><td style='border-left:1px solid #ccc'><b>สุทธิ </b></td><td colspan='2' align='right'><b>".number_format($amount,2)." </b></td></tr></table>";

		}else{
			echo"
			<tr><td colspan='6' align='center'><h4>ไม่มีรายการสินค้า</h4></td></tr></table>";
		}

	}

	public function order_customer($id_customer,$valid){
		 echo "<div class='col-xs-12 col-sm-12'>
	<table class='table' width='100%'>
    	<thead style='background-color:#48CFAD;'>
        	<th style='width:5%; text-align:center;'>ID</th><th style='width:10%;'>เลขที่อ้างอิง</th>
            <th style='width:10%; text-align:center;'>ยอดเงิน</th>
			<th style='width:15%; text-align:center;'>การชำระเงิน</th><th style='width:10%; text-align:center;'>สถานะ</th>
			<th style='width:10%; text-align:center;'>วันที่สั่ง</th>
			<th style='width:10%; text-align:center;'>วันที่ส่ง</th>
        </thead>";
		$view = "";
		if(isset($_POST['from_date'])){	$from = date('Y-m-d',strtotime($_POST['from_date'])); }else{ $from = "";} if(isset($_POST['to_date'])){  $to =date('Y-m-d',strtotime($_POST['to_date'])); }else{ $to = "";}
		if($from==""){
			if($to==""){
				$view = "week";
			}
		}
		$result = dbQuery("SELECT id_order,reference,payment,current_state,date_add,delivery_date FROM tbl_order WHERE id_customer = '$id_customer' and valid IN ($valid) ORDER BY date_add DESC");
		$i=0;
		$row = dbNumRows($result);
		if($row>0){
		while($i<$row){
			list($id_order, $reference,$payment, $current_state, $date_add, $delivery_date)=dbFetchArray($result);
			list($status) = dbFetchArray(dbQuery("SELECT state_name FROM tbl_order_state WHERE id_order_state = $current_state"));
			list($amount) = dbFetchArray(dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail WHERE id_order = $id_order"));
	echo"<tr style='color:#FFF; background-color:".state_color($current_state).";'>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&detail=y&id_order=$id_order'\">$id_order</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=order&detail=y&id_order=$id_order'\">$reference</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&detail=y&id_order=$id_order'\">"; echo number_format($amount)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&detail=y&id_order=$id_order'\">$payment</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&detail=y&id_order=$id_order'\">$status</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&detail=y&id_order=$id_order'\">"; echo thaiDate($date_add)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=order&detail=y&id_order=$id_order'\">";if($delivery_date == "0000-00-00 00:00:00"){ echo "-";}else{ echo thaiDate($delivery_date);}echo "</td>
			</tr>";
			$i++;
		}
		}else if($row==0){
			echo"<tr><td colspan='9' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;ไม่มีรายการ</h3></td></tr>";
		}
		echo"</table>
        </div>	";
	}
	public function state_color(){
		$current_state = $this->current_state;
		$sql = dbQuery("SELECT color FROM tbl_order_state WHERE id_order_state = $current_state");
		list($color) = dbFetchArray($sql);
		return $color;
	}

	public function getCurrentOrderAmount($id_order=""){
		if($id_order == ""){$id_order = $this->id_order;}
		$amount = 0;
		$qs = dbQuery("SELECT SUM(total_amount) AS amount FROM tbl_order_detail WHERE id_order = $id_order");
		if(dbNumRows($qs) >0){
			list($current_order_amount) = dbFetchArray($qs);
			$amount += $current_order_amount;
		}
		return $amount;
	}

	public function get_final_price($id_product_attribute, $id_order = ""){
		$price = 0;
		if($id_order == ""){ $id_order = $this->id_order; }
		$qs = dbQuery("SELECT final_price FROM tbl_order_detail WHERE id_product_attribute = ".$id_product_attribute." AND id_order = ".$id_order);
		if(dbNumRows($qs) > 0){
			$rs = dbFetchArray($qs);
			$price = $rs['final_price'];
		}
		return $price;
	}

	public function qc_amount($id_order=""){
		$amount = 0;
		if($id_order == ""){ $id_order = $this->id_order; }
		$qs = dbQuery("SELECT id_product_attribute, SUM(qty) AS qty FROM tbl_qc WHERE id_order = ".$id_order." AND valid = 1 GROUP BY id_product_attribute");
		if(dbNumRows($qs) > 0){
			while($rs = dbFetchArray($qs)){
			$price = $this->get_final_price($rs['id_product_attribute']);
			$t_amount = $rs['qty'] * $price;
			$amount += $t_amount;
			}
		}
		return $amount;
	}

	public function check_credit($new_order_amount){
		$id_order = $this->id_order;
		$customer = new customer($this->id_customer);
		//$current_order_amount = $this->getCurrentOrderAmount();
		$credit_used = $customer->credit_used;
		$credit_amount = $customer->credit_amount;
		if(($credit_used + $new_order_amount) > $credit_amount){
			return false;
		}else{
			return true;
		}
	}

	public function check_limit_amount($new_order_amount){
		$id_order = $this->id_order;
		$customer = new customer($this->id_customer);
		//$current_order_amount = $this->getCurrentOrderAmount();
		$customer->sponsor_detail();
		$customer->customer_stat();
		$sponsor_used = $customer->sponsor_used;
		$sponsor_amount = $customer->sponsor_amount;
		if(($sponsor_used + $new_order_amount) > $sponsor_amount){
			return false;
		}else{
			return true;
		}
	}

	//////////////// ***************** แสดงรายละเอียดก่อนพิมพ์ในหน้ารอเปิดบิล ************************/////////////
	public function page_summary($total_order_amount, $total_discount_amount, $net_total, $remark){
		if($total_order_amount !=""){ $total_order_amount = number_format($total_order_amount,2);}
		if($total_discount_amount !=""){ $total_discount_amount = number_format($total_discount_amount,2); }
		if($net_total !=""){ $net_total = number_format($net_total,2); }
		echo"	<tr><td rowspan='3' colspan='3' style='border:solid 1px #AAA;  padding:10px; vertical-align:text-top;'>หมายเหตุ : $remark </td>
				<td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ราคารวม</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding:10px'>".$total_order_amount."</td></tr>
				<tr><td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ส่วนลด</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding:10px'>".$total_discount_amount."</td></tr>
				<tr><td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ยอดเงินสุทธิ</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding:10px'>".$net_total."</td></tr>
				</table>";
	}
	public function page_summary_detail($total_order_amount, $total_discount_amount, $net_total, $remark){
		if($total_order_amount !=""){ $total_order_amount = number_format($total_order_amount,2);}
		if($total_discount_amount !=""){ $total_discount_amount = number_format($total_discount_amount,2); }
		if($net_total !=""){ $net_total = number_format($net_total,2); }
		echo"	<tr><td rowspan='3' colspan='4' style='border:solid 1px #AAA;  padding:10px; vertical-align:text-top;'>หมายเหตุ : $remark </td>
				<td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ราคารวม</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding:10px'>".$total_order_amount."</td></tr>
				<tr><td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ส่วนลด</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding:10px'>".$total_discount_amount."</td></tr>
				<tr><td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ยอดเงินสุทธิ</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding:10px'>".$net_total."</td></tr>
				</table>";
	}
		public function order_detail_table(){ //แสดงรายการสินค้า
		$id_order = $this->id_order;
		$remark = $this->comment;
		$role = $this->role;
		$total_order = ""; ///เก็บยอดรวมเพิ่มทีละรายการเวลาวนลูป
		$total_order_amount = "";///วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
		$total_discount_order =""; //เก็บยอดเงินส่วนลดตอนวนลูป
		$total_discount_amount = ""; //วนลูปจบเอายอดเงินส่วนลดมาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
		$net_total =""; //มูลค่าสินค้าหลังหักส่วนลด
		$sql = dbQuery("SELECT id_order_detail, id_product_attribute, barcode, product_reference, product_name, product_price, product_qty, reduction_percent, reduction_amount, discount_amount, total_amount FROM tbl_order_detail WHERE id_order = $id_order ORDER BY barcode ASC");
		$row = dbNumRows($sql);
		$i=0;
		$n = 1;
		echo"
		<table id='product_table' style='width:100%; padding:10px;'>
		<thead>
			<th style='width:5%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>ลำดับ</th>
			<th style='width:15%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>รหัสสินค้า</th>
			<th style='width:35%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>รหัสสินค้า</th>
			<th style='width:10%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>จำนวน</th>
			<th style='width:10%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>ราคา</th>
			<th style='width:10%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>ส่วนลด</th>
			<th style='width:15%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>จำนวนเงิน</th>
		</thead>";
	if($row>0){
		while($i<$row){
		list($id_order_detail, $id_product_attribute, $barcode, $product_reference, $product_name, $product_price, $product_qty, $discount_percent, $discount_amount, $total_discount, $total_amount)= dbFetchArray($sql);
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product);
		$product->product_attribute_detail($id_product_attribute);
		$total = $product_price * $product_qty;
		if($discount_percent !== 0.00){ $discount = $discount_percent ."%";}else if($discount_amount != 0.00){ $discount = $discount_amount . "฿" ;}
		echo"<tr style='height:12mm;'>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA;'>$n</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA;'>$barcode</td>
		<td style='vertical-align:middle; padding:3px; border: solid 1px #AAA;'>$product_reference : $product_name</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA;'>".number_format($product_price,2)."</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA;'>".number_format($product_qty)."</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; '>$discount</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; ;'>".number_format($total,2)."</td>
		</tr>";
				$total_order += $total;
				$total_discount_order += $total_discount;
				$i++;
				if($n==$row){
				$total_order_amount = $total_order;
				$total_discount_amount = $total_discount_order;
				$net_total = $total_order_amount - $total_discount_amount;
				$this->page_summary($total_order_amount, $total_discount_amount, $net_total, $remark);
					}
				$n++;
			}
		}
	}
	public function order_detail_qc_table(){ //แสดงรายการสินค้า
		$id_order = $this->id_order;
		$remark = $this->comment;
		$role = $this->role;
		$total_discount_amount1 = 0;
		$discount = 0;
		$total_order = ""; ///เก็บยอดรวมเพิ่มทีละรายการเวลาวนลูป
		$total_order_amount = "";///วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
		$total_discount_order =""; //เก็บยอดเงินส่วนลดตอนวนลูป
		$total_discount_amount = ""; //วนลูปจบเอายอดเงินส่วนลดมาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
		$net_total =""; //มูลค่าสินค้าหลังหักส่วนลด
		$sql = dbQuery("SELECT id_order_detail, id_product_attribute, barcode, product_reference, product_name, product_price, product_qty, reduction_percent, reduction_amount, discount_amount, total_amount FROM tbl_order_detail WHERE id_order = $id_order ORDER BY barcode ASC");
		$row = dbNumRows($sql);
		$i=0;
		$n = 1;
		echo"
		<table id='product_table' style='width:100%; padding:10px;'>
		<thead>
			<th style='width:5%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>ลำดับ</th>
			<th style='width:15%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>รหัสสินค้า</th>
			<th style='width:35%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>รหัสสินค้า</th>
			<th style='width:10%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>ราคา</th>
			<th style='width:8%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>จำนวน</th>
			<th style='width:8%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>จำนวนที่ได้</th>
			<th style='width:8%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>ส่วนลด</th>
			<th style='width:12%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>จำนวนเงิน</th>
		</thead>";
	if($row>0){
		while($i<$row){
		list($id_order_detail, $id_product_attribute, $barcode, $product_reference, $product_name, $product_price, $product_qty, $discount_percent, $discount_amount, $total_discount, $total_amount)= dbFetchArray($sql);
		list($qty) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_qc WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order AND valid = 1"));
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product);
		$product->product_attribute_detail($id_product_attribute);
		$total = $product_price * $qty;
		if($discount_percent !== 0.00){ $discount = $discount_percent ."%"; $total_discount_amount1 = ($total/100)*$discount_percent;}else if($discount_amount != 0.00){ $discount = $discount_amount . "฿" ;$total_discount_amount1 = $total-($discount_percent*$qty);}
		echo"<tr style='height:12mm;";if($product_qty != "$qty"){echo "color:red";}echo "'>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA;'>$n</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA;'>$barcode</td>
		<td style='vertical-align:middle; padding:3px; border: solid 1px #AAA;'>$product_reference : $product_name</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA;'>".number_format($product_price,2)."</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA;'>".number_format($product_qty)."</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA;'>".number_format($qty)."</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; '>$discount</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; ;'>".number_format($total,2)."</td>
		</tr>";
				$total_order += $total;
				$total_discount_order += $total_discount_amount1;
				$i++;
				if($n==$row){
				$total_order_amount = $total_order;
				$total_discount_amount = $total_discount_order;
				$net_total = $total_order_amount - $total_discount_amount;
				$this->page_summary_detail($total_order_amount, $total_discount_amount, $net_total, $remark);
					}
				$n++;
			}
		}
	}
	public function order_product_detail($id_product_attribute){
		$id_order = $this->id_order;
		$sql = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute");
		$rs = dbFetchArray($sql);
		$this->id_order_detail = $rs['id_order_detail'];
		$this->id_product = $rs['id_product'];
		$this->id_product_attribute = $rs['id_product_attribute'];
		$this->product_name = $rs['product_name'];
		$this->product_qty = $rs['product_qty'];
		$this->product_reference = $rs['product_reference'];
		$this->barcode = $rs['barcode'];
		$this->product_price = $rs['product_price'];
		$this->reduction_percent = $rs['reduction_percent'];
		$this->reduction_amount = $rs['reduction_amount'];
		$this->discount_amount = $rs['discount_amount'];
		$this->final_price = $rs['final_price'];
		$this->total_amount = $rs['total_amount'];
	}
}//end class


?>
