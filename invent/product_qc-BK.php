<?php 
	$page_menu = "invent_order_qc";
	$page_name = "ตรวจสินค้า";
	$id_tab = 18;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
	$ps = checkAccess($id_profile, 58);
	$suppervisor = $ps['add'] + $ps['edit'] + $ps['delete'] > 0 ? TRUE : FALSE;
  	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	function product_from_zone($id_order, $id_product_attribute){
		$sql = dbQuery("SELECT zone_name, SUM(qty) AS qty FROM tbl_temp JOIN tbl_zone ON tbl_temp.id_zone = tbl_zone.id_zone WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute GROUP BY tbl_temp.id_zone");
		$result = "";
			while($row = dbFetchArray($sql)){
				$zone = $row['zone_name'];
				$qty = $row['qty'];
				$result = $result." ".$zone." : ".$qty."<br/>";
			}
			return $result;
	}
	
 	 
	  if(!isset($_GET['id_order'])&&!isset($_GET['process'])){	
	  		$btn = can_do($add, "<a href='index.php?content=qc&process'><button type='button' class='btn btn-info'><i class='fa fa-hourglass-half'></i>&nbsp รายการกำลังตรวจ</button></a>");
	   }else if(isset($_GET['id_order'])&&isset($_GET['process'])){
		     $id_order = $_GET['id_order'];
			 list($sumqty_order) = dbFetchArray(dbQuery("SELECT SUM(product_qty) FROM tbl_order_detail WHERE id_order = $id_order"));
			 list($sumqty) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_temp WHERE id_order = $id_order"));
			 $btn = can_do($add, "<a href='index.php?content=qc' ><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp กลับ</button></a>");
		   if($sumqty_order != $sumqty){
			   $c = checkAccess($id_profile, 17);
			   $btn .= can_do($c['edit'], "<a href='index.php?content=prepare&process=y&id_order=$id_order'><button type='button' class='btn btn-info'><i class='fa fa-refresh'></i>&nbsp จัดสินค้าใหม่</button></a>");
		   }  
	   }else{
		   $btn = "<a href='index.php?content=qc' ><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp กลับ</button></a>";
	   }   
	   ?>
<div class="container"><audio id="sound1"><source src="../library/beep.mp3" type="audio/mpeg"></audio>
<!-- page place holder -->
<div class="row">
	<div class="col-lg-8"><h3 class="title"><i class="fa fa-check-square-o"></i>&nbsp;<?php echo $page_name; ?></h3>
  </div>
    <div class="col-lg-4">
       <p class="pull-right">
       	<?php echo $btn; ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<!-- ++++++++++++++++++++++++++++++++++++   จัดสินค้า  ++++++++++++++++++++++++++++-->
<?php 
if(isset($_GET['id_order'])) :
	$id_order = $_GET['id_order'];
	$id_user = $_COOKIE['user_id'];
	$order = new order($id_order);
	$customer = new customer($order->id_customer);
	if(isset($_GET['id_box']))
	{
		$id_box = $_GET['id_box'];
	}else{
		$id_box = "";
		echo "<input type='hidden' id='box_detect' value='0' />";	
	}
	if(isset($_GET['confirm_error'])&&isset($_GET['id_zone'])&&isset($_GET['id_product_attribute'])){
		$confirm_error = $_GET['confirm_error'];
		$id_zone = $_GET['id_zone'];
		$id_product_attribute = $_GET['id_product_attribute'];
		$zone = product_from_zone($id_order, $id_product_attribute);
		$arr = explode(":",$zone);
		$zone_name = $arr[0];
	}else{
		$confirm_error = "";
		$id_zone = "";
		$id_product_attribute ="";
		$zone_name = "";
	}
	$idc = dbNumRows(dbQuery("SELECT id_order_state_change FROM tbl_order_state_change WHERE id_order = $id_order AND id_order_state = 11 AND id_employee != $id_user"));
	$ids = dbNumRows(dbQuery("SELECT id_order_state_change FROM tbl_order_state_change WHERE id_order = $id_order AND id_order_state = 11 AND id_employee = $id_user"));
	?>

<?php if( $suppervisor === FALSE ) : ?> 
	<?php if($idc > 0 && $ids < 1) : ?>
    <script type="text/javascript">
		window.location = "index.php?content=qc&error=เออเดอร์นี้มีคนตรวจแล้ว";	
	</script>
	<?php endif; ?>
<?php endif; ?>
	<div class="row">
		<div class="col-xs-2">เลขที่ : <?php echo $order->reference; ?></div>
        <div class="col-xs-2">ลูกค้า : <?php echo $customer->full_name; ?></div>
        <div class="col-xs-2">วันที่สั่ง : <?php echo thaiDate($order->date_add); ?></div>	
		<div class="col-xs-2">จำนวนรายการ : <?php echo $order->total_product; ?> รายการ </div>
        <div class="col-xs-2">จำนวนตัว : <?php echo $order->total_qty; ?> ตัว </div>
        <div class="col-xs-2">มูลค่า : <?php echo number_format($order->total_amount,2); ?> </div>
		<div class="col-xs-12">&nbsp;</div>
		<div class="col-xs-12">หมายเหตุ : <?php echo $order->comment; ?></div>
        <div class="col-xs-12">&nbsp;</div>
        <div class="col-xs-12">  
        <p class="pull-right" id="print_row" >
<?php $qs = dbQuery("SELECT id_box FROM tbl_box WHERE id_order = ".$id_order); ?>        
<?php if(dbNumRows($qs) > 0 ) : ?>
<?php 	$i = 1; ?>
<?php 	while($ro = dbFetchArray($qs)) : ?>
<?php 	$qo = dbQuery("SELECT SUM(qty) AS qty FROM tbl_qc WHERE id_order = ".$id_order." AND id_box = ".$ro['id_box']." AND valid = 1"); ?>
<?php 	$rs = dbFetchArray($qo) ; ?>
<a href="controller/qcController.php?print_packing_list&id_order=<?php echo $id_order; ?>&id_box=<?php echo $ro['id_box']; ?>&number=<?php echo $i; ?>" target="_blank">
 <button type="button" id="print_<?php echo $ro['id_box']; ?>" class="btn btn-success" ><i class="fa fa-print"></i>&nbsp;กล่องที่<?php echo $i; ?> :  <span id="<?php echo $ro['id_box'];?>"><?php echo $rs['qty']; ?></span> pcs.</button>  
 </a>          
            <?php $i++; ?>   
<?php 	endwhile; ?>
<?php else : ?>
			ยังไม่มีการตรวจสินค้า
<?php endif; ?>    
			</p>     
            </div>
	</div>
	
	<hr style="border-color:#CCC; margin-top: 0px; margin-bottom:15px;" />
    <input type="hidden" id="id_user" name="id_user" value="<?php echo $id_user; ?>"/>
    <input type="hidden" id="id_order" name="id_order" value="<?php echo $id_order; ?>" />
    <input type="hidden" id="id_box" name="id_box" value="<?php echo $id_box; ?>" />
	<div class="row">
    <div class="col-xs-3">
    	<div class="input-group">
        	<span class="input-group-addon">บาร์โค้ดกล่อง</span>
            <input type="text" name="barcode_box" id="barcode_box" class="form-control" autocomplete="off" <?php if($id_box =="" ){ echo "autofocus"; }else{ echo "disabled"; } ?>  />
        </div>
    </div>
	<div class="col-xs-3">
    	<div class="input-group">
        	<span class="input-group-addon">บาร์โค้ดสินค้า</span>
            <input type="text" id="barcode_item" name="barcode_item" class="form-control" autocomplete="off" <?php if($id_box !="" ){ echo "autofocus"; }else{ echo "disabled"; } ?>  />
        </div> 
    </div>
	<div class="col-xs-2" id="load">
    	<button type="button" class="btn btn-default" id="add" onclick="qc_process()" >ตกลง</button>
    </div>
    <div class="col-xs-2 col-xs-offset-2">
    	<button type="button" class="btn btn-warning" id="change_box" onclick="change_box()" ><i class="fa fa-refresh"></i>&nbsp; เปลี่ยนกล่อง</button>
    </div>
	</div>
	<hr style="border-color:#CCC; margin-top: 15px; margin-bottom:15px;" />	
	
	
	<div id="value">
	<a href="controller/qcController.php?over_order&id_order=$id_order&id_product_attribute=$id_product_attribute&id_zone=$id_zone" >
	<button type="button" id="move_zone" style="display:none;" onclick=\"return confirm('สินค้าเกิน ต้องการย้ายสต็อกจากโซน $zone_name ไปยัง Buffer หรือไม่'); \">
	click</button></a>
	<table class='table' id='table1'>
	<thead id='head'>
	
		<th style='width:20%; text-align:center;'>บาร์โค้ด</th><th style='width:35%;'>สินค้า</th>
		<th style='width:10%; text-align:center;'>จำนวนที่สั่ง</th><th style='width:10%; text-align:center;'>จำนวนที่จัด</th>	<th style='width:10%; text-align:center;'>ตรวจแล้ว</th><th style='width:10%; text-align:center;'>จากโซน</th>
	</thead>
    <?php
	if($order->current_state == 5){ $order->state_change($order->id_order, 11, $id_user); }
	$sql = dbQuery("SELECT tbl_order_detail.id_product_attribute, product_qty FROM tbl_order_detail WHERE tbl_order_detail.id_order = $id_order ORDER BY date_upd DESC");
	$row = dbNumRows($sql);
	$n = 1;
	$row1 = 0;
	while($list = dbFetchArray($sql)){
			$id_product_attribute  = $list['id_product_attribute'];
			$product = new product();
			$product->product_attribute_detail($id_product_attribute);
			$product->product_detail($product->id_product);
			$barcode = $product->barcode;
			$product_code = $product->reference." : ".$product->product_name;
			$order_qty = $list['product_qty'];
			//$checked  = $list['qty'];
			list($prepare_qty) = dbFetchArray(dbQuery("SELECT  SUM(qty) AS qty FROM tbl_temp WHERE id_order = $id_order  AND id_product_attribute = $id_product_attribute"));
			list($checked) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_qc WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND valid =1"));
			$balance_qty = $prepare_qty - $checked;
			if($order_qty == "$checked"){
				
			}else{
				$row1++;
		echo"
			<tr id='row".$id_product_attribute."' ";if($order_qty > "$prepare_qty"){echo "style='color:#FF0000'";} echo ">
				<td align='center'>$barcode</td><td> $product_code </td><td align='center'> ".number_format($order_qty)." </td>
				<td align='center' ><p id='prepare".$id_product_attribute."'>".number_format($prepare_qty)."</p></td><td align='center' id='checked".$id_product_attribute."'> ".number_format($checked)." </td>
				<td align='center'>"; if($checked>$order_qty){ echo" <a href='#'  onclick='edit_qc(".$id_product_attribute.",".$id_order.")'><button type='button' class='btn btn-default'>แก้ไข</button></a><input type='hidden' name='must_edit' value='1' />
  <form action='controller/qcController.php?edit_qc' method='post'>
	<div class='modal fade' id='edit_qc' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
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
								</div></form>";}else{
									
				echo" <button type='button' id='$id_product_attribute' class='btn btn-default' data-container='body' data-toggle='popover' data-html='true' data-placement='right' data-content='".product_from_zone($id_order,$id_product_attribute)."'>จากโซน</button></td>
			</tr>
			<script>
			$('#$id_product_attribute').mouseenter(function(){
				$(this).popover('show');
			});
			$('#$id_product_attribute').mouseleave(function(){
				$(this).popover('hide');
			});
			</script>";
				}
			}
	}
//	if($row1 == "0"){
		echo"<tr id='finish' style='display:none;'><td colspan='6' align='center'><a href='controller/qcController.php?close_job&id_order=$id_order&id_employee=$id_user'><button type='button' id='btn_close_job' class='btn btn-success' onclick=\"return check_cancal() \">ตรวจเสร็จแล้ว</button></a></td></tr>";
//	}else{
	echo "<tr><td id='force_close' colspan='6' align='center'>
<input type='checkbox' id='checkboxes'  onclick='getcondition()' />
สินค้ามีไม่ครบ
<br />
<br />
<div id='continue_bt'></div>
";
echo "</td></tr>";
//	}
	echo"<button data-toggle='modal' data-target='#edit_qc' id='btn_toggle' style='display:none;'>toggle</button>
			</table>
			<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
			<div class='row'><div class='col-xs-12'><h4 style='text-align:center;'>รายการที่ครบแล้ว</h4></div></div>
			<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
		<table class='table'>
	<thead id='head2'>
		<th style='width:20%; text-align:center;'>บาร์โค้ด</th><th style='width:35%;'>สินค้า</th>
		<th style='width:10%; text-align:center;'>จำนวนที่สั่ง</th><th style='width:10%; text-align:center;'>จำนวนที่จัด</th>	<th style='width:10%; text-align:center;'>ตรวจแล้ว</th><th style='width:10%; text-align:center;'>จากโซน</th>
	</thead>";
	$sql = dbQuery("SELECT SUM(qty)AS qty,id_product_attribute FROM tbl_qc WHERE id_order = $id_order AND valid = 1 GROUP BY id_product_attribute ORDER BY id_product_attribute ASC ");
	$row = dbNumRows($sql);
	$n=0;
	if($row>0){
	while($list = dbFetchArray($sql)){
			$id_product_attribute  = $list['id_product_attribute'];
			list($order_qty) = dbFetchArray(dbQuery("SELECT product_qty FROM tbl_order_detail WHERE id_product_attribute = $id_product_attribute AND id_order = $id_order"));
			$product = new product();
			$product->product_attribute_detail($id_product_attribute);
			$product->product_detail($product->id_product);
			$barcode = $product->barcode;
			$product_code = $product->reference." : ".$product->product_name;
			$prepare_qty = $list['qty'];
			list($checked) = dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_qc WHERE id_order = $id_order AND id_product_attribute = $id_product_attribute AND valid =1"));
			$balance_qty = $prepare_qty - $checked;
			if($order_qty == "$checked"){
			$row1++;
	echo"
			<tr ";if($order_qty > "$prepare_qty"){echo "style='color:#FF0000'";} echo ">
				<td align='center'>$barcode</td>
				<td> $product_code </td><td align='center'> ".number_format($order_qty)." </td>
				<td align='center'>".number_format($prepare_qty)."</td>
				<td align='center'> ".number_format($checked)." </td>
				<td align='center'><button type='button' id='$id_product_attribute' class='btn btn-default' data-container='body' data-toggle='popover' data-html='true' data-placement='right' data-content='".product_from_zone($id_order,$id_product_attribute)."'>จากโซน</button></td>
			</tr>
			<script>
			$('#$id_product_attribute').mouseenter(function(){
				$(this).popover('show');
			});
			$('#$id_product_attribute').mouseleave(function(){
				$(this).popover('hide');
			});
			</script>";
			}else if(number_format($order_qty) == 0 ){
				$n++;
				echo"
			<tr ";if($order_qty > "$prepare_qty"){echo "style='color:#FF0000'";} echo ">
				<td align='center'>$barcode</td><td> $product_code </td><td align='center'> ".number_format($order_qty)." </td>
				<td align='center'>".number_format($prepare_qty)."</td><td align='center'> ".number_format($checked)." </td>
				<td align='center'><a href='controller/qcController.php?delete&id_product_attribute=$id_product_attribute&id_order=$id_order'><button type='button' class='btn btn-danger'>ยกเลิก</button></a></td>
			</tr>
			<script>
			$('#$id_product_attribute').mouseenter(function(){
				$(this).popover('show');
			});
			$('#$id_product_attribute').mouseleave(function(){
				$(this).popover('hide');
			});
			</script>";
			}
	}
	}
	echo"
			</table>	<div><input type='hidden' id='loop_cancal' value='$n' >
	";
?>		
<script id="template" type="text/x-handlebars-template">
 	{{#each this}}
	{{#if nocontent}}
		{{ nocontent }}
	{{else}}
		<a href="controller/qcController.php?print_packing_list&id_order=<?php echo $id_order; ?>&id_box={{id_box}}&number={{i}}" target="_blank">
 		<button type="button" id="print_{{id_box}}" class="btn {{class}}" ><i class="fa fa-print"></i>&nbsp;กล่องที่{{i}} :  <span id="{{id_box}}">{{qty}}</span> pcs.</button>  
 		</a> 
		
	{{/if}}
	{{/each}}	 
</script>	
<!-------------------------------------------  	รายการระหว่างจัด  ------------------------------------------------------>
<?php elseif(isset($_GET['process'])) :  /////  ?>
<div class="row">
	<div class="col-xs-12">
		<table class="table">
		<thead>
			<th style="width: 5%; text-align:center;">ลำดับ</th>
            <th style="width: 20%; text-align:center;">เลขที่เอกสาร</th>
			<th style="width: 15%; text-align:center;">ลูกค้า</th>
            <th style="width: 15%; text-align:center;">รูปแบบ</th>
            <th style="width: 15%; text-align:center;">วันที่สั่ง</th>
            <th style="width: 20%; text-align:center;">พนักงานจัด</th>
            <th style="width: 5%; text-align:center;">&nbsp;</th>
		</thead>
<?php         
		$sql = dbQuery("SELECT tbl_order.id_order, tbl_temp.id_employee FROM tbl_order LEFT JOIN tbl_temp ON tbl_order.id_order = tbl_temp.id_order WHERE current_state = 11  GROUP BY tbl_order.id_order");
		$n = 1;
		while($row = dbFetchArray($sql)) :
			$order = new order($row['id_order']);
			$customer = new customer($order->id_customer);
			$employee = new employee($row['id_employee']); ?>
			<tr>
				<td align="center"><?php echo $n; ?></td>
				<td align="center"><?php echo $order->reference; ?></td>
				<td align="center"><?php echo $customer->full_name; ?></td>
				<td align="center"><?php echo $order->role_name; ?></td>
				<td align="center"><?php echo thaiDate($order->date_add); ?></td>
				<td align="center"><?php echo $employee->full_name; ?></td>
				<td align="center"><a href="index.php?content=qc&process=y&id_order=<?php echo $order->id_order; ?>"><span class="btn btn-default">ตรวจสินค้าต่อ</span></a></td>
			</tr>
		<?php $n++; ?>
<?php endwhile; ?>
		</table>
	</div> <!-- col-xs-12 -->
</div> <!--  row -->

<!----------------------------------------------  จบรายการระหว่างจัด  ----------------------------------------------->
<?php  else : ?>
<!----------------------------------------------- แสดงรายการรอตรวจ ------------------------------------------------>

<div class="row">
	<div class="col-xs-12" id="reload">
		<table class="table">
		<thead>
			<th style="width: 5%; text-align:center;">ลำดับ</th>
            <th style="width: 20%; text-align:center;">เลขที่เอกสาร</th>
			<th style="width: 15%; text-align:center;">ลูกค้า</th>
            <th style="width: 15%; text-align:center;">รูปแบบ</th>
            <th style="width: 15%; text-align:center;">วันที่สั่ง</th>
			<th style="width: 15%; text-align:center;">พนักงานจัด</th>
            <th style="width: 15%; text-align:center;">&nbsp;</th>
		</thead>
<?php        
		$sql = dbQuery("SELECT id_order FROM tbl_order WHERE current_state = 5");
		$n = 1;
		while($row = dbFetchArray($sql)) :
			$order = new order($row['id_order']);
			$customer = new customer($order->id_customer);
			list($id_employee) = dbFetchArray(dbQuery("SELECT id_employee FROM tbl_temp WHERE id_order =".$order->id_order." AND status = 1 GROUP BY id_employee"));
			$employee = new employee($id_employee); ?>
			<tr>
				<td align="center"><?php echo $n; ?></td>
				<td align="center"><?php echo $order->reference; ?></td>
				<td align="center"><?php echo $customer->full_name; ?></td>
				<td align="center"><?php echo $order->role_name; ?></td>
				<td align="center"><?php echo thaiDate($order->date_add); ?></td>
				<td align="center"><?php echo $employee->full_name; ?></td>
				<td align="center"><a href="index.php?content=qc&process=y&id_order=<?php echo $order->id_order; ?>"><span class='btn btn-default'>ตรวจสินค้า</span></a></td>
			</tr>
		<?php $n++; ?>
<?php endwhile; ?>
		</table>
	</div> <!-- col-xs-12 -->
</div> <!--  row -->

<script>
setInterval(function() {
    $.get('controller/qcController.php?reload', function(data) {
      $("#reload").html(data);
    });
}, 60000);
</script>
<?php
endif;
?>

<div class="modal" id="qc_error" tabindex="-1" role="dialog" aria-labelledby="qcModal" data-backdrop="static">
  <div class="modal-dialog" style="width:300px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="gridSystemModalLabel">&nbsp;</h4>
      </div>
      <div class="modal-body">
        <center><h4 id="error_label">Error</h4></center>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>	

// ตรวจสอบหากยังไม่ได้ยิงบาร์โค้ด box ให้ป๊อบอัพเตือน
$(document).ready(function(e) {
    if($("#box_detect").length){
		swal("ยิงบาร์โค้ดกล่องก่อน");
	}
});

$(document).ready(function(e) {
    if($("#error").length){
		document.getElementById("sound1").play();
		swal($("#error").text());
	}
});
$(document).ready(function(e) {
    if($("#confirm_error").length){
		$("#move_zone").click();
	}
});
function edit_qc(id_product_attribute, id_order){
	$.ajax({
		url:"controller/qcController.php?getData&id_product_attribute="+id_product_attribute+"&id_order="+id_order,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				$("#modal").css("width","500px");
				$("#modal_title").html("แก้ไข QC เกิน");
				$("#modal_body").html(dataset);
				$("#btn_toggle").click();
			}else{
				alert("NO DATA");
			}		
		}
	});
}
function getcondition(){
	if(checkboxes.checked){
		$("#continue_bt").html("<a href='controller/qcController.php?close_job&id_order=<?php echo $id_order;?>&id_employee=<?php echo $id_user;?>'><button type='button' class='btn btn-success' onclick=\"return check_cancal() \">บังคับจบ</button></a>");
	}else{
		$("#continue_bt").html("");
	}
}
$("#barcode_item").bind("enterKey",function(){
	//alert("123");
	qc_process();
});
$("#barcode_item").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});		

$("#barcode_box").bind("enterKey", function(){
	get_box();
});;

$("#barcode_box").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});	

function change_box(){
	$("#barcode_item").attr("disabled", "disabled");
	$("#id_box").val("");
	$("#barcode_box").removeAttr("disabled");
	$("#barcode_box").val("");
	swal("ยิงบาร์โค้ดกล่องก่อน");
	$("#barcode_box").focus();
}

function get_box(){	
    var code = $("#barcode_box").val();
	if(code ==""){
		swal("ยังไม่ได้ระบุบาร์โค้ดกล่อง");
		$("#barcode_box").focus();
	}else{
		var id_order = $("#id_order").val();
		$.ajax({
			url: "controller/qcController.php?add_box",
			type:"GET", cache:false, data:{"id_order" : id_order, "barcode_box" : code},
			success: function(rs){  ///// ได้ id_box กลับมา ในรูปแบบ   success : id_box  หากกล่องนั้นถูกส่งไปแล้ว ( valid = 1)  closed : id_box  หากไม่สำเร็จ  fail : xx
				var arr = rs.split(" : ");
				var status = $.trim(arr[0]);
				var id_box = $.trim(arr[1]);
				if(status == "success"){
					$("#id_box").val(id_box);
					$("#barcode_box").attr("disabled", "disabled");
					$("#barcode_item").removeAttr("disabled");
					$("#barcode_item").focus();
					update_box(id_box);
				}else if(status == "closed"){
					swal("กล่องนี้ถูกบันทึกว่าจัดส่งไปแล้ว");
				}else{
					swal("เพิ่มกล่องเข้าระบบไม่สำเร็จ");
				}
			}
		});
	}
}

function qc_process(){
	var barcode_item = $("#barcode_item").val();
	var id_box = $("#id_box").val();
	var id_order = $("#id_order").val();
	var id_user = $("#id_user").val();
	if(barcode_item != "")
	{
		$("#barcode_item").attr("disabled", "disabled");
		$("#barcode_item").val('');
		//$("#add").focus();
		$("#add").html("<i class='fa fa-spinner fa-spin'></i>");
		$.ajax({
		url:"controller/qcController.php?checked=y",
		data: {"id_order" : id_order, "id_user" : id_user, "barcode_item" : barcode_item, "id_box" : id_box},
		type:"GET", cache:false, 
		success: function(dataset){
			dataset = $.trim(dataset);
			arr = dataset.split(":");
			if(arr[0].trim()=="ok"){
				$("#add").html("ตกลง");
				id_product_attribute = arr[1];
				qc_qty = parseInt(arr[2]);
				pre_qty = parseInt($("#prepare"+id_product_attribute).html());
				if(qc_qty == pre_qty){
				$("#checked"+id_product_attribute).html(qc_qty);
				$("#row"+id_product_attribute).insertAfter($("#head2"));
				}else{
				$("#checked"+id_product_attribute).html(qc_qty);
				$("#row"+id_product_attribute).insertAfter($("#head"));
				}
				var is = parseInt($("#"+id_box).html());
				$("#"+id_box).html(is+1);
				$("#barcode_item").removeAttr("disabled");
				$("#barcode_item").focus();
			}else{
				$("#add").html("ตกลง");
				error = arr[1];
				beep();
				$("#error_label").text(error);
				$("#qc_error").modal("show");
				//swal({ title : "Error !!", text : error, showConfirmButton: true, showCancelButton : false, closeOnConfirm: false }, function(isConfirm){ swal.close(); $("#barcode_item").removeAttr("disabled"); $("#barcode_item").focus(); });
				
			}
			count = $("#table1 td").size();
			if(count<3){
				$("#force_close").css("display","none");
				$("#finish").css("display", "");
			}
			$("#barcode_item").focus();
		}
	});
	}else{
		swal("คุณยังไม่ได้ใส่บาร์โค้ด");
		$("#barcode_item").focus();
	}
	
}

count = $("#table1 td").size();
	if(count<3){
		$("#force_close").css("display","none");
		$("#finish").css("display", "");
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

$("#qc_error").on("hidden.bs.modal", function(e){
	$("#barcode_item").removeAttr("disabled"); 
	$("#barcode_item").focus();
});

$(document).ready(function(e) {
    if($("input[name='must_edit']").length){ 
		$("#checkboxes").css("display","none");
		$("#btn_close_job").css("display", "none");
		$("#force_close").html("<span style='color:red;'>รายการตรวจสินค้าที่ไม่ถูกต้อง จำเป็นต้องแก้ไขก่อนจึงจะทำงานลำดับต่อไปได้</span>");
	}
});
function update_box(id_box)
{
	var id_order = $("#id_order").val();
	$.ajax({
		url: "controller/qcController.php?update_box&id_order="+id_order+"&id_box="+id_box,
		type:"GET", cache:false,
		success: function(data){
			var source = $("#template").html();
			var data = $.parseJSON(data);
			var output = $("#print_row");
			render(source, data, output);
		}
	});
}

</script>
<script src="../library/js/beep.js"></script>
