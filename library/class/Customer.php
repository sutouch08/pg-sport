<?php 
//require SRV_ROOT."library/database.php";
class customer{
	public $id_customer;
	public $customer_code;
	public $id_default_group;	
	public $id_sale;
	public $id_gender;
	public $company ="";
	public $first_name = '';
	public $last_name ="";
	public $full_name ="";
	public $email ="";
	public $password="";
	public $birthday="";
	public $credit_amount=0;
	public $credit_term=0;
	public $credit_used = 0;
	public $credit_balance =0;
	public $total_spent =0;
	public $total_order_place =0;
	public $active;
	public $date_add;
	public $date_upd;
	public $last_login;
	public $date_order_first;
	public $sponsor_amount;
	public $sponsor_used;
	public $sponsor_balance;
	public $sponsor_reference;
	public $total_sponsor_place;
	public $sponsor_start;
	public $sponsor_end;
	public $address1 = "";
	public $address2 = "";
	public $city = "";
	public $phone = "";
	public $in_period; /// true or false
	public $error_message;
public function __construct($id_customer = 0)	{
	if($id_customer != 0)
	{
		$qs = dbQuery("SELECT * FROM tbl_customer WHERE id_customer = ".$id_customer);
		if( dbNumRows($qs) == 1 )
		{
			$rs 							= dbFetchArray($qs);
			$this->id_customer 		= $rs['id_customer'];
			$this->customer_code 	= $rs['customer_code'];
			$this->id_default_group 	= $rs['id_default_group'];
			$this->id_sale 				= $rs['id_sale'];
			$this->id_gender 			= $rs['id_gender'];
			$this->company 			= $rs['company'];
			$this->first_name 			= $rs['first_name'];
			$this->last_name 			= $rs['last_name'];
			$this->full_name 			= trim($rs['first_name'])." ".trim($rs['last_name']);
			$this->email 				= $rs['email'];
			$this->password 			= $rs['password'];
			$this->birthday 			= $rs['birthday'];
			$this->credit_amount 		= $rs['credit_amount'];
			$this->credit_term 		= $rs['credit_term'];
			$this->active 				= $rs['active'];
			$this->date_add 			= $rs['date_add'];
			$this->date_upd 			= $rs['date_upd'];
			$this->last_login 			= $rs['last_login'];
		}
	}
}


public function total_bill_discount($id_customer, $from, $to) /// ยอดรวมส่วนลดท้ายบิลแยกตามลูกค้า ในช่วงเวลาที่กำหนด
{
	$discount = 0;
	$qs = dbQuery("SELECT id_order FROM tbl_order_detail_sold WHERE id_role = 1 AND id_customer = ".$id_customer." AND (date_upd BETWEEN '".$from."' AND '".$to."') GROUP BY id_order");
	$row = dbNumRows($qs);
	if($row > 0 )
	{
		$i = 1;
		$in ="";
		while($rs = dbFetchArray($qs))
		{
			$in .= $rs['id_order'];
			if($i<$row){ $in .= ", "; }
			$i++;
		}
		$qr = dbQuery("SELECT SUM(discount_amount) AS amount FROM tbl_order_discount WHERE id_order IN(".$in.")");
		if(dbNumRows($qr) == 1 )
		{
			$ro = dbFetchArray($qr);
			$discount += $ro['amount'];
		}
	}		
	return $discount;	
}

//********************** เพิ่มลูกค้าใหม่ ********************************//	
public function add(array $data){
		list($customer_code, $id_default_group, $id_sale, $id_gender, $company, $first_name, $last_name, $email, $password, $birthday, $credit_amount, $credit_term, $active) = $data;
		if($password !=""){ $password = md5($password); }
		if(!$this->check_email($email)){ // เช็คว่าอีเมล์ซ้ำกับคนอื่นหรือเปล่า
			return false;
		}else if(!$this->check_customer_code($customer_code)){ // เช็คว่ารหัสลูกค้าซ้ำกับคนอื่นหรือเปล่า	
			return false;
		}else{
		$sql = dbQuery("INSERT INTO tbl_customer(customer_code, id_default_group, id_sale, id_gender, company, first_name, last_name, email, password, birthday, credit_amount, credit_term, active, date_add, 
		date_upd) VALUES ('$customer_code', $id_default_group, $id_sale, $id_gender, '$company', '$first_name', '$last_name', '$email', '$password', '$birthday', $credit_amount, $credit_term, $active, NOW(), NOW())");
		}
		if($sql){
			list($id_customer) = dbFetchArray(dbQuery("SELECT id_customer FROM tbl_customer WHERE first_name = '$first_name' AND last_name = '$last_name' AND email = '$email'"));
			$this->id_customer = $id_customer;
			return true;
		}else{
			$this->error_message = "เพิ่มลูกค้าไม่สำเร็จ";
			return false;
		}
}


//******************************************  แก้ไขลูกค้า  **********************************//
public function edit(array $data){
		list($id_customer, $customer_code, $id_default_group, $id_sale, $id_gender, $company, $first_name, $last_name, $email, $password, $birthday, $credit_amount, $credit_term, $active) = $data;
		if($password !=""){ $password = md5($password); $pass = ", password = '$password'"; }else{ $pass = ""; }
		if(!$this->check_email($email, $id_customer)){
			return false;
		}else if(!$this->check_customer_code($customer_code, $id_customer)){
			return false;
		}else{
		$sql = dbQuery("UPDATE tbl_customer SET customer_code = '$customer_code', id_default_group = $id_default_group, id_sale = '$id_sale', id_gender = $id_gender, company = '$company', 
			first_name = '$first_name', last_name = '$last_name', email = '$email' $pass , birthday = '$birthday', credit_amount = $credit_amount, credit_term = $credit_term, active = $active, date_upd = NOW() 
			WHERE id_customer = $id_customer");
		}
		if($sql){
			return true;
		}else{
			$this->error_message = "แก้ไขข้อมูลลูกค้าไม่สำเร็จ";
			return false;
		}
}


//************************************  ลบลูกค้า ****************************//
public function delete($id_customer){
	$valid = 0;
	//**** ตรวจสอบ transection *****
	$or = dbNumRows(dbQuery("SELECT id_customer FROM tbl_order WHERE id_customer = $id_customer"));
	$cs = dbNumRows(dbQuery("SELECT id_customer FROM tbl_order_consign WHERE id_customer = $id_customer"));
	$valid = $valid + $or + $cs;
	if($valid>0){ // ถ้ามี transection ไม่อณุญาติให้ลบ
		$this->error_message = "ไม่สามารถลบลูกค้าได้เนื่องจากมี ทรานเซ็คชั่นในระบบแล้ว";
		return false;
	}else{
		$sql = dbQuery("DELETE FROM tbl_customer WHERE id_customer = $id_customer");
		$sql = dbQuery("DELETE FROM tbl_customer_group WHERE id_customer = $id_customer");
		$sql = dbQuery("DELETE FROM tbl_customer_discount WHERE id_customer = $id_customer");
		$sql = dbQuery("DELETE FROM tbl_address WHERE id_customer = $id_customer");
		return true;
	}
}



//*******************************  ตรวจสอบ อีเมล์ซ้ำ  *****************************//
public function check_email($email, $id_customer=""){
			if($id_customer !=""){
				$rs = dbNumRows(dbQuery("SELECT email FROM tbl_customer WHERE email = '$email' AND email != '' AND id_customer != $id_customer"));
			}else{
				$rs = dbNumRows(dbQuery("SELECT email FROM tbl_customer WHERE email = '$email' AND email != '' "));
			}
			if($rs>0){ 
				$this->error_message = "อีเมล์ซ้ำ มีอีเมล์นี้ในระบบแล้ว";
				return false; 
			}else{
				 return true; 
			 }
}


/*******************************  ตรวจสอบ รหัสลูกค้าซ้ำ  **************************************/
public function check_customer_code($customer_code, $id_customer=""){
			if($id_customer !=""){
				$rs = dbNumRows(dbQuery("SELECT customer_code FROM tbl_customer WHERE customer_code = '$customer_code' AND customer_code !='' AND id_customer != $id_customer"));
			}else{
				$rs = dbNumRows(dbQuery("SELECT customer_code FROM tbl_customer WHERE customer_code = '$customer_code' AND customer_code !='' "));
			}
			if($rs>0){ 
				$this->error_message = "รหัสซ้ำ มีรหัสนี้นี้ในระบบแล้ว";
				return false; 
			}else{ 
				return true; 
			}	
}

//****************************************  เพิ่มลูกค้าเข้ากล่ม  *****************************************//
public function add_to_group($id_customer, $id_group){
			$sql = dbQuery("INSERT INTO tbl_customer_group(id_customer, id_group) VALUES ($id_customer, $id_group)");
			if($sql){ return true; }else{ return false; }
}

//******************************************  ลบลูกค้าออกจากกลุ่ม  ***********************************//
public function drop_group($id_customer, $id_group, $option ="single"){
			if($option !="all"){
				$sql = dbQuery("DELETE FROM tbl_customer_group WHERE id_customer = $id_customer AND id_group = $id_group");	
			}else{
				$sql = dbQuery("DELETE FROM tbl_customer_group WHERE id_customer = $id_customer");
			}
			if($sql){
				return true;
			}else{
				$this->error_message = "ลบกลุ่มไม่สำเร็จ";
				return false;
			}
}


//***********************************************  เพิ่มส่วนลดให้ลูกค้า  **************************************//
public function add_discount($id_customer, $id_category, $discount =0){
		$sql = dbQuery("INSERT INTO tbl_customer_discount (id_customer, id_category, discount) VALUES ( $id_customer, '$id_category', $discount)");
		if($sql){
			return true;
		}else{
			$this->error_message = "เพิ่มส่วนลดไม่สำเร็จ";
			return false;
		}
}


//***********************************************  แก้ไขส่วนลดลูกค้า  ********************************************//
public function update_discount($id_customer, $id_category, $discount){
		if($this->check_discount($id_customer, $id_category)){
			$sql = dbQuery("UPDATE tbl_customer_discount SET discount = '$discount' WHERE id_customer = $id_customer AND id_category = $id_category");
		}else{
			$sql = dbQuery("INSERT INTO tbl_customer_discount (id_customer, id_category, discount) VALUES ($id_customer, $id_category, '$discount')");
		}
			if($sql){
				return true;
			}else{
				$this->error_message = "แก้ไขส่วนลดไม่สำเร็จ";
				return false;
			}			
}


//**********************************  ลบส่วนลดลูกค้า  *****************************************//
public function drop_discount($id_customer, $id_category, $option = ""){
			if($option =="all"){
				$sql = dbQuery("DELETE FROM tbl_customer_discount WHERE id_customer = $id_customer");
			}else{
				$sql = dbQuery("DELETE FROM tbl_customer_discount WHERE id_customer = $id_customer AND id_category = $id_category");
			}
			if($sql){
				return true;
			}else{
				$this->error_message = "ลบส่วนลดไม่สำเร็จ";
				return false;
			}
}


public function sponsor_detail(){
		$id_customer = $this->id_customer;
		$sqr = dbQuery("SELECT id_sponsor, reference, limit_amount, start, end, remark FROM tbl_sponsor WHERE id_customer = '$id_customer' AND active = 1");
		list($id_sponsor, $reference, $limit_amount, $start, $end, $remark) = dbFetchArray($sqr);	
		$today = date("Y-m-d");
		if($today >= $start && $today <= $end){ $this->in_period = true; }else{ $this->in_period = false; }
		$this->sponsor_amount = $limit_amount;
		$this->sponsor_reference = $reference;
		$this->sponsor_start = $start;
		$this->sponsor_end = $end;
}


public function group()	{ 
	$result = array();
	$sql = dbQuery("SELECT id_group FROM tbl_customer_group WHERE id_customer = '".$this->id_customer."'");
	$row = dbNumRows($sql);
	$i=0;
	while($i<$row){
		list($id) = dbFetchArray($sql);
		array_push($result,$id);
		$i++;
	}
	return $result;
}


public function getDiscount($id_category)	{
	$sqr = dbQuery("SELECT discount FROM tbl_customer_discount WHERE id_category = $id_category AND id_customer = '".$this->id_customer."'");
	if(dbNumRows($sqr) >0){	list($discount) = dbFetchArray($sqr); }else{ $discount = 0 ;}
	return $discount;	
}

public function check_discount($id_customer, $id_category){ //***** return true ถ้ามีส่วนลดอยู่ใน Database
		$rs = dbNumRows(dbQuery("SELECT discount FROM tbl_customer_discount WHERE id_customer = $id_customer AND id_category = $id_category"));
		if($rs>0){ return true; }else{ return false; }
}

public function address($id_customer){
	$data = dbFetchArray(dbQuery("select id_address,id_country,alias,company,firstname,lastname,address1,address2,city,postcode,phone,id_number,other from tbl_address where id_customer = '$id_customer'"));
	list($id_address,$id_country,$alias,$company,$firstname,$lastname,$address1,$address2,$city,$postcode,$phone,$id_number,$other) = $data;
		$this->id_address = $id_address;
		$this->id_country = $id_country;
		$this->alias = $alias;
		$this->address1 = $address1;
		$this->address2 = $address2;
		$this->city = $city;
		$this->postcode = $postcode;
		$this->phone = $phone;
		$this->id_number = $id_number;
		$this->other = $other;
}

public function totalSpent($id_customer, $role)
{
	$sc = 0;
	$qs = dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail_sold WHERE id_customer = ".$id_customer." AND id_role = ".$role);
	list( $amount ) = dbFetchArray($qs);
	if( ! is_null( $amount ) )
	{
		$sc = $amount;
	}
	return $sc;
}

//----------------------- นับจำนวนออเดอร์  ---------------//
public function totalOrder($id_customer, $role)
{
	$sc = 0;
	$qs = dbQuery("SELECT id_order FROM tbl_order WHERE id_customer = ".$id_customer." AND role = ".$role);
	if( dbNumRows($qs) > 0 )
	{
		$sc = dbNumRows($qs);
	}
	return $sc;	
}


public function customer_stat(){
		$id_customer 					= $this->id_customer;
		$this->total_spent 				= $this->totalSpent($id_customer, 1);
		$this->total_order_place 	= $this->totalOrder($id_customer, 1);
		$this->total_sponsor_place	= $this->totalOrder($id_customer, 4);
		$this->credit_used 			= $this->totalSpent($id_customer, 1);
		$this->credit_balance 		= $this->credit_amount - $this->credit_used;
		$this->sponsor_used 		= $this->totalSpent($id_customer, 4);
		$this->sponsor_balance 	= $this->sponsor_amount - $this->sponsor_used;
		
}
	//*********** แสดงยอดซื้อของลูกค้า ตามช่วงที่กำหนด ***********************//
	public function customer_sale_amount($id_customer="",$from="", $to=""){
		if($id_customer ==""){ $id_customer = $this->id_customer; }
		if($from !=="" || $to !==""){
				$from = dbDate($from)." 00:00:00";
				$to = dbDate($to)." 23:59:59"; 
			}else{
				$rang = getMonth();
				$to = $rang['to']." 00:00:00";
				$from = $rang['from']." 23:59:59";
		}
			$sql = dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail_sold WHERE id_customer = '$id_customer' AND id_role IN(1,5) AND (date_upd BETWEEN '$from' AND '$to')");
			$total_amount = 0;
			$row = dbNumRows($sql);
			if($row>0){
				list($amount) = dbFetchArray($sql);
				$total_amount = $amount;
			}
		return $total_amount;
	}
	
}//end class


?>