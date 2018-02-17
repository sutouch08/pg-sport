<?php

function getIdCart($id_cus)
{
	$id = 0;
	$rs = get_instance()->db->select('id_cart')->where('id_customer', $id_cus)->where('valid', 0)->get('tbl_cart');
	if( $rs->num_rows() == 1 )
	{
		$id = $rs->row()->id_cart;	
	}
	return $id;
}

function cartValue($id_cart = 0)
{
	$value = 0.00;
	if( $id_cart != 0 )
	{
		$rs = get_instance()->db->join('tbl_cart', 'tbl_cart.id_cart = tbl_cart_product.id_cart')->where('tbl_cart_product.id_cart', $id_cart)->get('tbl_cart_product');
		if( $rs->num_rows() > 0 )
		{
			foreach($rs->result() as $rd)
			{
				$id_pd 		= getIdProduct($rd->id_product_attribute);
				$qty 			= $rd->qty;
				$price 		= itemPrice($rd->id_product_attribute);
				$dis			= get_discount($rd->id_customer, $id_pd);
				$sell_price	= sell_price($price, $dis['discount'], $dis['type']);
				$value += $sell_price;
			}
		}
	}
	return $value;
}

function delivery_cost($qty = 0)
{
	$cost = 0;
	if( $qty > 0 )
	{
		$extra = $qty -1;
		$basic = 50;
		$cost = $basic + ($extra * 10 );
	}
	return $cost;	
}

?>