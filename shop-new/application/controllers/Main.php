<?php 
class Main extends CI_Controller
{

	public $home;
	public $layout = "include/template";
	public $title = "ยินดีต้อนรับ";
	public $id_customer;
	public $id_cart;
	public $cart_value;
	public $cart_items;
	public $cart_qty;
	
	public function __construct()
	{
		parent::__construct();		
		
		$this->load->model("main_model");
		$this->load->model('product_model');
		$this->load->model('cart_model');
		$this->home = base_url()."shop/main";
		$this->id_customer = getIdCustomer();
		$this->id_cart 	= getIdCart($this->id_customer);
		$this->cart_value	= $this->cart_model->cartValue($this->id_cart);
		$this->cart_items 	= $this->cart_model->getCartProduct($this->id_cart);
		$this->cart_qty		= $this->cart_model->cartQty($this->id_cart);
		
	}
	
	public function index()
	{
		$data['title']				= $this->title;
		$data['new_arrivals'] 	= $this->main_model->new_arrivals();
		$data['features']		= $this->main_model->features();
		$data['cart_items']		= $this->cart_items;
		$data['view'] 			= 'main';			
		
		$this->load->view($this->layout, $data);
	}
	
	public function productDetail($id_pd)
	{
		$data['title']		= 'Product Details';
		$data['pd'] 	= $this->product_model->getProductDetail($id_pd);
		$data['images']	= $this->product_model->productImages($id_pd);
		$data['count_attrs']	= $this->product_model->getAttrs($id_pd);
		$data['cart_items']		= $this->cart_items;
		$data['product_info']	= $this->product_model->getProductInfo($id_pd);
		$data['view']				= 'product_detail';
		
		$this->load->view($this->layout, $data);	
	}
		
	public function loadMoreFeatures()
	{
		$this->load->model('main_model');
		$data = array();
		if( $this->input->post('offset') )
		{
			$result = $this->main_model->moreFeatures($this->input->post('offset'));
			if( $result !== FALSE )
			{
				foreach( $result as $rs )
				{
					$promo = 0;
					if( $rs->discount != 0 OR is_new_product($rs->id_product) )
					{
						$promo = 1;
					}
					$arr = array(
								'link'				=>	'main/productDetail/'.$rs->id_product,
								'image_path'		=> get_image_path(get_id_cover_image($rs->id_product), 3),
								'promotion'		=> $promo,
								'new_product'	=> is_new_product($rs->id_product) === TRUE ? 1 : 0,
								'discount'			=> intval($rs->discount),
								'discount_label'	=> discount_label($rs->discount, $rs->discount_type),
								'product_code'	=> $rs->product_code,
								'product_name'	=> $rs->product_name,
								'sell_price'		=> sell_price($rs->product_price, $rs->discount, $rs->discount_type),
								'price'				=> $rs->product_price
						);	
					array_push($data, $arr);
				}
				echo json_encode($data);
			}
			else
			{
				echo 'none';	
			}
		}
		else
		{
			echo 'none';
		}
	}
	
	public function cart($id)
	{
		$data['title']				= 'Cart detail';
		$data['cart_items']		= $this->cart_items;
		$data['view'] 			= 'cart';			
		
		$this->load->view($this->layout, $data);
		
	}
	
}/// end class


?>