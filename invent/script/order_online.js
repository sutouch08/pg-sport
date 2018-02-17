// JavaScript Document

function saveService()
{
	var id_order 	= $("#id_order").val();
	var serCharge 	= $("#serCharge").val();
	$.ajax({
		url:"controller/orderController.php?saveServiceFee",
		type:"POST", cache:"false", data:{ "id_order" : id_order, "fee" : serCharge },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' )
			{
				swal({ title: 'เรียบร้อย', text: 'บันทึกค่าบริการเรียบร้อยแล้ว', timer: 1000, type: 'success' });
				setTimeout(function(){ window.location.reload(); }, 1200);
			}
			else
			{
				swal('ข้อผิดพลาด !!', 'บันทึกค่าบริการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง', 'error');
			}
		}
	});	
}


function editService()
{
	$("#serCharge").removeAttr('disabled');
	$("#btn-edit-service").addClass('hide');
	$("#btn-update-service").removeClass('hide');
	$("#serCharge").focus();
}


function viewImage(imageUrl)
{
	var image = '<img src="'+imageUrl+'" width="100%" />';
	$("#imageBody").html(image);
	$("#imageModal").modal('show');
}


function viewPaymentDetail(id_order)
{
	load_in();
	$.ajax({
		url:"controller/paymentController.php?viewPaymentDetail",
		type:"POST", cache:"false", data:{ "id_order" : id_order },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'fail' ){
				swal('ข้อผิดพลาด', 'ไม่พบข้อมูล', 'error');
			}else{
				var source 	= $("#detailTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#detailBody");
				render(source, data, output);
				$("#confirmModal").modal('show');
			}
		}
	});
}

$("#emsNo").keyup(function(e) {
    if( e.keyCode == 13 )
	{
		saveDeliveryNo();	
	}
});

function inputDeliveryNo()
{
	$("#deliveryModal").modal('show');	
}

function saveDeliveryNo()
{
	var deliveryNo 	= $("#emsNo").val();
	var id_order 	= $("#id_order").val();
	if( deliveryNo != '')
	{
		$("#deliveryModal").modal('hide');
		$.ajax({
			url:"controller/orderController.php?updateDeliveryNo",
			type:"POST", cache:"false", data:{ "deliveryNo" : deliveryNo, "id_order" : id_order },
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'success')
				{
					window.location.reload();
				}
			}
		});
	}
}

function submitPayment()
{
	var id_order	= $("#id_order").val();
	var id_account	= $("#id_account").val();
	var image		= $("#image")[0].files[0];
	var payAmount	= $("#payAmount").val();
	var orderAmount = $("#orderAmount").val();
	var payDate		= $("#payDate").val();
	var payHour		= $("#payHour").val();
	var payMin		= $("#payMin").val();
	if( id_order == '' ){ swal('ข้อผิดพลาด', 'ไม่พบไอดีออเดอร์กรุณาออกจากหน้านี้แล้วเข้าใหม่อีกครั้ง', 'error'); return false; }
	if( id_account == '' ){ swal('ข้อผิดพลาด', 'ไม่พบข้อมูลบัญชีธนาคาร กรุณาออกจากหน้านี้แล้วลองแจ้งชำระอีกครั้ง', 'error'); return false; }
	if( image == '' ){ swl('ข้อผิดพลาด', 'ไม่สามารถอ่านข้อมูลรูปภาพที่แนบได้ กรุณาแนบไฟล์ใหม่อีกครั้ง', 'error'); return false; }
	if( payAmount == 0 || isNaN( parseFloat(payAmount) ) || parseFloat(payAmount) < parseFloat(orderAmount) ){ swal("ข้อผิดพลาด", "ยอดชำระไม่ถูกต้อง", 'error'); return false; }
	if( !isDate(payDate) ){ swal('วันที่ไม่ถูกต้อง'); return false; }
	$("#paymentModal").modal('hide');
	var fd = new FormData();
	fd.append('image', $('input[type=file]')[0].files[0]);
	fd.append('id_order', id_order);
	fd.append('id_account', id_account);
	fd.append('payAmount', payAmount);
	fd.append('orderAmount', orderAmount);
	fd.append('payDate', payDate);
	fd.append('payHour', payHour);
	fd.append('payMin', payMin);
	load_in();
	$.ajax({
		url:"controller/orderController.php?confirmPayment",
		type:"POST", cache: "false", data: fd, processData:false, contentType: false,
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success')
			{
				swal({ title : 'สำเร็จ', text : 'แจ้งชำระเงินเรียบร้อยแล้ว', type: 'success', timer: 1000 });
				clearPaymentForm();
			}
			else if( rs == 'fail' )
			{
				swal("ข้อผิดพลาด", "ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง", "error");	
			}
			else
			{
				swal("ข้อผิดพลาด", rs, "error");	
			}
		}
	});	
}

function readURL(input) 
{
   if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
          $('#previewImg').html('<img id="previewImg" src="'+e.target.result+'" width="200px" alt="รูปสลิปของคุณ" />');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#image").change(function(){
	if($(this).val() != '')
	{
		var file 		= this.files[0];
		var name		= file.name;
		var type 		= file.type;
		var size		= file.size;
		if(file.type != 'image/png' && file.type != 'image/jpg' && file.type != 'image/gif' && file.type != 'image/jpeg' )
		{
			swal("รูปแบบไฟล์ไม่ถูกต้อง", "กรุณาเลือกไฟล์นามสกุล jpg, jpeg, png หรือ gif เท่านั้น", "error");
			$(this).val('');
			return false;
		}
		if( size > 2000000 )
		{ 
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 2 MB", "error"); 
			$(this).val(''); 
			return false;
		}
		readURL(this);
		$("#btn-select-file").css("display", "none");
		$("#block-image").animate({opacity:1}, 1000);
	}
});

function clearPaymentForm()
{
	$("#id_account").val('');
	$("#payAmount").val('');
	$("#payDate").val('');
	$("#payHour").val('00');
	$("#payMin").val('00');
	removeFile();
}
function removeFile()
{
	$("#previewImg").html('');
	$("#block-image").css("opacity","0");
	$("#btn-select-file").css('display', '');	
	$("#image").val('');
}

$("#payAmount").focusout(function(e) {
	if( $(this).val() != '' && isNaN(parseFloat($(this).val())) )
	{
		swal('กรุณาระบุยอดเงินเป็นตัวเลขเท่านั้น');
	}
});

function dateClick()
{
	$("#payDate").focus();	
}
$("#payDate").datepicker({ dateFormat: 'dd-mm-yy'});
function selectFile()
{
	$("#image").click();	
}

function payOnThis(id)
{
	$("#selectBankModal").modal('hide');
	$.ajax({
		url:"controller/bankController.php?getAccountDetail",
		type:"POST", cache:"false", data:{ "id_account" : id },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'fail' )
			{
				swal('ข้อผิดพลาด', 'ไม่พบข้อมูลที่ต้องการ กรุณาลองใหม่', 'error');
			}else{
				var ds = rs.split(' | ');
				var logo 	= '<img src="'+ ds[0] +'" width="50px" height="50px" />';
				var acc	= ds[1];
				$("#id_account").val(id);
				$("#logo").html(logo)
				$("#detail").html(acc);
				$("#paymentModal").modal('show');
			}
		}
	});
}

function payOrder()
{
	$("#selectBankModal").modal('show');	
}

function removeAddress(id)
{
	swal({
		title: 'ต้องการลบที่อยู่ ?',
		text: 'คุณแน่ใจว่าต้องการลบที่อยู่นี้ โปรดจำไว้ว่าการกระทำนี้ไม่สามารถกู้คืนได้',	
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ลบเลย',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: "controller/orderController.php?deleteOnlineAddress",
				type:"POST", cache:"false", data:{ "id_address" : id },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title : "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });	
						reloadAddressTable();						
					}else{
						swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");	
					}
				}
			});
		});	
}
//----------  edit address  -----------//
function editAddress(id)
{
	$.ajax({
		url:"controller/orderController.php?getAddressDetail",
		type:"POST", cache:"false", data:{ "id_address" : id },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'fail' ){
				swal("ข้อผิดพลาด!", "ไม่พบข้อมูลที่อยู่", "error");
			}else{
				var arr = rs.split(' | ');
				$("#id_address").val(arr[0]);
				$("#Fname").val(arr[1]);
				$("#Lname").val(arr[2]);
				$("#address1").val(arr[3]);
				$("#address2").val(arr[4]);
				$("#province").val(arr[5]);
				$("#postcode").val(arr[6]);
				$("#phone").val(arr[7]);
				$("#email").val(arr[8]);
				$("#alias").val(arr[9]);
				$("#addressModal").modal('show');
			}
		}
	});
}
//--------- set address as default address  ------------------//
function setDefault(id)
{
	var id_order = $("#id_order").val();
	$.ajax({
		url:"controller/orderController.php?setDefaultAddress",
		type:"POST", cache:"false", data:{ "id_address" : id, "id_order" : id_order },
		success: function(rs){			
			reloadAddressTable();
		}
	});
}

function reloadAddressTable()
{
	var id_order = $("#id_order").val();
	$.ajax({
		url:"controller/orderController.php?getAddressTable",
		type:"POST", cache:"false", data:{ "id_order" : id_order },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'fail' )
			{
				$("#adrs").html('<tr><td colspan="6" align="center">ไม่พบที่อยู่</td></tr>');
			}else{
				var source 	= $("#addressTableTemplate").html();
				var data 		= $.parseJSON(rs);
				var output 	= $("#adrs");
				render(source, data, output);
			}
		}
	});
}

function saveAddress()
{
	var id_order 	= $("#id_order").val();
	var name			= $("#Fname").val();
	var add1			= $("#address1").val();
	var email			= $("#email").val();
	var alias 		= $("#alias").val();
	if( name == '' ){ swal('กรุณาระบุชื่อผู้รับ'); return false; }
	if( add1 == '' ){ swal('กรุณาระบุที่อยู่ 1 '); return false; }
	if( alias == '' ){ swal('กรุณาตั้งชื่อให้ที่อยู่'); return false; }
	if( email != '' && !validEmail(email) ){ swal("อีเมล์ไม่ถูกต้องกรุณาตรวจสอบ"); return false; }
	$("#addressModal").modal('hide');
	load_in();
	$.ajax({
		url:"controller/orderController.php?addOnlineAddress&id_order="+id_order,
		type:"POST", cache:"false", data: $("#addAddressForm").serialize(),
		success: function(rs){
			load_out();
			var rs 		= $.trim(rs);
			if( rs == 'fail'){
				swal('ข้อผิดพลาด', 'เพิ่ม/แก้ไข ที่อยู่ไม่สำเร็จ', 'error');
				$("#addressModal").modal('show');
			}else if( rs == 'success'){
				reloadAddressTable();
				clearAddressField();
			}
		}
	});			
}


function addNewAddress()
{
	clearAddressField();
	$("#addressModal").modal('show');	
}

function clearAddressField()
{
	$("#id_address").val('');
	$("#Fname").val('');
	$("#Lname").val('');
	$("#address1").val('');
	$("#address2").val('');
	$("#province").val('');
	$("#postcode").val('');
	$("#phone").val('');
	$("#email").val('');
	$("#alias").val('');	
}


function valid_password(){
	$("#loader").css("z-index","1100");
	load_in();
	var password = $("#bill_password").val();
	$.ajax({
		url:"controller/orderController.php?check_password&password="+password,
		type:"GET", cache:false, 
		success: function(data){
			if(data == "0"){
				load_out();
				$("#bill_message").html("รหัสลับไม่ถูกต้องกรุณาตรวจสอบ");
				$("#bill_password").val("");
			}else{
				insert_discount(data);
			}
		}
	});
}

function valid_approve()
{
	$("#loader").css("z-index","1100");
	load_in();
	var password = $("#edit_bill_password").val();
	$.ajax({
		url:"controller/orderController.php?check_password&password="+password,
		type:"GET", cache:false, 
		success: function(data){
			if(data == "0"){
				load_out();
				$("#edit_bill_message").html("รหัสลับไม่ถูกต้องกรุณาตรวจสอบ");
				$("#edit_bill_password").val("");
			}else{
				update_bill_discount(data);
			}
		}
	});
}

function update_bill_discount(id_approve)
{
	var id_order = $("#id_order").val();
	var discount = $("#last_discount").val();
	$.ajax({
		url:"controller/orderController.php?update_bill_discount", type:"POST", cache:false,
		data: { "id_order" : id_order, "id_approve" : id_approve, "discount" : discount },
		success: function(rs){
			var rs = $.trim(rs);
			load_out();
			if(rs == "success")
			{
				window.location.reload();
			}else{
				$("#modal_approve_edit").modal("hide");
				swal("แก้ไขส่วนลดไม่สำเร็จ");
				$("#btn_update_discount").css("display","none");
				$("#btn_edit_discount").css("display","");
				$("#btn_delete_discount").css("display","");
				$("#edit_reduction").removeAttr("disabled");
			}
		}
		
	});
}

$("#password").keyup(function(e){ if( e.keyCode == 13 && $(this).val() != '' ){ checkPassword(); } });
$("#bill_password").keyup(function(e){ if( e.keyCode == 13 && $(this).val() != '' ){ valid_password(); } });
$("#edit_bill_password").keyup(function(e){ if( e.keyCode == 13 && $(this).val() != '' ){ valid_approve(); } });

$("#btn_update_discount").click(function(e) {
    var discount = $("#last_discount").val();
	if( discount == "" || discount < 1)
	{
		swal("ส่วนลดต้องมากกว่า 0");
		return false;
	}else{
		$("#modal_approve_edit").modal("show");
	}
});

function action_delete(id_order, amount)
{
	var text = "คุณต้องการลบส่วนลดท้ายบิล มูลค่า "+ amount;
	var url  = "controller/orderController.php?delete_bill_discount&id_order="+id_order;
	confirm_delete('คุณแน่ใจนะ ?', text, url );
}

function edit_discount()
{
	$("#discount_label").css("display","none");
	$("#last_discount").css("display","");
	$("#btn_edit_discount").css("display","none");
	$("#btn_delete_discount").css("display","none");
	$("#edit_reduction").attr("disabled", "disabled");
	$("#btn_update_discount").css("display","");	
}

function insert_discount(id_approve)
{
	var id_order = $("#id_order").val();
	var discount = $("#last_discount").val();
	$.ajax({
		url:"controller/orderController.php?insert_bill_discount", type:"POST", cache:false,
		data: { "id_order" : id_order, "id_approve" : id_approve, "discount" : discount },
		success: function(rs){
			var rs = $.trim(rs);
			load_out();
			if(rs == "success")
			{
				window.location.reload();
			}else{
				swal("เพิ่มส่วนลดไม่สำเร็จ");
				$("#btn_save_discount").css("display","none");
				$("#btn_add_discount").css("display","");
				$("#edit_reduction").removeAttr("disabled");
			}
		}
		
	});
}


function add_discount()
{
	var id_order = $("#id_order").val();
	load_in();
	var discount = $("#last_discount").val();
	if( discount == "" || discount < 1 )
	{
		load_out();
		swal("ส่วนลดท้ายบิลต้องมากกว่า 0");
		return false;
	}else{
		load_out();
		$("#modal_approve").modal("show");	
	}
}

$("#btn_add_discount").click(function(e){
	$("#edit_reduction").attr("disabled", "disabled");
	$(this).css("display", "none");
	$("#btn_save_discount").css("display","");
	$("#last_discount_row").css("display","");
	$("#last_discount").focus();
});


$("#from_date").datepicker({
	dateFormat: 'dd-mm-yy', 
	onClose: function( selectedDate ) {
      $( "#to_date" ).datepicker( "option", "minDate", selectedDate );
	  if( $(this).val() != '' && $("#to_date").val() == '' ){ $("#to_date").focus(); }
	}
});

$( "#to_date" ).datepicker({
	dateFormat: 'dd-mm-yy',   
	onClose: function( selectedDate ) {
        $( "#from_date" ).datepicker( "option", "maxDate", selectedDate );
      }
});
 

$("#date").datepicker({  dateFormat: 'dd-mm-yy' });

function validate() 
{
	var from_date	= $("#from_date").val();
	var to_date 		= $("#to_date").val();
	if( !isDate(from_date) || !isDate(to_date) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	$("#form").submit();
}	

function state_change()
{
	var state = $("#order_state").val();
	if(state == 0){ swal("กรุณาเลือกสถานะ"); return false; }
	$("#state_change").submit();
}

var clipboard = new Clipboard('.btn');	


//-------------------------------------  New code ----------------------------------------//

//-----------------  ตรวจสอบการแก้ไขส่วนลดรายการ  ---------------------//
function verifyDiscount(id, price)
{
	var inp	= $("#reduction"+id);
	var disc 	= parseFloat(inp.val());
	var unt	= $("#unit"+id).val();
	var price	= parseFloat(price);
	if( inp.val() != '' && isNaN(disc) ){ swal("กรุณาใช้เฉพาะตัวเลขเท่านั้น"); inp.val(0); }
	if( unt == 'percent' && disc > 100 ){ swal("ส่วนลดต้องไม่เกิน 100%"); inp.val(0); }
	if( unt == 'percent' && disc < 0 ){ swal("ส่วนลดต้องไม่น้อยกว่า 0%"); inp.val(0);}
	if( unt == 'amount' && disc > price ){ swal("ส่วนลดต้องไม่เกินราคาขาย"); inp.val(0);}
	if( unt == 'amount' && disc < 0 ){ swal("ส่วนลดต้องไม่ติดลบ"); inp.val(0); }		
}


//------------------  ยืนยันรหัสการแก้ไขส่วนลดรายการ  ---------------------------//
function verifyPassword()
{
	$("#ModalLogin").modal('show');
}


//-------------------  ตรวจสอบรหัสยืนยันการแก้ไขส่วนลดรายการ  ---------------------//
function checkPassword(){
	$("#loader").css("z-index","1100");
	$("#ModalLogin").modal("hide");
	load_in();
	var password = $("#password").val();
	if( password == "" ){ return false; }
	$.ajax({
		url:"controller/orderController.php?check_password&password="+password,
		type:"GET", cache:false, 
		success: function(data){
			load_out();
			if(data == "0"){
				$("#message").html("รหัสลับไม่ถูกต้องกรุณาตรวจสอบ");
				$("#password").val("");
				$("#ModalLogin").modal("show");
			}else{
				updateDiscount(data);
			}
		}
	});
}


//----------------------------- บันทึกส่วนลดรายการ -----------------------//
function updateDiscount( id_emp )
{
	var id_order = $("#id_order").val();
	$.ajax({
		url: "controller/orderController.php?updateDiscount&id_order="+id_order+"&id_approve="+id_emp,
		type:"POST", cache:"false", data: $("#editOrderForm").serialize(),
		success: function(rs){
			window.location.reload();
		}
	});
}

//---------------------------  เพิ่มการสั่งซื้อสินค้า  -----------------------//
function addToOrder(id_order)
{
	$("#order_grid").modal("hide");
	load_in();
	$.ajax({
		url:"controller/orderController.php?addToOrder",
		type: "POST", cache:"false", data: $("#gridForm").serialize(),
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			var arr = rs.split(' | ');
			var c		= arr.length;
			if( c == 1 && arr[0] == 'success' ){
				swal({ title: "สำเร็จ", text: "เพิ่มรายการเรียบร้อยแล้ว", type: "success", timer: 1000 });
				reloadOrderProduct(id_order);
			}else if( c == 2 && arr[0] == 'fail'){
				swal("ข้อผิดพลาด!!", "เพิ่มสินค้าในรายการไม่สำเร็จ กรุณาตรวจสอบ แล้วลองใหม่อีกครั้ง", "error");
				$("#order_grid").modal("show");
			}else if( c == 2 && arr[0] == 'overstock' ){
				swal("ข้อผิดพลาด!!", arr[1]+" มีสินค้าคงเหลือไม่เพียงพอ กรุณาตรวจสอบแล้วสั่งใหม่อีกครั้ง", "error");
				$("#order_grid").modal("show");
			}
		}
	});
}

//---------------------------  โหลดตารางรายการสั่งสินค้าใหม่  --------------------------//
function reloadOrderProduct(id_order)
{
	$.ajax({
		url:"controller/orderController.php?getOrderProductTable"	,
		type:"POST", cache: "false", data:{ "id_order" : id_order },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != 'fail' && rs != '' )
			{
				var source = $("#orderProductTemplate").html();
				var data 		= $.parseJSON(rs);
				var output	= $("#orderProductTable");
				render(source, data, output);
			}
		}
	});
}


//--------------------------------  โหลดรายการสินค้าสำหรับจิ้มสั่งสินค้า  -----------------------------//
function getCategory(id)
{
	var output = $("#cat-"+id);
	if( output.html() == '')
	{
		load_in();
		$.ajax({
			url:"controller/orderController.php?getCategoryProductGrid",
			type:"POST", cache:"false", data:{ "id_category" : id },
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( rs != 'no_product' ){
					output.html(rs);
				}else{
					rs = '<h4><center><i class="fa fa-tags"></i> ไม่พบสินค้าในหมวดหมู่นี้ </center></h4>';
					output.html(rs);
				}
			}
		});
	}
}


//--------------------------------  โหลดรายการสินค้าสำหรับดูยอดคงเหลือ  -----------------------------//
function getViewCategory(id)
{
	var output = $("#cat-"+id);
	if( output.html() == '')
	{
		load_in();
		$.ajax({
			url:"controller/orderController.php?getCategoryGrid",
			type:"POST", cache:"false", data:{ "id_category" : id },
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( rs != 'no_product' ){
					output.html(rs);
				}else{
					swal("ไม่พบข้อมูล", "ไม่พบข้อมูลสินค้าในหมวดหมู่ที่เลือก", "warning");		
				}
			}
		});
	}
}

//----------------------------  โหลดตารางใส่จำนวนสั่งซื้อของสินค้า  --------------------//
function getProduct()
{
	var st 		= $("#sProduct").val();
	var id_cus	= $("#id_customer").val();
	
	if( st == '' ){ swal("กรุณาระบุรหัสสินค้า"); return false; }
	
	load_in();
	$.ajax({
		url:"controller/orderController.php?getProductGrid",
		type:"POST", cache: "false", data:{ "product_code" : st, "id_customer" : id_cus },
		success: function(dataset){
			load_out();
			if(dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				$("#modal").css("width",table_w+"px");
				$("#modal_title").html(title);
				$("#modal_body").html(data);
				$("#order_grid").modal("show");
			}else{
				swal("ไม่พบสินค้า", "รหัสสินค้าไม่ถูกต้อง หรือ ไม่มีสินค้านี้ในระบบ กรุณาตรวจสอบ", "error");
			}		
		}
	});		
}



//----------------------------  โหลดตารางดูยอดคงเหลือสินค้า  --------------------//
function viewProduct()
{
	var st 		= $("#vProduct").val();
	if( st == '' ){ swal("กรุณาระบุรหัสสินค้า"); return false; }
	
	load_in();
	$.ajax({
		url:"controller/orderController.php?viewProductGrid",
		type:"POST", cache: "false", data:{ "product_code" : st },
		success: function(dataset){
			load_out();
			if(dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				$("#modal").css("width",table_w+"px");
				$("#modal_title").html(title);
				$("#modal_body").html(data);
				$("#order_grid").modal("show");
			}else{
				swal("ไม่พบสินค้า", "รหัสสินค้าไม่ถูกต้อง หรือ ไม่มีสินค้านี้ในระบบ กรุณาตรวจสอบ", "error");
			}		
		}
	});		
}
//----------------------  ลบรายการสินค้าในหน้าเพิ่ม  ------------------//
function deleteRow(id, ref)
{
	swal({
		title: 'ต้องการลบ ?',
		text: 'คุณแน่ใจว่าต้องการลบ '+ref+' ?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: "controller/orderController.php?deleteOrderDetail",
				type:"POST", cache:"false", data:{ "id_order_detail" : id },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title : "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });	
						var id_order = $("#id_order").val();
						reloadOrderProduct(id_order);
					}else{
						swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");	
					}
				}
			});
		});	
}

//------------------------------  ลบรายการสินค้าในหน้า แสดงรายละเอียด  ---------------------//
function deleteItem(id, ref)
{
	swal({
		title: 'ต้องการลบ ?',
		text: 'คุณแน่ใจว่าต้องการลบ '+ref+' ?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: "controller/orderController.php?deleteOrderDetail",
				type:"POST", cache:"false", data:{ "id_order_detail" : id },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title : "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });	
						$("#row_"+id).remove();
						recalOrder();
					}else{
						swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");	
					}
				}
			});
		});	
}

function recalOrder()
{
	var id_order = $("#id_order").val();
	$.ajax({
		url:"controller/orderController.php?getBillSummary",
		type: "POST", cache: "false", data:{ "id_order" : id_order },
		success: function(rs){
			var rs = $.trim(rs);
			var ar = rs.split(' | ');
			var total_price = addCommas(ar[0]);
			var total_disc	= addCommas(ar[1]);
			var net			= addCommas(ar[2]);
			$("#total_price").html("<b>"+total_price+"</b>");
			$("#total_disc").html("<b>"+total_disc+"</b>");
			$("#net").html("<b>"+net+"</b>");
		}
	});
}

$("#doc_date").datepicker({ 	dateFormat: 'dd-mm-yy'});

function newOrder()
{
	var date 			= $("#doc_date").val();
	var cus_name	= $("#customer_name").val();
	var id_cus		= $("#id_customer").val();
	if( date == '' ){ swal('วันที่ไม่ถูกต้อง'); return false; }
	if( id_cus == '' || cus_name == '' ){ swal('ชื่อลูกค้าไม่ถูกต้อง'); return false; }
	load_in();
	$.ajax({
		url:"controller/orderController.php?addNewOrder",
		type:"POST", cache:false, data: $("#addForm").serialize(),
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'fail' ){
				swal("ข้อผิดพลาด!!", "เพิ่มออเดอร์ไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}else{
				window.location.href = 'index.php?content=order_online&add&id_order='+rs;	
			}
		}
	});
}


function editOrder()
{
	$("#doc_date").removeAttr("disabled");
	$("#customer_name").removeAttr("disabled");
	$("#payment").removeAttr("disabled");
	$("#online").removeAttr("disabled");
	$("#comment").removeAttr("disabled");
	$("#btnEdit").css("display","none");
	$("#btnUpdate").css("display", "");
}


function updateOrder(id)
{
	var date 			= $("#doc_date").val();
	var cus_name	= $("#customer_name").val();
	var id_cus		= $("#id_customer").val();
	if( date == '' ){ swal('วันที่ไม่ถูกต้อง'); return false; }
	if( id_cus == '' || cus_name == '' ){ swal('ชื่อลูกค้าไม่ถูกต้อง'); return false; }
	load_in();
	$.ajax({
		url:"controller/orderController.php?updateEditOrderHeader",
		type:"POST", cache:"false", data: $("#addForm").serialize(),
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' )
			{
				swal({ title : "เรียบร้อย", text: "ปรับปรุงข้อมูลเรียบร้อยแล้ว", type: "success", timer: 1000 });
				updated();
			}
		}
	});
}


function updated()
{
	$("#doc_date").attr("disabled", "disabled");
	$("#customer_name").attr("disabled", "disabeld");
	$("#payment").attr("disabled", "disabled");
	$("#online").attr("disabled", "disabled");
	$("#comment").attr("disabled", "disabled");
	$("#btnUpdate").css("display", "none");
	$("#btnEdit").css("display", "");	
}


//-----------  Save Order ------------//
function save(id)
{
	load_in();
	$.ajax({
		url:"controller/orderController.php?saveOrder",
		type: "POST", cache: "false", data: { "id_order" : id },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title: "เรียบร้อย", text: "บันทึกออเดอร์เรียบร้อยแล้ว", type: "success", timer: 1000 });
				setTimeout( function(){ goBack(); }, 1500);
			}else{
				swal("ข้อผิดพลาด!!", "บันทึกออเดอร์ไม่สำเร็จ กรุณาลองใหม่อีกครั้งภายหลัง", "error");	
			}
		}
	});
}

function goBack()
{
	window.location.href = 'index.php?content=order_online';	
}


function addNew()
{
	$.ajax({
		url:"controller/orderController.php?checkOrderNotSave",
		type:"GET", cache:"false", success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'ok'){
				window.location.href = 'index.php?content=order_online&add';	
			}else{
				window.location.href = 'index.php?content=order_online&add&id_order='+rs+'&warning=ยังไม่ได้บันทึกออเดอร์นี้';
			}
		}
	});
}

function addNewOnline()
{
	$.ajax({
		url:"controller/orderController.php?checkOrderNotSave",
		type:"GET", cache:"false", success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'ok'){
				window.location.href = 'index.php?content=order_online&add';	
			}else{
				window.location.href = 'index.php?content=order_online&add&id_order='+rs+'&warning=ยังไม่ได้บันทึกออเดอร์นี้';
			}
		}
	});
}

function viewOrder(id)
{
	window.location.href = 'index.php?content=order_online&edit&view_detail&id_order='+id;	
}

function getEdit(id)
{
	window.location.href = 'index.php?content=order_online&add&id_order='+id;
}


function viewStock()
{
	window.location.href = 'index.php?content=order&view_stock';	
}


$("#sProduct").autocomplete({
	source: "controller/autoComplete.php?product_code",
	autoFocus: true		
});


$("#sProduct").keyup(function(e){
	if(e.keyCode == 13 ){
		if( $(this).val() != '' ){
			getProduct();
		}
	}
});

$("#vProduct").autocomplete({
	source: "controller/autoComplete.php?product_code",
	autoFocus: true		
});


$("#vProduct").keyup(function(e){
	if(e.keyCode == 13 ){
		if( $(this).val() != '' ){
			viewProduct();
		}
	}
});


$("#customer_name").autocomplete({
	source:"controller/orderController.php?customer_name",
	autoFocus: true,
	close: function(event,ui){
		var data = $("#customer_name").val();
		var arr = data.split(' | ');
		var name = arr[1];
		var id_customer = arr[2];
		if( !isNaN(parseInt(id_customer)) ){
			$("#id_customer").val(id_customer);
			$(this).val(name);
		}else{
			$("#id_customer").val('');
			$(this).val('');
		}
	}
});		

function getSearch()
{
	$("#searchForm").submit();
}

function clearFilter()
{
	$.ajax({
		url: "controller/orderController.php?clearFilter",
		success: function(rs){
			goBack();
		}
	});
}

$("#s_ref, #s_cus, #s_emp").keyup(function(e) {
    if( e.keyCode == 13 )
	{
		getSearch();
	}
});

function addDeliveryFee()
{
	$("#deliveryFee").css("display", "inline");
	$("#btn-add-fee").css("display", "none");
	$("#btn-update-fee").css("display", "inline");
	$("#deliveryFee").focus();
}

function editDeliveryFee()
{
	$("#deliveryFee").removeAttr("disabled");
	$("#btn-edit-fee").css("display", "none");
	$("#btn-update-fee").css("display", "inline");
	$("#deliveryFee").focus();
}


function updateDeliveryFee(id_order)
{
	var fee = parseFloat($("#deliveryFee").val());
	if( isNaN( fee) ){ swal("ค่าจัดส่งไม่ถูกต้อง"); return false; }
	load_in();
	$.ajax({
		url:"controller/orderController.php?updateDeliveryFee",
		type:"POST",cache: "false", data: { "id_order" : id_order, "fee" : fee },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title : "สำเร็จ", text: "", timer: 1000, type: "success"});
				setTimeout(function(){	window.location.reload(); }, 1500);
			}else{
				swal("ข้อผิดพลาด!!", "แก้ไขค่าจัดส่งไม่สำเร็จ", "error");
			}
		}
	});
}

$("#deliveryFee").keyup(function(e) {
    if( e.keyCode == 13 ){
		$("#btn-update-fee").click();
	}
});
//-----------------  End New code ----------------/

function getData(id_product){
	var id_cus = $("#id_customer").val();
	$.ajax({
		url:"controller/orderController.php?getData&id_product="+id_product+"&id_customer="+id_cus,
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
				$("#order_grid").modal('show');
			}else{
				alert("NO DATA");
			}		
		}
	});
}
function view_data(id_product){
	$.ajax({
		url:"controller/orderController.php?view_stock_data&id_product="+id_product,
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
				$("#order_grid").modal('show');
			}else{
				swal("NO DATA");
			}		
		}
	});
}
$("#edit_reduction").click(function(e) {
    $(".reduction").css("display","none");
	$(".input_reduction").css("display","");
	$("#edit_reduction").css("display", "none");
	$("#save_reduction").css("display","");
});


function check_order(id)
{
	var wid = $(document).width();
	var left = (wid - 1100) /2;
	window.open("index.php?content=bill&id_order="+id+"&check_order&view_detail=y&nomenu", "_blank", "width=1100, height=800, left="+left+", location=no, scrollbars=yes");	
}

function print_order(id)
{
	var wid = $(document).width();
	var left = (wid - 900) /2;
	window.open("controller/orderController.php?print_order&id_order="+id, "_blank", "width=900, height=1000, left="+left+", location=no, scrollbars=yes");	
}

function getSummary()
{
	$("#orderSummaryTab").modal("show");
}

function closeOrder(id_order)
{
	$.ajax({
		url:"controller/orderController.php?closeOrder",
		type:"POST", cache:"false", data:{ "id_order" : id_order },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success')
			{
				swal({ title: 'เรียบร้อย', timer: 1000, type: 'success'});
				setTimeout(function(){ window.location.reload(); }, 1200);
			}else{
				swal("ข้อผิดพลาด!!", "ปิดออเดอร์ไม่สำเร็จ กรุณาลองใหม่อีกครั้งภายหลัง", "error");
			}
		}
	});
}


function toggleMe()
{
	var  me	= $("#viewType").val();
	if( me == 1 ){
		$("#viewType").val(0);
		$("#btn-view-me").removeClass('btn-info');
	}else{
		$("#viewType").val(1);
		$("#btn-view-me").addClass('btn-info');
	}
	getSearch();	
}

function toggleClosed()
{
	var closed = $("#closed").val();
	if( closed == 1 )
	{
		$("#closed").val(0);
		$("#btn-closed").removeClass('btn-primary');	
	}else{
		$("#closed").val(1);
		$("#btn-closed").addClass('btn-primary');
	}
	getSearch();
}

function toggleDelivered()
{
	var delivered = $("#delivered").val();
	if( delivered == 1 )
	{
		$("#delivered").val(0);
		$("#btn-delivered").removeClass('btn-primary');	
	}else{
		$("#delivered").val(1);
		$("#btn-delivered").addClass('btn-primary');
	}
	getSearch();
}

$("#Fname").keyup(function(e){ if( e.keyCode == 13 ){ $("#Lname").focus(); 	} });
$("#Lname").keyup(function(e){ if( e.keyCode == 13 ){ $("#address1").focus(); 	} });
$("#address1").keyup(function(e){ if( e.keyCode == 13 ){ $("#address2").focus(); 	} });
$("#address2").keyup(function(e){ if( e.keyCode == 13 ){ $("#province").focus(); 	} });
$("#province").keyup(function(e){ if( e.keyCode == 13 ){ $("#postcode").focus(); 	} });
$("#postcode").keyup(function(e){ if( e.keyCode == 13 ){ $("#phone").focus(); 	} });
$("#phone").keyup(function(e){ if( e.keyCode == 13 ){ $("#email").focus(); 	} });
$("#email").keyup(function(e){ if( e.keyCode == 13 ){ $("#alias").focus(); 	} });
$("#alias").keyup(function(e){ if( e.keyCode == 13 ){ saveAddress(); } });
