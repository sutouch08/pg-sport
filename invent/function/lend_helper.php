<?php
//**********************   Lend Helper  *****************//

function get_lend_user_id($id_order)
{
	$id_user = 0;
	$qs = dbQuery("SELECT id_user FROM tbl_lend WHERE id_order = ".$id_order);
	if( dbNumRows($qs) == 1 )
	{
		list($id_user) = dbFetchArray($qs);
	}
	return $id_user;
}

function get_lend_id_by_order($id_order)
{
	$id_lend = "";
	$qs = dbQuery("SELECT id_lend FROM tbl_lend WHERE id_order = ".$id_order);
	if( dbNumRows($qs) == 1 )
	{
		list($id_lend) = dbFetchArray($qs);
	}
	return $id_lend;
}

function update_lend_qty($id_order)
{
	$id_lend = get_lend_id_by_order($id_order);
	$qs = dbQuery("SELECT id_lend_detail, id_product_attribute FROM tbl_lend_detail WHERE id_lend = ".$id_lend);
	$rd = TRUE;
	if( dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$qr = dbQuery("SELECT sold_qty FROM tbl_order_detail_sold WHERE id_order = ".$id_order." AND id_product_attribute = ".$rs['id_product_attribute']);
			if(dbNumRows($qr) == 1)
			{
				list($qty) = dbFetchArray($qr);
				$qa = dbQuery("UPDATE tbl_lend_detail SET qty = ".$qty." WHERE id_lend_detail = ".$rs['id_lend_detail']);
				if(!$qa){ $rd = false; }
			}
			else
			{
				$qa = dbQuery("DELETE FROM tbl_lend_detail WHERE id_lend_detail = ".$rs['id_lend_detail']);
				if(!$qa){ $rd = false; }	
			}
		}
	}
	return $rd;
}

?>