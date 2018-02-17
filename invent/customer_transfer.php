<?php 
	$page_name = "โอน/ย้าย ลูกค้า";
	$id_tab = 53;
	$id_profile = $_COOKIE['profile_id'];
   $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
	?>
<div class="container">
<div class="row" style="height:30px;">
	<div class="col-lg-6" style="margin-top:10px;">
    	<h4 class="title"><i class="fa fa-retweet"></i>&nbsp;
		<?php echo $page_name; ?></h4>
    </div>
    <div class="col-lg-6">
    	<p class="pull-right" style="margin-bottom:0px;">
    	<?php if( $edit || $add ) : ?>
        	<button type="button" class="btn btn-success btn-sm" onClick="confirm_action()"><i class="fa fa-retweet"></i>&nbsp; ยืนยันการโอน</button>
        <?php endif; ?>
        </p>
    </div>
</div>
<hr/>
<div class="row">
	<div class="col-lg-3">
    	<label>โอนจาก</label>
        <select name="from" id="from" class="form-control input-sm">
        	<option value="">เลือกพนักงานขาย</option>
            <?php $qx = dbQuery("SELECT * FROM tbl_sale"); ?>
            <?php while($rs = dbFetchArray($qx)) : ?>
            <option value="<?php echo $rs['id_sale']; ?>"><?php echo employee_name($rs['id_employee']); ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-lg-1">
    	<label style="display:block; visibility:hidden;">>>></label>
    	<center><h4 style="title"><i class="fa fa-arrow-right" style="color:green"></i></h4></center>
    </div>
    <div class="col-lg-3">
    	<label>โอนไปที่</label>
        <select name="to" id="to" class="form-control input-sm">
            <option value="">เลือกพนักงานขาย</option>
            <?php $qx = dbQuery("SELECT * FROM tbl_sale"); ?>
            <?php while($rs = dbFetchArray($qx)) : ?>
            <option value="<?php echo $rs['id_sale']; ?>"><?php echo employee_name($rs['id_employee']); ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-lg-2">
    	<label style="display:block;">เงื่อนไข</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-sm btn-primary" style="width:50%;" id="btn_all" onClick="select_all()">ทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%;" id="btn_some" onClick="select_some()">บางรายการ</button>
        </div>
    </div>
    <div class="col-lg-2">
    	<label style="display:block; visibility:hidden">xx</label>
        <button type="button" class="btn btn-default btn-sm" id="btn_list" onClick="show_list()" style="display:none;"><i class="fa fa-search"></i>&nbsp; แสดงรายชื่อลูกค้า</button>
    </div>
    <input type="hidden" id="condition" value="0" />
    
</div>
<hr/>
<div class="row">
	<form id="select_form">
   
	<div class="col-lg-12" id="rs"></div>
    </form>
</div>
</div><!---- container --->
<script>

function confirm_action()
{
	var from = $("#from").val();
	var to 	= $("#to").val();
	var rank	= $("#condition").val();
	var ck	= count_checked();
	if(from == to){ swal("พนักงานขายต้นทางต้องไม่ใช่คนเดียวกับพนักงานขายปลายทาง"); return false; }
	if( from == "" || to == ""){ swal("กรุณาเลือกพนักงานขายต้นทางและปลายทาง"); return false; }
	if(rank == 1 && ck == 0){ swal("คุณยังไม่ได้เลือกรายการใดๆ"); return false; }
	swal({
		  title: "โปรดระวัง !!!",
		  text: "<span style='color:red;'>คุณกำลังโอน/ย้าย ลูกค้าระหว่างพนักงานขาย โปรดตรวจสอบรายการและเงื่อนไขให้ถูกต้องก่อนกดยืนยัน</span>",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "ยืนยันการโอนย้าย",
		  cancelButtonText: "ยกเลิก",
		  closeOnConfirm: false,
		  html: true
		}, function(){
			load_in();
			$.ajax({
				url:"controller/customerController.php?transfer_customer&from_id="+from+"&to_id="+to+"&rank="+rank,
				type:"POST", cache:"false", data: $("#select_form").serialize(), 
				success: function(rs)
				{
					load_out();
					var rs = $.trim(rs);
					if(rs == "success")
					{
						swal({ title: "เรียบร้อย", text: "โอนย้ายลูกค้าเรียบร้อยแล้ว", timer: 1000, type: "success"});
						setTimeout(function(){ show_list() }, 2000);
						
					}else{
						swal("ไม่สำเร็จ", "ไม่สามารถโอนย้ายลูกค้าได้ กรุณาลองใหม่อีกครั้ง", "error");
					}
				}
			});						
	});
}
function count_checked()
{
	var c = 0;
	$(".ck").each(function(index, element) {
        if($(this).is(":checked"))
		{
			c++;
		}
    });
	return c;
}
function check_all()
{
	if($("#check_box").is(":checked"))
	{ 
		$(".ck").each(function(index, element) {
            $(this).prop("checked", true);
        });
	 }
	 else
	 {
		 $(".ck").each(function(index, element) {
            $(this).prop("checked", false);
        });
	 }
}

function show_list()
{
	var id_sale = $("#from").val();
	if( from == ""){ swal("กรุณาเลือกพนักงานขายต้นทาง"); return false; }
	load_in();
	$.ajax({
		url:"controller/customerController.php?get_customer_list&id_sale="	+id_sale,
		type:"GET", cache:"false", success: function(rs){
			load_out();
			$("#rs").html(rs);
		}
	});
}

$("#from").change(function(e) {
    var from 	= $(this).val();
	var to 	= $("#to").val();
	if(from == to && from != "")
	{ 
		swal("พนักงานขายต้นทางต้องไม่ใช่คนเดียวกับพนักงานขายปลายทาง");
		$("#from").val("");
		 return false; 
	}
});

$("#to").change(function(e) {
    var from 	= $("#from").val();
	var to 	= $(this).val();
	if(from == to && to != "")
	{ 
		swal("พนักงานขายต้นทางต้องไม่ใช่คนเดียวกับพนักงานขายปลายทาง");
		$("#to").val("");
		 return false; 
	}
});

function select_all()
{
	$("#condition").val(0);
	$("#btn_list").css("display","none");
	$("#rs").html("");
	$("#btn_some").removeClass('btn-primary');
	$("#btn_all").addClass("btn-primary");	
}

function select_some()
{
	$("#condition").val(1);
	$("#btn_all").removeClass("btn-primary");
	$("#btn_some").addClass("btn-primary");
	$("#btn_list").css("display", "");
	$("#rs").html("");
}
</script>