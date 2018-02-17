<script src="<?php echo WEB_ROOT; ?>library/js/clipboard.min.js"></script>
<?php
	$page_name	= "ขายออนไลน์";
	$id_tab 			= 14;
    $pm 				= checkAccess($id_profile, $id_tab);
	$view 			= $pm['view'];
	$add 				= $pm['add'];
	$edit 				= $pm['edit'];
	$delete 			= $pm['delete'];
	accessDeny($view);
	require 'function/order_helper.php';
	require 'function/bank_helper.php';
	//-------------  ตรวจสอบออเดอร์ที่หมดอายุทุกๆ 24 ชั่วโมง  -----------//
	if( ! getCookie('expirationCheck') )
	{
		orderExpiration();
	}
	//-------------/  ตรวจสอบออเดอร์ที่หมดอายุทุกๆ 24 ชั่วโมง  /-----------//

?>
<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-desktop"></i>&nbsp;<?php echo $page_name; ?></h4></div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
	<?php if( isset($_GET['add'] ) || isset( $_GET['edit'] ) || isset( $_GET['view_stock'] ) ) : ?>
    	<button type="button" class="btn btn-sm btn-warning" onClick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
    <?php endif; ?>

    <?php if( isset( $_GET['add'] ) && isset( $_GET['id_order'] ) && $add ) : ?>
    <?php 	if( isSaved($_GET['id_order']) === FALSE ) : ?>
        	<button type="button" class="btn btn-success btn-sm" onClick="save(<?php echo $_GET['id_order']; ?>)"><i class="fa fa-save"></i> บันทึก</button>
    <?php 	endif; ?>
    <?php endif; ?>

    <?php if( isset( $_GET['edit'] ) && isset( $_GET['id_order'] ) && isset( $_GET['view_detail'] ) ) : ?>
    		<?php if( $edit ) : ?>
    		<button type="button" class="btn btn-sm btn-info" onClick="inputDeliveryNo()"><i class="fa fa-truck"></i> บันทึกการจัดส่ง</button>
            <?php endif; ?>
    	<?php $order = new order($_GET['id_order']); ?>
        <?php if( $order->valid == 0 ) : ?>
        	<?php if( $edit ) : ?>
        	<button type="button" class="btn btn-sm btn-primary" onClick="payOrder()"><i class="fa fa-credit-card"></i> แจ้งชำระเงิน</button>
            <?php endif; ?>
        <?php endif; ?>
    	<?php if( $order->valid == 0 && ($order->current_state ==1 || $order->current_state == 3 ) ) : ?>
        	<?php if( $edit ) : ?>
        	<button type="button" class="btn btn-warning btn-sm" onClick="getEdit(<?php echo $_GET['id_order']; ?>)"><i class="fa fa-pencil"></i> แก้ไข</button>
            <?php endif; ?>
        <?php endif; ?>
        <?php if( $order->valid == 1 && $order->current_state == 9 && isOrderClosed($order->id_order) == FALSE ) : ?>
        	<?php if( $edit ) : ?>
        	<button type="button" class="btn btn-sm btn-success" onClick="closeOrder(<?php echo $order->id_order; ?>)"><i class="fa fa-check"></i> ปิดออเดอร์</button>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if( !isset($_GET['add'] ) && !isset( $_GET['edit'] ) && !isset( $_GET['view_stock'] ) ) : ?>
    	<?php if( $add ) : ?>
        <button type="button" class="btn btn-primary btn-sm" onClick="addNewOnline()"><i class="fa fa-plus"></i> เพิ่มใหม่ ( ออนไลน์ )</button>
		<?php endif; ?>
    <?php endif; ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:15px;' />

<?php
//*********************************************** เพิ่มออเดอร์ใหม่ ********************************************************//
if(isset($_GET['add'])) :
	$vt_c					= getCookie('viewType') ? getCookie('viewType') : '';
	$user_id 			= $_COOKIE['user_id'];
	$id_order			= isset($_GET['id_order']) ? $_GET['id_order'] : '';
	$active 				= isset($_GET['id_order']) ? 'disabled' : '';
	$order 				= isset($_GET['id_order']) ? new order($id_order) : '';
	$new_ref 			= isset($_GET['id_order']) ? $order->reference: get_max_role_reference("PREFIX_ORDER",1);
	$customer 			= isset($_GET['id_order']) ? new customer($order->id_customer) : '';
	$id_customer 		= isset($_GET['id_order']) ? $customer->id_customer : '';
	$customer_name 	= isset($_GET['id_order']) ? $customer->full_name : '';
	$comment 			= isset($_GET['id_order']) ? $order->comment : '';
	$payment 			= isset($_GET['id_order']) ? $order->payment : '';
	$onlineCustomer	= isset($_GET['id_order']) ? getCustomerOnlineReference($id_order) : '';

?>
<form id='addForm'>
<div class='row'>
	<input type='hidden' name='id_employee' value='<?php echo $user_id; ?>' />
    <input type='hidden' name='id_order' id='id_order' value='<?php echo $id_order; ?>' />
    <input type='hidden' name='id_customer' id='id_customer' value='<?php echo $id_customer; ?>' />
    <input type="hidden" name="role" id="role" value="1" />
	<div class='col-sm-2'>
    	<label>เลขที่เอกสาร</label>
        <input type='text' id='doc_id' class='form-control input-sm' value='<?php echo $new_ref; ?>' disabled='disabled'/>
    </div>
	<div class='col-sm-2'>
		<label>วันที่</label>
		<input type='text' id='doc_date' name='doc_date' class='form-control input-sm text-center' value='<?php echo date('d-m-Y'); ?>' <?php echo $active; ?> />
    </div>
	<div class='col-sm-4'>
        	<label>ชื่อลูกค้า</label>
            <input type='text' id='customer_name' class='form-control input-sm' value='<?php echo $customer_name; ?>' autocomplete='off' <?php echo $active; ?> />
    </div>
    <div class="col-sm-2">
    	<label>อ้างอิงลูกค้า</label>
        <input type="text" name="online" id="online" class="form-control input-sm" value="<?php echo $onlineCustomer; ?>" <?php echo $active; ?> />
        <input type="hidden" name="payment" id="payment" value="ออนไลน์" />
    </div>
	<div class='col-sm-10'>
		<label>หมายเหตุ</label>
    	<input type='text' id='comment' name='comment' class='form-control input-sm' value='<?php echo $comment; ?>' autocomplete='off' <?php echo $active; ?> />
    </div>
	<div class='col-sm-2'>
    	<label style="display:block; visibility:hidden">button</label>
    <?php if( !isset( $_GET['id_order'] ) ) : ?>
		<button class='btn btn-default btn-sm btn-block' type='button' id='btnAdd' onClick="newOrder()">สร้างออเดอร์</button>
	<?php else : ?>
		<?php if( $edit ) : ?>
        	<button class='btn btn-default btn-sm btn-block' type='button' id='btnEdit' onClick="editOrder()"><i class="fa fa-pencil"></i> แก้ไขออเดอร์</button>
            <button type="button" class="btn btn-sm btn-success btn-block" id="btnUpdate" onClick="updateOrder(<?php echo $id_order; ?>)" style="display:none;"><i class="fa fa-save"></i> ปรับปรุง</button>
		<?php endif; ?>
	<?php endif; ?>
    </div>
</div><!--/ row -->
</form>

<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />

<?php if( isset( $_GET['id_order'] ) ) :  ?>

<div class='row'>
	<div class="col-sm-3">
    	<input type="text" class="form-control input-sm text-center" id="sProduct" placeholder="ค้นหาสินค้า" />
    </div>
    <div class="col-sm-2">
    	<button type="button" class="btn btn-primary btn-sm btn-block" onClick="getProduct()"><i class="fa fa-tags"></i> แสดงสินค้า</button>
    </div>
</div>

<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />

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

<form id="gridForm">
	<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' id='modal'>
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
					<h4 class='modal-title' id='modal_title'>title</h4>
                    <center><span style="color: red;">ใน ( ) = ยอดคงเหลือทั้งหมด   ไม่มีวงเล็บ = สั่งได้ทันที</span></center>
                    <input type="hidden" name="id_order" value="<?php echo $id_order; ?>" />
				 </div>
				 <div class='modal-body' id='modal_body'></div>
				 <div class='modal-footer'>
					<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
					<button type='button' class='btn btn-primary' onClick="addToOrder(<?php echo $id_order; ?>)" >เพิ่มในรายการ</button>
				 </div>
			</div>
		</div>
	</div>
</form>

<div class='row'>
<div class='col-sm-12'>
	<table class='table' id='order_detail' style="border: solid 1px #ddd;">
	<thead>
    <tr style='font-size: 12px;'>
		<th stype='width:5%; text-align:center;'>ลำดับ</th>
        <th style='width:5%; text-align:center;'>รูป</th>
        <th style='width:10%;'>บาร์โค้ด</th>
        <th style='width:30%;'>สินค้า</th>
		<th style='width:10%; text-align:center;'>ราคา</th>
        <th style='width:10%; text-align:center;'>จำนวน</th>
		<th style='width:10%; text-align:center;'>ส่วนลด</th>
        <th style='width:10%; text-align:center;'>มูลค่า</th>
        <th style='text-align:center;'>การกระทำ</th>
	</tr>
    </thead>
    <tbody id="orderProductTable">
<?php	$qs 		= dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = ".$id_order." ORDER BY id_order_detail DESC"); 	?>
<?php	$n 		= 1;	?>
<?php	$tq 		= 0;	?>
<?php	if( dbNumRows($qs) > 0 ) :	?>
<?php		$product = new product();	?>
<?php		while( $rs = dbFetchArray($qs) ) :		?>
		<tr style='font-size: 12px;'>
        	<td style='text-align:center; vertical-align:middle;'><?php echo $n; ?></td>
            <td style='text-align:center; vertical-align:middle;'><img src='<?php echo $product->get_product_attribute_image($rs['id_product_attribute'], 1); ?>' width='35px' height='35px' /> </td>
            <td style='vertical-align:middle;'><?php echo $rs['barcode']; ?></td>
            <td style='vertical-align:middle;'><?php echo $rs['product_reference']." : ".$rs['product_name']; ?></td>
            <td style='text-align:center; vertical-align:middle;'><?php echo number_format($rs['product_price'], 2); ?></td>
            <td style='text-align:center; vertical-align:middle;'><?php echo number_format($rs['product_qty']); ?></td>
            <td style='text-align:center; vertical-align:middle;'><?php echo discountLabel($rs['reduction_percent'], $rs['reduction_amount']);  ?></td>
            <td style='text-align:center; vertical-align:middle;'><?php echo number_format($rs['total_amount'], 2); ?></td>
            <td style='text-align:center; vertical-align:middle;'>
            	<button type="button" class="btn btn-danger btn-xs" onClick="deleteRow(<?php echo $rs['id_order_detail']; ?>, '<?php echo $rs['product_reference']; ?>')"><i class="fa fa-trash"></i></button>
            </td>
      	</tr>
<?php	$tq += $rs['product_qty'];	$n++;		?>
<?php endwhile; ?>
	<tr>
		<td colspan='6'></td>
        <td><h4>จำนวน</h4></td>
        <td style='text-align:center; vertical-align:middle;'><h4><?php echo number_format($tq); ?></h4></td>
        <td><h4>ชิ้น<h4></td>
	</tr>
<?php else : ?>
	<tr>
    	<td colspan='9' align='center'><h4>&nbsp;</h4></td>
    </tr>
<?php endif; ?>
	</tbody>
</table>
</div>
</div>
<?php endif; ?>

<!-----------------------------------------------------------จบหน้าเพิ่มออเดอร์ ---------------------------------------------->
<?php elseif( isset( $_GET['edit'] ) && isset( $_GET['id_order'] ) ) : ?>
<!--------------------------------------------------------- แก้ไขออเดอร์ ----------------------------------------------------->

<?php
	$id_employee	= $_COOKIE['user_id'];
	$id_order 		= $_GET['id_order'];
	$order 			= new order($id_order);
	if($order->id_customer != "0") :
		$customer = new customer($order->id_customer);
		$customer->customer_stat();
	endif;
	$sale 				= new sale($order->id_sale);
	$state 			= $order->orderState();
	$role 				= $order->role;
	$fee 				= getDeliveryFee($id_order);
	$service			= getServiceFee($id_order);
	$onlineCustomer = getCustomerOnlineReference($id_order);
	$online 			= $order->payment == 'ออนไลน์' ? TRUE : FALSE;
?>
<input type='hidden' name='id_order' id='id_order' value='<?php echo $id_order; ?>' />
<div class="row">
	<div class="col-sm-12">
    	<h5 class="title">
		<?php 	echo $order->reference." - ";  	if($order->id_customer != "0") : echo $customer->full_name; endif; ?>
        <?php 	if( $online && $onlineCustomer != '') : echo ' ( '.$onlineCustomer.' ) '; endif; ?>
        <p class='pull-right'>พนักงาน : &nbsp; <?php echo $sale->full_name; ?></p>
        </h5>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:5px;' />
<div class="row">
	<div class="col-sm-12">
		<dl><dt>วันที่สั่ง : <dd><?php echo thaiDate($order->date_add); ?></dd> | </dt></dl>
		<dl><dt>สินค้า : <dd><?php echo number_format($order->total_product); ?></dd> | </dt></dl>
		<dl><dt>จำนวน : <dd><?php echo number_format($order->total_qty); ?></dd> | </dt></dl>
		<dl><dt>ยอดเงิน : <dd><?php echo number_format($order->total_amount,2); ?></dd> </dt></dl>

        <p class='pull-right' style="margin-bottom:0px;">

        <?php if( $online ) : ?>
        		ค่าจัดส่ง :
			<?php if( ! $fee && ! isPaidOrder($id_order) ) : ?>
                <input type="text" id="deliveryFee" class="form-control input-sm input-mini" style="display:none;" placeholder="ค่าจัดส่ง" />
                <?php if( $edit ) : ?>
                <button type="button" id="btn-add-fee" class="btn btn-sm btn-info" onClick="addDeliveryFee()" ><i class="fa fa-plus"></i> เพิ่มค่าจัดส่ง</button>
                <button type="button" id="btn-update-fee" class="btn btn-sm btn-success" style="display:none;" onClick="updateDeliveryFee(<?php echo $id_order; ?>)" ><i class="fa fa-save"></i> บันทึกค่าส่ง</button>
                <?php endif; ?>
            <?php else : ?>
                <input type="text" id="deliveryFee" class="form-control input-sm input-mini" style="display:inline; " placeholder="ค่าจัดส่ง" value="<?php echo $fee; ?>" disabled />
                <?php if( ! isPaidOrder($id_order) ) : ?>
                	<?php if( $edit ) : ?>
                <button type="button" id="btn-edit-fee" class="btn btn-sm btn-warning" onClick="editDeliveryFee()" ><i class="fa fa-pencil"></i> แก้ไขค่าจัดส่ง</button>
                <button type="button" id="btn-update-fee" class="btn btn-sm btn-success" style="display:none;" onClick="updateDeliveryFee(<?php echo $id_order; ?>)" ><i class="fa fa-save"></i> บันทึกค่าส่ง</button>
                	<?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
                <button type="button" class="btn btn-sm btn-primary" onClick="getSummary()"><i class="fa fa-list"></i> ข้อมูลสรุป</button>
        <?php endif; ?>
        <?php if($order->current_state == 5 || $order->current_state == 9 || $order->current_state == 10 || $order->current_state == 11) : ?>
        	<button type="button" class="btn btn-info btn-sm" onclick="check_order(<?php echo $id_order; ?>)"><i class="fa fa-search"></i>&nbsp; ตรวจสอบรายการ</button>
        <?php endif; ?>
			<button type="button" class="btn btn-success btn-sm" onclick="print_order(<?php echo $id_order; ?>)"><i class="fa fa-print"></i>&nbsp; พิมพ์</button>
        </p>
	</div>
</div><!-- /row -->
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:5px;' />
<div class="row"><div class="col-sm-12" style="padding-bottom:5px;"><?php echo paymentLabel($id_order); ?><?php echo emsLabel($id_order); ?><?php echo closedLabel($id_order); ?></div></div>
<div class='row'>
    <?php  	$ado = getOnlineAddress($id_order);	?>
    <div class="col-sm-12">
    	<table class='table table-bordered'>
        <thead>
        <tr><td colspan="6" align="center">ที่อยู่สำหรับจัดส่ง  <p class="pull-right top-p"><button type="button" class="btn btn-info btn-xs" onClick="addNewAddress()"> เพิ่มที่อยู่ใหม่</button></p></td></tr>
        <tr style="font-size:12px;">
        	<td align="center" width="10%">ชื่อเรียก</td>
            <td width="12%">ผู้รับ</td>
            <td width="39%">ที่อยู่</td>
            <td width="15%">อีเมล์</td>
            <td width="15%">โทรศัพท์</td>
            <td width="8%"></td>
        </tr>
        </thead>
        <tbody id="adrs">
        <?php if( $ado !== FALSE ) : ?>
        <?php 	while( $rs = dbFetchArray($ado) ) : ?>
        	<tr style="font-size:12px;" id="<?php echo $rs['id_address']; ?>">
            <td align="center"><?php echo $rs['alias']; ?></td>
            <td><?php echo $rs['first_name'] .' '.$rs['last_name']; ?></td>
            <td><?php echo $rs['address1'] .' '. $rs['address2'] .' '. $rs['province'] .' '. $rs['postcode']; ?></td>
            <td><?php echo $rs['email']; ?></td>
            <td><?php echo $rs['phone']; ?></td>
            <td align="right">
            <?php if( $rs['is_default'] == 1 ) : ?>
            <button type="button" class="btn btn-xs btn-success"><i class="fa fa-check"></i></button>
            <?php else : ?>
                <button type="button" class="btn btn-xs btn-default" onClick="setDefault(<?php echo $rs['id_address']; ?>)"><i class="fa fa-check"></i></button>
			<?php endif; ?>
                <button type="button" class="btn btn-xs btn-warning" onClick="editAddress(<?php echo $rs['id_address']; ?>)"><i class="fa fa-pencil"></i></button>
                <button type="button" class="btn btn-xs btn-danger" onClick="removeAddress(<?php echo $rs['id_address']; ?>)"><i class="fa fa-trash"></i></button>
			</td>
            </tr>
        <?php 	endwhile; ?>
        <?php else : ?>
        <tr><td colspan="6" align="center">ไม่พบที่อยู่</td></tr>
        <?php endif; ?>
        </tbody>
        </table>
    </div>


</div><!-- /row-->
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<form id='editOrderForm'>
<div class='row'>
    <div class='col-sm-12'>
<!---------------------------------------------------------------------  Order Table  ----------------------------------------------------------->
<?php $l_discount = bill_discount($id_order); 	?>
<?php 	$orderTxt	= '';							?>
<?php if($order->current_state != 9 && $order->current_state != 8 && $order->valid == 0) : ?>
	<?php if($edit || $add) : ?>
        <button type='button' id='edit_reduction' class='btn btn-default btn-sm' >แก้ไขส่วนลด</button>
        <button type='button' id='save_reduction' class='btn btn-default btn-sm' onclick="verifyPassword()" style="display:none;" >บันทึกส่วนลด</button>
       	<?php if(!$l_discount) : ?>
       		<button type="button" id="btn_add_discount" class="btn btn-default btn-sm" ><i class="fa fa-plus"></i>&nbsp;เพิ่มส่วนลดท้ายบิล</button>
            <button type="button" id="btn_save_discount" class="btn btn-success btn-sm" onclick="add_discount()" style="display:none;"><i class="fa fa-save"></i>&nbsp;บันทึกส่วนลดท้ายบิล</button>
       	<?php endif; ?>
       <p class="pull-right">
		<?php if( $service == 0 ) : ?>
        <input type="text" class="form-control input-sm input-medium inline" name="serCharge" id="serCharge" placeholder="ค่าบริการอื่นๆ" />
        <button type="button" class="btn btn-sm btn-primary" onClick="saveService()">บันทึกค่าบริการ</button>
        <?php else : ?>
       <input type="text" class="form-control input-sm input-medium inline" name="serCharge" id="serCharge" placeholder="ค่าบริการอื่นๆ" value="<?php echo $service; ?>"  disabled />
       <button type="button" class="btn btn-sm btn-warning" onClick="editService()" id="btn-edit-service">แก้ไขค่าบริการ</button>
       <button type="button" class="btn btn-sm btn-success hide" id="btn-update-service" onClick="saveService()">บันทึกค่าบริการ</button>
       <?php endif; ?>
       </p>
	<?php endif; ?>
<?php endif; ?>
	<table id='product_table' class='table table-striped' style='width:100%; padding:10px; border: 1px solid #ccc; margin-top:10px;'>
    <thead>
    	<th style='width:10%; text-align:center;'>รูปภาพ</th>
        <th>สินค้า</th>
        <th style='width:10%; text-align:center;'>ราคา</th>
        <th style='width:12%; text-align:center;'>ส่วนลด</th>
        <th style='width:10%; text-align:center;'>จำนวน</th>
        <th style='width:10%; text-align:center;'>มูลค่า</th>
        <th style='width:5% text-align:center;'></th>
    </thead>
    <tbody id="orderTable">
<?php	$qs = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = ".$id_order);		?>
<?php 	$orderTxt = 'สรุปการสั่งซื้อ<br/> Order No : '.$order->reference.' <br/>';		?>
<?php 	$orderTxt .= '--------------------------------------- <br/>';	?>
<?php	if( dbNumRows($qs) > 0 ) :	?>
<?php	$total_disc = 0;  $total_price = 0; $total_amount = 0; 	?>
<?php		$product = new product();	?>
<?php		while( $rs = dbFetchArray($qs) ) : 	?>
<?php			$id 		= $rs['id_order_detail'];	?>
<?php 			$id_pa 	= $rs['id_product_attribute']; 	?>
<?php 			$disc		= $rs['reduction_percent'] > 0 ? $rs['reduction_percent'] : $rs['reduction_amount']; 	?>
				<tr id="row_<?php echo $id; ?>" style="font-size:12px;">
                    <td style='text-align:center; vertical-align:middle;'><img src="<?php echo $product->get_product_attribute_image($id_pa,1); ?>"  /></td>
                    <td style='vertical-align:middle;'><?php echo $rs['product_reference']." : ".get_product_name($rs['id_product'])." : ".$rs['barcode']; ?></td>
                    <td style='text-align:center; vertical-align:middle;'><span id="price_<?php echo $id; ?>"><?php echo number_format($rs['product_price'], 2); ?></span></td>
                    <td style='text-align:center; vertical-align:middle;'>
                        <p class='reduction'><?php echo discountLabel($rs['reduction_percent'], $rs['reduction_amount']); ?></p>
                       <div class='input_reduction' style='display:none;'>
                       		<input type='text' class='form-control input-sm input-discount' id="reduction<?php echo $id; ?>"
                       				name="reduction[<?php echo $id; ?>]" value='<?php echo $disc; ?>' onKeyUp="verifyDiscount(<?php echo $id; ?>, '<?php echo $rs['product_price']; ?>')" />
                            <select class="form-control input-sm input-unit" id="unit<?php echo $id; ?>" name="unit[<?php echo $id; ?>]" onChange="verifyDiscount(<?php echo $id; ?>, '<?php echo $rs['product_price']; ?>')" >
                                <option value="percent" <?php if( $rs['reduction_percent'] > 0 ) { echo "selected"; } ?> >%</option>
                                <option value="amount" <?php if( $rs['reduction_amount'] > 0 ) { echo "selected"; } ?> >฿</option>
                            </select>
                        </div>
                    </td>
                    <td style='text-align:center; vertical-align:middle;'>	<?php echo number_format($rs['product_qty']); ?></td>
                    <td style='text-align:center; vertical-align:middle;'><?php echo number_format($rs['total_amount'], 2); ?></td>
                    <td style='text-align:center; vertical-align:middle;'>
                    <?php if($edit && ($order->current_state == 3 || $order->current_state == 1 ) ) : ?>
                            <button type="button" class="btn btn-danger btn-xs" onClick="deleteItem(<?php echo $id; ?>, '<?php echo $rs['product_reference']; ?>')"><i class="fa fa-trash"></i></button>
                    <?php endif; ?>
                    </td>
                </tr>
<?php
					$orderTxt .=   $rs['product_reference'].' :  ('.number_format($rs['product_qty']).') x '.number_format($rs['product_price'], 2).' <br/>';
					$total_disc 		+= $rs['discount_amount'];
					$total_price 	+= $rs['product_qty'] * $rs['product_price'];
					$total_amount 	+= $rs['total_amount'];
			endwhile;

?>
<?php if( $l_discount ) : ?>
		<tr id="last_discount_row" >
        	<td colspan="5" style="text-align: right; vertical-align:middle; padding-right:20px;">ส่วนลดท้ายบิล</td>
            <td style='text-align:center; vertical-align:middle;'>
            	<span id="discount_label"><?php echo number_format($l_discount, 2); ?></span>
                <input type="text" id="last_discount" class="form-control input-sm" style="text-align:right; display:none;" value="<?php echo $l_discount; ?>" />
            </td>
            <td style='text-align:center; vertical-align:middle;'>
            <?php if($order->current_state == 3 || $order->current_state == 1 ) : ?>
            	<?php if($edit) : ?>
            	<button type="button" class="btn btn-warning btn-xs" id="btn_edit_discount" onclick="edit_discount()"><i class="fa fa-pencil"></i></button>
                <button type="button" class="btn btn-danger btn-xs" id="btn_delete_discount" onclick="action_delete(<?php echo $id_order.", ".number_format($l_discount, 2); ?>)"><i class="fa fa-trash"></i></button>
                <button type="button" class="btn btn-success btn-xs" id="btn_update_discount" style="display:none;"><i class="fa fa-save"></i>&nbsp; Update</button>
                <?php endif; ?>
            <?php endif; ?>
            </td>
        </tr>
<?php else : ?>
		<tr id="last_discount_row" style="display:none;" >
        	<td colspan="5" align="right" style="padding-right:20px;">ส่วนลดท้ายบิล</td>
            <td><input type="text" id="last_discount" class="form-control input-sm" style="text-align:right" /></td>
            <td>บาท</td>
        </tr>
<?php endif; ?>

		<tr>
			<td rowspan='3' colspan='4'></td>
			<td style='border-left:1px solid #ccc'><b>สินค้า</b></td>
            <td colspan='2' align='right' id="total_price"><b><?php echo number_format($total_price,2); ?> </b></td>
       </tr>
		<tr>
        	<td style='border-left:1px solid #ccc'><b>ส่วนลด</b></td>
        	<td colspan='2' align='right' id="total_disc"><b><?php echo number_format(($total_disc + $l_discount), 2); ?> </b></td>
        </tr>
		<tr>
        	<td style='border-left:1px solid #ccc'><b>สุทธิ </b></td>
        	<td colspan='2' align='right' id="net"><b><?php echo number_format(($total_amount-$l_discount),2); ?> </b></td>
        </tr>
<?php 	$orderTxt .= '--------------------------------------- <br/>';	?>
<?php 	if( ($total_disc + $l_discount) > 0 )
			{
				$orderTxt .= 'ส่วนลดรวม'.getSpace('- '.number_format( ($total_disc + $l_discount), 2), 27).'<br/>';
			 	$orderTxt .= '--------------------------------------- <br/>';
			}
?>
<?php 	if( $fee > 0 ){
				$orderTxt .= 'ค่าจัดส่ง'.getSpace(number_format($fee, 2), 31).'<br/>';
			 	$orderTxt .= '--------------------------------------- <br/>';
			}
?>

<?php 	if( $service > 0 ){
				$orderTxt .= 'อื่นๆ'.getSpace(number_format($service, 2), 36).'<br/>';
			 	$orderTxt .= '--------------------------------------- <br/>';
			}
?>

<?php 	$payAmount = ($total_amount - $l_discount) + $fee + $service; ?>
<?php	$orderTxt .= 'ยอดชำระ' . getSpace(number_format( $payAmount, 2), 29).'<br/>';	?>
<?php 	$orderTxt .= '--------------------------------------- <br/><br/>';	?>

<?php	$qrs = getActiveBank();	?>
<?php	if( dbNumRows($qrs) > 0 ) : ?>
<?php		$orderTxt .= 'สามารถชำระได้ที่ <br/>'; ?>
<?php 	$orderTxt .= '--------------------------------------- <br/>';	?>
<?php		while( $rs = dbFetchArray($qrs) ) : ?>
<?php			$orderTxt .= '- '.$rs['bank_name'].'<br/>'; ?>
<?php			$orderTxt .= '&nbsp;&nbsp;&nbsp;&nbsp;สาขา '.$rs['branch'].'<br/>'; ?>
<?php			$orderTxt .= '&nbsp;&nbsp;&nbsp;&nbsp;ชื่อบัญชี '.$rs['acc_name'].'<br/>'; ?>
<?php			$orderTxt .= '&nbsp;&nbsp;&nbsp;&nbsp;เลขที่บัญชี '.$rs['acc_no'].'<br/>'; 	?>
<?php 	$orderTxt .= '--------------------------------------- <br/>';	?>
<?php		endwhile; 	?>
<?php	endif; ?>
<?php	else :  ?>
		<tr>
            <td colspan='7' align='center'><h4>ไม่มีรายการสินค้า</h4></td>
       	</tr>

<?php endif;  ?>
		</tbody>
   	</table>

<!--------------------------------------------------------------------  End order table  --------------------------------------------------------->
	</div>
</div>
<div class='row'>
	<div class='col-sm-12'>
    	<p>
        <strong>หมายเหตุ :  </strong><?php if($order->comment ==""){ echo"ไม่มีข้อความ";}else{ echo $order->comment; } ?></p>
    </div>
</div>
<h4>&nbsp;</h4>
</form>
<!----------  สรุปยอดส่ง Line --------->
<div class='modal fade' id='orderSummaryTab' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style="width:300px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
            </div>
            <div class='modal-body' id="summaryText">
            		<?php echo $orderTxt; ?>
            </div>
            <div class='modal-footer'>
                <button class="btn btn-sm btn-info btn-block" data-dismiss='modal' data-clipboard-action="copy" data-clipboard-target="#summaryText">Copy</button>
            </div>
        </div>
    </div>
</div>

<div class='modal fade' id='ModalLogin' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width: 350px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site text-center' > รหัสลับผู้มีอำนาจการแก้ไขส่วนลด </h4>
			</div>
			<input type='hidden' id='id_employee' name='id_employee' />
			<div class='modal-body'>
				<div class='form-group login-password'>
					<input name='password' id='password' class='form-control input'  size='20' placeholder='รหัสลับ' type='password' />
				</div>
				<input id='login' class='btn  btn-block btn-lg btn-primary' value='ตกลง' type='button' onclick='checkPassword()' />
			</div>
			<p style='text-align:center; color:red;' id='message'></p>
			<div class='modal-footer'>
			</div>
		</div>
	</div>
</div>
<script>
$('#ModalLogin').on('shown.bs.modal', function () {  $('#password').focus(); });
$("#password").keyup(function(e) { if(e.keyCode == 13 ){ checkPassword(); }});
</script>

<div class='modal fade' id='modal_approve' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width: 350px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site text-center' > รหัสลับผู้มีอำนาจอนุมัติส่วนลด</h4>
			</div>
			<input type='hidden' id='id_approve' name='id_approve'>
			<div class='modal-body'>
				<div class='form-group login-password'>
					<input name='password' id='bill_password' class='form-control input'  size='20' placeholder='รหัสลับ' type='password' />
				</div>
				<input class='btn  btn-block btn-lg btn-primary' value='ตกลง' type='button' onclick='valid_password()' />
			</div>
			<p style='text-align:center; color:red;' id='bill_message'></p>
			<div class='modal-footer'>
			</div>
		</div>
	</div>
</div>
<script>
$('#modal_approve').on('shown.bs.modal', function () {  $('#bill_password').focus(); });
</script>

<div class='modal fade' id='modal_approve_edit' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width: 350px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site text-center' > รหัสลับผู้มีอำนาจอนุมัติส่วนลด</h4>
			</div>
			<div class='modal-body'>
				<div class='form-group login-password'>
					<input name='password' id='edit_bill_password' class='form-control input'  size='20' placeholder='รหัสลับ' type='password' />
				</div>
				<input class='btn  btn-block btn-lg btn-primary' value='ตกลง' type='button' onclick='valid_approve()' >
				<!--userForm-->
			</div>
			<p style='text-align:center; color:red;' id='edit_bill_message'></p>
			<div class='modal-footer'>
			</div>
		</div>
	</div>
</div>
<script>
$('#modal_approve_edit').on('shown.bs.modal', function () {  $('#edit_bill_password').focus(); });
</script>
<!-------------  Add New Address Modal  --------->
<div class='modal fade' id='addressModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style="width:500px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title-site text-center' >เพิ่ม/แก้ไข ที่อยู่สำหรับจัดส่ง</h4>
            </div>
            <div class='modal-body'>
            <form id="addAddressForm"	>
            <input type="hidden" name="id_address" id="id_address" />
            <div class="row">
            	<div class="col-sm-6">
                	<label class="input-label">ชื่อ</label>
                    <input type="text" class="form-control input-sm" name="Fname" id="Fname" placeholder="ชื่อผู้รับ (จำเป็น)" />
                </div>
                <div class="col-sm-6">
                	<label class="input-label">สกุล</label>
                    <input type="text" class="form-control input-sm" name="Lname" id="Lname" placeholder="นามสกุลผู้รับ" />
                </div>
                <div class="col-sm-12">
                	<label class="input-label">ที่อยู่ 1 </label>
                    <input type="text" class="form-control input-sm" name="address1" id="address1" placeholder="เลขที่, หมู่บ้าน, ถนน (จำเป็น)" />
                </div>
                <div class="col-sm-12">
                	<label class="input-label">ที่อยู่ 2 </label>
                    <input type="text" class="form-control input-sm" name="address2" id="address2" placeholder="ตำบล, อำเภอ" />
                </div>
                <div class="col-sm-6">
                	<label class="input-label">จังหวัด</label>
                    <input type="text" class="form-control input-sm" name="province" id="province" placeholder="จังหวัด (จำเป็น)" />
                </div>
                <div class="col-sm-6">
                	<label class="input-label">รหัสไปรษณีย์</label>
                    <input type="text" class="form-control input-sm" name="postcode" id="postcode" placeholder="รหัสไปรษณีย์" />
                </div>
                <div class="col-sm-6">
                	<label class="input-label">เบอร์โทรศัพท์</label>
                    <input type="text" class="form-control input-sm" name="phone" id="phone" placeholder="000 000 0000" />
                </div>
                <div class="col-sm-6">
                	<label class="input-label">อีเมล์</label>
                    <input type="text" class="form-control input-sm" name="email" id="email" placeholder="someone@somesite.com" />
                </div>
                <div class="col-sm-6">
                	<label class="input-label">ชื่อเรียก</label>
                    <input type="text" class="form-control input-sm" name="alias" id="alias" placeholder="ใช้เรียกที่อยู่ เช่น บ้าน, ที่ทำงาน (จำเป็น)" />
                </div>
            </div>
            </form>
            </div>
            <div class='modal-footer'>
                <button type="button" class="btn btn-sm btn-success" onClick="saveAddress()" ><i class="fa fa-save"></i> บันทึก</button>
            </div>
        </div>
    </div>
</div>

<?php $bank = getActiveBank(); ?>
<!-------------  เลือกธนาคารที่แจ้งชำระ  --------->
<div class='modal fade' id='selectBankModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style="width:400px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title-site text-center' >เลือกช่องทางการชำระเงิน</h4>
            </div>
            <div class='modal-body'>
            	<div class="row">
	<?php if( dbNumRows($bank) > 0 ) : ?>
    <?php	while( $rs = dbFetchArray($bank) ) : ?>
    				<div class="col-sm-12" style="padding-top:15px; padding-bottom:15px; border-top:solid 1px #ccc; ">
                    	<table style="width:100%; border:0px;">
                        <tr>
                        	<td width="25%" style="vertical-align:text-top;"><img src="<?php echo bankLogoUrl($rs['bankcode']); ?>" height="50px"/></td>
                            <td>
									<?php echo $rs['bank_name']; ?> สาขา  <?php echo $rs['branch']; ?> <br/>
                                    เลขที่บัญชี <?php echo $rs['acc_no']; ?> <br/>
                                    ชื่อบัญชี  <?php echo $rs['acc_name']; ?> <br/>
                                    <button type="button" class="btn btn-sm btn-primary" style="margin-top:10px;" onClick="payOnThis(<?php echo $rs['id_account']; ?>)">ชำระด้วยช่องทางนี้</button>
							</td>
                        </tr>
                        </table>
                    </div>
	<?php	endwhile; ?>
    <?php endif; ?>
				</div>
            </div>
            <div class='modal-footer'>
            </div>
        </div>
    </div>
</div>

<!-------------  แจ้งชำระเงิน  --------->
<form id="paymentForm" name="paymentForm" enctype="multipart/form-data" method="post">
<input type="hidden" name="id_account" id="id_account"/>
<input type="hidden" name="orderAmount" id="orderAmount" value="<?php echo $payAmount; ?>" />
<input type="file" name="image" id="image" accept="image/*" style="display:none;" />
<div class='modal fade' id='paymentModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style="width:400px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
            </div>
            <div class='modal-body'>
                <div class="col-sm-12" style="padding-bottom:15px; margin-bottom:15px; border-bottom:solid 1px #eee;">
                	<span style="font-size:25px; color:#75ce66;">จำนวน <?php echo number_format($payAmount, 2); ?> ฿ </span>
                </div>
                <div class="col-sm-12" style="padding-bottom:15px; margin-bottom:15px; border-bottom:solid 1px #eee;">
                    <div class="row">
                        <div class="col-sm-3" id="logo" style="padding-top:5px;"></div>
                        <div class="col-sm-9" id="detail"></div>
                    </div>
				</div>
                 <div class="col-sm-12" style="padding-bottom:15px; margin-bottom:15px; border-bottom:solid 1px #eee;">
                	<div class="row">
                    	<div class="col-sm-12" style="margin-bottom:20px;"><span style="font-size:18px; color:#888; font-weight:500">แจ้งหลักฐานการโอนเงิน</span></div>
                    	<div class="col-sm-4 label-left" style="padding-right:15px; padding-top:10px;">
                        	<span style="font-weight:bold; color:#888;">แนบสลิป</span>
                        </div>
                        <div class="col-sm-8">

                        	<button type="button" class="btn btn-block btn-primary" id="btn-select-file" onClick="selectFile()"><i class="fa fa-file-image-o"></i> เลือกรูปภาพ</button>
                            <div id="block-image" style="opacity:0;"><div id="previewImg" ></div><span onClick="removeFile()" style="position:absolute; left:190px; top:0px; cursor:pointer; color:red;"><i class="fa fa-times fa-2x"></i></span></div>
                        </div>
                        <div class="col-sm-4 label-left" style="padding-right:15px; padding-top:10px;">
                        	<span style="font-weight:bold; color:#888;">ยอดเงินที่โอน</span>
                        </div>

                        <div class="col-sm-8 top-col">
                        	<div class="input-group">
                            	<input type="text" class="form-control input-sm input-lagre" name="payAmount" id="payAmount" />
                                <span class="input-group-addon">บาท</span>
                            </div>
                        </div>

                        <div class="col-sm-4 label-left" style="padding-right:15px; padding-top:10px;">
                        	<span style="font-weight:bold; color:#888;">วันที่โอน</span>
                        </div>
                        <div class="col-sm-8 top-col">
                        	<div class="input-group">
                        		<input type="text" class="form-control input-sm" name="payDate" id="payDate" />
                            	<span class="input-group-btn"><button type="button" class="btn btn-sm btn-default" onClick="dateClick()"><i class="fa fa-calendar"></i></button></span>
                            </div>
                        </div>

                        <div class="col-sm-4 label-left" style="padding-right:15px; padding-top:10px;">
                        	<span style="font-weight:bold; color:#888;">เวลา</span>
                        </div>
                        <div class="col-sm-4 top-col">
                        	<select id="payHour" name="payHour" class="form-control input-sm"><?php echo selectHour(); ?></select>
                        </div>
                        <div class="col-sm-4 top-col">
                        	<select id="payMin" name="payMin" class="form-control input-sm"><?php echo selectMin(); ?></select>
                        </div>
                    </div><!--/ row -->
                </div>
            </div>
            <div class='modal-footer'>
            	<button type="button" class="btn btn-sm btn-primary" onClick="submitPayment()" ><i class="fa fa-save"></i> บันทึก</button>
            </div>
        </div>
    </div>
</div>
</form>

<div class='modal fade' id='deliveryModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width: 350px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site' >บันทึกเลขที่การจัดส่ง</h4>
			</div>
			<div class='modal-body'>
				<div class="row">
                	<div class="col-sm-12">
                        <input type="text" class="form-control input-sm" name="emsNo" id="emsNo" placeholder="เลขที่ EMS หรือ เลขที่การจัดส่ง" />
                    </div>
                </div>
			</div>
			<div class='modal-footer'>
            	<button type="button" class="btn btn-sm btn-primary btn-block" onClick="saveDeliveryNo()">บันทึก</button>
			</div>
		</div>
	</div>
</div>


<div class='modal fade' id='paymentDetailModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width:400px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site' >ข้อมูลการชำระเงิน</h4>
			</div>
			<div class='modal-body' id="paymentDetailBody">

			</div>
			<div class='modal-footer'>
            	<button type="button" class="btn btn-sm btn-default" data-dismiss='modal' >Close</button>
			</div>
		</div>
	</div>
</div>
<!---------------------------------------------------------------- จบหน้าแก้ไข -------------------------------------------------->
<?php else : ?>
<!----------------------------------------------------- แสดงรายการ -------------------------------------------------->
<?php
	$s_ref 	= isset( $_POST['s_ref'] ) ? $_POST['s_ref'] : ( getCookie('s_ref') ? getCookie('s_ref') : '');
	$s_cus 	= isset( $_POST['s_cus'] ) ? $_POST['s_cus'] : ( getCookie('s_cus') ? getCookie('s_cus') : '' );
	$s_emp	= isset( $_POST['s_emp'] ) ? $_POST['s_emp'] : ( getCookie('s_emp') ? getCookie('s_emp') : '');
	$from		= isset( $_POST['from_date'] ) ? $_POST['from_date'] : ( getCookie('orderFrom') ? getCookie('orderFrom') : '');
	$to		= isset( $_POST['to_date'] ) ? $_POST['to_date'] : ( getCookie('orderTo') ? getCookie('orderTo') : '');
	//----- เฉพาะฉัน  ------//
	$vt			= isset( $_POST['viewType'] ) ? $_POST['viewType'] : (getCookie('viewType') ? getCookie('viewType') : 0 );
	$me		= $vt == 1 ? 'btn-info' : '';
	if( isset( $_POST['viewType'] ) ){ createCookie('viewType', $vt, 3600*24*60); }

	//-------  เปิดบิลแล้ว  ------//
	$is_closed 	= isset( $_POST['closed'] ) ? $_POST['closed'] : ( getCookie('closed') ? getCookie('closed') : 0 );
	$closed		= $is_closed == 1 ? 'btn-info' : '';
	if( isset( $_POST['closed'] ) ){ createCookie('closed', $is_closed, 3600*24*60); }

	//---------  ยังแจ้งการจัดส่ง ? -------//
	$delivered	= isset( $_POST['delivered'] ) ? $_POST['delivered'] : ( getCookie('delivered') ? getCookie('delivered') : 0 ); //--- ถ้าต้องการกรองเฉพาะตัวที่ยังไม่ได้แจ้งการจัดส่ง --//
	$dv			= $delivered == 1 ? 'btn-info' : '';
	if( isset( $_POST['delivered'] ) ){ createCookie('delivered', $delivered, 3600*24*60); }

	$paginator = new paginator();
	$get_rows = isset( $_POST['get_rows'] ) ? $_POST['get_rows'] : ( getCookie('get_rows') ? getCookie('get_rows') : 50);
?>
<form  method='post' id='searchForm'>
<div class="row">
	<div class="col-sm-2 padding-5" style="padding-left:15px;">
 		<label>เอกสาร</label>
        <input type="text" class="form-control input-sm" id="s_ref" name="s_ref" value="<?php echo $s_ref; ?>" placeholder="พิมพ์เลขที่เอกสาร" />
    </div>
    <div class="col-sm-2 padding-5">
 		<label>ลูกค้า</label>
        <input type="text" class="form-control input-sm" id="s_cus" name="s_cus" value="<?php echo $s_cus; ?>" placeholder="พิมพ์ชื่อลูกค้า" />
    </div>
    <div class="col-sm-2 padding-5">
 		<label>พนักงาน</label>
        <input type="text" class="form-control input-sm" id="s_emp" name="s_emp" value="<?php echo $s_emp; ?>" placeholder="พิมพ์ชื่อพนักงาน" />
    </div>
    <div class="col-sm-2 padding-5">
 		<label style="display:block;">วันที่</label>
        <input type="text" class="form-control input-sm input-discount text-center" id="from_date" name="from_date" value="<?php echo $from; ?>" placeholder="เริ่มต้น" />
        <input type="text" class="form-control input-sm input-unit text-center" id="to_date" name="to_date" value="<?php echo $to; ?>" placeholder="สิ้นสุด" />
    </div>
    <div class="col-sm-1 padding-5">
 		<label style="display:block; visibility:hidden;">&nbsp;</label>
            <button type="button" class="btn btn-sm btn-block <?php echo $me; ?>" id="btn-view-me" onClick="toggleMe()">เฉพาะฉัน</button>
    </div>
    <div class="col-sm-1 padding-5">
 		<label style="display:block; visibility:hidden;">&nbsp;</label>
        <button type="button" id="btn-closed" class="btn btn-sm btn-block <?php echo $closed; ?>" onClick="toggleClosed()">เปิดบิลแล้ว</button>
    </div>
    <div class="col-sm-1 padding-5">
 		<label style="display:block; visibility:hidden;">&nbsp;</label>
         <button type="button" id="btn-delivered" class="btn btn-sm btn-block <?php echo $dv; ?>" onClick="toggleDelivered()">ยังไม่แจ้งจัดส่ง</button>
    </div>
    <div class="col-sm-1 padding-5" style="padding-right:15px;">
 		<label style="display:block; visibility:hidden;">&nbsp;</label>
        <button type="button" class="btn btn-warning btn-sm btn-block" onClick="clearFilter()">Reset</button>
    </div>

</div>
<input type="hidden" name="viewType" id="viewType" value="<?php echo $vt; ?>" /><!-- ไว้กำหนดว่า ดูเฉาะฉันหรือทั้งหมด -->
<input type="hidden" name="closed" id="closed" value="<?php echo $is_closed; ?>" /><!-- กรองเฉพาะตัวที่เปิดบิลแล้วหรือไม่ 0 =ไม่, 1 = เปิดแล้ว -->
<input type="hidden" name="delivered" id="delivered" value="<?php echo $delivered; ?>" /><!-- บันทึกการจัดส่งไปแล้วหรือยัง 0 = ยัง, 1 = แจ้งแล้ว -->
</form>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />
<?php
//--------------------  เงื่อนไข ------------------//
	$where = "JOIN tbl_order_online ON tbl_order.id_order = tbl_order_online.id_order WHERE order_status = 1 AND role = 1 AND payment = 'ออนไลน์' ";
	if( $s_ref != '' OR $s_cus != '' OR $s_emp != '' OR $from != '' )
	{
		if( $s_ref != '' )
		{
			createCookie('s_ref', $s_ref);
			$where .= "AND reference LIKE '%".$s_ref."%' ";
		}
		if( $s_cus != '' )
		{
			createCookie('s_cus', $s_cus);
			$in = onlineOrderByCustomer($s_cus);
			if( $in !== FALSE )
			{
				$where .= "AND tbl_order_online.id_order IN(".$in.") ";
			}
			else
			{
				$where .= "AND id_customer = '' ";
			}
		}
		if( $vt == 0 )
		{
			if( $s_emp != '' )
			{
				createCookie('s_emp', $s_emp);
				$in = employee_in($s_emp);
				if( $in !== FALSE )
				{
					$where .= "AND id_employee IN(".$in.") ";
				}
				else
				{
					$where .= "AND id_employee = '' ";
				}
			}
		}
		if( $from != '' )
		{
			createCookie('orderFrom', $from);
			if( $to != '' ){ createCookie('orderTo', $to); }
			$to	= $to == '' ? toDate($from) : toDate($to);
			$from = fromDate($from);
			$where .= "AND ( date_add BETWEEN '".$from."' AND '".$to."' ) ";
		}
	}
	if( $vt == 1 && getCookie('user_id') !== FALSE )
	{
		$where .= "AND id_employee = ".getCookie('user_id')." ";
	}
	if( $is_closed == 1 )
	{
		$where .= "AND current_state = 9 ";
	}
	if( $delivered == 1 )  //----- ถ้าต้องการกรองเฉพาะรายการที่ยังไม่แจ้งการจัดส่ง ---//
	{
		$where .= "AND delivery_code IS NULL ";
	}
	$where .= "ORDER BY date_add DESC, tbl_order.id_order DESC";
//echo $where;
?>
<div class='row'>
	<div class='col-sm-7'>
	<?php	$paginator->Per_Page("tbl_order",$where,$get_rows);	?>
	<?php	$paginator->display($get_rows,"index.php?content=order_online"); ?>
	</div>
	<div class="col-sm-3" style="margin-top:15px;">
		<input type="text" class="form-control input-sm text-center" id="vProduct" placeholder="ค้นหาสินค้า ตรวจสอบยอดคงเหลือ" />
	</div>
	<div class="col-sm-2" style="margin-top:15px;">
		<button type="button" class="btn btn-primary btn-sm btn-block" onClick="viewProduct()"><i class="fa fa-tags"></i> แสดงสินค้า</button>
	</div>
</div>
<div class="row">
<div class="col-sm-12">
		<table class="table" style="border:solid 1px #ccc;">
            <thead>
                <th style='width:5%; text-align:center;'>ID</th>
                <th style='width:10%;'>เลขที่อ้างอิง</th>
                <th style='width:20%;'>ลูกค้า</th>
                <th style='width:10%;'>พนักงาน</th>
                <th style='width:10%; text-align:center;'>ยอดเงิน</th>
                <th style='width:15%; text-align:center;'>การชำระเงิน</th>
                <th style='width:10%; text-align:center;'>สถานะ</th>
                <th style='width:10%; text-align:center;'>วันที่เพิ่ม</th>
                <th style='width:10%; text-align:center;'>วันที่ปรับปรุง</th>
            </thead>
<?php	$qs = dbQuery("SELECT * FROM tbl_order ".$where." LIMIT ".$paginator->Page_Start." , ".$paginator->Per_Page);		?>
<?php	if( dbNumRows($qs) > 0) :		?>
<?php 		while( $rs = dbFetchArray($qs) ) :			?>
<?php			$id = $rs['id_order'];		?>
<?php			$order = new order($id);		?>
<?php			$online = getCustomerOnlineReference($id); ?>
<?php			$customer_name = customer_name($order->id_customer); ?>
<?php			$customer  = $order->payment != 'ออนไลน์' ? $customer_name : ( $online != '' ? $customer_name.' ( '.$online.' )' : $customer_name );	?>
<?php			$isClosed	= isOrderClosed($id);		?>
<?php			$orderAmount = orderAmount($id) + getDeliveryFee($id) + getServiceFee($id);	?>
<?php			if( $order->valid != 2 ) : ?>
			<?php	if( !$isClosed ) : ?>
			<tr style='color:#FFF; background-color:<?php echo state_color($order->current_state); ?>; font-size:12px;'>
            <?php 	else : ?>
            <tr style='color:#555; background-color:#E6E9ED; font-size:12px;'>
            <?php	endif;	?>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo $id; ?></td>
				<td style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo $order->reference; ?></td>
				<td style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo $customer; ?></td>
				<td style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo employee_name($order->id_employee); ?></td>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo number_format($orderAmount); ?></td>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo $order->payment; ?></td>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo $order->current_state_name; ?></td>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo thaiDate($order->date_add); ?></td>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo thaiDate($order->date_upd); ?></td>
			</tr>
<?php			else : ?>
			<tr style='color:#FFF; background-color:#434A54; font-size:12px;'>
				<td align='center'><?php echo $id; ?></td>
				<td><?php echo $order->reference; ?></td>
				<td><?php echo $customer; ?></td>
				<td><?php echo employee_name($order->id_employee); ?></td>
				<td align='center'><?php echo number_format(orderAmount($id)); ?></td>
				<td align='center'><?php echo $order->payment; ?></td>
				<td align='center'><?php echo $order->current_state_name; ?></td>
				<td align='center'><?php echo thaiDate($order->date_add); ?></td>
				<td align='center'><?php echo thaiDate($order->date_upd); ?></td>
			</tr>

<?php			endif;	?>
<?php	endwhile; ?>
<?php else : ?>
			<tr><td colspan='9' align='center'><h4>ไม่พบรายการตามเงื่อนไขที่กำหนด</h4></td></tr>
<?php endif; ?>
		</table>
<?php 	echo $paginator->display_pages(); ?>
<br/><br/>
<script>  var x = setTimeout(function () { location.reload(); }, 60 * 5000); </script>
	</div><!--/ col-sm-12 -->
</div><!--/ row -->
<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' id='modal'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title' id='modal_title'></h4>
                <center><span style="color: red;">ใน ( ) = ยอดคงเหลือทั้งหมด   ไม่มีวงเล็บ = สั่งได้ทันที</span></center>
            </div>
            <div class='modal-body' id='modal_body'></div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
</div><!--/ Container -->

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

<script id="orderProductTemplate" type="text/x-handlebars-template" >
	{{#each this }}
		{{#if @last}}
			<tr>
				 <td colspan="7" align="right"><h4>จำนวนรวม</h4></td>
				 <td  align="right"><h4>{{ total_qty }}</h4></td>
				 <td align="center"><h4>ชิ้น</h4></td>
			</tr>
		{{else}}
    	<tr style="font-size:12px;">
        	<td align="center" style="vertical-align:middle;">{{ no }}</td>
            <td align="center" style="vertical-align:middle;">{{{ img }}}</td>
            <td align="center" style="vertical-align:middle;">{{ barcode }}</td>
            <td style="vertical-align:middle;">{{ product }}</td>
            <td align="center" style="vertical-align:middle;">{{ price }}</td>
            <td align="center" style="vertical-align:middle;">{{ qty }}</td>
            <td align="center" style="vertical-align:middle;">{{ discount }}</td>
            <td align="center" style="vertical-align:middle;">{{ amount }}</td>
            <td align="right" style="vertical-align:middle;">
            	<button type="button" class="btn btn-danger btn-xs" onClick="deleteRow({{ id }}, '{{ product }}')"><i class="fa fa-trash"></i></button>
            </td>
      	</tr>
		{{/if}}
	{{/each}}
</script>
<script id="addressTemplate" type="text/x-handlebars-template">
<tr style="font-size:12px;" id="{{ id }}">
	<td align="center">{{ alias }}</td>
	<td>{{ name }}</td>
	<td>{{ address }}</td>
	<td>{{ email }}</td>
	<td>{{ phone }}</td>
	<td align="right">
	{{#if default}}
		<button type="button" class="btn btn-xs btn-success"><i class="fa fa-check"></i></button>
	{{else}}
		<button type="button" class="btn btn-xs btn-default" onClick="setDefault({{ id }})"><i class="fa fa-check"></i></button>
	{{/if}}
		<button type="button" class="btn btn-xs btn-warning" onClick="editAddress({{ id }})"><i class="fa fa-pencil"></i></button>
		<button type="button" class="btn btn-xs btn-danger" onClick="removeAddress({{ id }})"><i class="fa fa-trash"></i></button>
	</td>
</tr>
</script>

<script id="addressTableTemplate" type="text/x-handlebars-template">
{{#each this}}
<tr style="font-size:12px;" id="{{ id }}">
	<td align="center">{{ alias }}</td>
	<td>{{ name }}</td>
	<td>{{ address }}</td>
	<td>{{ email }}</td>
	<td>{{ phone }}</td>
	<td align="right">
	{{#if default}}
		<button type="button" class="btn btn-xs btn-success"><i class="fa fa-check"></i></button>
	{{else}}
		<button type="button" class="btn btn-xs btn-default" onClick="setDefault({{ id }})"><i class="fa fa-check"></i></button>
	{{/if}}
		<button type="button" class="btn btn-xs btn-warning" onClick="editAddress({{ id }})"><i class="fa fa-pencil"></i></button>
		<button type="button" class="btn btn-xs btn-danger" onClick="removeAddress({{ id }})"><i class="fa fa-trash"></i></button>
	</td>
</tr>
{{/each}}
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
	<div class="col-sm-4 label-left">เวลาแจ้งชำระ : </div><div class="col-sm-8">{{date_add}}</div>
	{{#if imageUrl}}
		<div class="col-sm-12 top-row top-col text-center"><a href="javascript:void(0)" onClick="viewImage('{{ imageUrl }}')">รูปสลิปแนบ <i class="fa fa-paperclip fa-rotate-90"></i></a> </div>
	{{else}}
		<div class="col-sm-12 top-row top-col text-center">---  ไม่พบไฟล์แนบ  ---</div>
	{{/if}}
</div>
</script>
<script>
<?php $prov = getProvinceArray(); ?>
var Province = [<?php foreach($prov as $province){ echo '"'.$province .'", '; } ?>];
$('#province').autocomplete({
	source: Province,
	autoFocus: true
});
var adx = 0;
$("#btnAdd").click(function(e) {
	console.log(adx);
    adx++;
	if( adx == 1 ){
		$(this).attr('disabled', 'disabled');
		setTimeout(function(){ adx = 0; $("#btnAdd").removeAttr("disabled"); }, 3000);
	 }
});
</script>
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
<script src="script/order_online.js"></script>
