<?php 
	$page_menu = 'invent_size';
	$page_name = 'ไซด์';
	$id_tab = 4;
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
<div class='container'>
<!-- page place holder -->
<?php 
if(isset($_GET['add'])){
	echo"<form action='controller/sizeController.php?add=y' method='post'>";
}else if(isset($_GET['edit'])){
	echo"<form action='controller/sizeController.php?edit=y' method='post'>";
}
?>
<div class='row'>
	<div class='col-sm-6'><h3><span class='glyphicon glyphicon-tag'></span>&nbsp;รายการไซด์</h3>
	</div>
    <div class='col-sm-6'>
       <ul class='nav navbar-nav navbar-right'>
       <?php if(isset($_GET['add']) || isset($_GET['edit'])){
		   echo"
		   <li><a href='index.php?content=size' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a href='#' style='text-align:center; background-color:transparent;'><button type='submit' class='btn btn-link'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	   }else{
		   echo"
       		<li $can_add><a href='index.php?content=size&add=y' style='text-align:center; background-color:transparent;'><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:large;'></span><br />เพิ่มไซด์ </a></li>";
	   }
	   ?>
       </ul>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:5px;' />
<!-- End page place holder -->
<div class='col-sm-12'>
<?php 
//************************************************************* หน้าแก้ไข *************************************************//
	if(isset($_GET['edit'])&&isset($_GET['id_size'])){
		$id_size = $_GET['id_size'];
		$size = dbFetchArray(dbQuery("SELECT * FROM tbl_size WHERE id_size = $id_size"));
		$size_name = $size['size_name'];
		$position = $size['position'];
		echo"
<div class='col-sm-12'>
<table width='60%'  border='0' align='center'><tr><td colspan='4'>&nbsp;</td></tr>
	<tr><input type='hidden' name='id_size' value='$id_size' /><input type='hidden' name='position' value='$position' />
    	<td width='18%' align='right' valign='middle'>ไซด์ :&nbsp;&nbsp;</td>
		<td width='60%' align='left'><input type='text' class='form-control' name='size_name' value='$size_name' required='required' /></td><td>&nbsp;</td>
		<td width='20%' align='left'><button type='submit' class='btn btn-default' $can_edit>บันทึก</button></td>
	</tr>
	<tr><td colspan='4'>&nbsp;</td></tr>
</table><hr style='border-color:#CCC; margin-top: 5px; margin-bottom:5px;' />
</div>
</form>
";
	}else if(isset($_GET['add'])){ 
	//************************************************************* หน้าเพิ่ม ************************************************//
	echo"
<div class='col-sm-12'>
<table width='60%'  border='0' align='center'><tr><td colspan='4'>&nbsp;</td></tr>
	<tr>
    	<td width='18%' align='right' valign='middle'>ไซด์ :&nbsp;&nbsp;</td>
		<td width='60%' align='left'><input type='text' class='form-control' name='size_name' required='required' /></td><td>&nbsp;</td>
		<td width='20%' align='left'><button type='submit' class='btn btn-default' $can_add>เพิ่ม</button></td>
	</tr>
	<tr><td colspan='4'>&nbsp;</td></tr>
</table><hr style='border-color:#CCC; margin-top: 5px; margin-bottom:5px;' />
</div>
</form>";
	}
	//**********************************************  หน้าแรก ************************************************//
	?>
  </div>
    
<div class='col-sm-12'>

<table class='table table-striped'>
<thead>
<tr>
<th width='5%' style='text-align:center'>ID</th><th width='75%'>ไซด์</th><th width='5%' style='text-align:center'>ตำแหน่ง</th><th width='5%' style='text-align:center'>&nbsp;</th><th colspan='2' style='text-align:center'>การกระทำ</th>
</tr>
</thead>
<?php 
		list($top) = dbFetchArray(dbQuery("SELECT max(position) FROM tbl_size"));
		$sql = dbQuery('SELECT * FROM tbl_size ORDER BY position ASC');
		$row = dbNumRows($sql);
		$i = 0;
		while($i<$row){
			$result = dbFetchArray($sql);
			$id_size = $result['id_size'];
			$size_name = $result['size_name'];
			$position = $result['position'];
			echo "<tr>
						<td align='center'>$id_size</td><td>$size_name</td><td align='center'>$position</td><td align='center'>";
						if($position==1){ 
						echo "<a href='controller/sizeController.php?move=down&id_size=$id_size&position=$position' $can_edit><img title='down' alt='เลื่อนลง' src='../img/down.gif' /></a>";}else if($position==$top){
						echo"<a href='controller/sizeController.php?move=up&id_size=$id_size&position=$position' $can_edit><img title='up' alt='เลื่อนขิ้น' src='../img/up.gif' /></a>";}else{
						echo"<a href='controller/sizeController.php?move=up&id_size=$id_size&position=$position' $can_edit><img title='up' alt='เลื่อนขิ้น' src='../img/up.gif' /></a><br>
								<a href='controller/sizeController.php?move=down&id_size=$id_size&position=$position' $can_edit><img title='down' alt='เลื่อนลง' src='../img/down.gif' /></a>";} 
						echo"
						<td align='center'><a href='index.php?content=size&edit=y&id_size=$id_size' $can_edit><button class='btn btn-warning btn-sx'><span class='glyphicon glyphicon-pencil' style='color: #fff;'></span></button></a></td>
						<td align='center'><a href='controller/sizeController.php?delete=y&id_size=$id_size' $can_delete>
						<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบรายการ $size_name ');\" ><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button>
						</a>
						</td>
					</tr>";
					$i++;
		}
?>
		</table>
</div>
</div><!--  end Container -->