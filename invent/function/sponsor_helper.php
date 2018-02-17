<?php

function budget_used($id_customer, $from, $to)
{
	$used = 0;
	$qs = dbQuery("SELECT SUM(total_amount) AS amount FROM tbl_order_detail_sold WHERE id_role = 4 AND id_customer = ".$id_customer." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
	list($amount) = dbFetchArray($qs);
	if( !is_null($amount) )
	{
		$used = $amount;
	}
	return $used;
}




function budget_used_cost($id_customer, $from, $to)
{
	$cost_used = 0;
	$qs = dbQuery("SELECT SUM( total_cost) AS cost FROM tbl_order_detail_sold WHERE id_role = 4 AND id_customer = ".$id_customer." AND (date_upd BETWEEN '".$from."' AND '".$to."')");
	list( $cost ) = dbFetchArray($qs);
	if( ! is_null( $cost ) )
	{
		$cost_used = $cost;	
	}
	return $cost_used;
}




function get_sponsor_current_year($id_sponsor)
{
	$year = '';
	$qs = dbQuery("SELECT year FROM tbl_sponsor WHERE id_sponsor = ".$id_sponsor);
	if(dbNumRows($qs) == 1 )
	{
		$r = dbFetchArray($qs);
		$year = $r['year'];
	}
	return $year;
}




function get_current_sponsor_budget_year($id_sponsor_budget)
{
	$year = "";
	$qs = dbQuery("SELECT year FROM tbl_sponsor_budget WHERE id_sponsor_budget = ".$id_sponsor_budget);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$year = $rs['year'];
	}
	return $year;
}




function get_current_sponsor_rank($id_sponsor_budget)
{
	$rank = "";
	$qs = dbQuery("SELECT start, end FROM tbl_sponsor_budget WHERE id_sponsor_budget = ".$id_sponsor_budget);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$rank['start'] = $rs['start'];
		$rank['end']	= $rs['end'];
	}
	return $rank;
}





function get_id_employee_by_id_sponsor($id_sponsor)
{
	$id = "";
	$qs = dbQuery("SELECT id_employee FROM tbl_sponsor WHERE id_sponsor = ".$id_sponsor." LIMIT 1");
	if( dbNumRows($qs) == 1 )
	{
		$r = dbFetchArray($qs);
		$id = $r['id_employee'];
	}
	return $id;
}





//-----------------------------  ตรวจสอบยอด งบประมาณคงเหลือ
function get_sponsor_budget($id_sponsor_budget)
{
	$amount = 0;
	$qs = dbQuery("SELECT limit_amount FROM tbl_sponsor_budget WHERE id_sponsor_budget = ".$id_sponsor_budget);
	if(dbNumRows($qs) > 0)
	{
		$rs = dbFetchArray($qs);
		$amount = $rs['limit_amount'];
	}
	return $amount;
}






function get_id_order_sponsor($id_order)
{
	$id = 0;
	$sql = dbQuery("SELECT id_order_sponsor FROM tbl_order_sponsor WHERE id_order = ".$id_order);
	$rw = dbNumRows($sql);
	if($rw>0){
		$rs = dbFetchArray($sql);
		$id = $rs['id_order_sponsor'];
	}
	return $id;
}




		
function get_id_user_sponsor($id_order)
{
	$id = 0;
	$sql = dbQuery("SELECT id_user FROM tbl_order_sponsor WHERE id_order = ".$id_order);
	$rw = dbNumRows($sql);
	if($rw>0){
		$rs = dbFetchArray($sql);
		$id = $rs['id_user'];
	}
	return $id;
}	
	
	
	
	
	
function get_id_sponsor_budget_by_order($id_order)
{
	$id_budget = "";
	$qs = dbQuery("SELECT id_budget FROM tbl_order_sponsor	WHERE id_order = ".$id_order);
	if(dbNumRows($qs) > 0)
	{
		$rs = dbFetchArray($qs);
		$id_budget = $rs['id_budget'];
	}
	return $id_budget;
}





	
function get_id_budget_by_id_sponsor($id_sponsor)
{
	$id_budget = 0;
	$year = get_sponsor_active_year($id_sponsor);
	$qs = dbQuery("SELECT id_sponsor_budget FROM tbl_sponsor_budget WHERE id_sponsor = ".$id_sponsor." AND year = '".$year."'");
	if(dbNumRows($qs) > 0 )
	{
		$rs = dbFetchArray($qs);
		$id_budget = $rs['id_sponsor_budget'];
	}
	return $id_budget;
}





	
function get_id_sponsor_by_customer($id_customer)	
{
	$id_sponsor = 0;
	$qs = dbQuery("SELECT id_sponsor FROM tbl_sponsor WHERE id_customer = ".$id_customer);
	if(dbNumRows($qs) > 0)
	{
		$rs = dbFetchArray($qs);
		$id_sponsor = $rs['id_sponsor'];
	}
	return $id_sponsor;
}
	
function update_sponsor_balance($id_budget, $amount) ///  อัพเดตงบคงเหลือ
{
	return dbQuery("UPDATE tbl_sponsor_budget SET balance =".$amount." WHERE id_sponsor_budget = ".$id_budget);
}





function get_sponsor_balance($id_sponsor_budget)
{
	$amount = 0;
	$qr = dbQuery("SELECT balance FROM tbl_sponsor_budget WHERE id_sponsor_budget = ".$id_sponsor_budget);
	if(dbNumRows($qr) > 0)
	{
		$rs = dbFetchArray($qr);
		$amount = $rs['balance'];
	}
	return $amount;
}





function update_order_sponsor_status($id_order, $status)
{
	//********* 0 = ยังไม่เปิดบิล   1 = เปิดบิลแล้ว   2 = ยกเลิก **********//
	return dbQuery("UPDATE tbl_order_sponsor SET status = ".$status." WHERE id_order = ".$id_order);
}	
	
	
	
	
			
function update_order_sponsor_amount($id_order, $amount)
{
	return dbQuery("UPDATE tbl_order_sponsor SET amount = ".$amount." WHERE id_order = ".$id_order);
}






function get_sponsor_active_year($id_sponsor)
{
	$year = "";
	$qs = dbQuery("SELECT year FROM tbl_sponsor WHERE id_sponsor = ".$id_sponsor);
	if(dbNumRows($qs) > 0 )
	{
		$rs = dbFetchArray($qs);
		$year = $rs['year'];
	}
	return $year;
}





function add_sponsor_log($id_sponsor, $id_sponsor_budget, $action_type, $action, $from_value, $to_value)
{
	/// ส่วนของผู้ทำรายการ ///
	$user_name = employee_name($_COOKIE['user_id']); /// ชื่อพนักงานผู้ทำรายการ
	$id_user = $_COOKIE['user_id']; /// id_employee ผู้ทำรายการ
	/// จบส่วนผู้ทำรายการ
	$qs = dbQuery("INSERT INTO tbl_sponsor_log ( id_employee, employee_name, id_sponsor, id_sponsor_budget, action_type, action, from_value, to_value) VALUES (".$id_user.", '".$user_name."', ".$id_sponsor.", ".$id_sponsor_budget.", '".$action_type."', '".$action."', '".$from_value."', '".$to_value."')");
	return $qs;	
}





function getReturnSponsorAmount($id_customer, $from, $to)
{
	$sc = 0;
	$qr = "SELECT id_product_attribute, qty FROM tbl_return_sponsor_detail JOIN tbl_return_sponsor ON tbl_return_sponsor.id_return_sponsor = tbl_return_sponsor_detail.id_return_sponsor ";
	$qr .= "WHERE id_customer = ".$id_customer." AND	 ( tbl_return_sponsor_detail.date_add BETWEEN '".$from."' AND '".$to."' ) AND tbl_return_sponsor_detail.status = 1";
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





function getReturnSponsorCostAmount($id_customer, $from, $to)
{
	$sc = 0;
	$qr = "SELECT id_product_attribute, qty FROM tbl_return_sponsor_detail JOIN tbl_return_sponsor ON tbl_return_sponsor.id_return_sponsor = tbl_return_sponsor_detail.id_return_sponsor ";
	$qr .= "WHERE id_customer = ".$id_customer." AND	 ( tbl_return_sponsor_detail.date_add BETWEEN '".$from."' AND '".$to."' ) AND tbl_return_sponsor_detail.status = 1";
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





function getReturnSponsorQtyAndAmount($id_return_sponsor)
{
	$amount	= 0;
	$qty		= 0;
	$qs = dbQuery("SELECT id_product_attribute, qty FROM tbl_return_sponsor_detail WHERE id_return_sponsor = ".$id_return_sponsor);
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




function getReturnSponsorByCustomer($id_customer, $from, $to)
{
	return dbQuery("SELECT * FROM tbl_return_sponsor WHERE id_customer = ".$id_customer." AND status = 1 AND ( date_add BETWEEN '".$from."' AND '".$to."') ORDER BY date_add ASC");	
}

?>