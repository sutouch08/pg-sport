<?php
require "../../library/config.php";
require"../../library/functions.php";
require "../function/tools.php";

//------------------------------------- NEW CODE -----------------------------------//

//--------------  ตรวจสอบรหัสสินค้าซ้ำก่อนเพิ่มหรือแก้ไข  --------------------//
if( isset( $_GET['validProductCode'] ) )
{
	$sc 		= 0;
	$id_pd	= $_POST['id_product'];
	$pCode	= $_POST['product_code'];
	if( $id_pd != '' )
	{
		$qs = dbQuery("SELECT id_product FROM tbl_product WHERE id_product != ".$id_pd." AND product_code = '".$pCode."'");	
	}
	else
	{
		$qs = dbQuery("SELECT id_product FROM tbl_product WHERE product_code = '".$pCode."'");
	}
	if( dbNumRows($qs) > 0 )
	{
		$sc = 1;
	}
	echo $sc;
}

if( isset($_GET['available_product_qty']) && isset( $_GET['id_product'] ))
{
	$product = new product();
	echo $product->available_product_qty($_GET['id_product']);	
}
///********************* Auto Complete ********************//
if(isset($_REQUEST['term'])){
	$sql = dbQuery("SELECT product_code FROM tbl_product WHERE product_code LIKE'%".$_REQUEST['term']."%' ORDER BY id_product ASC");
	$data = array();
	while($row = dbFetchArray($sql)){
		$data[] = $row['product_code'];
	}
	echo json_encode($data);
}
		
//************ เพิ่มรายการสินค้า **************************//
if(isset($_GET['add'])&&isset($_POST['product_code'])){
	$data = array( $_POST['product_code'], $_POST['product_name'], $_POST['cost'], $_POST['price'], $_POST['weight'], $_POST['width'], $_POST['length'], $_POST['height'], $_POST['discount_type'],
						$_POST['discount'], $_POST['default_category'], $_POST['active'], $_POST['description'], $_POST['category_id'] ); 	
	$product = new product();
	if($product->add_product($data)){
		$message = "เพิ่มสินค้าสำเร็จ";
		header("location: ../index.php?content=product&message=$message");
	}else{
		$message = "เพิ่มสินค้าไม่สำเร็จ";
		header("location: ../index.php?content=product&error=$message");
		}
}

//*********************** แก้ไขรายการสินค้า ***************************//
if(isset($_GET['edit'])&&isset($_POST['edit_product'])){
	$data = array( $_POST['id_product'], $_POST['product_code'], $_POST['product_name'], $_POST['cost'], $_POST['price'], $_POST['weight'], $_POST['width'], $_POST['length'], $_POST['height'], $_POST['discount_type'],
						$_POST['discount'], $_POST['default_category'], $_POST['active'], $_POST['description'], $_POST['category_id'] );
	$product = new product();
	if($product->edit_product($data)){
		$message = "แก้ไขข้อมูลสินค้าเรียบร้อยแล้ว";
		header("location: ../index.php?content=product&message=$message");
	}else{
		$message = "แก้ไขข้อมูลสินค้าไม่สำเร็จ";
		header("location: ../index.php?content=product&error=$message");
	}
}

//********************** แก้ไข combination ***********************//
if(isset($_GET['edit'])&&isset($_POST['id_product_attribute'])){
	$id_product = $_POST['id_product'];
	$product = new product();
	$data = array(	$_POST['id_product_attribute'], $_POST['reference'], $_POST['barcode'], $_POST['id_color'], $_POST['id_size'], $_POST['id_attribute'], $_POST['cost'], $_POST['price'], $_POST['weight'], $_POST['width'], 
						$_POST['length'], $_POST['height'],$_POST['id_image'], $_POST['barcode_pack'], $_POST['qty'] );
	if($product->edit_product_attribute($data)){
		header("location: ../index.php?content=product&edit=y&id_product=$id_product&tab=2");
	}else{
		$message = "ไม่สามารถแก้ไขรายการได้";
		header("location: ../index.php?content=product&edit=y&id_product=$id_product&tab=2&error=$message");
	}
}

//****************************** เพิ่ม combination *************************//
if(isset($_GET['add'])&&isset($_POST['id_product'])){
	$id_product = $_POST['id_product'];
	$product = new product();
	$data = array(	$id_product, $_POST['reference'], $_POST['barcode'], $_POST['id_color'], $_POST['id_size'], $_POST['id_attribute'], $_POST['cost'], $_POST['price'], $_POST['weight'], $_POST['width'], 
						$_POST['length'], $_POST['height'],$_POST['id_image'], $_POST['barcode_pack'], $_POST['qty'] );
	if($product->add_product_attribute($data)){
		header("location: ../index.php?content=product&edit=y&id_product=$id_product&tab=2");
	}else{
		$message = "เพิ่มรายการสินค้าไม่สำเร็จ";
		header("location: ../index.php?content=product&edit=y&id_product=$id_product&tab=2&error=$message");
	}
}

//********************** ลบรายการสินค้า *********************************//
if(isset($_GET['delete'])&&isset($_GET['id_product'])){
	$id_product = $_GET['id_product'];
	$product = new product();
	$result = $product->delete_product($id_product); /// ลบสินค้าทั้งรายการ ถ้าสำเร็จจะคืนค่าเป็น true หากไม่สำเร็จจะคืนค่าเป็นข้อความ
	if($result === true){
		$message = "ลบสินค้าเรียบร้อยแล้ว";
		header("location: ../index.php?content=product&message=$message");
	}else{
		header("location: ../index.php?content=product&error=$result");
	}
}

//****************************** ลบ combination *******************************//
if(isset($_GET['delete'])&&isset($_GET['id_product_attribute'])){
	$id_product_attribute = $_GET['id_product_attribute'];
	$product = new product();
	$id_product = $product->getProductId($id_product_attribute);
	$result = $product->deletd_product_attribute($id_product_attribute); /// ลบ SKU  ถ้าสำเร็จจะคืนค่าเป็น true หากไม่สำเร็จจะคืนค่าเป็นข้อความ
	$code = dbFetchArray(dbQuery("SELECT id_product, barcode FROM tbl_product_attribute WHERE id_product_attribute = $id_product_attribute"));
	if($result === true){
		header("location: ../index.php?content=product&edit=y&id_product=$id_product&tab=2");
	}else{
		header("location: ../index.php?content=product&edit=y&id_product=$id_product&tab=2&error=$result");
	}
}

//*************** ตรวจสอบรหัสสินค้าซ้ำก่อนเพิ่มข้อมูล ***********************//	
if(isset($_GET['product_code'])){
	$product_code = $_GET['product_code'];
	$result = dbQuery("select product_code from tbl_product where product_code = '$product_code'");
	$row = dbNumRows($result);
	if($row >0){
		$message ="1";
		echo $message;
	}else{
		$message ="0";
		echo $message;
	}
}

///********************** ตรวจสอบรหัสอ้างอิงซ้ำก่อนเพิ่มหรือแก้ไข *******************************///
if(isset($_GET['reference'])){
	$reference = $_GET['reference'];
	$row = dbNumRows(dbQuery("select reference from tbl_product_attribute where reference = '$reference'"));
	if($row >0){
		$message ="1";
	}else{
		$message ="0";
	}
	echo $message;
}

///********************** ตรวจสอบบาร์โค้ดซ้ำก่อนเพิ่มหรือแก้ไข *******************************///
if(isset($_GET['barcode'])){
	$barcode = $_GET['barcode'];
	$row = dbNumRows(dbQuery("select barcode from tbl_product_attribute LEFT JOIN tbl_product_pack ON tbl_product_attribute.id_product_attribute = tbl_product_pack.id_product_attribute where barcode = '$barcode' OR barcode_pack = $barcode"));
	if($row >0){
		$message ="1";
	}else{
		$message ="0";
	}
	echo $message;
}
///********************************* Active / disactive สินค้า ********************************************//
if(isset($_GET['active'])&&isset($_GET['id_product'])){
	$id_product = $_GET['id_product'];
	$active = $_GET['active'];
	if($active==1){
		dbQuery("UPDATE tbl_product SET active = 0 WHERE id_product= $id_product");
	}else if($active==0){
		dbQuery("UPDATE tbl_product SET active = 1 WHERE id_product= $id_product");
	}
	header("location: ../index.php?content=product");
}

//**************************  อัพโหลด รูปภาพหลายภาพพร้อมกัน  *****************************//
if(isset($_GET['img_upload'])&&isset($_POST['id_product'])){
	$id_product = $_POST['id_product'];
	require "../../library/class/class.upload.php";
	$files = array();
	foreach ($_FILES['image'] as $k => $l) {
		 foreach ($l as $i => $v) {
			 if (!array_key_exists($i, $files))
				   $files[$i] = array();
				   $files[$i][$k] = $v;
			 }
		}   
	foreach($files as $file){
	list($sql) =dbFetchArray(dbQuery("SELECT max(id_image) FROM tbl_image"));
	$id_image = $sql+1;
	$img_name = $id_image;
	$count = strlen($id_image);
	$path = str_split($id_image);
	$image_path = "../../img/product";
	$n=0;
			while($n<$count){
				$image_path .= "/".$path[$n];
				$n++;
			}
	$image_path .= "/";
	$image =new upload($file);
	$use_size = 4;
	$i=1;
	if($image->uploaded){
		while($i<=$use_size){
			switch($i){
				case "1" :
					$pre_fix = "product_mini_";
					$img_size = 60;
					break;
				case "2" :
					$pre_fix = "product_default_";
					$img_size = 125;
					break;
				case "3" :
					$pre_fix = "product_medium_";
					$img_size = 250;
					break;
				case "4" :
					$pre_fix = "product_lage_";
					$img_size = 1500;
					break;
				default :
					$pre_fix = "";
					$img_size = 300;
					break;
			}
		$image->file_new_name_body = $pre_fix.$img_name;
		$image->image_resize = true;
		$image->image_ratio_fill = true;
		$image->file_overwrite = true;
		$image->auto_create_dir = true;
		$image->image_x = $img_size;
		$image->image_y = $img_size;
		$image->image_background_color = "#FFFFFF";
		$image->image_convert = "jpg";
		$image->process($image_path);
		if($image->processed){
					
		}else{
			echo 'error : ' . $image->error;
		}
		$i++;
		}
	}
	$image->clean();
	$cove_checked=dbNumRows(dbQuery("SELECT * FROM tbl_image WHERE id_product = $id_product AND cover = 1"));
	$qr = dbFetchArray(dbQuery("SELECT max(position) as top FROM tbl_image WHERE id_product = $id_product"));
	$top = $qr['top']+1;
	$have_img = dbNumRows(dbQuery("SELECT id_image FROM tbl_image WHERE id_product = $id_product"));
	if($have_img ==0){
		if($cove_checked == 0){
			dbQuery("INSERT INTO tbl_image (id_image, id_product, position, cover) VALUES ($id_image, $id_product, 1, 1 )");
		}else{
			dbQuery("INSERT INTO tbl_image (id_image, id_product, position, cover) VALUES ($id_image, $id_product, 1, 0 )");
		}
	}else{
		if($cove_checked == 0){
			dbQuery("INSERT INTO tbl_image (id_image, id_product, position, cover) VALUES ($id_image, $id_product, $top, 1 )");
		}else{
			dbQuery("INSERT INTO tbl_image (id_image, id_product, position, cover) VALUES ($id_image, $id_product, $top, 0 )");
		}
	}
	}
	header("location: ../index.php?content=product&edit=y&id_product=$id_product&tab=3");
}
//***********************************  ลบรูปภาพ ****************************************//
if(isset($_GET['delete_image'])&&isset($_GET['id_image'])){
	$id_image = $_GET['id_image'];
	$image_path = array( getImagePath($id_image,1), getImagePath($id_image,2), getImagePath($id_image,3) ,getImagePath($id_image,4 ));
	list($id_product) =dbFetchArray(dbQuery("SELECT id_product FROM tbl_image WHERE id_image= $id_image"));
	$is_cover = dbNumRows(dbQuery("SELECT * FROM tbl_image WHERE id_image = $id_image AND cover = 1"));
	if($is_cover==1){
		dbQuery("DELETE FROM tbl_image WHERE id_image = $id_image");
		dbQuery("DELETE FROM tbl_product_attribute_image WHERE id_image = $id_image");
		list($top) = dbFetchArray(dbQuery("SELECT max(position) as top FROM tbl_image WHERE id_product = $id_product"));
		dbQuery("UPDATE tbl_image SET cover = 1 WHERE id_product = $id_product AND position = $top");
		foreach( $image_path as $img_path){
			unlink($img_path);
		}
	}else{
		dbQuery("DELETE FROM tbl_image WHERE id_image = $id_image");
		dbQuery("DELETE FROM tbl_product_attribute_image WHERE id_image = $id_image");
		foreach( $image_path as $img_path){
			unlink($img_path);	
		}
	}
	header("location: ../index.php?content=product&edit=y&id_product=$id_product&tab=3");
}
//****************************************** เปลี่ยนภาพหน้าปก ****************************************//
if(isset($_GET['cover'])&&isset($_GET['id_image'])){
	$id_image = $_GET['id_image'];
	list($id_product) = dbFetchArray(dbQuery("SELECT id_product FROM tbl_image WHERE id_image = $id_image"));
	dbQuery("UPDATE tbl_image SET cover = 0 WHERE id_product = $id_product AND cover = 1");
	dbQuery("UPDATE tbl_image SET cover = 1 WHERE id_image = $id_image");
	header("location: ../index.php?content=product&edit=y&id_product=$id_product&tab=3");
}
///************************ attribute_gen **************************//
if(isset($_GET['attribute_gen'])&&isset($_GET['id_product'])){
	$id_product = $_GET['id_product'];
	if(isset($_POST['color'])){ $color = $_POST['color']; }
	if(isset($_POST['size'])){ $size = $_POST['size']; }
	if(isset($_POST['attribute'])){ $attr = $_POST['attribute']; $attribute = $_POST['attribute'];}
	if(isset($_POST['image'])){ $image = $_POST['image']; }
	if(isset($_POST['set_color'])){ $set_color = $_POST['set_color'];}else{ $set_color = "0";}
	if(isset($_POST['set_size'])){ $set_size = $_POST['set_size'];}else{ $set_size = "0";}
	if(isset($_POST['set_attribute'])){ $set_attribute = $_POST['set_attribute'];}else{ $set_attribute = "0";}
	if(isset($_POST['matching'])){ $matching = $_POST['matching'];}
	$product = new product();
	$product->product_detail($id_product);
	$product_cost = $product->product_cost;
	$product_price = $product->product_price;
	$weight = $product->weight;
	$width = $product->width;
	$length = $product->length;
	$height = $product->height;
	$split = "-";
	$barcode = "";
	if($set_color == "1" && $set_size == "2" && $set_attribute == "3"){
		$t_set1 = 'tbl_color'; $f_set1 = 'color_code'; $set1 = "color"; $t_set2 = 'tbl_size'; $f_set2 = 'size_name'; $set2 = "size"; $t_set3 = 'tbl_attribute'; $f_set3 = 'attribute_name'; $set3 = "attribute";
	}else if($set_color == "2" && $set_size == "1" && $set_attribute =="3"){
		$t_set1 ='tbl_size'; $f_set1 = 'size_name'; $set1 = "size"; $t_set2 = 'tbl_color'; $f_set2 = 'color_code'; $set2 = "color"; $t_set3 = 'tbl_attribute'; $f_set3 = 'attribute_name'; $set3 = "attribute";
	}else if($set_color == "3" && $set_size == "2" && $set_attribute == "1"){
		$t_set1 = 'tbl_attribute'; $f_set1 = 'attribute_name'; $set1 = "attribute"; $t_set2 = 'tbl_size'; $f_set2 = 'size_name'; $set2 = "size"; $t_set3 = 'tbl_color'; $f_set3 = 'color_code'; $set3 = "color";
	}else if($set_color == "3" && $set_size == "1" && $set_attribute == "2"){
		$t_set1 = 'tbl_size'; $f_set1 = 'size_name'; $set1 = "size"; $t_set2 = 'tbl_attribute'; $f_set2 = 'attribute_name'; $set2 = "attribute"; $t_set3 = 'tbl_color'; $f_set3 = 'color_code'; $set3 = "color";
	}else if($set_color == "2" && $set_size == "3" && $set_attribute == "1"){
		$t_set1 = 'tbl_attribute'; $f_set1 = 'attribute_name'; $set1 = "attribute"; $t_set2 = 'tbl_color'; $f_set2 = 'color_code'; $set2 = "color"; $t_set3 = 'tbl_size'; $f_set3 = 'size_name'; $set3 = "size";
	}else if($set_color == "1" && $set_size == "3" && $set_attribute == "2"){
		$t_set1 = 'tbl_color'; $f_set1 = 'color_code'; $set1 = "color"; $t_set2 = 'tbl_attribute'; $f_set2 = 'attribute_name'; $set2 = "attribute"; $t_set3 = 'tbl_size'; $f_set3 = 'size_name'; $set3 = "size";
	//
	}else if($set_color == "1" && $set_size == "2" && $set_attribute == "0"){
		$t_set1 = 'tbl_color'; $f_set1 = 'color_code'; $set1 = "color"; $t_set2 = 'tbl_size'; $f_set2 = 'size_name'; $set2 = "size";
	}else if($set_color == "2" && $set_size == "1" && $set_attribute == "0"){
		$t_set1 ='tbl_size'; $f_set1 = 'size_name'; $set1 = "size"; $t_set2 = 'tbl_color'; $f_set2 = 'color_code'; $set2 = "color";
	}else if($set_color == "0" && $set_size =="2" && $set_attribute == "1"){
		$t_set1 = 'tbl_attribute'; $f_set1 = 'attribute_name'; $set1 = "attribute"; $t_set2 = 'tbl_size'; $f_set2 = 'size_name'; $set2 = "size";
	}else if($set_color == "0" && $set_size == "1" && $set_attribute == "2"){
		$t_set1 = 'tbl_size'; $f_set1 = 'size_name'; $set1 = "size"; $t_set2 = 'tbl_attribute'; $f_set2 = 'attribute_name'; $set2 = "attribute";
	}else if($set_color == "2" && $set_size == "0" && $set_attribute == "1"){
		$t_set1 = 'tbl_attribute'; $f_set1 = 'attribute_name'; $set1 = "attribute"; $t_set2 = 'tbl_color'; $f_set2 = 'color_code'; $set2 = "color";
	}else if($set_color == "1" && $set_size == "0" && $set_attribute == "2"){
		$t_set1 = 'tbl_color'; $f_set1 = 'color_code'; $set1 = "color"; $t_set2 = 'tbl_attribute'; $f_set2 = 'attribute_name'; $set2 = "attribute";
	}
	if(isset($color)&&isset($size)&&isset($attr)){ 
		foreach($$set1 as $id_set1){
			list($set1_name) = dbFetchArray(dbQuery("SELECT $f_set1 FROM $t_set1 WHERE id_$set1=$id_set1"));
				foreach($$set2 as $id_set2){
					list($set2_name) = dbFetchArray(dbQuery("SELECT $f_set2 FROM $t_set2 WHERE id_$set2 = $id_set2"));
						foreach($$set3 as $id_set3){
							$reference = $product->product_code;
							list($set3_name) = dbFetchArray(dbQuery("SELECT $f_set3 FROM $t_set3 WHERE id_$set3 = $id_set3"));
							$reference .= $split.$set1_name.$split.$set2_name.$split.$set3_name;
				dbQuery("INSERT INTO tbl_product_attribute (id_product, reference, barcode, id_$set1, id_$set2, id_$set3, cost, price, weight, width, length, height, date_upd) 
							VALUES ($id_product, '$reference', '$barcode', $id_set1, $id_set2, $id_set3, $product_cost, $product_price, $weight, $width, $length, $height, NOW())");
						}//foreach attr
				}//foreach size
		}//foreach color
	}else if(isset($color)&&isset($size)){ 
		foreach($$set1 as $id_set1){
			echo "SELECT $f_set1 FROM $t_set1 WHERE id_$set1=$id_set1";
			list($set1_name) = dbFetchArray(dbQuery("SELECT $f_set1 FROM $t_set1 WHERE id_$set1=$id_set1"));
				foreach($$set2 as $id_set2){
					list($set2_name) = dbFetchArray(dbQuery("SELECT $f_set2 FROM $t_set2 WHERE id_$set2 = $id_set2"));
					$reference = $product->product_code;
					$reference .= $split.$set1_name.$split.$set2_name;
					$id_attribute = 0;
					//echo "$reference<br>";
					dbQuery("INSERT INTO tbl_product_attribute (id_product, reference, barcode, id_$set1, id_$set2, id_attribute, cost, price, weight, width, length, height, date_upd) 
								VALUES ($id_product, '$reference', '$barcode', $id_set1, $id_set2, $id_attribute, $product_cost, $product_price, $weight, $width, $length, $height, NOW())");
				}//foreach size
		}//foreach color
	}else if(isset($color)&&isset($attr)){
		$id_size = 0;
		foreach($$set1 as $id_set1){
			list($set1_name) = dbFetchArray(dbQuery("SELECT $f_set1 FROM $t_set1 WHERE id_$set1=$id_set1"));
				foreach($$set2 as $id_set2){
					list($set2_name) = dbFetchArray(dbQuery("SELECT $f_set2 FROM $t_set2 WHERE id_$set2 = $id_set2"));
					$reference = $product->product_code;
					$reference .= $split.$set1_name.$split.$set2_name;
					//echo "$reference<br>";
					dbQuery("INSERT INTO tbl_product_attribute (id_product, reference, barcode, id_$set1, id_size, id_$set2, cost, price, weight, width, length, height, date_upd) 
								 VALUES ($id_product, '$reference', '$barcode', $id_set1,$id_size , $id_set2, $product_cost, $product_price, $weight, $width, $length, $height, NOW())");
						}//foreach attr
				}//foreach color
	}else if(isset($size)&&isset($attr)){
		$id_color = 0;
		foreach($$set1 as $id_set1){
			list($set1_name) = dbFetchArray(dbQuery("SELECT $f_set1 FROM $t_set1 WHERE id_$set1=$id_set1"));
				foreach($$set2 as $id_set2){
					list($set2_name) = dbFetchArray(dbQuery("SELECT $f_set2 FROM $t_set2 WHERE id_$set2 = $id_set2"));
					$reference = $product->product_code;
					$reference .= $split.$set1_name.$split.$set2_name;
					//echo "$reference<br>";
					dbQuery("INSERT INTO tbl_product_attribute (id_product, reference, barcode, id_color, id_$set1, id_$set2, cost, price, weight, width, length, height, date_upd) 
								 VALUES ($id_product, '$reference', '$barcode', $id_color ,$id_set1, $id_set2, $product_cost, $product_price, $weight, $width, $length, $height, NOW())");
						}//foreach attr
				}//foreach size
	}else if(isset($color)){ 
		foreach($color as $id_color){
			$reference = $product->product_code;
			list($color_code) = dbFetchArray(dbQuery("SELECT color_code FROM tbl_color WHERE id_color=$id_color"));
					$reference .= $split.$color_code;
					$id_attribute = 0;
					$id_size = 0;
					//echo "$reference<br>";
					dbQuery("INSERT INTO tbl_product_attribute (id_product, reference, barcode, id_color, id_size, id_attribute, cost, price, weight, width, length, height, date_upd) 
								 VALUES ($id_product, '$reference', '$barcode', $id_color, $id_size, $id_attribute, $product_cost, $product_price, $weight, $width, $length, $height, NOW())");
		}//foreach color
	}else if(isset($size)){
		$id_color = 0;
		$id_attribute = 0;
		foreach($size as $id_size){
			$reference = $product->product_code;
				list($size_name) = dbFetchArray(dbQuery("SELECT size_name FROM tbl_size WHERE id_size = $id_size"));
				$reference .= $split.$size_name;
				//echo "$reference<br>";
				dbQuery("INSERT INTO tbl_product_attribute (id_product, reference, barcode, id_color, id_size, id_attribute, cost, price, weight, width, length, height, date_upd) 
							VALUES ($id_product, '$reference', '$barcode', $id_color, $id_size, $id_attribute, $product_cost, $product_price, $weight, $width, $length, $height, NOW())");
				}//foreach size
	}else if(isset($attr)){
		$id_color = 0;
		$id_size = 0;
		foreach($attr as $id_attribute){
			$reference = $product->product_code;
				list($attr_name) = dbFetchArray(dbQuery("SELECT attribute_name FROM tbl_attribute WHERE id_attribute = $id_attribute"));
				$reference .= $split.$attr_name;
				//echo "$reference<br>";
				dbQuery("INSERT INTO tbl_product_attribute (id_product, reference, barcode, id_color, id_size, id_attribute, cost, price, weight, width, length, height, date_upd) 
							VALUES ($id_product, '$reference', '$barcode', $id_color, $id_size, $id_attribute, $product_cost, $product_price, $weight, $width, $length, $height, NOW())");
		}//foreach attr
	}else{
		$reference = $product->product_code;
		$id_color = 0;
		$id_size = 0;
		$id_attribute = 0;
		dbQuery("INSERT INTO tbl_product_attribute (id_product, reference, barcode, id_color, id_size, id_attribute, cost, price, weight, width, length, height, date_upd) 
					VALUES ($id_product, '$reference', '$barcode', $id_color, $id_size, $id_attribute, $product_cost, $product_price, $weight, $width, $length, $height, NOW())");
	}
	//matching รูป
	if(isset($image)){
		foreach($image as $img){
			list($id_image, $id_matching) = explode(":", $img);
			$sql = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = $id_product AND id_$matching = $id_matching");
			while($rs = dbFetchArray($sql)){
				$id_product_attribute = $rs['id_product_attribute'];
				dbQuery("INSERT INTO tbl_product_attribute_image(id_product_attribute, id_image) VALUES ( $id_product_attribute, $id_image)");
			}
		}
	}
	header("location: ../index.php?content=product&edit=y&id_product=$id_product&tab=2"); 
			
	}
	
	
	///// อัพเดตบาร์โค้ด////
	if(isset($_GET['check'])&&isset($_GET['code'])){
	$barcode = $_GET['code'];
	$id_product_attribute = $_GET['id_product_attribute'];
	$row = dbNumRows(dbQuery("SELECT barcode FROM tbl_product_attribute LEFT JOIN tbl_product_pack ON tbl_product_attribute.id_product_attribute = tbl_product_pack.id_product_attribute 
								WHERE (barcode = '$barcode' OR barcode_pack = '$barcode') AND tbl_product_attribute.id_product_attribute != $id_product_attribute"));
	if($row >0){
		$message ="1";
		}else{
			if(dbQuery("UPDATE tbl_product_attribute SET barcode = '$barcode' WHERE id_product_attribute = $id_product_attribute")){
				$message = "0";
			}else{
				$message = "2";	
			}
	}
	echo $message;
}


if(isset($_GET['check_pack'])&&isset($_GET['code'])){
	$barcode = $_GET['code'];
	$id_product_attribute = $_GET['id_product_attribute'];
	$result = dbQuery("select barcode from tbl_product_attribute LEFT JOIN tbl_product_pack ON tbl_product_attribute.id_product_attribute = tbl_product_pack.id_product_attribute where barcode = '$barcode' OR barcode_pack = $barcode");
	$row = dbNumRows($result);
	if($row >0){
		$message ="1";
		echo $message;
	}else{
		list($id_product_pack) = dbFetchArray(dbQuery("SELECT id_product_pack FROM tbl_product_pack WHERE id_product_attribute = $id_product_attribute"));
		if($id_product_pack != ""){
			if(dbQuery("UPDATE tbl_product_pack SET barcode_pack = '$barcode' WHERE id_product_attribute = $id_product_attribute")){
				$message = "0";
				echo $message;
			}else{
				$message = "2";
				echo $message;
			}
		}else{
			if(dbQuery("INSERT INTO tbl_product_pack (id_product_attribute,qty,barcode_pack) VALUES ($id_product_attribute,'',$barcode)")){
				$message = "0";
				echo $message;
			}else{
				$message = "2";
				echo $message;
			}
		}
	}
	
}
if(isset($_GET['check_qty'])&&isset($_GET['qty'])){
	$qty = $_GET['qty'];
	$id_product_attribute = $_GET['id_product_attribute'];
		list($id_product_pack) = dbFetchArray(dbQuery("SELECT id_product_pack FROM tbl_product_pack WHERE id_product_attribute = $id_product_attribute"));
		if($id_product_pack != ""){
			if(dbQuery("UPDATE tbl_product_pack SET qty = '$qty' WHERE id_product_attribute = $id_product_attribute")){
				$message = "0";
				echo $message;
			}else{
				$message = "2";
				echo $message;
			}
		}else{
			if(dbQuery("INSERT INTO tbl_product_pack (id_product_attribute,qty,barcode_pack) VALUES ($id_product_attribute,'$qty','')")){
				$message = "0";
				echo $message;
			}else{
				$message = "2";
				echo $message;
			}
		}
}
if(isset($_GET['text'])){
	$id_tab = 1;
	$id_profile = $_COOKIE['profile_id'];
    list($view, $add, $edit, $delete)=dbFetchArray(checkAccess($id_profile, $id_tab));
	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	$text = $_GET['text'];
//	$html = $header;
	/////////////////////////////////
	$paginator = new paginator();
	if(isset($_GET['get_rows'])){$get_rows = $_GET['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
	$query = "WHERE product_code LIKE '%$text%' OR product_name LIKE '%$text%'";
	$paginator->Per_Page("tbl_product",$query,$get_rows);
	$Page_Start = $paginator->Page_Start;
	$Per_Page = $paginator->Per_Page; 
	$sql = dbQuery("SELECT id_product FROM tbl_product  WHERE product_code LIKE '%$text%' OR product_name LIKE '%$text%' LIMIT $Page_Start , $Per_Page");
	//$sql = dbQuery("SELECT * FROM product_table WHERE product_code LIKE '%$text%' OR product_name LIKE '%$text%' OR category_name LIKE '%$text%' LIMIT $Page_Start , $Per_Page");
	//////////////////////////////////////
	$header ="
<table class='table table-striped'>
<thead>
	<th width='5%' style='text-align:center;'>ID</th><th width='10%' style='text-align:center;' >รูปภาพ</th><th width='15%'>รหัสสินค้า</th><th width='25%'>ชื่อสินค้า</th><th width='15%'>หมวดหมู่</th>
      <th width='8%' style='text-align:center;'>ราคาทุน</th><th width='8%' style='text-align:center;'>ราคาขาย</th>
      <th width='5%' style='text-align:center;'>สถานะ</th><th colspan='2'style='text-align:center;'>การกระทำ</th>
</thead>";
	$html = $paginator->display($get_rows,"index.php?content=product&searchtext=$query&text=$text");
	$html .= $header;
	$row = dbNumRows($sql);
	$i=0;
	while($i<$row){
		list($id_product) = dbFetchArray($sql);
		$product = new product();
		$product->product_detail($id_product);
		$product_code = $product->product_code;
		$product_name = $product->product_name;
		$category_name = get_category_name($product->default_category_id);
		$product_cost = $product->product_cost;
		$product_price = $product->product_price;
		$active = $product->active;
		$html .= "
		<tr>
			<td align='center'>$id_product</td>
			<td style='text-align:center; cursor:pointer;' onclick=\"document.location = 'index.php?content=product&edit=y&id_product=$id_product&tab=1'\">".getCoverImage($id_product,1)."
			<td align='left' style='cursor:pointer;' onclick=\"document.location = 'index.php?content=product&edit=y&id_product=$id_product&tab=1'\">$product_code</td>
			<td align='left' style='cursor:pointer;' onclick=\"document.location = 'index.php?content=product&edit=y&id_product=$id_product&tab=1'\">$product_name</td>
			<td align='left' style='cursor:pointer;' onclick=\"document.location = 'index.php?content=product&edit=y&id_product=$id_product&tab=1'\">$category_name</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location = 'index.php?content=product&edit=y&id_product=$id_product&tab=1'\">". number_format($product_cost,2)."</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location = 'index.php?content=product&edit=y&id_product=$id_product&tab=1'\">". number_format($product_price,2)."</td>
			<td align='center'>"; if($active ==1){ $html .= "<a href='controller/productController.php?active=$active&id_product=$id_product'><span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span></a>";}else{ $html .= "<a href='controller/productController.php?active=$active&id_product=$id_product'><span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span></a>";} $html .="</td>
			<td align='center'>
				<a href='index.php?content=product&edit=y&id_product=$id_product&tab=1' $can_edit><button class='btn btn-warning btn-sx'><span class='glyphicon glyphicon-pencil' style='color: #fff;'></span></button></a>
			</td>
			<td align='center'><a href='controller/productController.php?delete=y&id_product=$id_product' $can_delete><button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $product_code : $product_name ? ');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a>
			</td>
		</tr>";
		$i++;
	}			
$html .="</table>";
$html .= $paginator->display_pages();
$html .="</div></div>"; $html .= "<br><br>";
	echo $html;
}

?>