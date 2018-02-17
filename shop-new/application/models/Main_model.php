<?php
class Main_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function new_arrivals()
	{
		$items	= getConfig('NEW_PRODUCT_QTY');
		$days	= getConfig('NEW_ARRIVAL_DAYS');
		$from		= fromDate(beforeDate($days, date('Y-m-d')));
		$to		= toDate(NOW());
		///  ใช้ตอนทดสอบ
		$qs	= $this->db->where('active', 1)->where('show_in_shop', 1)->where('date_upd >=', $from)->where('date_upd <=', $to)->order_by('date_upd', 'desc')->limit($items)->get('tbl_product');
		/// ใช้จริง
		//$qs	= $this->db->where('show_in_shop', 1)->where('active', 1)->where('date_add >=', $from)->where('date_add <=', $to)->order_by('date_add', 'desc')->limit($items)->get('tbl_product');
		
		if( $qs->num_rows() > 0 )
		{
			return $qs->result();	
		}
		else
		{
			return false;
		}
	}

	
	public function features()
	{
		$item	= getConfig('FEATURES_PRODUCT');		
		$this->db->select('tbl_product.id_product, product_code, product_name, product_price, discount_type, discount');
		$this->db->from('tbl_product')->join('tbl_category_product', 'tbl_category_product.id_product = tbl_product.id_product');
		//-- ใช้ตอนทดสอบ
		$this->db->where('active', 1)->where('id_category', 0)->order_by('date_add', 'desc')->limit($item);
		//-- ใช้จริง
		//$this->db->where('show_in_shop', 1)->where('active', 1)->where('id_category', 0)->order_by('date_add', 'desc')->limit($item);
		$rs	= $this->db->get();
		if( $rs->num_rows() > 0 )
		{
			return $rs->result();	
		}
		else
		{
			return FALSE;
		}	
	}
	
	public function moreFeatures($offset)
	{
		$item	= getConfig('FEATURES_PRODUCT');
		$this->db->select('tbl_product.id_product, product_code, product_name, product_price, discount_type, discount');
		$this->db->from('tbl_product')->join('tbl_category_product', 'tbl_category_product.id_product = tbl_product.id_product');
		//--- ใช้ตอนทดสอบ
		$this->db->where('active', 1)->where('id_category', 0)->order_by('date_add', 'desc')->limit($item, $offset);
		
		//--- ใช้จริง
		//$this->db->where('active', 1)->where('show_in_shop', 1)->where('id_category', 0)->order_by('date_add', 'desc')->limit($item, $offset);
		$rs	= $this->db->get();
		if( $rs->num_rows() > 0 )
		{
			return $rs->result();	
		}
		else
		{
			return FALSE;
		}	
	}

}
/// end class


?>