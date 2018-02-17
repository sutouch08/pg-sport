<?php 
	$page_menu = "invent_customer";
	$page_name = "กำหนดสิทธิ์";
	$id_tab = 29;
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
	function isAccess($id_profile, $id_tab){
		$sql = dbQuery("SELECT tbl_access.view, tbl_access.add, tbl_access.edit, tbl_access.delete FROM tbl_access WHERE id_profile = '$id_profile' AND id_tab = '$id_tab'");
		$rw = dbNumRows($sql);
		if($rw > 0){
			while($rs = dbFetchArray($sql)){
				$ac['view'] = $rs['view'];
				$ac['add'] = $rs['add'];
				$ac['edit'] = $rs['edit'];
				$ac['delete'] = $rs['delete'];
			}
		}else{
				$ac['view'] = 0;
				$ac['add'] = 0;
				$ac['edit'] = 0;
				$ac['delete'] = 0;
		}
		return $ac;
	}
	?>
<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-sm-8" style="margin-top:10px;"><h4 class="title"><i class="fa fa-unlock-alt"></i>&nbsp;<?php echo $page_name; ?></h4>
	</div>
    <div class="col-sm-4">
       <p class="pull-right" style="margin-bottom:0px;">
       <?php if(isset($_GET['id_profile'])) : ?>
       <a href="index.php?content=securable"><button type="button" class="btn btn-warning btn-sm"><i class="fa fa-arrow-left"></i>&nbsp; กลับ</button></a>
       <a href="javascript:void(0);"><button type="button" class="btn btn-success btn-sm" onclick="save()"><i class="fa fa-save"></i>&nbsp; บันทึก</button></a>
       <?php endif; ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:15px;' />
<!-- End page place holder -->
<div class="row">
<div class="col-lg-12">

<?php if(isset($_GET['edit_right']) && isset($_GET['id_profile']) ) : ?>
	<?php $id_profile 	= $_GET['id_profile']; ?>
    <?php $qs 			= dbQuery("SELECT * FROM tbl_tab_group ORDER BY position ASC"); ?>
    <?php $row 		= dbNumRows($qs); ?>
    <?php list($profile_name) = dbFetchArray(dbQuery("SELECT profile_name FROM tbl_profile WHERE id_profile = $id_profile"));  ?>
    <?php  if($row > 0) : ?>
    <p><h4>กำหนดสิทธิ์ : <?php echo $profile_name; ?></h4></p>
    <form id="right_form" action="controller/securableController.php?id_profile=<?php echo $id_profile; ?>" method="post">
    <table class="table table-striped">
    <thead>
    <th style="width:50%">เมนู</th>
    <th style="width:10%; text-align:center; ">	<input type="checkbox" id="view_all" />	<label for="view_all" style="vertical-align:middle">&nbsp;&nbsp;ดู</label></th>
    <th style="width:10%; text-align:center">	<input type="checkbox" id="add_all" />	<label for="add_all" style="vertical-align:middle">&nbsp;&nbsp;เพิ่ม</label></th>
    <th style="width:10%; text-align:center">	<input type="checkbox" id="edit_all" />	<label for="edit_all" style="vertical-align:middle">&nbsp;&nbsp;แก้ไข</label></th>
    <th style="width:10%; text-align:center">	<input type="checkbox" id="delete_all" /><label for="delete_all" style="vertical-align:middle">&nbsp;&nbsp;ลบ</label></th>
    <th style="width:10%; text-align:center">	<input type="checkbox" id="check_all" /><label for="check_all" style="vertical-align:middle">&nbsp;&nbsp;ทั้งหมด</label></th>
    </thead>
    <tbody>    
    <?php while($rd = dbFetchArray($qs) ) : ?>
    	 <?php $group = $rd['id_group']; ?>
    	<tr >
        	<td style="background-color:#0CC;"><?php echo $rd['group_name']; ?></td>
            <td align="center" style="background-color:#0CC;"><input type="checkbox" id="view_group<?php echo $group; ?>" onchange="group_view_check($(this), <?php echo $rd['id_group']; ?>)" /></td>
            <td align="center" style="background-color:#0CC;"><input type="checkbox" id="add_group<?php echo $group; ?>" onchange="group_add_check($(this), <?php echo $rd['id_group']; ?> )" /></td>
            <td align="center" style="background-color:#0CC;"><input type="checkbox" id="edit_group<?php echo $group; ?>" onchange="group_edit_check($(this), <?php echo $rd['id_group']; ?> )" /></td>
            <td align="center" style="background-color:#0CC;"><input type="checkbox" id="delete_group<?php echo $group; ?>" onchange="group_delete_check($(this), <?php echo $rd['id_group']; ?> )" /></td>
            <td align="center" style="background-color:#0CC;"><input type="checkbox" id="all_group<?php echo $group; ?>" onchange="group_all_check($(this), <?php echo $rd['id_group']; ?> )" /></td>
        </tr>
        <?php $qr = dbQuery("SELECT * FROM tbl_tab WHERE id_group = ".$rd['id_group']." ORDER BY position ASC"); ?>
        <?php while($rs = dbFetchArray($qr) ) : ?>
        	<?php $id = $rs['id_tab']; ?>
        	<?php $ac = isAccess($id_profile, $id); ?>
    	<tr style="font-size:12px;">
        	<td style="padding-left:30px;">
				<?php echo $rs['tab_name']; ?>
                <input type="hidden" name="tab[<?php echo $id; ?>]" value="<?php echo $id; ?>"  />
            </td>
            <td align="center">
            <input type="checkbox" class="view <?php echo $id." view".$group; ?> " name="view[<?php echo $id; ?>]" id="view[<?php echo $id; ?>]" <?php if($ac['view'] == 1 ){ echo "checked"; } ?> value="1" />
            </td>
            <td align="center">
            <input type="checkbox" class="add <?php echo $id." add".$group; ?>" name="add[<?php echo $id; ?>]" id="add[<?php echo $id; ?>]" <?php if($ac['add'] == 1 ){ echo "checked"; } ?> value="1"  />
            </td>
            <td align="center">
            <input type="checkbox" class="edit <?php echo $id." edit".$group; ?>" name="edit[<?php echo $id; ?>]" id="edit[<?php echo $id; ?>]" <?php if($ac['edit'] == 1 ){ echo "checked"; } ?> value="1" />
            </td>
            <td align="center">
            <input type="checkbox" class="delete <?php echo $id." delete".$group; ?>" name="delete[<?php echo $id; ?>]" id="delete[<?php echo $id; ?>]" <?php if($ac['delete'] == 1 ){ echo "checked"; } ?> value="1"  />
            </td>
            <td align="center">
            <input type="checkbox" class="all <?php echo $id." all".$group; ?>" onchange="check($(this), <?php echo $id; ?>)" <?php if($ac['view'] == 1&&$ac['add']==1&&$ac['edit']==1&&$ac['delete']==1 ){ echo "checked"; } ?> />
            </td>
         </tr>
    	<?php endwhile; ?>
     <?php endwhile; ?>
     </tbody>
    </table>
    <?php endif; ?>
<?php else : ?>    
	
<?php 
	$sql = dbQuery("SELECT * FROM tbl_profile WHERE id_profile != 1");
	$row = dbNumRows($sql);
	$n = 1;
	if($row>0) : 
?>
    <table class="table table-striped">
    <thead><th style="widht: 10%; text-align:center">ลำดับ</th><th style="width:75%;">โปรไฟล์</th><th style="width:15%; text-align:right">&nbsp;</th></thead>
    <?php while($rs = dbFetchArray($sql) ) : ?>
    	<tr style="font-size:12px;">
        	<td align="center"><?php echo $n; ?></td>
            <td><?php echo $rs['profile_name']; ?></td>
            <td align="right">
            <?php if($rs['id_profile'] != 1) : ?>
            	<a href="index.php?content=securable&edit_right=y&id_profile=<?php echo $rs['id_profile']; ?>"><button type="button" class="btn btn-warning btn-xs"><i class="fa fa-cog"></i>&nbsp;กำหนดสิทธิ์</button></a></td>
           <?php endif; ?>
        </tr>
    <?php $n++; ?>
    <?php endwhile; ?>
    </table>
    <?php endif; ?>
<?php endif; ?>	
</form>
</div>
</div>
</div>
<script>
function group_view_check(el, id)
{
	if(el.is(":checked")){
		$(".view"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});	
	}else{
		$(".view"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});	
	}
}

function group_add_check(el, id)
{
	if(el.is(":checked")){
		$(".add"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});	
	}else{
		$(".add"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});	
	}
}

function group_edit_check(el, id)
{
	if(el.is(":checked")){
		$(".edit"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});	
	}else{
		$(".edit"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});	
	}
}

function group_delete_check(el, id)
{
	if(el.is(":checked")){
		$(".delete"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});	
	}else{
		$(".delete"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});	
	}
}

function group_all_check(el, id)
{	
	if(el.is(":checked")){
		var view = $("#view_group"+id);
		var add = $("#add_group"+id);
		var edit = $("#edit_group"+id);
		var del  = $("#delete_group"+id);
		view.prop("checked", true);
		group_view_check(view, id);
		add.prop("checked", true);
		group_add_check(add, id);
		edit.prop("checked", true);
		group_edit_check(edit, id);
		del.prop("checked", true);
		group_delete_check(del, id);		
		
	}else{
		$(".all"+id).each(function(index, element) {
			if($(this).is(":checked"))
			{
				$(this).trigger("click");
			}else{
				return false;
			}
		});	
	}
}
/*
function group_all_check(el, id)
{	
	if(el.is(":checked")){
		$(".all"+id).each(function(index, element) {
			if($(this).is(":checked"))
			{
				return false;
			}else{
				$(this).trigger("click");
			}
		});	
	}else{
		$(".all"+id).each(function(index, element) {
			if($(this).is(":checked"))
			{
				$(this).trigger("click");
			}else{
				return false;
			}
		});	
	}
}*/

$("#view_all").change(function(e) {
    if($(this).is(":checked")){
		$(".view").each(function(index, element) {
            $(this).prop("checked",true);
        });
	}else{
		$(".view").each(function(index, element) {
            $(this).prop("checked",false);
        });
	}
});

$("#add_all").change(function(e) {
    if($(this).is(":checked")){
		$(".add").each(function(index, element) {
            $(this).prop("checked",true);
        });
	}else{
		$(".add").each(function(index, element) {
            $(this).prop("checked",false);
        });
	}
});

$("#edit_all").change(function(e) {
    if($(this).is(":checked")){
		$(".edit").each(function(index, element) {
            $(this).prop("checked",true);
        });
	}else{
		$(".edit").each(function(index, element) {
            $(this).prop("checked",false);
        });
	}
});

$("#delete_all").change(function(e) {
    if($(this).is(":checked")){
		$(".delete").each(function(index, element) {
            $(this).prop("checked",true);
        });
	}else{
		$(".delete").each(function(index, element) {
            $(this).prop("checked",false);
        });
	}
});

$("#check_all").change(function(e) {
    if($(this).is(":checked")){
		$("input[type='checkbox']").each(function(index, element) {
            $(this).prop("checked",true);
        });
	}else{
		$("input[type='checkbox']").each(function(index, element) {
            $(this).prop("checked",false);
        });
	}
});

function check(el, id_tab){
	if(el.is(":checked")){
		$("."+id_tab).each(function(index, element) {
            $(this).prop("checked", true);
        });
	}else{
		$("."+id_tab).each(function(index, element) {
            $(this).prop("checked", false);
        });
	}
}

function save(){
	$("#right_form").submit();	
}
</script>