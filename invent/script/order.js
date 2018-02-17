// JavaScript Document
function confirmEditPrice() {
    $("#editPriceModal").modal('show');
}

function validConfirmPrice() {
    var ps = MD5($("#confirmPricePassword").val());
    $.ajax({
        url: "controller/orderController.php?validEditPricePassword",
        type: "POST",
        cache: "false",
        data: { "s_key": ps },
        success: function(rs) {
            var rs = $.trim(rs);
            if (rs == '0') {
                $("#confirmPrice-error").text('รหัสไม่ถูกต้องหรือไม่มีอำนาจในการอนุมัติ');
                $("#confirmPrice-error").css("display", "");
                $("#confirmPricePassword").val('');
                $("#confirmPricePassword").focus();
            } else {
                $("#editPriceModal").modal('hide');
                $("#confirmPricePassword").val('');
                updatePrice();
            }
        }
    });
}

function updatePrice() {
    load_in();
    $.ajax({
        url: "controller/orderController.php?updateEditPrice",
        type: "POST",
        cache: "false",
        data: $(".input-price").serialize(),
        success: function(rs) {
            load_out();
            var rs = $.trim(rs);
            if (rs == 'success') {
                swal({ title: 'เรียบร้อย', text: 'บันทึกราคาใหม่เรียบร้อยแล้ว', timer: 1000, type: 'success' });
                setTimeout(function() { window.location.reload(); }, 1200);
            } else {
                swal("ข้อผิดพลาด !", "บันทึกราคาใหม่ไม่สำเร็จ", "error");
            }
        }
    });
}


$("#confirmPricePassword").keyup(function(e) {
    if (e.keyCode == 13) {
        validConfirmPrice();
    }
});

$(".input-price").focusout(function(e) {
    var price = $(this).val();
    if (isNaN(parseFloat(price))) {
        swal('ราคาไม่ถูกต้อง');
        $(this).addClass('has-error');
    } else if (price < 0) {
        swal('ราคาไม่ถูกต้อง');
        $(this).addClass('has-error');
    } else {
        $(this).removeClass('has-error');
    }
});


function getEditPrice() {
    $("#btn-edit-price").css("display", "none");
    $("#btn-update-price").css("display", "");
    $(".price_label").css('display', 'none');
    $(".input-price").css("display", "");
}


function showAll() {
    $("#viewType").val(1);
    $("#btn-show-online").removeClass("btn-info");
	$("#btn-show-normal").removeClass("btn-info");
    $("#btn-show-all").addClass("btn-info");
    getSearch();
}

function showOnline() {
    $("#viewType").val(2);
    $("#btn-show-all").removeClass("btn-info");
	$("#btn-show-normal").removeClass("btn-info");
    $("#btn-show-online").addClass("btn-info");
    getSearch();
}

function showNormal() {
	 $("#viewType").val(3);
    $("#btn-show-all").removeClass("btn-info");
	$("#btn-show-online").removeClass("btn-info");
    $("#btn-show-normal").addClass("btn-info");
    getSearch();
}

function valid_password() {
    $("#loader").css("z-index", "1100");
    load_in();
    var password = $("#bill_password").val();
    $.ajax({
        url: "controller/orderController.php?check_password&password=" + password,
        type: "GET",
        cache: false,
        success: function(data) {
            if (data == "0") {
                load_out();
                $("#bill_message").html("รหัสลับไม่ถูกต้องกรุณาตรวจสอบ");
                $("#bill_password").val("");
            } else {
                insert_discount(data);
            }
        }
    });
}

function valid_approve() {
    $("#loader").css("z-index", "1100");
    load_in();
    var password = $("#edit_bill_password").val();
    $.ajax({
        url: "controller/orderController.php?check_password&password=" + password,
        type: "GET",
        cache: false,
        success: function(data) {
            if (data == "0") {
                load_out();
                $("#edit_bill_message").html("รหัสลับไม่ถูกต้องกรุณาตรวจสอบ");
                $("#edit_bill_password").val("");
            } else {
                update_bill_discount(data);
            }
        }
    });
}

function update_bill_discount(id_approve) {
    var id_order = $("#id_order").val();
    var discount = $("#last_discount").val();
    $.ajax({
        url: "controller/orderController.php?update_bill_discount",
        type: "POST",
        cache: false,
        data: { "id_order": id_order, "id_approve": id_approve, "discount": discount },
        success: function(rs) {
            var rs = $.trim(rs);
            load_out();
            if (rs == "success") {
                window.location.reload();
            } else {
                $("#modal_approve_edit").modal("hide");
                swal("แก้ไขส่วนลดไม่สำเร็จ");
                $("#btn_update_discount").css("display", "none");
                $("#btn_edit_discount").css("display", "");
                $("#btn_delete_discount").css("display", "");
                $("#edit_reduction").removeAttr("disabled");
            }
        }

    });
}

$("#btn_update_discount").click(function(e) {
    var discount = $("#last_discount").val();
    if (discount == "" || discount < 1) {
        swal("ส่วนลดต้องมากกว่า 0");
        return false;
    } else {
        $("#modal_approve_edit").modal("show");
    }
});

function action_delete(id_order, amount) {
    var text = "คุณต้องการลบส่วนลดท้ายบิล มูลค่า " + amount;
    var url = "controller/orderController.php?delete_bill_discount&id_order=" + id_order;
    confirm_delete('คุณแน่ใจนะ ?', text, url);
}

function edit_discount() {
    $("#discount_label").css("display", "none");
    $("#last_discount").css("display", "");
    $("#btn_edit_discount").css("display", "none");
    $("#btn_delete_discount").css("display", "none");
    $("#edit_reduction").attr("disabled", "disabled");
    $("#btn_update_discount").css("display", "");
}

function insert_discount(id_approve) {
    var id_order = $("#id_order").val();
    var discount = $("#last_discount").val();
    $.ajax({
        url: "controller/orderController.php?insert_bill_discount",
        type: "POST",
        cache: false,
        data: { "id_order": id_order, "id_approve": id_approve, "discount": discount },
        success: function(rs) {
            var rs = $.trim(rs);
            load_out();
            if (rs == "success") {
                window.location.reload();
            } else {
                swal("เพิ่มส่วนลดไม่สำเร็จ");
                $("#btn_save_discount").css("display", "none");
                $("#btn_add_discount").css("display", "");
                $("#edit_reduction").removeAttr("disabled");
            }
        }

    });
}


function add_discount() {
    var id_order = $("#id_order").val();
    load_in();
    var discount = $("#last_discount").val();
    if (discount == "" || discount < 1) {
        load_out();
        swal("ส่วนลดท้ายบิลต้องมากกว่า 0");
        return false;
    } else {
        load_out();
        $("#modal_approve").modal("show");
    }
}

$("#btn_add_discount").click(function(e) {
    $("#edit_reduction").attr("disabled", "disabled");
    $(this).css("display", "none");
    $("#btn_save_discount").css("display", "");
    $("#last_discount_row").css("display", "");
    $("#last_discount").focus();
});


$("#from_date").datepicker({
    dateFormat: 'dd-mm-yy',
    onClose: function(selectedDate) {
        $("#to_date").datepicker("option", "minDate", selectedDate);
        if ($(this).val() != '' && $("#to_date").val() == '') { $("#to_date").focus(); }
    }
});

$("#to_date").datepicker({
    dateFormat: 'dd-mm-yy',
    onClose: function(selectedDate) {
        $("#from_date").datepicker("option", "maxDate", selectedDate);
    }
});


$("#date").datepicker({ dateFormat: 'dd-mm-yy' });

function validate() {
    var from_date = $("#from_date").val();
    var to_date = $("#to_date").val();
    if (!isDate(from_date) || !isDate(to_date)) { swal("วันที่ไม่ถูกต้อง"); return false; }
    $("#form").submit();
}

function state_change() {
    var state = $("#order_state").val();
    if (state == 0) { swal("กรุณาเลือกสถานะ"); return false; }
    $("#state_change").submit();
}

var clipboard = new Clipboard('.btn');


//-------------------------------------  New code ----------------------------------------//

//-----------------  ตรวจสอบการแก้ไขส่วนลดรายการ  ---------------------//
function verifyDiscount(id, price) {
    var inp = $("#reduction" + id);
    var disc = parseFloat(inp.val());
    var unt = $("#unit" + id).val();
    var price = parseFloat(price);
    if (inp.val() != '' && isNaN(disc)) {
        swal("กรุณาใช้เฉพาะตัวเลขเท่านั้น");
        inp.val(0);
    }
    if (unt == 'percent' && disc > 100) {
        swal("ส่วนลดต้องไม่เกิน 100%");
        inp.val(0);
    }
    if (unt == 'percent' && disc < 0) {
        swal("ส่วนลดต้องไม่น้อยกว่า 0%");
        inp.val(0);
    }
    if (unt == 'amount' && disc > price) {
        swal("ส่วนลดต้องไม่เกินราคาขาย");
        inp.val(0);
    }
    if (unt == 'amount' && disc < 0) {
        swal("ส่วนลดต้องไม่ติดลบ");
        inp.val(0);
    }
}


//------------------  ยืนยันรหัสการแก้ไขส่วนลดรายการ  ---------------------------//
function verifyPassword() {
    $("#ModalLogin").modal('show');
}


//-------------------  ตรวจสอบรหัสยืนยันการแก้ไขส่วนลดรายการ  ---------------------//
function checkPassword() {
    $("#loader").css("z-index", "1100");
    $("#ModalLogin").modal("hide");
    load_in();
    var password = $("#password").val();
    if (password == "") { return false; }
    $.ajax({
        url: "controller/orderController.php?check_password&password=" + password,
        type: "GET",
        cache: false,
        success: function(data) {
            load_out();
            if (data == "0") {
                $("#message").html("รหัสลับไม่ถูกต้องกรุณาตรวจสอบ");
                $("#password").val("");
                $("#ModalLogin").modal("show");
            } else {
                updateDiscount(data);
            }
        }
    });
}


//----------------------------- บันทึกส่วนลดรายการ -----------------------//
function updateDiscount(id_emp) {
    var id_order = $("#id_order").val();
    $.ajax({
        url: "controller/orderController.php?updateDiscount&id_order=" + id_order + "&id_approve=" + id_emp,
        type: "POST",
        cache: "false",
        data: $("#editOrderForm").serialize(),
        success: function(rs) {
            window.location.reload();
        }
    });
}

//---------------------------  เพิ่มการสั่งซื้อสินค้า  -----------------------//
function addToOrder(id_order) {
    $("#order_grid").modal("hide");
    load_in();
    $.ajax({
        url: "controller/orderController.php?addToOrder",
        type: "POST",
        cache: "false",
        data: $("#gridForm").serialize(),
        success: function(rs) {
            load_out();
            var rs = $.trim(rs);
            var arr = rs.split(' | ');
            var c = arr.length;
            if (c == 1 && arr[0] == 'success') {
                swal({ title: "สำเร็จ", text: "เพิ่มรายการเรียบร้อยแล้ว", type: "success", timer: 1000 });
                reloadOrderProduct(id_order);
            } else if (c == 2 && arr[0] == 'fail') {
                swal("ข้อผิดพลาด!!", "เพิ่มสินค้าในรายการไม่สำเร็จ กรุณาตรวจสอบ แล้วลองใหม่อีกครั้ง", "error");
                $("#order_grid").modal("show");
            } else if (c == 2 && arr[0] == 'overstock') {
                swal("ข้อผิดพลาด!!", arr[1] + " มีสินค้าคงเหลือไม่เพียงพอ กรุณาตรวจสอบแล้วสั่งใหม่อีกครั้ง", "error");
                $("#order_grid").modal("show");
            }
        }
    });
}

//---------------------------  โหลดตารางรายการสั่งสินค้าใหม่  --------------------------//
function reloadOrderProduct(id_order) {
    $.ajax({
        url: "controller/orderController.php?getOrderProductTable",
        type: "POST",
        cache: "false",
        data: { "id_order": id_order },
        success: function(rs) {
            var rs = $.trim(rs);
            if (rs != 'fail' && rs != '') {
                var source = $("#orderProductTemplate").html();
                var data = $.parseJSON(rs);
                var output = $("#orderProductTable");
                render(source, data, output);
            }
        }
    });
}


//--------------------------------  โหลดรายการสินค้าสำหรับจิ้มสั่งสินค้า  -----------------------------//
function getCategory(id) {
    var output = $("#cat-" + id);
	$('.tab-pane').removeClass('active');
	$(".menu").removeClass("active");
    if (output.html() == '') {
        load_in();
        $.ajax({
            url: "controller/orderController.php?getCategoryProductGrid",
            type: "POST",
            cache: "false",
            data: { "id_category": id },
            success: function(rs) {
                load_out();
                var rs = $.trim(rs);
                if (rs != 'no_product') {
                    output.html(rs);
                } else {
                    output.html('<center><h4>ไม่พบสินค้าในหมวดหมู่ที่เลือก</h4></center>');
                    $('.tab-pane').removeClass('active');
                    output.addClass('active');
                }
            }
        });
    }
	output.addClass('active');
}


//--------------------------------  โหลดรายการสินค้าสำหรับดูยอดคงเหลือ  -----------------------------//
function getViewCategory(id) {
    var output = $("#cat-" + id);
	$('.tab-pane').removeClass('active');
	$(".menu").removeClass("active");
    if (output.html() == '') {
        load_in();
        $.ajax({
            url: "controller/orderController.php?getCategoryGrid",
            type: "POST",
            cache: "false",
            data: { "id_category": id },
            success: function(rs) {
                load_out();
                var rs = $.trim(rs);
                if (rs != 'no_product') {
                    output.html(rs);
                } else {
                    output.html('<center><h4>ไม่พบสินค้าในหมวดหมู่ที่เลือก</h4></center>');
                }
            }
        });
    }

	output.addClass('active');
}

//----------------------------  โหลดตารางใส่จำนวนสั่งซื้อของสินค้า  --------------------//
function getProduct() {
    var st = $("#sProduct").val();
    var id_cus = $("#id_customer").val();
    var id_order = $('#id_order').val();

    if (st == '') { swal("กรุณาระบุรหัสสินค้า"); return false; }

    load_in();
    $.ajax({
        url: "controller/orderController.php?getProductGrid",
        type: "POST",
        cache: "false",
        data: {
          "product_code": st,
          "id_customer": id_cus,
          "id_order" : id_order 
        },
        success: function(dataset) {
            load_out();
            if (dataset != "") {
                var arr = dataset.split("|");
                var data = arr[0];
                var table_w = arr[1];
                var title = arr[2];
                $("#modal").css("width", table_w + "px");
                $("#modal_title").html(title);
                $("#modal_body").html(data);
                $("#order_grid").modal("show");
            } else {
                swal("ไม่พบสินค้า", "รหัสสินค้าไม่ถูกต้อง หรือ ไม่มีสินค้านี้ในระบบ กรุณาตรวจสอบ", "error");
            }
        }
    });
}

//----------------------  ลบรายการสินค้าในหน้าเพิ่ม  ------------------//
function deleteRow(id, ref) {
    swal({
        title: 'ต้องการลบ ?',
        text: 'คุณแน่ใจว่าต้องการลบ ' + ref + ' ?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6855',
        confirmButtonText: 'ใช่ ฉันต้องการลบ',
        cancelButtonText: 'ยกเลิก',
        closeOnConfirm: false
    }, function() {
        $.ajax({
            url: "controller/orderController.php?deleteOrderDetail",
            type: "POST",
            cache: "false",
            data: { "id_order_detail": id },
            success: function(rs) {
                var rs = $.trim(rs);
                if (rs == 'success') {
                    swal({ title: "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });
                    var id_order = $("#id_order").val();
                    reloadOrderProduct(id_order);
                } else {
                    swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
                }
            }
        });
    });
}

//------------------------------  ลบรายการสินค้าในหน้า แสดงรายละเอียด  ---------------------//
function deleteItem(id, ref) {
    swal({
        title: 'ต้องการลบ ?',
        text: 'คุณแน่ใจว่าต้องการลบ ' + ref + ' ?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DD6855',
        confirmButtonText: 'ใช่ ฉันต้องการลบ',
        cancelButtonText: 'ยกเลิก',
        closeOnConfirm: false
    }, function() {
        $.ajax({
            url: "controller/orderController.php?deleteOrderDetail",
            type: "POST",
            cache: "false",
            data: { "id_order_detail": id },
            success: function(rs) {
                var rs = $.trim(rs);
                if (rs == 'success') {
                    swal({ title: "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });
                    $("#row_" + id).remove();
                    recalOrder();
                } else {
                    swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
                }
            }
        });
    });
}

function recalOrder() {
    var id_order = $("#id_order").val();
    $.ajax({
        url: "controller/orderController.php?getBillSummary",
        type: "POST",
        cache: "false",
        data: { "id_order": id_order },
        success: function(rs) {
            var rs = $.trim(rs);
            var ar = rs.split(' | ');
            var total_price = addCommas(ar[0]);
            var total_disc = addCommas(ar[1]);
            var net = addCommas(ar[2]);
            $("#total_price").html("<b>" + total_price + "</b>");
            $("#total_disc").html("<b>" + total_disc + "</b>");
            $("#net").html("<b>" + net + "</b>");
        }
    });
}

$("#doc_date").datepicker({ dateFormat: 'dd-mm-yy' });

function newOrder() {
    var date = $("#doc_date").val();
    var cus_name = $("#customer_name").val();
    var id_cus = $("#id_customer").val();
    if (date == '') { swal('วันที่ไม่ถูกต้อง'); return false; }
    if (id_cus == '' || cus_name == '') { swal('ชื่อลูกค้าไม่ถูกต้อง'); return false; }
    load_in();
    $.ajax({
        url: "controller/orderController.php?addNewOrder",
        type: "POST",
        cache: false,
        data: $("#addForm").serialize(),
        success: function(rs) {
            load_out();
            var rs = $.trim(rs);
            if (rs == 'fail') {
                swal("ข้อผิดพลาด!!", "เพิ่มออเดอร์ไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
            } else {
                window.location.href = 'index.php?content=order&add&id_order=' + rs;
            }
        }
    });
}


function editOrder() {
    $("#doc_date").removeAttr("disabled");
    $("#customer_name").removeAttr("disabled");
    $("#payment").removeAttr("disabled");
    $("#online").removeAttr("disabled");
    $("#comment").removeAttr("disabled");
    $("#btnEdit").css("display", "none");
    $("#btnUpdate").css("display", "");
}


function updateOrder(id) {
    var date = $("#doc_date").val();
    var cus_name = $("#customer_name").val();
    var id_cus = $("#id_customer").val();
    if (date == '') { swal('วันที่ไม่ถูกต้อง'); return false; }
    if (id_cus == '' || cus_name == '') { swal('ชื่อลูกค้าไม่ถูกต้อง'); return false; }
    load_in();
    $.ajax({
        url: "controller/orderController.php?updateEditOrderHeader",
        type: "POST",
        cache: "false",
        data: $("#addForm").serialize(),
        success: function(rs) {
            load_out();
            var rs = $.trim(rs);
            if (rs == 'success') {
                swal({ title: "เรียบร้อย", text: "ปรับปรุงข้อมูลเรียบร้อยแล้ว", type: "success", timer: 1000 });
                updated();
            }
        }
    });
}


function updated() {
    $("#doc_date").attr("disabled", "disabled");
    $("#customer_name").attr("disabled", "disabeld");
    $("#payment").attr("disabled", "disabled");
    $("#online").attr("disabled", "disabled");
    $("#comment").attr("disabled", "disabled");
    $("#btnUpdate").css("display", "none");
    $("#btnEdit").css("display", "");
}


//-----------  Save Order ------------//
function save(id) {
    load_in();
    $.ajax({
        url: "controller/orderController.php?saveOrder",
        type: "POST",
        cache: "false",
        data: { "id_order": id },
        success: function(rs) {
            load_out();
            var rs = $.trim(rs);
            if (rs == 'success') {
                swal({ title: "เรียบร้อย", text: "บันทึกออเดอร์เรียบร้อยแล้ว", type: "success", timer: 1000 });
                setTimeout(function() { goBack(); }, 1500);
            } else {
                swal("ข้อผิดพลาด!!", "บันทึกออเดอร์ไม่สำเร็จ กรุณาลองใหม่อีกครั้งภายหลัง", "error");
            }
        }
    });
}

function goBack() {
    window.location.href = 'index.php?content=order';
}


function addNew() {
    $.ajax({
        url: "controller/orderController.php?checkOrderNotSave",
        type: "GET",
        cache: "false",
        success: function(rs) {
            var rs = $.trim(rs);
            if (rs == 'ok') {
                window.location.href = 'index.php?content=order&add';
            } else {
                window.location.href = 'index.php?content=order&add&id_order=' + rs + '&warning=ยังไม่ได้บันทึกออเดอร์นี้';
            }
        }
    });
}

function addNewOnline()

{
    $.ajax({
        url: "controller/orderController.php?checkOrderNotSave",
        type: "GET",
        cache: "false",
        success: function(rs) {
            var rs = $.trim(rs);
            if (rs == 'ok') {
                window.location.href = 'index.php?content=order&add&online';
            } else {
                window.location.href = 'index.php?content=order&add&id_order=' + rs + '&warning=ยังไม่ได้บันทึกออเดอร์นี้';
            }
        }
    });
}

function viewOrder(id) {
    window.location.href = 'index.php?content=order&edit&view_detail&id_order=' + id;
}

function getEdit(id) {
    window.location.href = 'index.php?content=order&add&id_order=' + id;
}


function viewStock() {
    window.location.href = 'index.php?content=order&view_stock';
}


$("#sProduct").autocomplete({
    source: "controller/autoComplete.php?product_code",
    autoFocus: true
});


$("#sProduct").keyup(function(e) {
    if (e.keyCode == 13) {
        if ($(this).val() != '') {
            getProduct();
        }
    }
});


$("#customer_name").autocomplete({
    source: "controller/orderController.php?customer_name",
    autoFocus: true,
    close: function(event, ui) {
        var data = $("#customer_name").val();
        var arr = data.split(' | ');
        var name = arr[1];
        var id_customer = arr[2];
        if (!isNaN(parseInt(id_customer))) {
            $("#id_customer").val(id_customer);
            $(this).val(name);
        } else {
            $("#id_customer").val('');
            $(this).val('');
        }
    }
});

function getSearch() {
    $("#searchForm").submit();
}

function clearFilter() {
    $.ajax({
        url: "controller/orderController.php?clearFilter",
        success: function(rs) {
            goBack();
        }
    });
}

$("#s_ref, #s_cus, #s_emp").keyup(function(e) {
    if (e.keyCode == 13) {
        getSearch();
    }
});

function addDeliveryFee() {
    $("#deliveryFee").css("display", "inline");
    $("#btn-add-fee").css("display", "none");
    $("#btn-update-fee").css("display", "inline");
    $("#deliveryFee").focus();
}

function editDeliveryFee() {
    $("#deliveryFee").removeAttr("disabled");
    $("#btn-edit-fee").css("display", "none");
    $("#btn-update-fee").css("display", "inline");
    $("#deliveryFee").focus();
}


function updateDeliveryFee(id_order) {
    var fee = parseFloat($("#deliveryFee").val());
    if (isNaN(fee)) { swal("ค่าจัดส่งไม่ถูกต้อง"); return false; }
    load_in();
    $.ajax({
        url: "controller/orderController.php?updateDeliveryFee",
        type: "POST",
        cache: "false",
        data: { "id_order": id_order, "fee": fee },
        success: function(rs) {
            load_out();
            var rs = $.trim(rs);
            if (rs == 'success') {
                swal({ title: "สำเร็จ", text: "", timer: 1000, type: "success" });
                setTimeout(function() { window.location.reload(); }, 1500);
            } else {
                swal("ข้อผิดพลาด!!", "แก้ไขค่าจัดส่งไม่สำเร็จ", "error");
            }
        }
    });
}

$("#deliveryFee").keyup(function(e) {
    if (e.keyCode == 13) {
        $("#btn-update-fee").click();
    }
});
//-----------------  End New code ----------------/

function getData(id_product) {
    var id_cus = $("#id_customer").val();
    var id_order = $('#id_order').val();
    $.ajax({
        url: "controller/orderController.php?getData&id_product=" + id_product + "&id_customer=" + id_cus +"&id_order="+id_order,
        type: "GET",
        cache: false,
        success: function(dataset) {
            if (dataset != "") {
                var arr = dataset.split("|");
                var data = arr[0];
                var table_w = arr[1];
                var title = arr[2];
                $("#modal").css("width", table_w + "px");
                $("#modal_title").html(title);
                $("#modal_body").html(data);
                $("#order_grid").modal('show');
            } else {
                alert("NO DATA");
            }
        }
    });
}

function view_data(id_product) {
    $.ajax({
        url: "controller/orderController.php?view_stock_data&id_product=" + id_product,
        type: "GET",
        cache: false,
        success: function(dataset) {
            if (dataset != "") {
                var arr = dataset.split("|");
                var data = arr[0];
                var table_w = arr[1];
                var title = arr[2];
                $("#modal").css("width", table_w + "px");
                $("#modal_title").html(title);
                $("#modal_body").html(data);
                $("#order_grid").modal('show');
            } else {
                swal("NO DATA");
            }
        }
    });
}

$("#edit_reduction").click(function(e) {
    $(".reduction").css("display", "none");

    $(".input_reduction").css("display", "");
    $("#edit_reduction").css("display", "none");
    $("#save_reduction").css("display", "");
});


function check_order(id) {
    var wid = $(document).width();
    var left = (wid - 1100) / 2;
    window.open("index.php?content=bill&id_order=" + id + "&check_order&view_detail=y&nomenu", "_blank", "width=1100, height=800, left=" + left + ", location=no, scrollbars=yes");
}

function print_order(id) {
    var wid = $(document).width();
    var left = (wid - 900) / 2;
    window.open("controller/orderController.php?print_order&id_order=" + id, "_blank", "width=900, height=1000, left=" + left + ", location=no, scrollbars=yes");
}

function getSummary() {
    $("#orderSummaryTab").modal("show");
}

function toggleState(state)
{
	var value = $("#"+state).val();
	if( value == 1 ){
		$("#"+state).val(0);
		$("#btn-"+state).removeClass('btn-info');
	}else{
		$("#"+state).val(1);
		$("#btn-"+state).addClass('btn-info');
	}
	getSearch();
}
