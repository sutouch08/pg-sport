<?php 
	$page_name = "เพิ่ม/แก้ไข ประเภทการสั่งซื้อ";
	$id_tab = 54;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
?> 
<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-sm-6" style="margin-top:10px;"><h4 class="title"><i class="fa fa-archive"></i>&nbsp;<?php echo $page_name; ?></h4>
	</div>
    <div class="col-sm-6">
      <p class="pull-right" style="margin-bottom:0px;">
      <?php if( $add ) : ?>
		<button type="button" class="btn btn-sm btn-success" onClick="new_role()"><i class="fa fa-plus"></i> &nbsp; เพิ่มใหม่</button>
      <?php endif; ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:15px;' />
<!-- End page place holder -->
<div class="row">
	<div class="col-lg-12" id="list">
		<table class="table table-striped">
        <thead style="font-size:12px;">
        	<th style="width:10%; text-align:center;">ลำดับ</th>
            <th>ประเภทการสั่งซื้อ</th>
            <th style="width:10%; text-align:center;">ค่าเริ่มต้น</th>
            <th style="width:10%; text-align:center;">สถานะ</th>
            <th style="width:15%; text-align:right;"></th>
        </thead>
        <tbody id="rs">
	<?php $qs = dbQuery("SELECT * FROM tbl_po_role"); ?>
    <?php	$n = 1; ?>
    <?php while($rs = dbFetchArray($qs)) : ?>
    	<tr style="font-size:12px;">
        	<td align="center"><?php echo $n; ?></td>
            <td><?php echo $rs['role_name']; ?></td>
            <td align="center">
            <?php if( $rs['is_default'] == 1 ) : ?>
            	<i class="fa fa-check" style="color: green;"></i>
            <?php else : ?>
            	<?php if( $edit ) : ?>
            	<button type="button" class="btn btn-primary btn-xs" onClick="set_default(<?php echo $rs['id_po_role']; ?>)" >ตั้งเป็นค่าเริ่มต้น</button>
                <?php endif; ?>
            <?php endif; ?>	
            </td>
            <td align="center">
            	<?php echo isActived($rs['active']); ?>
            </td>
            <td align="right">
            <?php if( $edit ) : ?>
				<?php if( $rs['active'] == 1 ) : ?>
                    <button type="button" class="btn btn-xs btn-default" onClick="disable(<?php echo $rs['id_po_role']; ?>)">ปิดใช้งาน</button>
               <?php else : ?>
                    <button type="button" class="btn btn-xs btn-primary" onClick="enable(<?php echo $rs['id_po_role']; ?>)">เปิดใช้งาน</button>
               <?php endif; ?>
               <button type="button" class="btn btn-xs btn-warning" onClick="edit_role(<?php echo $rs['id_po_role']; ?>)">แก้ไข</button>
           <?php endif; ?>          
            </td>
        </tr>
    <?php $n++; ?>
    <?php endwhile; ?> 
    	</tbody>      
        </table>    	
    </div>
</div>
<div class='modal fade' id='add_modal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog'  style="width:400px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class="modal-title" style="text-align:center;">เพิ่มประเภทการสั่งซื้อ</h4>
				 </div>
				 <div class='modal-body' id='info_body'>
                     <div class="row">
                        <div class="col-lg-12">
                            <label>ประเภทการสั่งซื้อ</label>
                            <input type="text" class="form-control input-sm" name="role_name" id="role_name" />
                        </div>
                        <div class="col-lg-12">&nbsp;</div>
                        <div class="col-lg-12">
                            <label><input type="checkbox" name="act" id="act" style="margin-right:15px;" onChange="setActived()" checked /> เปิดใช้งาน</label>
                        </div>
                        <div class="col-lg-12">&nbsp;</div>
                        <div class="col-lg-12">
                        <button type="button" class="btn btn-sm btn-success btn-block" onClick="add_role()">บันทึก</button>
                        </div>
                        <div class="col-lg-12" style="margin-bottom:15px;"><input type="hidden" name="active" id="active" value="1" /></div>
                     </div>
                 </div>
				 
			</div>
		</div>
	</div>
    
<div class='modal fade' id='edit_modal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog'  style="width:400px;">
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class="modal-title" style="text-align:center;">เพิ่มประเภทการสั่งซื้อ</h4>
				 </div>
				 <div class='modal-body'>
                     <div class="row">
                        <div class="col-lg-12">
                            <label>ประเภทการสั่งซื้อ</label>
                            <input type="text" class="form-control input-sm" name="edit_role_name" id="edit_role_name" />
                        </div>
                        <div class="col-lg-12">&nbsp;</div>
                        <div class="col-lg-12">
                            <label><input type="checkbox" name="edit_act" id="edit_act" style="margin-right:15px;" onChange="setEditActived()" checked /> เปิดใช้งาน</label>
                        </div>
                        <div class="col-lg-12">&nbsp;</div>
                        <div class="col-lg-12">
                        <button type="button" class="btn btn-sm btn-success btn-block" onClick="update_role()">บันทึก</button>
                        </div>
                        <div class="col-lg-12" style="margin-bottom:15px;">
                        <input type="hidden" name="id_role" id="id_role" />
                        <input type="hidden" name="edit_active" id="edit_active"  />
                        <input type="hidden" name="default" id="default" value="0" />
                        </div>
                     </div>
                 </div>
				 
			</div>
		</div>
	</div>    

<script id='template' type='text/x-handlebars-template'>
<tr style="font-size:12px;">
        	<td align="center">{{ no }}</td>
            <td>{{ role }}</td>
            <td align="center">
			{{#if default}}
				<i class="fa fa-check" style="color: green;"></i>
			{{/ if}}
			{{#unless default}}
            	<button type="button" class="btn btn-primary btn-xs" onClick="set_default({{ id }})" >ตั้งเป็นค่าเริ่มต้น</button>
			{{/unless}}	
            </td>
			<td align="center">
				{{#if actived}}<i class="fa fa-check" style="color: green;"></i>{{/if}}
				{{#unless actived}}<i class="fa fa-close" style="color: red;"></i>{{/unless}}
			</td>
            <td align="right">
			<?php if( $edit ) : ?>
            {{#if actived}}<button type="button" class="btn btn-xs btn-default" onClick="disable({{ id }})">ปิดใช้งาน</button>{{/if}}
			{{#unless actived}}	<button type="button" class="btn btn-xs btn-primary" onClick="enable({{ id }})">เปิดใช้งาน</button> {{/unless}}
			<button type="button" class="btn btn-xs btn-warning" onClick="edit_role({{ id }})">แก้ไข</button>
		   <?php endif; ?>
            </td>
        </tr>
</script>

<script>

function set_default(id)
{
	load_in()
	$.ajax({
		url:"controller/poController.php?set_default_role",
		type:"POST", cache: "false", data:{ "id_po_role" : id },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == "success" ){
				window.location.reload();
			}else{
				swal("ไม่สำเร็จ", "เปลี่ยนการตั้งค่าไม่สำเร็จ", "warning");
			}
		}
	});
}

function update_role()
{
	var id 			= $("#id_role").val();
	var role_name 	= $("#edit_role_name").val();
	var active 		= $("#edit_active").val();
	if( role_name == '' ){ swal("กรุณาระบุชื่อประเภทการสั่งซื้อ"); return false; }
	$("#edit_modal").modal("hide");
	load_in();
	$.ajax({
		url:"controller/poController.php?update_po_role",
		type: "POST", cache: "false", data:{ "id_po_role" : id, "role_name" : role_name, "active" : active },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == "success" ){
				swal({ title: "สำเร็จ", text: "แก้ไขข้อมูลเรียบร้อยแล้ว", type: "success", timer: 1000 });	
				setTimeout(function(){ window.location.reload(); }, 2000);
			}else{
				swal({ title: "ไม่สำเร็จ", text: "แก้ไขข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", type: "warning"}, function(){ $("#edit_modal").modal("show"); });
			}
		}
	});
}

function edit_role(id)
{
	$.ajax({
		url:"controller/poController.php?get_role_detail",
		type:"POST", cache: "false", data:{ "id_po_role" : id },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == "fail" ){ 
				swal("Error", "ไม่พบข้อมูล", "error");
			}else{
				err = rs.split(" | ");
				$("#id_role").val(err[0]);
				$("#edit_role_name").val(err[1]);
				if( err[2] == 1 ){
					$("#edit_act").prop("checked", true);
				}else{
					$("#edit_act").prop("checked", false);
				}
				$("#edit_active").val(err[2]);
				$("#default").val(err[3]);
			}
			$("#edit_modal").modal("show");
		}
	});
}


function add_role()
{
	var role 	= $("#role_name").val();
	var act	= $("#active").val();	
	if( role == ''){ swal("กรุณากำหนดชื่อประเภทการสั่งซื้อ"); return false; }
	$("#add_modal").modal("hide");
	load_in();
	$.ajax({
		url:"controller/poController.php?add_po_role",
		type:"POST", cache: "false", data:{ "role_name" : role, "active" : act },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'false' ){
				swal('Error !!', 'เพิ่มข้อมูลไม่สำเร็จ', 'error');
			}else{
				var source	= $('#template').html();
				var data		= $.parseJSON(rs);
				var output	= $('#rs');
				render_append(source, data, output);
				$("#role_name").val('');
				$("#active").val(1);
				$("#act").prop("checked", true);
				swal({title: 'สำเร็จ', text: 'เพิ่มรายการเรียบร้อยแล้ว', timer: 1000, type: 'success'});
			}
		}
	});	
}

function disable(id)
{
	$.ajax({
		url:"controller/poController.php?is_default",
		type:"POST", cache:"false", data:{ "id_po_role" : id },
		success: function(ds){
			var ds = $.trim(ds);
			if(ds == 1 ){ 
				swal({ title: "คำเตือน", text: "ไม่สามารถปิดการใช้งานรายการที่ถูกกำหนดเป็นค่าเริ่มต้นได้", type: "warning"}); return false; 
			}else{
				load_in();
				$.ajax({
					url:"controller/poController.php?active_po_role",
					type:"POST", cache: "false", data:{ "id_po_role" : id, "active" : 0 },
					success: function(rs){
						load_out();
						var rs = $.trim(rs);
						if( rs == "success"){
							window.location.reload();
						}else{
							swal("ไม่สำเร็จ", "เปลี่ยนสถานะไม่สำเร็จ", "warinig");
						}
					}
				});
			}
		}
	});
}

function enable(id)
{
	load_in();
	$.ajax({
		url:"controller/poController.php?active_po_role",
		type:"POST", cache: "false", data:{ "id_po_role" : id, "active" : 1 },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == "success"){
				window.location.reload();
			}else{
				swal("ไม่สำเร็จ", "เปลี่ยนสถานะไม่สำเร็จ", "warinig");
			}
		}
	});
}

function setActived(){
	if( $("#act").is(":checked") ){
		$("#active").val(1);
	}else{
		$("#active").val(0);
	}
}

function setEditActived(){
	var df = $("#default").val();
	if( df == 1 && $("#edit_act").is(":checked") == false)	{
		swal({ title: "คำเตือน", text: "ไม่สามารถปิดการใช้งานรายการที่ถูกกำหนดเป็นค่าเริ่มต้นได้", type: "warning"});
		$("#edit_act").prop("checked", true);
		return false;	
	}else{
		if( $("#edit_act").is(":checked") ){
			$("#edit_active").val(1);
		}else{
			$("#edit_active").val(0);
		}
	}
}

function new_role()
{
	$("#add_modal").modal("show");
	$("#add_modal").on("shown.bs.modal", function(){ $("#role_name").focus(); });
}
</script>    
</div><!--- container -->