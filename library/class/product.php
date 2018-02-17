<?php

class product {
	public $id_product;
	public $product_attribute;
	public $id_product_attribute;
	public $reference; //รหัสสินค้าของ SKU
	public $barcode;  // บาร์โค้ดสินค้าของ SKU
	public $id_color;
	public $id_size;
	public $id_attribute;
	public $product_code; //รหัสสินค้าของ Style
	public $product_name;
	public $product_price;
	public $product_cost;
	public $weight = 0.00;
	public $width = 0.00;
	public $length = 0.00;
	public $height = 0.00;
	public $show_in_shop = 0;
	public $is_visual = 0;
	public $cover_image; // รูปภาพใช้เป็นหน้าปก
	public $default_category_id; //หมวดหมู่หลักของสินค้า
	public $color_code;
	public $color_name;
	public $size_name;
	public $attribute_name;
	public $product_detail;
	public $value;
	public $discount = 0; //ส่วนลดสุดท้าย(เป็นจำนวนเงิน)
	public $product_discount; //ส่วนลดที่ตัวสินค้า
	public $discount_type; // ประเภทส่วนลด ( percentage or amount )
	public $date_add;
	public $date_upd;
	public $active;
	public $stock_qty;
	public $product_sell; //ราคาขายสุดท้ายหลังหักส่วนลด
	public $product_discount1;
	public $id_customer; // เก็บ id_customer ตอนที่เรียกใช้ function product_detail เอาไว้ใช้กับตัวอื่นต่อ
	public $error;

public function __construct()
{

}


//-------------------------  NEW CODE  ------------------------//
public function addProduct(array $ds)
{
	$fields 	= '';
	$values 	= '';
	$n 		= count($ds);
	$i 			= 1;
	foreach( $ds as $key => $val )
	{
		$fields .=	 $key;
		if( $i < $n ){ $fields .= ', '; }
		$values .= "'".$val."'";
		if( $i < $n ){ $values .= ', '; }
		$i++;
	}
	$qs = dbQuery("INSERT INTO tbl_product (".$fields.") VALUES (".$values.")");
	if( $qs )
	{
		return dbInsertId();
	}
	else
	{
		return FALSE;
	}

}


private function isExistsProductAttribute($id_pd, $co, $si, $at)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = ".$id_pd." AND id_color = ".$co." AND id_size = ".$si." AND id_attribute = ".$at);
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;
	}
	return $sc;
}

public function addProductAttribute( array $ds )
{
	$sc		= TRUE;
	$id_pd 	= $ds['id_product'];
	$color 	= $ds['id_color'];
	$size		= $ds['id_size'];
	$attr		= $ds['id_attribute'];
	if( $this->isExistsProductAttribute($id_pd, $color, $size, $attr) === FALSE ) //---- ถ้าไม่มีรายการอยู่ก่อนหน้าแล้ว
	{
		$fields 	= '';
		$values 	= '';
		$n 		= count($ds);
		$i 			= 1;
		foreach( $ds as $key => $val )
		{
			$fields .=	 $key;
			if( $i < $n ){ $fields .= ', '; }
			$values .= "'".$val."'";
			if( $i < $n ){ $values .= ', '; }
			$i++;
		}
		$qs = dbQuery("INSERT INTO tbl_product_attribute (".$fields.") VALUES (".$values.")");
		if( ! $qs ){ $sc = FALSE; }
	}
	else
	{
		$sc = 1; //--- ถ้ามีรายการอยู่ก่อนหน้าแล้ว ให้ข้าม
	}
	return $sc;
}


public function updateProductAttribute($id_pa, $ds)
{
	$set = '';
	$n = count($ds);
	$i = 1;
	foreach( $ds as $key => $val )
	{
		$set .= $key." = '".$val."'";
		if( $i < $n ){ $set .= ", "; }
		$i++;
	}
	return dbQuery("UPDATE tbl_product_attribute SET ".$set." WHERE id_product_attribute = ".$id_pa);
}


public function updateProduct($id, array $ds)
{
	$set = '';
	$n = count($ds);
	$i = 1;
	foreach( $ds as $key => $val )
	{
		$set .= $key." = '".$val."'";
		if( $i < $n ){ $set .= ", "; }
		$i++;
	}
	return dbQuery("UPDATE tbl_product SET ".$set." WHERE id_product = ".$id);
}

//----------------------------- DELETE Item ------------------------------//
public function deleteItem($id_pa)
{
		$sc = FALSE;
		//---- ตรวจสอบยอดคงเหลือในสต็อก
		$stock 	= $this->stockExists($id_pa);

		//---- ตรวจสอบสินค้าในออเดอร์
		$order	= $this->orderExists($id_pa);

		//---- ตรวจสอบความเคลื่อนไหว
		$trans	= $this->transectionExists($id_pa);

		if( $stock === TRUE OR $order === TRUE OR $trans === TRUE )
		{
			$sc = FALSE;
			$error	= $stock === TRUE ? 'stockExists' : ( $order == TRUE ? 'orderExists' : 'transectionExists');
			$this->error = $error;
		}
		else
		{
			$rs = dbQuery("DELETE FROM tbl_product_attribute WHERE id_product_attribute = ".$id_pa);
			$rd = dbQuery("DELECT FROM tbl_product_pack WHERE id_product_attribute = ".$id_pa);
			$ra = dbQuery("DELETE FROM tbl_product_attribute_image WHERE id_product_attribute = ".$id_pa);
			$sc = TRUE;
		}

		return $sc;
}


//------------------------------ Delete Product  --------------------------------//
public function deleteProduct($id_pd)
{
	$sc = FALSE;
	$id_pa = productAttributeIn($id_pd); //---- id_product_attribute in product LIke 1,23

	if( $id_pa !== FALSE )
	{
		//---- ตรวจสอบยอดคงเหลือในสต็อก
		$stock 	= $this->stockExists($id_pa);

		//---- ตรวจสอบสินค้าในออเดอร์
		$order	= $this->orderExists($id_pa);

		//---- ตรวจสอบความเคลื่อนไหว
		$trans	= $this->transectionExists($id_pa);

		//-----  ถ้ามีสต็อก หรือ มีสินค้าในออเดอร์ หรือ มีความเคลื่อนไหว ไม่ให้ลบ
		if( $stock === TRUE OR $order === TRUE OR $trans === TRUE )
		{
			$sc 		= FALSE;
			$error	= $stock === TRUE ? 'stockExists' : ( $order == TRUE ? 'orderExists' : 'transectionExists');
			$this->error = $error;
		}
		else if( $stock === FALSE && $order === FALSE && $trans === FALSE )
		{
			//-----  ถ้าไม่มีความเคลื่อนไหวใดๆ ลบรายการสินค้าได้
			$pAttribute = $this->getIdProductAttributeInArray($id_pd); //----- เอา id_product_attribute เก็บไว้ก่อน ใช้ตอนลบรูปภาพ
			//print_r($pAttribute);

			//------ เริ่มใช้งาน transection -------//
			startTransection();

			//------ ลบสินค้าในหมวดหมู่
			$ra = dbQuery("DELETE FROM tbl_category_product WHERE id_product = ".$id_pd);

			//----- คำอธิบายสินค้า
			$rb = dbQuery("DELETE FROM tbl_product_detail WHERE id_product = ".$id_pd);

			//----- ลบรายการสินค้า
			$rc = dbQuery("DELETE FROM tbl_product_attribute WHERE id_product = ".$id_pd);

			//----- ลบสินค้า
			$rd = dbQuery("DELETE FROM tbl_product WHERE id_product = ".$id_pd);

			if( $ra && $rb && $rc && $rd)
			{
				$sc = TRUE;

				foreach( $pAttribute as $id )
				{
					$this->dropAttributeImage($id);
				}
				$this->deleteProductImages($id_pd);
				commitTransection();
			}
			else
			{
				$sc = FALSE;
				dbRollback();
			}

		}
	}

	return $sc;
}



//----------  มีสินค้าอยู่ในสต็อกหรือไม่  ---------//
private function stockExists($id_pa_in)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT qty FROM tbl_stock WHERE id_product_attribute IN(".$id_pa_in.") AND qty != 0");
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;
	}
	return $sc;
}

//-----------  มีสินค้าอยู่ในออเดอร์หรือไม่  -----------//
private function orderExists($id_pa_in)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT product_qty FROM tbl_order_detail WHERE id_product_attribute IN(".$id_pa_in.")");
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;
	}
	return $sc;

}

//-----------  มีสินค้าอยู่ใน movementหรือไม่  -----------//
private function transectionExists($id_pa_in)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_product_attribute FROM tbl_stock_movement WHERE id_product_attribute IN(".$id_pa_in.")");
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;
	}
	return $sc;
}

private function  getIdProductAttributeInArray($id_pd)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = ".$id_pd);
	if( dbNumRows($qs) > 0 )
	{
		$sc = array();
		while( $rs = dbFetchArray($qs) )
		{
			$sc[] = $rs['id_product_attribute'];
		}
	}
	return $sc;
}

private function dropAttributeImage($id_pa)
{
	return dbQuery("DELETE FROM tbl_product_attribute_image WHERE id_product_attribute = ".$id_pa);
}

private function deleteProductImages($id_pd)
{
	$sc = TRUE;
	$qs = dbQuery("SELECT id_image FROM tbl_image WHERE id_product = ".$id_pd);
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$rd = deleteImage($rs['id_image']);
			if( ! $rd ){ $sc = FALSE; }
		}
		if( $sc )
		{
			$ra = dbQuery("DELETE FROM tbl_image WHERE id_product = ".$id_pd);
			if( ! $ra ){ $sc = FALSE; }
		}
	}
	return $sc;
}




//----------------------  END NEW CODE  ----------------------//
public function add_product(array $ds)
{

		list($product_code, $product_name, $product_cost, $product_price, $weight, $width, $length, $height, $discount_type, $discount, $default_category_id, $active, $description, $category_id) = $data;
		$sql = "INSERT INTO tbl_product (product_code, product_name, product_cost, product_price, weight, width, length, height, discount_type, discount, default_category_id, active, date_add) VALUES ('$product_code', '$product_name', $product_cost, $product_price, $weight, $width, $length, $height, '$discount_type', $discount, $default_category_id, $active, NOW())";

		if(dbQuery($sql)){
			$id_product = $this->get_product_id_by_code($product_code);
			$this->set_product_description($id_product, $description);
			$this->set_product_category($id_product, $category_id);
			return true;
		}else{
			return false;
		}
}
	//**********************  Edit product *************************//
public function edit_product( array $data){
	list($id_product, $product_code, $product_name, $product_cost, $product_price, $weight, $width, $length, $height, $discount_type, $discount, $default_category_id, $active, $description, $category_id) = $data;
	$sql = "UPDATE tbl_product SET product_code ='$product_code', product_name = '$product_name', product_cost = $product_cost, product_price = $product_price, weight = $weight, width = $width, length = $length, height = $height, discount_type = '$discount_type', discount = $discount, default_category_id = $default_category_id, active = $active WHERE id_product = $id_product";
	if(dbQuery($sql)){
		$this->set_product_description($id_product, $description);
		$this->set_product_category($id_product, $category_id);
		return true;
	}else{
		return false;
	}
}



public function delete_product($id_product){
	// ตรวจสอบ ยอดคงเหลือก่อนลบข้อมูล
	$checked = dbNumRows(dbQuery("SELECT qty FROM tbl_stock LEFT JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = $id_product AND qty>0"));
	$r1 = dbNumRows(dbQuery("SELECT id_product FROM tbl_order_detail WHERE id_product = $id_product"));
	$r2 = dbNumRows(dbQuery("SELECT id_product FROM tbl_adjust_detail LEFT JOIN tbl_product_attribute ON tbl_adjust_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = $id_product"));
	$r3 = dbNumRows(dbQuery("SELECT id_product FROM tbl_recieved_detail LEFT JOIN tbl_product_attribute ON tbl_recieved_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = $id_product"));
	$r4 = dbNumRows(dbQuery("SELECT id_product FROM tbl_stock_movement LEFT JOIN tbl_product_attribute ON tbl_stock_movement.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = $id_product"));
	$transection = 0+$r1+$r2+$r3+$r4;
	if($checked !=0){
		$message = "คุณไม่สามารถลบสินค้านี้ได้เนื่องจากยังมียอดสินค้าคงเหลือ";
		return $message;
	}else if($transection>0){
		$message = "คุณไม่สามารถลบสินค้านี้ได้เนื่องจากมี transection ที่เกิดจากสินค้านี้ในระบบแล้ว";
		return $message;
	}else{
		dbQuery("DELETE FROM tbl_product WHERE id_product = $id_product");
		dbQuery("DELETE FROM tbl_product_attribute WHERE id_product = $id_product");
		dbQuery("DELETE FROM tbl_product_detail WHERE id_product = $id_product");
		dbQuery("DELETE FROM tbl_category_product WHERE id_product = $id_product");
		return true;
	}
}
public function product_upd($id_product_attribute)
{
	$id_product = $this->get_id_product($id_product_attribute);
	$qs = dbQuery("UPDATE tbl_product SET date_upd = '".date("Y-m-d H:i:s")."' WHERE id_product = ".$id_product);
}
//**********************  Add Combination  ******************//




public function add_product_attribute(array $data){
	list($id_product, $reference, $barcode, $id_color, $id_size, $id_attribute, $cost, $price, $weight, $width, $length, $height, $id_image, $barcode_pack, $qty) = $data;
	$sql = "INSERT INTO tbl_product_attribute (id_product, reference, barcode, id_color, id_size, id_attribute, cost, price, weight, width, length, height ) VALUES ($id_product, '$reference', '$barcode', $id_color, $id_size, $id_attribute, ";
	$sql .= "$cost, $price, $weight, $width, $length, $height)";
	if(dbQuery($sql)){
		if($id_image !=""){
			list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = $id_product AND reference = '$reference'"));
			$this->set_image($id_product_attribute, $id_image);
		}
		$this->set_pack($id_product_attribute, $barcode_pack, $qty);
		$this->product_upd($id_product_attribute);
		return true;
	}else{
		return false;
	}
}

//***************************  Edit Combination  ******************************//
public function edit_product_attribute(array $data){
	list($id_product_attribute, $reference, $barcode, $id_color, $id_size, $id_attribute, $cost, $price, $weight, $width, $length, $height, $id_image, $barcode_pack, $qty) = $data;
	$sql = "UPDATE tbl_product_attribute SET reference = '$reference', barcode = '$barcode', id_color = $id_color, id_size = $id_size, id_attribute = $id_attribute, cost = $cost, price = $price, weight = $weight, width = $width,
			  length = $length, height = $height WHERE id_product_attribute = $id_product_attribute";
	if(dbQuery($sql) && $this->set_image($id_product_attribute, $id_image)&& $this->set_pack($id_product_attribute, $barcode_pack, $qty)){
		$this->product_upd($id_product_attribute);
		return true;
	}else{
		return false;
	}
}

//************************  Delete Combination  *********************************//
public function deletd_product_attribute($id_product_attribute){
	$checked = dbNumRows(dbQuery("SELECT qty FROM stock_qty WHERE id_product_attribute=$id_product_attribute AND qty>0")); // ตรวจสอบ ยอดคงเหลือก่อนลบข้อมูล
	$r1 = dbNumRows(dbQuery("SELECT id_product_attribute FROM tbl_order_detail WHERE id_product_attribute = $id_product_attribute"));
	$r2 = dbNumRows(dbQuery("SELECT id_product_attribute FROM tbl_adjust_detail WHERE id_product_attribute = $id_product_attribute"));
	$r3 = dbNumRows(dbQuery("SELECT id_product_attribute FROM tbl_recieved_detail WHERE id_product_attribute = $id_product_attribute"));
	$r4 = dbNumRows(dbQuery("SELECT id_product_attribute FROM tbl_stock_movement WHERE id_product_attribute = $id_product_attribute"));
	$transection = 0+$r1+$r2+$r3+$r4;
	if($checked !=0){
		$message = "คุณไม่สามารถลบสินค้านี้ได้เนื่องจากยังมียอดสินค้าคงเหลือ";
		return $message;
	}else if($transection>0){
		$message = "คุณไม่สามารถลบสินค้านี้ได้เนื่องจากมี transection ที่เกิดจากสินค้านี้ในระบบแล้ว";
		return $message;
	}else{
		dbQuery("DELETE FROM tbl_product_attribute WHERE id_product_attribute = $id_product_attribute");
		dbQuery("DELETE FROM tbl_stock WHERE id_product_attribute = $id_product_attribute");
		dbQuery("DELETE FROM tbl_product_attribute_image WHERE id_product_attribute = $id_product_attribute");
		return true;
	}
}

//*****************************  คืนค่า id_product จากรหัสสินค้า ************//
public function get_product_id_by_code($product_code){
	list($id_product) = dbFetchArray(dbQuery("SELECT id_product FROM tbl_product WHERE product_code='$product_code'"));
	return $id_product;
}
//*********  ตรวจสอบว่า มีรูปผูกไว้กับสินค้านี้หรือไม่ ถ้ามี Update ถ้าไม่มี Insert
public function set_image($id_product_attribute, $id_image){
	$row = dbNumRows(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute_image WHERE id_product_attribute = $id_product_attribute"));
	if($row>0){
			$sql = "UPDATE tbl_product_attribute_image SET id_image = '$id_image' WHERE id_product_attribute = $id_product_attribute";
		}else{
			$sql = "INSERT INTO tbl_product_attribute_image (id_product_attribute, id_image) VALUES ($id_product_attribute, '$id_image')";
	}
	if(dbQuery($sql)){ 	return true; }else{ return false; }
}

//*********  บาร์โค้ดและจำนวนใน แพ็คสินค้า
public function set_pack($id_product_attribute, $barcode_pack, $qty){
	if($barcode_pack !=""){
	list($id_product_pack) = dbFetchArray(dbQuery("SELECT id_product_pack FROM tbl_product_pack WHERE id_product_attribute = $id_product_attribute"));
	if($id_product_pack != ""){
		$sql = "UPDATE tbl_product_pack SET barcode_pack = '$barcode_pack' , qty = '$qty' WHERE id_product_pack = $id_product_pack";
	}else{
		$sql = "INSERT INTO tbl_product_pack (id_product_attribute, qty, barcode_pack) VALUES ($id_product_attribute,$qty,$barcode_pack)";
	}
	if(dbQuery($sql)){ 	return true; }else{ return false; }
	}else{
		return true;
	}
}

//**************************  Product Description **********************************//
public function setProductDescription($id_pd, $desc)
{
	$qs = dbQuery("SELECT id_product_detail AS id FROM tbl_product_detail WHERE id_product = ".$id_pd);
	if( dbNumRows($qs) == 1 )
	{
		$qr = "UPDATE tbl_product_detail SET product_detail = '".$desc."' WHERE id_product = ".$id_pd;
	}
	else
	{
		$qr = "INSERT INTO tbl_product_detail (id_product, product_detail) VALUES (".$id_pd.", '".$desc."')";
	}
	return dbQuery($qr);
}

public function setProductCategory($id_pd, array $pCategory)
{
	$sc = TRUE;
	$rs = dbQuery("DELETE FROM tbl_category_product WHERE id_product = ".$id_pd);
	if( $rs )
	{
		foreach( $pCategory as $id)
		{
			$qs = dbQuery("INSERT INTO tbl_category_product (id_category, id_product, position) VALUES (".$id.", ".$id_pd.", 0)");
			if( ! $qs ){ $sc = FALSE; }
		}
	}
	else
	{
		$sc = FALSE;
	}
	return $sc;
}

public function set_product_category($id_product, array $category_id){
		dbQuery("DELETE FROM tbl_category_product WHERE id_product = $id_product");
		foreach($category_id as $cate_id){
			list($max) = dbFetchArray(dbQuery("SELECT max(position) as max FROM tbl_category_product"));
			$position = $max+1;
			dbQuery("INSERT INTO tbl_category_product (id_category, id_product, position) VALUES ($cate_id, $id_product, $position)");
		}
		return true;
}

public function product_detail($id_pd,$id_cust = 0){
$qs = dbQuery("SELECT * FROM tbl_product WHERE id_product = ".$id_pd);
$rs = dbFetchArray($qs);

$sqr = dbQuery("SELECT product_detail FROM tbl_product_detail WHERE id_product = $id_pd");
list($product_detail) = dbFetchArray($sqr);
$this->id_customer = $id_cust;
$this->id_product =$id_pd;
$this->product_code = $rs['product_code'];
$this->product_name = $rs['product_name'];
$this->product_price = $rs['product_price'];
$this->product_cost = $rs['product_cost'];
$this->weight = $rs['weight'];
$this->width = $rs['width'];
$this->length = $rs['length'];
$this->height = $rs['height'];
$this->discount_type = $rs['discount_type'];
$this->product_detail = $product_detail;
$this->product_discount = $rs['discount'];
$this->default_category_id = $rs['default_category_id'];
$this->active = $rs['active'];
$this->date_add = $rs['date_add'];
$this->date_upd = $rs['date_upd'];
$this->id_product_group = $rs['id_product_group'];
$this->show_in_shop = $rs['show_in_shop'];
$this->is_visual = $rs['is_visual'];
$this->get_discount($id_pd, $rs['product_price'], $id_cust); // ได้ค่าเป็นส่วนลดเป็นจำนวนเงิน
$this->product_sell = $this->product_price - $this->discount;
$this->cover_image = $this->getCoverImage($id_pd,2);

}

public function productDetail($id_pd)
{
	$qs = dbQuery("SELECT * FROM tbl_product WHERE id_product = ".$id_pd);
	if( dbNumRows($qs) == 1 )
	{
		return dbFetchObject($qs);
	}
	else
	{
		return FALSE;
	}
}



public function productAttributeDetail($id_pa)
{
	$qs = $this->get_product_attribute_detail($id_pa);
	if( dbNumRows($qs) == 1 )
	{
		return dbFetchObject($qs);
	}
	else
	{
		return FALSE;
	}
}

public function product_attribute_detail($id_pa, $id_cus = 0)
{
	$id_customer = $id_cus == 0 ? $this->id_customer : $id_cus;
	$qs = $this->get_product_attribute_detail($id_pa);
	$rs = dbFetchArray($qs);
	$this->id_product_attribute	= $rs['id_product_attribute'];
	$this->id_product 				= $rs['id_product'];
	$this->reference 				= $rs['reference'];
	$this->barcode 				= $rs['barcode'];
	$this->id_attribute 			= $rs['id_attribute'];
	$this->id_color 				= $rs['id_color'];
	$this->color_code 			= $this->get_color_code($rs['id_color']);
	$this->color_name 			= $this->get_color_name($rs['id_color']);
	$this->id_size 					= $rs['id_size'];
	$this->size_name 				= $this->get_size_name($rs['id_size']);
	$this->attribute_name 		= $this->get_attribute_name($rs['id_attribute']);
	$this->product_cost 			= $rs['cost'];
	$this->product_price 		= $rs['price'];
	$this->weight 					= $rs['weight'];
	$this->width 					= $rs['width'];
	$this->length 					= $rs['length'];
	$this->height 					= $rs['height'];
	$this->date_upd 				= $rs['date_upd'];
	$this->active 					= $rs['active'];
	$this->id_image 				= $this->get_id_image_attribute($rs['id_product_attribute']);
	$this->image_attribute 		= $this->get_image_path($this->id_image,2);
	$this->get_discount($this->id_product, $rs['price'], $id_customer);
	$this->product_sell 			= $this->product_price - $this->discount;
}

public function get_product_attribute_detail($id_pa)
{
	return dbQuery("SELECT * FROM tbl_product_attribute WHERE id_product_attribute = ".$id_pa);
}

public function get_discount($id_product, $price, $id_customer=0){
	$sql = dbQuery("SELECT discount_type, discount FROM tbl_product WHERE id_product = $id_product");
	list($discount_type, $discount) = dbFetchArray($sql);
	// หาส่วนลดที่มากที่สุดตามสิทธิ์ลูกค้า
		if($discount_type=="percentage"){ $product_discount = $price *($discount * 0.01); }else{  $product_discount = $discount;} // แปลงส่วนลดที่ตัวสินค้าเป็นจำนวนเงิน
		$customer_discount = ($price*$this->get_max_discount($this->id_product,$id_customer)) * 0.01; // ดึงส่วนลดสูงสุดตามหมวดหมู่ที่ลูกค้าได้รับ แล้วแปลงเป็นจำนวนเงิน
		if($product_discount >= $customer_discount){ // ถ้าส่วนลดจากสินค้า "มากกว่า หรือ เท่ากับ " ส่วนลด
			if($product_discount == 0){
					$this->product_discount = "0"; //เอาไปแสดงผล
					$this->discount_type = ""; // เอาไว้คำนวณ
					$this->product_discount1 = 0; //เอาไว้ตรวจสอบเงื่อนไข
				}else{
					if($discount_type=="percentage"){
							$this->product_discount = "$discount %"; //เอาไปแสดงผล
							$this->product_discount1 = $discount; // เอาไว้คำนวณ
							$this->discount_type = "percentage"; //เอาไว้ตรวจสอบเงื่อนไข
						}else{
							$this->product_discount = "$discount ฿";//เอาไปแสดงผล
							$this->product_discount1 = $discount; //เอาไว้คำนวณ
							$this->discount_type = "amount"; //เอาไว้ตรวจสอบเงื่อนไข
					}
			}
			$this->discount = $product_discount;  //ส่วนลดสุดท้าย
		}else{
			$this->product_discount = $this->get_max_discount($id_product,$id_customer)." %"; //เอาไปแสดงผล
			$this->product_discount1 = $this->get_max_discount($id_product,$id_customer); // เอาไว้คำนวณ
			$this->discount_type = "cus_percentage"; //เอาไว้ตรวจสอบเงื่อนไข
			$this->discount = $customer_discount;  //ส่วนลดสุดท้าย
		}
	return true;
}

public function getProductId($id_product_attribute){
	list($id_product) = dbFetchArray(dbQuery("SELECT id_product FROM tbl_product_attribute WHERE id_product_attribute = 	$id_product_attribute"));
	return $id_product;
}

public function product_name($id_product)
{
	$name = '';
	$qs = dbQuery("SELECT product_name FROM tbl_product WHERE id_product = ".$id_product);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$name = $rs['product_name'];
	}
	return $name;
}

public function product_code($id_product)
{
	$code = "";
	$qs = dbQuery("SELECT product_code FROM tbl_product WHERE id_product = ".$id_product);
	if( dbNumRows($qs) == 1 )
	{
		list( $code ) = dbFetchArray($qs);
	}
	return $code;
}

public function product_price($id_product)
{
	$price = 0;
	$qs = dbQuery("SELECT product_price FROM tbl_product WHERE id_product = ".$id_product);
	if( dbNumRows($qs) == 1 )
	{
		list( $price ) = dbFetchArray($qs);
	}
	return $price;
}

public function product_reference($id_product_attribute)
{
	$reference = '';
	$qs = dbQuery("SELECT reference FROM tbl_product_attribute WHERE id_product_attribute = ".$id_product_attribute);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$reference = $rs['reference'];
	}
	return $reference;
}

public function get_product_cost($id_product_attribute){
	$cost = 0;
	$rs = dbQuery("SELECT cost FROM tbl_product_attribute WHERE id_product_attribute = ".$id_product_attribute);
	if(dbNumRows($rs) == 1 ){
		$ro = dbFetchArray($rs);
		$cost = $ro['cost'];
	}
	return $cost;
}


public function get_product_price($id_product_attribute){
	$price = 0;
	$rs = dbQuery("SELECT price FROM tbl_product_attribute WHERE id_product_attribute = ".$id_product_attribute);
	if(dbNumRows($rs) == 1 ){
		$ro = dbFetchArray($rs);
		$price = $ro['price'];
	}
	return $price;
}


public function get_size_name($id_size){
	$sql = dbQuery("SELECT size_name FROM tbl_size WHERE id_size = $id_size");
	list($size_name) = dbFetchArray($sql);
	return $size_name;
}

public function get_color_name($id_color){
	$sql = dbQuery("SELECT color_name FROM tbl_color WHERE id_color = $id_color");
	list($color_name) = dbFetchArray($sql);
	return $color_name;
}

public function get_color_code($id_color){
	$sql = dbQuery("SELECT color_code FROM tbl_color WHERE id_color = $id_color");
	list($color_code) = dbFetchArray($sql);
	return $color_code;
}

public function get_attribute_name($id_attribute){
	$sql = dbQuery("SELECT attribute_name FROM tbl_attribute WHERE id_attribute = $id_attribute");
	list($attribute_name) = dbFetchArray($sql);
	return $attribute_name;
}

public function get_pack($id_product_attribute){
	$sql = dbQuery("SELECT qty, barcode_pack FROM tbl_product_pack WHERE id_product_attribute = $id_product_attribute");
	list($qty, $barcode) = dbFetchArray($sql);
	return $arr = array("qty"=>$qty, "barcode"=>$barcode);
}

public function get_id_image_attribute($id_product_attribute){
	$sql = dbQuery("SELECT id_image FROM tbl_product_attribute_image WHERE id_product_attribute = $id_product_attribute");
	list($id_image) = dbFetchArray($sql);
	return $id_image;
}

public function getCategoryId($id_product)
{
	$id_category = array();
	$sql = dbQuery("SELECT tbl_category.id_category FROM tbl_category LEFT JOIN tbl_category_product ON tbl_category_product.id_category = tbl_category.id_category WHERE id_product = $id_product");
	$row = dbNumRows($sql);
	$i = 0;
	while($i<$row){
		list($id) = dbFetchArray($sql);
		array_push($id_category, $id);
		$i++;
	}
	return $id_category;
}


public function productDiscount($id_product_attribute, $id_customer)
{
	$id_category = $this->getCategoryId($this->getProductId($id_product_attribute));
	list($discount) = dbFetchArray(dbQuery("SELECT discount FROM tbl_customer_discount WHERE id_customer = $id_customer AND id_category = $id_category"));
	return $discount;
}


public function get_max_discount($id_product, $id_customer){
	$id_category = $this->getCategoryId($id_product);
	$discount = array(0);
	foreach($id_category as $id){
		list($disc) = dbFetchArray(dbQuery("SELECT discount FROM tbl_customer_discount WHERE id_customer = '$id_customer' AND id_category = $id"));
		array_push($discount,$disc);
	}
	$result = max($discount);
	return $result;
}


public function orderQty($id_pa)
{
	$sc = 0;
	$qs = dbQuery("SELECT SUM(product_qty) AS qty FROM tbl_order_detail JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order WHERE id_product_attribute = ".$id_pa." AND valid_detail = 0 AND current_state NOT IN(6,7,8,9)");
	list( $rs ) = dbFetchArray($qs);
	if( !is_null( $rs ) )
	{
		$sc = $rs;
	}
	return $sc;
}


public function available_qty($id_product_attribute="", $id_warehouse =''){
	if($id_product_attribute ==""){	$id_product_attribute = $this->id_product_attribute; }
	if($id_warehouse =="" ){
		list($qty) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_sock JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone WHERE id_product_attribute = $id_product_attribute AND tbl_stock.id_zone != 0 AND id_warehouse != 2"));
		//list($qty) = dbFetchArray(dbQuery("select SUM(qty) from tbl_stock where id_product_attribute = $id_product_attribute AND id_warehouse != 2"));
		}else{
		list($qty) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_stock LEFT JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone WHERE id_product_attribute = $id_product_attribute AND tbl_zone.id_warehouse = $id_warehouse GROUP BY id_product_attribute"));
	}
	return $qty;
}

public function order_qty(){
	$id_product_attribute = $this->id_product_attribute;
	list($qty) = dbFetchArray(dbQuery("SELECT SUM(product_qty) FROM tbl_order_detail LEFT JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order WHERE id_product_attribute = $id_product_attribute AND valid_detail = 0 AND current_state NOT IN(6,7,8,9)"));
	return $qty;
	}

public function orderProductQty($id_product){
	$order_qty = 0;
	$sql = dbQuery("SELECT SUM(product_qty) FROM tbl_order_detail JOIN tbl_order ON tbl_order_detail.id_order = tbl_order.id_order WHERE tbl_order_detail.id_product = '$id_product' and valid_detail = '0' and current_state NOT IN(6,7,8,9)");
	if(dbNumRows($sql) >0)
	{
		list($qty) = dbFetchArray($sql);
		if( !is_null($qty)){ $order_qty = $qty; }
	}
	return $order_qty;
}

///////  return stock from all zone  //////
public function stock_qty($id_product_attribute)
{
	$qty = 0;
	$sql = dbQuery("SELECT SUM(qty) AS qty FROM tbl_stock WHERE id_product_attribute = ".$id_product_attribute);
	$row = dbNumRows($sql);
	if($row > 0 ){
		$rs = dbFetchArray($sql);
		$qty += $rs['qty'];
	}
	return $qty;
}
public function move_qty($id_product_attribute)
{
	$qty = 0;
	$sql = dbQuery("SELECT SUM(qty_move) AS qty FROM tbl_move WHERE id_product_attribute = ".$id_product_attribute);
	$row = dbNumRows($sql);
	if($row > 0){
		$rs = dbFetchArray($sql);
		$qty += $rs['qty'];
	}
	return $qty;
}

public function cancle_qty($id_product_attribute)
{
	$qty = 0;
	$sql = dbQuery("SELECT SUM(qty) AS qty FROM tbl_cancle WHERE id_product_attribute = ".$id_product_attribute);
	$row = dbNumRows($sql);
	if($row > 0 ){
		$rs = dbFetchArray($sql);
		$qty += $rs['qty'];
	}
	return $qty;
}

public function buff_qty($id_product_attribute)
{
	$qty = 0;
	$sql = dbQuery("SELECT SUM(qty) AS qty FROM tbl_buffer WHERE id_product_attribute = ".$id_product_attribute);
	$row = dbNumRows($sql);
	if($row > 0 ){
		$rs = dbFetchArray($sql);
		$qty += $rs['qty'];
	}
	return $qty;
}


public function buffer_qty_by_order($id_pa, $id_order)
{
	$sc = 0;
	if($id_order != '')
	{
		$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_buffer WHERE id_product_attribute = '".$id_pa."' AND id_order = '".$id_order."'");
		list($qty) = dbFetchArray($qs);

		$sc = is_null($qty) ? 0 : $qty;
	}

	return $qty;
}


/// ส่งกลับยอดคงเหลือสินค้าที่สามารถสั่งซื้อได้เท่านั้น  ////
public function available_order_qty($id_product_attribute, $id_order='')
{
	$qty = 0;
	$move = $this->move_qty($id_product_attribute);
	$cancle = $this->cancle_qty($id_product_attribute);
	$order_qty = $this->orderQty($id_product_attribute);
	$buffer_qty = $id_order == '' ? 0 : $this->buffer_qty_by_order($id_product_attribute, $id_order);
	$sql = dbQuery("SELECT SUM(qty) AS qty FROM tbl_stock JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone WHERE id_product_attribute = ".$id_product_attribute." AND id_warehouse != 2");
	$row = dbNumRows($sql);
	if($row > 0){
		$rs = dbFetchArray($sql);
		$qty += $rs['qty'];
	}
	$qty += $move;
	$qty += $cancle;
	$qty += $buffer_qty;
	$qty -= $order_qty;

	return $qty;
}


///// ส่งกลับยอดคงเหลือทั้งหมดที่มีทุกคลังทุกโซน รวม move , cancle , buffer ** สำหรับออกรายงานคงเหลือปัจุบัน//////
public function all_available_qty($id_product_attribute){
	$qty = 0;
	/// stock in zone
	$stock_qty = $this->stock_qty($id_product_attribute);
	/// stock on move
	$move_qty = $this->move_qty($id_product_attribute);
	/// stock in Cancle
	$cancle_qty = $this->cancle_qty($id_product_attribute);
	/// stock in buffer
	$buffer_qty = $this->buff_qty($id_product_attribute);

	$qty = $stock_qty + $move_qty + $cancle_qty + $buffer_qty;

	return $qty;
}

public function all_available_qty_by_warehouse($id_product_attribute, $id_warehouse){
	$qty = 0;
	/// stock in zone
	$stock_qty = $this->stock_qty_by_warehouse($id_product_attribute, $id_warehouse);
	/// stock on move
	$move_qty = $this->move_qty_by_warehouse($id_product_attribute, $id_warehouse);
	/// stock in Cancle
	$cancle_qty = $this->cancle_qty_by_warehouse($id_product_attribute, $id_warehouse);
	/// stock in buffer
	$buffer_qty = $this->buffer_qty_by_warehouse($id_product_attribute, $id_warehouse);

	$qty = $stock_qty + $move_qty + $cancle_qty + $buffer_qty;

	return $qty;
}

////  ส่งกลับ อาเรย์ เป็นยอดรวมทั้งหมด และ ราคาทุนรวมทั้งหมด [total_qty] and [total_cost] ของสินค้าที่ระบุ ///
public function total_product_qty_and_cost($id_product, $id_warehouse = ""){
	$arr = array();
	$total_qty = 0;
	$total_cost = 0;
	$sql = dbQuery("SELECT id_product_attribute, cost FROM tbl_product_attribute WHERE id_product =".$id_product);
	while($rs = dbFetchArray($sql) ){
		$id_product_attribute = $rs['id_product_attribute'];
		$cost = $rs['cost'];
		if($id_warehouse != ""){
			$qty = $this->all_available_qty_by_warehouse($id_product_attribute, $id_warehouse);
			$total_qty += $qty;
			$sum_cost = $qty * $cost;
			$total_cost += $sum_cost;
		}else{
			$qty = $this->all_available_qty($id_product_attribute);
			$total_qty += $qty;
			$sum_cost = $qty * $cost;
			$total_cost += $sum_cost;
		}
	}
	$arr['total_qty'] = $total_qty;
	$arr['total_cost'] = $total_cost;
	return $arr;
}
////  ส่งกลับ อาเรย์ เป็นยอดรวมทั้งหมด และ ราคาทุนรวมทั้งหมด [total_qty] and [total_cost] ของสินค้าทั้งหมดตามคลังที่ระบุ ///
public function all_qty_and_cost($id_warehouse =""){
	$arr = array();
	$total_qty = 0;
	$total_cost = 0;
	$sql = dbQuery("SELECT id_product FROM tbl_product");
	if($id_warehouse !=""){
		while($rs = dbFetchArray($sql)){
			$id_product = $rs['id_product'];
			$cs = $this->total_product_qty_and_cost($id_product, $id_warehouse);
			$total_qty += $cs['total_qty'];
			$total_cost += $cs['total_cost'];
		}
	}else{
		while($rs = dbFetchArray($sql)){
			$id_product = $rs['id_product'];
			$cs = $this->total_product_qty_and_cost($id_product);
			$total_qty += $cs['total_qty'];
			$total_cost += $cs['total_cost'];
		}
	}
	$arr['total_qty'] = $total_qty;
	$arr['total_cost'] = $total_cost;
	return $arr;
}


public function stock_qty_by_warehouse($id_product_attribute, $id_warehouse){
	$qty = 0;
	$sql = dbQuery("SELECT SUM(qty) AS qty FROM tbl_stock JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone WHERE id_product_attribute = ".$id_product_attribute." AND id_warehouse IN(".$id_warehouse.")");
	$row = dbNumRows($sql);
	if($row > 0 ){
		$rs = dbFetchArray($sql);
		$qty += $rs['qty'];
	}
	return $qty;
}

public function move_qty_by_warehouse($id_product_attribute, $id_warehouse){
	$qty = 0;
	$sql = dbQuery("SELECT SUM(qty_move) AS qty FROM tbl_move WHERE id_product_attribute = ".$id_product_attribute." AND id_warehouse IN(".$id_warehouse.")");
	$row = dbNumRows($sql);
	if($row > 0){
		$rs = dbFetchArray($sql);
		$qty += $rs['qty'];
	}
	return $qty;
}


public function cancle_qty_by_warehouse($id_product_attribute, $id_warehouse)
{
	$qty = 0;
	$sql = dbQuery("SELECT SUM(qty) AS qty FROM tbl_cancle WHERE id_product_attribute = ".$id_product_attribute." AND id_warehouse IN(".$id_warehouse.")");
	$row = dbNumRows($sql);
	if($row > 0 ){
		$rs = dbFetchArray($sql);
		$qty += $rs['qty'];
	}
	return $qty;
}

public function buffer_qty_by_warehouse($id_product_attribute, $id_warehouse)
{
	$qty = 0;
	$sql = dbQuery("SELECT SUM(qty) AS qty FROM tbl_buffer WHERE id_product_attribute = ".$id_product_attribute." AND id_warehouse IN(".$id_warehouse.")");
	$row = dbNumRows($sql);
	if($row > 0 ){
		$rs = dbFetchArray($sql);
		$qty += $rs['qty'];
	}
	return $qty;
}

///// แสดงยอดคงเหลือสำหรับสั่งสินค้าแสดงเป็นรุ่นสินค้า //////

public function available_product_qty($id_pd)
{
	$sc = 0;
	if( $this->isVisual($id_pd) === FALSE)
	{
		$qs = "SELECT SUM(qty) FROM tbl_stock JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$qs .= "JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone WHERE id_product = ".$id_pd." AND id_warehouse != 2 AND qty > 0";
		$qs = dbQuery($qs);
		if(dbNumRows($qs) == 1 )
		{
			list($qty) = dbFetchArray($qs);
			if( ! is_null($qty))
			{
				$orderQty = $this->orderProductQty($id_pd);
				$sc = $qty - $orderQty;
			}
		}
	}

	return $sc;
}


public function get_id_product_attribute_by_barcode($barcode){
	$sql = dbQuery("SELECT tbl_product_attribute.id_product_attribute FROM tbl_product_attribute LEFT JOIN tbl_product_pack ON tbl_product_attribute.id_product_attribute = tbl_product_pack.id_product_attribute WHERE tbl_product_attribute.barcode ='$barcode' OR tbl_product_pack.barcode_pack ='$barcode' ");
		list($id_product_attribute) = dbFetchArray($sql);
		return $id_product_attribute;
	}


public function check_barcode($barcode)
{
	$sql = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE barcode ='$barcode'");
	$row = dbNumRows($sql);
	if($row > 0 )
	{
		list($id_product_attribute) = dbFetchArray($sql);
		$qty = 1;
	}
	else
	{
		$sqr = dbQuery("SELECT id_product_attribute, qty FROM tbl_product_pack WHERE barcode_pack = '$barcode'");
		if(dbNumRows($sqr) > 0 )
		{
			list($id_product_attribute, $qty) = dbFetchArray($sqr);
		}
		else
		{
			$id_product_attribute 	= 0;
			$qty							= 0;
		}
	}
	$arr = array('id_product_attribute'=>$id_product_attribute, 'qty'=>$qty);
	return $arr;
}

//********************  ส่งกลับ ยอดรวมของราคาทุนสินค้าแต่ละ Style **********************//
public function get_current_stock($id_product){
	$sql = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = $id_product");
	$row = dbNumRows($sql);
	$total_cost = 0;
	$total_qty = 0;
	$i =0;
	while($i<$row){
		list($id_product_attribute) = dbFetchArray($sql);
		list($cost) = dbFetchArray(dbQuery("SELECT cost FROM tbl_product_attribute WHERE id_product_attribute = $id_product_attribute"));
		$qty = $this->stock_qty($id_product_attribute); ///// ได้ยอดสินค้าในโซนทั้งหมด
		$move_qty = $this->move_qty($id_product_attribute); // ได้ค่ากลับมาเป็นจำนวนสินค้าที่กำลังถูกย้ายอยู่ (ไม่อยู่ในสต็อก)
		$cancle_qty = $this->cancle_qty($id_product_attribute); //// ได้ยอดสินค้าในตาราง ยกเลิกกลับมา
		$buffer_qty = $this->buff_qty($id_product_attribute); //// ได้ยอดสินค้าในตาราง Buffer
		$qty = $qty + $move_qty + $cancle_qty + $buffer_qty;
		$cost_amount = $cost*$qty;
		$total_qty += $qty;
		$total_cost += $cost_amount;
		$i++;
	}
	$product['total_qty'] = $total_qty;
	$product['total_cost'] = $total_cost;
	return $product;
}

public function getCoverImage($id_productx,$use_size='',$class=''){
	if($class !=""){ $class_name = "class='".$class."'";}else{ $class_name ="";}
	$sql=dbQuery("SELECT * FROM tbl_image WHERE id_product =$id_productx AND cover=1");
	$row = dbNumRows($sql);
	if($row==1){
			$list = dbFetchArray($sql);
			list($id_image, $id_product, $position, $cover) = $list;
			$count = strlen($id_image);
			$path = str_split($id_image);
			$image_path = WEB_ROOT."img/product";
			$n=0;
			while($n<$count){
				$image_path .= "/".$path[$n];
				$n++;
			}
			if($use_size != ""){
				switch($use_size){
					case "1" :
						$pre_fix = "product_mini_";
						$no_image = "no_image_mini";
						break;
					case "2" :
						$pre_fix = "product_default_";
						$no_image = "no_image_default";
						break;
					case "3" :
						$pre_fix = "product_medium_";
						$no_image = "no_image_medium";
						break;
					case "4" :
						$pre_fix = "product_lage_";
						$no_image = "no_image_lage";
						break;
					default :
						$pre_fix = "";
						$no_image = "no_image_mini";
						break;
				}
			}else{
				$pre_fix = "product_mini_";
			}
			$image_path .= "/".$pre_fix.$id_image.".jpg";
			$image = DOC_ROOT.$image_path;
			if( file_exists($image) )
			{
				return"<img ".$class_name."  src='$image_path' />";
			}
			else
			{
				return "<img  ".$class_name." src='".WEB_ROOT."img/product/".$no_image.".jpg' />";
			}
	}else{
		if($use_size != ""){
			switch($use_size){
				case "1" :
					$pre_fix = "product_mini_";
					$no_image = "no_image_mini";
					break;
				case "2" :
					$pre_fix = "product_default_";
					$no_image = "no_image_default";
					break;
				case "3" :
					$pre_fix = "product_medium_";
					$no_image = "no_image_medium";
					break;
				case "4" :
					$pre_fix = "product_lage_";
					$no_image = "no_image_lage";
					break;
				default :
					$pre_fix = "";
					$no_image = "no_image_mini";
					break;
			}
		}
		return "<img  ".$class_name." src='".WEB_ROOT."img/product/".$no_image.".jpg' />";
	}
}


public function get_image_path($id_image,$use_size){
			$count = strlen($id_image);
			$path = str_split($id_image);
			$image_path = WEB_ROOT."img/product";
			$n=0;
					while($n<$count){
						$image_path .= "/".$path[$n];
						$n++;
					}
				$image_path .= "/";
				$image_path_name ="";
					switch($use_size){
						case "1" :
							$pre_fix = "product_mini_";
							$no_image = "no_image_mini";
							break;
						case "2" :
							$pre_fix = "product_default_";
							$no_image = "no_image_default";
							break;
						case "3" :
							$pre_fix = "product_medium_";
							$no_image = "no_image_medium";
							break;
						case "4" :
							$pre_fix = "product_lage_";
							$no_image = "no_image_lage";
							break;
						default :
							$pre_fix = "";
							$no_image = "no_image_mini";
							break;
					}
					if($n == "0"){
						$image_path_name = $image_path.$no_image.".jpg";
					}else{
						$image_path_name = $image_path.$pre_fix.$id_image.".jpg";
					}
		return $image_path_name;
}


public function get_product_attribute_image($id_product_attribute,$use_size){
		list($id_image) = dbFetchArray(dbQuery("SELECT id_image FROM tbl_product_attribute_image WHERE id_product_attribute = $id_product_attribute"));
		list($id_product) = dbFetchArray(dbQuery("SELECT id_product FROM tbl_product_attribute WHERE id_product_attribute = $id_product_attribute"));
		list($id_image_cover) = dbFetchArray(dbQuery("SELECT id_image FROM tbl_image WHERE id_product = $id_product AND cover = 1"));
		if($id_image !=""){
			$image = $this->get_image_path($id_image,$use_size);
		}else{
			$image = $this->get_image_path($id_image_cover,$use_size);
		}
		return $image;
}


public function showImage($id_productx,$use_size){
		$sql=dbQuery("SELECT * FROM tbl_image WHERE id_product =$id_productx ORDER BY position ASC");
		$row = dbNumRows($sql);
		$i=0;
		$image_product = "";
		if($row>0){
				while($i<$row){
					$list = dbFetchArray($sql);
					list($id_image, $id_product, $position, $cover) = $list;
					$count = strlen($id_image);
					$path = str_split($id_image);
					$image_path = WEB_ROOT."img/product";

					$n=0;
						while($n<$count){
							$image_path .= "/".$path[$n];
							$n++;
						}
					if($use_size != ""){
					switch($use_size){
						case "1" :
							$pre_fix = "product_mini_";
							$no_image = "no_image_mini";
							break;
						case "2" :
							$pre_fix = "product_default_";
							$no_image = "no_image_default";
							break;
						case "3" :
							$pre_fix = "product_medium_";
							$no_image = "no_image_medium";
							break;
						case "4" :
							$pre_fix = "product_lage_";
							$no_image = "no_image_lage";
							break;
						default :
							$pre_fix = "";
							$no_image = "no_image_mini";
							break;
					}
				}
					$image_path .= "/";
					$image_path .= $pre_fix.$id_image.".jpg";
					$image_product .="<a href='$image_path'><img src='$image_path' class='img-responsive' alt='img'></a> ";
					$i++;
				}
		}else{
			$image_path = WEB_ROOT."img/product";
			if($use_size !=""){
				switch($use_size){
					case "1" :
						$pre_fix = "no_image_mini";
						break;
					case "2" :
						$pre_fix = "no_image_default";
						break;
					case "3" :
						$pre_fix = "no_image_medium";
						break;
					case "4" :
						$pre_fix = "no_image_lage";
						break;
					default :
						$pre_fix = "no_image_lage";
						break;
				}
			}
			$image_path .= "/";
			$image_path .= $pre_fix.".jpg";
			$image_product .="<a href='$image_path'><img src='$image_path' class='img-responsive' alt='img'></a> ";
		}

		$this->image_product = $image_product;
	}

public function maxShowstock(){
	return getConfig("MAX_SHOW_STOCK");
}

public function get_product_reference($id_product_attribute)
{
	$reference = "";
	$sql = dbQuery("SELECT reference FROM tbl_product_attribute WHERE id_product_attribute = ".$id_product_attribute);
	if(dbNumRows($sql) == 1){
		$rs = dbFetchArray($sql);
		$reference = $rs['reference'];
	}
	return $reference;
}

public function attributeGrid($id_product)
{
	$result = "";
	$filter = $this->maxShowstock();
	if($filter == 0 || $filter ==""){ $filter = 100000000000; } /// กำหนดสต็อกฟิลว์เตอร์
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
		$result .= "<table class='table table-bordered' style='width:100%'>";
		//$row = dbNumRows($data);
		$i = 0;
		while($rd = dbFetchArray($data))
		{
			if($i%2 == 0){ $result .= "<tr>"; 	}
			$qty = $this->available_order_qty($rd['id_product_attribute']);
			if($qty <1){ $disabled = "disabled"; }else if($qty > $filter){ $qty = $filter; $disabled = ""; }else{ $disabled = ""; }
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
		$result .="<table class='table table-bordered' style='width:100%'>";
		if(isset($colors) && isset($sizes) ){
			$co = dbQuery("SELECT tbl_color.id_color,color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
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
					$qty = $this->available_order_qty($id_product_attribute);
					if($qty <1){ $disabled = "disabled"; $stock = "<span style='color:red'>สินค้าหมด</span>";  }else if($qty > $filter){ $qty = $filter; $disabled =""; $stock = $qty; }else{ $disabled = ""; $stock = $qty; }
					$result .= "
						<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>
							<input type='text' class='form-control' name='qty[".$id_product_attribute."]' onkeyup='valid_qty($(this), ".$qty.")' ".$disabled." /><center>".$stock."</center>
						</td>";
				}//end while
				$result .= "</tr>";
			}//end while
		}else if(isset($colors) && isset($attributes) ){
			$co = dbQuery("SELECT tbl_color.id_color, color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
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
					$qty = $this->available_order_qty($id_product_attribute);
					if($qty <1){ $disabled = "disabled"; $stock = "<span style='color:red'>สินค้าหมด</span>";  }else if($qty > $filter){ $qty = $filter; $disabled =""; $stock = $qty; }else{ $disabled = ""; $stock = $qty; }
					$result .= "
						<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>
							<input type='text' class='form-control' name='qty[".$id_product_attribute."]' onkeyup='valid_qty($(this), ".$qty.")' ".$disabled." /><center>".$stock."</center>
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
					list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND tbl_product_attribute.id_attribute = ".$rc['id']. "ORDER BY position ASC"));
					$qty = $this->available_order_qty($id_product_attribute);
					if($qty <1){ $disabled = "disabled"; $stock = "<span style='color:red'>สินค้าหมด</span>";  }else if($qty > $filter){ $qty = $filter; $disabled =""; $stock = $qty; }else{ $disabled = ""; $stock = $qty; }
					$result .= "
						<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>
							<input type='text' class='form-control' name='qty[".$id_product_attribute."]' onkeyup='valid_qty($(this), ".$qty.")' ".$disabled." /><center>".$stock."</center>
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
			$qr = dbQuery("SELECT tbl_color.id_color, color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
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
					$qty = $this->available_order_qty($id_product_attribute);
					if($qty <1){ $disabled = "disabled"; $stock = "<span style='color:red'>สินค้าหมด</span>";  }else if($qty > $filter){ $qty = $filter; $disabled =""; $stock = $qty; }else{ $disabled = ""; $stock = $qty; }
					$result .= "
						<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>
							<input type='text' class='form-control' name='qty[".$id_product_attribute."]' onkeyup='valid_qty($(this), ".$qty.")' ".$disabled." /><center>".$stock."</center>
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
	if(isset($_COOKIE['id_cart'])){
			$id_cart = $_COOKIE['id_cart'];
		}else{
			$id_cart = "";
		}
		$result .= "<input type='hidden' id='id_cart' value='$id_cart' >";
	return $result;
}// end function


public function hasAttribute($id_pd, $attr)
{
	$sc = FALSE;
	$id		= $attr == 'color' ? 'id_color' : ( $attr == 'size' ? 'id_size' : 'id_attribute' );
	$qs = dbQuery("SELECT ".$id." FROM tbl_product_attribute WHERE id_product = ".$id_pd." AND ".$id." != 0 ");
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;
	}
	return $sc;
}


private function colorAttributeGrid($id_product)
{
	$qs = "SELECT id_product_attribute, tbl_product_attribute.id_color AS id, color_code AS code, active  ";
	$qs .= "FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color ";
	$qs .= "WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 ";

	return dbQuery($qs);

}

private function sizeAttributeGrid($id_product)
{
	$qs = "SELECT id_product_attribute, tbl_product_attribute.id_size AS id, size_name AS code, active ";
	$qs .= "FROM tbl_product_attribute JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size ";
	$qs .= "WHERE id_product = ".$id_product." AND tbl_product_attribute.id_size != 0";

	return dbQuery($qs);
}


private function attrAttributeGrid($id_product)
{
	$qs = "SELECT id_product_attribute, tbl_product_attribute.id_attribute AS id, attribute_name AS code, active ";
	$qs .= "FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute ";
	$qs .= "WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0";

	return dbQuery($qs);
}





//-----  Header of grid table
private function gridHeader($id_pd, $attr='color')
{
	$sc = '';
	switch($attr)
	{
		case 'color' :
			$qs = "SELECT tbl_product_attribute.id_color AS id, color_code AS code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color ";
			$qs .= "WHERE id_product = ".$id_pd." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC";
		break;
		case 'size' :
			$qs  = "SELECT tbl_product_attribute.id_size AS id, size_name AS code FROM tbl_product_attribute JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size ";
			$qs .= "WHERE id_product = ".$id_pd." AND tbl_product_attribute.id_size != 0 GROUP BY tbl_product_attribute.id_size ORDER BY position ASC";
		break;
		case 'attribute' :
			$qs  = "SELECT tbl_product_attribute.id_attribute AS id, attribute_name AS code FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute ";
			$qs .= "WHERE id_product = ".$id_pd." AND tbl_product_attribute.id_attribute != 0 GROUP BY tbl_product_attribute.id_attribute ORDER BY position ASC";
		break;
	}
	$qs = dbQuery($qs);
	$sc .= '<tr style="font-size:12px;"><td>&nbsp;</td>';
	while($rs = dbFetchArray($qs))
	{
		$sc .= '<td class="text-center middle"><strong>'.$rs['code']. ($attr === 'color' ? '<br/>'. color_name($rs['id']) : '').'</strong></td>';
	}
	$sc .= '</tr>';

	return $sc;
}

private function getColorGridQuery($id_pd)
{
	$co  = "SELECT tbl_product_attribute.id_color AS id FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color ";
	$co .= "WHERE id_product = ".$id_pd." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC ";

	return dbQuery($co);
}

private function getSizeGridQuery($id_pd)
{
	$qr  = "SELECT tbl_product_attribute.id_size AS id FROM tbl_product_attribute JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size ";
	$qr .= "WHERE id_product = ".$id_pd." AND tbl_product_attribute.id_size != 0 GROUP BY tbl_product_attribute.id_size  ORDER BY position ASC ";

	return dbQuery($qr);
}

private function getAttributeGridQuery($id_pd)
{
	$attribute	= "SELECT tbl_product_attribute.id_attribute AS id FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute ";
	$attribute .= "WHERE id_product = ".$id_pd." AND tbl_product_attribute.id_attribute != 0 GROUP BY tbl_product_attribute.id_attribute  ORDER BY position ASC ";

	return dbQuery($attribute);
}


private function getIdProductAttributeByAttrs($id_pd, $id_color = 0, $id_size = 0, $id_attribute = 0)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = ".$id_pd." AND id_color = ".$id_color." AND id_size = ".$id_size." AND id_attribute = ".$id_attribute);
	if(dbNumRows($qs) == 1)
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}

private function orderAttributeGridOneAttribute($id_pd, $attr, $isVisual, $id_order = '')
{
	$sc 	= '';
	$data 	= $attr === 'color' ? $this->colorAttributeGrid($id_pd) : ($attr === 'size' ? $this->sizeAttributeGrid($id_pd) : $this->attrAttributeGrid($id_pd));
	$sc 	.= "<table class='table table-bordered'>";
	$i 		= 0;
	while($rs = dbFetchArray($data))
	{
		$id_pa 		= $rs['id_product_attribute'];
		$active		= $rs['active'];
		$sc 		.= $i%2 == 0 ? '<tr>' : '';
		$stock 		= $isVisual === FALSE ? ( $active == 1 ? $this->stock_qty($id_pa) : 0 ) : 0; //--- สต็อกทุกคลัง
		$orderQty 	= $isVisual === FALSE ? ( $active == 1 ? $this->orderQty($id_pa) : 0 ) : 0; //--- ออเดอร์ค้างส่ง
		$all_qty 		= $isVisual === FALSE ? ( $active == 1 ? (($stock - $orderQty) < 0 ? 0 : $stock - $orderQty) : 0) : 0;
		$qty        = $isVisual === FALSE ? ( $active == 1 ? $this->available_order_qty($id_pa, $id_order) : FALSE ) : FALSE; //---- ถ้าเป็นสินค้าเสมือนไม่ต้องมีสต็อก


		$disabled 	= $isVisual === TRUE  && $active == 1 ? '' : ($qty < 1 ? 'disabled' : '');

		$sc 	.= '<td class="middle" style="border-right:0px;">';
		$sc 	.= '<strong>' .	$rs['code'] . '</strong>';
		$sc 	.= 		$qty !== FALSE && $qty > 0 && $active == 1 ? '<p class="pull-rigth;" style="color:green;">'.$qty.' ในสต็อก</p>':'';
		$sc 	.= 		($qty !== FALSE && $qty < 1 ) OR $active == 0 ? '<p class="pull-right;" style="color:red;">สินค้าหมด</p>' : '';
		$sc 	.= '</td>';

		$sc 	.= '<td class="middle" style="width:100px; padding-right:10px; border-left:0px;">';
		$sc 	.= $isVisual === FALSE ? '<center><span style="color:blue; font-size:10px;">('.$all_qty.')</span></center>':'';
		$sc 	.= '<input type="text" class="form-control" title="'.$id_pa.'" name="qty[0]['.$id_pa.']" id="qty_'.$id_pa.'" onkeyup="valid_qty($(this), '.($qty === FALSE ? 1000000 : $qty).')" '.$disabled.' />';
		$sc 	.= '</td>';

		$i++;

		$sc 	.= $i%2 == 0 ? '</tr>' : '';

	}// end while

	$sc	.= "</table>";

	return $sc;
}



private function orderAttributeGridTwoAttribute($id_pd, $color, $size, $attr, $isVisual, $id_order = '')
{
	$sc 	= '';
	$attrs 	= $color === TRUE ? 'color' : ($attr === TRUE ? 'attribute' : 'size');
	$sc 	.= '<table class="table table-bordered">';
	$sc 	.= $this->gridHeader($id_pd, $attrs);

	if( $color === TRUE )  //---- กรณีมีสี ใช้สีเป็น column ไซส์ หรือ อื่นๆ เป็น row
	{
		$qs = $size === TRUE ? $this->getSizeGridQuery($id_pd) : $this->getAttributeGridQuery($id_pd);

		while($rs = dbFetchArray($qs))
		{
			$label 		= $size === TRUE ? get_size_name($rs['id']) : get_attribute_name($rs['id']);
			$id_size 		= $size === TRUE ? $rs['id'] : 0;
			$id_attr 		= $attr === TRUE ? $rs['id'] : 0;

			$sc 		.= '<tr style="font-size:12px;">';
			$sc 		.= '<td class="text-center middle" style="width:70px;"><strong>'.$label.'</strong></td>';

			$qr = $this->getColorGridQuery($id_pd);  //----- Color data

			while($rd = dbFetchArray($qr))
			{
				$id_pa = $this->getIdProductAttributeByAttrs($id_pd, $rd['id'], $id_size, $id_attr);
				$active = $this->isActiveItem($id_pa) === TRUE ? 1 : 0;
				if( $id_pa !== FALSE)
				{
					$stock 		= $isVisual === FALSE && $active == 1 ? $this->stock_qty($id_pa) : 0; //--- สต็อกทุกคลัง
					$orderQty 	= $isVisual === FALSE && $active == 1 ? $this->orderQty($id_pa) : 0; //--- ออเดอร์ค้างส่ง
					$all_qty 		= $isVisual === FALSE && $active == 1 ? (($stock - $orderQty) < 0 ? 0 : $stock - $orderQty ) : 0;
					$qty        	= $isVisual === FALSE && $active == 1 ? $this->available_order_qty($id_pa, $id_order) : FALSE; //---- ถ้าเป็นสินค้าเสมือนไม่ต้องมีสต็อก
					$disabled 	= $isVisual === TRUE && $active == 1 ? '' : ($qty < 1 ? 'disabled' : ( $active == 1 ? '' : 'disabled' ));
					$available 	= $qty === FALSE && $active == 1 ? '' : (($qty < 1 || $active == 0) ? '<span style="color:red;">สินค้าหมด</span>' : $qty);

					$sc 	.= '<td class="middle text-center" style="width:70px; padding:5px;">';
					$sc 	.= $isVisual === FALSE ? '<center><span style="color:blue; font-size:10px;">('.$all_qty.')</span></center>' : '';
					$sc 	.= '<input type="text" class="form-control" title="'. $id_pa .'" name="qty['.$rd['id'].']['.$id_pa.']" id="qty_'.$id_pa.'" onkeyup="valid_qty($(this), '.($qty === FALSE ? 1000000 : $qty).')" '.$disabled.' />';
					$sc 	.= $isVisual === FALSE ? '<center>'.$available.'</center>' : '';
					$sc 	.= '</td>';
				}
				else
				{
					$sc .= '<td class="middle text-center padding-5">ไม่มีสินค้า</td>';
				}
			}//---- end while

			$sc .= '</tr>';
		} //---- End while

	}
	else //---- กรณีไม่มีสี แสดงว่า มีแค่ ไซส์ กับ อื่นๆ ใช้ อื่นๆเป็น column และ ไซส์ เป็น row
	{
		$qs = $this->getAttributeGridQuery($id_pd);
		while($rs = dbFetchArray($qs))
		{
			$label 		= get_attribute_name($rs['id']);
			$id_color 	= 0;
			$id_attr 	= $attr === TRUE ? $rs['id'] : 0;
			$sc 		.= '<tr>';
			$sc 		.= '<td class="text-center middle" style="width:70px;"><strong>'.$label.'</strong></td>';

			$qr = $this->getSizeGridQuery($id_pd);  //----- Color data

			while($rd = dbFetchArray($qr))
			{
				$id_pa = $this->getIdProductAttributeByAttrs($id_pd, $id_color, $rd['id'], $id_attr);
				$active = $this->isActiveItem($id_pa);
				if( $id_pa !== FALSE)
				{
					$stock 		= $isVisual === FALSE && $active == 1 ? $this->stock_qty($id_pa) : 0; //--- สต็อกทุกคลัง
					$orderQty 	= $isVisual === FALSE && $active == 1 ? $this->orderQty($id_pa) : 0; //--- ออเดอร์ค้างส่ง
					$all_qty 		= $isVisual === FALSE && $active == 1 ? (($stock - $orderQty) < 0 ? 0 : $stock - $orderQty) : 0;
					$qty       	= $isVisual === FALSE && $active == 1 ? $this->available_order_qty($id_pa, $id_order) : FALSE; //---- ถ้าเป็นสินค้าเสมือนไม่ต้องมีสต็อก
					$disabled 	= $isVisual === TRUE && $active == 1 ? '' : ($qty < 1 ? 'disabled' : '');
					$available 	= $qty === FALSE && $active == 1 ? '' : (($qty < 1 OR $active == 0) ? '<span style="color:red;">สินค้าหมด</span>' : $qty);

					$sc 	.= '<td class="middle text-center" style="width:70px; padding:5px;">';
					$sc 	.= $isVisual === FALSE ? '<center><span style="color:blue; font-size:10px;">('.$all_qty.')</span></center>' : '';
					$sc 	.= '<input type="text" class="form-control" title="'.$id_pa.'" name="qty['.$rd['id'].']['.$id_pa.']" id="qty_'.$id_pa.'" onkeyup="valid_qty($(this), '.$qty === FALSE ? 1000000 : $qty.')" '.$disabled.' />';
					$sc 	.= $isVisual === FALSE ? '<center>'.$available.'</center>' : '';
					$sc 	.= '</td>';
				}
				else
				{
					$sc .= '<td class="middle text-center padding-5">ไม่มีสินค้า</td>';
				}
			}//---- end while

			$sc .= '</tr>';
		} //---- End while

	}

	$sc .= '</table>';

	return $sc;

}


private function orderAttributeGridThreeAttribute($id_pd, $id_attr, $isVisual, $id_order = '')
{
	$sc 	 = '';
	$attrs 	 = 'color';
	$sc 	.= '<table class="table table-bordered">';
	$sc 	.= $this->gridHeader($id_pd, $attrs);

	$qs 	 = $this->getSizeGridQuery($id_pd);

	while($rs = dbFetchArray($qs))
	{
		$label 		= get_size_name($rs['id']);
		$id_size 	= $rs['id'];
		$sc 		.= '<tr>';
		$sc 		.= '<td class="text-center middle" style="width:70px;"><strong>'.$label.'</strong></td>';

		$qr = $this->getColorGridQuery($id_pd);  //----- Color data

		while($rd = dbFetchArray($qr))
		{
			$id_pa = $this->getIdProductAttributeByAttrs($id_pd, $rd['id'], $id_size, $id_attr);
			$active = $this->isActiveItem($id_pa);
			if( $id_pa !== FALSE)
			{
				$stock 		= $isVisual === FALSE && $active == 1 ? $this->stock_qty($id_pa) : 0; //--- สต็อกทุกคลัง
				$orderQty 	= $isVisual === FALSE && $active == 1 ? $this->orderQty($id_pa) : 0; //--- ออเดอร์ค้างส่ง
				$all_qty 		= $isVisual === FALSE && $active == 1 ? (($stock - $orderQty) < 0 ? 0 : $stock - $orderQty ) : 0;
				$qty        	= $isVisual === FALSE && $active == 1 ? $this->available_order_qty($id_pa, $id_order) : FALSE; //---- ถ้าเป็นสินค้าเสมือนไม่ต้องมีสต็อก
				$disabled 	= $isVisual === TRUE && $active == 1 ? '' : ($qty < 1 ? 'disabled' : '');
				$available 	= $qty === FALSE && $active == 1 ? '' : (($qty < 1 OR $active == 0) ? '<span style="color:red;">สินค้าหมด</span>' : $qty);

				$sc 	.= '<td class="middle text-center" style="width:70px; padding:5px;">';
				$sc 	.= '<center><span style="color:blue; font-size:10px;">('.$all_qty.')</span></center>';
				$sc 	.= '<input type="text" class="form-control" title="'.$id_pa.'" name="qty['.$rd['id'].']['.$id_pa.']" id="qty_'.$id_pa.'" onkeyup="valid_qty($(this), '.($qty === FALSE ? 1000000 : $qty).')" '.$disabled.' />';
				$sc 	.= '<center>'.$available.'</center>';
				$sc 	.= '</td>';
			}
			else
			{
				$sc .= '<td class="middle text-center padding-5">ไม่มีสินค้า</td>';
			}
		}//---- end while

		$sc .= '</tr>';
	} //---- End while

	$sc .= '</table>';
	return $sc;
}


///******************  test  ***********************///
public function order_attribute_grid($id_product, $id_order = '')
{
	$sc = '';
    $isVisual 	= $this->isVisual($id_product);

	/**********************  ตรวจสอบว่าสินค้ามีกี่คุณลักษณะ *********************/

	$q_color 		= $this->colorAttributeGrid($id_product);
	$q_size 		= $this->sizeAttributeGrid($id_product);
	$q_attribute	= $this->attrAttributeGrid($id_product);

	$rc 		= $this->hasAttribute($id_product, 'color') === TRUE ? 1 : 0 ;
	$rs			= $this->hasAttribute($id_product, 'size') === TRUE ? 1 : 0 ;
	$ra			= $this->hasAttribute($id_product, 'attribute') === TRUE ? 1 : 0 ;

	$c_count	= $rc . $rs . $ra;

	if($c_count == "100" || $c_count == "010" || $c_count == "001") /// กรณีมี คุณลักษณะเดียว
	{
		$attr = $rc == 1 ? 'color' : ($rs == 1 ? 'size' : 'attribute');
		$sc .= $this->orderAttributeGridOneAttribute($id_product, $attr, $isVisual, $id_order);
	}

	else if($c_count == "110" || $c_count == "101" || $c_count == "011") /// กรณีมี 2 คุณลักษณะ

	{
		$color 	= ($c_count === '110' OR $c_count === '101') ? TRUE : FALSE;
		$size 	= ($c_count === '110' OR $c_count === '011') ? TRUE : FALSE;
		$attr 	= ($c_count === '011' OR $c_count === '101') ? TRUE : FALSE;

		$sc .= $this->orderAttributeGridTwoAttribute($id_product, $color, $size, $attr, $isVisual, $id_order);
	}
	else if($c_count == "111") /// กรณีมี 3 คุณลักษณะ
	{

		$sc .= '<ul class="nav nav-tabs">';

		$tab = dbQuery("SELECT tbl_product_attribute.id_attribute AS id, attribute_name AS name FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND tbl_product_attribute.id_attribute != 0 GROUP BY tbl_product_attribute.id_attribute ORDER BY position ASC" );

		$n = 1;

		while($cs = dbFetchArray($tab) )
		{
			$sc .= '<li role="presentation" class="'.($n == 1 ? 'active' : '').'">';
			$sc .= '<a href="#'.$cs['id'].'" aria-controls="'.$cs['id'].'" role="tab" data-toggle="tab">'.$cs['name'].'</a>';
			$sc .= '</li>';

			$n++;

		}//end while


		$sc .= '</ul>';

		$co = $this->getAttributeGridQuery($id_product);

		$sc .= '<div class="tab-content">';

		$n = 1;

		while($cs = dbFetchArray($co) )
		{
			$id_attr = $cs['id'];
			$sc .= '<div role="tabpanel" class="tab-pane'.($n == 1 ? ' active' : '').'" id="'.$id_attr.'">';

			$sc .= $this->orderAttributeGridThreeAttribute($id_product, $id_attr, $isVisual, $id_order);

			$sc .= '</div>';

			$n++;
		}// end while

		$sc .= '</div><! ---- end tab-content ----->';

	} /// จบ เงื่อนไขนับคุณลักษณะ

	$sc .= "
		<script>
			function valid_qty(el, qty)
			{
					var order_qty = el.val();
					if(isNaN(order_qty))
					{
						swal('กรุณาใส่ตัวเลขเท่านั้น');
						el.val('');
						el.focus();
						return false;
					}else{
						if(parseInt(order_qty) > parseInt(qty) )
						{
							swal('มีสินค้าในสต็อกแค่ '+qty);
							el.val('');
							el.focus();
						}
					}
			}
		</script>
	";

	return $sc;
}// end function




//*************************************  Request attribute Grid  *****************************//
public function request_attribute_grid($id_product)
{
	$result = "";
	$filter = $this->maxShowstock();
	if($filter == 0 || $filter ==""){ $filter = 100000000000; } /// กำหนดสต็อกฟิลว์เตอร์
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
			$result .= $this->po_input_field($rd['id_product_attribute']);
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
			$co = dbQuery("SELECT tbl_product_attribute.id_color,color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
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

					$result .= $this->po_input_field($id_product_attribute);

				}//end while
				$result .= "</tr>";
			}//end while
		}else if(isset($colors) && isset($attributes) ){
			$co = dbQuery("SELECT tbl_color.id_color, color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
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

					$result .= $this->po_input_field($id_product_attribute);

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
					list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND tbl_product_attribute.id_attribute = ".$rc['id']." ORDER BY position ASC"));

					$result .= $this->po_input_field($id_product_attribute);

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
			$qr = dbQuery("SELECT tbl_color.id_color, color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
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

					$result .= $this->po_input_field($id_product_attribute);

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
			function valid_qty(el)
			{
					var order_qty = el.val();
					if(isNaN(order_qty))
					{
						alert('กรุณาใส่ตัวเลขเท่านั้น');
						el.val('');
						el.focus();
						return false;
					}
			}
		</script>
	";
	return $result;
}
//********************************** End Request Attribute Grid  *********************************//

public function po_input_field($id)
{
	if($id != "")
	{
		$rs = "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'><input type='text' class='form-control input_qty' name='qty[".$id."]' onkeyup='valid_qty($(this))' /></td>";
	}
	else
	{
		$rs = "<td align='center' style='width:70px; vertical-align:middle; padding:5px; font-size: 11px;'><center>ไม่มีสินค้า</center></td>";
	}
	return $rs;
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




		public function stock_in_zone($id_product_attribute,$all_zone=false){
			if($all_zone){
			$sql = dbQuery("SELECT zone_name,  SUM(qty) AS qty FROM tbl_stock JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone WHERE id_product_attribute = $id_product_attribute GROUP BY tbl_stock.id_zone  ORDER BY zone_name ASC");
			}else{
			$sql = dbQuery("SELECT zone_name,  SUM(qty) AS qty FROM tbl_stock JOIN tbl_zone ON tbl_stock.id_zone = tbl_zone.id_zone WHERE id_product_attribute = $id_product_attribute AND id_warehouse != 2  GROUP BY tbl_stock.id_zone ORDER BY zone_name ASC");
			}
			$result = "";
			while($row = dbFetchArray($sql)){
				$zone = $row['zone_name'];
				$qty = $row['qty'];
				$result = $result." ".$zone." : ".$qty."<br />";
			}
			if( $all_zone){
				list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = ".$id_product_attribute));
			}else{
				list($qty_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute' AND id_warehouse != 2"));
			}
			if($qty_moveing != ""){
				$result." กำลังย้ายโซน : ".$qty_moveing."<br />";
			}
			return $result;
		}

// ******************************************  ใช้กับ รายงาน  *********************************************//
public function reportAttributeGrid($id_product)
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
			$qty = $this->all_available_qty($rd['id_product_attribute']);
			if($qty == 0){ $stock = "<span style='color:red'>หมด</span>"; }else{ $stock = "<span style='color:green'>".$qty."</span>"; }
			$result .="<td style='vertical-align:middle;'>".$rd['code']."</td>
						<td align='center' style='width:100px; padding-right:10px; vertical-align:middle;'>".$stock."</td>";
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
			$co = dbQuery("SELECT tbl_color.id_color, color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
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
					$qty = $this->all_available_qty($id_product_attribute);
					if($qty == 0){ $stock = "<span style='color:red'>หมด</span>"; }else{ $stock = "<span style='color:green'>".$qty."</span>"; }
					$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>".$stock."</td>";
				}//end while
				$result .= "</tr>";
			}//end while
		}else if(isset($colors) && isset($attributes) ){
			$co = dbQuery("SELECT tbl_color.id_color, color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
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
					$qty = $this->all_available_qty($id_product_attribute);
					if($qty == 0){ $stock = "<span style='color:red'>หมด</span>"; }else{ $stock = "<span style='color:green'>".$qty."</span>"; }
					$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>".$stock."</td>";
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
					list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND tbl_product_attribute.id_attribute = ".$rc['id']." ORDER BY position ASC"));
					$qty = $this->all_available_qty($id_product_attribute);
					if($qty == 0){ $stock = "<span style='color:red'>หมด</span>"; }else{ $stock = "<span style='color:green'>".$qty."</span>"; }
					$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>".$stock."</td>";
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
			$qr = dbQuery("SELECT tbl_color.id_color, color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
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
					$qty = $this->all_available_qty($id_product_attribute);
					if($qty == 0){ $stock = "<span style='color:red'>หมด</span>"; }else{ $stock = "<span style='color:green'>".$qty."</span>"; }
					$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>".$stock."</td>";
				}//end while
				$result .= "</tr>";
			}//end while
			$result .= "</table>";

			$result .= "</div>";
			$n++;
		}// end while

		$result .= "</div><! ---- end tab-content ----->";

	} /// จบ เงื่อนไขนับคุณลักษณะ
	return $result;
}

// ******************************************  ใช้กับหลังบ้าน *********************************************//
public function order_report_attribute_grid($id_product){
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
			$active 	= $this->isActiveItem($rd['id_product_attribute']);
			$qty 		= $active === TRUE ? ( $this->available_order_qty($rd['id_product_attribute']) - $this->orderQty($rs['id_product_attribute']) ) : 0;
			$all_qty 	= $active === TRUE ? $this->stock_qty($rs['id_product_attribute']) : 0;
			if($qty < 1){ $stock = "<span style='color:red'>สินค้าหมด</span>"; }else{ $stock = "<span style='color:green'>".$qty."</span>"; }
			$result .="<td style='vertical-align:middle;'>".$rd['code']."</td>
						<td style='width:100px; padding-right:10px; vertical-align:middle;'>".$stock." <center><span style='color: blue; font-size:10px;'>(".$all_qty.")</span></td>";
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
			$co = dbQuery("SELECT tbl_color.id_color, color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
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
					$qc = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND tbl_product_attribute.id_color = ".$rc['id']." ORDER BY color_code ASC");
					if( dbNumRows($qc) == 1 )
					{
					list($id_product_attribute) = dbFetchArray($qc);
					$active	= $this->isActiveItem($id_product_attribute);
					$all_qty 	= $active === TRUE ? ($this->stock_qty($id_product_attribute) - $this->orderQty($id_product_attribute) ) : 0;
					$qty 		= $active === TRUE ? $this->available_order_qty($id_product_attribute) : 0;
					if($qty <1){ $stock = "<span style='color:red'>สินค้าหมด</span>"; }else{ $stock = "<span style='color:green'>".$qty."</span>"; }
					$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>".$stock." <center><span style='color: blue; font-size:10px;'>(".$all_qty.")</span></td>";
					}
					else
					{
						$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>ไม่มีสินค้า</td>";
					}
				}//end while
				$result .= "</tr>";
			}//end while
		}else if(isset($colors) && isset($attributes) ){
			$co = dbQuery("SELECT tbl_color.id_color, color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
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
					$qc = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND id_attribute =".$rd['id']." AND tbl_product_attribute.id_color = ".$rc['id']." ORDER BY color_code ASC");
					if( dbNumRows($qc) == 1 )
					{
						list($id_product_attribute) = dbFetchArray($qc);
						$active	= $this->isActiveItem($id_product_attribute);
						$all_qty 	= $active === TRUE ? ($this->stock_qty($id_product_attribute) - $this->orderQty($id_product_attribute) ) : 0;
						$qty 		= $active === TRUE ? $this->available_order_qty($id_product_attribute) : 0;
						if($qty <1){ $stock = "<span style='color:red'>สินค้าหมด</span>"; }else{ $stock = "<span style='color:green'>".$qty."</span>"; }
						$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>".$stock." <center><span style='color: blue; font-size:10px;'>(".$all_qty.")</span></td>";
					}
					else
					{
						$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>ไม่มีสินค้า</td>";
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
					$qc = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND tbl_product_attribute.id_attribute = ".$rc['id']." ORDER BY position ASC");
					if( dbNumRows($qc) == 1 )
					{
						list($id_product_attribute) = dbFetchArray($qc);
						$active	= $this->isActiveItem($id_product_attribute);
						$all_qty 	= $active === TRUE ? ($this->stock_qty($id_product_attribute) - $this->orderQty($id_product_attribute) ) : 0;
						$qty 		= $active === TRUE ? $this->available_order_qty($id_product_attribute) : 0;
						if($qty <1){ $stock = "<span style='color:red'>สินค้าหมด</span>"; }else{ $stock = "<span style='color:green'>".$qty."</span>"; }
						$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>".$stock."  <center><span style='color: blue; font-size:10px;'>(".$all_qty.")</span></td>";
					}
					else
					{
						$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>ไม่มีสินค้า</td>";
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
			$qr = dbQuery("SELECT tbl_color.id_color, color_code FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND tbl_product_attribute.id_color != 0 GROUP BY tbl_product_attribute.id_color ORDER BY color_code ASC" );
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
					$qc = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color WHERE id_product = ".$id_product." AND id_size =".$rd['id']." AND tbl_product_attribute.id_color = ".$rc['id']." ORDER BY color_code ASC");
					if( dbNumRows($qc) == 1 )
					{
						list($id_product_attribute) = dbFetchArray($qc);
						$active	= $this->isActiveItem($id_product_attribute);
						$all_qty 	= $active === TRUE ? ($this->stock_qty($id_product_attribute) - $this->orderQty($id_product_attribute) ) : 0;
						$qty 		= $active === TRUE ? $this->available_order_qty($id_product_attribute) : 0;
						if($qty <1){ $stock = "<span style='color:red'>สินค้าหมด</span>"; }else{ $stock = "<span style='color:green'>".$qty."</span>"; }
						$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>".$stock." <center><span style='color: blue; font-size:10px;'>(".$all_qty.")</span></td>";
					}
					else
					{
						$result .= "<td align='center' style='width:70px; vertical-align:middle; padding:5px;'>ไม่มีสินค้า</td>";
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

	return $result;
}
		public function get_id_product_attribute($where){
			list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE $where"));
			return $id_product_attribute;
		}

		public function get_id_product($id_product_attribute)
		{
			$id		= 0;
			$qs = dbQuery("SELECT id_product FROM tbl_product_attribute WHERE id_product_attribute = ".$id_product_attribute);
			if( dbNumRows($qs) == 1 )
			{
				list($id) = dbFetchArray($qs);
			}
			return $id;
		}


public function isVisual($id_pd)
{
    $sc = FALSE;
    $qs = dbQuery("SELECT is_visual FROM tbl_product WHERE id_product = ".$id_pd." AND is_visual = 1");
    if(dbNumRows($qs) == 1)
    {
        $sc = TRUE;
    }
    return $sc;
}

public function isActiveItem($id_pa)
{
	$sc = FALSE;
	if( $id_pa !== FALSE )
	{
		$qs = dbQuery("SELECT active FROM tbl_product_attribute WHERE id_product_attribute = ".$id_pa." AND active = 1");
		if( dbNumRows($qs) == 1 )
		{
			$sc = TRUE;
		}
	}
	return $sc;
}

}//จบ class
?>
