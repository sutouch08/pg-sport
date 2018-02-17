<?php
class sale{
	public $id_sale;
	public $id_employee;
	public $first_name;
	public $last_name;
	public $full_name;
	public $id_group;
	public $group_name;

	public function __construct($id_sale=""){
		if($id_sale ==""){
			return true;
		}else{
		$sql = dbQuery("SELECT tbl_sale.id_sale, tbl_sale.id_employee, first_name, last_name, tbl_sale.id_group, group_name FROM tbl_sale LEFT JOIN tbl_employee ON tbl_sale.id_employee = tbl_employee.id_employee LEFT JOIN tbl_group ON tbl_sale.id_group = tbl_group.id_group WHERE tbl_sale.id_sale = $id_sale");
		list($id_sale, $id_employee, $first_name, $last_name, $id_group, $group_name) = dbFetchArray($sql);
		$this->id_sale = $id_sale;
		$this->id_employee = $id_employee;
		$this->first_name = $first_name;
		$this->last_name = $last_name;
		$this->full_name = $first_name." ".$last_name;
		$this->id_group = $id_group;
		$this->group_name = $group_name;
		}
	}
	/************************* ตารางยอดขาย แสดงยอด สัปดาห์ล่าสุด *****************/
	public function saleWeekTable(){
		$today = date('Y-m-d');
		$rang = getWeek($today);
		$from = $rang['from'];
		$monday = $from;
		$tuesday = date('Y-m-d', strtotime("+1 day $from"));
		$wednesday = date('Y-m-d', strtotime("+2 day $from"));
		$thursday = date('Y-m-d', strtotime("+3 day $from"));
		$friday = date('Y-m-d', strtotime("+4 day $from"));
		$saturday = date('Y-m-d', strtotime("+5 day $from"));
		$sunday = date('Y-m-d', strtotime("+6 day $from"));
		$week = array("monday"=>$monday, "tuesday"=>$tuesday, "wednesday"=>$wednesday, "thursday"=>$thursday, "friday"=>$friday, "saturday"=>$saturday, "sunday"=>$sunday);
		$day_in_week = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday","sunday");
		$sql = dbQuery("SELECT id_sale, id_employee FROM tbl_sale");
		$data = array();
		while($rs = dbFetchArray($sql)){
			$id_employee = $rs['id_employee'];
			$id_sale = $rs['id_sale'];
			$sqm = dbQuery("SELECT first_name FROM tbl_sale LEFT JOIN tbl_employee ON tbl_sale.id_employee = tbl_employee.id_employee WHERE tbl_sale.id_employee = $id_employee");
			list($first_name) = dbFetchArray($sqm);
			$amount = array();
			$amount['sale'] = $first_name;
			$d = 0;
			foreach($week as $day){
			$sqr= dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail_sold WHERE id_sale = $id_sale AND id_role IN(1,5) AND date_upd LIKE'$day%'"); /// ยอดขาย
			if(dbNumRows($sqr) == 1 ){ list($res) = dbFetchArray($sqr); }else{ $res = 0; }
			$qs = dbQuery("SELECT SUM(discount_amount) FROM tbl_order_discount WHERE id_sale = $id_sale AND role IN(1,5) AND date_upd LIKE'$day%'"); /// ส่วนลดท้ายบิล
			if(dbNumRows($qs) == 1 ){ list($bill_discount) = dbFetchArray($qs); }else{ $bill_discount = 0; }
			$res = $res - $bill_discount;
			$net_res = $res/1.07;
			$sale_amount = 0;
			$sale_amount = $sale_amount + $net_res;
			$days = $day_in_week[$d];
			$amount[$days] = $sale_amount;
			$d++;
			}
			array_push($data, $amount);
		}
		return $data;
	}

	public function total_bill_discount($id_sale, $from, $to)
	{
		$amount = 0;
		$qr = dbQuery("SELECT id_order FROM tbl_order_detail_sold WHERE id_sale = ".$id_sale." AND id_role = 1 AND (date_upd BETWEEN '".$from."' AND '".$to."') GROUP BY id_order");
		$row = dbNumRows($qr);
		if( $row > 0 )
		{
			$in  = "";
			$i = 1;
			while($rs = dbFetchArray($qr) )
			{
				$in .= $rs['id_order'];
				if($i < $row)
				{
					$in .= ", ";
				}
				$i++;
			}
			$qs = dbQuery("SELECT SUM(discount_amount) FROM tbl_order_discount WHERE id_order IN(".$in.")");
			if(dbNumRows($qs) == 1 )
			{
				list($discount) = dbFetchArray($qs);
				$amount += $discount;
			}
		}
		return $amount;
	}

	public function total_group_bill_discount($id_group, $from, $to)
	{
		$amount = 0;
		$qr = dbQuery("SELECT id_order FROM tbl_order_detail_sold JOIN tbl_customer ON tbl_order_detail_sold.id_customer = tbl_customer.id_customer WHERE id_role = 1 AND id_default_group = ".$id_group." AND (tbl_order_detail_sold.date_upd BETWEEN '".$from."' AND '".$to."') GROUP BY id_order");
		$row = dbNumRows($qr);
		if( $row > 0 )
		{
			$i = 1;
			$in = "";
			while($rs = dbFetchArray($qr) )
			{
				$in .= $rs['id_order'];
				if($i < $row ){ $in .= ", "; }
				$i++;
			}
			$qs = dbQuery("SELECT SUM(discount_amount) FROM tbl_order_discount WHERE id_order IN(".$in.")");
			if(dbNumRows($qs) == 1 )
			{
				list($discount) = dbFetchArray($qs);
				$amount += $discount;
			}
		}
		return $amount;
	}


	public function all_bill_discount($from, $to)
	{
		$amount = 0;
		$qs = dbQuery("SELECT id_order FROM tbl_order_detail_sold WHERE id_role = 1 AND (date_upd BETWEEN '".$from."' AND '".$to."') GROUP BY id_order");
		$row = dbNumRows($qs);
		if($row > 0)
		{
			$i = 	 1;
			$in = "";
			while($rs = dbFetchArray($qs) )
			{
				$in .= $rs['id_order'];
				if($i < $row){ $in .= ", "; }
				$i++;
			}
			$qr = dbQuery("SELECT SUM(discount_amount) FROM tbl_order_discount WHERE id_order IN(".$in.")");
			if(dbNumRows($qr) == 1 )
			{
				list($discount) = dbFetchArray($qr);
				$amount += $discount;
			}
		}
		return $amount;
	}
	/************************  ตารางยอดขายเดือนนี้ *************************/
	public function saleLeaderBoard($from="", $to=""){ // แสดงเป็นเดือน
		if($from=="" || $to ==""){
				$rang = getMonth();
				$from = $rang['from']." 00:00:00";
				$to = $rang['to']." 23:59:59";
		}
		$sql = dbQuery("SELECT id_sale, id_employee FROM tbl_sale");
		$data = array();
		while($rs = dbFetchArray($sql)){
			$id_employee = $rs['id_employee'];
			$id_sale = $rs['id_sale'];
			$sqm = dbQuery("SELECT first_name FROM tbl_sale LEFT JOIN tbl_employee ON tbl_sale.id_employee = tbl_employee.id_employee WHERE tbl_sale.id_employee = $id_employee");
			list($first_name) = dbFetchArray($sqm);
			$sqr = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_sale = $id_sale AND id_role IN(1,5) AND (date_upd BETWEEN '$from' AND '$to')");
			list($amount) = dbFetchArray($sqr);
			$bill_discount = $this->total_bill_discount($id_sale, $from, $to);
			$amount = $amount - $bill_discount;
			$net_amount =  $amount/1.07;
			$sale_amount = 0;
			$sale_amount = $sale_amount + $net_amount;
		$arr = array("id"=>$id_sale, "first_name"=>$first_name, "sale_amount"=>$sale_amount);
		array_push($data, $arr);
		}
		function sale_amount_desc($item1,$item2){
			if ($item1['sale_amount'] == $item2['sale_amount']) return 0;
			return ($item1['sale_amount'] < $item2['sale_amount']) ? 1 : -1;
		}
		uasort($data, 'sale_amount_desc');
		return $data;
	}

	/************************* ยอดขายแยกตามภาค เรียงตามมากไปหาน้อย แสดงเดือนปัจจุบัน *****************************/
	public function groupLeaderBoard($from="",$to=""){
		if($from=="" || $to ==""){
				$rang = getMonth();
				$from = $rang['from']." 00:00:00";
				$to = $rang['to']." 23:59:59";
		}
		$sql = dbQuery("SELECT id_group, group_name FROM tbl_group ORDER BY group_name DESC");
		$data = array();
		while($rs = dbFetchArray($sql)){
			$id_group = $rs['id_group'];
			$group_name = $rs['group_name'];
			$sqr = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold JOIN tbl_customer ON tbl_order_detail_sold.id_customer = tbl_customer.id_customer WHERE id_default_group = $id_group AND id_role IN(1,5) AND (tbl_order_detail_sold.date_upd BETWEEN '$from' AND '$to')");
			$sale_amount = 0;
			list($amount) = dbFetchArray($sqr);
			$bill_discount = $this->total_group_bill_discount($id_group, $from, $to);  /// ส่วนลดท้ายบิล
			$amount = $amount - $bill_discount;
			$net_amount =  $amount/1.07;
			$sale_amount = $sale_amount + $net_amount;
			$arr = array("zone_name"=>$group_name, "sale_amount"=>$sale_amount);
			array_push($data, $arr);
		}
		function group_amount_desc($item1,$item2){
			if ($item1['sale_amount'] == $item2['sale_amount']) return 0;
			return ($item1['sale_amount'] < $item2['sale_amount']) ? 1 : -1;
		}
		uasort($data, 'group_amount_desc');
		return $data;
	}

	public function getSaleAmount($id_sale){
		$rang = getMonth();
		$from = $rang['from']." 00:00:00";
		$to = $rang['to']." 23:59:59";
		$sqr = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_sale = $id_sale AND id_role IN(1,5) AND (date_upd BETWEEN '$from' AND '$to')");
		list($amount) = dbFetchArray($sqr); //
		$bill_discount = $this->total_bill_discount($id_sale, $from, $to);
		$amount = $amount - $bill_discount;
		$sale_amount = 0;
		$net_amount =  $amount/1.07;
		$sale_amount = $sale_amount + $net_amount;
		return number_format($sale_amount,2);
	}

	public function getSaleLastMonth($id_sale){
		$date = date('Y-m');
		$from = date('Y-m-01',strtotime('last month' ,strtotime($date)));
		$to = date('Y-m-t',strtotime('-1 month' ,strtotime($date)));
		$from .= " 00:00:00";
		$to .= " 23:59:59";
		$sale_amount = 0;
		$sqr = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_sale = $id_sale AND id_role IN(1,5) AND (date_upd BETWEEN '$from' AND '$to')");
		list($amount) = dbFetchArray($sqr); //
		$bill_discount = $this->total_bill_discount($id_sale, $from, $to);  ///ส่วนลดท้ายบิล
		$amount = $amount - $bill_discount;
		$net_amount =  $amount/1.07;
		$sale_amount = $sale_amount + $net_amount;
		return number_format($sale_amount,2);
	}

	public function getSaleLastYear($id_sale){
		$from = date('Y-m-01',strtotime('-1 year'));
		$to = date('Y-m-t',strtotime('-1 year'));
		$from .= " 00:00:00";
		$to .= " 23:59:59";
		$sale_amount = 0;
		$sqr = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_sale = $id_sale AND id_role IN(1,5) AND (date_upd BETWEEN '$from' AND '$to')");
		list($amount) = dbFetchArray($sqr); /// ยอดขาย
		$bill_discount = $this->total_bill_discount($id_sale, $from, $to);
		$amount = $amount - $bill_discount;
		$net_amount =  $amount/1.07;
		$sale_amount = $sale_amount + $net_amount;
		return number_format($sale_amount,2);
	}

	public function getSaleToday($id_sale){
		$today = date('Y-m-d');
		$from = $today." 00:00:00";
		$to = $today." 23:59:59";
		$sql = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_sale = $id_sale AND id_role IN(1,5) AND date_upd LIKE '$today%'");
		list($amount) = dbFetchArray($sql);
		$bill_discount = $this->total_bill_discount($id_sale, $from, $to);  /// ส่วนลดท้ายบิล
		$amount = $amount - $bill_discount;
		$sale_amount = 0;
		$net_amount =  $amount/1.07;
		$sale_amount = $sale_amount + $net_amount;
		return number_format($sale_amount,2);
	}
	/* ******************** ยอดขายรวมวันนี้ *************************/
	public function totalToday(){
		$today = date('Y-m-d');
		$from = $today." 00:00:00";
		$to = $today." 23:59:59";
		$sql = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND date_upd LIKE '$today%'");
		list($amount) = dbFetchArray($sql);
		$bill_discount = $this->all_bill_discount($from, $to); //// ส่วนลดท้ายบิล
		$amount -= $bill_discount;
		$sale_amount = 0;
		$net_amount =  $amount/1.07;
		$sale_amount = $sale_amount + $net_amount;
		return number_format($sale_amount,2);
	}
	/***************************** ยอดขายรวมเมื่อวานนี้ ********************************/
	public function totalLastDay(){
		$yesterday = date('Y-m-d', strtotime("-1 day"));
		$from = $yesterday." 00:00:00";
		$to = $yesterday." 23:59:59";
		$sql = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND date_upd LIKE '$yesterday%'");
		list($amount) = dbFetchArray($sql);
		$bill_discount = $this->all_bill_discount($from, $to); //// ส่วนลดท้ายบิล
		$amount -= $bill_discount;
		$sale_amount = 0;
		$net_amount =  $amount/1.07;
		$sale_amount = $sale_amount + $net_amount;
		return number_format($sale_amount,2);
	}
	/***************************** ยอดขายรวม สัปดาห์นี้ ******************************/
	public function totalThisWeek(){
		$today = date('Y-m-d');
		$rang = getWeek($today);
		$from = $rang['from']." 00:00:00";
		$to = $rang['to']." 23:59:59";
		$sqr = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND (date_upd BETWEEN '$from' AND '$to')");
		list($amount) = dbFetchArray($sqr); //
		$bill_discount = $this->all_bill_discount($from, $to);
		$amount -= $bill_discount;
		$sale_amount = 0;
		$net_amount =  $amount/1.07;
		$sale_amount = $sale_amount + $net_amount;
		return number_format($sale_amount,2);
	}
	/******************************** ยอดขายรวม สัปดาห์ที่แล้ว *************************/
	public function totalLastWeek(){
		$date = date('Y-m-d', strtotime("-7 day"));
		$rang = getWeek($date);
		$from = $rang['from']." 00:00:00";
		$to = $rang['to']." 23:59:59";
		$sqr = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND (date_upd BETWEEN '$from' AND '$to')");
		list($amount) = dbFetchArray($sqr); //
		$bill_discount = $this->all_bill_discount($from, $to);
		$amount -= $bill_discount;
		$sale_amount = 0;
		$net_amount =  $amount/1.07;
		$sale_amount = $sale_amount + $net_amount;
		return number_format($sale_amount,2);
	}
	/***************************** ยอดขายรวมเดือนนี้ **********************************/
	public function totalThisMonth(){
		$rang = getMonth();
		$from = $rang['from']." 00:00:00";
		$to = $rang['to']." 23:59:59";
		$sqr = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND (date_upd BETWEEN '$from' AND '$to')");
		list($amount) = dbFetchArray($sqr); //
		$bill_discount = $this->all_bill_discount($from, $to);
		$amount -= $bill_discount;
		$sale_amount = 0;
		$net_amount =  $amount/1.07;
		$sale_amount = $sale_amount + $net_amount;
		return number_format($sale_amount,2);
	}
	/*********************************  ยอดขายรวมเดือนที่แล้ว **************************************/
	public function totalLastMonth(){
		$date = date('Y-m');
		$from = date('Y-m-01',strtotime('last month' ,strtotime($date)));
		$to = date('Y-m-t',strtotime('-1 month' ,strtotime($date)));
		$from .= " 00:00:00";
		$to .= " 23:59:59";
		$sale_amount = 0;
		$sqr = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND (date_upd BETWEEN '$from' AND '$to')");
		list($amount) = dbFetchArray($sqr); //
		$bill_discount = $this->all_bill_discount($from, $to);
		$amount -= $bill_discount;
		$net_amount =  $amount/1.07;
		$sale_amount = $sale_amount + $net_amount;
		return number_format($sale_amount,2);
	}
	/********************************* ยอดขายรวมเดือนนี้ปีที่แล้ว *************************************/
	public function totalLastYear(){
		$from = date('Y-m-01',strtotime('-1 year'));
		$to = date('Y-m-t',strtotime('-1 year'));
		$from .= " 00:00:00";
		$to .= " 23:59:59";
		$sale_amount = 0;
		$sqr = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND (date_upd BETWEEN '$from' AND '$to')");
		list($amount) = dbFetchArray($sqr); //
		$bill_discount = $this->all_bill_discount($from, $to);
		$amount -= $bill_discount;
		$net_amount =  $amount/1.07;
		$sale_amount = $sale_amount + $net_amount;
		return number_format($sale_amount,2);
	}
	/***************************  ยอดขายรวมปีนี้ **********************************/
	public function totalThisYear(){
		$from = date('Y-01-01')." 00:00:00";
		$to = date('Y-12-t')." 23:59:59";
		$sale_amount = 0;
		$sqr = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND (date_upd BETWEEN '$from' AND '$to')");
		list($amount) = dbFetchArray($sqr); //
		$bill_discount = $this->all_bill_discount($from, $to);
		$amount -= $bill_discount;
		$net_amount =  $amount/1.07;
		$sale_amount = $sale_amount + $net_amount;
		return number_format($sale_amount,2);
	}
	/**************************  จัดอันดับตามยอดขาย *************************/
	public function LeaderBoard($option){
		switch($option){
			case "today" :
				$from = date('Y-m-d')." 00:00:00";
				$to = date('Y-m-d')." 23:59:59";
			break;
			case "yesterday" :
				$from = date('Y-m-d', strtotime("-1 day"))." 00:00:00";
				$to = date('Y-m-d', strtotime("-1 day"))." 23:59:59";
			break;
			case "this_week" :
				$date = date('Y-m-d');
				$rang = getWeek($date);
				$from = $rang['from']." 00:00:00";
				$to = $rang['to']." 23:59:59";
			break;
			case "last_week" :
				$date = date('Y-m-d', strtotime("-7 day"));
				$rang = getWeek($date);
				$from = $rang['from']." 00:00:00";
				$to = $rang['to']." 23:59:59";
			break;
			case "this_month" :
				$date = date('Y-m-d');
				$rang = getMonth($date);
				$from = $rang['from']." 00:00:00";
				$to = $rang['to']." 23:59:59";
			break;
			case "last_month" :
				$from = date('Y-m-01', strtotime("-1 month"))." 00:00:00";
				$to = date('Y-m-t', strtotime("-1 month"))." 23:59:59";
			break;
			case "this_year" :
				$from = date('Y-01-01')." 00:00:00";
				$to = date('Y-12-t')." 23:59:59";
			break;
			case "last_year" :
				$from = date('Y-01-01', strtotime("-1 year"))." 00:00:00";
				$to = date('Y-12-t', strtotime("-1 year"))." 23:59:59";
			break;
			default :
				$date = date('Y-m-d');
				$rang = getMonth($date);
				$from = $rang['from']." 00:00:00";
				$to = $rang['to']." 23:59:59";
			break;
		}
		$sql = dbQuery("SELECT id_sale, id_employee FROM tbl_sale");
		$data = array();
		while($rs = dbFetchArray($sql)){
			$id_employee = $rs['id_employee'];
			$id_sale = $rs['id_sale'];
			$sqm = dbQuery("SELECT first_name FROM tbl_sale LEFT JOIN tbl_employee ON tbl_sale.id_employee = tbl_employee.id_employee WHERE tbl_sale.id_employee = $id_employee");
			list($first_name) = dbFetchArray($sqm);
			$sqr = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_sale = $id_sale AND id_role IN(1,5) AND (date_upd BETWEEN '$from' AND '$to')");
			list($amount) = dbFetchArray($sqr);
			$bill_discount = $this->total_bill_discount($id_sale, $from, $to);
			$amount -= $bill_discount;
			$net_amount =  $amount/1.07;
			$sale_amount = 0;
			$sale_amount = $sale_amount + $net_amount;
		$arr = array("id"=>$id_sale, "first_name"=>$first_name, "sale_amount"=>$sale_amount);
		array_push($data, $arr);
		}
		foreach ($data as $key=>$row){
			$volume[$key] = $row['sale_amount'];
		}
		array_multisort($volume, SORT_DESC, $data);
		return $data;
	}


	/***********************  ตารางยอดขาย แยกตามพื้นที่ *******************/
	public function LeaderGroup($option){
		switch($option){
			case "today" :
				$from = date('Y-m-d');
				$to = date('Y-m-d');
				break;
			case "yesterday" :
				$from = date('Y-m-d', strtotime("-1 day"));
				$to = date('Y-m-d', strtotime("-1 day"));
			break;
			case "this_week" :
				$date = date('Y-m-d');
				$rang = getWeek($date);
				$from = $rang['from'];
				$to = $rang['to'];
			break;
			case "last_week" :
				$date = date('Y-m-d', strtotime("-7 day"));
				$rang = getWeek($date);
				$from = $rang['from'];
				$to = $rang['to'];
			break;
			case "this_month" :
				$date = date('Y-m-d');
				$rang = getMonth($date);
				$from = $rang['from'];
				$to = $rang['to'];
			break;
			case "last_month" :
				$from = date('Y-m-01', strtotime("-1 month"));
				$to = date('Y-m-t', strtotime("-1 month"));
			break;
			case "this_year" :
				$from = date('Y-01-01');
				$to = date('Y-12-t');
			break;
			case "last_year" :
				$from = date('Y-01-01', strtotime("-1 year"));
				$to = date('Y-12-t', strtotime("-1 year"));
			break;
			default :
				$date = date('Y-m-d');
				$rang = getMonth($date);
				$from = $rang['from'];
				$to = $rang['to'];
			break;
		}


		$from .= " 00:00:00";
		$to .= " 23:59:59";

		$data = array();

		$qr = "SELECT g.id_group, g.group_name, SUM(o.total_amount) AS amount ";
		$qr .= "FROM tbl_order_detail_sold AS o ";
		$qr .= "JOIN tbl_sale AS s ON o.id_sale = s.id_sale ";
		$qr .= "JOIN tbl_group AS g ON s.id_group = g.id_group ";
		$qr .= "WHERE o.id_role = 1 AND o.date_upd >= '".$from."' AND o.date_upd <= '".$to."' ";
		$qr .= "GROUP BY s.id_group ORDER BY amount DESC";

		$qs = dbQuery($qr);
		while( $rs = dbFetchObject($qs) )
		{
			$data[$rs->id_group] = array('group_name' => $rs->group_name, 'amount' => $rs->amount);
		}

		$ds = array();

		$sql = dbQuery("SELECT id_group, group_name FROM tbl_group ORDER BY group_name DESC");

		while( $rs = dbFetchObject($sql))
		{
			$amount = isset( $data[$rs->id_group] ) ? $data[$rs->id_group]['amount'] : 0.00;
			$ds[$rs->id_group] = array('group_name' => $rs->group_name, 'amount' => $amount);
		}
		
		foreach ($ds as $key=>$row)
		{
			$volume[$key] = $row['amount'];
		}

		array_multisort($volume, SORT_DESC, $ds);

		return $ds;
	}




	//****************************************  สรุปยอดขายเซล  *******************************//
	public function sale_amount($option, $id_sale){
		switch($option){
			case "today" :
				$from = date('Y-m-d');
				$to = date('Y-m-d');
			break;
			case "yesterday" :
				$from = date('Y-m-d', strtotime("-1 day"));
				$to = date('Y-m-d', strtotime("-1 day"));
			break;
			case "this_week" :
				$date = date('Y-m-d');
				$rang = getWeek($date);
				$from = $rang['from'];
				$to = $rang['to'];
			break;
			case "last_week" :
				$date = date('Y-m-d', strtotime("-7 day"));
				$rang = getWeek($date);
				$from = $rang['from'];
				$to = $rang['to'];
			break;
			case "this_month" :
				$date = date('Y-m-d');
				$rang = getMonth($date);
				$from = $rang['from'];
				$to = $rang['to'];
			break;
			case "last_month" :
				$from = date('Y-m-01', strtotime("-1 month"));
				$to = date('Y-m-t', strtotime("-1 month"));
			break;
			case "this_year" :
				$from = date('Y-01-01');
				$to = date('Y-12-t');
			break;
			case "last_year" :
				$from = date('Y-01-01', strtotime("-1 year"));
				$to = date('Y-12-t', strtotime("-1 year"));
			break;
			case "this_month_last_year" :
				$from = date('Y-m-01', strtotime("-1 year"));
				$to = date('Y-m-t', strtotime("-1 year"));
			break;
			default :
				$date = date('Y-m-d');
				$rang = getMonth($date);
				$from = $rang['from'];
				$to = $rang['to'];
			break;
		}
		$from .= " 00:00:00";
		$to .= " 23:59:59";
		$sql = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND id_sale = $id_sale AND (date_upd BETWEEN '$from' AND '$to')");
		$total_amount = 0;
		list($amount) = dbFetchArray($sql);
		$bill_discount = $this->total_bill_discount($id_sale, $from, $to);
		$amount -= $bill_discount;
		$net_amount =  $amount/1.07;
		$total_amount = $total_amount+$net_amount;
		return number_format($total_amount,2);
	}
	public function totalSale($day=""){
		if($day ==""){ $day = date("Y-m-d"); }
		$from = $day." 00:00:00";
		$to = $day." 23:59:59";
		$sql = dbQuery("SELECT SUM(total_amount) AS total_amount FROM tbl_order_detail_sold WHERE id_role IN(1,5) AND date_upd LIKE '$day%'");
		list($amount) = dbFetchArray($sql);
		$bill_discount = $this->all_bill_discount($from, $to);
		$amount -= $bill_discount;
		$sale_amount = 0;
		$net_amount =  $amount/1.07;
		$sale_amount = $sale_amount + $net_amount;
		return $sale_amount;
	}

}


?>
