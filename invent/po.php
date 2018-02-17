<?php 
	$page_name = "ใบสั่งซื้อ/PO";
	$id_tab = 46;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	$ps = checkAccess($id_profile, 50);
	$close = $ps['add']+$ps['edit']+$ps['delete'];
	accessDeny($view);
	include "function/po_helper.php"; 
  	$btn_back = "<button type='button' class='btn btn-warning btn-sm' onclick='go_back()' ><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button>";
	$btn = "";
  	if( isset( $_GET['add'] ) && isset( $_GET['id_po'] ) )
	{
		$btn .= $btn_back;
		if($add){ $btn .= "<button type='button' class='btn btn-success btn-sm' onclick='save_add()' ><i class='fa fa-save'></i>&nbsp; บันทึก</button>"; }
	}else if( isset( $_GET['add'] ) && !isset( $_GET['id_po'] ) ){
		$btn .= "<button type='button' class='btn btn-primary btn-sm' onClick='check_amount()'><i class='fa fa-clock-o'></i>&nbsp; ตรวจสอบมูลค่าสั่งซื้อเดือนนี้</button>";
		$btn .= $btn_back;
	}else if( isset($_GET['edit']) ){
		$btn .= $btn_back;
		$p = new po($_GET['id_po']);
		if($close)
		{
			if($p->valid == 0 && $p->status != 0 ){ $btn .= "<button type='button' class='btn btn-danger btn-sm' id='btn_close_po' onclick='close_po()' ><i class='fa fa-lock'></i>&nbsp; ปิดใบสั่งซื้อ</button>"; }
			if($p->valid == 1 ){ $btn .= "<button type='button' class='btn btn-primary btn-sm' id='btn_cancle_close' onclick='cancle_close_po()'><i class='fa fa-unlock'></i>&nbsp; ยกเลิกการปิดใบสั่งซื้อ</button>"; }			 
		}
		if($edit){ $btn .= "<button type='button'class='btn btn-success btn-sm' id='btn_save_edit' onclick='save_edit()' ><i class='fa fa-save'></i>&nbsp; บันทึก</button>"; }
		$btn .= "<button type='button' class='btn btn-info btn-sm' onclick='print_po()' ><i class='fa fa-print'></i>&nbsp; พิมพ์</button>"; 
	}else if( isset($_GET['view_detail']) ){
		$btn .= $btn_back;
		$p = new po($_GET['id_po']);
		if($close)
		{
			if($p->valid == 0 && $p->status != 0 ){ $btn .= "<button type='button' class='btn btn-danger btn-sm' id='btn_close_po' onclick='close_po()' ><i class='fa fa-lock'></i>&nbsp; ปิดใบสั่งซื้อ</button>"; }
			if($p->valid == 1 ){ $btn .= "<button type='button' class='btn btn-primary btn-sm' id='btn_cancle_close' onclick='cancle_close_po()'><i class='fa fa-unlock'></i>&nbsp; ยกเลิกการปิดใบสั่งซื้อ</button>"; }			 
		}
		if($edit){ $btn .= "<button type='button' class='btn btn-primary btn-sm' id='btn_go_edit' onclick='go_edit()' ><i class='fa fa-pencil'></i>&nbsp; แก้ไข</button>"; }
		if($add){ $btn .= "<button type='button' class='btn btn-success btn-sm' onclick='add()' ><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button>"; }
		$btn .= "<button type='button' class='btn btn-info btn-sm' onclick='print_po()' ><i class='fa fa-print'></i>&nbsp; พิมพ์</button>"; 
		$btn .= "<button type='button' class='btn btn-info btn-sm' onclick='print_barcode()' ><i class='fa fa-barcode'></i>&nbsp; พิมพ์</button>"; 
	}else{
		$btn .= '<button type="button" class="btn btn-sm btn-info" onclick="updateReceived()"><i class="fa fa-retweet"></i> &nbsp; คำนวนยอดรับสินค้าใหม่</button>';
		if($add){ $btn .= "<button type='button' class='btn btn-success btn-sm' onclick='add()' ><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button>"; }
		
	}
  
	?>
 <style>
 	.input-xs{
		height:20px;
		padding: 2px 5px;
		font-size: 12px;
		line-height: 15px;
		border-radius: 3px;
	}
 </style>   
<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-sm-6" style="margin-top:10px;"><h4 class="title"><i class="fa fa-archive"></i>&nbsp;<?php echo $page_name; ?></h4>
	</div>
    <div class="col-sm-6">
      <p class="pull-right" style="margin-bottom:0px;">
		<?php echo $btn; ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php
if( isset( $_GET['add'] ) ) :
?>
<!-----------------------------------------------  Add  ----------------------------------->
    <?php if( !isset($_GET['id_po']) ) : ?>
<div class="row">
	<div class="col-sm-2">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm" id="reference" name="reference" placeholder="เลขที่ PO" style="text-align:center;" onblur="check_reference('')" />
    </div>
    <div class="col-sm-2">
    	<label>วันที่เอกสาร</label>
        <input type="text" name="date_add" id="date_add" class="form-control input-sm" style="text-align:center;" placeholder="วันที่เอกสาร" value="<?php echo date("d-m-Y"); ?>" />
    </div>
    <div class="col-sm-2">
    	<label>รหัสผู้ขาย</label>
        <input type="text" name="s_code" id="s_code" class="form-control input-sm" style="text-align:center" placeholder="รหัสผู้ขาย"  />
        <input type="hidden" name="s_id" id="s_id" />
    </div>
    <div class="col-sm-4">
    	<label>ชื่อผู้ขาย</label>
        <input type="text" name="s_name" id="s_name" class="form-control input-sm" style="text-align:center" placeholder="ชื่อผู้ขาย/บริษัท" />
    </div>
    <div class="col-sm-2">
    	<label>กำหนดรับสินค้า</label>
        <input type="text" name="due_date" id="due_date" class="form-control input-sm" style="text-align:center" placeholder="วันที่ต้องการใช้" />
    </div>
    
    <div class="col-sm-12">&nbsp;</div>
    
    <div class="col-sm-2">
        <select name="role" id="role" class="form-control input-sm">
        	<?php echo select_po_role(); ?>
        </select>
    </div>
    <div class="col-sm-8">
        <input type="text" name="remark" id="remark" class="form-control input-sm" placeholder="ระบุหมายเหตุ (ถ้ามี)" />
    </div>
    <div class="col-sm-2">
        <button type="button" class="btn btn-success btn-block" id="btn_add" onclick="add_new_po()"><i class="fa fa-plus"></i>&nbsp; เพิ่ม</button>
        <input type="hidden" id="is_duplicate" value="0"  />
    </div>
</div>    
    <?php else : ?>
    <?php 	$po = new po($_GET['id_po']); 	?>
<div class="row">
	<div class="col-sm-2">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm" id="reference" name="reference" placeholder="เลขที่ PO" style="text-align:center;" value="<?php echo $po->reference; ?>" disabled />
        <input type="hidden" name="id_po" id="id_po" value="<?php echo $po->id_po; ?>"  />
    </div>
    <div class="col-sm-2">
    	<label>วันที่เอกสาร</label>
        <input type="text" name="date_add" id="date_add" class="form-control input-sm" style="text-align:center;" placeholder="วันที่เอกสาร" value="<?php echo thaiDate($po->date_add); ?>" disabled />
    </div>
    <div class="col-sm-2">
    	<label>รหัสผู้ขาย</label>
        <input type="text" name="s_code" id="s_code" class="form-control input-sm" style="text-align:center" placeholder="รหัสผู้ขาย" value="<?php echo supplier_code($po->id_supplier); ?>" disabled />
        <input type="hidden" name="s_id" id="s_id" value="<?php echo $po->id_supplier; ?>" />
    </div>
    <div class="col-sm-4">
    	<label>ชื่อผู้ขาย</label>
        <input type="text" name="s_name" id="s_name" class="form-control input-sm" style="text-align:center" placeholder="ชื่อผู้ขาย/บริษัท" value="<?php echo supplier_name($po->id_supplier);  ?>" disabled />
    </div>
    <div class="col-sm-2">
    	<label>กำหนดรับสินค้า</label>
        <input type="text" name="due_date" id="due_date" class="form-control input-sm" style="text-align:center" placeholder="วันที่ต้องการใช้" value="<?php echo thaiDate($po->due_date); ?>" disabled />
    </div>
    
    <div class="col-sm-12">&nbsp;</div>
    
    <div class="col-sm-2">
        <select name="role" id="role" class="form-control input-sm" disabled>
        	<?php echo select_po_role($po->role); ?>
        </select>
    </div>
    <div class="col-sm-8">
        <input type="text" name="remark" id="remark" class="form-control input-sm" placeholder="ระบุหมายเหตุ (ถ้ามี)" value="<?php echo $po->remark; ?>" disabled />
    </div>
    <div class="col-sm-2">
        <button type="button" class="btn btn-sm btn-warning btn-block" id="btn_edit" onclick="edit()" ><i class="fa fa-pencil"></i>&nbsp; แก้ไข</button>
        <button type="button" class="btn btn-sm btn-success btn-block" id="btn_update" style="display:none; margin-top:0px;" onclick="update()"><i class="fa fa-save"></i>&nbsp; บันทึก</button>
    </div>
</div>    

<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-sm-3">
        <input type="text" name="product_code" id="product_code" class="form-control input-sm" placeholder="ค้นหาตามรหัสสินค้า" />
    </div>
    <div class="col-sm-2">
        <button type="button" id="btn_grid" onclick="get_product()" class="btn btn-sm btn-primary btn-block"><i class="fa fa-tags"></i>&nbsp; ค้นหา</button>
    </div>
    <div class="col-sm-2">
    	<button type="button" id="btn_info" onclick="get_info()" class="btn btn-sm btn-success btn-block"><i class="fa fa-eye"> ประวัติ (รุ่น)</i></button>
    </div>
    <div class="col-sm-2">
    	<button type="button" id="btn_info" onclick="get_info_detail()" class="btn btn-sm btn-info btn-block"><i class="fa fa-eye"> ประวัติ (รายการ)</i></button>
    </div>
    <div class="col-sm-1">&nbsp;</div>
   <div class="col-sm-2">
    	<button type="button" class="btn btn-sm btn-danger btn-block" onclick="delete_all()"><i class="fa fa-exclamation-triangle"></i>&nbsp; ลบทั้งหมด</button>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<form id="add_form">
<div class="row">
	<div class="col-sm-12">
    	<table class="table table-bordered" style="margin-bottom:0px;">
        	<thead style="font-size:14px;">
            	<th style="width:5%; text-align:center">ลำดับ</th>
                <th style="width:15%;">รหัสสินค้า</th>
                <th>รายละเอียด</th>
                <th style="width:6%; text-align:right;">จำนวน</th>
                <th style="width:6%; text-align:right;">ราคา/หน่วย</th>
                <th style="width:6%; text-align:center">ส่วนลด</th>
                <th style="width:8%; text-align:center;">หน่วย</th>
                <th style="width:10%; text-align:right;">จำนวนเงิน</th>
                <th style="width:5%; text-align:right"></th>
            </thead>
            <tbody id="result">
       			<tr id="bf"><td colspan="10" style="height:50px; border-left:solid 1px #DDD; border-right:solid 1px #DDD; border-top:0px;"><center><h4>-----  ไม่มีรายการ  -----</h4></center></td></tr>
            </tbody>
        </table>
        <table class="table table-bordered" style="margin-bottom:50px;">
        	<tbody style="width:1140px;">
        	<tr>
            	<td style="width:60%; text-align:right;">
                	ส่วนลดท้ายบิล : <input type="text" name="bill_discount" id="bill_discount" class="form-control input-sm" style="display:inline; width:150px; text-align:right" value="0.00" onkeyup="total_recal()"  />
                </td>
                <td style="width:20%;"><strong>จำนวนรวม</strong></td>
                <td align="right" style="padding-right:5px;"><span id="total_qty">0</span></td>
            </tr>
            <tr>
            	<td id="remark2" rowspan="3" style="font-size:12px;"><strong>หมายเหตุ : </strong><?php echo $po->remark; ?></td>
                <td><strong>ราคารวม</strong></td>
                <td align="right" style="padding-right:5px;"><span id="total_price">0.00</span></td>
            </tr>
            <tr>
                <td><strong>ส่วนลดรวม</strong></td>
                <td align="right" style="padding-right:5px;"><span id="total_discount">0.00</span></td>
            </tr>
            <tr>
                <td><strong>ยอดเงินสุทธิ</strong></td>
                <td align="right" style="padding-right:5px;"><span id="total_amount">0.00</span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
</form>

    <?php endif; ?>
<!-----------------------------------------------  Add  ----------------------------------->
<?php
elseif( isset( $_GET['edit'] ) && isset( $_GET['id_po'] ) ) : 
?>
<!-----------------------------------------------  Edit  ----------------------------------->
	<?php 	$id_po	= $_GET['id_po']; ?>
    <?php 	$po = new po($id_po); 	?>
    <?php	if($po->valid == 1 ) : ?>
 	<div class="row">
    	<div class="col-sm-6 col-sm-offset-3">
        	<div class="alert alert-warning">
            	<center><h4><i class="fa fa-exclamation-triangle"></i>&nbsp; ใบสั่งซื้อนี้ถูกปิดแล้ว ไม่อนุญาติให้แก้ไข</h4></center>
            </div>
        </div>
    </div>
    <script>
         $("#btn_save_edit").remove();
		 $("#btn_close").remove();
	</script>
    <?php 	else : ?>
<div class="row">
	<div class="col-sm-2">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm" name="reference" id="reference" placeholder="เลขที่ PO" style="text-align:center;" value="<?php echo $po->reference; ?>" onblur="check_reference(<?php echo $po->id_po; ?>)" disabled  />
        <input type="hidden" name="id_po" id="id_po" value="<?php echo $po->id_po; ?>"  />
    </div>
    <div class="col-sm-2">
    	<label>วันที่เอกสาร</label>
        <input type="text" name="date_add" id="date_add" class="form-control input-sm" style="text-align:center;" placeholder="วันที่เอกสาร" value="<?php echo thaiDate($po->date_add); ?>" disabled />
    </div>
    <div class="col-sm-2">
    	<label>รหัสผู้ขาย</label>
        <input type="text" name="s_code" id="s_code" class="form-control input-sm" style="text-align:center" placeholder="รหัสผู้ขาย" value="<?php echo supplier_code($po->id_supplier); ?>" disabled />
        <input type="hidden" name="s_id" id="s_id" value="<?php echo $po->id_supplier; ?>" />
    </div>
    <div class="col-sm-4">
    	<label>ชื่อผู้ขาย</label>
        <input type="text" name="s_name" id="s_name" class="form-control input-sm" style="text-align:center" placeholder="ชื่อผู้ขาย/บริษัท" value="<?php echo supplier_name($po->id_supplier);  ?>" disabled />
    </div>
    <div class="col-sm-2">
    	<label>กำหนดรับสินค้า</label>
        <input type="text" name="due_date" id="due_date" class="form-control input-sm" style="text-align:center" placeholder="วันที่ต้องการใช้" value="<?php echo thaiDate($po->due_date); ?>" disabled />
    </div>
    
    <div class="col-sm-12">&nbsp;</div>
    <div class="col-sm-2">
        <select name="role" id="role" class="form-control input-sm" disabled>
        	<?php echo select_po_role($po->role); ?>
        </select>
    </div>
    <div class="col-sm-8">
        <input type="text" name="remark" id="remark" class="form-control input-sm" placeholder="ระบุหมายเหตุ (ถ้ามี)" value="<?php echo $po->remark; ?>" disabled />
    </div>
    <div class="col-sm-2">
        <button type="button" class="btn btn-sm btn-warning btn-block" id="btn_edit" onclick="edit()" ><i class="fa fa-pencil"></i>&nbsp; แก้ไข</button>
        <button type="button" class="btn btn-sm btn-success btn-block" id="btn_update" style="display:none; margin-top:0px;" onclick="update()"><i class="fa fa-save"></i>&nbsp; บันทึก</button>
        <input type="hidden" id="is_duplicate" value="0"  />
    </div>
</div>    

<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-sm-3">
        <input type="text" name="product_code" id="product_code" class="form-control input-sm" placeholder="ค้นหาตามรหัสสินค้า" />
    </div>
    <div class="col-sm-2">
        <button type="button" id="btn_grid" onclick="get_product()" class="btn btn-sm btn-primary btn-block"><i class="fa fa-tags"></i>&nbsp; ค้นหา</button>
    </div>
     <div class="col-sm-2">
    	<button type="button" id="btn_info" onclick="get_info()" class="btn btn-sm btn-success btn-block"><i class="fa fa-eye"> ประวัติ (รุ่น)</i></button>
    </div>
    <div class="col-sm-2">
    	<button type="button" id="btn_info" onclick="get_info_detail()" class="btn btn-sm btn-info btn-block"><i class="fa fa-eye"> ประวัติ (รายการ)</i></button>
    </div>
    <div class="col-sm-1">&nbsp;</div>
   <div class="col-sm-2">
    	<button type="button" class="btn btn-sm btn-danger btn-block" onclick="delete_all()"><i class="fa fa-exclamation-triangle"></i>&nbsp; ลบทั้งหมด</button>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<form id="add_form">
<div class="row">
	<div class="col-sm-12">
    	<table class="table table-bordered" style="margin-bottom:0px;">
        	<thead style="font-size:14px;">
            	<th style="width:5%; text-align:center">ลำดับ</th>
                <th style="width:15%;">รหัสสินค้า</th>
                <th>รายละเอียด</th>
                <th style="width:6%; text-align:right;">จำนวน</th>
                <th style="width:6%; text-align:right;">ราคา/หน่วย</th>
                <th style="width:6%; text-align:center">ส่วนลด</th>
                <th style="width:8%; text-align:center;">หน่วย</th>
                <th style="width:10%; text-align:right;">จำนวนเงิน</th>
                <th style="width:5%; text-align:right"></th>
            </thead>
            <tbody id="result">
	<?php    $qs = $po->get_detail($id_po); ?>
    <?php	$total_qty = 0; $total_discount = 0; $total_amount = 0; $total_price = 0; ?>
    <?php 	if(dbNumRows($qs) > 0 ) : ?>
    <?php	$n = 1; ?>
    
    <?php	while($rs = dbFetchArray($qs) ) : ?>
	<?php		$id 	= $rs['id_product_attribute']; ?>    	
    <?php 		$dis	= $po->getDiscount($rs['discount_percent'], $rs['discount_amount']); ?>		
    			<tr id="row_<?php echo $id; ?>" style="font-size:12px;">
				<td align="center" style="border-left:solid 1px #DDD;">
                	<span class="no"><?php echo $n; ?></span>
                	<input type="hidden" class="id" value="<?php echo $id; ?>" />
                    <input type="hidden" id="received_<?php echo $id; ?>" value="<?php echo $rs['received']; ?>" />
                </td>
				<td style="border-left:solid 1px #DDD;"><?php echo get_product_reference($id); ?></td>
				<td style="border-left:solid 1px #DDD;"><?php echo get_product_name($rs['id_product']); ?></td>
				<td align="rignt" style="border-left:solid 1px #DDD;">
                <input type="text" name="qty[<?php echo $id; ?>]" id="qty_<?php echo $id; ?>" class="form-control input-xs" value="<?php echo $rs['qty']; ?>" onblur="recal(<?php echo $id; ?>)" />
                </td>
				<td align="right" style="border-left:solid 1px #DDD;"><input type="text" name="price[<?php echo $id; ?>]" id="price_<?php echo $id; ?>" class="form-control input-xs" value="<?php echo $rs['price']; ?>" onkeyup="recal(<?php echo $id; ?>)" /></td>
				<td align="center" style="border-left:solid 1px #DDD;"><input type="text" name="discount[<?php echo $id; ?>]" id="discount_<?php echo $id; ?>" class="form-control input-xs" value="<?php echo $dis['value']; ?>" onkeyup="recal(<?php echo $id; ?>)"  /></td>
				<td align="center" style="border-left:solid 1px #DDD;">
					<select name="unit[<?php echo $id; ?>]" id="unit_<?php echo $id; ?>" class="form-control input-xs" onchange="recal(<?php echo $id; ?>)">
						<option value="percent" <?php echo isSelected("%", $dis['unit']); ?>>%</option>
                        <option value="amount" <?php echo isSelected("THB", $dis['unit']); ?>>THB</option>
					</select>
				</td>
			   <td align="right" style="border-left:solid 1px #DDD;"><span id="total_<?php echo $id; ?>" class="number" style="font-size:14px;"><?php echo number_format($rs['total_amount'],2); ?></span></td>
			   <td align="right" style="border-right:solid 1px #DDD;">
               <?php if($rs['received'] == 0 ) : ?>
               <button type="button" class="btn btn-danger btn-xs" onclick="delete_row(<?php echo $id; ?>)"><i class="fa fa-trash"></i></button>
               <?php endif;?>
               </td>
			</tr>
	<?php $n++; $total_qty += $rs['qty']; $total_discount += $rs['total_discount'];  $total_amount += $rs['total_amount']; $total_price += $rs['qty']*$rs['price']; ?>            
    <?php	endwhile; ?>    
    <?php	else : ?>
       			<tr id="bf"><td colspan="10" style="height:50px; border-left:solid 1px #DDD; border-right:solid 1px #DDD; border-top:0px;"><center><h4>-----  ไม่มีรายการ  -----</h4></center></td></tr>
	<?php	endif; ?>                
            </tbody>
        </table>
        <table class="table table-bordered" style="margin-bottom:50px;">
        	<tbody style="width:1140px;">
        	<tr>
            	<td style="width:60%; text-align:right;">
                	ส่วนลดท้ายบิล : <input type="text" name="bill_discount" id="bill_discount" class="form-control input-sm" style="display:inline; width:150px; text-align:right" value="<?php echo $po->bill_discount; ?>" onkeyup="total_recal()"  />
                </td>
                <td style="width:20%;"><strong>จำนวนรวม</strong></td>
                <td align="right" style="padding-right:5px;"><span id="total_qty"><?php echo number_format($total_qty); ?></span></td>
            </tr>
            <tr>
            	<td id="remark2" rowspan="3" style="font-size:12px;"><strong>หมายเหตุ : </strong><?php echo $po->remark; ?></td>
                <td><strong>ราคารวม</strong></td>
                <td align="right" style="padding-right:5px;"><span id="total_price"><?php echo number_format($total_price,2); ?></span></td>
            </tr>
            <tr>
                <td><strong>ส่วนลดรวม</strong></td>
                <td align="right" style="padding-right:5px;"><span id="total_discount"><?php echo number_format($total_discount + $po->bill_discount, 2); ?></span></td>
            </tr>
            <tr>
                <td><strong>ยอดเงินสุทธิ</strong></td>
                <td align="right" style="padding-right:5px;"><span id="total_amount"><?php echo number_format($total_amount - $po->bill_discount,2); ?></span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
</form>
<?php endif; ?>
<!-----------------------------------------------  Edit  ----------------------------------->
<?php
elseif( isset( $_GET['view_detail'] ) && isset($_GET['id_po']) ) : 
?>
<!-----------------------------------------------  View  ----------------------------------->
		<?php 	$id_po	= $_GET['id_po']; ?>
    <?php 	$po = new po($id_po); 	?>
    <?php if($po->valid == 1 ) : ?>
    	<script>
			$("#btn_go_edit").remove();
			$("#btn_close").remove();
		</script>
    <?php endif; ?>
<div class="row">
	<div class="col-sm-2">
    	<label>เลขที่เอกสาร</label>
        <span class="form-control input-sm" style="border:0px; padding-left:0px;" ><?php echo $po->reference; ?></span>
        <input type="hidden" name="id_po" id="id_po" value="<?php echo $id_po; ?>"  />
    </div>
    <div class="col-sm-2">
    	<label>วันที่เอกสาร</label>
        <span class="form-control input-sm" style="border:0px; padding-left:0px;"><?php echo thaiDate($po->date_add); ?></span>
    </div>
    <div class="col-sm-2">
    	<label>รหัสผู้ขาย</label>
        <span class="form-control input-sm" style="border:0px; padding-left:0px;"><?php echo supplier_code($po->id_supplier); ?></span>
    </div>
    <div class="col-sm-4">
    	<label>ชื่อผู้ขาย</label>
        <span class="form-control input-sm" style="border:0px; padding-left:0px;"><?php echo supplier_name($po->id_supplier);  ?></span>
    </div>
    <div class="col-sm-2">
    	<label>กำหนดรับสินค้า</label>
        <span class="form-control input-sm" style="border:0px; padding-left:0px;"><?php echo thaiDate($po->due_date); ?></span>
    </div>    
</div>    

<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-sm-12">
    	<table class="table table-bordered" style="margin-bottom:0px;">
        	<thead style="font-size:14px;">
            	<th style="width:5%; text-align:center">ลำดับ</th>
                <th style="width:15%;">รหัสสินค้า</th>
                <th>รายละเอียด</th>
                <th style="width:6%; text-align:right;">จำนวน</th>
                <th style="width:6%; text-align:right;">ราคา/หน่วย</th>
                <th style="width:6%; text-align:center">ส่วนลด</th>
                <th style="width:8%; text-align:center;">หน่วย</th>
                <th style="width:10%; text-align:right;">จำนวนเงิน</th>
                <th style="width:6%; text-align:right;">รับแล้ว</th>
            </thead>
            <tbody id="result">
	<?php    $qs = $po->get_detail($id_po); ?>
    <?php 	if(dbNumRows($qs) > 0 ) : ?>
    <?php	$n = 1; ?>
    <?php	$total_qty = 0; $total_discount = 0; $total_amount = 0; $total_price = 0; ?>
    <?php	while($rs = dbFetchArray($qs) ) : ?>
	<?php		$id 	= $rs['id_product_attribute']; ?>    	
    <?php 		$dis	= $po->getDiscount($rs['discount_percent'], $rs['discount_amount']); ?>		
    			<tr id="row_<?php echo $id; ?>" style="font-size:12px;">
				<td align="center" style="border-left:solid 1px #DDD;"><?php echo $n; ?></td>
				<td style="border-left:solid 1px #DDD;"><span class="form-control input-sm" style="border:0px; height:20px; padding:0px;"><?php echo get_product_reference($id); ?></span></td>
				<td style="border-left:solid 1px #DDD;"><span class="form-control input-sm" style="border:0px; height:20px;  padding:0px;"><?php echo get_product_name($rs['id_product']); ?></span></td>
				<td align="right" style="border-left:solid 1px #DDD;"><?php echo number_format($rs['qty']); ?></td>
				<td align="right" style="border-left:solid 1px #DDD;"><?php echo number_format($rs['price'],2); ?></td>
				<td align="center" style="border-left:solid 1px #DDD;"><?php echo number_format($dis['value'],2); ?></td>
				<td align="center" style="border-left:solid 1px #DDD;"><?php echo $dis['unit']; ?></td>
			   <td align="right" style="border-left:solid 1px #DDD;"><?php echo number_format($rs['total_amount'],2); ?></td>
               <td align="right" style="border-left:solid 1px #DDD;"><?php echo number_format($rs['received']); ?></td>
			</tr>
	<?php $n++; $total_qty += $rs['qty']; $total_discount += $rs['total_discount'];  $total_amount += $rs['total_amount']; $total_price += $rs['qty']*$rs['price']; ?>            
    <?php	endwhile; ?>    
    <?php	else : ?>
       			<tr id="bf"><td colspan="10" style="height:50px; border-left:solid 1px #DDD; border-right:solid 1px #DDD; border-top:0px;"><center><h4>-----  ไม่มีรายการ  -----</h4></center></td></tr>
	<?php	endif; ?>                
            </tbody>
        </table>
        <table class="table table-bordered" style="margin-bottom:50px;">
        	<tbody>
        	<tr>
            	<td style="width:60%; text-align:right;">
                	ส่วนลดท้ายบิล : <span class="form-control input-sm" style="border:0px; display:inline; height:20px; text-align:right" ><?php echo $po->bill_discount; ?></span>
                </td>
                <td style="width:20%;"><strong>จำนวนรวม</strong></td>
                <td align="right" style="width:20%; padding-right:5px;"><span id="total_qty"><?php if(isset($total_qty)){ echo number_format($total_qty); }else{ echo 0.00; } ?></span></td>
            </tr>
            <tr>
            	<td rowspan="3" style="font-size:12px;"><strong style="font-size:14px;">หมายเหตุ : </strong><?php echo $po->remark; ?></td>
                <td><strong>ราคารวม</strong></td>
                <td align="right" style="padding-right:5px;"><span id="total_price"><?php if( isset( $total_price ) ){ echo number_format($total_price,2); }else{ echo 0.00; }?></span></td>
            </tr>
            <tr>
                <td ><strong>ส่วนลดรวม</strong></td>
                <td align="right" style="width:20%; padding-right:5px;"><span id="total_discount"><?php if( isset($total_discount) ){ echo number_format($total_discount + $po->bill_discount, 2); }else{ echo 0.00; } ?></span></td>
            </tr>
            <tr>
                <td><strong>ยอดเงินสุทธิ</strong></td>
                <td align="right" style="padding-right:5px;"><span id="total_amount"><?php if( isset($total_amount) ){ echo number_format($total_amount - $po->bill_discount,2); }else{ echo 0.00; } ?></span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<!-----------------------------------------------  View  ----------------------------------->

<?php 
else : 
?>
<!-----------------------------------------------  List  ----------------------------------->
<?php
	if( isset($_POST['from_date']) && $_POST['from_date'] !=""){ setcookie("po_from_date", date("Y-m-d", strtotime($_POST['from_date'])), time() + 3600, "/"); }else{ setcookie("po_from_date", "", time() + 3600, "/"); }
	if( isset($_POST['to_date']) && $_POST['to_date'] != ""){ setcookie("po_to_date",  date("Y-m-d", strtotime($_POST['to_date'])), time() + 3600, "/"); }else{ setcookie("po_to_date", "", time() + 3600, "/"); }
	$paginator = new paginator();
?>	
<form  method='post' id='form'>
<div class='row'>
	<div class='col-sm-2 col-md-2 col-sm-3 col-sx-3'>
			<label>เงื่อนไข</label>
			<select class='form-control' name='filter' id='filter'>
            	<option value="reference"  <?php if( isset( $_POST['filter'] ) ){ echo isSelected($_POST['filter'], "reference"); 	}else if( isset( $_COOKIE['po_filter'] ) ){ echo isSelected($_COOKIE['po_filter'], "reference"); 	} ?>>เลขที่เอกสาร</option>
				<option value='supplier'     <?php if( isset( $_POST['filter'] ) ){ echo isSelected($_POST['filter'], "supplier");    	}else if( isset( $_COOKIE['po_filter'] ) ){ echo isSelected($_COOKIE['po_filter'], "supplier");   	} ?>>ชื่อผู้ขาย</option>
			</select>
		
	</div>	
	<div class='col-sm-3 col-md-3 col-sm-3 col-sx-3'>
    	<label>คำค้น</label>
        <?php 
			$value = '' ; 
			if(isset($_POST['search_text'])) : 
				$value = $_POST['search_text']; 
			elseif(isset($_COOKIE['po_search_text'])) : 
				$value = $_COOKIE['po_search_text']; 
			endif; 
		?>
		<input class='form-control' type='text' name='search_text' id='search_text' placeholder="ระบุคำที่ต้องการค้นหา" value='<?php echo $value; ?>' />	
	</div>	
	<div class='col-sm-2 col-md-2 col-sm-2 col-sx-2'>
		<label>จากวันที่</label>
            <?php 
				$value = ""; 
				if(isset($_POST['from_date']) && $_POST['from_date'] != "") : 
					$value = date("d-m-Y", strtotime($_POST['from_date'])); 
				elseif( isset($_COOKIE['po_from_date'])) : 
					$value = date("d-m-Y", strtotime($_COOKIE['po_from_date'])); 
				endif; 
				?>
			<input type='text' class='form-control' name='from_date' id='from_date' placeholder="ระบุวันที่" style="text-align:center;"  value='<?php echo $value; ?>'/>
	</div>	
	<div class='col-sm-2 col-md-2 col-sm-2 col-sx-2'>
		<label>ถึงวันที่</label>
            <?php
				$value = "";
				if( isset($_POST['to_date']) && $_POST['to_date'] != "" ) :
				 	$value = date("d-m-Y", strtotime($_POST['to_date'])); 
				 elseif( isset($_COOKIE['po_to_date']) ) :
					$value = date("d-m-Y", strtotime($_COOKIE['po_to_date']));
				 endif;
			?>  
			<input type='test' class='form-control'  name='to_date' id='to_date' placeholder="ระบุวันที่" style="text-align:center" value='<?php echo $value; ?>' />
	</div>
	<div class='col-sm-2 col-md-2 col-sm-2 col-sx-2'>
    	<label style="visibility:hidden">show</label>
		<button class='btn btn-sm btn-primary btn-block' id='search-btn' type='submit' onclick="load_in()" ><i class="fa fa-search"></i>&nbsp;ค้นหา</button>
	</div>	
	<div class='col-sm-1 col-md-1 col-sm-1 col-sx-1'>
    	<label style="visibility:hidden">show</label>
		<button type='button' class='btn btn-sm btn-danger' onclick="clear_filter()"><i class='fa fa-refresh'></i>&nbsp;reset</button>
	</div>
</div>
</form>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />
<?php

		if(isset($_POST['from_date']) && $_POST['from_date'] != ""){$from = date('Y-m-d',strtotime($_POST['from_date'])); }else if( isset($_COOKIE['po_from_date'])){ $from = date('Y-m-d',strtotime($_COOKIE['po_from_date'])); }else{ $from = "";} 
		if(isset($_POST['to_date']) && $_POST['to_date'] != ""){ $to =date('Y-m-d',strtotime($_POST['to_date']));  }else if(  isset($_COOKIE['po_to_date'])){  $to =date('Y-m-d',strtotime($_COOKIE['po_to_date'])); }else{ $to = "";}
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		
		/****  เงื่อนไขการแสดงผล *****/
		if(isset($_POST['search_text'])/* && $_POST['search_text'] !="" */) :
			$text = $_POST['search_text'];
			$filter = $_POST['filter'];
			setcookie("po_search_text", $text, time() + 3600, "/");
			setcookie("po_filter",$filter, time() +3600,"/");
		elseif(isset($_COOKIE['po_search_text']) && isset($_COOKIE['po_filter'])) :
			$text = $_COOKIE['po_search_text'];
			$filter = $_COOKIE['po_filter'];
		else : 
			$text	= "";
			$filter	= "";
		endif;
		$where = "WHERE id_po != 0 ";
		if( $text != "" ) :
			switch( $filter) :				
				case "supplier" :
					$in = "";
					$q = dbQuery("SELECT id FROM tbl_supplier WHERE code LIKE '%".$text."%' OR name LIKE '%".$text."%' ORDER BY id");
					$row = dbNumRows($q);
					if($row > 0 )
					{
						$i = 1;
						while($r = dbFetchArray($q) )	
						{
							$in .= $r['id'];
							if($i<$row){ $in .=", "; }
							$i++;
						}
						
						$where .= "AND id_supplier IN(".$in.")";
					}else{
						$where .= "AND id_supplier = 0";
					}
				break;
				case "reference" :
				$where .= "AND reference LIKE'%$text%'";
				break;
			endswitch;
			if($from != "" && $to != "" ) : 
				$where .= " AND (date_add BETWEEN '".$from." 00:00:00' AND '".$to." 23:59:59')";  
			endif;
		else :
			if($from != "" && $to != "" ) : 
				$where .= "AND (date_add BETWEEN '".$from." 00:00:00' AND '".$to." 23:59:59')";  
			endif;	
		endif;
		$where .= " ORDER BY date_add DESC";
		
?>		

<?php
$paginator = new paginator();
if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		$paginator->Per_Page("tbl_po",$where,$get_rows);
		$paginator->display($get_rows,"index.php?content=po");
		$Page_Start = $paginator->Page_Start;
		$Per_Page = $paginator->Per_Page;
?>	
<style>
	.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td 
	{
		vertical-align:middle;
	}
</style>
<?php $qs = dbQuery("SELECT * FROM tbl_po ".$where." LIMIT ".$Page_Start.", ".$Per_Page); ?>
<div class="row">
	<div class="col-sm-12">
    <table class="table table-striped">
    <thead>
    	<th style="width:5%; text-align:center">ลำดับ</th>
        <th style="width:8%;">วันที่</th>
        <th style="width:10%;">เลขที่เอกสาร</th>
        <th>ผู้ขาย</th>
        <th style="width:10%;">ประเภท</th>
        <th style="width:10%; text-align:center;">สถานะ</th>
        <th style="width:15%; text-align:right;">การกระทำ</th>
    </thead>
	<?php if( dbNumRows($qs) > 0 ) : ?>
    	<?php $n = 1; ?>
        <?php while( $rs = dbFetchArray($qs) ) : ?>
        <?php 	$sup = supplier_name($rs['id_supplier']); ?>
     <tr style="font-size:12px;" id="row_<?php echo $rs['id_po']; ?>">
     	<td align="center"><?php echo $n; ?></td>
        <td><?php echo thaiDate($rs['date_add']); ?></td>
        <td><?php echo $rs['reference']; ?></td>
        <td><input type="text" style="width:100%; background-color:transparent; border:0px;" value="<?php echo $sup; ?>" disabled="disabled"  /></td>
        <td><?php echo role_name($rs['role']); ?></td>
        <td align="center">
			<?php if($rs['valid'] == 1 ) : ?>
            	<span style="color:blue">ปิดแล้ว</span>
            <?php elseif($rs['status'] == 1 ) : ?>
            	<span style="color:green">บันทึกแล้ว</span>
            <?php elseif($rs['status'] == 0 ) : ?>
            	<span style="color:red">ยังไม่บันทึก</span>
            <?php elseif($rs['status'] == 2 ) : ?>
            	<span style="color:#F90;">รับแล้วบางส่วน</span>
            <?php endif; ?>
        <td align="right">
        	<button type="button" class="btn btn-info btn-xs" onclick="window.location.href='index.php?content=po&view_detail=y&id_po=<?php echo $rs['id_po']; ?>'"><i class="fa fa-eye"></i></button>
            <?php if($rs['valid'] != 1 ) : ?>
				<?php if($edit) : ?>
                <button type="button" class="btn btn-warning btn-xs" onclick="window.location.href='index.php?content=po&edit=y&id_po=<?php echo $rs['id_po']; ?>'"><i class="fa fa-pencil"></i></button>
                <?php endif; ?>
               	<?php if( $rs['status'] != 2 ) : ?>
					<?php if( $delete ) : ?>
                    <button type="button" class="btn btn-danger btn-xs" onclick="delete_po(<?php echo $rs['id_po']; ?>, '<?php echo $rs['reference']; ?>', '<?php echo $sup; ?>')"><i class="fa fa-trash"></i></button>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
       	</td>
     </tr>
     	<?php $n++; ?>
        <?php endwhile; ?>
  	<?php else : ?>
    <tr><td colspan="6"><center><h4>-----  ไม่มีรายการ  -----</h4></center></td></tr>
    <?php endif; ?>	    
    
    </table>
    </div>
</div>
<!-----------------------------------------------  List  ----------------------------------->
<?php
endif;
?>
<input type="hidden" id="no" value="0" />
<script id="template" type="text-xhandlebars-template">
			<tr id="row_{{ id }}" style="font-size:12px;">
				<td align="center" style="border-left:solid 1px #DDD;"><span class="no">{{ no }}</span><input type="hidden" class="id" value="{{ id }}" /></td>
				<td style="border-left:solid 1px #DDD;">{{ code }}</td>
				<td style="border-left:solid 1px #DDD;">{{ product_name }}</td>
				<td align="rignt" style="border-left:solid 1px #DDD;"><input type="text" name="qty[{{ id }}]" id="qty_{{ id }}" class="form-control input-xs" value="{{ qty }}" onkeyup="recal({{id}})"  /></td>
				<td align="right" style="border-left:solid 1px #DDD;"><input type="text" name="price[{{ id }}]" id="price_{{ id }}" class="form-control input-xs" value="{{ price }}" onkeyup="recal({{id}})" /></td>
				<td align="center" style="border-left:solid 1px #DDD;"><input type="text" name="discount[{{ id }}]" id="discount_{{ id }}" class="form-control input-xs" value="{{ discount }}" onkeyup="recal({{id}})"  /></td>
				<td align="center" style="border-left:solid 1px #DDD;">
					<select name="unit[{{id}}]" id="unit_{{id}}" class="form-control input-xs" onchange="recal({{id}})">
						{{{unit}}}
					</select> 
				</td>
			   <td align="right" style="border-left:solid 1px #DDD;"><span id="total_{{id}}" class="number" style="font-size:14px;">{{ total_amount }}</span></td>
			   <td align="right" style="border-right:solid 1px #DDD;"><button type="button" class="btn btn-danger btn-xs" onclick="delete_row({{ id }})"><i class="fa fa-trash"></i></button></td>
			</tr>
</script>	
<!-----------------------  Order Grid ------------->
<button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button>
<form id="item_form">
	<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' id='modal'>
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
					<center style="margin-bottom:10px;"><h4 class='modal-title' id='modal_title'>title</h4></center>
                    <input type='hidden' name='id_product' id="id_product" />
				 </div>
				 <div class='modal-body' id='modal_bodyx'>
                 	<div class="col-sm-4">
                    	<label>ราคา/หน่วย</label>
                        <input type="text" name="cost" id="cost" class="form-control input-sm" />
                    </div>
                 	<div class="col-sm-4">
                    	<label>ส่วนลด</label>
                        <input type="text" class="form-control input-sm" name="discount" value="0.00" />
                    </div>
                    <div class="col-sm-4">
                    	<label>หน่วย</label>
                        <select name="unit" class="form-control input-sm"><option value="percent">เปอร์เซ็น</option><option value="amount">จำนวนเงิน</option></select>
                    </div>
                    <div class="col-sm-12"><hr/></div>
                    <div id="modal_body"></div>
                 </div>
				 <div class='modal-footer'>
					<button type='button' class='btn btn-default' id="btn_close" data-dismiss='modal'>ปิด</button>
					<button type='button' class='btn btn-primary' onclick="insert_item()">เพิ่มในรายการ</button>
				 </div>
			</div>
		</div>
	</div>
</form>
<input type="hidden" name="id_employee" id="id_employee" value="<?php echo $_COOKIE['user_id']; ?>"  />

<div class='modal fade' id='product_info' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' id='modal_info' style="width:800px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
				 </div>
				 <div class='modal-body' id='info_body'>
                 	
                 </div>
				 <div class='modal-footer'>
				 </div>
			</div>
		</div>
	</div>

<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg">
    	<div class="modal-content">
        	<div class="modal-header">
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class="modal-title" style="text-align:center;" id="title">reference</h4>
            </div>
            <div class="modal-body" id="detail_body">
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="amount_modal" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
	<div class="modal-dialog" id="amount_dialog" style="width:300px;">
    	<div class="modal-content" >
        	<div class="modal-header">
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class="modal-title" style="text-align:center;" id="amount_title">มูลค่าสั่งซื้อแต่ละเดือน</h4>
            </div>
            <div class="modal-body" id="amount_body">
            	
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<script id="amount_template" type="text/x-handlebars-template">
<table class="table table-striped table-bordered">
{{#each this}}
{{#if @first}}
<thead>
<th style="width:150px; text-align:center;">เดือน</th>
	{{#each roles}}
<th style="width:150px; text-align:center;">{{ role }}</th>		
	{{/each}}
</thead>
{{/if}}
	{{#if last}}
    <tr>
    	<td align="right"><strong>รวม</strong></td>
    	<td align="right"><strong>{{ total_amount}}</strong></td>
    </tr>
    {{else}}
    <tr>
    	<td >{{ month }}</td>
        <td align="right">{{ amount }}</td>
    </tr>
    {{/if}}
{{/each}}
</table>
</script>

<script id="list_template" type="text/x-handlebars-template">
<table class="table table-striped">
    <thead>
    <th style="width:5%; text-align:center;">ลำดับ</th>
    <th style="width:25%; text-align:center;">สินค้า</th>
    <th style="width:15%; text-align:center;">สั่งรวม</th>
    <th style="width:15%; text-align:center;">รับแล้ว</th>
    <th style="width:15%; text-align:center;">ค้างรับ</th>
    <th style="text-align:center;">เลขที่เอกสาร</th>
    </thead>
	{{#each this}}
    <tr style="font-size:12px;">
    	<td align="center">{{ no }}</td>
        <td id="{{id_product_attribute}}">{{ product }}</td>
        <td align="center">{{ qty }}</td>
        <td align="center">{{ received }}</td>
        <td align="center">{{ backlog }}</td>
        <td align="right">
		{{#if content}}
		<button class="btn btn-xs btn-info" onclick="view_item_po({{ id_product_attribute }}, {{ id_sup }})"><i class="fa fa-eye"></i> &nbsp; ดูใบสั่งซื้อ</button>
		{{/if}}
		</td>
    </tr>
	{{/each}}
</table>  
</script>  
<script id="po_template" type="text/x-handlebars-template">
<table class="table table-striped">
<thead style="font-size:12px;">
    <th style="width:20%;">ใบสั่งซื้อ</th>
    <th style="width:15%;">วันที่</th>
    <th>ผู้ขาย</th>
    <th style="width:10%; text-align:center;">สั่งซื้อ</th>
    <th style="width:10%; text-align:center;">รับแล้ว</th>
    <th style="width:10%; text-align:center;">ค้ารับ</th>
</thead>
{{#each this}}
    {{#if @last}}
        <tr style="font-size:14px; font-weight:bold;">
            <td colspan="3" align="right">รวม</td>
            <td align="center">{{ total_qty }}</td>
            <td align="center">{{ total_received }}</td>
            <td align="center">{{ total_backlog }}</td>
        </tr>
    {{else}}
        <tr style="font-size:12px;">
            <td>{{ reference }}</td>
            <td>{{ date_add }}</td>
            <td>{{ sup_name }}</td>
            <td align="center">{{ qty }}</td>
            <td align="center">{{ received }}</td>
            <td align="center">{{ backlog }}</td>
        </tr>
    {{/if}}
{{/each}}
</table>
</script>
<script id="info_template" type="text/x-handlebars-template">
<table class="table table-striped">
{{#each this}}
{{#if @first}}
<thead>
<tr><th colspan="6" style="text-align:center;">{{ title }}</th></tr>
<tr>
<th style="width:10%;">วันที่</th>
<th style="width:30%;">ใบสั่งซื้อ</th>
<th style="width:30%;">ผู้ขาย</th>
<th style="width:10%; text-align:right;">จำนวน</th>
<th style="width:10%; text-align:right;">รับแล้ว</th>
<th style="width:10%; text-align:right;">สถานะ</th>
</tr>
</thead>
{{else}}
	{{#if nocontent}}
    	<tr><td colspan="6" align="center"><h4>-----  {{ nocontent }}  -----</h4></td></tr>
    {{else}}
		{{#if @last}}
		 <tr>
            <td colspan="3" align="right">รวม</td>
            <td align="right">{{ qty }}</td>
            <td align="right">{{ received }}</td>
            <td align="right"></td>
        </tr>
		{{else}}
        <tr>
            <td>{{ date }}</td>
            <td><a href="javascript:void(0)" onclick="view_po({{id_po}})">{{ po }}</a></td>
            <td>{{ sup }}</td>
            <td align="right">{{ qty }}</td>
            <td align="right">{{ received }}</td>
            <td align="right">{{{ status }}}</td>
        </tr>
		{{/if}}
	{{/if}}
{{/if}}
{{/each}}
</table>
</script>
<script>

function check_amount()
{
	load_in();
	var width = 300 + ((<?php echo count_role(); ?>) * 150);
	$("#amount_dialog").css("width", width);
	$.ajax({
		url:"controller/poController.php?get_monthly_amount",
		type:"GET", cache: "false", success: function(rs){
			load_out();
			var rs = $.trim(rs);
			$("#amount_body").html(rs);
			$("#amount_modal").modal("show");
		}
	});
}

function view_item_po(id)
{
	var left = ($(document).width() - 800)/2;
	var url = "index.php?content=po_by_product&get_item_po&id_product_attribute="+id+"&nomenu";
	window.open(url, "_blank", "width=600, height=800, left="+left+", scrollbars=yes");	
}

function get_info_detail()
{
	var code		 	= $("#product_code").val();
	if( code != "")
	{
		var sup_rank 	= 0;
		var id_sup		= 0;
		var from			= "<?php echo date("01-01-Y"); ?>";
		var to				= "<?php echo date("d-m-Y"); ?>";
		$.ajax({
			url:"report/reportController/poReportController.php?check_product_code",
			type:"POST", cache:"false", data:{ "product_code" : code },
			success: function(ps)
			{
				var ps = $.trim(ps);
				if(ps != "0" && ps != "")
				{
					load_in();
					$.ajax({
						url:"report/reportController/poReportController.php?po_by_product&report",
						type:"POST", cache:"false", data:{ "id_product" : ps, "sup_rank" : sup_rank, "id_sup" : id_sup, "from" : from, "to" : to },
						success: function(rs)
						{
							load_out();
							var rs = $.trim(rs);
							var source = $("#list_template").html();
							var data 		= $.parseJSON(rs);
							var output 	= $("#detail_body");
							render(source, data, output);
							$("#detail_modal").modal("show");
						}
					});
				}
				else
				{
					swal("รหัสสินค้าไม่ถูกต้อง", "รหัสสินค้าที่ระบุไม่ถูกต้อง คุณต้องระบุรหัสรุ่นสินค้าเท่านั้น", "error");	
				}
			}
		});
	}
}

function view_po(id)
{
	window.open("index.php?content=po&view_detail=y&id_po="+id, "_blank", "width=1000, height=800, scrollbars=yes");	
}

function get_info()
{
	var code 	= $("#product_code").val();
	if(code != "")
	{
		load_in();
		$.ajax({
			url:"controller/poController.php?get_product_info",
			type: "POST", cache: "false", data:{ "product_code" : code },
			success: function(rs)
			{
				load_out();
				var rs = $.trim(rs);
				var source 	= $("#info_template").html();
				var data 		= $.parseJSON(rs);
				var output	= $("#info_body");
				render(source, data, output);
				$("#product_info").modal("show");				
			}
		});
	}
}
</script>    
<script>
$.fn.digits = function(){ 
    return this.each(function(){ 
        $(this).text( $(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") ); 
    })
}
</script>
<script>

function save_edit()
{
	load_in();
	var id_po	= $("#id_po").val();
	$.ajax({
		url:"controller/poController.php?save_edit&id_po="+id_po,
		type:"POST", cache:false, data: $("#add_form").serialize(),
		success: function(rs)
		{
			var rs = $.trim(rs);
			if( rs == "success")
			{
				load_out();
				swal({ title:"เรียบร้อย", text: "บันทึกรายการเรียบร้อยแล้ว", timer: 1500, type: "success"});
			}else if(rs == "fail"){
				load_out();
				swal("บันทึกใบสั่งซื้อไม่สำเร็จ", "ไม่สามารถบันทึกรายการได้ ตรวจสอบรายการแล้วลองอีกครั้ง", "error");
				return false;
			}else{
				load_out();
				swal({
					  title: "มีบางอย่างผิดพลาด",
					  text: "ปรับปรุงรายการสำเร็จ แต่ไม่สามารถลบรายการต่อไปนีได้ เนื่องจากมีการรับสินค้าเข้าคลังแล้ว <br/>"+rs,
					  type: "warning",
					  html: true,
					  showCancelButton: false,
					  confirmButtonText: "รับทราบ",
					  closeOnConfirm: true
					}, function(){
						load_in();
						window.location.reload();
						});
				
				return false;
			}
		}
	});
}

function save_add()
{
	load_in();
	var id_po	= $("#id_po").val();
	$.ajax({
		url:"controller/poController.php?save_add&id_po="+id_po,
		type:"POST", cache:false, data: $("#add_form").serialize(),
		success: function(rs)
		{
			var rs = $.trim(rs);
			if( rs == "success")
			{
				window.location.href = "index.php?content=po&id_po="+id_po+"&edit=Y";
			}else{
				load_out();
				swal("บันทึกใบสั่งซื้อไม่สำเร็จ", "ไม่สามารถบันทึกรายการได้ ตรวจสอบรายการแล้วลองอีกครั้ง", "error");
				return false;
			}
		}
	});
}

function recal(id)
{
	var qty 			= parseInt($("#qty_"+id).val());
	var received 	= parseInt($("#received_"+id).val());
	if(qty < received){
		swal("รายการนี้มีการรับสินค้าแล้ว", "รายการนี้มีการรับสินค้าแล้วจำนวน "+received+" ตัว ไม่อนุญาติให้แก้ไขยอดน้อยกว่ายอดที่รับแล้ว", "warning");
		$("#qty_"+id).val(received);
		qty = received;
	}
	var price 		= parseFloat($("#price_"+id).val());
	var discount 	= parseFloat($("#discount_"+id).val());
	var units			= $("#unit_"+id).val();	
	if(isNaN(qty) ){ qty = 0; }
	if(isNaN(price) ){ price = 0; }
	if(isNaN(discount) ){ discount = 0; }
	if( units == "percent" ){  var d_price = price - (price * (discount*0.01)); }else{ var d_price = price - discount; }
	var total = d_price * qty;
	$("#total_"+id).html(total.toFixed(2));
	$("#total_"+id).digits();
	total_recal();
}

function total_recal()
{
	var total_qty 		= 0;
	var total_price		= 0;
	var total_discount 	= 0;
	var total_amount 	= 0;
	var no 				= 0;
	$(".id").each(function(index, element) {
		var id 			= $(this).val();
        var qty 			= parseInt($("#qty_"+id).val());
		var price 		= parseFloat($("#price_"+id).val());
		var discount 	= parseFloat($("#discount_"+id).val());
		var units			= $("#unit_"+id).val();
		if( isNaN(qty) ){ qty = 0; }
		if( isNaN(price) ){ price = 0; }
		if( isNaN(discount) ){ discount = 0; }
		if( units == "percent" ){  
			var d_price = price - (price * (discount*0.01));  
			var dis_c 	= price * (discount*0.01);  
		}else{ 
			var d_price = price - discount; 
			var dis_c	= discount;
		}
			total_qty += qty;
			total_price += qty*price;
			total_discount += dis_c * qty;
			total_amount	 += d_price * qty;
			no++;
	});	
	var bill_discount = parseFloat($("#bill_discount").val());
	if(isNaN(bill_discount) ){ bill_discount = 0.00; }
	total_discount += bill_discount;
	total_amount -= bill_discount;
	$("#total_qty").html(total_qty);
	$("#total_price").html(total_price.toFixed(2));
	$("#total_discount").html(total_discount.toFixed(2));
	$("#total_amount").html(total_amount.toFixed(2));
	$("#total_qty").digits();
	$("#total_price").digits();
	$("#total_discount").digits();
	$("#total_amount").digits();
	$("#no").val(no);
}

function reorder()
{
	var i = 1;
	$(".no").each(function(index, element) {
        $(this).html(i);
		$("#no").val(i);
		i++;
    });
}
function delete_row(id)
{
	$("#row_"+id).remove();	
	reorder()
	total_recal();
}
function delete_all()
{
	swal({
  title: "ต้องการลบรายการทั้งหมด?",
  text: "แน่ใจนะว่าคุณต้องการลบรายการทั้งหมดจริๆ",
  type: "warning",
  showCancelButton: true,
  confirmButtonColor: "#DD6B55",
  confirmButtonText: "ใช่ ลบทั้งหมด",
  closeOnConfirm: false
}, function(){
	$(".id").each(function(index, element) {  var id = $(this).val(); $("#row_"+id).remove(); });
  	swal({ title:"เรียบร้อย!", text:"ลบรายการทั้งหมดเรียบร้อยแล้ว", timer: 1500, type:"success"});
	total_recal();
	$("#no").val(0);
	});
}

function delete_po(id, reference, supplier)
{
	swal({
		  title: "ต้องการลบใบสั่งซื้อ ?",
		  text: "<span style='color:red;'>ใบสั่งซื้อเลขที่ "+reference+"</span> <br/><span style='color:red;'>"+supplier+"</span> จะถูกลบ <br/> หากใบสั่งซื้อถูกปิดแล้วหรือมีการรับเข้าแล้วจะไม่สามารถลบได้ ",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "ใช่ ลบเลย",
		  cancelButtonText: "ยกเลิก",
		  closeOnConfirm: false,
		  html: true
		}, function(){
			load_in();
			$.ajax({
				url:"controller/poController.php?delete_po&id_po="+id,
				type:"GET", cache:"false",
				success: function(rs)
				{
					var rs = $.trim(rs);
					if(rs == "success")
					{
						load_out();
						$("#row_"+id).remove();
						swal({ title: "เรียบร้อย", text: "ลบใบสั่งซื้อเรียบร้อยแล้ว", timer: 1000, type: "success"});
					}else{
						load_out();
						swal("ไม่สำเร็จ", "ลบใบสั่งซื้อไม่สำเร็จ ใบสั่งซื้ออาจถูกปิดแล้วหรือมีการรับสินค้าแล้ว", "error");
					}
				}
			});						
	});
}


function check_qty()
{
	var qty = 0;
	$(".input_qty").each(function(index, element) {
        var q = parseInt($(this).val());
		if( !isNaN(q)){ qty += q; }
    });	
	return qty;
}

function insert_item()
{
	var id = $("#id_po").val();
	var q = check_qty();
	var no	= $("#no").val();
	if( q > 0 )
	{
		$("#btn_close").click();
		load_in();
		$.ajax({
			url:"controller/poController.php?insert_item&id_po="+id+"&no="+no,
			type:"POST", cache: false, data: $("#item_form").serialize(),
			success: function(rs)
			{
				var rs = $.trim(rs);
				if( rs != "")
				{
					$("#bf").remove();
					var arr = rs.split(" | ");
					var data		= $.parseJSON(arr[1]);
					for(var index in data)
					{
						var id = data[index].id;
						if( $("#row_"+id).length == 0 )
						{
							var source 	= $("#template").html();
							var row		= Handlebars.compile(source);
							var datax 	= data[index];
							var output	= row(datax);
							$("#result").append(output);
						}else{
							var qty = parseInt($("#qty_"+id).val());
							qty	+= parseInt(data[index].qty);
							$("#qty_"+id).val(qty);
							recal(id);
						}
					}
					$("#no").val(arr[0]);
					total_recal();
					reorder();
					$(".number").digits();
					load_out();
				}else{
					load_out();
					swal("error");
				}
			}
		});					
	}else{
		swal("คุณต้องใส่จำนวนอย่างน้อย 1 รายการ");
		return false;
	}
}


function get_product(){
	var product  = $("#product_code").val();
	load_in();
	$.ajax({
		url:"controller/poController.php?get_product",
		type:"POST", cache:false, data:{ "product_code" : product },
		success: function(dataset){
			var dataset = $.trim(dataset);
			if(dataset !="fail" && dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				var id_product = arr[3];
				$("#id_product").val(id_product);
				$("#cost").val(arr[4]);
				$("#modal").css("width",table_w+"px");
				$("#modal_title").html(title);
				$("#modal_body").html(data);
				load_out();
				$("#btn_toggle").click();
			}else{
				load_out();
				swal("ไม่มีรายการสินค้าที่ค้นหา");
			}		
		}
	});
}


function update()
{
	var id_po		= $("#id_po").val();
	var reference	= $("#reference").val();
	var date_add 	= $("#date_add").val();
	var due_date	= $("#due_date").val();
	var s_id			= $("#s_id").val();
	var role			= $("#role").val();
	var remark		= $("#remark").val();
	var id_em		= $("#id_employee").val();
	var dup			= $("#is_duplicate").val();
	if( !isDate(date_add) )
	{ 
		swal("วันที่เอกสารไม่ถูกต้อง", "ตรวจสอบดูว่าวันที่เอกสารไม่ใช่ค่าว่าง หรือรูปแบบวันที่ถูกต้องหรือไม่", "error");
		return false;
	}else if( !isDate(due_date) ){
		swal("วันที่ไม่ถูกต้อง", "ตรวจสอบดูว่าวันที่กำหนดรับสินค้าไม่ใช่ค่าว่าง หรือรูปแบบวันที่ถูกต้องหรือไม่", "error");
		return false;
	}else if( dup == '1' ){
		swal("เลขที่เอกสารซ้ำ", "ไม่สามารถแก้เอกสารได้เนื่องจากเลขที่เอกสารซ้ำ", "error");
		return false;
	}else if( s_id != "" ){
		load_in();
		$.ajax({
			url:"controller/poController.php?check_new_ref",
			type: "POST", cache: "false", data: { "id_po" : id_po, "reference" : reference },
			success: function(r)
			{
				var r = $.trim(r);
				if(r == "0")
				{
					$.ajax({
						url:"controller/poController.php?update_po",
						type:"POST", cache:false,
						data:{ "id_po" : id_po, "reference" : reference, "date_add" : date_add, "due_date" : due_date, "s_id" : s_id, "role" : role, "remark" : remark , "id_employee" : id_em },
						success: function(rs)
						{
							var rs = $.trim(rs);
							if( rs != "fail" && rs !="" )
							{
								updated();
								load_out();
								swal({ title: "เรียบร้อย", text: "ปรับปรุงข้อมูลเรียบร้อยแล้ว", type: "success", timer: 1000 });
							}else{
								load_out();
								swal("แก้ไขข้อมูลไม่สำเร็จ","ไม่สามารถแก้ไขเอกสารได้ กรุณาลองใหม่อีกครั้งหรือติดต่อผู้ดูแลระบบ","error");
							}
						}
					});
				}else{
					load_out();
					swal("เลขที่ใบสั่งซื้อซ้ำ");
				}
			}
		});
	}
}

function edit()
{
	$("#reference").removeAttr("disabled");
	$("#date_add").removeAttr("disabled");
	$("#s_code").removeAttr("disabled");
	$("#s_name").removeAttr("disabled");
	$("#due_date").removeAttr("disabled");
	$("#role").removeAttr("disabled");
	$("#remark").removeAttr("disabled");
	$("#btn_edit").css("display","none");
	$("#btn_update").css("display","");	
	
}

function updated()
{
	$("#reference").attr("disabled", "disabled");
	$("#date_add").attr("disabled", "disabled");
	$("#s_code").attr("disabled", "disabled");
	$("#s_name").attr("disabled", "disabled");
	$("#due_date").attr("disabled", "disabled");
	$("#role").attr("disabled", "disabled");
	$("#remark").attr("disabled", "disabled");
	$("#btn_update").css("display","none");
	$("#btn_edit").css("display","");	
}

function check_reference(id_po)
{
	var reference = $("#reference").val();
	if( reference == ''){ return false; }
	load_in();
	$.ajax({
		url:"controller/poController.php?check_new_ref"	,
		type: "POST", cache: "false", data: { "reference" : reference, "id_po" : id_po },
		success: function(rs)
		{
			load_out();
			var rs = $.trim(rs);
			if(rs == "0")
			{
				$("#is_duplicate").val(0);
			}else{
				$("#is_duplicate").val(1);
				swal("เลขที่เอกสารซ้ำ");
			}
		}
	});
}

function add_new_po()
{
	var reference 	= $("#reference").val();
	var date_add 	= $("#date_add").val();
	var due_date	= $("#due_date").val();
	var s_id			= $("#s_id").val();
	var role			= $("#role").val();
	var remark		= $("#remark").val();
	var id_em		= $("#id_employee").val();
	var dup			= $("#is_duplicate").val();
	if( !isDate(date_add) )
	{ 
		swal("วันที่เอกสารไม่ถูกต้อง", "ตรวจสอบดูว่าวันที่เอกสารไม่ใช่ค่าว่าง หรือรูปแบบวันที่ถูกต้องหรือไม่", "error");
		return false;
	}else if( !isDate(due_date) ){
		swal("วันที่ไม่ถูกต้อง", "ตรวจสอบดูว่าวันที่กำหนดรับสินค้าไม่ใช่ค่าว่าง หรือรูปแบบวันที่ถูกต้องหรือไม่", "error");
		return false;
	}else if(dup == 1 ){
		swal("เลขที่เอกสารซ้ำ", "ไม่สามารถเพิ่มเอกสารใหม่ได้เนื่องจากเลขที่เอกสารซ้ำ", "error");
		return false;
	}else if( s_id != "" ){
		load_in();
		$.ajax({
			url:"controller/poController.php?add_po",
			type:"POST", cache:false,
			data:{ "reference" : reference, "date_add" : date_add, "due_date" : due_date, "s_id" : s_id, "role" : role, "remark" : remark , "id_employee" : id_em },
			success: function(rs)
			{
				var rs = $.trim(rs);
				if( rs != "fail" && rs !="" )
				{
					window.location.href = "index.php?content=po&add=y&id_po="+rs;
				}else{
					load_out();
					swal("เพิ่มเอกสารไม่สำเร็จ","ไม่สามารถเพิ่มเอกสารได้ กรุณาลองใหม่อีกครั้งหรือติดต่อผู้ดูแลระบบ","error");
				}
			}
		});
	}
}


$("#date_add").datepicker({ dateFormat: "dd-mm-yy" });
$("#due_date").datepicker({ dateFormat: "dd-mm-yy" });

$("#product_code").autocomplete({
	minLength: 2,
	source: "controller/autoComplete.php?product_code",
	autoFocus: true
});
		
$("#s_code").autocomplete({
	minLength: 1,
	source: "controller/autoComplete.php?get_supplier_code", 
	autoFocus: true,
	close: function(event, ui)
	{
		var rs	= $("#s_code").val();
		var arr = rs.split(" : ");
		if( arr[0] != "ไม่พบข้อมูล" )
		{
			$("#s_code").val(arr[0]);
			$("#s_name").val(arr[1]);
			$("#s_id").val(arr[2]);
		}
	}
});

$("#s_name").autocomplete({
	minLength: 1,
	source: "controller/autoComplete.php?get_supplier_name",
	autoFocus: true,
	close: function(event, ui)
	{
		var rs	= $("#s_name").val();
		var arr = rs.split(" : ");
		if( arr[0] != "ไม่พบข้อมูล" )
		{
			$("#s_code").val(arr[0]);
			$("#s_name").val(arr[1]);
			$("#s_id").val(arr[2]);
		}
	}
});
</script>
<script>
function go_back()
{
	window.location.href = "index.php?content=po";	
}
function add()
{
	window.location.href = "index.php?content=po&add=y";	
}
function go_edit()
{
	var id_po = $("#id_po").val();
	window.location.href="index.php?content=po&edit=y&id_po="+id_po;	
}
function print_po()
{
	var center = ($(document).width() - 800) /2;
	var id_po = $("#id_po").val();
	window.open("controller/poController.php?print_po&id_po="+id_po, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}

function print_barcode()
{
	var center = ($(document).width() - 800)/2;
	var id_po = $("#id_po").val();
	window.open("controller/poController.php?print_barcode&id_po="+id_po, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}

$("#date_add").datepicker({	dateFormat: "dd-mm-yy" });
$(function() {
    $("#from_date").datepicker({
      dateFormat: 'dd-mm-yy', onClose: function( selectedDate ) {
        $( "#to_date" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to_date" ).datepicker({
      dateFormat: 'dd-mm-yy',   onClose: function( selectedDate ) {
        $( "#from_date" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
function clear_filter()
{
	load_in();
	$.ajax({
		url:"controller/poController.php?clear_filter"	,
		type:"GET", cache: "false", success: function(rs){
			window.location.href="index.php?content=po";
		}
	});
}

function cancle_close_po()
{
	swal({
		  title: "ยกเลิกการปิดใบสั่งซื้อ ?",
		  text: "<span style='color:red;'>เอกสารจะถูกปลดล็อก สามารลบหรือแก้ไขได้อีกครั้ง ดำเนินการต่อหรือไม่ ?</span> ",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "ดำเนินการต่อ",
		  cancelButtonText: "ไม่ ยกเลิก",
		  closeOnConfirm: false,
		  html: true
		}, function(){
			load_in();
			var id = $("#id_po").val();
			$.ajax({
				url:"controller/poController.php?cancle_close_po&id_po="+id,
				type:"GET", cache:"false",
				success: function(rs)
				{
					var rs = $.trim(rs);
					if(rs == "success")
					{
						load_out();
						swal({title: "สำเร็จ", text: "ยกเลิกการปิดใบสั่งซื้อเรียบร้อยแล้ว", type:"success", timer: 1000} );
						setTimeout(function(){ window.location.href = "index.php?content=po&edit=y&id_po="+id; }, 1500);
					}else{
						load_out();
						swal("ไม่สำเร็จ", "ยกเลิกการปิดใบสั่งซื้อไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
						return false;
					}
				}
			});						
	});
}

function close_po()
{
	swal({
		  title: "ต้องการปิดใบสั่งซื้อ ?",
		  text: "<span style='color:red;'>เมื่อปิดใบสั่งซื้อแล้วเอกสารจะถูกล็อก ไม่สามารลบหรือแก้ไขได้อีก</span> ",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "ใช่ ปิดเลย",
		  cancelButtonText: "ยกเลิก",
		  closeOnConfirm: false,
		  html: true
		}, function(){
			load_in();
			var id = $("#id_po").val();
			$.ajax({
				url:"controller/poController.php?close_po&id_po="+id,
				type:"GET", cache:"false",
				success: function(rs)
				{
					var rs = $.trim(rs);
					if(rs == "success")
					{
						load_out();
						swal("สำเร็จ", "ปิดใบสั่งซื้อเรียบร้อยแล้ว", "success");
						window.location.href = "index.php?content=po&id_po="+id+"&view_detail";
					}else{
						load_out();
						swal("ไม่สำเร็จ", "ปิดใบสั่งซื้อไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
						return false;
					}
				}
			});						
	});
}

function updateReceived(){
	load_in();
	$.ajax({
		url:"controller/poController.php?updateReceivedQty",
		type:"GET", cache:"false",
		success: function(rs){
			load_out();
			swal({ title: 'Updated', text: 'ปรับปรุงยอดรับสินค้าเรียบร้อยแล้ว', type: 'success', timer: 1000 });
		}
	});
}

</script>
</div><!--- container -->