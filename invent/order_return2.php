<?php 
	$page_name = "คืนสินค้าจากการขาย(ลดหนี้ อ้างอิงครั้งเดียว)";
	$id_tab = 40;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
	
  	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }	
	$btn = "";
	if(isset($_GET['edit']) && isset($_GET['id_return_order']) ) :
		$btn .= "<a href='index.php?content=order_return2' style='text-decoration:none;'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		$btn .= "<button class='btn btn-success' onclick='save_edit()' style='margin-left:10px;'><i class='fa fa-save'></i>&nbsp; บันทึก</button>";
	elseif( isset($_GET['add']) && isset($_GET['id_return_order'])) :
		$btn .= "<a href='index.php?content=order_return2' style='text-decoration:none;'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		$btn .= "<button class='btn btn-success' onclick='save_add()' style='margin-left:10px;'><i class='fa fa-save'></i>&nbsp; บันทึก</button>";
	elseif( isset($_GET['add']) ) :
		$btn .= "<a href='index.php?content=order_return2' style='text-decoration:none;'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";	
	elseif( isset($_GET['view_detail']) ) :
		$btn .= "<a href='index.php?content=order_return2' style='text-decoration:none;'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
	else :
		$btn .= can_do($add, "<a href='index.php?content=order_return2&add=y'><button type='button' class='btn btn-success'><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button></a>");
	endif;
	
	?>
  
<div class="container">
<div class="row">
	<div class="col-xs-6">
    	<h4 class="title"><span class="glyphicon glyphicon-import"></span>&nbsp;<?php echo $page_name; ?></h4>
	</div>
    <div class="col-xs-6">
       <p class="pull-right" style="margin:0px;">
       	<?php echo $btn; ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:10px;' />
<?php if( isset($_GET['add']) ) : ?>
	<?php if(isset($_GET['id_return_order'])) : 
				$id_return_order 	= $_GET['id_return_order'];
				$rs					= new return_order($id_return_order);
				$reference 			= $rs->reference;
				$order_reference = $rs->order_reference;
				$id_customer		= $rs->id_customer;
				$customer_name 	= customer_name($id_customer);
				$date_add			= $rs->date_add;
				$remark				= $rs->remark;
				$active				= "disabled";
			else : 
				$id_return_order	= '';
				$reference			= get_max_return_reference();
				$order_reference	= '';
				$id_customer		= '';
				$customer_name	= '';
				$date_add			= date("Y-m-d");
				$remark				= '';
				$active				= '';
			endif;	
	?>
<!------------------------------------------------  เพิ่มเอกสารใหม่  --------------------------------->
		<!-----------------  หัวเอกสาร  ------------------->
<form id="add_form" action="controller/returnController.php?add=y&role=2" method="post">
<div class="row">
	<div class="col-lg-3 col-md-2 col-sm-4">
    	<div class="input-group">
    		<span class="input-group-addon">เลขที่เอกสาร</span>
        	<span class="form-control" style="text-align:center"><?php echo $reference; ?></span>
        </div>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-4">
    	<div class="input-group">
    		<span class="input-group-addon">วันที่</span>
        	<input type="text" class="form-control" style="text-align:center" id="date_add" name="date_add" value="<?php echo thaiDate($date_add); ?>" <?php echo $active; ?> />
        </div>    
    </div>
    <div class="col-lg-3 col-md-3 col-sm-4">
    	<div class="input-group">
    		<span class="input-group-addon">ลูกค้า</span>
        	<input type="text" class="form-control" style="text-align:center" id="customer" name="customer" value="<?php echo $customer_name; ?>" placeholder="ระบุลูกค้าที่คืนสินค้า" autofocus="autofocus" <?php echo $active; ?> />
       	</div>     
        <input type="hidden" name="id_customer" id="id_customer" value="<?php echo $id_customer; ?>"  />
    </div>
    <div class="col-lg-3 col-md-3 col-sm-4">
    	<div class="input-group">
    		<span class="input-group-addon">เอกสารอ้างอิง</span>
        	<input type="text" class="form-control" style="text-align:center" id="order_reference" name="order_reference" value="<?php echo $order_reference; ?>" placeholder="อ้างอิงเลขที่เอกสารขาย" <?php echo $active; ?> />
        </div>
    </div>
    <div class="col-lg-12">&nbsp; <!--- Divider ---></div>
    <div class="col-lg-10 col-md-10 col-sm-8">
    	<div class="input-group">
    		<span class="input-group-addon">หมายเหตุ</span>
        	<input type="text" class="form-control" name="remark" id="remark" value="<?php echo $remark; ?>" placeholder="ระบุหมายเหตุ(ถ้ามี)" <?php echo $active; ?> />
        </div>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-4">
        <?php if(!$active) : ?>
    	<button type="button" class="btn btn-primary btn-block" id="btn_add" onclick="add()"><i class="fa fa-plus"></i>&nbsp; เพิ่มใหม่</button>
        <?php	else : ?>
        <button type="button" class="btn btn-warning btn-block" id="btn_edit" onclick="edit()"><i class="fa fa-pencil"></i>&nbsp; แก้ไข</button>
        <button type="button" class="btn btn-success btn-block" id="btn_update" onclick="update(<?php echo $id_return_order; ?>)" style="display:none;"><i class="fa fa-save"></i>&nbsp; อัพเดต</button>
        <?php endif; ?>
    </div>
    
</div>
</form>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
		<!-----------------  จบหัวเอกสาร  ------------------->
        <!----------------  ส่วนยิงรับเข้า  -------------------->
	<?php if( isset($_GET['id_return_order']) ) : ?> 
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-3">
    	<div class="input-group">
    		<span class="input-group-addon">บาร์โค้ดโซน</span>
        	<input type="text" name="barcode_zone" id="barcode_zone" value="" class="form-control" placeholder="ยิงบาร์โค้โซนเพื่อรับเข้า" autofocus="autofocus" />
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3">
    	<div class="input-group">
    		<span class="input-group-addon">ชื่อโซน</span>
        	<input type="text" name="zone_name" id="zone_name" value="" class="form-control" placeholder="ระบุชื่อโซนเพื่อรับเข้า"  />
        </div>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2">
    	<div class="input-group">
    		<span class="input-group-addon">จำนวน</span>
        	<input type="text" class="form-control" name="qty" id="qty" value="1" style="text-align:center" />
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3">
    	<div class="input-group">
    		<span class="input-group-addon">บาร์โค้ดสินค้า</span>
	        <input type="text" name="barcode_item" id="barcode_item" class="form-control" placeholder="ยิงบาร์โค้ดสินค้าเพื่อรับเข้า" />
        </div>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1">
        <button type="button" id="btn_add_item" class="btn btn-default btn-block" onclick="add_item()"><i class="fa fa-plus"></i>&nbsp; Add</button>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4"><i class="fa fa-check" style="color:green;"></i>&nbsp; = บันทึกแล้ว <i class="fa fa-remove" style="color:red; margin-left:15px;"></i>&nbsp; = ยังไม่บันทึก</div>
    <div class="col-lg-4 col-md-4 col-sm-4">
    	<center><span style="font-size:18px; padding:10px;">รวม</span><span id="total_qty" style="font-size:18px; padding:10px;">0</span><span style="font-size:18px; padding:10px;">หน่วย</span></center>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-4">
    	<button type="button" class="btn btn-primary btn-block" onclick="sum_item()"><i class="fa fa-plus-square"></i>&nbsp; รวมยอด</button>
    </div>
	<div class="col-lg-2 col-md-2 col-sm-4">
        <button type="button" id="zone_btn" onclick="change_zone()" class="btn btn-info btn-block"><i class="fa fa-retweet"></i>&nbsp; เปลี่ยนโซน (F2)</button>
        <input type="hidden" name="id_zone" id="id_zone" value="" />
        <input type="hidden" name="id_return_order" id="id_return_order" value="<?php echo $_GET['id_return_order']; ?>"  />
    </div>
</div>    
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
		<!----------------  จบส่วนยิงรับเข้า  -------------------->
        <!----------------  แสดงรายการรับเข้า  -------------------->
<form id="add_detail">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
	<table class="table table-striped">
    <thead  style="font-size:12px;">
    	<th style="width:10%;">วันที่</th>
        <th>สินค้า</th>
        <th style="width:10%; text-align:center">เลขที่อ้างอิง</th>
        <th style="width:5%; text-align:right">ราคา</th>
        <th style="width:8%; text-align:right">ส่วนลด(%)</th>
        <th style="width:8%; text-align:right">ส่วนลด(บาท)</th>
        <th style="width:5%; text-align:right">จำนวน</th>
        <th style="width:8%; text-align:right">มูลค่า</th>
        <th style="width:10%; text-align:center">โซน</th>
        <th style="width:5%; text-align:center">สถานะ</th>
        <th style="width:5%; text-align:right;"></th>
    </thead>
    <tbody id="data">
<?php $qs = dbQuery("SELECT * FROM tbl_return_order_detail WHERE id_return_order = ".$id_return_order); ?>
<?php	$rw = dbNumRows($qs); ?>
<?php	if( $rw > 0 ) : 
			while( $rs = dbFetchArray($qs) ) : 
				$product = new product();
				$id_product		= $product->getProductId($rs['id_product_attribute']);
				$product_name = $product->product_reference($rs['id_product_attribute'])." : ".$product->product_name($id_product);
				$ro	= new return_order();
				$id = $rs['id_return_order_detail'];
?>
		<tr id="row<?php echo $rs['id_return_order_detail']; ?>" style="font-size:12px;">
        	<td><?php echo thaiDate($rs['date_add']); ?><input type="hidden" class="id_detail" name="id_detail[<?php echo $id; ?>]" value="<?php echo $id; ?>"  /></td>
            <td id="product<?php echo $id; ?>"><?php echo $product_name; ?></td>
            <td align="center">
            <?php	echo $rs['order_reference']; ?>
            </td>
           	<td align="right"><?php echo number_format($rs['product_price'],2); ?><input type="hidden" id="price_<?php echo $id; ?>" value="<?php echo $rs['product_price']; ?>" /></td>
            <td align="right">
    <input type="text" class="form-control input-sm" id="reduction_percent_<?php echo $id; ?>" name="reduction_percent[<?php echo $id; ?>]" value="<?php echo $rs['reduction_percent']; ?>" onblur="update_percent(<?php echo $id; ?>)" />
            </td>
            <td align="right">
     <input type="text" class="form-control input-sm" id="reduction_amount_<?php echo $id; ?>" name="reduction_amount[<?php echo $id; ?>]" value="<?php echo $rs['reduction_amount']; ?>" onblur="update_amount(<?php echo $id; ?>)"  />
            </td>
            <td align="right"><?php echo number_format($rs['qty']); ?><input type="hidden" class='qty' id="qty<?php echo $id;?>" value="<?php echo $rs['qty']; ?>"/></td>
            <td align="right"><span id="total_amount_<?php echo $id; ?>"><?php echo $rs['total_amount']; ?></span></td>
            <td id="zone<?php echo $rs['id_return_order_detail']; ?>" align="center"><?php echo get_zone($rs['id_zone']); ?></td>
            <td align="center"><?php echo isActived($rs['status']); ?></td>
            <td align="right"><button type="button" class="btn btn-danger btn-xs btn-block" onclick="delete_row(<?php echo $id; ?>)"><i class="fa fa-trash"></i></button></td>
        </tr>
<?php  	endwhile;	?>
<?php endif; ?>
	</tbody>
</table>

</div>
</div>
</form>
	<?php endif; ?>
		<!----------------  จบแสดงรายการรับเข้า  -------------------->
<?php elseif( isset($_GET['edit']) && isset($_GET['id_return_order']) ) : ?>     
	<?php $id_return_order	= $_GET['id_return_order']; ?>
	<?php $sr = dbQuery("SELECT * FROM tbl_return_order WHERE id_return_order = ".$id_return_order); ?>
    <?php if(dbNumRows($sr) == 1 ) : ?>
    <?php 	$rs = dbFetchArray($sr); ?>
    <?php	$id_customer	= $rs['id_customer']; ?>
		<!-----------------  หัวเอกสาร  ------------------->
<div class="row">
	<div class="col-lg-3 col-md-2 col-sm-4">
    	<div class="input-group">
    		<span class="input-group-addon">เลขที่เอกสาร</span>
        	<span class="form-control" style="text-align:center"><?php echo $rs['reference']; ?></span>
        </div>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-4">
    	<div class="input-group">
    		<span class="input-group-addon">วันที่</span>
        	<input type="text" class="form-control" style="text-align:center" id="date_add" name="date_add" value="<?php echo thaiDate($rs['date_add']); ?>" disabled />
        </div>    
    </div>
    <div class="col-lg-3 col-md-3 col-sm-4">
    	<div class="input-group">
    		<span class="input-group-addon">ลูกค้า</span>
        	<input type="text" class="form-control" style="text-align:center" id="customer" name="customer" value="<?php echo customer_name($rs['id_customer']); ?>" placeholder="ระบุลูกค้าที่คืนสินค้า" autofocus="autofocus" disabled />
       	</div>     
        <input type="hidden" name="id_customer" id="id_customer" value="<?php echo $rs['id_customer']; ?>"  />
    </div>
    <div class="col-lg-3 col-md-3 col-sm-4">
    	<div class="input-group">
    		<span class="input-group-addon">เอกสารอ้างอิง</span>
        	<input type="text" class="form-control" style="text-align:center" id="order_reference" name="order_reference" value="<?php echo $rs['order_reference']; ?>" placeholder="อ้างอิงเลขที่เอกสารขาย" disabled />
        </div>
    </div>
    <div class="col-lg-12">&nbsp; <!--- Divider ---></div>
    <div class="col-lg-10 col-md-10 col-sm-8">
    	<div class="input-group">
    		<span class="input-group-addon">หมายเหตุ</span>
        	<input type="text" class="form-control" name="remark" id="remark" value="<?php echo $rs['remark']; ?>" placeholder="ระบุหมายเหตุ(ถ้ามี)" disabled />
        </div>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-4">
    <?php if($edit) : ?>
        <button type="button" class="btn btn-warning btn-block" id="btn_edit" onclick="edit()"><i class="fa fa-pencil"></i>&nbsp; แก้ไข</button>
        <button type="button" class="btn btn-success btn-block" id="btn_update" onclick="update(<?php echo $id_return_order; ?>)" style="display:none; margin:0px;"><i class="fa fa-save"></i>&nbsp; อัพเดต</button>
    <?php endif; ?>
    </div>
    
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
		<!-----------------  จบหัวเอกสาร  ------------------->
        <!----------------  ส่วนยิงรับเข้า  --------------------> 
<div class="row">
    <div class="col-lg-3 col-md-3 col-sm-3">
    	<div class="input-group">
    		<span class="input-group-addon">บาร์โค้ดโซน</span>
        	<input type="text" name="barcode_zone" id="barcode_zone" value="" class="form-control" placeholder="ยิงบาร์โค้โซนเพื่อรับเข้า" autofocus="autofocus" />
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3">
    	<div class="input-group">
    		<span class="input-group-addon">ชื่อโซน</span>
        	<input type="text" name="zone_name" id="zone_name" value="" class="form-control" placeholder="ระบุชื่อโซนเพื่อรับเข้า"  />
        </div>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2">
    	<div class="input-group">
    		<span class="input-group-addon">จำนวน</span>
        	<input type="text" class="form-control" name="qty" id="qty" value="1" style="text-align:center" />
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3">
    	<div class="input-group">
    		<span class="input-group-addon">บาร์โค้ดสินค้า</span>
	        <input type="text" name="barcode_item" id="barcode_item" class="form-control" placeholder="ยิงบาร์โค้ดสินค้าเพื่อรับเข้า" />
        </div>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1">
    <?php if($edit) : ?>
        <button type="button" id="btn_add_item" class="btn btn-default btn-block" onclick="add_item()"><i class="fa fa-plus"></i>&nbsp; Add</button>
	<?php endif; ?>        
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4"><i class="fa fa-check" style="color:green;"></i>&nbsp; = บันทึกแล้ว <i class="fa fa-remove" style="color:red; margin-left:15px;"></i>&nbsp; = ยังไม่บันทึก</div>
    <div class="col-lg-4 col-md-4 col-sm-4">
    	<center><span style="font-size:18px; padding:10px;">รวม</span><span id="total_qty" style="font-size:18px; padding:10px;">0</span><span style="font-size:18px; padding:10px;">หน่วย</span></center>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-4">
    	<button type="button" class="btn btn-primary btn-block" onclick="sum_item()"><i class="fa fa-plus-square"></i>&nbsp; รวมยอด</button>
    </div>
	<div class="col-lg-2 col-md-2 col-sm-4">
        <button type="button" id="zone_btn" onclick="change_zone()" class="btn btn-info btn-block"><i class="fa fa-retweet"></i>&nbsp; เปลี่ยนโซน (F2)</button>
        <input type="hidden" name="id_zone" id="id_zone" value="" />
        <input type="hidden" name="id_return_order" id="id_return_order" value="<?php echo $_GET['id_return_order']; ?>"  />
    </div>
</div>    
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
		<!----------------  จบส่วนยิงรับเข้า  -------------------->
        <!----------------  แสดงรายการรับเข้า  -------------------->
<form id="add_detail">        
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
	<table class="table table-striped">
   <thead  style="font-size:12px;">
    	<th style="width:8%;">วันที่</th>
        <th>สินค้า</th>
        <th style="width:10%; text-align:center">เลขที่อ้างอิง</th>
        <th style="width:7%; text-align:right">ราคา</th>
        <th style="width:8%; text-align:center">ส่วนลด(%)</th>
        <th style="width:8%; text-align:center">ส่วนลด(บาท)</th>
        <th style="width:5%; text-align:right">จำนวน</th>
        <th style="width:8%; text-align:right">มูลค่า</th>
        <th style="width:10%; text-align:center">โซน</th>
        <th style="width:5%; text-align:center">สถานะ</th>
        <th style="width:5%; text-align:right;"></th>
    </thead>
    <tbody id="data">
<?php $qs = dbQuery("SELECT * FROM tbl_return_order_detail WHERE id_return_order = ".$id_return_order); ?>
<?php	$rw = dbNumRows($qs); ?>
<?php	if( $rw > 0 ) : 
			while( $rs = dbFetchArray($qs) ) : 
				$product = new product();
				$id_product		= $product->getProductId($rs['id_product_attribute']);
				$product_name = $product->product_reference($rs['id_product_attribute'])." : ".$product->product_name($id_product);
				$ro	= new return_order();
				$id = $rs['id_return_order_detail'];
?>
		<tr id="row<?php echo $rs['id_return_order_detail']; ?>" style="font-size:12px;">
        	<td><?php echo thaiDate($rs['date_add']); ?><input type="hidden" class="id_detail" name="id_detail[<?php echo $id; ?>]" value="<?php echo $id; ?>"  /></td>
            <td id="product<?php echo $id; ?>"><?php echo $product_name; ?></td>
            <td align="center">
            <?php echo $rs['order_reference']; ?>
            </td>
            <td align="right"><?php echo number_format($rs['product_price'],2); ?><input type="hidden" id="price_<?php echo $id; ?>" value="<?php echo $rs['product_price']; ?>" /></td>
            <td align="right">
    <input type="text" class="form-control input-sm" id="reduction_percent_<?php echo $id; ?>" name="reduction_percent[<?php echo $id; ?>]" value="<?php echo $rs['reduction_percent']; ?>" onblur="update_percent(<?php echo $id; ?>)" />
            </td>
            <td align="right">
     <input type="text" class="form-control input-sm" id="reduction_amount_<?php echo $id; ?>" name="reduction_amount[<?php echo $id; ?>]" value="<?php echo $rs['reduction_amount']; ?>" onblur="update_amount(<?php echo $id; ?>)"  />
            </td>
            <td align="right"><?php echo number_format($rs['qty']); ?><input type="hidden" class='qty' id="qty<?php echo $id;?>" value="<?php echo $rs['qty']; ?>"/></td>
            <td align="right"><span id="total_amount_<?php echo $id; ?>"><?php echo $rs['total_amount']; ?></span></td>
            <td id="zone<?php echo $id; ?>" align="center"><?php echo get_zone($rs['id_zone']); ?></td>
            <td align="center"><?php echo isActived($rs['status']); ?></td>
            <td align="right"><?php if($edit) : ?><button type="button" class="btn btn-danger btn-xs btn-block" onclick="delete_row(<?php echo $id; ?>)"><i class="fa fa-trash"></i></button><?php endif; ?></td>
        </tr>
<?php  	endwhile;	?>

<?php endif; ?>
	</tbody>
</table>

</div>
</div>
</form>
	<?php else : ?>
<div class="row"><div class="col-lg-12"><center><h3> ไม่พบข้อมูลของรายการที่กำหนด หรือ เอกสารไม่มีอยู่จริง </h3></center></div></div>
    <?php endif; ?>
		<!----------------  จบแสดงรายการรับเข้า  -------------------->
<?php elseif( isset($_GET['view_detail']) ) : ?>
	<?php if( !isset($_GET['id_return_order']) ) : ?>
    	<div class="row"><div class="col-lg-12"><center><h3> ไม่พบข้อมูลของรายการที่กำหนด หรือ เอกสารไม่มีอยู่จริง </h3></center></div></div>
    <?php else : ?>
		<?php $id_return_order	= $_GET['id_return_order']; ?>
        <?php $sr = dbQuery("SELECT * FROM tbl_return_order WHERE id_return_order = ".$id_return_order); ?>
        <?php if(dbNumRows($sr) == 1 ) : ?>
        <?php 	$rs = dbFetchArray($sr); ?>
		<!-----------------  หัวเอกสาร  ------------------->
<div class="row">
	<div class="col-lg-3 col-md-2 col-sm-4">
    	<span class="form-control" style="border:0px;">
    	<label style="padding-right:10px;">เลขที่เอกสาร</label>
        	<span ><?php echo $rs['reference']; ?></span>
        </span>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-4">
    	<span class="form-control" style="border:0px;">
        	<label style="padding-right:10px;">วันที่</label>
            <span><?php echo thaiDate($rs['date_add']); ?></span>
       </span>  
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4">
    	<span class="form-control" style="border:0px;">
    		<label style="padding-right:10px;">ลูกค้า</label>
        	<span><?php echo customer_name($rs['id_customer']); ?></span>
       	</span>   
    </div>
    <div class="col-lg-3 col-md-3 col-sm-4">
    	<span class="form-control" style="border:0px;">
    		<label style="padding-right:10px;">เอกสารอ้างอิง</label>
        	<span><?php echo $rs['order_reference']; ?></span>
        </span>
    </div>
    <div class="col-lg-12">&nbsp; <!--- Divider ---></div>
    <div class="col-lg-12 col-md-12 col-sm-12">
    	<span style="border:0px;">
    		<label style="padding-right:10px;">หมายเหตุ</label>
        	<span><?php echo $rs['remark']; ?></span>
        </span>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
   <div class="col-lg-6 col-md-6 col-sm-6"><strong>สถานะ</strong>&nbsp; : &nbsp;<i class="fa fa-check" style="color:green;"></i>&nbsp; = บันทึกแล้ว <i class="fa fa-remove" style="color:red; margin-left:15px;"></i>&nbsp; = ยังไม่บันทึก</div>
   <div class="col-lg-6 col-md-6 col-sm-6">
   	<p class="pull-right" style="margin:0px;">
	<?php if( $rs['status'] ) : ?>
    <button type="button" class="btn btn-success" onclick="print_return(<?php echo $id_return_order; ?>)"><i class="fa fa-print"></i>&nbsp; พิมพ์</button>
    <button type="button" class="btn btn-info" onclick="printReturnWithBarcode(<?php echo $id_return_order; ?>)"><i class="fa fa-print"></i>&nbsp; พิมพ์แบบมีบาร์โค้ด</button>
	<?php endif; ?>
    </p>
   </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
		<!-----------------  จบหัวเอกสาร  ------------------->

        <!----------------  แสดงรายการรับเข้า  -------------------->
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
	<table class="table table-striped">
    <thead>
    	<th style="width:5%;">ลำดับ</th>
        <th>สินค้า</th>
        <th style="width:10%; text-align:center">เลขที่อ้างอิง</th>
        <th style="width:8%; text-align:center">ราคา</th>
        <th style="width:8%; text-align:center">ส่วนลด</th>
        <th style="width:8%; text-align:right">จำนวน</th>
        <th style="width:8%; text-align:right">มูลค่า</th>
        <th style="width:15%; text-align:center">โซน</th>
        <th style="width:5%; text-align:center">สถานะ</th>
    </thead>
    <tbody id="data">
<?php $qs = dbQuery("SELECT * FROM tbl_return_order_detail WHERE id_return_order = ".$id_return_order); ?>
<?php	$rw = dbNumRows($qs); ?>
<?php	if( $rw > 0 ) : 
			$n = 1;
			$total_qty = 0;
			$total_amount = 0;
			while( $rs = dbFetchArray($qs) ) : 
				$product = new product();
				$id_product		= $product->getProductId($rs['id_product_attribute']);
				$product_name = $product->product_reference($rs['id_product_attribute'])." : ".$product->product_name($id_product);
				if($rs['reduction_percent'] > 0 )
				{
					$discount = $rs['reduction_percent'];
					$unit 			= " %";
				}else if($rs['reduction_amount'] > 0 ){
					$discount 	= $rs['reduction_amount'];
					$unit			= " ฿";
				}else{
					$discount 	= 0.00;
					$unit			= "";
				}
?>
		<tr style="font-size:12px;">
        	<td align="center"><?php echo $n; ?></td>
            <td ><?php echo $product_name; ?></td>
            <td align="center"><?php echo $rs['order_reference']; ?></td>
            <td align="center"><?php echo number_format($rs['product_price']); ?></td>
            <td align="center"><?php echo number_format($discount, 2); ?><?php echo $unit; ?></td>
            <td align="right"><?php echo number_format($rs['qty']); ?></td>
            <td align="right"><?php echo number_format($rs['final_price']*$rs['qty'],2); ?></td>
            <td align="center"><?php echo get_zone($rs['id_zone']); ?></td>
            <td align="center"><?php echo isActived($rs['status']); ?></td>
        </tr>
        <?php $n++; $total_qty += $rs['qty']; $total_amount += $rs['final_price']*$rs['qty']; ?>
<?php  	endwhile;	?>
		<tr>
        	<td colspan="5" align="right"><span style="margin-right:10px;"><strong>รวม</strong></span></td>
            <td align="right"><strong><?php echo number_format($total_qty); ?></strong></td>
            <td align="right"><strong><?php echo number_format($total_amount, 2); ?></strong></td>
            <td colspan="2"></td>
        </tr>
<?php endif; ?>
	</tbody>
</table>
</div>
</div>
    	<?php endif; ?>
    <?php endif; ?>
<?php else : ?>
	<!------------------  แสดงรายการหน้าแรก   ------------------------->
<?php
	if( isset($_POST['from_date']) && $_POST['from_date'] !=""){ setcookie("return_from_date", date("Y-m-d", strtotime($_POST['from_date'])), time() + 3600, "/"); }
	if( isset($_POST['to_date']) && $_POST['to_date'] != ""){ setcookie("return_to_date",  date("Y-m-d", strtotime($_POST['to_date'])), time() + 3600, "/"); }
	$paginator = new paginator();
?>	

<form  method='post' id='form'>
<div class='row'>
	<div class='col-lg-2 col-md-2 col-sm-3 col-sx-3'>
			<label>เงื่อนไข</label>
			<select class='form-control' name='filter' id='filter'>
				<option value='customer' <?php if( isset($_POST['filter']) && $_POST['filter'] =="customer"){ echo "selected"; }else if( isset($_COOKIE['return_filter']) && $_COOKIE['return_filter'] == "customer"){ echo "selected"; } ?> >ลูกค้า</option>
				<option value='order_reference' <?php if( isset($_POST['filter']) && $_POST['filter'] =="order_reference"){ echo "selected"; }else if( isset($_COOKIE['return_filter']) && $_COOKIE['return_filter'] == "order_reference"){ echo "selected"; } ?>>ใบกำกับภาษี</option>
				<option value='reference'<?php if( isset($_POST['filter']) && $_POST['filter'] =="reference"){ echo "selected"; }else if( isset($_COOKIE['return_filter']) && $_COOKIE['return_filter'] == "reference"){ echo "selected"; } ?>>เลขที่เอกสาร</option>
			</select>
		
	</div>	
	<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'>
    	<label>คำค้น</label>
        <?php 
			$value = '' ; 
			if(isset($_POST['search-text'])) : 
				$value = $_POST['search-text']; 
			elseif(isset($_COOKIE['return_search-text'])) : 
				$value = $_COOKIE['return_search-text']; 
			endif; 
		?>
		<input class='form-control' type='text' name='search-text' id='search-text' placeholder="ระบุคำที่ต้องการค้นหา" value='<?php echo $value; ?>' />	
	</div>	
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
		<label>จากวันที่</label>
            <?php 
				$value = ""; 
				if(isset($_POST['from_date']) && $_POST['from_date'] != "") : 
					$value = date("d-m-Y", strtotime($_POST['from_date'])); 
				elseif( isset($_COOKIE['return_from_date'])) : 
					$value = date("d-m-Y", strtotime($_COOKIE['return_from_date'])); 
				endif; 
				?>
			<input type='text' class='form-control' name='from_date' id='from_date' placeholder="ระบุวันที่" style="text-align:center;"  value='<?php echo $value; ?>'/>
	</div>	
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
		<label>ถึงวันที่</label>
            <?php
				$value = "";
				if( isset($_POST['to_date']) && $_POST['to_date'] != "" ) :
				 	$value = date("d-m-Y", strtotime($_POST['to_date'])); 
				 elseif( isset($_COOKIE['return_to_date']) ) :
					$value = date("d-m-Y", strtotime($_COOKIE['return_to_date']));
				 endif;
			?>  
			<input type='test' class='form-control'  name='to_date' id='to_date' placeholder="ระบุวันที่" style="text-align:center" value='<?php echo $value; ?>' />
	</div>
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
    	<label style="visibility:hidden">show</label>
		<button class='btn btn-primary btn-block' id='search-btn' type='button' onclick="search_text()"><i class="fa fa-search"></i>&nbsp;ค้นหา</button>
	</div>	
	<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
    	<label style="visibility:hidden">show</label>
		<button type='button' class='btn btn-danger' onclick="clear_filter()"><i class='fa fa-refresh'></i>&nbsp;reset</button>
	</div>
</div>
</form>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />
<?php

		if(isset($_POST['from_date']) && $_POST['from_date'] != ""){$from = date('Y-m-d',strtotime($_POST['from_date'])); }else if( isset($_COOKIE['return_from_date'])){ $from = date('Y-m-d',strtotime($_COOKIE['return_from_date'])); }else{ $from = "";} 
		if(isset($_POST['to_date']) && $_POST['to_date'] != ""){ $to =date('Y-m-d',strtotime($_POST['to_date']));  }else if(  isset($_COOKIE['order_to_date'])){  $to =date('Y-m-d',strtotime($_COOKIE['order_to_date'])); }else{ $to = "";}
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		
		/****  เงื่อนไขการแสดงผล *****/
		if(isset($_POST['search-text'])/* && $_POST['search-text'] !="" */) :
			$text = $_POST['search-text'];
			$filter = $_POST['filter'];
			setcookie("return_search-text", $text, time() + 3600, "/");
			setcookie("return_filter",$filter, time() +3600,"/");
		elseif(isset($_COOKIE['return_search-text']) && isset($_COOKIE['return_filter'])) :
			$text = $_COOKIE['return_search-text'];
			$filter = $_COOKIE['return_filter'];
		else : 
			$text	= "";
			$filter	= "";
		endif;
		$where = "WHERE role = 1 ";
		if( $text != "" ) :
			switch( $filter) :
				case "customer" :
				$in_cause = "";
				$qs = dbQuery("SELECT id_customer FROM tbl_customer WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%' GROUP BY id_customer");
				$rs = dbNumRows($qs);
				$i=0;
				if($rs>0) :
					while($i<$rs) :
						list($in) = dbFetchArray($qs);
						$in_cause .="$in";
						$i++;
						if($i<$rs){ $in_cause .=","; 	}
					endwhile;
					$where .= "AND id_customer IN($in_cause)";
					else :
						$where .= "AND id_customer = 0";
					endif;
				break;
				case "order_reference" :
					$where .= "AND order_reference LIKE'%$text%'";
				break;
				case "reference" :
				$where .= "AND reference LIKE'%$text%'";
				break;
			endswitch;
			if($from != "" && $to != "" ) : 
				$where .= " AND (date_add BETWEEN '".$from."' AND '".$to."')";  
			endif;
		else :
			$where .= " AND id_return_order != 0";
			if($from != "" && $to != "" ) : 
				$where .= " AND (date_add BETWEEN '".$from."' AND '".$to."')";  
			endif;	
		endif;
		$where .= " ORDER BY date_add DESC";
		
?>		

<?php
$paginator = new paginator();
if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		$paginator->Per_Page("tbl_return_order",$where,$get_rows);
		$paginator->display($get_rows,"index.php?content=return_order2");
		$Page_Start = $paginator->Page_Start;
		$Per_Page = $paginator->Per_Page;
?>	
<div class="row">
	<div class="col-lg-12">
    <table class="table table-striped">
    <thead>
    	<th style="width:5%;">ID</th>
        <th style="width:12%;">เลขที่เอกสาร</th>
        <th style="width:12%;">ใบกำกับภาษี</th>
        <th style="width:25%;">ลูกค้า</th>
        <th style="width:10%; text-align:center">จำนวน</th>
        <th style="width:10%;">วันที่</th>
        <th style="width:10%;">สถานะ</th>
        <th style="text-align:right">การกระทำ</th> 
    </thead>
    <tbody>
	<?php $sql = dbQuery("SELECT * FROM tbl_return_order ".$where." LIMIT ".$Page_Start.", ".$Per_Page); ?>
    <?php if(dbNumRows($sql) > 0 ) : ?>
    	<?php $ro	= new return_order(); ?>
    	<?php while($rs = dbFetchArray($sql) ) : ?>
        <?php 	$status = $rs['status']; ?>
    	<tr id="row_<?php echo $rs['id_return_order']; ?>" style="font-size: 12px;">
        	<td><?php echo $rs['id_return_order']; ?></td>
            <td><?php echo $rs['reference']; ?></td>
            <td><?php echo $rs['order_reference']; ?></td>
            <td><?php echo customer_name($rs['id_customer']); ?></td>
            <td align="center"><?php echo number_format($ro->total_return($rs['id_return_order'])); ?></td>
            <td><?php echo thaiDate($rs['date_add']); ?></td>
            <td>
				<?php if($status == 1 ) : ?>
                	<span style="color:#6C0;"><strong>บันทึกแล้ว</strong></span>
               	<?php else : ?>
                	<span style="color:#F00"><strong>ยังไม่บันทึก</strong></span>
               	<?php endif; ?>
            </td>
            <td align="right">
            	<a href="index.php?content=order_return2&view_detail&id_return_order=<?php echo $rs['id_return_order']; ?>"><button type="button" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></button></a>
            <?php if($edit) : ?>
                <a href='index.php?content=order_return2&edit=y&id_return_order=<?php echo $rs['id_return_order']; ?>'><button type='button' class='btn btn-warning btn-sm'><i class='fa fa-pencil'></i></button></a>
			<?php endif; ?>                    
            <?php if($delete) : ?>
                <button type="button" class="btn btn-danger btn-sm" onclick="delete_return(<?php echo $rs['id_return_order']; ?>, '<?php echo $rs['reference']; ?>')"><i class="fa fa-trash"></i></button>
            <?php endif;?>
        </tr>
    	<?php endwhile; ?>
    <?php else : ?>    
    	<tr><td colspan="8" ><center><h4>---------- ไม่มีรายการ  ----------</h4></center></td></tr>
    <?php endif; ?>   
    </tbody>
    </table>
    </div>
</div>

<?php endif; ?>   
        
        
        

</div><!-- Container -->
<script id='template' type="text/x-handlebars-template">
		<tr style="font-size:12px;">
			<td>{{ date_add }}</td>
			<td>{{ product }}</td>
			<td>{{ order_reference }}</td>
			<td align="right">{{ price }}</td>
			<td align="right"></td>
			<td align="right"></td>
			<td align="right">{{ qty }}</td>
			<td align="right"></td>
			<td align="center">{{ zone }}</td>
			<td align="center"><i class="fa fa-remove" style="color:red"></i></td>
			<td align="right"></td>
		</tr>
</script>


<script id='sum_item' type="text/x-handlebars-template">
	{{#each this}}
		<tr id="row{{id}}" style="font-size:12px;">
			<td>{{ date_add }} <input type="hidden" class="id_detail" name="id_detail[{{id}}]" value="{{id}}" /></td>
			<td id="product{{id}}">{{ product }}</td>
			<td align="center">{{{ list }}}</td>
			<td align="right">{{ price }} <input type="hidden" id="price_{{id}}" value="{{price}}" /></td>
			
			<td align="right"><input type="text" class="form-control input-sm" id="reduction_percent_{{id}}" name="reduction_percent[{{id}}]" value="{{percent}}" onblur="update_percent({{id}})" /></td>
			<td align="right"><input type="text" class="form-control input-sm" id="reduction_amount_{{id}}" name="reduction_amount[{{id}}]" value="{{amount}}" onblur="update_amount({{id}})" /></td>
			<td align="right">{{ qty }} <input type="hidden" class="qty" id="qty{{id}}" value="{{qty}}" /></td>
			<td align="right"><span id="total_amount_{{id}}">{{ total_amount }}</span></td>
			<td id="zone{{id}}" align="center">{{ zone }}</td>
			<td align="center">{{{ status }}}</td>
			<td align="right">
			<?php if(isset($_GET['add']) && $add || isset($_GET['edit']) && $edit ) : ?>
			<button type="button" class="btn btn-danger btn-xs btn-block" onclick="delete_row({{id}})"><i class="fa fa-trash"></i></button></td>
			<?php endif; ?>
		</tr>
	{{/each}}
</script>
<script>
$(document).ready(function(e) {
	var qty = 0;
    $(".qty").each(function(index, element) {
        n = parseInt($(this).val());
		qty += n;
    });
	$("#total_qty").html(qty);
});
$(document).ready(function(e) {
    checkCookie();
});
var today = new Date();
$("#date_add").datepicker({ dateFormat: 'dd-mm-yy', maxDate : today });


function total_qty()
{
		var qty = 0;
    $(".qty").each(function(index, element) {
        n = parseInt($(this).val());
		qty += n;
    });
	$("#total_qty").html(qty);	
}

function add()
{
	load_in();
	var order_reference 	= $("#order_reference").val();
	var id_customer		= $("#id_customer").val();
	if( order_reference != "" && id_customer != "" )
	{
		
					$.ajax({
						url:"controller/returnController.php?add&role=1",
						type:"POST", cache:false,
						data: $("#add_form").serialize(),
						success: function(res)
						{
							if($.trim(res) != "fail")
							{
								window.location.href="index.php?content=order_return2&add=y&id_return_order="+res;
								load_out();
							}else{
								load_out();
								swal("ไม่สามารถเพิ่มเอกสารใหม่ได้ กรุณาลองใหม่อีกครั้ง");
							}
						}
					});
	}else{
		load_out();
		swal("ยังไม่ได้เลือกลูกค้า หรือ ยังไม่ได้ระบุเอกสารอ้างอิง");
	}
}

function edit()
{
	$("#date_add").removeAttr("disabled");
	$("#customer").removeAttr("disabled");
	$("#order_reference").removeAttr("disabled");
	$("#remark").removeAttr("disabled");
	$("#btn_edit").css("display","none");
	$("#btn_update").css("display","");	
}

function update(id)
{
	load_in();
	var date_add = $("#date_add").val();
	var id_customer = $("#id_customer").val();
	var order_reference = $("#order_reference").val();
	var remark	= $("#remark").val();
	$.ajax({
		url:"controller/returnController.php?update2", type:"POST", cache:false,
		data:{ "id_return_order" : id, "date_add" : date_add, "id_customer" : id_customer, "order_reference" : order_reference,  "remark" : remark },
		success: function(ra)
		{
			if(ra == "success")
			{
				$("#dae_add").attr("disabled", "disabled");
				$("#customer").attr("disabled", "disabled");
				$("#order_reference").attr("disabled", "disabled");
				$("#remark").attr("disabled", "disabled");
				$("#btn_update").css("display","none");
				$("#btn_edit").css("display","");
				swal({ title: "เรียบร้อย", text: "ปรับปรุงข้อมูลเรียบร้อยแล้ว", timer: 1000, type: "success"});
				sum_item();
			}else{
				load_out();
				swal("ไม่สำเร็จ", "ปรับปรุงข้อมูลไม่สำเร็จ ลองใหม่อีกครั้ง", "error");
			}
		}
	});
}

function add_item()
{
	load_in();
	var id_return_order 	= $("#id_return_order").val();
	var order_reference	= $("#order_reference").val();
	var id_zone 			= $("#id_zone").val();
	var barcode 			= $("#barcode_item").val();
	var qty					= $("#qty").val();
	var date_add			= $("#date_add").val();
	$("#barcode_item").val('');
	if( id_zone == "")
	{
		load_out();
		swal("คุณยังไม่ได้ระบุโซน");
	}else if(barcode == ""){
		load_out();
		swal("คุณยังไม่ได้ระบุบาร์โค้ดสินค้า");
	}else{
		$.ajax({
			url: "controller/returnController.php?add_item2",type:"POST", cache:false,
			data: {"id_return_order" : id_return_order, "order_reference" : order_reference, "id_zone" : id_zone, "barcode" : barcode, "qty" : qty, "date_add" : date_add },
			success: function(data)
			{
				if(data == "fail" )
				{
					load_out();
					swal("รับคืนไม่สำเร็จ");
				}else{
					var source = $("#template").html();
					var data = $.parseJSON(data);
					var row	= Handlebars.compile(source);
					var html	= row(data);
					//console.log(html);
					$("#data").prepend(html);
					total_qty();
					load_out();
					$("#barcode_item").val('');
					$("#qty").val(1);
					$("#barcode_item").focus();
				}
			}
		});
	}
}

function sum_item()
{
	load_in();
	var id_return_order 	= $("#id_return_order").val();
	var id_customer = $("#id_customer").val();
	$.ajax({
		url: "controller/returnController.php?sum_item2&id_return_order="+id_return_order+"&id_customer="+id_customer, type:"POST", cache:false,
		success: function(data)
		{
			if(data == "fail" )
			{
				load_out();
				swal("ไม่สามารถรวมยอดรายการได้");
			}else{
				var source = $("#sum_item").html();
				var data = $.parseJSON(data);
				var output = $("#data");
				render(source, data, output);
				total_qty();
				load_out();
				$("#barcode_item").focus();
			}
		}
	});
}

function delete_row(id)
{	
	var confirm_text = "ใช่";
	var cancle_text = "ไม่ใช่";
	var product = $("#product"+id).html();
	var zone = $("#zone"+id).html();
	var row = $("#row"+id);
	swal({
	  title: "คุณแน่ใจว่าต้องการลบ",
	  text: product+" โซน "+zone,
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: confirm_text,
	  cancelButtonText: cancle_text,
	  closeOnConfirm: true},
	  function(isConfirm){
	  if (isConfirm) {
		  load_in();
		$.ajax({
			url:"controller/returnController.php?delete_row&id_return_order_detail="+id, type:"GET", cache:false,
			success: function(rs)
			{
				if(rs == "success")
				{
					row.remove();
					total_qty();
					load_out();
				}else{
					load_out();
					swal("ลบรายการไม่สำเร็จ");
				}
			}
		});
	  } 
	});
}

$(document).bind("keyup", function(e){ 
	if(e.keyCode == 113){
		$("#zone_btn").click(); 
	}
});

function change_zone()
{
	$("#id_zone").val('');
	$("#barcode_zone").val('');
	$("#barcode_zone").removeAttr("disabled");
	$("#zone_name").val("");
	$("#zone_name").removeAttr("disabled");
	$("#barcode_zone").focus();
}

$("#customer").autocomplete({
	source: "controller/autoComplete.php?get_customer_id",
	autoFocus: true,
	close: function(event, ui){
		var rs = $(this).val();
		var arr = rs.split(" : ");
		var id		= arr[0];
		var name = arr[1];
		$("#id_customer").val(id);
		$("#customer").val(name);
	}
});

$("#zone_name").autocomplete({
	source : "controller/autoComplete.php?get_zone_name",
	autoFocus: true,
	close: function()
	{
		var rs = $(this).val();
		var arr = rs.split(" : ");
		var id = arr[0];
		var name = arr[1];
		$("#id_zone").val(id);
		$("#zone_name").val(name);
		$("#barcode_zone").attr("disabled", "disabled");
		$("#zone_name").attr("disabled", "disabled");
		$("#barcode_item").focus();
	}
});

$("#barcode_zone").keyup(function(e){
	if(e.keyCode == 13){ 
		var barcode = $(this).val();
		if(barcode != "")
		{
			load_in();
			$.ajax({
				url: "controller/returnController.php?get_zone",
				type:"POST", cache:false, data: {"barcode" : barcode},
				success: function(rs)
				{
					if(rs == "fail")
					{
						load_out();
						swal("บาร์โค้ดไม่ถูกต้อง");
					}else{
						arr = rs.split(" : ");
						id = arr[0];
						name = arr[1];
						$("#id_zone").val(id);
						$("#zone_name").val(name);
						$("#barcode_zone").attr("disabled", "disabled");
						$("#zone_name").attr("disabled", "disabled");
						load_out();
						$("#barcode_item").focus();
					}
				}
			});
		}else{
			$("#zone_name").focus();	
		}
	}
});
$("#barcode_item").keyup(function(e) {
    if(e.keyCode == 13 ){ 
		var barcode = $(this).val();
		if(barcode != ""){
			$("#btn_add_item").click();
		}
	}
});

function save_add()
{
	load_in();
	var id_return_order = $("#id_return_order").val();
	$.ajax({
		url:"controller/returnController.php?save_add2&id_return_order="+id_return_order,
		type:"POST", cache:false, data: $("#add_detail").serialize(),
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs == "success" )
			{
				load_out();
				swal({title : "สำเร็จ", text : "บันทึกรายการเรียบร้อยแล้ว", timer : 2000, type : "success"});
				window.location.href="index.php?content=order_return2";
			}else{
				load_out();
				swal("ไม่สำเร็จ !!", "บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}
		}
	});
}

function save_edit()
{
	if( !check_discount() ){
		load_in();
		var id_return_order = $("#id_return_order").val();
		$.ajax({
			url:"controller/returnController.php?save_edit2&id_return_order="+id_return_order,
			type:"POST", cache:false, data: $("#add_detail").serialize(),
			success: function(rs)
			{
				var rs = $.trim(rs);
				if(rs == "success" )
				{
					load_out();
					swal({title : "สำเร็จ", text : "บันทึกรายการเรียบร้อยแล้ว", timer : 2000, type : "success"});
					window.location.href="index.php?content=order_return2";
					}else{
					load_out();
					swal("ไม่สำเร็จ !!", "บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
				}
			}
		});
	}else{
		swal({ title: "Error !", text: "มีการให้ส่วนลด ทั้ง 2 ช่อง", type: "warning"});
		return false;
	}
}

$("#from_date").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(selectedDate){
		$("#to_date").datepicker("option", "minDate", selectedDate);
	}
});

$("#to_date").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(selectedDate){
		$("#from_date").datepicker("option", "maxDate", selectedDate);
	}
});

function delete_return(id, reference)
{
	var confirm_text = "ใช่";
	var cancle_text = "ไม่ใช่";
	swal({
	  title: "คุณแน่ใจว่าต้องการลบ",
	  text: reference,
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: confirm_text,
	  cancelButtonText: cancle_text,
	  closeOnConfirm: true},
	  function(isConfirm){
	  if (isConfirm) {
		  load_in();
		$.ajax({
			url:"controller/returnController.php?delete_return&id_return_order="+id, type:"GET", cache:false,
			success: function(rs)
			{
				if(rs == "success")
				{
					load_out();
					swal({ title: "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1500, type: "success"});
					$("#row_"+id).remove();
				}else{
					load_out();
					swal("ลบรายการไม่สำเร็จ");
				}
			}
		});
	  } 
	});
}

function print_return(id)
{
	var Link = "controller/returnController.php?print_return&id_return_order="+id;
	window.open(Link, '_blank');	
}

function printReturnWithBarcode(id)
{
	window.open("controller/returnController.php?print_return_barcode&id_return_order="+id, '_blank');	
}

function search_text()
{
	load_in();
	$("#form").submit();	
	load_out();
}

function clear_filter()
{
	load_in();
	$.ajax({
		url:"controller/returnController.php?clear_filter",
		type:"GET", cache:false,
		success: function(rs)
		{
			window.location.href="index.php?content=order_return2";
			load_out();
		}
	});
}

function setCookie(cname, cvalue, exdays) {
	if(exdays !=""){
    	var d = new Date();
    	d.setTime(d.getTime() + (exdays*24*60*60*1000));
    	var expires = "expires="+d.toUTCString();
	}else{
		var expires = "";
	}
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}

function checkCookie() {
    var as = getCookie("warning");
    if (as == "") {
        swal({
			title:"Warning!",
			text: "*** โปรดใช้งานเมนูนี้ในกรณีที่ยอมรับเงื่อนไขต่อไปนี้ได้เท่านั้น ***<br/><br/>ระบบไม่มีการตรวจสอบ การอ้างอิงเอกสารขาย<br/>ระบบไม่มีการตรวจสอบ ความถูกต้องของรายการสินค้า<br/>"+
					"ระบบไม่มีการตรวจสอบ ความถูกต้องของส่วนลด<br/>ราคาสินค้าเป็นราคาสินค้า ณ วันที่ทำรายการ<br/>",
			html: true
		}, function(){
			setCookie("warning", 1, "");
		});
    }
}

function update_amount(id)
{
	var discount = parseFloat($("#reduction_amount_"+id).val());
	var price = parseFloat($("#price_"+id).val());
	
	if(discount > price || discount < 0)
	{ 
		$("#reduction_amount_"+id).val("0.00");
		swal({ title: "Error !", text : "ส่วนลดต้องไม่เกินราคาขาย และต้องไม่น้อยกว่า 0 ", type: "warning"});
		return false;
	}
	if(discount != 0 && discount != NaN)
	{
  		var qty = parseInt($("#qty"+id).val());
		var discount = discount*qty;
  		var total_amount = ((qty*price) - discount).toFixed(2) ;
  		$("#total_amount_"+id).text(total_amount);
  		$("#reduction_percent_"+id).val("0.00");
	}
}

function update_percent(id)
{
	var percent = parseFloat($("#reduction_percent_"+id).val());
	if(percent < 0 || percent > 100)
	{ 
		$("#reduction_percent_"+id).val('0.00');
		swal({ title: "Error!", text:"ส่วนลดต้องอยู่ในช่วง 0 - 100% เท่านั้น", type: "warning", showCancelButton: false, closeOnConfirm: true});
		return false;
	}
	if(percent != 0 && percent != "")
	{
		var percent = percent*0.01;
	  	var qty = parseInt($("#qty"+id).val());
	  	var price = $("#price_"+id).val();
	  	var discount = (price*percent)*qty;
	  	var total_amount = ((qty*price) - discount).toFixed(2) ;
	  	$("#total_amount_"+id).text(total_amount);
	  	$("#reduction_amount_"+id).val('0.00');
	}
}
function check_discount()
{
	var valid = 0;
	$(".id_detail").each(function(index, element) {
		var id	= $(this).val();
        var price = $("#price_"+id).val();
		var percent = $("#reduction_percent_"+id).val();
		var amount = $("#reduction_amount_"+id).val();
		if( percent != 0 && amount != 0 )
		{  
			valid += 1;			
		}
    });
	return valid;
}
</script>