<?php
	$page_menu = "invent_order_prepare";
	$page_name = "จัดสินค้า";
	$id_tab = 17;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	$ps	= checkAccess($id_profile, 57); /// จัดสินค้าแทนคนอื่นได้หรือป่าว
	$supervisor = $ps['add'] + $ps['edit'] + $ps['delete'] > 0 ? 1 : 0;
	require_once 'function/prepare_helper.php';
	require_once 'function/qc_helper.php';
        require_once 'function/product_helper.php';
	accessDeny($view);
	?>

<div class="container">
<!-- page place holder -->
<div class="row top-row">
	<div class="col-xs-6 top-col"><h4 class="title"><i class="fa fa-inbox"></i> <?php echo $page_name; ?></h4>
  </div>
    <div class="col-xs-6">
    	<p class="pull-right top-p">
       	<?php if( isset( $_GET['id_order'] ) OR isset( $_GET['process'] ) OR isset( $_GET['view_handle'] ) ) : ?>
        <button type="button" class="btn btn-sm btn-warning" onClick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
        <?php else : ?>
        	<?php if( $edit ) : ?>
        		<button type="button" class="btn btn-sm btn-info" onClick="viewCurrent()"><i class="fa fa-play"></i> กำลังจัด</button>
			<?php endif; ?>
            <?php if( $delete ) : ?>
        		<button type="button" class="btn btn-sm btn-primary" onClick="viewHandle()"><i class="fa fa-eye"></i> ดูการจัด</button>
			<?php endif; ?>
        <?php endif; ?>
      </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php $checkstock = new checkstock(); 	?>
<?php if($checkstock->check_open() == true) : ?>
	<center><h4><i class="fa fa-exclamation-triangle"></i>  ขณะนี้มีการตรวจนับสินค้าอยู่ไม่สามารถจัดสินค้าได้</h4></center>
<?php else :  //--- if not stock checking  ?>
<?php

if( isset( $_GET['id_order'] ) ) :
	$id_order	= $_GET['id_order'];
	$id_user 	= getCookie('user_id');
	$order 		= new order($id_order);
	$cus_name	= $order->id_customer == '' ? '' : customer_name($order->id_customer);
	$barcode	= isset( $_GET['barcode_zone'] ) ? $_GET['barcode_zone'] : '';
	$id_zone		= isset( $_GET['id_zone'] ) ? $_GET['id_zone'] : '';
	$active		= $id_zone != '' ? 'disabled' : '';
	$actived		= $id_zone != '' ? '' : 'disabled';
	$autofocus	= $id_zone != '' ? 'autofocus' : '';
	$autofo		= $id_zone != '' ? '' : 'autofocus';
	$showZone	= getCookie('showZone') === FALSE ? 0 : getCookie('showZone');
	$show		= $showZone == 1 ? '' : 'style="display:none;"';
	$notShow	= $showZone == 1 ? 'style="display:none;"' : '';
?>

<div class="row">
	<div class="col-sm-2">เลขที่ : <?php echo $order->reference; ?></div>
    <div class="col-sm-4 padding-5">ลูกค้า : <?php echo $cus_name; ?></div>
    <div class="col-sm-2 padding-5">วันที่สั่ง : <?php echo thaiDate($order->date_add); ?></div>
    <div class="col-sm-2 padding-5">จำนวน : <?php echo number_format($order->total_product); ?> รายการ</div>
    <div class="col-sm-2 padding-5">จำนวนตัว : <?php echo number_format($order->total_qty); ?> ตัว</div>
    <div class="col-sm-12" style="margin-top:10px;">หมายเหตุ : <?php echo $order->comment; ?></div>
</div>
<hr />
<form id="addFrom">
<div class="row">
	<div class="col-sm-3">
    	<input type="hidden" name="id_order" id="id_order" value="<?php echo $id_order; ?>" />
    	<input type="hidden" name="id_zone" id="id_zone" value="<?php echo $id_zone; ?>" />
        <input type="hidden" name="id_user" id="id_user" value="<?php echo $id_user; ?>" />
        <div class="input-group">
        	<span class="input-group-addon">บาร์โค้ดโซน</span>
            <input type="text" id="barcode_zone" name="barcode_zone" class="form-control input-sm" value="<?php echo $barcode; ?>" autocomplete="off" <?php echo $autofo; echo $active; ?> />
        </div><!--/ input-group -->
    </div>
    <div class="col-sm-2">
    	<div class="input-group">
        	<span class="input-group-addon">จำนวน</span>
            <input type="text" name="qty" id="qty" class="form-control input-sm text-center" value="1" autocomplete="off" <?php echo $actived; ?> />
        </div><!--/ input-group -->
    </div>
    <div class="col-sm-3">
    	<div class="input-group">
        	<span class="input-group-addon">บาร์โค้ดสินค้า</span>
            <input type="text" name="barcode_item" id="barcode_item" class="form-control input-sm" autocomplete="off" <?php echo $autofocus; echo $actived; ?> />
        </div><!--/ input-group -->
    </div>
    <div class="col-sm-2">
    	<button type="button" class="btn btn-sm btn-default" id="add" onClick="doPrepare()" <?php echo $actived; ?>>ตกลง</button>
    </div>
    <div class="col-sm-2">
    	<button type='button' class='btn btn-default' id='change_zone' onclick='changeZone()' <?php echo $actived; ?> >เปลี่ยนโซน(F2)</button>
    </div>
</div><!--/ row -->
</form>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
<!----------------------  รายการที่ยังจัดไม่ครบ  ----------------------->
<div class="row">
	<div class="col-sm-12">
    	<table class="table table-striped" style="border:solid 1px #ccc; margin-bottom:0px;" id="prepareList">
        <thead>
        <tr>
        	<td colspan="6" align="center"><h4 class="title">รายการรอจัด</h4></td>
            <td align="center"><label><input type="checkbox" id="showZone" <?php echo isChecked($showZone, 1); ?> onChange="toggleZone()" style="margin-right:10px;" /> แสดงที่เก็บ</label></td></tr>
        <tr style="font-size:12px;">
			<th style='width:5%; text-align:center;'></th>
            <th style='width:20%;'>บาร์โค้ด</th>
            <th style='width:30%;'>สินค้า</th>
            <th style='width:10%; text-align:center;'>จำนวน</th>
            <th style='width:10%; text-align:center;'>จัดแล้ว</th>
            <th style='width:10%; text-align:center;'>คงเหลือ</th>
            <th style='width:15%; text-align:center;'>สถานที่</th>
		</tr>
        </thead>
         <tbody id="topTable">
        <?php  if( $order->current_state == 3 ){  $order->state_change($order->id_order, 4, $id_user);  }   //----  ถ้าสถานะเป็นรอจัด เปลี่ยนเป็นกำลังจัด  --// ?>
       	<?php prepareOrder($id_order);  //------ ตรวจสอบการจัดถ้ามีการดึงกลับ ให้ update คนจัด ถ้าไม่มีใครจัดมาก่อน ให้เพิ่มคนจัด ถ้ามีอยู่แล้ว ไม่ต้องทำอะไร  --// ?>
        <?php $qs = dbQuery("SELECT tbl_order_detail.* FROM tbl_order_detail JOIN tbl_product ON tbl_order_detail.id_product = tbl_product.id_product WHERE id_order = " .$id_order. " AND valid_detail = 0 AND is_visual = 0 ORDER BY barcode ASC");	?>
        <?php if( dbNumRows($qs) > 0 ) : ?>
        <?php 	$product = new product(); 	?>
        <?php	while( $rs = dbFetchArray($qs) ) : ?>
        <?php		$id_pa 	= $rs['id_product_attribute'];	?>
        <?php		$orderQty = $rs['product_qty'];				?>
        <?php		$prepared	= getBufferQty($id_order, $id_pa); ?>
        <?php		$balance		= $orderQty - $prepared;		?>
        <?php		$inZone		= $product->stock_in_zone($id_pa);	?>
        <tr style="font-size:12px;">
        	<td align="center"><img src="<?php echo $product->get_product_attribute_image($id_pa, 1); ?>" title="<?php echo $id_pa; ?>" /></td>
            <td><?php echo $rs['barcode']; ?></td>
            <td><?php echo $rs['product_reference'] .' : '.$rs['product_name']; ?></td>
            <td align="center"><?php echo number_format($orderQty); ?></td>
            <td align="center"><?php echo number_format($prepared); ?></td>
            <td align="center"><?php echo number_format($balance); ?></td>
            <td align="center">
            <button type="button" class="btn btn-sm btn-default btn-pop" data-container="body" data-toggle="popover" data-placement="right" data-html="true" data-content="<?php echo $inZone; ?>" <?php echo $notShow; ?>>ที่เก็บ</button>
            <span class="zoneLabel" <?php echo $show; ?>><?php echo $inZone; ?></span>
            </td>
        </tr>
        <?php 	endwhile; ?>
        <?php endif; ?>
        </tbody>
        </table>
        <table style="width:100%; border:solid 1px #ccc; border-top:0px; margin-bottom:10px;">
        <tr id="finish" style="display:none;">
           	<td align="center" style="padding:10px;">
               	<button type="button" class="btn btn-sm btn-success" onClick="closeJob(<?php echo $id_order; ?>)">จัดเสร็จแล้ว</button>
            </td>
        </tr>
		<tr id="force_close" style="display:none;">
          	<td align="center" style="padding:10px;">
           		<label style="display:block;"><input type='checkbox' id='checkboxes' onChange="getcondition()" /> สินค้ามีไม่ครบ</label>
           		<button type="button" class="btn btn-sm btn-success" id="btn_close_job" style="display:none;"  onClick="closeJob(<?php echo $id_order; ?>)">บังคับจบ</button>
			</td>
		</tr>
        </table>
    </div>
</div>

<!-------------------------  รายการที่จัดครบแล้ว  -------------------------->
<div class="row">
    <div class="col-sm-12">
    	<table class="table table-striped" style="border: solid 1px #ccc;">
        <thead>
        <tr><td colspan="7" align="center"><h4 class="title">รายการที่ครบแล้ว</h4></td></tr>
        <tr style="font-size:12px;">
			<th style='width:5%; text-align:center;'></th>
            <th style='width:20%;'>บาร์โค้ด</th>
            <th style='width:30%;'>สินค้า</th>
            <th style='width:10%; text-align:center;'>จำนวน</th>
            <th style='width:10%; text-align:center;'>จัดแล้ว</th>
            <th style='width:10%; text-align:center;'>คงเหลือ</th>
            <th style='width:15%; text-align:center;'>สถานที่</th>
		</tr>
        </thead>
        <tbody id="lastTable">
	<?php $qs = dbQuery("SELECT tbl_order_detail.* FROM tbl_order_detail JOIN tbl_product ON tbl_order_detail.id_product = tbl_product.id_product WHERE id_order = " .$id_order. " AND (valid_detail = 1 OR is_visual = 1) ORDER BY barcode ASC");	?>
        <?php if( dbNumRows($qs) > 0 ) : ?>
        <?php 	$product = new product(); 	?>
        <?php	while( $rs = dbFetchArray($qs) ) : ?>
        <?php		$id_pa 	= $rs['id_product_attribute'];	?>
        <?php		$orderQty = $rs['product_qty'];				?>
        <?php		$prepared	= getBufferQty($id_order, $id_pa); ?>
        <?php		$balance		= $orderQty - $prepared;		?>
        <?php 		$fromZone	= product_from_zone($id_order, $id_pa);	?>
        <tr style="font-size:12px;">
        	<td align="center"><img src="<?php echo $product->get_product_attribute_image($id_pa, 1); ?>" /></td>
            <td><?php echo $rs['barcode']; ?></td>
            <td><?php echo $rs['product_reference'] .' : '.$rs['product_name']; ?></td>
            <td align="center"><?php echo number_format($orderQty); ?></td>
            <td align="center"><?php echo number_format($prepared); ?></td>
            <td align="center"><?php echo number_format($balance); ?></td>
            <td>
				<button type="button" class="btn btn-sm btn-default btn-pop" data-container="body" data-toggle="popover" data-placement="right" data-html="true" data-content="<?php echo $fromZone; ?>">
                    	จากโซน
                 </button>
			</td>
        </tr>
        <?php 	endwhile; ?>
        <?php endif; ?>
        </tbody>
        </table>
    </div>
</div><!--/ row -->

<?php elseif( isset( $_GET['process'] ) ) : //----- ระหว่างการจัด	?>
<div class="row">
	<div class="col-sm-12">
    	<table class="table table-striped">
        <thead style="font-size:12px;">
        	<th style='width: 5%; text-align:center;'>ลำดับ</th>
            <th style='width: 15%; text-align:center;'>เลขที่เอกสาร</th>
			<th style='width: 25%; text-align:center;'>ลูกค้า</th>
            <th style='width: 15%; text-align:center;'>รูปแบบ</th>
            <th style='width: 15%; text-align:center;'>วันที่สั่ง</th>
            <th style='width: 20%; text-align:center;'>พนักงานจัด</th>
            <th style='width: 5%; text-align:center;'></th>
        </thead>
        <tbody>
	<?php $qs = dbQuery("SELECT tbl_prepare.id_order, tbl_prepare.id_employee FROM tbl_prepare JOIN tbl_order ON tbl_prepare.id_order = tbl_order.id_order WHERE  current_state = 4 ");  ?>
    <?php if( dbNumRows($qs) > 0 ) : ?>
    <?php	$n = 1;	?>
    <?php	while( $rs = dbFetchArray($qs) ):	?>
    <?php		$id_order 	= $rs['id_order'];	?>
    <?php		$order		= new order($id_order); 	?>
    <?php		$emp_name	= $rs['id_employee'] == -1 ? 'ไม่มีคนจัด' : employee_name($rs['id_employee']);	?>
    		<tr style="font-size:12px;">
					<td align='center'><?php echo $n; ?></td>
					<td align='center'><?php echo $order->reference; ?></td>
					<td align='center'><?php echo customer_name($order->id_customer); ?></td>
					<td align='center'><?php echo $order->role_name; ?></td>
					<td align='center'><?php echo thaiDate($order->date_add); ?></td>
					<td align='center'><?php echo $emp_name; ?></td>
					<td align='center'>
                    	<button type="button" class="btn btn-sm btn-default" onClick="continuePrepare(<?php echo $id_order; ?>, <?php echo $rs['id_employee']; ?>, <?php echo $supervisor; ?>)">จัดสินค้าต่อ</button>
                    </td>
			</tr>
	<?php 	$n++; ?>
    <?php	endwhile; ?>
    <?php endif; ?>
        </tbody>
        </table>
    </div><!--/ col-sm-12 -->
</div><!--/ row -->


<?php elseif( isset( $_GET['view_handle'] ) ) : //-------  เอารายการที่มีคนจัดแล้วกลับ ?>

 <table class="table table-striped">
 <thead>
 <th style="width:10%; text-align: center;">ลำดับ</th>
 <th style="width: 25%;">ออเดอร์</th>
 <th style="width:25%;">พนักงาน</th>
 <th style="width:15%; text-align:center">เริ่ม</th>
 <th style="text-align:right">ดึงกลับ</th>
 </thead>
 <?php  $n = 1; ?>
<?php  $qs = dbQuery("SELECT id_prepare, tbl_prepare.id_order, tbl_prepare.id_employee, start, reference, id_customer FROM tbl_prepare JOIN tbl_order ON tbl_prepare.id_order = tbl_order.id_order WHERE current_state = 4"); ?>
<?php if(dbNumRows($qs) > 0) : ?>
<?php 	while($rs = dbFetchArray($qs)) : ?>
	<tr>
    	<td align="center"><?php echo $n; ?></td>
        <td><?php echo $rs['reference']; ?></td>
        <td><?php echo employee_name($rs['id_employee']); ?></td>
        <td align="center"><?php echo date("d-m-Y H:i:s", strtotime($rs['start'])); ?></td>
        <td align="right"><a href="controller/prepareController.php?bring_it_back&id_prepare=<?php echo $rs['id_prepare']; ?>"><button type="button" class="btn btn-warning"><i class="fa fa-refresh"></i>&nbsp; ดึงกลับ</button></a></td>
    </tr>
<?php $n++; ?>
<?php	endwhile; ?>

<?php else: ?>
 <tr><td colspan="5"><center><h4>---------- ไม่มีรายการระหว่างจัด  ----------</h4></center></td></tr>

<?php endif;  ?>
</table>

<?php else : ?>
<!---------------------------  แสดงรายการรอจัด  ---------------------->
<div class="row">
	<div class="col-sm-12">
    	<table class="table table-striped">
        <thead style="font-size:12px;">
        	<th style="width:5%; text-align:center;">ลำดับ</th>
            <th style='width: 20%; text-align:center;'>เลขที่เอกสาร</th>
			<th style='width: 35%; text-align:center;'>ลูกค้า</th>
            <th style='width: 15%; text-align:center;'>รูปแบบ</th>
            <th style='width: 15%; text-align:center;'>วันที่สั่ง</th>
            <th style='width: 10%; text-align:center;'>&nbsp;</th>
        </thead>
        <tbody>
	<?php $qs = dbQuery("SELECT id_order FROM tbl_order WHERE current_state = 3 AND order_status = 1 AND valid != 2");	?>
    <?php if( dbNumRows($qs) > 0 ) : ?>
    <?php	$n = 1;	?>
    <?php	while( $rs = dbFetchArray($qs) ) : ?>
    <?php		$id_order 	= $rs['id_order']; 	?>
    <?php		$order		= new order($id_order);	?>
    		<tr style="font-size:12px;">
            	<td align="center"><?php echo $n; ?></td>
                <td align='center'><?php echo $order->reference; ?></td>
                <td align='center'><?php echo customer_name($order->id_customer); ?></td>
                <td align='center'><?php echo $order->role_name; ?></td>
                <td align='center'><?php echo thaiDate($order->date_add); ?></td>
                <td align="right"><button type="button" class="btn btn-sm btn-default" onClick="takeOrder(<?php echo $id_order; ?>)">จัดสินค้า</button></td>
        </tr>
	<?php 	$n++;	?>
    <?php 	endwhile; ?>	
    <?php endif; ?>
        </tbody>
        </table>
    </div> <!--/ col-sm-12 -->
</div><!--/ row -->
<script>
//------------ Reload page every minute  ------//
setTimeout(function(){ goBack(); }, 60000 );
</script>
<?php endif; //----- end of main if ?>
<?php endif; //----- end of if do stock checking ? ?>

<script id="topTableTemplate" type="text/x-handlebars-template">
{{#each this}}
    <tr style="font-size:12px;">
        <td align="center"><img src="{{ image }}" title="{{id_pa}}" /></td>
        <td>{{ barcode }}</td>
        <td>{{ product }}</td>
        <td align="center">{{ orderQty }}</td>
        <td align="center">{{ prepared }}</td>
        <td align="center">{{ balance }}</td>
        <td align="center">
            {{#if show}}
            <button type="button" class="btn btn-sm btn-default btn-pop" data-container="body" data-toggle="popover" data-placement="right" data-html="true" data-content="{{ inZone }}" style="display:none;" >ที่เก็บ</button>
            <span class="zoneLabel">{{{ inZone }}}</span>
            {{else}}
            <button type="button" class="btn btn-sm btn-default btn-pop" data-container="body" data-toggle="popover" data-placement="right" data-html="true" data-content="{{ inZone }}" >ที่เก็บ</button>
            <span class="zoneLabel" style="display:none;">{{{ inZone }}}</span>
            {{/if}}
        </td>
    </tr>
{{/each}}
</script>

<script id="lastTableTemplate" type="text/x-handlebars-template">
{{#each this}}
    <tr style="font-size:12px;">
        <td align="center"><img src="{{ image }}" /></td>
        <td>{{ barcode }}</td>
        <td>{{ product }}</td>
        <td align="center">{{ orderQty }}</td>
        <td align="center">{{ prepared }}</td>
        <td align="center">{{ balance }}</td>
        <td align="center">
            <button type="button" class="btn btn-sm btn-default btn-pop" data-container="body" data-toggle="popover" data-placement="right" data-html="true" data-content="{{ fromZone }}">ที่เก็บ</button>
        </td>
    </tr>
{{/each}}
</script>
<script>

//-----------------------------------------------------------  NEW CODE  -----------------------------------------//
$(document).ready(function(e) {
    validPrepare();
});

function validPrepare()
{
	count = $("#topTable td").size();
	if(count < 1 ){
		$("#force_close").css("display","none");
		$("#finish").css("display", "");
	}else{
		$("#force_close").css("display","");
		$("#finish").css("display", "none");
	}
}

//------------- เพิ่มสินค้าเข้าไปในตะกร้าจัดสินค้าและตัดออกจากโซน	 ------------------//
function doPrepare()
{
	var id_order	= $("#id_order").val();
	var id_zone		= $("#id_zone").val();
	var barcode	= $("#barcode_item").val();
	var qty 			= $("#qty").val();
	$("#barcode_item").val('');
	$("#qty").val(1);
	if( id_zone == '' ){ swal({ title: 'โซนไม่ถูกต้อง', timer: 2000, type: 'warning'}); return false; }
	if( barcode == '' ){ swal({title: "บาร์โค้ดสินค้าไม่ถูกต้อง", timer: 2000, type: "warning"}); return false; }
	if( qty == '' || qty > 9999 || isNaN( parseInt( qty ) ) ){ swal({ title: "จำนวนสินค้าไม่ถูกต้อง", timer: 2000, type: "warning"}); return false; }
	load_in();
	$.ajax({
		url:"controller/prepareController.php?perparedItem",
		type: "POST", cache:"false", data:{ "id_order" : id_order, "id_zone" : id_zone, "barcode" : barcode, "qty" : qty },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success')
			{
				updateList();
			}
			else
			{
				tellError(rs);
			}
		}
	});
}

function tellError(err)
{
	swal("ข้อผิดพลาด!!", err, "error");
	beep();
}

function updateList()
{
	var id_order = $("#id_order").val();
	updateTopTable(id_order);
	updateLastTable(id_order);
}

function updateTopTable(id_order)
{
	$.ajax({
		url:"controller/prepareController.php?getTopTable",
		type:"POST", cache:"false", data:{ "id_order" : id_order },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != 'fail' )
			{
				var source 	= $("#topTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#topTable");
				render(source, data, output);
				setPopover();
			}
			else
			{
				$("#topTable").html('');
			}
			validPrepare();
		}
	});
}

function updateLastTable(id_order)
{
	$.ajax({
		url:"controller/prepareController.php?getLastTable",
		type:"POST", cache:"false", data:{ "id_order" : id_order },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != 'fail' )
			{
				var source 	= $("#lastTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#lastTable");
				render(source, data, output);
				setPopover();
			}
			else
			{
				$("#lastTable").html('');
			}
		}
	});
}

function toggleZone()
{
	if( $("#showZone").is(":checked") )
	{
		$.get("controller/prepareController.php?toggleZone&show=1");
		$('.btn-pop').css("display", "none");
		$(".zoneLabel").css('display', '');
	}else{
		$.get("controller/prepareController.php?toggleZone&show=0");
		$('.btn-pop').css("display", "");
		$(".zoneLabel").css('display', 'none');
	}
}

function continuePrepare(id_order, id_emp, supper)
{
	if( id_emp == -1 || supper == 1 )
	{
		window.location.href = "index.php?content=prepare&process=y&id_order="+id_order;
	}
	else
	{
		$.ajax({
			url:"controller/prepareController.php?checkPrepared",
			type:"POST", cache:"false", data:{ "id_order" : id_order, "id_employee" : id_emp },
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'ok' )
				{
					window.location.href = "index.php?content=prepare&process=y&id_order="+id_order;
				}
				else
				{
					swal("ออเดอร์นี้มีคนจัดอยู่แล้ว");
				}
			}
		});
	}
}

function closeJob(id_order)
{
	$.ajax({
		url:"controller/prepareController.php?closeJob",
		type:"POST", cache:"false", data:{ "id_order" : id_order },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'fail')
			{
				swal("ข้อผิดพลาด", "ปิดการจัดไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}else{
				swal({ title: "สำเร็จ", timer: 1000, type: "success"});
				setTimeout(function(){ goBack(); }, 1500);
			}
		}
	});
}

function takeOrder(id_order)
{
	window.location.href = "index.php?content=prepare&process=y&id_order="+id_order;
}

function viewHandle()
{
	window.location.href = "index.php?content=prepare&view_handle";
}


function viewCurrent()
{
	window.location.href = "index.php?content=prepare&process";
}

function goBack()
{
	window.location.href = "index.php?content=prepare";
}

$(".btn-pop").mouseenter(function(e) {
    $(this).popover("show");
});
$(".btn-pop").mouseleave(function(e) {
    $(this).popover("hide");
});

function setPopover()
{
	$(".btn-pop").mouseenter(function(e) {
		$(this).popover("show");
	});
	$(".btn-pop").mouseleave(function(e) {
		$(this).popover("hide");
	});
}
function getcondition(){
	if( $("#checkboxes").prop("checked") )
	{
		$("#btn_close_job").css("display", "");
	}else{
		$("#btn_close_job").css("display","none");
	}
}

function getZone(barcode)
{
	$.ajax({
		url:"controller/prepareController.php?getZone",
		type:"POST", cache:"false", data:{"barcode" : barcode },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'fail' || isNaN(parseInt(rs)) ){
				swal("บาร์โค้ดโซนไม่ถูกต้อง กรุณาตรวจสอบ");
				$("#barcode_zone").focus();
			}else{
				$("#id_zone").val(rs);
				$("#barcode_item").removeAttr("disabled");
				$("#qty").removeAttr("disabled");
				$("#add").removeAttr("disabled");
				$("#change_zone").removeAttr("disabled");
				$("#barcode_zone").attr("disabled", "disabled");
				$("#barcode_item").focus();
			}
		}
	});
}

$("#barcode_zone").keyup(function(e) {
    if( e.keyCode == 13 ){
		var barcode = $(this).val();
		if(barcode !=""){
			getZone(barcode);
		}
	}
});

function validateItem()
{
	var qty	= $("#qty").val();
	var items	= $("#barcode_item").val();
	if( items == '' ){ swal({title: "บาร์โค้ดสินค้าไม่ถูกต้อง", timer: 2000, type: "warning"}, function(){ $("#barcode_item").focus(); }); return false; }
	if( qty == '' || qty > 9999 ){ swal({ title: "จำนวนสินค้าไม่ถูกต้อง", timer: 2000, type: "warning"}, function(){ $("#qty").focus(); }); return false; }
	doPrepare();
}

$("#barcode_item").keyup(function(e){
    if(e.keyCode == 13)
    {
        validateItem();
    }
});

$("#qty").keyup(function(e){
	if( e.keyCode == 13 )
	{
		var qty = $(this).val();
		if( qty == '' || qty > 9999 || isNaN( parseInt(qty) ) )
		{
			$(this).val(1);
		}
		$("#barcode_item").focus();
	}
});

$("#qty").focusout(function(e) {
    var qty = $(this).val();
	if( qty == '' || qty > 9999 || isNaN( parseInt(qty) ) )
	{
		$(this).val(1);
	}
});

$(document).keyup(function(e){
	if(e.keyCode == 113)
	{
		changeZone();
	}
});

$(document).keyup(function(e){
	if( e.keyCode == 18 )
	{
		$("#qty").focus();
	}
});
$("#qty").focus(function(e) {
    $(this).select();
});

function changeZone()
{
	window.location.reload();
}

//------------------------------------------------  END NEW CODE  -----------------------------------------//

</script>
<script src="../library/js/beep.js"></script>
