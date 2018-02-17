<?php
class pos_order
{
  public $id_order;
	public $reference;
	public $id_customer;
	public $customer_name;
	public $id_employee;
	public $id_sale;
	public $id_cart;
	public $current_state;
	public $current_state_name;
	public $payment;
	public $comment;
	public $valid;
	public $role;
	public $role_name;
	public $date_add;
	public $date_upd;
	public $order_status;
	public $total_product;
	public $total_qty;
	public $total_amount;
	public $state_color;
	public $error_message;
	public $id_order_detail ;
	public $id_product;
	public $id_product_attribute;
	public $product_name;
	public $product_qty;
	public $product_reference;
	public $barcode;
	public $product_price;
	public $reduction_precent;
	public $reduction_amount;
	public $discount_amount;
	public $final_price;

  public function __construct($id='')
  {
    if($id != '')
    {
      $qs = dbQuery("SELECT * FROM tbl_order WHERE id_order = '".$id."' AND role = 11");
      if(dbNumRows($qs) == 1)
      {
        $ds = dbFetchArray($qs);
        foreach($ds as $key => $value)
        {
          $this->$key = $value;
        }
      }
    }
  }



}
 ?>
