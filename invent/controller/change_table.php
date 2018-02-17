<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<body>
<?php
require "../../library/config.php";
require "../../library/functions.php";
//require "../function/tools.php";
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
	function order_sold($id_order){
		 $sql = dbQuery("SELECT id_product_attribute, SUM(qty) AS qty FROM tbl_qc WHERE id_order = $id_order AND valid= 1 GROUP BY id_product_attribute");
		 $row = dbNumRows($sql);
		 $c = 0; $f =0;
		 while($rs = dbFetchArray($sql)){
			 $id_product_attribute = $rs['id_product_attribute'];
			 $sold_qty = $rs['qty'];
			 $order = new order($id_order);
			 $id_role = $order->role;
			 $id_sale = $order->id_sale;
			 $id_customer = $order->id_customer;
			 $order->order_product_detail($id_product_attribute);
			 $id_product = $order->id_product;
			 $product_name = $order->product_name;
			 $product_reference = $order->product_reference;
			 $barcode = $order->barcode;
			 $product_price = $order->product_price;
			 $order_qty = $order->product_qty;
			 $reduction_percent = $order->reduction_percent;
			 $reduction_amount = $order->reduction_amount;
			 $final_price = $order->final_price;
			 $full_amount = $sold_qty * $product_price;
			 $sold_amount = $sold_qty * $final_price;
			 $discount_amount = $full_amount - $sold_amount;
			 $total_amount = $sold_qty * $final_price;
			 $date_upd = $order->date_upd;
			
			/* echo"*******************************************************************************<br>";
			 echo "id_product_attribute = $id_product_attribute <br>";
			 echo "sold_qty= ".$sold_qty."<br>"."id_sale = $id_sale <br> id_customer =  $id_customer <br>
			 id_product = $id_product <br>
			 product_name = $product_name <br>
			 reference = $product_reference <br>
			 barcode = $barcode <br>
			 price = $product_price <br>
			 order_qty = $order_qty <br>
			 percent = $reduction_percent <br>
			 amount = $reduction_amount <br>
			 final_price = $final_price <br>
			 full_amount = $full_amount <br>
			 sold_amount = $sold_amount <br>
			 discount_amount = $discount_amount <br>
			 total_amount = $total_amount <br>";
			 echo "*******************************************************************************************<br>";*/
			$qs = dbQuery("INSERT INTO tbl_order_detail_sold(id_order, id_role, id_customer, id_sale, id_product, id_product_attribute, product_name, product_reference, barcode, product_price, order_qty, sold_qty, reduction_percent, reduction_amount, discount_amount, final_price, total_amount, date_upd) VALUES( $id_order, $id_role, $id_customer, $id_sale, $id_product, $id_product_attribute, '$product_name', '$product_reference', '$barcode', $product_price, $order_qty, $sold_qty, $reduction_percent, $reduction_amount, $discount_amount, $final_price, $total_amount, '$date_upd')");
			if($qs){ 
			$c++;
			//return true;
			}else{ 
			$f++;
			//return false;
			}
		 }
		 if($row ==0){ 
		 	$mes = "<span style='color:red;'>id_order $id_order : ไม่มีรายการให้นำเข้า </span><br>";
			echo $mes;
			return false;
		 }else if($c==$row){
			 $mes = "id_order $id_order :สำเร็จ $c รายการ  ไม่สำเร็จ 0 รายการ<br>";
			 echo $mes;
			 return true;
		 }else if($c ==0){
			 $mes = "<span style='color:red;'>id_order $id_order :ไม่สำเร็จ $row รายการ</span><br>";
			 echo $mes;
			 return false;
		 }else{ 
			$mes = "<span style='color:red;'>id_order $id_order :ไม่สำเร็จ</span><br>";
			echo $mes;
			return false;
		 }
	 }
$sql = dbQuery("SELECT id_order FROM tbl_order WHERE current_state IN(6,7,9)");
$row = dbNumRows($sql);
echo "ทั้งหมด $row ออเดอร์ <br>";
$n = 0;
$s = 0;
while($rs = dbFetchArray($sql)){
	$id_order = $rs['id_order'];
	if(order_sold($id_order)){
		$n++;
	}else{
		$s++;
	}	
}
echo" สำเร็จ $n รายการ<br>";
echo "ไม่สำเร็จ $s รายการ <br>";

?>
</body>
</html>