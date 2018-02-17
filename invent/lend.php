<?php 
	$page_name = $pageTitle;
	$id_tab = 8;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$ps = checkAccess($id_profile, 37);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	if( $ps['add'] || $ps['edit'] ){ $return = 1; }else{ $return = 0; }
	accessDeny($view);
	include("function/lend_helper.php");
	include("function/order_helper.php");
	?>


<?php 
$btn = "";
$back = "<button type='button' class='btn btn-warning btn-sm' onclick='go_back()'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button>";
if( isset( $_GET['add'] ) ) 
{
	$btn .= $back;
	if($add && isset($_GET['id_order'])){ $btn .= "<button type='button' class='btn btn-success btn-sm' onclick='save_order()'><i class='fa fa-save'></i>&nbsp; บันทึก</button>"; }
}
else if( isset( $_GET['edit'] ) && isset( $_GET['id_order'] ) ) 
{
	$btn .= $back;
	if($edit){ $btn .= "<button type='button' class='btn btn-success btn-sm' onclick='save_edit(".$_GET['id_order'].")'><i class='fa fa-save'></i>&nbsp; บันทึก</button>"; }
}
elseif( isset( $_GET['view_detail'] ) && isset( $_GET['id_order'] ) ) 
{
	$btn .= $back;
}
elseif( isset( $_GET['return'] ) && isset( $_GET['id_order'] ) )
{
	$btn .= $back;
}
else 
{
	if($add){ $btn .= "<button type='button' class='btn btn-success btn-sm' onclick='new_add()'	><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button>"; }
}
?>  	
<div class="container">
<div class="row" style="height:30px;">
	<div class="col-lg-6" style="margin-top:10px;"><h4 class="title"><i class="fa fa-credit-card"></i>&nbsp; <?php echo $page_name; ?></h4></div>
    <div class="col-lg-6"><p class="pull-right" style="margin-bottom:0px;"><?php echo $btn; ?></p></div>
</div>
<hr/>
<?php if( isset( $_GET['add'] ) || isset($_GET['edit']) ) : ?>
<?php 	if( !isset($_GET['id_order']) ) : ?>
<div class="row">
<div class="col-sm-2">
	<label>วันที่</label>
    <input type="text" class="form-control input-sm" name="doc_date" id="doc_date" style="text-align:center;" value="<?php echo date("d-m-Y"); ?>" />
</div>
<div class="col-sm-3">
	<label>ผู้ยืม</label>
    <input type="text" class="form-control input-sm" name="lender" id="lender" placeholder="ระบุชื่อพนักงานผู้ยืมสินค้า" />
    <input type="hidden" name="id_employee" id="id_employee" value="" />
</div>
<div class="col-sm-6">
	<label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" placeholder="ระบุหมายเหตุ (ถ้ามี)" />
</div>
<div class="col-sm-1">
	<label style="display:block; visibility:hidden">add</label>
    <button type="button" class="btn btn-success btn-sm btn-block" onclick="add_new()"><i class="fa fa-plus"></i>&nbsp; เพิ่ม</button>
</div>
</div>
<?php else :   //// if( id order ) ?>
<?php 
	$id_order 	= $_GET['id_order']; 
	$order 		= new order($id_order);
?>

<div class="row">
<div class="col-sm-2">
	<label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm" value="<?php echo $order->reference; ?>" disabled />
    <input type="hidden" name="id_order" id="id_order" value="<?php echo $id_order; ?>" />
</div>
<div class="col-sm-2">
	<label>วันที่</label>
    <input type="text" class="form-control input-sm" name="doc_date" id="doc_date" style="text-align:center;" value="<?php echo thaiDate($order->date_add); ?>" disabled />
</div>
<div class="col-sm-3">
	<label>ผู้ยืม</label>
    <input type="text" class="form-control input-sm" name="lender" id="lender" placeholder="ระบุชื่อพนักงานผู้ยืมสินค้า" value="<?php echo employee_name($order->id_employee); ?>" disabled />
    <input type="hidden" name="id_employee" id="id_employee" value="<?php echo $order->id_employee; ?>" />
</div>
<div class="col-sm-4">
	<label>หมายเหตุ</label>
    <input type="text" class="form-control input-sm" name="remark" id="remark" placeholder="ระบุหมายเหตุ (ถ้ามี)" value="<?php echo $order->comment; ?>" disabled />
</div>
<div class="col-sm-1">
	<label style="display:block; visibility:hidden">add</label>
    <button type="button" class="btn btn-warning btn-sm" id="btn_edit" onclick="edit()"><i class="fa fa-pencil"></i>&nbsp; แก้ไข</button>
    <button type="button" class="btn btn-success btn-sm" id="btn_update" onclick="update()" style="display: none;"><i class="fa fa-save"></i> &nbsp; บันทึก</button>
</div>
</div>
<hr style="margin-top:10px;"/>
<!----------------------------------------- Category Menu ---------------------------------->
<div class='row'>
	<div class='col-sm-12'>
		<ul class='nav navbar-nav' role='tablist' style='background-color:#EEE'>
		<?php echo categoryTabMenu('order'); ?>
		</ul>
	</div><!---/ col-sm-12 ---->
</div><!---/ row -->
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<div class='row'>
	<div class='col-sm-12'>		
		<div class='tab-content' style="min-height:1px; padding:0px;">
		<?php echo getCategoryTab(); ?>
		</div>
	</div>
</div>
<!------------------------------------ End Category Menu ------------------------------------>	

<!-----------------------------------------  เริ่ม ORDER GRID ---------------------------------->

<hr/>
<div class="row">
<div class="col-sm-12">
<?php $qr = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = ".$id_order); ?>
<?php $row = dbNumRows($qr); ?>

<table class="table">
<thead style="font-size:12px;">
	<th style="width:5%; text-align:center;">ลำดับ</th>
    <th style="width:5%; text-align:center;">รูป</th>
    <th style="width:10%;">บาร์โค้ด</th>
    <th style="width:35%;">สินค้า</th>
    <th style="width:10%; text-align:center;">ราคา</th>
    <th style="width:10%; text-align:center;">จำนวน</th>
    <th style="width:10%; text-align:center;">มูลค่า</th>
    <th style="text-align:center;">การกระทำ</th> 
</thead>
<tbody id="rs">
<?php if( $row > 0 ) : ?>
<?php 	$n 				= 1; 		?>
<?php 	$total_qty 		= 0; 			?>
<?php	$total_amount 	= 0; 			?>
<?php 	while($rs = dbFetchArray($qr) ) : ?>
<?php 		$id = $rs['id_product_attribute']; ?>
<?php		$product = new product(); ?>
	<tr id="row_<?php echo $id; ?>" style="font-size:12px;">
    	<td align="center"><?php echo $n; ?></td>
        <td align="center"><img src='<?php echo $product->get_product_attribute_image($id,1); ?>' height="35px" width="35px" /></td>
        <td><?php echo $rs['barcode']; ?></td>
        <td><?php echo $rs['product_reference']." : ".$rs['product_name']; ?></td>
        <td align="center"><?php echo number_format($rs['product_price'], 2); ?></td>
        <td align="center"><?php echo number_format($rs['product_qty']); ?></td>
        <td align="center"><?php echo number_format($rs['total_amount'],2); ?></td>
        <td align="right">
        <?php if($edit) : ?>
        	<button type="button" class="btn btn-sm btn-danger" onclick="delete_row(<?php echo $id; ?>)"><i class="fa fa-trash"></i></button>
		<?php endif; ?>
        </td>
    </tr>
<?php $total_qty += $rs['product_qty'];  $total_amount += $rs['product_qty'] * $rs['product_price']; ?>    
<?php	$n++; ?>
<?php	endwhile; ?>
	<tr style="font-size:18px;">
    	<td align="right" colspan="5"><strong>รวม</strong></td>
        <td align="center"><strong><?php echo number_format($total_qty); ?></strong></td>
        <td align="center"><strong><?php echo number_format($total_amount, 2); ?></strong></td>
        <td></td>
    </tr>
<?php else : ?>
	<tr><td colspan="7" align="center"><h4>----- ไม่พบรายการสินค้า  -----</h4></td></tr>
<?php endif; ?>    
</tbody>
</table>


</div>
</div>
<script id="lend_template" type="text/x-handlebars-template">
{{#each this}}
	{{#if @last}}
		<tr id="row_{{id}}" style="font-size:18px;">
			<td align="right" colspan="5"><strong>รวม</strong></td>
			<td align="center"><strong>{{total_qty}}</strong></td>
			<td align="center"><strong>{{total_amount}}</strong></td>
			<td align="right"></td>
		</tr>
    {{else}}
		<tr id="row_{{id}}" style="font-size:12px;">
			<td align="center">{{no}}</td>
			<td align="center"><img src="{{img}}" height="35px" width="35px" /></td>
			<td>{{barcode}}</td>
			<td>{{product}}</td>
			<td align="center">{{price}}</td>
			<td align="center">{{qty}}</td>
			<td align="center">{{amount}}</td>
			<td align="right">{{{btn}}}</td>
		</tr>
	{{/if}}
{{/each}}
</script>
<!------------------------------------ จบ ORDER GRID ------------------------------------>	
<form id="add_form" method='post'>
	<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' id='modal'>
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
					<h4 class='modal-title' id='modal_title'>title</h4>
				 </div>
				 <div class='modal-body' id='modal_body'></div>
				 <div class='modal-footer'>
					<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
					<button type='button' class='btn btn-primary' onclick="add_to_order()">เพิ่มในรายการ</button>
				 </div>
			</div>
		</div>
	</div>
</form>

<?php endif;  /// endif( ! isset( $id_order )) ?>

<?php elseif( isset( $_GET['view_detail'] ) && isset( $_GET['id_order'] ) ) : ?>
	<?php $id_order = $_GET['id_order']; ?>
	<?php $order	= new order($id_order); ?>
    <?php $lend 	= new lend(); 		?>
    
    <input type="hidden" id="id_order" name="id_order" value="<?php echo $id_order; ?>" />
    <input type="hidden" id="current_state" value="<?php echo $order->current_state; ?>" />
    <input type="hidden" id="user_id" value="<?php echo $user_id; /// user_id from index.php ?>" />
    <div class='row'>
        <div class='col-sm-12 col-sx-12'>
            <h4 style="margin-bottom:0px;"><?php 	echo $order->reference; ?> <p class='pull-right' style="margin-bottom:0px;">ผู้ยิม : &nbsp; <?php echo employee_name($order->id_employee); ?></p></h4>
        </div>
	</div>
    <hr/>
	<div class='row'>
        <div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
            <dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>วันที่สั่ง : &nbsp;</dt>
            <dd style='float:left; margin:0px; padding-right:10px'><?php echo thaiDate($order->date_add); ?></dd>  |</dt></dl>
            <dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>สินค้า :&nbsp;</dt>
            <dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_product); ?></dd>  |</dt></dl>
            <dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>จำนวน : &nbsp;</dt>
            <dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_qty); ?></dd>  |</dt></dl>
            <dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>ยอดเงิน : &nbsp;</dt>
            <dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_amount,2); ?></dd>  |</dt></dl>
            <dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>ผู้ทำรายการ : &nbsp;</dt>
            <dd style='float:left; margin:0px; padding-right:10px'><?php echo employee_name(get_lend_user_id($order->id_order)); ?></dd> </dt></dl>
            <p class='pull-right' style="margin-bottom:0px;">
            	<button type="button" class="btn btn-info btn-sm" onClick="printLend()" /><i class="fa fa-print"></i> พิมพ์</button>
            	<?php $closed = $lend->isClosed($id_order);  /// 1 = คืนหมดแล้ว 0 = ยังต้องคืนอีก ?>
            	<?php if( $return ) : ?> 
				<?php 	if($order->order_status ==1 && !$closed && $order->current_state == 9 ) : ?>
            					<button type="button" class="btn btn-primary btn-sm" onclick="return_product(<?php echo $id_order; ?>)"><i class="fa fa-retweet"></i>&nbsp; คืนสินค้า</button>
				<?php 	endif; ?>
				<?php endif; ?>
            	<?php if( $edit && $order->current_state < 4) : ?> 
                	<button type="button" class="btn btn-warning btn-sm" onclick="edit_order(<?php echo $id_order; ?>)"><i class="fa fa-pencil"></i>&nbsp; แก้ไข</button> 
				<?php endif; ?>
            </p>
        </div>
	</div>
    <hr />
    <?php if($order->order_status == 1 ) : ?>
    <div class="row">
    	<div class="col-sm-6 col-xs-12">
        <table class="table" style="border: solid #ccc 1px;">
        <?php if( $lend->total_return_qty($id_order) == 0 ) : ?>
        <tr id="state_table">
        	<td style="width:25%; text-align:right; vertical-align:middle;">สถานะ</td>
            <td style="width:40%;">
            	<select id="state" name="state" class="form-control input-sm">
                	<option value="">เลือกสถานะ</option>
                    <option value="1">รอการยืนยัน</option>
                    <option value="3">รอจัดสินค้า</option>
                    <option value="8">ยกเลิก</option>
                </select>
            </td>
            <td style="text-align:left; padding-left:15px;">
            	<button type="button" class="btn btn-primary btn-sm" onclick="state_change()"><i class="fa fa-plus"></i>&nbsp; เพิ่ม</button>
            </td>
        </tr>
        <?php else : ?>
        <tr><td colspan="3" style="color:red;">*** ไม่สามารถเปลี่ยนสถานะได้ เนื่องจากมีการคืนสินค้าแล้ว ***</td></tr>
        <?php endif; ?>
	<?php $qs = $order->orderState(); ?>
    <?php while($rs = dbFetchArray($qs) ) : ?>
    	<tr  style='background-color:<?php echo state_color($rs['id_order_state']); ?>'>
            <td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'><?php echo $rs['state_name']; ?></td>
			<td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'><?php echo $rs['first_name']." ".$rs['last_name']; ?></td>
			<td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'><?php echo date('d-m-Y H:i:s', strtotime($rs['date_add'])); ?></td>
        </tr>    
	<?php endwhile; ?>           
        </table>
        </div>
    </div>
    <hr />
    <?php endif; ?>
    <div class="row">
    <div class="col-lg-12">
    <?php $qr = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = ".$order->id_order); ?>
    <?php if(dbNumRows($qr) > 0 ) : ?>
    <p style="color:red;">*** เมื่อเปิดบิลแล้ว จำนวนยืม = จำนวนที่มีการเปิดบิล (ซึ่งอาจน้อยกว่าจำนวนที่ยืม)   หากยังไม่ถูกเปิดบิล จำนวนยืม = จำนวนที่ยืมตามเอกสาร (ยังไม่ได้รับของจริง) </p>
    <table class="table table-striped" style="border: solid 1px #CCC;">
    <thead style="font-size:12px;">
    	<th style="width:10%; text-align:center;">รูปภาพ</th>
        <th style="width:35%;">สินค้า</th>
        <th style="width:10%; text-align:right;">ราคา</th>
        <th style="width:10%; text-align:right;">จำนวนยืม</th>
        <th style="width:10%; text-align:right;">คืนแล้ว</th>
        <th style="width:10%; text-align:right;">คงเหลือ</th>
        <th style="width:15%; text-align:right;">มูลค่าคงเหลือ</th>
    </thead>
    <?php 	$total_lend 		= 0; 					?>
    <?php 	$total_amount 	= 0; 					?>
    <?php	$total_return	= 0; 					?>
    <?php 	$total_balance	= 0;					?>
    <?php 	while($rd = dbFetchArray($qr)) : 	?>
    <?php 		$id_pa		= $rd['id_product_attribute']; 	?>
    <?php		$product 	= new product(); 					?>
    <?php		$lend_qty 	= $order->current_state == 9 ? $lend->lend_qty($order->id_order, $id_pa) : $rd['product_qty']; 	?>
    <?php 		$return_qty = $order->current_state == 9 ? $lend->return_qty($order->id_order, $id_pa) : 0; 					?>
    <?php		$bal_qty		= $order->current_state == 9 ? $lend_qty - $return_qty : 0; 												?>	
    <?php		$amount 		= $order->current_state == 9 ? ($lend_qty - $return_qty) * $rd['product_price'] : $lend_qty * $rd['product_price']; ?>
    <tr style="font-size:12px;">
    	<td align="center"><img src="<?php echo $product->get_product_attribute_image($id_pa, 1); ?>" width="35px" height="35px" /></td>
        <td><?php echo $rd['product_reference']." : ".$rd['product_name']." : ".$rd['barcode']; ?></td>
        <td align="right"><?php echo number_format($rd['product_price'], 2); ?></td>
        <td align="right"><?php echo number_format($lend_qty); ?></td>
        <td align="right"><?php echo number_format($return_qty); ?></td>
        <td align="right"><?php echo number_format($bal_qty); ?></td>
        <td align="right"><?php echo number_format($amount, 2); ?></td>
    </tr>
    <?php 		$total_lend += $lend_qty;   $total_return += $return_qty;   $total_balance += $bal_qty;   $total_amount += $amount; ?>
    <?php 	endwhile; ?>
    <tr style="font-size:12px;">
    	<td colspan="3"><strong>หมายเหตุ : </strong><?php echo $order->comment; ?></td>
        <td align="right"><strong><?php echo number_format($total_lend); ?></strong></td>
        <td align="right"><strong><?php echo number_format($total_return); ?></strong></td>
        <td align="right"><strong><?php echo number_format($total_balance); ?></strong></td>
        <td align="right"><strong><?php echo number_format($total_amount, 2); ?></strong></td>
    </tr>
    </table>
    <?php else : ?>
    <center><h4>----- ไม่พบรายการ  -----</h4></center>
	<?php endif; ?>
    </div>
    </div>
<?php elseif( isset($_GET['return']) && isset($_GET['id_order']) ) :    /////  ****** คืนสินค้า ****  ?>
	<?php $id_order 	= $_GET['id_order']; 					?>
    <?php $order 		= new order($id_order); 				?>
<input type="hidden" name="id_employee" id="id_employee" value="<?php echo $order->id_employee; ?>" />  
 <input type="hidden" name="id_order" id="id_order" value="<?php echo $id_order; ?>" />  
<div class="row">
    <div class="col-sm-3">
    	<div class="input-group">
        	<span class="input-group-addon">เลขที่เอกสาร</span>
            <input type="text" class="form-control input-sm" value="<?php echo $order->reference; ?>" disabled="disabled" />
        </div>  
    </div>
    <div class="col-sm-3">
    	<div class="input-group">
	        <span class="input-group-addon">วันที่</span>
            <input type="text" class="form-control input-sm" style="text-align:center;" value="<?php echo thaiDate($order->date_add); ?>" disabled  />
		</div>        
    </div>
    <div class="col-sm-3">
    	<div class="input-group">
        	<span class="input-group-addon">ผู้ยืม</span>
            <input type="text" class="form-control input-sm" value="<?php echo employee_name($order->id_employee); ?>" disabled  />
        </div>
    </div>
    <div class="col-sm-3">
    	<div class="input-group">
        	<span class="input-group-addon">ผู้ทำรายการ</span>
            <input type="text" class="form-control input-sm" value="<?php echo employee_name(get_lend_user_id($id_order)); ?>" disabled  />
        </div>
    </div>
    <div class="col-sm-12" style="min-height:15px;"></div>
    <div class="col-sm-12">
    	<div class="input-group">
        	<span class="input-group-addon">หมายเหตุ</span>
            <input type="text" class="form-control input-sm" disabled value="<?php echo $order->comment; ?>" />
    	</div>
    </div>
</div>
<hr style="margin-top:10px;"/>
<input type="hidden" id="id_zone" />
<div class="row">
	<div class="col-sm-3">
    	<div class="input-group">
        	<span class="input-group-addon">บาร์โค้ดโซน</span>
            <input type="text" class="form-control input-sm" id="zone_barcode" placeholder="ยิงบาร์โค้ดโซน" autofocus />
        </div>
    </div>
    <div class="col-sm-3">
    	<div class="input-group">
        	<span class="input-group-addon">ชื่อโซน</span>
            <input type="text" class="form-control input-sm" id="zone_name" placeholder="ค้นหาชื่อโซน" />
        </div>
    </div>
    
    <div class="col-sm-1" style="padding-left:5px; padding-right:5px;">
    	<div class="input-group">
        	<span class="input-group-addon" style="padding-left:5px; padding-right:5px;">จำนวน</span>
            <input type="text" class="form-control input-sm" style="padding-left:2px; text-align:center;" id="qty" value="1" />
        </div>
    </div>
    <div class="col-sm-3">
    	<div class="input-group">
        	<span class="input-group-addon">สินค้า</span>
            <input type="text" class="form-control input-sm" id="item_barcode" placeholder="ยิงบาร์โค้ดสินค้า" />
        </div>
    </div>
    <div class="col-sm-2">
    	<button type="button" class="btn btn-primary btn-sm btn-block" onclick="change_zone()">เปลี่ยนโซน (F2)</button>
    </div>
</div>
<hr style="margin-top:10px;"/>
<div class="row">
	<div class="col-sm-12">
		<table class="table table-striped">
            <thead style="font-size:12px;">
                <th style="width:10%; text-align:center;">รูปภาพ</th>
                <th style="width:35%;">สินค้า</th>
                <th style="width:10%; text-align:right;">ราคา</th>
                <th style="width:10%; text-align:right;">จำนวนยืม</th>
                <th style="width:10%; text-align:right;">คืนแล้ว</th>
                <th style="width:10%; text-align:right;">คงเหลือ</th>
                <th style="width:15%; text-align:right;">มูลค่าคงเหลือ</th>
            </thead>
            <tbody id="rs">
	<?php $qs = dbQuery("SELECT * FROM tbl_order_detail_sold WHERE id_order = ".$id_order); ?>
    <?php if(dbNumRows($qs) > 0) : 										?>
    <?php $n 			= 1; 													?>
    <?php $lend 		= new lend(); 										?>
    <?php $product 	= new product(); 									?>
    <?php $total_lend 		= 0; 												?>
    <?php $total_amount 	= 0; 												?>
    <?php $total_return	= 0; 												?>
    <?php $total_balance	= 0;												?>
    <?php 	while($rs = dbFetchArray($qs)) : 							?>
    <?php		$id_pa = $rs['id_product_attribute']; 					?>
    <?php 		$lend_qty	= $rs['sold_qty']; 							?>
    <?php 		$return_qty	= $lend->return_qty($id_order, $id_pa); ?>
    <?php		$bal_qty		= $lend_qty - $return_qty; 					?>
    <?php		$amount		= $bal_qty * $rs['product_price']; 			?>
    <tr style="font-size:12px;" id="row_<?php echo $id_pa; ?>">
    	<td align="center"><img src="<?php echo $product->get_product_attribute_image($id_pa, 1); ?>" width="35px" height="35px" /></td>
        <td><?php echo $rs['product_reference']." : ".$rs['product_name']; ?></td>
        <td align="right"><?php echo number_format($rs['product_price'], 2); ?></td>
        <td align="right" class="lend_qty"><?php echo number_format($lend_qty); ?></td>
        <td align="right" class="return_qty"><?php echo number_format($return_qty); ?></td>
        <td align="right" class="balance_qty"><?php echo number_format($bal_qty); ?></td>
        <td align="right" class="amount"><?php echo number_format($amount, 2); ?></td>
    </tr>
    <?php 		$total_lend += $lend_qty;   $total_return += $return_qty;   $total_balance += $bal_qty;   $total_amount += $amount; ?>
    <?php  	endwhile; ?>
    <tr style="font-size:16px;">
    	<td colspan="3" align="right"><strong>รวม</strong></td>
        <td align="right" class="total_lend"><strong><?php echo number_format($total_lend); ?></strong></td>
        <td align="right" class="total_return"><strong><?php echo number_format($total_return); ?></strong></td>
        <td align="right" class="total_balance"><strong><?php echo number_format($total_balance); ?></strong></td>
        <td align="right" class="total_amount"><strong><?php echo number_format($total_amount, 2); ?></strong></td>
    </tr>
    <?php endif; ?>               
            </tbody>
        </table>
	</div>
</div>

<script id="row_template" type="text/x-handlebars-template">
    	<td align="center">{{{ img }}}</td>
        <td>{{ product }}</td>
        <td align="right">{{ price }}</td>
        <td align="right" class="lend_qty">{{ lend_qty }}</td>
        <td align="right" class="return_qty">{{ return_qty }}</td>
        <td align="right" class="balance_qty">{{ balance_qty }}</td>
        <td align="right" class="amount">{{ amount }}</td>
</script>	
<?php else :   /// main if ?>
<?php
	if( isset($_POST['from_date']) && $_POST['from_date'] !=""){ $from = fromDate($_POST['from_date']); setcookie("lend_from_date", dbDate($_POST['from_date']), 0, "/"); }else if( isset($_COOKIE['lend_from_date']) ){ $from = fromDate($_COOKIE['lend_from_date']); }else{ $from = ""; }
	if( isset($_POST['to_date']) && $_POST['to_date'] != ""){ $to = toDate($_POST['to_date']); setcookie("lend_to_date",  dbDate($_POST['to_date']), 0, "/"); }else if( isset($_COOKIE['lend_to_date']) ){ $to = toDate($_COOKIE['lend_to_date']); }else{ $to = ""; }
	if( isset($_POST['search_text']) && $_POST['search_text'] != "" ){ $text = $_POST['search_text']; setcookie("lend_search_text", $text, 0, "/"); }else if( isset($_COOKIE['lend_search_text']) ){ $text = $_COOKIE['lend_search_text']; }else{ $text = ""; }
	if( isset($_POST['filter']) ){ $filter = $_POST['filter']; setcookie("lend_filter", $filter, 0, "/"); }else if( isset($_COOKIE['lend_filter']) ){ $filter = $_COOKIE['lend_filter']; }else{ $filter = ""; }
	$paginator = new paginator();
?>	
<form  method='post' id='form'>
<div class='row'>
	<div class='col-lg-2 col-md-2 col-sm-3 col-sx-3'>
		<label>เงื่อนไข</label>
		<select class='form-control input-sm' name='filter' id='filter'>
        <option value="reference" <?php if( $filter != "" ){ echo isSelected($filter, "reference"); } ?>>เลขที่เอกสาร</option>
		<option value="employee" <?php if( $filter != "" ){  echo isSelected($filter, "employee"); } ?>>ผู้ยืม</option>
		</select>
		
	</div>	
	<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'>
    	<label>คำค้น</label>
		<input class='form-control input-sm' type='text' name='search_text' id='search_text' placeholder="ระบุคำที่ต้องการค้นหา" value='<?php echo $text; ?>' />	
	</div>	
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
		<label>จากวันที่</label>
			<input type='text' class='form-control input-sm' name='from_date' id='from_date' placeholder="ระบุวันที่" style="text-align:center;"  value='<?php echo $from == "" ? $from : thaiDate($from); ?>'/>
	</div>	
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
		<label>ถึงวันที่</label>
			<input type='text' class='form-control input-sm'  name='to_date' id='to_date' placeholder="ระบุวันที่" style="text-align:center" value='<?php echo $to == "" ? $to : thaiDate($to); ?>' />
	</div>
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
    	<label style="visibility:hidden">show</label>
		<button class='btn btn-primary btn-sm btn-block' id='search-btn' type='submit' onclick="load_in()" ><i class="fa fa-search"></i>&nbsp;ค้นหา</button>
	</div>	
	<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
    	<label style="visibility:hidden">show</label>
		<button type='button' class='btn btn-danger btn-sm' onclick="clear_filter()"><i class='fa fa-refresh'></i>&nbsp;reset</button>
	</div>
</div>
</form>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />
<?php
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		
		/****  เงื่อนไขการแสดงผล *****/
		$where = "WHERE role = 3 ";
		if( $text != "" ) :
			switch( $filter) :				
				case "employee" :
					$in_cause = "";
					$qs = dbQuery("SELECT id_employee FROM tbl_employee WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%'");
					$rs = dbNumRows($qs);
					$i=0;
					if($rs>0) :
						while($i<$rs) :
							list($in) = dbFetchArray($qs);
							$in_cause .="$in";
							$i++;
							if($i<$rs){ $in_cause .=","; 	}
						endwhile;
						$where .= "AND id_employee IN($in_cause)" ; 
					else :
						$where .= "AND id_employee = 0" ; 
					endif;
				break;
				case "reference" :
				$where .= "AND reference LIKE'%$text%'";
				break;
			endswitch;
			if($from != "" && $to != "" ) : 
				$where .= " AND (date_add BETWEEN '".$from."' AND '".$to."')";  
			endif;
		else :
			if($from != "" && $to != "" ) : 
				$where .= "AND (date_add BETWEEN '".$from."' AND '".$to."')";  
			endif;	
		endif;
		$where .= " ORDER BY date_add DESC";
		
?>		
<?php
		$paginator->Per_Page("tbl_order", $where, $get_rows);
		$paginator->display($get_rows,"index.php?content=lend");
?>		


<div class="row">
<table class="table">
<thead style="font-12px; background-color:#3C9">
	<th style="width: 5%; text-align:center;">ID</th>
    <th style="width: 10%;">เลขที่เอกสาร</th>
    <th style="width: 25%;">ผู้ยิม</th>
    <th style="width: 10%; text-align:center;">จำนวน</th>
    <th style="width: 10%; text-align:center;">คืนแล้ว</th>
    <th style="width: 10%; text-align:center;">สถานะ</th>
    <th style="width: 10%; text-align:center;">วันที่ยืม</th>
    <th ></th>
</thead>
	<?php $qs = dbQuery("SELECT * FROM tbl_order ".$where." LIMIT ".$paginator->Page_Start.", ".$paginator->Per_Page );  	?>
    <?php if( dbNumRows($qs) > 0 )  : 		?>
    <?php	while( $rs = dbFetchArray($qs) ) :					?>
    <?php 		$id 	= $rs['id_order']; 							?>
    <?php 		$lend	= new lend();									?>
    <?php 		$rt		= $rs['valid'] == 2 ? 'color: #FFF; background-color: #434A54;' : ($rs['order_status'] == 0 ? "" : 'color: #EEE; background-color: '.state_color($rs['current_state']).';');  ?>
    <?php 		$st 	= $rs['valid'] == 2 ? '' : "style='cursor: pointer;' onclick='view_detail(".$id.")'";  ?>
    <tr id="row_<?php echo $id; ?>" style="font-size:12px; <?php echo $rt; ?>" >
    	<td align="center" <?php echo $st; ?>><?php echo $id; ?></td>
        <td <?php echo $st; ?>><?php echo $rs['reference']; ?></td>
        <td <?php echo $st; ?>><?php echo employee_name($rs['id_employee']); ?></td>
        <td align="center" <?php echo $st; ?>><?php echo number_format($lend->total_lend_qty($id)); ?></td>
        <td align="center" <?php echo $st; ?>><?php echo number_format($lend->total_return_qty($id)); ?></td>
        <td align="center" <?php echo $st; ?>><?php echo $rs['order_status'] == 0 ? "ยังไม่บันทึก" : current_order_state($id); ?></td>
        <td align="center" <?php echo $st; ?>><?php echo thaiDate($rs['date_add']); ?></td>
        <td align="right">
        	<?php $closed = $lend->isClosed($id);  /// 1 = คืนหมดแล้ว 0 = ยังต้องคืนอีก ?>
        	<?php if( $return ) : ?> 
				<?php if($rs['order_status'] ==1 && !$closed && $rs['current_state'] == 9 ) : ?>
            		<button type="button" class="btn btn-primary btn-xs" onclick="return_product(<?php echo $id; ?>)"><i class="fa fa-retweet"></i>&nbsp; คืนสินค้า</button>
				<?php endif; ?>
			<?php endif; ?>
            <?php if( $rs['current_state'] != 8 && $rs['valid'] != 2 ) : ?>
            	<button type="button" class="btn btn-info btn-xs" onclick="view_detail(<?php echo $id; ?>)"><i class="fa fa-eye"></i>&nbsp; รายละเอียด</button>
			<?php endif; ?>
            <?php if( $edit && $rs['current_state'] < 4 && $rs['valid'] != 2 ) : ?> 		
            	<button type="button" class="btn btn-warning btn-xs" onclick="edit_order(<?php echo $id; ?>)"><i class="fa fa-pencil"></i>&nbsp; แก้ไข</button> 
			<?php endif; ?>
            <?php if( $delete && $rs['current_state'] < 4  ) : ?> 	
            	<button type="button" class="btn btn-danger btn-xs" onclick="delete_order(<?php echo $id; ?>)"><i class="fa fa-trash"></i>&nbsp; ลบ</button> 
			<?php endif; ?>    
        </td>
    </tr>
    <?php 	endwhile; 	?>
    <?php endif; ?>
    
</div>
<?php endif; ////  end main if ?> 



</div><!--- container --->
<script>
	function expandCategory(el)
	{
		var className = 'open';
		if (el.classList)
		{
    		el.classList.add(className)
		}else if (!hasClass(el, className)){
			el.className += " " + className
		}
	}

	function collapseCategory(el)
	{
		var className = 'open';
		if (el.classList)
		{
			el.classList.remove(className)
		}else if (hasClass(el, className)) {
			var reg = new RegExp('(\\s|^)' + className + '(\\s|$)')
			el.className=el.className.replace(reg, ' ')
  		}
	}
</script>

<script>
function total_lend_qty()
{
	var qty = 0;
	$(".lend_qty").each(function(index, element) {
        qty += parseInt($(this).html());
    });	
	return qty;
}
function total_return_qty()
{
	var qty = 0;
	$(".return_qty").each(function(index, element) {
        qty += parseInt($(this).html());
    });	
	return qty;
}
function get_total_amount()
{
	var amount = 0.00;
	$(".amount").each(function(index, element) {
        amount += parseFloat($(this).html());
    });	
	return amount;
}

function total_recal()
{
	var total_qty 		= total_lend_qty();
	var total_return 	= total_return_qty();
	var total_balance 	= total_qty - total_return;
	var total_amount 	= get_total_amount();
	$(".total_qty").html('<strong>'+addCommas(total_qty)+'</strong>');
	$(".total_return").html('<strong>'+addCommas(total_return)+'</strong>');
	$(".total_balance").html('<strong>'+addCommas(total_balance)+'</strong>');
}

function return_item()
{
	var id_order	= $("#id_order").val();
	var id_zone 	= $("#id_zone").val();
	var qty			= $("#qty").val();
	var barcode	= $("#item_barcode").val();
	if( id_order == "" || isNaN(parseInt(id_order))){ swal({title:"เกิดข้อผิดพลาด", text: "ไม่พบเลขที่เอกสาร กรุณาลองใหม่อีกครั้ง", type: "error", timer: 1500}); window.reload(); return false; }
	if(id_zone =="" || isNaN(parseInt(id_zone))){ swal("โซนไม่ถูกต้อง", "กรุณาระบุโซนที่ถูกต้อง", "error"); return false; }
	if(qty =='' || isNaN(parseInt(qty)) ){ swal("จำนวนไม่ถูกต้อง", "กรุณาระบุจำนวนที่ถูกต้อง", "error"); return false; }
	if(barcode ==''){ return false; }
	$("#item_barcode").val("");
	load_in();
	$.ajax({
		url:"controller/lendController.php?return_product&id_order="+id_order,
		type:"POST", cache:"false", data:{ "id_zone" : id_zone, "qty" : qty, "barcode" : barcode },
		success: function(rs)
		{
			load_out();
			var rs = $.trim(rs);
			if(!isNaN(parseInt(rs))){	update_row(rs);	 $("#item_barcode").focus(); }
			if(rs == "no_product"){ swal("บาร์โค้ดผิด !!", "ไม่พบสินค้านี้ในฐานข้อมูล สินค้าอาจยังไม่ถูกเพิ่มหรือบาร์โค้ดผิด", "error"); }
			if(rs == "product_not_in"){ swal("สินค้าผิด", "ไม่มีสินค้านี้ในเอกสารการยืม กรุณาตรวจสอบความถูกต้อง", "error"); }
			if(rs == "returned"){ swal("สินค้าเกิน", "สินค้านี้ถูกคืนครบแล้วไม่สามารถรับคืนเกินกว่าจำนวนที่ยืมได้ กรุณาตรวจสอบ", "error"); }
			if(rs == "over_return"){ swal("สินค้าเกิน", "จำนวนสินค้าที่จะยกเลิกการคืนเกินกว่าจำนวนที่คืนไปแล้ว", "error"); }
			if(rs == "fail"){ swal("ไม่สำเร็จ", "ไม่สามารถรับคืนสินค้านี้ได้สำเร็จ กรุณาลองใหม่อีกครั้งภายหลัง", "error"); }	
		}
	});		
}

function update_row(id)
{
	var id_order = $("#id_order").val();
	$.ajax({
		url:"controller/lendController.php?get_lend_detail&id_order="+id_order+"&id_product_attribute="+id,
		type:"GET", cache:false, success: function(rs)
		{
			var rs = $.trim(rs);
			var source = $("#row_template").html();
			var data		= $.parseJSON(rs);
			var output	= $("#row_"+id);	
			render(source, data, output);
			$("#row_"+id).animate({"background-color" : "#F36D6D"}, 1000);
			total_recal();
		}
	});
}

function change_zone()
{
	$("#id_zone").val('');
	$("#zone_barcode").val('');
	$("#zone_barcode").removeAttr("disabled");
	$("#zone_name").val('');
	$("#zone_name").removeAttr("disabled");
	$("#zone_barcode").focus();	
}
$(document).keyup(function(e) {
    if(e.keyCode == 113)
	{
		change_zone();	
	}
});

$("#item_barcode").keyup(function(e) {
	if(e.keyCode == 13)
	{
		return_item();	
	}
});

$("#zone_barcode").keyup(function(e) {
    if(e.keyCode == 13)
	{
		get_zone();
	}
});

function get_zone()
{
	$("#id_zone").val("");
	var barcode = $("#zone_barcode").val();
	if(barcode != '')
	{
		$.ajax({
			url:"controller/lendController.php?get_zone&barcode="+barcode,
			type:"GET", cache:false, success: function(rs)
			{
				var rs = $.trim(rs);
				if(rs != "fail")
				{
					var arr = rs.split(" | ");
					if(isNaN(arr[0]))
					{ 
						$("#zone_barcode").val("");
						swal("บาร์โค้ดไม่ถูกต้อง");	
					}
					else
					{
						$("#id_zone").val(arr[0]);
						$("#zone_name").val(arr[1]);
						$("#zone_barcode").attr("disabled", "disabled");
						$("#zone_name").attr("disabled", "disabled");
						$("#item_barcode").focus();	
					}
				}
				else
				{
					$("#zone_barcode").val("");
					swal("บาร์โค้ดไม่ถูกต้อง");	
				}
			}
		});
	}
}

$("#zone_name").autocomplete({
	minLength : 1,
	source : "controller/autoComplete.php?get_zone_name",
	autoFocus : true,
	close: function(event, ui)
	{
		$("#id_zone").val("");
		var rs = $(this).val();
		if(rs !="" )
		{
			var arr = rs.split(" : ");
			var id		= arr[0];
			if( !isNaN(parseInt(id)) && typeof(arr[1]) !== 'undefined')
			{
				$("#id_zone").val(id);
				$(this).val(arr[1]);
				$("#zone_barcode").attr("disabled", "disabled");
				$(this).attr("disabled", "disabled");
				$("#item_barcode").focus();
			}
		}
	}
});

function view_detail(id)
{
	window.location.href="index.php?content=lend&view_detail&id_order="+id;	
}

function return_product(id)
{
	window.location.href="index.php?content=lend&return&id_order="+id;	
}

function state_change()
{
	var id_order 	= $("#id_order").val();
	var c_state		= $("#current_state").val();
	var n_state		= $("#state").val();
	var user_id		= $("#user_id").val();
	if( n_state != "" && n_state != c_state )
	{
		load_in();
		$.ajax({
			url:"controller/lendController.php?change_state&id_order="	+id_order,
			type:"GET", cache:"false", data:{"user_id" : user_id, "order_state" : n_state},
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if(rs == "success")
				{
					swal({ title: "สำเร็จ", text: "เปลี่ยนสถานะเรียบร้อยแล้ว", timer: 1000, type: "success"});
					setTimeout(function(){ window.location.href="index.php?content=lend&id_order="+id_order+"&view_detail"; }, 1000);	
				}
				else
				{
					swal("ไม่สำเร็จ", "เปลี่ยนสถานะไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
				}				
			}
		});
	}
}

function delete_row(id)
{
	if( !isNaN(parseInt(id) ) )
	{
		swal({
			  title: "ต้องการลบรายการ ?",
			  text: "คุณแน่ใจว่าต้องการลบรายการนี้ โปรดเลือกการดำเนินการต่อไป",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			  confirmButtonText: "ใช่ ลบเลย",
			  cancelButtonText: "ไม่ ยกเลิก",
			  closeOnConfirm: false,
			}, function(){
				load_in();
				var id_order = $("#id_order").val();
				$.ajax({
					url:"controller/lendController.php?delete_row&id_order="+id_order+"&id_product_attribute="+id,
					type:"GET", cache:"false",
					success: function(rs)
					{
						var rs = $.trim(rs);
						if(rs == "success")
						{
							load_out();
							swal({title: "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", type:"success", timer: 1000} );
							reload_table();
						}else{
							load_out();
							swal("ไม่สำเร็จ", "ลบรายการที่ต้องการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
							return false;
						}
					}
				});						
		});
	}
}

function delete_order(id)
{
	if( !isNaN(parseInt(id) ) )
	{
		swal({
			  title: "คุณแน่ใจนะ ?",
			  text: "คุณกำลังจะลบเอกสาร เอกสารและรายการทั้งหมดในเอกสารจะถูกลบ ดำเนินการต่อ ?",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			  confirmButtonText: "ใช่ ลบเลย",
			  cancelButtonText: "ไม่ ยกเลิก",
			  closeOnConfirm: false,
			}, function(){
				load_in();
				$.ajax({
					url:"controller/lendController.php?delete_order",
					type:"POST", cache:"false", data:{ "id_order" : id },
					success: function(rs)
					{
						load_out();
						var rs = $.trim(rs);
						if(rs == "success")
						{
							swal({title: "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", type:"success", timer: 1000} );
							$("#row_"+id).remove();
						}else{
							swal("ไม่สำเร็จ", "ลบเอกสารและรายการที่ต้องการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
						}
					}
				});						
		});
	}
}


function add_to_order()
{
	var qty 			= 0;
	var id_order 	= $("#id_order").val();
	$('input[name^="qty"]').each(function(index, element) {
        qty += $(this).val();
    });
	/*
	$(".qty").each(function(index, element) {
        qty += $(this).val();
    });*/
	if(qty == 0){ swal("กรุณาใส่จำนวนอย่างน้อย 1"); return false; }
	$("#order_grid").modal("hide");
	load_in();
	$.ajax({
		url:"controller/lendController.php?add_to_order&id_order="+id_order,
		type:"POST", cache:"false", data: $("#add_form").serialize(),
		success: function(rs)
		{
			load_out();
			var rs = $.trim(rs);
			if(!isNaN(rs))
			{
				swal({ title: "เรียบร้อย", text: "เพิ่มสำเร็จ "+rs+" รายการ", timer: 1000, type: "success"});
				reload_table();
			}
			else
			{
				swal({ title: "ผิดพลาด", text: rs, type: "error"});
				reload_table();
			}			
		}
	});
}
function loader_in()
{
	$("#rs").animate({ opacity: 0.5 }, 300, function(){
		var pos 	= $("#rs").offset();
		var w		= $("#loader").width() /2;
		var wd	= $(document).width() /2;
		var left 	= wd - w;
		$("#loader").css("top", pos.top);
		$("#loader").css("left", left);
		$("#loader").css("display", "");
		$("#loader").animate({opacity:1 , top: '+=100'}, 300);
	});
}

function loader_out()
{
	$("#loader").animate({ opacity: 0, top: '-=100'}, 300, function(){
		$("#loader").css("display","none");
		$("#rs").animate({ opacity:1}, 300); });
		
}

function reload_table()
{
	loader_in();
	var id = $("#id_order").val();
	var edit = <?php if($edit){ echo 1; }else{ echo 0; } ?>;
	$.ajax({
		url:"controller/lendController.php?get_lend_table&id_order="+id+"&edit="+edit,
		type:"GET", cache:"false",
		success: function(rs)
		{
			var rs = $.trim(rs);
			var data 	= $.parseJSON(rs);
			var source = $("#lend_template").html();
			var output 	= $("#rs");
			render(source, data, output);
			loader_out();
		}
	});
	
}

function add_new()
{
	var id_emp 		= $("#id_employee").val();
	var emp			= $("#lender").val();
	var user_id		= <?php echo $user_id; /// user_id from index.php ?>;
	var doc_date	= $("#doc_date").val();
	var remark		= $("#remark").val();
	if( id_emp == "" || emp == ""){ swal("กรุณาระบุผู้ยืม"); return false; }
	if( !isDate(doc_date) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	load_in();
	$.ajax({
		url:"controller/lendController.php?add_new",
		type:"POST", cache: "false", data:{ "id_employee" : id_emp, "date_add" : doc_date, "remark" : remark, "id_user" : user_id  },
		success: function(rs)
		{
			load_out();
			var id = $.trim(rs);
			if( !isNaN(parseInt(id)) )
			{
				window.location.href = "index.php?content=lend&add=y&id_order="+id;
			}
			else
			{
				swal("Error !!", "ไม่สามารถเพิ่มเอกสารใหม่ได้ในขณะนี้ กรุณาลองใหม่อีกครั้งภายหลัง", "error");
			}
		}
	});		
}

function save_order()
{
	var id_order = $("#id_order").val();	
	load_in();
	$.ajax({
		url:"controller/lendController.php?save_order&id_order="+id_order,
		type:"GET", cache:"false", success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if(rs == "success")
			{
				swal({ title: "เรียบร้อย", text: "บันทึกเอกสารเรียบร้อยแล้ว", timer: 1000, type: "success"});
				setTimeout(go_back(), 3000);
			}
			else
			{
				swal({ title: "ไม่สำเร็จ", text: "บันทึกเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", type: "error"});	
			}
		}
	});
}

function save_edit(id_order)
{
	load_in();
	$.ajax({
		url:"controller/lendController.php?save_order&id_order="+id_order,
		type:"GET", cache:"false", success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if(rs == "success")
			{
				swal({ title: "เรียบร้อย", text: "บันทึกเอกสารเรียบร้อยแล้ว", timer: 1000, type: "success"});
				setTimeout(go_back(), 3000);
			}
			else
			{
				swal({ title: "ไม่สำเร็จ", text: "บันทึกเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", type: "error"});	
			}
		}
	});
}
function update()
{
	var id_order 	= $("#id_order").val();
	var id_emp		= $("#id_employee").val();
	var doc_date	= $("#doc_date").val();
	var remark		= $("#remark").val();
	var user_id		= <?php echo $user_id; ?>;
	if( id_order == ""){ swal("ไม่พบ id_order ออกจากหน้านี้แล้วเข้าใหม่อีกครั้ง"); return false; }
	if( id_employee == "" || id_employee == 0 ){ swal("ไม่พบ ID ของผู้ยืม ออกจากหน้านี้แล้วลองใหม่อีกครั้ง"); return false; }
	if( !isDate(doc_date) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	load_in();
	$.ajax({
		url:"controller/lendController.php?update_order&id_order="+id_order,
		type:"POST", cache: "false", data:{ "id_employee" : id_emp, "date_add" : doc_date, "remark" : remark, "user_id" : user_id },
		success: function(rs)
		{
			load_out();
			var rs = $.trim(rs);
			if(rs == "success")
			{
				swal({title: "สำเร็จ", text: "ปรับปรุงข้อมูลเรียบร้อยแล้ว", timer: 1000, type: "success"});
				$("#doc_date").attr("disabled", "disabled");
				$("#lender").attr("disabled", "disabled");
				$("#remark").attr("disabled", "disabled");
				$("#btn_update").css("display", "none");
				$("#btn_edit").css("display", "");
			}
			else
			{
				swal("ไม่สำเร็จ", "แก้ไขเอกสารไม่สำเร็จกรุณาลองใหม่ภายหลัง", "error");	
			}
		}
	});	
}

function edit_order(id)
{
	window.location.href="index.php?content=lend&edit&id_order="+id;	
}
function edit()
{
	$("#doc_date").removeAttr("disabled");
	$("#lender").removeAttr("disabled");
	$("#remark").removeAttr("disabled");
	$("#btn_edit").css("display", "none");
	$("#btn_update").css("display", "");
}

function getData(id_product){
	var id_cus = 0;
	$.ajax({
		url:"controller/lendController.php?getData&id_product="+id_product,
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
				$("#order_grid").modal("show");
			}else{
				swal("ไม่พบสินค้า");
			}		
		}
	});
}
function new_add()
{
	var id_user = <?php echo $_COOKIE['user_id']; ?>;
	window.location.href = "controller/lendController.php?check_order_is_saved&id_user="+id_user;
}
function go_back()
{
	window.location.href="index.php?content=lend";	
}
function clear_filter()
{
	$.ajax({
		url:"controller/lendController.php?clear_filter",
		type:"GET", cache:false, success: function(rs)
		{
			go_back();	
		}
	});
}
$("#from_date").datepicker({	dateFormat : "dd-mm-yy", onClose: function(selectedDate){ $("#to_date").datepicker("option", "minDate", selectedDate); } });
$("#to_date").datepicker({ dateFormat: "dd-mm-yy", onClose: function(selectedDate){ $("#from_date").datepicker("option", "maxDate", selectedDate); } });
$("#doc_date").datepicker({ dateFormat: "dd-mm-yy"});
$("#lender").autocomplete({
	source : "controller/autoComplete.php?get_employee_id",
	autoFocus: true,
	close: function()
	{
		var emp = $(this).val();
		var arr	= emp.split(" : ");
		$("#id_employee").val(arr[0]);
		$(this).val(arr[1]);
	}
});
	
function printLend()
{
	var id_order = $("#id_order").val();
	var center = ($(document).width() - 800) /2;
	window.open("controller/lendController.php?print_lend&id_order="+id_order, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
	
}

//--------------------------------  โหลดรายการสินค้าสำหรับจิ้มสั่งสินค้า  -----------------------------//
function getCategory(id) {
    var output = $("#cat-" + id);
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
}
</script>

