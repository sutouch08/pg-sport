<?php
class Product_detail extends CI_Controller
{
	public $home;
	public $layout = "include/template";
	public $title = "รายละเอียดสินค้า";
	public $id_customer;
	
	public function __construct()
	{
		parent::__construct();		
		$this->load->model("product_model");
		$this->home = base_url()."product_detail";
		$this->id_customer = getIdCustomer();		
	}
	
	public function index($id_pd)
	{
		$this->load->helper('value');
		$this->load->model('product_model');
		$data['pd'] 	= $this->product_model->getProductDetail($id_pd);
		$data['images']	= $this->product_model->productImages($id_pd);
		//$data['pinfo']	= $this->product->model->getProductInfo($id_pd);
		$data['view']					= 'product_detail';
		
		$this->load->view($this->layout, $data);
	}
	
}
/// end class

?>