<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
$this_date= date('Y-m-d');
ini_set('max_execution_time', 300); //300 seconds = 5 minutes

if(isset($_GET['get_zone'])&& isset($_REQUEST['term'])){
	$sql = dbQuery("SELECT id_zone, zone_name FROM tbl_zone WHERE zone_name LIKE '%".$_REQUEST['term']."%'");
	$data = array();
	while($rs = dbFetchArray($sql)){
		$data[] = $rs['id_zone'].":".$rs['zone_name'];
	}
	echo json_encode($data);
	
}
function getSoldQty($id_order, $id_pa)
{
	$sc = 0;
	$qs = dbQuery("SELECT sold_qty FROM tbl_order_detail_sold WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}

function getMovement($reference, $id_pa)
{
	$sc = 0;
	$qs = dbQuery("SELECT SUM(move_out) AS qty FROM tbl_stock_movement WHERE id_product_attribute = ".$id_pa." AND reference = '".$reference."'");
	$rs = dbFetchArray($qs);
	if( ! is_null($rs['qty']) )
	{
		$sc = $rs['qty'];
	}
	return $sc;
}

function getOrderRole($id_order)
{
	$sc = 1;
	$qs = dbQuery("SELECT role FROM tbl_order WHERE id_order = ".$id_order);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;		
}


function getAction($prepared, $checked, $sold, $movement)
{
	$sc = 'rebill';
	if( $prepared == $checked && $checked == $sold && $sold == $movement )
	{
		$sc = 'delete';
	}
	else if( $prepared > $checked && $checked == $sold && $sold == $movement )
	{
		$sc = 'move';	
	}
	else if( $checked != $sold OR $sold != $movement )
	{
		$sc = 'rebill';	
	}
	return $sc;
}
//-----------------------------  ตรวจสอบออเดอร์ค้างใน Cancle zone -------------------//
if( isset( $_GET['verifyOrder'] ) )
{
	require_once '../function/qc_helper.php';
	//-------  ต้องการทราบ จำนวนที่สั่ง / จำนวนที่จัด / จำนวนที่ตรวจ / จำนวนที่บันทึกขาย / จำนวนที่บันทึกความเคลื่อนไหว ----//
	$sc 			= '';
	$id_order	= $_POST['id_order'];
	$order		= new order();
	$reference	= get_order_reference($id_order);
	$c_state		= getCurrentState($id_order);
	$role			= getOrderRole($id_order);
	$qs 			= dbQuery("SELECT * FROM tbl_cancle WHERE id_order = ".$id_order);
	$rebill			= 0;
	$move 		= 0;
	$sc .= '<table class="table table-bordered"	>';
	$sc .= '<tr style="font-size:12px;">';
	$sc .= '<th style="width:25%; text-align:center;">สินค้า</th>';
	$sc .= '<th style="width:10%; text-align:center;">สั่ง</th>';
	$sc .= '<th style="width:10%; text-align:center;">จัด</th>';
	$sc .= '<th style="width:10%; text-align:center;">ตรวจ</th>';
	$sc .= '<th style="width:15%; text-align:center;">บันทึกขาย</th>';
	$sc .= '<th style="width:15%; text-align:center;">เคลื่อนไหว</th>';
	$sc .= '<th style="width:15%; text-align:center;">การกระทำ</th>';
	$sc .= '</tr>';
	if( dbNumRows($qs) > 0 )
	{
		while( $rs = dbFetchArray($qs) )
		{
			$id_pa 		= $rs['id_product_attribute'];
			$order_qty	= sumOrderQty($id_order, $id_pa);
			$prepared	= sumPreparedQty($id_order, $id_pa);
			$checked	= sumCheckedQty($id_order, $id_pa);
			$sold_qty	= getSoldQty($id_order, $id_pa);
			$movement	= getMovement($reference, $id_pa);
			$act			= getAction($prepared, $checked, $sold_qty, $movement);
			if( $role == 5 && $prepared > $checked && $rs['qty'] == ($prepared - $checked) )
			{
				$action 	= 'ย้าย'; $move++;
			}
			else
			{
				$action		=  $act == 'delete' ? 'ลบ' : ( $act == 'move' ? 'ย้าย' : 'ย้อนกลับ');
			}
			
			if( $act == 'move' ){ $move++; }else if( $act == 'rebill' ){ $rebill++; }
			$sc .= '<tr id="row_'.$id_order .'_'. $id_pa.'" style="font-size:12px;">';
			$sc .= '<td>'.get_product_reference($id_pa).'</td>';
			$sc .= '<td align="center">'. number_format($order_qty) .'</td>';
			$sc .= '<td align="center">'. number_format($prepared) .'</td>';
			$sc .= '<td align="center">'. number_format($checked) .'</td>';
			$sc .= '<td align="center">'. number_format($sold_qty) .'</td>';
			$sc .= '<td align="center">'. number_format($movement) .'</td>';
			if( $role == 5 && $prepared == $checked && $checked == $movement )
			{ 
				$sc .= '<td align="center">'. $action .' &nbsp; <a href="javascript:void(0)" onClick="deleteCancleItem('.$id_order.', '.$id_pa.')">ลบ</a></td>';
			}
			else
			{
				$sc .= '<td align="center">'. $action .'</td>';
			}
			$sc .= '</tr>';			
		}
		if( $rebill == 0 && $move == 0 && $c_state != 8)
		{
			$sc .= '<tr>';
			$sc .= '<td colspan="6">รายการทั้งหมดถูกบันทึกไว้อย่างถูกต้องแล้ว คุณสามารถลบออเดอร์นี้ออกจาก Cancle Zone ได้ทั้นที</td>';
			$sc .= '<td align="center><button type="button" class="btn btn-sm btn-danger btn-block" onClick="removeCancle('.$id_order.')">ลบ</button></td>';
			$sc .= '</tr>';
		}
		
	}
	if( $c_state == 8 )
	{
		$sc .= '<tr>';
		$sc .= '<td colspan="7" align="center">ออเดอร์นี้ถูกย้ายมาที่ Cancle เพราะออเดอร์ถูกยกเลิกหลังจากมีการจัดสินค้าไปแล้ว คุณต้อง "ย้ายสินค้ากลับเข้าโซนปกติ"</td>';
		$sc .= '</tr>';	
	}
	$sc .= '</table>';
	echo $sc;
}

//---------------------  ลบรายการที่อยู่ใน Cancle Zone  -----------------///
if( isset( $_GET['removeItemsFromCancleZone'] ) )
{
	$sc = 'fail';
	$id = $_POST['id_order'];
	$qs = dbQuery("DELETE FROM tbl_cancle WHERE id_order = ".$id);
	if( $qs )
	{
		$sc = 'success';
	}
	echo $sc;	
}

if( isset( $_GET['deleteCancleItem'] ) )
{
	$sc = 'fail';
	$id_order = $_POST['id_order'];
	$id_pa 	= $_POST['id_product_attribute'];
	$qs = dbQuery("DELETE FROM tbl_cancle WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_pa);
	if( $qs)
	{
		$sc = 'success';
	}
	echo $sc;
}

//******************************** เพิ่มหัวเอกสารการรับสินค้าเข้า ***************************************//
if(isset($_GET['add'])&&isset($_POST['recieved_no'])){
	$recieved_no = $_POST['recieved_no'];
	$reference = $_POST['reference'];
	$date_add = dbDate($_POST['date']);
	if($date_add == "1970-01-01"){ $date_add = date('Y-m-d'); }
	$employee_id = $_COOKIE['user_id'];
	$role = $_POST['role'];
	if(dbQuery("INSERT INTO tbl_recieved_product (recieved_product_no, reference_no, date, id_employee, role) VALUES ('$recieved_no', '$reference', '$date_add', $employee_id, $role)")){
		list($id_recieved_product)=dbFetchArray(dbQuery("SELECT id_recieved_product FROM tbl_recieved_product WHERE recieved_product_no = '$recieved_no'"));
		$first_use = dbNumRows(dbQuery("SELECT id_stock_movement FROM tbl_stock_movement WHERE id_stock_movement = 1")); 
		if($first_use<1){ $import = "&first=y"; }else{ $import="";}
		if($id_recieved_product !=""){
			header("location: ../index.php?content=product_in&add=y&id_recieved_product=$id_recieved_product&date_add=$date_add$import");
		}
	}else{
		$message = "เพิ่มรายการรับไม่สำเร็จ อาจมีบางอย่างผิดพลาด";
		header("location: ..inedx.php?content=product_in&add=y&error=$message");
	}
}
//********************************** เพิ่มรายการรับสินค้าเข้าทีละรายการ(ยังไม่บันทึกยอด) *****************************************//
if(isset($_GET['add_detail'])&&isset($_GET['id_recieved_product'])){
	$id_recieved_product = $_GET['id_recieved_product']; 
	$barcode_item = trim($_POST['barcode_item']);
	if(isset($_POST['barcode_zone'])){ 	$barcode_zone = trim($_POST['barcode_zone']); }else{ $barcode_zone = ""; }
	if(isset($_POST['zone_name'])){ 	$zone_name = $_POST['zone_name']; }else{ $zone_name = "";}
	if(isset($_POST['id_zone'])){ 	$id_zone = $_POST['id_zone']; }else{ $id_zone = "";}
	$pos_qty = $_POST['qty']; 
	$id_warehouse = $_POST['id_warehouse'];
	$product = new product();
    list($date_add) = dbFetchArray(dbQuery("SELECT date FROM tbl_recieved_product WHERE id_recieved_product = $id_recieved_product"));
	$arr = $product->check_barcode($barcode_item); ///ดึง id_product_attribute และ จำนวน จากบาร์โค้ด คืนค่ามาเป็น array [id_product_attribute] และ [qty] ตามลำดับ 
	$id_product_attribute = $arr['id_product_attribute'];
	$qty = $pos_qty*$arr['qty'];
	if($id_product_attribute ==""){ // ตรวจสอบว่ามีรหัสสินค้าหรือไม่ ถ้าไม่มีให้แสดงข้อผิดพลาด
			$message ="รหัสสินค้าไม่ถูกต้อง กรุณาตรวจสอบ";
			header("location: ../index.php?content=product_in&edit=y&id_recieved_product=$id_recieved_product&error=$message&id_warehouse=$id_warehouse&date_add=$date_add");
			exit;
	}
	if($id_zone ==""){
		if($barcode_zone !=""){ // ตรวจสอบว่ารหัสโซนหรือชื่อโซนถูกต้องหรือไม่ 
				list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_zone WHERE barcode_zone = '$barcode_zone' AND id_warehouse = $id_warehouse")); 	
		}else if($zone_name !=""){
				list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_zone WHERE zone_name = '$zone_name' AND id_warehouse = $id_warehouse"));
		}
	}
		list($barcode_zone, $name_zone) = dbFetchArray(dbQuery("SELECT barcode_zone, zone_name FROM tbl_zone WHERE id_zone = $id_zone"));
	if($id_zone ==""){ ///ถ้าไม่มีรหัสหรือชื่อโซน ให้แจ้งข้อผิดพลาด
			$message ="รหัสหรือชื่อโซนไม่ถูกต้อง หรือ ไม่มีโซนนี้ในคลังที่เลือกอยู่ กรุณาตรวจสอบ"; 
			header("location: ../index.php?content=product_in&edit=y&id_recieved_product=$id_recieved_product&error=$message&id_warehouse=$id_warehouse&date_add=$date_add");
	}
	$check = dbQuery("SELECT id_recieved_detail, qty FROM tbl_recieved_detail WHERE id_recieved_product = $id_recieved_product AND id_product_attribute = $id_product_attribute AND id_zone = $id_zone AND status = 0");
	$rs = dbNumRows($check);
	if($rs>0){
		list($id_recieved_detail, $old_qty) = dbFetchArray($check);
		$new_qty = $qty+$old_qty;
		if(dbQuery("UPDATE tbl_recieved_detail SET qty = $new_qty WHERE id_recieved_detail = $id_recieved_detail")){
			header("location: ../index.php?content=product_in&edit=y&id_recieved_product=$id_recieved_product&id_warehouse=$id_warehouse&date_add=$date_add&barcode_zone=$barcode_zone&id_zone=$id_zone&name_zone=$name_zone");
		}else{
		$message ="รับเข้ารายการสินค้านี้ไม่สำเร็จ";
		header("location: ../index.php?content=product_in&edit=y&id_recieved_product=$id_recieved_product&error=$message&id_warehouse=$id_warehouse&date_add=$date_add");
	}
	}else{
		if(dbQuery("INSERT INTO tbl_recieved_detail (id_recieved_product, id_product_attribute, qty, id_warehouse, id_zone, date) VALUES ($id_recieved_product, $id_product_attribute, $qty, $id_warehouse, $id_zone, '$date_add')")){
		header("location: ../index.php?content=product_in&edit=y&id_recieved_product=$id_recieved_product&id_warehouse=$id_warehouse&date_add=$date_add&barcode_zone=$barcode_zone&id_zone=$id_zone&name_zone=$name_zone");
	}else{
		$message ="รับเข้ารายการสินค้านี้ไม่สำเร็จ";
		echo $message."<br>";
		echo "id_product_attribute = ".$id_product_attribute."<br>";
		echo"id_zone = ".$id_zone."<br>";
		echo "id_warehouse = ".$id_warehouse."<br>";
		echo "qty = ".$qty."</br>";
		//header("location: ../index.php?content=product_in&edit=y&id_recieved_product=$id_recieved_product&error=$message&id_warehouse=$id_warehouse&date_add=$date_add");
	}
	}
}

//**************************************** ลบรายละเอียดการรับสินค้าทีละตัว(ยังไม่ได้บันทึกยอด) ******************************************//
if(isset($_GET['delete'])&&isset($_GET['id_recieved_detail'])){
	$id_recieved_detail = $_GET['id_recieved_detail'];
	list($id_recieved_product, $id_warehouse, $date_add) = dbFetchArray(dbQuery("SELECT id_recieved_product, id_warehouse, date FROM tbl_recieved_detail WHERE id_recieved_detail = $id_recieved_detail"));
	if(dbQuery("DELETE FROM tbl_recieved_detail WHERE id_recieved_detail = $id_recieved_detail AND id_recieved_product = $id_recieved_product")){
		header("location: ../index.php?content=product_in&add=y&id_recieved_product=$id_recieved_product&id_warehouse=$id_warehouse&date_add=$date_add");
	}else{
		$message = "ไม่สามารถลบรายการได้"; 
		header("location: ../index.php?content=product_in&add=y&id_recieved_product=$id_recieved_product&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
	}
}

//**************************************** ลบรายละเอียดการรับสินค้าทีละตัว(บันทึกยอดแล้ว) ******************************************//
if(isset($_GET['delete_stocked'])&&isset($_GET['id_recieved_detail'])){
	$id_recieved_detail = $_GET['id_recieved_detail'];
	list($recieved_no,$date_add) = dbFetchArray(dbQuery("SELECT recieved_product_no, tbl_recieved_detail.date FROM tbl_recieved_product LEFT JOIN tbl_recieved_detail ON tbl_recieved_detail.id_recieved_product = tbl_recieved_product.id_recieved_product WHERE id_recieved_detail = $id_recieved_detail"));
	list($id_recieved_product, $id_product_attribute, $qty, $id_warehouse, $id_zone) = dbFetchArray(dbQuery("SELECT id_recieved_product, id_product_attribute, qty, id_warehouse, id_zone FROM tbl_recieved_detail WHERE id_recieved_detail = $id_recieved_detail"));
	if($id_recieved_detail !=""){

		list($stock_qty) = dbFetchArray(dbQuery("SELECT qty FROM tbl_stock WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone"));
		$new_qty = $stock_qty - $qty;
	if($new_qty<0){
			$zone_name = get_zone($id_zone);
			$message ="ไม่สามารถลบรายการนี้ได้ เพราะจะทำให้สต็อกโซน $zone_name ติดลบ $new_qty";
			header("location: ../index.php?content=product_in&edit=y&id_recieved_product=$id_recieved_product&error=$message");
	}else 	if($new_qty == 0){
		if(dbQuery("DELETE FROM tbl_stock WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute")){
			if(dbQuery("DELETE FROM tbl_recieved_detail WHERE id_recieved_detail = $id_recieved_detail")){
				dbQuery("DELETE FROM tbl_stock_movement WHERE id_product_attribute = $id_product_attribute AND reference = '$recieved_no'");
				header("location: ../index.php?content=product_in&edit=y&id_recieved_product=$id_recieved_product&message=$message");
				}else{
					$message = "ลบรายการรับสินค้าเข้าแล้ว แต่ ลบยอดสินค้าออกจากสต็อกไม่สำเร็จ";
					header("location: ../index.php?content=product_in&edit=y&id_recieved_product=$id_recieved_product&error=$message");
				}			
		}else{
			$message = "ทำรายการไม่สำเร็จ";
			header("location: ../index.php?content=product_in&edit=y&id_recieved_product=$id_recieved_product&error=$message");
			}
	}else if($new_qty > 0){
			if(dbQuery("UPDATE tbl_stock SET qty = $new_qty  WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute")){
				if(dbQuery("DELETE FROM tbl_recieved_detail WHERE id_recieved_detail = $id_recieved_detail")){
					dbQuery("DELETE FROM tbl_stock_movement WHERE id_product_attribute = $id_product_attribute AND reference = '$recieved_no'");
					header("location: ../index.php?content=product_in&edit=y&id_recieved_product=$id_recieved_product&message=$message");	
			}else{
			$message = "ลบรายการรับสินค้าเข้าแล้ว แต่ ลบยอดสินค้าออกจากสต็อกไม่สำเร็จ";
			header("location: ../index.php?content=product_in&edit=y&id_recieved_product=$id_recieved_product&error=$message");
		}
		}else{
		$message = "ทำรายการไม่สำเร็จ";
		header("location: ../index.php?content=product_in&edit=y&id_recieved_product=$id_recieved_product&error=$message");
	}
			}
	}
}
				

//********************************************* บันทึกยอดสินค้ารับเข้าไปยังสต็อกจริง ************************************************//
if(isset($_GET['add'])&&isset($_POST['id_recieved_product'])){
	$id_recieved_product = $_POST['id_recieved_product'];
	$date = dbDate($_POST['date']);
	list($recieved_no) = dbFetchArray(dbQuery("SELECT recieved_product_no FROM tbl_recieved_product WHERE id_recieved_product = $id_recieved_product")); //ดึงเลขที่เอกสาร
	$sql = dbQuery("SELECT* FROM tbl_recieved_detail WHERE id_recieved_product = $id_recieved_product AND status=0"); // เลือกกรองรายการสินค้าที่ยังไม่ได้บันทึกยอดไปยังสต็อก
	$row = dbNumRows($sql);
	$i=0;
	if($row>0){
		while($i<$row){
			list($id_recieved_detail, $id_recieved_product,$id_product_attribute, $qty,$id_warehouse, $id_zone, $date_add, $status) = dbFetchArray($sql);
			$qr = dbQuery("SELECT qty FROM tbl_stock WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone"); // ดึงยอดคงเหลือในสต็อกปัจจุบันมาดูก่อน ถ้ามียอดให้บวกเพิ่ม ถ้าไม่มีให้เพิ่มใหม่
			list($stock_qty) = dbFetchArray($qr);
			$result = dbNumRows($qr);
			$new_qty = $stock_qty + $qty; // ยอดสต็อกใหม่ ได้มาจาก ยอดเก่า + ยอดใหม่ที่เพิ่มเข้าไป
			if($result <1){	//ถ้าไม่มียอดเก่าอยู่ให้เพิ่ม		
				if(dbQuery("INSERT INTO tbl_stock (id_zone, id_product_attribute, qty) VALUES ($id_zone, $id_product_attribute, $qty)")){
					if(stock_movement("in",1,$id_product_attribute,$id_warehouse,$qty, $recieved_no, $date, $id_zone)){ // บันทึกความเคลื่อนไหวของสินค้า
					dbQuery("UPDATE tbl_recieved_detail SET status=1 WHERE id_recieved_detail = $id_recieved_detail AND status=0"); // เปลี่ยนสถานะในรายการให้เป็นบันทึกแล้ว
					}else{
						$message="ไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
						header("location: ../index.php?content=product_in&add=y&id_recieved_product=$id_recieved_product&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
					}
				}else{
					$message = "ไม่สามารถบันทึกยอดสต็อกได้ อาจเกิดจาก คลังสินค้า หรือ โซน หรือ รายการสินค้า ไม่ถูกต้อง กรุณาตรวจสอบความถูกต้องอีกครั้ง";
					header("location: ../index.php?content=product_in&add=y&id_recieved_product=$id_recieved_product&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
				}
			}else if($result >0){
				if(dbQuery("UPDATE tbl_stock SET qty = $new_qty WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute")){
					if(stock_movement("in",1,$id_product_attribute,$id_warehouse,$qty, $recieved_no, $date, $id_zone)){
					dbQuery("UPDATE tbl_recieved_detail SET status=1 WHERE id_recieved_detail = $id_recieved_detail AND status=0");
					}else{
						$message="ไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
						header("location: ../index.php?content=product_in&add=y&id_recieved_product=$id_recieved_product&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
					}
				}else{
					$message = "ไม่สามารถบันทึกยอดสต็อกได้ อาจเกิดจาก คลังสินค้า หรือ โซน หรือ รายการสินค้า ไม่ถูกต้อง กรุณาตรวจสอบความถูกต้องอีกครั้ง";
					header("location: ../index.php?content=product_in&add=y&id_recieved_product=$id_recieved_product&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
				}
			}
				$i++;
				if($i==$row){
					dbQuery("UPDATE tbl_recieved_product SET status = 1, date='$date' WHERE id_recieved_product = $id_recieved_product");
				}
		}
		header("location: ../index.php?content=product_in");
		
	}else{
		$message = "ไม่สามารถบันทึกยอดสต็อกได้ เนื่องจากไม่มีรายการให้บันทึก";
		header("location: ../index.php?content=product_in&add=y&id_recieved_product=$id_recieved_product&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
	}
}
//***********************************  แก้ไขรายการส่วนหัว   ******************************//
if(isset($_GET['edit_recieved'])&&isset($_GET['id_recieved_product'])){
	$id_recieved_product = $_GET['id_recieved_product'];
	$role = $_POST['id_role'];
	$reference = $_POST['ref'];
	list($recieved_no) = dbFetchArray(dbQuery("SELECT recieved_product_no FROM tbl_recieved_product WHERE id_recieved_product = $id_recieved_product"));
	$date = dbDate($_POST['doc_date']);
	if(dbQuery("UPDATE tbl_recieved_product SET reference_no = '$reference', date = '$date', role = $role WHERE id_recieved_product = $id_recieved_product")){
		dbQuery("UPDATE tbl_recieved_detail SET date ='$date' WHERE id_recieved_product = $id_recieved_product");
		dbQuery("UPDATE tbl_stock_movement SET date_upd = '$date' WHERE reference ='$recieved_no'");
		echo "ok";
	}else{
		echo "";
	}
}
//********************************** แก้ไขรายการรับ **********************************//
if(isset($_GET['edit'])&&isset($_POST['id_recieved_product'])){
	$id_recieved_product = $_POST['id_recieved_product'];
	$date = dbDate($_POST['date']);
	list($recieved_no) = dbFetchArray(dbQuery("SELECT recieved_product_no FROM tbl_recieved_product WHERE id_recieved_product = $id_recieved_product"));
	if(dbQuery("UPDATE tbl_recieved_product SET date = '$date' WHERE id_recieved_product= $id_recieved_product")){
			dbQuery("UPDATE tbl_recieved_detail SET date = '$date' WHERE id_recieved_product=$id_recieved_product");
			dbQuery("UPDATE tbl_stock_movement SET date_upd ='$date' WHERE reference ='$recieved_no'");
			}
	$sql = dbQuery("SELECT* FROM tbl_recieved_detail WHERE id_recieved_product = $id_recieved_product AND status=0");
	$row = dbNumRows($sql);
	$i=0;
	if($row>0){
		while($i<$row){
			list($id_recieved_detail, $id_recieved_product,$id_product_attribute, $qty,$id_warehouse, $id_zone, $date_add, $status) = dbFetchArray($sql);
			$qr = dbQuery("SELECT qty FROM tbl_stock WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone"); // ดึงยอดคงเหลือในสต็อกปัจจุบันมาดูก่อน ถ้ามียอดให้บวกเพิ่ม ถ้าไม่มีให้เพิ่มใหม่
			list($stock_qty) = dbFetchArray($qr);
			$result = dbNumRows($qr);
			$new_qty = $stock_qty + $qty; // ยอดสต็อกใหม่ ได้มาจาก ยอดเก่า + ยอดใหม่ที่เพิ่มเข้าไป
			if($result <1){	//ถ้าไม่มียอดเก่าอยู่ให้เพิ่ม	
				if(dbQuery("INSERT INTO tbl_stock (id_zone, id_product_attribute, qty) VALUES ($id_zone, $id_product_attribute, $qty)")){
					if(stock_movement("in",1,$id_product_attribute,$id_warehouse, $qty, $recieved_no, $date,$id_zone)){
					dbQuery("UPDATE tbl_recieved_detail SET status=1 WHERE id_recieved_detail = $id_recieved_detail AND status=0");
					}else{
						$message="ไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
						header("location: ../index.php?content=product_in&add=y&id_recieved_product=$id_recieved_product&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
					}
				}else{
					$message = "ไม่สามารถบันทึกยอดสต็อกได้ อาจเกิดจาก คลังสินค้า หรือ โซน หรือ รายการสินค้า ไม่ถูกต้อง กรุณาตรวจสอบความถูกต้องอีกครั้ง";
					header("location: ../index.php?content=product_in&add=y&id_recieved_product=$id_recieved_product&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
				}
				}else if($result >0){
				if(dbQuery("UPDATE tbl_stock SET qty = $new_qty WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute")){
					if(stock_movement("in",1,$id_product_attribute,$id_warehouse, $qty, $recieved_no, $date, $id_zone)){
					dbQuery("UPDATE tbl_recieved_detail SET status=1 WHERE id_recieved_detail = $id_recieved_detail AND status=0");
					}else{
						$message="ไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
						header("location: ../index.php?content=product_in&add=y&id_recieved_product=$id_recieved_product&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
					}
				}else{
					$message = "ไม่สามารถบันทึกยอดสต็อกได้ อาจเกิดจาก คลังสินค้า หรือ โซน หรือ รายการสินค้า ไม่ถูกต้อง กรุณาตรวจสอบความถูกต้องอีกครั้ง";
					header("location: ../index.php?content=product_in&add=y&id_recieved_product=$id_recieved_product&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
				}
			}
				$i++;
				if($i==$row){
					dbQuery("UPDATE tbl_recieved_product SET status = 1, date='$date' WHERE id_recieved_product = $id_recieved_product");
				}
		}
		header("location: ../index.php?content=product_in");
		
	}else{
		if(dbQuery("UPDATE tbl_recieved_product SET date = '$date' WHERE id_recieved_product= $id_recieved_product")){
			if(dbQuery("UPDATE tbl_recieved_detail SET date = '$date' WHERE id_recieved_product=$id_recieved_product")){
		$message = "ปรับปรุงรายการเรียบร้อยแล้ว";
		header("location: ../index.php?content=product_in&edit=y&id_recieved_product=$id_recieved_product&id_warehouse=$id_warehouse&date_add=$date_add&message=$message");
			}
		}
	}
}
//******************************************************** ลบรายการรับทั้งรายการ *******************************************//
if(isset($_GET['delete'])&&isset($_GET['id_recieved_product'])){
	$id_recieved_product = $_GET['id_recieved_product'];
	dbQuery("DELETE FROM tbl_recieved_detail WHERE id_recieved_product = $id_recieved_product AND status = 0");
	list($recieved_no) = dbFetchArray(dbQuery("SELECT recieved_product_no FROM tbl_recieved_product WHERE id_recieved_product = $id_recieved_product"));
	$sql = dbQuery("SELECT* FROM tbl_recieved_detail WHERE id_recieved_product = $id_recieved_product AND status=1");
	$row = dbNumRows($sql);
	$i=0;
	if($row>0){
		while($i<$row){
			list($id_recieved_detail, $id_recieved_product, $id_product_attribute, $qty, $id_warehouse, $id_zone, $date, $status)= dbFetchArray($sql);
			list($stock_qty) = dbFetchArray(dbQuery("SELECT qty FROM tbl_stock WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone"));
			$new_qty = $stock_qty - $qty;
			if($new_qty ==0){
				if(dbQuery("DELETE FROM tbl_stock WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute")){
					if(dbQuery("DELETE FROM tbl_recieved_detail WHERE id_recieved_detail = $id_recieved_detail")){
						if(stock_movement("out",8,$id_product_attribute,$id_warehouse, $qty, $recieved_no, $date, $id_zone)){
								}else{
									$message = "ลบรายการสินค้าสำเร็จ แต่ ไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
									header("location: ../index.php?content=product_in&error=$message");
								}
							}else{
								$message = "ลบยอดสินค้าสำเร็จ แต่ ลบรายการรับเข้าไม่สำเร็จและไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
								header("location: ../index.php?content=product_in&error=$message");
							}
				}else{
					$message = "ไม่สามารถลบรายการสินค้าได้";
					header("location: ../index.php?content=product_in&error=$message");
				}
			}else if($new_qty >0){
				if(dbQuery("UPDATE tbl_stock SET qty = $new_qty WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute")){
					if(dbQuery("DELETE FROM tbl_recieved_detail WHERE id_recieved_detail = $id_recieved_detail")){
						if(stock_movement("out",8,$id_product_attribute,$id_warehouse, $qty, $recieved_no, $date, $id_zone)){
						}else{
							$message = "ลบรายการสินค้าสำเร็จ แต่ ไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
							header("location: ../index.php?content=product_in&error=$message");
						}
							
					}else{
						$message = "ลบยอดสินค้าสำเร็จ แต่ ลบรายการรับเข้าไม่สำเร็จและไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
						header("location: ../index.php?content=product_in&error=$message");
					}
				}else{
					$message = "ไม่สามารถลบรายการสินค้าได้";
					header("location: ../index.php?content=product_in&error=$message");
					}
				}
			$i++;
			if($i == $row){
				if(dbQuery("DELETE FROM tbl_recieved_product WHERE id_recieved_product = $id_recieved_product")){
					dbQuery("DELETE FROM tbl_stock_movement WHERE reference ='$recieved_no'");
					$message = "ลบรายการเรียบร้อยแล้ว";
					header("location: ../index.php?content=product_in&message=$message");
				}else{
					$message="ไม่สามารถลบรายการได้";
					header("location: ../index.php?content=product_in&error=$message");
				}
			}	
		}
	}else if($row==0){
		if(dbQuery("DELETE FROM tbl_recieved_product WHERE id_recieved_product = $id_recieved_product")){
					dbQuery("DELETE FROM tbl_stock_movement WHERE reference ='$recieved_no'");
					$message = "ลบรายการเรียบร้อยแล้ว";
					header("location: ../index.php?content=product_in&message=$message");
				}else{
					$message="ไม่สามารถลบรายการได้";
					header("location: ../index.php?content=product_in&error=$message");
				}
	}			
	
}
if(isset($_GET['print'])&&isset($_GET['id_recieved_product'])){
	$id_recieved_product = $_GET['id_recieved_product']; 
	$company = new company();
	$sql = dbQuery("SELECT * FROM tbl_recieved_product WHERE id_recieved_product = '$id_recieved_product'");
	$data = dbFetchArray($sql);
	$recieved_product_no = $data['recieved_product_no'];
	$reference = $data['reference_no'];
	$date = $data['date'];
	$id_employee = $data['id_employee'];
	$status = $data['status'];
	$role = $data['role']; // 1 = รับเข้าปกติ  2 = รับเข้าจากการแปรรูป
	$employee = new employee($id_employee);
	$employee_name = $employee->full_name;
	$row = 17;
	$sqr = dbQuery("SELECT id_product_attribute, qty, id_warehouse, id_zone FROM tbl_recieved_detail WHERE id_recieved_product = '$id_recieved_product'");
	$rs = dbNumRows($sqr);
	$count = 1;
	$total_page = ceil($rs/$row);
	$page = 1;
	$total_qty = 0;
	$n = 1;
	$i = 0;
	$html = "	<!DOCTYPE html>
				<html>
				<head>
					<meta charset='utf-8'>
					<meta name='viewport' content='width=device-width, initial-scale=1.0'>
					<link rel='icon' href='../favicon.ico' type='image/x-icon' />
					<title>รายการรับสินค้าเข้าคลัง</title>
					<!-- Core CSS - Include with every page -->
					<link href='../../library/css/bootstrap.css' rel='stylesheet'>
					<link href='../../library/css/font-awesome.css' rel='stylesheet'>
					<link href='../../library/css/bootflat.min.css' rel='stylesheet'>
					 <link rel='stylesheet' href='../../library/css/jquery-ui-1.10.4.custom.min.css' />
					 <script src='../../library/js/jquery.min.js'></script>
					<script src='../../library/js/jquery-ui-1.10.4.custom.min.js'></script>
					<script src='../../library/js/bootstrap.min.js'></script>  
					<!-- SB Admin CSS - Include with every page -->
					<link href='../../library/css/sb-admin.css' rel='stylesheet'>
					<link href='../../library/css/template.css' rel='stylesheet'>
				</head>";
				$doc_body_top = "<body style='padding-top:0px; margin-top:-15px;'><div style='width:180mm; margin-right:auto; margin-left:auto; padding-left:10px; padding-right:10px; padding-top:0px;'>
				<div class=\"hidden-print\" style='margin-bottom:0px;'>
				<button  class='btn btn-primary pull-right' onClick=\"print();\" type='button' />พิมพ์</button>
				<a href='../index.php?content=product_in&view_detail=y&id_recieved_product=$id_recieved_product' ><button  class='btn btn-primary pull-right' type='button' style='margin-right:20px;' />ยกเลิก</button></a>
</div>";
			/*	$doc_head = "
			<!--<div style='width:100%; height:40mm; margin-right:0.5%;'>
			<table width='100%' border='0px'><tr>
				<td style='width:20%; padding:10px; text-align:center; vertical-align:top;'><img src='../../img/company/logo.png' style='width:100px; padding-right:10px;' /></td>
				<td style='width:40%; padding:10px; vertical-align:text-top;'>
				<h4 style='margin-top:0px; margin-bottom:5px;'>".$company->full_name."</h4>
				<p style='font-size:12px'>".$company->address." &nbsp; ".$company->post_code."</p>
				<p style='font-size:12px'>โทร. ".$company->phone." &nbsp;แฟกซ์. ".$company->fax."</p>
				<p style='font-size:12px'>เลขประจำตัวผู้เสียภาษี ".$company->tax_id."</p></td>
				<td style='vertical-align:text-top; text-align:right; padding-bottom:10px;'><strong>รายการรับสินค้าเข้าคลัง</strong></td></tr>
			</table>
			</div>-->";*/
			function doc_head($date, $recieved_product_no, $reference, $employee_name, $page, $total_page){
				$result ="
		<h4>รายการรับสินค้าเข้าคลัง</h4><p class='pull-right'>หน้า $page / $total_page</p>
		<table align='center' style='width:100%; table-layout:fixed;'>
		<tr><td style='width:50%;'>
			<div style='width:99.5%; height:30mm; margin-right:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:35%; padding:10px; height:5mm; vertical-align:text-top;'>เลขที่เอกสาร :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'> $recieved_product_no </td></tr>
				<tr><td style='width:20%; padding:10px; vertical-align:text-top;'>อ้างอิง :</td><td style='padding:10px; height:30mm; vertical-align:text-top;'> $reference </td></tr>
				</table>	</div>
				</td>
			<td style='width:50%;'><div style='width:99.5%; height:30mm; margin-left:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:35%; padding:10px; height:5mm; vertical-align:text-top;'>วันที่ :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".thaiDate($date)."</td></tr>
				<tr><td style='width:35%; padding:10px; height:5mm; vertical-align:text-top;'>พนักงาน :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>$employee_name</td></tr>
				</table>	</div></td></tr>
	</table>
	
		<table class='table table-striped' align='center' style='width:100%; table-layout:fixed; margin-top:5px;' id='order_detail'>
			<tr>
				<td style='width:10%; text-align:center; border:solid 1px #AAA; padding:10px;'>ลำดับ</td>
				<td style='width:20%; text-align:center; border:solid 1px #AAA;  padding:10px'>บาร์โค้ด</td>
				<td style='width:30%; border:solid 1px #AAA; text-align:center; padding:10px'>สินค้า</td>
			   <td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>จำนวน</td>
			   <td style='width:15%; text-align:center; border:solid 1px #AAA;  padding:10px'>คลัง</td>
			   <td style='width:15%; text-align:center; border:solid 1px #AAA;  padding:10px'>โซน</td>
			</tr>"; return $result; }
			function footer($total_qty=""){
				$result = "<tr style='height:9mm;'><td colspan='6' style='text-align:right; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 14px;'>รวม $total_qty หน่วย</td></tr></table>
				<div style='page-break-after:always'>
				<table style='width:100%; border:0px;'>
				<tr><td>	<div class='col-lg-12' style='text-align:center;'>ผู้รับของ</div></td>
					<td><div class='col-lg-12' style='text-align:center;'>ผู้ส่งของ</div></td>
					<td><div class='col-lg-12' style='text-align:center;'>ผู้ตรวจสอบ</div></td>
					<td><div class='col-lg-12' style='text-align:center;'>ผู้อนุมัติ</div></td>
				</tr>
				<tr><td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>ได้รับสินค้าถูกต้องแล้ว</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div></td>
					<td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>&nbsp;</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div></td>
					<td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>&nbsp;</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div></td>
					<td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>&nbsp;</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div>
				</td></tr></table></div>
				"; return $result; }
	
	if($rs>0){
		echo $html.$doc_body_top.doc_head($date, $recieved_product_no, $reference, $employee_name, $page, $total_page);
	while($i<$rs){
		list($id_product_attribute, $qty, $id_warehouse, $id_zone)= dbFetchArray($sqr);
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);	
		$product->product_detail($id_product);	
		$product->product_attribute_detail($id_product_attribute);
		if($count+1 >$row){  $css_row ="border-bottom: solid 1px #AAA; border-top: 0px;";  }else{ $css_row ="border-top: 0px;";}
		echo"<tr style='height:9mm;'>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>$n</td>
				<td style='vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>
					<img src='".WEB_ROOT."library/class/barcode/barcode.php?text=".$product->barcode."' style='height: 8mm;' /></td>
				<td style='vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>".$product->reference." : ".$product->product_name."</td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'> $qty </td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>".get_warehouse_name_by_id($id_warehouse)."</td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; border-right: solid 1px #AAA; font-size: 8px;'>".get_zone($id_zone)."</td>
				</tr>";
				$total_qty = $total_qty+$qty;
				$i++; $count++;
				if($n==$rs){ 
				$ba_row = $row - $count; 
				$ba = 0;
				if($ba_row >0){
					while($ba <= $ba_row){
						if($count+1 >$row){  $css_ba_row ="border-bottom: solid 1px #AAA; border-top: 0px;";  }else{ $css_ba_row ="border-top: 0px;";}
						echo"<tr style='height:9mm;'>
						<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
						<td style='vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
						<td style='vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
						<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
						<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
						<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; border-right: solid 1px #AAA; font-size: 8px;'></td>
						</tr>";
						$ba++; $count++;
					}
				}
				echo footer(number_format($total_qty));
				
				}else{
				if($count>$row){  $page++; echo footer().doc_head($date, $recieved_product_no, $reference, $employee_name, $page, $total_page); $count = 1; }
				}
				$n++; 
	}
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr></table>";
	}
	echo "</div></body></html>";	
}
//**************************************************** ******************************************************  รับคืนสินค้า **************************************************************************************** ******************************************//
//******************************** เพิ่มหัวเอกสารการรับคืนสินค้า ***************************************//
if(isset($_GET['add'])&&isset($_POST['return_no'])){
	$return_no = $_POST['return_no'];
	$ro = new return_order();
	$customer = new customer($_POST['id_customer']);
	$id_sale = $customer->id_sale;
	$data = array($return_no, $_POST['id_reason'], $_POST['id_customer'], $id_sale, $_COOKIE['user_id'], dbDate($_POST['date']), $_POST['remark']);
	if($ro->add($data)){
		$id_return_order = $ro->getReturnId($return_no);
			header("location: ../index.php?content=order_return&add=y&id_return_order=$id_return_order");
	}else{
		$message = $ro->error_message;
		header("location: ..index.php?content=order_return&add=y&error=$message");
	}
}

//********************************** แก้ไขเอกสาร  ******************************//
if(isset($_GET['edit'])&&isset($_GET['id_return'])&&isset($_GET['id_customer'])){
	$id_retrun_order = $_GET['id_return'];
	$customer = new customer($_GET['id_customer']);
	$id_sale = $customer->id_sale;
	$data = array($id_retrun_order, $_GET['id_reason'], $_GET['id_customer'], $id_sale, $_COOKIE['user_id'], dbDate($_GET['date']), $_GET['remark']);
	$ro = new return_order();
	if($ro->edit($data)){
		$result = "1:complete";
	}else{
		$result= "0:".$ro->error_message;
	}
	echo $result;
}
//********************************** เพิ่มรายการรับคืนสินค้าทีละรายการ(ยังไม่บันทึกยอด) *****************************************//
if(isset($_GET['add_detail'])&&isset($_GET['id_return_order'])){
	$id_return_order = $_GET['id_return_order']; 
	$ro = new return_order($id_return_order);
	$barcode_item = trim($_POST['barcode_item']);
	if(isset($_POST['barcode_zone'])){ 	$barcode_zone = trim($_POST['barcode_zone']); }else{ $barcode_zone = ""; }
	if(isset($_POST['zone_name'])){ 	$zone_name = $_POST['zone_name']; }else{ $zone_name = "";}
	if(isset($_POST['id_zone'])){ 	$id_zone = $_POST['id_zone']; }else{ $id_zone = "";}
	$pos_qty = $_POST['qty']; 
	$id_warehouse = $_POST['id_warehouse'];
	$product = new product();
    $date_add = $ro->date_add;
	$arr = $product->check_barcode($barcode_item); ///ดึง id_product_attribute และ จำนวน จากบาร์โค้ด คืนค่ามาเป็น array [id_product_attribute] และ [qty] ตามลำดับ 
	$id_product_attribute = $arr['id_product_attribute'];
	$qty = $pos_qty*$arr['qty'];
	$status = 0;
	if($id_product_attribute ==""){ // ตรวจสอบว่ามีรหัสสินค้าหรือไม่ ถ้าไม่มีให้แสดงข้อผิดพลาด
			$message ="รหัสสินค้าไม่ถูกต้อง กรุณาตรวจสอบ";
			header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&error=$message&id_warehouse=$id_warehouse&date_add=$date_add");
			exit;
	}
	if($id_zone ==""){
		if($barcode_zone !=""){ // ตรวจสอบว่ารหัสโซนหรือชื่อโซนถูกต้องหรือไม่ 
				list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_zone WHERE barcode_zone = '$barcode_zone' AND id_warehouse = $id_warehouse")); 	
		}else if($zone_name !=""){
				list($id_zone) = dbFetchArray(dbQuery("SELECT id_zone FROM tbl_zone WHERE zone_name = '$zone_name' AND id_warehouse = $id_warehouse"));
		}
	}
		list($barcode_zone, $name_zone) = dbFetchArray(dbQuery("SELECT barcode_zone, zone_name FROM tbl_zone WHERE id_zone = $id_zone"));
	if($id_zone ==""){ ///ถ้าไม่มีรหัสหรือชื่อโซน ให้แจ้งข้อผิดพลาด
			$message ="รหัสหรือชื่อโซนไม่ถูกต้อง หรือ ไม่มีโซนนี้ในคลังที่เลือกอยู่ กรุณาตรวจสอบ"; 
			header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&error=$message&id_warehouse=$id_warehouse&date_add=$date_add");
	}
	$check = dbQuery("SELECT id_return_order_detail, qty FROM tbl_return_order_detail WHERE id_return_order = $id_return_order AND id_product_attribute = $id_product_attribute AND id_zone = $id_zone AND status = 0");
	$rs = dbNumRows($check);
	if($rs>0){
		list($id_return_order_detail, $old_qty) = dbFetchArray($check);
		$new_qty = $qty+$old_qty;
		if(dbQuery("UPDATE tbl_return_order_detail SET qty = $new_qty, date_add = NOW() WHERE id_return_order_detail = $id_return_order_detail")){
			dbQuery("UPDATE tbl_return_order SET status = 0 WHERE id_return_order = $id_return_order");
			header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&id_warehouse=$id_warehouse&date_add=$date_add&barcode_zone=$barcode_zone&id_zone=$id_zone&name_zone=$name_zone");
		}else{
		$message ="รับเข้ารายการสินค้านี้ไม่สำเร็จ";
		header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&error=$message&id_warehouse=$id_warehouse&date_add=$date_add");
	}
	}else{
		if(dbQuery("INSERT INTO tbl_return_order_detail (id_return_order, id_product_attribute, qty, id_zone, date_add, status) VALUES ($id_return_order, $id_product_attribute, $qty, $id_zone, NOW(), $status)")){
			dbQuery("UPDATE tbl_return_order SET status = 0 WHERE id_return_order = $id_return_order");
		header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&id_warehouse=$id_warehouse&date_add=$date_add&barcode_zone=$barcode_zone&id_zone=$id_zone&name_zone=$name_zone");
	}else{
		$message ="รับเข้ารายการสินค้านี้ไม่สำเร็จ";
		echo $message."<br>";
		echo "id_product_attribute = ".$id_product_attribute."<br>";
		echo"id_zone = ".$id_zone."<br>";
		echo "id_warehouse = ".$id_warehouse."<br>";
		echo "qty = ".$qty."</br>";
		//header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&error=$message&id_warehouse=$id_warehouse&date_add=$date_add");
	}
	}
}
//********************************************* บันทึกยอดคืนสินค้าไปยังสต็อกจริง ************************************************//
if(isset($_GET['add'])&&isset($_GET['id_return_order'])){
	$id_return_order = $_GET['id_return_order'];
	$ro = new return_order($id_return_order);
	$reference = $ro->reference;  //ดึงเลขที่เอกสาร
	$date = $ro->date_add;
	$sql = dbQuery("SELECT id_return_order_detail, id_product_attribute, qty, id_zone FROM tbl_return_order_detail WHERE id_return_order = $id_return_order AND status=0"); // เลือกกรองรายการสินค้าที่ยังไม่ได้บันทึกยอดไปยังสต็อก
	$row = dbNumRows($sql);
	$i=0;
	if($row>0){
		while($i<$row){
			list($id_return_order_detail, $id_product_attribute, $qty,$id_zone) = dbFetchArray($sql);
			$qr = dbQuery("SELECT qty FROM tbl_stock WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone"); // ดึงยอดคงเหลือในสต็อกปัจจุบันมาดูก่อน ถ้ามียอดให้บวกเพิ่ม ถ้าไม่มีให้เพิ่มใหม่
			list($stock_qty) = dbFetchArray($qr);
			$result = dbNumRows($qr);
			$new_qty = $stock_qty + $qty; // ยอดสต็อกใหม่ ได้มาจาก ยอดเก่า + ยอดใหม่ที่เพิ่มเข้าไป
			if($result <1){	//ถ้าไม่มียอดเก่าอยู่ให้เพิ่ม		
				if(dbQuery("INSERT INTO tbl_stock (id_zone, id_product_attribute, qty) VALUES ($id_zone, $id_product_attribute, $qty)")){
					$id_warehouse = get_warehouse_by_zone($id_zone); //ดึง id_warehouse จาก โซน เพื่อบันทึก movement
					if(stock_movement("in",1,$id_product_attribute,$id_warehouse,$qty, $reference, $date, $id_zone)){ // บันทึกความเคลื่อนไหวของสินค้า
						$sqm = dbQuery("SELECT id_return_order_detail, qty FROM tbl_return_order_detail WHERE id_product_attribute = $id_product_attribute AND id_return_order = $id_return_order AND status = 1");
						$rm = dbNumRows($sqm);
						if($rm>0){
							list($old_id_return_order_detail, $old_qty) = dbFetchArray($sqm);
							$update_qty = $qty + $old_qty;
							dbQuery("UPDATE tbl_return_order_detail SET qty = $update_qty WHERE id_return_order_detail = $old_id_return_order_detail");
							dbQuery("DELETE FROM tbl_return_order_detail WHERE id_return_order_detail = $id_return_order_detail");
							}else{
							dbQuery("UPDATE tbl_return_order_detail SET status=1 WHERE id_return_order_detail = $id_return_order_detail AND status=0"); // เปลี่ยนสถานะในรายการให้เป็นบันทึกแล้ว
							}
					}else{
						$message="ไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
						header("location: ../index.php?content=order_return&add=y&id_return_order=$id_return_order&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
					}
				}else{
					$message = "ไม่สามารถบันทึกยอดสต็อกได้ อาจเกิดจาก คลังสินค้า หรือ โซน หรือ รายการสินค้า ไม่ถูกต้อง กรุณาตรวจสอบความถูกต้องอีกครั้ง";
					header("location: ../index.php?content=order_return&add=y&id_return_order=$id_return_order&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
				}
			}else if($result >0){
				if(dbQuery("UPDATE tbl_stock SET qty = $new_qty WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute")){ // ถ้าสำเร็จบันทึก movement
					$id_warehouse = get_warehouse_by_zone($id_zone); //ดึง id_warehouse จาก โซน เพื่อบันทึก movement
					if(stock_movement("in",1,$id_product_attribute,$id_warehouse,$qty, $reference, $date, $id_zone)){ // บันทึกความเคลื่อนไหวของสินค้า
						$sqm = dbQuery("SELECT id_return_order_detail, qty FROM tbl_return_order_detail WHERE id_product_attribute = $id_product_attribute AND id_return_order = $id_return_order AND status = 1");
						$rm = dbNumRows($sqm);
						if($rm>0){
							list($old_id_return_order_detail, $old_qty) = dbFetchArray($sqm);
							$update_qty = $qty + $old_qty;
							dbQuery("UPDATE tbl_return_order_detail SET qty = $update_qty WHERE id_return_order_detail = $old_id_return_order_detail");
							dbQuery("DELETE FROM tbl_return_order_detail WHERE id_return_order_detail = $id_return_order_detail");
							}else{
							dbQuery("UPDATE tbl_return_order_detail SET status=1 WHERE id_return_order_detail = $id_return_order_detail AND status=0"); // เปลี่ยนสถานะในรายการให้เป็นบันทึกแล้ว
							}
					}else{
						$message="ไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
						header("location: ../index.php?content=order_return&add=y&id_return_order=$id_return_order&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
					}
				}else{
					$message = "ไม่สามารถบันทึกยอดสต็อกได้ อาจเกิดจาก คลังสินค้า หรือ โซน หรือ รายการสินค้า ไม่ถูกต้อง กรุณาตรวจสอบความถูกต้องอีกครั้ง";
					header("location: ../index.php?content=order_return&add=y&id_return_order=$id_return_order&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
				}
			}
				$i++;
				if($i==$row){
					dbQuery("UPDATE tbl_return_order SET status = 1  WHERE id_return_order = $id_return_order");
				}
		}
		header("location: ../index.php?content=order_return");
		
	}else{
		$message = "ไม่สามารถบันทึกยอดสต็อกได้ เนื่องจากไม่มีรายการให้บันทึก";
		header("location: ../index.php?content=order_return&add=y&id_return_order=$id_return_order&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
	}
}
//********************************** แก้ไขรายการรับ **********************************//
if(isset($_GET['edit'])&&isset($_GET['id_return_order'])){
	$id_return_order = $_GET['id_return_order'];
	$ro = new return_order($id_return_order);
	$reference = $ro->reference;  //ดึงเลขที่เอกสาร
	$date = $ro->date_add;
	$sql = dbQuery("SELECT id_return_order_detail, id_product_attribute, qty, id_zone FROM tbl_return_order_detail WHERE id_return_order = $id_return_order AND status=0"); // เลือกกรองรายการสินค้าที่ยังไม่ได้บันทึกยอดไปยังสต็อก
	$row = dbNumRows($sql);
	$i=0;
	if($row>0){ // ถ้า $row มากกว่า 0 แสดงว่ามีรายการที่ยังไม่ได้บันทึก หรือ รายการที่เพิ่มเข้ามาใหม่
		while($i<$row){
			list($id_return_order_detail, $id_product_attribute, $qty,$id_zone) = dbFetchArray($sql);
			$qr = dbQuery("SELECT qty FROM tbl_stock WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone"); // ดึงยอดคงเหลือในสต็อกปัจจุบันมาดูก่อน ถ้ามียอดให้บวกเพิ่ม ถ้าไม่มีให้เพิ่มใหม่
			list($stock_qty) = dbFetchArray($qr);
			$result = dbNumRows($qr);
			$new_qty = $stock_qty + $qty; // ยอดสต็อกใหม่ ได้มาจาก ยอดเก่า + ยอดใหม่ที่เพิ่มเข้าไป
			if($result <1){	//ถ้าไม่มียอดเก่าอยู่ให้เพิ่ม	
				if(dbQuery("INSERT INTO tbl_stock (id_zone, id_product_attribute, qty) VALUES ($id_zone, $id_product_attribute, $qty)")){
					$id_warehouse = get_warehouse_by_zone($id_zone); //ดึง id_warehouse จาก โซน เพื่อบันทึก movement
					if(stock_movement("in",1,$id_product_attribute,$id_warehouse, $qty, $reference, $date, $id_zone)){
						$sqm = dbQuery("SELECT id_return_order_detail, qty FROM tbl_return_order_detail WHERE id_product_attribute = $id_product_attribute AND id_return_order = $id_return_order AND status = 1");
						$rm = dbNumRows($sqm);
						if($rm>0){
							list($old_id_return_order_detail, $old_qty) = dbFetchArray($sqm);
							$update_qty = $qty + $old_qty;
							dbQuery("UPDATE tbl_return_order_detail SET qty = $update_qty WHERE id_return_order_detail = $old_id_return_order_detail");
							dbQuery("DELETE FROM tbl_return_order_detail WHERE id_return_order_detail = $id_return_order_detail");
							}else{
							dbQuery("UPDATE tbl_return_order_detail SET status=1 WHERE id_return_order_detail = $id_return_order_detail AND status=0"); // เปลี่ยนสถานะในรายการให้เป็นบันทึกแล้ว
							}
					}else{
						$message="ไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
						header("location: ../index.php?content=order_return&add=y&id_return_order=$id_return_order&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
					}
				}else{
					$message = "ไม่สามารถบันทึกยอดสต็อกได้ อาจเกิดจาก คลังสินค้า หรือ โซน หรือ รายการสินค้า ไม่ถูกต้อง กรุณาตรวจสอบความถูกต้องอีกครั้ง";
					header("location: ../index.php?content=order_return&add=y&id_return_order=$id_return_order&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
				}
				}else if($result >0){
				if(dbQuery("UPDATE tbl_stock SET qty = $new_qty WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute")){
					$id_warehouse = get_warehouse_by_zone($id_zone); //ดึง id_warehouse จาก โซน เพื่อบันทึก movement
					if(stock_movement("in",1,$id_product_attribute,$id_warehouse, $qty, $reference, $date, $id_zone)){
						$sqm = dbQuery("SELECT id_return_order_detail, qty FROM tbl_return_order_detail WHERE id_product_attribute = $id_product_attribute AND id_return_order = $id_return_order AND status = 1");
						$rm = dbNumRows($sqm);
						if($rm>0){
							list($old_id_return_order_detail, $old_qty) = dbFetchArray($sqm);
							$update_qty = $qty + $old_qty;
							dbQuery("UPDATE tbl_return_order_detail SET qty = $update_qty WHERE id_return_order_detail = $old_id_return_order_detail");
							dbQuery("DELETE FROM tbl_return_order_detail WHERE id_return_order_detail = $id_return_order_detail");
							}else{
							dbQuery("UPDATE tbl_return_order_detail SET status=1 WHERE id_return_order_detail = $id_return_order_detail AND status=0"); // เปลี่ยนสถานะในรายการให้เป็นบันทึกแล้ว
							}
					}else{
						$message="ไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
						header("location: ../index.php?content=order_return&add=y&id_return_order=$id_return_order&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
					}
				}else{
					$message = "ไม่สามารถบันทึกยอดสต็อกได้ อาจเกิดจาก คลังสินค้า หรือ โซน หรือ รายการสินค้า ไม่ถูกต้อง กรุณาตรวจสอบความถูกต้องอีกครั้ง";
					header("location: ../index.php?content=order_return&add=y&id_return_order=$id_return_order&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
				}
			}
				$i++;
				if($i==$row){
					dbQuery("UPDATE tbl_return_order SET status = 1 WHERE id_return_order = $id_return_order");
				}
		}
		header("location: ../index.php?content=order_return");
		
	}else{
		$message = "ปรับปรุงรายการเรียบร้อยแล้ว";
		header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&id_warehouse=$id_warehouse&date_add=$date_add&message=$message");
	}
}
//**************************************** ลบรายละเอียดการคืนสินค้าทีละตัว(ยังไม่ได้บันทึกยอด) ******************************************//
if(isset($_GET['delete'])&&isset($_GET['id_return_order_detail'])){
	$id_return_order_detail = $_GET['id_return_order_detail'];
	$ro = new return_order();
	$ro->return_order_detail($id_return_order_detail);
	$id_return_order = $ro->id_return_order;
	$id_warehouse = get_warehouse_by_zone($ro->id_zone);
	if(dbQuery("DELETE FROM tbl_return_order_detail WHERE id_return_order_detail = $id_return_order_detail AND id_return_order = $id_return_order")){
		header("location: ../index.php?content=order_return&add=y&id_return_order=$id_return_order&id_warehouse=$id_warehouse");
	}else{
		$message = "ไม่สามารถลบรายการได้"; 
		header("location: ../index.php?content=order_return&add=y&id_return_order=$id_return_order&id_warehouse=$id_warehouse&date_add=$date_add&error=$message");
	}
}

//**************************************** ลบรายละเอียดการรับสินค้าทีละตัว(บันทึกยอดแล้ว) ******************************************//
if(isset($_GET['delete_stocked'])&&isset($_GET['id_return_order_detail'])){
	$id_return_order_detail = $_GET['id_return_order_detail'];
	$ro = new return_order();
	$ro->return_order_detail($id_return_order_detail);
	$id_return_order = $ro->id_return_order;
	$id_warehouse = get_warehouse_by_zone($ro->id_zone);
	$id_product_attribute = $ro->id_product_attribute;
	$qty = $ro->qty;
	$id_zone = $ro->id_zone;
	$reference = $ro->reference;
	$date = $ro->date_add;
	if($id_return_order_detail !=""){
		list($stock_qty) = dbFetchArray(dbQuery("SELECT qty FROM tbl_stock WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone"));
		$new_qty = $stock_qty - $qty;
	if($new_qty == 0){
		if(dbQuery("DELETE FROM tbl_stock WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute")){
			if(dbQuery("DELETE FROM tbl_return_order_detail WHERE id_return_order_detail = $id_return_order_detail")){
				if(stock_movement("out", 8, $id_product_attribute,$id_warehouse, $qty, $reference, $date, $id_zone)){
					$message = "ลบรายการสำเร็จแล้ว";
					header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&message=$message");
					}else{
						$message = "ลบรายการสำเร็จ แต่ บันทึกความเคลื่อนไหวสินค้าไม่สำเร็จ";
						header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&error=$message");
				}
				}else{
					$message = "ลบรายการรับสินค้าเข้าแล้ว แต่ ลบยอดสินค้าออกจากสต็อกไม่สำเร็จ";
					header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&error=$message");
				}			
		}else{
			$message = "ทำรายการไม่สำเร็จ";
			header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&error=$message");
			}
	}else if($new_qty > 0){
			if(dbQuery("UPDATE tbl_stock SET qty = $new_qty  WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute")){
				if(dbQuery("DELETE FROM tbl_return_order_detail WHERE id_return_order_detail = $id_return_order_detail")){
					if(stock_movement("out", 8, $id_product_attribute,$id_warehouse, $qty, $reference, $date, $id_zone)){
					$message = "ลบรายการสำเร็จแล้ว";
					header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&message=$message");
					}else{
					$message = "ลบรายการสำเร็จ แต่ บันทึกความเคลื่อนไหวสินค้าไม่สำเร็จ";
					header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&error=$message");
				}
			}else{
			$message = "ลบรายการรับสินค้าเข้าแล้ว แต่ ลบยอดสินค้าออกจากสต็อกไม่สำเร็จ";
			header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&error=$message");
		}
		}else{
		$message = "ทำรายการไม่สำเร็จ";
		header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&error=$message");
	}
		}else{
			$message = "ไม่สามารถลบสินค้าได้เนื่องจากสินค้าในโซนนี้มีการเคลื่อนไหวเเล้ว";
			header("location: ../index.php?content=order_return&edit=y&id_return_order=$id_return_order&error=$message");	
		}
	}
}
//******************************************************** ลบรายการรับทั้งรายการ *******************************************//
if(isset($_GET['delete'])&&isset($_GET['id_return_order'])){
	$id_return_order = $_GET['id_return_order'];
	$ro = new return_order($id_return_order);
	dbQuery("DELETE FROM tbl_return_order_detail WHERE id_return_order = $id_return_order AND status = 0");
	$reference = $ro->reference;
	$date = $ro->date_add;
	$result ="";
	$success = 0;
	$fail = 0;
	$sql = dbQuery("SELECT id_return_order_detail, id_product_attribute, qty, id_zone FROM tbl_return_order_detail WHERE id_return_order = $id_return_order AND status=1");
	$row = dbNumRows($sql);
	$i=0;
	if($row>0){
		while($i<$row){
			list($id_return_order_detail, $id_product_attribute, $qty, $id_zone)= dbFetchArray($sql);
			list($stock_qty) = dbFetchArray(dbQuery("SELECT qty FROM tbl_stock WHERE id_product_attribute = $id_product_attribute AND id_zone = $id_zone"));
			$new_qty = $stock_qty - $qty;
			$id_warehouse = get_warehouse_by_zone($id_zone);
			if($new_qty ==0){
				if(dbQuery("DELETE FROM tbl_stock WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute")){
					if(dbQuery("DELETE FROM tbl_return_order_detail WHERE id_return_order_detail = $id_return_order_detail")){
						if(stock_movement("out",8,$id_product_attribute,$id_warehouse, $qty, $reference, $date, $id_zone)){
							$success++;
								}else{
									$message = "ลบรายการสินค้าสำเร็จ แต่ ไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
									$result .= $message."</br>";
									$fail++;
								//	header("location: ../index.php?content=order_return&error=$message");
								}
							
							}else{
								$message = "ลบยอดสินค้าสำเร็จ แต่ ลบรายการรับเข้าไม่สำเร็จและไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
								$result .= $message."</br>";
								$fail++;
								//header("location: ../index.php?content=order_return&error=$message");
							}
				}else{
					$message = "ไม่สามารถลบรายการสินค้าได้";
					$result .= $message."</br>";
					$fail++;
					//header("location: ../index.php?content=order_return&error=$message");
				}
			}else if($new_qty != 0){
				if(dbQuery("UPDATE tbl_stock SET qty = $new_qty WHERE id_zone = $id_zone AND id_product_attribute = $id_product_attribute")){
					if(dbQuery("DELETE FROM tbl_return_order_detail WHERE id_return_order_detail = $id_return_order_detail")){
						if(stock_movement("out",8,$id_product_attribute,$id_warehouse, $qty, $reference, $date,$id_zone)){
							$success++;
						}else{
							$message = "ลบรายการสินค้าสำเร็จ แต่ ไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
							$result .= $message."</br>";
							$fail++;
							//header("location: ../index.php?content=order_return&error=$message");
						}
							
					}else{
						$message = "ลบยอดสินค้าสำเร็จ แต่ ลบรายการรับเข้าไม่สำเร็จและไม่สามารถบันทึกความเคลื่อนไหวสินค้าได้";
						$result .= $message."</br>";
						$fail++;
						//header("location: ../index.php?content=order_return&error=$message");
					}
				}else{
					$message = "ไม่สามารถลบรายการสินค้าได้";
					$result .= $message."</br>";
					$fail++;
					//header("location: ../index.php?content=order_return&error=$message");
					}
				}
			$i++;
			if($i == $row && $success == $row){
				if(dbQuery("DELETE FROM tbl_return_order WHERE id_return_order = $id_return_order")){
					$message = "ลบ $success รายการเรียบร้อยแล้ว";
					header("location: ../index.php?content=order_return&message=$message");
				}else{
					$message="ไม่สามารถลบรายการได้ </br> $result";
					header("location: ../index.php?content=order_return&error=$message");
				}
			}	
		}
	}else if($row==0 ){
		if(dbQuery("DELETE FROM tbl_return_order WHERE id_return_order = $id_return_order")){
					$message = "ลบรายการเรียบร้อยแล้ว";
					header("location: ../index.php?content=order_return&message=$message");
				}else{
					$message="ไม่สามารถลบรายการได้";
					header("location: ../index.php?content=order_return&error=$message");
				}
	}			
	
}
if(isset($_GET['print'])&&isset($_GET['id_return_order'])){
	$id_return_order = $_GET['id_return_order']; 
	$ro = new return_order($id_return_order);
	$company = new company();
	$reference = $ro->reference;
	$date = $ro->date_add;
	$customer = new customer($ro->id_customer);
	$customer_name = $customer->full_name;
	$employee = new employee($ro->id_employee);
	$employee_name = $employee->full_name;
	$reason = $ro->return_reason;
	$status = $ro->status;
	$row = 17;
	$sqr = dbQuery("SELECT id_product_attribute, qty, id_zone FROM tbl_return_order_detail WHERE id_return_order = '$id_return_order'");
	$rs = dbNumRows($sqr);
	$count = 1;
	$total_page = ceil($rs/$row);
	$page = 1;
	$total_qty = 0;
	$n = 1;
	$i = 0;
	$html = "	<!DOCTYPE html>
				<html>
				<head>
					<meta charset='utf-8'>
					<meta name='viewport' content='width=device-width, initial-scale=1.0'>
					<link rel='icon' href='../favicon.ico' type='image/x-icon' />
					<title>รายการรับคืนสินค้าเข้าคลัง</title>
					<!-- Core CSS - Include with every page -->
					<link href='../../library/css/bootstrap.css' rel='stylesheet'>
					<link href='../../library/css/font-awesome.css' rel='stylesheet'>
					<link href='../../library/css/bootflat.min.css' rel='stylesheet'>
					 <link rel='stylesheet' href='../../library/css/jquery-ui-1.10.4.custom.min.css' />
					 <script src='../../library/js/jquery.min.js'></script>
					<script src='../../library/js/jquery-ui-1.10.4.custom.min.js'></script>
					<script src='../../library/js/bootstrap.min.js'></script>  
					<!-- SB Admin CSS - Include with every page -->
					<link href='../../library/css/sb-admin.css' rel='stylesheet'>
					<link href='../../library/css/template.css' rel='stylesheet'>
				</head>";
				$doc_body_top = "<body style='padding-top:0px; margin-top:-15px;'><div style='width:180mm; margin-right:auto; margin-left:auto; padding-left:10px; padding-right:10px; padding-top:0px;'>
				<div class=\"hidden-print\" style='margin-bottom:0px;'>
				<button  class='btn btn-primary pull-right' onClick=\"print();\" type='button' />พิมพ์</button>
				<a href='../index.php?content=order_return&view_detail=y&id_return_order=$id_return_order' ><button  class='btn btn-primary pull-right' type='button' style='margin-right:20px;' />ยกเลิก</button></a>
</div>";
			/*	$doc_head = "
			<!--<div style='width:100%; height:40mm; margin-right:0.5%;'>
			<table width='100%' border='0px'><tr>
				<td style='width:20%; padding:10px; text-align:center; vertical-align:top;'><img src='../../img/company/logo.png' style='width:100px; padding-right:10px;' /></td>
				<td style='width:40%; padding:10px; vertical-align:text-top;'>
				<h4 style='margin-top:0px; margin-bottom:5px;'>".$company->full_name."</h4>
				<p style='font-size:12px'>".$company->address." &nbsp; ".$company->post_code."</p>
				<p style='font-size:12px'>โทร. ".$company->phone." &nbsp;แฟกซ์. ".$company->fax."</p>
				<p style='font-size:12px'>เลขประจำตัวผู้เสียภาษี ".$company->tax_id."</p></td>
				<td style='vertical-align:text-top; text-align:right; padding-bottom:10px;'><strong>รายการรับสินค้าเข้าคลัง</strong></td></tr>
			</table>
			</div>-->";*/
			function doc_head($date, $reference, $customer_name, $employee_name, $page, $total_page, $reason){
				$result ="
		<h4>รายการรับคืนสินค้าเข้าคลัง</h4><p class='pull-right'>หน้า $page / $total_page</p>
		<table align='center' style='width:100%; table-layout:fixed;'>
		<tr><td style='width:50%;'>
			<div style='width:99.5%; height:30mm; margin-right:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:35%; padding:10px; height:5mm; vertical-align:text-top;'>เลขที่เอกสาร :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'> $reference </td></tr>
				<tr><td style='width:20%; padding:10px; vertical-align:text-top;'>ลูกค้า :</td><td style='padding:10px; height:30mm; vertical-align:text-top;'> $customer_name </td></tr>
				</table>	</div>
				</td>
			<td style='width:50%;'><div style='width:99.5%; height:30mm; margin-left:0.5%; border: 1px solid #AAA;'>
				<table width='100%'>
				<tr><td style='width:35%; padding:10px; height:5mm; vertical-align:text-top;'>วันที่ :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>".thaiDate($date)."</td></tr>
				<tr><td style='width:35%; padding:10px; height:5mm; vertical-align:text-top;'>พนักงาน :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>$employee_name</td></tr>
				<tr><td style='width:35%; padding:10px; height:5mm; vertical-align:text-top;'>สาเหตุ :</td><td style='padding:10px; vertical-align:text-top; height:5mm;'>$reason</td></tr>
				</table>	</div></td></tr>
	</table>
	
		<table class='table table-striped' align='center' style='width:100%; table-layout:fixed; margin-top:5px;' id='order_detail'>
			<tr>
				<td style='width:10%; text-align:center; border:solid 1px #AAA; padding:10px;'>ลำดับ</td>
				<td style='width:20%; text-align:center; border:solid 1px #AAA;  padding:10px'>บาร์โค้ด</td>
				<td style='width:30%; border:solid 1px #AAA; text-align:center; padding:10px'>สินค้า</td>
			   <td style='width:10%; text-align:center; border:solid 1px #AAA;  padding:10px'>จำนวน</td>
			   <td style='width:15%; text-align:center; border:solid 1px #AAA;  padding:10px'>คลัง</td>
			   <td style='width:15%; text-align:center; border:solid 1px #AAA;  padding:10px'>โซน</td>
			</tr>"; return $result; }
			function footer($total_qty=""){
				if($total_qty !=""){ $total_qty = number_format($total_qty); }
				$result = "<tr style='height:9mm;'><td colspan='6' style='text-align:right; vertical-align:middle; padding:3px; border: solid 1px #AAA; font-size: 14px;'>รวม $total_qty หน่วย</td></tr></table>
				<div style='page-break-after:always'>
				<table style='width:100%; border:0px;'>
				<tr><td>	<div class='col-lg-12' style='text-align:center;'>ผู้รับของ</div></td>
					<td><div class='col-lg-12' style='text-align:center;'>ผู้ส่งของ</div></td>
					<td><div class='col-lg-12' style='text-align:center;'>ผู้ตรวจสอบ</div></td>
					<td><div class='col-lg-12' style='text-align:center;'>ผู้อนุมัติ</div></td>
				</tr>
				<tr><td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>ได้รับสินค้าถูกต้องแล้ว</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div></td>
					<td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>&nbsp;</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div></td>
					<td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>&nbsp;</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div></td>
					<td><div class='col-lg-12' style='border: solid 1px #AAA; font-size: 8px; border-radius:10px;'><p style='text-align:center;'>&nbsp;</p><p>&nbsp;</p><p><hr style='margin:0px; border-style:dotted; border-color:#CCC;'/></p><p >วันที่...............................</p></div>
				</td></tr></table></div>
				"; return $result; }
	
	if($rs>0){
		echo $html.$doc_body_top.doc_head($date, $reference, $customer_name, $employee_name, $page, $total_page, $reason);
	while($i<$rs){
		list($id_product_attribute, $qty, $id_zone)= dbFetchArray($sqr);
		$id_warehouse = get_warehouse_by_zone($id_zone);
		$product = new product();
		$id_product = $product->getProductId($id_product_attribute);
		$product->product_detail($id_product);
		$product->product_attribute_detail($id_product_attribute);	
		if($count+1 >$row){  $css_row ="border-bottom: solid 1px #AAA; border-top: 0px;";  }else{ $css_row ="border-top: 0px;";}
		echo"<tr style='height:9mm;'>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>$n</td>
				<td style='vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>
					<img src='".WEB_ROOT."library/class/barcode/barcode.php?text=".$product->barcode."' style='height: 8mm;' />
				</td>
				<td style='vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>".$product->reference." : ".$product->product_name."</td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'> $qty </td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; font-size: 8px;'>".get_warehouse_name_by_id($id_warehouse)."</td>
				<td style='text-align:center; vertical-align:middle; padding:3px; $css_row border-left: solid 1px #AAA; border-right: solid 1px #AAA; font-size: 8px;'>".get_zone($id_zone)."</td>
				</tr>";
				$total_qty += $qty;
				$i++; $count++;
				if($n==$rs){ 
				$ba_row = $row - $count; 
				$ba = 0;
				if($ba_row >0){
					while($ba <= $ba_row){
						if($count+1 >$row){  $css_ba_row ="border-bottom: solid 1px #AAA; border-top: 0px;";  }else{ $css_ba_row ="border-top: 0px;";}
						echo"<tr style='height:9mm;'>
						<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
						<td style='vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
						<td style='vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
						<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
						<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; font-size: 8px;'></td>
						<td style='text-align:center; vertical-align:middle; padding:3px; $css_ba_row border-left: solid 1px #AAA; border-right: solid 1px #AAA; font-size: 8px;'></td>
						</tr>";
						$ba++; $count++;
					}
				}
				echo footer($total_qty);
				
				}else{
				if($count>$row){  $page++; echo footer().doc_head($date, $reference, $customer_name, $employee_name, $page, $total_page, $reason); $count = 1; }
				}
				$n++; 
	}
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไมีมีรายการสินค้า</h3></td></tr></table>";
	}
	echo "</div></body></html>";	
}
?>