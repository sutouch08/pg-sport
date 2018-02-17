<?php
function get_customer_discount($id_cus, $id_pd)
{
	$disc = 0;  /// ส่งคืนส่วนลดที่ดีที่สุด เป็น เปอร์เซ็นต์ เสมอ
	$cates = get_product_category($id_pd);
	if( $cates !== FALSE )
	{
		$init = array();
		foreach( $cates as $cate )
		{
			array_push($init, $cate->id_category);
		}
		$c =& get_instance();
		$c->db->select('discount');
		$c->db->where('id_customer', $id_cus);
		$c->db->where_in('id_category', $init);
		$c->db->order_by('discount', 'desc');
		$rs = $c->db->get('tbl_customer_discount');
		if( $rs->num_rows() > 0 )
		{
			$disc = $rs->row()->discount;	
		}
	}
	return $disc;
}


function getIdCustomer()
{
	$id_customer = 0;
	$c =& get_instance();
	if( !$c->input->cookie("id_customer") )
	{
		$id_customer = uniqid();
		$c->input->set_cookie("id_customer", $id_customer, (3600*24*14));
	}
	else
	{
		$id_customer = $c->input->cookie("id_customer");
	}
	return $id_customer;
}

function customerName($id)
{
	$name = '';
	$c =& get_instance();
	$rs = $c->db->select('first_name, last_name')->where('id_customer', $id)->get('tbl_customer');
	if( $rs->num_rows() == 1 )
	{
		$name = $rs->row()->first_name.' '.$rs->row()->last_name;
	}
	return $name;
}

function userNameByCustomer($id)
{
	$userName = '';
	$c =& get_instance();
	$rs = $c->db->select('user_name')->where('id_customer', $id)->get('tbl_user');
	if( $rs->num_rows() == 1 )
	{
		$userName = $rs->row()->user_name;
	}
	return $userName;
}

function getIdSaleByCustomer($id_cus)
{
	$id_sale = 0;
	$rs = get_instance()->db->select('id_sale')->where('id_customer', $id_cus)->get('tbl_customer');
	if( $rs->num_rows() == 1 )
	{
		$id_sale = $rs->row()->id_sale;
	}
	return $id_sale;
}
		

?>