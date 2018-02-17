<?php 
//-----------------------  ดูปีที่ใช้งานปัจจุบัน
function get_support_current_year($id_support)
{
	$year = '';
	$qs = dbQuery("SELECT year FROM tbl_support WHERE id_support = ".$id_support);
	if(dbNumRows($qs) == 1 )
	{
		$r = dbFetchArray($qs);
		$year = $r['year'];
	}
	return $year;
}



//-------------------------  ดูปีของงบประมาณปัจจุบัน
function get_current_support_budget_year($id_support_budget)
{
	$year = "";
	$qs = dbQuery("SELECT year FROM tbl_support_budget WHERE id_support_budget = ".$id_support_budget);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$year = $rs['year'];
	}
	return $year;
}


//--------------------------  ระยะสัญญา เริ่มต้น / สิ้นสุด
function get_current_support_rank($id_support_budget)
{
	$rank = "";
	$qs = dbQuery("SELECT start, end FROM tbl_support_budget WHERE id_support_budget = ".$id_support_budget);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$rank['start'] = $rs['start'];
		$rank['end']	= $rs['end'];
	}
	return $rank;
}



//---------------------------  id ของผู้ใช้งบ
function get_id_employee_by_id_support($id_support)
{
	$id = "";
	$qs = dbQuery("SELECT id_employee FROM tbl_support WHERE id_support = ".$id_support." LIMIT 1");
	if( dbNumRows($qs) == 1 )
	{
		$r = dbFetchArray($qs);
		$id = $r['id_employee'];
	}
	return $id;
}


//-------------------------  ดูงบประมาณ
function get_support_budget($id_support_budget)
{
	$amount = 0;
	$qs = dbQuery("SELECT limit_amount FROM tbl_support_budget WHERE id_support_budget = ".$id_support_budget);
	if(dbNumRows($qs) > 0)
	{
		$rs = dbFetchArray($qs);
		$amount = $rs['limit_amount'];
	}
	return $amount;
}



function get_id_order_support($id_order)
{
	$id 	= 0;
	$sql 	= dbQuery("SELECT id_order_support FROM tbl_order_support WHERE id_order = ".$id_order);
	if( dbNumRows($sql) > 0 )
	{
		$rs = dbFetchArray($sql);
		$id = $rs['id_order_support'];
	}
	return $id;
}
	

//------------------------  ไอดี ของพนักงานที่ทำรายการ		
function get_id_user_support($id_order)
{
	$id = 0;
	$sql = dbQuery("SELECT id_user FROM tbl_order_support WHERE id_order = ".$id_order);
	$rw = dbNumRows($sql);
	if($rw>0){
		$rs = dbFetchArray($sql);
		$id = $rs['id_user'];
	}
	return $id;
}	



//------------------------  หา id_budget โดยใช้ id_order	
function get_id_support_budget_by_order($id_order)
{
	$id_budget = "";
	$qs = dbQuery("SELECT id_budget FROM tbl_order_support	WHERE id_order = ".$id_order);
	if(dbNumRows($qs) > 0)
	{
		$rs = dbFetchArray($qs);
		$id_budget = $rs['id_budget'];
	}
	return $id_budget;
}



//--------------------  อัพเดตงบคงเหลือ	
function update_support_balance($id_budget, $amount) 
{
	return dbQuery("UPDATE tbl_support_budget SET balance =".$amount." WHERE id_support_budget = ".$id_budget);
	
}



//--------------------  ขอดูยอดงบคงเหลือ
function get_support_balance($id_support_budget)
{
	$amount = 0;
	$qr = dbQuery("SELECT balance FROM tbl_support_budget WHERE id_support_budget = ".$id_support_budget);
	if(dbNumRows($qr) > 0)
	{
		$rs = dbFetchArray($qr);
		$amount = $rs['balance'];
	}
	return $amount;
}



function update_order_support_status($id_order, $status)
{
	//********* 0 = ยังไม่เปิดบิล   1 = เปิดบิลแล้ว   2 = ยกเลิก **********//
	return dbQuery("UPDATE tbl_order_support SET status = ".$status." WHERE id_order = ".$id_order);
}



//--------------------------- ปรับปรุงยอดการใช้งบ
function update_order_support_amount($id_order, $amount)
{
	return dbQuery("UPDATE tbl_order_support SET amount = ".$amount." WHERE id_order = ".$id_order);
}



function get_id_budget_by_id_support($id_support)
{
	$id_budget = 0;
	$year = get_support_active_year($id_support);
	$qs = dbQuery("SELECT id_support_budget FROM tbl_support_budget WHERE id_support = ".$id_support." AND year = '".$year."'");
	if(dbNumRows($qs) > 0 )
	{
		$rs = dbFetchArray($qs);
		$id_budget = $rs['id_support_budget'];
	}
	return $id_budget;
}
	
	
	
function get_id_support_by_employee($id_employee)	
{
	$id_support = 0;
	$qs = dbQuery("SELECT id_support FROM tbl_support WHERE id_employee = ".$id_employee);
	if(dbNumRows($qs) > 0)
	{
		$rs = dbFetchArray($qs);
		$id_support = $rs['id_support'];
	}
	return $id_support;
}



function get_support_active_year($id_support)
{
	$year = "";
	$qs = dbQuery("SELECT year FROM tbl_support WHERE id_support = ".$id_support);
	if(dbNumRows($qs) > 0 )
	{
		$rs = dbFetchArray($qs);
		$year = $rs['year'];
	}
	return $year;
}



function add_support_log($id_support, $id_support_budget, $action_type, $action, $from_value, $to_value)
{
	/// ส่วนของผู้ทำรายการ ///
	$user_name = employee_name($_COOKIE['user_id']); /// ชื่อพนักงานผู้ทำรายการ
	$id_user = $_COOKIE['user_id']; /// id_employee ผู้ทำรายการ
	/// จบส่วนผู้ทำรายการ
	$qs = dbQuery("INSERT INTO tbl_support_log ( id_employee, employee_name, id_support, id_support_budget, action_type, action, from_value, to_value) VALUES (".$id_user.", '".$user_name."', ".$id_support.", ".$id_support_budget.", '".$action_type."', '".$action."', '".$from_value."', '".$to_value."')");
	return $qs;	
}



function getSupportUsed($id_emp, $from, $to)
{
	$sc = 0;
	$qs = dbQuery("SELECT SUM(total_amount) AS amount FROM tbl_order_detail_sold WHERE id_role = 7 AND id_employee = ".$id_emp." AND ( date_upd BETWEEN '".$from."' AND '".$to."')");
	list( $amount ) = dbFetchArray($qs);	
	if( ! is_null( $amount ) )
	{
		$sc = $amount;
	}
	return $sc;
}



function getReturnSupportAmount($id_emp, $from, $to)
{
	$sc = 0;
	$qr = "SELECT id_product_attribute, qty FROM tbl_return_support_detail JOIN tbl_return_support ON tbl_return_support.id_return_support = tbl_return_support_detail.id_return_support ";
	$qr .= "WHERE id_employee = ".$id_emp." AND	 ( tbl_return_support_detail.date_add BETWEEN '".$from."' AND '".$to."' ) AND tbl_return_support_detail.status = 1";
	$qs = dbQuery($qr);
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$sc += getProductPrice($rs['id_product_attribute']) * $rs['qty'];
		}
	}
	return $sc;
}



function getReturnSupportCostAmount($id_emp, $from, $to)
{
	$sc = 0;
	$qr = "SELECT id_product_attribute, qty FROM tbl_return_support_detail JOIN tbl_return_support ON tbl_return_support.id_return_support = tbl_return_support_detail.id_return_support ";
	$qr .= "WHERE id_employee = ".$id_emp." AND	 ( tbl_return_support_detail.date_add BETWEEN '".$from."' AND '".$to."' ) AND tbl_return_support_detail.status = 1";
	$qs = dbQuery($qr);
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$sc += getProductCost($rs['id_product_attribute']) * $rs['qty'];
		}
	}
	return $sc;	
}




function getReturnSupportQtyAndAmount($id_return_support)
{
	$amount	= 0;
	$qty		= 0;
	$qs = dbQuery("SELECT id_product_attribute, qty FROM tbl_return_support_detail WHERE id_return_support = ".$id_return_support);
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$qty 		+= $rs['qty'];
			$amount 	+= getProductPrice($rs['id_product_attribute']) * $rs['qty'];
		}
	}
	return array('qty' => $qty, 'amount' => $amount);
}


function getReturnSupportByEmployee($id_emp, $from, $to)
{
	return dbQuery("SELECT * FROM tbl_return_support WHERE id_employee = ".$id_emp." AND status = 1 AND ( date_add BETWEEN '".$from."' AND '".$to."') ORDER BY date_add ASC");	
}
?>