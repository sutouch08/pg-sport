<?php
	require_once 'function/consign_helper.php';
	$page_name 	= "ตัดยอดฝากขาย";
	$id_tab 			= 16;
	$cancle_tab_id = 36;  //// ยกเลิกการเปิดบิล
	$id_profile 		= $_COOKIE['profile_id'];
    $pm 				= checkAccess($id_profile, $id_tab);
	$view 			= $pm['view'];
	$add 				= $pm['add'];
	$edit 				= $pm['edit'];
	$delete 			= $pm['delete'];
	accessDeny($view);
	$px 				= checkAccess($id_profile, $cancle_tab_id);
	$redo 			= $px['add'] + $px['edit'] + $px['delete'];
	$role = 6; /// ตัดยอดฝากขาย

	$btn = "";
	$btn_back = '<button type="button" class="btn btn-sm btn-warning" onClick="go_back()"><i class="fa fa-arrow-left"></i> กลับ</button>';
	if( isset( $_GET['add'] ) )
	{
		$btn .= $btn_back;
		if( isset( $_GET['id_order_consign'] ) )
		{
			if( $add ){ $btn .= '<button type="button" class="btn btn-sm btn-success" onClick="viewDetail('.$_GET['id_order_consign'].')"><i class="fa fa-save"></i> บันทึก</button>'; }
		}
	}
	else if( isset( $_GET['edit'] ) && isset( $_GET['id_order_consign'] ) )
	{
		$btn .= $btn_back;
		if( $edit ){  $btn .= '<button type="button" class="btn btn-sm btn-success" onClick="viewDetail('.$_GET['id_order_consign'].')"><i class="fa fa-save"></i> บันทึก</button>'; }
	}
	else if( isset( $_GET['view_detail'] ) && isset( $_GET['id_order_consign'] ) )
	{
		$btn .= $btn_back;
	}
	else
	{
		if( $add ){ $btn .= '<button type="button" class="btn btn-sm btn-success" onClick="addConsign()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>'; }
	}

	?>
<style>
	.table > tbody > tr > td { vertical-align:middle !important; }
	.input { font-size:12px; }
</style>
<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-sm-6" style="margin-top:10px;"><h4 class="title"><i class="fa fa-check-square-o"></i>&nbsp;<?php echo $page_name; ?></h4></div>
    <div class="col-sm-6"><p class="pull-right" style="margin-bottom:0px;"><?php echo $btn; ?></p></div>
</div>
<hr style="margin-bottom:15px;" />
<!-- End page place holder -->
<!--  เพิ่มรายการใหม่  -->
<?php if( isset( $_GET['add'] ) ) :  ?>
<?php 	if( !isset( $_GET['id_order_consign'] ) ) : ?>
<div class="row">
	<div class="col-lg-2">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm" id="date" name="date" style="text-align:center;" value="<?php echo date('d-m-Y'); ?>" />
    </div>
    <div class="col-lg-4">
    	<label>ลูกค้า</label>
        <input type="text" class="form-control input-sm" id="customer" name="customer" placeholder="ค้นหาชื่อลูกค้า" />
    </div>
    <div class="col-lg-4">
    	<label>โซน</label>
        <input type="text" class="form-control input-sm" id="zone" name="zone" placeholder="ค้นหาโซน" />
    </div>
    <div class="col-lg-10">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" id="remark" name="remark" placeholder="ระบุหมายเหตุ (ถ้ามี)" />
    </div>
    <div class="col-lg-2">
    	<label style="display:block; visibility:hidden">button</label>
        <?php if( $add ) : ?>
        <button type="button" class="btn btn-sm btn-success btn-block" onClick="newConsign()"><i class="fa fa-plus"></i> เพิ่ม</button>
        <?php endif; ?>
    </div>
</div>
	<input type="hidden" name="id_customer" id="id_customer" />
    <input type="hidden" name="id_zone" id="id_zone" />
<hr style="margin-top:15px;"/>
<?php	else : 		?>
<?php 	$id 			= $_GET['id_order_consign']; ?>
<?php 	$consign 	= new consign($id);	?>
<?php	$cst			= $consign->consign_status; ?>
<div class="row">
	<div class="col-lg-2">
    	<label>เลขที่เอกสาร</label>
        <span class="form-control input-sm disabled" style="text-align:center;"><?php echo $consign->reference; ?></span>
    </div>
	<div class="col-lg-2">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm" id="date" name="date" style="text-align:center;" value="<?php echo thaiDate($consign->date_add); ?>" disabled />
    </div>
    <div class="col-lg-4">
    	<label>ลูกค้า</label>
        <input type="text" class="form-control input-sm" id="customer" name="customer" placeholder="ค้นหาชื่อลูกค้า" value="<?php echo customer_name($consign->id_customer); ?>" disabled />
    </div>
    <div class="col-lg-4">
    	<label>โซน</label>
        <input type="text" class="form-control input-sm" id="zone" name="zone" placeholder="ค้นหาโซน" value="<?php echo get_zone($consign->id_zone); ?>" disabled />
    </div>
    <div class="col-lg-10">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" id="remark" name="remark" placeholder="ระบุหมายเหตุ (ถ้ามี)" value="<?php echo $consign->comment; ?>" disabled />
    </div>
    <div class="col-lg-2">
    	<label style="display:block; visibility:hidden">button</label>
        <?php if( $edit && $consign->consign_status == 0 ) : ?>
        <button type="button" class="btn btn-sm btn-warning btn-block" id="btn_edit" onClick="editConsign()"><i class="fa fa-pencil"></i> แก้ไข</button>
        <button type="button" class="btn btn-sm btn-success btn-block" id="btn_update" onClick="updateConsign()" style="display:none;"><i class="fa fa-save"></i> บันทึก</button>
        <?php endif; ?>
    </div>
</div>
	<input type="hidden" name="id_consign" id="id_consign" value="<?php echo $id; ?>" />
	<input type="hidden" name="id_customer" id="id_customer" value="<?php echo $consign->id_customer; ?>" />
    <input type="hidden" name="id_zone" id="id_zone" value="<?php echo $consign->id_zone; ?>" />
    <input type="hidden" name="id_pa" id="id_pa" />
    <input type="hidden" name="stock_qty" id="stock_qty" />
<hr style="margin-top:15px;"/>
<?php if( $cst == 0 ) : ?>
<div class="row">

    <div class="col-lg-2">
        <label class="input">บาร์โค้ดสินค้า</label>
        <input type="text" class="form-control input-sm" name="barcode_item" id="barcode_item" placeholder="ยิงบาร์โค้ดสินค้า" autocomplete='off' autofocus />
    </div>
    <div class="col-lg-3">
    	<label class="input">สินค้า</label>
        <input type="text" class="form-control input-sm" name="item" id="item" placeholder="ค้นหาชื่อสินค้า" autocomplete='off' />
    </div>
    <div class="col-lg-1">
    	<label class="input">ราคา</label>
        <input type="text" class="form-control input-sm" name="price" id="price" />
    </div>
    <div class="col-lg-1">
    	<label class="input">ส่วนลด (%)</label>
        <input type="text" class="form-control input-sm" name="p_dis" id="p_dis" />
    </div>
    <div class="col-lg-1">
    	<label class="input">ส่วนลด (฿)</label>
        <input type="text" class="form-control input-sm" name="a_dis" id="a_dis" />
    </div>
    <div class="col-lg-1">
    	<label class="input">ในโซน</label>
        <input type="text" class="form-control input-sm" id="stock_label" disabled />
    </div>
    <div class="col-lg-1">
    	<label class="input">จำนวน</label>
        <input type="text" class="form-control input-sm" name="qty" id="qty" autocomplete='off' />
    </div>
    <div class="col-lg-1">
    	<label class="input" style="display:block; visibility:hidden;">button</label>
        <button type="button" class="btn btn-sm btn-primary btn-block" onClick="insertConsignDetail()">เพิ่ม</button>
    </div>
    <div class="col-lg-1">
    	<label class="input" style="display:block; visibility:hidden;">button</label>
        <button type="button" class="btn btn-sm btn-default btn-block" onClick="clearInput()">เคลียร์</button>
    </div>

</div>
<hr style="margin-top:15px;"/>
<?php endif; ?>
<div class="row">
	<div class="col-lg-12">
    	<table class="table table-striped table-bordered">
        <thead style="font-size:12px;">
        	<tr>
            	<th style="width: 5%; text-align:center;">ลำดับ.</th>
                <th style="width: 12%;">บาร์โค้ด</th>
                <th style="width: 25%;">สินค้า</th>
                <th style="width: 10%; text-align:right;">ราคา</th>
                <th style="width: 10%; text-align:right;">ส่วนลด (%)</th>
                <th style="width: 10%; text-align:right;">ส่วนลด (฿)</th>
                <th style="width: 8%; text-align:right;">จำนวน</th>
                <th style="width: 10%; text-align:right;">มูลค่า</th>
                <th style="width: 10%; "></th>
            </tr>
        </thead>
        <tbody id="rs">
        <?php $qs = dbQuery("SELECT * FROM tbl_order_consign_detail WHERE id_order_consign = ".$id); ?>
        <?php $n = 1;  ?>
        <?php $total_qty = 0; ?>
        <?php $total_amount = 0; ?>
        <?php while( $rs = dbFetchArray($qs) ) : ?>
        <?php 	$id_cd = $rs['id_order_consign_detail']; ?>
        	<tr style="font-size:12px;" id="row<?php echo $id_cd; ?>">
                <td align="center" class="number"><?php echo $n; ?></td>
                <td><?php echo get_barcode($rs['id_product_attribute']); ?></td>
                <td><?php echo get_product_reference($rs['id_product_attribute']); ?></td>
                <td align="right"><?php echo number_format($rs['product_price'],2); ?></td>
                <td align="right"><?php echo $rs['reduction_percent']; ?></td>
                <td align="right"><?php echo $rs['reduction_amount']; ?></td>
                <td align="right" class="qty"><?php echo number_format($rs['qty']); ?></td>
                <td align="right" class="amount"><?php echo number_format(getConsignSumAmount($rs['qty'], $rs['product_price'], $rs['reduction_percent'], $rs['reduction_amount']), 2); ?></td>
                <td align="right">
                <?php if( $cst == 0 ) : ?>
                <button type="button" class="btn btn-xs btn-danger" onClick="deleteRow(<?php echo $id_cd; ?>)"><i class="fa fa-trash"></i>&nbsp; Delete</button></td>
                <?php endif; ?>
			</tr>
        <?php $n++;  $total_qty += $rs['qty']; $total_amount += getConsignSumAmount($rs['qty'], $rs['product_price'], $rs['reduction_percent'], $rs['reduction_amount']); ?>
        <?php endwhile; ?>
        <tr style="font-size:12px;" id="sumRow">
        	<td colspan="6" align="right"><strong>รวม</strong></td>
            <td align="right"><strong id="total_qty"><?php echo number_format($total_qty); ?></strong></td>
            <td align="right"><strong id="total_amount"><?php echo number_format($total_amount, 2); ?></strong></td>
            <td></td>
        </tr>

        </tbody>
        </table>
        <table
    </div>
</div>

<?php 	endif;		?>

 <!------------------------------  แก้ไขรายการ  ------------------------------->
 <?php elseif( isset( $_GET['edit'] ) && isset( $_GET['id_order_consign'] ) ) : ?>

 <?php 	$id 			= $_GET['id_order_consign']; ?>
<?php 	$consign 	= new consign($id);	?>
<?php	$cst			= $consign->consign_status; ?>
<div class="row">
	<div class="col-lg-2">
    	<label>เลขที่เอกสาร</label>
        <span class="form-control input-sm disabled" style="text-align:center;"><?php echo $consign->reference; ?></span>
    </div>
	<div class="col-lg-2">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm" id="date" name="date" style="text-align:center;" value="<?php echo thaiDate($consign->date_add); ?>" disabled />
    </div>
    <div class="col-lg-4">
    	<label>ลูกค้า</label>
        <input type="text" class="form-control input-sm" id="customer" name="customer" placeholder="ค้นหาชื่อลูกค้า" value="<?php echo customer_name($consign->id_customer); ?>" disabled />
    </div>
    <div class="col-lg-4">
    	<label>โซน</label>
        <input type="text" class="form-control input-sm" id="zone" name="zone" placeholder="ค้นหาโซน" value="<?php echo get_zone($consign->id_zone); ?>" disabled />
    </div>
    <div class="col-lg-10">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" id="remark" name="remark" placeholder="ระบุหมายเหตุ (ถ้ามี)" value="<?php echo $consign->comment; ?>" disabled />
    </div>
    <div class="col-lg-2">
    	<label style="display:block; visibility:hidden">button</label>
        <?php if( $edit && $consign->consign_status == 0 ) : ?>
        <button type="button" class="btn btn-sm btn-warning btn-block" id="btn_edit" onClick="editConsign()"><i class="fa fa-pencil"></i> แก้ไข</button>
        <button type="button" class="btn btn-sm btn-success btn-block" id="btn_update" onClick="updateConsign()" style="display:none;"><i class="fa fa-save"></i> บันทึก</button>
        <?php endif; ?>
    </div>
</div>
	<input type="hidden" name="id_consign" id="id_consign" value="<?php echo $id; ?>" />
	<input type="hidden" name="id_customer" id="id_customer" value="<?php echo $consign->id_customer; ?>" />
    <input type="hidden" name="id_zone" id="id_zone" value="<?php echo $consign->id_zone; ?>" />
    <input type="hidden" name="id_pa" id="id_pa" />
    <input type="hidden" name="stock_qty" id="stock_qty" />
<hr style="margin-top:15px;"/>
<?php if( $cst == 0 ) : ?>
<div class="row">

    <div class="col-lg-2">
        <label class="input">บาร์โค้ดสินค้า</label>
        <input type="text" class="form-control input-sm" name="barcode_item" id="barcode_item" placeholder="ยิงบาร์โค้ดสินค้า" autocomplete='off' autofocus />
    </div>
    <div class="col-lg-3">
    	<label class="input">สินค้า</label>
        <input type="text" class="form-control input-sm" name="item" id="item" placeholder="ค้นหาชื่อสินค้า" autocomplete='off' />
    </div>
    <div class="col-lg-1">
    	<label class="input">ราคา</label>
        <input type="text" class="form-control input-sm" name="price" id="price" />
    </div>
    <div class="col-lg-1">
    	<label class="input">ส่วนลด (%)</label>
        <input type="text" class="form-control input-sm" name="p_dis" id="p_dis" />
    </div>
    <div class="col-lg-1">
    	<label class="input">ส่วนลด (฿)</label>
        <input type="text" class="form-control input-sm" name="a_dis" id="a_dis" />
    </div>
    <div class="col-lg-1">
    	<label class="input">ในโซน</label>
        <input type="text" class="form-control input-sm" id="stock_label" disabled />
    </div>
    <div class="col-lg-1">
    	<label class="input">จำนวน</label>
        <input type="text" class="form-control input-sm" name="qty" id="qty" autocomplete='off' />
    </div>
    <div class="col-lg-1">
    	<label class="input" style="display:block; visibility:hidden;">button</label>
        <button type="button" class="btn btn-sm btn-primary btn-block" onClick="insertConsignDetail()">เพิ่ม</button>
    </div>
    <div class="col-lg-1">
    	<label class="input" style="display:block; visibility:hidden;">button</label>
        <button type="button" class="btn btn-sm btn-default btn-block" onClick="clearInput()">เคลียร์</button>
    </div>

</div>
<hr style="margin-top:15px;"/>
<?php endif; ?>
<div class="row">
	<div class="col-lg-12" style="padding-bottom:5px;">
    	<button type="button" class="btn btn-warning btn-sm" id="btnEditPrice" onClick="showEditPrice()"><i class="fa fa-tag"></i> แก้ไขราคา</button>
        <button type="button" class="btn btn-primary btn-sm" id="btnSavePrice" onClick="inputPassword()" style="display:none;"><i class="fa fa-save"></i> บันทึกราคา</button>
        <button type="button" class="btn btn-warning btn-sm" id="btnEditDiscount" onClick="showEditDiscount()"><i class="fa fa-pencil"></i> แก้ไขส่วนลด</button>
        <button type="button" class="btn btn-primary btn-sm" id="btnSaveDiscount" onClick="inputPassword()" style="display:none;"><i class="fa fa-save"></i> บันทึกส่วนลด</button>
    </div>
	<div class="col-lg-12">
		<form id="editForm" method="post">
    	<table class="table table-striped table-bordered">
        <thead style="font-size:12px;">
        	<tr>
            	<th style="width: 5%; text-align:center;">ลำดับ.</th>
                <th style="width: 12%;">บาร์โค้ด</th>
                <th style="width: 25%;">สินค้า</th>
                <th style="width: 10%; text-align:right;">ราคา</th>
                <th style="width: 10%; text-align:right;">ส่วนลด (%)</th>
                <th style="width: 10%; text-align:right;">ส่วนลด (฿)</th>
                <th style="width: 8%; text-align:right;">จำนวน</th>
                <th style="width: 10%; text-align:right;">มูลค่า</th>
                <th style="width: 10%; "></th>
            </tr>
        </thead>
        <tbody id="rs">
        <?php $qs = dbQuery("SELECT * FROM tbl_order_consign_detail WHERE id_order_consign = ".$id); ?>
        <?php $n = 1;  ?>
        <?php $total_qty = 0; ?>
        <?php $total_amount = 0; ?>
        <?php while( $rs = dbFetchArray($qs) ) : ?>
        <?php 	$id_cd = $rs['id_order_consign_detail']; ?>
        	<tr style="font-size:12px;" id="row<?php echo $id_cd; ?>">
                <td align="center" class="number"><?php echo $n; ?></td>
                <td><?php echo get_barcode($rs['id_product_attribute']); ?></td>
                <td><?php echo get_product_reference($rs['id_product_attribute']); ?></td>
                <td align="right">
					<input type="hidden" id="originPrice<?php echo $id_cd; ?>" value="<?php echo $rs['product_price']; ?>">
					<input type="text" class="form-control input-xs edit-price price<?php echo $n; ?>" id="productPrice<?php echo $id_cd; ?>" name="productPrice[<?php echo $id_cd; ?>]" value="<?php echo $rs['product_price']; ?>" style="text-align:right; display:none;"
                    	onClick="$(this).select()" onKeyUp="editPrice(<?php echo $n; ?>, <?php echo $id_cd; ?>)">
					<span class="priceLabel" id="priceLabel<?php echo $id_cd; ?>"><?php echo number_format($rs['product_price'],2); ?></span>
				</td>
                <td align="right">
					<input type="hidden" id="originPercent<?php echo $id_cd; ?>" value="<?php echo $rs['reduction_percent']; ?>">
					<input type="text" class="form-control input-xs edit-discount percent<?php echo $n; ?>" id="p_dis<?php echo $id_cd; ?>" name="p_dis[<?php echo $id_cd; ?>]" value="<?php echo $rs['reduction_percent']; ?>" style="text-align:right; display:none;"
                    	onClick="$(this).select()" onKeyUp="editPercent(<?php echo $n; ?>, <?php echo $id_cd; ?>)">
					<span class="percentLabel discountLabel" id="percentLabel<?php echo $id_cd; ?>"><?php echo $rs['reduction_percent']; ?></span>
				</td>
                <td align="right">
					<input type="hidden" id="originAmount<?php echo $id_cd; ?>" value="<?php echo $rs['reduction_amount']; ?>">
					<input type="text" class="form-control input-xs edit-discount amount<?php echo $n; ?>" id="a_dis<?php echo $id_cd; ?>" name="a_dis[<?php echo $id_cd; ?>]" value="<?php echo $rs['reduction_amount']; ?>" style="text-align:right; display:none;"
                    	onClick="$(this).select()" onKeyUp="editAmount(<?php echo $n; ?>, <?php echo $id_cd; ?>)">
					<span class="amountLabel discountLabel" id="amountLabel<?php echo $id_cd; ?>"><?php echo $rs['reduction_amount']; ?></span>
				</td>
                <td align="right" class="qty">
					<span id="qty<?php echo $id_cd; ?>"><?php echo number_format($rs['qty']); ?></span>
				</td>
                <td align="right" class="amount">
					<span id="rowAmount<?php echo $id_cd; ?>"><?php echo number_format(getConsignSumAmount($rs['qty'], $rs['product_price'], $rs['reduction_percent'], $rs['reduction_amount']), 2); ?></span>
				</td>
                <td align="right">
                <?php if( $cst == 0 ) : ?>
                <button type="button" class="btn btn-xs btn-danger" onClick="deleteRow(<?php echo $id_cd; ?>)"><i class="fa fa-trash"></i>&nbsp; Delete</button></td>
                <?php endif; ?>
			</tr>
        <?php $n++;  $total_qty += $rs['qty']; $total_amount += getConsignSumAmount($rs['qty'], $rs['product_price'], $rs['reduction_percent'], $rs['reduction_amount']); ?>
        <?php endwhile; ?>
        <tr style="font-size:12px;" id="sumRow">
        	<td colspan="6" align="right"><strong>รวม</strong></td>
            <td align="right"><strong id="total_qty"><?php echo number_format($total_qty); ?></strong></td>
            <td align="right"><strong id="total_amount"><?php echo number_format($total_amount, 2); ?></strong></td>
            <td></td>
        </tr>

        </tbody>
        </table>
        <table>
			</form>
    </div>
</div>
<?php elseif( isset( $_GET['view_detail'] ) && isset( $_GET['id_order_consign'] ) ) : ?>
<?php $id 			= $_GET['id_order_consign']; ?>
<?php $consign	= new consign($id); 	?>
<?php $cst			= $consign->consign_status; ?>


<div class="row">
    <div class="col-lg-2">เอกสาร | <strong><?php echo $consign->reference; ?></strong></div>
    <div class="col-lg-2">วันที่ | <strong><?php echo thaiDate($consign->date_add, '/'); ?></strong></div>
    <div class="col-lg-4">ลูกค้า | <strong><?php echo customer_name($consign->id_customer); ?></strong></div>
    <div class="col-lg-4">โซน | <strong><?php echo get_zone($consign->id_zone); ?></strong></div>
    <div class="col-lg-12">&nbsp;</div>
</div>
<hr/>
<div class="row">
	<div class="col-lg-6">ผู้ทำรายการ |  <strong><?php echo employee_name($consign->id_employee); ?></strong></div>
	<div class="col-lg-6">
        <p class="pull-right">
        <?php if( $cst == 0 && ( $add || $edit ) ) : ?>
            <button type="button" class="btn btn-sm btn-primary" id="btn_bill" onClick="saveConsign(<?php echo $id; ?>)">เปิดบิล (ตัดสต็อก)</button>
            <button type="button" class="btn btn-sm btn-warning" id="btn_edit" onClick="editConsignDetail(<?php echo $id; ?>)"><i class="fa fa-pencil"></i>&nbsp; แก้ไข</button>
            <script>
			function stopInt() { clearInterval(interv); }
			var interv = setInterval(function () {
										var id_consign = <?php echo $id; ?>;
										$.ajax({
											url : 'controller/consignController.php?getConsignStatus&id_order_consign='+id_consign,
											type: "GET", cache: "false", success: function(rs){
												var rs = $.trim(rs);
												if( rs == '1' ){
													$("#btn_bill").remove();
													$("#btn_edit").remove();
													stopInt();
													window.location.reload();
												}
											}
										});
									}, 2000);
            </script>
        <?php endif; ?>
        <?php if( $cst == 1 && $redo ) : ?>
        	<button type="button" class="btn btn-sm btn-warning" id="btn_cancle_bill" onClick="rollBackConsign(<?php echo $id; ?>)">ยกเลิกการเปิดบิล</button>
        <?php endif; ?>
        <?php if( $cst == 1 ) : ?>
        	<button type="button" class="btn btn-sm btn-primary" id="btn_print" onClick="printConsign(<?php echo $id; ?>)"><i class="fa fa-print"></i> พิมพ์</button>
        <?php endif; ?>
        </p>
    </div>
</div>
<div class="row">
	<div class="col-lg-12">
    	<table class="table table-striped table-bordered">
        <thead style="font-size:12px;">
        	<th style="width:5%; text-align:center;">ลำดับ</th>
            <th style="width: 10%;">บาร์โค้ด</th>
            <th style="width: 25%;">สินค้า</th>
            <th style="width:10%; text-align:right;">ราคา</th>
            <th style="width:10%; text-align:right;">ส่วนลด (%)</th>
            <th style="width:10%; text-align:right;">ส่วนลด (฿)</th>
            <th style="width:10%; text-align:right;">จำนวน</th>
            <th style="width:10%; text-align:right;">มูลค่า</th>
        </thead>
	<?php $qs = $consign->getConsignItems($id); 			?>
    <?php 	$n = 1;	?>
    <?php 	$total_qty = 0;  $total_amount = 0; 		?>
    <?php 	while( $rs = dbFetchArray($qs) ) : ?>
    <?php 		$id_pa = $rs['id_product_attribute']; ?>
    <?php 		$amount = getConsignSumAmount($rs['qty'], $rs['product_price'], $rs['reduction_percent'], $rs['reduction_amount']); ?>
    	<tr style="font-size:12px;">
        	<td align="center"><?php echo $n; ?></td>
            <td><?php echo get_barcode($id_pa); ?></td>
            <td><?php echo get_product_reference($id_pa); ?></td>
            <td align="right"><?php echo number_format($rs['product_price'], 2); ?></td>
            <td align="right"><?php echo $rs['reduction_percent']; ?></td>
            <td align="right"><?php echo number_format($rs['reduction_amount'],2); ?></td>
            <td align="right"><?php echo number_format($rs['qty']); ?></td>
            <td align="right"><?php echo number_format($amount, 2); ?></td>
        </tr>
    <?php 		$n++;		?>
    <?php 		$total_qty += $rs['qty'];  $total_amount += $amount; ?>
    <?php   	endwhile; ?>
    	<tr>
        	<td colspan="6" align="right"><strong>รวม</strong></td>
            <td align="right"><strong><?php echo number_format($total_qty); ?></strong></td>
            <td align="right"><strong><?php echo number_format($total_amount, 2); ?></strong></td>
        </tr>
        </table>
    </div>
    <div class="col-lg-12">&nbsp;</div>
</div>




<!--------------------------------------  แสดงรายการ ------------------------------->
<?php else :  ?>

<?php
	$paginator = new paginator();
	/// @ รับวันที่จาก Form
	if( isset($_POST['from_date']) && $_POST['from_date'] !="")
	{
		createCookie( 'consignFromDate', $_POST['from_date'] );
		$from_date = $_POST['from_date'];
	}
	else if( isset( $_COOKIE['consignFromDate'] ) )
	{
		$from_date = getCookie('consignFromDate');
	}
	else
	{
		//deleteCookie('consignFromDate');
		$from_date = '';
	}

	if( isset($_POST['to_date']) && $_POST['to_date'] != "")
	{
		createCookie( 'consignToDate',  $_POST['to_date'] );
		$to_date = $_POST['to_date'];
	}
	else if( isset( $_COOKIE['consignToDate'] ) )
	{
		$to_date = getCookie('consignToDate');
	}
	else
	{
		//deleteCookie('consignToDate');
		$to_date = '';
	}


	if( isset($_POST['filter'] ) )
	{
		$filter = $_POST['filter'];
		createCookie('consign_filter', $_POST['filter']);
	}
	else if( isset( $_COOKIE['consign_filter'] ) )
	{
		$filter = getCookie('consign_filter');
	}
	else
	{
		$filter = '';
	}


	if( isset( $_POST['search-text'] ) && $_POST['search-text'] != '')
	{
		createCookie('consign_search_text', $_POST['search-text'] );
		$search_text = $_POST['search-text'];
	}
	else if( isset( $_COOKIE['consign_search_text'] ) )
	{
		$search_text = getCookie('consign_search_text');
	}
	else
	{
		//deleteCookie('consign_search_text');
		$search_text = '';
	}

	if( isset( $_POST['get_rows'] ) )
	{
		createCookie('get_rows', $_POST['get_rows']);
	}
	$get_rows = isset( $_COOKIE['get_rows'] ) ? getCookie('get_rows') : 50;
?>
<form id="form" method="post" action="index.php?content=consign">
<div class="row">
	<div class="col-lg-2">
    	<label>เงื่อนไข</label>
        <select class="form-control input-sm" name="filter" id="filter">
        <option value="customer" <?php echo isSelected($filter, 'customer'); ?>>ลูกค้า</option>
        <option value="reference" <?php echo isSelected($filter, 'reference'); ?>>เลขที่เอกสาร</option>
        <option value="notsave" <?php echo isSelected($filter, 'notsave'); ?>>ยังไม่ได้เปิดบิล</option>
        </select>
    </div>
    <div class="col-lg-3">
    	<label>คำค้นหา</label>
        <input type="text" class="form-control input-sm" id="search-text" name="search-text" value="<?php echo $search_text; ?>">
    </div>
    <div class="col-lg-2">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm" name="from_date" id="from_date" placeholder="เริ่มต้น" value="<?php echo $from_date; ?>" />
    </div>
    <div class="col-lg-2">
    	<label style="visibility:hidden">วันที่</label>
        <input type="text" class="form-control input-sm" name="to_date" id="to_date" placeholder="สิ้นสุด" value="<?php echo $to_date; ?>" />
    </div>
    <div class="col-lg-1">
    	<label style="display:block; visibility:hidden">button</label>
        <button type="button" class="btn btn-sm btn-primary btn-block" id="btn_search" onClick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
    </div>
    <div class="col-lg-1">
    	<label style="display:block; visibility:hidden;">button</label>
        <button type="button" class="btn btn-sm btn-warning btn-block" onClick="clearFilter()"><i class="fa fa-refresh"></i> รีเซ็ต</button>
    </div>
</div>
</form>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />
<?php
		/****  เงื่อนไขการแสดงผล *****/
	$where = "WHERE id_order_consign != 0 ";
	if( $search_text != '' )
	{
		switch( $filter ){
			case 'customer' :
				$in_cause 	= customer_incause($search_text);
				$where = "WHERE id_customer IN(".$in_cause.") " ;
				break;
			case 'reference' :
				$where = "WHERE reference LIKE '%".$search_text."%' ";
				break;
			case 'notsave' :
				$in_cause 	= customer_incause($search_text);
				$where = "WHERE consign_status = 0 AND (reference LIKE '%".$search_text."%' OR id_customer IN(".$in_cause.") ) ";
				break;
		} /// end switch
	}
	else
	{
		if( $filter == 'notsave' )
		{
			$where = "WHERE consign_status = 0 ";
		}
	}

	if( $from_date != '' && $to_date != '')
	{
		$where .= "AND (date_add BETWEEN '".fromDate($from_date)."' AND '".toDate($to_date)."') ";
	}
	$where .= "ORDER BY date_add DESC";

?>

<?php
	$paginator->Per_Page("tbl_order_consign", $where, $get_rows);
	$paginator->display($get_rows, "index.php?content=consign");
	$Page_Start	= $paginator->Page_Start;
	$Per_Page 	= $paginator->Per_Page;
	$qs = dbQuery("SELECT * FROM tbl_order_consign ".$where." LIMIT ".$Page_Start.", ".$Per_Page);
	//echo "SELECT * FROM tbl_order_consign ".$where." LIMIT ".$Page_Start.", ".$Per_Page;
?>
<div class='row' style="padding-bottom:30px;">
	<div class='col-lg-12' id='result'>
	<table class='table table-striped' style="border: solid 1px #ccc;">
    	<thead style='font-size:12px;'>
        	<th style='width:5%; text-align:center;'>ID</th>
            <th style='width:15%;'>เลขที่อ้างอิง</th>
            <th style='width:35%;'>ลูกค้า</th>
            <th style='width:10%;'>สถานะ</th>
			<th style='width:10%; text-align:center;'>วันที่</th>
            <th style="text-align:right;"></th>
        </thead>
<?php if( dbNumRows($qs) > 0 ) : ?>
<?php 	while( $rs = dbFetchArray($qs) ) : 				?>
<?php		$id = $rs['id_order_consign']; 				?>
<?php		$st = $rs['consign_status'];					?>
		<tr style="font-size:12px;">
        	<td align="center"><?php echo $id; ?></td>
            <td><?php echo $rs['reference']; ?></td>
            <td><?php echo customer_name($rs['id_customer']); ?></td>
            <td>
            	<?php if( $st == 0 ) : ?>
                <span style="color:red;">ยังไม่ได้เปิดบิล</span>
                <?php elseif( $st == 1 ) : ?>
                	เปิดบิลแล้ว
                <?php elseif( $st == 2 ) : ?>
                <span style="color:red">ยกเลิก</span>
                <?php endif; ?>
            </td>
            <td align="center"><?php echo thaiDate($rs['date_add']); ?></td>
            <td align="right">
            	<button type="button" class="btn btn-xs btn-info" onClick="viewDetail(<?php echo $id; ?>)"><i class="fa fa-eye"></i> รายละเอียด</button>
            <?php if( $st == 0 && $edit ) : ?>
                <button type="button" class="btn btn-xs btn-warning" onClick="editConsignDetail(<?php echo $id; ?>)"><i class="fa fa-pencil"></i> แก้ไข</button>
            <?php endif; ?>
            <?php if( $st != 1 && $st != 2 && $delete) : ?>
            	<button type="button" class="btn btn-xs btn-danger" onClick="goDelete(<?php echo $id; ?>, '<?php echo $rs['reference']; ?>')"><i class="fa fa-trash"></i> ลบ</button>
                <button type="button" class="btn btn-xs btn-danger" onClick="goCancle(<?php echo $id; ?>, '<?php echo $rs['reference']; ?>')"><i class="fa fa-times"></i> ยกเลิก</button>
            <?php endif; ?>

            </td>
        </tr>
<?php 	endwhile; ?>
<?php endif; ?>
</table>
<?php echo $paginator->display_pages();	?>
	</div>
</div>
<script> var intv = setInterval(function(){ window.location.href = window.location.href; }, 300000);</script>
<?php
endif;
?>

<script id='rowTemplate' type="text/x-handlebars-template">
		<tr style="font-size:12px;" id="row{{ id }}">
        	<td align="center" class="number">{{ no }}</td>
            <td>{{ barcode }}</td>
            <td>{{ item_code }}</td>
            <td align="right">{{ price }}</td>
            <td align="right">{{ p_dis }}</td>
            <td align="right">{{ a_dis }}</td>
			<td align="right" class="qty">{{ qty }}</td>
            <td align="right" class="amount">{{ amount }}</td>
            <td align="right"><button type="button" class="btn btn-xs btn-danger" onClick="deleteRow({{ id }})"><i class="fa fa-trash"></i>&nbsp; Delete</button></td>
	</tr>
</script>
<script id='rowUpdate' type="text/x-handlebars-template">
        	<td align="center" class="number">{{ no }}</td>
            <td>{{ barcode }}</td>
            <td>{{ item_code }}</td>
            <td align="right">{{ price }}</td>
            <td align="right">{{ p_dis }}</td>
            <td align="right">{{ a_dis }}</td>
			<td align="right" class="qty">{{ qty }}</td>
            <td align="right" class="amount">{{ amount }}</td>
            <td align="right"><button type="button" class="btn btn-xs btn-danger" onClick="deleteRow({{ id }})"><i class="fa fa-trash"></i>&nbsp; Delete</button></td>
</script>
<div class='modal fade' id='discountApprove' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' style="width:300px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class="modal-title">อนุมัติส่วนลด</h4>
				 </div>
				 <div class='modal-body'>
                     <div class="row">
                     	<div class="col-lg-12">
                        	<input type="password" class="form-control input-sm" id="s_key" name="s_key" placeholder="ป้อนรหัส อนุมัติ" style="text-align:center;" autocomplete="off">
                        </div>
                     </div>
                 </div>
				 <div class='modal-footer'>
                 	<button type="button" class="btn btn-primary btn-sm" onClick="validEditDiscountPermission()"><i class="fa fa-check"></i> อนุมัติ</button>
				 </div>
			</div>
		</div>
	</div>
<script src="<?php echo WEB_ROOT; ?>library/js/jquery.md5.js"></script>
<script>
function validEditDiscountPermission()
{
	$("#discountApprove").modal('hide');
	var pass = MD5($("#s_key").val());
	$.ajax({
		url:"controller/consignController.php?validEditDiscountPermission",
		type:"POST", cache:"false", data: {"password" : pass },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != 0 && !isNaN(parseInt(rs)) ){
				$('#s_key').val('');
				saveEditDiscount(rs);
			}else{
				swal("รหัสไม่ถูกต้อง หรือ คุณไม่มีอำนาจอนุมัติ");
			}
		}
	});
}
function inputPassword()
{
	$('#discountApprove').modal('show');	
}

$("#discountApprove").on("shown.bs.modal", function(){ $("#s_key").focus(); });
$("#s_key").keyup(function(e) {
    if( e.keyCode == 13 ){
		validEditDiscountPermission();
	}
});

function saveEditDiscount(apv)
{
	load_in();
	var id = $("#id_consign").val();
	$.ajax({
		url:"controller/consignController.php?saveEditDiscount&id="+id+"&apv="+apv,
		type:"POST", cache:"false", data: $("#editForm").serialize(),
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == "success" ){
				swal({ title: 'สำเร็จ', text: 'แก้ไขข้อมูลเรียบร้อยแล้ว', type: 'success', timer: 1000 });
				hideEditPrice();
				hideEditDiscount();
			}else{
				swal("ไม่สำเร็จ", "ไม่สามารถแก้ไขข้อมูลได้ กรุณาลองใหม่อีกครั้ง", "error");	
			}
		}
	});
}
function rowRecal(id)
{
	var price 	= parseFloat($("#productPrice"+id).val());
	var p_dis 	= parseFloat($("#p_dis"+id).val());
	var a_dis 	= parseFloat($("#a_dis"+id).val());
	var qty	 	= parseFloat(removeCommas($("#qty"+id).text()));
	var dis		= (p_dis > 0 ? ((p_dis * 0.01) * price) * qty : qty * a_dis);
	var amount	= (qty * price) - dis;
	$("#rowAmount"+id).text(addCommas(amount.toFixed(2)));
}
function editPrice(n, id)
{
	var ne = parseInt(n) + 1;
	$('.price'+n).keyup(function(e){
		if(e.keyCode == 13 ){
			if( $(this).val() == ''){ 
				swal('ราคาสินค้าไม่ถูกต้อง');
				return false;
			}
			$("#priceLabel"+id).text(addCommas($(this).val()));
			rowRecal(id);
			recal();
			$('.price'+ne).focus().select();
		}
	}); 
}

function editPercent(n, id)
{
	var ne = parseInt(n) + 1;
	$('.percent'+n).keyup(function(e){
		if(e.keyCode == 13 ){
			if( $(this).val() != '' || $(this).val() != 0){ 
				$("#a_dis"+id).val(0);
				$("#amountLabel"+id).text(0.0);
			}else{
				$(this).val(0);
			}
			$("#percentLabel"+id).text(addCommas($(this).val()));
			rowRecal(id);
			recal();
			$('.percent'+ne).focus().select();
		}
	}); 
}

function editAmount(n, id)
{
	var ne = parseInt(n) + 1;
	$('.amount'+n).keyup(function(e){
		if(e.keyCode == 13 ){
			if( $(this).val() != '' || $(this).val() != 0){ 
				$("#p_dis"+id).val(0);
				$("#percentLabel"+id).text(0.0);
			}else{
				$(this).val(0);
			}
			$("#amountLabel"+id).text(addCommas($(this).val()));
			rowRecal(id);
			recal();
			$('.amount'+ne).focus().select();
		}
	}); 
}



function showEditPrice()
{
	$('.priceLabel').css('display', 'none');
	$('.edit-price').css('display', '');
	$('#btnEditPrice').css('display', 'none');
	$('#btnSavePrice').css('display', '');
}

function hideEditPrice()
{
	$('.priceLabel').css('display', '');
	$('.edit-price').css('display', 'none');
	$('#btnEditPrice').css('display', '');
	$('#btnSavePrice').css('display', 'none');
}

function showEditDiscount()
{
	$('.discountLabel').css('display', 'none');	
	$('.edit-discount').css('display', '');
	$('#btnEditDiscount').css('display', 'none');
	$('#btnSaveDiscount').css('display', '');
}
function hideEditDiscount()
{
	$('.discountLabel').css('display', '');	
	$('.edit-discount').css('display', 'none');
	$('#btnEditDiscount').css('display', '');
	$('#btnSaveDiscount').css('display', 'none');
}

function deleteConsignDetail(id)
{
	load_in();
	$.ajax({
		url:"controller/consignController.php?deleteConsignDetail",
		type:"POST", cache:"false", data:{ "id_cd" : id },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title: 'สำเร็จ', text : 'ลบรายการเรียบร้อยแล้ว', type: 'success', timer: 1000});
				$("#row"+id).remove();
				recal();
			}else{
				swal('ไม่สำเร็จ', 'ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง', 'error');
			}
		}
	});
}

function deleteRow(id)
{
	swal({
		title : 'ต้องการลบรายการ?',
		text : 'คุณแน่ใจ ? ว่าต้องการลบรายการนี้',
		type : 'warning',
		showCancelButton: true,
		confirmButtonText: 'ใช่ ลบเลย',
		confirmButtonColor: '#DD6B55',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function( isConfirm ){
			if( isConfirm ){
				deleteConsignDetail(id);
			}
	});
}

function deleteConsign(id)
{
	load_in();
	$.ajax({
		url:"controller/consignController.php?deleteConsign",
		type: "POST", cache: "false", data:{ "id_order_consign" : id },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title: 'สำเร็จ', text: 'ลบเอกสารที่ต้องการเรียบร้อยแล้ว', timer: 1000, type: 'success'});
				setTimeout(function(){ window.location.reload(true); }, 2000);
			}else if( rs == '1'){
				swal("ไม่สำเร็จ", "ไม่สามารถลบเอกสารที่เปิดบิลไปแล้วได้", "error");
			}else{
				swal("ไม่สำเร็จ", "ลบเอกสารที่ต้องการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}
		}
	});
}

function goDelete(id, ref)
{
	swal({
		title: 'ต้องการลบ '+ref+' ?',
		text: 'โปรดจำไว้ว่า การกระทำนี้ไม่สามารถกู้คืนได้  ต้องการลบหรือไม่ ?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonText: 'ฉันต้องการลบ',
		confirmButtonColor: '#DD6B55',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
	}, function(isConfirm){
		if( isConfirm ){
			deleteConsign(id);
		}
	});
}


function goCancle(id, ref)
{
	swal({
		title: 'ต้องการยกเลิก '+ref+' ?',
		text: 'โปรดจำไว้ว่า การกระทำนี้ไม่สามารถกู้คืนได้  ต้องการยกเลิกหรือไม่ ?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonText: 'ฉันต้องการยกเลิก',
		confirmButtonColor: '#DD6B55',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
	}, function(isConfirm){
		if( isConfirm ){
			cancleConsign(id);
		}
	});
}

function cancleConsign(id)
{
	load_in();
	$.ajax({
		url:"controller/consignController.php?cancleConsign",
		type:"POST", cache: "false", data:{ "id_order_consign" : id },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title: 'สำเร็จ', text: 'ยกเลิกเอกสารที่ต้องการเรียบร้อยแล้ว', timer: 1000, type: 'success'});
				setTimeout(function(){ window.location.reload(true); }, 2000);
			}else{
				swal("ไม่สำเร็จ", "ยกเอกสารที่ต้องการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}
		}
	});
}


function recal()
{
	var qty = sumQty();
	var amount = sumAmount();
	$('#total_qty').text(addCommas(qty));
	$("#total_amount").text(addCommas(parseFloat(amount).toFixed(2)));
	reorder();
}

function reorder()
{
	var i = 1;
	$('.number').each(function(index, element) {
        $(this).text(i);
		i++;
    });
}

function sumQty()
{
	var qty = 0;
	$('.qty').each(function(index, element) {
        var qt = parseInt( removeCommas( $(this).text() ) );
		qty 	= qty+qt;
    });
	return qty;
}
function sumAmount()
{
	var amount = 0;
	$('.amount').each(function(index, element) {
        var am 	= parseFloat( removeCommas( $(this).text() ) );
		amount += am;
    });
	return amount;
}

function insertConsignDetail()
{
	var id_consign 	= $("#id_consign").val();
	var qty 			= $("#qty").val();
	var id_pa 		= $("#id_pa").val();
	var id_zone 	= $("#id_zone").val();
	var price			= $("#price").val();
	var p_dis		= $("#p_dis").val();
	var a_dis		= $("#a_dis").val();
	if( qty == '' || qty == 0 ){ swal('กรุณาระบุจำนวนที่ต้องการตัดยอดขาย'); return false; }
	if( id_pa =='' || id_pa == 0 ){ swal('กรุณาระบุรายการสินค้าที่ต้องการตัดยอดขาย'); return false; }
	if( id_zone =='' || id_zone == 0 ){ swal('กรุณาระบุโซนที่ต้องการตัดยอดขาย'); return false; }
	if( id_consign == '' || id_consign == 0 ){ swal('ไม่พบ ID ของเอกสาร กรุณาออกจากหน้านี้แล้วกลับเข้ามาใหม่'); return false; }
	if( p_dis > 100 ){ swal('ส่วนลดต้องไม่มากกว่า 100%'); return false; }
	if( parseFloat(a_dis) > parseFloat(price) ){ swal('ส่วนลดต้องไม่มากกว่าราคาขาย'); return false; }
	load_in();
	$.ajax({
		url:"controller/consignController.php?insertConsignDetail",
		type:"POST", cache: false, data:{ "id_order_consign" : id_consign, "id_zone" : id_zone, "id_pa" : id_pa, "qty" : qty, "price" : price, "p_dis" : p_dis, "a_dis" : a_dis },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs != 'false' && rs != '' )
			{
				var arr = rs.split(' | ');
				if( arr[0] == 'add' ){
					var rs 		= arr[1];
					var source 	= $('#rowTemplate').html();
					var data 		= $.parseJSON(rs);
					var output	= $('#rs');
					render_prepend(source, data, output);
				}else{
					var id 		= arr[0];
					var rs 		= arr[1];
					var source 	= $('#rowUpdate').html();
					var data 		= $.parseJSON(rs);
					var output	= $('#row'+id);
					render(source, data, output);
				}
				recal();
				clearInput();
			}
		}
	});
}

function editConsign()
{
	$("#date").removeAttr("disabled");
	$("#customer").removeAttr("disabled");
	$("#zone").removeAttr("disabled");
	$("#remark").removeAttr("disabled");
	$("#btn_edit").css("display", "none");
	$("#btn_update").css("display", "");
}
function updateConsign()
{
	var id			= $("#id_consign").val();
	var date 		= $("#date").val();
	var id_cus	= $("#id_customer").val();
	var cus		= $("#customer").val();
	var id_zone	= $("#id_zone").val();
	var zone		= $("#zone").val();
	var remark	= $("#remark").val();
	if( !isDate(date) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	if( id_cus == '' || cus == ''){ swal('ลูกค้าไม่ถูกต้อง เลือกลูกค้าใหม่อีกครั้ง'); return false; }
	if( id_zone == '' || zone == ''){ swal('โซนไม่ถูกต้อง เลือกโซนใหม่อีกครั้ง'); return false; }
	load_in();
	$.ajax({
		url: "controller/consignController.php?update_consign",
		type: "POST", cache: "false", data: { "id_order_consign" : id, "id_customer" : id_cus, "id_zone" : id_zone, "date_add" : date, "remark" : remark },
		success: function(rs){
			load_out();
			var rs 	= $.trim(rs);
			if( rs == 'success' ){
				updated();
			}else{
				swal("Error", "ไม่สามารถแก้ไขข้อมูลได้ กรุณาลองใหม่อีกครั้งภายหลัง", "warning");
			}
		}
	});
}

function updated()
{
	$("#date").attr("disabled", "disabled");
	$("#customer").attr("disabled", "disabled");
	$("#zone").attr("disabled", "disabled");
	$("#remark").attr("disabled", "disabled");
	$("#btn_update").css("display", "none");
	$("#btn_edit").css("display", "");
}

function newConsign()
{
	var date 		= $("#date").val();
	var id_cus	= $("#id_customer").val();
	var cus		= $("#customer").val();
	var id_zone	= $("#id_zone").val();
	var zone		= $("#zone").val();
	var remark	= $("#remark").val();
	if( !isDate(date) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	if( id_cus == '' || cus == ''){ swal('ลูกค้าไม่ถูกต้อง เลือกลูกค้าใหม่อีกครั้ง'); return false; }
	if( id_zone == '' || zone == ''){ swal('โซนไม่ถูกต้อง เลือกโซนใหม่อีกครั้ง'); return false; }
	load_in();
	$.ajax({
		url: "controller/consignController.php?new_consign",
		type: "POST", cache: "false", data: { "id_customer" : id_cus, "id_zone" : id_zone, "date_add" : date, "remark" : remark },
		success: function(rs){
			load_out();
			var rs 	= $.trim(rs);
			if( !isNaN(parseInt(rs)) ){
				var target = "index.php?content=consign&add&id_order_consign="+rs;
				window.location.href = target;
			}else{
				swal("Error !!", "เพิ่มเอกสารใหม่ไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}
		}
	});
}

function clearInput()
{
	$("#id_pa").val('');
	$("#barcode_item").val('');
	$("#item").val('');
	$("#price").val('');
	$("#p_dis").val('');
	$("#a_dis").val('');
	$("#stock_label").val('');
	$("#qty").val('');
	$("#barcode_item").focus();
}

function checkBarcode(barcode)
{
	var id_zone = $('#id_zone').val();
	load_in();
	$.ajax({
		url:"controller/consignController.php?check_barcode",
		type:"POST", cache:"false", data:{ "barcode" : barcode, "id_zone" : id_zone },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);				//// ได้ข้อมูลกลับมา id_pa | item_code | stock_qty
			if( rs != 'fail' && rs != ''){
				var arr = rs.split(' | ');
				$("#id_pa").val(arr[0]);
				$("#item").val(arr[1]);
				$("#price").val(arr[3]);
				$("#p_dis").val(0);
				$("#a_dis").val(0);
				$("#stock_label").val(arr[2]);
				$("#price").focus().select();

			}else{
				swal('ไม่พบสินค้าในฐานข้อมูล');
			}
		}
	});
}
$("#price").keyup(function(e){
	if( e.keyCode ==13 ){
		$("#p_dis").focus().select();
	}
});

$("#p_dis").keyup(function(e){
	if( e.keyCode ==13 ){
		if($(this).val() > 100){ swal('ส่วนลดไม่สามารถมากกว่า 100% ได้'); $(this).val(''); $(this).focus(); return false; }
		$("#a_dis").focus().select();
	}else{
		$("#a_dis").val(0);
	}
});

$("#a_dis").keyup(function(e){
	if( e.keyCode == 13 ){
		var price = parseFloat($('#price').val());
		if( parseFloat($(this).val()) > price ){ swal({ title: 'ข้อผิดพลาด !', text:'ส่วนลดไม่ควรเกินราคาขาย', type: 'warning'}, function(){ $('#a_dis').focus().select(); });  return false; }
		$("#qty").focus();
	}else{
		$("#p_dis").val(0);
	}
});

$("#qty").keyup(function(e){
	if( e.keyCode == 13 ){
		insertConsignDetail();
	}
});

$("#barcode_item").keyup(function(e) {
    if( e.keyCode == 13 )
	{
		var barcode = $(this).val();
		if( barcode == '' ){
			$("#item").focus();
		}else{
			checkBarcode(barcode);
		}
	}
});

function getItemData(id_pa)
{
	var id_zone = $("#id_zone").val();
	var item 		= $("#item").val();
	$.ajax({
		url:"controller/consignController.php?getItemData",
		type:"POST", cache:"false", data: { "reference" : item, "id_zone" : id_zone },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != 'fail' && rs != ''){
				var arr = rs.split(' | ');
				$("#id_pa").val(arr[0]);
				$("#price").val(arr[2]);
				$("#stock_label").val(arr[1]);
				$("#price").focus().select();
			}
		}
	});
}

$("#item").autocomplete({
	source : "controller/autoComplete.php?getProductReferenceOnly",
	autoFocus: true,
	close: function(){
		$("#id_pa").val('');
		var rs = $.trim($(this).val());
		$(this).val(rs);
	}
});

$("#item").keyup(function(e){
	if( e.keyCode == 13 ){
		if( $("#item").val() != '' )
		{
			getItemData();
		}
	}
});


$("#customer").autocomplete({
	source : "controller/autoComplete.php?get_customer",
	autoFocus: true,
	close: function(){
		$("#id_customer").val('');
		var rs 	= $(this).val();
		var arr	= rs.split(" | ");
		var name	= arr[1];
		var id		= arr[2];
		$("#id_customer").val(id);
		$(this).val(name);
		$("#zone").focus();
	}
});

$("#zone").autocomplete({
	source : "controller/autoComplete.php?consign_zone",
	autoFocus: true,
	close: function(){
		$("#id_zone").val('');
		var rs 	= $(this).val();
		var arr	= rs.split(" | ");
		var name= arr[0];
		var id		= arr[1];
		$(this).val(name);
		$("#id_zone").val(id);
		$("#remark").focus();
	}
});

$("#from_date").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(sd){
		$("#to_date").datepicker("option", "minDate", sd );
	}
});

$("#to_date").datepicker({
	dateFormat: "dd-mm-yy",
	onClose: function(sd){
		$("#from_date").datepicker("option", "maxDate", sd );
	}
});

$("#date").datepicker({ dateFormat: "dd-mm-yy" });

function getSearch(){
	var text 	= $("#search-text").val();
	var filter	= $("#filter").val();
	var from	= $("#from_date").val();
	var to		= $("#to_date").val();
	if( filter != 'notsave' && text == '' && from == '' && to == '' ){ return false; }
	if( (from != '' || to != '') && ( !isDate(from) || !isDate(to) ) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	$("#form").submit();
}

function clearFilter()
{
	$.ajax({
		url:"controller/consignController.php?clear_filter",
		type: "GET", cache: "false", success: function(rs){
			var rs = $.trim(rs);
			if(rs == 'done')
			{
				window.location.href = "index.php?content=consign";
			}
			else
			{
				swal(rs);
			}
		}
	});
}

$("#search-text").keyup(function(e) {
    if( e.keyCode == 13 )
	{
		getSearch();
	}
});

/// เปิดบิล ตัดสต็อก บันทึก movement และ บันทึกยอดขาย
function saveConsign(id)
{
	load_in();
	$.ajax({
		url:"controller/consignController.php?saveConsign",
		type:"POST", cache: "false", data:{ "id_order_consign" : id },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == "success" ){
				swal({ title: 'สำเร็จ', text: 'เปิดบิลและตัดสต็อกเรียบร้อยแล้ว', timer: 1000, type: 'success' });
			}else{
				swal("เกิดข้อผิดพลาด!!", "เปิดบิลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}
		}
	});
}

function rollBackConsign(id)
{
	load_in();
	$.ajax({
		url:"controller/consignController.php?rollBackConsign",
		type:"POST", cache: "false", data:{ "id_order_consign" : id },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == "success" ){
				swal({ title: "สำเร็จ", text: "ยกเลิกการเปิดบิลและคืนสต็อก เรียบร้อยแล้ว", timer: 1000, type: "success" });
				setTimeout(function(){ window.location.reload(); }, 2000);
			}else{
				swal("เกิดข้อผิดพลาด !!", "ยกเลิกการเปิดบิลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}
		}
	});
}

function addConsign()
{
	window.location.href = "index.php?content=consign&add";
}

function editConsignDetail(id)
{
	window.location.href = 'index.php?content=consign&edit&id_order_consign='+id;
}

function printConsign(id)
{
	var center = ($(document).width() - 800) /2;
	var target = 'controller/consignController.php?printConsign&id='+id;
	window.open(target, '_blank', 'width=800, height=900, left='+center+', scrollbars=yes');
}

function viewDetail(id)
{
	window.location.href = "index.php?content=consign&view_detail&id_order_consign="+id;
}

function go_back()
{
	load_in();
	window.location.href="index.php?content=consign";
}

</script>
