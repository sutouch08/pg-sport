<?php
function getInvoice($id_order)
{
	$sc = "";
	$qs = dbQuery("SELECT invoice FROM tbl_order_invoice WHERE id_order = ".$id_order);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}
function getCategoryMaxDepth()
{
	$sc = 1;
	$qs = dbQuery("SELECT MAX(level_depth) AS max FROM tbl_category LIMIT 1");
	$rs = dbFetchObject($qs);
	if(! is_null($rs->max) )
	{
		$sc = $rs->max;
	}

	return $sc;
}

//-----------------------  ส่งกลับ id_customer ที่คั่นด้วย , เพื่อนำไปใช้กับ query
function customer_in($txt)
{
	$qs = dbQuery("SELECT id_customer FROM tbl_customer WHERE customer_code LIKE '%".$txt."%' OR first_name LIKE '%".$txt."%' OR last_name LIKE '%".$txt."%' OR company LIKE '%".$txt."%' OR company LIKE '%".$txt."%'" );
	$row = dbNumRows($qs);
	if( $row > 0 )
	{
		$in = '';
		$i = 1;
		while($rs = dbFetchArray($qs))
		{
			$in .= $rs['id_customer'];
			if( $i != $row ){ $in .= ', '; }
			$i++;
		}
	}
	else
	{
		$in = FALSE;
	}
	return $in;
}




//-----------------------  ส่งกลับ id_employee ที่คั่นด้วย , เพื่อนำไปใช้กับ query
function employee_in($txt)
{
	$qs = dbQuery("SELECT id_employee FROM tbl_employee WHERE first_name LIKE '%".$txt."%' OR last_name LIKE '%".$txt."%'");
	$row = dbNumRows($qs);
	if( $row > 0 )
	{
		$in = '';
		$i = 1;
		while($rs = dbFetchArray($qs))
		{
			$in .= $rs['id_employee'];
			if( $i != $row ){ $in .= ', '; }
			$i++;
		}
	}
	else
	{
		$in = FALSE;
	}
	return $in;
}




//-------------------------  สร้าง cookie
function createCookie($name, $value, $time = 3600, $path = '/')
{
	return setcookie($name, $value, time()+$time, $path);
}




//----------------------------  ลบ cookie
function deleteCookie($name, $path = '/')
{
	return setcookie($name, '', time()-3600, $path);
}




//--------------------------  ใช้งาน cookie
function getCookie($name)
{
	if( isset( $_COOKIE[$name] ) )
	{
		return $_COOKIE[$name];
	}
	else
	{
		return FALSE;
	}
}



//-------------------------  ส่งกลับ id_profile
function getProfile($id_employee)
{
	$id_profile = FALSE;
	$qs = dbQuery("SELECT id_profile FROM tbl_employee WHERE id_employee = ".$id_employee);
	if( dbNumRows($qs) == 1 )
	{
		list($id_profile) = dbFetchArray($qs);
	}
	return $id_profile;
}




function setError($name, $value)
{
	session_start();
     $_SESSION[$name] = $value;
     session_write_close();
}





function getError($name)
{
	 session_start();
     $var = isset( $_SESSION[$name] ) ? $_SESSION[$name] : FALSE;
     session_write_close();
	 unset($_SESSION[$name]);
     return $var;
}





function checkError()
{
	session_start();
	foreach( array('error', 'warning', 'message') as $name)
	{
     $var = isset( $_SESSION[$name] ) ? $_SESSION[$name] : FALSE;
	 if( $var !== FALSE )
	 {
		 echo '<input type="hidden" id="'.$name.'" value="'.$var.'" />';
	 }
	 unset($_SESSION[$name]);
	}
	session_write_close();
}




function get_order_remark($id_order)
{
	$rs = "";
	$qs = dbQuery("SELECT comment FROM tbl_order WHERE id_order = ".$id_order);
	if( dbNumRows($qs) == 1 )
	{
		list($rs) = dbFetchArray($qs);
	}
	return $rs;
}



//-----------------------  AC format
function ac_format($number)
{
	if($number == 0)
	{
		$number = "-";
	}
	return $number;
}




function number($number, $digit=0)
{
	return number_format($number, $digit);
}



function get_id_product_attribute_by_barcode($barcode)
{
	$id_pa = 0;
	$qs = dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE barcode = '".$barcode."'");
	if( dbNumRows($qs) == 1 )
	{
		list($id_pa) = dbFetchArray($qs);
	}
	return $id_pa;
}





function get_id_product($id_pa)
{
	$id_pd = 0;
	$qs = dbQuery("SELECT id_product FROM tbl_product_attribute WHERE id_product_attribute = ".$id_pa);
	if( dbNumRows($qs) == 1 )
	{
		list( $id_pd ) = dbFetchArray($qs);
	}
	return $id_pd;
}






function get_id_product_by_barcode($barcode)
{
	$id_pr = 0;
	$qs = dbQuery("SELECT id_product FROM tbl_product_attribute WHERE barcode = '".$barcode."'");
	if( dbNumRows($qs) == 1 )
	{
		list($id_pr) = dbFetchArray($qs);
	}
	return $id_pr;
}






function bill_discount($id_order)
{
	$discount = 0;
	$qs = dbQuery("SELECT discount_amount FROM tbl_order_discount WHERE id_order = ".$id_order);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$discount = $rs['discount_amount'];
	}
	return $discount;
}





function drop_zero()
{
	return dbQuery("DELETE FROM tbl_stock WHERE qty = 0");
}





function root_category_tree($id_cat=""){
		if($id_cat ==""){ $id_cat =0; }
		list($parent_id) =dbFetchArray(dbQuery("SELECT parent_id FROM tbl_category WHERE id_category = $id_cat"));
		echo" <ul class='tree'>";
		$qr=dbQuery("SELECT id_category, category_name FROM tbl_category WHERE level_depth = 0 ORDER BY category_name ASC");
		$row = dbNumRows($qr);
		$i = 0;
		while($i<$row){
			$category = dbFetchArray($qr);
			$id_category = $category['id_category'];
			$category_name = $category['category_name'];
			echo "<li><input type='radio' name='root_category'  value='$id_category' id='$category_name' "; if($parent_id==$id_category){ echo" checked='checked'";} echo"/><label for='$category_name'>&nbsp;&nbsp;  $category_name</label>";
				$q1 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category AND level_depth = 1 AND id_category != $id_cat ORDER BY category_name ASC");
				$r1 = dbNumRows($q1);
				$n1 = 0;
				if($r1>0){ echo"<ul>";}
				while($n1<$r1){
					$sub_cat1 = dbFetchArray($q1);
					$id_sub1 = $sub_cat1['id_category'];
					$sub_name1 = $sub_cat1['category_name'];
					echo "<li><input type='radio' name='root_category'  value='$id_sub1' id='$sub_name1' "; if($parent_id==$id_sub1){ echo" checked='checked'";} echo"/><label for='$sub_name1'>&nbsp;&nbsp;  $sub_name1</label>";
					$q2 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_sub1 AND level_depth = 2 AND id_category != $id_cat ORDER BY category_name ASC");
					$r2 = dbNumRows($q2);
					$n2 = 0;
					if($r2>0){ echo"<ul>";}
						while($n2<$r2){
							$sub_cat2 = dbFetchArray($q2);
							$id_sub2 = $sub_cat2['id_category'];
							$sub_name2 = $sub_cat2['category_name'];
							echo "<li><input type='radio' name='root_category'  value='$id_sub2' id='$sub_name2' "; if($parent_id==$id_sub2){ echo" checked='checked'";} echo"/><label for='$sub_name2'>&nbsp;&nbsp;  $sub_name2</label>";
							$q3 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_sub2 AND level_depth = 3 AND id_category != $id_cat  ORDER BY category_name ASC");
							$r3 = dbNumRows($q3);
							$n3 = 0;
							if($r3>0){ echo "<ul>"; }
								while($n3<$r3){
									$sub_cat3 = dbFetchArray($q3);
									$id_sub3 = $sub_cat3['id_category'];
									$sub_name3 = $sub_cat3['category_name'];
									echo "<li><input type='radio' name='root_category'  value='$id_sub3' id='$sub_name3'"; if($parent_id==$id_sub3){ echo" checked='checked'";} echo" /><label for='$sub_name3'>&nbsp;&nbsp;  $sub_name3</label>";
									$q4 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_sub3 AND level_depth = 4 AND id_category != $id_cat ORDER BY category_name ASC");
									$r4 = dbNumRows($q4);
									$n4 = 0;
									if($r4>0){ echo "<ul>"; }
										while($n4<$r4){
											$sub_cat4 = dbFetchArray($q4);
											$id_sub4 = $sub_cat4['id_category'];
											$sub_name4 = $sub_cat4['category_name'];
											echo "<li><input type='radio' name='root_category'  value='$id_sub4' id='$sub_name4' "; if($parent_id==$id_sub4){ echo" checked='checked'";} echo" /><label for='$sub_name4'>&nbsp;&nbsp;  $sub_name4</label></li>";
											$n4++;
										}
									if($r4>0){ echo "</ul>";}
									$n3++;
								}
								if($r3>0){ echo "</ul>";} echo"</li>";
							$n2++;
						}
						if($r2>0){ echo "</ul>";} echo"</li>";
					$n1++;
				}
				if($r1>0){ echo "</ul>";} echo"</li>";
			$i++;
		}
		echo"</ul>";
}







function category_tree($id_product=0){///checkbox
		echo" <ul class='tree'>";
		$qr=dbQuery("SELECT id_category, category_name FROM tbl_category WHERE level_depth = 0 ORDER BY category_name ASC");
		$row = dbNumRows($qr);
		$i = 0;
		while($i<$row){
			$category = dbFetchArray($qr);
			$id_category = $category['id_category'];
			$category_name = $category['category_name'];
			$check =dbNumRows(dbQuery("SELECT * FROM tbl_category_product WHERE id_category=$id_category AND id_product=$id_product"));
			echo "<li><input type='checkbox' name='category_id[]' value='$id_category' id='$category_name' "; if($check==1){ echo" checked='checked'";} echo"/><label for='$category_name'>&nbsp;&nbsp;  $category_name</label>";
				$q1 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category AND level_depth = 1 ORDER BY category_name ASC");
				$r1 = dbNumRows($q1);
				$n1 = 0;
				if($r1>0){ echo"<ul>";}
				while($n1<$r1){
					$sub_cat1 = dbFetchArray($q1);
					$id_sub1 = $sub_cat1['id_category'];
					$sub_name1 = $sub_cat1['category_name'];
					$check1 =dbNumRows(dbQuery("SELECT * FROM tbl_category_product WHERE id_category=$id_sub1 AND id_product=$id_product"));
					echo "<li><input type='checkbox' name='category_id[]' value='$id_sub1' id='$sub_name1' "; if($check1==1){ echo" checked='checked'";} echo"/><label for='$sub_name1'>&nbsp;&nbsp;  $sub_name1</label>";
					$q2 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_sub1 AND level_depth = 2 ORDER BY category_name ASC");
					$r2 = dbNumRows($q2);
					$n2 = 0;
					if($r2>0){ echo"<ul>";}
						while($n2<$r2){
							$sub_cat2 = dbFetchArray($q2);
							$id_sub2 = $sub_cat2['id_category'];
							$sub_name2 = $sub_cat2['category_name'];
							$check2 =dbNumRows(dbQuery("SELECT * FROM tbl_category_product WHERE id_category=$id_sub2 AND id_product=$id_product"));
							echo "<li><input type='checkbox' name='category_id[]' value='$id_sub2' id='$sub_name2' "; if($check2==1){ echo" checked='checked'";} echo"/><label for='$sub_name2'>&nbsp;&nbsp;  $sub_name2</label>";
							$q3 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_sub2 AND level_depth = 3 ORDER BY category_name ASC");
							$r3 = dbNumRows($q3);
							$n3 = 0;
							if($r3>0){ echo "<ul>"; }
								while($n3<$r3){
									$sub_cat3 = dbFetchArray($q3);
									$id_sub3 = $sub_cat3['id_category'];
									$sub_name3 = $sub_cat3['category_name'];
									$check3 =dbNumRows(dbQuery("SELECT * FROM tbl_category_product WHERE id_category=$id_sub3 AND id_product=$id_product"));
									echo "<li><input type='checkbox' name='category_id[]' value='$id_sub3' id='$sub_name3'"; if($check3==1){ echo" checked='checked'";} echo" /><label for='$sub_name3'>&nbsp;&nbsp;  $sub_name3</label>";
									$q4 = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_sub3 AND level_depth = 4 ORDER BY category_name ASC");
									$r4 = dbNumRows($q4);
									$n4 = 0;
									if($r4>0){ echo "<ul>"; }
										while($n4<$r4){
											$sub_cat4 = dbFetchArray($q4);
											$id_sub4 = $sub_cat4['id_category'];
											$sub_name4 = $sub_cat4['category_name'];
											$check4 =dbNumRows(dbQuery("SELECT * FROM tbl_category_product WHERE id_category=$id_sub4 AND id_product=$id_product"));
											echo "<li><input type='checkbox' name='category_id[]' value='$id_sub4' id='$sub_name4' "; if($check4==1){ echo" checked='checked'";} echo" /><label for='$sub_name4'>&nbsp;&nbsp;  $sub_name4</label></li>";
											$n4++;
										}
									if($r4>0){ echo "</ul>";}
									$n3++;
								}
								if($r3>0){ echo "</ul>";} echo"</li>";
							$n2++;
						}
						if($r2>0){ echo "</ul>";} echo"</li>";
					$n1++;
				}
				if($r1>0){ echo "</ul>";} echo"</li>";
			$i++;
		}
			echo "</ul>";
		}





function categoryList($se='')
{
	$qs = dbQuery("SELECT * FROM tbl_category");
	$option = '';
	while( $rs = dbFetchArray($qs) )
	{
		$option .= '<option value="'.$rs['id_category'].'" '.isSelected($rs['id_category'], $se).'>'.$rs['category_name'].'</option>';
	}
	return $option;
}


function category_list($selected =""){
	$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category !=0 ORDER BY category_name ASC");
	$option = "<option value='0'>------ทั้งหมด---------</option>";
	while($rs = dbFetchArray($sql)){
		$id_category = $rs['id_category'];
		$category_name = $rs['category_name'];
		if($selected == $id_category){ $select = "selected='selected'"; }else{ $select = ""; }
		$option .="<option value='$id_category' $select >$category_name</option>";
	}
	return $option;
}






	function get_product_list($selected=0){
		$sql = dbQuery("SELECT id_product, product_code, product_name FROM tbl_product");
		echo"<option value='0'>---เลือกรายการสินค้า---</option>";
			while($r=dbFetchArray($sql)){
				$id = $r['id_product'];
				$product_code = $r['product_code'];
				$product_name = $r['product_name'];
				if($selected == $id){ $select ="selected='selected'"; }else{ $select = ""; }
				echo"<option value='$id' $select>$product_code : $product_name</option>";
			}
		}





function getViewList($view,$from_date)
{
	if($from_date != "เลือกวัน"){$view = "";}
	echo "<option value='0' "; if($view==""){echo "selected='selected'";} echo ">เลือกการแสดงผล</option>";
	echo "<option value='week' "; if($view == "week"){echo "selected='selected'";} echo ">แสดงเป็นสัปดาห์</option>";
	echo "<option value='month' "; if($view == "month"){echo "selected='selected'";} echo ">แสดงเป็นเดือน</option>";
	echo "<option value='year' "; if($view == 'year'){echo "selected='selected'";} echo ">แสดงเป็นปี</option>";
}






function get_view_list($view=0)
{
	echo "<option value='0' "; if($view=="0"){echo "selected='selected'";} echo ">เลือกการแสดงผล</option>";
	echo "<option value='week' "; if($view == "week"){echo "selected='selected'";} echo ">แสดงเป็นสัปดาห์</option>";
	echo "<option value='month' "; if($view == "month"){echo "selected='selected'";} echo ">แสดงเป็นเดือน</option>";
	echo "<option value='year' "; if($view == 'year'){echo "selected='selected'";} echo ">แสดงเป็นปี</option>";
}






function getWeek($today){
	$day = date("l",strtotime("$today"));
	$from_date ='';
	$to_date = '';
	switch ($day){
		case 'Monday':
		$from_date = $today;
		$to_date = date('Y-m-d',strtotime("+6 day",strtotime("$today")));
		break;
		case 'Tuesday' :
		$from_date = date('Y-m-d',strtotime("-1 day",strtotime("$today")));
		$to_date = date('Y-m-d',strtotime("+5 day",strtotime("$today")));
		break;
		case 'Wednesday' :
		$from_date = date('Y-m-d',strtotime("-2 day",strtotime("$today")));
		$to_date = date('Y-m-d',strtotime("+4 day",strtotime("$today")));
		break;
		case 'Thursday' :
		$from_date = date('Y-m-d',strtotime("-3 day",strtotime("$today")));
		$to_date = date('Y-m-d',strtotime("+3 day",strtotime("$today")));
		break;
		case 'Friday' :
		$from_date = date('Y-m-d',strtotime("-4 day",strtotime("$today")));
		$to_date = date('Y-m-d',strtotime("+2 day",strtotime("$today")));
		break;
		case 'Saturday' :
		$from_date = date('Y-m-d',strtotime("-5 day",strtotime("$today")));
		$to_date = date('Y-m-d',strtotime("+1 day",strtotime("$today")));
		break;
		case 'Sunday' :
		$from_date = date('Y-m-d',strtotime("-6 day",strtotime("$today")));
		$to_date =  $today;
		break;
		default :
		$from_date = $today;
		$to_date = date('Y-m-d',strtotime("+6 day",strtotime("$today")));
		break;

	}
	$array["from"] =$from_date;
	$array["to"] = $to_date;
	return $array;
}






function DateDiff($strDate1,$strDate2)
{
	return (strtotime($strDate2) - strtotime($strDate1))/  ( 60 * 60 * 24 );  // 1 day = 60*60*24
 }






function getMonth($date=""){
	if($date ==""){
	$array["from"] = date('Y-m-01',strtotime('this month'));
	$array["to"] = date('Y-m-t',strtotime('this month'));
	}else{
	$d = date("m",strtotime($date));
	$y = date("Y",strtotime($date));
	$month = shortMonthName($d);
	$array['from'] = date($y.'-m-01',strtotime($month));
	$array['to'] = date($y.'-m-t',strtotime($month));
	}
	return $array;
}





function date_in_month($month,$year){
	$first = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
    $last = date('Y-m-t', mktime(0, 0, 0, $month, 1, $year));
	$thisTime = strtotime($first);
   	$endTime = strtotime($last);
	$date = array();
   while($thisTime <= $endTime)
   {
   		$thisDate = date('Y-m-d', $thisTime);
   		array_push($date,$thisDate);
 		$thisTime = strtotime('+1 day', $thisTime); // increment for loop
   }
   return $date;
}





function getYear($date=""){
	if($date ==""){
	$array["from"] = date('Y-01-01',strtotime('this year'));
	$array["to"] = date('Y-12-31',strtotime('this year'));
	}else{
	$y = date("Y", strtotime($date));
	$array['from'] = date($y.'-01-01', strtotime($y));
	$array['to'] = date($y.'-12-31', strtotime($y));
	}
	return $array;
}





function getMonthName()
{
	$date = date("m",strtotime("this month"));
	switch($date){
		case "01" :
		$month = "มกราคม";
		break;
		case "02" :
		$month = "กุมภาพันธ์";
		break;
		case "03" :
		$month = "มีนาคม";
		break;
		case "04" :
		$month = "เมษายน";
		break;
		case "05" :
		$month = "พฤษภาคม";
		break;
		case "06" :
		$month = "มิถุนายน";
		break;
		case "07" :
		$month = "กรกฎาคม";
		break;
		case "08" :
		$month = "สิงหาคม";
		break;
		case "09" :
		$month = "กันยายน";
		break;
		case "10" :
		$month = "ตุลาคม";
		break;
		case "11" :
		$month = "พฤษจิกายน";
		break;
		case "12" :
		$month = "ธันวาคม";
		break;
		default :
		$month = "เดือนไม่ถูกต้อง";
		break;
	}
	return $month;
}






function MonthName($no)
{
	switch($no){
		case "1" :
		$month = "January";
		break;
		case "2" :
		$month = "February";
		break;
		case "3" :
		$month = "March";
		break;
		case "4" :
		$month = "April";
		break;
		case "5" :
		$month = "May";
		break;
		case "6" :
		$month = "June";
		break;
		case "7" :
		$month = "July";
		break;
		case "8" :
		$month = "August";
		break;
		case "9" :
		$month = "September";
		break;
		case "10" :
		$month = "October";
		break;
		case "11" :
		$month = " November";
		break;
		case "12" :
		$month = "December";
		break;
		default :
		$month = "เดือนไม่ถูกต้อง";
		break;
	}
	return $month;
}





function shortMonthName($no)
{
	switch($no){
		case "1" :
		$month = "Jan";
		break;
		case "2" :
		$month = "Feb";
		break;
		case "3" :
		$month = "Mar";
		break;
		case "4" :
		$month = "Apr";
		break;
		case "5" :
		$month = "May";
		break;
		case "6" :
		$month = "Jun";
		break;
		case "7" :
		$month = "Jul";
		break;
		case "8" :
		$month = "Aug";
		break;
		case "9" :
		$month = "Sep";
		break;
		case "10" :
		$month = "Oct";
		break;
		case "11" :
		$month = " Nov";
		break;
		case "12" :
		$month = "Dec";
		break;
		default :
		$month = "เดือนไม่ถูกต้อง";
		break;
	}
	return $month;
}





function getThaiMonthName($date="")
{
	if($date==""){ $day = date("m",strtotime("this month")); }else{ $day = date("m", strtotime($date)); }
	switch($day){
		case "01" :
		$month = "มกราคม";
		break;
		case "02" :
		$month = "กุมภาพันธ์";
		break;
		case "03" :
		$month = "มีนาคม";
		break;
		case "04" :
		$month = "เมษายน";
		break;
		case "05" :
		$month = "พฤษภาคม";
		break;
		case "06" :
		$month = "มิถุนายน";
		break;
		case "07" :
		$month = "กรกฎาคม";
		break;
		case "08" :
		$month = "สิงหาคม";
		break;
		case "09" :
		$month = "กันยายน";
		break;
		case "10" :
		$month = "ตุลาคม";
		break;
		case "11" :
		$month = "พฤษจิกายน";
		break;
		case "12" :
		$month = "ธันวาคม";
		break;
		default :
		$month = "เดือนไม่ถูกต้อง";
		break;
	}
	return $month;
}






function thaiDate($date='', $d="-"){
	if( $date == '' )
	{
		$date = date('Y-m-d');
	}
	return date("d".$d."m".$d."Y",strtotime($date));
}





function thaiDateTime($date='')
{
	if( $date == '' )
	{
		$date = date('Y-m-d');
	}
	return date("d-m-Y H:i:s", strtotime($date));
}





function thaiTextDate($datetime) {
	$date=date('Y-m-d',strtotime($datetime));
	list($Y,$m,$d) = explode('-',$date); // แยกวันเป็น ปี เดือน วัน

	$Y = $Y+543; // เปลี่ยน ค.ศ. เป็น พ.ศ.
	switch($m) {
		case "01": $m = "ม.ค."; break;
		case "02": $m = "ก.พ."; break;
		case "03": $m = "มี.ค."; break;
		case "04": $m = "เม.ย."; break;
		case "05": $m = "พ.ค."; break;
		case "06": $m = "มิ.ย."; break;
		case "07": $m = "ก.ค."; break;
		case "08": $m = "ส.ค."; break;
		case "09": $m = "ก.ย."; break;
		case "10": $m = "ต.ค."; break;
		case "11": $m = "พ.ย."; break;
		case "12": $m = "ธ.ค."; break;
	}
		return $d." ".$m." ".$Y;
}







function recievedTable($view="",$from ="", $to =""){
	if($view=="week"){
		$sql = dbQuery("SELECT id_recieved_product, recieved_product_no, reference_no, date, id_employee, status FROM tbl_recieved_product ORDER BY date DESC LIMIT 50");
	}else{
	if($from==""){ $from= date('Y-m-d');}
	if($to ==""){ $to = date("Y-m-d");}
	$sql = dbQuery("SELECT id_recieved_product, recieved_product_no, reference_no, date, id_employee, status FROM tbl_recieved_product WHERE date BETWEEN '$from' AND '$to'");
	}
	return $sql;
}







function return_order_table($view="",$from="", $to=""){
			if($view =="month"){
				$sql = dbQuery("SELECT id_return_order, reference, id_customer, id_employee, date_add, status FROM tbl_return_order ORDER BY id_return_order DESC LIMIT 50");
			}else{
				if($from==""){ $from= date('Y-m-d');}else{ $from = dbDate($from); }
				if($to ==""){ $to = date("Y-m-d");}else{ $to = dbDate($to); }
				$sql = dbQuery("SELECT id_return_order, reference, id_customer, id_employee, date_add, status FROM tbl_return_order WHERE date_add BETWEEN '$from' AND '$to' ORDER BY id_return_order DESC");
			}
			return $sql;
	}





function newRecievedNO(){
	$y= date('y');
	$m= date('m');
	$run_no = "0001";
	list($id, $recieved_no) = dbFetchArray(dbQuery("SELECT id_recieved_product, recieved_product_no FROM tbl_recieved_product WHERE  id_recieved_product= (SELECT max(id_recieved_product) FROM tbl_recieved_product)"));
	$pre_fix = "RE-";
	$start = 7;
	$end = 7;
	$str1 = substr_unicode($recieved_no, $start, 11)+1;
	$str2 = substr_unicode($recieved_no, 0,$end);
	if($str2=="$pre_fix$y$m"){
		$result = $pre_fix.$y.$m.sprintf("%04d",$str1);
	}else{
		$result = $pre_fix.$y.$m.$run_no;
	}
	return $result;
}

function getRecievedNO($id_recieved_product){
	list($recieved_no) = dbFetchArray(dbQuery("SELECT recieved_product_no FROM tbl_recieved_product WHERE id_recieved_product='$id_recieved_product' "));
	return $recieved_no;
}





function getRecievedReference($id){
	list($reference)= dbFetchArray(dbQuery("SELECT reference_no FROM tbl_recieved_product WHERE id_recieved_product='$id' "));
	return $reference;
}





function getRecievedDate($id){
	list($recieved_date)= dbFetchArray(dbQuery("SELECT date FROM tbl_recieved_product WHERE id_recieved_product='$id' "));
	return $recieved_date;
}





function getReturnNO($id_return_order){
	list($return_no) = dbFetchArray(dbQuery("SELECT reference FROM tbl_return_order WHERE id_return_order='$id_return_order' "));
	return $return_no;
}





function getReturnDate($id){
	list($recieved_date)= dbFetchArray(dbQuery("SELECT date_add FROM tbl_return_order WHERE id_return_order='$id' "));
	return $recieved_date;
}






//// หาค่าสูงสุดของ reference ของแต่ละ role
function get_max_role_reference($config_name, $role, $date=""){
		list($prefix) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = '$config_name'"));
		if($date ==""){ $date = date("Y-m-d"); }
		$sumtdate = date("y", strtotime($date));
		$m = date("m", strtotime($date));
		if($role == 2 || $role == 6){
			$sql="SELECT  MAX(reference) AS max FROM tbl_order WHERE role IN(2,6) AND reference LIKE '%$prefix-$sumtdate$m%' ORDER BY  reference DESC";
		}else{
			$sql="SELECT  MAX(reference) AS max FROM tbl_order WHERE role=$role AND reference LIKE '%$prefix-$sumtdate$m%' ORDER BY  reference DESC";
		}
		$Qtotal = dbQuery($sql);
		$rs=dbFetchArray($Qtotal);
		$num = "00001";
		$str = $rs['max'];
		$s = 7; // start from "0" (nth) char
		$l = 7; // get "3" chars
		$str2 = substr_unicode($str, $s ,5)+1;
		$str1 = substr_unicode($str, 0 ,$l);
		if($str1=="$prefix-$sumtdate$m"){
		$reference_no = "$prefix-$sumtdate$m".sprintf("%05d",$str2)."";
		}else{
		$reference_no = "$prefix-$sumtdate$m$num";
		}

		return $reference_no;
}



function get_max_po_reference($date = "")
{
	$prefix = getConfig("PREFIX_PO");
	if($date == ''){ $date = date("Y-m-d"); }
	$year = date("y", strtotime($date));
	$month = date("m", strtotime($date));
	$qs = dbQuery("SELECT MAX(reference) AS reference FROM tbl_po WHERE reference LIKE '%".$prefix."-".$year.$month."%'");
	$rs = dbFetchArray($qs);
	$str = $rs['reference'];
	if($str !="")
	{
		$ra = explode('-', $str, 2);
		$num = $ra[1];
		$run_num = $num + 1;
		$reference = $prefix."-".$run_num;
	}else{
		$reference = $prefix."-".$year.$month."00001";
	}
	return $reference;
}







function get_max_return_sponsor_reference($date = '')
{
	$date 	= $date == '' ? date("Y-m-d") : $date;
	$Y			= date('y', strtotime($date));
	$M			= date('m', strtotime($date));
	$prefix	= getConfig("PREFIX_RETURN_SPONSOR");
	$preRef	= $prefix . '-' . $Y . $M;
	$qs		= dbQuery("SELECT MAX(reference) AS max FROM tbl_return_sponsor WHERE reference LIKE '".$preRef."%' ORDER BY reference DESC");
	list( $ref ) = dbFetchArray($qs);
	if( ! is_null($ref) )
	{
		$runNo = mb_substr($ref, -4, NULL, 'UTF-8')+1;
		$ref = $prefix . '-' . $Y . $M . sprintf('%04d', $runNo);
	}
	else
	{
		$ref = $prefix . '-' . $Y . $M	. '0001';
	}

	return $ref;
}






function get_max_return_support_reference($date = '')
{
	$date 	= $date == '' ? date("Y-m-d") : $date;
	$Y			= date('y', strtotime($date));
	$M			= date('m', strtotime($date));
	$prefix	= getConfig("PREFIX_RETURN_SUPPORT");
	$preRef	= $prefix . '-' . $Y . $M;
	$qs		= dbQuery("SELECT MAX(reference) AS max FROM tbl_return_support WHERE reference LIKE '".$preRef."%' ORDER BY reference DESC");
	list( $ref ) = dbFetchArray($qs);
	if( ! is_null($ref) )
	{
		$runNo = mb_substr($ref, -4, NULL, 'UTF-8')+1;
		$ref = $prefix . '-' . $Y . $M . sprintf('%04d', $runNo);
	}
	else
	{
		$ref = $prefix . '-' . $Y . $M	. '0001';
	}

	return $ref;
}





function get_max_return_reference($date = '')
{
	$prefix = getConfig("PREFIX_RETURN");
	if($date == ''){ $date = date("Y-m-d"); }
	$year = date("y", strtotime($date));
	$month = date("m", strtotime($date));
	$qs = dbQuery("SELECT MAX(reference) AS reference FROM tbl_return_order WHERE reference LIKE '%".$prefix."-".$year.$month."%'");
	$rs = dbFetchArray($qs);
	$str = $rs['reference'];
	if($str !="")
	{
		$ra = explode('-', $str, 2);
		$num = $ra[1];
		$run_num = $num + 1;
		$reference = $prefix."-".$run_num;
	}else{
		$reference = $prefix."-".$year.$month."00001";
	}
	return $reference;
}







function get_max_role_reference_consign($config_name, $role, $date=""){
		$prefix = getConfig($config_name);
		if($date ==""){ $date = date("Y-m-d"); }
		$sumtdate = date("y", strtotime($date));
		$m = date("m", strtotime($date));
		$sql="SELECT  MAX(reference) AS max FROM tbl_order_consign WHERE reference LIKE '%$prefix-$sumtdate$m%' ORDER BY  reference DESC";
		$Qtotal = dbQuery($sql);
		$rs=dbFetchArray($Qtotal);
		$num = "00001";
		$str = $rs['max'];
		$s = 7; // start from "0" (nth) char
		$l = 7; // get "3" chars
		$str2 = substr_unicode($str, $s ,5)+1;
		$str1 = substr_unicode($str, 0 ,$l);
		if($str1=="$prefix-$sumtdate$m"){
		$reference_no = "$prefix-$sumtdate$m".sprintf("%05d",$str2)."";
		}else{
		$reference_no = "$prefix-$sumtdate$m$num";
		}

		return $reference_no;
}





function get_max_role_reference_consign_check($config_name, $role=""){
		list($prefix) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = '$config_name'"));
		$sql="SELECT  MAX(reference) AS max FROM tbl_consign_check ORDER BY  reference DESC";
		$Qtotal = dbQuery($sql);
		$rs=dbFetchArray($Qtotal);
		$sumtdate = date("y");
		$m = date("m");
		$num = "00001";
		$str = $rs['max'];
		$s = 7; // start from "0" (nth) char
		$l = 7; // get "3" chars
		$str2 = substr_unicode($str, $s ,5)+1;
		$str1 = substr_unicode($str, 0 ,$l);
		if($str1=="$prefix-$sumtdate$m"){
		$reference_no = "$prefix-$sumtdate$m".sprintf("%05d",$str2)."";
		}else{
		$reference_no = "$prefix-$sumtdate$m$num";
		}

		return $reference_no;
}





function get_max_request_reference($config_name){
		list($prefix) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = '$config_name'"));
		$sql="SELECT  MAX(reference) AS max FROM tbl_request_order ORDER BY  reference DESC";
		$Qtotal = dbQuery($sql);
		$rs=dbFetchArray($Qtotal);
		$sumtdate = date("y");
		$m = date("m");
		$num = "00001";
		$str = $rs['max'];
		$s = 7; // start from "0" (nth) char
		$l = 7; // get "3" chars
		$str2 = substr_unicode($str, $s ,5)+1;
		$str1 = substr_unicode($str, 0 ,$l);
		if($str1=="$prefix-$sumtdate$m"){
		$reference_no = "$prefix-$sumtdate$m".sprintf("%05d",$str2)."";
		}else{
		$reference_no = "$prefix-$sumtdate$m$num";
		}

		return $reference_no;
}





function get_max_role_reference_tranfer($config_name){
		list($prefix) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = '$config_name'"));
		$sql="SELECT  MAX(reference) AS max FROM tbl_tranfer ORDER BY  reference DESC";
		$Qtotal = dbQuery($sql);
		$rs=dbFetchArray($Qtotal);
		$sumtdate = date("y");
		$m = date("m");
		$num = "00001";
		$str = $rs['max'];
		$s = 7; // start from "0" (nth) char
		$l = 7; // get "3" chars
		$str2 = substr_unicode($str, $s ,5)+1;
		$str1 = substr_unicode($str, 0 ,$l);
		if($str1=="$prefix-$sumtdate$m"){
		$reference_no = "$prefix-$sumtdate$m".sprintf("%05d",$str2)."";
		}else{
		$reference_no = "$prefix-$sumtdate$m$num";
		}

		return $reference_no;
}






//************************************* รายการสินค้าที่ยิงแล้วแต่ยังไม่บันทึกยอดสต็อก ********************************************///
function recievedDetail($id){
	$sql = dbQuery("SELECT * FROM recieved_detail_table WHERE id_recieved_product = $id ORDER BY id_detail DESC");
	$row = dbNumRows($sql);
	$total = 0;
	$total_row = "";
	$table = "";
	$i=0;
	$n=$row;
	if($row>0){
	while($i<$row){
	list($id_detail, $id_recieved, $reference, $qty, $warehouse, $zone, $date, $employee, $status) = dbFetchArray($sql);
	$table .="<tr><td align='center'>$n</td><td>$reference</td><td align='center'>$qty</td><td align='center'>$warehouse</td><td align='center'>$zone</td><td align='center'>$date</td><td align='center'>$employee</td><td align='center'>"; if($status==1){ $table .="<a href='controller/storeController.php?delete_stocked=y&id_recieved_detail=$id_detail'>";}else if($status==0){ $table .="<a href='controller/storeController.php?delete=y&id_recieved_detail=$id_detail'>";} $table .="<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $reference ');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a></td></tr>";
	$total = $total +$qty;
	$i++;
	$n--;
	}
	$total_row .="<tr><td colspan='8' align='center'><h4>รวม $total หน่วย </h4></td></tr>";
	$total_row .= $table;
	echo $total_row;
}	else{
		echo"<tr><td colspan='8' align='center'><h3>ไม่มีรายการ</h3></td></tr>";
}
}





//*******************************************************  รายการสินค้าที่บันทึกยอดสต็อกแล้ว  ***************************************************************//
function getRecievedDetail($id){
	$sql = dbQuery("SELECT * FROM recieved_detail_table WHERE id_recieved_product = $id AND status =1");
	$row = dbNumRows($sql);
	$i=0;
	$n=1;
	if($row>0){
	while($i<$row){
	list($id_detail, $id_recieved, $reference, $qty, $warehouse, $zone, $date, $employee, $status) = dbFetchArray($sql);
	echo"<tr><td align='center'>$n</td><td>$reference</td><td align='center'>"; if($qty==NULL){ echo"0";}else{ echo $qty; } echo"</td><td align='center'>$warehouse</td><td align='center'>$zone</td><td align='center'>"; echo thaiDate($date); echo"</td><td align='center'>$employee</td></tr>";
	$i++;
	$n++;
	}
}else{
		echo"<tr><td colspan='8' align='center'><h3>ไม่มีรายการ</h3></td></tr>";
	}
}






function getLastDays($days){ /// คืนค่าวันที่เริ่มต้น และ สิ้นสุด ย้อนหลังตามจำนวนวันที่ต้องการ
	$today = date('Y-m-d', strtotime("+1 day",strtotime(date('Y-m-d'))));
	$from_date = date('Y-m-d', strtotime("-$days day",strtotime("$today")));
	$to_date = $today;
	$arr['from'] = $from_date;
	$arr['to'] = $to_date;
	return $arr;
}






function getLastMonth(){
	$last_month = array();
	$last_month['start'] = date("Y-m-01", strtotime("-1month"));
	$last_month['end'] = date("Y-m-t", strtotime("-1month"));
	return $last_month;
}





function getOrderTable($view="",$from ="", $to ="", $Page_Start="",$Per_Page="",$role = "1,4"){
	if($view !=""){
			$date = getLastDays($view);
			$from = $date['from'];
			$to = $date['to'];
		}
	return dbQuery("SELECT id_order,reference,id_customer,id_employee,payment,tbl_order.date_add,current_state,tbl_order.date_upd FROM tbl_order WHERE (tbl_order.date_add BETWEEN '$from' AND '$to') AND current_state !=1 AND role IN($role) AND order_status = 1 ORDER BY id_order DESC LIMIT $Page_Start , $Per_Page");
}





function getTrackOrderTable($view="",$from ="", $to ="", $Page_Start="",$Per_Page="",$role = "1",$id_sale){
	$sale = new sale($id_sale);
	$sale_name = $sale->full_name;
	if($view !=""){
			$date = getLastDays($view);
			$from = $date['from'];
			$to = $date['to'];
		}
	return dbQuery("SELECT id_order,reference, tbl_order.id_customer, current_state, tbl_order.date_upd FROM tbl_customer LEFT JOIN tbl_order ON tbl_customer.id_customer = tbl_order.id_customer WHERE (tbl_order.date_add BETWEEN '$from' AND '$to') AND role IN($role)  AND id_sale = '$id_sale' ORDER BY id_order DESC LIMIT $Page_Start , $Per_Page");
}





 function state_color($current_state){
		$sql = dbQuery("SELECT color FROM tbl_order_state WHERE id_order_state = $current_state");
		list($color) = dbFetchArray($sql);
		return $color;
	}





function getTitleRadio($gender=""){
	$sql=dbQuery("SELECT * FROM tbl_gender WHERE type !=0");
	$row=dbNumRows($sql);
	$i=0;
	while($i<$row){
		list($id_gender, $type, $prefix)=dbFetchArray($sql);
		echo"<input type='radio' name='gender' id='gender' value='$id_gender' style='margin-left:15px;'"; if($gender==$id_gender){echo" checked='checked'";} echo" /><label for='$id_gender' style='margin-left:15px;'>$prefix</label>";
		$i++;
	}
}





function selectDay($selected=""){
	echo"<option value='0'"; if($selected==""){echo" selected='selected'";} echo"> - </option>";
	$i=1;
	$day = 31;
	while($i<=$day){
		echo"<option value='$i' "; if($i==$selected){echo" selected='selected'";} echo">$i</option>";
		$i++;
	}
}





function selectMonth($selected=""){
	$array = array(" - ","มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฏาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
	$i=0;
	$month = 12;
	while($i<=$month){
		echo"<option value='$i' "; if($i==$selected){echo" selected='selected'";} echo">$array[$i]</option>";
		$i++;
	}
}





function selectYear($selected=""){
	$this_year= date('Y' ,strtotime("+1 year"));
	$n= $this_year - 100;
	$i= $this_year;
	echo"<option value='0000'  "; if($selected==""){echo" selected='selected'";} echo"> - </option>";
	while($i>=$n){
		echo"<option value='$i' "; if($i==$selected){echo" selected='selected'";} echo">";echo  $i+543 ." </option>";
		$i--;
	}
}





function customerGroupTable($id_cus="",$all=false){
	echo"<table class='table table-striped table-condensed' style='border:1px solid #ccc;'>
                    	<thead><th width='15%'><input type='checkbox' id='check_all' /></th><th width='15%'>ID</th><th width='70%'>กลุมลูกค้า</th></thead>";
						////*******ดึงข้อมูลมาแสดงเป็นตารางกลุ่มลูกค้า*******////
						$query = dbQuery("SELECT * FROM tbl_group");
						$rows = dbNumRows($query);
						$n =  0;
						$default = 1;
						while($n<$rows){
							$res = dbFetchArray($query);
							$id_group = $res['id_group'];
							$group_name = $res['group_name'];
							$checked = "";
							if($id_cus !=""){
							$default = "";
							$check = dbNumRows(dbQuery("SELECT * FROM tbl_customer_group WHERE id_customer=$id_cus AND id_group=$id_group"));
							if($all ==true){ $checked = "checked='checked'"; }else if($check==1){ $checked = "checked='checked'"; }else{$checked = "";}
							}
							echo "<tr><td width='15%'><input type='checkbox' id='groupcheck$id_group' value='$id_group' name='groupcheck[]' $checked /></td>
									<td width='15%'>$id_group</td><td width='70%'><label for='groupcheck$id_group'>$group_name</label></td></tr>";
									$n++;
						}
						echo"</table>
						<script>
						$('#check_all').click(function(){
     	 					$(\":checkbox[name='groupcheck[]']\").prop('checked', this.checked);
   						 });
						</script>";
}





function getCustomerDetail($id){
		$result = dbFetchArray(dbQuery("SELECT * FROM tbl_customer WHERE id_customer=$id"));
		return $result;
}





function customer_group($id_group){
	list($result) = dbFetchArray(dbQuery("SELECT group_name FROM tbl_group WHERE id_group = '$id_group'"));
	return $result;
}





function getCustomerGroup($id){
	$result = dbFetchArray(dbQuery("SELECT * FROM tbl_customer_group WHERE id_customer = $id"));
	return $result;
}





function selectCustomerGroup($selected=""){
		$sql = dbQuery("SELECT * FROM tbl_group");
		$row = dbNumRows($sql);
		$i = 0;
		while($i<$row)
		{
			list($id_group, $group_name) = dbFetchArray($sql);
			echo"<option value='$id_group' ".isSelected($id_group, $selected)." >$group_name</option>";
			$i++;
		}
}





function selectEmployeeGroup($selected="", $title = "------ เลือก ------"){
	$profile = $_COOKIE['profile_id'];
		echo "<option value='' "; if($selected == ""){ echo"selected='selected'";} echo">$title</option>";
		$sql = dbQuery("SELECT * FROM tbl_profile where id_profile >= '$profile'");
		$row = dbNumRows($sql);
		$i = 0;
		while($i<$row){
			list($id_profile, $profile_name) = dbFetchArray($sql);
			echo"<option value='$id_profile' "; if($id_profile == $selected){ echo"selected='selected'";} echo">$profile_name</option>";
			$i++;
		}
}





function getCustomerAddress($id){
	$result = dbQuery("SELECT * FROM tbl_address WHERE id_customer = $id");
	return $result;
}





function getAddressDetail($id){
	$result = dbFetchArray(dbQuery("SELECT * FROM tbl_address WHERE id_address = $id"));
	return $result;
}






function selectCity($selected="")
{
	$array = array("---เลือกจังหวัด---","กรุงเทพมหานคร","กระบี่","กาญจนบุรี","กาฬสินธุ์"	,"กำแพงเพชร"	,"ขอนแก่น"	,"จันทบุรี"	,"ฉะเชิงเทรา"	,"ชัยนาท","ชัยภูมิ"	,"ชุมพร"	,"ชลบุรี"	,"เชียงใหม่"	,"เชียงราย"	,"ตรัง"	,"ตราด"	,"ตาก"	,"นครนายก","นครปฐม","นครพนม","นครราชสีมา"	,"นครศรีธรรมราช"	,"นครสวรรค์"	,"นราธิวาส","น่าน"	,"นนทบุรี"	,"บึงกาฬ"	,"บุรีรัมย์"	,"ประจวบคีรีขันธ์"	,"ปทุมธานี"	,"ปราจีนบุรี"	,"ปัตตานี"	,"พะเยา"	,"พระนครศรีอยุธยา"	,"พังงา"	,"พิจิตร","พิษณุโลก","เพชรบุรี"	,"เพชรบูรณ์","แพร่","พัทลุง","ภูเก็ต"	,"มหาสารคาม"	,"มุกดาหาร"	,"แม่ฮ่องสอน"	,"ยโสธร"	,"ยะลา"	,"ร้อยเอ็ด"	,"ระนอง"	,"ระยอง","ราชบุรี"	,"ลพบุรี"	,"ลำปาง"	,"ลำพูน"	,"เลย"	,"ศรีสะเกษ"	,"สกลนคร"	,"สงขลา"	,"สมุทรสาคร"	,"สมุทรปราการ"	,"สมุทรสงคราม"	,"สระแก้ว"	,"สระบุรี"	,"สิงห์บุรี"	,"สุโขทัย"	,"สุพรรณบุรี"	,"สุราษฎร์ธานี"	,"สุรินทร์"	,"สตูล"	,"หนองคาย"	,"หนองบัวลำภู"	,"อำนาจเจริญ"	,"อุดรธานี"	,"อุตรดิตถ์"	,"อุทัยธานี"	,"อุบลราชธานี"	,"อ่างทอง"	,"อื่นๆ"	);
	foreach($array as $city){
		echo"<option value=\""; if($city =="---เลือกจังหวัด---"){echo"\"";}else{echo"$city\"";} if($city==$selected){ echo"selected='selected'";} echo">$city </option>";
	}
}





function selectProvince($se = '')
{
	$sc = '';
	$arr = array(
						"กรุงเทพมหานคร", "กระบี่", "กาญจนบุรี", "กาฬสินธุ์", "กำแพงเพชร", "ขอนแก่น", "จันทบุรี", "ฉะเชิงเทรา", "ชัยนาท", "ชัยภูมิ", "ชุมพร",
						"ชลบุรี", "เชียงใหม่", "เชียงราย", "ตรัง", "ตราด", "ตาก", "นครนายก", "นครปฐม", "นครพนม", "นครราชสีมา", "นครศรีธรรมราช", "นครสวรรค์",
						"นราธิวาส", "น่าน", "นนทบุรี", "บึงกาฬ", "บุรีรัมย์", "ประจวบคีรีขันธ์", "ปทุมธานี", "ปราจีนบุรี", "ปัตตานี", "พะเยา", "พระนครศรีอยุธยา",
						"พังงา", "พิจิตร", "พิษณุโลก", "เพชรบุรี", "เพชรบูรณ์", "แพร่", "พัทลุง", "ภูเก็ต", "มหาสารคาม", "มุกดาหาร", "แม่ฮ่องสอน", "ยโสธร", "ยะลา",
						"ร้อยเอ็ด", "ระนอง", "ระยอง", "ราชบุรี", "ลพบุรี", "ลำปาง", "ลำพูน", "เลย", "ศรีสะเกษ", "สกลนคร", "สงขลา", "สมุทรสาคร", "สมุทรปราการ",
						"สมุทรสงคราม", "สระแก้ว", "สระบุรี", "สิงห์บุรี", "สุโขทัย", "สุพรรณบุรี", "สุราษฎร์ธานี", "สุรินทร์", "สตูล", "หนองคาย", "หนองบัวลำภู",
						"อำนาจเจริญ", "อุดรธานี", "อุตรดิตถ์", "อุทัยธานี", "อุบลราชธานี", "อ่างทอง", "อื่นๆ"
					);
	foreach( $arr as $rs)
	{
		$sc .= '<option value="'.$rs.'" '.isSelected($se, $rs).' >'.$rs.'</option>';
	}
	return $sc;
}





function getProvinceArray()
{
	$arr = array(
						"กรุงเทพมหานคร", "กระบี่", "กาญจนบุรี", "กาฬสินธุ์", "กำแพงเพชร", "ขอนแก่น", "จันทบุรี", "ฉะเชิงเทรา", "ชัยนาท", "ชัยภูมิ", "ชุมพร",
						"ชลบุรี", "เชียงใหม่", "เชียงราย", "ตรัง", "ตราด", "ตาก", "นครนายก", "นครปฐม", "นครพนม", "นครราชสีมา", "นครศรีธรรมราช", "นครสวรรค์",
						"นราธิวาส", "น่าน", "นนทบุรี", "บึงกาฬ", "บุรีรัมย์", "ประจวบคีรีขันธ์", "ปทุมธานี", "ปราจีนบุรี", "ปัตตานี", "พะเยา", "พระนครศรีอยุธยา",
						"พังงา", "พิจิตร", "พิษณุโลก", "เพชรบุรี", "เพชรบูรณ์", "แพร่", "พัทลุง", "ภูเก็ต", "มหาสารคาม", "มุกดาหาร", "แม่ฮ่องสอน", "ยโสธร", "ยะลา",
						"ร้อยเอ็ด", "ระนอง", "ระยอง", "ราชบุรี", "ลพบุรี", "ลำปาง", "ลำพูน", "เลย", "ศรีสะเกษ", "สกลนคร", "สงขลา", "สมุทรสาคร", "สมุทรปราการ",
						"สมุทรสงคราม", "สระแก้ว", "สระบุรี", "สิงห์บุรี", "สุโขทัย", "สุพรรณบุรี", "สุราษฎร์ธานี", "สุรินทร์", "สตูล", "หนองคาย", "หนองบัวลำภู",
						"อำนาจเจริญ", "อุดรธานี", "อุตรดิตถ์", "อุทัยธานี", "อุบลราชธานี", "อ่างทอง", "อื่นๆ"
					);
	return $arr;
}




function getGroupDetail($id_group=""){
	if($id_group !=""){
	$sql = dbQuery("SELECT * FROM tbl_group WHERE id_group = $id_group");
	return dbFetchArray($sql);
	}else{
	$sql = dbQuery("SELECT * FROM tbl_group");
	return dbFetchArray($sql);
	}
}





function customerTableByGroup($id_group){
	$sql = dbQuery("SELECT * FROM customer_group_table WHERE id_group = $id_group");
	return $sql;
}





function get_id_images($id_product){
	$sql = dbQuery("SELECT id_image FROM tbl_image WHERE id_product = $id_product ORDER BY position ASC");
	return dbFetchArray($sql);
}





function employeeList($selected=""){
	$sql = dbQuery("SELECT id_employee, first_name, last_name FROM tbl_employee");
	echo "<option value='' "; if($selected == ""){ echo"selected='selected'";} echo">------ เลือก ------</option>";
	$row = dbNumRows($sql);
	$i=0;
	while($i<$row){
		list($id_employee, $first_name, $last_name) = dbFetchArray($sql);
		echo"<option value='$id_employee'"; if($selected==$id_employee){ echo"selected='selected'";} echo"> $first_name  $last_name</option>";
		$i++;
	}
}





function saleGroupList($selected=""){
	$sql = dbQuery("SELECT id_group, group_name FROM tbl_group");
	echo "<option value='' "; if($selected == ""){ echo"selected='selected'";} echo">------ เลือก ------</option>";
	$row = dbNumRows($sql);
	$i=0;
	while($i<$row){
		list($id_group, $group_name) = dbFetchArray($sql);
		echo"<option value='$id_group'"; if($selected==$id_group){ echo"selected='selected'";} echo">$group_name</option>";
		$i++;
	}
}





function sale_group_list($selected=""){
	$sql = dbQuery("SELECT id_group, group_name FROM tbl_group WHERE id_group !=1");
	$row = dbNumRows($sql);
	$i=0;
	while($i<$row){
		list($id_group, $group_name) = dbFetchArray($sql);
		echo"<option value='$id_group'"; if($selected==$id_group){ echo"selected='selected'";} echo">$group_name</option>";
		$i++;
	}
}





function saleList($selected=""){
	$sql = dbQuery("SELECT id_sale, first_name, last_name FROM tbl_sale LEFT JOIN tbl_employee ON tbl_sale.id_employee = tbl_employee.id_employee");
	echo"<option value='0' "; if($selected==""){echo "selected='selected'";} echo "> ------- เลือก ------ </option>";
	$row = dbNumRows($sql);
	$i=0;
	while($i<$row){
		list($id_sale, $first_name, $last_name) = dbFetchArray($sql);
		echo "<option value='$id_sale'"; if($id_sale==$selected){ echo"selected='selected'";} echo ">$first_name $last_name</option>";
		$i++;
	}
}






function newArrival($i,$id_customer){
	$NEW = new category();
	$company = new company();
		$sql = dbQuery("SELECT tbl_product.id_product, product_code, product_name, product_price,discount_type, discount, product_detail FROM tbl_product LEFT JOIN tbl_product_detail ON tbl_product.id_product = tbl_product_detail.id_product WHERE active = 1 ORDER BY tbl_product.date_add DESC LIMIT $i");
		$row = dbNumRows($sql);
		if($row>0){
			while($data = dbFetchArray($sql)){
				$product = new product();
				$product->product_detail($data['id_product'],$id_customer);
				$array = $product->getCategoryId($product->id_product);
				$id_cat = array();
				foreach($array as $ar){
					array_push($id_cat,$ar);
				}
				$id_category = max($id_cat);
				echo"
		<div class='item'>
			<div class='product'>
			  <div class='image'> <a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'>".$product->getCoverImage($product->id_product,4,"img-responsive")."</a>
				<div class='promotion'>";
					$NEW->category_show_new($company->product_new,$product->id_product);
					echo "".$NEW->NEW."
				";/*if($product->product_discount>0){echo"<span class='discount'>".$product->product_discount."OFF</span>";} */echo" </div>
			  </div>
			  <div class='description'>
				<h4><a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'>".$product->product_code." : ".$product->product_name."</a></h4>
				<p><a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'>".substr_replace($product->product_detail,'....',200)."</a></p> </div>
			  <div class='price'> <span>&nbsp;</span>";
			  /*if($product->product_discount>0){echo"<span class='old-price'>".number_format($product->product_price,2)." ฿</span>";} */echo" </div>
			  <div class='action-control'> <a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'><span class='btn btn-primary' style='width:50%;'>".number_format($product->product_price,2)." ฿</span></a>  </div>
			</div>
		  </div>";
			}
		}
	}





/// แสดงรายการสินค้าในส่วนของ เซลล์ ตามหมวดหมู่ที่เลือก
function product_grid($id_category, $id_cus=0){
	$sql = dbQuery("SELECT tbl_product.id_product FROM tbl_product  LEFT JOIN tbl_category_product ON tbl_product.id_product = tbl_category_product.id_product WHERE id_category = $id_category AND tbl_product.active =1");
	$row = dbNumRows($sql);
	if(isset($_COOKIE['id_cart'])){ $id_cart = $_COOKIE['id_cart']; }else{ $id_cart = "" ; }
	if($row>0){
		$i=0;
		while($i<$row){
			list($id_product) = dbFetchArray($sql);
			$product = new product();
			$product->product_detail($id_product, $id_cus);
			echo"<div class='item2 col-lg-3 col-md-3 col-sm-4 col-xs-6'>
			<div class='product'>
			<div class='image'><a href='#' onclick='getData(".$product->id_product.")'>".$product->getCoverImage($product->id_product,3,"img-responsive")."</a></div>
			<div class='description' style='font-size:1.5vmin; '>
				<a href='#' onclick='getData(".$product->id_product.")'>".$product->product_code." <br> ".$product->product_name."</a>
			</div>
			  <div class='price'>"; if($product->product_discount>0){echo"<span class='old-price'>".number_format($product->product_price,2)." ฿</span>";}else{ echo"<span class='old-price'>&nbsp;</span>";} echo" </div>
			  <div class='action-control'> <a href='#' data-toggle='modal' data-target='#".$product->id_product."'><span class='btn btn-primary' style='width:80%; font-size:1.5vmin;'>".number_format($product->product_sell,2)." ฿</span></a>  </div></div></div>";
			$i++;
		}
		echo "
		<button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button>
		<form id='order_form' action='controller/orderController.php?add_to_cart' method='post'>
		<input type='hidden' id='id_product' name='id_product'>
		<input type='hidden' id='id_cart' name='id_cart' value='".$id_cart."' >
		<input type='hidden' name='id_customer' value='".$id_cus."' >
				<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
				<div class='modal-dialog' id='modal'>
					<div class='modal-content'>
						<div class='modal-header'>
							<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
							<h4 class='modal-title' id='modal_title'>title</h4>
						</div>
						<div class='modal-body'  id='modal_body'></div>
						<div class='modal-footer'>
							<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
							<button type='button' class='btn btn-primary' onclick=\"submit_product()\">หยิบใส่ตะกร้า</button>
						</div>
					</div>
				</div>
			</div> </form>";
	}else{
		echo"<h4 style='align:center;'>ไม่มีรายการสินค้าในหมวดหมู่นี้</h4>";
	}
}





/// แสดงรายการสินค้ามาใหม่ในส่วนของ เซลล์
function newProduct($i, $id_cus=0){
		$sql = dbQuery("SELECT id_product FROM tbl_product WHERE active = 1 ORDER BY date_add DESC LIMIT $i");
		$row = dbNumRows($sql);
	if($row>0){
		$i=0;
		echo"<div class='row xsResponse'>";
		while($i<$row){
			list($id_product) = dbFetchArray($sql);
			$product = new product();
			$product->product_detail($id_product, $id_cus);
			echo"<div class='item2 col-lg-3 col-md-3 col-sm-6 col-xs-12'>
			<div class='product'>
			<div class='image'><a href='#' onclick='getData(".$product->id_product.")'>".$product->getCoverImage($product->id_product,3,"img-responsive")."</a></div>
			<div class='description' style='min-height:60px;'>
				<a href='#' onclick='getData(".$product->id_product.")'>".$product->product_code." <br> ".$product->product_name."</a>
			</div>
			  <div class='price'>"; if($product->product_discount>0){echo"<span class='old-price'>".number_format($product->product_price,2)." ฿</span>";}else{ echo"<span class='old-price'>&nbsp;</span>";} echo" </div>
			  <div class='action-control'> <a href='#' data-toggle='modal' data-target='#".$product->id_product."'><span class='btn btn-primary' style='width:80%; '>".number_format($product->product_sell,2)." ฿</span></a>  </div></div></div>";
			$i++;
		}

	}
}







function featureProduct($i, $id_cus=0){
	$NEW = new category();
	$company = new company();
		$sql = dbQuery("SELECT tbl_product.id_product, product_code, product_name, product_price,discount_type, discount, product_detail FROM tbl_product LEFT JOIN tbl_product_detail ON tbl_product.id_product = tbl_product_detail.id_product LEFT JOIN tbl_category_product ON tbl_product.id_product = tbl_category_product.id_product WHERE active = 1 AND id_category = 0 ORDER BY tbl_product.date_add DESC LIMIT $i");
		$row = dbNumRows($sql);
	if($row>0){
		$i=0;
		echo"<div class='row xsResponse'>";
		while($i<$row){
			list($id_product) = dbFetchArray($sql);
			$product = new product();
			$product->product_detail($id_product, $id_cus);
			$array = $product->getCategoryId($product->id_product);
				$id_cat = array();
				foreach($array as $ar){
					array_push($id_cat,$ar);
				}
				$id_category = max($id_cat);
			echo"<div class='item col-lg-3 col-md-3 col-sm-4 col-xs-6'>
			<div class='product'>
			<div class='image'><a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'>".$product->getCoverImage($product->id_product,4,"img-responsive")."</a>
			<div class='promotion'> ";
					$NEW->category_show_new($company->product_new,$product->id_product);
					echo "".$NEW->NEW."
				"; //if($product->product_discount>0){echo"<span class='discount'>".$product->product_discount."OFF</span>";}
				echo" </div>

			</div>

			<div class='description' style='min-height:60px;'>
				<h5><a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'>".$product->product_code." : ".$product->product_name."</a></h5>
				<p><a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'>".substr_replace($product->product_detail,'....',200)."</a></p>
			</div>
			  <div class='price'>"; /*if($product->product_discount>0){echo"<span class='old-price'>".number_format($product->product_price,2)." ฿</span>";}else{ echo"<span>&nbsp;</span>";} */ echo" </div>
			  <div class='action-control'> <a href='index.php?content=product&id_category=$id_category&id_product=".$product->id_product."'><span class='btn btn-primary' style='width:50%;'>".number_format($product->product_price /*$product->product_sell*/,2)." ฿</span></a>  </div></div></div>";
			$i++;
		}
		echo"</div>";
	}
}






function getSaleId($id_user){
	list($id_sale) = dbFetchArray(dbQuery("SELECT id_sale FROM tbl_sale WHERE id_employee = $id_user"));
	return $id_sale;
}






function get_id_sale_by_customer($id_customer)
{
	$id_sale = 0;
	$qs = dbQuery("SELECT id_sale FROM tbl_customer WHERE id_customer = ".$id_customer);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$id_sale = $rs['id_sale'];
	}
	return $id_sale;
}






function customerList($id_user){
	$sql = dbQuery("SELECT id_customer, first_name, last_name FROM tbl_customer WHERE id_sale = ".$id_user);
	echo "<option value='0'>------ เลือกลูกค้า ------</option>";
	while($data=dbFetchArray($sql)){
		echo "<option value='".$data['id_customer']."'>".$data['first_name']."&nbsp;".$data['last_name']."</option>";
	}
}






function order_grid($id_cus=0, $id_order, $action="controller/orderController.php?add_to_order"){
		$sql = dbQuery("SELECT tbl_product.id_product, product_code, product_name, product_price,discount_type, discount, product_detail FROM tbl_product LEFT JOIN tbl_product_detail ON tbl_product.id_product = tbl_product_detail.id_product WHERE active = 1 ORDER BY tbl_product.product_code ASC");
		$row = dbNumRows($sql);
	if($row>0){
		$i=0;
		echo"<div class='row xsResponse'>";
		while($i<$row){
			list($id_product) = dbFetchArray($sql);
			$product = new product();
			$product->product_detail($id_product, $id_cus);
			$config = getConfig("ATTRIBUTE_GRID_HORIZONTAL");
			$sqr = dbQuery("SELECT id_$config FROM tbl_product_attribute WHERE id_product = $id_product AND id_$config !=0 GROUP BY id_$config");
			$colums = dbNumRows($sqr);
			$table_w = "style='width:".(70*($colums+1)+100)."px;'";
			echo"<div class='item2 col-lg-1 col-md-1 col-sm-3 col-xs-4'><form action='$action' method='post'>
		<div class='modal fade' id='".$product->id_product."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' $table_w>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='myModalLabel'>".$product->product_code."</h4><input type='hidden' name='id_order' value='$id_order' />
									  </div>
									  <div class='modal-body'>"; $product->order_attribute_grid($product->id_product); echo"</div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
										<button type='submit' class='btn btn-primary'>เพิ่มในรายการ</button>
									  </div>
									</div>
								  </div>
								</div>";
			echo"


			<div class='product'>
			<div class='image'><a href='#' data-toggle='modal' data-target='#".$product->id_product."'>".$product->getCoverImage($product->id_product,1,"img-responsive")."</a></div>
			<div class='description' style='font-size:12px;'>
				<a href='#' data-toggle='modal' data-target='#".$product->id_product."'>".$product->product_code."</a>
			</div>
			  </div></form></div>";
			$i++;
		}
		echo"</div>";
	}
}






function order_grid_consign($id_cus=0, $id_order, $action="",$id_zone){
		$sql = dbQuery("SELECT tbl_product.id_product, product_code, product_name, product_price,discount_type, discount, product_detail FROM tbl_product LEFT JOIN tbl_product_detail ON tbl_product.id_product = tbl_product_detail.id_product WHERE active = 1 ORDER BY tbl_product.product_code ASC");
		$row = dbNumRows($sql);
	if($row>0){
		$i=0;
		echo"<div class='row xsResponse'>";
		while($i<$row){
			list($id_product) = dbFetchArray($sql);
			$product = new product();
			$product->product_detail($id_product, $id_cus);
			$config = getConfig("ATTRIBUTE_GRID_HORIZONTAL");
			$sqr = dbQuery("SELECT id_$config FROM tbl_product_attribute WHERE id_product = $id_product AND id_$config !=0 GROUP BY id_$config");
			$colums = dbNumRows($sqr);
			$table_w = "style='width:".(70*($colums+1)+100)."px;'";
			echo"<div class='item2 col-lg-1 col-md-1 col-sm-3 col-xs-4'><form action='$action' method='post'>
			<div class='modal fade' id='".$product->id_product."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' $table_w>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='myModalLabel'>".$product->product_code."</h4><input type='hidden' name='id_order' value='$id_order' />
									  </div>
									  <div class='modal-body'>"; $product->consign_attribute_grid($product->id_product,$id_cus,$id_zone); echo"</div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
										<button type='submit' class='btn btn-primary'>เพิ่มในรายการ</button>
									  </div>
									</div>
								  </div>
								</div>



			<div class='product'>
			<div class='image'><a href='#' data-toggle='modal' data-target='#".$product->id_product."'>".$product->getCoverImage($product->id_product,1,"img-responsive")."</a></div>
			<div class='description' >
				<a href='#' data-toggle='modal' data-target='#".$product->id_product."'>".$product->product_code."</a>
			</div>
			  </div></form></div>";
			$i++;
		}
		echo"</div>";
	}
}





function orderStateList($id_order)
{
	$id_tab 	= 14;
	$id_profile = getCookie('profile_id');
    $pm 		= checkAccess($id_profile, $id_tab);
	$edit 		= $pm['edit'];
	$delete 	= $pm['delete'];
	$sc 		= '<option value="0"> ---- สถานะ ---- </option>';
	$sc 		.= $edit == 1 ? '<option value="1">รอการชำระเงิน</option>' : '';
	$sc 		.= $edit == 1 ? '<option value="3">รอจัดสินค้า</option>' : '';
	$sc 		.= $delete == 1 ? '<option value="8">ยกเลิก</option>' : '';

	return $sc;
}





	//-----------------------dropmovement------------------//
function drop_movement($id_order)
{
	$order = new order($id_order);
	$a = dbQuery("DELETE FROM tbl_stock_movement WHERE reference = '".$order->reference."'");
	$b = dbQuery("DELETE FROM tbl_order_detail_sold WHERE id_order= ".$id_order);
	return $a && $b;
}





	//---------------- return ออร์เดอร์ที่เปิดบิลแล้ว----------------------//
	function order_return($id_order){
		$sql = dbQuery("SELECT id_product_attribute,product_qty FROM tbl_order_detail WHERE id_order = $id_order");
			$row = dbNumRows($sql);
			$i=0;
			while($i<$row){
				list($id_product_attribute,$product_qty) = dbFetchArray($sql);
				list($id_stock,$qty) = dbFetchArray(dbQuery("SELECT id_stock,qty FROM tbl_stock WHERE id_zone = 0 AND id_product_attribute = $id_product_attribute"));
				if($id_stock !=""){
					$sumqty = $product_qty + $qty;
					dbQuery("UPDATE tbl_stock SET qty = '$sumqty' WHERE id_stock = $id_stock");
				}else{
					dbQuery("INSERT INTO tbl_stock(id_zone,id_product_attribute,qty)VALUES(0,$id_product_attribute,'$product_qty')");
				}
			$i++;
			}
			drop_movement($id_order);
			dbQuery("UPDATE tbl_temp SET status = 3 WHERE id_order = $id_order");
	}






//--------- กรณียกเลิกออเดอร์  -----//
function drop_temp_qc($id_order)
{
	$a = dbQuery("DELETE FROM tbl_temp WHERE id_order = ".$id_order);
	$b = dbQuery("DELETE FROM tbl_qc WHERE id_order = ".$id_order);
	return $a && $b;
}








function delete_movement($reference, $id_pa, $id_zone = 0 )
{
	return dbQuery("DELETE FROM tbl_stock_movement WHERE reference = '".$reference."' AND id_product_attribute = ".$id_pa." AND id_zone = ".$id_zone);
}








function delete_detail_sold($id_order, $id_pa)
{
	return dbQuery("DELETE FROM tbl_order_detail_sold WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa);
}







function drop_order_detail($id_order)
{
	return dbQuery("DELETE FROM tbl_order_detail WHERE id_order = ".$id_order);
}






//--------------------------- เปลี่ยนสถานะออเดอร์ -------------------------//
function order_state_change($id_order, $state, $id_emp)
{
	$sc		= TRUE;
	$order 	= new order($id_order);
	$c_state	= $order->current_state;  //---  สถานะปัจจุบัน
	if($state == 2)
	{
		if( ! $order->validOrder($id_order)){ $sc = FALSE; }
	}
	else if($state == 1 OR $state == 3 )
	{
		if( $c_state == 9 )  //----- ถ้าเปิดบิลไปแล้ว ----//
		{
			if( ! rollback_order($id_order) ){ $sc = FALSE; }  ///-----  ย้อนกระบวนการ  ----///
		}
	}
	else if($state == 8 )
	{
		require_once 'sponsor_helper.php';
		require_once 'support_helper.php';
		require_once 'lend_helper.php';
		if($c_state == 9 OR $c_state == 7 OR $c_state == 6 )
		{
			//----  ถ้าสถานะเป็นเปิดบิลแล้ว ให้ยกเลิกออเดอร์  ---//
			if( ! cancle_order($id_order) ){ $sc = FALSE; }
		}
		else if($c_state == 11 || $c_state == 10 || $c_state == 5 || $c_state == 4 || $c_state == 3 || $c_state == 1)
		{
			//---- ถ้าสถานะคือ สินค้าถูกจัดออกมาแล้ว แต่ยังไม่ได้เปิดบิล ดึงยอดจาก buffer เพิ่มเข้า cancle
			if( ! clear_buffer($id_order) ){ $sc = FALSE; }

			///****************  คืนยอดงบประมาณคงเหลือ  ****************///
			if( ! return_budget($id_order) ){ $sc = FALSE; }
			if($order->role == 7 )
			{
				if( ! update_order_support_amount($id_order, 0.00 ) ){ $sc = FALSE; }
				if( ! update_order_support_status($id_order, 2) ){ $sc = FALSE; } /// 0 = notvalid   1 = valid  2 = cancle
			}
			else if($order->role == 4 )
			{
				if( ! update_order_sponsor_amount($id_order, 0.00 ) ){ $sc = FALSE;	}
				if( ! update_order_sponsor_status($id_order, 2) ){ $sc = FALSE; }  /// 0 = notvalid   1 = valid  2 = cancle
			}
			else if($order->role == 3 )
			{
				$lend 		= new lend();
				$id_lend 	= $lend->get_id_lend_by_order($id_order);
				$lend->change_lend_status($id_lend, 3);  /// 0 = not save 1 = saved  2 = closed  3 = cancled
				$lend->change_all_lend_detail_valid($id_lend, 2);    /// 0 = not return or not all  1 = returned all   2 = cancled  ถ้าสถานะเป็น 0 จะตรวจสอบก่อนว่าคืนของครบแล้วหรือยัง ถ้าครบแล้วจะเปลี่ยนเป็น 1
			}
			drop_temp_qc($id_order);
			drop_order_detail($id_order);
		}
	}
	if( ! dbQuery("UPDATE tbl_order SET current_state = ".$state." WHERE id_order = ".$id_order) ){ $sc = FALSE; };
	if( ! dbQuery("INSERT INTO tbl_order_state_change ( id_order, id_order_state, id_employee ) VALUES (".$id_order.", ".$state.", ".$id_emp.")") ){ $sc = FALSE; }

	return $sc;
}




//------------------------------  เมื่อเปลี่ยนสถานะของออเดอร์ที่เปิดบิลไปแล้ว ( แต่ไม่ได้ยกเลิกออเดอร์)
function rollback_order($id_order)
{
	//-----  กำหนดค่าเริ่มต้นสำหรับส่งกลับกรณีที่ไม่มีข้อผิดพลาดใดๆ
	$sc 		= TRUE;
	$order 	= new order($id_order);
	if($order->role == 5 )
	{
		rollback_consign($id_order);
	}
	else
	{
		//----- ดึงยอดทีบันทึกยอดขายใน order_detail_sold กลับมา
		$qs = dbQuery("SELECT id_product, id_product_attribute, reference FROM tbl_order_detail_sold WHERE id_order = ".$id_order);
		//----- ถ้ามียอดขาย
		if( dbNumRows($qs) > 0 )
		{
			while( $rs = dbFetchArray($qs) )
			{
				//-----  ดึงยอดจาก qc เพื่อเตรียมเพิ่มกลับเข้าไปที่ Buffer
				$qr = "SELECT SUM(tbl_qc.qty) AS qty, id_warehouse, id_zone, tbl_qc.id_employee FROM tbl_qc JOIN tbl_temp ON tbl_qc.id_temp = tbl_temp.id_temp ";
				$qr .= "WHERE tbl_qc.id_order = ".$id_order." AND tbl_qc.id_product_attribute = ".$rs['id_product_attribute']." GROUP BY id_zone";
				$qr = dbQuery($qr);

				if( dbNumRows($qr) > 0 )
				{
					//----- ทำการเพิ่มรายการกลับเข้า buffer
					while( $rm = dbFetchArray($qr) )
					{
						//-----  เพิ่มสินค้าเข้า Buffer
						$ra = update_buffer_zone($rm['qty'], $rs['id_product'], $rs['id_product_attribute'], $id_order, $rm['id_zone'], $rm['id_warehouse'], $rm['id_employee']);

						//-----  ลบรายการใน stock_movement
						$rb = delete_movement($rs['reference'], $rs['id_product_attribute'], $rm['id_zone']);

						//-----  ลบรายการใน order_detail_sold (ยอดขาย)
						$rc = delete_detail_sold($id_order, $rs['id_product_attribute']);

						if( ! $ra OR ! $rb OR ! $rc ){ $sc = FALSE; }
					}
				}
			}

			//--------------------------------------  UPdate Budget  ------------------------------//
			//------------------  กรณีเบิกอภินันท์
			if($order->role == 7)
			{
				$order_amount	=  $order->getCurrentOrderAmount($id_order);		//----- ตรวจสอบยอดเงินสั่งซื้อ
				$qc_amount 	= $order->qc_amount($id_order);						//----- ตรวจสอบยอดเงินที่ qc ได้

				//----- ถ้าไม่เท่ากันให้ทำการปรับปรุงยอดงบประมาณคงเหลือ
				if( $order_amount != $qc_amount )
				{
					$id_budget	= get_id_support_budget_by_order($id_order);

					//----- ยอดต่างระหว่างยอดเงินสั่งซื้อ กับ ยอดเงิน qc  แล้วทำให้ติดลบเพื่อทำการลดงบประมาณให้เป็นไปตามออเดอร์ เพราะเมื่อเปิดบิลอีกครั้งหากมียอดต่างจะบวกกลับให้
					$amount 		= ($order_amount - $qc_amount) * -1;

					//----- ดึงยอดงบประมาณคงเหลือขึ้นม
					$balance 	= get_support_balance($id_budget);

					//----- บวกยอดต่างกลับเข้าไป กรณีที่ ยอดสั่งมากกว่ายอด qc ต้องคืนยอดต่างกลับเข้างบ
					$balance 	+= $amount;

					//-----  ปรับปรุงยอดงบประมาณคงเหลือ
					if( ! update_support_balance($id_budget, $balance) ){ $sc = FALSE; }
				}

				//----- ปรับปรุงยอดออเดอร์ใน order_sponsor
				$ra = update_order_support_amount($id_order, 0.00 );

				//----- อัพเดท สภานะของ order_support  0 = notvalid /  1 = valid / 2 = cancle
				$rb = update_order_support_status($id_order, 0);

				if( ! $ra OR ! $rb ){ $sc = FALSE; }
			}

			//------------------- กรณีเบิกสปอนเซอร์
			if($order->role == 4)
			{
				$order_amount 	=  $order->getCurrentOrderAmount($id_order); 	//----- ตรวจสอบยอดเงินสั่งซื้อ
				$qc_amount 		= $order->qc_amount($id_order);						//----- ตรวจสอบยอดเงินที่ qc ได้

				//----- ถ้าไม่เท่ากันให้ทำการปรับปรุงยอดงบประมาณคงเหลือ
				if($order_amount != $qc_amount)
				{
					$id_budget 	= get_id_sponsor_budget_by_order($id_order);

					//----- ยอดต่างระหว่างยอดเงินสั่งซื้อ กับ ยอดเงิน qc  แล้วทำให้ติดลบเพื่อทำการลดงบประมาณให้เป็นไปตามออเดอร์ เพราะเมื่อเปิดบิลอีกครั้งหากมียอดต่างจะบวกกลับให้
					$amount 		= ($order_amount - $qc_amount) * -1;

					//----- ดึงยอดงบประมาณคงเหลือขึ้นมา
					$balance 	= get_sponsor_balance($id_budget);

					//----- บวกยอดต่างกลับเข้าไป กรณีที่ ยอดสั่งมากกว่ายอด qc ต้องคืนยอดต่างกลับเข้างบ
					$balance 	+= $amount;

					//-----  ปรับปรุงยอดงบประมาณคงเหลือ
					if( ! update_sponsor_balance($id_budget, $balance) ){ $sc = FALSE; }

				}

				//----- ปรับปรุงยอดออเดอร์ใน order_sponsor
				$ra = update_order_sponsor_amount($id_order, 0.00 );

				//-----  อัพเดทสถานะของ order_sponsor  0 = notvalid /  1 = valid / 2 = cancle
				$rb = update_order_sponsor_status($id_order, 0);

				if( ! $ra OR ! $rb ){ $sc = FALSE; }
			}
			//----------------------------------------  END Update Budget  --------------------------------//
		}
	}

	return $sc;
}






//-------------------------------------------------  เมื่อมีการยกเลิกออเดอร์
function cancle_order($id_order)
{
	//---- ลบยอดขาย ลบการ qc ลบtemp ลบ movement นำยอดสินค้าเพิ่มเข้า tbl_cancle

	$sc = TRUE;  //-----  ค่าสำหรับส่งกลับ ถ้าไม่มีอะไรผิดพลาด
	//---- ดึงยอดทีบันทึกยอดขายใน order_detail_sold กลับมา
	$sql = dbQuery("SELECT id_product, id_product_attribute, reference, total_amount FROM tbl_order_detail_sold WHERE id_order = ".$id_order);
	$order = new order($id_order);
	if($order->role == 5 )
	{
		if( ! cancle_consign($id_order) ){ $sc = FALSE; }
	}
	else
	{

		if( ! dbQuery("DELETE FROM tbl_order_discount WHERE id_order = ".$id_order) ){ $sc = FALSE; }

		///---- ถ้ามีรายการในตาราง order_detail_sold แสดงว่ามียอดขาย
		if( dbNumRows($sql) > 0 )
		{
			while( $rs = dbFetchArray($sql) )
			{
				//----- ดึงรายการในตาราง qc
				$qr = "SELECT SUM(tbl_qc.qty) AS qty, id_warehouse, id_zone, tbl_qc.id_employee FROM tbl_qc JOIN tbl_temp ON tbl_qc.id_temp = tbl_temp.id_temp ";
				$qr .= "WHERE tbl_qc.id_order = ".$id_order." AND tbl_qc.id_product_attribute = ".$rs['id_product_attribute']." AND tbl_qc.valid = 1  GROUP BY id_zone";
				$qr = dbQuery($qr);
				if( dbNumRows($qr) > 0 )
				{
					//----- ทำการเพิ่มรายการเข้า tbl_cancle
					while($rm = dbFetchArray($qr) )
					{
						//----- เพิ่มรายการเข้าตาราง ยกเลิก
						$ra = cancle_product( $rm['qty'], $rs['id_product'], $rs['id_product_attribute'], $id_order, $rm['id_zone'], $rm['id_warehouse'], $rm['id_employee'] );

						//----- ลบ stock_movement
						$rb = delete_movement( $rs['reference'], $rs['id_product_attribute'], $rm['id_zone'] );

						//----- ลบยอดขาย
						$rc = delete_detail_sold( $id_order, $rs['id_product_attribute'] );

						if( ! $ra OR ! $rb OR ! $rc ){ $sc = FALSE; }
					}
				}
			}
			//----------------  คืนยอดงบประมาณ
			if( ! return_budget($id_order) ){ $sc = FALSE; }

			if($order->role == 7 )
			{
				//----- ปรับปรุงยอดออเดอร์ใน order_support
				$ra =	update_order_support_amount($id_order, 0.00 );

				//----- อัพเดทสถานะ order_support   0 = notvalid  / 1 = valid / 2 = cancle
				$rb = update_order_support_status($id_order, 2);

				if( ! $ra OR ! $rb ){ $sc = FALSE; }

			}

			if($order->role == 4 )
			{
				//----- ปรับปรุงยอดออเดอร์ใน order_sponsor
				$ra = update_order_sponsor_amount($id_order, 0.00 );

				//----- 0 = notvalid   1 = valid  2 = cancle
				$rb = update_order_sponsor_status($id_order, 2);

				if( ! $ra OR ! $rb ){ $sc = FALSE; }
			}

			if($order->role == 3 )
			{
				$lend 		= new lend();
				$id_lend 	= $lend->get_id_lend_by_order($id_order);

				//----- 0 = not save 1 = saved  2 = closed  3 = cancled
				$ra 		= $lend->change_lend_status($id_lend, 3);

				//----- 2 = cancled
				$rb		= $lend->change_all_lend_detail_valid($id_lend, 2);

				if( ! $ra OR ! $rb ){ $sc = FALSE; }
			}
		}
	}
	//-----  ลบรายการใน temp และ qc
	$ra = drop_temp_qc($id_order);

	//-----  ลบรายละเอียดออเดอร์
	$rb = drop_order_detail($id_order);

	if( ! $ra OR ! $rb ){ $sc = FALSE; }

	return $sc;
}





//------------------------  เมื่อเปลี่ยนสถานะของออเดอร์ฝากขายที่เปิดบิลไปแล้ว ( แต่ไม่ได้ยกเลิกออเดอร์)  --------------------------//
function rollback_consign($id_order)
{
	$sc = TRUE;
	$reference = get_order_reference($id_order);
	$id_emp		= getCookie('user_id') == FALSE ? 0 : getCookie('user_id');
	$qs = dbQuery("SELECT * FROM tbl_stock_movement WHERE reference = '".$reference."'");
	//------ ถ้ามีการบันทึกเรียบร้อย
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$product 	= new product();
			$id_product = $product->getProductId($rs['id_product_attribute']);

			//-----  ถ้า move_out = 0 แสดงว่าเป็นปลายทาง ให้ตัดยอดออกจากโซน
			if( $rs['move_in'] > 0 && $rs['move_out'] == 0 )
			{
				if( ! update_stock_zone($rs['move_in']*-1, $rs['id_zone'], $rs['id_product_attribute']) ){ $sc = FALSE;  }
			}

			//-----  ถ้า move_in = 0 แสดงว่า เป็นต้นทาง ให้คืนยอดเข้า buffer
			if( $rs['move_out'] > 0 && $rs['move_in'] == 0 )
			{
				if( ! update_buffer_zone($rs['move_out'], $id_product, $rs['id_product_attribute'], $id_order, $rs['id_zone'], $rs['id_warehouse'], $id_emp) ){ $sc = FALSE; }
			}

			$rc =	dbQuery("DELETE FROM tbl_stock_movement WHERE id_stock_movement = ".$rs['id_stock_movement']);

			if( ! $rc ){ $sc = FALSE; }
		}
	}
	else
	{
		//------------- ถ้าบันทึกไม่ผ่านให้ไปดูที่ cancle และ buffer
		$qs = dbQuery("SELECT * FROM tbl_cancle WHERE id_order = ".$id_order);
		if( dbNumRows($qs) > 0 )
		{
			while( $rs = dbFetchObject($qs) )
			{
				$rd = update_buffer_zone($rs->qty, $rs->id_product, $rs->id_product_attribute, $rs->id_order, $rs->id_zone, $rs->id_warehouse, $rs->id_employee);
				if( $rd === TRUE )
				{
					dbQuery("DELETE FROM tbl_cancle WHERE id_order = ".$rs->id_order." AND id_product_attribute = ".$rs->id_product_attribute." AND id_zone = ".$rs->id_zone." AND qty = ".$rs->qty);
				}
			}
		}

	}
	return $sc;
}






//-----------------------------  เมื่อยกเลิกออเดอร์ฝากขาย
function cancle_consign($id_order)
{
	$sc 			= TRUE;
	$reference 	= get_order_reference($id_order);
	$id_emp		= getCookie('user_id') === FALSE ? 0 : getCookie('user_id');
	$qs 			= dbQuery("SELECT * FROM tbl_stock_movement WHERE reference = '".$reference."'");
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$product = new product();
			$id_product = $product->getProductId($rs['id_product_attribute']);

			if( $rs['move_in'] > 0 && $rs['move_out'] == 0 )
			{
				//-----  ตัดยอดออกจากโซน
				if( ! update_stock_zone( $rs['move_in']*-1, $rs['id_zone'], $rs['id_product_attribute'] ) ){ $sc = FALSE;  }
			}
			if( $rs['move_out'] > 0 && $rs['move_in'] == 0 )
			{
				//----- เพิ่มรายการเข้าตาราง ยกเลิก
				if( ! cancle_product( $rs['move_out'], $id_product, $rs['id_product_attribute'], $id_order, $rs['id_zone'], $rs['id_warehouse'], $id_emp ) ){ $sc = FALSE;  }

			}

			//-----  ลบรายการออกจาก movement
			$rc = dbQuery("DELETE FROM tbl_stock_movement WHERE id_stock_movement = ".$rs['id_stock_movement']);

			if( ! $rc ){ $sc = FALSE; }

		}//--- End while
	}//--- End if

	return $sc;
}






function check_current_qty($id_order, $id_product_attribute, $qty)
{
	$sql = dbQuery("SELECT SUM(qty) FROM tbl_temp WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_product_attribute." AND status != 5");
	$sqr =dbQuery("SELECT product_qty FROM tbl_order_detail WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_product_attribute );
	list($re) = dbFetchArray($sql);
	list($rs) = dbFetchArray($sqr);
	$result['current'] = $re;
	$result['order_qty'] = $rs;
	return $result;
}






function check_product_in_order($id_pa, $id_order)
{
	$sc = FALSE;
	$qs	= dbQuery("SELECT id_product_attribute FROM tbl_order_detail WHERE id_product_attribute = ".$id_pa." AND id_order = ".$id_order );
	if( dbNumRows($qs) > 0 )
	{
		$sc = TRUE;
	}
	return $sc;
}







function check_qty_in_order($id_product_attribute, $id_order)
{
	$qt	= 0;
	$qs 	= dbQuery("SELECT product_qty FROM tbl_order_detail WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order");
	if(dbNumRows($qs) > 0 )
	{
		list( $qty ) = dbFetchArray($qs);
	}
	return $qty;
}








function insert_to_temp( $id_order, $id_pa, $qty, $id_wh, $id_zone, $status, $id_emp)
{
	$sc = TRUE;
	startTransection();
        $date = date('Y-m-d H:i:s');
	while( $qty > 0 )
	{
		$qs = dbQuery("INSERT INTO tbl_temp ( id_order, id_product_attribute, qty, id_warehouse, id_zone, status, id_employee, date ) VALUES ( ".$id_order.", ".$id_pa.", 1, ".$id_wh.", ".$id_zone.", ".$status.", ".$id_emp.", '".$date."' )");
		$qty--;
		if( ! $qs ){ $sc = FALSE; }
	}
	if( $sc === TRUE )
	{
		commitTransection();
	 }
	 else
	 {
		 dbRollback();
	 }
	return $sc;
 }







function update_stock_zone( $qty, $id_zone, $id_pa)
{
	$qs = dbQuery("SELECT id_stock, qty FROM tbl_stock WHERE id_zone = ".$id_zone." AND id_product_attribute = ".$id_pa);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$newQty = $qty + $rs['qty'];
		if( $newQty == 0 )
		{
			$qr = dbQuery("DELETE FROM tbl_stock WHERE id_stock = ".$rs['id_stock']);
		}
		else
		{
			$qr = dbQuery("UPDATE tbl_stock SET qty = ".$newQty." WHERE id_stock = ".$rs['id_stock']);
		}
	}
	else
	{
		$qr = dbQuery("INSERT INTO tbl_stock ( id_zone, id_product_attribute, qty ) VALUES ( ".$id_zone.", ".$id_pa.", ".$qty." )");
	}
	return $qr;
}







function update_buffer_zone($qty, $id_pd, $id_pa, $id_order, $id_zone, $id_wh, $id_emp)
{
	$qs = dbQuery("SELECT id_buffer, qty FROM tbl_buffer WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa." AND id_zone = ".$id_zone);
	if( dbNumRows($qs) > 0 )
	{
		$rs = dbFetchArray($qs);
		$qty = $qty + $rs['qty'];
		if( $qty == 0 )
		{
			$qr = dbQuery("DELETE FROM tbl_buffer WHERE id_buffer = ".$rs['id_buffer'] );
		}
		else
		{
			$qr = dbQuery("UPDATE tbl_buffer SET qty = ".$qty." WHERE id_buffer = ".$rs['id_buffer'] );
		}
	}
	else
	{
		$qr = dbQuery("INSERT INTO tbl_buffer (id_order, id_product, id_product_attribute, qty, id_zone, id_warehouse, id_employee) VALUES (".$id_order.", ".$id_pd.", ".$id_pa.", ".$qty.", ".$id_zone.", ".$id_wh.", ".$id_emp.")");
	}
	return $qr;
}






function clear_buffer($id_order)
{
	$sc 	= TRUE;
	 $qs	= dbQuery("SELECT * FROM tbl_buffer WHERE id_order = ".$id_order);
	 if( dbNumRows($qs) > 0 )
	 {
		 while( $rs = dbFetchArray($qs) )
		 {
			 if( ! cancle_product($rs['qty'], $rs['id_product'], $rs['id_product_attribute'], $rs['id_order'], $rs['id_zone'], $rs['id_warehouse'], $rs['id_employee']) )
			 {
				 $sc = FALSE;
			 }
			 if( ! update_buffer_zone($rs['qty']*-1, $rs['id_product'], $rs['id_product_attribute'], $rs['id_order'], $rs['id_zone'], $rs['id_warehouse'], $rs['id_employee']) )
			 {
				 $sc = FALSE;
			 }
		 }
	 }
	 return $sc;
}





function cancle_product($qty, $id_pd, $id_pa, $id_order, $id_zone, $id_wh, $id_emp)
{
	$qs	= dbQuery("SELECT qty FROM tbl_cancle WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa." AND id_zone = ". $id_zone." AND id_warehouse = ".$id_wh);
	if( dbNumRows($qs) > 0 )
	{
		list( $old_qty )	= dbFetchArray($qs);
		$new_qty 		= $old_qty + $qty;
		if( $new_qty == 0 )
		{
			$rs = dbQuery("DELETE FROM tbl_cancle WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa." AND id_zone = ".$id_zone." AND id_warehouse = ".$id_wh);
		}
		else
		{
			$rs = dbQuery("UPDATE tbl_cancle SET qty = ".$new_qty." WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa." AND id_zone = ".$id_zone." AND id_warehouse = ".$id_wh);
		}
	}
	else
	{
		$rs = dbQuery("INSERT INTO tbl_cancle (id_order, id_product, id_product_attribute, qty, id_zone, id_warehouse, id_employee) VALUES (".$id_order.", ".$id_pd.", ".$id_pa.", ".$qty.", ".$id_zone.", ".$id_wh.", ".$id_emp.")");
	}
	return $rs;
}






function check_cart($id_sale)
{
	$qs = dbQuery("SELECT id_cart, id_customer FROM tbl_cart WHERE id_sale = ".$id_sale." AND valid = 0 ");
	if(dbNumRows($qs) > 0 )
	{
		$rs = dbFetchArray($qs);
		$cart['id_cart']	= $rs['id_cart'];
		$cart['id_customer']	= $rs['id_customer'];
	}else{
		$cart = false;
	}
	return $cart;
}







function get_detail_from_order($id_order, $id_pa)
{
	$qs = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa);
	if( dbNumRows($qs) == 1 )
	{
		return dbFetchArray($qs);
	}
	else
	{
		return FALSE;
	}
}







function sold_product($id_order, $id_pa, $qty)
{
	$order 	= new order($id_order);
	$product	= new product();
	$ref		= $order->reference;
	$id_cus	= $order->id_customer;
	$id_sale	= get_id_sale_by_customer($id_cus);
	$role		= $order->role;
	$id_emp	= $order->id_employee;
	$date_upd = dbDate($order->date_add, TRUE);

	$sc	= TRUE;
	$ds		= get_detail_from_order($id_order, $id_pa); /// ตรวจสอบว่ามีสินค้านี้อยูทในออเดอร์หรือป่าว
	if( $ds !== FALSE )
	{
		$id_pd 		= $ds['id_product'];
		$p_name		= $ds['product_name'];
		$p_ref		= $ds['product_reference'];
		$order_qty	= $ds['product_qty'];
		$barcode	= $ds['barcode'];
		$price		= $ds['product_price'];
		$p_dis		= $ds['reduction_percent'];
		$a_dis		= $ds['reduction_amount'];
		$dis_amount	= $p_dis > 0 ? $qty * ($price * ($p_dis * 0.01)) : $qty * $a_dis;
		$final_price	= $p_dis > 0 ? $price - ($price * ($p_dis * 0.01)) : $price - $a_dis;
		$total_amount	= $qty * $final_price;
		$cost 		= $product->get_product_cost($id_pa);
		$total_cost	= $qty * $cost;

		$qr = "INSERT INTO tbl_order_detail_sold ";
		$qr .= "(id_order, reference, id_role, id_customer, id_employee, id_sale, id_product, ";
		$qr .= "id_product_attribute, product_name, product_reference, barcode, product_price, ";
		$qr .= "order_qty, sold_qty, reduction_percent, reduction_amount, discount_amount, final_price, total_amount, date_upd, cost, total_cost)";
		$qr .= " VALUES ";
		$qr .= "(".$id_order.", '".$ref."', ".$role.", ".$id_cus.", ".$id_emp.", ".$id_sale.", ".$id_pd.", ".$id_pa.", '".$p_name."', '".$p_ref."', '".$barcode."', ";
		$qr .= $price.", ".$order_qty.", ".$qty.", ".$p_dis.", ".$a_dis.", ".$dis_amount.", ".$final_price.", ".$total_amount.", '".$date_upd."', ".$cost.", ".$total_cost.")";
		$sc = dbQuery($qr);
	}
	else
	{
		$sc = FALSE;
	}
	return $sc;
}





function order_sold($id_order)
{
	$order 	= new order($id_order);
	$product	= new product();
	$ref		= $order->reference;
	$id_cus	= $order->id_customer;
	$id_sale	= get_id_sale_by_customer($id_cus);
	$role		= $order->role;
	$id_emp	= $order->id_employee;
	$date_upd = dbDate($order->date_add, TRUE);

	$result	= TRUE;

	$qr 		= dbQuery("SELECT id_product_attribute, product_qty AS qty FROM tbl_order_detail JOIN tbl_product ON tbl_order_detail.id_product = tbl_product.id_product WHERE id_order = ".$id_order." AND is_visual = 1");
	if( dbNumRows($qr) > 0 )
	{
		while( $rs = dbFetchArray($qr) )
		{
			$id_pa 	= $rs['id_product_attribute'];
			$qty	 	= $rs['qty'];
			sold_product($id_order, $id_pa, $qty);
		}
	}
	$qs 		= dbQuery('SELECT id_product_attribute, SUM(qty) AS qty FROM tbl_qc WHERE id_order = '.$id_order.' AND valid = 1 GROUP BY id_product_attribute');
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$id_pa 	= $rs['id_product_attribute'];
			$qty	 	= $rs['qty'];
			$ds		= get_detail_from_order($id_order, $id_pa);
			if( $ds !== FALSE )
			{
				$id_pd 		= $ds['id_product'];
				$p_name		= $ds['product_name'];
				$p_ref		= $ds['product_reference'];
				$order_qty	= $ds['product_qty'];
				$barcode	= $ds['barcode'];
				$price		= $ds['product_price'];
				$p_dis		= $ds['reduction_percent'];
				$a_dis		= $ds['reduction_amount'];
				$dis_amount	= $p_dis > 0 ? $qty * ($price * ($p_dis * 0.01)) : $qty * $a_dis;
				$final_price	= $p_dis > 0 ? $price - ($price * ($p_dis * 0.01)) : $price - $a_dis;
				$total_amount	= $qty * $final_price;
				$cost 		= $product->get_product_cost($id_pa);
				$total_cost	= $qty * $cost;

				$qr = "INSERT INTO tbl_order_detail_sold ";
				$qr .= "(id_order, reference, id_role, id_customer, id_employee, id_sale, id_product, ";
				$qr .= "id_product_attribute, product_name, product_reference, barcode, product_price, ";
				$qr .= "order_qty, sold_qty, reduction_percent, reduction_amount, discount_amount, final_price, total_amount, date_upd, cost, total_cost)";
				$qr .= " VALUES ";
				$qr .= "(".$id_order.", '".$ref."', ".$role.", ".$id_cus.", ".$id_emp.", ".$id_sale.", ".$id_pd.", ".$id_pa.", '".$p_name."', '".$p_ref."', '".$barcode."', ";
				$qr .= $price.", ".$order_qty.", ".$qty.", ".$p_dis.", ".$a_dis.", ".$dis_amount.", ".$final_price.", ".$total_amount.", '".$date_upd."', ".$cost.", ".$total_cost.")";

				$sc = dbQuery($qr);
				if( ! $sc )
				{
					$result = FALSE;
				}
			} // end if
		}// end while
	}
	else
	{
		$result = FALSE;
	}

	return $result;
}


	 function get_zone($id_zone){
		 list($name_zone) = dbFetchArray(dbQuery("SELECT zone_name FROM tbl_zone WHERE id_zone = $id_zone"));
		 return $name_zone;
	 }






	 function get_product_reference($id_product_attribute){
		 $ref = "";
		$qs = dbQuery("SELECT reference FROM tbl_product_attribute WHERE id_product_attribute = ".$id_product_attribute);
		if( dbNumRows($qs) == 1 )
		{
			list($ref) = dbFetchArray($qs);
		}
		return $ref;
	 }






	 function get_product_name($id_product)
	 {
		 $name 		= "";
		 $qs 			= dbQuery("SELECT product_name FROM tbl_product WHERE id_product = ".$id_product);
		 if( dbNumRows($qs) == 1 )
		 {
			 list($name)	= dbFetchArray($qs);
		 }
		 return $name;
	 }








	 function get_product_price($id_product_attribute)
	 {
		 $price = 0.00;
		 $qs = dbQuery("SELECT price FROM tbl_product_attribute WHERE id_product_attribute = ".$id_product_attribute);
		 if( dbNumRows($qs) == 1 )
		 {
			 list($price) = dbFetchArray($qs);
		 }
		 return $price;
	 }







	 function get_product_cost($id_product_attribute)
	 {
		 $cost = 0.00;
		 $qs = dbQuery("SELECT cost FROM tbl_product_attribute WHERE id_product_attribute = ".$id_product_attribute);
		 if( dbNumRows($qs) == 1 )
		 {
			 list( $cost ) = dbFetchArray($qs);
		 }
		 return $cost;
	 }







	 function qty_check_category_before($id_check,$id_category){
			list($total_qty_check) = dbFetchArray(dbQuery("SELECT SUM(qty_before) FROM tbl_stock_check LEFT JOIN tbl_product_attribute ON tbl_stock_check.id_product_attribute = tbl_product_attribute.id_product_attribute LEFT JOIN tbl_product ON tbl_product.id_product = tbl_product_attribute.id_product LEFT JOIN tbl_category_product ON tbl_product.id_product = tbl_category_product.id_product WHERE id_category = $id_category and id_check = $id_check"));
		return $total_qty_check;
	 }







	  function qty_check_category_after($id_check,$id_category){
			list($total_qty_check) = dbFetchArray(dbQuery("SELECT SUM(qty_after) FROM tbl_stock_check LEFT JOIN tbl_product_attribute ON tbl_stock_check.id_product_attribute = tbl_product_attribute.id_product_attribute LEFT JOIN tbl_product ON tbl_product.id_product = tbl_product_attribute.id_product LEFT JOIN tbl_category_product ON tbl_product.id_product = tbl_category_product.id_product WHERE id_category = $id_category and id_check = $id_check"));
		return $total_qty_check;
	 }







	 function qty_check_product_after($id_check,$id_product){
			list($total_qty_check) = dbFetchArray(dbQuery("SELECT SUM(qty_after) FROM tbl_stock_check LEFT JOIN tbl_product_attribute ON tbl_stock_check.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = $id_product and id_check = $id_check"));
		return $total_qty_check;
	 }






	 function qty_check_product_before($id_check,$id_product){
			list($total_qty_check) = dbFetchArray(dbQuery("SELECT SUM(qty_before) FROM tbl_stock_check LEFT JOIN tbl_product_attribute ON tbl_stock_check.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_product = $id_product and id_check = $id_check"));
		return $total_qty_check;
	 }







	 function sale_access($id_employee){
		$sql = dbQuery("SELECT id_sale FROM tbl_sale WHERE id_employee = $id_employee");
		$row = dbNumRows($sql);
		if($row>0){
			return true;
		}else{
			return false;
		}
	 }






	 function get_warehouse_name_by_id($id_warehouse){
			$sql = dbQuery("SELECT warehouse_name FROM tbl_warehouse WHERE id_warehouse = $id_warehouse");
			list($warehouse_name) = dbFetchArray($sql);
			 $result = $warehouse_name;
			return $result;
		}







		function get_warehouse_by_zone($id_zone){
			$sql = dbQuery("SELECT id_warehouse FROM tbl_zone WHERE id_zone = $id_zone");
			list($id_warehouse) = dbFetchArray($sql);
			 $result = $id_warehouse;
			return $result;
		}






	function get_category_name($id_category){
		$sql = dbQuery("SELECT category_name FROM tbl_category WHERE id_category = '$id_category'");
		$row = dbNumRows($sql);
		if($row>0){
			list($category_name) = dbFetchArray($sql);
		}else{
			$category_name = "ไม่พบหมวดหมู่";
		}
		return $category_name;
	}






	function stock_moveing($id_product_attribute){
		list($stock_moveing) = dbFetchArray(dbQuery("SELECT qty_move FROM tbl_move WHERE id_product_attribute = '$id_product_attribute'"));
		return $stock_moveing;
	}






	function insert_import_fales($id_consign_check,$barcode,$qty,$comment){
		dbQuery("INSERT INTO tbl_consign_import_fales (id_consign_check,barcode,qty,comment)VALUES($id_consign_check,'$barcode','$qty','$comment')");
	}






	function get_id_zone($zone_name){
		list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_zone WHERE zone_name = '$zone_name'"));
		return $id_zone;
	}





	function get_order_reference($id_order){
		$order = "";
		$sql = dbQuery("SELECT reference FROM tbl_order WHERE id_order = ".$id_order);
		$row = dbNumRows($sql);
		if($row > 0 ){
			$rs = dbFetchArray($sql);
			$order = $rs['reference'];
		}
		return $order;
	}





	function report_view($option){
	$today = date('Y-m-d');
	switch($option){
				case "week" :
						$rang = getWeek($today);
						break;
				case "month" :
						$rang = getMonth();
						break;
				case "year" :
						$rang = getYear();
						break ;
				default :
						$rang = getMonth();
						break;
			}
			$rs['from'] = $rang['from']." 00:00:00";
			$rs['to'] = $rang['to']." 23:59:59";
			return $rs;
}





function reorder($p_from, $p_to){
			if($p_to < $p_from){
				$from = $p_to;
				$to = $p_from;
			}else{
				$from = $p_from;
				$to = $p_to;
			}
			$arr['from'] = $from;
			$arr['to'] = $to;
			return $arr;
}





function get_lend_not_return_qty($id_order, $id_product_attribute){
	$qty = 0;
	list($lend_qty) = dbFetchArray(dbQuery("SELECT SUM(qty) AS qty FROM tbl_temp WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_product_attribute." AND status = 4")); // total lend
	list($return_qty) = dbFetchArray(dbQuery("SELECT SUM(qty) AS qty FROM tbl_temp WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_product_attribute." AND status = 5")); /// total return
	$bl = $lend_qty - $return_qty;
	$qty += $bl;
	return $qty;
}






function current_order_state($id_order){
	$state = "";
	$sql = dbQuery("SELECT id_order, state_name FROM tbl_order JOIN tbl_order_state ON tbl_order.current_state = tbl_order_state.id_order_state WHERE id_order = ".$id_order);
	$row = dbNumRows($sql);
	if($row > 0){
		$rs = dbFetchArray($sql);
		$state = $rs['state_name'];
	}
	return $state;
}







function getCurrentState($id_order)
{
	$sc = 1;
	$qs = dbQuery("SELECT current_state FROM tbl_order WHERE id_order = ".$id_order);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}






function get_id_color($id_product_attribute){
	$id = "";
	$qs = dbQuery("SELECT id_color FROM tbl_product_attribute WHERE id_product_attribute = ".$id_product_attribute);
	if(dbNumRows($qs) > 0 ){
		$rs = dbFetchArray($qs);
		$id = $rs['id_color'];
	}
	return $id;
}






function get_color_code($id_color){
	$color = "";
	$sql = dbQuery("SELECT color_code FROM tbl_color WHERE id_color = ".$id_color);
	if(dbNumRows($sql) > 0 ){
		list($code) = dbFetchArray($sql);
		$color = $code;
	}
	return $color;
}






function get_id_size($id_product_attribute){
	$id = "";
	$qs = dbQuery("SELECT id_size FROM tbl_product_attribute WHERE id_product_attribute = ".$id_product_attribute);
	if(dbNumRows($qs) > 0 ){
		$rs = dbFetchArray($qs);
		$id = $rs['id_size'];
	}
	return $id;
}







function get_size_name($id_size){
	$size = "";
	$sql = dbQuery("SELECT size_name FROM tbl_size WHERE id_size = ".$id_size);
	if(dbNumRows($sql) > 0 ){
		list($name) = dbFetchArray($sql);
		$size = $name;
	}
	return $size;
}






function get_id_attribute($id_product_attribute){
	$id = "";
	$qs = dbQuery("SELECT id_attribute FROM tbl_product_attribute WHERE id_product_attribute = ".$id_product_attribute);
	if(dbNumRows($qs) > 0 ){
		$rs = dbFetchArray($qs);
		$id = $rs['id_attribute'];
	}
	return $id;
}






function get_attribute_name($id_attribute){
	$attr = "";
	$sql = dbQuery("SELECT attribute_name FROM tbl_attribute WHERE id_attribute = ".$id_attribute);
	if(dbNumRows($sql) > 0){
		list($name) = dbFetchArray($sql);
		$attr = $name;
	}
	return $attr;
}







function employee_name($id_employee){
	$name = "";
	$qs = dbQuery("SELECT first_name, last_name FROM tbl_employee WHERE id_employee =".$id_employee);
	if(dbNumRows($qs) > 0){
		$rs = dbFetchArray($qs);
		$name = $rs['first_name']." ".$rs['last_name'];
	}
	return $name;
}






function sale_name($id_sale)
{
	$name = "";
	$qs = dbQuery("SELECT first_name, last_name FROM tbl_sale JOIN tbl_employee ON tbl_sale.id_employee = tbl_employee.id_employee WHERE tbl_sale.id_sale = ".$id_sale);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$name = $rs['first_name']." ".$rs['last_name'];
	}
	return $name;
}






function customer_name($id_customer){
	$name = "";
	$qs = dbQuery("SELECT first_name, last_name FROM tbl_customer WHERE id_customer = ".$id_customer);
	if(dbNumRows($qs) > 0){
		$rs = dbFetchArray($qs);
		$name = $rs['first_name']." ".$rs['last_name'];
	}
	return $name;
}







function supplier_code($id)
{
	$code = "";
	$qs = dbQuery("SELECT code FROM tbl_supplier WHERE id = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$code = $rs['code'];
	}
	return $code;
}





function supplier_name($id)
{
	$name = "";
	$qs = dbQuery("SELECT name FROM tbl_supplier WHERE id = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$name = $rs['name'];
	}
	return $name;
}






function order_amount($id_order){
	$amount = 0;
	$sql = dbQuery("SELECT SUM(total_amount) AS amount FROM tbl_order_detail WHERE id_order = ".$id_order);
	if(dbNumRows($sql)){
		$rs = dbFetchArray($sql);
		$amount = $rs['amount'];
	}
	return $amount;
}






function get_product_code($id_product){
	$code = "";
	$sql = dbQuery("SELECT product_code FROM tbl_product WHERE id_product = ".$id_product);
	if(dbNumRows($sql) >0){
		$rs = dbFetchArray($sql);
		$code = $rs['product_code'];
	}
	return $code;
}







	function get_order_diff_amount($id_order)
	{
		$amount = 0;
		$qs = dbQuery("SELECT id_product_attribute, product_qty, final_price FROM tbl_order_detail WHERE id_order = ".$id_order);
		while($rs = dbFetchArray($qs) )
		{
			$id = $rs['id_product_attribute'];
			$price = $rs['final_price'];
			$qty = $rs['product_qty'];
			$diff = 0;
			list($qc_qty) = dbFetchArray(dbQuery("SELECT SUM(qty) AS qty FROM tbl_qc WHERE id_product_attribute = ".$id." AND id_order = ".$id_order." AND valid = 1"));
			if($qc_qty != $qty){ $diff = ($qty - $qc_qty ) * $price;  $amount += $diff; }
		}
		return $amount;
	}







//-----------------  คืนยอดงบประมาณคงเหลือ  ---------------//
function return_budget($id_order)
{
	$sc 		= TRUE;
	$order	= new order($id_order);
	if($order->current_state == 9 )
	{
		$amount = $order->qc_amount($id_order);
	}
	else
	{
		$amount = $order->getCurrentOrderAmount($id_order);
	}

	if($order->role == 7 )
	{
		require_once 'support_helper.php';
		$id_budget	= get_id_support_budget_by_order($id_order);
		$balance 	= get_support_balance($id_budget);
		$balance 	+= $amount;

		//-----  ปรับปรุงยอดคงเหลือในงบประมาณ
		$ra	= update_support_balance($id_budget, $balance);

		//----- ปรับปรุงยอดออเดอร์ใน order_sponsor
		$rb	= update_order_support_amount($id_order, 0.00 );

		if( ! $ra OR ! $rb ){ $sc = FALSE; }
	}

	if($order->role == 4 )
	{
		require_once "sponsor_helper.php";
		$id_budget	= get_id_sponsor_budget_by_order($id_order);
		$balance 	= get_sponsor_balance($id_budget);
		$balance 	+= $amount;

		//-----  ปรับปรุงงบคงเหลือ
		$ra	= update_sponsor_balance($id_budget, $balance);

		//-----  ปรับปรุงยอดออเดอร์ใน order_sponsor
		$rb	= update_order_sponsor_amount($id_order, 0.00 );

		if( ! $ra OR ! $rb ){ $sc = FALSE; }
	}
	return $sc;
}





//----------------------  ตัดยอดงบประมาณตามออเดอร์ที่เปลี่ยนสถานะจากยกเลิกเป็นสถานะ รอชำระเงิน หรือ รอจัดสินค้า
function apply_budget($id_order)
{
	$sc 		= TRUE;
	$order	 = new order($id_order);
	if($order->role == 7 )
	{
		$id_budget	= get_id_support_budget_by_order($id_order);
		$balance 	= get_support_balance($id_budget);
		$amount 		= $order->getCurrentOrderAmount($id_order);
		$balance 	+= $amount * -1;
		$sc 			= update_support_balance($id_budget, $balance);
	}

	if($order->role == 4 )
	{
		$id_budget 	= get_id_sponsor_budget_by_order($id_order);
		$balance 	= get_sponsor_balance($id_budget);
		$amount 		= $order->getCurrentOrderAmount($id_order);
		$balance 	+= $amount * -1;
		$sc 			= update_sponsor_balance($id_budget, $balance);
	}
	return $sc;
}






function color_name($id_color)
{
	$name = "";
	$qs = dbQuery("SELECT color_name FROM tbl_color WHERE id_color = ".$id_color);
	if(dbNumRows($qs) > 0){
		$rs = dbFetchArray($qs);
		$name = $rs['color_name'];
	}
	return $name;
}






function profile_name($id_profile)
{
	$name = "";
	$qs = dbQuery("SELECT profile_name FROM tbl_profile WHERE id_profile = ".$id_profile);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$name = $rs['profile_name'];
	}
	return $name;
}






function select_year()
{
	$option = "";
	$c_year = date("Y");
	$l_year	= $c_year - 4;
	while($c_year >= $l_year )
	{
		$option .= "<option value='".$c_year."'>".$c_year."</option>";
		$c_year--;
	}
	return $option;
}






function get_barcode($id_product_attribute)
{
	$barcode = "";
	$qs = dbQuery("SELECT barcode FROM tbl_product_attribute WHERE id_product_attribute = ".$id_product_attribute);
	if( 	dbNumRows($qs) == 1 )
	{
		list($barcode) = dbFetchArray($qs);
	}
	return $barcode;
}





function get_zone_by_barcode($barcode)
{
	$id_zone = "";
	$qs = dbQuery("SELECT id_zone FROM tbl_zone WHERE barcode_zone = '".$barcode."'");
	if( dbNumRows($qs) == 1)
	{
		list($id_zone) = dbFetchArray($qs);
	}
	return $id_zone;
}






function clearToken($token)
{
	setcookie("file_download_token", $token, time() +3600,"/");
}

?>
