<?php
class Cart extends CI_Controller
{
	public $home;
	public $layout = "include/template";
	public $title = "ตะกร้าสินค้า";
	public $id_customer;
	public $id_cart;
	public $cart_value;
	public $cart_items;
	public $cart_qty;
	public function __construct()
	{
		parent:: __construct();
		$this->load->model('product_model');
		$this->load->model('cart_model');
		$this->id_customer = getIdCustomer();
		$this->id_cart 	= getIdCart($this->id_customer);
		$this->cart_value	= $this->cart_model->cartValue($this->id_cart);
		$this->cart_items 	= $this->cart_model->getCartProduct($this->id_cart);
		$this->cart_qty		= $this->cart_model->cartQty($this->id_cart);	
	}
	
	public function index()
	{
		
	}
	
	public function getCartQty()
	{
		if( $this->input->post('id_cart') )
		{
			$id_cart = $this->input->post('id_cart');
			if( $id_cart != 0 )
			{
				$qty = $this->cart_model->cartQty($id_cart);
				echo $qty;	
			}
			else
			{
				echo 'no_item';
			}				
		}
	}
	
	public function updateCart()
	{
		if( $this->input->post('id_cart') )
		{
			$id_cart 	= $this->input->post('id_cart');
			$id_pa 	= $this->input->post('id_pa');
			$qty 		= $this->input->post('qty');	
			$rs = $this->cart_model->updateCartProduct($id_cart, $id_pa, $qty);
			if( $rs )
			{
				echo 'success';
			}
			else
			{
				echo 'fail';
			}
		}
	}
	
	public function deleteCartProduct()
	{
		if( $this->input->post('id_cart') && $this->input->post('id_pa') )
		{
			$id_cart = $this->input->post('id_cart');
			$id_pa 	= $this->input->post('id_pa');
			$rs = $this->cart_model->deleteCartProduct($id_cart, $id_pa);
			if( $rs )
			{
				echo 'success';
			}
			else
			{
				echo 'fail';
			}
		}
	}
	
}/// end class

?>