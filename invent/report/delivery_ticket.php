<?php 
	$page_name = "พิมพ์ตั๋วจัดส่ง";
	include 'function/order_helper.php';
?>
<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-sm-8" style="margin-top:10px;"><h4 class="title"><i class="fa fa-ticket"></i>&nbsp; <?php echo $page_name; ?></h4></div>
    <div class="col-sm-4">
    	<p class="pull-right" style="margin-bottom:0px;">
        	<button type="button" class="btn btn-success btn-sm" onClick="printSelected()"><i class="fa fa-print"></i>&nbsp; พิมพ์</button>
         </p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<?php 
	$s_cus 	= isset( $_POST['s_cus'] ) ? $_POST['s_cus'] : ( getCookie('s_cus') ? getCookie('s_cus') : '' );
	$s_ref	= isset( $_POST['s_ref'] ) ? $_POST['s_ref'] : ( getCookie('s_ref') ? getCookie('s_ref') : '' );
	$range	= isset( $_POST['range'] ) ? $_POST['range'] : ( getCookie('range') ? getCookie('range') : 0 );  /// 0 = all , 1 = billed, 2 = not bill
	$from		= isset( $_POST['from'] ) ? $_POST['from'] : ( getCookie('from') ? getCookie('from') : '');
	$to	 	= isset( $_POST['to'] ) ? $_POST['to'] : ( getCookie('to') ? getCookie('to') : '' );
	$all		= $range == 0 ? 'btn-primary' : '';
	$billed		= $range == 1 ? 'btn-primary' : '';
	$notBilled	= $range == 2 ? 'btn-primary' : '';
	$paginator 	= new paginator();
	$get_rows = isset( $_POST['get_rows'] ) ? $_POST['get_rows'] : ( getCookie('get_rows') ? getCookie('get_rows') : 50);	
?>
<form id="searchForm" method="post">
<div class="row">
	<div class="col-lg-2 col-sm-2" style="padding-right:5px;">
    	<input type="text" class="form-control input-sm" id="s_cus" name="s_cus" placeholder="ค้นหาชื่อลูกค้า" value="<?php echo $s_cus; ?>" />
    </div>
    <div class="col-lg-2 col-sm-2 padding-5">
    	<input type="text" class="form-control input-sm" id="s_ref" name="s_ref" placeholder="ค้นหาเลขที่เอกสาร" value="<?php echo $s_ref; ?>" />
    </div>
    <div class="col-lg-2 col-sm-2 padding-5">
    	<input type="text" class="form-control input-sm input-discount text-center" id="from" name="from" placeholder="เริ่มต้น" value="<?php echo $from; ?>" />
    	<input type="text" class="form-control input-sm input-unit text-center" id="to" name="to" placeholder="สิ้นสุด" value="<?php echo $to; ?>" />
    </div>
    <div class="col-lg-3 col-sm-3 padding-5">
    	<div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-sm <?php echo $all; ?>" id="btn-all" onClick="allRange()" style="width:33%;">ทั้งหมด</button>
            <button type="button" class="btn btn-sm <?php echo $billed; ?>" id="btn-bill" onClick="billRange()" style="width:33%;">เปิดบิลแล้ว</button>
            <button type="button" class="btn btn-sm <?php echo $notBilled; ?>" id="btn-not-bill" onClick="notBillRange()" style="width:34%;">ยังไม่เปิดบิล</button>
        </div>
        <input type="hidden" name="range" id="range" value="<?php echo $range; ?>" />
    </div>
    <div class="col-lg-1 col-sm-1 padding-5">
    	<button type="button" class="btn btn-sm btn-default btn-block" onClick="getSearch()"><i class="fa fa-search"></i> ใช้ตัวกรอง</button>
    </div>
    <div class="col-lg-1 col-sm-1 padding-5">
    	<button type="button" class="btn btn-sm btn-warning btn-block" onClick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
    </div>
</div><!--/ Row -->
</form>
<hr/>
<?php ?>
<?php 
	//--------------------  เงื่อนไข ------------------//
	$where = "WHERE order_status = 1 AND role IN(1, 4, 5, 7) ";
		if( $s_ref != '' )
		{
			createCookie('s_ref', $s_ref);
			$where .= "AND reference LIKE '%".$s_ref."%' ";	
		}
		if( $s_cus != '' )
		{
			createCookie('s_cus', $s_cus);
			$in = customer_in($s_cus);
			if( $in !== FALSE )
			{
				$where .= "AND id_customer IN(".$in.") ";
			}
		}
		
		createCookie('range', $range);
		$where .= $range == 1 ? "AND current_state = 9 " : ( $range == 2 ? "AND current_state NOT IN(8, 9) " : '');	
		if( $from != '' )
		{
			createCookie('from', $from);
			if( $to != '' ){ createCookie('to', $to); }
			$to	= $to == '' ? toDate($from) : toDate($to);
			$from = fromDate($from);		
			$where .= "AND ( date_add BETWEEN '".$from."' AND '".$to."' ) ";	
		}
	$where .= "ORDER BY date_add DESC";	
?>


<div class="row">
	<div class="col-lg-12" id="rs" style="padding-bottom:20px;">
		<table class='table'>
            <thead style='color:#FFF; background-color:#48CFAD; font-size:12px;'>
                <th style='width:7%; text-align:center;'><label style="margin:0px;"><input type="checkbox" id="checkAll" /> ทั้งหมด</label></th>
                <th style='width:10%;'>เลขที่อ้างอิง</th>
                <th style='width:30%;'>ลูกค้า</th>
                <th style='width:10%;'>พนักงาน</th>
                <th style='width:10%; text-align:center;'>ยอดเงิน</th>
                <th style='width:15%; text-align:center;'>การชำระเงิน</th>
                <th style='width:10%; text-align:center;'>สถานะ</th>
                <th style='width:10%; text-align:center;'>วันที่เพิ่ม</th>
            </thead>
<?php 	$paginator->Per_Page("tbl_order",$where,$get_rows);	?>
<?php	$paginator->display($get_rows,"index.php?content=delivery_ticket");	?>
<?php 	$qs = dbQuery("SELECT * FROM tbl_order ".$where." LIMIT ".$paginator->Page_Start." , ".$paginator->Per_Page);		?>
<?php	if( dbNumRows($qs) > 0) :		?>
<?php 		while( $rs = dbFetchArray($qs) ) :			?>
<?php			$id = $rs['id_order'];		?>
<?php			$order = new order($id);		?>
			<tr style='color:#FFF; background-color:<?php echo state_color($order->current_state); ?>; font-size:12px;'>
				<td align='center'>
                	<input type="checkbox" class="se" name="order[<?php echo $id; ?>]" id="order_<?php echo $id; ?>" value="<?php echo $id; ?>" />
                </td>
				<td><label for="order_<?php echo $id; ?>"><?php echo $order->reference; ?></label></td>
				<td><label for="order_<?php echo $id; ?>"><?php echo customer_name($order->id_customer); ?></label></td>
				<td><label for="order_<?php echo $id; ?>"><?php echo employee_name($order->id_employee); ?></label></td>
				<td align='center'><label for="order_<?php echo $id; ?>"><?php echo number_format(orderAmount($id)); ?></label></td>
				<td align='center'><label for="order_<?php echo $id; ?>"><?php echo $order->payment; ?></label></td>
				<td align='center'><label for="order_<?php echo $id; ?>"><?php echo $order->current_state_name; ?></label></td>
				<td align='center'><label for="order_<?php echo $id; ?>"><?php echo thaiDate($order->date_add); ?></label></td>
			</tr>
<?php	endwhile; ?>		
<?php else : ?>
			<tr><td colspan='8' align='center'><h4>-----  ไม่มีรายการในช่วงนี้  ----- </h4></td></tr>
<?php endif; ?>		
		</table>
<?php 	echo $paginator->display_pages(); ?>
    </div>
</div>
</div><!---/ Container --->

<script>
function printSelected()
{
	var order = $("input:checked").serialize();
	var wid = $(document).width();
	var left = (wid - 900) /2;
	window.open("report/reportController/deliveryReportController.php?getDeliveryTicket&"+order, "_blank", "width=900, height=1000, left="+left+", location=no, scrollbars=yes");	
}

function getSearch()
{
	$("#searchForm").submit();	
}

function clearFilter()
{
	$.get("report/reportController/deliveryReportController.php?clearFilter", function(){ goBack(); });	
}


function allRange()
{
	$("#range").val(0);
	$("#btn-all").addClass('btn-primary');
	$("#btn-bill").removeClass('btn-primary');
	$("#btn-not-bill").removeClass('btn-primary');
	getSearch();
}

function billRange()
{
	$("#range").val(1);
	$("#btn-all").removeClass('btn-primary');
	$("#btn-not-bill").removeClass('btn-primary');
	$("#btn-bill").addClass('btn-primary');
	getSearch();
}

function notBillRange()
{
	$("#range").val(2);
	$("#btn-all").removeClass('btn-primary');
	$("#btn-bill").removeClass('btn-primary');
	$("#btn-not-bill").addClass('btn-primary');
	getSearch();
}


$("#from").datepicker({ 
	dateFormat: 'dd-mm-yy', 
	onClose: function( sd ){ 
		$("#to").datepicker('option', 'minDate', sd);
		if( $(this).val() != '' && $("#to").val() == '' ){
			$("#to").focus();
		}
	}
});

$("#to").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function( sd ){
		$("#from").datepicker("option", "maxDate", sd);
		if( $(this).val() != '' && $("#from").val() == '' ){
			$("#from").focus();
		}
	}
});


$('input[type="text"]').keyup(function(e) {
    if( e.keyCode == 13 ){
		getSearch();
	}
});
function goBack()
{
	window.location.href = "index.php?content=delivery_ticket";	
}
$("#checkAll").change(function(e) {
    if( $(this).is(" :checked") )
	{
		$(".se").prop("checked", true);	
	}else{
		$(".se").prop("checked", false);
	}
});
</script>



