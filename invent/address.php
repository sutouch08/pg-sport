<?php 
	$page_menu = "invent_address";
	$page_name = "เพิ่ม/แก้ไข ที่อยู่ลูกค้า";
	$id_tab = 22;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
  	include 'function/transport_helper.php';
	?>    
<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-lg-6" style="margin-top:10px;"><h4 class="title"><i class="fa fa-home"></i>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
    	<p class="pull-right" style="margin-bottom:0px;">
        <?php if( isset( $_GET['add'] ) OR isset( $_GET['edit'] ) OR isset( $_GET['view_detail'] ) ) : ?>
        	<button type="button" class="btn btn-warning btn-sm" onClick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
        <?php endif; ?>
        <?php if( !isset( $_GET['add'] ) && !isset( $_GET['edit'] ) && !isset( $_GET['view_detail'] ) ) : ?>
        	<?php if( $add ) : ?>
        	<button type="button" class="btn btn-success btn-sm" onClick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
            <?php endif; ?>
        <?php endif; ?>
        <?php if( isset( $_GET['add'] ) ) : ?>
        	<?php if( $add ) : ?>
            <button type="button" class="btn btn-success btn-sm" onClick="save()"><i class="fa fa-save"></i> บันทึก</button>
            <?php endif; ?>
        <?php endif; ?>
        <?php if( isset( $_GET['edit'] ) && isset( $_GET['id_address'] ) ) : ?>
        	<?php if( $edit ) : ?>
            <button type="button" class="btn btn-success btn-sm" onClick="saveEdit(<?php echo $_GET['id_address']; ?>)"><i class="fa fa-save"></i> บันทึก</button>
            <?php endif; ?>
        <?php endif; ?>
        </p>
    </div>
</div>
<hr style="margin-bottom:10px;" />
<!-- End page place holder -->
<?php if( isset( $_GET['add'] ) ) : ?>
	<form id="addForm">
	<div class="row">
    	<div class="col-lg-4 col-lg-offset-4">
        	<label class="input-label">ลูกค้า</label>
            <input type="text" class="form-control input-sm" id="customer" name="customer" placeholder="เลือกลูกค้า" />
            <input type="hidden" name="id_customer" id="id_customer" />
            <span class="required">*</span>
        </div>
        <div class="col-lg-4">
        	<span style="display:block;"><span style="color: red; margin-left:100px;">* </span> &nbsp; ช่องที่จำเป็นต้องกรอก</span>
            <span><span style="color:red; margin-left:100px;">* </span> &nbsp; จำเป็นต้องกรอกอย่างน้อย 1 ช่อง </span>
        </div>
        
    	<div class="col-lg-2 col-lg-offset-4">
        	<label class="input-label">ชื่อ</label>
            <input type="text" class="form-control input-sm" id="fname" name="fname" placeholder="ชื่อผู้ติดต่อ" />
            <span class="required">**</span>
        </div>
        <div class="col-lg-2">
        	<label class="input-label">นามสกุล</label>
            <input type="text" class="form-control input-sm" id="lname" name="lname" placeholder="นามสกุลผู้ติดต่อ" />
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label class="input-label">บริษัท/ห้าง/ร้าน</label>
            <input type="text" class="form-control input-sm" id="company" name="company" placeholder="ชื่อ บริษัท/ห้าง/ร้าน" />
            <span class="required">**</span>
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label class="input-label">ที่อยู่</label>
            <input type="text" class="form-control input-sm" id="address1" name="address1" placeholder="เลขที่ อาคาร ถนน หมู่บ้าน" />
            <span class="required">*</span>
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label class="input-label">ที่อยู่บรรทัด 2</label>
            <input type="text" class="form-control input-sm" id="address2" name="address2" placeholder="ตำบล อำเภอ" />
        </div>
        <div class="col-lg-2 col-lg-offset-4">
        	<label class="input-label">จังหวัด</label>
            <select class="form-control input-sm" id="city" name="city">
            	<?php echo selectCity(); ?>
            </select>
            <span class="required">*</span>
        </div>
        <div class="col-lg-2">
        	<label class="input-label">รหัสไปรษณีย์</label>
            <input type="text" class="form-control input-sm" id="postcode" name="postcode"  />
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label class="input-label">เบอร์โทรศัพท์</label>
            <input type="text" class="form-control input-sm" id="phone" name="phone" placeholder="เช่น 081-234-5678, 082-345-6789" />
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label class="input-label">ชื่อแทน</label>
            <input type="text" class="form-control input-sm" id="alias" name="alias" placeholder="เช่น ที่ทำงาน บ้าน หรือ ที่อยู่ของฉัน เป็นต้น" />
            <span class="required">*</span>
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label class="input-label">หมายเหตุ</label>
            <textarea class='form-control input-sm' name='remark' id='remark' rows='8' placeholder="ใส่หมายเหตุ (ถ้ามี) เช่น ร้านปิด 17.00 ต้องไปก่อนร้านปิด เป็นต้น"></textarea>
        </div>
        
         <div class="col-lg-4 col-lg-offset-4">
        	<h3></h3>
        </div>
    </div>
    
    </form>
	
<?php elseif( isset( $_GET['edit'] ) ) : ?>
	<?php if( !isset( $_GET['id_address'] ) ) : ?>
    	<h3 style="width:100%; text-align:center;">--- เกิดข้อผิดพลาด กรุณาออกแล้วลองใหม่อีกครั้ง ---</h3>
    <?php else : ?>
    <?php $ad = new address($_GET['id_address']); ?>
	<form id="editForm">
	<div class="row">
    	<div class="col-lg-4 col-lg-offset-4">
        	<label class="input-label">ลูกค้า</label>
            <input type="text" class="form-control input-sm" id="customer" name="customer" placeholder="เลือกลูกค้า" value="<?php echo customer_name($ad->id_customer); ?>" disabled />
            <input type="hidden" name="id_customer" id="id_customer" value="<?php echo $ad->id_customer; ?>" />
            <span class="required">*</span>
        </div>
        <div class="col-lg-1">
        	<label class="input-label" style="visibility:hidden;">ลูกค้า</label>
        	<button type="button" class="btn btn-default btn-sm" onClick="activeCustomerField()">เปลี่ยนลูกค้า</button>
        </div>
        <div class="col-lg-3">
        	<span style="display:block;"><span style="color: red; margin-left:10px;">* </span> &nbsp; ช่องที่จำเป็นต้องกรอก</span>
            <span><span style="color:red; margin-left:10px;">* </span> &nbsp; จำเป็นต้องกรอกอย่างน้อย 1 ช่อง </span>
        </div>
        
    	<div class="col-lg-2 col-lg-offset-4">
        	<label class="input-label">ชื่อ</label>
            <input type="text" class="form-control input-sm" id="fname" name="fname" placeholder="ชื่อผู้ติดต่อ" value="<?php echo $ad->first_name; ?>" />
            <span class="required">**</span>
        </div>
        <div class="col-lg-2">
        	<label class="input-label">นามสกุล</label>
            <input type="text" class="form-control input-sm" id="lname" name="lname" placeholder="นามสกุลผู้ติดต่อ" value="<?php echo $ad->last_name; ?>" />
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label class="input-label">บริษัท/ห้าง/ร้าน</label>
            <input type="text" class="form-control input-sm" id="company" name="company" placeholder="ชื่อ บริษัท/ห้าง/ร้าน" value="<?php echo $ad->company; ?>" />
            <span class="required">**</span>
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label class="input-label">ที่อยู่</label>
            <input type="text" class="form-control input-sm" id="address1" name="address1" placeholder="เลขที่ อาคาร ถนน หมู่บ้าน" value="<?php echo $ad->address1; ?>" />
            <span class="required">*</span>
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label class="input-label">ที่อยู่บรรทัด 2</label>
            <input type="text" class="form-control input-sm" id="address2" name="address2" placeholder="ตำบล อำเภอ" value="<?php echo $ad->address2; ?>" />
        </div>
        <div class="col-lg-2 col-lg-offset-4">
        	<label class="input-label">จังหวัด</label>
            <select class="form-control input-sm" id="city" name="city">
            	<?php echo selectCity($ad->city); ?>
            </select>
            <span class="required">*</span>
        </div>
        <div class="col-lg-2">
        	<label class="input-label">รหัสไปรษณีย์</label>
            <input type="text" class="form-control input-sm" id="postcode" name="postcode" value="<?php echo $ad->postcode; ?>"  />
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label class="input-label">เบอร์โทรศัพท์</label>
            <input type="text" class="form-control input-sm" id="phone" name="phone" placeholder="เช่น 081-234-5678, 082-345-6789" value="<?php echo $ad->phone; ?>" />
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label class="input-label">ชื่อแทน</label>
            <input type="text" class="form-control input-sm" id="alias" name="alias" placeholder="เช่น ที่ทำงาน บ้าน หรือ ที่อยู่ของฉัน เป็นต้น" value="<?php echo $ad->alias; ?>" />
            <span class="required">*</span>
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label class="input-label">หมายเหตุ</label>
            <textarea class='form-control input-sm' name='remark' id='remark' rows='8' placeholder="ใส่หมายเหตุ (ถ้ามี) เช่น ร้านปิด 17.00 ต้องไปก่อนร้านปิด เป็นต้น"><?php echo $ad->remark; ?></textarea>
        </div>
        
         <div class="col-lg-4 col-lg-offset-4">
        	<h3></h3>
        </div>
    </div>
    </form>
    <?php endif; ?>

<?php elseif( isset( $_GET['view_detail'] ) && isset( $_GET['id_address'] ) ) : ?>

<?php else : ?>
	<?php if( isset($_POST['cus_search']) ){ $cus_search = $_POST['cus_search']; }else if( isset($_COOKIE['cus_search']) ){ $cus_search = $_COOKIE['cus_search']; }else{ $cus_search = ''; } ?>
	<?php if( isset($_POST['ad_search']) ){ $ad_search = $_POST['ad_search']; }else if( isset($_COOKIE['ad_search']) ){ $ad_search = $_COOKIE['ad_search']; }else{ $ad_search = ''; } ?>
    <?php if( isset($_POST['city_search']) ){ $city_search = $_POST['city_search']; }else if( isset($_COOKIE['city_search']) ){ $city_search = $_COOKIE['city_search']; }else{ $city_search = ''; } ?>
 
<form id="searchFrom" method="post">
<div class="row">
	<div class="col-lg-3">
    	<div class="input-group">
        	<span class="input-group-addon">ลูกค้า</span>
            <input type="text" class="form-control input-sm" name="cus_search" value="<?php echo $cus_search; ?>" />
        </div>
    </div>
    <div class="col-lg-3">
    	<div class="input-group">
        	<span class="input-group-addon">ที่อยู่</span>
            <input type="text" class="form-control input-sm" name="ad_search" value="<?php echo $ad_search; ?>" />
        </div>
    </div>
    <div class="col-lg-3">
    	<div class="input-group">
        	<span class="input-group-addon">จังหวัด</span>
            <input type="text" class="form-control input-sm" name="city_search" value="<?php echo $city_search; ?>" />
        </div>
    </div>
    <div class="col-lg-2">
    	<button type="button" class="btn btn-primary btn-sm btn-block" onClick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
    </div>
    <div class="col-lg-1">
    	<button type="button" class="btn btn-warning btn-sm btn-block" onClick="clearFilter()"><i class="fa fa-retweet"></i> รีเซ็ต</button>
    </div>
</div>
</form>
<hr style="margin-top:10px; margin-bottom:10px;" />
<?php
	$qr = 'WHERE id_address != 0 ';
	if( $cus_search != '' )
	{
		setcookie('cus_search', $cus_search, time()+3600, '/');
		$in = customer_in($cus_search);	
		if( $in !== FALSE )
		{
			$qr .= "AND id_customer IN(".$in.") ";
		}
	}
	if( $ad_search != '' )
	{
		setcookie('ad_search', $ad_search, time()+3600, '/');
		$qr .= "AND (address1 LIKE '%".$ad_search."%' OR address2 LIKE '%".$ad_search."%') ";
	}
	if( $city_search != '' )
	{
		$qr .= "AND city LIKE '%".$city_search."%' ";
	}
	$qr .= "ORDER BY id_address DESC";
	
	$paginator = new paginator();
	if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows']; $paginator->setcookie_rows($get_rows); }else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
	$paginator->Per_Page("tbl_address",$qr,$get_rows);
	$Page_Start = $paginator->Page_Start;
	$Per_Page = $paginator->Per_Page;
	
?>
<?php $paginator->display($get_rows, 'index.php?content=address'); ?>
<div class="row">
	<div class="col-lg-12">
    	<table class="table table-striped" style="border: solid 1px #CCC;">
        <thead style="font-size:12px;">
        	<th style="width:5%; text-align:center;">ลำดับ</th>
            <th style="width:20%;">ลูกค้า</th>
            <th style="width:10%;">ชื่อแทน</th>
            <th style="width:35%;">ที่อยู่</th>
            <th style="width:10%;">จังหวัด</th>
            <th style="width:10%;">เบอร์โทร</th>
            <th style="width:10%;"></th>
        </thead>
    <?php $qs = dbQuery("SELECT * FROM tbl_address ".$qr." LIMIT ".$Page_Start.", ".$Per_Page);  ?>
    <?php if( dbNumRows($qs) > 0 ) : ?>
    	<?php $rows = isset( $_COOKIE['get_rows'] ) ? $_COOKIE['get_rows'] : 50; ?>
    	<?php $n = isset( $_GET['Page'] ) ? (($_GET['Page'] -1) * $rows)+1 : 1; ?>
        <?php while( $rs = dbFetchArray($qs) ) : ?>
        <?php 	$id = $rs['id_address']; ?>
        	<tr id="row_<?php echo $id; ?>" style="font-size:12px;">
            	<td align="center"><?php echo $n; ?></td>
                <td><?php echo $rs['company'] != '' ? $rs['company'] : $rs['first_name'].' '.$rs['last_name']; ?></td>
                <td><?php echo $rs['alias']; ?></td>
                <td><?php echo $rs['address1']. ' '. $rs['address2']; ?></td>
                <td><?php echo $rs['city']; ?></td>
                <td><?php echo $rs['phone']; ?></td>
                <td align="right">
                	<button type="button" class="btn btn-info btn-xs" onClick="getInfo(<?php echo $id; ?>)"><i class="fa fa-eye"></i></button>
				<?php if( $edit ) : ?>
                	<button type="button" class="btn btn-warning btn-xs" onClick="getEdit(<?php echo $id; ?>)"><i class="fa fa-pencil"></i></button>
                <?php endif; ?>                    
                <?php if( $delete ) : ?>
                	<button type="button" class="btn btn-danger btn-xs" onClick="deleteRow(<?php echo $id; ?>)"><i class="fa fa-trash"></i></button>
                <?php endif; ?>
                </td>
            </tr>
            <?php $n++; ?>
        <?php endwhile; ?>
    <?php else : ?>
    	<tr><td colspan="7" align="center"><h3>--- ไม่พบข้อมูล  ---</h3></td></tr>
    <?php endif; ?>
    	</table>
      
    </div>
</div><!--/ row -->


<div class='modal fade' id='addressInfo' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal_info' style="width:500px;">
		<div class='modal-content'>
  			<div class='modal-header'>	</div>
			 <div class='modal-body' id='info_body'></div>
			 <div class='modal-footer'>
             	<button type='button' class='btn btn-default' data-dismiss='modal' aria-hidden='true'>ปิด</button>
			 </div>
		</div>
	</div>
</div>
<?php endif; ?>
</div><!--/ container -->
<script id='info_template' type="text/x-handlebars-template">
<div class="row">
	<div class="col-lg-12">
    <table class="table table-bordered table-striped" style="margin-bottom:0px;">
    	<tr><td width="25%">ชื่อแทน</td><td>{{ alias }}</td></tr>
		<tr><td width="25%">บริษัท/ร้าน</td><td>{{ company }}</td></tr>
		<tr><td width="25%">ผู้ติดต่อ</td><td>{{ customer }}</td></tr>
        <tr><td width="25%">ที่อยู่</td><td>{{ address }}</td></tr>
		<tr><td width="25%">จังหวัด</td><td>{{ city }}</td></tr>
		<tr><td width="25%">รหัสไปรษณีย์</td><td>{{ postcode }}</td></tr>
        <tr><td width="25%">เบอร์โทร</td><td>{{ phone }}</td></tr>
		<tr><td width="25%">หมายเหตุ</td><td>{{ remark }}</td></tr>
    </table>
    </div>
</div>
</script>
<script>
function getInfo(id)
{
	load_in();
	$.ajax({
		url:"controller/addressController.php?getAddressInfo",
		type: "GET", cache:"false", data:{ "id_address" : id },
		success: function(rs){
			load_out();
			var source 	= $("#info_template").html();
			var data 		= $.parseJSON(rs);
			var output	= $("#info_body");
			render(source, data, output);
			$("#addressInfo").modal('show');
		}
	});				
}

function deleteRow(id)
{
	swal({
		  title: "ต้องการลบที่อยู่ ?",
		  text: "โปรดจำไว้ว่า การกระทำนี้จะไม่สามารถกู้คืนได้ ",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "ใช่ ลบเลย",
		  cancelButtonText: "ยกเลิก",
		  closeOnConfirm: false,
		}, function(){
			load_in();
			$.ajax({
				url:"controller/addressController.php?deleteAddress&id_address="+id,
				type:"GET", cache:"false",
				success: function(rs)
				{
					var rs = $.trim(rs);
					if(rs == "success")
					{
						load_out();						
						swal({ title: "เรียบร้อย", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success"});
						setTimeout(function(){ window.location.reload(); }, 1500);
					}else{
						load_out();
						swal("ไม่สำเร็จ", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
					}
				}
			});						
	});
}

function save()
{
	var id_cus	= $('#id_customer').val();
	var cus		= $('#customer').val();
	var fname	= $('#fname').val();
	var cname	= $('#company').val();
	var adr1		= $('#address1').val();
	var city		= $('#city').val();
	var alias		= $('#alias').val();
	
	if( id_cus == '' || cus == ''){ swal('ชื่อลูกค้าไม่ถูกต้อง'); return false; }
	if( fname == '' && cname == '' ){ swal('ข้อมูลไม่ครบ !', 'คุณต้องระบุชื่อผู้ติดต่อ หรือ ชื่อบริษัท/ห้าง/ร้าน อย่างน้อย 1 ช่อง หรือ ทั้ง 2 ช่อง', 'warning'); return false; }	
	if( adr1 == '' ){ swal('กรุณาระบุที่อยู่'); return false; }
	if( city == '' ){ swal('กรุณาเลือกจังหวัด'); return false; }
	if( alias == '' ){ swal('กรุณากำหนดชื่อแทน'); return false; }
	load_in();
	$.ajax({
		url:"controller/addressController.php?insertAddress",
		type:"POST", cache:"false", data: $('#addForm').serialize(),
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({
					title: 'สำเร็จ',
					text: 'เพิ่มที่อยู่ใหม่เรียบร้อยแล้ว ต้องการเพิ่มที่อยู่อื่นอีกหรือไม่ ? ',
					showCancelButton: true,
					cancelButtonText: 'ไม่ต้องการ',
					confirmButtonText: 'ใช่ ฉันต้องการเพิ่มอีก',
					closeOnConfirm: true,
					type: 'success'
				}, function(isConfirm){
					if( isConfirm ){
						addNew();
					}else{
						goBack();
					}
				});
			}else{
				swal('ไม่สำเร็จ !!', 'เพิ่มที่อยู่ไม่สำเร็จ กรุณาตรวจสอบข้อมูล แล้วลองใหม่อีกครั้ง', 'error');
			}
		}
	});
}

function saveEdit(id)
{
	var id_cus	= $('#id_customer').val();
	var cus		= $('#customer').val();
	var fname	= $('#fname').val();
	var cname	= $('#company').val();
	var adr1		= $('#address1').val();
	var city		= $('#city').val();
	var alias		= $('#alias').val();
	
	if( id_cus == '' || cus == ''){ swal('ชื่อลูกค้าไม่ถูกต้อง'); return false; }
	if( fname == '' && cname == '' ){ swal('ข้อมูลไม่ครบ !', 'คุณต้องระบุชื่อผู้ติดต่อ หรือ ชื่อบริษัท/ห้าง/ร้าน อย่างน้อย 1 ช่อง หรือ ทั้ง 2 ช่อง', 'warning'); return false; }	
	if( adr1 == '' ){ swal('กรุณาระบุที่อยู่'); return false; }
	if( city == '' ){ swal('กรุณาเลือกจังหวัด'); return false; }
	if( alias == '' ){ swal('กรุณากำหนดชื่อแทน'); return false; }
	load_in();
	$.ajax({
		url:"controller/addressController.php?updateAddress&id_address="+id,
		type:"POST", cache:"false", data: $('#editForm').serialize(),
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title: "สำเร็จ", text: "แก้ไขที่อยู่เรียบร้อยแล้ว", timer: 1000, type: "success"});
				setTimeout(function(){ goBack(); }, 1500);
			}else{
				swal('ไม่สำเร็จ !!', 'แก้ไขที่อยู่ไม่สำเร็จ กรุณาตรวจสอบข้อมูล แล้วลองใหม่อีกครั้ง', 'error');
			}
		}
	});
}

$("#customer").autocomplete({
	source: 'controller/autoComplete.php?get_customer',
	autoFocus:true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 3 ){
			$("#id_customer").val(arr[2]);
			$(this).val(arr[1]);
		}else{
			$(this).val('');
			$("#id_customer").val('');
		}
	}
});

function getSearch()
{
	$("#searchFrom").submit();	
}

function clearFilter()
{
	$.ajax({
		url:"controller/addressController.php?clearFilter",
		type:"GET",cache:"false", success: function(rs){
			goBack();
		}
	});
}
function goBack()
{
	window.location.href = 'index.php?content=address';	
}
function addNew()
{
	window.location.href = 'index.php?content=address&add=y';	
}
function getEdit(id)
{
	window.location.href = 'index.php?content=address&edit&id_address='+id;	
}
function activeCustomerField()
{
	$("#customer").removeAttr('disabled');
	$("#customer").focus();	
}
	
</script>