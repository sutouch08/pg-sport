<?php

//---------------------------------------------  Category  ---------------------------------------//
function productInCategory($id_category)
{
	$in = FALSE;
	$qs = dbQuery("SELECT id_product FROM tbl_category_product WHERE id_category = ".$id_category);
	$row = dbNumRows($qs);
	if( $row > 0 )
	{
		$in 	= '';
		$i 		= 1;
		while( $rs = dbFetchArray($qs) )
		{
			$in .= $rs['id_product'];
			if( $i < $row){ $in .= ', '; }
			$i++;
		}
	}
	return $in;
}

function isProductInCategory($id_pd, $id_cat)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_category FROM tbl_category_product WHERE id_product = ".$id_pd." AND id_category = ".$id_cat);
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;	
	}
	return $sc;
}

function getMaxDepth()
{
	$sc = 0;
	$qs = dbQuery("SELECT MAX(level_depth) AS max FROM tbl_category");
	list( $max ) = dbFetchArray($qs);
	if( ! is_null( $max ) )
	{
		$sc = $max;
	}
	return $sc;
}

function getMinDepth()
{
	$sc = 0;
	$qs = dbQuery("SELECT MIN(level_depth) AS MIN FROM tbl_category");
	list( $min ) = dbFetchArray($qs);
	if( ! is_null( $min ) )
	{
		$sc = $min;
	}
	return $sc;
}

function hasSubCategory($id_category)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_category FROM tbl_category WHERE parent_id = ".$id_category);
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;
	}
	return $sc;
}



function categoryTree($id_pd)
{
	$sc 		= '<ul class="tree">';
	$level		= 0;
	$qs = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE level_depth = ".$level." ORDER BY category_name ASC");
	while( $rs = dbFetchArray($qs) )
	{
		$isChecked = isProductInCategory($id_pd, $rs['id_category']) === TRUE ? 'checked' : '' ;
		$sc .= '<li>';
		
		
		//----- Next Level
		if( hasSubCategory($rs['id_category']) === TRUE )
		{
			$sc .= '<i class="fa fa-plus-square-o" id="catbox-'.$rs['id_category'].'" onClick="toggleTree('.$rs['id_category'].')"></i>';
			$sc .= '<label class="padding-10"><input type="checkbox" class="margin-right-10" name="category_id[]" value="'.$rs['id_category'].'" '.$isChecked.' />'.$rs['category_name'].'</label>';
			$sc .= '<ul id="catchild-'.$rs['id_category'].'" class="">';
			$sc .= subCategory($rs['id_category'], $id_pd) ;		
			$sc .= '</ul>';	
		}
		else
		{
			$sc .= '<label><input type="checkbox" class="padding-10" name="category_id[]" value="'.$rs['id_category'].'" '.$isChecked.' />'.$rs['category_name'].'</label>';		
		}//---- has sub cate
		$sc .= '</li>';
	}
	$sc	.= '</ul>';
	$sc .= '<script>';
	$sc .= 'function toggleTree(id){';
	$sc .= 'var ul 	= $("#catchild-"+id);';
	$sc .= 'var rs 	= ul.hasClass("hide");';
	$sc .= 'if( rs == true){';
	$sc .= 'ul.removeClass("hide");';
	$sc .= '$("#catbox-"+id).removeClass("fa-plus-square-o");';
	$sc .= '$("#catbox-"+id).addClass("fa-minus-square-o");';
	$sc .= '}else	{';
	$sc .= 'ul.addClass("hide");';
	$sc .= '$("#catbox-"+id).removeClass("fa-minus-square-o");';
	$sc .= '$("#catbox-"+id).addClass("fa-plus-square-o");';
	$sc .= '} ';
	$sc .= '}';
	$sc .= '</script>';
	return $sc;
}






function subCategory($parent, $id_pd = '')
{
	$sc = '';
	$qr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = ".$parent." ORDER BY category_name ASC");
	if( dbNumRows($qr) > 0 )
	{
		while( $rs = dbFetchArray($qr) )
		{
			$isChecked = $id_pd == '' ? '' : (isProductInCategory($id_pd, $rs['id_category']) === TRUE ? 'checked' : '' );
			$sc .= '<li>';
			if( hasSubCategory($rs['id_category']) === TRUE ) //----- ถ้ามี sub category 
			{
				$sc .= '<i class="fa fa-plus-square-o" id="catbox-'.$rs['id_category'].'" onClick="toggleTree('.$rs['id_category'].')"></i>';
				$sc .= '<label class="padding-10"><input type="checkbox" class="margin-right-10" name="category_id[]" value="'.$rs['id_category'].'" '.$isChecked.' />'.$rs['category_name'].'</label>'; 
				$sc .= '<ul id="catchild-'.$rs['id_category'].'" class="hide">';
				$sc .= getSubCategory($rs['id_category'], $id_pd);
				$sc .= '</ul>';
			}
			else
			{
				$sc .= '<label><input type="checkbox" class="padding-10" name="category_id[]" value="'.$rs['id_category'].'" '.$isChecked.' />'.$rs['category_name'].'</label>'; 
			}
			$sc .= '</li>';
		}
	}
	return $sc;
}

function getSubCategory($parent, $id_pd='')
{
	$sc = '';
	$qr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = ".$parent." ORDER BY category_name ASC");
	if( dbNumRows($qr) > 0 )
	{
		while( $rs = dbFetchArray($qr) )
		{
			$isChecked = $id_pd == '' ? '' : (isProductInCategory($id_pd, $rs['id_category']) === TRUE ? 'checked' : '' );
			$sc .= '<li>';
			if( hasSubCategory($rs['id_category']) === TRUE ) //----- ถ้ามี sub category 
			{
				$sc .= '<i class="fa fa-plus-square-o" id="catbox-'.$rs['id_category'].'" onClick="toggleTree('.$rs['id_category'].')"></i>';
				$sc .= '<label><input type="checkbox" class="padding-10" name="category_id[]" value="'.$rs['id_category'].'" '.$isChecked.' />'.$rs['category_name'].'</label>'; 
				$sc .= '<ul id="catchild-'.$rs['id_category'].'" class="hide">';
				$sc .= subCategory($rs['id_category'], $id_pd);
				$sc .= '</ul>';
			}
			else
			{
				$sc .= '<label><input type="checkbox" class="padding-10" name="category_id[]" value="'.$rs['id_category'].'" '.$isChecked.' />'.$rs['category_name'].'</label>'; 
			}
			$sc .= '</li>';
		}
	}
	return $sc;
}
		
function categoryName($id)
{
	$sc = '';
	$qs = dbQuery("SELECT category_name FROM tbl_category WHERE id_category = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);	
	}
	return $sc;
}

function selectCategory($se = 0)
{
	$sc = '<option value="0"' . isSelected($se, 0) .'>ทั้งหมด</option>';
	$qs = dbQuery("SELECT * FROM tbl_category WHERE id_category != 0 ORDER BY parent_id ASC, id_category ASC");
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$sc .= '<option value="'.$rs['id_category'].'" '. isSelected($rs['id_category'], $se).' >'.$rs['category_name'].'</option>';
		}
	}
	return $sc;	
}

//--------------------------------------------------------- End Category  -----------------------------------------------------//

//---------------------------------------------------------  Product  -----------------------------------------------------//
		
function getProductDetail($id='')
{
	if($id != '')
	{
		$qs  = dbQuery("SELECT * FROM tbl_product WHERE id_product = ".$id);	
	}
	$rs = $id == '' ? '' : dbFetchArray($qs);
	$ds = array(
				'product_code' => $id != '' ? $rs['product_code'] : '',
				'product_name'	=> $id != '' ? $rs['product_name'] : '',
				'product_cost'	=> $id != '' ? $rs['product_cost'] : '0.00',
				'product_price'	=> $id != '' ? $rs['product_price'] : '0.00',
				'weight'			=> $id != '' ? $rs['weight'] : '0.00',
				'width'				=> $id != '' ? $rs['width'] : '0.00',
				'length'			=> $id != '' ? $rs['length'] : '0.00',
				'height'			=> $id != '' ? $rs['height'] : '0.00',
				'discount_type'	=> $id != '' ? $rs['discount_type'] : 'percentage',
				'discount'			=> $id != '' ? $rs['discount'] : '0.00',
				'default_category_id'	=> $id != '' ? $rs['default_category_id'] : '',
				'active'			=> $id != '' ? $rs['active'] : '1',
				'date_add'		=> $id != '' ? $rs['date_add'] : '',
				'date_upd'		=> $id != '' ? $rs['date_upd'] : '',
				'id_product_group'		=> $id != '' ? $rs['id_product_group'] : '',
				'show_in_shop'	=> $id != '' ? $rs['show_in_shop'] : '0',
				'is_visual'		=> $id != '' ? $rs['is_visual'] : '0'				
			);
	return $ds;			
}




function productDescription($id = '' )
{
	$sc = '';
	if( $id != '' )
	{
		$qs = dbQuery("SELECT product_detail FROM tbl_product_detail WHERE id_product = ".$id);
		if( dbNumRows($qs) == 1 )
		{
			list( $sc ) = dbFetchArray($qs);	
		}
	}
	return $sc;
}

function getProductPack($id_pa)
{
	$sc = array('barcode' => '', 'qty' => '');
	$qs = dbQuery("SELECT qty, barcode_pack FROM tbl_product_pack WHERE id_product_attribute = ".$id_pa);
	if( dbNumRows($qs) == 1 )
	{
		list( $qty, $barcode ) = dbFetchArray($qs);
		$sc['barcode'] 	= $barcode;
		$sc['qty']			= $qty;
	}
	return $sc;
}

function productAttributeIn($id_pd)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE id_product = ".$id_pd);
	if( dbNumRows($qs) > 0 )
	{
		$sc 	= '';
		$row 	= dbNumRows($qs);
		$i 		= 1;
		while( $rs = dbFetchArray($qs) )
		{
			$sc .= $rs['id_product_attribute'];
			if( $i < $row ){ $sc .= ', '; }
			$i++;	
		}		
	}
	return $sc;
}

function selectColor($id = '')
{
	$sc = '<option value="">เลือกสี</option>';
	$qs = dbQuery("SELECT * FROM tbl_color");
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$sc .= '<option value="'.$rs['id_color'].'" '.isSelected($id, $rs['id_color']).'>'.$rs['color_code'].' : '.$rs['color_name'].'</option>';
		}
	}
	return $sc;
}

function selectSize($id = '')
{
	$sc = '<option value="">เลือกไซส์</option>';
	$qs = dbQuery("SELECT * FROM tbl_size");
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$sc .= '<option value="'.$rs['id_size'].'" '.isSelected($id, $rs['id_size']).'>'.$rs['size_name'].'</option>';
		}
	}
	return $sc;
}

function selectAttribute($id = '')
{
	$sc = '<option value="">เลือกคุณลักษณะ</option>';
	$qs = dbQuery("SELECT * FROM tbl_attribute");
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$sc .= '<option value="'.$rs['id_attribute'].'" '.isSelected($id, $rs['id_attribute']).'>'.$rs['attribute_name'].'</option>';
		}
	}
	return $sc;
}

function getBarcodePack($id_pa)
{
	$sc = '';
	$qs = dbQuery("SELECT barcode_pack FROM tbl_product_pack WHERE id_product_attribute = ".$id_pa." LIMIT 1");
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);	
	}
	return $sc;
}

function getPackQty($id_pa)
{
	$sc = 0;
	$qs = dbQuery("SELECT qty FROM tbl_product_pack WHERE id_product_attribute = ".$id_pa." LIMIT 1");
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);	
	}
	return $sc;
}
//-------------------------------------------------  End Product  ---------------------------------------------//


//------------------------------------------------  Image -------------------------------------------------//

//----- มีรูปอยู่แล้วหรือเปล่า-----//
function hasImage($id_product)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_image FROM tbl_image WHERE id_product = ".$id_product);
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;	
	}
	return $sc;
}



//----- Insert image to data base ---//
function addImage($id_image, $id_product, $position, $cover)
{
	return dbQuery("INSERT INTO tbl_image ( id_image, id_product, position, cover ) VALUES ( ".$id_image.", ".$id_product.", ".$position.", ".$cover.")");
}



function newImageId()
{
	$sc = 0;
	$qs = dbQuery("SELECT MAX(id_image) AS id FROM tbl_image");
	list( $rs ) = dbFetchArray($qs);
	if( ! is_null( $rs ) )
	{
		$sc = $rs + 1;
	}
	return $sc;
}




function newImagePosition($id_product)
{
	$sc = 1;
	$qs = dbQuery("SELECT MAX( position ) AS pos FROM tbl_image WHERE id_product = ".$id_product);
	list( $rs ) = dbFetchArray($qs);
	if( ! is_null( $rs ) )
	{
		$sc = $rs + 1;
	}
	return $sc;
}




function haveCover($id_product)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_image FROM tbl_image WHERE id_product = ".$id_product." AND cover = 1");
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;	
	}
	return $sc;
}


function isCover($id_image)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_image FROM tbl_image WHERE id_image = ".$id_image." AND cover = 1");
	if( dbNumRows($qs) == 1 )
	{
		$sc = TRUE;
	}
	return $sc;
}


function newCover($id_pd)
{
	$sc = TRUE;
	$rs = haveCover($id_pd);
	if( ! $rs )	
	{
		$qs = dbQuery("SELECT MAX( position ) AS max FROM tbl_image WHERE id_product = ".$id_pd);
		list( $rd ) = dbFetchArray($qs);
		if( ! is_null($rd) )
		{
			$qr = dbQuery("UPDATE tbl_image SET cover = 1 WHERE id_product = ".$id_pd." AND position = ".$rd);
			if( ! $qr ){ $sc = FALSE; }
		}
	}
	return $sc;
}


function ImagePath($id_image, $size)
{
	$sc 		= '';
	$path		= str_split($id_image);	
	$iPath		= WEB_ROOT .'img/product';
	foreach( $path as $p )
	{
		$iPath .= '/' . $p;	
	}
	$iPath .= '/';
	$img	= getImageSizeProperties($size);
	$sc	= $iPath . $img['prefix'] . $id_image . '.jpg';
	return $sc;
}

//--------------- แสดงรูปภาพสินค้า ----------------//
function getProductAttributeImagePath($id_pa, $useSize)
{
	$sc = FALSE;
	$imgId		= getProductAttributeImageId($id_pa);
	if( $imgId !== FALSE )
	{
		$sc = ImagePath($imgId, $useSize);	
	}
	return $sc;
}


function getProductAttributeImageId($id_pa)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_image FROM tbl_product_attribute_image WHERE id_product_attribute = ".$id_pa);
	if( dbNumRows($qs) > 0 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}

function getCoverImagePath($id_pd, $useSize = 2)
{
	$sc = FALSE;
	$id_img = getCoverImageId($id_pd);
	if( $id_img !== FALSE )
	{
		$sc = ImagePath($id_img, $useSize);
	}
	else
	{
		$sc = noImage($useSize);
	}
	return $sc;		
}

function getCoverImageId($id_pd)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_image FROM tbl_image WHERE id_product = ".$id_pd." AND cover = 1");
	if( dbNumRows($qs) > 0 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}


function noImage($useSize = 2)
{
	$iPath		= WEB_ROOT .'img/product';
	switch( $useSize )
	{
		case 1 :
		$iPath .= '/no_image_mini.jpg';
		break;
		
		case 2 :
		$iPath .= '/no_image_default.jpg';
		break;
		
		case 3 :
		$iPath .= '/no_image_medium.jpg';
		break;
		
		case 4 :
		$iPath .= '/no_image_lage.jpg';
		break;
		
		default :
		$iPath .= '/no_image_default.jpg';	
	}
	return $iPath;
		
}

//---------------- ลบไฟล์ออกจาก server --------------//
function deleteImage($id_image)
{
	$sc = TRUE;
	$size = 4;
	while( $size > 0 )
	{
		$path = getImagePath($id_image, $size);
		if( ! unlink($path) )
		{
			$sc = FALSE;
		}
		$size--;	
	}
	return $sc;
}

function getImageSizeProperties($size)
{
	$sc = array();
	switch($size)
	{
		case "1" :
		$sc['prefix']	= "product_mini_";
		$sc['size'] 	= 60;
		break;
		case "2" :
		$sc['prefix'] 	= "product_default_";
		$sc['size'] 	= 125;
		break;
		case "3" :
		$sc['prefix'] 	= "product_medium_";
		$sc['size'] 	= 250;
		break;
		case "4" :
		$sc['prefix'] 	= "product_lage_";
		$sc['size'] 	= 1500;
		break;
		default :
		$sc['prefix'] 	= "";
		$sc['size'] 	= 300;
		break;
	}//--- end switch	
	return $sc;
}

function getProductImage($id_product)
{
	return dbQuery("SELECT * FROM tbl_image WHERE id_product = ".$id_product);	
}

function doUpload($file, $id_pd)
{
	$sc 			= TRUE;
	$id_image	= newImageId(); //-- เอา id_image ล่าสุด มา + 1
	$imgName 	= $id_image; //-- ตั้งชื่อรูปตาม id_image
	$count		= strlen($id_image);  //--- นับจำนวนหน่วยของไอดี เพื่อเอาไปแยกเป็น Folder
	$path			= str_split($id_image);
	$imgPath 	= '../../img/product';
	foreach( $path as $p )
	{
		$imgPath .= '/' . $p;
	}
	$imgPath .= '/';
	$image 	= new upload($file);
	$size 	= 4; //---- ใช้ทั้งหมด 4 ขนาด
	if( $image->uploaded )
	{
		//------  ทำการปรับขนาดและตั้งชื่อไฟล์แต่ละไซด์ที่ต้องการใช้งาน
		while( $size > 0 )
		{
			$img	= getImageSizeProperties($size); //--- ได้ $img['prefix'] , $img['size'] กลับมา
			$size--;				
			$image->file_new_name_body	= $img['prefix'] . $imgName; 		//--- เปลี่ยนชือ่ไฟล์ตาม prefix + id_image
			$image->image_resize			= TRUE;		//--- อนุญาติให้ปรับขนาด
			$image->image_retio_fill			= TRUE;		//--- เติกสีให้เต็มขนาดหากรูปภาพไม่ได้สัดส่วน
			$image->file_overwrite			= TRUE;		//--- เขียนทับไฟล์เดิมได้เลย
			$image->auto_create_dir		= TRUE;		//--- สร้างโฟลเดอร์อัตโนมัติ กรณีที่ไม่มีโฟลเดอร์
			$image->image_x					= $img['size'];		//--- ปรับขนาดแนวตั้ง
			$image->image_y					= $img['size'];		//--- ปรับขนาดแนวนอน
			$image->image_background_color	= "#FFFFFF";		//---  เติมสีให้ตามี่กำหนดหากรูปภาพไม่ได้สัดส่วน
			$image->image_convert			= 'jpg';		//--- แปลงไฟล์
			
			$image->process($imgPath);						//--- ดำเนินการตามที่ได้ตั้งค่าไว้ข้างบน
			
			if( ! $image->processed )	//--- ถ้าไม่สำเร็จ
			{
				$sc 	= $image->error;
			}
		}//--- end while
	}//--- end if
	$image->clean();	//--- เคลียร์รูปภาพออกจากหน่วยความจำ
	$cover	= haveCover($id_pd) == TRUE ? 0 : 1  ;  		//--- มี cover อยู่แล้วหรือป่าว  มีอยู่แล้ว = TRUE , ไม่มี = FALSE
	$top		= newImagePosition($id_pd); 					//--- ตำแหน่งล่าสุดของรูปสินค้านั้นๆ +1
	$rs 		= addImage($id_image, $id_pd, $top, $cover);		//--- เพิ่มข้อมูลรูปภาพลงฐานข้อมูล		
	return $sc;
}


function imageAttributeGrid($id_pd)
{
	$sc = 'noimage';
	$qs = dbQuery("SELECT id_product_attribute, reference FROM tbl_product_attribute WHERE id_product = ".$id_pd);
	$qr = dbQuery("SELECT id_image FROM tbl_image WHERE id_product = ".$id_pd);
	$ic	 = dbNumRows($qr);  //---- จำนวนรูปภาพ
	$pc = dbNumRows($qs); //---- จำนวนรายการสินค้า
	if( $pc > 0 && $ic > 0 )
	{
		$topRow	= $qr;
		$imgs		= array();
		$width	= ceil(80/$ic);
		
		$sc = '<table class="table table-bordered">';
		
		//---- image header
		$sc .= '<tr><td style="width:20%;"></td>';
		while($ra = dbFetchArray($topRow) )
		{
			$sc .= '<td style="width:'.$width.'%;">';
			$sc .= '<img src="'.ImagePath($ra['id_image'], 2).'" width="100%" />';
			$sc .= '</td>';
			$imgs[] = $ra['id_image'];
		}
		$sc .= '</tr>';
		//---- End image header
		
		while( $rs = dbFetchArray($qs) )
		{
			$img	= $imgs;
			$sc .= '<tr>';
			$sc .= '<td>'.$rs['reference'].'</td>';
			$qa = dbQuery("SELECT id_image FROM tbl_product_attribute_image WHERE id_product_attribute = ".$rs['id_product_attribute']." LIMIT 1");
			if( dbNumRows($qa) == 1 )
			{ 
				list( $id_image )	= dbFetchArray($qa);
			}
			else
			{
				$id_image = '';	
			}
			foreach($img as $id)
			{
				$sc .= '<td><label style="width:100%; text-align:center;"><input type="radio" name="items['.$rs['id_product_attribute'].']" value="'.$id.'" '.isChecked($id, $id_image).' /></label></td>';
			}
			$sc .= '</tr>';
		}
		$sc .= '</table>';		
	}	
	
	return $sc;
}

//--------------------------------------------------------  End Images ----------------------------------------------------//


//-------------------------------------------------------  Product Group  ----------------------------------------------------//

function isProductGroupExists($name, $id='')
{
	$sc = FALSE; //--- false = ไม่ซ้ำ
	if( $id != '' )
	{
		$qs = dbQuery("SELECT * FROM tbl_product_group WHERE id != ".$id." AND name = '".$name."'");	
	}
	else
	{
		$qs = dbQuery("SELECT * FROM tbl_product_group WHERE name = '".$name."'");	
	}
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;	
	}
	return $sc;		
}


function productInGroup($id_product_group)
{
	$qs = dbQuery("SELECT COUNT(*) FROM tbl_product WHERE id_product_group = ".$id_product_group);
	list( $rs ) = dbFetchArray($qs);
	return $rs;	
}

function getDefaultProductGroup()
{
	$sc = 1;
	$qs = dbQuery("SELECT id FROM tbl_product_group WHERE isDefault = 1");
	if( dbNumRows($qs) > 0 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}

function selectProductGroup($se = '' )
{
	$sc = '';
	$df = getDefaultProductGroup();
	$qs = dbQuery("SELECT * FROM tbl_product_group");
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$sel = $se == $rs['id'] ? 'selected' : ( $se === '' && $rs['id'] == $df ? 'selected' : '' );
			$sc .= '<option value="'.$rs['id'].'" '. $sel .'>'.$rs['name'].'</option>';
		}
	}
	return $sc;
}

function productGroupName($id)
{
	$sc = '';
	$qs = dbQuery("SELECT name FROM tbl_product_group WHERE id = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}

function validBarcode($bc, $id_pa = '')
{
	$sc 		= TRUE;
	$qs 		= dbQuery("SELECT barcode FROM tbl_product_attribute WHERE barcode = '".$bc."' AND id_product_attribute != ".$id_pa);
	if( dbNumRows($qs) > 0 )
	{
		$sc = FALSE;
	}
	$qr 		= dbQuery("SELECT barcode_pack FROM tbl_product_pack WHERE barcode_pack = '".$bc."'");
	if( dbNumRows($qr) > 0 )
	{
		$sc = FALSE;
	}
	return $sc;
}

function validBarcodePack($bc, $id_pa)
{
	$sc	 	= TRUE;
	$qs 		= dbQuery("SELECT barcode_pack FROM tbl_product_pack WHERE barcode_pack = '".$bc."' AND id_product_attribute != ".$id_pa);
	if( dbNumRows($qs) > 0 )
	{
		$sc = FALSE;
	}
	$qr 		= dbQuery("SELECT barcode FROM tbl_product_attribute WHERE barcode = '".$bc."'");
	if( dbNumRows($qr) > 0 )
	{
		$sc = FALSE;	
	}	
	return $sc;
}

function validReference($reference, $id_pa)
{
	$sc = TRUE;
	$qs = dbQuery("SELECT id_product FROM tbl_product_attribute WHERE reference = '".$reference."' AND id_product_attribute != ".$id_pa);
	if( dbNumRows($qs) > 0 )
	{
		$sc = FALSE;	
	}
	return $sc;
}

function validProductAttribute($id_pd, $id_pa, $id_color, $id_size, $id_attribute)
{
	$sc = TRUE;
	if( $id_color != 0 OR $id_size != 0 OR $id_attribute != 0 )
	{
		$qs = dbQuery("SELECT id_product FROM tbl_product_attribute WHERE id_product = ".$id_pd." AND id_product_attribute != ".$id_pa." AND id_color = ".$id_color." AND id_size = ".$id_size." AND id_attribute = ".$id_attribute);
		if( dbNumRows($qs) > 0 )
		{
			$sc = FALSE;
		}
	}
	return $sc;
}
//---------------------------------------------------------  End Product Group  --------------------------------------//

function isVisual($id_pa){
    $sc = 0;
    $qs = dbQuery("SELECT is_visual FROM tbl_product JOIN tbl_product_attribute ON tbl_product.id_product = tbl_product_attribute.id_product WHERE id_product_attribute = ".$id_pa);
    if(dbNumRows($qs) == 1 ){
       list( $sc ) = dbFetchArray($qs);
    }
    return $sc;
}

function getBarcode($id_pa)
{
	$sc = "";
	$qs = dbQuery("SELECT barcode FROM tbl_product_attribute WHERE id_product_attribute = ".$id_pa);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}
?>