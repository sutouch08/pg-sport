function unsaveAdjust()
{
	var id		= $("#id_adjust").val();
	$.ajax({
		url:"controller/productAdjustController.php?unsaveAdjust",
		type:"POST", cache:"false", data:{ "id_adjust" : id },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({title: 'เรียบร้อย', text: 'ยกเลิกการปรับยอดเรียบร้อยแล้ว', type: 'success', timer: 1000 });
				setTimeout(function(){ window.location.reload(); }, 1200);
			}else{
				swal({title: "ข้อผิดพลาด", text: rs, type: "error" }, function(){ window.location.reload(); });
			}
		}
	});
}

function editHeader()
{
	$("#adj_ref").removeAttr('disabled');
	$("#date").removeAttr('disabled');
	$("#remark").removeAttr('disabled');
	$("#btn-edit-header").addClass('hide');
	$("#btn-update-header").removeClass('hide');
}

function headerUpdated()
{
	$("#adj_ref").attr('disabled','disabled');
	$("#date").attr('disabled','disabled');
	$("#remark").attr('disabled','disabled');
	$("#btn-update-header").addClass('hide');
	$("#btn-edit-header").removeClass('hide');
}

function updateHeader()
{
	var id_adj = $("#id_adjust").val();
	var ref = $("#adj_ref").val();
	var date = $("#date").val();
	var remark = $("#remark").val();
	if( ! isDate(date) ){
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}
	load_in();
	$.ajax({
		url:"controller/productAdjustController.php?updateHeader",
		type:"POST", cache:"false", data:{ "id_adjust" : id_adj, "adjust_reference" : ref, "date" : date, "remark" : remark },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title: 'เรียบร้อย', type: 'success', timer: 1000 });
				headerUpdated();	
			}else{
				swal("ข้อผิดพลาด", "ปรับปรุงข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");	
			}
		}
	});
}

function getDiff()
{
	var id = $("#id_adjust").val();
	window.location.href = "index.php?content=diff&id_adjust="+id;
}

function viewDiff()
{
	window.location.href = "index.php?content=diff";
}

$(document).ready(function(e) {
    $("#increase").numberOnly();
	$("#decrease").numberOnly();
});
$("#paCode").autocomplete({
	source: "controller/autoComplete.php?get_product_attribute",
	minLength: 1,
	autoFocus: true,
	close: function(event, ui){
		var rs = $(this).val();
		if( rs != 'ไม่พบข้อมูล'){
			var ar = rs.split(' | ');
			var ref = ar[0];
			var id_pa = ar[1];
			if( ! isNaN( id_pa ) ){
				$("#id_pa").val(id_pa);
				$("#paCode").val(ref);
				$("#paCode").removeClass('has-error');	
			}else if( rs != '' ){
				$("#id_pa").val('');
				$("#paCode").addClass('has-error');	
			}
		}
	}
});

$("#paCode").keyup(function(e) {
    if( e.keyCode == 13 ){
		setTimeout(function(){
			var id_pa = $("#id_pa").val();
			if( id_pa != '' ){
				$("#increase").focus();
			}
		},100);
	}
});

$("#increase").keyup(function(e){
	if(e.keyCode == 13 ){
		if($(this).val() === ''){
			$(this).val(0);
		}
		$("#decrease").focus();
	}
});

$("#decrease").keyup(function(e){
	if( e.keyCode == 13 ){
		if($(this).val() === '' ){
			$(this).val(0);
		}
		if( $("#id_adjust_detail").val() !== ""){
			updateDetail();
		}else{
			insertDetail();	
		}
	}
});

function insertDetail()
{
	var id_adj 	= $("#id_adjust").val();
	var id_emp	= $("#id_user").val();
	var id_zone = $("#id_zone").val();
	var id_pa	= $("#id_pa").val();
	var incr		= $("#increase").val();
	var decr		= $("#decrease").val();
	if( id_zone == '' ){
		swal('โซนสินค้าไม่ถูกต้อง');
		return false;
	}
	if( id_pa == '' ){
		swal('รหัสสินค้าไม่ถูกต้อง');
		return false;
	}
	if( (incr == '0' && decr == '0' ) || ( incr == decr ) ){
		swal('จำนวนไม่ถูกต้อง');
		return false;
	}
	
	load_in();
	$.ajax({
		url:"controller/productAdjustController.php?insertDetail",
		type:"POST", cache:"false", data:{ "id_adjust" : id_adj, "id_emp" : id_emp, "id_zone" : id_zone, "id_pa" : id_pa, "increase" : incr, "decrease" : decr },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if(rs == 'success')
			{
				updateAdjustTable();
				clearInputField();
			}else{
				swal(rs);	
			}
		}
	});
	
}



function save()
{
	load_in();
	var id_adj = $("#id_adjust").val();
	$.ajax({
		url:"controller/productAdjustController.php",
		type:"POST", cache:"false", data:{ "saveAdjust" : "Y", "id_adjust" : id_adj },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title: "สำเร็จ", text: "บันทึกการปรับยอดเรียบร้อยแล้ว", type: "success", timer: 1000 });		
			}else{
				swal("ข้อผิดพลาด", rs, "error");
			}
			updateAdjustTable();
		}
	});
}


function confirmDelete(id, product)
{
	swal({
        title: 'ต้องการลบ ?',
        text: 'คุณแน่ใจว่าต้องการลบ ' + product + ' ?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6855',
        confirmButtonText: 'ใช่ ฉันต้องการลบ',
        cancelButtonText: 'ยกเลิก',
        closeOnConfirm: false
    }, function() {
        deleteAdjustDetail(id);
    });
}

function deleteAdjustDetail(id)
{
	var id_adj = $("#id_adjust").val();
	load_in();
	$.ajax({
            url: "controller/productAdjustController.php",
            type:"POST",
            cache: "false",
            data: { "deleteDetail" : "Y", "id_adjust" : id_adj, "id_adjust_detail": id },
            success: function(rs) {
				load_out();
                var rs = $.trim(rs);
                if (rs == 'success') {
                    swal({ title: "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });
                    updateAdjustTable();
                } else {
                    swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
                }
            }
        });	
}

function getDelete(id, reference)
{
	swal({
        title: 'ต้องการลบ ?',
        text: 'คุณแน่ใจว่าต้องการลบ ' + reference + ' ?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6855',
        confirmButtonText: 'ใช่ ฉันต้องการลบ',
        cancelButtonText: 'ยกเลิก',
        closeOnConfirm: false
    }, function() {
        deleteAdjust(id);
    });
}

function deleteAdjust(id)
{
	load_in();
	$.ajax({
            url: "controller/productAdjustController.php?deleteAdjust",
            type:"POST",
            cache: "false",
            data: {"id_adjust" : id },
            success: function(rs) {
				load_out();
                var rs = $.trim(rs);
                if (rs == 'success') {
                    swal({ title: "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });
                    setTimeout(function(){ window.location.reload(); }, 1200);
                } else {
                    swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
                }
            }
        });	
}

function editAdjustDetail(id)
{
	var id_zone = $("#id_zone_"+id).val();
	var id_pa 	= $("#id_pa_"+id).val();
	var zone		= $.trim($("#zone_"+id).text());
	var product = $.trim($("#product_"+id).text());
	var add		= $.trim($("#add_"+id).text());
	var minus	= $.trim($("#minus_"+id).text());
	
	$("#id_adjust_detail").val(id);
	$("#id_zone").val(id_zone);
	$("#id_pa").val(id_pa);
	$("#zoneName").val(zone);
	$("#paCode").val(product);
	$("#increase").val(add);
	$("#decrease").val(minus);
	
	$("#zoneName").attr("disabled", "disabled");
	$("#btn-setZone").addClass('hide');
	$("#btn-changeZone").removeClass('hide');
	$("#paCode").removeAttr('disabled');
	$("#increase").removeAttr('disabled');
	$("#decrease").removeAttr('disabled');
	$("#btn-insert").addClass('hide');
	$("#btn-update").removeClass('hide');
	
	$("#increase").focus();
	
}



function updateDetail()
{
	var id_adj	= $("#id_adjust_detail").val();	
	var id_pa 	= $("#id_pa").val();
	var id_zone = $("#id_zone").val();
	
	var incr		= $("#increase").val();
	var decr		= $("#decrease").val();
	if( id_zone == '' ){
		swal('โซนสินค้าไม่ถูกต้อง');
		return false;
	}
	if( id_pa == '' ){
		swal('รหัสสินค้าไม่ถูกต้อง');
		return false;
	}
	if( (incr == '0' && decr == '0' ) || ( incr == decr ) ){
		swal('จำนวนไม่ถูกต้อง');
		return false;
	}
	
	load_in();
	$.ajax({
		url:"controller/productAdjustController.php?updateDetail",
		type:"POST", cache:"false", data:{ "id_adjust_detail" : id_adj, "id_zone" : id_zone, "id_pa" : id_pa, "increase" : incr, "decrease" : decr },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if(rs == 'success')
			{
				updateAdjustTable();
				changeZone();
			}
			else
			{
				swal(rs);	
			}
		}
	});
}



function updateAdjustTable()
{
	var id_adj = $("#id_adjust").val();
	$.ajax({
		url: "controller/productAdjustController.php",
		type:"GET", cache:"false", data:{ "getAdjustTable" : "Y", "id_adjust" : id_adj },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'fail' ){
				swal({ title: 'ข้อผิดพลาด', text: 'ไม่สามารถเข้าถึงข้อมูลได้กรุณาลองใหม่อีกครั้ง', type: 'warning' });
			}else if( rs == 'nodata' || rs == '' ){
				$("#result").html('');	
			}else{
				var source = $("#adjTableTemplate").html();
				var data 	= $.parseJSON(rs);
				var output = $("#result");
				render(source, data, output);
			}
		}
	});
}

function clearInputField()
{
	$("#paCode").val('');
	$("#increase").val('');
	$("#decrease").val('');
	$("#id_pa").val('');
	$("#paCode").focus();
}


$("#zoneName").autocomplete({
	source: "controller/autoComplete.php?getZone",
	minLength: 1,
	autoFocus: true,
	close: function(event, ui){
		var rs = $(this).val();
		if( rs != 'ไม่พบข้อมูล' )
		{
			var ar = rs.split(' | ');
			var zone_name = ar[0];
			var id_zone = ar[1];
			if( ! isNaN(id_zone) ){
				$("#id_zone").val(id_zone);
				$("#zoneName").val(zone_name);
				$("#zoneName").removeClass('has-error');
			}else if( rs != ''){
				$("#id_zone").val('');
				$("#zoneName").addClass('has-error');
				swal({ title: 'โซนไม่ถูกต้อง', type: 'warning'}, function(){ $("#zoneName").focus(); });	
			}
		}
	}
});

$("#zoneName").keyup(function(e) {
    if( e.keyCode == 13 ){
		setTimeout(function(){ 
			setZone();
		}, 100);
	}
});

function setZone()
{
	var id_zone = $("#id_zone").val();
	if( id_zone != '' ){
		$("#zoneName").attr('disabled', 'disabled');
		$(".adj").removeAttr('disabled');
		$("#btn-setZone").addClass('hide');
		$("#btn-changeZone").removeClass('hide');
		$("#paCode").focus();
	}	
}

function changeZone()
{
	$(".adj").val('');
	$(".adj").attr('disabled', 'disabled');
	$("#id_zone").val('');
	$("#id_pa").val('');
	$("#id_adjust_detail").val('');
	$("#zoneName").val('');
	$("#zoneName").removeAttr('disabled');
	$("#btn-changeZone").addClass('hide');
	$("#btn-setZone").removeClass('hide');
	$("#btn-update").addClass('hide');
	$("#btn-insert").removeClass('hide');
	$("#zoneName").focus();
}

function addNewAdjust()
{
	var adj_ref	= $("#adj_ref").val();
	var id_emp	= $("#id_user").val();
	var date 		= $("#date").val();
	var remark	= $("#remark").val();
	if( ! isDate(date) ){ 
		swal('วันที่ไม่ถูกต้อง');
		$("#date").focus();
		return false;
	}
	load_in();
	$.ajax({
		url:"controller/productAdjustController.php?addNewAdjust",
		type:"POST", cache:"false", data:{ "adj_ref" : adj_ref, "date" : date, "remark" : remark, "id_employee" : id_emp },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			var rs = parseInt(rs);
			if( ! isNaN(rs) ){
				window.location.href = "index.php?content=ProductAdjust&add&id_adjust="+rs;
			}else{
				swal('บันทึกรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');	
			}
		}
	});
}


$(".sf").keyup(function(e) {
    if(e.keyCode == 13 ){
		getSearch();
	}
});

function toggleStatus(id)
{
	var id = parseInt(id);
	var vt = parseInt($("#adj_vt").val());
	if( id === 0 ){
		if( vt === id ){
			$("#btn-unsave").removeClass('btn-info');
			$("#adj_vt").val('');
		}else{
			$("#btn-saved").removeClass('btn-info');
			$("#btn-unsave").addClass('btn-info');
			$("#adj_vt").val(0);
		}
	}else if( id === 1 ){
		if( vt === id ){
			$("#btn-saved").removeClass('btn-info');
			$("#adj_vt").val('');
		}else{
			$("#btn-unsave").removeClass('btn-info');
			$("#btn-saved").addClass('btn-info');
			$("#adj_vt").val(1);
		}
	}
	getSearch();
}

function getSearch()
{
	var from = $("#from").val();
	var to 	= $("#to").val();
	if( from != "" || to != "" ){
		if( ! isDate(from) || ! isDate(to) ){ 
			swal('วันที่ไม่ถูกต้อง');
			return false;
		}
	}
	$("#searchForm").submit();
}

$("#from").datepicker({ 
	dateFormat: 'dd-mm-yy', 
	onClose: function(sd){ 
		$("#to").datepicker('option', 'minDate', sd);
		if( $(this).val() != '' ){ 
			$("#to").focus();
		}
	}
});

$("#to").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(sd){
		$("#from").datepicker('option', 'maxDate', sd);
	}
});

$("#date").datepicker({	dateFormat: 'dd-mm-yy' });

function clearFilter()
{
	$.ajax({
		url:"controller/productAdjustController.php?clearFilter",
		type:"POST", cache:"false", success: function(rs){
			goBack();
		}
	});
}

function newAdjust()
{
	window.location.href = "index.php?content=ProductAdjust&add";	
}

function editAdjust(id)
{
	window.location.href = "index.php?content=ProductAdjust&add&id_adjust="+id;	
}

function goBack()
{
	window.location.href = "index.php?content=ProductAdjust";	
}

function newAdjust()
{
	window.location.href = "index.php?content=ProductAdjust&add";	
}