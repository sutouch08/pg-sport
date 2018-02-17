<?php 
	$page_menu = "invent_category";
	$page_name = "หมวดหมู่";
	$id_tab = 2;
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
	?>
<div class="container">
<!-- page place holder -->
<?php if(isset($_GET['edit'])){
	echo"<form action='controller/categoryController.php?edit=y' method='post'>";
}else if(isset($_GET['add'])){
	echo"<form action='controller/categoryController.php?add=y' method='post'>";
}
?>
<div class="row">
	<div class="col-sm-6"><h2><span class="glyphicon glyphicon-bookmark"></span>&nbsp;หมวดหมู่</h2>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['edit']) || isset($_GET['add'])){
		   echo"
		   <li><a href='index.php?content=category' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li ><a href='#' style='text-align:center; background-color:transparent;'><button type='submit' class='btn btn-link'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	   }else{
		   echo"
       		<li $can_add><a href='index.php?content=category&add=y' style='text-align:center; background-color:transparent;'><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:large;'></span><br />เพิ่มหมวดหมู่</a></li>";
	   }
	   ?>
       </ul>
    </div>
</div>
<hr style="border-color:#CCC; margin-top: 0px; margin-bottom:5px;" />
<!-- End page place holder -->
<?php
if(isset($_GET['add'])){ /////****เพิ่มหมวดหมู่*****/////
////*****แสดงหน้าเพิ่มหมวดหมู่******/////
	echo"
	<div class='col-sm-12' style='background-color:#EEE;'>
<h2></h2>
	
		<table style='width:100%; border:none;'>
        	<tr>
            	<td style='width: 10%; text-align:right;'>ชื่อหมวดหมู่ : </td><td style='width:35%; text-align:left; padding-left:15px;'><input type='text' class='form-control input-sm' name='category_name' id='category_name' required='required' /></td><td></td>
            </tr>
            <tr>
            	<td style='width: 10%; text-align:right; padding-top:15px;'>เปิดใช้งาน : </td>
                <td style='width:35%; text-align:left; padding-left:15px; padding-top:15px;'><div class='row'>&nbsp;&nbsp;<input type='radio' name='active' id='yes' value='1' checked='checked' /><label for='yes' style='margin-left:5px;'><span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='active' id='no' value='0' /><label for='no' style='margin-left:5px;'><span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span></label></div></td><td></td>
            </tr>
            <tr>
            	<td style='width: 10%; text-align:right; padding-top:15px;'>หมวดหมู่หลัก : </td>
                <td style='width:35%; text-align:left; vertical-align:text-top; padding-left:10px; padding-top:15px;'>";
				root_category_tree();
						echo"
							</td><td></td>
            </tr>
        	<tr>
            	<td style='width: 10%; text-align:right; vertical-align:text-top; padding-top:15px;'>คำอธิบาย : </td><td style='width:35%; text-align:left; padding-left:15px; padding-top:15px;'><textarea name='description' rows='8' class='form-control input-sm'></textarea></td><td></td>
            </tr>
            <tr>
            	<td style='width: 10%; text-align:right; vertical-align:text-top; padding-top:15px;'>การเข้าถึง : </td>
                <td style='width:35%; text-align:left; padding-left:15px; padding-top:10px;'>"; customerGroupTable(); echo"</td><td></td>
            </tr>
        </table>
    </form>
    <h2></h2>
</div>";
///*************************  จบหน้าเพิ่มหมวดหมู่  *****************************************///


///******************************* หน้าแก้ไขหมวดหมู่  ************************************///
}else if(isset($_GET['edit'])){ 
	if(isset($_GET['id_category'])){
		$id_category = $_GET['id_category'];
		$qs = dbFetchArray(dbQuery("SELECT * FROM tbl_category WHERE id_category = $id_category"));
		$category_name = $qs['category_name'];
		$description = $qs['description'];
		$parent_id = $qs['parent_id'];
		$position = $qs['position'];
		$active = $qs['active'];
	}
	echo"
	<div class='col-sm-12' style='background-color:#EEE;'>
<h2></h2>
	
		<table style='width:100%; border:none;'>
        	<tr><input type='hidden' value='$id_category' name='id_category'/><input type='hidden' value='$position' name='position' />
            	<td style='width: 10%; text-align:right;'>ชื่อหมวดหมู่ : </td><td style='width:35%; text-align:left; padding-left:15px;'><input type='text' class='form-control input-sm' name='category_name' id='category_name' value='$category_name' required='required' /></td><td></td>
            </tr>
            <tr>
            	<td style='width: 10%; text-align:right; padding-top:15px;'>เปิดใช้งาน : </td>
                <td style='width:35%; text-align:left; padding-left:15px; padding-top:15px;'><div class='row'>&nbsp;&nbsp;
                <input type='radio' name='active' id='yes' value='1'"; if($active==1){ echo "checked='checked'";} echo" />
                <label for='yes' style='margin-left:5px;'><span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type='radio' name='active' id='no' value='0'"; if($active==0){ echo "checked='checked'";} echo" /><label for='no' style='margin-left:5px;'><span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span></label></div></td><td></td>
            </tr>
            <tr>
            	<td style='width: 10%; text-align:right; vertical-align:text-top; padding-top:15px;'>หมวดหมู่หลัก : </td>
                <td style='width:35%; text-align:left; padding-left:10px; padding-top:15px;'>";
				root_category_tree($id_category);
						echo"
							</td><td></td>
            </tr>
        	<tr>
            	<td style='width: 10%; text-align:right; vertical-align:text-top; padding-top:15px;'>คำอธิบาย : </td>
                <td style='width:35%; text-align:left; padding-left:15px; padding-top:15px;'><textarea name='description' rows='8' class='form-control input-sm'>$description</textarea></td><td></td>
            </tr>
            <tr>
            	<td style='width: 10%; text-align:right; vertical-align:text-top; padding-top:15px;'>การเข้าถึง : </td>
                <td style='width:35%; text-align:left; padding-left:15px; padding-top:10px;'>
                	<table class='table table-striped table-condensed'>
                    	<thead><th width='15%'> </th><th width='15%'>ID</th><th width='70%'>กลุมลูกค้า</th></thead>";
						$query = dbQuery("SELECT * FROM tbl_group");
						$rows = dbNumRows($query);
						$n =  0;
						while($n<$rows){
							$res = dbFetchArray($query);
							$id_group = $res['id_group'];
							$group_name = $res['group_name'];
							$qc = dbQuery("SELECT * FROM tbl_category_group WHERE id_category = $id_category AND id_group = $id_group");
							$checked = dbNumRows($qc);
							echo "<tr><td width='15%'><input type='checkbox' id='groupcheck$id_group' value='$id_group' name='groupcheck[]'"; 
							if($checked==1){echo " checked='checked'";} echo "></input></td>
									<td width='15%'>$id_group</td><td width='70%'><label for='groupcheck$id_group'>$group_name</label></td></tr>";
									$n++;
						}
						echo"
                    </table>
                </td><td></td>
            </tr>
        </table>
    </form>
    <h2></h2>
</div>";
///**************************************************  จบหน้าแก้ไขหมวดหมู่  ***********************************///
///************************************************** หน้าหมวดหมู่ปกตก ***************************************///
}else{
	echo"
<div class='col-sm-12'>
<table class='table table-striped'>
<thead>
<tr>
<th width='5%' style='text-align:center'>ID</th><th width='35%'>หมวดหมู่</th><th width='40%'>คำอธิบาย</th>
<th width='5%' style='text-align:center'>ตำแหน่ง</th><th width='5%' style='text-align:center'>สถานะ</th><th colspan='2' style='text-align:center'>การกระทำ</th>
</tr>
</thead>";
	$sql = dbQuery("SELECT * FROM tbl_category WHERE id_category !=0 ORDER BY position ASC");
	$row = dbNumRows($sql);
	$i=0;
	while($i<$row){
		$result = dbFetchArray($sql);
		$id_category = $result['id_category'];
		$category_name = $result['category_name'];
		$description = $result['description'];
		$position = $result['position'];
		$status = $result['active'];
		echo "<tr>
					<td align='center'>$id_category</td><td>$category_name</td><td>$description</td><td align='center'>$position</td>
					<td align='center'>"; if($status ==1){ echo "<span class='glyphicon glyphicon-ok' style='color: #5cb85c;'></span>";}else{ echo "<span class='glyphicon glyphicon-remove' style='color: #d9534f;'></span>";} echo "</td>
					<td align='center'><a href='index.php?content=category&edit=y&id_category=$id_category' $can_edit><button class='btn btn-warning btn-sx'><span class='glyphicon glyphicon-pencil' style='color: #fff;'></span></button></a></td>
					<td align='center'><a href='controller/categoryController.php?delete=y&id_category=$id_category' $can_delete><button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $category_name ');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a></td>
					</tr>";
					$i++;
	}
echo"
</table>
</div>"; }
?>
</div><!--  end Container -->