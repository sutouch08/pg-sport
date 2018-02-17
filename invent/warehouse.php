<?php 
	$page_menu = "invent_warehouse";
	$page_name = "คลังสินค้า";
	$id_tab = 13;
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
    <script src="<?php echo WEB_ROOT; ?>library/js/jquery.min.js"></script>
<?php if(isset($_GET['edit'])){
	echo"<form id='warehouse_form' action='controller/warehouseController.php?edit=y' method='post'>";
}else if(isset($_GET['add'])){
	echo"<form id='warehouse_form' action='controller/warehouseController.php?add=y' method='post'>";
}
?>
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-home"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php if(isset($_GET['edit']) || isset($_GET['add'])){
		   echo"
       		<li><a href='index.php?content=warehouse' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
	   }else{
		   echo"
       		<li $can_add><a href='index.php?content=warehouse&add=y' style='text-align:center; background-color:transparent;'><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:35px;'></span><br />เพิ่มคลัง</a></li>";
	   }
			?>
       </ul>
    </div>
</div>
<hr style="border-color:#CCC; margin-top: 0px; margin-bottom:5px;" />
<!-- End page place holder -->
<div class='row'>
<?php if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
	if(isset($_GET['edit'])&&isset($_GET['id_warehouse'])){
	$id_warehouse = $_GET['id_warehouse'];
	$warehouse = dbFetchArray(dbQuery("SELECT * FROM tbl_warehouse WHERE id_warehouse = $id_warehouse"));
	$warehouse_name = $warehouse['warehouse_name'];
	$active = $warehouse['active'];
	///**********************************************  หน้าแก้ไขคลังสินค้า  *******************************************///
		echo"
<div class='col-sm-12'>
<table width='60%'  border='0' align='center'>
	<tr><input type='hidden' name='id_warehouse' value='$id_warehouse' /><input type='hidden' id='valid' />
    	<td style='width: 15%; text-align:right;'>ชื่อคลัง : </td>
		<td style='width:50%; text-align:left; padding-left:15px;'><input type='text' class='form-control input-sm' name='warehouse_name' id='wh_name' value='$warehouse_name' required='required' /></td>
            	<td id='validate' style='color:#AAA;'>&nbsp;&nbsp;*เช่น คลังหลัก เป็นต้น (จำเป็นต้องใส่)</td>
            </tr>
            <tr>
            	<td style='width: 15%; text-align:right; padding-top:15px;'>เปิดใช้งาน : </td>
                <td style='width:50%; text-align:left; padding-left:15px; padding-top:15px;'><div class='row'>&nbsp;&nbsp;<input type='radio' name='active' id='yes' value='1'"; if($active==1){echo" checked='checked'";} echo" /><label for='yes' style='margin-left:5px;'><span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='active' id='no' value='0'"; if($active==0){ echo"checked='checked'";} echo" /><label for='no' style='margin-left:5px;'><span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span></label></div></td>
                <td style='color:#AAA;'></td>
			</tr>
			<tr>
		<td colspan='2' align='right' style='padding-top: 10px;'><button type='button' class='btn btn-default' onclick='validate()' $can_edit>บันทึก</button></td><td></td>
	</tr>
</table><hr style='border-color:#CCC; margin-top: 5px; margin-bottom:5px;' />
</div>
</form>
";
///****************************************************  จบหน้าแก้ไขคลังสินค้า  ******************************************//
//*****************************************************  เริ่มหน้าเพิ่มคลังสินค้า  ******************************************//
	}else if(isset($_GET['add'])){ 
	echo"
<div class='col-sm-12'>
<table width='60%'  border='0' align='center'>
	<tr><input type='hidden' id='valid' />
    	<td style='width: 15%; text-align:right;'>ชื่อคลัง : </td>
		<td style='width:50%; text-align:left; padding-left:15px;'><input type='text' class='form-control input-sm' name='warehouse_name' id='wh_name' required='required' /></td>
            	<td id='validate' style='color:#AAA;'>&nbsp;&nbsp;*เช่น คลังหลัก เป็นต้น (จำเป็นต้องใส่)</td>
            </tr>
            <tr>
            	<td style='width: 15%; text-align:right; padding-top:15px;'>เปิดใช้งาน : </td>
                <td style='width:50%; text-align:left; padding-left:15px; padding-top:15px;'><div class='row'>&nbsp;&nbsp;<input type='radio' name='active' id='yes' value='1' checked='checked' /><label for='yes' style='margin-left:5px;'><span class='glyphicon glyphicon-ok' style='color: #5cb85c; font-size:20px;'></span></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='active' id='no' value='0' /><label for='no' style='margin-left:5px;'><span class='glyphicon glyphicon-remove' style='color: #d9534f; font-size:20px;'></span></label></div></td>
                <td style='color:#AAA;'></td>
			</tr>
			<tr>
		<td colspan='2' align='right' style='padding-top: 10px;'><button type='button' class='btn btn-default' onclick='validate()' $can_add>บันทึก</button></td><td></td>
	</tr>
</table><hr style='border-color:#CCC; margin-top: 5px; margin-bottom:5px;' />
</div>
</form>
";
	}
	?>
<div class="col-sm-12">

<table class="table table-striped">
<thead>
<tr>
<th width="5%" style="text-align:center">ID</th><th width="80%">คลังสินค้า</th>
<th width="5%" style="text-align:center">สถานะ</th><th colspan="2" style="text-align:center">การกระทำ</th>
</tr>
</thead>
<?php 
		$sql = dbQuery("SELECT * FROM tbl_warehouse");
		$row = dbNumRows($sql);
		$i = 0;
		while($i<$row){
			$result = dbFetchArray($sql);
			$id_warehouse = $result['id_warehouse'];
			$warehouse_name = $result['warehouse_name'];
			$active = $result['active'];
			echo "<tr>
						<td align='center'>$id_warehouse</td><td>$warehouse_name</td><td align='center'>"; if($active ==1){ echo "<span class='glyphicon glyphicon-ok' style='color: #5cb85c;'></span>";}else{ echo "<span class='glyphicon glyphicon-remove' style='color: #d9534f;'></span>";} echo"</td>
						<td align='center'><a href='index.php?content=warehouse&edit=y&id_warehouse=$id_warehouse' $can_edit><button class='btn btn-warning btn-sx'><span class='glyphicon glyphicon-pencil' style='color: #fff;'></span></button></a></td>
						<td align='center'>";
						if($id_warehouse == 1 || 2 ){ 
						echo"<button class='btn btn-danger btn-sx' disabled='disabled'><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button>";
						}else{ echo"
						<a href='controller/warehouseController.php?delete=y&id_warehouse=$id_warehouse' $can_delete><button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ  : $warehouse_name ');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a>"; } echo"</td>
					</tr>";
					$i++;
		}
?>
		</table>
</div>
</div>
</div><!--  end Container -->
<script>
$(document).ready(function(){
	$("#wh_name").keyup(function(){
		var reference = $("#wh_name").val();
		$.ajax({	
			type: "GET", 
			url:"controller/warehouseController.php",
			cache:false, data:"warehouse_name="+reference,
			success: function(msg)	{
				if( $("#wh_name").val().length >3){
						if(msg==1){
							$("#valid").val(msg);
							$("#validate").html("&nbsp;&nbsp;ชื่อคลังซ้ำ กรุณาเปลี่ยน");
						}else if(msg==0){
							$("#valid").val(msg);
							$("#validate").html(" ");
						}
				
				}
			}
		});
	});
});
function validate(){
	var checked = $("#valid").val();
	if(checked==1){
		alert ("ชื่อคลังซ้ำ กรุณาเปลี่ยนใหม่");
		$("#wh_name").focus();
		}else{
		$("#warehouse_form").submit();
	}
}	
</script>