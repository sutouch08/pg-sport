<?php 
	
	$page_menu = "invent_order_prepare";
	$page_name = "จัดสินค้า";
	$id_tab = 17;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	$ps	= checkAccess($id_profile, 57); /// จัดสินค้าแทนคนอื่นได้หรือป่าว
	$supervisor = $ps['add'] + $ps['edit'] + $ps['delete'] > 0 ? TRUE : FALSE;
	accessDeny($view);
  	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	
	if( !isset($_GET['id_order']) && !isset($_GET['process']) && !isset($_GET['view_handle']) )
	{
		$btn = "<a href='index.php?content=prepare' ><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		$btn .= can_do($edit, "&nbsp;<a href='index.php?content=prepare&process' ><button type='button' class='btn btn-info' ><i class='fa fa-spinner fa-spin'></i>&nbsp; กำลังจัด</button></a>");
		$btn .= can_do($delete, "&nbsp;<a href='index.php?content=prepare&view_handle'><button type='button' class='btn btn-primary'><i class='fa fa-eye'></i>&nbsp; ดูการจัด</button></a>");
	}else{
		$btn = "<a href='index.php?content=prepare' ><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
	}
	
	
	?>
    
<div class="container"><audio id="sound1"><source src="../library/beep.mp3" type="audio/mpeg"></audio>
<!-- page place holder -->
<div class="row">
	<div class="col-xs-6"><h3 class="title"><span class="glyphicon glyphicon-inbox"></span>&nbsp;<?php echo $page_name; ?></h3>
  </div>
    <div class="col-xs-6">
    	<p class="pull-right">
       
<?php  echo $btn ?>	   
      </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php 

$checkstock = new checkstock();
if($checkstock->check_open() == true){
	echo "<br><div align='center'><h1><span class='glyphicon glyphicon-info-sign'></span>&nbsp;&nbsp;ขณะนี้มีการตรวจนับสินค้าอยู่ไม่สามารถจัดสินค้าได้</h1></div>";
}else{
if(isset($_GET['id_order'])){
	$id_order = $_GET['id_order'];
	$id_user = $_COOKIE['user_id'];
	$order = new order($id_order);
	if($order->id_customer == ""){
				$full_name = "";
			}else{
				$customer = new customer($order->id_customer);
				$full_name = $customer->full_name;
			}
	if(isset($_GET['barcode_zone'])){ $barcode_zone = $_GET['barcode_zone']; }else{ $barcode_zone = "";}
	if(isset($_GET['id_zone'])){ $id_zone = $_GET['id_zone']; }else{ $id_zone = "";}
	if($id_zone !=""){ 
		$active = "disabled=disabled"; 
		$actived = "";
		$autofocus = "autofocus='autofocus'";
		$autofo = "";
		}else{ 
		$active = "";
		$actived = "disabled=disabled"; 
		$autofo = "autofocus='autofocus'";
		$autofocus = "";
		}
	if( $supervisor === FALSE ){
		$id_prepare = dbNumRows(dbQuery("SELECT id_prepare FROM tbl_prepare WHERE id_order = $id_order AND id_employee != $id_user"));
		if($id_prepare > 0){ ?>
		<script type="text/javascript">
			window.location = "index.php?content=prepare&error=เออเดอร์นี้มีคนจัดอยู่แล้ว";
		</script>
		<?php 
		exit;
		}
		$id_pr = dbNumRows(dbQuery("SELECT id_prepare FROM tbl_prepare WHERE id_order = $id_order AND id_employee = $id_user"));
	}
	else
	{
		$id_pr = 0;	
	}
	
echo"
	<div class='row'>
		<div class='col-xs-2'>เลขที่ : ".$order->reference."</div><div class='col-xs-2'>ลูกค้า : ".$full_name."</div><div class='col-xs-2'>วันที่สั่ง : ".thaiDate($order->date_add)."</div>	
		<div class='col-xs-2'>จำนวนรายการ : ".$order->total_product." รายการ </div><div class='col-xs-2'>จำนวนตัว : ".$order->total_qty." ตัว </div><div class='col-xs-2'>มูลค่า : ".number_format($order->total_amount,2)." </div>
		<div class='col-xs-12'>&nbsp;</div>
		<div class='col-xs-12'>หมายเหตุ : ".$order->comment."</div>
	</div>
	
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	<form id='prepare_order_form' action='controller/prepareController.php?prepared=y&id_order=".$order->id_order."' method='post'>
	<div class='row'><input type='hidden' name='id_zone' id='id_zone' value='$id_zone' /><input type='hidden' name='id_user' value='$id_user' />
	<div class='col-xs-3'><div class='input-group'><span class='input-group-addon'>บาร์โค้ดโซน</span><input type='text' id='barcode_zone' name='barcode_zone' class='form-control' $autofo autocomplete='off' $active value='$barcode_zone' /></div> </div>
	<div class='col-xs-2'><div class='input-group'><span class='input-group-addon'>จำนวน</span><input type='text' id='qty' name='qty' class='form-control' value='1' autocomplete='off' $actived' /></div> </div> 
	<div class='col-xs-3'><div class='input-group'><span class='input-group-addon'>บาร์โค้ดสินค้า</span><input type='text' id='barcode_item' name='barcode_item' class='form-control'  $autofocus autocomplete='off' $actived  /></div> </div>
	<div class='col-xs-2'><button type='button' class='btn btn-default' id='add' onclick='add_to_cart()' $actived >ตกลง</button></div>
	<div class='col-xs-2'><button type='button' class='btn btn-default' id='change_zone' onclick='reset_zone($id_order)' $actived >เปลี่ยนโซน(F2)</button></div>
	</div></form>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	
	<table class='table'>
	<thead>
		<th style='width:5%; text-align:center;'></th><th style='width:20%; text-align:center;'>บาร์โค้ด</th><th style='width:30%;'>สินค้า</th>
		<th style='width:10%; text-align:center;'>จำนวน</th><th style='width:10%; text-align:center;'>จัดแล้ว</th>
		<th style='width:10%; text-align:center;'>คงเหลือ</th><th style='width:15%; text-align:center;'>สถานที่</th>
	</thead>";
	
	if($order->current_state == 3){ $order->state_change($order->id_order, 4, $id_user);  if($id_pr < 1){dbQuery("INSERT INTO tbl_prepare( id_order, id_employee,start) VALUES($id_order, $id_user, NOW())");} }
	$sql = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = $id_order AND valid_detail = 0 ORDER BY barcode ASC ");
	$row = dbNumRows($sql);
	if($row>0){
	$n = 1;
	while($list = dbFetchArray($sql)){
			$id_product_attribute  = $list['id_product_attribute'];
			$barcode = $list['barcode'];
			$product_code = $list['product_reference']." : ".$list['product_name'];
			$order_qty = $list['product_qty'];
			$qr = dbQuery("SELECT SUM(qty) FROM tbl_temp WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_product_attribute);
			list($rd) = dbFetchArray($qr);
			if( $rd != NULL ){ $prepared = $rd; }else{ $prepared = 0; }
			$balance_qty = $order_qty - $prepared;
			$product = new product();
	echo"
			<tr>
				<td align='center'><img src='".$product->get_product_attribute_image($id_product_attribute,1)."' /></td><td align='center'>$barcode</td><td> $product_code </td><td align='center'> $order_qty </td>
				<td align='center'>".$prepared." </td><td align='center'> $balance_qty </td>
				<td align='center'> ".$product->stock_in_zone($id_product_attribute)."</td>
			</tr>
			<script>
			$('#$id_product_attribute').mouseenter(function(){
				$(this).popover('show');
			});
			$('#$id_product_attribute').mouseleave(function(){
				$(this).popover('hide');
			});
			</script>";
			$n++;
	}		
	
	echo "<tr><td colspan='7' align='center' id='force_close'>
<input type='checkbox' id='checkboxes'  onclick='getcondition()' />
สินค้ามีไม่ครบ
<br />
<br />
<div id='continue_bt'></div>
";
?>
<script type='text/javascript'>
function getcondition(){
	if(checkboxes.checked){
		$("#continue_bt").html("<a href='controller/prepareController.php?close_job&id_order=<?php echo $id_order;?>&id_employee=<?php echo $id_user;?>'><button type='button' class='btn btn-success' onclick=\"return check_cancal() \">บังคับจบ</button></a>");
	}else{
		$("#continue_bt").html("");
	}
}
</script>
<?php
echo "</td></tr>";
	}else{
		echo"<tr><td colspan='7' align='center'><a href='controller/prepareController.php?close_job&id_order=$id_order&id_employee=$id_user'><button type='button' id='btn_close_job' class='btn btn-success' onclick=\"return check_cancal() \">จัดเสร็จแล้ว</button></a></td></tr>";
	}
	echo"
			</table>
			<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
			<div class='row'><div class='col-xs-12'><h4 style='text-align:center;'>รายการที่ครบแล้ว</h4></div></div>
			<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
		<table class='table'>
	<thead>
		<th style='width:5%; text-align:center;'></th><th style='width:15%; text-align:center;'>บาร์โค้ด</th><th style='width:25%;'>สินค้า</th>
		<th style='width:10%; text-align:center;'>จำนวน</th><th style='width:10%; text-align:center;'>จัดแล้ว</th>
		<th style='width:10%; text-align:center;'>คงเหลือ</th><th style='width:15%; text-align:center;'>สถานที่</th><th style='width:10%; text-align:center;'>การกระทำ</th>
	</thead>";
	$sql = dbQuery("SELECT SUM(qty)AS qty,id_product_attribute FROM tbl_temp WHERE id_order = $id_order GROUP BY id_product_attribute");
	$n = 1;
	$c = 0;
	while($list = dbFetchArray($sql)){
		$id_product_attribute = $list['id_product_attribute'];
		list($barcode,$product_reference,$product_name,$order_qty,$valid_detail) = dbFetchArray(dbQuery("SELECT barcode,product_reference,product_name,product_qty,valid_detail FROM tbl_order_detail WHERE id_order = $id_order AND  id_product_attribute = '$id_product_attribute' ORDER BY barcode ASC"));
			$product_code = "$product_reference : $product_name";
			$prepared = $list['qty'];
			$balance_qty = $order_qty - $prepared;
			if($valid_detail == ""){$valid_detail = 1;}
			if($valid_detail != 0){
			$product = new product();
			if($barcode == ""){
				list($product_reference,$barcode,$product_name) = dbFetchArray(dbQuery("SELECT reference,barcode,product_name FROM tbl_product_attribute LEFT JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product WHERE id_product_attribute = '$id_product_attribute'"));
				$product_code = "$product_reference : $product_name";
			}
	echo"<tr>
				<td align='center'><img src='".$product->get_product_attribute_image($id_product_attribute,1)."' /></td><td align='center'> $barcode </td><td> $product_code </td><td align='center'>".number_format($order_qty)."</td>
				<td align='center'>"; if($prepared<1){ echo "0"; }else{ echo $prepared; } echo"  </td><td align='center'> $balance_qty </td>
				<td align='center'><button type='button' id='$id_product_attribute' class='btn btn-default' data-container='body' data-toggle='popover' data-html='true' data-placement='right' data-content='".$product->stock_in_zone($id_product_attribute)."'>
  แสดงที่เก็บ</button></td><td>"; if($balance_qty<0){ if($order_qty == ""){ $c++;echo"<a href='controller/prepareController.php?delete&id_product_attribute=$id_product_attribute&id_order=$id_order'><button type='button' class='btn btn-danger'>ยกเลิก</button></a>";}else{ echo "<a href='#'  onclick='edit_temp(".$id_product_attribute.",".$id_order.")'><button type='button' class='btn btn-default'>แก้ไข</button></a><input type='hidden' name='must_edit' value='1' />";}
  echo "<form action='controller/prepareController.php?edit_temp' method='post'>
	<div class='modal fade' id='edit_temp' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' id='modal'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='modal_title'></h4><input type='hidden' name='id_order' value='$id_order'/>
									  </div>
									  <div class='modal-body' id='modal_body'></div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
										<button type='submit' class='btn btn-primary'>แก้ไขรายการ</button>
									  </div>
									</div>
								  </div>
								</div></form>";} echo"
								
								</td>
			</tr>";
			}
			$n++;
	}
	echo"
			</table>	<button data-toggle='modal' data-target='#edit_temp' id='btn_toggle' style='display:none;'>toggle</button><input type='hidden' id='loop_cancal' value='$c' >
	";
	
	
}else if(isset($_GET['process'])){ ///// รายการระหว่างจัด
	echo"
<div class='row'>
	<div class='col-xs-12'>
		<table class='table'>
		<thead>
			<th style='width: 5%; text-align:center;'>ลำดับ</th><th style='width: 20%; text-align:center;'>เลขที่เอกสาร</th>
			<th style='width: 15%; text-align:center;'>ลูกค้า</th><th style='width: 15%; text-align:center;'>รูปแบบ</th><th style='width: 15%; text-align:center;'>วันที่สั่ง</th><th style='width: 20%; text-align:center;'>พนักงานจัด</th><th style='width: 5%; text-align:center;'>&nbsp;</th>
		</thead>";
		$sql = dbQuery("SELECT tbl_prepare.id_order, tbl_prepare.id_employee FROM tbl_prepare LEFT JOIN tbl_order ON tbl_prepare.id_order = tbl_order.id_order WHERE  current_state = 4 ");
		$n = 1;
		while($row = dbFetchArray($sql)){
			$order = new order($row['id_order']);
			if($order->id_customer == ""){
				$full_name = "";
			}else{
				$customer = new customer($order->id_customer);
				$full_name = $customer->full_name;
			}
			$employee = new employee($row['id_employee']);
			echo"
			<tr>
					<td align='center'>$n</td>
					<td align='center'>".$order->reference."</td>
					<td align='center'>".$full_name."</td>
					<td align='center'>".$order->role_name."</td>
					<td align='center'>".thaiDate($order->date_add)."</td>
					<td align='center'>".$employee->full_name."</td>
					<td align='center'><a href='index.php?content=prepare&process=y&id_order=".$order->id_order."'><span class='btn btn-default'>จัดสินค้าต่อ</span></a></td>
			</tr>";
			$n++;
		}
echo"		</table>
	</div> <!-- col-xs-12 -->
</div> <!--  row -->
";	
}else if(isset($_GET['view_handle'])){
	?>
 <table class="table table-striped">
 <thead>
 <th style="width:10%; text-align: center;">ลำดับ</th>
 <th style="width: 25%;">ออเดอร์</th>
 <th style="width:25%;">พนักงาน</th>
 <th style="width:15%; text-align:center">เริ่ม</th>
 <th style="text-align:right">ดึงกลับ</th>
 </thead>
 <?php  $n = 1; ?>
<?php  $qs = dbQuery("SELECT id_prepare, tbl_prepare.id_order, tbl_prepare.id_employee, start, reference, id_customer FROM tbl_prepare JOIN tbl_order ON tbl_prepare.id_order = tbl_order.id_order WHERE current_state = 4"); ?>
<?php if(dbNumRows($qs) > 0) : ?>
<?php 	while($rs = dbFetchArray($qs)) : ?>
	<tr>
    	<td align="center"><?php echo $n; ?></td>
        <td><?php echo $rs['reference']; ?></td>
        <td><?php echo employee_name($rs['id_employee']); ?></td>
        <td align="center"><?php echo date("d-m-Y H:i:s", strtotime($rs['start'])); ?></td>
        <td align="right"><a href="controller/prepareController.php?bring_it_back&id_prepare=<?php echo $rs['id_prepare']; ?>"><button type="button" class="btn btn-warning"><i class="fa fa-refresh"></i>&nbsp; ดึงกลับ</button></a></td>
    </tr>
<?php $n++; ?>    
<?php	endwhile; ?>
	
<?php else: ?>
 <tr><td colspan="5"><center><h4>---------- ไม่มีรายการระหว่างจัด  ----------</h4></center></td></tr>
 
<?php endif;  ?>
</table>

<?php
}else{
/// ***************************  แสดงรายการรอจัดสินค้า ****************************////
echo"
<div class='row'>
	<div class='col-xs-12' id='reload'>
		<table class='table' >
		<thead>
			<th style='width: 5%; text-align:center;'>ลำดับ</th><th style='width: 20%; text-align:center;'>เลขที่เอกสาร</th>
			<th style='width: 15%; text-align:center;'>ลูกค้า</th><th style='width: 15%; text-align:center;'>รูปแบบ</th><th style='width: 15%; text-align:center;'>วันที่สั่ง</th><th style='width: 5%; text-align:center;'>&nbsp;</th>
		</thead>";
		$sql = dbQuery("SELECT id_order FROM tbl_order WHERE current_state = 3 AND order_status = 1 ");
		$n = 1;
		while($row = dbFetchArray($sql)){
			$order = new order($row['id_order']);
			if($order->id_customer == ""){
				$full_name = "";
			}else{
				$customer = new customer($order->id_customer);
				$full_name = $customer->full_name;
			}
			echo"
			<tr>
					<td align='center'>$n</td>
					<td align='center'>".$order->reference."</td>
					<td align='center'>".$full_name."</td>
					<td align='center'>".$order->role_name."</td>
					<td align='center'>".thaiDate($order->date_add)."</td>
					<td align='center'><a href='index.php?content=prepare&process=y&id_order=".$order->id_order."'><span class='btn btn-default'>จัดสินค้า</span></a></td>
			</tr>";
			$n++;
		}
echo"		</table>
	</div> <!-- col-xs-12 -->
</div> <!--  row -->
";
?>
<script>
setInterval(function() {
    $.get('controller/prepareController.php?reload', function(data) {
      $("#reload").html(data);
    });
}, 60000);
</script>
<?php
}

}

	
?>
<script>
$("#barcode_zone").bind("enterKey",function(){
	var barcode = $(this).val();
	if(barcode !=""){
	$.ajax({
		url:"controller/zoneController.php?check&barcode_zone="+barcode
		,type:"GET",cache:false,success: function(data){
			if(data !="0"){
				$("#id_zone").val(data);
				$("#barcode_item").removeAttr("disabled");
				$("#qty").removeAttr("disabled");
				$("#add").removeAttr("disabled");
				$("#change_zone").removeAttr("disabled");
				$("#barcode_item").focus();
				$("#barcode_zone").attr("disabled","disabled");
			}else{
				alert("บาร์โค้ดโซนไม่ถูกต้อง กรุณาตรวจสอบ");
				$(this).focus();
			}
		}
	});
	}
});
$("#barcode_zone").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$("#barcode_item").bind("enterKey",function(){
	qty = $("#qty").val();
	items = $(this).val();
	if(items ==""){
		if(qty ==""){
			$("#qty").focus();
		}else{
		alert("บาร์โค้ดสินค้าไม่ถูกต้อง");
		}
	}else 	if(qty ==""){
		alert("จำนวนสินค้าไม่ถูกต้อง");
		$("#qty").focus();
	}else if(qty > 9999){
		alert("จำนวนสินค้าผิดปกติ");
		$("#qty").focus();
	}else{
		$("#add").click();
	}
});
$("#barcode_item").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});		

$("#qty").bind("enterKey",function(){
	qty = $(this).val();
	if(qty ==""){
		alert("ยังไม่ได้ใส่จำนวน");
		$("#qty").focus();
	}else if(qty > 9999){
		alert("จำนวนสินค้าผิดปกติ");
		$("#qty").focus();
	}else{
		$("#barcode_item").focus();
	}
});
$("#qty").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});		
///// กด shift เพื่อสลับระหว่างช่องจำนวนกับบาร์โค้ดสินค้า
$("#barcode_item").bind("spaceKey",function(){
	$("#qty").focus();
});
$("#barcode_item").keyup(function(e){
    if(e.keyCode == 17)
    {
        $(this).trigger("spaceKey");
    }
});		
$("#qty").bind("spaceKey",function(){
	$("#barcode_item").focus();
});
$("#qty").keyup(function(e){
    if(e.keyCode == 17)
    {
        $(this).trigger("spaceKey");
    }
});		
//// เปลี่ยนโซน///
$(document).bind("F2",function(){
	$("#change_zone").click();
});
$(document).keyup(function(e){
	if(e.keyCode == 113)
	{
		$(this).trigger("F2");
	}
});
/// เพิ่มสินค้าเข้าไปในตะกร้าจัดสินค้าและตัดออกจากโซน	
function add_to_cart(){
	qty = $("#qty").val();
	items = $("#barcode_item").val();
	id_zone = $("#id_zone").val();
	if(id_zone ==""){
		alert("ไม่พบค่า id_zone กรุณาติดต่อผู้ดูแลระบบ");
	}else if(items ==""){
		alert("บาร์โค้ดสินค้าไม่ถูกต้อง");
	}else 	if(qty ==""){
		alert("จำนวนสินค้าไม่ถูกต้อง");
		$("#qty").focus();
	}else if(qty > 9999){
		alert("จำนวนสินค้าผิดปกติ");
		$("#qty").focus();
	}else{	
	$("#prepare_order_form").submit();
	}
}
function reset_zone(id_order){
	window.location.href="index.php?content=prepare&process=y&id_order="+id_order;
}
$(document).ready(function(e) {
    if($("#error").length){
		document.getElementById("sound1").play();
		
	}
});

function edit_temp(id_product_attribute, id_order){
	$.ajax({
		url:"controller/prepareController.php?getData&id_product_attribute="+id_product_attribute+"&id_order="+id_order,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				$("#modal").css("width","600px");
				$("#modal_title").html("แก้ไขการจัดเกิน");
				$("#modal_body").html(dataset);
				$("#btn_toggle").click();
			}else{
				alert("NO DATA");
			}		
		}
	});
}
function check_qty(id){
	var order_qty = $("#order_qty"+id).val();
	var edit = $("#edit"+id).val();
	if(parseInt(order_qty) < parseInt(edit)){
		alert("ใส่จำนวนที่ออกเกิน");
		$("#edit"+id).val(order_qty);
		$("#edit"+id).focus();
	}
}
function check_cancal(){
	var loop_cancal = $("#loop_cancal").val();
	if(loop_cancal > 0){
		alert("มีรายการสินค้าที่ยกเลิกกรุณายกเลิกรายรายสินค้าก่อน");
		return false;
	}else{
		return true;
	}
}
$(document).ready(function(e) {
    if($("input[name='must_edit']").length){ 
		$("#checkboxes").css("display","none");
		$("#btn_close_job").css("display", "none");
		$("#force_close").html("<span style='color:red;'>รายการจัดสินค้าที่ไม่ถูกต้อง จำเป็นต้องแก้ไขก่อนจึงจะทำงานลำดับต่อไปได้</span>");
	}
});
</script>