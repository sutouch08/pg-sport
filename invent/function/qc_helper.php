<?php
function product_from_zone($id_order, $id_pa)
{
	$sc = '';
	$qs = dbQuery("SELECT zone_name, SUM(qty) AS qty FROM tbl_temp JOIN tbl_zone ON tbl_temp.id_zone = tbl_zone.id_zone WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa." GROUP BY tbl_temp.id_zone");
	$row = dbNumRows($qs) > 1 ? '<br/>' : '';
	while($rs = dbFetchArray( $qs ) )
	{
		$sc .= $rs['zone_name']." : ".number_format($rs['qty']) . $row;
	}
	return $sc;
}

function sumOrderQty($id_order, $id_pa = '')
{
	$sc = 0;
	if( $id_pa != '' )
	{
		$qs = dbQuery("SELECT SUM( product_qty ) AS qty FROM tbl_order_detail WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa);
	}
	else
	{
		$qs = dbQuery("SELECT SUM( product_qty ) AS qty FROM tbl_order_detail WHERE id_order = ".$id_order);
	}
	$rs = dbFetchArray($qs);
	if( ! is_null( $rs['qty'] ) )
	{
		$sc = $rs['qty'];
	}
	return $sc;
}

function sumPrepareQty($id_order)
{
	$sc = 0;
	$qs = dbQuery("SELECT SUM( qty ) AS qty FROM tbl_temp WHERE id_order = ".$id_order);
	$rs = dbFetchArray($qs);
	if( ! is_null( $rs['qty'] ) )
	{
		$sc = $rs['qty'];
	}
	return $sc;
}

function sumPreparedQty($id_order, $id_pa)
{
	$sc = 0;
	$qs = dbQuery("SELECT SUM( qty ) AS qty FROM tbl_temp WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa);
	$rs = dbFetchArray($qs);
	if( ! is_null( $rs['qty'] ) )
	{
		$sc = $rs['qty'];
	}
	return $sc;
}

function sumCheckedQty($id_order, $id_pa)
{
	$sc = 0;
	$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_qc WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa." AND valid = 1");
	$rs = dbFetchArray($qs);
	if( ! is_null( $rs['qty'] ) )
	{
		$sc = $rs['qty'];
	}
	return $sc;	
}

function checked_qty($id_order, $id_product_attribute)
{
	$sql = dbQuery("SELECT SUM(qty) FROM tbl_qc WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND valid =1");
	$sqr =dbQuery("SELECT SUM(qty) FROM tbl_temp WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND status IN(1,2,3,4,6)");
	$sqm = dbQuery("SELECT product_qty FROM tbl_order_detail WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order");
	$sqa = dbQuery("SELECT id_temp FROM tbl_temp WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND status = 1");
	list($re) = dbFetchArray($sql);
	list($rs) = dbFetchArray($sqr);
	list($rm) = dbFetchArray($sqm);
	list($ra) = dbFetchArray($sqa);
	if($re <1){ $result['current'] = 0; }else{ $result['current'] = $re; } // ยอด qc
	$result['prepare_qty'] = $rs; // ยอดจัด
	$result['order_qty'] = $rm; //ยอดสั่ง
	$result['id_temp'] = $ra; //id_temp
	return $result;
}

function getIdTemp($id_order, $id_product_attribute)
{
	$id_temp = FALSE;
	$qs = dbQuery("SELECT id_temp FROM tbl_temp WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND status = 1");
	if( dbNumRows($qs) > 0 )
	{
		list( $id_temp ) = dbFetchArray($qs);	
	}
	return $id_temp;
}


function tempIn($id_order, $id_pa)
{
	$sc = '';
	$limit	= sumOrderQty($id_order, $id_pa);
	if( $limit > 0 )
	{
		$qs 	= dbQuery("SELECT id_temp FROM tbl_temp WHERE id_product_attribute = ".$id_pa." AND id_order = ".$id_order." LIMIT ".$limit);
		$rs 	= dbNumRows($qs);
		if($rs > 0)
		{
			$i = 0;
			while($ro = dbFetchArray($qs) )
			{
				$sc .= $ro['id_temp'];
				$i++;
				if($i < $rs){ $sc .= ", "; }
			}
		}
	}
	return $sc;
}

?>