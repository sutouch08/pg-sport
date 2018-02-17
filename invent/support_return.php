<?php
	$page_name = "คืนสินค้าจากอภินันท์";
	$id_tab = 41;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
	$btn = "";
	if(isset($_GET['edit']) && isset($_GET['id_return_support']) ) :
		$btn .= "<a href='index.php?content=support_return' style='text-decoration:none;'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		$btn .= "<button class='btn btn-success' onclick='save_edit()' style='margin-left:10px;'><i class='fa fa-save'></i>&nbsp; บันทึก</button>";
	elseif( isset($_GET['add']) && isset($_GET['id_return_support'])) :
		$btn .= "<a href='index.php?content=support_return' style='text-decoration:none;'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		$btn .= "<button class='btn btn-success' onclick='save_add()' style='margin-left:10px;'><i class='fa fa-save'></i>&nbsp; บันทึก</button>";
	elseif( isset($_GET['add']) ) :
		$btn .= "<a href='index.php?content=support_return' style='text-decoration:none;'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
	elseif( isset($_GET['view_detail']) ) :
		$btn .= "<a href='index.php?content=support_return' style='text-decoration:none;'><button type='button' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
	else :
		$btn .= can_do($add, "<a href='index.php?content=support_return&add=y'><button type='button' class='btn btn-success'><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button></a>");
	endif;

	?>

<div class="container">
<div class="row">
	<div class="col-xs-6">
    	<h3 class="title"><?php echo $page_name; ?></h3>
	</div>
    <div class="col-xs-6">
       <p class="pull-right">
       	<?php echo $btn; ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:10px;' />
<?php if( isset($_GET['add']) ) : ?>
	<?php if(isset($_GET['id_return_support'])) :
				$id_return_support 	= $_GET['id_return_support'];
				$rs					= new return_support($id_return_support);
				$reference 			= $rs->reference;
				$order_reference = $rs->order_reference;
				$id_employee		= $rs->id_employee;
				$employee_name 	= employee_name($id_employee);
				$date_add			= $rs->date_add;
				$remark				= $rs->remark;
				$active				= "disabled";
			else :
				$id_return_support	= '';
				$reference			= get_max_return_support_reference();
				$order_reference	= '';
				$id_employee		= '';
				$employee_name	= '';
				$date_add			= date("Y-m-d");
				$remark				= '';
				$active				= '';
			endif;
	?>
<!------------------------------------------------  เพิ่มเอกสารใหม่  --------------------------------->
		<!-----------------  หัวเอกสาร  ------------------->
<form id="add_form" action="controller/returnController.php?add=y&role=7" method="post">
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
    		<span class="input-group-addon">ผู้เบิก</span>
        	<input type="text" class="form-control" style="text-align:center" id="employee" name="employee" value="<?php echo $employee_name; ?>" placeholder="ระบุพนักงานที่คืนสินค้า" autofocus="autofocus" <?php echo $active; ?> />
       	</div>
        <input type="hidden" name="id_employee" id="id_employee" value="<?php echo $id_employee; ?>"  />
    </div>
    <div class="col-lg-3 col-md-3 col-sm-4">
    	<div class="input-group">
    		<span class="input-group-addon">เอกสารอ้างอิง</span>
        	<input type="text" class="form-control" style="text-align:center" id="order_reference" name="order_reference" value="<?php echo $order_reference; ?>" placeholder="อ้างอิงเลขที่เอกสารเบิกอภินันท์" <?php echo $active; ?> />
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
        <button type="button" class="btn btn-success btn-block" id="btn_update" onclick="update(<?php echo $id_return_support; ?>)" style="display:none;"><i class="fa fa-save"></i>&nbsp; อัพเดต</button>
        <?php endif; ?>
    </div>

</div>
</form>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
		<!-----------------  จบหัวเอกสาร  ------------------->
        <!----------------  ส่วนยิงรับเข้า  -------------------->
	<?php if( isset($_GET['id_return_support']) ) : ?>
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
        <input type="hidden" name="id_return_support" id="id_return_support" value="<?php echo $_GET['id_return_support']; ?>"  />
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
		<!----------------  จบส่วนยิงรับเข้า  -------------------->
        <!----------------  แสดงรายการรับเข้า  -------------------->
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
	<table class="table table-striped">
    <thead>
    	<th style="width:15%;">วันที่</th>
        <th>สินค้า</th>
        <th style="width:10%; text-align:right">จำนวน</th>
        <th style="width:15%; text-align:center">โซน</th>
        <th style="width:10%; text-align:center">คลัง</th>
        <th style="width:5%; text-align:center">สถานะ</th>
        <th style="width:5%; text-align:right;"></th>
    </thead>
    <tbody id="data">
<?php $qs = dbQuery("SELECT * FROM tbl_return_support_detail WHERE id_return_support = ".$id_return_support); ?>
<?php	$rw = dbNumRows($qs); ?>
<?php	if( $rw > 0 ) :
			while( $rs = dbFetchArray($qs) ) :
				$product = new product();
				$id_product		= $product->getProductId($rs['id_product_attribute']);
				$product_name = $product->product_reference($rs['id_product_attribute'])." : ".$product->product_name($id_product);
?>
		<tr id="row<?php echo $rs['id_return_support_detail']; ?>">
        	<td><?php echo thaiDateTime($rs['date_add']); ?></td>
            <td id="product<?php echo $rs['id_return_support_detail']; ?>"><?php echo $product_name; ?></td>
            <td align="right"><?php echo number_format($rs['qty']); ?><input type="hidden" class='qty' id="qty<?php echo $rs['id_return_support_detail'];?>" value="<?php echo $rs['qty']; ?>"/></td>
            <td id="zone<?php echo $rs['id_return_support_detail']; ?>" align="center"><?php echo get_zone($rs['id_zone']); ?></td>
            <td align="center"><?php echo get_warehouse_name_by_id(get_warehouse_by_zone($rs['id_zone'])); ?></td>
            <td align="center"><?php echo isActived($rs['status']); ?></td>
            <td align="right"><button type="button" class="btn btn-danger btn-xs btn-block" onclick="delete_row(<?php echo $rs['id_return_support_detail']; ?>)"><i class="fa fa-trash"></i></button></td>
        </tr>
<?php  	endwhile;	?>
<?php else : ?>
		<tr><td colspan="7" align="center"><h4>---------- ไม่มีรายการ  ----------</h4></td></tr>
<?php endif; ?>
	</tbody>
</table>

</div>
</div>

	<?php endif; ?>
		<!----------------  จบแสดงรายการรับเข้า  -------------------->
<?php elseif( isset($_GET['edit']) && isset($_GET['id_return_support']) ) : ?>
	<?php $id_return_support	= $_GET['id_return_support']; ?>
	<?php $sr = dbQuery("SELECT * FROM tbl_return_support WHERE id_return_support = ".$id_return_support); ?>
    <?php if(dbNumRows($sr) == 1 ) : ?>
    <?php 	$rs = dbFetchArray($sr); ?>
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
    		<span class="input-group-addon">ผู้เบิก</span>
        	<input type="text" class="form-control" style="text-align:center" id="employee" name="employee" value="<?php echo employee_name($rs['id_employee']); ?>" placeholder="ระบุพนักงานที่คืนสินค้า" autofocus="autofocus" disabled />
       	</div>
        <input type="hidden" name="id_employee" id="id_employee" value="<?php echo $rs['id_employee']; ?>"  />
    </div>
    <div class="col-lg-3 col-md-3 col-sm-4">
    	<div class="input-group">
    		<span class="input-group-addon">เอกสารอ้างอิง</span>
        	<input type="text" class="form-control" style="text-align:center" id="order_reference" name="order_reference" value="<?php echo $rs['order_reference']; ?>" placeholder="อ้างอิงเลขที่เอกสารเบิกอภินันท์" disabled />
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
        <button type="button" class="btn btn-success btn-block" id="btn_update" onclick="update(<?php echo $id_return_support; ?>)" style="display:none;"><i class="fa fa-save"></i>&nbsp; อัพเดต</button>
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
        <input type="hidden" name="id_return_support" id="id_return_support" value="<?php echo $_GET['id_return_support']; ?>"  />
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
		<!----------------  จบส่วนยิงรับเข้า  -------------------->
        <!----------------  แสดงรายการรับเข้า  -------------------->
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
	<table class="table table-striped">
    <thead>
    	<th style="width:15%;">วันที่</th>
        <th>สินค้า</th>
        <th style="width:10%; text-align:right">จำนวน</th>
        <th style="width:15%; text-align:center">โซน</th>
        <th style="width:10%; text-align:center">คลัง</th>
        <th style="width:5%; text-align:center">สถานะ</th>
        <th style="width:5%; text-align:right;"></th>
    </thead>
    <tbody id="data">
<?php $qs = dbQuery("SELECT * FROM tbl_return_support_detail WHERE id_return_support = ".$id_return_support); ?>
<?php	$rw = dbNumRows($qs); ?>
<?php	if( $rw > 0 ) :
			while( $rs = dbFetchArray($qs) ) :
				$product = new product();
				$id_product		= $product->getProductId($rs['id_product_attribute']);
				$product_name = $product->product_reference($rs['id_product_attribute'])." : ".$product->product_name($id_product);
?>
		<tr id="row<?php echo $rs['id_return_support_detail']; ?>">
        	<td><?php echo thaiDateTime($rs['date_add']); ?></td>
            <td id="product<?php echo $rs['id_return_support_detail']; ?>"><?php echo $product_name; ?></td>
            <td align="right"><?php echo number_format($rs['qty']); ?><input type="hidden" class='qty' id="qty<?php echo $rs['id_return_support_detail'];?>" value="<?php echo $rs['qty']; ?>"/></td>
            <td id="zone<?php echo $rs['id_return_support_detail']; ?>" align="center"><?php echo get_zone($rs['id_zone']); ?></td>
            <td align="center"><?php echo get_warehouse_name_by_id(get_warehouse_by_zone($rs['id_zone'])); ?></td>
            <td align="center"><?php echo isActived($rs['status']); ?></td>
            <td align="right"><?php if($edit) : ?><button type="button" class="btn btn-danger btn-xs btn-block" onclick="delete_row(<?php echo $rs['id_return_support_detail']; ?>)"><i class="fa fa-trash"></i></button><?php endif; ?></td>
        </tr>
<?php  	endwhile;	?>
<?php else : ?>
		<tr><td colspan="7" align="center"><h4>---------- ไม่มีรายการ  ----------</h4></td></tr>
<?php endif; ?>
	</tbody>
</table>

</div>
</div>

	<?php else : /// if(isset($_GET['id_return_support']) ?>
    	<div class="row"><div class="col-lg-12"><center><h3> ไม่พบข้อมูลของรายการที่กำหนด หรือ เอกสารไม่มีอยู่จริง </h3></center></div></div>
    <?php endif; ?>
		<!----------------  จบแสดงรายการรับเข้า  -------------------->

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
			if(isset($_POST['search-text']) && $_POST['search-text'] !="") :
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
			<input type='text' class='form-control'  name='to_date' id='to_date' placeholder="ระบุวันที่" style="text-align:center" value='<?php echo $value; ?>' />
	</div>
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
    	<label style="visibility:hidden">show</label>
		<button class='btn btn-primary btn-block' id='search-btn' type='button'><i class="fa fa-search"></i>&nbsp;ค้นหา</button>
	</div>
	<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
    	<label style="visibility:hidden">show</label>
		<button type='button' class='btn btn-danger' onclick="window.location.href='controller/returnController.php?clear_filter&role=support_return'"><i class='fa fa-refresh'></i>&nbsp;reset</button>
	</div>
</div>
</form>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />
<?php

		if(isset($_POST['from_date']) && $_POST['from_date'] != ""){$from = date('Y-m-d',strtotime($_POST['from_date'])); }else if( isset($_COOKIE['return_from_date'])){ $from = date('Y-m-d',strtotime($_COOKIE['return_from_date'])); }else{ $from = "";}
		if(isset($_POST['to_date']) && $_POST['to_date'] != ""){ $to =date('Y-m-d',strtotime($_POST['to_date']));  }else if(  isset($_COOKIE['return_to_date'])){  $to =date('Y-m-d',strtotime($_COOKIE['return_to_date'])); }else{ $to = "";}
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}

		/****  เงื่อนไขการแสดงผล *****/
		if(isset($_POST['search-text']) && $_POST['search-text'] !="" ) :
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
		$where = "WHERE id_return_support != 0 ";
		if( $text != "" ) :
			switch( $filter) :
				case "customer" :
				$in_cause = "";
				$qs = dbQuery("SELECT id_employee FROM tbl_customer WHERE first_name LIKE'%$text%' OR last_name LIKE'%$text%' GROUP BY id_employee");
				$rs = dbNumRows($qs);
				$i=0;
				if($rs>0) :
					while($i<$rs) :
						list($in) = dbFetchArray($qs);
						$in_cause .="$in";
						$i++;
						if($i<$rs){ $in_cause .=","; 	}
					endwhile;
					$where .= "AND id_employee IN($in_cause)";
					else :
						$where .= "AND id_employee = 0";
					endif;
				break;
				case "order_reference" :
					$where .= "AND order_reference LIKE'%$text%'";
				break;
				case "reference" :
				$where .= "AND reference LIKE'%$text%'";
				break;
			endswitch;
		endif;
		if($from != "" && $to != "" ) :
			$where .= " AND (date_add BETWEEN '".$from."' AND '".$to."')";
		endif;

?>

<?php
$paginator = new paginator();
if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		$paginator->Per_Page("tbl_return_support",$where,$get_rows);
		$paginator->display($get_rows,"index.php?content=return_support");
		$Page_Start = $paginator->Page_Start;
		$Per_Page = $paginator->Per_Page;
?>
<div class="row">
	<div class="col-lg-12">
    <table class="table table-striped">
    <thead>
    	<th style="width:5%;">ID</th>
        <th style="width:15%;">เลขที่เอกสาร</th>
        <th style="width:15%;">ใบกำกับภาษี</th>
        <th style="width:25%;">ลูกค้า</th>
        <th style="width:10%; text-align:center">จำนวน</th>
        <th style="width:10%;">วันที่</th>
        <th style="width:10%; text-align:center">สถานะ</th>
        <th style="width:10%; text-align:right">การกระทำ</th>
    </thead>
    <tbody>
	<?php $sql = dbQuery("SELECT * FROM tbl_return_support ".$where." LIMIT ".$Page_Start.", ".$Per_Page); ?>
    <?php if(dbNumRows($sql) > 0 ) : ?>
    	<?php $ro	= new return_support(); ?>
    	<?php while($rs = dbFetchArray($sql) ) : ?>
        <?php 	$status = $rs['status']; ?>
    	<tr>
        	<td><?php echo $rs['id_return_support']; ?></td>
            <td><?php echo $rs['reference']; ?></td>
            <td><?php echo $rs['order_reference']; ?></td>
            <td><?php echo employee_name($rs['id_employee']); ?></td>
            <td align="center"><?php echo number_format($ro->total_return($rs['id_return_support'])); ?></td>
            <td><?php echo thaiDate($rs['date_add']); ?></td>
            <td align="center"><?php if($status == 1 ){ echo "บันทึกแล้ว"; }else if($status == 2 ){ echo "ยกเลิก"; }else{ echo "ยังไม่บันทึก"; } ?></td>
            <td align="right">
            <?php if($edit) : ?>
                <a href='index.php?content=support_return&edit=y&id_return_support=<?php echo $rs['id_return_support']; ?>'><button type='button' class='btn btn-warning btn-sm'><i class='fa fa-pencil'></i></button></a>
			<?php endif; ?>
            <?php if($delete) : ?>
                <button type="button" class="btn btn-danger btn-sm" onclick="delete_return(<?php echo $rs['id_return_support']; ?>, '<?php echo $rs['reference']; ?>')"><i class="fa fa-trash"></i></button>
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
<?php if(isset($_GET['add']) || isset($_GET['edit'])) : ?>
<?php $sql = dbQuery("SELECT tbl_employee.id_employee, first_name, last_name, tbl_support.id_support, id_support_budget, balance, start, end FROM tbl_employee JOIN tbl_support ON tbl_employee.id_employee = tbl_support.id_employee JOIN tbl_support_budget ON tbl_support.id_support = tbl_support_budget.id_support AND tbl_support.year = tbl_support_budget.year WHERE tbl_support.active = 1 AND tbl_support_budget.active =1"); ?>
<?php if(dbNumRows($sql) > 0 ) : ?>
        	<script>
			var SupportList = [
        	<?php while($rs = dbFetchArray($sql)) : ?>
				<?php $today = date("Y-m-d H:i:s"); ?>
				<?php if( $rs['start'] <= $today && $rs['end'] >= $today ) : ?>
            	<?php echo "\"".$rs['id_employee']." : ".$rs['first_name']." ".$rs['last_name']." : "."คงเหลือ"." : ".$rs['balance']."\","; ?>
				<?php endif; ?>
            <?php endwhile; ?>
			];
			</script>
        <?php endif; ?>
<script>
$("#employee").autocomplete({
	source: SupportList,
	autoFocus: true,
	close: function(event,ui){
		var data = $(this).val();
		var arr = data.split(" : ");
		var id = arr[0];
		var name = arr[1];
		var balance = arr[3]
		$("#id_employee").val(id);
		$(this).val(name);
	}
});
</script>
<?php endif; ?>
<script id='template' type="text/x-handlebars-template">
		<tr>
			<td>{{ date_add }}</td>
			<td>{{ product }}</td>
			<td align="right">{{ qty }} <input type="hidden" class="qty" name="row[]" value="{{qty}}" /></td>
			<td align="center">{{ zone }}</td>
			<td align="center">{{ wh }}</td>
			<td align="center">{{#if status}} <i class="fa fa-remove" style="color:red"></i> {{else}} <i class="fa fa-check" style="color:green"></i>{{/if}} </td>
			<td align="right"></td>
		</tr>
</script>


<script id='sum_item' type="text/x-handlebars-template">
	{{#each this}}
		<tr id="row{{id}}">
			<td>{{ date_add }}</td>
			<td id="product{{id}}">{{ product }}</td>
			<td align="right">{{ qty }} <input type="hidden" class="qty" id="qty{{id}}" value="{{qty}}" /></td>
			<td id="zone{{id}}" align="center">{{ zone }}</td>
			<td align="center">{{ wh }}</td>
			<td align="center">{{#if status}} <i class="fa fa-check" style="color:green"></i> {{ else }} <i class="fa fa-remove" style="color:red"></i> {{/if}} </td>
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

$("#date_add").datepicker({ dateFormat: 'dd-mm-yy' });
function total_qty()
{
		var qty = 0;
    $(".qty").each(function(index, element) {
        n = parseInt($(this).val());
		qty += n;
    });
	$("#total_qty").html(qty);
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

function add()
{
	load_in();
	var order_reference 	= $("#order_reference").val();
	var id_employee		= $("#id_employee").val();
	if( order_reference != "" && id_employee != "" )
	{
		$.ajax({
			url:"controller/returnController.php?check_support_order",
			type:"POST", cache:false, data: {"reference" : order_reference, "id_employee" : id_employee},
			success: function(rs)
			{
				if(rs == "ok")
				{
					$("#add_form").submit();
					load_out();
				}else{
					load_out();
					swal("รหัสอ้างอิงไม่ตรงกับชื่อผู้เบิก", "เลขที่บิลอาจผิด หรือ พนักงานคนนี้ไม่มีส่วนเกี่ยวข้องกับเลขที่บิลนี้", "error");
				}
			}
		});
	}else{
		load_out();
		swal("ยังไม่ได้เลือกผู้คืน หรือ ยังไม่ได้ระบุเอกสารอ้างอิง");
	}
}

function edit()
{
	$("#date_add").removeAttr("disabled");
	$("#employee").removeAttr("disabled");
	$("#order_reference").removeAttr("disabled");
	$("#remark").removeAttr("disabled");
	$("#btn_edit").css("display","none");
	$("#btn_update").css("display","");
}

function update(id)
{
	load_in();
	var date = $("#date_add").val();
	var id_employee = $("#id_employee").val();
	var reference = $("#order_reference").val();
	var remark	= $("#remark").val();
	$.ajax({
		url:"controller/returnController.php?check_support_order",type:"POST", cache:false,
		data:{ "id_employee" : id_employee, "reference" : reference },
		success: function(rs)
		{
			if(rs == "ok")
			{
				$.ajax({
					url:"controller/returnController.php?update", type:"POST", cache:false,
					data:{ "id_return_support" : id, "date_add" : date, "id_employee" : id_employee, "reference" : reference, "remark" : remark },
					success: function(ra)
					{
						if(ra == "success")
						{
							load_out();
							$("#date_add").attr("disabled", "disabled");
							$("#employee").attr("disabled", "disabled");
							$("#order_reference").attr("disabled", "disabled");
							$("#remark").attr("disabled", "disabled");
							$("#btn_update").css("display","none");
							$("#btn_edit").css("display","");
							swal({ title: "เรียบร้อย", text: "ปรับปรุงข้อมูลเรียบร้อยแล้ว", timer: 1000, type: "success"});
						}else{
							load_out();
							swal("ไม่สำเร็จ", "ปรับปรุงข้อมูลไม่สำเร็จ ลองใหม่อีกครั้ง", "error");
						}
					}
				});
			}else{
				load_out();
				swal("รหัสอ้างอิงไม่ตรงกับผู้เบิก", "เลขที่บิลอาจผิด หรือ พนักงานคนนี้ไม่มีส่วนเกี่ยวข้องกับเลขที่บิลนี้", "error");
			}
		}
	});
}

function add_item()
{
	load_in();
	var id_return_support 	= $("#id_return_support").val();
	var id_zone 			= $("#id_zone").val();
	var barcode 			= $("#barcode_item").val();
	var qty					= $("#qty").val();
	if( id_zone == "")
	{
		load_out();
		swal("คุณยังไม่ได้ระบุโซน");
	}else if(barcode == ""){
		load_out();
		swal("คุณยังไม่ได้ระบุบาร์โค้ดสินค้า");
	}else{
		$.ajax({
			url: "controller/returnController.php?add_item",type:"POST", cache:false,
			data: {"id_return_support" : id_return_support, "id_zone" : id_zone, "barcode" : barcode, "qty" : qty },
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
	var id_return_support 	= $("#id_return_support").val();
	$.ajax({
		url: "controller/returnController.php?sum_item&id_return_support="+id_return_support, type:"POST", cache:false,
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
			url:"controller/returnController.php?delete_row&id_return_support_detail="+id, type:"GET", cache:false,
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
			url:"controller/returnController.php?delete_return&id_return_support="+id, type:"GET", cache:false,
			success: function(rs)
			{
				if(rs == "success")
				{
					load_out();
					swal({ title: "สำเร็จ", text: "ลบรายการเรียบร้อยแล้ว", timer: 1500, type: "success"});
					window.location.reload();
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
	var id_return_support = $("#id_return_support").val();
	$.ajax({
		url:"controller/returnController.php?save_add&id_return_support="+id_return_support,
		type:"GET", cache:false,
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs == "success" )
			{
				load_out();
				swal({title : "สำเร็จ", text : "บันทึกรายการเรียบร้อยแล้ว", timer : 2000, type : "success"});
				window.location.href = "index.php?content=support_return&edit=Y&id_return_support="+id_return_support;
			}else{
				load_out();
				swal("ไม่สำเร็จ !!", "บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}
		}
	});
}

function save_edit()
{
	load_in();
	var id_return_support = $("#id_return_support").val();
	$.ajax({
		url:"controller/returnController.php?save_edit&id_return_support="+id_return_support,
		type:"GET", cache:false,
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs == "success" )
			{
				load_out();
				swal({title : "สำเร็จ", text : "บันทึกรายการเรียบร้อยแล้ว", timer : 2000, type : "success"});
				window.location.reload();

			}else{
				load_out();
				swal("ไม่สำเร็จ !!", "บันทึกข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}
		}
	});
}

$("#search-btn").click(function(e) {
    $("#form").submit();
});
</script>
