<?php
class lend
{
public $id_order;
public $id_lend;
public $date_add;
public $date_upd;
public $id_employee;
public $status;
public $id_user;
public $reference;
public function __construct($id = "")
{
	if( $id != '')
	{
		$qs = dbQuery("SELECT * FROM tbl_lend WHERE id_lend = ".$id);
		if(dbNumRows($qs) == 1 )
		{
			$rs = dbFetchArray($qs);
			$this->id_order 	= $rs['id_order'];
			$this->id_lend 		= $rs['id_lend'];	
			$this->id_employee 	= $rs['id_employee'];
			$this->status			= $rs['status'];
			$this->id_user			= $rs['id_user'];
			$this->date_add		= $rs['date_add'];
			$this->date_upd		= $rs['date_upd'];
			$this->reference 		= get_order_reference($rs['id_order']);
		}
	}
}

public function lend_data($id_lend)
{
	$qs = dbQuery("SELECT * FROM tbl_lend WHERE id_lend = ".$id_lend);
	$qr = $qs;
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$this->id_order 		= $rs['id_order'];
		$this->id_lend 			= $rs['id_lend'];	
		$this->id_employee 	= $rs['id_employee'];
		$this->status			= $rs['status'];
		$this->id_user			= $rs['id_user'];
		$this->date_add		= $rs['date_add'];
		$this->date_upd		= $rs['date_upd'];
		$this->reference 		= get_order_reference($rs['id_order']);
	}
	return $qr;
}

public function get_detail($id_lend)
{
	return dbQuery("SELECT * FROM tbl_lend_detail WHERE id_lend = ".$id_lend);	
}

public function delete_order($id_order)
{
	$id_lend = $this->get_id_lend_by_order($id_order);
	startTransection();
	$ra	= $this->drop_lend_detail($id_lend);
	$rb 	= $this->drop_lend($id_lend);
	$rc	= $this->drop_order_detail($id_order);
	$rd	= $this->drop_order($id_order);
	if( $ra && $rb && $rc && $rd )
	{
		commitTransection();
		return true;
	}
	else
	{
		dbRollback();
		return false;
	}	
}


public function drop_lend_detail($id_lend)
{
	return dbQuery("DELETE FROM tbl_lend_detail WHERE id_lend = ".$id_lend);
}

public function drop_lend($id_lend)
{
	return dbQuery("DELETE FROM tbl_lend WHERE id_lend = ".$id_lend);	
}

public function drop_order_detail($id_order)
{
	return dbQuery("DELETE FROM tbl_order_detail WHERE id_order = ".$id_order);	
}

public function drop_order($id_order)
{
	return dbQuery("DELETE FROM tbl_order WHERE id_order = ".$id_order." AND role = 3 AND current_state IN(1, 3, 8)");
}

public function update_order($id_order, array $data)
{
	startTransection();
	$qs = dbQuery("UPDATE tbl_order SET id_employee = ".$data['id_employee'].", date_add = '".$data['date_add']."', comment = '".$data['remark']."' WHERE id_order = ".$id_order);
	$qr = dbQuery("UPDATE tbl_lend SET id_employee = ".$data['id_employee']." , date_add = '".$data['date_add']."', id_user = ".$data['user_id']." WHERE id_order = ".$id_order);
	
	if( $qs && $qr )
	{
		commitTransection();
		return true;
	}
	else
	{
		dbRollback();
		return false;	
	}
		
}
public function add_to_order($id_order, $id_pa, $qty)
{
	$res = false;
	startTransection();
	$rs 	= $this->insert_detail($id_order, $id_pa, $qty);
	$rd	= $this->insert_lend_detail($id_order, $id_pa, $qty);
	if( $rs && $rd )
	{
		commitTransection();
		$res = true;
	}
	else
	{
		dbRollback();
	}
	return $res;
}

public function delete_detail($id_order, $id_pa)
{
	return dbQuery("DELETE FROM tbl_order_detail WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa);	
}

public function delete_lend_detail($id_lend, $id_pa)
{
	return dbQuery("DELETE FROM tbl_lend_detail WHERE id_lend = ".$id_lend." AND id_product_attribute = ".$id_pa);
}

public function insert_detail($id_order, $id_product_attribute, $qty)
{	
	$product 		= new product();
	$id_product 	= $product->getProductId($id_product_attribute);
	$product->product_detail($id_product);
	$product->product_attribute_detail($id_product_attribute);
	$product_name	= $product->product_name;
	$barcode 		= $product->barcode;
	$product_price	= $product->product_price;
	$final_price 		= $product->product_sell;
	$reference 		= $product->reference;
	$total_amount 	= $product->product_sell * $qty;
	$qr = dbQuery("SELECT id_order_detail FROM tbl_order_detail WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_product_attribute);
	$row = dbNumRows($qr);
	if($row>0)
	{
		list($id)	= dbFetchArray($qr);
		$rs 		= dbQuery("UPDATE tbl_order_detail SET product_qty = product_qty + ".$qty.", total_amount = total_amount + ".$total_amount." WHERE id_order_detail = ".$id);	
	}
	else
	{	
		$qs = "INSERT INTO tbl_order_detail ";
		$qs .= "(id_order, id_product, id_product_attribute, product_name, product_qty, product_reference, barcode, product_price, reduction_percent, reduction_amount, discount_amount, final_price, total_amount, valid_detail ) VALUES ";
		$qs .= "($id_order, $id_product, $id_product_attribute, '$product_name', $qty, '$reference', '$barcode', $product_price, 0.00, 0.00, 0.00, $final_price, $total_amount, 0 )";
		$rs = dbQuery($qs);
	}
	return $rs;
}

public function insert_lend_detail($id_order, $id_pa, $qty)
{
	$rs = false;
	$id_lend 		= $this->get_id_lend_by_order($id_order);
	if( $id_lend != false)
	{
		$qr 			= dbQuery("SELECT id_lend_detail FROM tbl_lend_detail WHERE id_lend = ".$id_lend." AND id_product_attribute = ".$id_pa);
		if( dbNumRows($qr) == 1 )
		{
			list($id)	= dbFetchArray($qr);
			$rs 		= dbQuery("UPDATE tbl_lend_detail SET qty = qty + ".$qty." WHERE id_lend_detail = ".$id);	
		}
		else
		{
			$rs 	= dbQuery("INSERT INTO tbl_lend_detail (id_lend, id_product_attribute, qty) VALUES (".$id_lend.", ".$id_pa.", ".$qty.")");
		}
	}
	return $rs;
}

public function add_order(array $data)
{
	$qs = dbQuery("INSERT INTO tbl_order ( reference, id_employee, payment, comment, role, date_add ) VALUES ('".$data['reference']."', ".$data['id_employee'].", 'ยืมสินค้า', '".$data['comment']."', 3, '".$data['date_add']."')");
	if( $qs )
	{ 
		return dbInsertId(); 
	}
	else
	{ 
		return false; 
	}
}


public function add_lend($data)
{
	$qs = dbQuery("INSERT INTO tbl_lend (id_order, id_employee, date_add, id_user) VALUES ( ".$data['id_order'].", ".$data['id_employee'].", '".$data['date_add']."', ".$data['id_user'].")");
	return $qs;
}

public function change_lend_status($id_lend, $status)
{
	if($status == 2 ){ $status = $this->isReturnAll($id_lend) ? 2 : 1 ; }
	return dbQuery("UPDATE tbl_lend SET status = ".$status." WHERE id_lend = ".$id_lend);	
}

public function change_all_lend_detail_valid($id_lend, $status)
{
	$rd = true;
	$qs = dbQuery("SELECT id_lend_detail FROM tbl_lend_detail WHERE id_lend = ".$id_lend);
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs))
		{
			$ra = $this->change_lend_detail_valid($rs['id_lend_detail'], $status);	
			if(!$ra){ $rd = false; }
		}
	}
	return $rd;
}

public function change_lend_detail_valid($id_lend_detail, $status = 0)   /// 0 = not return or return but not complete 1 = returned all  2 = cancled
{
	if($status == 0 ){ $status = $this->isValid($id_lend_detail); }
	return dbQuery("UPDATE tbl_lend_detail SET valid = ".$status." WHERE id_lend_detail = ".$id_lend_detail);	
}

public function isValid($id_lend_detail)
{
	$valid = dbNumRows(dbQuery("SELECT valid FROM tbl_lend_detail WHERE id_lend_detail = ".$id_lend_detail." AND return_qty >= qty"));
	return $valid;
}

public function isReturnAll($id_lend)
{
	$rs = true;
	$qs = dbQuery("SELECT id_lend_detail FROM tbl_lend_detail WHERE id_lend = ".$id_lend." AND return_qty < qty AND valid = 0");
	$rows = dbNumRows($qs);
	if( $rows > 0 ){ $rs = false; }
	return $rs;
}

public function isExists($id_lend, $id_pa)
{
	$id_ld = 0;
	$qs = dbQuery("SELECT id_lend_detail FROM tbl_lend_detail WHERE id_lend = ".$id_lend." AND id_product_attribute = ".$id_pa);
	if(dbNumRows($qs) == 1)
	{
		list($id_ld) = dbFetchArray($qs);
	}
	return $id_ld;
}

public function change_order_status($id_order, $status)
{
	return dbQuery("UPDATE tbl_order SET order_status = ".$status." WHERE id_order = ".$id_order);	
}

public function get_order_not_save($id_user)
{
	$id_order = 0;
	$qs = dbQuery("SELECT id_order FROM tbl_lend WHERE status = 0 AND id_user = ".$id_user);
	if( dbNumRows($qs) > 0 )
	{
		list($id_order) = dbFetchArray($qs);
	}
	return $id_order;
}

public function get_id_lend_by_order($id_order)
{
	$id_lend = "";
	$qs = dbQuery("SELECT id_lend FROM tbl_lend WHERE id_order = ".$id_order);
	if( dbNumRows($qs) == 1 )
	{
		list($id_lend) = dbFetchArray($qs);
	}
	return $id_lend;
}

public function isClosed($id_order)
{
	$rs = 0;
	$qs = dbQuery("SELECT status FROM tbl_lend WHERE id_order = ".$id_order);
	if(dbNumRows($qs) == 1)
	{
		list($rd) = dbFetchArray($qs);	
		if($rd == 2 ){ $rs = 1; }else{ $rs = 0; }  /// 0 = not saved,  1 = saved,   2 = closed
	}
	return $rs;
}

public function total_lend_qty($id)
{
	$qty = 0;
	$qs = dbQuery("SELECT SUM(sold_qty) AS qty FROM tbl_order_detail_sold WHERE id_role = 3 AND id_order = ".$id);
	list($res) = dbFetchArray($qs);
	if(!is_null($res))
	{
		$qty = $res;
	}
	else
	{
		$qs = dbQuery("SELECT SUM(product_qty) AS qty FROM tbl_order_detail WHERE id_order = ".$id);
		list($res) = dbFetchArray($qs);
		if( !is_null($res) )
		{
			$qty = $res;
		}
	}
	return $qty;
}

public function total_return_qty($id)
{
	$qty = 0;
	$qs = dbQuery("SELECT SUM(return_qty) AS qty FROM tbl_lend_detail JOIN tbl_lend ON tbl_lend_detail.id_lend = tbl_lend.id_lend WHERE id_order = ".$id);	
	list($re) = dbFetchArray($qs);
	if( !is_null($re) )
	{
		$qty = $re;
	}
	return $qty;
}

public function lend_qty($id_order, $id_pa)
{
	$qty = 0; 
	$qs = dbQuery("SELECT sold_qty FROM tbl_order_detail_sold WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa." AND id_role = 3");
	if(dbNumRows($qs) == 1)
	{
		list($qty) = dbFetchArray($qs);	
	}
	return $qty;
}

public function return_qty($id_order, $id_pa)
{
	$qty = 0;
	$qs = dbQuery("SELECT return_qty FROM tbl_lend_detail JOIN tbl_lend ON tbl_lend_detail.id_lend = tbl_lend.id_lend WHERE id_order = ".$id_order." AND tbl_lend_detail.id_product_attribute = ".$id_pa);
	if(dbNumRows($qs) == 1)
	{
		list($qty) = dbFetchArray($qs);
	}
	return $qty;
}

public function return_product($id_lend, $id_pa, $qty)
{
	return dbQuery("UPDATE tbl_lend_detail SET return_qty = return_qty + ".$qty." WHERE id_lend = ".$id_lend." AND id_product_attribute = ".$id_pa);	
}

public function lend_attribute_grid($id_product)
{
	$result = "";
	$product = new product();
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
			$qty = $product->available_order_qty($rd['id_product_attribute']);
			if($qty <1){ $disabled = "disabled"; }else{ $disabled = ""; }
			$result .="<td style='vertical-align:middle;'>".$rd['code'] ; if($qty >0){ $result.="<p class='pull-right' style='color:green'>".$qty." ในสต็อก</p>"; }else{ $result .="<p class='pull-right' style='color:red'>สินค้าหมด</p>"; } $result .= "</td>
						<td style='width:100px; padding-right:10px; vertical-align:middle;'>
								<input type='text' class='form-control qty' name='qty[".$rd['id_product_attribute']."]' id='qty_".$rd['id_product_attribute']."' onkeyup='valid_qty($(this), ".$qty.")' ".$disabled." />
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
					$qty = $product->available_order_qty($id_product_attribute);
					if($qty <1){ $disabled = "disabled"; $stock = "<span style='color:red'>สินค้าหมด</span>";  }else{ $disabled = ""; $stock = $qty; }
					$result .= "
						<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>
							<input type='text' class='form-control qty' name='qty[".$id_product_attribute."]' onkeyup='valid_qty($(this), ".$qty.")' ".$disabled." /><center>".$stock."</center>
						</td>";
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
					$qty = $product->available_order_qty($id_product_attribute);
					if($qty <1){ $disabled = "disabled"; $stock = "<span style='color:red'>สินค้าหมด</span>";  }else{ $disabled = ""; $stock = $qty; }
					$result .= "
						<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>
							<input type='text' class='form-control qty' name='qty[".$id_product_attribute."]' onkeyup='valid_qty($(this), ".$qty.")' ".$disabled." /><center>".$stock."</center>
						</td>";
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
					$qty = $product->available_order_qty($id_product_attribute);
					if($qty <1){ $disabled = "disabled"; $stock = "<span style='color:red'>สินค้าหมด</span>";  }else{ $disabled = ""; $stock = $qty; }
					$result .= "
						<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>
							<input type='text' class='form-control qty' name='qty[".$id_product_attribute."]' onkeyup='valid_qty($(this), ".$qty.")' ".$disabled." /><center>".$stock."</center>
						</td>";
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
					$qty = $product->available_order_qty($id_product_attribute);
					if($qty <1){ $disabled = "disabled"; $stock = "<span style='color:red'>สินค้าหมด</span>";  }else{ $disabled = ""; $stock = $qty; }
					$result .= "
						<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>
							<input type='text' class='form-control qty' name='qty[".$id_product_attribute."]' onkeyup='valid_qty($(this), ".$qty.")' ".$disabled." /><center>".$stock."</center>
						</td>";
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

}/// end class

?>