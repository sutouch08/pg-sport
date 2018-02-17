<?php

function fifoBeforeBalance($id_pa, $wh=0, $fromDate)
{
	$sc = 0;
	if( $wh != 0 )
	{
		$qs = dbQuery("SELECT SUM(move_in), SUM(move_out) FROM tbl_stock_movement WHERE id_product_attribute = ".$id_pa." AND id_warehouse = ".$wh." AND date_upd < '".$fromDate."'");
	}
	else
	{
		$qs = dbQuery("SELECT SUM(move_in), SUM(move_out) FROM tbl_stock_movement WHERE id_product_attribute = ".$id_pa." AND date_upd < '".$fromDate."'");
	}
	list( $moveIn, $moveOut) = dbFetchArray($qs);
	if( ! is_null( $moveIn ) && ! is_null( $moveOut ) )
	{
		$sc = $moveIn - $moveOut;	
	}
	return $sc;
}


?>