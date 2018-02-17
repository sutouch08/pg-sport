<?php
require "../../library/config.php";
require"../../library/functions.php";
require "../function/tools.php";
require "../function/product_helper.php";

//------------------------------------- NEW CODE -----------------------------------//
//----- Product DB Report-----//
if( isset( $_GET['getProductDB'] ) && isset( $_GET['report'] ) )
{
	$sc = 'nodata';
	$priceQuery 	= $_GET['showPrice'] == 1 ? ", price" : "";
	$costQuery		= $_GET['showCost'] == 1 ? ", cost" : "";
	$id_category	= $_GET['id_category'];
	$isAll				=  $id_category == 0 ? TRUE : FALSE;
	
	if( $isAll )
	{
		$qr =	"SELECT barcode, reference, product_name ".$costQuery . $priceQuery." FROM tbl_product_attribute ";
		$qr .= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product";
	}
	else
	{
		$pdIn = productInCategory($id_category);
		if( $pdIn === FALSE )
		{
			$qr = "SELECT * FROM tbl_product_attribute WHERE id_product_attribute = 0"; 
			//---- เพื่อให้ได้ Numrows = 0
		}
		else
		{
			$qr = "SELECT barcode, reference, product_name ". $costQuery . $priceQuery . " FROM tbl_product_attribute ";
			$qr .= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product WHERE tbl_product_attribute.id_product IN(".$pdIn.")";	
		}
	}
	
	//echo $qr;
	$qs = dbQuery($qr);
	if( dbNumRows( $qs ) > 0 )
	{
		$no = 1;
		$ds = array();
		while( $rs = dbFetchObject($qs) )
		{
			$arr = array(
						'no'		=> $no,
						'barcode'	=> $rs->barcode,
						'pCode'		=> $rs->reference,
						'pName'		=> $rs->product_name,
						'cost'			=> $_GET['showCost'] == 1 ? number_format($rs->cost, 2) : '-',
						'price'			=> $_GET['showPrice'] == 1 ? number_format($rs->price, 2) : '-'
						);
			array_push($ds, $arr);						
			$no++;	
		}
		$sc = json_encode($ds);
	}
	echo $sc;
}

//----- Product DB Export-----//
if( isset( $_GET['getProductDB'] ) && isset( $_GET['export'] ) )
{
	$priceQuery 	= $_GET['showPrice'] == 1 ? ", price" : "";
	$costQuery		= $_GET['showCost'] == 1 ? ", cost" : "";
	$showCost		= $_GET['showCost'] == 1 ? TRUE : FALSE;
	$showPrice		= $_GET['showPrice'] == 1 ? TRUE : FALSE;
	$id_category	= $_GET['id_category'];
	$isAll				=  $id_category == 0 ? TRUE : FALSE;
	
	if( $isAll )
	{
		$qr =	"SELECT barcode, reference, product_name ".$costQuery . $priceQuery." FROM tbl_product_attribute ";
		$qr .= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product";
	}
	else
	{
		$pdIn = productInCategory($id_category);
		if( $pdIn === FALSE )
		{
			$qr = "SELECT * FROM tbl_product_attribute WHERE id_product_attribute = 0"; 
			//---- เพื่อให้ได้ Numrows = 0
		}
		else
		{
			$qr = "SELECT barcode, reference, product_name ". $costQuery . $priceQuery . " FROM tbl_product_attribute ";
			$qr .= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product WHERE tbl_product_attribute.id_product IN(".$pdIn.")";	
		}
	}
	
	$excel	= new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('ฐานข้อมูลสินค้า');
	
	$excel->getActiveSheet()->setCellValue('A1', 'ลำดับ');
	$excel->getActiveSheet()->setCellValue('B1', 'บาร์โค้ด');
	$excel->getActiveSheet()->setCellValue('C1', 'รหัสสินค้า');
	$excel->getActiveSheet()->setCellValue('D1', 'ชื่อสินค้า');
	$excel->getActiveSheet()->setCellValue('E1', 'ราคาทุน');
	$excel->getActiveSheet()->setCellValue('F1', 'ราคาขาย');

	$qs = dbQuery($qr);
	if( dbNumRows( $qs ) > 0 )
	{
		$no = 1;
		$row = 2; //-- start at row 2
		while( $rs = dbFetchObject($qs) )
		{
			$cost = $showCost === TRUE ? $rs->cost : 0;
			$price = $showPrice == TRUE ? $rs->price : 0;
			$excel->getActiveSheet()->setCellValue('A'.$row, $no);
			$excel->getActiveSheet()->setCellValue('B'.$row, $rs->barcode);
			$excel->getActiveSheet()->setCellValue('C'.$row, $rs->reference);
			$excel->getActiveSheet()->setCellValue('D'.$row, $rs->product_name);
			$excel->getActiveSheet()->setCellValue('E'.$row, $cost);
			$excel->getActiveSheet()->setCellValue('F'.$row, $price);				
			$no++;	
			$row++;
		}
		$excel->getActiveSheet()->getStyle('B2:B'.$row)->getNumberFormat()->setFormatCode('0');
		$excel->getActiveSheet()->getStyle('E2:F'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
	}
	
	setcookie("file_download_token", $_GET['token'], time() +3600,"/"); //-- setToken($_GET['token']);	
	$file_name = "ฐานข้อมูลสินค้า-".getConfig('COMPANY_NAME').".xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');
}



//----------- check Default Group  -----------//
if( isset( $_GET['checkDefaultGroup'] ) )
{
	$id = $_POST['id'];
	$qs = dbQuery("SELECT isDefault FROM tbl_product_group WHERE id = ".$id." AND isDefault = 1");
	$sc = dbNumRows($qs);
	echo $sc;
}



//------------- Remove Product Group  --------------//
if( isset( $_GET['removeProductGroup'] ) )
{
	$sc = 'fail';
	$id = $_POST['id'];
	$df = getDefaultProductGroup(); //----- id group of default product group
	$qs = dbQuery("DELETE FROM tbl_product_group WHERE id = ".$id);
	if( $qs )
	{
		$qr = dbQuery("UPDATE tbl_product SET id_product_group = ".$df." WHERE id_product_group = ".$id);
		if( $qr ){ $sc = 'success'; }
	}
	echo $sc;
}


//-----------  Get Product group detail  -----------//
if( isset( $_GET['getProductGroupDetail'] ) )
{
	$sc 	= 'fail';
	$id		= $_POST['id'];
	$qs = dbQuery("SELECT * FROM tbl_product_group WHERE id = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$sc = $rs['id'].' | '.$rs['name'];	
	}
	echo $sc;
}




//------------  Set Default product group  ----//
if( isset( $_GET['setDefaultGroup'] ) )
{
	$sc 	= 'fail';
	$id 	= $_POST['id'];
	$qs 	= dbQuery("UPDATE tbl_product_group SET isDefault = 0 WHERE isDefault = 1");
	if( $qs )
	{
		$qr = dbQuery("UPDATE tbl_product_group SET isDefault = 1 WHERE id = ".$id);
		if( $qr ){ $sc = 'success'; }
	}
	echo $sc;
}



//-------------  Add product group  ------------//
if( isset( $_GET['addProductGroup'] ) )
{
	$sc 		= 'fail';
	$id 		= isset( $_POST['id'] ) ? $_POST['id'] : '';
	$name	= $_POST['name'];	
	if( $id != '' )
	{
		if( isProductGroupExists($name, $id) === TRUE )
		{
			$sc = 1;	
		}
		else
		{
			$qs = dbQuery("UPDATE tbl_product_group SET name = '".$name."' WHERE id = ".$id);
			if( $qs ){ $sc = 'success'; }
		}
	}
	else
	{
		if( isProductGroupExists($name) === TRUE )
		{
			$sc = 1;	
		}
		else
		{
			$qs = dbQuery("INSERT INTO tbl_product_group ( Name ) VALUES ('".$name."')");
			if( 	$qs )
			{
				$qr = dbQuery("SELECT * FROM tbl_product_group WHERE name = '".$name."'");
				$rs = dbFetchArray($qr);
				$ds = array("id" => $rs['id'], "name" => $rs['name'], "onGroup" => productInGroup($rs['id']));
				$sc = json_encode($ds);
			}
			else
			{
				$sc = 'fail';
			}
		}
	}
	echo $sc;
}


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

//------------------------  เพิ่มสินค้าใหม่ ----------------//
if( isset( $_GET['addNewProduct'] ) )
{
	$sc = 'fail';
	$ds = array(
					'product_code'	=> trim($_POST['pCode']),
					'product_name'	=> trim($_POST['pName']),
					'product_cost'	=> $_POST['cost'],
					'product_price'	=> $_POST['price'],
					'weight'			=> $_POST['weight'],
					'length'			=> $_POST['length'],
					'height'			=> $_POST['height'],
					'discount_type'	=> $_POST['discount_type'],
					'discount'			=> $_POST['discount'],
					'default_category_id'	=> $_POST['dCategory'],
					'active'			=> $_POST['active'],
					'date_add'		=> date('Y-m-d H:i:s'),
					'id_product_group'		=> $_POST['pGroup'],
					'show_in_shop'	=> $_POST['inShop'],
					'is_visual'		=> $_POST['isVisual']					
				);	
	$cat 		= $_POST['category_id'];
	$desc	= $_POST['description'];				
	$product	= new product();
	$rs 		= $product->addProduct($ds); //----- ถ้าสำเร็จ ได้ id_product กลับมา
	if( $rs !== FALSE )
	{
		$sc = $rs;
		$product->setProductCategory($rs, $cat);
		$product->setProductDescription($rs, $desc);
	}
	echo $sc;				
}


if( isset( $_GET['updateProduct'] ) )
{
	$sc 		= 'fail';
	$id_pd	= $_POST['id_product'];
	if( $id_pd != '' )
	{
		$ds = array(
					'product_code'	=> trim($_POST['pCode']),
					'product_name'	=> trim($_POST['pName']),
					'product_cost'	=> $_POST['cost'],
					'product_price'	=> $_POST['price'],
					'weight'			=> $_POST['weight'],
					'length'			=> $_POST['length'],
					'height'			=> $_POST['height'],
					'discount_type'	=> $_POST['discount_type'],
					'discount'			=> $_POST['discount'],
					'default_category_id'	=> $_POST['dCategory'],
					'active'			=> $_POST['active'],
					'date_add'		=> date('Y-m-d H:i:s'),
					'id_product_group'		=> $_POST['pGroup'],
					'show_in_shop'	=> $_POST['inShop'],
					'is_visual'		=> $_POST['isVisual']					
				);		
		$cat 		= $_POST['category_id'];
		$desc	= $_POST['description'];				
		$product	= new product();	
		$rs		= $product->updateProduct($id_pd, $ds);
		if( $rs !== FALSE )
		{
			$sc = 'success';
			$product->setProductCategory($id_pd, $cat);
			$product->setProductDescription($id_pd, $desc);
		}					
	}
	echo $sc;		
}


//---------------  Delete Product  -----------//
if( isset( $_GET['deleteProduct'] ) )
{
	$sc = 'success';
	$id_pd = $_POST['id_product'];
	$product = new product();
	$rs = $product->deleteProduct($id_pd);
	if( $rs !== TRUE )
	{
		$sc = $product->error;
	}
	
	echo $sc;
}

//---------------------------------- Upload images  ------------------------//
if( isset( $_GET['upload'] ) )
{
	$id_pd 	= $_GET['id_product'];
	$sc		= 'success';
	require '../../library/class/class.upload.php';
	if( ! empty( $_FILES ) )
	{
		
		$files 			= $_FILES['file'];
		if( is_string($files['name']) )
		{
			$rs = doUpload($files, $id_pd);	
		}
		else if( is_array($files['name']) )
		{
			$fileCount = count($files['name']);
			for($i = 0; $i < $fileCount; $i++)
			{
				$file = array(
								'name' => $files['name'][$i],
			    				'type' => $files['type'][$i],
			    				'size' => $files['size'][$i],
								'tmp_name' => $files['tmp_name'][$i],
			  					'error' => $files['error'][$i]
								);
				$rs = doUpload($file, $id_pd);
				if( $rs !== TRUE )
				{
					$sc = 'fail';	
				}
			}//--------- For Loop
		}//----- endif
	}
	else
	{
		$sc = "no_file";
	}//--- end if
	echo $sc;
}


if( isset( $_GET['getItemDetail'] ) )
{
	$sc = 'fail';
	$id_pa = $_POST['id_pa'];
	$qs = dbQuery("SELECT * FROM tbl_product_attribute WHERE id_product_attribute = ".$id_pa);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$ds = array(
							'id_pa'		=> $id_pa,
							'reference'		=> $rs['reference'],
							'colors'			=> selectColor($rs['id_color']),
							'sizes'			=> selectSize($rs['id_size']),
							'attributes'		=> selectAttribute($rs['id_attribute']),
							'barcode'		=> $rs['barcode'],
							"cost"				=> $rs['cost'],
							"price"			=> $rs['price'],
							'weight'			=> $rs['weight'],
							'width'				=> $rs['width'],
							'height'			=> $rs['height'],
							'length'			=> $rs['length']							
						);
		$sc = json_encode($ds);						
	}
	echo $sc;
}


if( isset( $_GET['updateItem'] ) && isset( $_GET['id_pd'] ) )
{
	$sc = 'fail';
	$id_pd 		= $_GET['id_pd'];
	$id_pa 		= $_POST['id_pa'];
	$id_color 	= $_POST['color'] == '' ? 0 : $_POST['color'];
	$id_size		= $_POST['size'] == '' ? 0 : $_POST['size'];
	$id_attribute = $_POST['attribute'] == '' ? 0 : $_POST['attribute'];
	$bc 			= $_POST['barcode'] == '' ? TRUE : validBarcode($_POST['barcode'], $id_pa);
	$ref  			= validReference($_POST['reference'], $id_pa);
	$attrs 		= validProductAttribute($id_pd, $id_pa, $id_color, $id_size, $id_attribute);
	if( $bc && $ref && $attrs )
	{
		$pd = new product();
		$ds = array(
							"reference"	=> $_POST['reference'],
							"barcode"	=> $_POST['barcode'],
							"id_color"		=> $id_color,
							"id_size"		=> $id_size,
							"id_attribute"	=> $id_attribute,
							"cost"			=> $_POST['cost'],
							"price"		=> $_POST['price'],
							"weight"		=> $_POST['weight'],
							"width"		=> $_POST['width'],
							"height"		=> $_POST['height'],
							"length"		=> $_POST['length']
						);
		$rs = $pd->updateProductAttribute($id_pa, $ds);
		if( $rs === TRUE )
		{
			$sc = 'success';	
		}
	}
	else
	{
		$sc = $bc === FALSE ? 'บาร์โค้ดสินค้าซ้ำ' : ($ref === FALSE ? 'รหัสสินค้าซ้ำ' : ($attrs === FALSE ? 'สี ไซส์ และ คุณลักษณะอื่นๆ ซ้ำกับรายการอื่นในสินค้าเดียวกัน' : ''));	
	}
	
	echo $sc;	
}


if( isset( $_GET['deleteItem'] ) && isset( $_POST['id_product_attribute'] ) )
{
	$sc 		= 'success';
	$id_pa	= $_POST['id_product_attribute'];
	$product	= new product();
	$rs 		= $product->deleteItem($id_pa);
	if( $rs === FALSE )
	{
		$sc = 'ไม่สามารถลบสินค้าได้ เนื่องจาก';
		if( $product->error == 'stockExists' )
		{
			$sc .= 'มีสินค้าค้างในสต็อก';
		}
		if( $product->error == 'orderExists' OR $product->error == 'transectionExists')
		{
			$sc .= 'มีความเคลื่อนไหวของสินค้าเกิดขึ้นแล้ว';
		}
	}
	
	echo $sc;	
}


//------------------------  Set Cover image  ---------------//
if( isset( $_GET['setCoverImage'] ) )
{
	$sc = 'success';
	$id_image	= $_POST['id_image'];
	$id_pd		= $_POST['id_product'];
	$qs = dbQuery("UPDATE tbl_image SET cover = 0 WHERE id_product = ".$id_pd);
	if( $qs )
	{
		$qr = dbQuery("UPDATE tbl_image SET cover = 1 WHERE id_image = ".$id_image);
		if( ! $qr )
		{
			$sc = 'fail';	
		}
	}
	echo $sc;
}



//----------------------  Delete image  ----------------//

if( isset( $_GET['removeImage'] ) )
{
	$sc = 'success';
	$id_image	= $_POST['id_image'];
	$id_pd		= $_POST['id_product'];
	$cover		= isCover($id_image);
	$rd			= dbQuery("DELETE FROM tbl_image WHERE id_image = ".$id_image);
	$rc			= dbQuery("DELETE FROM tbl_product_attribute_image WHERE id_image =".$id_image);
	$rs			= deleteImage($id_image);
	if( $cover )
	{
		newCover($id_pd);	
	}
	if( ! $rd OR ! $rc ){ $sc = 'fail'; }
	echo $sc;	
}

//-------------------  Load Image Table ----------------//
if( isset( $_GET['getImageTable'] ) )
{
	$sc 	= array();
	$id_pd = $_POST['id_product'];
	$qs 	= getProductImage($id_pd);
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$id_img		= $rs['id_image'];
			$cover		= $rs['cover'] == 1 ? 'btn-success' : '';
			$ds = array(
							'id_pd'		=> $id_pd,
							'id_img'		=> $id_img,
							'thumbImage'	=> imagePath($id_img, 3),
							'bigImage'	=> imagePath($id_img, 4),
							'isCover'		=> $cover
						);
			array_push($sc, $ds);
		}				
	}
	else
	{
		$sc = array("noimage" => "noimage");	
	}
	echo json_encode($sc);
}


//---------------- จับคู่รูปภาพกับสินค้า
if( isset( $_GET['doMappingImageWithProductAttribute'] ) )
{
	$sc 		= 'success';
	$items	= $_POST['items'];
	if( count($items) > 0 )
	{
		foreach( $items as $id_pa => $id_image )
		{
			$qr = dbQuery("SELECT * FROM tbl_product_attribute_image WHERE id_product_attribute = ".$id_pa);
			if( dbNumRows($qr) > 0 )
			{
				$qs = dbQuery("UPDATE tbl_product_attribute_image SET id_image = ".$id_image." WHERE id_product_attribute = ".$id_pa);	
			}
			else
			{
				$qs = dbQuery("INSERT INTO tbl_product_attribute_image (id_product_attribute, id_image) VALUES (".$id_pa.", ".$id_image.")");	
			}
			if( ! $qs ){ $sc = 'fail'; }
		}
	}
	echo $sc;
}



if( isset( $_GET['clearFilter'] ) )
{
	deleteCookie('sCode');
	deleteCookie('sName');
	deleteCookie('sCategory');
	deleteCookie('sGroup');
	deleteCookie('sStatus');
	deleteCookie('sShop');
	echo 'success';
}


//----------------  Toggle Active  -----------------//
if( isset( $_GET['toggleActiveItem'] ) )
{
	$active = 1;
	$id_pa = $_POST['id_pa'];
	$qs = dbQuery("SELECT active FROM tbl_product_attribute WHERE id_product_attribute = ".$id_pa);
	if( dbNumRows($qs) == 1 )
	{
		list( $rs ) = dbFetchArray($qs);
		$active = $rs == 1 ? 0 : 1;
		$qs = dbQuery("UPDATE tbl_product_attribute SET active = ".$active." WHERE id_product_attribute = ".$id_pa);
	}
	echo $active;
}
if( isset( $_GET['setActive'] ) )
{
	$sc 		= 'fail';
	$id_pd	= $_POST['id_product'];
	$qs = dbQuery("SELECT active FROM tbl_product WHERE id_product = ".$id_pd);
	if( dbNumRows($qs) == 1 )
	{
		list( $ac ) 	= dbFetchArray($qs);
		$active 		= $ac == 1 ? 0 : 1;	//----- สลับสถานะ
		$qr = dbQuery("UPDATE tbl_product SET active = ".$active." WHERE id_product = ".$id_pd);
		if( $qr )
		{
			$sc = isActived($active);	
		}
	}
	echo $sc;
}

//----------------  Toggle Show in shop  -----------------//
if( isset( $_GET['setShowInShop'] ) )
{
	$sc 		= 'fail';
	$id_pd 	= $_POST['id_product'];
	$qs 		= dbQuery("SELECT show_in_shop FROM tbl_product WHERE id_product = ".$id_pd);
	if( dbNumRows($qs) == 1 )
	{
		list( $shop ) 	= dbFetchArray($qs);
		$show 		= $shop == 1 ? 0 : 1; //----- สลับสถานะ
		$qr			= dbQuery("UPDATE tbl_product SET show_in_shop = ".$show." WHERE id_product = ".$id_pd);
		if( $qr )
		{
			$sc = isActived($show);	
		}
	}
	echo $sc;
}

if( isset( $_GET['validBarcode'] ) )
{
	$id_pa	= $_POST['id_pa'];
	$bc		= $_POST['barcode'];
	$rs 		= validBarcode($bc, $id_pa);
	$sc		= $rs === TRUE ? 'ok' : 'duplicated';
	echo $sc;
}

if( isset( $_GET['validBarcodePack'] ) )
{
	$sc 		= 'ok';
	$id_pa	= $_POST['id_pa'];
	$bc		= $_POST['barcode'];
	$sc		= validBarcodePack($bc, $id_pa) === TRUE ? 'ok' : 'duplicated';
	
	echo $sc;
}



if( isset( $_GET['updateBarcode'] ) )
{
	$sc 		= 'success';
	$bc		= $_POST['barcode'];
	$id_pa	= $_POST['id_pa'];	
	$qs 		= dbQuery("UPDATE tbl_product_attribute SET barcode = '".$bc."' WHERE id_product_attribute = ".$id_pa);
	if( $qs === FALSE )
	{
		$sc = 'fail';	
	}
	echo $sc;
}

if( isset( $_GET['updateBarcodePack'] ) )
{
	$sc 		= 'success';
	$bc		= $_POST['barcode'];
	$id_pa	= $_POST['id_pa'];	
	$qr		= dbQuery("SELECT * FROM tbl_product_pack WHERE id_product_attribute = ".$id_pa);
	if( dbNumRows($qr) > 0 )
	{
		$qs 		= dbQuery("UPDATE tbl_product_pack SET barcode_pack = '".$bc."' WHERE id_product_attribute = ".$id_pa);
	}
	else
	{
		$qs	= dbQuery("INSERT INTO tbl_product_pack (id_product_attribute, qty, barcode_pack) VALUES (".$id_pa.", 1, '".$bc."')");	
	}
	
	if( $qs === FALSE )
	{
		$sc = 'fail';	
	}
	echo $sc;	
}

if( isset( $_GET['updatePackQty'] ) )
{
	$sc 		= 'success';
	$qty		= $_POST['qty'];
	$id_pa	= $_POST['id_pa'];	
	$qr		= dbQuery("SELECT qty FROM tbl_product_pack WHERE id_product_attribute = ".$id_pa);
	if( dbNumRows($qr) > 0 )
	{
		$qs 		= dbQuery("UPDATE tbl_product_pack SET qty = ".$qty." WHERE id_product_attribute = ".$id_pa);
		if( $qs === FALSE )
		{
			$sc = 'fail';
		}
	}
	else
	{
		$sc = 'nopack';	
	}
	echo $sc;
}

if( isset( $_GET['getImageAttributeGrid'] ) )
{
	$id_pd = $_POST['id_pd'];
	echo imageAttributeGrid($id_pd);	
}


//-------------------------  End New code  ----------------------------------//

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


//-------------------- Attribute Generate ---------------//
if( isset( $_GET['generateProductAttribute'] ) )
{
	$sc 			= TRUE;  		//---- เตรียมค่าสำหรับส่งกลับ โดยตั้งให้สำเร็จไว้ก่อน
	//--------------  รับข้อมูลที่ส่งมา  -------------//
	$id_pd 		= $_POST['id_product'];
	$options		= $_POST['options'];		//-----  จำนวน attribute 1 - 3
	$setColor	= isset( $_POST['set_color'] ) ? $_POST['set_color'] : FALSE;  		//---- ลำดับของรหัสสี 1-3
	$setSize		= isset( $_POST['set_size'] ) ? $_POST['set_size'] : FALSE;			//---- ลำดับของรหัสไซส์ 1-3
	$setAttr		= isset( $_POST['set_attribute'] ) ? $_POST['set_attribute'] : FALSE;	//---- ลำดับของรหัสอื่นๆ 1-3
	$images		= isset( $_POST['image'] ) ? $_POST['image'] : FALSE;	//---- จับคู่รูปภาพ ได้ค่าเป็น Array 
	$colors		= isset( $_POST['color'] ) ? $_POST['color'] : FALSE;
	$sizes		= isset( $_POST['size'] ) ? $_POST['size'] : FALSE; 
	$attrs			= isset( $_POST['attribute'] ) ? $_POST['attribute'] : FALSE; 
	$matching	= $_POST['matching'];
	
	//--------------  ดึงข้อมูลสินค้าต้นแบบ  -------------//
	$product		= new product();
	$product	->product_detail($id_pd);  //------  ดึงข้อมูลสินค้าจากฐานข้อมูล
	$pCode		= $product->product_code;  //----- Product Code
	$pCost		= $product->product_cost;
	$pPrice		= $product->product_price;
	$pWeight		= $product->weight;
	$pWidth		= $product->width;
	$pLength		= $product->length;
	$pHeight		= $product->height;
	$sp			= '-';
	$barcode	= '';
	
	//-------------- ตรวจสอบจำนวน Attribute -------//
	if( $options == 1 )
	{
		$data 	= $colors !== FALSE ? $colors : ( $sizes !== FALSE ? $sizes : $attrs) ; 
		foreach( 	$data as $id )
		{
			$ops	= $colors !== FALSE ? get_color_code($id) : ( $sizes !== FALSE ? get_size_name($id) : get_attribute_name($id) );
			$reference = $pCode . $sp . $ops;
			$ds = array(
								"id_product" => $id_pd,
								"reference" 		=> $reference,
								"barcode"		=> $barcode,
								"id_color"			=> $colors !== FALSE ? $id : 0,
								"id_size"			=> $sizes !== FALSE ? $id : 0,
								"id_attribute"		=> $attrs !== FALSE ? $id : 0,
								"cost"				=> $pCost,
								"price"			=> $pPrice,
								"weight"			=> $pWeight,
								"width"			=> $pWidth,
								"length"			=> $pLength,
								"height"			=> $pHeight
								);
			$rs = $product->addProductAttribute($ds);
			if( $rs === FALSE ){ $sc = FALSE; }
		}
	} //---- if option == 1
	
	if( $options == 2 )
	{
		$opsA	= $setColor == 1 ? $colors : ( $setSize == 1 ? $sizes : $attrs);
		$opsB		= $setColor == 2 ? $colors : ( $setSize == 2 ? $sizes : $attrs);

		foreach( $opsA as $A )
		{
			$ops = $setColor == 1 ? get_color_code($A) : ( $setSize == 1 ? get_size_name($A) : get_attribute_name($A) ) ;
			foreach( $opsB as $B )
			{
				$opsa	= $setColor == 2 ? get_color_code($B) : ( $setSize == 2 ? get_size_name($B) : get_attribute_name($B) ) ;
				$reference = 	$pCode . $sp . $ops . $sp . $opsa;
				$ds = array(
								"id_product" 	=> $id_pd,
								"reference" 		=> $reference,
								"barcode"		=> $barcode,
								"id_color"			=> $colors !== FALSE ? ( $setColor == 1 ? $A : $B ) : 0,
								"id_size"			=> $sizes !== FALSE ? ( $setSize == 1 ? $A : $B ) : 0,
								"id_attribute"		=> $attrs !== FALSE ? ( $setAttr == 1 ? $A : $B ) : 0,
								"cost"				=> $pCost,
								"price"			=> $pPrice,
								"weight"			=> $pWeight,
								"width"			=> $pWidth,
								"length"			=> $pLength,
								"height"			=> $pHeight
								);										
				$rs = $product->addProductAttribute($ds);
				if( $rs === FALSE ){ $sc = FALSE; }
							
			}				
		}	
	}//--- if option == 2
	
	if( $options == 3 )
	{
		$opsA	= $setColor == 1 ? $colors : ( $setSize == 1 ? $sizes : $attrs );
		$opsB		= $setColor	== 2 ? $colors : ( $setSize == 2 ? $sizes : $attrs);
		$opsC	= $setColor == 3 ? $colors : ( $setSize == 3 ? $sizes : $attrs);
		//--- reference = $pCode - $A - $B;
		foreach( $opsA as $A )
		{
			$opA = $setColor == 1 ? get_color_code($A) : ( $setSize == 1 ? get_size_name($A) : get_attribute_name($A) );
			foreach( $opsB as $B )
			{
				$opB = $setColor == 2 ? get_color_code($B) : ( $setSize == 2 ? get_size_name($B) : get_attribute_name($B) );
				foreach( $opsC as $C )
				{
					$opC = $setColor == 3 ? get_color_code($C) : ( $setSize == 3 ? get_size_name($C) : get_attribute_name($C) );
					$reference = 	$pCode . $sp . $opA . $sp . $opB . $sp . $opC;
					$ds = array(
									"id_product" 	=> $id_pd,
									"reference" 		=> $reference,
									"barcode"		=> $barcode,
									"id_color"			=> $setColor == 1 ? $A : ( $setColor == 2 ? $B : $C ),
									"id_size"			=> $setSize == 1 ? $A : ( $setSize == 2 ? $B : $C ),
									"id_attribute"		=> $setAttr == 1 ? $A : ( $setAttr == 2 ? $B : $C ),
									"cost"				=> $pCost,
									"price"			=> $pPrice,
									"weight"			=> $pWeight,
									"width"			=> $pWidth,
									"length"			=> $pLength,
									"height"			=> $pHeight
									);													
				$rs = $product->addProductAttribute($ds);
				if( $rs === FALSE ){ $sc = FALSE; }				
				}	
			}
		}	
	}//--- if option == 3
	//matching รูป
	if( $images !== FALSE )
	{
		$match = $matching == 'color' ? 'id_color' : ( $matching == 'size' ? 'id_size' : 'id_attribute' );
		foreach( $images as $id_image => $id )
		{
			$qr = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = ".$id_pd." AND ".$match." = ".$id);
			if( dbNumRows($qr) > 0 )
			{
				while( $rs = dbFetchArray($qr) )
				{
					$qs = dbQuery("INSERT INTO tbl_product_attribute_image ( id_product_attribute, id_image) VALUES ( ".$rs['id_product_attribute'].", ".$id_image." )");	
				}
			}
		}		
	}
	
	if( $sc === TRUE )
	{
		$sc = 'success';
	}
	else
	{
		$sc = 'fail';	
	}
	
	echo $sc;	
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