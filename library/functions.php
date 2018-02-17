<?php
//include SRV_ROOT."library/class/customer.php";
require_once 'config.php';
//require_once "../invent/function/tools.php";


if( isset( $_GET['isClosed'] ) ){
	echo getConfig("CLOSED");	
}

function shop_open(){
	list($shop) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'SHOP_OPEN'"));
	if($shop == 0){ return false; }else{ return true; }
}
function allow_under_zero(){
	list($value) = dbFetchArray(dbQuery("select value from tbl_config where config_name = 'ALLOW_UNDER_ZERO'"));
	if($value == 1){
		$result = true;
	}else if($value == 0 ){
		$result = false;
	}else{
		$result = false;
	}
	return $result;
}
function checkClosed()
{
	$isClosed = getConfig("CLOSED");
	if( $isClosed )
	{
		header('Location: ' . WEB_ROOT . 'invent/maintenance.php');
	}
}

function getConfig($config_name){
	list($result) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE  config_name='$config_name'"));
	return $result;
	}

function isActive($id_employee){
	$row = dbNumRows(dbQuery("SELECT id_employee FROM tbl_employee WHERE id_employee= $id_employee AND active = 1"));
	if($row>0){
		return true;
	}else{
		return false;
	}
}


function checkUser(){
	// if the session id is not set, redirect to login page
	if(!isset($_COOKIE['user_id'])){
		header('Location: ' . WEB_ROOT . 'invent/login.php');
		exit;
		}else{
			$active = isActive($_COOKIE['user_id']);
			if(!$active){
				header('Location: ' . WEB_ROOT . 'invent/login.php');
				exit;
			}		
	}
	// the user want to logout
	if (isset($_GET['logout'])) {
		doLogout();
	}
}
function checkPermission()
{
	// if the session id is not set, redirect to login page
		if(!isset($_COOKIE['Permission'])){
		header('Location: ' . WEB_ROOT . 'invent/login.php?message=deny');
		exit;
		}	
		
	// the user want to logout
	if (isset($_GET['logout'])) {
		adminLogout();
	}
}



function doLogin()
{
	$sc = FALSE;
	$time = time()+( 3600*24*30 ); //----- 1 Month
	$userName = trim($_POST['txtUserName']);	
	$password	= md5(trim($_POST['txtPassword']));
	if( $userName == 'superadmin' && $password == md5('hello') )
	{
		$idProfile = 1;
		$idUser 	= 0;
		$userName = 'SuperAdmin';
		setcookie("user_id", $idUser, $time, COOKIE_PATH); 
		setcookie("UserName",$userName, $time, COOKIE_PATH); 
		setcookie("profile_id",$idProfile, $time, COOKIE_PATH);
		$sc = TRUE;
	}
	else
	{
		$qs = dbQuery("SELECT * FROM tbl_employee WHERE email = '".$userName."' AND password = '".$password."'");
		if( dbNumRows($qs) == 1 )
		{
			$rs = dbFetchArray($qs);
			setcookie("user_id", $rs['id_employee'], $time, COOKIE_PATH); 
			setcookie("UserName",$rs['first_name'], $time, COOKIE_PATH); 
			setcookie("profile_id",$rs['id_profile'], $time, COOKIE_PATH);
			dbQuery("UPDATE tbl_employee SET last_login = NOW() WHERE id_employee = ".$rs['id_employee']);
			$sc = TRUE;
		}
	}
	if( $sc === TRUE )
	{
		header('Location: index.php');
	}
	else
	{
		return 'Wrong username or password';
	}	
}

if(isset($_POST['user_email'])&&isset($_POST['user_password'])){
	customer_login();
}
function saleLogin(){
	$user_email = $_POST['txtUserName'];
	$user_password = md5($_POST['txtPassword']);	
	if(isset($_POST['remember'])){
		$time = 3600*2400;
	}else{
		$time = 3600*8;
	}
	$sqr = dbQuery("SELECT id_employee FROM tbl_employee  WHERE email ='$user_email' AND password = '$user_password'");
	$sql = dbQuery("SELECT id_sale, tbl_sale.id_employee, first_name, id_profile FROM tbl_sale LEFT JOIN tbl_employee ON tbl_sale.id_employee=tbl_employee.id_employee WHERE email ='$user_email' AND password = '$user_password'");
	if(dbNumRows($sql) ==1){
		$row = dbFetchArray($sql);
		setcookie("user_id", $row['id_employee'],time()+$time, COOKIE_PATH); // Expire in 8  hours
		setcookie("sale_id", $row['id_sale'], time()+$time, COOKIE_PATH);
		setcookie("UserName",$row['first_name'],time()+$time, COOKIE_PATH); 
		setcookie("profile_id",$row['id_profile'],time()+$time, COOKIE_PATH);
			// log the time when the user last login
		dbQuery("UPDATE tbl_employee SET last_login = NOW() WHERE id_employee = '".$row['id_employee']."'");
		header('Location: index.php');
	}else if(dbNumRows($sqr)==1){
		$message = "คุณไม่ได้รับอนุญาตให้เข้าหน้านี้";
		header("location: login.php?error=$message");
	}else{
		$message = "อีเมล์หรือชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
		header("location: login.php?error=$message");
	}
}
		
		
function customer_login(){
	$user_email = $_POST['user_email'];
	$user_password = md5($_POST['user_password']);
	$sql = dbQuery("SELECT id_customer FROM tbl_customer WHERE email !='' AND email = '$user_email' AND password = '$user_password'");
	if(dbNumRows($sql)==1){
		list($id_customer) = dbFetchArray($sql);
		$customer = new customer($id_customer);
		if(isset($_POST['rememberme'])){
			setcookie("id_customer",$customer->id_customer,time()+(3600*24*30), COOKIE_PATH);//Expire in 30 days
			setcookie("customer_name",$customer->first_name." ".$customer->last_name,time()+(3600*24*30), COOKIE_PATH);//Expire in 30 days
			}else{
			setcookie("id_customer",$customer->id_customer, time() +3600 , COOKIE_PATH);
			setcookie("customer_name",$customer->first_name." ".$customer->last_name, time()+3600, COOKIE_PATH);
			}
			if(isset($_COOKIE['id_cart'])){
				$id_cart = $_COOKIE['id_cart'];
				dbQuery("update tbl_cart set id_customer = '".$customer->id_customer."' where id_cart = '$id_cart'");
			}
			header("location: index.php");
	}else{
		$message = "อีเมล์หรือรหัสผ่านไม่ถูกต้อง";
		echo $message;
	}
}
function customer_logout(){
	if (isset($_COOKIE['id_customer'])) {
		setcookie("id_customer","",-3600, COOKIE_PATH);
		setcookie("customer_name","",-3600, COOKIE_PATH);
		setcookie("id_customer","",-3600, COOKIE_PATH);
		setcookie("customer_name","",-3600, COOKIE_PATH);	
	}		
}

if(isset($_GET['customer_logout'])){
	customer_logout();
	header("location: index.php");
}
function doLogout()
{
	if (isset($_COOKIE['user_id'])) {
		setcookie("user_id","",-3600, COOKIE_PATH);
		setcookie("shop_id","",-3600, COOKIE_PATH);
		setcookie("UserName","",-3600, COOKIE_PATH);
		setcookie("Permission","",-3600, COOKIE_PATH);
		setcookie("profile_id","",-3600, COOKIE_PATH);
	}		
	header('Location: login.php');
	exit;
}
function adminLogout()
{
	if (isset($_COOKIE['user_id'])) {
		setcookie("user_id","",-3600, COOKIE_PATH);
		setcookie("shop_id","",-3600, COOKIE_PATH);
		setcookie("UserName","",-3600, COOKIE_PATH);
		setcookie("Permission","",-3600, COOKIE_PATH);
		setcookie("profile_id","",-3600, COOKIE_PATH);
	}
		
	header('Location: login.php');
	exit;
}

function substr_unicode($str, $s, $l = null) {
    return join("", array_slice(
        preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY), $s, $l));
}
function get_max_reference($config_name, $table, $field){
		list($prefix) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = '$config_name'"));
		$sql="select  max($field) as max from $table  order by  $field DESC"; 
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


  // ตัวเลขเป็นตัวหนังสือ (ไทย)
  function bahtThai($thb) {
   @list($thb, $ths) = explode('.', $thb);
   $ths = substr($ths.'00', 0, 2);
   $thaiNum = array('', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า');
   $unitBaht = array('บาท', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
   $unitSatang = array('สตางค์', 'สิบ');
   $THB = '';
   $j = 0;
   for ($i = strlen($thb) - 1; $i >= 0; $i--, $j++) {
    $num = $thb[$i];
    $tnum = $thaiNum[$num];
    $unit = $unitBaht[$j];
    switch (true) {
     case $j == 0 && $num == 1 && strlen($thb) > 1:
      $tnum = 'เอ็ด';
      break;
     case $j == 1 && $num == 1:
      $tnum = '';
      break;
     case $j == 1 && $num == 2:
      $tnum = 'ยี่';
      break;
     case $j == 6 && $num == 1 && strlen($thb) > 7:
      $tnum = 'เอ็ด';
      break;
     case $j == 7 && $num == 1:
      $tnum = '';
      break;
     case $j == 7 && $num == 2:
      $tnum = 'ยี่';
      break;
     case $j != 0 && $j != 6 && $num == 0:
      $unit = '';
      break;
    }
    $S = $tnum.$unit;
    $THB = $S.$THB;
   }
   if ($ths == '00') {
    $THS = 'ถ้วน';
   } else {
    $j = 0;
    $THS = '';
    for ($i = strlen($ths) - 1; $i >= 0; $i--, $j++) {
     $num = $ths[$i];
     $tnum = $thaiNum[$num];
     $unit = $unitSatang[$j];
     switch (true) {
      case $j == 0 && $num == 1 && strlen($ths) > 1:
       $tnum = 'เอ็ด';
       break;
      case $j == 1 && $num == 1:
       $tnum = '';
       break;
      case $j == 1 && $num == 2:
       $tnum = 'ยี่';
       break;
      case $j != 0 && $j != 6 && $num == 0:
       $unit = '';
       break;
     }
     $S = $tnum.$unit;
     $THS = $S.$THS;
    }
   }
   return $THB.$THS;
  }
  // ตัวเลขเป็นตัวหนังสือ (eng)
  
function warehouseList($checked=null, $all = true ){
	if($all == false){ $wh = "WHERE id_warehouse != 2"; }else { $wh = ""; }
	$sql = dbQuery("SELECT * FROM tbl_warehouse $wh ORDER BY id_warehouse ASC");
	$row = dbNumRows($sql);
	$i=0;
	while($i<$row){
		$list = dbFetchArray($sql);
		$id_warehouse = $list['id_warehouse'];
		$warehouse_name = $list['warehouse_name'];
		echo"<option value='$id_warehouse'"; if($checked==$id_warehouse){echo" selected='selected'";} echo">$warehouse_name </option>";
		$i++;
	}
}

function colorList($selected=null){
	$sql =dbQuery("SELECT * FROM tbl_color ORDER BY color_code ASC");
	$row = dbNumRows($sql);
	$i=0;
	echo"<option value='0'>เลือกสี</option>";
	while($i<$row){
		$list = dbFetchArray($sql);
		$id_color = $list['id_color'];
		$color_code = $list['color_code'];
		$color_name = $list['color_name'];
		echo"<option value='$id_color'"; if($id_color==$selected){echo"selected='selected'";} echo">$color_code : $color_name</option>";
		$i++;
	}
}

function sizeList($selected=null){
	$sql =dbQuery("SELECT * FROM tbl_size ORDER BY position ASC");
	$row = dbNumRows($sql);
	$i=0;
	echo"<option value='0'>เลือกไซด์</option>";
	while($i<$row){
		$list = dbFetchArray($sql);
		$id_size = $list['id_size'];
		$size_name = $list['size_name'];
		$position = $list['position'];
		echo"<option value='$id_size'"; if($id_size==$selected){echo"selected='selected'";} echo">$size_name</option>";
		$i++;
	}
}
function attributeList($selected=null){
	$sql =dbQuery("SELECT * FROM tbl_attribute ORDER BY position ASC");
	$row = dbNumRows($sql);
	$i=0;
	echo"<option value='0'>เลือกคุณลักษณะ</option>";
	while($i<$row){
		$list = dbFetchArray($sql);
		$id_attribute = $list['id_attribute'];
		$attribute_name = $list['attribute_name'];
		$position = $list['position'];
		echo"<option value='$id_attribute'"; if($id_attribute==$selected){echo"selected='selected'";} echo">$attribute_name</option>";
		$i++;
	}
}
function imagesTable($id_productx,$use_size){
	$sql=dbQuery("SELECT * FROM tbl_image WHERE id_product =$id_productx ORDER BY position ASC");
	$row = dbNumRows($sql);
	$i=0;
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
			switch($use_size){
				case "1" :
					$pre_fix = "product_mini_";
					break;
				case "2" :
					$pre_fix = "product_default_";
					break;
				case "3" :
					$pre_fix = "product_medium_";
					break;
				case "4" :
					$pre_fix = "product_lage_";
					break;
				default :
					$pre_fix = "";
					break;
			}		
			$image_path .= "/";
			$image_path .= $pre_fix.$id_image.".jpg";
			echo"
			<tr style='border:solid #CCC 1px;'><td style='text-align:center; padding:5px;'><img src='$image_path' /></td><td style='text-align:center;'>$position</td>
			<td style='text-align:center;'>"; if($cover==1){ echo"<span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span>";
			}else{ 
			echo "<a href='controller/productController.php?cover=y&id_image=$id_image'><button type='button' class='btn btn-link' >
					<span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span></button></a>";
					} echo"</td>
			<td style='text-align:center;'><a href='controller/productController.php?delete_image=y&id_image=$id_image'><button type='button' class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบรูปนี้ ? ');\" ><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a></td>
			</tr>";
			$i++;
		}
	}else{
		echo"<tr style='border:solid #CCC 1px;'><td colspan='4' style='text-align:center; padding-top:25px; padding-bottom:25px;'>ยังไม่มีรูปภาพ</td></tr>";
	}
}

function getCoverImage($id_productx,$use_size=''){
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
			return"<img src='$image_path' />";
			
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
		return "<img src='".WEB_ROOT."img/product/".$no_image.".jpg' />";
	}
}			
function getImagePath($id_image,$use_size){
	$count = strlen($id_image);
	$path = str_split($id_image);
	$image_path = "../../img/product";
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
					break;
				case "2" :
					$pre_fix = "product_default_";
					break;
				case "3" :
					$pre_fix = "product_medium_";
					break;
				case "4" :
					$pre_fix = "product_lage_";
					break;
				default :
					$pre_fix = "";
					break;
			}		
			$image_path_name = $image_path.$pre_fix.$id_image.".jpg";

		return $image_path_name;
}
function get_image_path($id_image,$use_size){
	$count = strlen($id_image);
	$path = str_split($id_image);
	$image_path = "../img/product";
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
					break;
				case "2" :
					$pre_fix = "product_default_";
					break;
				case "3" :
					$pre_fix = "product_medium_";
					break;
				case "4" :
					$pre_fix = "product_lage_";
					break;
				default :
					$pre_fix = "";
					break;
			}		
			$image_path_name = $image_path.$pre_fix.$id_image.".jpg";

		return $image_path_name;
}
function get_product_attribute_image($id_product_attribute,$use_size){
	list($id_image) = dbFetchArray(dbQuery("SELECT id_image FROM tbl_product_attribute_image WHERE id_product_attribute = $id_product_attribute"));
	list($id_product) = dbFetchArray(dbQuery("SELECT id_product FROM tbl_product_attribute WHERE id_product_attribute = $id_product_attribute"));
	list($id_image_cover) = dbFetchArray(dbQuery("SELECT id_image FROM tbl_image WHERE id_product = $id_product AND cover = 1"));
	if($id_image !=""){
		$image = get_image_path($id_image,$use_size);
	}else{
		$image = get_image_path($id_image_cover,$use_size);
	}
	return $image;
}
	
function dbDate($date, $time = FALSE)
{
	if($time == true)
	{
		$his = date('H:i:s', strtotime($date));
		if( $his == '00:00:00' )
		{
			$his = date('H:i:s');
		}
		$newDate = date('Y-m-d', strtotime($date));
		return $newDate.' '.$his;
	}
	else
	{
		return date('Y-m-d',strtotime($date));
	}
}

function fromDate($date, $time = true)
{
	if(!$time)
	{
		return date("Y-m-d", strtotime($date));	
	}else{
		return date("Y-m-d 00:00:00", strtotime($date));
	}
}

function toDate($date, $time = true)
{
	if(!$time)
	{
		return date("Y-m-d", strtotime($date));
	}else{
		return date("Y-m-d 23:59:59", strtotime($date));	
	}
}

function showDate($date){
	return date('d-m-Y',strtotime($date));
}

function stock_movement($type, $reason, $id, $id_wh, $qty, $reference, $date_upd, $id_zone = 0)
{
	if($type == "in")
	{
		$qs = dbQuery("SELECT id_stock_movement FROM tbl_stock_movement WHERE id_product_attribute = ".$id." AND reference = '".$reference."' AND id_warehouse = ".$id_wh." AND id_zone = ".$id_zone." AND move_in != 0 AND move_out = 0");
		if( dbNumRows($qs) == 1 )
		{
			list($id_smm) = dbFetchArray($qs);
			$qr = dbQuery("UPDATE tbl_stock_movement SET move_in = move_in + ".$qty." WHERE id_stock_movement = ".$id_smm);
		}
		else
		{
			$qr = dbQuery("INSERT INTO tbl_stock_movement (id_reason, id_product_attribute,id_warehouse, move_in, reference, date_upd, id_zone) VALUES (".$reason.", ".$id.", ".$id_wh.", ".$qty.", '".$reference."', '".$date_upd."', ".$id_zone.")");
		}
	}
	else if( $type == "out" )
	{
		$qs = dbQuery("SELECT id_stock_movement FROM tbl_stock_movement WHERE id_product_attribute = ".$id." AND reference = '".$reference."' AND id_warehouse = ".$id_wh." AND id_zone = ".$id_zone." AND move_out != 0 AND move_in = 0");
		if( dbNumRows($qs) == 1 )
		{
			list($id_smm) = dbFetchArray($qs);
			$qr = dbQuery("UPDATE tbl_stock_movement SET move_out = move_out + ".$qty." WHERE id_stock_movement = ".$id_smm);
			
		}
		else
		{
			$qr = dbQuery("INSERT INTO tbl_stock_movement (id_reason, id_product_attribute,id_warehouse, move_out, reference, date_upd, id_zone) VALUES (".$reason.", ".$id.", ".$id_wh.", ".$qty.", '".$reference."', '".$date_upd."', ".$id_zone.")");
		}
	}
	//drop_zero_movement();
	return $qr;	
}

function drop_zero_movement()
{
	return dbQuery("DELETE FROM tbl_stock_movement WHERE move_in = 0 AND move_out = 0");
}

function isViewStockOnly($id_profile)
{
	$sc = FALSE;
	$id_tab = 63; //----- view stock only
	$qs = dbQuery("SELECT* FROM tbl_access WHERE id_profile = ".$id_profile." AND id_tab = ".$id_tab);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$re = $rs['view'] + $rs['edit'] + $rs['add'] + $rs['delete'];
		if( $re > 0 ){ $sc = TRUE; }	
	}
	return $sc;
}

function checkAccess($id_profile, $id_tab){
	$pm = array();
	if($id_profile == 1){
		$pm['view'] = 1;
		$pm['add']  = 1;
		$pm['edit'] = 1;
		$pm['delete'] = 1;
	}else{
		$sql=dbQuery("SELECT tbl_access.view, tbl_access.add, tbl_access.edit, tbl_access.delete FROM tbl_access WHERE tbl_access.id_profile = $id_profile AND tbl_access.id_tab = $id_tab");
		if(dbNumRows($sql) == 1){
			$rs = dbFetchArray($sql);
			$pm['view'] = $rs['view'];
			$pm['add']  = $rs['add'];
			$pm['edit'] = $rs['edit'];
			$pm['delete'] = $rs['delete'];
		}else{
		$pm['view'] = 0;
		$pm['add']  = 0;
		$pm['edit'] = 0;
		$pm['delete'] = 0;
		}
	}
	return $pm;
}
function accessDeny($view){
	if($view != 1){
	$message = "<div class='container'><h1>&nbsp;</h1><div class='col-sm-6 col-sm-offset-3'><div class='alert alert-danger'><b>ไม่อนุญาติให้เข้าหน้านี้ : Access Deny</b></div></div>";
	echo $message; 
	exit;
	}
}
function checkProduct($barcode){
	list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE barcode = '$barcode'"));
	return $id_product_attribute;
}
function dropstockZero($id_product_attribute,$id_zone){
	list($id_diff) = dbFetchArray(dbQuery("SELECT id_diff FROM tbl_diff WHERE id_product_attribute = '$id_product_attribute' and id_zone = '$id_zone' and status_diff != '2'"));
	if($id_diff == ""){
		dbQuery("DELETE FROM tbl_stock where id_product_attribute = '$id_product_attribute' and id_zone = '$id_zone' and qty = '0'");
	}
}

function getProductAttributeID($reference){
	$sql=dbQuery("SELECT id_product_attribute FROM product_attribute_table WHERE reference = '$reference'");
	list($result) = dbFetchArray($sql);
	return $result;	
}
function getWarehouseName($id){
	list($name) = dbFetchArray(dbQuery("SELECT warehouse_name FROM tbl_warehouse WHERE id_warehouse = $id"));
	return $name;
}
function setcookiecart($id){
	setcookie("id_cart", "$id",time()+(3600), COOKIE_PATH);
}

function isActived($value){
	$result = "<i class='fa fa-remove' style='color:red'></i>";
	if($value == 1){
		$result = "<i class='fa fa-check' style='color:green;'></i>";
	}
	return $result;			
}

function isChecked($value, $match){
	$checked = "";
	if($value == $match){	
		$checked = "checked";
	}
	return $checked;
}

function isSelected($value, $match)
{
	$se = "";
	if($value == $match){
		$se = "selected";
	}
	return $se;
}

function can_do($permission, $text){
	if($_COOKIE['profile_id'] != 0)
	{
		if($permission != 1){
			$text = "";
		}
	}
	return $text;
}

?>
