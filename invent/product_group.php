<?php
	$page_name 	= "เพิ่ม/แก้ไข กลุ่มสินค้า";
	$id_tab 			= 64;
	$id_profile 		= $_COOKIE['profile_id'];
    $pm 				= checkAccess($id_profile, $id_tab);
	$view 			= $pm['view'];
	$add 				= $pm['add'];
	$edit 				= $pm['edit'];
	$delete 			= $pm['delete'];
	accessDeny($view);
	include 'function/product_helper.php';
?>
<div class="container">
<div class="row top-row">
	<div class="col-sm-6 top-col">
    	<h4 class="title"><i class="fa fa-archive"></i>&nbsp;<?php echo $page_name; ?></h4>
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
<?php $qs = dbQuery("SELECT * FROM tbl_product_group"); ?>    
	<table class="table table-striped" >
    	<thead style="font-size:12px;">
        	<tr>
            	<th style="width:10%; text-align:center;">ไอดี</th>
                <th style="width:40%;">ชื่อกลุ่ม</th>
                <th style="width:10%; text-align:center;">สินค้าในกลุ่ม</th>
                <th style="text-align:center;"></th>
            </tr>
        </thead>
        <tbody id="groupTable">
<?php	if( dbNumRows($qs) > 0 ) : ?>
<?php		while( $rs = dbFetchArray($qs) ) : ?>
<?php			$isDefault = $rs['isDefault'] == 1 ? 'btn-success' : '' ; ?>
			<tr>
            	<td align="center"><?php echo $rs['id']; ?></td>
                <td id="td<?php echo $rs['id']; ?>"><?php echo $rs['name']; ?></td>
                <td align="center"><?php echo number_format( productInGroup( $rs['id'] ) ); ?></td>
                <td align="right">
                	<button type="button" class="btn btn-sm <?php echo $isDefault; ?> bs" id="btn-<?php echo $rs['id']; ?>" onClick="setAsDefault(<?php echo $rs['id']; ?>)"><i class="fa fa-check"></i> ค่าเริ่มต้น</button>
                    <button type="button" class="btn btn-sm btn-warning" onClick="editGroup(<?php echo $rs['id']; ?>)"><i class="fa fa-pencil"></i> แก้ไข</button>
                    <button class="btn btn-sm btn-danger" onClick="checkDeleteGroup(<?php echo $rs['id']; ?>)"><i class="fa fa-trash"></i> ลบกลุ่ม</button>
                </td>
            </tr>
<?php			endwhile; 	?>		
<?php	else : ?>
			<tr id="nogroup"><td colspan="4" align="center"><h4 class="title" style="color:#AAA;">ไม่พบกลุ่มใดๆ</h4></td></tr>	
<?php 	endif; ?>        
 
        </tbody>
    </table>
    </div>
    
</div>

<div class='modal fade' id='addModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style="width:300px;">
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title-site text-center' >เพิ่ม/แก้ไข กลุ่มสินค้า</h4>
            </div>
            <div class='modal-body'>
            <input type="hidden" name="id_group" id="id_group" />
            <div class="row">
                <div class="col-sm-12">
                	<label class="input-label">ชื่อกลุ่ม</label>
                    <input type="text" class="form-control input-sm" name="gName" id="gName" placeholder="กำหนดชื่อกลุ่มสินค้า" />
                </div>
            </div>
            <div class='modal-footer'>
                <button type="button" class="btn btn-sm btn-success" onClick="saveGroup()" ><i class="fa fa-save"></i> บันทึก</button>
            </div>
        </div>
    </div>
</div>

</div><!--/ container -->

<script id="rowTemplate" type="text/x-handlebars-template">
<tr id="row_{{ id }}">
<td align="center">{{ id }}</td>
<td id="td{{ id }}">{{ name }}</td>
<td align="center">{{ onGroup }}</td>
<td align="right">
<button class="btn btn-sm {{ isDefault }}" onClick="setAsDefault({{ id }})"><i class="fa fa-check"></i> ค่าเริ่มต้น</button>
<button class="btn btn-sm btn-warning" onClick="editGroup({{ id }})"><i class="fa fa-pencil"></i> แก้ไข</button>
<button class="btn btn-sm btn-danger" onClick="checkDeleteGroup({{ id }})"><i class="fa fa-trash"></i> ลบกลุ่ม</button>
</td>
</tr>
</script>

<script>
function checkDeleteGroup(id)
{
	$.ajax({
		url:"controller/productController.php?checkDefaultGroup",
		type:"POST",cache:"false", data:{ "id" : id },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == '0')
			{
				removeGroup(id);
			}
			else if( rs == '1' )
			{
				swal("ข้อผิดพลาด !!", "คุณไม่สามารถลบกลุ่มสินค้าเริ่มต้นได้", "error");
			}
		}
	});
}

function setAsDefault(id)
{
	$.ajax({
		url:"controller/productController.php?setDefaultGroup",
		type:"POST", cache:"false", data:{ "id" : id },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' )
			{
				$('.bs').removeClass('btn-success');
				$("#btn-"+id).addClass('btn-success');
			}
		}
	});
}

function editGroup(id)
{
	$.ajax({
		url:"controller/productController.php?getProductGroupDetail",
		type:"POST",cache:"false",data:{'id' : id },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != 'fail' )
			{
				var arr = rs.split(' | ');
				$("#gName").val(arr[1]);	
				$("#id_group").val(arr[0]);
				$("#addModal").modal('show');
			}
			else
			{
				swal("ข้อผิดพลาด!", "ไม่พบข้อมูลที่ต้องการแก้ไข", "error");	
			}
		}
	});
}

function removeGroup(id)
{
	var name = $("#td"+id).text();
	
	swal({
		title: 'คุณแน่ใจ ?',
		text: 'คุณกำลังจะลบ "'+ name +'" <br/>  สินค้าที่อยู่ในกลุ่มนี้จะถูกย้ายไปกลุ่มเริ่มต้น',
		type: 'warning',
		html: true,
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ลบเลย',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url:"controller/productController.php?removeProductGroup",
				type:"POST",cache:false, data:{ "id" : id },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'fail' )
					{
						swal('ข้อผิดพลาด', 'ลบกลุ่สินค้าไม่สำเร็จ กรุณาลองใหม่อีกครั้ง', 'error');
					}else{
						swal({ title: 'สำเร็จ', timer: 1000, type: 'success'});
						setTimeout(function(){ window.location.reload(); }, 1200);
					}
				}
			});
		});
		
}


function saveGroup()
{
	$("#addModal").modal('hide');
	var name = $("#gName").val();
	var id 	= $("#id_group").val();
	if( id == '' ){ 
		var ds = { "name" : name } ; 
	}else{ 
		var ds = { "name" : name, "id" : id };
	}
	
	if( name != '' )
	{
		$.ajax({
			url:"controller/productController.php?addProductGroup",
			type:"POST", cache:"false", data: ds,
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == '1' ) //--- ซ้ำ
				{
					swal({ title:"ข้อผิดพลาด!", text:"ชื่อกลุ่มสินค้าซ้ำ", type:"error"}, function(){ $("#addModal").modal('show'); });
				}
				else if( rs == 'fail' )
				{
					swal({ title:"ข้อผิดพลาด!", text:"เพิ่ม/แก้ไข กลุ่มสินค้าไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", type:"error"}, function(){ $("#addModal").modal('show'); });
				}
				else
				{
					if( rs == 'success' )
					{
						swal({ title: 'เรียบร้อย', timer: 1000, type: 'success' });
						$("#td"+id).text(name);
						clearField();
					}
					else
					{
						swal({ title: 'สำเร็จ', timer: 1000, type: 'success' });
						$("#nogroup").remove();
						var source	= $("#rowTemplate").html();
						var data		= $.parseJSON(rs);
						var output	= $("#groupTable");
						render_append(source, data, output);		
						clearField();
					}
				}
			}
		});
	}
		
}


$("#addModal").on('shown.bs.modal', function(){ $("#gName").focus(); });

function clearField()
{
	$("#id_group").val('');
	$("#gName").val('');
}

function addNew()
{
	clearField();
	$("#addModal").modal('show');
}
</script>
