<?php
	$page_name 	= "ตรวจสอบยอดชำระเงิน";
	$id_tab 			= 62;
	$id_profile 		= $_COOKIE['profile_id'];
    $pm 				= checkAccess($id_profile, $id_tab);
	$view 			= $pm['view'];
	$add 				= $pm['add'];
	$edit 				= $pm['edit'];
	$delete 			= $pm['delete'];
	accessDeny($view);
	include 'function/bank_helper.php';
	include 'function/order_helper.php';
	$qs = dbQuery("SELECT * FROM tbl_payment WHERE valid = 0");
?>
<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col">
    	<h4 class="title"><i class="fa fa-exclamation-triangle"></i>&nbsp;<?php echo $page_name; ?></h4>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        	<button type="button" class="btn btn-sm btn-primary" onClick="reloadOrderTable()"><i class="fa fa-refresh"></i> โหลดรายการ</button>
        </p>
    </div>
</div><!-- / row -->

<hr style="margin-bottom:15px;" />

<div class="row">
	<div class="col-sm-12">
	<table class="table" style="border:solid 1px #ccc;">
            <thead>
            	<tr style="font-size:11px;">
                <th style='width:10%;'>เลขที่อ้างอิง</th>
                <th style='width:12%;'>ลูกค้า</th>
                <th style='width:8%; text-align:center;'>ค่าสินค้า</th>
                <th style='width:8%; text-align:center;'>ค่าจัดส่ง</th>
                <th style='width:8%; text-align:center;'>อื่นๆ</th>
                <th style='width:8%; text-align:center;'>ยอดชำระ</th>
                <th style='width:8%; text-align:center;'>ยอดโอน</th>
                <th style='width:10%; text-align:center;'>ธนาคาร</th>
                <th style='width:10%; text-align:center;'>เลขที่บัญชี</th>
                <th style='width:12%; text-align:center;'>เวลาโอน</th>
                <th style='text-align:center;'></th>
                </tr>
            </thead>
        <tbody id="orderTable">   </tbody>
    </table>
    </div>
</div>

<div class='modal fade' id='confirmModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style="width:350px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
            </div>
            <div class='modal-body' id="detailBody">
            	    	
            </div>
            <div class='modal-footer'>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' id='imageModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style="width:500px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'><i class="fa fa-times"></i></button>
            </div>
            <div class='modal-body' id="imageBody">
            	    	
            </div>
            <div class='modal-footer'>
            </div>
        </div>
    </div>
</div>

</div><!--/ container -->

<script id="detailTemplate" type="text/x-handlebars-template">
<div class="row">
	<div class="col-sm-12 text-center">ข้อมูลการชำระเงิน</div>
</div>
<hr/>
<div class="row">
	<div class="col-sm-4 label-left">ยอดที่ต้องชำระ :</div><div class="col-sm-8">{{ orderAmount }}</div>
	<div class="col-sm-4 label-left">ยอดโอนชำระ : </div><div class="col-sm-8"><span style="font-weight:bold; color:#E9573F;">฿ {{ payAmount }}</span></div>
	<div class="col-sm-4 label-left">วันที่โอน : </div><div class="col-sm-8">{{ payDate }}</div>
	<div class="col-sm-4 label-left">ธนาคาร : </div><div class="col-sm-8">{{ bankName }}</div>
	<div class="col-sm-4 label-left">สาขา : </div><div class="col-sm-8">{{ branch }}</div>
	<div class="col-sm-4 label-left">เลขที่บัญชี : </div><div class="col-sm-8"><span style="font-weight:bold; color:#E9573F;">{{ accNo }}</span></div>
	<div class="col-sm-4 label-left">ชื่อบัญชี : </div><div class="col-sm-8">{{ accName }}</div>
	{{#if imageUrl}}
		<div class="col-sm-12 top-row top-col text-center"><a href="javascript:void(0)" onClick="viewImage('{{ imageUrl }}')">รูปสลิปแนบ <i class="fa fa-paperclip fa-rotate-90"></i></a> </div>
	{{else}}
		<div class="col-sm-12 top-row top-col text-center">---  ไม่พบไฟล์แนบ  ---</div>
	{{/if}}
	<div class="col-sm-12 top-col"><button type="button" class="btn btn-warning btn-block" onClick="confirmPayment({{ id }})"><i class="fa fa-check-circle"></i> ยืนยันการชำระเงิน</button></div>
</div>   
</script>
<script id="orderTableTemplate" type="text/x-handlebars-template">
{{#each this}}
<tr id="{{ id }}" style="font-size:12px;">
<td> {{ reference }}</td>
<td>{{ customer }}</td>
<td align="center">{{ orderAmount }}</td>
<td align="center">{{ deliveryAmount }}</td>
<td align="center">{{ serviceAmount }}</td>
<td align="center">{{ totalAmount }}</td>
<td align="center">{{ payAmount }}</td>
<td align="center">{{ bankName }}</td>
<td align="center">{{ accNo }}</td>
<td align="center">{{ payDate }}</td>
<td align="center">
	<button type="button" class="btn btn-xs btn-warning" onClick="viewDetail({{ id }})"><i class="fa fa-eye"></i></button>
	<button type="button" class="btn btn-xs btn-danger" onClick="removePayment({{ id }})"><i class="fa fa-trash"></i></button>
 </td>
</tr>
{{/each}}
</script>
<script>
$(document).ready(function(e) {
    reloadOrderTable();
});

setInterval(function(){ reloadOrderTable(); }, 1000*60);

function removePayment(id_order)
{
	swal({
		title: 'ต้องการลบ ?',
		text: 'คุณแน่ใจว่าต้องการลบรายการนี้ ?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: "controller/paymentController.php?removePayment",
				type:"POST", cache:"false", data:{ "id_order" : id_order },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title : "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });	
						$("#"+id_order).remove();
					}else{
						swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ หรือ มีการยืนยันการชำระเงินแล้ว", "error");	
					}
				}
			});
		});	
}

function confirmPayment(id_order)
{
	$("#confirmModal").modal('hide');
	load_in();
	$.ajax({
		url:"controller/paymentController.php?confirmPayment",
		type:"POST", cache:"false", data:{ "id_order" : id_order },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title : 'เรียบร้อย', text: '', timer: 1000, type: 'success' });
				$("#"+id_order).remove();
			}else{
				swal("ข้อผิดพลาด", "ยืนยันการชำระเงินไม่สำเร็จ", "error");
			}
		}
	});
}


function viewImage(imageUrl)
{
	var image = '<img src="'+imageUrl+'" width="100%" />';
	$("#imageBody").html(image);
	$("#imageModal").modal('show');
}


function viewDetail(id_order)
{
	load_in();
	$.ajax({
		url:"controller/paymentController.php?getPaymentDetail",
		type:"POST", cache:"false", data:{ "id_order" : id_order },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'fail' ){
				swal('ข้อผิดพลาด', 'ไม่พบข้อมูล หรือ การชำระเงินถูกยืนยันไปแล้ว', 'error');
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
function reloadOrderTable()
{
	load_in();
	$.ajax({
		url:"controller/paymentController.php?getOrderTable",
		type:"GET", cache: false, success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs != 'fail' )
			{
				var source 	= $("#orderTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#orderTable");
				render(source, data, output);	
			}else{
				$("#orderTable").html('<tr><td colspan="9" align="center"><strong>ไม่พบรายการรอตรวจสอบ</strong></td></tr>');	
			}
		}
	});
}
</script>
