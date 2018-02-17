<?php 
function prepareOrder($id_order)
{
	$id_user = getCookie("user_id");
	$qs = dbQuery("SELECT id_prepare, id_employee FROM tbl_prepare WHERE id_order = ".$id_order);
	if( dbNumRows($qs) > 0 )
	{
		list( $id_prepare, $id_emp ) = dbFetchArray($qs);
		if( $id_emp == -1 )
		{
			$qr = dbQuery("UPDATE tbl_prepare SET id_employee = ".$id_user." WHERE id_prepare = ".$id_prepare);
		}
	}
	else
	{
		$qr = dbQuery("INSERT INTO tbl_prepare( id_order, id_employee, start ) VALUES ( ".$id_order.", ".$id_user.", '".date("Y-m-d H:i:s")."')");	
	}
}

function getStockInZone($id_zone, $id_pa)
{
	$sc = 0;
	$qs = dbQuery("SELECT qty FROM tbl_stock WHERE id_zone = ".$id_zone." AND id_product_attribute = ".$id_pa);
	if( dbNumRows($qs) > 0 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}

function getBufferQty($id_order, $id_pa)
{
	$sc = 0;
	$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_buffer WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa." GROUP BY id_product_attribute");
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);	
	}
	return $sc;
}

function setValidDetail($id_order, $id_pa, $valid)
{
	return dbQuery("UPDATE tbl_order_detail SET valid_detail = ".$valid." WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa);	
}

function setValidAllDetail($id_order, $valid)
{
	return dbQuery("UPDATE tbl_order_detail SET valid_detail = ".$valid." WHERE id_order = ".$id_order);	
}

function endPrepare($id_order)
{
	return  dbQuery("UPDATE tbl_prepare SET end = '".date("Y-m-d H:i:s")."' WHERE id_order = ".$id_order);	
}


?>