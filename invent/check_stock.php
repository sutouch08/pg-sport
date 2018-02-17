<?php 
	$page_menu = "invent_sale";
	$page_name = "ตรวจนับสินค้า";
	$id_tab = 30;
	$id_profile = $_COOKIE['profile_id'];
   $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
	?>

<div class="container"><audio id="sound1"><source src="../library/beep.mp3" type="audio/mpeg"></audio>
<!-- page place holder -->
<div class="row">
	<div class="col-lg-12 col-sm-12"><h3 class="title"><i class="fa fa-tags"></i>&nbsp;<?php echo $page_name; ?></h3>	</div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php	$checkstock = new checkstock(); ?>
<?php if( $checkstock->check_open() ) :
	$id_check = $checkstock->get_id_check();
	$id_user = $_COOKIE['user_id'];
	if(isset($_GET['barcode_zone'])){ $barcode_zone = $_GET['barcode_zone']; }else{ $barcode_zone = "";}
	if(isset($_GET['id_zone'])){ $id_zone = $_GET['id_zone']; }else{ $id_zone = "";}
	if($id_zone !=""){ 
			$active = "disabled"; 
			$actived = "";
			$focus_zone = "";
			$focus_item = "autofocus";
		}else{ 
			$active = "";
			$actived = "disabled"; 
			$focus_zone = "autofocus";
			$focus_item = "";
		}
		if(isset($_GET['id_zone'])){
			list($barcode_zone) = dbFetchArray(dbQuery("SELECT barcode_zone FROM tbl_zone WHERE id_zone = '".$_GET['id_zone']."'"));
		}
?>
<div class="row">
	<div class="col-lg-2 col-lg-offset-2">
    	<label>บาร์โค้ดโซน</label>
        <input type="text" name="barcode_zone" id="barcode_zone" class="form-control input-sm" value="<?php echo $barcode_zone; ?>" autocomplete="off" <?php echo $focus_zone; ?> style="text-align:center" <?php echo $active; ?> />
    </div>
    <div class="col-lg-1">
    	<label>จำนวน</label>
        <input type="text" id="qty" name="qty" class="form-control input-sm" value="1" autocomplete="off" style="text-align:center;" <?php echo $actived; ?>  />
    </div>
    <div class="col-lg-2">
    	<label>บาร์โค้ดสินค้า</label>
        <input type="text" id="barcode_item" name="barcode_item" class="form-control input-sm" style="text-align:center" autocomplete="off" <?php echo $actived; ?> <?php echo $focus_item; ?> />
    </div>
    <div class="col-lg-1">
    	<label style="visibility:hidden;">ดำเนินการ</label>
        <button type="button" id="add" class="btn btn-default btn-sm btn-block" onclick="check_process()" <?php echo $actived; ?>>ตกลง</button>
    </div>
    <div class="col-lg-2">
    	<label style="visibility:hidden;">change_zone</label>
    	<button type="button" id="btn_save" onclick="save_check()" class="btn btn-success btn-sm btn-block"><i class="fa fa-save"></i>&nbsp; บันทึก</button>
    	<input type='hidden' name='id_zone' id='id_zone' value='<?php echo $id_zone; ?>' />
        <input type='hidden' id='id_employee' name='id_employee' value='<?php echo $id_user; ?>' />
        <input type="hidden" name="id_check" id="id_check" value="<?php echo $id_check; ?>" />
    </div>
    <div class="col-lg-2">
    	<label style="visibility:hidden;">change_zone</label>
        <button type='button' class='btn btn-warning btn-sm btn-block' id='change_zone' onclick='reset_zone()' >เปลี่ยนโซน(F2)</button>
    </div>
</div>    
	<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<?php	if( isset($_GET['id_zone']) ) :	?>
<?php		$zone_name = get_zone($id_zone); 	?>
<div class="row">
	<div class="col-lg-4 col-lg-offset-4"><center><h4>---  <?php echo $zone_name; ?>  ---</h4></center></div>
</div>

<div class="row">
	<div class="col-lg-6">
    <form id="check_form">
    	<table class="table table-striped">
        	<thead>
            	<th style="width:10%; text-align:center">ลำดับ</th>
                <th style="width:50%;">บาร์โค้ด</th>
                <th style="width:20%; text-align:center">จำนวน</th>
                <th style="width:20%; text-align:center">เวลา</th>
            </thead>
            <tbody id="rs">
            <tr><td colspan="4"><center><h4 class="title">---- ยิงบาร์โค้ด  ----</h4></center></td></tr>
            
            </tbody>
        </table>
        </form>
    </div>
    <div class="col-lg-2"></div>

	<div class="col-lg-12">
	<table class='table table-bordered table-striped'>
	<thead>
		<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:75%;'>รหัสสินค้า</th>
		<th style='width:10%; text-align:center;'>จำนวน</th><th style='width:10%; text-align:center;'>การกระทำ</th>
	</thead>
    <tbody id="result">
<?php	$qr = dbQuery("SELECT id_stock_check,id_product_attribute, id_zone, qty_after FROM tbl_stock_check WHERE id_zone = ".$id_zone." AND id_check = ".$id_check." AND qty_after != 0"); ?>
<?php	$row = dbNumRows($qr); 	?>
<?php	$i = 0;	?>
<?php	$n = 1;	?>
<?php	if($row == 0) : ?>
			<tr><td align='center' colspan='4'><h4>-----  ยังไม่มีการตรวจสินค้าในโซนนี้  -----</h4></td></tr>
<?php 	endif; ?>		
<?php	while($rs = dbFetchArray($qr) ) : ?>
<?php		$id 	= $rs['id_stock_check']; ?>
	<tr>
    	<td align='center'><?php echo $n; ?></td>
        <td><?php echo get_product_reference($rs['id_product_attribute']); ?></td>
        <td align='center'><span id="qty_<?php echo $id; ?>"><?php echo number_format($rs['qty_after']); ?></span><input type='text' id='edit_qty_<?php echo $id; ?>' style='display:none;' /></td>
        <td align='center'>
			<button type='button' id='edit_<?php echo $id; ?>' class='btn btn-warning btn-xs' onclick='edit_product(<?php echo $id; ?>)' ><i class="fa fa-pencil"></i></button>
			<button type='button' id='update_<?php echo $id; ?>' onclick='update(<?php echo $id; ?>)' class='btn btn-default btn-xs' style='display:none;' >Update</button>
            <button type="button" class="btn btn-danger btn-xs" onclick="delete_item(<?php echo $id; ?>, <?php echo $id_zone; ?>)"><i class="fa fa-trash"></i></button>
		</td>
	</tr>       
<?php		$n++; ?>
<?php	endwhile; 	?>	
	</tbody>	
	</table>
    </div>
</div>
<?php else : 	?>
<div class="row"><div class="col-lg-12"><center><strong>----- ยิงบาร์โค้ดโซน  -----</strong></center></div></div>
<?php endif; ?>	
<?php 	else :	 ?>
<div class="col-lg-12"><center><strong>----------  ไม่มีการเปิดการตรวจนับ  ----------</strong></center></div>
<?php endif; ?>
<script> var count = 1; </script>
<script id="pre" type="text/x-handlebars-template">
	<tr>
		<td align="center">{{ count }}</td>
		<td>{{ barcode }} <input type="hidden" name="barcode[{{count}}]" value="{{barcode}}" /></td>
		<td align="center">{{ qty }} <input type="hidden" name="qty[{{count}}]" value="{{qty}}" /></td>
		<td align="center">{{ time }}</td>
	</tr>
</script>

<script id="row" type="text/x-handlebars-template">
<tr>
	<td align="center">{{ count }}</td>
    <td>{{ product }}</td>
    <td align="center">{{ qty }}</td>
    <td align="center">{{ time }}</td>
</tr>
</script>
<script>

$("#barcode_zone").keyup(function(e){
    if(e.keyCode == 13)
    {
        var barcode = $(this).val();
		if(barcode !=""){
		window.location.href = "controller/checkstockController.php?check_zone&barcode_zone="+barcode;
		}
    }
});


$("#qty").keyup(function(e){
    if(e.keyCode == 13)
    {
       var qty = $(this).val();
	   if(qty ==""){
			swal("ยังไม่ได้ใส่จำนวน");
			$("#qty").focus();
		}else if(qty > 9999){
			swal("จำนวนสินค้าผิดปกติ กรุณาตรวจสอบ");
			beep();
			$("#qty").focus();
		}else{
			$("#barcode_item").focus();
		}
    }
});		

///// กด Ctrl เพื่อสลับระหว่างช่องจำนวนกับบาร์โค้ดสินค้า
$("#barcode_item").keyup(function(e){
    if(e.keyCode == 17)
    {
        $("#qty").focus();
    }
});		

$("#qty").keyup(function(e){
    if(e.keyCode == 17)
    {
        $("#barcode_item").focus();
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
function reset_zone(id_order){
	window.location.href="index.php?content=checkstock";
}
$(document).ready(function(e) {
    if($("#error").length){
		beep();
	}
});
function edit_product(id_stock_check){
	var qty = parseInt($("#qty"+id_stock_check).text());
	$("#edit"+id_stock_check).css("display","none");
	$("#delete"+id_stock_check).css("display","none");
	$("#update"+id_stock_check).css("display","");
	$("#edit_qty"+id_stock_check).val(qty);
	$("#qty"+id_stock_check).css("display","none");
	$("#edit_qty"+id_stock_check).css("display","");	
}
function update(id_stock_check){
	var qty = $("#edit_qty"+id_stock_check).val();
		$("#new_qty").val(qty);
		$("#id_stock_check").val(id_stock_check);
		$("#edit_order_form").submit();
}

$("#barcode_item").keyup(function(e){
    if(e.keyCode == 13)
    {
       $("#add").click();
    }
});	

var arx = {};
function check_process()
{
	var barcode = $("#barcode_item").val();
	var qty = $("#qty").val();
	var date = new Date();
	var time = date.toLocaleTimeString();
	$("#barcode_item").val('');
	var data = {"count" : count, "barcode" : barcode, "qty" : qty, "time" : time };
	var source = $("#pre").html();
	var row = Handlebars.compile(source);
	var html = row(data);
	$("#rs").prepend(html);
	$("#qty").val(1);
	count++;	
	$("#barcode_item").focus();
}
function save_check()
{
	load_in();
	var id_zone = $("#id_zone").val();
	var id_employee = $("#id_employee").val();
	var id_check = $("#id_check").val();
	var data = $("#check_form").serialize();
	$.ajax({
		url: "controller/checkstockController.php?add&id_zone="+id_zone+"&id_employee="+id_employee+"&id_check="+id_check,
		type:"POST", cache:false, data: data,
		success: function(rs)
		{
			location.reload();
			load_out();
		}
	});
}
</script>
<script src="../library/js/beep.js"></script>