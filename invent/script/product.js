// JavaScript Document
// ใช้งานในไฟล์ product.php

//--------------------------  New Code  ---------------------//
function getEdit(id_pa)
{
	$.ajax({
		url:"controller/productController.php?getItemDetail",
		type:"POST", cache:"false", data:{ "id_pa" : id_pa },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != 'fail' && rs != ''){
				var source = $("#editItemTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#itemEditModalBody");
				render(source, data, output);
				$("#itemEditModal").modal('show');
				$(".input-number").numberOnly();
			}
		}
	});
}

function getDelete(id_pa, ref){
	swal({
		title: 'คุณแน่ใจ ?',
		text: 'ต้องการลบ '+ref+' ใช่หรือไม่ โปรดทราบว่าเมื่อลบแล้วจะไม่สามารถย้อนคืนได้',
		showCancelButton: true,
		confirmButtonColor: '#DD8655',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
	}, function(){
		load_in();
		$.ajax({
			url:"controller/productController.php?deleteItem",
			type:"POST", cache:"false", data:{ "id_product_attribute" : id_pa },
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( rs == 'success' ){
					$("#row-"+id_pa).remove();
					swal({ title: 'สำเร็จ', text: 'ลบ '+ref+' เรียบร้อยแล้ว', type: 'success', timer: 1000 });
				}else{
					swal('ข้อผิดพลาด', rs, 'error');
				}
			}
		});
	});
}

function saveEdit()
{
	var id_pd	= $("#id_product").val();
	var id_pa	= $("#id_pa").val();
	var reference = $("#editReference").val();
	if( reference == '' ){
		$("#editReference").addClass('has-error');
		$("#editReference").focus();
		return false;
	}else{
		$("#editReference").removeClass('has-error');
		$("#itemEditModal").modal('hide');
	}
	load_in();
	$.ajax({
		url:"controller/productController.php?updateItem&id_pd="+id_pd,
		type:"POST", cache:false, data: $("#editForm").serialize(),
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success')
			{
				swal({ title: 'เรียบร้อย', text: 'ปรับปรุงข้อมูลเรียบร้อยแล้ว', timer: 1000, type: 'success' });
				setTimeout(function(){ window.location.reload(); }, 1100);
			}else{
				swal({ title: 'ข้อผิดพลาด !!', text: rs, type: 'error' }, function(){ $("#itemEditModal").modal('show'); });
			}
		}
	});
}


function editBarcode()
{
	$(".bc-label").addClass('hide');
	$(".barcode").removeClass("hide");
}

$(".barcode").keyup(function(e){
	if( e.keyCode == 13 ){
		var box = $(this);
		var id = box.attr('id');
		var label = $("#"+id+"-label");
		var no	= $("#"+id+"-no").val();
		var barcode = $.trim(box.val());
		var id_pa = $("#"+id+"-id").val();
		if( barcode == '' ){ return false; }
		$.ajax({
			url:"controller/productController.php?validBarcode",
			type:"POST", cache:"false", data:{ "id_pa" : id_pa, "barcode" : barcode },
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'ok' ){
					no++;
					updateBarcode(id_pa, barcode, box, label, no)
				}else if( rs == 'duplicated' ){
					swal('ข้อผิดพลาด!!', 'บาร์โค้ดซ้ำ กรุณาใช้บาร์โค้ดอื่น', 'error');
				}else{

				}
			}
		});
	}
});

function updateBarcode(id_pa, barcode, box, label, no)
{
	$.ajax({
		url:"controller/productController.php?updateBarcode",
		type:"POST", cache:"false", data:{ "id_pa" : id_pa, "barcode" : barcode },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' ){
				label.text(barcode);
				box.addClass('hide');
				label.removeClass('hide');
				$(".no-"+no).focus();
			}else{
				swal('ข้อผิดพลาด!!', 'บันทึกบาร์โค้ดไม่สำเร็จ กรุณาลองใหม่อีกครั้ง', 'error');
			}
		}
	});
}

function editBarcodePack()
{
	$(".bp-label").addClass('hide');
	$(".barcode-pack").removeClass('hide');
}

$(".barcode-pack").keyup(function(e){
	if( e.keyCode == 13 ){
		var box 		= $(this);
		var id 		= box.attr('id');
		var label 		= $("#"+id+"-label");
		var no		= $("#"+id+"-no").val();
		var barcode = box.val();
		var id_pa = $("#"+id+"-id").val();
		if( barcode == '' ){ return false; }
		$.ajax({
			url:"controller/productController.php?validBarcodePack",
			type:"POST", cache:"false", data:{ "id_pa" : id_pa, "barcode" : barcode },
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'ok' ){
					no++;
					updateBarcodePack(id_pa, barcode, box, label, no)
				}else if( rs == 'duplicated' ){
					swal('ข้อผิดพลาด!!', 'บาร์โค้ดซ้ำ กรุณาใช้บาร์โค้ดอื่น', 'error');
				}else{

				}
			}
		});
	}
});

function updateBarcodePack(id_pa, barcode, box, label, no)
{
	$.ajax({
		url:"controller/productController.php?updateBarcodePack",
		type:"POST", cache:"false", data:{ "id_pa" : id_pa, "barcode" : barcode },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' ){
				label.text(barcode);
				box.addClass('hide');
				label.removeClass('hide');
				$(".bp-no-"+no).focus();
			}else{
				swal('ข้อผิดพลาด!!', 'บันทึกบาร์โค้ดไม่สำเร็จ กรุณาลองใหม่อีกครั้ง', 'error');
			}
		}
	});
}

function editPackQty()
{
	$(".pqty-label").addClass('hide');
	$(".pack-qty").removeClass('hide');
}

$(".pack-qty").keyup(function(e){
	if( e.keyCode == 13 ){
		var box 		= $(this);
		var id 		= box.attr('id');
		var label 		= $("#"+id+"-label");
		var no		= $("#"+id+"-no").val();
		var qty 		= box.val();
		var id_pa 	= $("#"+id+"-id").val();
		if( qty == '' || qty == '0' ){ return false; }
		$.ajax({
			url:"controller/productController.php?updatePackQty",
			type:"POST", cache:"false", data:{ "id_pa" : id_pa, "qty" : qty },
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'success' ){
					no++;
					label.text(qty);
					box.addClass('hide');
					label.removeClass('hide');
					$(".pqty-no-"+no).focus();
				}else if( rs == 'fail' ){
					swal('ข้อผิดพลาด!!', 'แก้ไขจำนวนในแพ็คไม่สำเร็จ กรุณาลองใหม่อีกครั้ง', 'error');
				}else if( rs == 'nopack'){
					swal('ข้อผิดพลาด!!', 'ยังไม่ได้กำหนดบาร์โค้ดแพ็ค กรุณากำหนดบาร์โค้ดแพ็คก่อนการแก้ไขจำนวน', 'error');
				}
			}
		});
	}
});

function setImage(id_pd)
{
	var id_pd	= $("#id_product").val();
	load_in();
	$.ajax({
		url:"controller/productController.php?getImageAttributeGrid",
		type:"POST", cache:"false", data:{ "id_pd" : id_pd },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'noimage' ){
				swal('ไม่พบรูปภาพ หรือ รายการสินค้า');
			}else{
				$("#mappingBody").html(rs);
				$("#imageMappingTable").modal('show');
			}
		}
	});
}


//-----  Mapping Image with id_pa
function doMapping()
{
	var id_pd = $("#id_product").val();
	$("#imageMappingTable").modal('hide');
	load_in();
	$.ajax({
		url:"controller/productController.php?doMappingImageWithProductAttribute",
		type:"POST", cache:"false", data: $("#mappingForm").serialize(),
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title: 'เรียบร้อย', text: 'จับคู่รูปภาพเรียบร้อยแล้ว', timer: 1000, type: 'success' });
				setTimeout(function(){ window.location.href = "index.php?content=product&edit&id_product="+id_pd+"&tab=2"; }, 1100);
			}else{
				swal('ข้อผิดพลาด !!', 'ทำรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง', 'error');
			}
		}
	});
}


function addProduct()
{
	var pCode	= $.trim($("#pCode").val());
	var isDup	= $("#isDuplicated").val();
	var pName 	= $("#pName").val();
	var cost		= $("#cost").val();
	var price		= parseFloat($("#price").val());
	var dType	= $("#dType").val();
	var dis		= parseFloat($("#discount").val());

	if( pCode == '' ){
		$("#pCode-error").text('จำเป็นต้องกำหนดช่องนี้');
		showError('pCode');
		$("#pCode").focus();
		return false;
	}

	if( isDup == 1 ){
		$("#pCode-error").text('รหัสสินค้าซ้ำ');
		showError('pCode');
		$("#pCode").focus();
		return false;
	}

	if( pName == '' ){
		showError('pName');
		$("#pName").focus();
		return false;
	}
	if( ( dType == 'percentage' && dis > 100 ) || ( dType == 'amount' && dis > price ) ){
		validDiscount();
		$("#discount").focus();
		return false;
	}
	if( isNaN( parseFloat( cost ) ) ){ showError('cost'); $("#cost").focus(); return false; }
	if( isNaN( price ) ){ showError('price'); $("#price").focus(); return false; }
	load_in();
	$.ajax({
		url:"controller/productController.php?addNewProduct",
		type:"POST", cache:"false", data: $("#productForm").serialize(),
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			var rs = parseInt(rs);
			if( isNaN( rs ) )
			{
				swal('ข้อผิดพลาด!!', 'เพิ่มสินค้าไม่สำเร็จ ลองออกจากหน้านี้แล้วกลับเข้ามาใหม่อีกครั้ง', 'error');
			}else{
				window.location.href = 'index.php?content=product&edit&id_product='+rs;
			}
		}
	});
}


//--------------------------  New Code  ---------------------//
function saveProduct()
{
	var pCode	= $.trim($("#pCode").val());
	var isDup	= $("#isDuplicated").val();
	var pName 	= $("#pName").val();
	var cost		= $("#cost").val();
	var price		= parseFloat($("#price").val());
	var dType	= $("#dType").val();
	var dis		= parseFloat($("#discount").val());

	if( pCode == '' ){
		$("#pCode-error").text('จำเป็นต้องกำหนดช่องนี้');
		showError('pCode');
		$("#pCode").focus();
		return false;
	}

	if( isDup == 1 ){
		$("#pCode-error").text('รหัสสินค้าซ้ำ');
		showError('pCode');
		$("#pCode").focus();
		return false;
	}

	if( pName == '' ){
		showError('pName');
		$("#pName").focus();
		return false;
	}
	if( ( dType == 'percentage' && dis > 100 ) || ( dType == 'amount' && dis > price ) ){
		validDiscount();
		$("#discount").focus();
		return false;
	}
	if( isNaN( parseFloat( cost ) ) ){ showError('cost'); $("#cost").focus(); return false; }
	if( isNaN( price ) ){ showError('price'); $("#price").focus(); return false; }
	load_in();
	$.ajax({
		url:"controller/productController.php?updateProduct",
		type:"POST", cache:"false", data: $("#productForm").serialize(),
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' )
			{
				swal({ title: 'เรียบร้อย', text: 'ปรับปรุงข้อมูลสินค้าเรียบร้อยแล้ว', timer: 1000, type: 'success'});
			}
			else if( rs == 'fail' )
			{
				swal('ข้อผิดพลาด!!', 'ปรับปรุงสินค้าไม่สำเร็จ ลองออกจากหน้านี้แล้วกลับเข้ามาใหม่อีกครั้ง', 'error');
			}
			else
			{
				swal('ข้อผิดพลาด!!', rs, 'error');
			}
		}
	});
}




$("#pCode").focusout(function(e) {
    var code = $.trim( $(this).val() );
	var id		= $("#id_product").val();
	if( code == '' )
	{
		$("#pCode-error").text('จำเป็นต้องกำหนดช่องนี้');
		showError('pCode');
	}else{
		hideError('pCode');
		validProductCode(code, id);
	}
});

$("#pName").focusout(function(e) {
	var name = $.trim( $(this).val() );
	if( name == '' ){ showError('pName'); }else{ hideError('pName'); }

});

$("#cost").focusout(function(e) {
    if( isNaN( parseFloat( $(this).val() ) ) ){	showError('cost'); }else{ hideError('cost'); }
});

$("#price").focusout(function(e) {
    if( isNaN( parseFloat( $(this).val() ) ) ){ showError('price'); }else{ hideError('price'); }
});

$("#discount").focusout(function(e) {
	validDiscount();
});

$(".ops").focusout(function(e) {
    var rs = $(this).val();
	if( isNaN( parseFloat(rs) ) ){ $(this).val('0.00'); }
});

$("#dType").change(function(e) {
    validDiscount();
});

function validDiscount()
{
	var dis	= parseFloat( $("#discount").val() );
	if( isNaN( dis ) ){ dis = 0.00; $("#discount").val('0.00'); }
	var type	= $("#dType").val();
	var price	= parseFloat($("#price").val());
	if( type == 'percentage' && dis > 100 ){
		$("#discount-error").text('ส่วนลดต้องไม่เกิน 100%');
		showError('discount');
	}else if( type == 'amount' && dis > price ){
		$("#discount-error").text('ส่วนลดต้องไม่เกินราคาขาย');
		showError('discount');
	}else{
		hideError('discount');
	}
}

function validProductCode(code, id)
{
	$.ajax({
		url:"controller/productController.php?validProductCode",
		type:"POST", cache: "false", data:{ "product_code" : code, "id_product" : id },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 1 ) //----- 1 = duplicated
			{
				$("#isDuplicated").val(1);
				$("#pCode-error").text('รหัสสินค้าซ้ำ');
				showError('pCode');
			}
			if( rs == 0 ) //----- 0 = not duplicate
			{
				$("#isDuplicated").val(0);
				hideError('pCode');
			}
		}
	});
}

function showError(el)
{
	var label = $("#"+el+"-error");
	var input	= $("#"+el);
	label.css('display', '');
	input.addClass('has-error');
}

function hideError(el)
{
	var label = $("#"+el+"-error");
	var input	= $("#"+el);
	label.css('display', 'none');
	input.removeClass('has-error');
}

function toggleShop( i )
{
	if( i == 1 )
	{
		$("#inShop").val(1);
		$("#btn-nis").removeClass('btn-danger');
		$("#btn-is").addClass('btn-success');
	}
	if( i == 0 )
	{
		$("#inShop").val(0);
		$("#btn-is").removeClass('btn-success');
		$("#btn-nis").addClass('btn-danger');
	}
}

function toggleActive( i )
{
	if( i == 1 )
	{
		$("#active").val(1);
		$("#btn-dac").removeClass('btn-danger');
		$("#btn-ac").addClass('btn-success');
	}
	if( i == 0 )
	{
		$("#active").val(0);
		$("#btn-ac").removeClass('btn-success');
		$("#btn-dac").addClass('btn-danger');
	}
}


function toggleVisual( i )
{
	if( i == 1 )
	{
		$("#isVisual").val(1);
		$("#btn-nvs").removeClass('btn-danger');
		$("#btn-vs").addClass('btn-success');
	}
	if( i == 0 )
	{
		$("#isVisual").val(0);
		$("#btn-vs").removeClass('btn-success');
		$("#btn-nvs").addClass('btn-danger');
	}
}


function newProduct()
{
	window.location.href = "index.php?content=product&add";
}

function goBack()
{
	window.location.href = "index.php?content=product";
}

function getGenerate()
{
	var id = $("#id_product").val();
	window.location.href = "index.php?content=attribute_gen&id_product="+id+"&step=1";
}

function changeURL(tab)
{
	var id = $("#id_product").val();
	var url = "index.php?content=product&edit&id_product="+id+"&tab="+tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'product', url);
}
//------------------------- End new code  -------------------//
