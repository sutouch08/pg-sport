<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../../invent/function/tools.php";
if(isset($_GET['new']) && isset($_POST['id_customer']) && isset($_GET['id_sale']) ){ 
	$id_customer = $_POST['id_customer'];
	$id_sale = $_GET['id_sale'];
	$is = check_cart($id_sale);
	$content = $_GET['content'];
	if( ! $is){
		$rs = dbQuery("INSERT INTO tbl_cart (id_customer, id_sale, valid, date_add) VALUES (".$id_customer.", ".$id_sale.", 0, NOW())");
	}else{
		$id_cart = $is['id_cart'];
		$rs = dbQuery("UPDATE tbl_cart SET id_customer = ".$id_customer." WHERE id_cart = ".$id_cart);		
	}
	header("location: ../index.php?content=$content");
}



if(isset($_GET['confirm_order']) && isset($_POST['id_cart']) && isset($_POST['id_customer']) )
{
	$id_cart = $_POST['id_cart'];
	$id_customer = $_POST['id_customer'];
	$id_employee = $_COOKIE['user_id'];
	$comment = $_POST['comment'];
	$qs = dbQuery("SELECT * FROM tbl_cart_product WHERE id_cart = ".$id_cart);
	$row = dbNumRows($qs);
	$i = 1;
	$data = array();
	if(dbNumRows($qs) > 0) :
		$error = "";
		$product = new product();
		while($rs = dbFetchArray($qs) )
		{
			$id_product_attribute = $rs['id_product_attribute'];
			$product->product_attribute_detail($id_product_attribute);
			$qty = $rs['qty'];
			$stock_qty = $product->available_order_qty($id_product_attribute);
			if($qty > $stock_qty){
				$error .=  $product->reference." : มียอดคงเหลือแค่ ".$stock_qty;
			}else{
				$arr = array("id_product_attribute"=>$id_product_attribute, "qty"=>$qty);
				array_push($data, $arr);
			}
		}
		if($error != ""){
			header("location: ../index.php?content=cart&error=".$error);
		}else{
			$reference = get_max_role_reference("PREFIX_ORDER",1);
			$qr = dbQuery("INSERT INTO tbl_order (reference, id_customer, id_employee, id_cart, current_state, payment, comment, valid, role, date_add, order_status) VALUES ('".$reference."', ".$id_customer.", ".$id_employee.", ".$id_cart.", 1, 'เครดิต', '".$comment."', 0, 1, NOW(), 1)");
			$id_order = dbInsertId();
			$order = new order($id_order);	
			foreach($data as $rs)
			{
				$order->insertDetail($rs['id_product_attribute'], $rs['qty']);
			}
			dbQuery("UPDATE tbl_cart SET valid = 1 WHERE id_cart = ".$id_cart);
			header("location: ../index.php?content=cart&message=ดำเนินการสั่งซื้อเรียบร้อยแล้ว");
		}	
	else :
		$message = "ไม่พบข้อมูลในตะกร้าสินค้า กรุณาติดต่อผู้ดูแลระบบ";
		header("location: ../index.php?content=cart&error=".$message);
	endif;
}


if(isset($_GET['new_request'])&&isset($_POST['id_customer'])){ 
$id = $_POST['id_customer'];
if(isset($_COOKIE['id_request_order'])){ dbQuery("UPDATE tbl_request_order SET id_customer = $id  WHERE id_request_order = ".$_COOKIE['id_request_order']);}
setcookie("id_customer",$id,time()+(3600*8*7), '/');
header("location: ../request/index.php?content=order");
}

if(isset($_GET['cancle_request'])){
	$id_request_order = $_GET['id_request_order'];
	$id_customer= $_GET['id_customer'];
 setcookie("id_customer","",time()-3600, '/');
 setcookie("id_request_order","",time()-3600, '/');
 if($id_request_order !=""){
 if( dbQuery("DELETE FROM tbl_request_order_detail WHERE id_request_order = $id_request_order")){
	 if(dbQuery("DELETE FROM tbl_request_order WHERE id_request_order = $id_request_order")){
		 header("location: ../request/index.php?content=order");
	 }else{
		 $message = "ลบตะกร้าสินค้าไม่สำเร็จ";
		 header("location: ../request/index.php?content=order&error=$message");
	 }
 }else{
	 
		 $message = "ลบรายการสินค้าในตะกร้าสินค้าไม่สำเร็จ";
		 header("location: ../request/index.php?content=order&error=$message");
 }
 }else{
	 header("location: ../request/index.php?content=order");
 }
}
if(isset($_GET['cancle'])){
	$id_cart = $_GET['id_cart'];
	$qs = dbQuery("DELETE FROM tbl_cart_product WHERE id_cart = ".$id_cart);
	if($qs){
		$qr = dbQuery("DELETE FROM tbl_cart WHERE id_cart = ".$id_cart);
		if($qr){
			header("location: ../index.php?content=order&message=ยกเลิกรายการเรียบร้อย");
		}else{
			header("location: ../index.php?content=order&error=ลบรายการในตะกร้าสำเร็จแต่ยกเลิกตะกร้าไม่สำเร็จ");
		}
	}else{
		header("location: ../index.php?content=order&error=ยกเลิกรายการไม่สำเร็จ");
	}
	
}

if(isset($_GET['text'])&&isset($_GET['id_customer'])){
	$text = $_GET['text'];
	$id_customer = $_GET['id_customer'];
	$html = "";
	$sql = dbQuery("SELECT id_product FROM tbl_product  WHERE product_code LIKE '%$text%' AND active =1");
	$row = dbNumRows($sql); 
	if($row>0){
		$i=0;
		while($i<$row){
			list($id_product) = dbFetchArray($sql);
			$product = new product();
			$product->product_detail($id_product, $id_customer);
			$config = getConfig("ATTRIBUTE_GRID_HORIZONTAL");
			$sqr = dbQuery("SELECT id_$config FROM tbl_product_attribute WHERE id_product = $id_product AND id_$config !=0 GROUP BY id_$config");
			$colums = dbNumRows($sqr);
			$table_w = "style='width:".(70*($colums+1)+100)."px;'";
			$html .="<div class='item2 col-lg-3 col-md-3 col-sm-6 col-xs-12'>					
			<div class='product'> 
			<div class='image'><a href='#' onclick='getData(".$product->id_product.")'>".$product->getCoverImage($product->id_product,3,"img-responsive")."</a></div>
			<div class='description'>
				<a href='#' onclick='getData(".$product->id_product.")'>".$product->product_code." <br> ".$product->product_name."</a>
			</div>
			  <div class='price'>"; if($product->product_discount>0){ $html .="<span class='old-price'>".number_format($product->product_price,2)."</span>";}else{ $html .="<span></span>";} $html .=" </div>
			  <div class='action-control'> <a href='#' data-toggle='modal' data-target='#".$product->id_product."'><span class='btn btn-primary' style='width:80%;'>".number_format($product->product_sell,2)."</span></a>  </div></div></div>";
			$i++;
		}
	}else{ 
		$html .="<h4 style='align:center;'>ไม่มีรายการสินค้าในหมวดหมู่นี้</h4>";
	}
	 echo $html;
}

if(isset($_GET['getData'])&&isset($_GET['id_product'])){
			$id_product = $_GET['id_product'];
			$id_cus = 0;
			$product = new product();
			$product->product_detail($id_product, $id_cus);
			$config = getConfig("ATTRIBUTE_GRID_HORIZONTAL");
			$sqr = dbQuery("SELECT id_$config FROM tbl_product_attribute WHERE id_product = $id_product AND id_$config !=0 GROUP BY id_$config");
			$colums = dbNumRows($sqr);
			$sqm = dbQuery("SELECT id_color, id_size, id_attribute FROM tbl_product_attribute WHERE id_product = $id_product LIMIT 1");
			list($co, $si, $at) = dbFetchArray($sqm);
			if($co !=0){ $co =1;}
			if($si !=0){ $si = 1;}
			if($at !=0){ $at = 1;}
			$count = $co+$si+$at;
			if($count >1){	$table_w = (70*($colums+1)+100); }else if($count ==1){ $table_w = 800; }
			$dataset = $product->request_attribute_grid($product->id_product);
			$dataset .= "|".$table_w;
			$dataset .= "|".$product->product_code;
			echo $dataset;
}


if(isset($_GET['add_to_order'])){
	$id_request_order= $_POST['id_request_order'];
	$id_customer = $_POST['id_customer'];
	$order_qty = $_POST['qty'];
	$id_employee = $_COOKIE['user_id'];
	$i =0;
	if($id_request_order !=""){
		foreach ($order_qty as $id=>$qty ){	
			if($qty !=""){
				$product = new product();
				$product->product_attribute_detail($id);
				$product->product_detail($product->id_product, $id_customer);
				$id_product_attribute = $id;
				$id_product = $product->id_product;
				$sql = dbQuery("SELECT id_request_order_detail, qty FROM tbl_request_order_detail WHERE id_request_order = $id_request_order AND id_product_attribute = $id_product_attribute");
				$row = dbNumRows($sql);
				if($row>0){
					list($id_request_order_detail, $old_qty) = dbFetchArray($sql);
					$new_qty = $old_qty + $qty;
					dbQuery("UPDATE tbl_request_order_detail SET qty = $new_qty WHERE id_request_order_detail = $id_request_order_detail");
				}else{
					dbQuery("INSERT INTO tbl_request_order_detail (id_request_order, id_product, id_product_attribute, qty) VALUES ( $id_request_order, $id_product, $id_product_attribute, $qty )");
				}
			}
			$i++;
		}
		header("location: ../request/index.php?content=order");
	}else{
		$reference = get_max_request_reference("PREFIX_REQUEST_ORDER");
		$customer = new customer($id_customer);
		$id_sale = $customer->id_sale;
		dbQuery("INSERT INTO tbl_request_order (reference, id_customer,id_employee, id_sale ) VALUES ('$reference', $id_customer, $id_employee, $id_sale )");
		$id_request = dbInsertId();
		if($id_request != ""){
			$id_request_order = $id_request;
			setcookie("id_request_order",$id_request_order,time()+(3600*8*7), '/');
				foreach ($order_qty as $id=>$qty ){	
					if($qty !=""){
						$product = new product();
						$product->product_attribute_detail($id);
						$product->product_detail($product->id_product, $order->id_customer);
						$id_product_attribute = $id;
						$id_product = $product->id_product;
						$sql = dbQuery("SELECT id_request_order_detail, qty FROM tbl_request_order_detail WHERE id_request_order = $id_request_order AND id_product_attribute = $id_product_attribute");
						$row = dbNumRows($sql);
						if($row>0){
							list($id_request_order_detail, $old_qty) = dbFetchArray($sql);
							$new_qty = $old_qty + $qty;
							dbQuery("UPDATE tbl_request_order_detail SET qty = $new_qty WHERE id_request_order_detail = $id_request_order_detail");
						}else{
							dbQuery("INSERT INTO tbl_request_order_detail (id_request_order, id_product, id_product_attribute, qty) VALUES ( $id_request_order, $id_product, $id_product_attribute, $qty )");
						}
					}
					$i++;
			}
			header("location: ../request/index.php?content=order");
		}else{
			$message = "ไม่สามารถเพิมการร้องขอได้";
			header("location: ../request/index.php?content=order&error=$message");
		}
	}		
}
if(isset($_GET['drop_request_detail'])&&isset($_GET['id_request_order'])&&isset($_GET['id_product_attribute'])){
	$id_request_order = $_GET['id_request_order'];
	$id_product_attribute = $_GET['id_product_attribute'];
	if(dbQuery("DELETE FROM tbl_request_order_detail WHERE id_request_order = $id_request_order AND id_product_attribute = $id_product_attribute")){
		echo "deleted";
	}else{
		echo "fail";
	}
}
if(isset($_GET['update_request_detail'])&&isset($_GET['id_request_order'])){
	$id_request_order = $_GET['id_request_order'];
	$product = new product();
	$html = "";
	$sql = dbQuery("SELECT id_request_order_detail, id_request_order, id_product_attribute, qty FROM tbl_request_order_detail WHERE id_request_order = '$id_request_order'");
							$row = dbNumRows($sql);
							if($row == "0"){
								$html .="<tr><td><h3></h3><h3 align='center'> ยังไม่มีรายการสินค้า (No item in cart) </h3><h3></h3></td>	</tr>";
							}else{
							$i=0;
							$sumtotal_cart = 0;
							while($i<$row){
								list($id_request_order_detail, $id_request_order, $id_product_attribute,$qty) = dbFetchArray($sql);
								$product->product_attribute_detail($id_product_attribute);
								$img = $product->image_attribute;
								$reference = $product->reference;
								$sumtotal_cart = $sumtotal_cart + $qty;
					$html .= "<tr class='miniCartProduct'>
								<td style='width:20%' class='miniCartProductThumb'><img src='$img' /></td>
								 <td style='width:40%'><div class='miniCartDescription'><h4> ".$product->reference."</h4><span class='size'>".$product->color_name."&nbsp;".$product->size_name." </span></div></td>
								<td  style='width:10%' class='miniCartQuantity' align='left'>$qty </td>
								<td  style='width:10%' class='delete' align='center' ><a onclick='drop_request_detail($id_request_order,$id_product_attribute)'><span class='glyphicon glyphicon-trash'></span> </a></td>
					  </tr>";
							$i++;
							}
						}
	echo $html;
}
if(isset($_GET['update_request_detail_mobile'])&&isset($_GET['id_request_order'])){
	$id_request_order = $_GET['id_request_order'];
	$product = new product();
	$html = "";
	$sql = dbQuery("SELECT id_request_order_detail, id_request_order, id_product_attribute, qty FROM tbl_request_order_detail WHERE id_request_order = '$id_request_order'");
							$row = dbNumRows($sql);
							if($row == "0"){
								$html .="<tr><td><h3></h3><h3 align='center'> ยังไม่มีรายการสินค้า (No item in cart) </h3><h3></h3></td>	</tr>";
							}else{
							$i=0;
							$sumtotal_cart = 0;
							while($i<$row){
								list($id_request_order_detail, $id_request_order, $id_product_attribute,$qty) = dbFetchArray($sql);
								$product->product_attribute_detail($id_product_attribute);
								$img = $product->image_attribute;
								$reference = $product->reference;
								$sumtotal_cart = $sumtotal_cart + $qty;
					$html .= "<tr class='miniCartProduct'>
								<td style='width:20%' class='miniCartProductThumb'><img src='$img' /></td>
								 <td style='width:40%'><div class='miniCartDescription'><h4> ".$product->reference."</h4><span class='size'>".$product->color_name."&nbsp;".$product->size_name." </span></div></td>
								<td  style='width:10%' class='miniCartQuantity' align='left'>$qty </td>
								<td  style='width:10%' class='delete' align='center' ><a onclick='drop_request_detail($id_request_order,$id_product_attribute)'><span class='glyphicon glyphicon-trash'></span> </a></td>
					  </tr>";
							$i++;
							}
						}
	echo $html;
}
if(isset($_GET['update_request_total_mobile'])&&isset($_GET['id_request_order'])){
	$id_request_order = $_GET['id_request_order'];
	$html = "";
	$sql = dbQuery("SELECT SUM(qty) FROM tbl_request_order_detail LEFT JOIN tbl_request_order ON tbl_request_order_detail.id_request_order = tbl_request_order.id_request_order WHERE tbl_request_order_detail.id_request_order = '$id_request_order' AND status=0");
				$row = dbNumRows($sql);
				if($row<1){ $qty = "ว่างเปล่า:Empty";}else{ list($qty) = dbFetchArray($sql); }
				$html .= "ตะกร้า $qty รายการ";
				echo $html;
}
if(isset($_GET['update_sumary'])&&isset($_GET['id_request_order'])){
	$id_request_order = $_GET['id_request_order'];
	$cart_mini = new cart();
	$html = $cart_mini->request_sumary($id_request_order);
	echo $html;
}
	
if(isset($_GET['confirm_request'])&&isset($_GET['id_request_order'])){
	$id_request_order = $_GET['id_request_order'];
	if(dbQuery("UPDATE tbl_request_order SET status = '1' WHERE id_request_order = '$id_request_order'")){
		setcookie("id_request_order","",time()-(3600*8*7), '/');
		setcookie("id_customer","",time()-3600, '/');
		header("location: ../request/index.php?content=cart&finish=y");
	}else{
		$message = "ไม่สามารถทำรายการได้";
		header("location: ../request/index.php?content=cart&error=$message");
	}
}
//***************************************** เพิ่มจำนวนทีละ 1 ****************************************//
if(isset($_GET['increase_qty'])&&isset($_GET['id_request_order'])&&isset($_GET['id_product_attribute'])){
	$id_request_order = $_GET['id_request_order'];
	$id_product_attribute = $_GET['id_product_attribute'];
	list($qty) = dbFetchArray(dbQuery("SELECT qty FROM tbl_request_order_detail WHERE id_request_order = $id_request_order AND id_product_attribute = $id_product_attribute"));
	$new_qty = $qty+1;
	if(dbQuery("UPDATE tbl_request_order_detail SET qty = $new_qty WHERE id_request_order = $id_request_order AND id_product_attribute = $id_product_attribute")){
		list($total) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_request_order_detail WHERE id_request_order = $id_request_order"));
		echo "ok:$new_qty:$total";
	}else{
		echo"fail";
	}
}
//***************************************** ลดจำนวนทีละ 1 ****************************************//
if(isset($_GET['decrease_qty'])&&isset($_GET['id_request_order'])&&isset($_GET['id_product_attribute'])){
	$id_request_order = $_GET['id_request_order'];
	$id_product_attribute = $_GET['id_product_attribute'];
	list($qty) = dbFetchArray(dbQuery("SELECT qty FROM tbl_request_order_detail WHERE id_request_order = $id_request_order AND id_product_attribute = $id_product_attribute"));
	$new_qty = $qty-1;
	if($new_qty ==0){
		if(dbQuery("DELETE FROM tbl_request_order_detail WHERE id_request_order = $id_request_order AND id_product_attribute = $id_product_attribute")){
			list($total) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_request_order_detail WHERE id_request_order = $id_request_order"));
			echo "ok:$new_qty:$total";
		}
	}else if($new_qty < 0){
		echo "false";
	}else{
	if(dbQuery("UPDATE tbl_request_order_detail SET qty = $new_qty WHERE id_request_order = $id_request_order AND id_product_attribute = $id_product_attribute")){
		list($total) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_request_order_detail WHERE id_request_order = $id_request_order"));
		echo "ok:$new_qty:$total";
	}else{
		echo"fail";
	}
	}
}

//************************************ Update qty  ***************************************//
if(isset($_GET['update_qty'])&&isset($_GET['id_request_order'])&&isset($_GET['id_product_attribute'])){
	$id_request_order = $_GET['id_request_order'];
	$id_product_attribute = $_GET['id_product_attribute'];
	$qty = $_GET['qty'];
	if($qty ==0){
		if(dbQuery("DELETE FROM tbl_request_order_detail WHERE id_request_order = $id_request_order AND id_product_attribute = $id_product_attribute")){
			list($total) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_request_order_detail WHERE id_request_order = $id_request_order"));
			echo"ok:$qty:$total";
		}
	}else if($qty < 0){
		echo "false";
	}else{
	if(dbQuery("UPDATE tbl_request_order_detail SET qty = $qty WHERE id_request_order = $id_request_order AND id_product_attribute = $id_product_attribute")){
		list($total) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_request_order_detail WHERE id_request_order = $id_request_order"));
			echo"ok:$qty:$total";
	}else{
		echo"fail";
	}
	}
}

if(isset($_GET['add_to_cart'])){
	$id_sale = $_COOKIE['sale_id'];
	$id_product = $_POST['id_product'];
	$id_customer = $_POST['id_customer'];
	$order_qty = $_POST['qty'];
	$rs = check_cart($id_sale);
	if($rs == false){
		$id_customer = $_POST['id_customer'];
		$qs = dbQuery("INSERT INTO tbl_cart (id_customer, id_sale, valid, date_add) VALUES (".$id_customer.", ".$id_sale.", 0, NOW() )");
		$id_cart = dbInsertId();
	}else{
		$id_cart = $rs['id_cart'];
	}
	foreach($order_qty as $color => $items)
	{
		foreach($items as $id_product_attribute => $qty)
		{
			if($qty != "")
			{
				$is = isDuplicate($id_cart, $id_product_attribute);
				if( $is != false )
				{
					dbQuery("UPDATE tbl_cart_product SET qty = qty+".$qty." WHERE id_cart_product = ".	$is);
				}else{
				dbQuery("INSERT INTO tbl_cart_product (id_cart, id_product_attribute, qty, date_add) VALUES (".$id_cart.", ".$id_product_attribute.", ".$qty.", NOW())");
				}
			}
		}
	}
	echo "success";
}

if( isset($_GET['delete_cart_product']) && isset($_GET['id_cart']) && isset($_GET['id_product_attribute']) )
{
	$qs = dbQuery("DELETE FROM tbl_cart_product WHERE id_cart = ".$_GET['id_cart']." AND id_product_attribute = ".$_GET['id_product_attribute']);
	if($qs)
	{
		$cart = new cart();
		$total = $cart->total_cart_amount($_GET['id_cart']);
		echo $total;
	}else{
		echo "fail";
	}
}

function isDuplicate($id_cart, $id_product_attribute)
{
	$id = false;
	$qs = dbQuery("SELECT id_cart_product FROM tbl_cart_product WHERE id_cart = ".$id_cart." AND id_product_attribute = ".$id_product_attribute);
	if(dbNumRows($qs) > 0){
		$rs = dbFetchArray($qs);
		$id = $rs['id_cart_product'];
	}
	return $id;
}

?>