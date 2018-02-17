<?php

function getOrderStateInTime($state, $from, $to) //--- กรองวันที่และเวลา ตามสถานะ
{
	$sc = FALSE;
	$qr = "SELECT distinct tbl_order.id_order FROM tbl_order JOIN tbl_order_state_change ON tbl_order.id_order = tbl_order_state_change.id_order ";
	$qr .= "WHERE id_order_state = $state AND tbl_order_state_change.date_add >= '".$from."' AND tbl_order_state_change.date_add <= '".$to."' ORDER BY tbl_order_state_change.date_add DESC";
	$qs = dbQuery($qr);
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchObject($qs) )
		{
			$sc .= $rs->id_order.', ';	
		}
		$sc = trim($sc, ', ');
	}	
	return $sc;
}


function selectStateTime($state)
{
	$states = array(
					'3'	=> 'รอจัดสินค้า', //-- รอจัดสินค้า
					'4'	=> 'กำลังจัดสินค้า', //-- กำลังจัดสินค้า
					'5'	=> 'รอตรวจสินค้า', //-- รอตรวจสินค้า
					'11'	=> 'กำลังตรวจสินค้า', //-- กำลังตรวจสินค้า
					'10'	=> 'รอเปิดบิล' //-- รอเปิดบิล
					);
	$sc = '<option value="">เลือกสถานะ</option>';
	foreach( $states as $id => $name)
	{
		$sc .= '<option value="'.$id.'" '.isSelected($state, $id).'>'.$name.'</option>';
	}
	return $sc;
}

function selectTime($time)
{
	$sc = '';
	$times = array('00:00','00:30','01:00','01:30','02:00','02:30','03:00','03:30','04:00','04:30','05:00','05:30','06:00','06:30','07:00','07:30','08:00','08:30','09:00',
						'09:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30',
						'20:00','20:30','21:00','21:30','22:00','22:30','23:00','23:30');
	foreach($times as $hrs)
	{
		$sc .= '<option value="'.$hrs.'" '.isSelected($time, $hrs).'>'.$hrs.'</option>';
	}
	return $sc;		
}

/*
function selectHour($hour)
{
	$sc = '';
	$time = array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23');
	foreach( $time as $hrs )
	{
		$sc .= '<option value="'.$hrs.'" '.isSelected($hour, $hrs).'>'.$hrs.'</option>';
	}
	return $sc;
}

function selectMin($min)
{
	$sc = '';
	$time = array('00', '15', '30', '45');
	foreach( $time as $mins )
	{
		$sc .= '<option value="'.$mins.'" '.isSelected($min, $mins).'>'.$mins.'</option>';
	}
	return $sc;
}
*/

function getStateIn($state)
{
	$sc = '';
	$id = array(
							'state_1'	=> 1, //-- รอชำระเงิน
							'state_3'	=> 3, //-- รอจัดสินค้า
							'state_4'	=> 4, //-- กำลังจัดสินค้า
							'state_5'	=> 5, //-- รอตรวจสินค้า
							'state_11'	=> 11, //-- กำลังตรวจสินค้า
							'state_10'	=> 10 //-- รอเปิดบิล
							);
	foreach( $state as $key => $val )
	{
		if( $val == 1 )
		{
			$sc .= $id[$key].',';	
		}
	}
	return trim($sc, ',');
}

function haveSubCategory($id_category)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_category FROM tbl_category WHERE parent_id = ".$id_category);
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;
	}
	return $sc;
}

function categoryTabMenu($mode = 'order')
{
	$ajax = $mode == 'order' ? 'getCategory' : 'getViewCategory';
	$sc = '';
	$level = 1;
	$qs = dbQuery("SELECT * FROM tbl_category WHERE level_depth = ".$level." ORDER BY category_name ASC");
	while( $rs = dbFetchObject($qs))
	{
		if( haveSubCategory($rs->id_category) === TRUE)
		{
			$sc .= '<li class="dropdown" onmouseover="expandCategory((this))" onmouseout="collapseCategory((this))">';
			$sc .= '<a id="ul-'.$rs->id_category.'" class="dropdown-toggle" role="tab" data-toggle="tab" href="#cat-'.$rs->id_category.'" onClick="'.$ajax.'('.$rs->id_category.')" >';
			$sc .=  $rs->category_name.'<span class="caret"></span></a>';
			$sc .= 	'<ul class="dropdown-menu" role="menu" aria-labelledby="ul-'.$rs->id_category.'">';
			$sc .= 	subCategoryTab($rs->id_category, $ajax);
			$sc .=  '</ul>';
			$sc .= '</li>';			
		}
		else
		{
			$sc .= '<li class="menu"><a href="#cat-'.$rs->id_category.'" role="tab" data-toggle="tab" onClick="'.$ajax.'('.$rs->id_category.')">'.$rs->category_name.'</a></li>';
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
						
						//--------------------------------  โหลดรายการสินค้าสำหรับดูยอดคงเหลือ  -----------------------------//
					function getViewCategory(id) 
					{
						var output = $("#cat-" + id);
						$(".tab-pane").removeClass("active");
						$(".menu").removeClass("active");
						if (output.html() == "") {
							load_in();
							$.ajax({
								url: "controller/orderController.php?getCategoryGrid",
								type: "POST",
								cache: "false",
								data: { "id_category": id },
								success: function(rs) {
									load_out();
									var rs = $.trim(rs);
									if (rs != "no_product") {
										output.html(rs);
									} else {
										output.html("<center><h4>ไม่พบสินค้าในหมวดหมู่ที่เลือก</h4></center>");
									}
								}
							});
						}
						
						output.addClass("active");
					}
					
					//--------------------------------  โหลดรายการสินค้าสำหรับจิ้มสั่งสินค้า  -----------------------------//
					function getCategory(id) {
						var output = $("#cat-" + id);
						$(".tab-pane").removeClass("active");
						$(".menu").removeClass("active");
						if (output.html() == "") {
							load_in();
							$.ajax({
								url: "controller/orderController.php?getCategoryProductGrid",
								type: "POST",
								cache: "false",
								data: { "id_category": id },
								success: function(rs) {
									load_out();
									var rs = $.trim(rs);
									if (rs != "no_product") {
										output.html(rs);
									} else {
										output.html("<center><h4>ไม่พบสินค้าในหมวดหมู่ที่เลือก</h4></center>");
										$(".tab-pane").removeClass("active");
										output.addClass("active");
									}
								}
							});
						}
						output.addClass("active");
					}
				</script>';
	return $sc;

}

//-- this function to view category product in order page
function subCategoryTab($parent, $ajax)
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
				$sc .= '<a id="ul-'.$rs->id_category.'" class="dropdown-toggle" href="#cat-'.$rs->id_category.'" role="tab" data-toggle="tab" onClick="'.$ajax.'('.$rs->id_category.')">';
				$sc .=  $rs->category_name.'</a>';
				$sc .= 	'<ul class="dropdown-menu" role="menu" aria-labelledby="ul-'.$rs->id_category.'">';
				$sc .= 	getSubCategoryTab($rs->id_category, $ajax);
				$sc .=  '</ul>';
				$sc .= '</li>';
			}
			else
			{
				$sc .= '<li class="menu"><a href="#cat-'.$rs->id_category.'" role="tab" data-toggle="tab" onClick="'.$ajax.'('.$rs->id_category.')">'.$rs->category_name.'</a></li>';
			}
			
		}
	}
	return $sc;
}


//-- this function to view category product in order page
function getSubCategoryTab($parent, $ajax)
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
				$sc .= '<a id="ul-'.$rs->id_category.'" class="dropdown-toggle" href="#cat-'.$rs->id_category.'" data-toggle="tab" onClick="'.$ajax.'('.$rs->id_category.')">';
				$sc .=  $rs->category_name.'</a>';
				$sc .= 	'<ul class="dropdown-menu" role="menu" aria-labelledby="ul-'.$rs->id_category.'">';
				$sc .= 	subCategoryTab($rs->id_category, $ajax);
				$sc .=  '</ul>';
				$sc .= '</li>';
			}
			else
			{
				$sc .= '<li class="menu"><a href="#cat-'.$rs->id_category.'" role="tab" data-toggle="tab" onClick="'.$ajax.'('.$rs->id_category.')">'.$rs->category_name.'</a></li>';
			}
			
		}
	}
	return $sc;
}



function getCategoryTab()
{
	$sc = '';
	$qs = dbQuery("SELECT * FROM tbl_category WHERE id_category != 0"); 
	while($rs = dbFetchObject($qs))
	{
		$sc .= '<div class="tab-pane" id="cat-'.$rs->id_category.'"></div>';
	}
	
	return $sc;
}



function getOrderDetail($id_order_detail)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order_detail = ".$id_order_detail);
	if( dbNumRows($qs) == 1 )
	{
		$sc = dbFetchArray($qs);	
	}
	return $sc;
}

function getDiscountAmount($qty, $price, $p_dis, $a_dis)
{
	$discount = $p_dis > 0 ? $price * ( $p_dis * 0.01 ) : $a_dis;
	return  $qty * $discount;
}

function getFinalPrice($price, $p_dis, $a_dis)
{
	$discount = $p_dis > 0 ? $price * ( $p_dis * 0.01 ) : $a_dis;
	return $price - $discount;
}

function getOnlineAddress($id_order)
{
	$sc = FALSE;
	$code = 	getCustomerOnlineReference($id_order);
	if( $code != '' )
	{
		$qs = dbQuery("SELECT * FROM tbl_address_online WHERE customer_code = '".$code."'");
		if( dbNumRows($qs) > 0 )
		{
			$sc = $qs;	
		}
	}
	return $sc;
}

function isAddressExists($code)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT * FROM tbl_address_online WHERE customer_code = '".$code."'");
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;
	}
	return $sc;
}

function getDefaultOnlineAddress($id_order)
{
	$sc = FALSE;
	$code = 	getCustomerOnlineReference($id_order);
	if( $code != '' )
	{
		$qs = dbQuery("SELECT * FROM tbl_address_online WHERE customer_code = '".$code."' AND is_default = 1");
		if( dbNumRows($qs) == 1 )
		{
			$sc = $qs;	
		}
		else if( dbNumRows($qs) == 0 )
		{
			$qr = dbQuery("SELECT * FROM tbl_address_online WHERE customer_code = '".$code."' LIMIT 1");	
			if( dbNumRows($qr) == 1 )
			{
				$sc = $qr;
			}
		}
	}
	return $sc;
}

function onlineOrderByCustomer($txt)
{
	$in = FALSE;
	$qs = dbQuery("SELECT id_order FROM tbl_order_online WHERE customer LIKE '%".$txt."%'");
	$row = dbNumRows($qs);
	if( $row > 0 )
	{
		$in = '';
		$i = 1;
		while($rs = dbFetchArray($qs))
		{
			$in .= $rs['id_order'];
			if( $i != $row ){ $in .= ', '; }
			$i++;	
		}
	}
	return $in;
}

function getSpace($amount, $length)
{
	$sc = '';
	$i	= strlen($amount);
	$m	= $length - $i;
	while($m > 0 )
	{
		$sc .= '&nbsp;';
		$m--;
	}
	return $sc.$amount;
}

function getCustomerOnlineReference($id_order)
{
	$sc = '';
	$qs = dbQuery("SELECT customer FROM tbl_order_online WHERE id_order = ".$id_order);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);	
	}
	return $sc;
}

function onlineCustomerName($id_order)
{
	$sc = 'ไม่ได้กำหนดชื่อลูกค้า';	
	$qs = getDefaultOnlineAddress($id_order);
	if( $qs !== FALSE ) //----- ถ้ามีที่อยู่
	{
		$rs = dbFetchArray($qs);
		$sc = $rs['first_name'] .' '.$rs['last_name'];
	}
	return $sc;
}

function paymentMethod($se = '' )
{
	$options = '<option value="เครดิต" '.isSelected('เครดิต', $se).'>เครดิต</option>';
	$options .= '<option value="เงินสด" '.isSelected('เงินสด', $se).'>เงินสด</option>';
	//$options .= '<option value="ออนไลน์" '.isSelected('ออนไลน์', $se).'>ออนไลน์</option>';
	return $options;		
}
function getIdProductByCode($code)
{
	$id = FALSE;
	$qs = dbQuery("SELECT id_product FROM tbl_product WHERE product_code = '".$code."'");
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$id = $rs['id_product'];
	}
	return $id;
}

function discountLabel($p_dis, $a_dis)
{
	$dis = 0.00;
	if( $p_dis > 0 && $a_dis == 0.00 )
	{
		$dis = $p_dis.' %';	
	}
	if( $a_dis > 0 && $p_dis == 0.00 )
	{
		$dis = number_format($a_dis, 2);
	}
	return $dis;
}

function isSaved($id_order)
{
	$sc = TRUE;
	$qs = dbQuery("SELECT order_status FROM tbl_order WHERE id_order = ".$id_order." AND order_status = 0");
	if( dbNumRows($qs) == 1 )
	{
		$sc = FALSE;
	}
	return $sc;		
}

function orderAmount($id)
{
	$sc = 0;
	$qs = dbQuery("SELECT SUM( total_amount ) AS amount FROM tbl_order_detail WHERE id_order = ".$id);
	list($amount) = dbFetchArray($qs);
	if(	! is_null( $amount ) )
	{
		$sc = $amount;
	}
	return $sc;
}

function qcQty($id_order)
{
	$sc = 0;
	$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_qc WHERE id_order = ".$id_order." AND valid = 1");
	list( $qty ) = dbFetchArray($qs);
	if( ! is_null( $qty ) )
	{
		$sc = $qty;
	}
	return $sc;
}

function orderQty($id_order)
{
	$sc = 0;
	$qs = dbQuery("SELECT SUM(product_qty) FROM tbl_order_detail WHERE id_order = ".$id_order);
	list( $qty ) = dbFetchArray($qs);
	if( ! is_null( $qty ) )
	{
		$sc = $qty;	
	}
	return $sc;
}

function countBox($id_order)
{
	$box = 0;
	$qs = dbQuery("SELECT * FROM tbl_box WHERE id_order = ".$id_order);
	if( dbNumRows($qs) > 0 )
	{
		$box = dbNumRows($qs);
	}
	return $box;
}

function getDeliveryFee($id_order)
{
	$fee = 0;
	$qs = dbQuery("SELECT amount FROM tbl_delivery_fee WHERE id_order = ".$id_order);
	if( dbNumRows($qs) > 0 )
	{
		list( $fee ) = dbFetchArray($qs);
	}
	return $fee;
}


//------------------------------------------------------  ค่าบริการอื่นๆ --------------------------------------//
function getServiceFee($id_order)
{
	$fee = 0;
	$qs = dbQuery("SELECT amount FROM tbl_service_fee WHERE id_order = ".$id_order);
	if( dbNumRows($qs) > 0 )
	{
		list( $fee ) = dbFetchArray($qs);	
	}
	return $fee;
}

function addServiceFee($id_order, $amount)
{
	return dbQuery("INSERT INTO tbl_service_fee (id_order, amount) VALUES (".$id_order.", ".$amount.")");	
}

function updateServiceFee($id_order, $amount)
{
	return dbQuery("UPDATE tbl_service_fee SET amount = ".$amount." WHERE id_order = ".$id_order);	
}

function removeServiceFee($id_order)
{
	$sc = TRUE;
	$qs = dbQuery("DELETE FROM tbl_service_fee WHERE id_order = ".$id_order);
	if( ! $qs ){ $sc = FALSE; }
	return $sc;	
}

//--------------------------------------------------------

function selectHour($se = '')
{
	$sc	= '';
	$hour = array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23');
	foreach($hour as $rs)
	{
		$sc .= '<option value="'.$rs.'" '.isSelected($rs, $se).'>'.$rs.'</option>';
	}
	return $sc;
}



function selectMin($se = '' )
{
	$sc = '<option value="00">00</option>';
	$m = 59;
	$i 	= 1;
	while( $i <= $m )
	{
		$ix = $i < 10 ? '0'.$i : $i;
		$sc .= '<option value="'.$ix.'" '.isSelected($se, $ix).'>'.$ix.'</option>';
		$i++;	
	}
	return $sc;
}


function isPaymentExists($id_order)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT id_payment FROM tbl_payment WHERE id_order = ".$id_order);
	if( dbNumRows($qs) > 0 )
	{
		list( $sc ) = dbFetchArray($qs);	
	}
	return $sc;
}

function validOrder($id_order)
{
	return dbQuery("UPDATE tbl_order SET valid = 1 WHERE id_order = ".$id_order);	
}

function insertOrderState($id_order, $state, $id_emp)
{
	return dbQuery("INSERT INTO tbl_order_state_change ( id_order, id_order_state, id_employee ) VALUES (".$id_order.", ".$state.", ".$id_emp.")");
}

function changeToPrepare($id_order, $id_emp)
{
	$rd = FALSE;
	$qs = dbQuery("UPDATE tbl_order SET current_state = 3 WHERE id_order = ".$id_order);
	if( $qs )
	{
		$rd = insertOrderState($id_order, 3, $id_emp);
	}
	return $rd;
}

function isPaidOrder($id_order)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT valid FROM tbl_order WHERE id_order = ".$id_order);
	if( dbNumRows($qs) == 1 )
	{
		list( $valid ) = dbFetchArray($qs);
		if( $valid == 1 )
		{
			$sc = TRUE;
		}
	}
	return $sc;
}

function orderExpiration()
{
	$days	= getConfig('ORDER_EXPIRATION');
	if( $days > 0 )
	{
		$deadLineDate	= date("Y-m-d", strtotime('-'.$days.' days'));
		$qs = dbQuery("SELECT id_order FROM tbl_order WHERE current_state IN(1,3) AND valid = 0 AND date_add < '".$deadLineDate."'");
		if( dbNumRows($qs) > 0 )
		{
			while( $rs = dbFetchArray($qs) )
			{
				$qr = dbQuery("UPDATE tbl_order_detail SET valid_detail = 2 WHERE id_order = ".$rs['id_order']);
				$qa = dbQuery("UPDATE tbl_order SET valid = 2 WHERE id_order = ".$rs['id_order']);	
			}
		}
	}
	createCookie('expirationCheck', 1, time()+3600*24);
	return TRUE;
}


//----------- ถ้าแจ้งชำระแล้วจะติด label ให้  -----//
function paymentLabel($id_order)
{
	$sc = '';
	$qs = dbQuery("SELECT id_payment FROM tbl_payment WHERE id_order = ".$id_order);
	if( dbNumRows($qs) > 0 )
	{
		if( isPaidOrder($id_order) )
		{
			$sc = '<a href="javascript:void(0)" onClick="viewPaymentDetail('.$id_order.')"><span class="label label-success" style="margin-right:5px;">จ่ายเงินแล้ว : ดูรายละเอียด</span></a>';
		}
		else
		{
			$sc = '<a href="javascript:void(0)" onClick="viewPaymentDetail('.$id_order.')"><span class="label label-primary" style="margin-right:5px;">แจ้งชำระแล้ว : ดูรายละเอียด</span></a>';
		}
	}
	return $sc;
}

function emsLabel($id_order)
{
	$sc = '';
	$qs = dbQuery("SELECT delivery_code FROM tbl_order_online WHERE id_order = ".$id_order);
	if( dbNumRows($qs) > 0 )
	{
		list( $ems ) = dbFetchArray($qs);
		if( ! is_null( $ems ) )
		{
			$sc 	= '<span class="label label-info" style="margin-right:5px;">จัดส่งแล้ว : '.$ems.'</span>';
		}
	}
	return $sc;
}

function closedLabel($id_order)
{
	$sc = '';
	$qs = dbQuery("SELECT valid FROM tbl_order_online WHERE id_order = ".$id_order);
	if( dbNumRows($qs) == 1 )
	{
		list( $valid ) = dbFetchArray($qs);
		if( $valid == 1 )
		{
			$sc = '<span class="label label-warning" style="margin-right:5px;">ปิดออเดอร์แล้ว</span>';
		}
	}
	return $sc;
}

function isDelivered($id_order)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT delivery_code FROM tbl_order_online WHERE id_order = ".$id_order);
	if( dbNumRows($qs) > 0 )
	{
		list( $code ) = dbFetchArray($qs);
		if( ! is_null( $code ) )
		{
			$sc = TRUE;	
		}
	}
	return $sc;
}

function isOrderClosed($id_order)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT valid FROM tbl_order_online WHERE id_order = ".$id_order);
	if( dbNumRows($qs) == 1 )
	{
		list( $valid ) = dbFetchArray($qs);
		if( $valid == 1 )
		{
			$sc = TRUE;
		}
	}
	return $sc;
}

function stateLabel($id_order_state)
{
	$sc = '';
	$qs = dbQuery("SELECT state_name FROM tbl_order_state WHERE id_order_state = ".$id_order_state);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);	
	}
	return $sc;
}


//----- ต้องการรู้ว่าความกว้างของตารางสั่งสินค้า
function getOrderTableWidth($id_pd)
{
	$sc = 800; //--- ชั้นต่ำ
	$tdWidth = 70;  //----- แต่ละช่อง
	$padding = 100; //----- สำหรับช่องแสดงไซส์
	$col = getConfig("ATTRIBUTE_GRID_HORIZONTAL");
	$column = $col == 'color' ? 'id_color' : ( $col == 'size' ? 'id_size' : 'id_attribute');
	$qs 	= dbQuery("SELECT ".$column." FROM tbl_product_attribute WHERE id_product = ".$id_pd." AND ".$column." != 0 GROUP BY ".$column);
	$rs 	= dbNumRows($qs);
	if( $rs > 0 )
	{
		$sc = $rs * $tdWidth + $padding;	
	}
	return $sc;
}

function countAttribute($id_pd)
{
	$sc = 0;
	$qs = dbQuery("SELECT id_color, id_size, id_attribute FROM tbl_product_attribute WHERE id_product = ".$id_pd." LIMIT 1");
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$color = $rs['id_color'] == 0 ? 0 : 1 ;
		$size	= $rs['id_size'] == 0 ? 0 : 1 ;
		$attr	= $rs['id_attribute'] == 0 ? 0 : 1;
		$sc 	= $color + $size + $attr;	
	}
	return $sc;
}

?>