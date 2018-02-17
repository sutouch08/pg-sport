<?php 
include "../../library/config.php";
include "../../library/functions.php";
include "../function/tools.php";

if(isset($_GET['add'])){
	$barcode = $_POST['barcode'];
	$qtyx			= $_POST['qty'];
	$id_zone		= $_GET['id_zone'];
	$id_check	= $_GET['id_check'];
	foreach($barcode as $c => $val)
	{
		$product = new product();
		$cs = new checkstock();
		$arr = $product->check_barcode($val);
		$qty = $qtyx[$c] * $arr['qty'];
		$zone_name = get_zone( $id_zone );
		if( $arr['id_product_attribute'] != "" )
		{
			$rs = $cs->add_stock($id_check, $id_zone, $arr['id_product_attribute'], $qty);
		}
	}
	echo "success";
}
/*
if(isset($_GET['add'])){
	$product = new product();
	$cs = new checkstock();
	$id_check = $cs->get_id_check();
	$arr = $product->check_barcode( trim( $_POST['barcode_item'] ) );
	$qty = $_POST['qty'] * $arr['qty'];
	$zone_name = get_zone( $_POST['id_zone'] );
	if( $arr['id_product_attribute'] != "" )
	{
		$rs = $cs->add_stock($id_check, $_POST['id_zone'], $arr['id_product_attribute'], $qty);
		if($rs)
		{
			$product_name = get_product_reference($arr['id_product_attribute']);
			$data = array("product"=>$product_name, "qty"=>$qty, "time"=>date("H:i:s"));
			$data = json_encode($data);
		}else{
			$data = "error2";	
		}
	}else{
		$data = "error1";
	}
	echo $data;
}
*/

if(isset($_GET['check_zone'])){
	$barcode = trim($_GET['barcode_zone']);
	$check_stock = new checkstock();
	$id 	= $check_stock->get_id_check();
	$id_zone = $check_stock->get_id_zone($barcode, $id);
	if(!$id_zone)
	{
		$message = "ไม่มีโซนนี้กรุณาตรวจสอบบาร์โค้ดโซน";
		header("location: ../index.php?content=checkstock&error=$message");
	}else{
		dbQuery("UPDATE tbl_stock_check SET status = 1 WHERE id_zone = ".$id_zone." AND id_check = ".$id);
		header("location: ../index.php?content=checkstock&id_zone=".$id_zone);
	}
}


if(isset($_GET['edit'])){
	$id_stock_check = $_POST['id_stock_check'];
	$id_zone = $_POST['id_zone'];
	$new_qty = $_POST['new_qty'];
	$check_stock = new checkstock();
	$check_stock->get_stock_check($id_stock_check);
	$qty = $check_stock->qty_after;
	$product = new product();
	$product->product_attribute_detail($check_stock->id_product_attribute);
	//echo "UPDATE tbl_stock_check SET qty_after = '$new_qty' WHERE id_stock_check = $id_stock_check";
	dbQuery("UPDATE tbl_stock_check SET qty_after = '$new_qty' WHERE id_stock_check = $id_stock_check");
	$message = "แก้ไข ".$product->reference." จาก $qty เป็น $new_qty เรียบร้อยแล้ว";
	header("location: ../index.php?content=checkstock&id_zone=$id_zone&message=$message");
}
if(isset($_GET['delete'])){
	$id_stock_check = $_GET['id_stock_check'];
	$id_zone = $_GET['id_zone'];
	$check_stock = new checkstock();
	$check_stock->get_stock_check($id_stock_check);
	$qty = $check_stock->qty;
	$product = new product();
	$product->product_attribute_detail($check_stock->id_product_attribute);
	dbQuery("UPDATE tbl_stock_check SET qty_after = 0 WHERE id_stock_check = $id_stock_check");
	$message = "ลบ ".$product->reference." ออกจากโซนนี้เรียบร้อยแล้ว";
	header("location: ../index.php?content=checkstock&id_zone=$id_zone&message=$message");
}
if(isset($_GET['save_diff'])){
	$id_employee = $_GET['id_employee'];
	$id_check = $_GET['id_check'];
	$id_employee = $_COOKIE['user_id'];
			dbQuery("INSERT INTO tbl_diff (id_zone,id_product_attribute,qty_add,qty_minus,id_employee,status_diff) SELECT id_zone,id_product_attribute,qty_after-qty_before,qty_before-qty_after,$id_employee,0 FROM tbl_stock_check WHERE id_check = $id_check");
			dbQuery("UPDATE tbl_diff SET qty_add = '0' WHERE qty_add < 0 ");
			dbQuery("UPDATE tbl_diff SET qty_minus = '0' WHERE qty_minus < 0");
				dbQuery("DELETE FROM tbl_diff WHERE qty_add = 0 AND qty_minus = 0");
	dbQuery("UPDATE tbl_check SET status = 3 WHERE id_check = $id_check");
	$message = "บันทึกยอดต่างเรียบร้อยแล้ว";
	header("location: ../index.php?content=ProductCount&view_stock_diff=y&id_check=$id_check&message=$message");
}
?>
<?php
	if(isset($_GET['get_data'])&&isset($_GET['id_check'])){
	$id_check = $_GET['id_check'];
	$data = "";
	$sql = dbQuery("SELECT id_zone, status FROM tbl_stock_check WHERE id_check = $id_check GROUP BY id_zone ");
	while($rs=dbFetchArray($sql)){
		$id_zone = $rs['id_zone'];
		$status = $rs['status'];
		switch($status){
		case '-1':
			$class = "zone no-item";
			break;
		case '0':
			$class = "zone before-check";
			break;
		case '1':
			$class = "zone active";
			break;
		default :
			$class = "zone before-check";
			break;	
		}
		$zone_name = get_zone($id_zone);
		$data .="<div class='$class' title='$zone_name'>&nbsp;</div>";
	}
	echo $data;
	}
		
?>