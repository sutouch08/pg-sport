<?php 
	$page_menu = "invent_consign";
	if(isset($_GET['consign_check']) || isset($_GET['add_consign_check']) || isset($_GET['consign_balance']) || isset($_GET['import_fales'])){
		$page_name = "กระทบยอด";
	}else{
		$page_name = "ตัดยอดฝากขาย";
	}
	$id_tab = 16;
	$cancle_tab_id = 36;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
  	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	$px = checkAccess($id_profile, $cancle_tab_id);
	$view_cancle = $px['view'];
	$add_cancle = $px['add'];
	$edit_cancle = $px['edit'];
	$delete_cancle = $px['delete']; 
	$role = 6; /// ตัดยอดฝากขาย
	if(isset($_GET['id_consign_check'])){$id_consign_check = $_GET['id_consign_check'];}else{$id_consign_check = "";}
	
	?>
<style>
	a {
		margin-left: 3px;
		margin-right: 3px;
	}
</style>    
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3 class="title"><i class="fa fa-check-square-o"></i>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <p class="pull-right">
       <?php 
	   		$btn = "";
	   		$btn_back_consign = "<button type='button' class='btn btn-warning' onclick='back_to_consign()'><i class='fa fa-arrow-left'></i>&nbsp กลับ</button>&nbsp;";
			$btn_back_compare = "<button type='button' class='btn btn-warning' onclick='back_to_compare()'><i class='fa fa-arrow-left'></i>&nbsp กลับ</button>&nbsp;";
	   if(isset($_GET['view_detail'])&&isset($_GET['id_order_consign']))
	   {
		   $id_order_consign = $_GET['id_order_consign'];
		   list($status_consign) = dbFetchArray(dbQuery("SELECT consign_status FROM tbl_order_consign WHERE id_order_consign = '$id_order_consign'"));
		   $btn .= $btn_back_consign;
		   if($status_consign == 0)
		   {
			   if($edit){ $btn .= "<a href='index.php?content=consign&add=y&id_order_consign=$id_order_consign' style='margin-right:5px;'><button type='button' class='btn btn-info'><i class='fa fa-pencil'></i>&nbsp; แก้ไข</button></a>"; }
		   }
		}else if(isset($_GET['edit']) || isset($_GET['add'])){
			if(isset($_GET['id_order_consign'])){
				$id_order_consign = $_GET['id_order_consign'];
				list($id_customer) = dbFetchArray(dbQuery("SELECT id_customer FROM tbl_order_consign WHERE id_order_consign = '$id_order_consign'"));
				$btn .= "<a href='index.php?content=consign&id_order_consign=$id_order_consign&id_customer=$id_customer&view_detail=y' ><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		   }else{
			   $btn .= $btn_back_consign;
		   }
	  	}else if(isset($_GET['consign_check'])){
			$btn .= $btn_back_consign;
			if($add){ $btn .= "<a href='index.php?content=consign&add_consign_check=y'><button type='button' class='btn btn-success' ><i class='fa fa-plus'></i>&nbsp เพิ่มรายการกระทบยอด</button></a>"; }
		}else if(isset($_GET['add_consign_check'])){
			$btn .= $btn_back_compare;
		   if(isset($_GET['id_consign_check'])){
			   	list($consign_valid) = dbFetchArray(dbQuery("SELECT consign_valid FROM tbl_consign_check WHERE id_consign_check = '$id_consign_check'"));
				if($consign_valid == 0)
				{
					if($add){ $btn .="<a href='controller/consignController.php?add_consign_diff=y&id_consign_check=$id_consign_check' ><button type='button' class='btn btn-primary' ><i class='fa fa-bolt'></i>&nbsp ดึงไปตัดยอดฝากขาย</button></a>";}
				 }
				 $btn .="<a href='index.php?content=consign&consign_balance_check&id_consign_check=$id_consign_check'><button type='button' class='btn btn-info'><i class='fa fa-list'></i>&nbsp; ยอดคงเหลือ</button></a>";
				 $btn .="<a href='index.php?content=consign&consign_balance&id_consign_check=$id_consign_check'><button type='button' class='btn btn-info'><i class='fa fa-list'></i>&nbsp; ยอดต่าง</button></a>";
				 $btn .="<a href='index.php?content=consign&import_fales&id_consign_check=$id_consign_check'><button type='button' class='btn btn-danger'><i class='fa fa-exclamation-triangle'></i>&ang; ข้อผิดพลาด</button></a>";
			 }
		}else if(isset($_GET['import_fales']) || isset($_GET['consign_balance']) || isset($_GET['consign_balance_check'])){
			$btn .="<a href='index.php?content=consign&add_consign_check&id_consign_check=$id_consign_check'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		   	if(isset($_GET['consign_balance']))
			{
				$btn .= "<a href='controller/consignController.php?print_diff&id_consign_check=$id_consign_check' ><button type='button' class='btn btn-success'><i class='fa fa-print'></i>&nbsp; พิมพ์</button></a>";
			}
			if(isset($_GET['consign_balance_check']))
			{
				$btn .="<a href='controller/consignController.php?print_balance&id_consign_check=$id_consign_check' ><button type='button' class='btn btn-success'><i class='fa fa-print'></i>&nbsp; พิมพ์</button></a>";	
			}
		}else{
			if($add)
			{
				$btn .="<a href='index.php?content=consign&consign_check=y'><button type='button' class='btn btn-primary' ><i class='fa fa-check-square-o'></i>&nbsp; กระทบยอด</button></a>";
				$btn .="<a href='index.php?content=consign&add=y'><button type='button' class='btn btn-success' ><i class='fa fa-plus'></i>&nbsp;". $page_name ."</button></a>";
			}
	   }
	   ?>
       <?php echo $btn; ?>
       </p>
       
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php 
if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div id='error' class='alert alert-danger' >
	 <b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET["message"];
	echo"<div class='alert alert-success'>$message</div>";
}
?>
<?php
//---------------------------ยอดคงเหลือ-------------------//
if(isset($_GET['consign_balance_check'])){
	echo "<table class='table table-bordered' id='table1'>
	<thead><th colspan='6' style='text-align:center;'><h4>ยอดคงเหลือ</h4></th></thead>
	<thead id='head'>
				<th style='width:5%;'>ลำดับ</th><th style='width:15%;'>บาร์โค้ด</th><th style='width:30%;'>รหัสสินค้า</th>
			   <th style='width:10%; text-align:center;'>ราคา</th><th style='width:10%; text-align:center;'>จำนวน</th>
				<th style='width:10%; text-align:center;'>จำนวนเงิน</th>
	</thead>";
	$sql = dbQuery("SELECT tbl_consign_check_detail.id_product_attribute, barcode, reference,qty_check FROM tbl_consign_check_detail LEFT JOIN tbl_product_attribute ON tbl_consign_check_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_consign_check = $id_consign_check AND qty_check != 0  ORDER BY barcode DESC");
	$row = dbNumRows($sql);
	$n = 1;
	$i = 0;
	$sumdiff = 0;
	$total = 0;
	if($row>0){
	while($i<$row){
		list($id_product_attribute,$barcode,$reference,$qty_check)= dbFetchArray($sql);
		$product = new product();
		$product->product_attribute_detail($id_product_attribute);
		$product_price = $product->product_price;
		$price = $qty_check * $product_price;
		$sumdiff = $sumdiff + $qty_check;
		$total = $total +$price;
		echo"<tr id='row$id_product_attribute'>
		<td style='text-align:center; vertical-align:middle;'>$n</td>
		<td style='vertical-align:middle;'>$barcode</td>
		<td style='vertical-align:middle;'>$reference </td>
		<td style='text-align:center; vertical-align:middle;'>$product_price</td>
		<td id='diff$id_product_attribute' style='text-align:center; vertical-align:middle;'>$qty_check</td>
		<td id='edit$id_product_attribute' style='text-align:right; vertical-align:middle;'>".number_format($price,2)."</td></tr>";
				$i++;
				$n++;
	}
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไม่มีรายการสินค้า</h3></td></tr>";
	}
	echo"<tr><td rowspan='2' colspan='4'></td><td>จำนวนรวม</td><td style='text-align:right; vertical-align:middle;'>$sumdiff</td></tr>
	<td>ยอดเงินรวม</td><td style='text-align:right; vertical-align:middle;'>".number_format($total,2)."</td></tr>
	</table>	
	";
	
//--------------------------------รายการที่importไม่สำเร็จ---------------------------------//
}else if(isset($_GET['import_fales'])){
	$id_consign_check = $_GET['id_consign_check'];
	echo "<table class='table table-bordered' id='table1'>
	<thead id='head'>
				<th style='width:5%;'>ลำดับ</th><th style='width:15%;'>บาร์โค้ด</th><th style='width:30%;'>รหัสสินค้า</th><th style='width:10%;'>จำนวน</th><th style='width:30%;'>สาเหตุ</th>
	</thead>";
	$sql = dbQuery("SELECT barcode,qty,comment FROM tbl_consign_import_fales WHERE id_consign_check = $id_consign_check");
	$row = dbNumRows($sql);
	$n = 1;
	$i = 0;
	if($row>0){
	while($i<$row){
		list($barcode,$qty,$comment)= dbFetchArray($sql);
		$product = new product();
		$arr = $product->check_barcode($barcode); ///ดึง id_product_attribute และ จำนวน จากบาร์โค้ด คืนค่ามาเป็น array [id_product_attribute] และ [qty] ตามลำดับ
		$id_product_attribute = $arr['id_product_attribute'];
		$product->product_attribute_detail($id_product_attribute);
		$reference = $product->reference;
		echo"<tr id='row$id_product_attribute'>
		<td style='text-align:center; vertical-align:middle;'>$n</td>
		<td style='vertical-align:middle;'>$barcode</td>
		<td style='vertical-align:middle;'>$reference</td>
		<td style='text-align:center; vertical-align:middle;'>$qty</td>
		<td id='diff$id_product_attribute' vertical-align:middle;'>$comment</td>
		</tr>";
				$i++;
				$n++;
	}
	}else{
		echo"<tr><td colspan='6' align='center'><h3>ไม่มีรายการที่ไม่สำเร็จ</h3></td></tr>";
	}
	echo"
				
	</table>	
	";
//------------------------------------ดูรายการยอดต่าง--------------------------------------//
}else if(isset($_GET['consign_balance'])){
	echo "<table class='table table-bordered' id='table1'>
		<thead><th colspan='6' style='text-align:center;'><h4>ยอดต่าง</h4></th></thead>
	<thead id='head'>
				<th style='width:5%;'>ลำดับ</th><th style='width:15%;'>บาร์โค้ด</th><th style='width:30%;'>รหัสสินค้า</th>
			   <th style='width:10%; text-align:center;'>ราคา</th><th style='width:10%; text-align:center;'>จำนวน</th>
				<th style='width:10%; text-align:center;'>จำนวนเงิน</th>
	</thead>";
	$sql = dbQuery("SELECT tbl_consign_check_detail.id_product_attribute, barcode, reference, qty_stock, qty_check FROM tbl_consign_check_detail LEFT JOIN tbl_product_attribute ON tbl_consign_check_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_consign_check = $id_consign_check AND qty_stock > qty_check ORDER BY barcode DESC");
	$row = dbNumRows($sql);
	$n = 1;
	$i = 0;
	$sumdiff = 0;
	$total = 0;
	if($row>0){
	while($i<$row){
		list($id_product_attribute,$barcode,$reference,$qty_stock,$qty_check)= dbFetchArray($sql);
		$product = new product();
		$product->product_attribute_detail($id_product_attribute);
		$product_price = $product->product_price;
		$diff = $qty_stock - $qty_check;
		$price = $diff * $product_price;
		$sumdiff = $sumdiff + $diff;
		$total = $total +$price;
		echo"<tr id='row$id_product_attribute'>
		<td style='text-align:center; vertical-align:middle;'>$n</td>
		<td style='vertical-align:middle;'>$barcode</td>
		<td style='vertical-align:middle;'>$reference </td>
		<td style='text-align:center; vertical-align:middle;'>$product_price</td>
		<td id='diff$id_product_attribute' style='text-align:center; vertical-align:middle;'>$diff</td>
		<td id='edit$id_product_attribute' style='text-align:right; vertical-align:middle;'>".number_format($price,2)."</td></tr>";
				$i++;
				$n++;
	}
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไม่มีรายการสินค้า</h3></td></tr>";
	}
	echo"<tr><td rowspan='2' colspan='4'></td><td>จำนวนรวม</td><td style='text-align:right; vertical-align:middle;'>$sumdiff</td></tr>
	<td>ยอดเงินรวม</td><td style='text-align:right; vertical-align:middle;'>".number_format($total,2)."</td></tr>
	</table>	
	";
//------------------------------------เพิ่มการกระทบยอด----------------------------//
}else if(isset($_GET['add_consign_check'])){
	if(!isset($_GET['id_consign_check'])){ 
			$reference = get_max_role_reference_consign_check("PREFIX_CONSIGN_CHECK",$role);
			$user_id = $_COOKIE['user_id'];
			$active = "";
			$id_customer = "";
			$customer_name = "";
			$comment = "";
			$payment = "credit";
			$id_zone = "";
			$active1 = "";
			$consign_valid = "";
		}else{
			$id_consign_check = $_GET['id_consign_check'];
			list($reference,$id_customer,$id_zone,$date_add,$date_upd,$comment,$consign_valid) = dbFetchArray(dbQuery("SELECT reference,id_customer,id_zone,date_add,date_upd,comment,consign_valid FROM tbl_consign_check WHERE id_consign_check = $id_consign_check"));
			$active = "disabled='disabled'"; 
			$customer = new customer($id_customer);
			$customer_name = $customer->full_name; 
			if($consign_valid == 1){
				$active1 =  "disabled='disabled'"; 
			}else if($consign_valid == 2){
				$active1 =  "disabled='disabled'"; 
			}else{
				$active1 = "";
			}
		}
function select_zone_consign($selected=""){
	echo"<option value='0'>-------เลือกพื้นที่--------</option>";
	$sql = dbQuery("SELECT * FROM tbl_zone WHERE id_warehouse = 2");
	while($rs = dbFetchArray($sql)){
		$id_zone = $rs['id_zone'];
		$zone_name = $rs['zone_name'];
		if($selected==$id_zone){ $select = "selected='selected'";}else{ $select = "";}
		echo"<option value='$id_zone' $select>$zone_name</option>";
	}
}
	/////////  เพิ่มออเดอร์ ID ใหม่
echo"<form id='add_order_form' action='controller/consignController.php?add_consign_check=y' method='post'>
	<div class='row'><input type='hidden' name='id_employee' value='$user_id' />
	 ";if(isset($_GET['id_consign_check'])){echo "<input type='hidden' name='edit_consign_check' value='$id_consign_check' />";}echo "
	<div class='col-xs-3'><div class='input-group'><span class='input-group-addon'>เลขที่เอกสาร</span><input type='text' id='doc_id' class='form-control' value='$reference' disabled='disabled'/></div> </div> 
	<div class='col-xs-2'><div class='input-group'><span class='input-group-addon'>วันที่</span><input type='text' id='doc_date' name='doc_date' class='form-control' value='".date('d-m-Y')."' $active/></div> </div>
	<div class='col-xs-4'><div class='input-group'><span class='input-group-addon'>ชื่อลูกค้า</span><input type='text' id='customer_name' class='form-control' value='$customer_name' autocomplete='off' $active/></div> </div>
	<div class='col-xs-3'><input type='checkbox' id='auto_zone' name='auto_zone' $active/><label for='auto_zone' style='margin-left:10px;'>เลือกโซนอัตโนมัติ</label></div>
	</div>
	<div class='row' style='margin-top:15px;'><input type='hidden' name='id_customer' id='id_customer' value='$id_customer' />
	<input type='hidden' name='id_zone' id='id_zone' value='$id_zone' />
	<div class='col-xs-3'><div class='input-group'><span class='input-group-addon'>เลือกโซน</span>
			<select name='zone_id' id='zone_id' class='form-control' $active>";
				select_zone_consign($id_zone);
	echo"
			</select>
	</div> </div>
	<div class='col-xs-6'><div class='input-group'><span class='input-group-addon'>หมายเหตุ</span><input type='text' id='comment' name='comment' class='form-control' value='$comment' autocomplete='off' $active/></div> </div>
	<div class='col-xs-2' >
		<button class='btn btn-default' type='button' id='add_order' ";if(isset($_GET['id_consign_check'])){echo "style='display:none;'";}echo ">&nbsp&nbsp;เพิ่ม&nbsp;&nbsp</button>
		<button class='btn btn-default' type='button' id='edit_order' $edit $can_edit ";if($consign_valid == 1){echo "disabled='disabled'";} if(!isset($_GET['id_consign_check'])){echo "style='display:none;'";}echo ">&nbsp&nbsp;แก้ไข&nbsp;&nbsp</button>
	</div>
	<div class='col-xs-1' >";if(isset($_GET['id_consign_check'])){if($consign_valid == 0 ){echo "<a href='controller/consignController.php?cancel_consign_check=y&id_consign_check=$id_consign_check' ><button type='button' class='btn btn-danger btn-sm' onclick=\"return confirm('คุณแน่ใจว่าต้องการยกเลิก $reference'); \" >ยกเลิก</button></a>";}}echo "</div>
	</div></form>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />";
	//////////////////// ได้ออเดอร์ ID แล้ว
	if(isset($_GET['id_consign_check'])){ 
		echo"
			<div class='row'><input type='hidden' name='id_consign_check' id='id_consign_check' value='$id_consign_check' />
			<input name='id_customer' id='id_customer' value='$id_customer' type='hidden' />
	<div class='col-xs-3'><div class='input-group'>&nbsp;&nbsp&nbsp;&nbsp<input type='checkbox' id='checkboxes' onclick='check_import()' $active1 /><label for='auto_zone' style='margin-left:10px;'>อัพยอดโดยใช้ไฟล์ CSV</label></div> </div> 
	<div id='attribute_import1'>
	<div class='col-xs-2'><div class='input-group'><span class='input-group-addon'>จำนวน</span><input type='text' id='qty' name='qty' value='1' class='form-control' autocomplete='off'  $active1 /></div> </div>
	<div class='col-xs-4'><div class='input-group'><span class='input-group-addon'>บาร์โค้ด</span><input type='text' id='barcode_consign' class='form-control' autocomplete='off' autofocus $active1 /></div> </div>
	<div class='col-xs-2'><div class='input-group' id='load'><button class='btn btn-default' type='button' id='add_detail' onclick='check_process()' $active1 >&nbsp&nbsp;ตกลง&nbsp;&nbsp</button></div> </div></div>
	<div id='attribute_import2' style='display:none'>
	<form method='post' enctype='multipart/form-data' action='controller/consignController.php?import_consign_check=y'>
	<div class='col-xs-2'><div class='input-group'><input type='hidden' name='id_consign_check' value='$id_consign_check'></div> </div>
	<div class='col-xs-4'><div class='input-group'><span class='input-group-addon'>import.csv</span><input type='file' id='file' name='file' accept='.csv' class='btn btn-default'  autocomplete='off' autofocus required /></div> </div>
	<div class='col-xs-2'><div class='input-group' id='load'><button class='btn btn-default' type='submit' id='add_detail'>&nbsp&nbsp;ตกลง&nbsp;&nbsp</button></div> </div></div>
	<div class='col-xs-1'></div>
	</form>
	</div>";
	echo"<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	<div class='row'>
	<table class='table' id='table1'>
	<thead id='head'>
				<th style='text-align:center;'>รูป</th><th style='width:15%;'>บาร์โค้ด</th><th style='width:30%;'>สินค้า</th>
			   <th style='width:10%; text-align:center;'>จำนวนสต๊อก</th><th style='width:10%; text-align:center;'>ยอดต่าง</th>
			   <th style='width:10%; text-align:center;'>จำนวนที่เช็ค</th><th style='width:10%; text-align:center;'></th>
	</thead>";
	$sql = dbQuery("SELECT id_consign_check_detail, tbl_consign_check_detail.id_product_attribute, barcode, reference, qty_stock, qty_check FROM tbl_consign_check_detail LEFT JOIN tbl_product_attribute ON tbl_consign_check_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_consign_check = $id_consign_check ORDER BY tbl_consign_check_detail.date_upd DESC");
	$row = dbNumRows($sql);
	$n = 1;
	$i = 0;
	$sumqty_stock = '0';
	$sumqty_check ='0';
	$sumqty_diff = '0';
	if($row>0){
	while($i<$row){
		list($id_consign_check_detail,$id_product_attribute,$barcode,$reference,$qty_stock,$qty_check)= dbFetchArray($sql);
		$product = new product();
		$product->product_attribute_detail($id_product_attribute);
		$diff = $qty_stock - $qty_check;
		$sumqty_stock = $sumqty_stock + $qty_stock;
		$sumqty_check = $sumqty_check + $qty_check;
		$sumqty_diff = $sumqty_diff + $diff;
		echo"<tr id='row$id_product_attribute'>
		<td style='text-align:center; vertical-align:middle;'><img src='".$product->get_product_attribute_image($id_product_attribute,1)."' /> </td>
		<td style='vertical-align:middle;'>$barcode</td>
		<td style='vertical-align:middle;'>$reference </td>
		<td style='text-align:center; vertical-align:middle;'>$qty_stock</td>
		<td id='diff$id_product_attribute' style='text-align:center; vertical-align:middle;'>$diff</td>
		<td id='check$id_product_attribute' style='text-align:center; vertical-align:middle;'>$qty_check</td>
		<td id='edit$id_product_attribute' style='text-align:center; vertical-align:middle;'>
				<button type='button' class='btn btn-primary btn-sm' $active1 onclick='edit_qty($id_product_attribute,$qty_check,$id_consign_check)'>
				<span class='glyphicon glyphicon-pencil' style='color: #fff;'></span></button>
				</td></tr>";
				$i++;
				$n++;
	}
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไม่มีรายการสินค้า</h3></td></tr>";
	}
	echo"
		<tr><td colspan='3'  align='center'>รวม</td><td align='center'>$sumqty_stock</td><td align='center'>$sumqty_diff</td><td align='center'>$sumqty_check</td><td align='center'></td>
	</table>	
	";
	
	 }

//-----------------------------------------รายการกระทบยอด------------------------------------------------//
}else if(isset($_GET['consign_check'])){
		$paginator = new paginator();
echo"<form  method='post' id='form'>
		<div class='row'>
			<div class='col-sm-2 col-sm-offset-4'>
				<div class='input-group'>
					<span class='input-group-addon'> จาก :</span>
					<input type='text' class='form-control' name='from_date' id='from_date'  value='";
					 if(isset($_POST['from_date']) && $_POST['to_date'] && $_POST['from_date'] && $_POST['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($_POST['from_date']));} else { echo "เลือกวัน";} 
					 echo "'/>
				</div>		
			</div>	
			<div class='col-sm-2 '>
				<div class='input-group'>
					<span class='input-group-addon'>ถึง :</span>
				 <input type='test' class='form-control'  name='to_date' id='to_date' value='";
				  if(isset($_POST['from_date']) && $_POST['to_date'] && $_POST['from_date'] && $_POST['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($_POST['to_date']));} else{ echo "เลือกวัน";}  echo"' />
				</div>
			</div>
			<div class='col-sm-1'>
					<button type='button' class='btn btn-default' onclick='validate()'>แสดง</button>
			</div>	
         </div>
				</form>
				<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />";
						$view = "";
		if(isset($_POST['from_date'])){ $from = date('Y-m-d',strtotime($_POST['from_date'])); }else{ $from = "";} if(isset($_POST['to_date'])){  $to =date('Y-m-d',strtotime($_POST['to_date'])); }else{ $to = "";}
		if($from==""){
			if($to==""){
				$view = getConfig("VIEW_ORDER_IN_DAYS");
			}
		}
				if($view !=""){
			$date = getLastDays($view);
			$from = $date['from'];
			$to = $date['to'];
		}
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		$paginator->Per_Page("tbl_consign_check","WHERE (date_add BETWEEN '$from' AND '$to') ORDER BY id_consign_check DESC",$get_rows);
		$paginator->display($get_rows,"index.php?content=consign");
		$Page_Start = $paginator->Page_Start;
		$Per_Page = $paginator->Per_Page;
	echo "<div class='row'>
	<div class='col-sm-12'>
		<table class='table'>
    		<thead style='color:#FFF; background-color:#48CFAD;'>
       		 	<th style='width:5%; text-align:center;'>ID</th><th style='width:10%;'>เลขที่อ้างอิง</th>
        	    <th style='width:10%;'>ลูกค้า</th><th style='width:10%;'>สถานะ</th>
				<th style='width:10%; text-align:center;'>วันที่เพิ่ม</th>
    	    </thead>";
		$result = dbQuery("SELECT id_consign_check,reference,id_customer,id_zone,date_add,consign_valid FROM tbl_consign_check WHERE (date_add BETWEEN '$from' AND '$to') ORDER BY id_consign_check DESC");
		$i=0;
		$row = dbNumRows($result);
		if($row>0){
		while($i<$row){
			list($id_consign_check,$reference,$id_customer,$id_zone,$date_add,$consign_valid)=dbFetchArray($result);
			$customer = new customer($id_customer);
			$customer_name = $customer->full_name;
			echo"<tr >
					<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=consign&add_consign_check=y&id_consign_check=$id_consign_check'\">".($i+1)."</td>
					<td style='cursor:pointer;' onclick=\"document.location='index.php?content=consign&add_consign_check=y&id_consign_check=$id_consign_check'\">$reference</td>
					<td style='cursor:pointer;' onclick=\"document.location='index.php?content=consign&add_consign_check=y&id_consign_check=$id_consign_check'\">$customer_name</td>
					<td style='cursor:pointer;' onclick=\"document.location='index.php?content=consign&add_consign_check=y&id_consign_check=$id_consign_check'\">";if($consign_valid == 0){echo "<span style='color:red'>ยังไม่ได้ตัดยอดฝากขาย</span>";}else if($consign_valid == 2){echo "ยกเลิก";}else{echo "ตัดยอดฝากขายแล้ว";}
			 echo "</td>
					<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=consign&add_consign_check=y&id_consign_check=$id_consign_check'\">"; echo thaiDate($date_add)."</td>
				</tr>";
		$i++;
		}
		}else if($row==0){
			echo"<tr><td colspan='9' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;ไม่มีรายการในช่วงนี้</h3></td></tr>";
		}
		echo"</table>";
		echo $paginator->display_pages();
		echo "<br><br></div></div>";
	//*********************************************** เพิ่มรายการใหม่ ********************************************************// 
}else if(isset($_GET['add'])){ 
	if(!isset($_GET['id_order_consign'])){ 
			$reference = "";//get_max_role_reference_consign("PREFIX_CONSIGN",$role);
			$user_id = $_COOKIE['user_id'];
			$active = "";
			$id_customer = "";
			$customer_name = "";
			$date_add = date("d-m-Y");
			$comment = "";
			$payment = "credit";
			$id_zone = "";
			$consign_status = "";
			$id_order_consign = '';
		}else{
			$id_order_consign = $_GET['id_order_consign'];
			$active = "disabled='disabled'"; 
			$consign = new consign($id_order_consign);
			$id_customer = $consign->id_customer;
			$customer = new customer($id_customer);
			$customer_name = $customer->full_name; 
			$date_add = $consign->date_add;
			$id_zone = $consign->id_zone;
			$reference = $consign->reference;
			$comment = $consign->comment;
			$consign_status = $consign->consign_status;
		}
function select_zone_consign($selected=""){
	echo"<option value='0'>-------เลือกพื้นที่--------</option>";
	$sql = dbQuery("SELECT * FROM tbl_zone WHERE id_warehouse = 2");
	while($rs = dbFetchArray($sql)){
		$id_zone = $rs['id_zone'];
		$zone_name = $rs['zone_name'];
		if($selected==$id_zone){ $select = "selected='selected'";}else{ $select = "";}
		echo"<option value='$id_zone' $select>$zone_name</option>";
	}
}
	/////////  เพิ่มออเดอร์ ID ใหม่
echo"<form id='add_order_form' action='controller/consignController.php?add=y' method='post'>
	<input type='hidden' name='role' value='$role' >
	<div class='row'><input type='hidden' name='id_employee' value='$user_id' /><input type='hidden' name='order_consign' value='$id_order_consign' />
	<div class='col-lg-3'><div class='input-group'><span class='input-group-addon'>เลขที่เอกสาร</span><input type='text' id='doc_id' class='form-control' value='$reference' disabled='disabled'/></div> </div> 
	<div class='col-lg-2'><div class='input-group'><span class='input-group-addon'>วันที่</span><input type='text' id='doc_date' name='doc_date' class='form-control' value='".thaiDate($date_add)."' $active/></div> </div>
	<div class='col-lg-4'><div class='input-group'><span class='input-group-addon'>ชื่อลูกค้า</span><input type='text' id='customer_name' class='form-control' value='$customer_name' autocomplete='off' $active/></div> </div>
	<div class='col-lg-3'><input type='checkbox' id='auto_zone' name='auto_zone' $active/><label for='auto_zone' style='margin-left:10px;'>เลือกโซนอัตโนมัติ</label></div>
	</div>
	<div class='row' style='margin-top:15px;'><input type='hidden' name='id_customer' id='id_customer' value='$id_customer' />
	<input type='hidden' name='id_zone' id='id_zone' value='$id_zone' />
	<div class='col-lg-3'><div class='input-group'><span class='input-group-addon'>เลือกโซน</span>
			<select name='zone_id' id='zone_id' class='form-control' $active>";
				select_zone_consign($id_zone);
	echo"
			</select>
	</div> </div>
	<div class='col-lg-6'><div class='input-group'><span class='input-group-addon'>หมายเหตุ</span><input type='text' id='comment' name='comment' class='form-control' value='$comment' autocomplete='off' $active/></div> </div>
	<div class='col-lg-2'>
		<button class='btn btn-default' type='button' id='add_order' ";if(isset($_GET['id_order_consign'])){echo "style='display:none;'";}echo " >&nbsp&nbsp;เพิ่ม&nbsp;&nbsp</button>
		<button class='btn btn-default' type='button' id='edit_order' $edit $can_edit ";if($consign_status == 1){echo "disabled='disabled'";} if(!isset($_GET['id_order_consign'])){echo "style='display:none;'";}echo ">&nbsp&nbsp;แก้ไข&nbsp;&nbsp</button>
		<button class='btn btn-default' type='button' id='update_order' style='display:none;'>&nbsp;&nbsp;บันทีก&nbsp;&nbsp;</button>
	</div>
	</div></form>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />";
	//////////////////// ได้ออเดอร์ ID แล้ว
	if(isset($_GET['id_order_consign'])){ 
		echo"<form id='add_detail_form' action='controller/consignController.php?insert_detail&id_zone=".$id_zone."' method='post'>
		<input type='hidden' name='id_role' value='$role' >
			<div class='row'><input type='hidden' name='id_order_consign' id='id_order_consign' value='$id_order_consign' />
			<input type='hidden' name='stock_qty' id='stock_qty' /><input name='id_product_attribute' id='id_product_attribute' type='hidden' /><input name='id_customer' id='id_customer' value='$id_customer' type='hidden' />
	<div class='col-lg-3'><div class='input-group'><span class='input-group-addon'>บาร์โค้ด</span><input type='text' id='barcode' class='form-control' autocomplete='off' autofocus /></div> </div> 
	<div class='col-lg-4'><div class='input-group'><span class='input-group-addon'>สินค้า</span><input type='text' id='product_code' class='form-control' autocomplete='off' /></div> </div>
	<div class='col-lg-2'><div class='input-group'><span class='input-group-addon'>ในสต็อก</span><input type='text' id='stock_label' class='form-control' disabled='disabled' /></div> </div>
	<div class='col-lg-2'><div class='input-group'><span class='input-group-addon'>จำนวน</span><input type='text' id='qty' name='qty' class='form-control' autocomplete='off' autofocus /></div> </div>
	<div class='col-lg-1'><button class='btn btn-default' type='button' id='add_detail' onclick='submit_detail()'>&nbsp&nbsp;เพิ่ม&nbsp;&nbsp</button></div>
	</div></form>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />";
	//*********************************  เริ่ม ORDER GRID ******************************************//
/*	echo"
	<div class='row'>
	<div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
	<ul class='nav nav-tabs' role='tablist' style='background-color:#EEE'>";
	$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = 0 AND level_depth = 1 ORDER BY position ASC");
				$row = dbNumRows($sql);
				$i=0;
				while($i<$row){
				list($id_category, $category_name) = dbFetchArray($sql);
				$sqr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category ORDER BY position ASC");
				$rs = dbNumRows($sqr);
				$n=0;
				if($rs<1){
					echo"<li calss=''><a href='#cat-$id_category' role='tab' data-toggle='tab'>$category_name</a>";
				}else{				
				echo"<li class='dropdown'><a id='ul-$id_category' class='dropdown-toggle' data-toggle='dropdown' href='#'>$category_name<span class='caret'></span></a>";
				echo"<ul class='dropdown-menu' role='menu' aria-labelledby='ul-$id_category'>";
				echo"<li class=''><a href='#cat-$id_category' tabindex='-1' role='tab' data-toggle='tab'>$category_name</a></li>";     
				while($n<$rs){
				list($id_sub_category, $sub_category_name) = dbFetchArray($sqr);
				echo" <li class=''><a href='#cat-$id_sub_category' tabindex='-1' role='tab' data-toggle='tab'>$sub_category_name</a></li>";
				$n++;
				}
				echo"</ul></li>";
				}	
				echo "</li>";
				$i++;
				}
	echo"
	</ul>
	</div>
	</div>
<div class='row'><div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />	
<div class='tab-content'>";	
$query = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category !=0");
	$rc = dbNumRows($query);
	$r =0;
	while($c = dbFetchArray($query)){
		$id_category = $c['id_category'];
		$cate_name = $c['category_name'];
		echo"<div class='tab-pane"; if($r==0){ echo" active";} echo"' id='cat-$id_category'>";	
		$sql = dbQuery("SELECT tbl_category_product.id_product FROM tbl_category_product LEFT JOIN tbl_product ON tbl_category_product.id_product = tbl_product.id_product WHERE id_category = $id_category ORDER BY product_code ASC");
		$row = dbNumRows($sql); 
		if($row>0){
			$i=0;
			while($i<$row){
				list($id_product) = dbFetchArray($sql);
				$product = new product();
				$product->product_detail($id_product);
				
		 echo"<div class='col-lg-1 col-md-1 col-sm-3 col-xs-4' style='text-align:center;'>
			<div class='product' style='padding:5px;'>
			<div class='image'><a href='#' onclick='getData(".$product->id_product.")'>".$product->getCoverImage($product->id_product,1,"img-responsive")."</a></div>
			<div class='description' style='font-size:10px; min-height:50px;'><a href='#'  onclick='getData(".$product->id_product.")'>".$product->product_code."</a></div>
			  </div></div>";
				$i++;
				$r++;
			}
		}else{ 
			echo"<br/><h4 style='text-align:center;'>ยังไม่มีรายการสินค้า</h4>";
		}
		echo "</div>";
	}	
	echo"</div> <button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button>
</div></div>";	*/
//************************************ จบ ORDER GRID **********************************************//		
/*echo"			
	<form action='controller/consignController.php?add_to_order' method='post'>
	<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' id='modal'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='modal_title'>title</h4><input type='hidden' name='id_order_consign' value='$id_order_consign'/>
									  </div>
									  <div class='modal-body' id='modal_body'></div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
										<button type='submit' class='btn btn-primary'>เพิ่มในรายการ</button>
									  </div>
									</div>
								  </div>
								</div></form>";
	echo"<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />*/
	echo "<div class='row'>
	<table class='table' id='order_detail'>
	<thead>
				<th stype='width:5%; text-align:center;'>ลำดับ</th><th style='text-align:center;'>รูป</th><th style='width:15%;'>บาร์โค้ด</th><th style='width:30%;'>สินค้า</th>
			   <th style='width:10%; text-align:center;'>ราคา</th><th style='width:10%; text-align:center;'>จำนวน</th>
			   <th style='width:10%; text-align:center;'>ส่วนลด</th><th style='width:10%; text-align:center;'>มูลค่า</th><th style='text-align:center;'>การกระทำ</th>
	</thead>";
	$sql = dbQuery("SELECT id_order_consign_detail,tbl_order_consign_detail.id_product_attribute,qty,tbl_order_consign_detail.date_upd,barcode,reference,product_price,reduction_percent,reduction_amount FROM tbl_order_consign_detail LEFT JOIN tbl_product_attribute ON tbl_order_consign_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_order_consign = $id_order_consign ORDER BY barcode ASC");
	$row = dbNumRows($sql);
	$n = 1;
	$i = 0;
	if($row>0){
	while($i<$row){
		list($id_order_consign_detail,$id_product_attribute,$qty,$date_add,$barcode,$product_reference,$product_price,$reduction_percent,$reduction_amount)= dbFetchArray($sql);
		$product = new product();
		$product->product_attribute_detail($id_product_attribute);
	
		if($reduction_amount == 0){
			$discount = $reduction_percent;
			$unit = "%";
		}else{
			$discount = $reduction_amount;
			$unit = "";
		}
		if($unit == "%"){
			$dis = ($product_price * $discount)/100;
			$total_discount_amount1 = $dis * $qty;
			$price = $product_price - $dis;
			$total = $price * $qty;
			$total1 = $product_price * $qty;
		}else{
			$total_discount_amount1 = $discount * $qty;;
			$price = $product_price - $discount;
			$total = $price * $qty;
			$total1 = $product_price * $qty;
		}
		echo"<tr><td style='text-align:center; vertical-align:middle;'>$n</td>
		<td style='text-align:center; vertical-align:middle;'><img src='".$product->get_product_attribute_image($id_product_attribute,1)."' /> </td>
		<td style='vertical-align:middle;'>$barcode</td>
		<td style='vertical-align:middle;'>$product_reference</td>
		<td style='text-align:center; vertical-align:middle;'>".number_format($product_price,2)."</td>
		<td style='text-align:center; vertical-align:middle;'>".number_format($qty)."</td>
		<td style='text-align:center; vertical-align:middle;'>$discount</td>
		<td style='text-align:center; vertical-align:middle;'>".number_format($total1,2)."</td>
		<td style='text-align:center; vertical-align:middle;'><a href='controller/consignController.php?delete=y&id_order_consign_detail=$id_order_consign_detail&id_order_consign=$id_order_consign&id_customer=$id_customer' >
				<button type='button' class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $reference '); \" >
				<span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a>
				</td></tr>";
				$i++;
				$n++;
	}
	}else{
		echo"<tr><td colspan='8' align='center'><h3>ยังไม่มีรายการสินค้า</h3></td></tr>";
	}
	echo"
				
	</table>	<br><br><br>
	";
	
	 }
}else if(isset($_GET['view_detail'])&&isset($_GET['id_order_consign'])){
//*********************************************** แก้ไขออเดอร์ **************************************************************//
	$id_employee = $_COOKIE['user_id'];
	$id_order_consign = $_GET['id_order_consign'];
	$id_customer = $_GET['id_customer'];
	$consign = new consign($id_order_consign);
	$customer = new customer($id_customer);
	$comment = $consign->comment;
	$consign_status = $consign->consign_status;
	echo "
        <div class='row'>
        	<div class='col-lg-12'><h4>".$consign->reference." - ";if($consign->id_customer != "0"){echo $customer->full_name;}echo "</h4></div>
        </div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'>
		<div class='col-lg-12'>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>วันที่สั่ง : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".thaiDate($consign->date_add)."</dd>  </dt></dl>
		<p class='pull-right'  >";
		if($consign_status == 0){
			echo "
			<button type='button' class='btn btn-primary' id='bill_btn'>เปิดใบกำกับภาษี</button>
			<script>
				 $('#bill_btn').one('click', function(){
				 window.location.href = 'controller/consignController.php?confirm_consign&id_order_consign=$id_order_consign&id_employee=$id_employee';
				  }); 
			</script>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href='controller/consignController.php?cancel_consign=y&id_order_consign=$id_order_consign' ><button type='button' class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการยกเลิกหรือไม่'); \" >ยกเลิก</button></a>";
		}else if($consign_status == 2){
		}else if($consign_status == 1){
			if($edit_cancle == 1){ 
				echo "<a href='controller/consignController.php?roll_back=y&id_order_consign=$id_order_consign' ><button type='button' class='btn btn-warning btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการยกเลิกการเปิดบิลแล้วกลับไปแก้ไขรายการใหม่'); \" >ยกเลิกการเปิดบิล</button></a>";
			}
			echo"
			<a href='controller/consignController.php?print_order&id_order_consign=$id_order_consign' >
			<button type='button' class='btn btn-primary'><i class='fa fa-print'></i>&nbsp;พิมพ์</button>
			</a>";
		}echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</p>
		</div></div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />";
		echo "	
		<div class='row'><div class='col-lg-12'>";
		$total_order = ""; ///เก็บยอดรวมเพิ่มทีละรายการเวลาวนลูป
		$total_order_amount = "";///วนลูปจบเอาค่ามาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
		$total_discount_order =""; //เก็บยอดเงินส่วนลดตอนวนลูป
		$total_discount_amount = ""; //วนลูปจบเอายอดเงินส่วนลดมาใส่ในนี้เพื่อแสดงหน้าสุดท้าย
		$net_total =""; //มูลค่าสินค้าหลังหักส่วนลด
		$total_qty = 0;
		$sql = dbQuery("SELECT id_order_consign_detail,tbl_order_consign_detail.id_product_attribute,qty,tbl_order_consign_detail.date_upd,barcode,reference,product_price,reduction_percent,reduction_amount FROM tbl_order_consign_detail LEFT JOIN tbl_product_attribute ON tbl_order_consign_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_order_consign = $id_order_consign ORDER BY barcode ASC");
		$row = dbNumRows($sql);
		$i=0;
		$n = 1;
		if($consign_status == 0){
		echo"
		<button type='button' id='edit_reduction' class='btn btn-default' $can_edit>แก้ไขส่วนลด</button>
		<a href='#'  data-toggle='modal' data-target='#ModalLogin' id='discount_link' style='display:none;' > <button type='button' id='save_reduction' class='btn btn-default' disabled='disabled' $can_edit>บันทึกส่วนลด</button></a>
		<button type='button' id='edit_price' class='btn btn-default' $can_edit>แก้ไขราคา</button>
		<a href='#'  data-toggle='modal' data-target='#Modal_price' id='price_link' style='display:none;' > <button type='button' id='save_price' class='btn btn-default' disabled='disabled' $can_edit>บันทึกราคา</button></a>
		<br><br><form id='detail_form' action='controller/consignController.php?edit_price&id_order_consign=$id_order_consign&id_customer=$id_customer' method='post'>";
		}
		echo "
		<table id='product_table' style='width:100%; padding:10px;'>
		<thead>
			<th style='width:5%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>ลำดับ</th>
			<th style='width:15%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>บาร์โค้ด</th>
			<th style='width:35%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>รหัสสินค้า</th>
			<th style='width:10%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>ราคา</th>
			<th style='width:8%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>จำนวน</th>
			<th style='width:8%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>ส่วนลด</th>
			<th style='width:12%; text-align:center; height:12mm; vertical-align:middle; border: solid 1px #AAA;'>จำนวนเงิน</th>
		</thead>";
	if($row>0){
		while($i<$row){
		list($id_order_consign_detail,$id_product_attribute,$qty,$date_add,$barcode,$product_reference,$product_price,$reduction_percent,$reduction_amount)= dbFetchArray($sql);
		if($reduction_amount == 0){
			$discount = $reduction_percent;
			$unit = "%";
		}else{
			$discount = $reduction_amount;
			$unit = "";
		}
		if($unit == "%"){
			$dis = ($product_price * $discount)/100;
			$total_discount_amount1 = $dis * $qty;
			$price = $product_price - $dis;
			$total = $price * $qty;
			$total1 = $product_price * $qty;
		}else{
			$total_discount_amount1 = $discount * $qty;;
			$price = $product_price - $discount;
			$total = $price * $qty;
			$total1 = $product_price * $qty;
		}
		$input_reduction = "<div class='input' style='display:none;'><div class='input_reduction' ><div class='input-group input-group-sm'  ><input type='text' class='form-control' id='percent$n' value='$reduction_percent' /><span class='input-group-addon'>%</span>
</div></div>หรือ
<div class='input_reduction' ><div class='input-group input-group-sm'  ><input type='text' class='form-control' id='amount$n' value='$reduction_amount' /><span class='input-group-addon'>฿</span>
</div></div></div>"; 
		echo"<tr style='height:12mm;'>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA;'>$n<input type='hidden' id='id_order_consign_detail$n' value='$id_order_consign_detail'> </td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA;'>$barcode</td>
		<td style='vertical-align:middle; padding:3px; border: solid 1px #AAA;'>$product_reference</td><input type='hidden' name='id[]' value='$id_order_consign_detail' />
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA;'><input type='text' name='price_$id_order_consign_detail' class='input-price input-label' disabled style='text-align:center' value='".number_format($product_price,2)."' required /></td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA;'>".number_format($qty)."</td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; '><p class='reduction' id='reduction' >$discount $unit</p>$input_reduction </td>
		<td style='text-align:center; vertical-align:middle; padding:3px; border: solid 1px #AAA; ;'>".number_format($total,2)."</td>
		</tr>";
				$total_order += $total1;
				$total_discount_order += $total_discount_amount1;
				$total_qty += $qty;
				$i++; 
				if($n==$row){ 
				$total_order_amount = $total_order;
				$total_discount_amount = $total_discount_order;
				$net_total = $total_order_amount - $total_discount_amount;
				echo "<tr><td rowspan='3' colspan='3' style='border:solid 1px #AAA;  padding:10px; vertical-align:text-top;'>หมายเหตุ : $comment</td>
				<td style='border:solid 1px #AAA;  padding:10px'>จำนวนรวม</td><td align='center' style='border:solid 1px #AAA;  padding:10px'>".number_format($total_qty)."</td>
				<td style='border:solid 1px #AAA;  padding:10px'>ราคารวม</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding:10px'>".number_format($total_order_amount)."</td></tr>
				<tr><td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ส่วนลด</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding:10px'>".number_format($total_discount_amount)."</td></tr>
				<tr><td colspan='2' style='border:solid 1px #AAA;  padding:10px'>ยอดเงินสุทธิ</td><td align='right' colspan='2' style='border:solid 1px #AAA;  padding:10px'>".number_format($net_total)."</td></tr>
				";
					}
				$n++; 
			}			
		}else{
			 if($consign_status == 2){
				 echo"<tr><td colspan='7' align='center'><h3>ยกเลิก</h3></td></tr>";
			 }else{
				echo"<tr><td colspan='7' align='center'><h3>ยังไม่มีรายการสินค้า</h3></td></tr>";
			 }
	}
		echo "</table></form></div></div><input type='hidden' id='id_order_consign' value='$id_order_consign'><input type='hidden' id='loop' value='".($n-1)."'>
		<div class='modal fade' id='ModalLogin' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width: 350px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site text-center' > รหัสลับผู้มีอำนาจการแก้ไขส่วนลด </h4>
			</div>
			<input type='hidden' id='id_employee' name='id_employee'>
			<div class='modal-body'>
				<div class='form-group login-password'>
					<input id='password_discount' class='form-control input'  size='20' placeholder='รหัสลับ' type='password' required='required'>
				</div>
				<input id='login' class='btn  btn-block btn-lg btn-primary' value='ตกลง' type='button' onclick='check_password(\"discount\")' >
				<!--userForm--> 
			</div>
			<p style='text-align:center; color:red;' id='message' class='message'></p>
			<div class='modal-footer'>
			</div>
		</div>
		<!-- /.modal-content --> 
	</div>
	<!-- /.modal-dialog --> 
</div>
<!-- /.Modal Login -->

<div class='modal fade' id='Modal_price' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width: 350px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site text-center' > รหัสลับผู้มีอำนาจการแก้ไขราคา </h4>
			</div>
			<input type='hidden' id='id_employee' name='id_employee'>
			<div class='modal-body'>
				<div class='form-group login-password'>
					<input id='password_price' class='form-control input'  size='20' placeholder='รหัสลับ' type='password' required='required'>
				</div>
				<input id='login' class='btn  btn-block btn-lg btn-primary' value='ตกลง' type='button' onclick='check_password(\"price\")' >
				<!--userForm--> 
			</div>
			<p style='text-align:center; color:red;' id='message' class='message'></p>
			<div class='modal-footer'>
			</div>
		</div>
		<!-- /.modal-content --> 
	</div>
	<!-- /.modal-dialog --> 
</div>
<!-- /.Modal Login --> ";
//*********************************************** จบหน้าแก้ไข ****************************************************//
}else{
//************************************************ แสดงรายการ *************************************************//
	if( isset($_POST['from_date']) && $_POST['from_date'] !="เลือกวัน"){ setcookie("from_date", date("Y-m-d", strtotime($_POST['from_date'])), time() + 3600, "/"); }
	if( isset($_POST['to_date']) && $_POST['to_date'] != "เลือกวัน"){ setcookie("to_date",  date("Y-m-d", strtotime($_POST['to_date'])), time() + 3600, "/"); }
	$paginator = new paginator();
echo"<form  method='post' id='form'>
		<div class='row'>
			<div class='col-lg-2 col-md-2 col-sm-3 col-sx-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เงื่อนไข</span>
						<select class='form-control' name='filter' id='filter'>
							<option value='customer'"; if( isset($_POST['filter']) && $_POST['filter'] =="customer"){ echo "selected"; }else if( isset($_COOKIE['filter']) && $_COOKIE['filter'] == "customer"){ echo "selected"; } echo ">ลูกค้า</option>
							<option value='reference'"; if( isset($_POST['filter']) && $_POST['filter'] =="reference"){ echo "selected"; }else if( isset($_COOKIE['filter']) && $_COOKIE['filter'] == "reference"){ echo "selected"; } echo ">เลขที่เอกสาร</option>
						</select>
				</div>		
			</div>	
			<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3' style='border-right:1px solid #ccc;'>
				<div class='input-group'>
					<span class='input-group-addon'>ค้นหา</span>
						<input class='form-control' type='text' name='search-text' id='search-text' value='";
						if(isset($_POST['search-text']) && $_POST['search-text'] !=""){ echo $_POST['search-text']; }else if(isset($_COOKIE['search-text'])){ echo $_COOKIE['search-text']; }
						echo "'/>
					<span class='input-group-btn'><button class='btn btn-default' id='search-btn' type='button'><span id='load'><span class='glyphicon glyphicon-search'></span></span></button>
				</div>		
			</div>	
			<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
				<div class='input-group'>
					<span class='input-group-addon'> จาก :</span>
					<input type='text' class='form-control' name='from_date' id='from_date'  value='";
					if(isset($_POST['from_date']) && $_POST['from_date'] != "เลือกวัน"){ 
						echo date("d-m-Y", strtotime($_POST['from_date'])); 
						}else if( isset($_COOKIE['from_date'])){ 
						echo date("d-m-Y", strtotime($_COOKIE['from_date'])); 
						}else{ 
						echo "เลือกวัน";
						 }
					 echo "'/>
				</div>		
			</div>	
			<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
				<div class='input-group'>
					<span class='input-group-addon'>ถึง :</span>
				 <input type='test' class='form-control'  name='to_date' id='to_date' value='";
				 if( isset($_POST['to_date']) && $_POST['to_date'] != "เลือกวัน" ){ 
				 	echo date("d-m-Y", strtotime($_POST['to_date'])); 
				 }else if( isset($_COOKIE['to_date']) ){
					 echo date("d-m-Y", strtotime($_COOKIE['to_date']));
				 }else{ 
				 	echo "เลือกวัน"; 
				 }
				 echo "'/>
				</div>
			</div>
			<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
					<button type='button' class='btn btn-default' onclick='validate()'>แสดง</button>
			</div>	
			<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
					<button type='button' class='btn btn-default' onclick='window.location.href=\"controller/consignController.php?clear_filter\"'><i class='fa fa-refresh'></i> เคลียร์ฟิลเตอร์</button>
			</div>
         </div>
				</form>
				<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />";
		$view = "";
		if(isset($_POST['from_date']) && $_POST['from_date'] != "เลือกวัน"){$from = date('Y-m-d',strtotime($_POST['from_date'])); }else if( isset($_COOKIE['from_date'])){ $from = date('Y-m-d',strtotime($_COOKIE['from_date'])); }else{ $from = "";} 
		if(isset($_POST['to_date']) && $_POST['to_date'] != "เลือกวัน"){ $to =date('Y-m-d',strtotime($_POST['to_date']));  }else if(  isset($_COOKIE['to_date'])){  $to =date('Y-m-d',strtotime($_COOKIE['to_date'])); }else{ $to = "";}
		if($from=="" || $to ==""){ $view = getConfig("VIEW_ORDER_IN_DAYS"); 	}
		if($view !=""){
			$date = getLastDays($view);
			$from = $date['from'];
			$to = $date['to'];
		}
		$from = $from." 00:00:00";
		$to = $to." 23:59:59";
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		
		/****  เงื่อนไขการแสดงผล *****/
		if(isset($_POST['search-text']) && $_POST['search-text'] !="" )
		{
			$text = $_POST['search-text'];
			$filter = $_POST['filter'];
			setcookie("search-text", $text, time() + 3600, "/");
			setcookie("filter",$filter, time() +3600,"/");
			switch( $_POST['filter']){
				case "customer" :
				$in_cause = "";
				$qs = dbQuery("SELECT id_customer FROM tbl_customer WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%' GROUP BY id_customer");
				$rs = dbNumRows($qs);
				$i=0;
				if($rs>0){
				while($i<$rs){
					list($in) = dbFetchArray($qs);
					$in_cause .="$in";
					$i++;
					if($i<$rs){ $in_cause .=","; 	}
				}
				$where = "WHERE id_customer IN($in_cause) ORDER BY date_add DESC" ; 
				}else{
					$where = "WHERE id_order_consign != NULL";
				}
				break;
				case "reference" :
				$where = "WHERE reference LIKE'%$text%' ORDER BY reference";
				break;
			}
		}else if(isset($_COOKIE['search-text']) && isset($_COOKIE['filter'])){
			$text = $_COOKIE['search-text'];
			$filter = $_COOKIE['filter'];
			switch( $filter){
				case "customer" :
				$in_cause = "";
				$qs = dbQuery("SELECT id_customer FROM tbl_customer WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%' GROUP BY id_customer");
				$rs = dbNumRows($qs);
				$i=0;
				if($rs>0){
				while($i<$rs){
					list($in) = dbFetchArray($qs);
					$in_cause .="$in";
					$i++;
					if($i<$rs){ $in_cause .=","; 	}
				}
				$where = "WHERE id_customer IN($in_cause) ORDER BY date_add DESC" ; 
				}else{
					$where = "WHERE id_order_consign != NULL";
				}
				break;
				case "reference" :
				$where = "WHERE reference LIKE'%$text%' ORDER BY reference";
				break;
			}
		}else{
			$where = "WHERE (date_add BETWEEN '$from' AND '$to') ORDER BY date_add DESC";
		}
		echo "
<div class='row'>
<div class='col-sm-12' id='result'>";
		$paginator->Per_Page("tbl_order_consign",$where,$get_rows);
		$paginator->display($get_rows,"index.php?content=consign");
		$Page_Start = $paginator->Page_Start;
		$Per_Page = $paginator->Per_Page;
	echo "
	<table class='table'>
    	<thead style='color:#FFF; background-color:#48CFAD;'>
        	<th style='width:5%; text-align:center;'>ID</th><th style='width:10%;'>เลขที่อ้างอิง</th>
            <th style='width:20%;'>ลูกค้า</th><th style='width:10%;'>สถานะ</th>
			<th style='width:10%; text-align:center;'>วันที่เพิ่ม</th>
        </thead>";

		$result = dbQuery("SELECT id_order_consign,reference,id_customer,date_add,consign_status FROM tbl_order_consign $where LIMIT $Page_Start , $Per_Page");
		$i=0;
		$row = dbNumRows($result);
		if($row>0){
		while($i<$row){
			list($id_order_consign,$reference,$id_customer,$date_add,$consign_status)=dbFetchArray($result);
			list($first_name,$last_name) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_customer WHERE id_customer = $id_customer"));
			$customer_name =" $first_name $last_name";
		echo"<tr >
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=consign&id_order_consign=$id_order_consign&id_customer=$id_customer&view_detail=y'\">$id_order_consign</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=consign&id_order_consign=$id_order_consign&id_customer=$id_customer&view_detail=y'\">$reference</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=consign&id_order_consign=$id_order_consign&id_customer=$id_customer&view_detail=y'\">$customer_name</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=consign&id_order_consign=$id_order_consign&id_customer=$id_customer&view_detail=y'\">";if($consign_status == 0){ echo "<span style='color:red'>ยังไม่ได้เปิดบิล</span>";}else if($consign_status == 2 ){echo "<span style='color:red'>ยกเลิก</span>";}else{echo "เปิดบิลแล้ว";} echo "</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=consign&id_order_consign=$id_order_consign&id_customer=$id_customer&view_detail=y'\">"; echo thaiDate($date_add)."</td>
			</tr>";
		$i++;
		}
		}else if($row==0){
			echo"<tr><td colspan='9' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;ไม่มีรายการในช่วงนี้</h3></td></tr>";
		}
		echo"</table>";
		echo $paginator->display_pages();
		echo "<br><br>";
}
?>
<script>
$(function() {
    $("#from_date").datepicker({
      dateFormat: 'dd-mm-yy', onClose: function( selectedDate ) {
        $( "#to_date" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to_date" ).datepicker({
      dateFormat: 'dd-mm-yy',   onClose: function( selectedDate ) {
        $( "#from_date" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
  $(function() {
    $("#date").datepicker({
      dateFormat: 'dd-mm-yy'
    });
  });
   function validate() {
	var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
	if(from_date =="เลือกวัน"){	
		alert("คุณยังไม่ได้เลือกช่วงเวลา");
		}else if(to_date ==""){
		alert("คุณยังไม่ได้เลือกวันสุดท้าย");	
	}else{
		$("#form").submit();
	}
}	
	function state_change(){
		var state = $("#order_state").val();
		if(state == 0){
			alert("ยังไม่ได้เลือกสถานะ");
		}else{
			$("#state_change").submit();
		}
	}
$("#add_product").click(function() {
    $("#new_row").css("display","");
	var id_cus = $("#id_customer").val();
	$("#id_cus").val(id_cus);
});	

$(document).ready(function(e) {
	$("#product").autocomplete(
	{
		 source: "controller/orderController.php?product",
		 close: function(event,ui){
			 var ref = $(this).val();
			var id_cus = $("#id_cus").val();	
		$.ajax({ 
			 url: "controller/consignController.php?reference="+ref+"&id_customer="+id_cus,
			 type: "GET", cache:false, 
			 success: function(data){
				 if(data !=""){ 
				 	var arr = data.split(':');
					var id = arr[0];
					var price = arr[1];
					var stock = arr[2];
					 $("#id_product_attribute").val(id);
					 $("#price").val(price);
					 $("#available").text("คงเหลือ : "+stock);
					 $("#stock_qty").val(stock);
					 $("#stock_label").val(stock);
				 }
			 }
		});
    }
	});

});

$(document).ready(function(e) {
    $("#doc_date").datepicker({ 
	dateFormat: 'dd-mm-yy'
	});
});
$(document).ready(function(e) {
    $("#customer_name").autocomplete({
		source:"controller/orderController.php?customer_name",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#customer_name").val();
			var arr = data.split(':');
			var id = arr[0];
			var name = arr[1];
			var id_customer = arr[2];
			$(this).val(name);
			$("#id_customer").val(id_customer);
		}
	});			
});
$(document).ready(function(e) {
    $("#auto_zone").change(function(e) {
		if($(this).prop("checked")){
        $("#zone_id").attr("disabled","disabled");
		}else{
		 $("#zone_id").removeAttr("disabled");
		}
    });
});
/// คลิ๊กปุ่มเพิ่มออเดอร์ใหม่ ////
$("#add_order").click(function(e) {
    var date = $("#doc_date").val();
	var cus_name = $("#customer_name").val();
	var cus_id = $("#id_customer").val();
	var id_zone = $("#zone_id").val();
	if(date ==""){
		alert("ยังไม่ได้ระบุวันที่");
	}else if(cus_name == ""){
		alert("ยังไม่ได้เลือกลูกค้า");
	}else if(cus_id ==""){
		alert("ระบบไม่พบ Customer ID ไม่สามารถเพิ่มออเดอร์ได้กรุณาเลือกลูกค้าใหม่หรือติดต่อผู้ดูแลระบบ");
	}else if(!$("#auto_zone").prop("checked")){
		if(id_zone == 0){
			alert("ยังไม่ได้เลือกพื้นที่เก็บ");
		}else{
		$("#add_order_form").submit();
		}
	}else{
		$("#add_order_form").submit();
	}
});

$(document).ready(function(e) {
    $("#product_code").autocomplete({
		source:"controller/orderController.php?product_code",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#product_code").val();
		}
	});			
});
//// product code
$("#product_code").focusout(function(e) {
    var product_code = $(this).val();
	var id_zone = $("#id_zone").val();
	if(product_code !=""){
	$.ajax({
		url:"controller/consignController.php?check_stock&product_code="+product_code+"&id_zone="+id_zone
		,type:"GET",cache:false,success: function(stock_qty){
			if(stock_qty !=""){
				var arr = stock_qty.split(":");
				var id = arr[0].trim();
				$("#id_product_attribute").val(id);
				$("#stock_qty").val(arr[1]);
				$("#stock_label").val(arr[1]);
			}
		}
	});
	}
});
//// barcode 
$("#barcode").focusout(function(e) {
    var barcode = $(this).val();
	var id_zone = $("#id_zone").val();
	if(barcode !=""){
	$.ajax({
		url:"controller/consignController.php?check_stock&barcode="+barcode+"&id_zone="+id_zone
		,type:"GET",cache:false,success: function(data){
			if(data !=""){
				var arr = data.split(":");
				var id = arr[0].trim();
				$("#id_product_attribute").val(id);
				$("#stock_qty").val(arr[1]);
				$("#stock_label").val(arr[1]);
				$("#product_code").val(arr[2]);
			}
		}
	});
	}
});
$("#barcode_consign").bind("enterKey",function(){
	check_process();
});
$("#barcode_consign").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
function check_process(){
    var barcode = $("#barcode_consign").val();
	var qty = $("#qty").val();
	var id_consign_check = $("#id_consign_check").val();
	if(barcode ==""){
		alert("ยังไม่ได้ใส่บาร์โค้ด");
	}else if(qty == ""){
		alert("ยังไม่ได้ใส่จำนวน");
	}else{
		$("#add_order").focus();
		$("#load").html("<img src='../img/ajax-loader.gif' width='32' height='32' />");
		$.ajax({
			url:"controller/consignController.php?add_stock_consign&barcode="+barcode+"&id_consign_check="+id_consign_check+"&qty="+qty
			,type:"GET",cache:false,success: function(dataset){
			$("#barcode_consign").focus();
				arr = dataset.split(":");
				if(arr[0].trim()=="ok"){
					id_product_attribute = arr[1];
					qty_upd = parseInt(arr[2]);
					diff_upd = parseInt(arr[3]);
					//pre_qty = parseInt($("#diff"+id_product_attribute).html());alert("OK");
					$("#check"+id_product_attribute).html(qty_upd);
					$("#diff"+id_product_attribute).html(diff_upd);
					$("#row"+id_product_attribute).insertAfter($("#head"));
				}else if(arr[0].trim()=="error"){
					error = arr[1];
					id_product_attribute = arr[2];
					$("#row"+id_product_attribute).insertAfter($("#head"));
					alert(error);
				}else{
					error = arr[1];
					alert(error);
				}
				$("#barcode_consign").val('');
				$("#load").html("<button class='btn btn-default' type='button' id='add_detail' onclick='check_process()'>&nbsp&nbsp;ตกลง&nbsp;&nbsp</button>");
			}
		});
	}
}
function edit_qty(id_product_attribute,qty_check,id_consign_check){
		$("#check"+id_product_attribute).html("<input type='text' class='form-control' id='edit_check"+id_product_attribute+"' value='"+qty_check+"' >");
		$("#edit"+id_product_attribute).html("<button class='btn btn-default' type='button' id='add_detail' onclick='update_qty("+id_product_attribute+","+id_consign_check+")'>&nbsp&nbsp;update&nbsp;&nbsp</button>");
}
function update_qty(id_product_attribute,id_consign_check){
	var qty = $("#edit_check"+id_product_attribute).val();
	$.ajax({
			url:"controller/consignController.php?edit_stock_consign&id_product_attribute="+id_product_attribute+"&id_consign_check="+id_consign_check+"&qty="+qty
			,type:"GET",cache:false,success: function(dataset){
			$("#barcode_consign").focus();
				arr = dataset.split(":");
				if(arr[0].trim()=="ok"){
					id_product_attribute = arr[1];
					qty_upd = parseInt(arr[2]);
					diff_upd = parseInt(arr[3]);
					//pre_qty = parseInt($("#diff"+id_product_attribute).html());alert("OK");
					$("#check"+id_product_attribute).html(qty_upd);
					$("#diff"+id_product_attribute).html(diff_upd);
					$("#row"+id_product_attribute).insertAfter($("#head"));
					$("#edit"+id_product_attribute).html("<button type='button' class='btn btn-primary btn-sm' onclick='edit_qty("+id_product_attribute+","+qty_upd+","+id_consign_check+")'><span class='glyphicon glyphicon-pencil' style='color: #fff;'></span>");
				}else{
					error = arr[1];
					id_product_attribute = arr[2];
					qty_check = arr[3];
					$("#edit_check"+id_product_attribute).val(qty_check);
					$("#row"+id_product_attribute).insertAfter($("#head"));
					alert(error);
				}
				$("#barcode_consign").val('');
				$("#load").html("<button class='btn btn-default' type='button' id='add_detail' onclick='check_process()'>&nbsp&nbsp;ตกลง&nbsp;&nbsp</button>");
			}
		});
}
/////
$("#qty").keyup(function(e) {
    var limit = parseInt($("#stock_qty").val());
	var qty = parseInt($("#qty").val());
/*	if(qty>limit){
		alert("มีสินค้าในสต็อกแค่ "+limit+" ตัวเท่านั้น");
		$("#qty").val(limit);
	}*/
});
$("#product_code").bind("enterKey",function(){
	if($("#product_code").val() != ""){
	$("#qty").focus();
	}
});
///// ชื่อสินค้า เมื่อกดปุ่ม enter
	$("#product_code").bind("enterKey",function(){
	if($("#product_code").val() != ""){
	$("#qty").focus();
	}
});
$("#product_code").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
/// barcode 
$("#barcode").bind("enterKey",function(){
	if($("#barcode").val() != ""){
		$("#qty").focus();
	}else{
		$("#product_code").focus();
	}
});
$("#barcode").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$("#qty").bind("enterKey",function(){
	if($("#qty").val() != ""){
		$("#add_detail").click();
	}
});
$("#qty").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$(document).ready(function(e) {
    $("#qty").keyup(function(e) {
        var qty = parseInt($(this).val());
		var stock = parseInt($("#stock_qty").val());
		var price = $("#price").val();
	/*	if(qty>stock){
			alert("มีสินค้าในสต็อกแค่ "+stock);
			$(this).val(stock);
			var total = price * qty;
			$("#total_amount").val(total);
			$("#total").html(total +" ฿");
		}else{*/
		var total = price * qty;
		$("#total_amount").val(total);
		$("#total").html(total +" ฿");
		//}
    });
});
function edit_product(id_order, id_product_attribute){
	var name = id_order.toString() + id_product_attribute.toString();
	var qty = parseInt($("#qty"+name).text());
	$("#edit"+name).css("display","none");
	$("#delete"+name).css("display","none");
	$("#update"+name).css("display","");
	$("#edit_qty"+name).val(qty);
	$("#qty"+name).css("display","none");
	$("#edit_qty"+name).css("display","");	
}
function update(id_order, id_product_attribute){
	var name = id_order.toString() + id_product_attribute.toString();
	var qty = $("#edit_qty"+name).val();
	var old_qty = parseInt($("#qty"+name).text());
	if(qty<=0){
		alert("จำนวนที่สั่งอย่างน้อย 1 ตัว");
	}else{
	$.ajax({
		url:"controller/orderController.php?check_stock&id_order="+id_order+"&id_product_attribute="+id_product_attribute, 
		cache:false,
		success: function(stock_qty){
			var stock = parseInt(stock_qty);
			if(qty<=old_qty){
				$("#new_qty").val(qty);
				$("#id_order").val(id_order);
				$("#id_product_attribute").val(id_product_attribute);
				$("#edit_order_form").submit();
			}else if(qty>stock){
				alert("มีสินค้าในสต็อกแค่ "+stock+" เท่านั้น");
				$("#edit_qty"+name).val(stock);
				//$("#new_qty").val(stock);
			}else{
				$("#new_qty").val(qty);
				$("#id_order").val(id_order);
				$("#id_product_attribute").val(id_product_attribute);
				$("#edit_order_form").submit();
			}
		}
	});
	}
}

function submit_detail(){
	var id_order = $("#id_order").val();
	var id_product_attribute =  $("#id_product_attribute").val();
	var order_qty = $("#qty").val();
	var stock_qty = $("#stock_qty").val();
	var id_customer = $("#id_customer").val();
	if(id_order==""){
		alert("ไม่พบตัวแปร id_order ติดต่อผู้ดูแลระบบ");
	}else if(id_product_attribute ==""){
		alert("ไม่พบตัวแปร id_product_attribute ติดต่อผู้ดูแลระบบ");
	}else if(id_customer==""){
		alert("ไม่พบตัวแปร id_product_attribute ติดต่อผู้ดูแลระบบ");
	}else if(order_qty==""){
		alert("ยังไม่ได้ใส่จำนวนสินค้า");
	/*}else if(parseInt(order_qty)>parseInt(stock_qty)){
		alert("จำนวนที่สังมากกว่าจำนวนที่มีในสต็อก");
		$("#qty").val(stock_qty);*/
	}else{
		$("#add_detail_form").submit();
	}
}
//// เพิ่มรายการสั่งซื้อสินค้าแล้วเปลียนหน้าใหม่ ///
function add_detail(){ 
	var id_order = $("#id_order").val();
	var id_product_attribute =  $("#id_product_attribute").val();
	var order_qty = $("#qty").val();
	var stock_qty = $("#stock_qty").val();
	var id_customer = $("#id_customer").val();
	if(id_order==""){
		alert("ไม่พบตัวแปร id_order ติดต่อผู้ดูแลระบบ");
	}else if(id_product_attribute ==""){
		alert("ไม่พบตัวแปร id_product_attribute ติดต่อผู้ดูแลระบบ");
	}else if(id_customer==""){
		alert("ไม่พบตัวแปร id_product_attribute ติดต่อผู้ดูแลระบบ");
	}else if(order_qty==""){
		alert("ยังไม่ได้ใส่จำนวนสินค้า");
	}else if(parseInt(order_qty)>parseInt(stock_qty)){
		alert("จำนวนที่สังมากกว่าจำนวนที่มีในสต็อก");
		$("#qty").val(stock_qty);
	}else{
		$("#edit_order_form").submit();
	}
}
var no = 0;
//////// เพิ่มรายการสั่งซื้อสินค้าแต่ไม่เปลียนหน้าใหม่ ///
function insert_detail(id_order,id_product_attribute, qty){
	$.ajax({
		url:"controller/orderController.php?insert_detail",
		data: {id_order:id_order, id_product_attribute:id_product_attribute, qty:qty},type:"POST",cache:false,
		success: function(complete){
			if(complete !="error"){
				no = no +1;
				var data = complete.split(":");
				var barcode = data[0];
				var product = data[1];
				var price = data[2];
				var quantity = data[3];
				var discount = data[4];
				var amount = data[5];
				$("#order_detail").append("<tr><td align='center'>"+no+"</td><td>"+barcode+"</td><td>"+product+"</td><td>"+price+"</td><td>"+quantity+"</td><td>"+discount+"</td><td>"+amount+"</td></tr>");
			}else{ 
			alert("เพิ่มข้อมูลไม่ได้");
			}
		}
	});
}
$(document).ready(function(e) {
    if($("#error").length){
		alert($("#error").text());
	}
});
function get_row(){
	$("#rows").submit();
}
function getData(id_product){
	var id_cus = $("#id_customer").val();
	var id_zone = $("#id_zone").val();
	$.ajax({
		url:"controller/consignController.php?getData&id_product="+id_product+"&id_customer="+id_cus+"&id_zone="+id_zone,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				$("#modal").css("width",table_w+"px");
				$("#modal_title").html(title);
				$("#modal_body").html(data);
				$("#btn_toggle").click();
			}else{
				alert("NO DATA");
			}		
		}
	});
}
function check_import(){
	if(checkboxes.checked){
		$("#attribute_import1"+name).css("display","none");
		$("#attribute_import2"+name).css("display","");
	}else{
		$("#attribute_import2"+name).css("display","none");
		$("#attribute_import1"+name).css("display","");
	}
}
$("#edit_reduction").click(function(e) {
    $(".reduction").css("display","none");
	$(".input").css("display","block");
	$("#save_reduction").removeAttr("disabled");
	$(this).css("display","none");
	$("#discount_link").css("display","");
});

$("#edit_price").click(function(e) {
    $(".price").css("display","none");
	$(".input-price").removeClass("input-label");
	$(".input-price").addClass("form-control");
	$(".input-price").removeAttr("disabled");
	$("#save_price").removeAttr("disabled");
	$(this).css("display","none");
	$("#price_link").css("display","");
});

function update_price(){
	$("#detail_form").submit();	
}
function update_discount(id_employee){
	//var reduction = $("#reduction[]").val();
	//alert ("reduction"+id_employee);
	var id_order_consign_detail_array = [];
	var amount_array = [];
	var percent_array = [];
	var id_order_consign = $("#id_order_consign").val();
	//alert(loop);
	for ( i = 0 ;i < $("#loop").val(); i++ ){
		//alert(i);
		var n = i+1;
		var id_order_consign_detail = $("#id_order_consign_detail"+n).val();
		var amount = $("#amount"+n).val();
		var percent = $("#percent"+n).val();
		id_order_consign_detail_array[n] = id_order_consign_detail;
		amount_array[n] = amount;
		percent_array[n] = percent;
		//alert("id_order_detail"+n);
	}
	$.ajax({
		url:"controller/consignController.php?edit_discount",
		data: {id_employee:id_employee, amount_array:amount_array,percent_array:percent_array, id_order_consign_detail_array:id_order_consign_detail_array,id_order_consign:id_order_consign},type:"POST",cache:false,
		success: function(complete){
			window.location.reload();
		}
	});
}
function check_password(action){
	var password = $("#password_"+action).val();
	$.ajax({
		url:"controller/orderController.php?check_password&password="+password,
		type:"GET", cache:false, 
		success: function(data){
			if(data == "0"){
				$(".message").html("รหัสลับไม่ถูกต้องกรุณาตรวจสอบ");
				$("#password").val("");
			}else{
				if(action =="discount"){
					update_discount(data);
				}else if(action == "price"){
					update_price();
				}
			}
		}
	});
}
$("#edit_order").click(function(e) {
    $("#doc_date").removeAttr("disabled");
	$("#customer_name").removeAttr("disabled");
	$("#auto_zone").removeAttr("disabled");
	$("#comment").removeAttr("disabled");
	$("#zone_id").removeAttr("disabled");
	$(this).css("display", "none");
	$("#update_order").css("display","");
});

$("#update_order").click(function(e) {
    var date = $("#doc_date").val();
	var id_order_consign = $("#id_order_consign").val();
	var cus_name = $("#customer_name").val();
	var comment = $("#comment").val();
	var cus_id = $("#id_customer").val();
	var id_zone = $("#zone_id").val();
	if(date ==""){
		alert("ยังไม่ได้ระบุวันที่");
	}else if(cus_name == ""){
		alert("ยังไม่ได้เลือกลูกค้า");
	}else if(cus_id ==""){
		alert("ระบบไม่พบ Customer ID ไม่สามารถเพิ่มออเดอร์ได้กรุณาเลือกลูกค้าใหม่หรือติดต่อผู้ดูแลระบบ");
	}else if(id_zone == 0){
			alert("ยังไม่ได้เลือกพื้นที่เก็บ");
	}else{
		$.ajax({
			url:"controller/consignController.php?update_order=y&id_order_consign="+id_order_consign+"&id_customer="+cus_id+"&id_zone="+id_zone+"&date_add="+date+"&comment="+comment,
			type:"GET", cache:false,
			success: function(rs){
				if(rs == 1){
					$("#doc_date").attr("disabled","disabled");
					$("#customer_name").attr("disabled","disabled");
					$("#auto_zone").attr("disabled","disabled");
					$("#comment").attr("disabled","disabled");
					$("#zone_id").attr("disabled","disabled");
					$("#update_order").css("display", "none");
					$("#edit_order").css("display","");
				}else{
					alert("ไม่สามารถแก้ไขออเดอร์ได้");
				}
			}
		});
	}
});

$("#search-text").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$("#search-text").bind("enterKey",function(){
	if($("#search-text").val() != ""){
		$("#search-btn").click();
	}
});

$("#search-btn").click(function(e) {
    var query_text = $("#search-text").val();
	var filter = $("#filter").val();
	if(query_text !=""){
		$("#form").submit();
	}else{
		alert("กรุณาใส่คำค้นหา");
	}
});

function back_to_consign()
{
	load_in();
	window.location.href="index.php?content=consign";	
}
function back_to_compare()
{
	load_in();
	window.location.href="index.php?content=consign&consign_check";	
}
/*
$("#search-btn").click(function(e) {
    var query_text = $("#search-text").val();
	var filter = $("#filter").val();
	if(query_text !=""){
	$("#load").html("<img src='../img/ajax-loader.gif' width='18' height='18' />");
	$.ajax({
		url:"controller/consignController.php?search-text="+query_text+"&filter="+filter , type: "GET", cache:false,
		success: function(result){
			$("#result").html(result);
			clearTimeout(x);
			$("#load").html("<span class='glyphicon glyphicon-search'></span>");
	}
	}); 
	}
});
*/
</script>