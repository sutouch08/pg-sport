<?php 
	$page_menu = "invent_color";
	$page_name = "สี";
	$id_tab = 3;
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
<?php if(isset($_GET['edit'])){
	echo"<form action='controller/colorController.php?edit=y' method='post'>";
}else if(isset($_GET['add'])){
	echo"<form action='controller/colorController.php?add=y' method='post'>";
}
?>
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-tint"></span>&nbsp;รายการสี</h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php if(isset($_GET['edit']) || isset($_GET['add'])){
		   echo"
       		<li><a href='index.php?content=color' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a href='#' style='text-align:center; background-color:transparent;'><button type='submit' class='btn btn-link'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	   }else{
		   echo"
       		<li $can_add><a href='index.php?content=color&add=y' style='text-align:center; background-color:transparent;'><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:large;'></span><br />เพิ่มสี</a></li>";
	   }
			?>
       </ul>
    </div>
</div>
<hr style="border-color:#CCC; margin-top: 0px; margin-bottom:5px;" />
<!-- End page place holder -->

<?php 
	if(isset($_GET['edit'])&&isset($_GET['id_color'])){
	$id_color = $_GET['id_color'];
	$color = dbFetchArray(dbQuery("SELECT * FROM tbl_color WHERE id_color = $id_color"));
	$color_code = $color['color_code'];
	$color_name = $color['color_name'];
	$position = $color['position'];
		echo"
<div class='col-sm-12'>
<table width='60%'  border='0' align='center'>
	<tr><input type='hidden' name='id_color' value='$id_color' /><input type='hidden' name='position' value='$position' />
    	<td style='width: 10%; text-align:right;'>รหัสสี : </td><td style='width:35%; text-align:left; padding-left:15px;'><input type='text' class='form-control input-sm' name='color_code' id='color_code' value='$color_code' required='required' /></td>
            	<td style='color:#AAA;'>&nbsp;&nbsp;*เช่น AW, BW เป็นต้น (จำเป็นต้องใส่)</td>
            </tr>
            <tr>
            	<td style='width: 10%; text-align:right; padding-top:15px;'>คำอธิบาย : </td>
                <td style='width:35%; text-align:left; padding-left:15px; padding-top:15px;'><input type='text' class='form-control input-sm' name='color_name' id='color_name' value='$color_name'  /></td>
                <td style='color:#AAA;'>&nbsp;&nbsp;*เช่น ดำ-ขาว, น้ำเงิน-ขาว เป็นต้น (ใส่หรือไม่ก็ได้)</td>
			</tr>
			<tr>
		<td colspan='2' align='right' style='padding-top: 10px;'><button type='submit' class='btn btn-default'>บันทึก</button></td><td></td>
	</tr>
</table><hr style='border-color:#CCC; margin-top: 5px; margin-bottom:5px;' />
</div>
</form>
";
	}else if(isset($_GET['add'])){ 
	echo"
	<div class='col-sm-12'>
<table width='60%'  border='0' align='center'>
	<tr>
    	<td style='width: 10%; text-align:right;'>รหัสสี : </td><td style='width:35%; text-align:left; padding-left:15px;'><input type='text' class='form-control input-sm' name='color_code' required='required' /></td>
            	<td style='color:#AAA;'>&nbsp;&nbsp;*เช่น AW, BW เป็นต้น (จำเป็นต้องใส่)</td>
            </tr>
            <tr>
            	<td style='width: 10%; text-align:right; padding-top:15px;'>คำอธิบาย : </td>
                <td style='width:35%; text-align:left; padding-left:15px; padding-top:15px;'><input type='text' class='form-control input-sm' name='color_name' /></td>
                <td style='color:#AAA;'>&nbsp;&nbsp;*เช่น ดำ-ขาว, น้ำเงิน-ขาว เป็นต้น (ใส่หรือไม่ก็ได้)</td>
			</tr>
			<tr>
		<td colspan='2' align='right' style='padding-top: 10px;'><button type='submit' class='btn btn-default'>บันทึก</button></td><td></td>
	</tr>
</table><hr style='border-color:#CCC; margin-top: 5px; margin-bottom:5px;' />
</div>
</form>";
	}
	?>
<div class="col-sm-12">
<table class="table table-striped">
<thead>
<tr>
<th width="5%" style="text-align:center">ID</th><th width="35%">รหัสสี</th><th width="40%">คำอธิบาย</th>
<th width="5%" style="text-align:center">ตำแหน่ง</th><th colspan="2" style="text-align:center">การกระทำ</th>
</tr>
</thead>
<?php 
		$sql = dbQuery("SELECT * FROM tbl_color");
		$row = dbNumRows($sql);
		$i = 0;
		while($i<$row){
			$result = dbFetchArray($sql);
			$id_color = $result['id_color'];
			$color_code = $result['color_code'];
			$color_name = $result['color_name'];
			$position = $result['position'];
			echo "<tr>
						<td align='center'>$id_color</td><td>$color_code</td><td>$color_name</td><td align='center'>$position</td>
						<td align='center'><a href='index.php?content=color&edit=y&id_color=$id_color' $can_edit><button class='btn btn-warning btn-sx'><span class='glyphicon glyphicon-pencil' style='color: #fff;'></span></button></a></td>
						<td align='center'><a href='controller/colorController.php?delete=y&id_color=$id_color' $can_delete><button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $color_code : $color_name ');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a></td>
					</tr>";
					$i++;
		}
?>
		</table>
</div>
</div><!--  end Container -->