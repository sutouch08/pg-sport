<?php 	
	$page_name 	= "กระทบยอดสินค้าฝากขาย";
	$id_tab 			= 52;
	$id_profile 		= $_COOKIE['profile_id'];
    $pm 				= checkAccess($id_profile, $id_tab);
	$view 			= $pm['view'];
	$add 				= $pm['add'];
	$edit 				= $pm['edit'];
	$delete 			= $pm['delete'];
	accessDeny($view);
	require "function/consign_helper.php";
	if( isset($_GET['add'] ) || isset( $_GET['edit'] ) || isset($_GET['view_detail']) )
	{
		$reference 		= get_max_role_reference_consign_check("PREFIX_CONSIGN_CHECK");
		$id_customer 	= "";
		$customer		= "";
		$id_zone 		= "";
		$zone 			= "";
		$date_add		= date("d-m-Y");
		$remark			= "";
		$valid 			= 0;
		$active			= "";
		if( isset($_GET['id_consign_check']) ) : 
			$qs = dbQuery("SELECT * FROM tbl_consign_check WHERE id_consign_check = ".$_GET['id_consign_check']);
			if( dbNumRows($qs) == 1 ) :
				$rs 				= dbFetchArray($qs);
				$id 				= $rs['id_consign_check'];
				$reference 		= $rs['reference'];
				$id_customer 	= $rs['id_customer'];
				$customer		= customer_name($id_customer);
				$id_zone 		= $rs['id_zone'];
				$zone 			= get_zone($id_zone);
				$date_add		= thaiDate($rs['date_add']);
				$remark			= $rs['comment'];
				$valid				= $rs['consign_valid'];
				$active			= "disabled";			
			endif;
		endif;	
	}
	$btn_back 		= "<button type='button' class='btn btn-warning btn-sm' onclick='go_back()'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button>";
	$btn 				= "";
	if( isset( $_GET['add'] ) )
	{
		$btn .= $btn_back;
		if( isset($_GET['id_consign_check']) )
		{
			if( !$valid ){ if($delete){ $btn .= "<button type='button' class='btn btn-danger btn-sm' onclick='cancle_consign_check()'><i class='fa fa-times'></i> ยกเลิก</button>"; } }
			if( !$valid ){ if($add){ $btn .= "<button type='button' class='btn btn-success btn-sm' onclick='add_to_consign_sold(".$id.")'><i class='fa fa-bolt'></i> ดึงไปตัดยอดฝากขาย</button>"; } }
			$btn .= "<button type='button' class='btn btn-primary btn-sm' onclick='check_balance()'><i class='fa fa-list'></i> ยอดคงเหลือ</button>";
			$btn .= "<button type='button' class='btn btn-primary btn-sm' onclick='check_diff()'><i class='fa fa-list'></i> ยอดต่าง</button>";
			$btn .= "<button type='button' class='btn btn-warning btn-sm' onclick='check_fail()'><i class='fa fa-exclamation-triangle'></i> ข้อผิดพลาด</button>";
			
		}
	}
	else if( isset( $_GET['edit'] ) )
	{
		$btn .= $btn_back;
		if( isset($_GET['id_consign_check']) )
		{
			if( !$valid ){ if($delete){ $btn .= "<button type='button' class='btn btn-danger btn-sm' onclick='cancle_consign_check()'><i class='fa fa-times'></i> ยกเลิก</button>"; } }
			if( !$valid ){ if($add){ $btn .= "<button type='button' class='btn btn-success btn-sm' onclick='add_to_consign_sold(".$id.")'><i class='fa fa-bolt'></i> ดึงไปตัดยอดฝากขาย</button>"; } }
			$btn .= "<button type='button' class='btn btn-primary btn-sm' onclick='check_balance()'><i class='fa fa-list'></i> ยอดคงเหลือ</button>";
			$btn .= "<button type='button' class='btn btn-primary btn-sm' onclick='check_diff()'><i class='fa fa-list'></i> ยอดต่าง</button>";
			$btn .= "<button type='button' class='btn btn-warning btn-sm' onclick='check_fail()'><i class='fa fa-exclamation-triangle'></i> ข้อผิดพลาด</button>";
			
		}
	}	
	else if( isset( $_GET['view_detail'] ) && isset( $_GET['id_consign_check'] ) )
	{
		$btn .= $btn_back;
		$btn .= "<button type='button' class='btn btn-primary btn-sm' onclick='check_balance()'><i class='fa fa-list'></i> ยอดคงเหลือ</button>";
		$btn .= "<button type='button' class='btn btn-primary btn-sm' onclick='check_diff()'><i class='fa fa-list'></i> ยอดต่าง</button>";
		$btn .= "<button type='button' class='btn btn-warning btn-sm' onclick='check_fail()'><i class='fa fa-exclamation-triangle'></i> ข้อผิดพลาด</button>";
		if( !$valid ){ if($edit){ $btn .= "<button type='button' class='btn btn-warning btn-sm' onclick='go_edit(".$_GET['id_consign_check'].")'><i class='fa fa-pencil'></i> แก้ไข</button>"; } }
	}
	else
	{
		if( $add){ $btn .=  "<button type='button' class='btn btn-success btn-sm' onclick='go_add()'><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button>"; }
	}
	?>
    
<div class="container">
<!-- page place holder -->
<div class="row" style="height:35px;">
	<div class="col-sm-4" style="padding-top:10px;"><h4 class="title"><i class="fa fa-check-square-o"></i>&nbsp;<?php echo $page_name; ?></h4>
	</div>
    <div class="col-sm-8">
       <p class="pull-right" style="margin-bottom:0px;">
      	<?php echo $btn; ?>
       </p>   
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php if( isset( $_GET['add'] ) ) : ?>
	

<div class="row">
	<div class="col-lg-2">
    	<div class="input-group">
    	<span class="input-group-addon">เลขที่</span>
        <input type="text" class="form-control input-sm" value="<?php echo $reference; ?>" style="text-align:center;" disabled />
        </div>
    </div>
    <div class="col-lg-2">
    	<div class="input-group">
    	<span class="input-group-addon">วันที่</span>
        <input type="text" id="date_add" class="form-control input-sm" style="text-align:center;" placeholder="วันที่เอกสาร" value="<?php echo $date_add; ?>" <?php echo $active; ?>  />
        </div>
    </div>
    <div class="col-lg-4">
    	<div class="input-group">
    	<span class="input-group-addon">ลูกค้า</span>
        <input type="text" id="customer" class="form-control input-sm" placeholder="เลือกลูกค้า" value="<?php echo $customer; ?>" <?php echo $active; ?>  />
        </div>
        <input type="hidden" id="id_customer" value="<?php echo $id_customer; ?>" />
    </div>
    <div class="col-lg-4">
    	<div class="input-group">
    	<span class="input-group-addon">โซน</span>
        <input type="text" id="zone" class="form-control input-sm" placeholder="เลือกโซน" value="<?php echo $zone; ?>" <?php echo $active; ?>  />
        </div>
        <input type="hidden" id="id_zone" value="<?php echo $id_zone; ?>" />
    </div>
    <div class="col-lg-12" style="height:10px; padding:5px;">&nbsp;</div>
    <div class="col-lg-10">
    	<div class="input-group">
    	<span class="input-group-addon">หมายเหตุ</span>
        <input type="text" id="remark" class="form-control input-sm" placeholder="ระบุหมายเหตุ(ถ้ามี)" value="<?php echo $remark; ?>" <?php echo $active; ?> />
        </div>
    </div>
    <div class="col-lg-2">
        <?php if( isset( $id ) ) : ?>
        	<?php if( !$valid ) : ?>
            <button type="button" id="btn_edit" class="btn btn-warning btn-sm btn-block" onclick="edit()" style="" ><i class="fa fa-pencil"></i> แก้ไข</button>
            <button type="button" id="btn_update" class="btn btn-success btn-sm btn-block" onclick="update()" style="display:none;"><i class="fa fa-save"></i> บันทึก</button>
            <?php endif; ?>
       <?php else : ?>
       		<button type="button" id="btn_add" class="btn btn-success btn-sm btn-block" onclick="add_new()"><i class="fa fa-plus"></i> เพิ่มเอกสาร</button>
        <?php endif; ?>
        <input type="hidden" id="id_consign_check" value="<?php echo isset($id) ? $id : ""; ?>" />
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:15px;' />
<?php if( isset($id) ) : ?>
<div class="row">
	<div class="col-lg-3">
    	<div class="input-group">
        	<span class="input-group-addon">กล่อง</span>
            <input type="text" class="form-control input-sm" id="box" onBlur="set_focus()" placeholder="ยิงบาร์โค้ดกล่อง" autofocus="autofocus" />
        </div>
        <input type="hidden" name="id_box" id="id_box" value="" />
    </div>
    <div class="col-lg-1">
    	<button type="button" class="btn btn-default btn-sm btn-block" id="btn_change_box" onclick="change_box()"><i class="fa fa-refresh"></i></button>
    </div>
    <div class="col-lg-2">
    	<div class="input-group">
        	<span class="input-group-addon">จำนวน</span>
            <input type="text" class="form-control input-sm" id="qty" onBlur="set_focus()" value="1" disabled />
        </div>
    </div>
    <div class="col-lg-3">
    	<div class="input-group">
        	<span class="input-group-addon">สินค้า</span>
            <input type="text" class="form-control input-sm" id="barcode_item" onBlur="set_focus()" placeholder="ยิงบาร์โค้ดสินค้า" disabled />
        </div>
    </div>
    <div class="col-lg-1">
    	<button type="button" class="btn btn-default btn-sm btn-block" id="btn_check" onclick="check_item()"><i class="fa fa-check"></i> ตกลง</button>
    </div>
    <div class="col-lg-2">
    	<button type="button" id="btn_print_box" class="btn btn-info btn-sm btn-block" onclick="get_box_list()"><i class="fa fa-print"></i> พิมพ์กล่อง</button>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:15px;' />
<div class="row">
	<div class="col-lg-12" style="color: #333; text-align:center; font-size:24px;">จำนวนในกล่อง <span style="color: #C00; text-align:center; font-size:24px;" id="box_qty">0</span> ชิ้น</div>
	<div class="col-lg-12">
    	<table class="table table-striped">
            <th style="width: 5%; text-align:center;">ลำดับ</th>
            <th style="width: 15%;">บาร์โค้ด</th>
            <th style="width:20%;">รหัสสินค้า</th>
            <th style="width: 15%; text-align:center;">จำนวนสต็อก</th>
            <th style="width: 15%; text-align:center;">จำนวนนับ</th>
            <th style="width: 15%; text-align:center;">ยอดต่าง</th>
            <th style="text-align:right;">action</th>
        
        
	<?php $qs = dbQuery("SELECT id_consign_check_detail, tbl_consign_check_detail.id_product_attribute, qty_stock, qty_check, barcode, reference FROM tbl_consign_check_detail JOIN tbl_product_attribute ON tbl_consign_check_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_consign_check = ".$id); ?>
    <?php $n = 1; ?>
    <?php $total_stock = 0; $total_check = 0;  ?>
    <?php 	while($rs = dbFetchArray($qs) ) : ?>
    	<?php 	$idx = $rs['id_consign_check_detail']; ?>
        <?php 	$id_pda = $rs['id_product_attribute']; ?>
        <tr id="row_<?php echo $idx; ?>" style="font-size:12px;">
        	<td align="center"><?php echo $n; ?></td>
            <td><?php echo $rs['barcode']; ?></td>
            <td><?php echo $rs['reference']; ?></td>
            <td align="center"><span id="st_<?php echo $id_pda; ?>" class="st"><?php echo $rs['qty_stock']; ?></span></td>
            <td align="center"><span id="ck_<?php echo $id_pda; ?>" class="ck"><?php echo $rs['qty_check']; ?></span></td>
            <td align="center"><span id="df_<?php echo $id_pda; ?>" class="df"><?php echo $rs['qty_stock'] - $rs['qty_check']; ?></span></td>
            <td align="right">
            <?php if(!$valid) : ?>
            	<button type="button" class="btn btn-primary btn-xs" onclick="edit_row(<?php echo $idx; ?>, <?php echo $id_pda; ?>, '<?php echo $rs['reference']; ?>')" ><i class="fa fa-pencil"></i></button>
            <?php endif; ?>
            </td>
        </tr>
        <?php $n++; $total_stock += $rs['qty_stock']; $total_check += $rs['qty_check']; ?>
    <?php  	endwhile; ?>
    	<tr style="font-size:18px;">
        	<td colspan="3" align="right">รวม</td>
            <td align="center" id="total_stock"><?php echo $total_stock; ?></td>
            <td align="center" id="total_check"><?php echo $total_check; ?></td>
            <td align="center" id="total_diff" style=""><?php echo $total_stock - $total_check; ?></td>
            <td></td>
    	</tr>
    </table>
	</div>
</div>    
<?php endif; ?>

<?php elseif( isset( $_GET['edit'] ) && isset( $_GET['id_consign_check'] ) ) : ?>
<div class="row">
	<div class="col-lg-2">
    	<div class="input-group">
    	<span class="input-group-addon">เลขที่</span>
        <input type="text" class="form-control input-sm" value="<?php echo $reference; ?>" style="text-align:center;" disabled />
        </div>
    </div>
    <div class="col-lg-2">
    	<div class="input-group">
    	<span class="input-group-addon">วันที่</span>
        <input type="text" id="date_add" class="form-control input-sm" style="text-align:center;" placeholder="วันที่เอกสาร" value="<?php echo $date_add; ?>" <?php echo $active; ?>  />
        </div>
    </div>
    <div class="col-lg-4">
    	<div class="input-group">
    	<span class="input-group-addon">ลูกค้า</span>
        <input type="text" id="customer" class="form-control input-sm" placeholder="เลือกลูกค้า" value="<?php echo $customer; ?>" <?php echo $active; ?>  />
        </div>
        <input type="hidden" id="id_customer" value="<?php echo $id_customer; ?>" />
    </div>
    <div class="col-lg-4">
    	<div class="input-group">
    	<span class="input-group-addon">โซน</span>
        <input type="text" id="zone" class="form-control input-sm" placeholder="เลือกโซน" value="<?php echo $zone; ?>" <?php echo $active; ?>  />
        </div>
        <input type="hidden" id="id_zone" value="<?php echo $id_zone; ?>" />
    </div>
    <div class="col-lg-12" style="height:10px; padding:5px;">&nbsp;</div>
    <div class="col-lg-10">
    	<div class="input-group">
    	<span class="input-group-addon">หมายเหตุ</span>
        <input type="text" id="remark" class="form-control input-sm" placeholder="ระบุหมายเหตุ(ถ้ามี)" value="<?php echo $remark; ?>" <?php echo $active; ?> />
        </div>
    </div>
    <div class="col-lg-2">
        <?php if( isset( $id ) ) : ?>
        	<?php if( !$valid ) : ?>
            <button type="button" id="btn_edit" class="btn btn-warning btn-sm btn-block" onclick="edit()" style="" ><i class="fa fa-pencil"></i> แก้ไข</button>
            <button type="button" id="btn_update" class="btn btn-success btn-sm btn-block" onclick="update()" style="display:none;"><i class="fa fa-save"></i> บันทึก</button>
            <?php endif; ?>
       <?php else : ?>
       		<button type="button" id="btn_add" class="btn btn-success btn-sm btn-block" onclick="add_new()"><i class="fa fa-plus"></i> เพิ่มเอกสาร</button>
        <?php endif; ?>
        <input type="hidden" id="id_consign_check" value="<?php echo isset($id) ? $id : ""; ?>" />
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:15px;' />
<?php if( isset($id) ) : ?>
<div class="row">
	<div class="col-lg-3">
    	<div class="input-group">
        	<span class="input-group-addon">กล่อง</span>
            <input type="text" class="form-control input-sm" id="box" onBlur="set_focus()" placeholder="ยิงบาร์โค้ดกล่อง" autofocus="autofocus" />
        </div>
        <input type="hidden" name="id_box" id="id_box" value="" />
    </div>
    <div class="col-lg-1">
    	<button type="button" class="btn btn-default btn-sm btn-block" id="btn_change_box" onclick="change_box()"><i class="fa fa-refresh"></i></button>
    </div>
    <div class="col-lg-2">
    	<div class="input-group">
        	<span class="input-group-addon">จำนวน</span>
            <input type="text" class="form-control input-sm" id="qty" onBlur="set_focus()" value="1" disabled />
        </div>
    </div>
    <div class="col-lg-3">
    	<div class="input-group">
        	<span class="input-group-addon">สินค้า</span>
            <input type="text" class="form-control input-sm" id="barcode_item" onBlur="set_focus()" placeholder="ยิงบาร์โค้ดสินค้า" disabled />
        </div>
    </div>
    <div class="col-lg-1">
    	<button type="button" class="btn btn-default btn-sm btn-block" id="btn_check" onclick="check_item()"><i class="fa fa-check"></i> ตกลง</button>
    </div>
    <div class="col-lg-2">
    	<button type="button" id="btn_print_box" class="btn btn-info btn-sm btn-block" onclick="get_box_list()"><i class="fa fa-print"></i> พิมพ์กล่อง</button>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:15px;' />
<div class="row">
	<div class="col-lg-12" style="color: #333; text-align:center; font-size:24px;">จำนวนในกล่อง <span style="color: #C00; text-align:center; font-size:24px;" id="box_qty">0</span> ชิ้น</div>
	<div class="col-lg-12">
    	<table class="table table-striped">
            <th style="width: 5%; text-align:center;">ลำดับ</th>
            <th style="width: 15%;">บาร์โค้ด</th>
            <th style="width:20%;">รหัสสินค้า</th>
            <th style="width: 15%; text-align:center;">จำนวนสต็อก</th>
            <th style="width: 15%; text-align:center;">จำนวนนับ</th>
            <th style="width: 15%; text-align:center;">ยอดต่าง</th>
            <th style="text-align:right;">action</th>
        
        
	<?php $qs = dbQuery("SELECT id_consign_check_detail, tbl_consign_check_detail.id_product_attribute, qty_stock, qty_check, barcode, reference FROM tbl_consign_check_detail JOIN tbl_product_attribute ON tbl_consign_check_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_consign_check = ".$id); ?>
    <?php $n = 1; ?>
    <?php $total_stock = 0; $total_check = 0;  ?>
    <?php 	while($rs = dbFetchArray($qs) ) : ?>
    	<?php 	$idx = $rs['id_consign_check_detail']; ?>
        <?php 	$id_pda = $rs['id_product_attribute']; ?>
        <tr id="row_<?php echo $idx; ?>" style="font-size:12px;">
        	<td align="center"><?php echo $n; ?></td>
            <td><?php echo $rs['barcode']; ?></td>
            <td><?php echo $rs['reference']; ?></td>
            <td align="center"><span id="st_<?php echo $id_pda; ?>" class="st"><?php echo $rs['qty_stock']; ?></span></td>
            <td align="center"><span id="ck_<?php echo $id_pda; ?>" class="ck"><?php echo $rs['qty_check']; ?></span></td>
            <td align="center"><span id="df_<?php echo $id_pda; ?>" class="df"><?php echo $rs['qty_stock'] - $rs['qty_check']; ?></span></td>
            <td align="right">
            <?php if(!$valid) : ?>
            	<button type="button" class="btn btn-primary btn-xs" onclick="edit_row(<?php echo $idx; ?>, <?php echo $id_pda; ?>, '<?php echo $rs['reference']; ?>')" ><i class="fa fa-pencil"></i></button>
            <?php endif; ?>
            </td>
        </tr>
        <?php $n++; $total_stock += $rs['qty_stock']; $total_check += $rs['qty_check']; ?>
    <?php  	endwhile; ?>
    	<tr style="font-size:18px;">
        	<td colspan="3" align="right">รวม</td>
            <td align="center" id="total_stock"><?php echo $total_stock; ?></td>
            <td align="center" id="total_check"><?php echo $total_check; ?></td>
            <td align="center" id="total_diff" style=""><?php echo $total_stock - $total_check; ?></td>
            <td></td>
    	</tr>
    </table>
	</div>
</div>    
<?php endif; ?>
<?php elseif( isset($_GET['view_detail'] ) && isset($_GET['id_consign_check'] ) ) : ?>
<style>
 	.display
 	{ 
 		border: 0px;
		padding-left: 0px;
	}
</style>
<div class="row">
	<div class="col-lg-2">
    	<label>เลขที่</label>
        <span class="form-control input-sm display"><?php echo $reference; ?></span>
    </div>
    <div class="col-lg-2">
   		<label>วันที่</label>
        <span class="form-control input-sm display"><?php echo $date_add; ?></span>
    </div>
    <div class="col-lg-4">
    	<label>ลูกค้า</label>
        <span class="form-control input-sm display"><?php echo $customer; ?></span>
    </div>
    <div class="col-lg-4">
    	<label>โซน</label>
        <span class="form-control input-sm display"><?php echo $zone; ?></span>
    </div>
    <div class="col-lg-12" style="height:10px; padding:5px;">&nbsp;</div>
    <div class="col-lg-10">
    	<label>หมายเหตุ</label>
        <span class="input-sm display"><?php echo $remark; ?></span>
    </div>
    <input type="hidden" id="id_consign_check" value="<?php echo $_GET['id_consign_check']; ?>" />
</div>
<div class="row">
	<div class="col-lg-12">
    	<table class="table table-striped">
        	<tr>
            	<th colspan="6" style="text-align:center;">รายละเอียดการกระทบยอดสินค้าฝากขาย 
            		<span class="pull-right">
            			<button type="button" id="btn_print_box" class="btn btn-info btn-xs" onclick="get_box_list()"><i class="fa fa-print"></i> พิมพ์กล่อง</button>
            		</span>
            	</th>
            </tr>
            <tr>
                <th style="width: 5%; text-align:center;">ลำดับ</th>
                <th style="width: 15%;">บาร์โค้ด</th>
                <th style="width:20%;">รหัสสินค้า</th>
                <th style="width: 15%; text-align:center;">จำนวนสต็อก</th>
                <th style="width: 15%; text-align:center;">จำนวนนับ</th>
                <th style="width: 15%; text-align:center;">ยอดต่าง</th>
            </tr>
      
	<?php $qs = dbQuery("SELECT id_consign_check_detail, tbl_consign_check_detail.id_product_attribute, qty_stock, qty_check, barcode, reference FROM tbl_consign_check_detail JOIN tbl_product_attribute ON tbl_consign_check_detail.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE id_consign_check = ".$id); ?>
    <?php $n = 1; ?>
    <?php $total_stock = 0; $total_check = 0;  ?>
    <?php 	while($rs = dbFetchArray($qs) ) : ?>
    	<?php 	$idx = $rs['id_consign_check_detail']; ?>
        <?php 	$id_pda = $rs['id_product_attribute']; ?>
        <tr id="row_<?php echo $idx; ?>" style="font-size:12px;">
        	<td align="center"><?php echo $n; ?></td>
            <td><?php echo $rs['barcode']; ?></td>
            <td><?php echo $rs['reference']; ?></td>
            <td align="center"><span id="st_<?php echo $id_pda; ?>" class="st"><?php echo $rs['qty_stock']; ?></span></td>
            <td align="center"><span id="ck_<?php echo $id_pda; ?>" class="ck"><?php echo $rs['qty_check']; ?></span></td>
            <td align="center"><span id="df_<?php echo $id_pda; ?>" class="df"><?php echo $rs['qty_stock'] - $rs['qty_check']; ?></span></td>
            
        </tr>
        <?php $n++; $total_stock += $rs['qty_stock']; $total_check += $rs['qty_check']; ?>
    <?php  	endwhile; ?>
    	<tr style="font-size:18px;">
        	<td colspan="3" align="right">รวม</td>
            <td align="center" id="total_stock"><?php echo $total_stock; ?></td>
            <td align="center" id="total_check"><?php echo $total_check; ?></td>
            <td align="center" id="total_diff" style=""><?php echo $total_stock - $total_check; ?></td>
    	</tr>
    </table>
	</div>
</div> 

<?php else : ?>
<?php 
	$from_date 	= "";
	$to_date 	= "";
	$value 		= "";
	$filter			= "";
 	if(isset($_POST['from_date'])){ $from_date = $_POST['from_date'];  if($from_date != ""){ setcookie("from_date", dbDate($from_date), time() +3600, "/"); } }else if( isset($_COOKIE['from_date'])){ $from_date = thaiDate($_COOKIE['from_date']); }
	if(isset($_POST['to_date'])){ $to_date = $_POST['to_date']; if($to_date != ""){ setcookie("to_date", dbDate($to_date), time()+3600, "/"); } }else if( isset( $_COOKIE['to_date'] ) ){ $to_date = thaiDate($_COOKIE['to_date']); }
	if(isset($_POST['search_text'])){ 	$value = $_POST['search_text']; if($value != ""){ setcookie("consign_search_text", $value, time()+3600, "/");} }else if( isset($_COOKIE['consign_search_text'])){ $value = $_COOKIE['consign_search_text']; } 
	if(isset($_POST['filter'])){ $filter = $_POST['filter']; setcookie("consign_filter", $filter, time()+3600, "/"); }else if( isset( $_COOKIE['consign_filter'] ) ){ $filter = $_COOKIE['consign_filter']; }
?>
<form id="search_form" method="post">
<div class="row">
	<div class="col-lg-2">
    	<div class="input-group">
        	<span class="input-group-addon">ค้นหา</span>
            <select class="form-control input-sm" id="filter" name="filter">
            	<option value="reference" <?php echo isSelected($filter, "reference"); ?>>เอกสาร</option>
                <option value="customer" <?php echo isSelected($filter, "customer"); ?>>ลูกค้า</option>
            </select>
        </div>
    </div>
    <div class="col-lg-3">
    	<div class="input-group">
        	<span class="input-group-addon">คำค้น</span>
            <input type="text" class="form-control input-sm" id="search_text" name="search_text" placeholder="ระบุคำค้น" value="<?php echo $value; ?>" autofocus />
        </div>
    </div>
    <div class="col-lg-2">
    	<div class="input-group">
        	<span class="input-group-addon">จาก</span>
            <input type="text" class="form-control input-sm" id="from_date" name="from_date" placeholder="ระบุวันที่" value="<?php echo $from_date; ?>" />
        </div>
    </div>
     <div class="col-lg-2">
    	<div class="input-group">
        	<span class="input-group-addon">ถึง</span>
            <input type="text" class="form-control input-sm" id="to_date" name="to_date" placeholder="ระบุวันที่" value="<?php echo $to_date; ?>" />
        </div>
    </div>
    <div class="col-lg-1">
    	<button type="submit" class="btn btn-primary btn-sm btn-block" id="btn_search" onclick="get_search()"><i class="fa fa-search"></i> ค้นหา</button>
    </div>
    <div class="col-lg-2">
    	<button type="button" class="btn btn-warning btn-sm btn-block" id="btn_search" onclick="clear_filter()"><i class="fa fa-refresh"></i> เคลียร์ตัวกรอง</button>
    </div>
</div>
</form>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:0px;' />
<?php 
	$where = "WHERE id_consign_check != 0 ";
	if($value != "")
	{
		switch($filter)
		{
			case "reference" :
				$where .= "AND reference LIKE '%".$value."%' ";
			break;	
			case "customer" :
				$incause = customer_incause($value);
				$where .= "AND id_customer IN(".$incause.") ";
			break;
		}
	}
	if( $from_date != "" && $to_date != "")
	{
		$from = fromDate($from_date);
		$to 	= toDate($to_date);
		$where .= "AND ( date_add BETWEEN '".$from."' AND '".$to."') ";
	}
	$where .= "ORDER BY date_add DESC";
	$paginator = new paginator();
	if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows']; $paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
	$paginator->Per_Page("tbl_consign_check",$where,$get_rows);
	$paginator->display($get_rows,"index.php?content=consign_check");
	$Page_Start = $paginator->Page_Start;
	$Per_Page = $paginator->Per_Page;
	$qs = dbQuery("SELECT * FROM tbl_consign_check ".$where." LIMIT ".$paginator->Page_Start." ,". $paginator->Per_Page);
?>
<div class="row">
	<div class="col-lg-12">
    	<table class="table table-striped">
        	<thead style="font-size:14px;">
            	<th style="width: 5%; text-align:center;">ID</th>
                <th style="width: 10%;">เลขที่เอกสาร</th>
                <th style="width: 35%;">ลูกค้า</th>
                <th style="width: 15%;">สถานะ</th>
                <th style="width: 10%;">วันที่เอกสาร</th>
                <th style="width: 15%;">ปรับปรุงล่าสุด</th>
                <th style="text-align:center;">Action</th>
            </thead>
<?php if( dbNumRows($qs) > 0 ) : ?>
<?php 	while( $rs = dbFetchArray($qs) ) : ?>
			<tr style="font-size:12px;">
            	<td align="center"><?php echo $rs['id_consign_check']; ?></td>
                <td><?php echo $rs['reference']; ?></td>
                <td><?php echo customer_name($rs['id_customer']); ?></td>
                <td>
                	<?php if( $rs['consign_valid'] == 1 ) : ?>
                    <span style="color:green;">ตัดยอดฝากขายแล้ว</span>
                    <?php elseif( $rs['consign_valid'] == 2 ) : ?>
                    <span style="color:#FC0;">ยกเลิก</span>
                    <?php elseif( $rs['consign_valid'] == 0 ) : ?>
                    <span style="color:red;">ยังไม่ได้ตัดยอดฝากขาย</span>
                    <?php endif; ?>
                </td>
                <td><?php echo thaiDate($rs['date_add']); ?></td>
                <td><?php echo thaiDateTime($rs['date_upd']); ?></td>
                <td align="right">
                	<button type="button" class="btn btn-info btn-xs btn-block" onclick="view_detail(<?php echo $rs['id_consign_check']; ?>)">รายละเอียด</button>
                </td>
            </tr>
<?php 	endwhile; ?>            
<?php endif; ?>     
        </table>
    </div>
</div>
<?php endif; ?>
<input type="text" id="out_focus" style="position:absolute; top:-30px;" />
	<div class='modal fade' id='print_box' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' id='modal' style="width: 500px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'><i class="fa fa-times"></i></button>                 
				 </div>
				 <div class='modal-body' id='box_list'>
                 	
                 </div>
				 <div class='modal-footer'>
					<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
				 </div>
			</div>
		</div>
	</div>
     <script id="box_template" type="text/x-handlebars-template">
    <table class="table table-striped">
    	<thead>
        	<th style="width: 30%; text-align:center;"></th><th style="width: 30%; text-align:center;"></th><th style="width:30%; text-align:center;"></th><th></th>
        </thead>
        {{#each this}}
        	{{#if nocontent}}
            <tr>
            	<td colspan="3" align="center"><h4>-----  ไม่พบรายการใดๆ  -----</h4></td>
                
            </tr>
            {{else}}
            <tr>
            	<td align="center" style="vertical-align:middle;">กล่องที่ {{ box_no }} </td>
                <td align="center" style="vertical-align:middle;">{{ barcode }}</td>
                <td style="vertical-align:middle;"><button type="button" class="btn btn-info btn-sm btn-block" onclick="print_box({{ id_box }}, {{ id_consign_check }})"><i class="fa fa-print"></i> พิมพ์</button></td>
				<td style="vertical-align:middle;"><button type="button" class="btn btn-danger btn-xs" onClick="confirm_delete_box({{ id_box }}, {{ id_consign_check }})"><i class="fa fa-trash"></i></button></td>
            </tr>
            {{/if}}
        {{/each}}
    </table>
	</script>
    
    <div class='modal fade' id='remove_modal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' style="width: 500px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'><i class="fa fa-times"></i></button>                 
				 </div>
				 <div class='modal-body' id='remove_list'>
                 	
                 </div>
				 <div class='modal-footer'>
					<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
				 </div>
			</div>
		</div>
	</div>
   
    <script id="remove_template" type="text/x-handlebars-template">
    <table class="table table-striped">
    {{#each this}}
    	{{#if @first}}
    <tr><td colspan="4" align="center">{{ reference }}</td></tr>
    <tr>
    	<th style="width: 35%; text-align:center;">เวลา</th>
        <th style="width: 25%; text-align:center;">กล่อง</th>
        <th style="width: 15%; text-align:center;">จำนวน</th>
        <th style="width: 25%; text-align:center;">เอาออก</th>
 	</tr>
    	{{else}}
    <tr id="{{ id }}">
    	<td>{{ time }}</td>
        <td>{{ box }}</td>
        <td align="center">{{ qty }}</td>
        <td align="center"><button type="button" class="btn btn-danger btn-xs" onclick="remove_checked({{ id }}, {{ id_check_detail }}, {{ id_pda }})"><i class="fa fa-trash"></i></button></td>
    </tr>
    	{{/if}}
   {{/each}} 	
    </table>
	</script>
    
     <div class='modal fade' id='diff_modal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' style="width: 1000px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'><i class="fa fa-times"></i></button>                 
				 </div>
				 <div class='modal-body' id='diff_list'>
                 	
                 </div>
				 <div class='modal-footer'>
					<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
                    
				 </div>
			</div>
		</div>
	</div>
    
    <script id="diff_template" type="text/x-handlebars-template">
    <table class="table table-striped table-bordered">
    	<tr><th style="text-align:center;" colspan="6">ยอดต่าง <span class="pull-right"><button type="button" class="btn btn-info btn-xs" onclick="print_diff()"><i class="fa fa-print"></i>&nbsp; พิมพ์</button></span></th></tr>
        <tr>
        	<th style="width: 5%; text-align:center;">ลำดับ</th>
            <th style="width: 20%; text-align:center;">บาร์โค้ด</th>
            <th style="width: 30%; text-align:center;">รหัสสินค้า</th>
            <th style="width: 15%; text-align:center;">ราคา</th>
            <th style="width: 15%; text-align:center;">จำนวน</th>
            <th style="width: 15%; text-align:center;">มูลค่า</th>
        </tr>
        {{#each this}}
        	{{#if nocontent}}
            	<tr>
                	<td colspan="6" align="center"><h4>-----  ไม่พบรายการยอดต่าง  -----</h4></td>
                </tr>            
            {{else}}
            	{{#if @last}}
                <tr>
                	<td colspan="4" align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{ total_qty }}</strong></td>
                    <td align="right"><strong>{{ total_amount }}</strong></td>
                </tr>
                {{else}}
            	<tr>
                	<td align="center">{{ no }}</td>
                    <td>{{ barcode }}</td>
                    <td>{{ reference }}</td>
                    <td align="right">{{ price }}</td>
                    <td align="right">{{ qty }}</td>
                    <td align="right">{{ amount }}</td>
                </tr>
                {{/if}}
            {{/if}}        
        {{/each}}
    </table>
	</script>
    
     <div class='modal fade' id='balance_modal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' style="width: 1000px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'><i class="fa fa-times"></i></button>                 
				 </div>
				 <div class='modal-body' id='balance_list'>
                 	
                 </div>
				 <div class='modal-footer'>
					<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
                    
				 </div>
			</div>
		</div>
	</div>
    
    <script id="balance_template" type="text/x-handlebars-template">
    <table class="table table-striped table-bordered">
    	<tr><th style="text-align:center;" colspan="6">ยอดคงเหลือ <span class="pull-right"><button type="button" class="btn btn-info btn-xs" onclick="print_balance()"><i class="fa fa-print"></i>&nbsp; พิมพ์</button></span></th></tr>
        <tr>
        	<th style="width: 5%; text-align:center;">ลำดับ</th>
            <th style="width: 20%; text-align:center;">บาร์โค้ด</th>
            <th style="width: 30%; text-align:center;">รหัสสินค้า</th>
            <th style="width: 15%; text-align:center;">ราคา</th>
            <th style="width: 15%; text-align:center;">จำนวน</th>
            <th style="width: 15%; text-align:center;">มูลค่า</th>
        </tr>
        {{#each this}}
        	{{#if nocontent}}
            	<tr>
                	<td colspan="6" align="center"><h4>-----  ไม่พบรายการยอดต่าง  -----</h4></td>
                </tr>            
            {{else}}
            	{{#if @last}}
                <tr>
                	<td colspan="4" align="right"><strong>รวม</strong></td>
                    <td align="right"><strong>{{ total_qty }}</strong></td>
                    <td align="right"><strong>{{ total_amount }}</strong></td>
                </tr>
                {{else}}
            	<tr>
                	<td align="center">{{ no }}</td>
                    <td>{{ barcode }}</td>
                    <td>{{ reference }}</td>
                    <td align="right">{{ price }}</td>
                    <td align="right">{{ qty }}</td>
                    <td align="right">{{ amount }}</td>
                </tr>
                {{/if}}
            {{/if}}        
        {{/each}}
    </table>
	</script>
    
    <div class='modal fade' id='error_modal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' style="width: 1000px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'><i class="fa fa-times"></i></button>                 
				 </div>
				 <div class='modal-body' id='error_list'>
                 	
                 </div>
				 <div class='modal-footer'>
					<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
                    
				 </div>
			</div>
		</div>
	</div>
    
    <script id="error_template" type="text/x-handlebars-template">
    <table class="table table-striped table-bordered">
    	<tr><th style="text-align:center;" colspan="7"><h4>ข้อผิดพลาด</h4></th></tr>
        <tr style="font-size:14px;">
        	<th style="width: 5%; text-align:center;">ลำดับ</th>
            <th style="width: 15%; text-align:center;">บาร์โค้ด</th>
            <th style="width: 20%; text-align:center;">รหัสสินค้า</th>
            <th style="width: 10%; text-align:center;">จำนวน</th>
            <th style="width: 20%; text-align:center;">ข้อผิดพลาด</th>
			<th style="width: 20%; text-align:center;">เวลา</th>
			<th style="width: 10%; text-align:center;">กล่อง</th>
			
        </tr>
        {{#each this}}
        	{{#if nocontent}}
            	<tr>
                	<td colspan="7" align="center"><h4>-----  ไม่พบรายการที่ผิดพลาด  -----</h4></td>
                </tr>            
            {{else}}
            	{{#if @last}}
                <tr>
                	<td colspan="4" align="right"><strong>รวม</strong></td>
                    <td colspan="3" align="center"><strong>{{ total_qty }}</strong></td>
                </tr>
                {{else}}
            	<tr>
                	<td align="center">{{ no }}</td>
                    <td>{{ barcode }}</td>
                    <td>{{ reference }}</td>
                    <td align="right">{{ qty }}</td>
                    <td align="right">{{ error }}</td>
					<td align="right">{{ time }}</td>
					<td align="right">{{ box }}</td>
                </tr>
                {{/if}}
            {{/if}}        
        {{/each}}
    </table>
	</script>
</div><!-- container -->
<script>
function confirm_delete_box(id_box, id_con)
{
	swal({ 
			title: "ระวัง !!",
			text: "คุณแน่ใจว่าต้องการลบรายการกระทบยอดในกล่องนี้ แล้วยิงกระทบยอดกล่องนี้ใหม่",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
		  	confirmButtonText: "ใช่ ลบเลย",
		  	cancelButtonText: "ยกเลิก",
		 	 closeOnConfirm: false
			}, function(){
				delete_consign_box(id_box, id_con);
			});
}

function delete_consign_box(id_box, id_con)
{
	load_in();
	$.ajax({
		url:"controller/consignCheckController.php?delete_consign_box&id_consign_box="+id_box+"&id_consign_check="+id_con,
		type:"GET", cache:"false", success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == "success")
			{
				swal({ title: "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success"});
			}
			else if( rs == "no_item")
			{
				swal("ไม่พบรายการสินค้าในกล่อง");
			}
			else
			{
				swal("ไม่สำเร็จ","ลบรายการกระทบยอดในกล่องไม่สำเร็จ", "error");	
			}
		}
	});
}

function cancle_consign_check()
{
	var id = $("#id_consign_check").val();
	if(id == ""){ swal("ไม่พบ id ของเอกสาร กรุณาออกจากหน้านี้แล้วกลับเข้ามาใหม่"); return false; }
	load_in();
	$.ajax({
		url:"controller/consignCheckController.php?cancle_consign_check&id_consign_check="+id,
		type: "GET", cache:"false",
		success: function(rs)
		{
			load_out();
			var rs = $.trim(rs);
			if(rs == "success")
			{
				window.location.href = "index.php?content=consign_check&message=ลบเอกสารเรียบร้อยแล้ว";
			}
			else
			{
				swal("ไม่สามารถยกเลิกเอกสารได้");
			}
		}
	});				
}
function view_detail(id)
{
	window.location.href = "index.php?content=consign_check&view_detail=y&id_consign_check="+id;
}

function go_edit(id)
{
	window.location.href = "index.php?content=consign_check&edit=y&id_consign_check="+id;
}

function add_to_consign_sold(id)
{
	var checked = parseInt($("#total_check").html());
	if(checked > 0 )
	{
		window.location.href = "controller/consignController.php?add_consign_diff=y&id_consign_check="+id;
	}
	else
	{
		swal({ 
			title: "ระวัง !!",
			text: "คุณแน่ใจว่าต้องการดึงรายการทั้งหมดนี้ไปตัดยอดฝากขาย นี่หมายความว่า คุณขายสินค้าได้หมดทุกชิ้น หากคุณขายสินค้าได้หมดทุกชิ้นจริงๆ กดยืนยันเพื่อดำเนินการต่อ แต่หากไม่ใช่ กดยกเลิกเพื่อกลับไปตรวจเช็คสินค้า",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
		  	confirmButtonText: "ใช่ ลบเลย",
		  	cancelButtonText: "ยกเลิก",
		 	 closeOnConfirm: false
			}, function(){
				window.location.href = "controller/consignController.php?add_consign_diff=y&id_consign_check="+id;
			});
	}
}

function check_diff()
{
	var id 	= $("#id_consign_check").val();
	load_in();
	$.ajax({
		url:"controller/consignCheckController.php?check_diff&id_consign_check="+id,
		type:"GET", cache:"false",
		success: function(rs)
		{
			load_out();
			var rs 	= $.trim(rs);
			if( rs != "fail" && rs != "")
			{
				var data 		= $.parseJSON(rs);
				var source	= $("#diff_template").html();
				var output	= $("#diff_list");
				render(source, data, output);
				$("#diff_modal").modal("show");
			}
			else
			{
				swal("เกิดข้อผิดพลาด", "ไม่สามารถคำนวณยอดต่างได้ในขณะนี้ กรุณาลองใหม่อีกครั้งภายหลัง", "error");
			}
		}
	});
}

function check_balance()
{
	var id 	= $("#id_consign_check").val();
	load_in();
	$.ajax({
		url:"controller/consignCheckController.php?check_balance&id_consign_check="+id,
		type:"GET", cache:"false",
		success: function(rs)
		{
			load_out();
			var rs 	= $.trim(rs);
			if( rs != "fail" && rs != "")
			{
				var data 		= $.parseJSON(rs);
				var source	= $("#balance_template").html();
				var output	= $("#balance_list");
				render(source, data, output);
				$("#balance_modal").modal("show");
			}
			else
			{
				swal("เกิดข้อผิดพลาด", "ไม่สามารถคำนวณยอดคงเหลือได้ในขณะนี้ กรุณาลองใหม่อีกครั้งภายหลัง", "error");
			}
		}
	});
}

function check_fail()
{
	var id 	= $("#id_consign_check").val();
	load_in();
	$.ajax({
		url:"controller/consignCheckController.php?check_error&id_consign_check="+id,
		type:"GET", cache:"false",
		success: function(rs)
		{
			load_out();
			var rs 	= $.trim(rs);
			if( rs != "fail" && rs != "")
			{
				var data 		= $.parseJSON(rs);
				var source	= $("#error_template").html();
				var output	= $("#error_list");
				render(source, data, output);
				$("#error_modal").modal("show");
			}
			else
			{
				swal("เกิดข้อผิดพลาด", "ไม่สามารถค้นหารายการที่ผิดพลาดได้ในขณะนี้ กรุณาลองใหม่อีกครั้งภายหลัง", "error");
			}
		}
	});
}

function remove_checked(id, id_checked, id_pda)
{
	$.ajax({
		url:"controller/consignCheckController.php?remove_checked",
		type:"POST", cache:"false", data: { "id_consign_box_detail" : id, "id_consign_check_detail" : id_checked },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs != "fail" && rs != "")
			{
				var qty 	= parseInt(rs);
				var st		= parseInt($("#st_"+id_pda).html());
				var ck 	= parseInt($("#ck_"+id_pda).html());
				ck = ck - qty;
				df = st - ck;
				$("#df_"+id_pda).html(df);
				$("#ck_"+id_pda).html(ck);
				total_recal();
				$("#"+id).children(this).css("background-color", "red");
				$("#"+id).animate({opacity:0.1}, 1000, function(){$("#"+id).remove(); });
			}
			else
			{
				alert("Fail to remove");
			}			
		}
	});
}

function edit_row(id_consign_check_detail, id_product_attribute, reference)
{
	var qty = parseInt($("#ck_"+id_product_attribute).html());
	console.log(qty);
	if(qty >0)
	{
		var id 	= $("#id_consign_check").val();
		$.ajax({
			url:"controller/consignCheckController.php?get_checked_detail",
			type:"POST", cache:"false", data: { "id_consign_check" : id, "id_consign_check_detail" : id_consign_check_detail, "id_product_attribute" : id_product_attribute, "reference" : reference },
			success: function(rs)
			{
				var rs = $.trim(rs);
				var source 	= $("#remove_template").html();
				var data		= $.parseJSON(rs);
				var output	= $("#remove_list");
				render(source, data, output);
				$("#remove_modal").modal("show");
			}
		});
	}
	else
	{
		console.log("No data to remove");	
	}
}
function total_recal()
{
	var st = stock_qty();
	var ck = check_qty();
	var df = st - ck;
	$("#total_stock").html(st);
	$("#total_check").html(ck);
	$("#total_diff").html(df);
}
function get_box_list()
{
	var id_c		= $("#id_consign_check").val();	
	$.ajax({
		url:"controller/consignCheckController.php?get_box_list&id_consign_check="+id_c,
		type: "GET", cache:"false", success: function(rs)
		{
			var rs = $.trim(rs);
			var source = $("#box_template").html();
			var data 		= $.parseJSON(rs);
			var output	= $("#box_list");
			render(source, data, output);
			$("#print_box").modal("show");
		}
	});	
}

function print_box(id_box, id_consign_check)
{
	var page = "controller/consignCheckController.php?print_box&id_box="+id_box+"&id_consign_check="+id_consign_check;
	window.open(page, "_blank", "width=800, height=1000, scrollbars=yes");
}
function stock_qty()
{
	qty = 0;
	$(".st").each(function(index, element) {
        var qt = parseInt($(this).html());
		qty += qt;
    });
	return qty;
}

function check_qty()
{
	qty = 0;
	$(".ck").each(function(index, element) {
        var qt = parseInt($(this).html());
		qty += qt;
    });	
	return qty;
}

function check_item()
{
	var id_cc		= $("#id_consign_check").val();
	var id_box		= $("#id_box").val();
	var barcode	= $("#barcode_item").val();
	var qty			= $("#qty").val();
	$("#qty").val(1);
	$("#barcode_item").val("");
	if( barcode != "")
	{
		$("#barcode_item").attr("disabled", "disabeld");
		if(id_cc == ""){ swal("ไม่พบ id_consign_check ลองออกจากหน้านี้แล้วเข้าใหม่"); return false; }
		if( id_box == ""){ swal("ยิงบาร์โค้ดกล่องก่อน"); return false; }
		if( isNaN(parseInt(qty)) ){ swal("รูปแบบตัวเลขในช่องจำนวนไม่ถูกต้อง"); return false; }
		
		$.ajax({
			url:"controller/consignCheckController.php?check_item",
			type:"POST", cache:"false", data: { "id_consign_check" : id_cc, "id_box" : id_box, "barcode" : barcode, "qty" : qty },
			success: function(rs)
			{
				$("#barcode_item").removeAttr("disabled");
				$("#barcode_item").focus();
				var rs = $.trim(rs);
				if( rs != "fail" && rs != "wrong" && rs != "")
				{
					var arr = rs.split(" | ");
					id		= arr[0];
					qtyx	= parseInt(arr[1]);
					var ck = parseInt($("#ck_"+id).html());
					var st  = parseInt($("#st_"+id).html());
					var ck  = ck + qtyx;
					var df  = st - ck;
					$("#ck_"+id).html(ck);
					$("#df_"+id).html(df);
					update_box_qty(id_box);
					total_recal();
				}
				else if( rs =="wrong")
				{
					beep();
					swal("ข้อผิดพลาด !!","สินค้าไม่มีในรายการฝากขาย", "error");
				}
				else if( rs =="fail")
				{
					beep();
					swal("ข้อผิดพลาด !!","ไม่มีสินค้านี้ในฐานข้อมูล", "error");
				}
			}
		});
	}		
}

function edit()
{	
	$("#date_add").removeAttr("disabled");
	$("#customer").removeAttr("disabled");
	$("#remark").removeAttr("disabled");
	$("#btn_edit").css("display", "none");
	$("#btn_update").css("display","");
}

function update()
{
	var id 				= $("#id_consign_check").val();
	var date 				= $("#date_add").val();	
	var id_customer	= $("#id_customer").val();
	var customer		= $("#customer").val();
	var id_zone			= $("#id_zone").val();
	var zone 			= $("#zone").val();
	var remark			= $("#remark").val(); 
	if( !isDate(date) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	if( id_customer == "" || customer == "" ){ swal("กรุณาเลือกลูกค้า"); return false; }
	if( id_zone == "" || zone == ""){ swal("กรุณาเลือกโซน"); return false; }
	load_in();
	load_in();
	$.ajax({
		url:"controller/consignCheckController.php?update",
		type: "POST", cache: "false", data:{ "id_consign_check" : id, "id_customer" : id_customer, "id_zone" : id_zone, "date_add" : date, "remark" : remark },
		success: function(rs)
		{
			load_out();
			var rs = $.trim(rs);
			if(rs == "success" )
			{
				$("#date_add").attr("disabled", "disabled");
				$("#customer").attr("disabled", "disabled");
				$("#remark").attr("disabled", "disabled");
				$("#btn_update").css("display","none");
				$("#btn_edit").css("display", "");
				swal({ title : "สำเร็จ", text : "อัพเดตข้อมูลเรียบร้อย", timer : 1000, type : "success" });
			}
			else
			{
				swal("ปรับปรุงข้อมูลไม่สำเร็จ");
			}
		}
	});
}

function add_new()
{
	var date 				= $("#date_add").val();	
	var id_customer	= $("#id_customer").val();
	var customer		= $("#customer").val();
	var id_zone			= $("#id_zone").val();
	var zone 			= $("#zone").val();
	var remark			= $("#remark").val();
	if( !isDate(date) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	if( id_customer == "" || customer == "" ){ swal("กรุณาเลือกลูกค้า"); return false; }
	if( id_zone == "" || zone == ""){ swal("กรุณาเลือกโซน"); return false; }
	load_in();
	$.ajax({
		url:"controller/consignCheckController.php?add_new",
		type: "POST", cache: "false", data:{ "id_customer" : id_customer, "id_zone" : id_zone, "date_add" : date, "remark" : remark },
		success: function(rs)
		{
			load_out();
			var rs = $.trim(rs);
			var id  = parseInt(rs);
			if( !isNaN(id) )
			{
				window.location.href = "index.php?content=consign_check&add=y&id_consign_check="+id;
			}
			else
			{
				swal("เพิ่มเอกสารไม่สำเร็จ");
			}
		}
	});
}

$("#box").keyup(function(e){
	if(e.keyCode == 13)
	{
		get_box();
	}
});

$("#barcode_item").keyup(function(e){
	if(e.keyCode == 13)
	{
		check_item();
	}
});

function get_box()
{
	var barcode = $("#box").val();
	var id 		= $("#id_consign_check").val();
	if(barcode == ""){ swal("ยิงบาร์โค้ดกล่อง");	return false; }
	if(id == ""){ swal("ไม่พบ id_consign_check กรุณาออกจากหน้านี้แล้วเข้าใหม่อีกครั้ง"); return false; }
	load_in();
	$.ajax({
		url:"controller/consignCheckController.php?get_box",
		type:"POST", cache:"false", data: { "barcode" : barcode, "id_consign_check" : id },
		success: function(rs)
		{
			load_out();
			var rs = $.trim(rs);
			if(rs != 0 && !isNaN(parseInt(rs)))
			{
				update_box_qty(rs);
				$("#id_box").val(rs);
				$("#box").attr("disabled", "disabled");
				$("#qty").removeAttr("disabled");
				$("#barcode_item").removeAttr("disabled", "disabled");
				$("#barcode_item").focus();
			}
			else
			{
				swal("ไม่พบกล่องหรือไม่สามารถเพิ่มกล่องใหม่ได้");	
			}
		}
	});
}
function update_box_qty(id)
{
	var id_cc = $("#id_consign_check").val();
	$.ajax({
		url:"controller/consignCheckController.php?get_qty_in_box&id_box="+id+"&id_consign_check="+id_cc,
		type:"GET", cache:"false", success: function(rs){
			var rs = $.trim(rs);
			$("#box_qty").text(rs);
		}
	});
}
function change_box()
{
	$("#id_box").val("");
	$("#box").val("");
	$("#box").removeAttr("disabled");
	$("#qty").attr("disabled", "disabled");
	$("#barcode_item").attr("disabled", "disabled");
	$("#box_qty").text(0);
	swal("ยิงบาร์โค้ดกล่องก่อน");
	$("#box").focus();	
}


$("#customer").autocomplete({
	source: "controller/autoComplete.php?get_customer",
	autoFocus: true,
	close: function(){
		var rs 		= $(this).val();
		var arr 		= rs.split(" | ");
		var code  	= arr[0];
		var name 	= arr[1];
		var id 		= arr[2];
		$(this).val(name);
		$("#id_customer").val(id);
	}
});

$("#zone").autocomplete({
	source: "controller/autoComplete.php?consign_zone",
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(" | ");
		var zone = arr[0];
		var id 	= arr[1];
		$(this).val(zone);
		$("#id_zone").val(id);
	}
});

$("#date_add").datepicker({ dateFormat: "dd-mm-yy" });
function go_back()
{
	window.location.href = "index.php?content=consign_check";
}

function go_add()
{
	window.location.href = "index.php?content=consign_check&add=y";	
}

$(document).ready(function(e) {
    var id_box = $("#id_box").val();
	if(id_box == "")
	{
		swal("ยิงบาร์โค้ดกล่อง");
		$("#box").focus();	
	}
});		
function clear_filter()
{
	$.ajax({
		url:"controller/consignCheckController.php?clear_filter"	,
		type: "POST", cache: "false", 
		success: function(rs)
		{
			window.location.href = "index.php?content=consign_check";
		}
	});			
}

$("#from_date").datepicker({ 	dateFormat : "dd-mm-yy", onClose: function(selectDate){ $("#to_date").datepicker("option", "minDate", selectDate); }});
$("#to_date").datepicker({ dateFormat: "dd-mm-yy", onClose: function(selectDate){ $("#from_date").datepicker("option", "maxDate", selectDate); }});

function print_diff()
{
	var id = $("#id_consign_check").val();
	window.open("controller/consignController.php?print_diff&id_consign_check="+id, "_blank", "width=800, height=1000, scrollbars=yes");
}

function print_balance()
{
	var id = $("#id_consign_check").val();
	window.open("controller/consignController.php?print_balance&id_consign_check="+id, "_blank", "width=800, height=1000, scrollbars=yes");	
}
function get_row()
{
	$("#rows").submit();
}

</script>
<script src="../library/js/beep.js"></script>
