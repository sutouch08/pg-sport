<?php 
class Product extends CI_Controller
{
	public $home;
	public $layout = "include/template";
	public $title = "รายละเอียดสินค้า";
	public $id_customer;
	public $cart_value;
	
	public function __construct()
	{
		parent::__construct();		
		$this->load->model("product_model");
		$this->home = base_url()."shop/product";
		$this->id_customer = getIdCustomer();	
		$this->cart_value	= cartValue(getIdCart($this->id_customer)); 
	}	
	
	public function index()
	{
		
	}
	
	public function addToCart($id_cus, $id_cart)
	{
		$this->load->model('cart_model');
		$sc = TRUE;
		if( $id_cart == 0 )
		{
			$id_c = $this->cart_model->createCart( array( 'id_customer' => $id_cus, 'id_sale' => getIdSaleByCustomer($id_cus), 'date_add' => NOW() ) );
		}
		else
		{
			$id_c = $id_cart;
		}
		if( $id_c !== FALSE )
		{
			$qtys = $this->input->post('qty');
			foreach($qtys as $id_pa => $qty)
			{
				if( $qty != '')
				{
					$rs = $this->cart_model->addToCart($id_c, $id_pa, $qty, NOW());	
				}
			}
			echo 'success';
		}
		else
		{
			echo 'fail';
		}
	}
	
	public function orderGrid()
	{
		if( $this->input->post('id_pd') )
		{
			$id_pd 	= $this->input->post('id_pd');
			// ต้องการรู้ว่า สินค้า มีกี่ Attr และ อะไรบ้าง ลำดับเป็นยังไง 
			 // return ค่ากลับมาเป็น Array ('length' => 1-3, 'horizontal ' => เอาอะไรอยู่ด้านหัว , 'vertical' => เอาอะไรอยู่ด้านข้าง, 'tab' => อะไรอยู่ tab เสริม
			$attrs 	= $this->product_model->getAttrs($id_pd); 
			$count 	= $attrs['length'];
			if( $count == 1 ){ $grid = $this->getOrderGridOneAttr($id_pd, $attrs); }
			if( $count == 2 ){ $grid = $this->getOrderGridTwoAttr($id_pd, $attrs); }
			if( $count == 3 ){ $grid = $this->getOrderGridThreeAttr($id_pd, $attrs); }
			echo $grid;
			
		}
	}
	
	public function orderGridWithFilter()
	{
		if( $this->input->post('filter')  )
		{
			$filter 	= json_decode($this->input->post('filter'));
			$id_pd 	= $this->input->post('id_pd');
			$c 		= $this->input->post('count');
			if( $c == 1 )		// 1 Filter
			{	
				$horizontal 	= $filter->attr;
				$vertical		= 'size';
				$id_filter		= $filter->id;	
				$grid = $this->getOrderGridWithOneFilter($id_pd, $id_filter, $horizontal, $vertical);
			}
			else if( $c == 2 )
			{
				$horizontal	= 'color';
				$vertical		= 'size';
				$id_color		= $filter->color;
				$id_attribute	= $filter->attribute;
				$grid = $this->getOrderGridWithTwoFilter($id_pd, $id_color, $id_attribute, $horizontal, $vertical);
			}
						
			echo $grid;		
		}
	}
	
	public function attrLabel($attr, $id)
	{
		$label = '';
		switch( $attr )
		{
			case 'color' :
				$label = color_code($id).' | '.color_name($id);
			break;
			case 'size' :
			$label = size_name($id);
			break;
			case 'attribute' :
			$label = attribute_name($id);
			break;
			default :
			$label = 'No Attr';
		}
		return $label;
	}
	
	public function createHeaderRow($id_pd, array $attrs)
	{
		$horizontal	= $attrs['horizontal'];
		$vertical		= $attrs['vertical'];
		$rs			= $this->product_model->getHeaderRow($id_pd, $horizontal, $vertical);
		$width		= 120;
		$ds 			= '';
		if( $rs )
		{
			$ds .= '<tr><td style="width: 120px; height: 53px; text-align:center; vertical-align:middle;">'.$vertical.' | '.$horizontal.'</td>';
			foreach( $rs as $rd )
			{
				$ds .= '<td style="width:120px; text-align:center; vertical-align:middle;">'.$this->attrLabel($horizontal, $rd->id).' </td>';
				$width += 120;
			}
			$ds .= '</tr>';
		}
		$data = array('width' => $width, 'header' => $ds);
		return $data;		
	}
	

	public function createTabs($id_pd, $tab)
	{
		$ds = '';
		$tabs = $this->product_model->getTabs($id_pd, $tab);
		if( $tabs )
		{
			$active 	= 'active';
			$ds 		.= '<ul class="nav nav-tabs">';
			$n			= 1;
			foreach( $tabs as $rs )
			{
				$ds .= '<li class="'.$active.'"><a href="#Tab'.$n.'" data-toggle="tab">'.$this->attrLabel($tab, $rs->id).'</a></li>';
				$active = '';
				$n++;
			}
			$ds .= '</ul>';
		}
		return $ds;
	}
	
	public function getOrderGridWithOneFilter($id_pd, $id_filter, $horizontal = 'color', $vertical = 'size')
	{
		$width = 240;
		$id		= 'id_'.$horizontal;
		$ds 	= '<table class="table table-bordered" style="width:100%;">';
		$ds 	.= '<tr><td style="width: 120px; height: 53px; text-align:center; vertical-align:middle;">'.$vertical.' | '.$horizontal.'</td>';
		$ds 	.= '<td style="width:120px; text-align:center; vertical-align:middle;">'.$this->attrLabel($horizontal, $id_filter).' </td>';
		$ds	.= '</tr>';
		$qs 	= $this->product_model->getVertical($id_pd, $vertical);
		if( $qs !== FALSE )
		{
			foreach( $qs as $rd )
			{
				$ds .= '<tr><td align="center" style="vertical-align:middle; height: 53px;">'.$this->attrLabel($vertical, $rd->id).'</td>';
				$rx = $this->product_model->get_id_product_attribute_by_attrs($id_pd, $horizontal, $id_filter, $vertical, $rd->id, 'attribute', 0);
				if( $rx )
				{
					$qty = apply_stock_filter($this->product_model->getAvailableQty($rx->id_pa));
					$ds .= '<td align="center" style="vertical-align:middle;">';
					if( $qty > 0 )
					{
						$ds .= '<input type="text" class="form-control input-sm input-qty" style="margin-bottom:0px;" id="qty_'.$rx->id_pa.'" name="qty['.$rx->id_pa.']" placeholder="'.$qty.' in stock" onkeyup="validQty($(this), '.$qty.')" />';	
					}
					else
					{
						$ds .= '<span style="color:#E6E9ED">สินค้าหมด</span>';
					}
					$ds .= '</td>';
				}
				else
				{
					$ds .= '<td align="center" style="vertical-align:middle;">';
					$ds .= '<span style="color:#E6E9ED">ไม่มีสินค้า</span>';
					$ds .= '</td>';
				}
				$ds .= '</tr>';
			}
		}
		$ds .= '</table>';
		$width = $width > 300 ? $width : 300;
		$data = $width.' || '.$ds;
		return $data;
	}
	
	
	public function getOrderGridTwoAttr($id_pd, array $attrs)
	{
		$horizontal	= $attrs['horizontal'];
		$vertical		= $attrs['vertical'];
		$header		= $this->createHeaderRow($id_pd, $attrs);
		$width 		= $header['width'];
		$ds 			= '<table class="table table-bordered" style="width:100%;">';
		$ds 			.= $header['header'];
								
		$rs = $this->product_model->getVertical($id_pd, $vertical);
		if( $rs )
		{
			foreach( $rs as $rd )
			{
				$ds .= '<tr><td align="center" style="vertical-align:middle; height: 53px;">'.$this->attrLabel($vertical, $rd->id).'</td>';
				$ra = $this->product_model->getHorizontal($id_pd, $horizontal);
				foreach( $ra as $rm )
				{				
					$rx = $this->product_model->get_id_product_attribute_by_attrs($id_pd, $horizontal, $rm->id, $vertical, $rd->id, 'attribute', 0);
					if( $rx )
					{
						$qty = apply_stock_filter($this->product_model->getAvailableQty($rx->id_pa));
						$ds .= '<td align="center" style="vertical-align:middle;">';
						if( $qty > 0 )
						{
							$ds .= '<input type="text" class="form-control input-sm input-qty" style="margin-bottom:0px;" id="qty_'.$rx->id_pa.'" name="qty['.$rx->id_pa.']" placeholder="'.$qty.' in stock" onkeyup="validQty($(this), '.$qty.')" />';	
						}
						else
						{
							$ds .= '<span style="color:#E6E9ED">สินค้าหมด</span>';
						}
						$ds .= '</td>';
					}
					else
					{
						$ds .= '<td align="center" style="vertical-align:middle;">';
						$ds .= '<span style="color:#E6E9ED">ไม่มีสินค้า</span>';
						$ds .= '</td>';
					}
				}
				$ds .= '</tr>';
			}
		}
		
		$ds .= '</table>';
		$width = $width > 300 ? $width : 300;
		$data = $width.' || '.$ds;
		return $data;
	}
	
	public function getOrderGridWithTwoFilter($id_pd, $id_color, $id_attribute, $horizontal = 'color', $vertical = 'size')
	{
		$id		= 'id_'.$horizontal;
		$ds 	= '<table class="table table-bordered" style="width:100%;">';
		$ds 	.= '<tr><td style="width: 200px; height: 53px; text-align:center; vertical-align:middle;">'.$vertical.' | '.$horizontal.'</td>';
		$ds 	.= '<td style="width:120px; text-align:center; vertical-align:middle;">'.$this->attrLabel($horizontal, $id_color).' </td>';
		$ds	.= '</tr>';
		$qs 	= $this->product_model->getVertical($id_pd, $vertical);
		if( $qs !== FALSE )
		{
			foreach( $qs as $rd )
			{
				$ds .= '<tr><td align="center" style="vertical-align:middle; height: 53px;">'. $this->attrLabel('attribute', $id_attribute).' | '.$this->attrLabel($vertical, $rd->id).'</td>';
				$rx = $this->product_model->get_id_product_attribute_by_attrs($id_pd, $horizontal, $id_color, $vertical, $rd->id, 'attribute', $id_attribute);
				if( $rx )
				{
					$qty = apply_stock_filter($this->product_model->getAvailableQty($rx->id_pa));
					$ds .= '<td align="center" style="vertical-align:middle;">';
					if( $qty > 0 )
					{
						$ds .= '<input type="text" class="form-control input-sm input-qty" style="margin-bottom:0px;" id="qty_'.$rx->id_pa.'" name="qty['.$rx->id_pa.']" placeholder="'.$qty.' in stock" onkeyup="validQty($(this), '.$qty.')" />';	
					}
					else
					{
						$ds .= '<span style="color:#E6E9ED">สินค้าหมด</span>';
					}
					$ds .= '</td>';
				}
				else
				{
					$ds .= '<td align="center" style="vertical-align:middle;">';
					$ds .= '<span style="color:#E6E9ED">ไม่มีสินค้า</span>';
					$ds .= '</td>';
				}
				$ds .= '</tr>';
			}
		}
		$ds .= '</table>';
		$data = '320 || '.$ds;
		return $data;
	}
	
	public function getOrderGridThreeAttr($id_pd, array $attrs)
	{
		$tab		= $attrs['tab'];
		$ver		= $attrs['vertical'];
		$hor		= $attrs['horizontal'];	
		$width	= 120;
		$ds 		= '';
		$ds		.= $this->createTabs($id_pd, $tab);
		$qs 		= $this->product_model->getTabs($id_pd, $tab);
		
		if( $qs )
		{
			$ds .= '<!-- Tab content --><div class="tab-content">';
			$n = 1;
			$active = 'active';
			foreach( $qs as $rs )
			{
				$ds .= '<div class="tab-pane '.$active.'" id="Tab'.$n.'">';
 				$grid = $this->getOrderGridTwoAttrByTabs($id_pd, $hor, $ver, $rs->id);
				$ds .= $grid['table'];				
				$ds .= '</div>';
				$n++;
				$active = '';
				$width = $width > $grid['width'] ? $width : $grid['width'];
			}
			
			$ds .= '</div><!-- /.tab content -->';	
		}
		
		$width = $width > 300 ? $width : 300;
		$data = $width.' || '.$ds;
		return $data;		
	}
	
	public function getOrderGridOneAttr($id_pd, array $attrs)
	{
		$attr 	= $attrs['horizontal'];
		$qs 	= 'SELECT id_product_attribute AS id_pa, id_'.$attr.' AS id FROM tbl_product_attribute WHERE id_product = '.$id_pd;
		$rs	= $this->db->query($qs);
		$ds 	= '<table class="table table-bordered" style="width:100%;">';
		if( $rs->num_rows() > 0)
		{
			foreach( $rs->result() as $rd )
			{
				$ds 	.= '<tr>';
				$ds 	.= '<td align="center" style="width:50%; vertical-align:middle; height: 53px;">'.$this->attrLabel($attr, $rd->id).'</td>'; 
				$qty 	= apply_stock_filter($this->product_model->getAvailableQty($rd->id_pa));
				$ds .= '<td align="center" style="vertical-align:middle;">';
				if( $qty > 0 )
				{
					$ds .= '<input type="text" class="form-control input-sm input-qty" style="margin-bottom:0px;" id="qty_'.$rd->id_pa.'" name="qty['.$rd->id_pa.']" placeholder="'.$qty.' in stock" onkeyup="validQty($(this), '.$qty.')" />';	
				}
				else
				{
					$ds .= '<span style="color:#E6E9ED">สินค้าหมด</span>';
				}
				$ds .= '</td>';
				$ds .= '</tr>';
			}
		}
		
		$ds	.= '</table>';
		$data = '300 || '.$ds;
		return $data;	
	}
	
	public function getOrderGridTwoAttrByTabs($id_pd, $horizontal, $vertical, $id_tab)
	{
		$attrs			= array('horizontal' => $horizontal, 'vertical' => $vertical);
		$header		= $this->createHeaderRow($id_pd, $attrs);
		$width 		= $header['width'];
		$ds 			= '<table class="table table-bordered" style="width:100%;">';
		$ds 			.= $header['header'];
								
		$rs = $this->product_model->getVertical($id_pd, $vertical);
		if( $rs )
		{
			foreach( $rs as $rd )
			{
				$ds .= '<tr><td align="center" style="vertical-align:middle; height: 53px;">'.$this->attrLabel($vertical, $rd->id).'</td>';
				$ra = $this->product_model->getHorizontal($id_pd, $horizontal);
				foreach( $ra as $rm )
				{				
					$rx = $this->product_model->get_id_product_attribute_by_attrs($id_pd, $horizontal, $rm->id, $vertical, $rd->id, 'attribute', $id_tab);
					if( $rx )
					{
						$qty = apply_stock_filter($this->product_model->getAvailableQty($rx->id_pa));
						$ds .= '<td align="center" style="vertical-align:middle;">';
						if( $qty > 0 )
						{
							$ds .= '<input type="text" class="form-control input-sm input-qty" style="margin-bottom:0px;" id="qty_'.$rx->id_pa.'" name="qty['.$rx->id_pa.']" placeholder="'.$qty.' in stock" onkeyup="validQty($(this), '.$qty.')" />';	
						}
						else
						{
							$ds .= '<span style="color:#E6E9ED">สินค้าหมด</span>';
						}
						$ds .= '</td>';
					}
					else
					{
						$ds .= '<td align="center" style="vertical-align:middle;">';
						$ds .= '<span style="color:#E6E9ED">ไม่มีสินค้า</span>';
						$ds .= '</td>';
					}
				}
				$ds .= '</tr>';
			}
		}
		
		$ds .= '</table>';
		$data = array('width' => $width, 'table' => $ds);
		return $data;
	}
}/// end class

?>