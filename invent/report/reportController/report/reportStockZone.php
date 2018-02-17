<?php
	$p_rank 		= $_POST['product_rank'];
	$zone_rank	= $_POST['zone_rank'];
	$id_wh		= $_POST['wh'];
	$data 		= array();
	$qs			= "SELECT tbl_stock.id_product_attribute, zone_name, barcode, reference, cost, qty ";
	$qs 			.= "FROM tbl_stock JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute ";
	$qs 			.= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
	if( $p_rank == 1 && $id_wh == 0 && $zone_rank == 1 )
	{
		$qs .= "ORDER BY tbl_product_attribute.id_product ASC";
	}
	else if( $p_rank == 1 && $id_wh != 0 && $zone_rank == 1 )
	{
		$qs .= "WHERE id_warehouse = ".$id_wh." ORDER BY tbl_product_attribute.id_product ASC";
	}
	else if( $p_rank == 2 && $id_wh == 0 && $zone_rank == 1 )
	{
		$qs .= "WHERE 	product_code BETWEEN '".$_POST['from']."' AND '".$_POST['to']."' ORDER BY tbl_product_attribute.id_product ASC";
	}
	else if( $p_rank == 2 && $id_wh != 0 && $zone_rank == 1 )
	{
		$qs .= "WHERE id_warehouse = ".$id_wh." AND	(product_code BETWEEN '".$_POST['from']."' AND '".$_POST['to']."') ORDER BY tbl_product_attribute.id_product ASC";
	}
	else if( $p_rank == 1 && $zone_rank == 2 )
	{
		$qs .= "WHERE zone_name = '".$_POST['zone_name']."' ORDER BY tbl_product_attribute.id_product ASC";
	}
	else if( $p_rank == 2 &&$zone_rank == 2 )
	{
		$qs .= "WHERE 	zone_name = '".$_POST['zone_name']."' AND (product_code BETWEEN '".$_POST['from']."' AND '".$_POST['to']."') ORDER BY tbl_product_attribute.id_product ASC";
	}

	$qs = dbQuery($qs);
	if(dbNumRows($qs) > 0 )
	{
		$n = 1;
		$total_qty = 0;
		$total_amount = 0;
		while($rs = dbFetchArray($qs) ) :
			$arr = array(
						"no" 			=> $n,
						"zone" 		=> $rs['zone_name'],
						"barcode" 	=> $rs['barcode'],
						"reference" 	=> $rs['reference'],
						"cost" 		=> number_format($rs['cost'],2),
						"qty"			=> number_format($rs['qty']),
						"amount" 		=> number_format($rs['cost'] * $rs['qty'],2)
						);
				array_push($data, $arr);
				$n++; 
				$total_qty += $rs['qty'];
				$total_amount += $rs['qty'] * $rs['cost'];
		endwhile;
		$arr = array(
						"no" 			=> "",
						"zone" 		=> "",
						"barcode" 	=> "",
						"reference" 	=> "",
						"cost" 		=> "รวม",
						"qty"			=> number_format($total_qty),
						"amount" 		=> number_format($total_amount,2)
						);
		array_push($data, $arr);
	}
	else
	{
		$arr = array("nodata" => "nodata");
		array_push($data, $arr);
	}
	echo json_encode($data);

?>