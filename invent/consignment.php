<?php 
	$page_name	= 'ฝากขาย';
	$id_tab 			= 16;
	$id_profile 		= $_COOKIE['profile_id'];
   $pm 				= checkAccess($id_profile, $id_tab);
	$view 			= $pm['view'];
	$add 				= $pm['add'];
	$edit 				= $pm['edit'];
	$delete 			= $pm['delete'];
	accessDeny($view);
	include_once 'function/consignment_helper.php';
	include_once 'function/order_helper.php';
  	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	$role = 5; /// ฝากขาย 
	   ?>
<div class="container">
<!-- page place holder -->
<div class="row top-row">
	<div class="col-xs-6 top-col"><h4 class="title"><i class="fa fa-cloud-upload"></i>&nbsp;<?php echo $page_name; ?></h4>
	</div>
    <div class="col-xs-6">
       	<p class="pull-right top-p">
       	<?php if( ! isset( $_GET['edit'] ) && ! isset( $_GET['add'] ) && ! isset( $_GET['view_detail'] ) && $add ) : ?>
       				<button type="button" class="btn btn-sm btn-success" onClick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>        
       	<?php endif; ?>
       	<?php if( isset( $_GET['add'] ) OR isset( $_GET['edit'] ) OR isset( $_GET['view_detail'] ) ) : ?>
       				<button type="button" class="btn btn-sm btn-warning" onClick="goBack(<?php echo isset( $_GET['add'] ) && isset( $_GET['id_order'] ) ? $_GET['id_order'] : 0 ; ?>)"><i class="fa fa-arrow-left"></i> กลับ</button>
       	<?php endif; ?>
       	<?php if( ( isset( $_GET['add'] ) OR isset( $_GET['edit'] ) ) && isset( $_GET['id_order'] ) && $add ) : ?>
        <?php 	$state =  getCurrentState($_GET['id_order']) ; ?>
        <?php 	if( $state == 1 OR $state == 3 ) : ?>
            		<button type="button" class="btn btn-sm btn-success" onClick="saveOrder(<?php echo $_GET['id_order']; ?>)"><i class="fa fa-save"></i> บันทึก</button>    
		<?php 	endif; ?>                                   
       	<?php endif; ?>
       	<?php if( isset( $_GET['view_detail'] ) && isset( $_GET['id_order'] ) ) : ?>
        <?php 	$c_state = getCurrentState($_GET['id_order']);	?>
        <?php	if( ( $c_state == 1 OR $c_state == 3 ) && $edit ) : ?>
        				<button type="button" class="btn btn-sm btn-warning" onClick="goEdit(<?php echo $_GET['id_order']; ?>)"><i class="fa fa-pencil"></i> แก้ไข</button>
        <?php	endif;		?>
        <?php endif; ?>
       	</p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<!-- End page place holder -->
<?php 
//------------------------------------  Add New Order  -------------------------------//	
if( isset( $_GET['add'] ) ) :
	$id_order	= isset( $_GET['id_order'] ) ? $_GET['id_order'] : '';
	$c_state		= $id_order != '' ? getCurrentState($id_order) : '';
	$order		= $id_order != '' ? new order($id_order) : FALSE;
	$cm			= $id_order != '' ? new consignment($id_order) : FALSE;
	$customer	= $id_order != '' ? new customer($order->id_customer) : FALSE;
	$reference	= $id_order != '' ? $order->reference : get_max_role_reference("PREFIX_CONSIGNMENT", $role);
	$doc_date	= $id_order != '' ? thaiDate($order->date_add) : thaiDate();
	$id_cus		= $id_order != '' ? $order->id_customer : '';
	$cus_name	= $customer != FALSE ? $customer->full_name : '';
	$remark		= $id_order != '' ? $order->comment : '';
	$id_zone		= $cm !== FALSE ? $cm->id_zone : '';
	$zone_name	= $id_zone != '' ? get_zone($id_zone) : '';
	$payment	= 'ฝากขาย';
	$active		= $id_order != '' ? 'disabled' : '';
?>
<?php if( $c_state == 1 OR $c_state == 3 OR $c_state == '' ) : //-- ?>
<!--------------------------------  เพิ่มออเดอร์ ID ใหม่  ------------------------------------->
<form id="addForm">
<div class="row">
	<div class="col-sm-2">
    	<label>เลขที่เอกสาร</label>
        <input type="text" name="reference" id="reference" class="form-control input-sm text-center" value="<?php echo $reference; ?>" disabled />
        <input type="hidden" name="id_order" id="id_order" value="<?php echo $id_order; ?>" />
    </div>
    <div class="col-sm-2">
    	<label>วันที่</label>
        <input type="text" id="doc_date" name="doc_date" class="form-control input-sm text-center" value="<?php echo $doc_date; ?>" <?php echo $active; ?> />
    </div>
    <div class="col-sm-4">
    	<label>ลูกค้า</label>
        <input type="text" id="customer" name="customer" class="form-control input-sm" placeholder="พิมพ์ชื่อลูกค้า" value="<?php echo $cus_name; ?>" <?php echo $active; ?> />
        <input type="hidden" id="id_customer" name="id_customer" value="<?php echo $id_cus; ?>" />
    </div>
    <div class="col-sm-4">
    	<label>โซน</label>
        <input type="text" id="zone" name="zone" class="form-control input-sm" placeholder="เลือกโซนฝากขาย" value="<?php echo $zone_name; ?>" <?php echo $active; ?> />
        <input type="hidden" id="id_zone" name="id_zone" value="<?php echo $id_zone; ?>" />
    </div>
    <div class="col-sm-10 top-col">
    	<label>หมายเหตุ</label>
        <input type="text" id="remark" name="remark" class="form-control input-sm" placeholder="ระบุหมายเหตุ (ถ้ามี)" value="<?php echo $remark; ?>" <?php echo $active; ?> />
    </div>
    <div class="col-sm-1 top-col">
    	<label style="display:block; visibility:hidden">button</label>
    <?php if( $id_order == '' ) : ?>
    	<button type="button" class="btn btn-sm btn-default btn-block" onClick="newOrder()"><i class="fa fa-plus"></i> เพิ่ม</button>
	<?php else : ?>
    	<button type="button" class="btn btn-sm btn-warning btn-block" id="btnEdit" onClick="getEdit()" ><i class="fa fa-pencil"></i> แก้ไข</button>
        <button type="button" class="btn btn-sm btn-success btn-block" id="btnUpdate" onClick="updateOrder(<?php echo $id_order; ?>)" style="display:none;"><i class="fa fa-save"></i> ปรับปรุง</button>
    <?php endif; ?>        
    </div>   
</div>
</form>

<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />

<!--------------------------------------------------- NEW ---------------------------------->
<?php if( isset( $_GET['id_order'] ) ) :  ?>

<div class='row'>
	<div class="col-lg-3">
    	<input type="text" class="form-control input-sm text-center" id="sProduct" placeholder="ค้นหาสินค้า" />
    </div>
    <div class="col-lg-2">
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
<div class='col-lg-12'>
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
<?php endif; //---- end if isset( $_GET['id_order'] ?>
<?php else : //----- else if( $s_state == 1 Or 3 Or '' ?>
<center><h4 style="padding:15px; margin-top:10px; background-color:#ed5565; color:#fff;"><i class="fa fa-exclamation-triangle"></i> ขออภัย ออเดอร์นี้อยู่ในสถานะห้ามแก้ไข </h4></center>
<?php endif; //----- $s_state?>
<!-----------------------------------------------------------จบหน้าเพิ่มออเดอร์ ---------------------------------------------->
<!-------------------------------------------------- END New ------------------------------>

<?php  elseif(isset($_GET['view_detail'])&&isset($_GET['id_order'])) : ?>
<?php 
//*********************************************** แก้ไขออเดอร์ **************************************************************//
	$id_employee = $_COOKIE['user_id'];
	$id_order = $_GET['id_order'];
	list($zone) =  dbFetchArray(dbQuery("SELECT zone_name FROM tbl_order_consignment JOIN tbl_zone ON tbl_order_consignment.id_zone = tbl_zone.id_zone WHERE id_order = ".$id_order));
	$order = new order($id_order);
	$customer = new customer($order->id_customer);
	$sale = new sale($order->id_sale);
	$state = $order->orderState();
	?>	
        <div class='row'>
        	<div class='col-xs-12'><strong><?php echo $order->reference." - ".$customer->full_name." - เข้าโซน : ".$zone."<p class='pull-right'>พนักงาน : &nbsp;".$sale->full_name."</p>"; ?></strong></div>
        </div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'>
		<div class='col-xs-12'>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>วันที่สั่ง : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo date("d-m-Y H:i:s", strtotime($order->date_add)); ?></dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>สินค้า :&nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_product); ?></dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>จำนวน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_qty); ?></dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>ยอดเงิน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'><?php echo number_format($order->total_amount,2); ?></dd> </dt></dl>
        <p class='pull-right'>
        <?php if($order->current_state == 5 || $order->current_state == 9 || $order->current_state == 10 || $order->current_state == 11) : ?>
        	<button type="button" class="btn btn-info" onclick="check_order(<?php echo $id_order; ?>)"><i class="fa fa-search"></i>&nbsp; ตรวจสอบรายการ</button>
        <?php endif; ?>
        
        <button class="btn btn-success" onclick="print_order(<?php echo $id_order; ?>)"><i class="fa fa-print"></i>&nbsp; พิมพ์</button>
      
        </p>
		</div>
        </div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'>
		<div class='col-xs-6'>
        <form id='stateForm' action='controller/consignmentController.php?edit&state_change' method='post'>
        	<input type='hidden' name='id_order' id="id_order" value='<?php echo $order->id_order; ?>' />
            <input type='hidden' name='id_employee' value='<?php echo $id_employee; ?>' />
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'>
        	<tr>
				<td style='width:25%; text-align:right; vertical-align:middle;'>สถานะ :&nbsp; </td>
                <td style='width:40%; padding-right:10px;'>
                <?php if( $edit ) : ?>
                        <select name='order_state' id='order_state' class='form-control input-sm'>
                        	<option value="0">---- สถานะ ----</option>
                            <option value="1">รอการชำระเงิน</option>
                            <option value="3">รอจัดสินค้า</option>
					<?php if( $delete == 1 ) : ?>
                    		<option value="8">ยกเลิก</option>
                    <?php endif; ?>                           
                        </select>
                    <?php endif; ?>
                    </td>
                    <td style='padding-right:10px;'>
                    <?php if( $edit ) : ?>
                    <button class='btn btn-default' type='button' onclick='stateChange()'>เพิ่ม</button>
                    <?php endif; ?>
                    </td>
               </tr>
    <?php $row = dbNumRows($state);
             $i=0;
             if($row>0) :
                while($i<$row) :
                    list($id_order_state, $state_name, $first_name, $last_name, $date_add)=dbFetchArray($state); 
    ?>
                <tr  style='background-color: <?php echo state_color($id_order_state); ?>'>
                    <td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo $state_name; ?></td>
                    <td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo $first_name." ". $last_name; ?></td>
                    <td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo date('d-m-Y H:i:s', strtotime($date_add)); ?></td>
                </tr>
    <?php 		$i++; 	?>
    <?php 	endwhile; ?>			
    <?php else : ?>
            <tr>
                <td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo $order->currentState(); ?></td>
                <td style='padding-top:10px; padding-bottom:10px; text-align:right;'></td>
                <td style='padding-top:10px; padding-bottom:10px; text-align:center;'><?php echo date('d-m-Y H:i:s', strtotime($order->date_upd)); ?></td>
            </tr>
    <?php endif; ?>		
            </table>
        </div>
        </form>
        </div><!--row-->
        
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />	
        
		<form id='editOrderForm'>
		<div class='row'>
       		<div class="col-lg-12">
            <!---------------------------------------------------------------------  Order Table  ----------------------------------------------------------->

<?php if($order->current_state != 9 && $order->current_state != 8 ) : ?>	
	<?php if($edit || $add) : ?>
        <button type='button' id='edit_reduction' class='btn btn-default' >แก้ไขส่วนลด</button>
        <button type='button' id='save_reduction' class='btn btn-default' onclick="verifyPassword()" style="display:none;" >บันทึกส่วนลด</button>
	<?php endif; ?>
<?php endif; ?>
	<table id='product_table' class='table' style='width:100%; padding:10px; border: 1px solid #ccc; margin-top:10px;'>
    <thead>
    	<th style='width:10%; text-align:center;'>รูปภาพ</th>
        <th style='width:40%'>สินค้า</th>
        <th style='width:10%; text-align:center;'>ราคา</th>
        <th style='width:10%; text-align:center;'>ส่วนลด</th>
        <th style='width:10%; text-align:center;'>จำนวน</th>
        <th style='width:10%; text-align:center;'>มูลค่า</th>
        <th style='width:10% text-align:center;'>การกระทำ</th>
    </thead>
    <tbody id="orderTable">
<?php	$qs = dbQuery("SELECT * FROM tbl_order_detail WHERE id_order = ".$id_order);		?>    
<?php	if( dbNumRows($qs) > 0 ) :	?>
<?php	$total_disc = 0;  $total_price = 0; $total_amount = 0; 	?>
<?php		$product = new product();	?>
<?php		while( $rs = dbFetchArray($qs) ) : 	?>
<?php			$id 		= $rs['id_order_detail'];	?>
<?php 			$id_pa 	= $rs['id_product_attribute']; 	?>			
<?php 			$disc		= $rs['reduction_percent'] > 0 ? $rs['reduction_percent'] : $rs['reduction_amount']; 	?>		
				<tr id="row_<?php echo $id; ?>" style="font-size:12px;">
                    <td style='text-align:center; vertical-align:middle;'><img src="<?php echo $product->get_product_attribute_image($id_pa,1); ?>" width="35px" height="35px" /></td>
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
                            <button type="button" class="btn btn-danger btn-sm" onClick="deleteItem(<?php echo $id; ?>, '<?php echo $rs['product_reference']; ?>')"><i class="fa fa-trash"></i></button>
                    <?php endif; ?>
                    </td>
                </tr>
<?php            
					$total_disc 		+= $rs['discount_amount'];
					$total_price 	+= $rs['product_qty'] * $rs['product_price'];
					$total_amount 	+= $rs['total_amount'];
			endwhile;	
?>		   
 
		<tr>
			<td rowspan='3' colspan='4'></td>
			<td style='border-left:1px solid #ccc'><b>สินค้า</b></td>
            <td colspan='2' align='right' id="total_price"><b><?php echo number_format($total_price,2); ?> </b></td>
       </tr>
		<tr>
        	<td style='border-left:1px solid #ccc'><b>ส่วนลด</b></td>
        	<td colspan='2' align='right' id="total_disc"><b><?php echo number_format(($total_disc), 2); ?> </b></td>
        </tr>
		<tr>
        	<td style='border-left:1px solid #ccc'><b>สุทธิ </b></td>
        	<td colspan='2' align='right' id="net"><b><?php echo number_format(($total_amount),2); ?> </b></td>
        </tr>	        
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
        	<div class='col-lg-12'>
            	<p><h4>ข้อความ :  <?php if($order->comment ==""){ echo"ไม่มีข้อความ";}else{ echo $order->comment; } ?></h4></p>
            </div>
         </div>
         <h4></h4>
         </form>
         
<!------------------ Modal Confirm Item edit discount  -------------->
<div class='modal fade' id='ModalLogin' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog ' style='width: 350px;'>
		<div class='modal-content'>
			<div class='modal-header'>
				<button type='button' class='close' data-dismiss='modal' aria-hidden='true'> &times; </button>
				<h4 class='modal-title-site text-center' > รหัสลับผู้มีอำนาจการแก้ไขส่วนลด </h4>
			</div>
			<input type='hidden' id='id_employee' name='id_employee'>
			<div class='modal-body'>
				<div class='form-group login-password'>
					<input name='password' id='password' class='form-control input'  size='20' placeholder='รหัสลับ' type='password' required='required' autofocus="autofocus">
				</div>
				<input id='login' class='btn  btn-block btn-lg btn-primary' value='ตกลง' type='button' onclick='checkPassword()'>
			</div>
			<p style='text-align:center; color:red;' id='message'></p>
			<div class='modal-footer'>
			</div>
		</div>
	</div>
</div>
<!------------------/ End Modal confirm item edit discount  -------------->
<script> 
$('#ModalLogin').on('shown.bs.modal', function () {  $('#password').focus(); }); 
$("#password").keyup(function(e) { if(e.keyCode == 13 ){ checkPassword(); }});
</script>


<!-------------------------------------------------- จบหน้าแก้ไข ---------------------------------------------->
<?php else : ?>
<?php 	//-----------------------------------------  หน้า แสดงรายการ  ----------------------------// ?>
<?php 
	$s_ref 	= isset( $_POST['s_ref'] ) ? $_POST['s_ref'] : ( getCookie('s_ref') ? getCookie('s_ref') : '');
	$s_cus 	= isset( $_POST['s_cus'] ) ? $_POST['s_cus'] : ( getCookie('s_cus') ? getCookie('s_cus') : '' );
	$s_emp	= isset( $_POST['s_emp'] ) ? $_POST['s_emp'] : ( getCookie('s_emp') ? getCookie('s_emp') : '');
	$from		= isset( $_POST['from_date'] ) ? $_POST['from_date'] : ( getCookie('orderFrom') ? getCookie('orderFrom') : '');
	$to		= isset( $_POST['to_date'] ) ? $_POST['to_date'] : ( getCookie('orderTo') ? getCookie('orderTo') : '');
	$paginator = new paginator();
	$get_rows = isset( $_POST['get_rows'] ) ? $_POST['get_rows'] : ( getCookie('get_rows') ? getCookie('get_rows') : 50);	
?>
<form  method='post' id='searchForm'>
<div class="row">
	<div class="col-lg-2 padding-5" style="padding-left:15px;">
 		<label>เอกสาร</label>
        <input type="text" class="form-control input-sm" id="s_ref" name="s_ref" value="<?php echo $s_ref; ?>" placeholder="พิมพ์เลขที่เอกสาร" />
    </div>
    <div class="col-lg-2 padding-5">
 		<label>ลูกค้า</label>
        <input type="text" class="form-control input-sm" id="s_cus" name="s_cus" value="<?php echo $s_cus; ?>" placeholder="พิมพ์ชื่อลูกค้า" />
    </div>
    <div class="col-lg-2 padding-5">
 		<label>พนักงาน</label>
        <input type="text" class="form-control input-sm" id="s_emp" name="s_emp" value="<?php echo $s_emp; ?>" placeholder="พิมพ์ชื่อพนักงาน" />
    </div>
    <div class="col-lg-2 padding-5">
 		<label style="display:block;">วันที่</label>
        <input type="text" class="form-control input-sm input-discount text-center" id="from_date" name="from_date" value="<?php echo $from; ?>" placeholder="เริ่มต้น" />
        <input type="text" class="form-control input-sm input-unit text-center" id="to_date" name="to_date" value="<?php echo $to; ?>" placeholder="สิ้นสุด" />
    </div>
    <div class="col-lg-1 padding-5">
 		<label style="display:block; visibility:hidden;">&nbsp;</label>
        <button type="button" class="btn btn-primary btn-sm btn-block" onClick="getSearch()">ใช้ตัวกรอง</button>
    </div>
    <div class="col-lg-1 padding-5" style="padding-right:15px;">
 		<label style="display:block; visibility:hidden;">&nbsp;</label>
        <button type="button" class="btn btn-warning btn-sm btn-block" onClick="clearFilter()">Reset</button>
    </div>
</div>
</form>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />
<?php
//--------------------  เงื่อนไข ------------------//
	$where = "WHERE order_status = 1 AND role IN(5) ";
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
			$in = customer_in($s_cus);
			if( $in !== FALSE )
			{
				$where .= "AND id_customer IN(".$in.") ";
			}
		}
		if( $s_emp != '' )
		{
			createCookie('s_emp', $s_emp);
			$in = employee_in($s_emp);
			if( $in !== FALSE )
			{
				$where .= "AND id_employee IN(".$in.") ";
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
	$where .= "ORDER BY date_add DESC";		

?>		
<div class='row' id='result'>			
	<div class='col-lg-12' id='search-table'>
<?php    
	$paginator->Per_Page("tbl_order",$where,$get_rows);
	$paginator->display($get_rows,"index.php?content=consignment");
	?>
		<table class='table'>
            <thead style='color:#FFF; background-color:#48CFAD;'>
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
<?php			if( $order->valid != 2 ) :	?>

			<tr style='color:#FFF; background-color:<?php echo state_color($order->current_state); ?>; font-size:12px;'>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo $id; ?></td>
				<td style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo $order->reference; ?></td>
				<td style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo customer_name($order->id_customer); ?></td>
				<td style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo employee_name($order->id_employee); ?></td>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo number_format(orderAmount($id)); ?></td>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo $order->payment; ?></td>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo $order->current_state_name; ?></td>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo thaiDate($order->date_add); ?></td>
				<td align='center' style='cursor:pointer;' onclick="viewOrder(<?php echo $id; ?>)"><?php echo thaiDate($order->date_upd); ?></td>
			</tr>
<?php		else : ?>
			<tr style='color:#FFF; background-color:#434A54; font-size:12px;'>
				<td align='center'><?php echo $id; ?></td>
				<td><?php echo $order->reference; ?></td>
				<td><?php echo customer_name($order->id_customer); ?></td>
				<td><?php echo employee_name($order->id_employee); ?></td>
				<td align='center'><?php echo number_format(orderAmount($id)); ?></td>
				<td align='center'><?php echo $order->payment; ?></td>
				<td align='center'><?php echo $order->current_state_name; ?></td>
				<td align='center'><?php echo thaiDate($order->date_add); ?></td>
				<td align='center'><?php echo thaiDate($order->date_upd); ?></td>
			</tr>            
<?php			endif; ?>                
<?php	endwhile; ?>		
<?php else : ?>
			<tr><td colspan='9' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;ไม่มีรายการในช่วงนี้</h3></td></tr>
<?php endif; ?>		
		</table>
<?php 	echo $paginator->display_pages(); ?>
<br><br>
<script>  var x = setTimeout(function () { location.reload(); }, 60 * 5000); </script>
	</div><!--/ col-lg-12 -->
</div><!--/ row -->
<?php endif; ?>
</div><!--/ Container -->

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
<script>
//-------------------------------------------------  NEW CODE  ------------------------------------//

//----------------------  ลบรายการสินค้าในหน้าเพิ่ม  ------------------//
function deleteRow(id, ref)
{
	swal({
		title: 'ต้องการลบ ?',
		text: 'คุณแน่ใจว่าต้องการลบ '+ref+' ?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: "controller/orderController.php?deleteOrderDetail",
				type:"POST", cache:"false", data:{ "id_order_detail" : id },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title : "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });	
						var id_order = $("#id_order").val();
						reloadOrderProduct(id_order);
					}else{
						swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");	
					}
				}
			});
		});	
}

//------------------------------  ลบรายการสินค้าในหน้า แสดงรายละเอียด  ---------------------//
function deleteItem(id, ref)
{
	swal({
		title: 'ต้องการลบ ?',
		text: 'คุณแน่ใจว่าต้องการลบ '+ref+' ?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url: "controller/orderController.php?deleteOrderDetail",
				type:"POST", cache:"false", data:{ "id_order_detail" : id },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({ title : "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success" });	
						$("#row_"+id).remove();
						recalOrder();
					}else{
						swal("ข้อผิดพลาด!!", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");	
					}
				}
			});
		});	
}

function recalOrder()
{
	var id_order = $("#id_order").val();
	$.ajax({
		url:"controller/orderController.php?getBillSummary",
		type: "POST", cache: "false", data:{ "id_order" : id_order },
		success: function(rs){
			var rs = $.trim(rs);
			var ar = rs.split(' | ');
			var total_price = addCommas(ar[0]);
			var total_disc	= addCommas(ar[1]);
			var net			= addCommas(ar[2]);
			$("#total_price").html("<b>"+total_price+"</b>");
			$("#total_disc").html("<b>"+total_disc+"</b>");
			$("#net").html("<b>"+net+"</b>");
		}
	});
}
function newOrder()
{
	var date		= $("#doc_date").val();
	var id_cus	= $("#id_customer").val();
	var name		= $("#customer").val();
	var id_zone	= $("#id_zone").val();
	var zone		= $("#zone").val();
	var remark	= $("#remark").val();
	if( ! isDate(date) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	if( isNaN(id_cus) || name == '' ){ swal("ข้อผิดพลาด", "ชื่อลูกค้าไม่ถูกต้อง กรุณาเลือกลูกค้าใหม่อีกครั้ง", "error"); return false; }
	if( isNaN(id_zone) || zone == '' ){ swal("ข้อผิดพลาด", "โซนไม่ถูกต้อง กรุณาเลือกโซนใหม่อีกครั้ง", "error"); return false; }
	load_in();
	$.ajax({
		url:"controller/consignmentController.php?addNewOrder",
		type:"POST", cache: "false", data:{ "date" : date, "id_customer" : id_cus, "id_zone" : id_zone, "remark" : remark },
		success: function(rs){
			load_out();
			var rs	= $.trim(rs);
			if( ! isNaN( parseInt(rs) ) ){
				window.location.href = 'index.php?content=consignment&add=y&id_order='+rs;
			}else{
				swal("ข้อผิดพลาด", "เพิ่มออเดอร์ใหม่ไม่สำเร็จกรุณาลองใหม่อีกครั้ง", "error");
			}
		}
	});
}

//-----------  Save Order ------------//
function saveOrder(id)
{
	load_in();
	$.ajax({
		url:"controller/orderController.php?saveOrder",
		type: "POST", cache: "false", data: { "id_order" : id },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title: "เรียบร้อย", text: "บันทึกออเดอร์เรียบร้อยแล้ว", type: "success", timer: 1000 });
				setTimeout( function(){ goBack(id); }, 1500);
			}else{
				swal("ข้อผิดพลาด!!", "บันทึกออเดอร์ไม่สำเร็จ กรุณาลองใหม่อีกครั้งภายหลัง", "error");	
			}
		}
	});
}

function getEdit()
{
	$("#doc_date").removeAttr('disabled');
	$("#customer").removeAttr("disabled");
	$("#zone").removeAttr("disabled");
	$("#remark").removeAttr("disabled");
	$("#btnEdit").css("display", "none");
	$("#btnUpdate").css("display", "");	
}

function updated()
{
	$("#doc_date").attr("disabled", "disabled");
	$("#customer").attr("disabled", "disabled");
	$("#zone").attr("disabled", "disabled");
	$("#remark").attr("disabled", "disabled");
	$("#btnUpdate").css("display", "none");	
	$("#btnEdit").css("display", "");
}

function updateOrder(id_order)
{
	var date		= $("#doc_date").val();
	var id_cus	= $("#id_customer").val();
	var name		= $("#customer").val();
	var id_zone	= $("#id_zone").val();
	var zone		= $("#zone").val();
	var remark	= $("#remark").val();
	if( ! isDate(date) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	if( isNaN(id_cus) || name == '' ){ swal("ข้อผิดพลาด", "ชื่อลูกค้าไม่ถูกต้อง กรุณาเลือกลูกค้าใหม่อีกครั้ง", "error"); return false; }
	if( isNaN(id_zone) || zone == '' ){ swal("ข้อผิดพลาด", "โซนไม่ถูกต้อง กรุณาเลือกโซนใหม่อีกครั้ง", "error"); return false; }
	load_in();
	$.ajax({
		url:"controller/consignmentController.php?updateOrder",
		type:"POST", cache: "false", data:{ "id_order" : id_order, "date" : date, "id_customer" : id_cus, "id_zone" : id_zone, "remark" : remark },
		success: function(rs){
			load_out();
			var rs	= $.trim(rs);
			if( rs == 'success' ){
				swal({ title: 'สำเร็จ', timer: 1000, type: 'success'});
				updated();
			}else{
				swal("ข้อผิดพลาด", "ปรับปรุงข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}
		}
	});
}



function goEdit(id_order)
{
	window.location.href = 'index.php?content=consignment&add&id_order='+id_order;	
}


function addNew()
{
	$.ajax({
		url:"controller/consignmentController.php?checkOrderNotSave",
		type:"GET", cache:"false", success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'ok' ){
				window.location.href = 'index.php?content=consignment&add';
			}else if( !isNaN( parseInt(rs) ) ){
				window.location.href = 'index.php?content=consignment&add&id_order='+rs;
			}
		}
	});
}

function goBack(id)
{
	if( id == 0 )
	{
		window.location.href = 'index.php?content=consignment';	
	}else{
		window.location.href = 'index.php?content=consignment&id_order='+id+'&view_detail=y';	
	}
}

$("#customer").autocomplete({
	source: 'controller/orderController.php?customer_name',
	autoFocus: true,
	close: function(event, ui){
		var rs 	= $(this).val();
		var arr	= rs.split(' | ');
		var id		= arr[2];
		var name	= arr[1];
		$(this).val(name);
		$('#id_customer').val(id);
	}
});

$('#zone').autocomplete({
	source: 'controller/autoComplete.php?consign_zone',
	autoFocus: true,
	close: function(event, ui){
		var rs = $(this).val();
		var arr 	= rs.split(' | ');
		if( arr[0] != 'ไม่พบข้อมูล' ){
			var name	= arr[0];
			var id		= arr[1];
		}else{
			name = '';
			id = '';
		}
		$(this).val(name);
		$('#id_zone').val(id);
	}
});

$('#doc_date').datepicker({ dateFormat: 'dd-mm-yy' });

$("#from_date").datepicker({
	dateFormat: 'dd-mm-yy', 
	onClose: function( selectedDate ) {
      $( "#to_date" ).datepicker( "option", "minDate", selectedDate );
	  if( $(this).val() != '' && $("#to_date").val() == '' ){ $("#to_date").focus(); }
	}
});

$( "#to_date" ).datepicker({
	dateFormat: 'dd-mm-yy',   
	onClose: function( selectedDate ) {
        $( "#from_date" ).datepicker( "option", "maxDate", selectedDate );
      }
});

$("#sProduct").autocomplete({
	source: "controller/autoComplete.php?product_code",
	autoFocus: true		
});


$("#sProduct").keyup(function(e){
	if(e.keyCode == 13 ){
		if( $(this).val() != '' ){
			getProduct();
		}
	}
});


//---------------------------  เพิ่มการสั่งซื้อสินค้า  -----------------------//
function addToOrder(id_order)
{
	$("#order_grid").modal("hide");
	load_in();
	$.ajax({
		url:"controller/orderController.php?addToOrder",
		type: "POST", cache:"false", data: $("#gridForm").serialize(),
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			var arr = rs.split(' | ');
			var c		= arr.length;
			if( c == 1 && arr[0] == 'success' ){
				swal({ title: "สำเร็จ", text: "เพิ่มรายการเรียบร้อยแล้ว", type: "success", timer: 1000 });
				reloadOrderProduct(id_order);
			}else if( c == 2 && arr[0] == 'fail'){
				swal("ข้อผิดพลาด!!", "เพิ่มสินค้าในรายการไม่สำเร็จ กรุณาตรวจสอบ แล้วลองใหม่อีกครั้ง", "error");
				$("#order_grid").modal("show");
			}else if( c == 2 && arr[0] == 'overstock' ){
				swal("ข้อผิดพลาด!!", arr[1]+" มีสินค้าคงเหลือไม่เพียงพอ กรุณาตรวจสอบแล้วสั่งใหม่อีกครั้ง", "error");
				$("#order_grid").modal("show");
			}
		}
	});
}

//---------------------------  โหลดตารางรายการสั่งสินค้าใหม่  --------------------------//
function reloadOrderProduct(id_order)
{
	$.ajax({
		url:"controller/orderController.php?getOrderProductTable"	,
		type:"POST", cache: "false", data:{ "id_order" : id_order },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != 'fail' && rs != '' )
			{
				var source = $("#orderProductTemplate").html();
				var data 		= $.parseJSON(rs);
				var output	= $("#orderProductTable");
				render(source, data, output);
			}
		}
	});
}


//--------------------------------  โหลดรายการสินค้าสำหรับจิ้มสั่งสินค้า  -----------------------------//
function getCategory(id)
{
	var output = $("#cat-"+id);
	if( output.html() == '')
	{
		load_in();
		$.ajax({
			url:"controller/orderController.php?getCategoryProductGrid",
			type:"POST", cache:"false", data:{ "id_category" : id },
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( rs != 'no_product' ){
					output.html(rs);
				}else{
					swal("ไม่พบข้อมูล", "ไม่พบข้อมูลสินค้าในหมวดหมู่ที่เลือก", "warning");		
				}
			}
		});
	}
}


//----------------------------  โหลดตารางใส่จำนวนสั่งซื้อของสินค้า  --------------------//
function getProduct()
{
	var st 		= $("#sProduct").val();
	var id_cus	= $("#id_customer").val();
	
	if( st == '' ){ swal("กรุณาระบุรหัสสินค้า"); return false; }
	
	load_in();
	$.ajax({
		url:"controller/orderController.php?getProductGrid",
		type:"POST", cache: "false", data:{ "product_code" : st, "id_customer" : id_cus },
		success: function(dataset){
			load_out();
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
				swal("ไม่พบสินค้า", "รหัสสินค้าไม่ถูกต้อง หรือ ไม่มีสินค้านี้ในระบบ กรุณาตรวจสอบ", "error");
			}		
		}
	});		
}

//----------------------------  โหลดตารางใส่จำนวนสั่งซื้อของสินค้า  --------------------//
function getData(id_product){
	var id_cus = $("#id_customer").val();
	$.ajax({
		url:"controller/orderController.php?getData&id_product="+id_product+"&id_customer="+id_cus,
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
				$("#order_grid").modal('show');
			}else{
				swal("ไม่พบข้อมูล");
			}		
		}
	});
}

//-------------------  ตรวจสอบรหัสยืนยันการแก้ไขส่วนลดรายการ  ---------------------//
function checkPassword(){
	$("#loader").css("z-index","1100");
	$("#ModalLogin").modal("hide");
	load_in();
	var password = $("#password").val();
	if( password == "" ){ return false; }
	$.ajax({
		url:"controller/orderController.php?check_password&password="+password,
		type:"GET", cache:false, 
		success: function(data){
			load_out();
			if(data == "0"){
				$("#message").html("รหัสลับไม่ถูกต้องกรุณาตรวจสอบ");
				$("#password").val("");
				$("#ModalLogin").modal("show");
			}else{
				updateDiscount(data);
			}
		}
	});
}


//----------------------------- บันทึกส่วนลดรายการ -----------------------//
function updateDiscount( id_emp )
{
	var id_order = $("#id_order").val();
	$.ajax({
		url: "controller/orderController.php?updateDiscount&id_order="+id_order+"&id_approve="+id_emp,
		type:"POST", cache:"false", data: $("#editOrderForm").serialize(),
		success: function(rs){
			window.location.reload();
		}
	});
}

//-----------------  ตรวจสอบการแก้ไขส่วนลดรายการ  ---------------------//
function verifyDiscount(id, price)
{
	var inp	= $("#reduction"+id);
	var disc 	= parseFloat(inp.val());
	var unt	= $("#unit"+id).val();
	var price	= parseFloat(price);
	if( inp.val() != '' && isNaN(disc) ){ swal("กรุณาใช้เฉพาะตัวเลขเท่านั้น"); inp.val(0); }
	if( unt == 'percent' && disc > 100 ){ swal("ส่วนลดต้องไม่เกิน 100%"); inp.val(0); }
	if( unt == 'percent' && disc < 0 ){ swal("ส่วนลดต้องไม่น้อยกว่า 0%"); inp.val(0);}
	if( unt == 'amount' && disc > price ){ swal("ส่วนลดต้องไม่เกินราคาขาย"); inp.val(0);}
	if( unt == 'amount' && disc < 0 ){ swal("ส่วนลดต้องไม่ติดลบ"); inp.val(0); }		
}


//------------------  ยืนยันรหัสการแก้ไขส่วนลดรายการ  ---------------------------//
function verifyPassword()
{
	$("#ModalLogin").modal('show');
}
//----------------------------------------------  END NEW CODE  ---------------------------------//
function stateChange()
{
	var st = $("#order_state").val();
	if( 	st != '' )
	{
		$("#stateForm").submit();	
	}
}


$("#edit_reduction").click(function(e) {
    $(".reduction").css("display","none");
	$(".input_reduction").css("display","block");
	$("#edit_reduction").css("display", "none");
	$("#save_reduction").css("display","");
});

function viewOrder(id)
{
	window.location.href = 'index.php?content=consignment&edit&view_detail&id_order='+id;	
}

$("#s_ref, #s_cus, #s_emp").keyup(function(e) {
    if( e.keyCode == 13 )
	{
		getSearch();
	}
});


function getSearch()
{
	$("#searchForm").submit();
}

function clearFilter()
{
	$.ajax({
		url: "controller/orderController.php?clearFilter",
		success: function(rs){
			goBack(0);
		}
	});
}

function check_order(id)
{
	var wid = $(document).width();
	var left = (wid - 1100) /2;
	window.open("index.php?content=order_check&id_order="+id+"&view_detail=y&nomenu", "_blank", "width=1100, height=800, left="+left+", location=no, scrollbars=yes");	
}

function print_order(id)
{
	var wid = $(document).width();
	var left = (wid - 900) /2;
	window.open("controller/orderController.php?print_order&id_order="+id, "_blank", "width=900, height=1000, left="+left+", location=no, scrollbars=yes");	
}

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