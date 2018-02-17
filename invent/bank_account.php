<?php
	$page_name 	= "เพิ่ม/แก้ไข บัญชีธนาคาร";
	$id_tab 			= 60;
	$id_profile 		= $_COOKIE['profile_id'];
    $pm 				= checkAccess($id_profile, $id_tab);
	$view 			= $pm['view'];
	$add 				= $pm['add'];
	$edit 				= $pm['edit'];
	$delete 			= $pm['delete'];
	accessDeny($view);
	include 'function/bank_helper.php';
	$qs = dbQuery("SELECT * FROM tbl_bank_account");
?>
<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col">
    	<h4 class="title"><i class="fa fa-bank"></i>&nbsp;<?php echo $page_name; ?></h4>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p" >
        <button type="button" class="btn btn-sm btn-success" onClick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
        </p>
    </div>
</div><!-- / row -->

<hr style="margin-bottom:15px;" />

<div class="row">
	<div class="col-sm-12">
	<table class="table table-striped" >
    	<thead style="font-size:12px;">
        	<tr>
            	<th style="width:10%; text-align:center;"></th>
                <th style="width:15%;">ธนาคาร</th>
                <th style="width:20%;">ชื่อบัญชี</th>
                <th style="width:15%;">เลขที่บัญชี</th>
                <th style="width:20%;">สาขา</th>
                <th style="width:5%; text-align:center;">สถานะ</th>
                <th style="width:20%; text-align:center;"></th>
            </tr>
        </thead>
        <tbody id="bankTable">
 
        </tbody>
    </table>
    </div>
    
</div>

<div class='modal fade' id='addModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style="width:300px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title-site text-center' >เพิ่ม/แก้ไข บัญชีธนาคาร</h4>
            </div>
            <div class='modal-body'>
            <form id="addForm"	>
            <input type="hidden" name="id_account" id="id_account" />
            <div class="row">
            	<div class="col-sm-12">
                	<label class="input-label">ธนาคาร</label>
                    <select name="bank" id="bank" class="form-control input-sm">
                    <option value="">เลือกธนาคาร</option>
                    <?php echo select_bank(); ?>
                    </select>
                </div>
                <div class="col-sm-12">
                	<label class="input-label">ชื่อบัญชี</label>
                    <input type="text" class="form-control input-sm" name="acName" id="acName" placeholder="ชื่อเรียกบัญชี" />
                </div>
                <div class="col-sm-12">
                	<label class="input-label">เลขที่บัญชี </label>
                    <input type="text" class="form-control input-sm" name="acNo" id="acNo" placeholder="000-0-00000-0" />
                </div>
                <div class="col-sm-12">
                	<label class="input-label">สาขา</label>
                    <input type="text" class="form-control input-sm" name="branch" id="branch" placeholder="สาขาธนาคาร" />
                </div>
                <div class="col-sm-12" style="margin-top:10px;">
                    <div class="btn-group">
                    	<button type="button" class="btn btn-sm btn-success" id="btn-active" onClick="Active()"><i class="fa fa-check"></i></button>
                        <button type="button" class="btn btn-sm" id="btn-disActive" onClick="disActive()"><i class="fa fa-times"></i></button>
                    </div>
                    <input type="hidden" name="active" id="active" value="1" />
                </div>
            </form>
            </div>
            <div class='modal-footer'>
                <button type="button" class="btn btn-sm btn-success" onClick="saveAccount()" ><i class="fa fa-save"></i> บันทึก</button>
            </div>
        </div>
    </div>
</div>
</div><!--/ container -->

<script id="bankTableTemplate" type="text/x-handlebars-template">
{{#each this}}
<tr>
<td align="center">{{{ logo }}}</td>
<td> {{ bankName }}</td>
<td>{{ accName }}</td>
<td>{{ accNo }}</td>
<td>{{ branch }}</td>
<td align="center"><button class="btn btn-xs btn-link" onclick="toggleActive({{ id }}, {{ active }})">{{{ actived }}}</button></td>
<td align="right">
<?php if( $edit ) : ?>
<button type="button" class="btn btn-xs btn-warning" onClick="editAccount({{ id }})"><i class="fa fa-pencil"></i></button>
<?php endif; ?>
<?php if( $delete ) : ?>
<button type="button" class="btn btn-xs btn-danger" onClick="removeAccount({{ id }}, '{{ accName }}')"><i class="fa fa-trash"></i></button>
<?php endif; ?>	
</td>
</tr>
{{/each}}
</script>
<script src="<?php echo WEB_ROOT; ?>library/js/jquery.maskedinput.js"></script>
<script>
function removeAccount(id, accName)
{
	swal({
		title: 'คุณแน่ใจ ?',
		text: 'คุณกำลังจะลบ "'+ accName +'" โปดจำไว้ว่าการกระทำนี้ไม่สามารถกู้คืนได้',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ลบเลย',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url:"controller/bankController.php?removeBankAccount",
				type:"POST",cache:false, data:{ "id_account" : id },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'fail' )
					{
						swal('ข้อผิดพลาด', 'ลบบัญชีไม่สำเร็จ กรุณาลองใหม่อีกครั้ง', 'error');
					}else{
						swal({ title: 'สำเร็จ', timer: 1000, type: 'success'});
						reloadBankTable();
					}
				}
			});
		});
}

function editAccount(id)
{
	$.ajax({
		url:"controller/bankController.php?getBankAccount",
		type:"POST", cache:false, data:{ "id_account" : id },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'fail' )
			{
				swal('ข้อผิดพลาด', 'ไม่พบข้อมูลที่ต้องการ กรุณาลองใหม่', 'error');
			}else{
				var ds = rs.split(' | ');
				$("#id_account").val(ds[0]);
				$("#bank").val(ds[1]);
				$("#acName").val(ds[2]);
				$("#acNo").val(ds[3]);
				$("#branch").val(ds[4]);
				if( ds[5] == 1 ){ Active(); }else if( ds[5] == 0 ){ disActive(); }
				$("#addModal").modal('show');
			}
		}
	});
}


function saveAccount()
{
	var bank 	= $("#bank").val();
	var acName	= $("#acName").val();
	var acNo		= $("#acNo").val();
	var branch	= $("#branch").val();
	$("#addModal").modal('hide');	
	if( bank == '' || acName == '' || acNo == '' || branch == '' )
	{
		swal('กรุณาระบุข้อมูลให้ครบทุกฟิลด์');
		return false;
	}
	$.ajax({
		url:"controller/bankController.php?addNewAccount",
		type:"POST",cache:false, data: $("#addForm").serialize(),
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'fail' )
			{
				swal('ข้อผิดพลาด!', 'เพิ่มบัญชีธนาคารไม่สำเร็จ กรุณาลองใหม่อีกครั้ง', 'error');
				$("#addModal").modal('show');
			}else{
				swal({ title : 'สำเร็จ', timer: 1000, type: 'success'});
				reloadBankTable();
			}
		}
	});	
}

$("#acNo").mask('999-9-99999-9');

$(document).ready(function(e) {
    reloadBankTable();
});
function reloadBankTable()
{
	load_in();
	$.ajax({
		url:"controller/bankController.php?getBankAccountTable",
		type: "POST", cache:"false", 
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'fail' ){
				$("#bankTable").html('<tr><td colspan="7" align="center"><strong>ไม่พบบัญชีธนาคาร</strong></td></tr>');
			}else{
				var source 	= $("#bankTableTemplate").html();
				var data 		= $.parseJSON(rs);
				var output	= $("#bankTable");
				render(source, data, output);
			}
		}
	});
}

function toggleActive(id, status)
{
	$.ajax({
		url:"controller/bankController.php?toggleActive",
		type:"POST", cache:"false", data:{ "id_account" : id, "active" : status },
		success: function(rs){
			if( rs == 'success' ){
				reloadBankTable();
			}				
		}
	});
}

function Active()
{
	$("#active").val(1);
	$("#btn-active").addClass('btn-success');
	$("#btn-disActive").removeClass('btn-danger');	
}

function disActive()
{
	$("#active").val(0);
	$("#btn-active").removeClass('btn-success');
	$("#btn-disActive").addClass('btn-danger');	
}
function clearField()
{
	$("#id_account").val('');
	$("#bank").val('');
	$("#acName").val('');
	$("#acNo").val('');	
	$("#branch").val('');
	$("#active").val(1);
	Active();
}

function addNew()
{
	clearField();
	$("#addModal").modal('show');
}
</script>
