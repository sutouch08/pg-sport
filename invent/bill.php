<?php
	$page_menu = "invent_order_bill";
	$page_name = "รายการรอเปิดบิล";
	$id_tab = 19;
	$id_profile = $_COOKIE['profile_id'];
   $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
	require "function/support_helper.php";
	require "function/sponsor_helper.php";
	require 'function/order_helper.php';
	require 'function/product_helper.php';
	require 'function/qc_helper.php';
	function get_temp_qty($id_order, $id_product_attribute)
	{
		$qty = 0;
		$qs = dbQuery("SELECT SUM(qty) AS qty FROM tbl_temp WHERE id_order = ".$id_order." AND id_product_attribute = ".$id_product_attribute);
		if(dbNumRows($qs) == 1 )
		{
			$rs = dbFetchArray($qs);
			$qty = $rs['qty'];
		}
		return $qty;
	}
	?>
<div class="container">
<!-- page place holder -->
<?php if( ! isset( $_GET['check_order'] ) ) : ?>
<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-file-text"></i>&nbsp;<?php echo $page_name; ?></h4>
  </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
       <?php if( isset( $_GET['view_detail'] ) && isset( $_GET['id_order'] ) ) : ?>
		   <button type='button' class='btn btn-warning btn-sm' onClick="goBack()"><i class="fa fa-arrow-left"></i>&nbsp; กลับ</button>
	   <?php endif; ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:15px;' />
<?php endif; ?>
<!-- End page place holder -->
<?php
///************************** แสดงรายละเอียด ****************************//
if(isset($_GET['view_detail'])&&isset($_GET['id_order'])){
	$id_employee = $_COOKIE['user_id'];
	$id_order = $_GET['id_order'];
	$bill_discount = bill_discount($id_order);
	$order = new order($id_order);
	$role = $order->role;
	$customer = new customer($order->id_customer);
	$sale = new sale($order->id_sale);
?>
<?php
			$reference	= $order->reference;
			$cus_label	= $role == 3 ? '' : (($role == 7 OR $role == 4 )? 'ผู้รับ : ' :  'ลูกค้า : ' );
			$onlineLabel	= $order->payment == 'ออนไลน์' ? ' ( '.getCustomerOnlineReference($id_order).' )' : '';
			$cus_info	= $customer->full_name;
			$em_label	= $role == 3 ? 'ผู้ยืม : ' : ( ($role == 1 OR $role == 5) ? 'พนักงานขาย : ' : 'ผู้เบิก : ');
			$em_info		= ($role == 1 OR $role == 5) ? $sale->full_name : employee_name($order->id_employee);
			$onlineEmp	= $order->payment == 'ออนไลน์' ? ' ( '.employee_name($order->id_employee).' ) ' : '';
			$user			= $role == 7 ? employee_name( get_id_user_support($id_order) ) : ( $role == 4 ? employee_name( get_id_user_sponsor($id_order) ) : employee_name( $order->id_employee ) );

?>
	  <div class='row'>
        	<div class='col-lg-2 col-sm-3'>	<strong><?php echo $reference; ?></strong></div>
            <div class="col-lg-5 col-sm-5"><strong><?php echo $cus_label . $cus_info .$onlineLabel; ?></strong></div>
            <div class="col-lg-5 col-sm-4"><strong><p class="pull-right"><?php echo $em_label . $em_info . $onlineEmp; ?></p></strong> </div>
        </div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'>
		<div class='col-lg-12'>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>วันที่สั่ง : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo thaiDate($order->date_add); ?></dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>สินค้า :&nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_product); ?></dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>จำนวน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->qc_qty()); ?></dd>  |</dt></dl>
<?php if($order->role == 7) : ?>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>ผู้ดำเนินการ : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo $user; ?></dd> </dt></dl>
<?php endif; ?>

        <p class="pull-right">
        <button type="button" class="btn btn-info btn-sm" onClick="printAddress(<?php echo $id_order; ?>, <?php echo $order->id_customer; ?>)"><i class="fa fa-file-text-o"></i> พิมพ์ใบปะหน้า</button>
        <?php if( ! isset( $_GET['check_order'] ) ) :  //------ ถ้าไม่ได้เป็นการเรียกดูข้อมูลจากหน้าออเดอร์ -----//	?>
			<?php if( $order->current_state == 9 ) : ?>
    		<button type="button" class="btn btn-sm btn-primary" onClick="printOrder(<?php echo $id_order; ?>)"><i class="fa fa-print"></i> พิมพ์</button>
            <button type="button" class="btn btn-success btn-sm" onClick="printBarcode(<?php echo $id_order; ?>)"><i class="fa fa-print"></i> พิมพ์บาร์โค้ด</button>
            <button type="button" class="btn btn-default btn-sm" onClick="printPackingList(<?php echo $id_order; ?>)"><i class="fa fa-file-text-o"></i> Picking List</button>
   			<?php endif; ?>
   			<?php if( $order->current_state == 10 && ($add OR $edit) ) : ?>
            <button type="button" class="btn btn-sm btn-primary" id="p_btn" onClick="save_iv()">เปิดบิลและตัดสต็อก</button>
    		<?php endif; ?>
    	<?php endif; ?>
    		<input type="hidden" id="id_order" value="<?php echo $id_order; ?>" />
             <?php if( $order->payment == 'ออนไลน์' ) : ?>
            	<input type="hidden" name="online" id="online" value="1" />
            <?php endif; ?>
        </p>


		</div></div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:10px;' />
        <?php if( ! isset( $_GET['check_order'] ) ) : ?>
        <?php 	if( $order->role == 1 ) : ?>
        <?php  		if( $order->current_state == 10 ) : ?>
        <div class="row">
        	<div class="col-lg-3">	<?php echo paymentLabel($id_order); ?></div>
        	<div class="col-lg-3 col-lg-offset-4">
            	<input type="text" id="bill_discount" class="form-control input-sm" placeholder="เพิ่มหรือแก้ไขส่วนลดท้ายบิล" style="text-align:center" />
            </div>
            <div class="col-lg-2">
            	<button class="btn btn-warning btn-sm btn-block" onclick="process_bill_discount(<?php echo $id_order; ?>)"><i class="fa fa-plus"></i>&nbsp; เพิ่มส่วนลดท้ายบิล</button>
            </div>
        </div>
        <hr style='border-color:#CCC; margin-top: 10px; margin-bottom:15px;' />
        <?php		endif; ?>
         <?php 	endif; ?>
         <?php endif; ?>

		<div class='row'>
        <div class='col-lg-12'>
        <table class="table table-bordered">
        <thead>
        	<th style="width:4%; text-align:center">ลำดับ</th>
            <th style="width:10%; text-align:center">บาร์โค้ด</th>
            <th style="width:30%; text-align:center">สินค้า</th>
            <th style="width:10%; text-align:center">ราคา</th>
            <th style="width:8%; text-align:center">จำนวนสั่ง</th>
            <th style="width:8%; text-align:center">จำนวนจัด</th>
            <th style="width:8%; text-align:center">จำนวนที่ได้</th>
            <th style="width:10%; text-align:center">ส่วนลด</th>
            <th style="width:10%; text-align:center">มูลค่า</th>
		</thead>
        <?php $qr = dbQuery("SELECT tbl_order_detail.*, is_visual FROM tbl_order_detail JOIN tbl_product ON tbl_order_detail.id_product = tbl_product.id_product WHERE id_order = ".$id_order); ?>
        <?php 	$n = 1; $total_amount = 0; $total_discount = 0; $full_amount = 0; $total_qty = 0; $total_valid_qty = 0; $total_temp = 0; ?>
        <?php 	while($rs = dbFetchArray($qr) ) : ?>
        <?php 		$isVisual = isVisual($rs['id_product_attribute']) == 1 ? TRUE : FALSE; ?>
        <?php 		$qty = $isVisual === TRUE ? $rs['product_qty'] : sumCheckedQty($id_order, $rs['id_product_attribute']) ; ?>
        <?php		$temp_qty = $isVisual === TRUE ? $rs['product_qty'] : get_temp_qty($id_order, $rs['id_product_attribute']); ?>
        <?php		if($rs['product_qty'] != $qty || $qty != $temp_qty){ $hilight = " color: red;"; }else{ $hilight = ""; } ?>
         <?php 			$p_name = $rs['product_reference']. " : ". $rs['product_name']; ?>
         <?php 			$p_name = substr($p_name, 0, 100); ?>
		<tr style="font-size:12px;<?php echo $hilight; ?>">
        	<td align="center"><?php echo $n; ?></td>
            <td align="center"><?php echo $rs['barcode']; ?></td>
            <td><?php echo $p_name; ?></td>
            <td align="center"><?php echo number_format($rs['product_price'],2); ?></td>
            <td align="center"><?php echo number_format($rs['product_qty']); ?></td>
            <td align="center"><?php echo number_format($temp_qty); ?></td>
            <td align="center"><?php echo number_format($qty); ?></td>
            <td align="center">
			<?php
				if($rs['reduction_percent'] != 0.00){
						$amount = $qty * $rs['product_price'];
						$discount = $rs['reduction_percent']." %";
						$discount_amount = $qty * ($rs['product_price'] * ($rs['reduction_percent']/100));
					}else if($rs['reduction_amount'] != 0.00){
						$amount = $qty * $rs['product_price'];
						$discount = ($qty * $rs['reduction_amount']) . " ฿";
						$discount_amount = $qty * $rs['reduction_amount'];
					}else{
						$discount = "0.00 %";
						$discount_amount = 0;
						$amount = $qty * $rs['product_price'];
					}

				echo $discount;
			?>
            </td>
            <td align="right"><?php echo number_format(($amount - $discount_amount),2); ?></td>
        </tr>
        <?php 	$total_amount += $amount; $total_discount += $discount_amount; $full_amount += $amount;  $total_qty += $rs['product_qty']; $total_valid_qty += $qty; $total_temp += $temp_qty; $n++; ?>
        <?php 	endwhile; ?>
        <tr>
        	<td colspan="4" align="right">รวม</td>
            <td align="center"><?php echo number_format($total_qty); ?></td>
            <td align="center"><?php echo number_format($total_temp); ?></td>
            <td align="center"><?php echo number_format($total_valid_qty); ?></td>
            <td >ส่วนลดท้ายบิล</td>
            <td align="right"><?php echo number_format($bill_discount, 2); ?></td>
        </tr>
        <tr >
        	<td colspan="4" rowspan="3"><strong>หมายเหตุ : </strong><?php echo $order->comment; ?></td>
            <td colspan="3"><strong>ราคารวม</strong></td><td colspan="2" align="right"><?php echo number_format($full_amount,2); ?></td>
        </tr>
        <tr>
        	<td colspan="3"><strong>ส่วนลด</strong></td><td colspan="2" align="right"><?php echo number_format($total_discount + $bill_discount, 2); ?></td>
        </tr>
         <tr>
        	<td colspan="3"><strong>ยอดเงินสุทธิ</strong></td><td colspan="2" align="right"><?php echo number_format($full_amount - ($total_discount + $bill_discount) ,2); ?></td>
        </tr>
        </table>
        </div>
        </div>
<?php if( $order->current_state == 10 ) : ?>
<script>	var interv = setInterval(function(){ checkBill(); }, 2000);  </script>
<?php endif; ?>
<?php

}else{
echo"
<div class='row'>
<div class='col-sm-12'>
	<table class='table table-striped'>
    	<thead style='color:#FFF; background-color:#48CFAD;'>
        	<th style='width:5%; text-align:center;'>ลำดับ</th>
			<th style='width:10%;'>เลขที่อ้างอิง</th><th style='width:20%;'>ลูกค้า</th>
            <th style='width:10%; text-align:center;'>ยอดเงิน</th>
			<th style='width:15%; text-align:center;'>เงื่อนไข</th>
			<th style='width:10%; text-align:center;'>สถานะ</th>
			<th style='width:10%;'>พนักงาน</th>
			<th style='width:10%; text-align:center;'>วันที่เพิ่ม</th>
			<th style='width:10%; text-align:center;'>วันที่ปรับปรุง</th>
        </thead>";
		$result = dbQuery("SELECT id_order,reference,date_add,date_upd,payment,id_customer,id_employee,current_state FROM tbl_order WHERE current_state = 10  ORDER BY id_order DESC ");
		$i=0;
		$n = 1;
		$row = dbNumRows($result);
		if($row>0){
		while($i<$row){
			list($id_order,$reference,$date_add,$date_upd,$payment,$id_customer,$id_employee,$current_state) = dbFetchArray($result);
			list($amount) = dbFetchArray(dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail WHERE id_order = '$id_order'"));
			list($cus_first_name,$cus_last_name) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_customer WHERE id_customer = '$id_customer'"));
			list($em_first_name,$em_last_name) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_employee WHERE id_employee = '$id_employee'"));
			list($status) = dbFetchArray(dbQuery("SELECT state_name FROM tbl_order_state WHERE id_order_state = '$current_state'"));
			$customer_name = "$cus_first_name $cus_last_name";
			$employee_name = "$em_first_name $em_last_name";
	echo"<tr style='font-size:12px;'>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">$n</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">$reference</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">$customer_name</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">"; echo number_format($amount)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">$payment</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">$status</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">$employee_name</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">"; echo thaiDate($date_add)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=bill&id_order=$id_order&view_detail=y'\">"; echo thaiDate($date_upd)."</td>
			</tr>";
			$i++; $n++;
		}
		}else if($row==0){
			echo"<tr><td colspan='9' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;ไม่มีรายการในช่วงนี้</h3></td></tr>";
		}
		echo"</table>";
}
?>
<div class='modal fade' id='modal_approve' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width: 350px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site text-center' > รหัสลับผู้มีอำนาจอนุมัติส่วนลด</h4>
			</div>
			<input type='hidden' id='id_employee' name='id_employee' value="<?php echo $_COOKIE['user_id']; ?>"  />
			<div class='modal-body'>
				<div class='form-group login-password'>
					<input name='password' id='bill_password' class='form-control input'  size='20' placeholder='รหัสลับ' type='password' required='required' autofocus="autofocus">
				</div>
				<input class='btn  btn-block btn-lg btn-primary' value='ตกลง' type='button' onclick='valid_password()' >
			</div>
			<p style='text-align:center; color:red;' id='bill_message'></p>
			<div class='modal-footer'>
			</div>
		</div>
	</div>
</div>


	<div class='modal fade' id='infoModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' style="width:500px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <input type="hidden" id="id_customer"/><input type="hidden" id="id_order" />
				 </div>
				 <div class='modal-body' id='info_body'>

                 </div>
				 <div class='modal-footer'>
                 	<button type="button" class="btn btn-primary btn-sm" onClick="printSelectAddress()"><i class="fa fa-print"></i> พิมพ์</button>
				 </div>
			</div>
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

<script>
$('#modal_approve').on('shown.bs.modal', function () {  $('#bill_password').focus(); });
</script>
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
</div>
</script>
<script>
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
function process_bill_discount() /// inspected discount value
{
    var discount = $("#bill_discount").val();
	if( isNaN(parseFloat(discount)) )
	{
		load_out();
		swal("รูปแบบตัวเลขส่วนลดไม่ถูกต้อง");
		return false;
	}else{
		$("#modal_approve").modal("show");
	}
}

/*function save_iv(){
	load_in();
	$("#iv_button").attr("disabled","disabled");
	var id_order = $("#id_order").val();
	var id_employee = $("#id_employee").val();
	window.location.href = "controller/billController.php?confirm_order&id_order="+id_order+"&id_employee="+id_employee;
}*/

function save_iv(){
	var id_order = $("#id_order").val();
	var id_employee = $("#id_employee").val();
	load_in();
	$.ajax({
		url:"controller/billController.php?confirm_order&id_order="+id_order+"&id_employee="+id_employee,
		type:"GET", cache:"false",
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title: 'สำเร็จ', text: 'บันทึกเอกสารเรียบร้อยแล้ว', type: 'success', timer: 1000 });
				setTimeout(function(){ window.location.reload(); }, 1200);
			}else{
				swal('ข้อผิดพลาด !', rs, 'error');
			}
		}
	});
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
				update_bill_discount(data);
			}
		}
	});
}

function checkBill()
{
	var id_order =  $("#id_order").val();
	$.ajax({
		url:'controller/billController.php?check_order_state&id_order='+id_order,
		type:'GET', cache:false,
		success: function(rs){
			if(rs != 10){
				$('#p_btn').css('display', 'none');
			}
		}
	});
}

function stopInt() { clearInterval(interv); }

function update_bill_discount(id_approve)
{
	var id_order = <?php if(isset($_GET['id_order'])){ echo $_GET['id_order']; }else{ echo "0"; } ?>;
	var discount = $("#bill_discount").val();
	$.ajax({
		url:"controller/orderController.php?insert_bill_discount", type:"POST", cache:false,
		data: { "id_order" : id_order, "id_approve" : id_approve, "discount" : discount },
		success: function(rs){
			var rs = $.trim(rs);
			load_out();
			if(rs == "success")
			{
				window.location.reload();
				load_out();
			}else{
				load_out();
				$("#modal_approve").modal("hide");
				swal("แก้ไขส่วนลดไม่สำเร็จ");
			}
		}

	});
}

function printAddress(id_order, id_customer)
{
	if( $("#online").length ){
		getOnlineAddress(id_order);
	}else{
		getAddressForm(id_order, id_customer);
	}
}

function getOnlineAddress(id_order)
{
	$.ajax({
		url:"controller/orderController.php?getOnlineAddress",
		type:"POST", cache:"false", data:{"id_order" : id_order },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'noaddress' || isNaN( parseInt(rs) ) ){
				noAddress();
			}else{
				printOnlineAddress(id_order, rs);
			}
		}
	});
}
function getAddressForm(id_order, id_customer)
{
	$.ajax({
		url:"controller/addressController.php?getAddressForm",
		type:"POST",cache: "false", data:{ "id_order" : id_order, "id_customer" : id_customer },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'no_address' ){
				noAddress();
			}else if( rs == 'no_sender' ){
				noSender();
			}else if( rs == 1 ){
				printPackingSheet(id_order, id_customer);
			}else{
				$("#id_customer").val(id_customer);
				$("#id_order").val(id_order);
				$("#info_body").html(rs);
				$("#infoModal").modal("show");
			}
		}
	});
}

function printPackingSheet(id_order, id_customer)
{
	var center = ($(document).width() - 800)/2;
	window.open("controller/addressController.php?printAddressSheet&id_order="+id_order+"&id_customer="+id_customer, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}

function printOnlineAddress(id_order, id_address)
{
	var center = ($(document).width() - 800)/2;
	window.open("controller/addressController.php?printOnlineAddressSheet&id_order="+id_order+"&id_address="+id_address, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}

function printSelectAddress()
{
	var id_order = $("#id_order").val();
	var id_cus = $("#id_customer").val();
	var id_ad =	$('input[name=id_address]:radio:checked').val();
	var id_sen	= $('input[name=id_sender]:radio:checked').val();
	if( isNaN(parseInt(id_ad)) ){ swal("กรุณาเลือกที่อยู่", "", "warning"); return false; }
	if( isNaN(parseInt(id_sen)) ){ swal("กรุณาเลือกขนส่ง", "", "warning"); return false; }
	$("#infoModal").modal('hide');
	var center = ($(document).width() - 800)/2;
	window.open("controller/addressController.php?printAddressSheet&id_order="+id_order+"&id_customer="+id_cus+"&id_address="+id_ad+"&id_sender="+id_sen, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}
function noAddress()
{
	swal("ข้อผิดพลาด", "ไม่พบที่อยู่ของลูกค้า กรุณาตรวจสอบว่าลูกค้ามีที่อยู่ในระบบแล้วหรือยัง", "warning");
}
function noSender()
{
	swal("ไม่พบผู้จัดส่ง", "ไม่พบรายชื่อผู้จัดส่ง กรุณาตรวจสอบว่าลูกค้ามีการกำหนดชื่อผู้จัดส่งในระบบแล้วหรือยัง", "warning");
}


function printOrder(id)
{
	var wid = $(document).width();
	var left = (wid - 900) /2;
	window.open("controller/billController.php?print_order&id_order="+id, "_blank", "width=900, height=1000, left="+left+", location=no, scrollbars=yes");
}

function printBarcode(id_order)
{
	var center = ($(document).width() - 800)/2;
	window.open("controller/billController.php?print_order_barcode&id_order="+id_order, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}

function printPackingList(id_order)
{
	window.open("index.php?content=order_closed&print_packing_list&id_order="+id_order, "_blank");
}

function goBack()
{
	window.location.href = "index.php?content=bill";
}

</script>
