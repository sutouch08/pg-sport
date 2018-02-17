<?php 
class event
{

	public function __construct($id = "")
	{
		if($id != "")
		{
			$qs = dbQuery("SELECT * FROM tbl_order_event WHERE id_order_event = ".$id);
			if(dbNumRows($qs) == 1 )
			{
				$rs = dbFetchArray($qs);
				$this->id_order_event	= $rs['id_order_event'];
				$this->id_order			= $rs['id_order'];
				$this->id_event_sale 		= $rs['id_event_sale'];
				$this->id_employee		= $rs['id_employee'];
				$this->id_zone				= $rs['id_zone'];
				$this->event_name		= $rs['event_name'];
				$this->date_add			= $rs['date_add'];
				$this->status				= $rs['status'];
			}
		}
	}
	
	public function save_order($id_order)
	{
		$qs = dbQuery("UPDATE tbl_order SET order_status = 1 WHERE id_order = ".$id_order);
		if($qs)
		{
			dbQuery("UPDATE tbl_order_event	SET status = 1 WHERE id_order = ".$id_order);
		}
		return $qs;
	}
	
	
	public function get_detail_data($id_order)
	{
		$qs = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = ".$id_order);
		return $qs;	
	}
	
	public function get_detail($id_order_detail)
	{
		$qs = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order_detail = ".$id_order_detail);
		if(dbNumRows($qs) == 1)
		{
			return dbFetchArray($qs);	
		}
		else
		{
			return false;
		}
	}
	
	public function delete_item($id)
	{
		$rs = dbQuery("DELETE FROM tbl_order_detail WHERE id_order_detail = ".$id);
		return $rs;
	}
	
	public function update_detail($id_order_detail, $id_product_attribute, $qty)
	{
		$rd = $this->get_detail($id_order_detail);
		if($rd)
		{
			$qty 			= $qty + $rd['product_qty'];
			$amount  	= $qty * $rd['final_price'];
			
			$rs = dbQuery("UPDATE tbl_order_detail SET product_qty = ".$qty.", total_amount = ".$amount." WHERE id_order_detail = ".$id_order_detail);
			return $rs;			
		}
		else
		{
			return false;	
		}
	}
	
	public function insert_detail($id_order, $id_product_attribute, $qty)
	{
		$product 			= new product();
		$id_product 		= $product->getProductId($id_product_attribute);
		$product->product_detail($id_product);
		$product->product_attribute_detail($id_product_attribute);
		$product_name		= $product->product_name;
		$barcode 			= $product->barcode;
		$product_price 	= $product->product_price;
		$final_price 			= $product->product_sell;
		$reference 			= $product->reference;
		$total_amount 		= $product->product_sell * $qty;
		$re_percent 		= 0.00;
		$re_amount 			= 0.00;
		$discount_amount 	= 0.00;
		
		$qs = "INSERT INTO tbl_order_detail ";
		$qs .= "(id_order, id_product, id_product_attribute, product_name, product_qty, product_reference, barcode, product_price, reduction_percent, reduction_amount, discount_amount, final_price, total_amount, valid_detail )";
		$qs .= " VALUES ";
		$qs .= "($id_order, $id_product, $id_product_attribute, '$product_name', $qty, '$reference', '$barcode', $product_price, $re_percent, $re_amount, $discount_amount, $final_price, $total_amount, 0 )";
		
		$rs = dbQuery($qs);
		return $rs;
	}
	
	public function isExists($id_order, $id_product_attribute)
	{
		$rs = 0;
		$qs = dbQuery("SELECT id_order_detail FROM tbl_order_detail WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_product_attribute);
		if(dbNumRows($qs) == 1 )
		{
			list($rs) = dbFetchArray($qs);			
		}
		return $rs;
	}
	
	public function update_order($id_order_event, array $data)
	{
		$rs = false;
		$qs = dbQuery("UPDATE tbl_order_event SET event_name = '".$data['event_name']."' WHERE id_order_event = ".$id_order_event);
		if($qs)
		{
			$qr = dbQuery("UPDATE tbl_order SET comment = '".$data['remark']."' WHERE id_order = ".$data['id_order']);
			if($qr)
			{
				$rs = true;
			}
		}
		return $rs;				
	}
	
	public function add_order(array $rs)
	{
		$qr = "INSERT INTO tbl_order (reference, id_customer, id_employee, id_cart, id_address_delivery, current_state, payment, comment, valid, role, date_add, order_status) VALUES ";
		$qr .= "('".$rs['reference']."', ".$rs['id_customer'].", ".$rs['id_employee'].", ".$rs['id_cart'].", ".$rs['id_address_delivery'].", ".$rs['current_state'].", '".$rs['payment']."', '".$rs['comment']."', ";
		$qr .= $rs['valid'].", ".$rs['role'].", '".$rs['date_add']."', ".$rs['order_status'].")";
		$qs = dbQuery($qr);
		if($qs)
		{
			$id = dbInsertId();
		}
		else
		{
			$id = 0;
		}
		return $id;
	}
	
	public function add_order_event(array $rs)
	{
		$qr = "INSERT INTO tbl_order_event (id_order, id_event_sale, id_employee, id_zone, event_name, date_add, status) VALUES ";
		$qr .= "(".$rs['id_order'].", ".$rs['id_event_sale'].", ".$rs['id_employee'].", ".$rs['id_zone'].", '".$rs['event_name']."', '".$rs['date_add']."', ".$rs['status'].")";
		$qs = dbQuery($qr);
		if($qs)
		{
			$id = dbInsertId();
		}
		else
		{
			$id = 0;
		}
		return $id;
	}
	
public function input_field($id, $qty)
{
	if($qty > 0)
	{ 
		$rs = "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>	<input type='text' class='form-control' name='qty[".$id."]' onkeyup='valid_qty($(this), ".$qty.")' /><center>".number_format($qty)."</center></td>";
	}
	else
	{
		$rs = "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>	<center>ไม่มีสินค้า</center></td>";
	}
	return $rs;
}

public function no_field()
{
	$rs = "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>	<center>ไม่มีสินค้า</center></td>";
	return $rs;	
}
//*************************************  Event attribute Grid  *****************************//

public function event_attribute_grid($id_product, $id_zone)
{
	$result = "";
	/**********************  ตรวจสอบว่าสินค้ามีกี่คุณลักษณะ *********************/
	$q_color 	= dbQuery("SELECT id_product_attribute, tbl_product_attribute.id_color AS id, color_code AS code  FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 ");
	$q_size 		= dbQuery("SELECT id_product_attribute, tbl_product_attribute.id_size AS id, size_name AS code FROM tbl_product_attribute JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size WHERE id_product = ".$id_product." AND tbl_product_attribute.id_size != 0");
	$q_attribute	= dbQuery("SELECT id_product_attribute, tbl_product_attribute.id_attribute AS id, attribute_name AS code FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0");
	$color 	= "SELECT tbl_product_attribute.id_color AS id FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC ";
	$size 	= "SELECT tbl_product_attribute.id_size AS id FROM tbl_product_attribute JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size WHERE id_product = ".$id_product." AND tbl_product_attribute.id_size != 0 GROUP BY tbl_product_attribute.id_size  ORDER BY position ASC ";
	$attribute	= "SELECT tbl_product_attribute.id_attribute AS id FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0 GROUP BY tbl_product_attribute.id_attribute  ORDER BY position ASC ";
	$rc 			= dbNumRows($q_color);
	$rs			= dbNumRows($q_size);
	$ra			= dbNumRows($q_attribute);
	$c_count		= "";
	if($rc >0){ $c_count .= "1"; }else{ $c_count .= "0"; } /*** ถ้ามีแถวให้นำ 1 มาต่อกันถ้าไม่มีนำ 0 มาต่อ เพื่อใช้ตรวจสอบเงื่อนไข ****/
	if($rs >0){ $c_count .= "1"; }else{ $c_count .= "0"; } /*** ถ้ามีแถวให้นำ 1 มาต่อกันถ้าไม่มีนำ 0 มาต่อ เพื่อใช้ตรวจสอบเงื่อนไข ****/
	if($ra >0){ $c_count .= "1"; }else{ $c_count .= "0"; } /*** ถ้ามีแถวให้นำ 1 มาต่อกันถ้าไม่มีนำ 0 มาต่อ เพื่อใช้ตรวจสอบเงื่อนไข ****/
	
	if($c_count == "100" || $c_count == "010" || $c_count == "001") /// กรณีมี คุณลักษณะเดียว
	{
		if($rc > 0){ $data = $q_color; }else if($rs > 0 ){ $data = $q_size; }else{ $data = $q_attribute; }
		$result .= "<table class='table table-bordered'>";
		//$row = dbNumRows($data);
		$i = 0;
		while($rd = dbFetchArray($data))
		{
			if($i%2 == 0){ $result .= "<tr>"; 	}
			$qty = $this->stock_event_zone($rd['id_product_attribute'], $id_zone);
			if($qty <1){ $disabled = "disabled"; }else{ $disabled = ""; }
			$result .="<td style='vertical-align:middle;'>".$rd['code'] ; if($qty >0){ $result.="<p class='pull-right' style='color:green'>".$qty." ในสต็อก</p>"; }else{ $result .="<p class='pull-right' style='color:red'>สินค้าหมด</p>"; } $result .= "</td>
						<td style='width:100px; padding-right:10px; vertical-align:middle;'>
								<input type='text' class='form-control' name='qty[".$rd['id_product_attribute']."]' id='qty_".$rd['id_product_attribute']."' onkeyup='valid_qty($(this), ".$qty.")' ".$disabled." />
						</td>";
			$i++;
			if($i%2 == 0){ $result .= "</tr>"; }
		}// end while
		$result .= "</table>";
		
	}
	else if($c_count == "110" || $c_count == "101" || $c_count == "011") /// กรณีมี 2 คุณลักษณะ
	{
		if($c_count == "110"){ $colors = $color; $sizes = $size; }else if($c_count == "101"){ $colors = $color; $attributes = $attribute; }else if($c_count == "011"){ $attributes = $attribute; $sizes = $size; }
		$result .="<table class='table table-bordered'>";
		if(isset($colors) && isset($sizes) ){
			$co = dbQuery("SELECT tbl_product_attribute.id_color, color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
			$result .= "<tr><td>&nbsp;</td>";
			while($co_head = dbFetchArray($co) )
			{
				$result .= "<td align='center' style='vertical-align:middle'><strong>".$co_head['color_code']."</strong><br/>".color_name($co_head['id_color'])."</td>";
			}
			$result .= "</tr>";
			$si = dbQuery($sizes);
			while($rd = dbFetchArray($si) )
			{
				$result .= "<tr><td align='center' style='vertical-align:middle; width:70px;'><strong>".get_size_name($rd['id'])."</strong></td>";
				$co = dbQuery($colors);
				while($rc = dbFetchArray($co) )
				{
					list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND tbl_product_attribute.id_color = ".$rc['id']." ORDER BY color_code ASC"));
					if($id_product_attribute)
					{
						$qty = $this->stock_event_zone($id_product_attribute, $id_zone);
						$result .= $this->input_field($id_product_attribute, $qty);
					}else{
						$result .= $this->no_field();
					}
					
				}//end while
				$result .= "</tr>";
			}//end while
		}else if(isset($colors) && isset($attributes) ){
			$co = dbQuery("SELECT tbl_product_attribute.id_color,color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
			$result .= "<tr><td>&nbsp;</td>";
			while($co_head = dbFetchArray($co) )
			{
				$result .= "<td align='center' style='vertical-align:middle'><strong>".$co_head['color_code']."</strong><br/>".color_name($co_head['id_color'])."</td>";
			}
			$result .= "</tr>";
			$si = dbQuery($attributes);
			while($rd = dbFetchArray($si) )
			{
				$result .= "<tr><td align='center' style='vertical-align:middle; width:70px;'><strong>".get_attribute_name($rd['id'])."</strong></td>";
				$co = dbQuery($colors);
				while($rc = dbFetchArray($co) )
				{
					list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND id_attribute =".$rd['id']." AND tbl_product_attribute.id_color = ".$rc['id']." ORDER BY color_code ASC"));
					if($id_product_attribute)
					{
						$qty = $this->stock_event_zone($id_product_attribute, $id_zone);
						$result .= $this->input_field($id_product_attribute, $qty);
					}
					else
					{
						$result .= $this->no_field();	
					}
				}//end while
				$result .= "</tr>";
			}//end while
			
		}else if(isset($attributes) && isset($sizes) ){
			$co = dbQuery("SELECT attribute_name FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0 GROUP BY tbl_product_attribute.id_attribute ORDER BY position ASC" );
			$result .= "<tr><td>&nbsp;</td>";
			while($co_head = dbFetchArray($co) )
			{
				$result .= "<td align='center' style='vertical-align:middle'><strong>".$co_head['attribute_name']."</strong></td>";
			}
			$result .= "</tr>";
			$si = dbQuery($sizes);
			while($rd = dbFetchArray($si) )
			{
				$result .= "<tr><td align='center' style='vertical-align:middle; width:70px;'><strong>".get_size_name($rd['id'])."</strong></td>";
				$co = dbQuery($attributes);
				while($rc = dbFetchArray($co) )
				{
					list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND tbl_product_attribute.id_attribute = ".$rc['id']."  ORDER BY position ASC"));
					if($id_product_attribute)
					{
						$qty = $this->stock_event_zone($id_product_attribute, $id_zone);
						$result .= $this->input_field($id_product_attribute, $qty);
					}
					else
					{
						$result .= $this->no_field();	
					}
				}//end while
				$result .= "</tr>";
			}//end while
			
		}
	}
	else if($c_count == "111") /// กรณีมี 3 คุณลักษณะ
	{
		$co = dbQuery("SELECT tbl_product_attribute.id_attribute, attribute_name FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0 GROUP BY tbl_product_attribute.id_attribute ORDER BY position ASC" );
		$result .= "<ul class='nav nav-tabs'>";
		$n = 1;
		while($cs = dbFetchArray($co) )
		{
			$result .= "<li role='presentation' class='"; if($n == 1){ $result .= "active"; } $result .="'><a href='#".$cs['id_attribute']."' aria-controls='".$cs['id_attribute']."' role='tab' data-toggle='tab'>".$cs['attribute_name']."</a></li>";
			$n++;
		}//end while
		$result .= "</ul>";
		$co = dbQuery("SELECT tbl_product_attribute.id_attribute, attribute_name FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0 GROUP BY tbl_product_attribute.id_attribute ORDER BY position ASC" );
		$result .= "<div class='tab-content'>";
		$n = 1;
		while($cs = dbFetchArray($co) )
		{
			$result .= "<div role='tabpanel' class='tab-pane "; if($n == 1){ $result .= "active"; } $result .= "' id='".$cs['id_attribute']."'>";
			$qr = dbQuery("SELECT tbl_product_attribute.id_color,color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
			$result .= "<table class='table table-bordered'>";
			$result .= "<tr><td>&nbsp;</td>";
			while($co_head = dbFetchArray($qr) )
			{
				$result .= "<td align='center' style='vertical-align:middle'><strong>".$co_head['color_code']."</strong><br/>".color_name($co_head['id_color'])."</td>";
			}
			$result .= "</tr>";
			$sizes = $size;
			$si = dbQuery($sizes);
			while($rd = dbFetchArray($si) )
			{
				$result .= "<tr><td align='center' style='vertical-align:middle; width:70px;'><strong>".get_size_name($rd['id'])."</strong></td>";
				$colors = $color;
				$qs = dbQuery($colors);
				while($rc = dbFetchArray($qs) )
				{
					list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND tbl_product_attribute.id_color = ".$rc['id']." ORDER BY color_code ASC"));
					if($id_product_attribute)
					{
						$qty = $this->stock_event_zone($id_product_attribute, $id_zone);
						$result .= $this->input_field($id_product_attribute, $qty);
					}
					else
					{
						$result .= $this->no_field();	
					}
				}//end while
				$result .= "</tr>";
			}//end while
			$result .= "</table>";
			
			$result .= "</div>";
			$n++;
		}// end while
		
		$result .= "</div><! ---- end tab-content ----->";
		
	} /// จบ เงื่อนไขนับคุณลักษณะ
	$result .= "
		<script>
			function valid_qty(el, qty)
			{
					var order_qty = el.val();
					if(isNaN(order_qty))
					{
						alert('กรุณาใส่ตัวเลขเท่านั้น');
						el.val('');
						el.focus();
						return false;
					}else{
						if(parseInt(order_qty) > parseInt(qty) )
						{
							alert('มีสินค้าในสต็อกแค่ '+qty);
							el.val('');
							el.focus();
						}
					}
			}
		</script>
	";

	return $result;
}// end function


public function stock_event_zone($id_product_attribute, $id_zone)
{
	$qty = 0;
	$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_stock WHERE id_zone = ".$id_zone." AND id_product_attribute = ".$id_product_attribute);
	$qr = dbQuery("SELECT SUM(product_qty) AS qty FROM tbl_order_detail JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order JOIN tbl_order_event ON tbl_order_event.id_order = tbl_order.id_order WHERE role = 8 AND valid = 0 AND id_product_attribute = ".$id_product_attribute." AND id_zone = ".$id_zone);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		if(dbNumRows($qr) == 1 ){ list($order) = dbFetchArray($qr); }else{ $order = 0; }
		if($rs['qty'] != NULL)
		{
			$qty = $rs['qty'];
			$qty -= $order;
		}
	}
	return $qty;		
}
//********************************** End Event Attribute Grid  *********************************//
	
	
}/// end class
?>
