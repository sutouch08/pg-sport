<?php
class Cart_model extends CI_Model
{
	public function __construct()
	{
		parent:: __construct();	
	}
	
	public function getCartProduct($id_cart)
	{
		$rs = $this->db->where('id_cart', $id_cart)->get('tbl_cart_product');
		if( $rs->num_rows() > 0 )
		{
			return $rs->result();
		}
		else
		{
			return FALSE;
		}
	}
	
	public function createCart($data)
	{
		$rs = $this->db->insert('tbl_cart', $data);
		if( $rs )
		{
			return $this->db->insert_id();
		}
		else
		{
			return FALSE;
		}
	}
	
	public function addToCart($id_cart, $id_pa, $qty, $date_add)
	{
		return $this->db->insert('tbl_cart_product', array('id_cart' => $id_cart, 'id_product_attribute' => $id_pa, 'qty' => $qty, 'date_add' => $date_add) );	
	}
	
	public function cartValue($id_cart = 0)
	{
		$value = 0.00;
		if( $id_cart != 0 )
		{
			$rs = $this->db->join('tbl_cart', 'tbl_cart.id_cart = tbl_cart_product.id_cart')->where('tbl_cart_product.id_cart', $id_cart)->get('tbl_cart_product');
			if( $rs->num_rows() > 0 )
			{
				foreach($rs->result() as $rd)
				{
					$id_pd 		= getIdProduct($rd->id_product_attribute);
					$qty 			= $rd->qty;
					$price 		= itemPrice($rd->id_product_attribute);
					$value 		+= ($price * $qty);
				}
			}
		}
		return $value;
	}
	
	public function cartQty($id_cart = 0)
	{
		$qty = 0;
		if( $id_cart != 0 )
		{
			$rs = $this->db->select_sum('qty')->where('id_cart', $id_cart)->get('tbl_cart_product');
			if( $rs->row()->qty != 0 && $rs->row()->qty !== NULL )
			{
				$qty = $rs->row()->qty;
			}
		}
		return $qty;
	}
	
	public function updateCartProduct($id_cart, $id_pa, $qty)
	{
		$rs = $this->db->where('id_cart', $id_cart)->where('id_product_attribute', $id_pa)->update('tbl_cart_product', array('qty'=>$qty));
		return $rs;	
	}
	
	public function deleteCartProduct($id_cart, $id_pa)
	{
		return $this->db->where('id_cart', $id_cart)->where('id_product_attribute', $id_pa)->delete('tbl_cart_product');	
	}

}// end class;


?>