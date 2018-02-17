<?php 
/***********************************  รายงานสรุปยอดสินค้า เปรียบเทียบ เข้า - ออก รวมยอดเป็นวัน ************************************/

if( isset($_GET['movement_summary']) && isset($_GET['report']) )
{
	$wh 				= $_POST['id_warehouse'];
	$id_wh			= $wh == 0 ? "" : $wh;
	$from 			= fromDate($_POST['from_date']);
	$to 				= toDate($_POST['to_date']);
	$whn				= $wh == 0 ? "รวมทุกคลัง" : get_warehouse_name_by_id($wh);
	$today 			= date("Y-m-d H:i:s");
	$data 			= array();
	$arr 	 			= array("from" => thaiDate($from, "/"), "to" => thaiDate($to, "/"), "wh" => $whn);
	array_push($data, $arr);
	$arr 				= array("header" => "header");
	array_push($data, $arr);	
	$b_date 			= date("Y-m-d", strtotime($from)); 
	$t_date 			= date("Y-m-d", strtotime($to));
	$product			= new product();
	$arr 				= $product->all_qty_and_cost($id_wh);
	$balance_qty 	= $arr['total_qty'];
	$balance_cost 	= $arr['total_cost'];
	while($today > $to){
		if( $wh != 0 )
		{
			$sql = "SELECT id_product_attribute, SUM(move_in) AS move_in, SUM(move_out) AS move_out FROM tbl_stock_movement WHERE ";
			$sql .= "id_warehouse = ".$wh." AND (date_upd BETWEEN '".fromDate($today)."' AND '".toDate($today)."') GROUP BY id_product_attribute";
		}
		else
		{
			$sql = "SELECT id_product_attribute, SUM(move_in) AS move_in, SUM(move_out) AS move_out FROM tbl_stock_movement WHERE date_upd BETWEEN '".fromDate($today)."' AND '".toDate($today)."' GROUP BY id_product_attribute";
		}
		$qs = dbQuery($sql);
		
		while( $r = dbFetchArray($qs) )
		{
			$id_product_attribute 	= $r['id_product_attribute'];
			$move_in 					= $r['move_in'];
			$move_out 					= $r['move_out'];
			$cost 						= $product->get_product_cost($id_product_attribute);
			$in_cost 						= $move_in * $cost;
			$out_cost 					= $move_out * $cost;
			$balance_qty 				+= $move_out;	
			$balance_qty 				-= $move_in;
			$balance_cost 				+= $out_cost;
			$balance_cost 				-= $in_cost;
		}
		$today = date("Y-m-d H:i:s", strtotime("-1day $today"));
	}
	$dataset = array();
	while($b_date <= $t_date) :
		$total_in 				= 0;
		$total_out 			= 0;
		$total_cost_in 		= 0;
		$total_cost_out		= 0;
		if($wh != 0)
		{
			$sql = "SELECT id_product_attribute, SUM(move_in) AS move_in, SUM(move_out) AS move_out FROM tbl_stock_movement WHERE ";
			$sql .= "id_warehouse = ".$wh." AND (date_upd BETWEEN '".fromDate($t_date)."' AND '".toDate($t_date)."') GROUP BY id_product_attribute";
		}
		else
		{
			$sql = "SELECT id_product_attribute, SUM(move_in) AS move_in, SUM(move_out) AS move_out FROM tbl_stock_movement WHERE date_upd BETWEEN '".fromDate($t_date)."' AND '".toDate($t_date)."'  GROUP BY id_product_attribute";
		}
		$qs = dbQuery($sql);
		while($r = dbFetchArray($qs)) :
			$id_product_attribute 	= $r['id_product_attribute'];
			$move_in 					= $r['move_in'];
			$move_out 					= $r['move_out'];
			$cost 						= $product->get_product_cost($id_product_attribute);
			$in_cost 						= $move_in * $cost;
			$out_cost 					= $move_out * $cost;
			$balance_qty 				+= $move_out;
			$balance_qty 				-= $move_in;
			$total_in 						+= $move_in;
			$total_cost_in 				+= $in_cost;
			$total_out 					+= $move_out;
			$total_cost_out 			+= $out_cost;
			$balance_cost 				+= $out_cost;
			$balance_cost 				-= $in_cost;
		endwhile;
		$dataset[$t_date]['date'] 				= $t_date;
		$dataset[$t_date]['move_in'] 		= $total_in;
		$dataset[$t_date]['cost_in'] 			= $total_cost_in;
		$dataset[$t_date]['move_out'] 		= $total_out;
		$dataset[$t_date]['cost_out'] 		= $total_cost_out;
		$dataset[$t_date]['balance_qty'] 	= $balance_qty;
		$dataset[$t_date]['balance_cost'] 	= $balance_cost;
		$t_date = date("Y-m-d", strtotime("-1day $t_date"));	
	endwhile;	
		$start 				= date("Y-m-d", strtotime($from));  
		$end 					= date("Y-m-d", strtotime($to));		
		$total_in2 			= 0;
		$total_out2 			= 0;
		$total_cost_in2 	= 0;
		$total_cost_out2 	= 0;
		
		while($start <= $end) :
			$arr 	= array(
								"date" 		=> thaiDate($dataset[$start]['date'], "/"), 
								"move_in" 	=> number_format($dataset[$start]['move_in']), 
								"in_amount" 	=> number_format($dataset[$start]['cost_in'],2),
								"move_out"	=> number_format($dataset[$start]['move_out']),
								"out_amount"	=> number_format($dataset[$start]['cost_out'],2),
								"balance"		=> number_format($dataset[$start]['balance_qty']),
								"amount"		=> number_format($dataset[$start]['balance_cost'],2)
								);
			array_push($data, $arr);
			$total_in2 			+= $dataset[$start]['move_in'];
			$total_out2 			+= $dataset[$start]['move_out'];
			$total_cost_in2 	+= $dataset[$start]['cost_in'];	
			$total_cost_out2 	+= $dataset[$start]['cost_out'];
			$start = date("Y-m-d", strtotime("+1day $start"));		
		endwhile;
		$arr = array(
						"date" 		=> "รวม",
						"move_in"		=> number_format($total_in2),
						"in_amount"	=> number_format($total_cost_in2,2),
						"move_out"	=> number_format($total_out2),
						"out_amount"	=> number_format($total_cost_out2,2),
						"balance"		=> "",
						"amount"		=> ""
						);
		array_push($data, $arr);
	echo json_encode($data);
}//// End 

/***********************************  รายงานสรุปยอดสินค้า เปรียบเทียบ เข้า - ออก รวมยอดเป็นวัน Export to Excel  ************************************/

if( isset($_GET['movement_summary']) && isset($_GET['export']) )
{
	$wh 				= $_GET['id_warehouse'];
	$id_wh			= $wh == 0 ? "" : $wh;
	$from 			= fromDate($_GET['from_date']);
	$to 				= toDate($_GET['to_date']);
	$whn				= $wh == 0 ? "รวมทุกคลัง" : get_warehouse_name_by_id($wh);
	$today 			= date("Y-m-d H:i:s");
	$data 			= array();
	$arr 	 			= array("รายงานสรุปยอดความเคลื่อนไหวสินค้า เปรียบเทียบยอด เข้า  -  ออก");
	array_push($data, $arr);
	$arr 				= array("ตั้งแต่วันที่  ". thaiDate($from, "/")."  ถึง   ". thaiDate($to, "/")."  ".$whn."  ".COMPANY);
	array_push($data, $arr);
	$arr 				= array("วันที่", "สินค้าเข้า(จำนวน)", "มูลค่าเข้า(ทุน)", "สินค้าออก(จำนวน)", "มูลค่าออก(ทุน)", "คงเหลือ", "มูลค่า(ทุน)");
	array_push($data, $arr);	
	$b_date 			= date("Y-m-d", strtotime($from)); 
	$t_date 			= date("Y-m-d", strtotime($to));
	$product			= new product();
	$arr 				= $product->all_qty_and_cost($id_wh);
	$balance_qty 	= $arr['total_qty'];
	$balance_cost 	= $arr['total_cost'];
	while($today > $to){
		if( $wh != 0 )
		{
			$sql = "SELECT id_product_attribute, SUM(move_in) AS move_in, SUM(move_out) AS move_out FROM tbl_stock_movement WHERE ";
			$sql .= "id_warehouse = ".$wh." AND (date_upd BETWEEN '".fromDate($today)."' AND '".toDate($today)."') GROUP BY id_product_attribute";
		}
		else
		{
			$sql = "SELECT id_product_attribute, SUM(move_in) AS move_in, SUM(move_out) AS move_out FROM tbl_stock_movement WHERE date_upd BETWEEN '".fromDate($today)."' AND '".toDate($today)."' GROUP BY id_product_attribute";
		}
		$qs = dbQuery($sql);
		
		while( $r = dbFetchArray($qs) )
		{
			$id_product_attribute 	= $r['id_product_attribute'];
			$move_in 					= $r['move_in'];
			$move_out 					= $r['move_out'];
			$cost 						= $product->get_product_cost($id_product_attribute);
			$in_cost 						= $move_in * $cost;
			$out_cost 					= $move_out * $cost;
			$balance_qty 				+= $move_out;	
			$balance_qty 				-= $move_in;
			$balance_cost 				+= $out_cost;
			$balance_cost 				-= $in_cost;
		}
		$today = date("Y-m-d H:i:s", strtotime("-1day $today"));
	}
	$dataset = array();
	while($b_date <= $t_date) :
		$total_in 				= 0;
		$total_out 			= 0;
		$total_cost_in 		= 0;
		$total_cost_out		= 0;
		if($wh != 0)
		{
			$sql = "SELECT id_product_attribute, SUM(move_in) AS move_in, SUM(move_out) AS move_out FROM tbl_stock_movement WHERE ";
			$sql .= "id_warehouse = ".$wh." AND (date_upd BETWEEN '".fromDate($t_date)."' AND '".toDate($t_date)."') GROUP BY id_product_attribute";
		}
		else
		{
			$sql = "SELECT id_product_attribute, SUM(move_in) AS move_in, SUM(move_out) AS move_out FROM tbl_stock_movement WHERE date_upd BETWEEN '".fromDate($t_date)."' AND '".toDate($t_date)."'  GROUP BY id_product_attribute";
		}
		$qs = dbQuery($sql);
		while($r = dbFetchArray($qs)) :
			$id_product_attribute 	= $r['id_product_attribute'];
			$move_in 					= $r['move_in'];
			$move_out 					= $r['move_out'];
			$cost 						= $product->get_product_cost($id_product_attribute);
			$in_cost 						= $move_in * $cost;
			$out_cost 					= $move_out * $cost;
			$balance_qty 				+= $move_out;
			$balance_qty 				-= $move_in;
			$total_in 						+= $move_in;
			$total_cost_in 				+= $in_cost;
			$total_out 					+= $move_out;
			$total_cost_out 			+= $out_cost;
			$balance_cost 				+= $out_cost;
			$balance_cost 				-= $in_cost;
		endwhile;
		$dataset[$t_date]['date'] 				= $t_date;
		$dataset[$t_date]['move_in'] 		= $total_in;
		$dataset[$t_date]['cost_in'] 			= $total_cost_in;
		$dataset[$t_date]['move_out'] 		= $total_out;
		$dataset[$t_date]['cost_out'] 		= $total_cost_out;
		$dataset[$t_date]['balance_qty'] 	= $balance_qty;
		$dataset[$t_date]['balance_cost'] 	= $balance_cost;
		$t_date = date("Y-m-d", strtotime("-1day $t_date"));	
	endwhile;	
		$start 				= date("Y-m-d", strtotime($from));  
		$end 					= date("Y-m-d", strtotime($to));		
		$total_in2 			= 0;
		$total_out2 			= 0;
		$total_cost_in2 	= 0;
		$total_cost_out2 	= 0;
		
		while($start <= $end) :
			$arr 	= array(
								thaiDate($dataset[$start]['date'], "/"),
								$dataset[$start]['move_in'], 
								$dataset[$start]['cost_in'], 
								$dataset[$start]['move_out'], 
								$dataset[$start]['cost_out'], 
								$dataset[$start]['balance_qty'], 
								$dataset[$start]['balance_cost'],
								);
			array_push($data, $arr);
			$total_in2 			+= $dataset[$start]['move_in'];
			$total_out2 			+= $dataset[$start]['move_out'];
			$total_cost_in2 	+= $dataset[$start]['cost_in'];	
			$total_cost_out2 	+= $dataset[$start]['cost_out'];
			$start = date("Y-m-d", strtotime("+1day $start"));		
		endwhile;
		$arr = array(
						"date" 		=> "รวม",
						"move_in"		=> $total_in2,
						"in_amount"	=> $total_cost_in2,
						"move_out"	=> $total_out2,
						"out_amount"	=> $total_cost_out2,
						"balance"		=> "",
						"amount"		=>""
						);
		array_push($data, $arr);
		$time = date("d-m-Y");		
		$sheet_name 	= "Movement_summary";
		$xls 				= new Excel_XML('UTF-8', true, $sheet_name); 
		$xls->addArray ($data ); 
		$xls->generateXML( "Movement_summary_".$time );
		clearToken($_GET['token']);
					
}//// End 
?>
