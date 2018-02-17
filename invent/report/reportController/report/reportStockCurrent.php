<?php
	include "../../function/order_helper.php";
	//---- Option view : all, instock, nonstock
	$option = $_POST['option'];  
	$sc = '';
		$sc .= '<div class="col-sm-12">
				<div class="col-sm-12" style="background-color:#EEE">
					<ul class="nav navbar-nav" role="tablist" style="background-color:#EEE">
						<li class="menu active"><a href="#all" role="tab" data-toggle="tab" onClick="viewCategory(\'all\')">ทั้งหมด</a></li>
					' . reportCategoryTabsMenu() .'
					</ul>
				</div><!---/ col-sm-12 ---->
			</div><!---/ col-sm-12 ---->
			<hr style="border-color:#CCC; margin-top: 0px; margin-bottom:0px;" />
			<div class="row">
				<div class="col-sm-12">	
					<div class="tab-content" style="min-height:1px; padding:0px;">
					' . reportCategoryTabItems($option) .'
					</div>
				</div>
			</div>';	
	
	echo $sc;
?>
<?php 

function reportCategoryTabItems($option = "instock")
{
	$ds = getTotalQtyAndAmount("all", $option);
	$pd = new product();
	$sc = '<div class="tab-pane active" id="cat-all">';
	$sc .= 	'<div class="col-sm-4 col-xs-12">';
	$sc .= 		'<h4>ทั้งหมด <span class="red padding-10">' . number_format($ds['total_qty']) . '</span> หน่วย </h4>';
	$sc .= 	'</div>';
	$sc .=	'<div class="col-sm-8 col-xs-12">';
	$sc .= 		'<h4>  มูลค่า <span class="blue padding-10">' . number_format($ds['total_amount'], 2) . '</span></h4>';
	$sc .= 	'</div>';
	$sc .= 	'<div class="col-sm-12"><hr/></div>';	 
	
	$qm = reportCategoryItems("all", $option);
	while($rb = dbFetchObject($qm))
	{		
		$sc .= '<div class="item2 col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center margin-bottom-15">';
		$sc .= 	'<div class="product padding-5">';
		$sc .= 		'<div class="image">';
		$sc .= 			'<a href="javascript:void(0)" onclick="getData('.$rb->id_product.')">';
		$sc .=			$pd->getCoverImage($rb->id_product, 2, 'img-responsive');
		$sc .=			'</a>';
		$sc .= 		'</div>';
		$sc .= 		'<div class="description">' . $rb->product_code . '</div>';
		$sc .= 		'<div class="price text-center">';
		$sc .=			'<span class="red">' . number_format($rb->total_qty) . '</span>';
		$sc .=			' | ';
		$sc .=			'<span class="blue">' . number_format($rb->total_amount, 2) . '</span>';
		$sc .=		'</div>';
		$sc .= 	'</div>';
		$sc .= '</div>';
	}	
	$sc .= '</div>';
	
	$qs = dbQuery("SELECT * FROM tbl_category WHERE id_category != 0"); 
	while($rs = dbFetchObject($qs))
	{
		$rd 	= getTotalQtyAndAmount($rs->id_category, $option);
		$sc .= '<div class="tab-pane" id="cat-'.$rs->id_category.'">';
		$sc .= 	'<div class="col-sm-4 col-xs-12">';
		$sc .=		'<h4>'.$rs->category_name.' <span class="red padding-10">' . number_format($rd['total_qty']) . '</span> หน่วย </h4>';
		$sc .= 	'</div>';
		$sc .= 	'<div class="col-sm-8 col-xs-12">';
		$sc .= 		'<h4>มูลค่า <span class="blue padding-10">' . number_format($rd['total_amount'], 2). '</span></h4>';
		$sc .=	'</div>';
		$sc .= 	'<div class="col-sm-12"><hr/></div>';
		
		$qa = reportCategoryItems($rs->id_category, $option);
		while($ra = dbFetchObject($qa))
		{
			$sc .= '<div class="item2 col-lg-2 col-md-3 col-sm-4 col-xs-6 text-center margin-bottom-15">';
			$sc .= 	'<div class="product padding-5">';
			$sc .= 		'<div class="image">';
			$sc .= 			'<a href="javascript:void(0)" onclick="getData('.$ra->id_product.')">';
			$sc .=			$pd->getCoverImage($ra->id_product, 2, 'img-responsive');
			$sc .=			'</a>';
			$sc .= 		'</div>';
			$sc .= 		'<div class="description">' . $ra->product_code . '</div>';
			$sc .= 		'<div class="price text-center">';
			$sc .=			'<span class="red">' . number_format($ra->total_qty) . '</span>';
			$sc .=			' | ';
			$sc .=			'<span class="blue">' . number_format($ra->total_amount, 2) . '</span>';
			$sc .=		'</div>';
			$sc .= 	'</div>';
			$sc .= '</div>';
		}		
		$sc .= '</div>';
	}
	return $sc;
}

function getTotalQtyAndAmount($id_category = 'all', $option = "instock")
{
	$sc = array("total_qty" => 0, "total_amount" => 0.00);
	if( $id_category == 'all' && $option != "nonstock" )
	{
		$qs = dbQuery("SELECT SUM(qty) AS total_qty, SUM(qty * cost) AS total_amount FROM tbl_stock JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute");		
		$sc = dbFetchArray($qs);
	}
	else if( $id_category != 'all' && $option != "nonstock" )
	{
		$qr = "SELECT SUM(qty) AS total_qty, SUM(qty * cost) AS total_amount ";
		$qr .= "FROM tbl_stock JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$qr .= "JOIN tbl_category_product ON tbl_product_attribute.id_product = tbl_category_product.id_product ";
		$qr .= "WHERE tbl_category_product.id_category = ".$id_category;
		$qs = dbQuery($qr);
		$sc = dbFetchArray($qs);
	}
	return $sc;	
}

function reportCategoryItems($id_category, $option = 'instock')
{
	//--- $option = 'all' 			=> สินค้าทั้งหมดในหมวดหมู่ จะมียอดคงเหลือหรือไม่ก็ตาม
	//--- $option = 'nonstock'	=> สินค้าทั้งหมดในหมวดหมู่ เฉพาะตัวที่ไม่มียอดคงเหลือ
	//--- $option = 'instock' 		=> สินค้าทั้งหมดในหมวดหมู่ เฉพาะตัวที่มียอดคงเหลือ
	
	if( $option == 'instock' )
	{
		$qr = "SELECT tbl_product.id_product, SUM( qty ) AS total_qty, SUM( qty * cost ) AS total_amount, product_code ";
		$qr .= "FROM tbl_stock JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$qr .= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
		$qr .= $id_category == "all" ? "" : "JOIN tbl_category_product ON tbl_category_product.id_product = tbl_product.id_product ";
		$qr .= $id_category == "all" ? "" : "WHERE tbl_category_product.id_category = ".$id_category;
		$qr .= " GROUP BY tbl_product.id_product";
	}
	else if( $option == 'nonstock' )
	{
		$qr = "SELECT tbl_product.id_product, SUM( qty ) AS total_qty, SUM( qty * cost ) AS total_amount, product_code ";
		$qr .= "FROM tbl_product JOIN tbl_product_attribute ON tbl_product.id_product = tbl_product_attribute.id_product ";
		$qr .= "LEFT JOIN tbl_stock ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$qr .= $id_category == "all" ? "" : "JOIN tbl_category_product ON tbl_category_product.id_product = tbl_product.id_product ";
		$qr .= "WHERE ";
		$qr .=  $id_category == "all" ? "" : "tbl_category_product.id_category = ".$id_category." AND ";
		$qr .= "tbl_product.id_product NOT IN( SELECT id_product FROM tbl_product_attribute JOIN tbl_stock ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute GROUP BY id_product) ";
		$qr .= "GROUP BY tbl_product.id_product";
	}
	else if( $option == "all")
	{
		$qr = "SELECT tbl_product.id_product, SUM( qty ) AS total_qty, SUM( qty * cost ) AS total_amount, product_code ";
		$qr .= "FROM tbl_product JOIN tbl_product_attribute ON tbl_product.id_product = tbl_product_attribute.id_product ";
		$qr .= "LEFT JOIN tbl_stock ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$qr .= $id_category == "all" ? "" : "JOIN tbl_category_product ON tbl_category_product.id_product = tbl_product.id_product ";
		$qr .= $id_category == "all" ? "" : "WHERE tbl_category_product.id_category = ".$id_category;
		$qr .= " GROUP BY tbl_product.id_product";
	}	
	return dbQuery($qr);
}



function subCategoryTabMenu($parent)
{
	$sc = '';
	$qs = dbQuery("SELECT * FROM tbl_category WHERE parent_id = ".$parent." ORDER BY category_name ASC");
	
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchObject($qs) )
		{
			if( haveSubCategory($rs->id_category) === TRUE ) //----- ถ้ามี sub category 
			{
				$sc .= '<li class="dropdown-submenu" >';
				$sc .= '<a id="ul-'.$rs->id_category.'" class="dropdown-toggle" href="#cat-'.$rs->id_category.'" role="tab" data-toggle="tab" onClick="viewCategory('.$rs->id_category.')">';
				$sc .=  $rs->category_name.'</a>';
				$sc .= 	'<ul class="dropdown-menu" role="menu" aria-labelledby="ul-'.$rs->id_category.'">';
				$sc .= 	subCategoryTabMenu($rs->id_category);
				$sc .=  '</ul>';
				$sc .= '</li>';
			}
			else
			{
				$sc .= '<li class="menu"><a href="#cat-'.$rs->id_category.'" role="tab" data-toggle="tab" onClick="viewCategory('.$rs->id_category.')">'.$rs->category_name.'</a></li>';
			}
			
		}
	}
	return $sc;
}
function reportCategoryTabsMenu()
{
	$sc = '';
	$level = 1;
	$qs = dbQuery("SELECT * FROM tbl_category WHERE level_depth = ".$level." ORDER BY category_name ASC");
	while( $rs = dbFetchObject($qs))
	{
		if( haveSubCategory($rs->id_category) === TRUE)
		{
			$sc .= '<li class="dropdown" onmouseover="expandCategory((this))" onmouseout="collapseCategory((this))">';
			$sc .= '<a id="ul-'.$rs->id_category.'" class="dropdown-toggle" role="tab" data-toggle="tab" href="#cat-'.$rs->id_category.'" onClick="viewCategory('.$rs->id_category.')" >';
			$sc .=  $rs->category_name.'<span class="caret"></span></a>';
			$sc .= 	'<ul class="dropdown-menu" role="menu" aria-labelledby="ul-'.$rs->id_category.'">';
			$sc .= 	subCategoryTabMenu($rs->id_category);
			$sc .=  '</ul>';
			$sc .= '</li>';			
		}
		else
		{
			$sc .= '<li class="menu"><a href="#cat-'.$rs->id_category.'" role="tab" data-toggle="tab" onClick="viewCategory('.$rs->id_category.')">'.$rs->category_name.'</a></li>';
		}
	}
	$sc .= '<script>
						function expandCategory(el)
						{
							var className = "open";
							if (el.classList)
							{
								el.classList.add(className)
							}else if (!hasClass(el, className)){
								el.className += " " + className
							}
						}
					
						function collapseCategory(el)
						{
							var className = "open";
							if (el.classList)
							{
								el.classList.remove(className)
							}else if (hasClass(el, className)) {
								var reg = new RegExp("(\\s|^)" + className + "(\\s|$)")
								el.className=el.className.replace(reg, " ")
							}
						}
				</script>';			
	return $sc;							
}

?>