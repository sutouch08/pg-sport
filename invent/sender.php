<?php 
	$page_name = "ผู้ให้บริการจัดส่ง";
	$id_tab = 55;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
	include 'function/date_helper.php';
	?>

<style> 
label { margin-top: 10px; } 
.input-medium { 
	width: 300px;
}
.input-small {
	width: 150px;
}
.input-label { 
	margin-top:0px;
}
hr { margin-top:5px; margin-bottom:5px; }
</style>    
<div class="container">
	<div class="row" style="height:30px;">
        <div class="col-lg-6" style="margin-top:10px;">
        	<h4 class="title"><i class="fa fa-truck"></i> <?php echo $page_name; ?></h4>
        </div>
        <div class="col-lg-6">
        	<p class="pull-right" style="margin-bottom:0px;">
        <?php if( isset( $_GET['add'] ) || isset( $_GET['edit'] ) ) : ?>
        	<button type="button" class="btn btn-warning btn-sm" onClick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
        <?php endif; ?>
		<?php if( isset( $_GET['add'] ) ) :	?>
        	<?php if( $add ) : ?>
        		<button type="button" class="btn btn-success btn-sm" onClick="save()"><i class="fa fa-save"></i> บันทึก</button>
			<?php endif; ?>             
        <?php endif; ?>   
        <?php if( isset( $_GET['edit'] ) && isset( $_GET['id_sender'] ) ) : ?>
            <?php if( $edit ) : ?>
            	<button type="button" class="btn btn-success btn-sm" onClick="saveEdit(<?php echo $_GET['id_sender']; ?>)"><i class="fa fa-save"></i> บันทึก</button>
            <?php endif; ?>
        <?php endif; ?> 
        <?php if( !isset( $_GET['add'] ) && !isset( $_GET['edit'] ) ) : ?>
        	<?php if( $add ) : ?>
            	<button type="button" class="btn btn-success btn-sm" onClick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
            <?php endif; ?>
        <?php endif; ?>            
            </p>
		</div>       
    </div>
    <hr/>
<?php if( isset( $_GET['add'] ) ) : ?>    
	<div class="row">
	<form id="addFrom">
        <div class="col-lg-4 col-lg-offset-4">
        	<label>ชื่อ</label>
            <input type="text" class="form-control input-sm" id="name" name="name" placeholder="กำหนดชื่อผู้ให้บริการ (จำเป็นต้องกรอก)" />
            <span style="color:red; display:inline; float:right; margin-right: -15px; margin-top:-25px;" >*</span>
        </div>
        <div class="col-lg-1"></div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label>ที่อยู่1</label>
            <input type="text" class="form-control input-sm" id="address1" name="address1" placeholder="สถานที่ตั้งสำหนักงาน"/>
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label>ที่อยู่2</label>
            <input type="text" class="form-control input-sm" id="address2" name="address2" placeholder="สถานที่ตั้งสำหนักงาน"/>
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label>เบอร์โทร</label>
            <input type="text" class="form-control input-sm" id="phone" name="phone" placeholder="เบอร์โทรศัพท์" />
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label style="display:block;">เวลาทำการ</label>
            <select class="form-control input-sm input-small" id="open" name="open" style="display:inline-block;">
            <?php echo dateSelect(); ?>
            </select>
            <span> - </span>
            <select class="form-control input-sm input-small" id="close" name="close" style="display:inline-block;">
            <?php echo dateSelect(); ?>
            </select>
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label>เงื่อนไข</label>
            <select class="form-control input-sm input-medium" id="type" name="type">
            	<option value="เก็บเงินปลายทาง">เก็บเงินปลายทาง</option>
                <option value="เก็บเงินต้นทาง">เก็บเงินต้นทาง</option>
            </select>
        </div>
	</form>
	</div><!--/ Row -->
<?php elseif( isset( $_GET['edit'] ) ) : ?>
	<?php if( !isset( $_GET['id_sender'] ) ) : ?>
    <h3 style="width:100%; text-align:center;">--- เกิดข้อผิดพลาด กรุณาออกแล้วลองใหม่อีกครั้ง ---</h3>
    <?php else : ?>
    <?php $id = $_GET['id_sender']; ?>
    <?php $qs = dbQuery("SELECT * FROM tbl_sender WHERE id_sender = ".$id); ?>
    <?php if( dbNumRows($qs) == 1 ) : ?>
    <?php 	$rs = dbFetchArray($qs); ?>
	<div class="row">
	<form id="editFrom">
        <div class="col-lg-4 col-lg-offset-4">
        	<label>ชื่อ</label>
            <input type="text" class="form-control input-sm" id="name" name="name" placeholder="กำหนดชื่อผู้ให้บริการ (จำเป็นต้องกรอก)" value="<?php echo $rs['name']; ?>" />
            <span style="color:red; display:inline; float:right; margin-right: -15px; margin-top:-25px;" >*</span>
        </div>
        <div class="col-lg-1"></div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label>ที่อยู่1</label>
            <input type="text" class="form-control input-sm" id="address1" name="address1" placeholder="สถานที่ตั้งสำหนักงาน" value="<?php echo $rs['address1']; ?>" />
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label>ที่อยู่2</label>
            <input type="text" class="form-control input-sm" id="address2" name="address2" placeholder="สถานที่ตั้งสำหนักงาน" value="<?php echo $rs['address2']; ?>" />
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label>เบอร์โทร</label>
            <input type="text" class="form-control input-sm" id="phone" name="phone" placeholder="เบอร์โทรศัพท์" value="<?php echo $rs['phone']; ?>" />
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label style="display:block;">เวลาทำการ</label>
            <select class="form-control input-sm input-small" id="open" name="open" style="display:inline-block;">
            <?php echo dateSelect($rs['open']); ?>
            </select>
            <span> - </span>
            <select class="form-control input-sm input-small" id="close" name="close" style="display:inline-block;">
            <?php echo dateSelect($rs['close']); ?>
            </select>
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label>เงื่อนไข</label>
            <select class="form-control input-sm input-medium" id="type" name="type">
            	<option value="เก็บเงินปลายทาง" <?php echo isSelected($rs['type'], 'เก็บเงินปลายทาง'); ?>>เก็บเงินปลายทาง</option>
                <option value="เก็บเงินต้นทาง" <?php echo isSelected($rs['type'], 'เก็บเงินต้นทาง'); ?>>เก็บเงินต้นทาง</option>
            </select>
        </div>
	</form>
	</div><!--/ Row -->
    	<?php endif; ?>
    <?php endif; ?>
<?php else : ?>

<?php if( isset( $_POST['name_search'] ) ){ $name_search = $_POST['name_search']; }else if( isset( $_COOKIE['name_search'] ) ){ $name_search = $_COOKIE['name_search']; }else{ $name_search = ''; } ?>
<?php if( isset($_POST['ad_search']) ){ $ad_search = $_POST['ad_search']; }else if( isset($_COOKIE['ad_search']) ){ $ad_search = $_COOKIE['ad_search']; }else{ $ad_search = ''; } ?>
<?php if( isset($_POST['phone_search']) ){ $phone_search = $_POST['phone_search']; }else if( isset($_COOKIE['phone_search']) ){ $phone_search = $_COOKIE['phone_search']; }else{ $phone_search = ''; } ?>
<?php if( isset($_POST['type_search']) ){ $type_search = $_POST['type_search']; }else if( isset($_COOKIE['type_search']) ){ $type_search = $_COOKIE['type_search']; }else{ $type_search = ''; } ?>

<form id="searchForm" method="post">
<div class="row">
    <div class="col-lg-2">
    	<label class="input-label">ผู้จัดส่ง</label>
        <input type="text" class="form-control input-sm" id="name_search" name="name_search" value="<?php echo $name_search; ?>" />
    </div>
    <div class="col-lg-2">
    	<label class="input-label">ที่อยู่</label>
        <input type="text" class="form-control input-sm" id="ad_search" name="ad_search" value="<?php echo $ad_search; ?>" />
    </div>
    <div class="col-lg-2">
    	<label class="input-label">เบอร์โทร</label>
        <input type="text" class="form-control input-sm" id="phone_search" name="phone_search" value="<?php echo $phone_search; ?>" />
    </div>
    <div class="col-lg-2">
    	<label class="input-label">เงื่อนไข</label>
        <select name="type_search" class="form-control input-sm">
        	<option value="">ทั้งหมด</option>
            <option value="เก็บเงินปลายทาง" <?php echo isSelected($type_search, 'เก็บเงินปลายทาง'); ?> >เก็บเงินปลายทาง</option>
            <option value="เก็บเงินต้นทาง" <?php echo isSelected($type_search, 'เก็บเงินต้นทาง'); ?>>เก็บเงินต้นทาง</option>
        </select>
    </div>
    <div class="col-lg-2">
    	<label class="input-label" style="display:block; visibility:hidden;">button</label>
        <button type="button" class="btn btn-primary btn-sm btn-block" onClick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
    </div>
     <div class="col-lg-2">
    	<label class="input-label" style="display:block; visibility:hidden;">button</label>
        <button type="button" class="btn btn-warning btn-sm btn-block" onClick="clearFilter()"><i class="fa fa-retweet"></i> รีเซ็ต</button>
    </div>
</div>
</form>
<hr style="margin-top:10px;"/>
<?php 
	$qr = "WHERE id_sender != 0 ";
	if( $name_search !='' ){
		setcookie('name_search', $name_search, time()+3600, '/');
		$qr .= "AND name LIKE '%".$name_search."%' ";
	}
	if( $ad_search != ''){
		setcookie('ad_search', $ad_search, time()+3600, '/');
		$qr .= "AND (address1 LIKE '%".$ad_search."%' OR address2 LIKE '%".$ad_search."%') ";
	}
	if( $phone_search != ''){
		setcookie('phone_search', $phone_search, time()+3600, '/');
		$qr .= "AND phone LIKE '%".$phone_search."%' ";
	}
	if( $type_search != ''){
		setcookie('type_search', $type_search, time()+3600, '/');
		$qr .= "AND type = '".$type_search."' ";
	}
	$qr .= "ORDER BY id_sender DESC";		
	$paginator = new paginator();
	if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		$paginator->Per_Page("tbl_sender",$qr,$get_rows);
		
		$Page_Start = $paginator->Page_Start;
		$Per_Page = $paginator->Per_Page;
?>
<?php $qs = dbQuery("SELECT * FROM tbl_sender ".$qr." LIMIT ".$Page_Start.", ".$Per_Page); ?>
<div class="row">
	<div class="col-lg-12">
    	<table class="table table-striped" style="border:solid 1px #ccc;">
        	<thead>
            	<tr style="font-size:12px;">
                	<th style="width:3%; text-align:center;">ลำดับ</th>
                    <th style="width:20%;">ชื่อผู้จัดส่ง</th>
                    <th style="width:35%">ที่อยู่</th>
                    <th style="width:15%;">เบอร์โทรศัพท์</th>
                    <th style="width:10%;">เวลาทำการ</th>
                    <th style="width:10%;">เงื่อนไข</th>
                    <th></th>
                </tr>
            </thead>
        <?php $n = 1; ?>
		<?php while( $rs = dbFetchArray($qs) ) : ?>
        <?php 	$id = $rs['id_sender']; ?>
        	<tr id="<?php echo $id; ?>" style="font-size:10px;">
            	<td align="center"><?php echo $n; ?></td>
                <td><?php echo $rs['name']; ?></td>
                <td><?php echo $rs['address1'].' '.$rs['address2']; ?></td>
                <td><?php echo $rs['phone']; ?></td>
                <td><?php echo date('H:i', strtotime($rs['open'])); ?> - <?php echo date('H:i', strtotime($rs['close'])); ?></td>
                <td><?php echo $rs['type']; ?></td>
                <td>
                	<?php if( $edit ) : ?>
                    	<button type="button" class="btn btn-warning btn-xs" onClick="goEdit(<?php echo $id; ?>)"><i class="fa fa-pencil"></i></button>
                    <?php endif; ?>
                    <?php if( $delete ) : ?>
                    	<button type="button" class="btn btn-danger btn-xs" onClick="deleteRow(<?php echo $id; ?>)"><i class="fa fa-trash"></i></button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php $n++; ?>
        <?php endwhile; ?>                 
        
        </table>
        <?php $paginator->display($get_rows,"index.php?content=sender"); ?>
    </div>
</div><!--/ row -->
<?php endif; ?>    
</div><!--/ Container -->
<script>
function getSearch()
{
	$("#searchForm").submit();	
}
function clearFilter()
{
	$.ajax({
		url:"controller/addressController.php?clearFilter",
		type:"GET", cache:false, success: function(rs){
			window.location.href = 'index.php?content=sender';
		}
	});	
}

function deleteRow(id)
{
	swal({
		  title: "ต้องการลบผู้จัดส่ง ?",
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
				url:"controller/addressController.php?deleteSender&id_sender="+id,
				type:"GET", cache:"false",
				success: function(rs)
				{
					var rs = $.trim(rs);
					if(rs == "success")
					{
						load_out();
						$("#"+id).remove();
						swal({ title: "เรียบร้อย", text: "ลบผู้จัดส่งเรียบร้อยแล้ว", timer: 1000, type: "success"});
					}else{
						load_out();
						swal("ไม่สำเร็จ", "ลบผู้จัดส่งไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
					}
				}
			});						
	});
}

function goEdit(id)
{
	window.location.href = 'index.php?content=sender&edit=y&id_sender='+id;	
}

function saveEdit(id)
{
	var name = $("#name").val();
	if( name == '' ){ swal("ชื่อไม่ถูกต้อง"); return false; }
	$.ajax({
		url:"controller/addressController.php",
		type:"GET", cache:false, data:{ "check_sender" : 'Y', "sender_name" : name, "id_sender" : id },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != '0' ){
				swal("Error!", "มีผู้จัดส่งชื่อนี้อยู่ในระบบแล้ว", "error");	
			}else{
				$.ajax({
					url:"controller/addressController.php?updateSender&id_sender="+id,
					type:"POST", cache:false, data: $("#editFrom").serialize(),
					success: function(rs){
						var rs = $.trim(rs);
						if( rs == 'success' ){
							swal({ title : 'สำเร็จ', text : 'แก้ไขผู้จัดส่งเสร็จเรียบร้อยแล้ว', type: 'success', timer: 1000});
							setTimeout(function(){ goBack(); }, 2000);
						}else{
							swal("Error!", "แก้ไขผู้จัดส่งไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
						}
					}
				});
			}
		}
	});	
}

function save()
{
	var name = $("#name").val();
	if( name == '' ){ swal("ชื่อไม่ถูกต้อง"); return false; }
	$.ajax({
		url:"controller/addressController.php",
		type:"GET", cache:false, data:{ "check_sender" : 'Y', "sender_name" : name },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != '0' ){
				swal("Error!", "มีผู้จัดส่งชื่อนี้อยู่ในระบบแล้ว", "error");	
			}else{
				$.ajax({
					url:"controller/addressController.php?addNewSender",
					type:"POST", cache:false, data: $("#addFrom").serialize(),
					success: function(rs){
						var rs = $.trim(rs);
						if( rs == 'success' ){
							swal({ 
								title : 'สำเร็จ', 
								text : 'เพิ่มผู้จัดส่งเสร็จเรียบร้อยแล้ว ต้องการเพิ่มอีกหรือไม่ ?', 
								type: 'success', 
								//confirmButtonColor: "#DD6B55",
								confirmButtonText: "เพิ่มอีก",
								cancelButtonText: "ไม่ใช่",
								closeOnConfirm: true,
								showCancelButton: true 
								}, function(isConfirm){
									if( isConfirm ){
										window.location.href = 'index.php?content=sender&add';
									}else{
										window.location.href = 'index.php?content=sender';
									}
								});
						}else{
							swal("Error!", "เพิ่มผู้จัดส่งไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
						}
					}
				});
			}
		}
	});
}

function addNew()
{
	window.location.href= 'index.php?content=sender&add=y';	
}
function goBack()
{
	window.location.href = 'index.php?content=sender';	
}
</script>