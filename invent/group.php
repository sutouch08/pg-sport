<?php 
	$page_name = "กลุ่มลูกค้า(พื้นที่การขาย)";
	$id_tab = 23;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
 	include_once 'function/group_helper.php';
	?>
<div class="container">
<!-- page place holder -->

<div class="row top-row">
	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-users"></i> <?php echo $page_name; ?></h4></div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">
        	<?php if( ! isset( $_GET['add'] ) && ! isset( $_GET['edit'] ) && ! isset( $_GET['view_detail'] ) ) : ?>
            	<button type="button" class="btn btn-sm btn-success" onClick="newGroup()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
            <?php endif; ?>
            <?php if( isset( $_GET['add'] ) OR isset( $_GET['edit'] ) OR isset( $_GET['view_detail'] ) ) : ?>
            	<button type="button" class="btn btn-sm btn-warning" onClick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
            <?php endif; ?>
            <?php if( isset( $_GET['add'] ) ) : ?>
            	<button type="button" class="btn btn-sm btn-success" onClick="saveAdd()"><i class="fa fa-save"></i> บันทึก</button>
            <?php endif; ?>
            <?php if( isset( $_GET['edit'] ) ) : ?>
            	<button type="button" class="btn btn-sm btn-success" onClick="saveEdit()"><i class="fa fa-save"></i> บันทึก</button>
            <?php endif; ?>
        </p>
    </div>
</div>
<hr style="margin-bottom:15px;" />
<!-- End page place holder -->
<?php  if( isset( $_GET['add'] ) ) : ?>

    <div class="row">
		<div class="col-sm-3"><span class="form-control label-left">รหัสสินค้า</span></div>
		<div class="col-sm-9">
			<input type="text" class="form-control input-sm input-large inline" name="gName" id="gName" placeholder="กำนหดชื่อกลุ่มลูกค้า" autofocus />
			<span id="gName-error" class="label-left red" style="margin-left:15px; display:none;">ชื่อกลุ่มซ้ำ</span>
		</div>
		<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
	</div>

<?php elseif( isset( $_GET['edit'] ) && isset( $_GET['id_group'] ) ) : ?>
	<?php $id	= $_GET['id_group']; 	?>
    <?php $ds	= getGroupDetail($id);	?>	
    

    <div class="row">
		<div class="col-sm-3"><span class="form-control label-left">รหัสสินค้า</span></div>
		<div class="col-sm-9">
			<input type="text" class="form-control input-sm input-large inline" name="gName" id="gName" value="<?php echo $ds['group_name']; ?>" placeholder="กำนหดชื่อกลุ่มลูกค้า" autofocus />
			<span id="gName-error" class="label-left red" style="margin-left:15px; display:none;">ชื่อกลุ่มซ้ำ</span>
            <input type="hidden" name="id_group" id="id_group" value="<?php echo $id; ?>" />
		</div>
		<div class="divider-hidden" style="margin-top:5px; margin-bottom:5px;"></div>
	</div>


<?php elseif( isset( $_GET['view_detail'] ) && isset( $_GET['id_group'] ) ) : ?>
	<?php 	$id 	= $_GET['id_group'];	?>
    <?php	 $ds	= getGroupDetail($id);	?>	
    <?php	$qs 	= dbQuery("SELECT * FROM tbl_customer WHERE id_default_group = ".$id); 	?>
    <?php	$mem	= dbNumRows($qs); 	?>   
<div class="row">
	<div class="col-sm-4"><h4 class="title">กลุ่ม : <?php echo $ds['group_name']; ?></h4></div>
    <div class="col-sm-4"><h4 class="title">สมาชิก : <?php echo number_format($mem); ?></h4></div>
</div>
<hr/>
<div class="row">
	<div class="col-sm-12">
    	<p class="pull-right top-p">
    	<button type="button" class="btn btn-sm btn-warning" id="btn-move" onClick="showCheckbox()">ย้ายกลุ่มลูกค้า</button>
        <button type="button" class="btn btn-sm btn-info bs" style="display:none;" onClick="checkAll()">เลือกทั้งหมด</button>
        <button type="button" class="btn btn-sm btn-default bs" style="display:none;" onClick="unCheckAll()">ไม่เลือกทั้งหมด</button>
        <button type="button" class="btn btn-sm btn-primary bs" style="display:none;" onClick="whereToMove()">ย้ายที่เลือก</button>
        </p>
    </div>
</div>
<hr/>
<div class="row">
	<div class="col-sm-12">
	<?php if( $mem > 0 ) : ?>
    <table class="table table-striped table-bordered">
    	<thead>
        	<th style="width:10%; text-align:center;">ลำดับ</th>
            <th style="width:15%; text-align:center;">รหัสลูกค้า</th>
            <th>ชื่อ - สกุล</th>
        </thead>
        <tbody>
        <?php $n = 1; ?>
        <?php while( $rs = dbFetchArray($qs) ) : ?>
        	<tr>
            	<td align="center"><?php echo number_format($n); ?></td>
                <td><?php echo $rs['customer_code']; ?></td>
                <td>
                	<label>
                    <input type="checkbox" class="ck" name="customer[<?php echo $rs['id_customer']; ?>]" value="<?php echo $rs['id_customer']; ?>" style="margin-right:15px;" />
                    <?php echo $rs['first_name'] . ' '. $rs['last_name']; ?>
               		</label>
				</td>                    
            </tr>
		<?php	$n++; ?>            
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php endif; ?>    
    </div>
</div>
<div class='modal fade' id='moveModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style="width:300px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title'>เลือกกลุ่มปลายทาง</h4>
            </div>
            <div class='modal-body'>
            	<div class="row">
            	<div class="col-sm-12">
                	<select class="form-control" id="tagetGroup" name="tagetGroup">
                    	<option value="">เลือกกลุ่มลูกค้า</option>
                        <?php echo selectGroup(); ?>
                    </select>
                </div>
                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
                <button type="button" class="btn btn-success" onClick="moveToGroup()">ดำเนินการ</button>
            </div>
        </div>
    </div>
</div>

<?php else : ?>
	<?php	$qs = dbQuery("SELECT * FROM tbl_group");	?>
	<div class='row'>
		<div class='col-sm-12'>
        	<table class="table table-striped table-bordered">
            	<thead>
                	<th style="width:10%; text-align:center;">ไอดี</th>
                    <th style="width:60%; text-align:center;">ชื่อกลุ่ม</th>
                    <th style="width:15%; text-align:center;">สมาชิก</th>
                    <th></th>
                </thead>
                <tbody>
		<?php if( dbNumRows($qs) > 0 ) : ?>
        <?php	while( $rs = dbFetchArray($qs) ) : ?>
        <?php		$member = countMember($rs['id_group']); ?>
        		<tr id="group-<?php echo $rs['id_group']; ?>">
                	<td align="center"><?php echo number_format($rs['id_group']); ?></td>
                    <td><?php echo $rs['group_name']; ?></td>
                    <td align="center"><?php echo number_format($member); ?></td>
                    <td align="right">
                    	<button type="button" class="btn btn-sm btn-info" onClick="viewGroup(<?php echo $rs['id_group']; ?>)"><i class="fa fa-search"></i></button>
                        <button type="button" class="btn btn-sm btn-warning" onClick="editGroup(<?php echo $rs['id_group']; ?>)"><i class="fa fa-pencil"></i></button>
                        <button type="button" class="btn btn-sm btn-danger" onClick="removeGroup(<?php echo $rs['id_group']; ?>, '<?php echo $rs['group_name']; ?>', <?php echo $member; ?>)"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
        <?php 	endwhile;		?>
        <?php else : ?>
        		<tr>
                	<td colspan="4" align="center"><h4 class="title"><i class="fa fa-users"></i> ไม่มีกลุ่ม</h4></td>
                </tr>
        <?php endif; ?>			                
                </tbody>
            </table>
        </div>
	</div>        
	
<?php endif; ?>
</div>
<script>
function moveToGroup()
{
	var id = $("#tagetGroup").val();
	if( id != '' )
	{
		$("#moveModal").modal('hide');
		load_in();
		var ds = $('input:checkbox:checked').serialize();
		$.ajax({
			url:"controller/groupController.php?moveToGroup&id_group="+id,
			type:"POST", cache:"false", data: ds,
			success: function(rs){
				load_out();
				var rs = $.trim(rs);
				if( rs == 'success' )
				{
					swal({ title: 'เรียบร้อย', text: 'ย้ายกลุ่มลูกค้าเรียบร้อยแล้ว', timer: 1000, type: 'success' });	
					setTimeout(function(){ window.location.reload(); }, 1200);
				}
				else
				{
					swal("ข้อผิดพลาด", "ย้ายกลุ่มลูกค้าไม่สำเร็จ", "error");
				}
			}
		});
	}
}

function whereToMove()
{
	var count = $('input:checkbox:checked').length;
	if(count < 1 )
	{
		swal('คุณยังไม่ได้เลือกลูกค้าที่จะย้าย');	
	}
	else
	{
		$("#moveModal").modal('show');	
	}
}
function checkAll()
{
	$(".ck").each(function(index, element) {
        $(this).prop("checked", true)
    });	
}
function unCheckAll()
{
	$(".ck").each(function(index, element) {
        $(this).prop('checked', false);
    });	
}

function showCheckbox()
{
	$("#btn-move").css('display', 'none');
	$(".bs").css('display','');	
}
function removeGroup(id, name, member)
{
	if( member != 0 )
	{
		swal('ข้อผิดพลาด !', 'คุณไม่สามารถลบกลุ่มที่มีสมาชิกในกลุ่มได้', 'error');
		return false;
	}
	swal({
		title: 'ต้องการลบ '+name+' ?',
		text: 'คุณแน่ใจว่าต้องการลบกลุ่มนี้จริงๆ โปรดจำไว้ว่าการกระทำนี้ไม่สามารถกู้คืนได้',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
	}, function(){
		$.ajax({
			url:"controller/groupController.php?removeGroup",
			type:"POST", cache:"false", data:{ "id_group" : id },
			success: function(rs){
				if( rs == 'success' )
				{
					swal({ title: 'ลบกลุมเรียบร้อยแล้ว', timer: 1000, type: 'success'});
					$("#group-"+id).remove();
				}else{
					swal({ title: 'ลบกลุ่มไม่สำเร็จ', text: rs, type: 'error' });	
				}
			}
		});
	});
}

function saveAdd()
{
	var name = $("#gName").val();
	if( gName == '' )
	{
		showError('จำเป็นต้องระบุช่องนี้');
		return false;
	}
	
	$.ajax({
		url:"controller/groupController.php?validGroupName",
		type:"POST", cache:"false", data:{ "group_name" : name },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 1 )
			{
				showError('ชื่อกลุ่มซ้ำ');
			}
			else if( rs == 0 )
			{
				addNewGroup(name);	
			}
		}
	});
		
}

function addNewGroup(name)
{
	$.ajax({
		url:"controller/groupController.php?addNewGroup",
		type:"POST", cache:"false", data:{ "group_name" : name },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success')
			{
				swal({ title: 'เรียบร้อย', text: 'เพิ่มกลุมลูกค้าเรียบร้อยแล้ว', timer: 1000, type: 'success' });
				setTimeout(function(){ goBack(); }, 1500);
			}
			else
			{
				swal('ข้อผิดพลาด !!', 'เพิ่มข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง', 'error');
			}
		}
	});	
}


function saveEdit()
{
	var id = $("#id_group").val();
	var name = $("#gName").val();
	if( name == '' )
	{
		showError('จำเป็นต้องระบุช่องนี้');
		return false;	
	}
	$.ajax({
		url:"controller/groupController.php?validGroupName",
		type:"POST", cache:"false", data:{ "id_group" : id, "group_name" : name },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 1 )
			{
				showError('ชื่อกลุ่มซ้ำ');
			}
			else if( rs == 0 )
			{
				updateGroup(id, name);	
			}
		}
	});
}



function updateGroup(id, name)
{
	$.ajax({
		url:"controller/groupController.php?updateGroupName",
		type:"POST", cache:"false", data:{ "id_group" : id, "group_name" : name },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success')
			{
				swal({ title: 'เรียบร้อย', text: 'ปรับปรุงข้อมูลเรียบร้อยแล้ว', timer: 1000, type: 'success' });
				setTimeout(function(){ goBack(); }, 1500);
			}
			else
			{
				swal('ข้อผิดพลาด !!', 'ปรับปรุงข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง', 'error');
			}
		}
	});
}



function showError(text)
{
	$("#gName").addClass('has-error');
	$("#gName-error").text(text);
	$("#gName-error").css('display', '');
	$("#gName").focus();
}


function viewGroup(id)
{
	window.location.href = "index.php?content=group&view_detail=y&id_group="+id;	
}


function editGroup(id)
{
	window.location.href = "index.php?content=group&edit=y&id_group="+id;	
}

function newGroup()
{
	window.location.href = "index.php?content=group&add";	
}

function goBack()
{
	window.location.href = "index.php?content=group";	
}

$(document).ready(function() {
    $("#group_name").keyup(function(){
		var name = $("#group_name").val();
		$.ajax({
			type: "GET", url:'controller/groupController.php', cache: false , data:"group_name="+name,
			success: function(msg){
				if($("#group_name").val().length >3){
					if(msg == 1){
						$("#valid").val(msg);
						$("#valid_name").html("<span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'>");
					}else if(msg == 0){
						$("#valid").val(msg);
						$("#valid_name").html("<span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'>");
					}	
				}
			}
		});
	});
});											
function validate(){
	var valid = $("#valid").val();
	if(valid == 1){
		alert("ชื่อกลุ่มซ้ำ");
	}else if(valid ==0){
		$("#group_form").submit();
	}
}
</script>