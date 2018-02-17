<?php 
	$page_menu = "invent_customer";
	$page_name = "โปรไฟร์";
	$id_tab = 28;
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
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-folder-close"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['edit']) || isset($_GET['add'])){
		   echo"
		   <li><a href='index.php?content=Profile' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a href='#' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='submit_add();'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	   }else{
		   echo"
		   <li $can_add><a href='index.php?content=Profile&add=y' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />$page_name</button></a></li>";
	   }
	   ?>
       </ul>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php
if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
}
if(isset($_GET['add'])){
	echo "<form method='post' name='add_profile' action='controller/profileController.php?add=y' >";
	$id_profile = "";
}else if(isset($_GET['edit'])){
	echo "<form method='post' name='add_profile' action='controller/profileController.php?edit=y'>";
	$id_profile = $_GET['id_profile'];
	list($profile_name) = dbFetchArray(dbQuery("SELECT profile_name from tbl_profile WHERE id_profile = '$id_profile'"));
	echo "<input type='hidden' name='profile_name' id='profile_name' value='$profile_name'/><input type='hidden' name='id_profile' id='id_profile' value='$id_profile'/>";
}
if(isset($_GET['edit']) || isset($_GET['add'])){
	echo"
	<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp;</td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>โปรไฟร์ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'><input type='text' name='profile_namee' id='profile_namee' value='";if(isset($_GET['edit'])){echo $profile_name;}echo "' class='form-control input-sm' required='required' autofocus /></td>
		<td style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	</table></form>";
}else{
	echo "<div class='row'>
	<div class='col-sm-12'>
		<table class='table table-striped table-hover'>
			<thead style='background-color:#48CFAD;'>
				<th style='width:10%; text-align:center;'>ลำดับ</th><th style='width:75%;'>โปรไฟร์</th>
				<th style='width:15%; text-align:center;'>การกระทำ</th>
			</thead>";
			$result = dbQuery("SELECT id_profile,profile_name FROM tbl_profile ");
			$i=0;
			$n=1;
			$row = dbNumRows($result);
			while($i<$row){
				list($id_profile, $profile_name) = dbFetchArray($result);
				echo "<tr>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=Profile&edit=y&id_profile=$id_profile'\">$n</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=Profile&edit=y&id_profile=$id_profile'\">$profile_name</td>
				<td align='center'>
					<a href='index.php?content=Profile&edit=y&id_profile=$id_profile' $can_edit>
						<button class='btn btn-warning btn-sx'>
							<span class='glyphicon glyphicon-pencil' style='color: #fff;'></span>
						</button>
					</a>&nbsp;
					<a href='controller/profileController.php?drop=y&id_profile=$id_profile'  ";if($id_profile == "1"){echo "style='display:none;'";}echo ">
						<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $profile_name ? ');\">
							<span class='glyphicon glyphicon-trash' style='color: #fff;'></span>
						</button>
					</a>
				</td>
				</tr>";
				$i++;
				$n++;
			}
			echo"       
		</table>
	</div> </div>";
}
	?>
</div>
<script>
function submit_add(){
	var profile_name = $("#profile_namee").val();
	if(profile_name == ""){
		alert("ยังไม่ได้ใส่ชื่อโปรไฟร์");
		$("#profile_namee").focus();
	}else{
		document.add_profile.submit();
	}
}
</script>