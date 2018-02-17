<?php
	$page_menu 	= "invent_product_in";
	$page_name 	= "รับสินค้าเข้า จากการแปรสภาพ";
	$id_tab 			= 48;
	$id_profile 		= $_COOKIE['profile_id'];
    $pm 				= checkAccess($id_profile, $id_tab);
	$view 			= $pm['view'];
	$add 				= $pm['add'];
	$edit 				= $pm['edit'];
	$delete 			= $pm['delete'];
	accessDeny($view);
?>
<?php
	$btn = "";
	$btn_back = "<button type='button' class='btn btn-warning btn-sm' onclick='go_back()'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button>";
	if( isset( $_GET['add'] ) )
	{
		$btn .= $btn_back;
		if( $add && isset( $_GET['id_receive_tranform'] ) ){ $btn .= "&nbsp;<button type='button' class='btn btn-success btn-sm' onclick='save_add()'><i class='fa fa-save'></i>&nbsp; บันทึก</button>"; }
	}
	else if( isset( $_GET['edit'] ) )
	{
		$btn .= $btn_back;
		if( $edit && isset( $_GET['id_receive_tranform'] ) ){ $btn .= "&nbsp;<button type='button' class='btn btn-success btn-sm' onclick='save_edit()'><i class='fa fa-save'></i>&nbsp; บันทึก</button>"; }
		$btn .= "&nbsp;<button type='button' class='btn btn-info btn-sm' onClick='print_doc()'><i class='fa fa-print'></i>&nbsp; พิมพ์</button>";
	}
	else if( isset( $_GET['view_detail'] ) )
	{
		$btn .= $btn_back;
		if( $edit && isset( $_GET['id_receive_tranform'] ) ){ $btn .= "&nbsp;<button type='button' class='btn btn-primary btn-sm' onclick='go_edit(".$_GET['id_receive_tranform'].")'><i class='fa fa-pencil'></i>&nbsp; แก้ไข</button>"; }
		$btn .= "&nbsp;<button type='button' class='btn btn-info btn-sm' onClick='print_doc()'><i class='fa fa-print'></i>&nbsp; พิมพ์</button>";
	}
	else
	{
		if($add){ $btn .= "<button type='button' class='btn btn-success btn-sm' onclick='go_add()'><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button>"; }
	}
?>
<div class="container">
<!-- page place holder -->

<div class="row">
	<div class="col-xs-6">
    	<h3 class="title" style="margin-bottom:0px;"><i class="fa fa-download"></i>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-xs-6">
      	<p class="pull-right" style="margin-bottom:5px;">
        	<?php echo $btn; ?>
        </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php	if( isset($_GET['add'] ) ) : ?>
<!--------------------------------------------------------------------------  ADD ---------------------------------------------------------------------->
	<?php 
			if( isset( $_GET['id_receive_tranform'] ) ) : 
 				$id_rt					= $_GET['id_receive_tranform']; 
				$rt						= new receive_tranform($id_rt);
				$reference 			= $rt->reference;
				$order_reference	= $rt->order_reference;
				$id_employee		= $rt->id_employee;
				$date_add			= thaiDate($rt->date_add);
				$remark				= $rt->remark;
				$disabled			= "disabled";
			else :
				$rt						= new receive_tranform();
				$id_rt					= "";
				$reference 			= $rt->get_new_reference();
				$order_reference	= "";
				$id_employee		= "";
				$date_add			= date("d-m-Y");
				$remark				= "";
				$disabled			= "";
				
    		endif;
 	?>
<div class="row">
	<div class="col-lg-2">
    	<label>เลขที่เอกสาร</label>
        <span class="form-control input-sm" style="text-align:center;" <?php echo $disabled; ?>><?php echo $reference; ?></span>
        <input type="hidden" name="id_receive_tranform" id="id_receive_tranform" value="<?php echo $id_rt; ?>" />
    </div>
    <div class="col-lg-2">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm" name="date_add" id="date_add" value="<?php echo $date_add; ?>" style="text-align:center;" <?php echo $disabled; ?> />
    </div>
    <div class="col-lg-2">
    	<label>อ้างอิงใบเบิกสินค้า</label>
        <input type="text" class="form-control input-sm" name="order_reference" id="order_reference" value="<?php echo $order_reference; ?>" style="text-align:center;" placeholder="ระบุเลขที่ใบเบิกสินค้าเพื่อแปรสภาพ" <?php echo $disabled; ?> />
    </div>
    <div class="col-lg-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $remark; ?>" placeholder="ระบุหมายเหตุ (ถ้ามี)" <?php echo $disabled; ?> />
    </div>
    <div class="col-lg-1">
    	<label style="visibility:hidden">OK</label>
       	<?php if( isset( $_GET['id_receive_tranform'] ) ) : ?>
        <button type="button" class="btn btn-warning btn-sm btn-block" id="btn_edit" onClick="edit()" ><i class="fa fa-pencil"></i>&nbsp; แก้ไข</button>
        <button type="button" class="btn btn-success btn-sm btn-block" id="btn_update" onClick="update()" style="display:none;"><i class="fa fa-save"></i>&nbsp; บันทึก</button>
        <?php else : ?>
        <button type="button" class="btn btn-success btn-sm btn-block" onClick="add_new()"><i class="fa fa-plus"></i>&nbsp; เพิ่ม</button>
        <?php endif; ?>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
	<?php if( isset( $_GET['id_receive_tranform'] ) ) : ?>
<div class="row"    >
	<div class="col-lg-2">
    	<label>บาร์โค้ดโซน</label>
        <input type="text" name="barcode_zone" id="barcode_zone" class="form-control input-sm" placeholder="ยิงบาร์โค้ดโซน" autofocus />
        <input type="hidden" name="id_zone" id="id_zone"/>
    </div>
    <div class="col-lg-3">
    	<label>ชื่อโซน</label>
        <input type="text" name="zone_name" id="zone_name" class="form-control input-sm" placeholder="ค้นหาชื่อโซน" />
    </div>
    <div class="col-lg-1">
    	<label>จำนวน</label>
        <input type="text" name="qty" id="qty" class="form-control input-sm" value="1" style="text-align:center;" disabled />
    </div>
    <div class="col-lg-2">
    	<label>บาร์โค้ดสินค้า</label>
        <input type="text" name="barcode_item" id="barcode_item" class="form-control input-sm" placeholder="ยิงบาร์โค้ดสินค้าเพื่อรับเข้า" disabled />
    </div>
    <div class="col-lg-1">
    	<label style="visibility:hidden;">ตกลง</label>
        <button type="button" class="btn btn-primary btn-sm btn-block" id="btn_add_item" onClick="check_item()" disabled ><i class="fa fa-check"></i>&nbsp; ตกลง</button>
    </div>
    <div class="col-lg-2 col-lg-offset-1">
    	<label style="visibility:hidden;">ตกลง</label>
        <button type="button" class="btn btn-info btn-sm btn-block" onClick="change_zone()"><i class="fa fa-retweet"></i>&nbsp; เปลี่ยนโซน</button>
    </div>
</div>   
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-4"><i class="fa fa-check" style="color:green;"></i>&nbsp; = &nbsp; บันทึกแล้ว &nbsp;&nbsp; <i class="fa fa-remove" style="color:red;"></i>&nbsp; = &nbsp; ยังไม่บันทึก </div>
    <div class="col-lg-4"><center><h4 style="margin:0px;">จำนวนรวม&nbsp;<span id="count_qty">0</span>&nbsp; / &nbsp;<span id="total_qty">0</span>&nbsp; ชิ้น</h4></center></div>
    <div class="col-lg-2"></div><div class="col-lg-2"><button type="button" class="btn btn-primary btn-sm btn-block" onclick="sum_item()"><i class="fa fa-plus"></i>&nbsp; รวมยอด</button></div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-12">
    	<table class='table table-striped'>
            <thead>
                <th style='width:5%; text-align:center;'>ลำดับ</th>
                <th style='width:15%; text-align:center;'>รหัส</th>
                <th style="text-align:center">สินค้า</th>
                <th style="text-align:center; width: 10%;">โซน</th>
                <th style='width:10%; text-align:center;'>จำนวน</th>
                <th style="width:5%; text-align:center;">สถานะ</th>
                <th style='width:10%; text-align:right;'>การกระทำ</th>
            </thead>
            <tbody id="result">
            <?php $qs = $rt->get_items($id_rt); ?>
            <?php if(dbNumRows($qs) == 0 ) : ?>
            	<tr id="pre_label"><td align='center' colspan='7'><h4>----------  ยังไม่มีสินค้า ----------</h4></td></tr>
			<?php else : ?>
            <?php	$n = 1; ?>
            <?php 	while($rs = dbFetchArray($qs) ) : ?>
            <?php		$id = $rs['id_receive_tranform_detail']; ?>
            <?php		$pro_code	= get_product_reference($rs['id_product_attribute']); ?>
            	<tr style="font-size:12px;" id="row_<?php echo $id; ?>">
                	<td align="center"><span class="no"><?php echo $n; ?></span></td>
                    <td><?php echo $pro_code; ?></td>
                    <td><?php echo get_product_name($rs['id_product']); ?></td>
                    <td align="center"><?php echo get_zone($rs['id_zone']); ?></td>
                    <td align="center"><span class="qty"><?php echo number_format($rs['qty'],2); ?></span></td>
                    <td align="center"><?php echo isActived($rs['status']); ?></td>
                    <td align="right">
                    	<button type="button" class="btn btn-danger btn-sm" onClick="delete_row(<?php echo $id; ?>, '<?php echo $pro_code; ?>')"><i class="fa fa-trash"></i></button>
                    </td>
			<?php	$n++; ?>             
			<?php 	endwhile; ?>                	
                </tr>
			<?php endif; ?> 
            </tbody>
		</table>
    </div>
</div>
    <?php endif; ?>
<!--------------------------------------------------------------------------  ADD ---------------------------------------------------------------------->
<?php  	elseif( isset( $_GET['edit'] ) && isset( $_GET['id_receive_tranform'] ) ) : ?>
<!--------------------------------------------------------------------------  EDIT ---------------------------------------------------------------------->
	<?php 
				$id_r = $_GET['id_receive_tranform']; 
				$rd = new receive_tranform($id_r); 
	?>
<div class="row">
	<div class="col-lg-2">
    	<label>เลขที่เอกสาร</label>
        <span class="form-control input-sm" style="text-align:center;" disabled><?php echo $rd->reference; ?></span>
        <input type="hidden" name="id_receive_tranform" id="id_receive_tranform" value="<?php echo $id_r; ?>" />
    </div>
    <div class="col-lg-2">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm" name="date_add" id="date_add" value="<?php echo thaiDate($rd->date_add); ?>" style="text-align:center;" disabled />
    </div>
    <div class="col-lg-2">
    	<label>อ้างอิงใบเบิกสินค้า</label>
        <input type="text" class="form-control input-sm" name="order_reference" id="order_reference" value="<?php echo $rd->order_reference; ?>" style="text-align:center;" placeholder="ระบุเลขที่ใบเบิกสินค้าเพื่อแปรสภาพ" disabled />
    </div>
    <div class="col-lg-5">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" name="remark" id="remark" value="<?php echo $rd->remark; ?>" placeholder="ระบุหมายเหตุ (ถ้ามี)" disabled />
    </div>
    <div class="col-lg-1">
    	<label style="visibility:hidden">OK</label>
        <?php if($edit) : ?>
        <button type="button" class="btn btn-warning btn-sm btn-block" id="btn_edit" onClick="edit()" ><i class="fa fa-pencil"></i>&nbsp; แก้ไข</button>
        <button type="button" class="btn btn-success btn-sm btn-block" id="btn_update" onClick="update()" style="display:none;"><i class="fa fa-save"></i>&nbsp; บันทึก</button>
        <?php endif; ?>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />

<div class="row"    >
	<div class="col-lg-2">
    	<label>บาร์โค้ดโซน</label>
        <input type="text" name="barcode_zone" id="barcode_zone" class="form-control input-sm" placeholder="ยิงบาร์โค้ดโซน" autofocus />
        <input type="hidden" name="id_zone" id="id_zone"/>
    </div>
    <div class="col-lg-3">
    	<label>ชื่อโซน</label>
        <input type="text" name="zone_name" id="zone_name" class="form-control input-sm" placeholder="ค้นหาชื่อโซน" />
    </div>
    <div class="col-lg-1">
    	<label>จำนวน</label>
        <input type="text" name="qty" id="qty" class="form-control input-sm" value="1" style="text-align:center;" disabled />
    </div>
    <div class="col-lg-2">
    	<label>บาร์โค้ดสินค้า</label>
        <input type="text" name="barcode_item" id="barcode_item" class="form-control input-sm" placeholder="ยิงบาร์โค้ดสินค้าเพื่อรับเข้า" disabled />
    </div>
    <div class="col-lg-1">
    	<label style="visibility:hidden;">ตกลง</label>
        <button type="button" class="btn btn-primary btn-sm btn-block" id="btn_add_item" onClick="check_item()" disabled ><i class="fa fa-check"></i>&nbsp; ตกลง</button>
    </div>
    <div class="col-lg-2 col-lg-offset-1">
    	<label style="visibility:hidden;">ตกลง</label>
        <button type="button" class="btn btn-info btn-sm btn-block" onClick="change_zone()"><i class="fa fa-retweet"></i>&nbsp; เปลี่ยนโซน</button>
    </div>
</div>   
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-4"><i class="fa fa-check" style="color:green;"></i>&nbsp; = &nbsp; บันทึกแล้ว &nbsp;&nbsp; <i class="fa fa-remove" style="color:red;"></i>&nbsp; = &nbsp; ยังไม่บันทึก </div>
    <div class="col-lg-4"><center><h4 style="margin:0px;">จำนวนรวม&nbsp;<span id="count_qty">0</span>&nbsp; / &nbsp;<span id="total_qty">0</span>&nbsp; ชิ้น</h4></center></div>
    <div class="col-lg-2"></div><div class="col-lg-2"><button type="button" class="btn btn-primary btn-sm btn-block" onclick="sum_item()"><i class="fa fa-plus"></i>&nbsp; รวมยอด</button></div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-12">
    	<table class='table table-striped'>
            <thead>
                <th style='width:5%; text-align:center;'>ลำดับ</th>
                <th style='width:15%; text-align:center;'>รหัส</th>
                <th style="text-align:center">สินค้า</th>
                <th style="text-align:center; width: 10%;">โซน</th>
                <th style='width:10%; text-align:center;'>จำนวน</th>
                <th style="width:5%; text-align:center;">สถานะ</th>
                <th style='width:10%; text-align:right;'>การกระทำ</th>
            </thead>
            <tbody id="result">
            <?php $qs = $rd->get_items($id_r); ?>
            <?php if(dbNumRows($qs) == 0 ) : ?>
            	<tr id="pre_label"><td align='center' colspan='7'><h4>----------  ยังไม่มีสินค้า ----------</h4></td></tr>
			<?php else : ?>
            <?php	$n = 1; ?>
            <?php 	while($rs = dbFetchArray($qs) ) : ?>
            <?php		$id = $rs['id_receive_tranform_detail']; ?>
            <?php		$pro_code	= get_product_reference($rs['id_product_attribute']); ?>
            	<tr style="font-size:12px;" id="row_<?php echo $id; ?>">
                	<td align="center"><span class="no"><?php echo $n; ?></span></td>
                    <td><?php echo $pro_code; ?></td>
                    <td><?php echo get_product_name($rs['id_product']); ?></td>
                    <td align="center"><?php echo get_zone($rs['id_zone']); ?></td>
                    <td align="center"><span class="qty"><?php echo number_format($rs['qty'],2); ?></span></td>
                    <td align="center"><?php echo isActived($rs['status']); ?></td>
                    <td align="right">
                    	<button type="button" class="btn btn-danger btn-sm" onClick="delete_row(<?php echo $id; ?>, '<?php echo $pro_code; ?>')"><i class="fa fa-trash"></i></button>
                    </td>
			<?php	$n++; ?>             
			<?php 	endwhile; ?>                	
                </tr>
			<?php endif; ?> 
            </tbody>
		</table>
    </div>
</div>
<!--------------------------------------------------------------------------  EDIT ---------------------------------------------------------------------->
<?php 	elseif( isset( $_GET['view_detail'] ) && isset( $_GET['id_receive_tranform'] ) ) : ?>
<!--------------------------------------------------------------------------  Detail ---------------------------------------------------------------------->
	<?php 
				$id_r = $_GET['id_receive_tranform']; 
				$rd = new receive_tranform($id_r); 
	?>
<div class="row">
	<div class="col-lg-3">
    	<span class="form-contol input-sm" style="border:0px;"><label style="padding-right:5px;">เลขที่เอกสาร</label> <span><?php echo $rd->reference; ?></span></span>
        <input type="hidden" name="id_receive_tranform" id="id_receive_tranform" value="<?php echo $id_r; ?>" />
    </div>
    <div class="col-lg-2">
    	<span class="form-contol input-sm" style="border:0px;"><label style="padding-right:5px;">วันที่</label><span><?php echo thaiDate($rd->date_add); ?></span></span>
    </div>
    <div class="col-lg-3">
    	<span class="form-contol input-sm" style="border:0px;"><label style="padding-right:5px;">อ้างอิงใบเบิกสินค้า</label><span><?php echo $rd->order_reference; ?></span></span>
    </div>
    <div class="col-lg-4">
    	<span class="form-contol input-sm" style="border:0px;"><label style="padding-right:5px;">ผู้ทำรายการ</label><span><?php echo employee_name($rd->id_employee); ?></span></span>
    </div>
    <div class="col-lg-12">
    	<span class="form-contol input-sm" style="border:0px;"><label style="padding-right:5px;">หมายเหตุ</label><span><?php echo $rd->remark; ?></span></span>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />

<div class="row">
	<div class="col-lg-4"><i class="fa fa-check" style="color:green;"></i>&nbsp; = &nbsp; บันทึกแล้ว &nbsp;&nbsp; <i class="fa fa-remove" style="color:red;"></i>&nbsp; = &nbsp; ยังไม่บันทึก </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-12">
    	<table class='table table-striped'>
            <thead>
                <th style='width:5%; text-align:center;'>ลำดับ</th>
                <th style='width:15%;'>รหัส</th>
                <th>สินค้า</th>
                <th style="text-align:center; width: 10%;">โซน</th>
                <th style='width:10%; text-align:center;'>จำนวน</th>
                <th style="width:5%; text-align:center;">สถานะ</th>
            </thead>
            <tbody>
            <?php $qs = $rd->get_items($id_r); ?>
            <?php if(dbNumRows($qs) == 0 ) : ?>
            	<tr id="pre_label"><td align='center' colspan='7'><h4>----------  ยังไม่มีสินค้า ----------</h4></td></tr>
			<?php else : ?>
            <?php	$n = 1; ?>
            <?php	$total_qty = 0; ?>
            <?php 	while($rs = dbFetchArray($qs) ) : ?>
            <?php		$id = $rs['id_receive_tranform_detail']; ?>
            <?php		$pro_code	= get_product_reference($rs['id_product_attribute']); ?>
            	<tr style="font-size:12px;" id="row_<?php echo $id; ?>">
                	<td align="center"><span class="no"><?php echo $n; ?></span></td>
                    <td><?php echo $pro_code; ?></td>
                    <td><?php echo get_product_name($rs['id_product']); ?></td>
                    <td align="center"><?php echo get_zone($rs['id_zone']); ?></td>
                    <td align="center"><span class="qty"><?php echo number_format($rs['qty'],2); ?></span></td>
                    <td align="center"><?php echo isActived($rs['status']); ?></td>
			<?php	$n++; $total_qty += $rs['qty']; ?>             
			<?php 	endwhile; ?>                	
                </tr>
            	<tr>
                	<td colspan="4" align="right"><h4>รวม</h4></td>
                    <td align="center"><h4><?php echo number_format($total_qty); ?></h4></td>
                    <td align="right"><h4>ชิ้น</h4></td>
                </tr>
			<?php endif; ?> 
            </tbody>
		</table>
    </div>
</div>

<!--------------------------------------------------------------------------  Detail ---------------------------------------------------------------------->
<?php	else : ?>
<!--------------------------------------------------------------------------  List ---------------------------------------------------------------------->
<?php
	if( isset($_POST['from_date']) && $_POST['from_date'] !=""){ setcookie("receive_from_date", date("Y-m-d", strtotime($_POST['from_date'])), time() + 3600, "/"); }else{ setcookie("receive_from_date", "", time() + 3600, "/"); }
	if( isset($_POST['to_date']) && $_POST['to_date'] != ""){ setcookie("receive_to_date",  date("Y-m-d", strtotime($_POST['to_date'])), time() + 3600, "/"); }else{ setcookie("receive_to_date",  "", time() + 3600, "/"); }
	$paginator = new paginator();
?>	
<form  method='post' id='form'>
<div class='row'>
	<div class='col-lg-2 col-md-2 col-sm-3 col-sx-3'>
			<label>เงื่อนไข</label>
			<select class='form-control' name='filter' id='filter'>
            	<option value="reference"  <?php if( isset($_POST['filter']) && $_POST['filter'] =="reference"){ echo "selected"; }else if( isset($_COOKIE['receive_filter']) && $_COOKIE['receive_filter'] == "reference"){ echo "selected"; } ?>>เลขที่เอกสาร</option>
				<option value='order_reference' <?php if( isset($_POST['filter']) && $_POST['filter'] =="order_reference"){ echo "selected"; }else if( isset($_COOKIE['receive_filter']) && $_COOKIE['receive_filter'] == "order_reference"){ echo "selected"; } ?>>เลขที่อ้างอิง</option>
			</select>
		
	</div>	
	<div class='col-lg-3 col-md-3 col-sm-3 col-sx-3'>
    	<label>คำค้น</label>
        <?php 
			$value = '' ; 
			if(isset($_POST['search_text'])) : 
				$value = $_POST['search_text']; 
			elseif(isset($_COOKIE['receive_search_text'])) : 
				$value = $_COOKIE['receive_search_text']; 
			endif; 
		?>
		<input class='form-control' type='text' name='search_text' id='search_text' placeholder="ระบุคำที่ต้องการค้นหา" value='<?php echo $value; ?>' />	
	</div>	
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
		<label>จากวันที่</label>
            <?php 
				$value = ""; 
				if(isset($_POST['from_date']) && $_POST['from_date'] != "") : 
					$value = date("d-m-Y", strtotime($_POST['from_date'])); 
				elseif( isset($_COOKIE['receive_from_date'])) : 
					$value = date("d-m-Y", strtotime($_COOKIE['receive_from_date'])); 
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
				 elseif( isset($_COOKIE['receive_to_date']) ) :
					$value = date("d-m-Y", strtotime($_COOKIE['receive_to_date']));
				 endif;
			?>  
			<input type='test' class='form-control'  name='to_date' id='to_date' placeholder="ระบุวันที่" style="text-align:center" value='<?php echo $value; ?>' />
	</div>
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
    	<label style="visibility:hidden">show</label>
		<button class='btn btn-primary btn-block' id='search-btn' type='submit' ><i class="fa fa-search"></i>&nbsp;ค้นหา</button>
	</div>	
	<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
    	<label style="visibility:hidden">show</label>
		<button type='button' class='btn btn-danger' onclick="clear_filter()"><i class='fa fa-refresh'></i>&nbsp;reset</button>
	</div>
</div>
</form>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />
<?php

		if(isset($_POST['from_date']) && $_POST['from_date'] != ""){$from = date('Y-m-d',strtotime($_POST['from_date'])); }else if( isset($_COOKIE['receive_from_date'])){ $from = date('Y-m-d',strtotime($_COOKIE['receive_from_date'])); }else{ $from = "";} 
		if(isset($_POST['to_date']) && $_POST['to_date'] != ""){ $to =date('Y-m-d',strtotime($_POST['to_date']));  }else if(  isset($_COOKIE['receive_to_date'])){  $to =date('Y-m-d',strtotime($_COOKIE['receive_to_date'])); }else{ $to = "";}
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		
		/****  เงื่อนไขการแสดงผล *****/
		if(isset($_POST['search_text'])/* && $_POST['search_text'] !="" */) :
			$text = $_POST['search_text'];
			$filter = $_POST['filter'];
			setcookie("receive_search_text", $text, time() + 3600, "/");
			setcookie("receive_filter",$filter, time() +3600,"/");
		elseif(isset($_COOKIE['receive_search_text']) && isset($_COOKIE['receive_filter'])) :
			$text = $_COOKIE['receive_search_text'];
			$filter = $_COOKIE['receive_filter'];
		else : 
			$text	= "";
			$filter	= "";
		endif;
		$where = "WHERE id_receive_tranform != 0 ";
		if( $text != "" ) :
			switch( $filter) :				
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
			if($from != "" && $to != "" ) : 
				$where .= "AND (date_add BETWEEN '".$from."' AND '".$to."')";  
			endif;	
		endif;
		$where .= " ORDER BY date_add DESC";
		
?>		

<?php
$paginator = new paginator();
if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		$paginator->Per_Page("tbl_receive_tranform",$where,$get_rows);
		$paginator->display($get_rows,"index.php?content=receive_tranform");
		$Page_Start = $paginator->Page_Start;
		$Per_Page = $paginator->Per_Page;
?>	
<style>
	.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td 
	{
		vertical-align:middle;
	}
</style>
<div class="row">
	<div class="col-lg-12">
    <table class="table table-striped">
    <thead>
    	<th style="width:5%;">ID</th>
        <th style="width:10%;">วันที่</th>
        <th style="width:15%;">เลขที่เอกสาร</th>
        <th style="width:15%;">เลขที่อ้างอิง</th>
        <th style="width:10%; text-align:center">จำนวน</th>
        <th style="width:30%; text-align:center;">สถานะ</th>
        <th style="text-align:right">การกระทำ</th> 
    </thead>
    <tbody>
	<?php $qs = dbQuery("SELECT * FROM tbl_receive_tranform ".$where." LIMIT ".$Page_Start.", ".$Per_Page); ?>
    <?php if(dbNumRows($qs) > 0 ) : ?>
    	<?php $ro	= new receive_tranform(); ?>
        <?php  $n = 1; ?>
    	<?php while($rs = dbFetchArray($qs) ) : ?>
        <?php 	$status 	= $rs['status']; ?>
        <?php	$id			= $rs['id_receive_tranform']; ?>
    	<tr id="row_<?php echo $id; ?>" style="font-size:12px;">
        	<td><?php echo $n; ?></td>
            <td><?php echo thaiDate($rs['date_add']); ?></td>
            <td><?php echo $rs['reference']; ?></td>
            <td><?php echo $rs['order_reference']; ?></td>
            <td align="center"><?php echo number_format($ro->total_qty($id)); ?></td>
            <td align="center">
				<?php if($status == 1 ) : ?>
                	<span style="color:#6C0;"><strong>บันทึกแล้ว</strong></span>
               	<?php else : ?>
                	<span style="color:#F00"><strong>ยังไม่บันทึก</strong></span>
               	<?php endif; ?>
            </td>
            <td align="right">
            	<a href="controller/receiveTranformController.php?print&id_receive_tranform=<?php echo $id; ?>" target="_blank"><button type="button" class="btn btn-success btn-sm"><i class="fa fa-print"></i></button></a>
            	<a href="index.php?content=receive_tranform&view_detail&id_receive_tranform=<?php echo $id; ?>"><button type="button" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></button></a>
            <?php if($edit) : ?>
                <a href='index.php?content=receive_tranform&edit=y&id_receive_tranform=<?php echo $id; ?>'><button type='button' class='btn btn-warning btn-sm'><i class='fa fa-pencil'></i></button></a>
			<?php endif; ?>                    
            <?php if($delete) : ?>
                <button type="button" class="btn btn-danger btn-sm" onclick="delete_doc(<?php echo $id; ?>, '<?php echo $rs['reference']; ?>')"><i class="fa fa-trash"></i></button>
            <?php endif;?>
        </tr>
        <?php $n++; ?>
    	<?php endwhile; ?>
    <?php else : ?>    
    	<tr><td colspan="8" ><center><h4>---------- ไม่มีรายการ  ----------</h4></center></td></tr>
    <?php endif; ?>   
    </tbody>
    </table>
    </div>
</div>
<!--------------------------------------------------------------------------  List ---------------------------------------------------------------------->
<?php 	endif; ?>
<script id='item' type="text/x-handlebars-template">
<tr style="font-size:12px;">
	<td>&nbsp;</td>
    <td>{{ product_code }}</td>
    <td>{{ product_name }}</td>
	<td align="center">{{ zone_name }}</td>
    <td align="center"><span class="qty">{{ qty }}</span></td>
	<td>&nbsp;</td>
    <td>&nbsp;</td>
</tr>
</script>
<script id='sum_item' type="text/x-handlebars-template">
{{#each this}}
<tr id="row_{{ id }}" style="font-size:12px;">
	<td align="center"><span class="no">{{ no }}</span></td>
    <td>{{ product_code }}</td>
    <td>{{ product_name }}</td>
	<td align="center">{{ zone_name }}</td>
    <td align="center"><span class="qty">{{ qty }}</span></td>
	<td align="center">{{{ status }}}</td>
    <td align="right"><button type="button" class="btn btn-danger btn-sm" onClick="delete_row({{ id }})"><i class="fa fa-trash"></i></button></td>
</tr>
{{/each}}
</script>
</div><!----- Container ---->
<script>
$(document).ready(function(e) {
    sum_row();
});
function save_edit()
{
	var id		= parseInt($("#id_receive_tranform").val());
	if( !isNaN(id) )
	{
		load_in();
		$.ajax({
			url:"controller/receiveTranformController.php?save_edit",
			type: "POST", cache: "false", data: { "id_receive_tranform" : id },
			success: function(rs)
			{
				var rs = $.trim(rs);
				if( rs == "success")
				{
					window.location.href="index.php?content=receive_tranform&edit=y&id_receive_tranform="+id+"&message=บันทึกเรียบร้อยแล้ว";
				}else{
					load_out();
					swal({ title: "Error !!", text: "บันทึกรายการไม่สำเร็จ", type: "error"});
				}
			}
		});
	}else{
		swal("Error !!", "ไม่พบเลขที่เอกสาร ลองออกจากหน้านี้แล้วกลับเข้ามาใหม่", "error");
	}
}

function save_add()
{
	var id		= parseInt($("#id_receive_tranform").val());
	if( !isNaN(id) )
	{
		load_in();
		$.ajax({
			url:"controller/receiveTranformController.php?save_add",
			type: "POST", cache: "false", data: { "id_receive_tranform" : id },
			success: function(rs)
			{
				var rs = $.trim(rs);
				if( rs == "success")
				{
					window.location.href="index.php?content=receive_tranform&edit=y&id_receive_tranform="+id+"&message=บันทึกเรียบร้อยแล้ว";
				}else{
					load_out();
					swal({ title: "Error !!", text: "บันทึกรายการไม่สำเร็จ", type: "error"});
				}
			}
		});
	}else{
		swal("Error !!", "ไม่พบเลขที่เอกสาร ลองออกจากหน้านี้แล้วกลับเข้ามาใหม่", "error");
	}
}

function delete_doc(id, reference)
{
	swal({
		title: "คุณแน่ใจ ?",
		text: "รายการทั้งหมดที่อยู่ในเอาสารนี้จะถูกลบถาวร โปรดจำไว้ว่า การกระทำนี้ไม่สามารถกู้คืนได้  คุณต้องการลบ "+ reference+" ใช่หรือไม่ ? ",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "ใช่ ฉันต้องการลบ",
		cancelButtonText: "ยกเลิก",
		closeOnConfirm: true,
		showLoaderOnConfirm: true,
	}, 	function(isConfirm){
			load_in();
			$.ajax({
				url:"controller/receiveTranformController.php?delete_doc&id_receive_tranform="+id,
				type: "GET", cache: "false",
				success: function(rs)
				{
					var rs = $.trim(rs);
					if(rs == "success")
					{
						load_out();
						$("#row_"+id).remove();
						swal({ title: "เรียบร้อย", text: "ลบเอกสารเรียบร้อยแล้ว", timer: 1000, type: "success"});
					}else if( rs == "fail" || rs == ""){
						load_out();
						swal({ title: "Error!!", text: "ลบเอกสารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", type: "error"});
					}
				}
			});
	});		
}


function delete_row(id, product)
{
	swal({
			title: "คุณแน่ใจ ?",
			text: "คุณต้องการลบ "+product+" ใช่หรือไม่",
			type: "warning",
			showCancelButton: true,
		  	confirmButtonColor: "#DD6B55",
		  	confirmButtonText: "ใช่ ฉันต้องการลบ",
		  	cancelButtonText: "ยกเลิก",
		  	closeOnConfirm: false 
			},
		  	function(isConfirm){
				if(isConfirm) {
					$.ajax({
						url:"controller/receiveTranformController.php?delete_item",
						type:"POST", cache: "false", data: { "id_receive_tranform_detail" : id },
						success: function(rs)
						{
							var rs = $.trim(rs);
							if( rs == "success")
							{
								$("#row_"+id).remove();
								update_no();
								sum_row();
								swal({ title: "เรียบร้อย", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success"});
							}else{
								swal("ผิดพลาด", "ลบรายการไม่สำเร็จ ลองใหม่อีกครั้ง", "error");
							}
						}
					});
				}
		});	
}

function check_item()
{
	var id_zone 	= $("#id_zone").val();
	var barcode	= $("#barcode_item").val();
	var qty			= parseInt($("#qty").val());
	var id				= $("#id_receive_tranform").val();
	if( isNaN(parseInt(id_zone)) ){ swal("Error !!", "ยังไม่ได้กำหนดโซน โปรดระบุโซนก่อนรับสินค้า", "error"); return false; }
	if( barcode	== "" ){ return false; }
	if( isNaN(qty) || qty > 99999 ){ swal("Warning","จำนวนสินค้าผิดปกติ ตรวจสอบดูให้ดีว่าจำนวนสินค้าถูกต้อง","warning"); return false; }
	if( id ==""){ swal("Error !!", "ไม่พบเลขที่เอกสาร กรุณาออกจากหน้านี้แล้วเข้าใหม่อีกครั้ง", "error"); return false; }
	add_item(id_zone, barcode, qty, id);
	add_qty(qty);
	$("#qty").val(1);
	$("#barcode_item").val('');
	$("#barcode_item").focus();
}

function add_item(id_zone, barcode, qty, id)
{
	$.ajax({
		url:"controller/receiveTranformController.php?add_item",
		type:"POST", cache:false, 
		data:{ "id_receive_tranform" : id, "id_zone" : id_zone, "barcode" : barcode, "qty" : qty },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs != "fail" && rs != "barcode_fail" && rs != "")
			{
				var data		= $.parseJSON(rs);
				var source 	= $("#item").html();
				var row 		= Handlebars.compile(source);
				var html 		= row(data);
				$("#result").prepend(html);
				count_qty(qty);
			}else if(rs == "barcode_fail"){
				add_qty(qty*-1);
				swal("Error !!", "บาร์โค้ดสินค้าไม่ถูกต้อง หรือไม่มีข้อมูลสินค้าในระบบ กรุณาตรวจสอบ", "error");
				beep();
				return false;
			}else{
				add_qty(qty*-1);
				swal("Error !!", "เพิ่มรายการไม่สำเร็จ ลองใหม่อีกครั้ง", "error");
				beep();
				return false;
			}
		}
	});
}
function sum_row()
{
	var qty = 0;
	$(".qty").each(function(index, element) {
        var q = parseInt($(this).html());
		qty += q;
    });	
	$("#total_qty").html(qty);
	$("#count_qty").html(qty);
}

function add_qty(qty)
{
	var q = parseInt($("#total_qty").html());
	q += qty;
	$("#total_qty").html(q);	
}

function count_qty(qty)
{
	var q = parseInt($("#count_qty").html());
	q += qty;
	$("#count_qty").html(q);	
}

function update_no()
{
	var i = 1;
	$(".no").each(function(index, element) {
        $(this).html(i);
		i++;
    });	
}
function sum_item()
{
	load_in();
	var id = $("#id_receive_tranform").val();
	$.ajax({
		url:"controller/receiveTranformController.php?sum_item",
		type:"POST", cache:"false", data:{ "id_receive_tranform" : id },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs != "fail" || rs != "")
			{
				var data		= $.parseJSON(rs);
				var source 	= $("#sum_item").html();
				var output	= $("#result");
				render(source, data, output);
				sum_row();
				load_out();
			}else{
				load_out();
				swal("ไม่มีข้อมูล");
			}
		}
	});
}

function add_new()
{
	var id_employee 		= <?php echo $_COOKIE['user_id']; ?>;
	var order_reference = $("#order_reference").val();
	var date_add			= $("#date_add").val();
	var remark				= $("#remark").val();
	if( order_reference == "")
	{
		swal("ข้อมูลไม่ครบ", "กรุณาอ้างอิงเลขที่เอกสารเบิกสินค้าแปรสภาพ", "error");
		return false;
	}else if( !isDate(date_add) ){
		swal("รูปแบบวันที่ไม่ถูกต้อง");
		return false;
	}else{
		load_in();
		$.ajax({
			url:"controller/receiveTranformController.php?add_new",
			type:"POST", cache:false, 
			data:{ "order_reference" : order_reference, "id_employee" : id_employee, "date_add" : date_add, "remark" : remark },
			success: function(rs)
			{
				var rs = $.trim(rs);
				if(rs !="fail" && rs !="" )
				{
					window.location.href="index.php?content=receive_tranform&add=y&id_receive_tranform="+rs;
				}else{
					load_out();
					swal("Error!!", "ไม่สามารถเพิ่มเอกสารใหม่ได้ในขณะนี้ กรุณาลองใหม่อีกครั้ง", "error");
				}
			}
		});
	}		
}

function edit()
{
	$("#date_add").removeAttr("disabled");
	$("#order_reference").removeAttr("disabled");
	$("#remark").removeAttr("disabled");
	$("#btn_edit").css("display", "none");
	$("#btn_update").css("display","");	
}
function updated()
{
	$("#date_add").attr("disabled", "disabled");
	$("#order_reference").attr("disabled", "disabled");
	$("#remark").attr("disabled", "disabled");
	$("#btn_edit").css("display", "");
	$("#btn_update").css("display","none");	
}

function update()
{
	var id						= $("#id_receive_tranform").val();
	var id_employee 		= <?php echo $_COOKIE['user_id']; ?>;
	var order_reference = $("#order_reference").val();
	var date_add			= $("#date_add").val();
	var remark				= $("#remark").val();
	if( id != "")
	{
		if( order_reference == "")
		{
			swal("ข้อมูลไม่ครบ", "กรุณาอ้างอิงเลขที่เอกสารเบิกสินค้าแปรสภาพ", "error");
			return false;
		}else if( !isDate(date_add) ){
			swal("รูปแบบวันที่ไม่ถูกต้อง");
			return false;
		}else{
			load_in();
			$.ajax({
				url:"controller/receiveTranformController.php?update&id_receive_tranform="+id,
				type:"POST", cache:false, 
				data:{ "order_reference" : order_reference, "id_employee" : id_employee, "date_add" : date_add, "remark" : remark },
				success: function(rs)
				{
					var rs = $.trim(rs);
					if(rs == "success" )
					{
						updated();
						load_out();					
					}else{
						load_out();
						swal("Error!!", "ไม่สามารถแก้ไขเอกสารได้ในขณะนี้ กรุณาลองใหม่อีกครั้ง", "error");
					}
				}
			});
		}	
	}else{
		swal("Error !!", "ไม่พบ ID เอกสาร กรุณาออกจากหน้านี้แล้วเข้าใหม่ หากยังพบปัญหานี้อีก ติดต่อผู้ดูแลระบบ", "error");	
	}
}

function change_zone()
{
	$("#id_zone").val('');
	$("#barcode_zone").val('');
	$("#zone_name").val('');
	$("#qty").val(1);
	$("#qty").attr("disabled","disabled");	
	$("#barcode_item").attr("disabled","disabled");
	$("#btn_add_item").attr("disabled","disabled");
	$("#barcode_zone").removeAttr("disabled");
	$("#zone_name").removeAttr("disabled");
	$("#barcode_zone").focus();
}
function get_zone(barcode)
{
	load_in();
	$.ajax({
		url:"controller/receiveTranformController.php?get_zone",
		type:"POST", cache:false, data:{ "barcode" : barcode },
		success: function(rs)
		{
			var rs = $.trim(rs);
			var arr = rs.split(" : ");
			if(!isNaN(parseInt(arr[0])))
			{
				$("#id_zone").val(arr[0]);
				$("#zone_name").val(arr[1]);
				$("#barcode_zone").attr("disabled", "disabled");
				$("#zone_name").attr("disabled","disabled");
				$("#qty").removeAttr("disabled");
				$("#barcode_item").removeAttr("disabled");
				$("#btn_add_item").removeAttr("disabled");
				$("#barcode_item").focus();
				load_out();
			}else{
				load_out();
				swal("ไม่พบโซนที่ระบุ", "โปรดตรวจสอบความถูกต้องของบาร์โค้ด หรือ ภาษาที่ใช้เป็นภาษาอังกฤษ", "error");
				beep();
			}
		}
	});
}
function go_add()
{
	window.location.href="index.php?content=receive_tranform&add=y";	
}

function go_edit(id)
{
	window.location.href="index.php?content=receive_tranform&edit=y&id_receive_tranform="+id;	
}

function go_back()
{
	window.location.href="index.php?content=receive_tranform";	
}

function print_doc()
{
	var id		= $("#id_receive_tranform").val();
	var Link = "controller/receiveTranformController.php?print&id_receive_tranform="+id;
	window.open(Link, '_blank');	
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


$("#barcode_zone").keyup(function(e) {
    if(e.keyCode == 13)
	{
		var barcode = $(this).val();
		if(barcode != "" )
		{
			get_zone(barcode);
		}
	}
});

$("#barcode_item").keyup(function(e){
	if(e.keyCode == 13)
	{
		var barcode = $(this).val();
		if(	barcode != "" )
		{
			$("#btn_add_item").click();	
		}
	}
});

$("#zone_name").autocomplete({
	source:"controller/autoComplete.php?get_zone_name",
	autoFocus: true,
	close: function(event, ui){
		var data = $(this).val();
		var arr	= data.split(" : ");
		$("#id_zone").val(arr[0]);
		$("#zone_name").val(arr[1]);
		$("#barcode_zone").attr("disabled", "disabled");
		$("#zone_name").attr("disabled", "disabled");
		$("#qty").removeAttr("disabled");
		$("#barcode_item").removeAttr("disabled");
		$("#btn_add_item").removeAttr("disabled");
		$("#barcode_item").focus();
	}
});
function clear_filter()
{
	load_in();
	$.ajax({
		url:"controller/receiveTranformController.php?clear_filter"	,
		type:"GET", cache: "false", success: function(rs){
			window.location.href="index.php?content=receive_tranform";
		}
	});
}
</script>
<!---------------  Beep sount for alert ----------->
<script src="../library/js/beep.js" type="text/javascript"></script>