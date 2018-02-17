<div class="container">
<div class="row">
	<div class="col-sm-6"><h3 style="margin-top:0px; margin-bottom:10px;">จัดออเดอร์ย้อนหลัง</h3></div>
    <div class="col-sm-6">
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<div class="row">
	<div class="col-sm-2"><label>ID ORDER</label><input type="text" class="form-control input-sm" id="orderReference" name="orderReference" /></div>
    <div class="col-sm-2"><label>ID ZONE</label><input type="text" class="form-control input-sm" id="id_zone" name="id_zone" /></div>
    <div class="col-sm-6">
    	<label style="display:block; visibility:hidden;">btn</label>
        <button type="button" class="btn btn-sm btn-warning" id="btn_check" onclick="checkOrder()">ตรวจสอบ</button>
        <button type="button" class="btn btn-sm btn-primary" id="btn-prepare" onclick="startPrepare()">จัดสินค้า</button>
        <button type="button" class="btn btn-sm btn-info" id="btn-qc" onclick="startQc()">ตรวจสินค้า</button>
        <button type="button" class="btn btn-sm btn-success" id="btn-bill" onclick="startBill()">เปิดบิล</button>
	</div> 
    <input type="hidden" name="id_order" id="id_order" />
</div>
<hr/>
<div class="row">
	<div class="col-lg-12 text-center margin-top-15" id="result"></div>

</div><!--/row -->
</div><!--/ container -->
<script>
$("#orderReference").autocomplete({
   minLength: 1,
	source: "controller/autoComplete.php?getOrderReference",
	autoFocus: true,
	close: function(event,ui){
		var data = $(this).val();
		var arr 	= data.split(" | ");
		$(this).val(arr[0]);
		$("#id_order").val(arr[1]);
		$("#id_zone").focus();
	}
});	


function startPrepare()
{
	var id_order = $("#id_order").val();
	var id_zone = $("#id_zone").val();
	if( id_order == '' || isNaN(id_order) || id_zone == '' || isNaN(id_zone) ){
		swal('ข้อมูลไม่ถูกต้อง');
		return false;	
	}
	var html		= $("#result").html() + '<br/>เริ่มจัดสินค้า...';
	$("#result").html(html);
	$.ajax({
		url:'controller/orderToComplateController.php?prepareOrder',
		type:"GET", cache:"false", data: {'id_order' : id_order, 'id_zone' : id_zone},
		success: function(rs){
			var rs = $.trim(rs);
			var arr = rs.split(' | ');
			if( arr[0] == 'success' )
			{
				html = html + '<br/>จัดเสร็จแล้ว : ' + arr[2] + '/' + arr[1];
			}else if(arr[0] == 'fail'){
				html = html + '<br/>จัดไม่สำเร็จ';
			}else if( arr[0] == 'tempExists'){
				html = html + '<br/><span style="background-color:red; color:white;">ออเดอร์ถูกจัดไปแล้ว</span>';
			}
			$("#result").html(html);
		}
	});
}

function startQc()
{
	var id_order = $("#id_order").val();
	var id_zone = $("#id_zone").val();
	if( id_order == '' || isNaN(id_order) || id_zone == '' || isNaN(id_zone) ){
		swal('ข้อมูลไม่ถูกต้อง');
		return false;	
	}
	var html		= $("#result").html() + '<br/>เริ่มตรวจสินค้า...';
	$("#result").html(html);
	$.ajax({
		url:'controller/orderToComplateController.php?qcOrder',
		type:"GET", cache:"false", data: {'id_order' : id_order, 'id_zone' : id_zone },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' )
			{
				html = html + '<br/>ตรวจเสร็จแล้ว';
			}else{
				html = html + '<br/><span style="background-color:red; color:white;">' + rs + '</span>';
			}
			$("#result").html(html);
		}
	});
}

function startBill()
{
	var id_order = $("#id_order").val();
	var id_zone = $("#id_zone").val();
	if( id_order == '' || isNaN(id_order) || id_zone == '' || isNaN(id_zone) ){
		swal('ข้อมูลไม่ถูกต้อง');
		return false;	
	}
	var html		= $("#result").html() + '<br/>เริ่มบันทึกขาย...';	
	$("#result").html(html);
	$.ajax({
		url:'controller/orderToComplateController.php?billOrder',
		type:"GET", cache:"false", data: {'id_order' : id_order, 'id_zone' : id_zone },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' )
			{
				html = html + '<br/>บันทึกขายเสร็จแล้ว';
			}else{
				html = html + '<br/><span style="background-color:red; color:white;">' + rs + '</span>';
			}
			$("#result").html(html);
		}
	});
	
}


function checkOrder()
{
	var id_order = $("#id_order").val();
	var id_zone = $("#id_zone").val();
	if( id_order == '' || isNaN(id_order) || id_zone == '' || isNaN(id_zone) ){
		swal('ข้อมูลไม่ถูกต้อง');
		return false;	
	}
	$("#result").html('Start checking...');
	$.ajax({
		url:"controller/orderToComplateController.php?checkOrder",
		type:"GET", cache:"false", data: {'id_order' : id_order, 'id_zone' : id_zone },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' ){
				$("#result").html('OK Let\'s go');			
			}else{
				var html = '<span style="background-color:red; color:white;">' + rs + '</span>';
				$("#result").html(html);	
			}
		}
	});
}
</script>
