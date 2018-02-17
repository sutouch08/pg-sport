<?php 
	$page_menu = "invent_customer_group";
	$page_name = "กลุ่มลูกค้า(พื้นที่การขาย)";
	$id_tab = 23;
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
	echo"<form id='group_form' action='controller/groupController.php?edit=y' method='post'>";
}else if(isset($_GET['add'])){
	echo"<form id='group_form' action='controller/groupController.php?add=y' method='post'>";
}
?>
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-filter"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['edit'])&&isset($_GET['id_group'])){
		    echo"
		   <li><a href='index.php?content=group' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a style='text-align:center; background-color:transparent;'><button type='submit' class='btn btn-link'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
			}else if(isset($_GET['edit']) || isset($_GET['add'])){
		   echo"
		   <li><a href='index.php?content=group' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='validate()'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	  		}else if(isset($_GET['view_detail'])){
		   echo"
		   <li><a href='index.php?content=group' style='text-align:center; background-color:transparent;'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</a></li>";
	   }else{
		   echo"
		   <li $can_add><a href='index.php?content=group&add=y' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />$page_name</button></a></li>";
	   }
	   ?>
       </ul>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php 
//////////////////// เพิ่ม ///////////////////////////
if(isset($_GET['add'])){
	echo"
	<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp;</td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>ชื่อกลุ่ม :&nbsp;</td><input type='hidden' id='valid' />
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='group_name' id='group_name' class='form-control input-sm' "; if(isset($_GET['group_name'])){echo"value='".$_GET['group_name']."'";} echo" /></td>
		<td style='padding-left:15px; vertical-align:text-top;' id='valid_name'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>แสดงราคา :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>";
			if(isset($_GET['show_price'])){ 
				if($_GET['show_price']==1){ $yes = " checked='checked'";}else{	$yes = "";}
				if($_GET['show_price']==0){ $no = "checked = 'checked'";}else{ $no = "";}
			}else{
				$yes = "checked='checked'";
				$no = "";
			} echo"
		<input type='radio' name='show_price' id='yes' value='1' $yes /><label for='yes'><span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px; margin-right:15px;'></span></label>
		<input type='radio' name='show_price' id='no' value='0' $no /><label for='no'><span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span></label>
			<span class='help-block'>กำหนดให้แสดงราคากับลูกค้าที่อยู่ในกลุ่มนี้หรือไม่</span></td><td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	</table></form>
	";
///////////////////// จบหน้าเพิ่ม //////////////////
}else if(isset($_GET['edit'])&&isset($_GET['id_group'])){
	///////////////////////  แก้ไข  ///////////////////////////
	$id_group = $_GET['id_group'];
	$data = getGroupDetail($id_group);
	echo"
	<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp;</td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>ชื่อกลุ่ม :&nbsp;</td><input type='hidden' name='id_group' id='id_group' value='$id_group' /><input type='hidden' id='valid' />
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='group_name' id='group_name' class='form-control input-sm' value='".$data['group_name']."' /></td>
		<td style='padding-left:15px; vertical-align:text-top;' id='valid_name'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>แสดงราคา :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>";
		if($data['show_price']==1){ $yes = "checked='checked'"; $no = "";}else{ $yes = ""; $no="checked='checked'";} echo"
		<input type='radio' name='show_price' id='yes' value='1' $yes /><label for='yes'><span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px; margin-right:15px;'></span></label>
		<input type='radio' name='show_price' id='no' value='0' $no /><label for='no'><span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span></label>
			<span class='help-block'>กำหนดให้แสดงราคากับลูกค้าที่อยู่ในกลุ่มนี้หรือไม่</span></td><td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	</table></form>
	";
	/////////////////////  จบหน้าแก้ไข  /////////////////////
}else if(isset($_GET['view_detail'])&&isset($_GET['id_group'])){
	//////////////////////   รายละเอียด  ////////////////////
	$id_group = $_GET['id_group'];
	list($group_name) = dbFetchArray(dbQuery("SELECT group_name FROM tbl_group WHERE id_group = ".$id_group));
	$qs = dbQuery("SELECT * FROM tbl_customer WHERE id_default_group = ".$id_group);
	$member = dbNumRows($qs);
	echo"<div class='row'>
			<div class='col-sm-12'>
				<div class='alert alert-info'>
				<p>ชื่อกลุ่ม : ".$group_name."</p>
				<p>สมาชิกในกลุ่ม : ".$member." &nbsp;คน</p>
				</div>
				<h3>สมาชิกในกลุ่มนี้</h3>
				<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
				<table class='table table-striped table-hover'>
					<thead>
						<th style='width:15%; text-align:center;'>ID</th><th style='width:35%;'>ชื่อ - สกุล</th><th style='width:30%;'>อีเมล์</th><th style='width:20%; text-align:center;'>วันที่เป็นสมาชิก</th>
					</thead>";
				if($member > 0){
					while($rs = dbFetchArray($qs)){
						$id_customer = $rs['id_customer'];
						echo"
					<tr>
					<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">".$rs['id_customer']."</td>
					<td style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">".$rs['first_name']." ".$rs['last_name']."</td>
					<td style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">".$rs['email']."</td>
					<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=customer&view_detail=y&id_customer=$id_customer'\">". thaiTextDate($rs['date_add'])."</td>
					</tr>";
					}
				}else{
					echo"<tr><td colspan='4' align='center'><h3>ไม่มีสมาชิกในกลุ่มนี้</h3></td></tr>";
				}
				echo"</table></div></div>";
	
	//////////////////////////  จบหน้ารายละเอียด   //////////////////
}else{
	/////////////////////////  แสดงรายการ   //////////////////////
	echo"</form>";
	echo"
<div class='row'>
<div class='col-sm-12'>
<table class='table table-striped table-hover'>
	<thead style='background-color:#48CFAD;'>
		<th style='width:10%; text-align:center;'>ID</th><th style='width:60%;'>ชื่อ</th>
		<th style='width:10%; text-align:center;'>แสดงราคา</th><th style='width:10%; text-align:center;'>สมาชิก</th><th colspan='2' style='text-align:center;'>การกระทำ</th>
	</thead>";
	$sql = dbQuery("SELECT * FROM tbl_group");
	$row = dbNumRows($sql);
	$i = 0;
	if($row>0){
		while($i<$row){
			list($id_group, $group_name, $show_price, $date_add, $date_upd) = dbFetchArray($sql);
			$member = dbNumRows(dbQuery("SELECT * FROM tbl_customer_group WHERE id_group = $id_group"));
			echo"
	<tr>
		<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=group&view_detail=y&id_group=$id_group'\">$id_group</td>
		<td style='cursor:pointer;' onclick=\"document.location='index.php?content=group&view_detail=y&id_group=$id_group'\">$group_name</td>
		<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=group&view_detail=y&id_group=$id_group'\">"; 
			if($show_price==1){echo "<span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span>";}else{echo"<span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'>";} echo"</td>
		<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=group&view_detail=y&id_group=$id_group'\">$member</td>
		<td align='center'>
			<a href='index.php?content=group&edit=y&id_group=$id_group' $can_edit><button class='btn btn-warning btn-sx'><span class='glyphicon glyphicon-pencil' style='color: #fff;'></span></button></a>
		</td>
		<td align='center'>"; if($id_group !=1){echo"
			<a href='controller/groupController.php?delete=y&id_group=$id_group' $can_delete>
					<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบกลุ่ม $group_name');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button>
			</a>";} echo"
		</td>
		</tr>";
		$i++;
		}
	}else{
		echo"<tr><td colspan='7' align='center'><h3>ยังไม่มีกลุ่ม</h3></td></tr>";
	}
	echo"</table></div></div>";
	
	
}
?>
</div>
<script>
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