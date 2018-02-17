<?php 
	$page_menu = "invent_sale";
	$page_name = "พนักงานขาย";
	$id_tab = 27;
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
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['add'])){
		   echo"
		   <li><a href='index.php?content=sale' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a href='#' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='submit_add();'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	   }else if(isset($_GET['edit'])){
		    echo"
		   <li><a href='index.php?content=sale' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a href='#' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='submit_edit();'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	   }else{
		   echo"
		   <li $can_add><a href='index.php?content=sale&add=y' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />$page_name</button></a></li>";
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
	echo"<form name='sale_form' id='sale_form' action='controller/saleController.php?add=y' method='post'>
	<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp;</td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>พนักงาน :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<select name='id_employee' id='id_employee' style='width: 100%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; employeeList(); echo"</select>
			<span class='help-block'>เลือกพนักงานเพื่อกำหนดให้เป็นพนักงานขาย</span></td>
		</td>
		<td id='em_error' style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>เขตการขาย :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<select name='id_group' id='id_group' style='width: 100%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; saleGroupList(); echo"</select>
			<span class='help-block'>กำหนดเขตการขายให้กำพนักงานขาย</span></td>
			<td id='group_error' style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	</table></form>
	";
	
}else if(isset($_GET['edit'])&&isset($_GET['id_sale'])){
	$id_sale = $_GET['id_sale'];
	list($id_employee, $id_gorup) = dbFetchArray(dbQuery("SELECT id_employee, id_group FROM tbl_sale WHERE id_sale = $id_sale"));
	list($first_name, $last_name) = dbFetchArray(dbQuery("SELECT first_name, last_name FROM tbl_employee WHERE id_employee=$id_employee"));
	echo"<form name='sale_form' id='sale_form' action='controller/saleController.php?edit=y' method='post'>
	<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp;</td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>พนักงาน :&nbsp;</td><input type='hidden' name='id_sale' id='id_sale' value='$id_sale' />
		<td width='40%' align='left' style='padding-bottom:10px;'>&nbsp; $first_name $last_name
			<span class='help-block'></span></td>
		</td>
		<td id='em_error'style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>เขตการขาย :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
			<select name='id_group' id='id_group' style='width: 100%; height:30px; border:1px solid #ccc; border-color: #AAB2BD; border-radius: 3px;'>"; saleGroupList($id_gorup); echo"</select>
			<span class='help-block'>กำหนดเขตการขายให้กำพนักงานขาย</span></td>
			<td id='group_error' style='color:red; vertical-align:text-top; padding-left:15px;'>*</td>
	</tr>
	</table></form>
	";
}else{
	echo"
<div class='row'>
<div class='col-sm-12'>
<table class='table table-striped table-hover'>
	<thead style='background-color:#48CFAD;'>
	<th style='width:10%; text-align:center;'>ลำดับ</th><th style='width:30%;' >ชื่อ - สกุล</th><th style='width:25%;' >Email</th><th style='width:25%;' >เขตการขาย</th><th colspan='2' style='width:10%; text-align:center;'>การกระทำ</th>
	</thead>			
	";
	$select = "id_sale, tbl_sale.id_employee, first_name, last_name, email, group_name";
	$sql = dbQuery("SELECT id_sale, tbl_sale.id_employee, first_name, last_name, email, group_name FROM tbl_sale LEFT JOIN tbl_employee ON tbl_sale.id_employee = tbl_employee.id_employee LEFT JOIN tbl_group ON tbl_sale.id_group = tbl_group.id_group");
	$row = dbNumRows($sql);
	$i=0;
	$n=1;
	while($i<$row){
		list($id_sale, $id_employee, $first_name, $last_name, $email, $group_name) = dbFetchArray($sql);
		echo"
		<tr>
		<td align='center'>$n</td>
		<td>$first_name $last_name</td>
		<td>$email</td>
		<td >$group_name</td>
		<td align='center'><a href='index.php?content=sale&edit=y&id_sale=$id_sale' $can_edit>
								<button class='btn btn-warning btn-sx'><span class='glyphicon glyphicon-pencil' style='color: #fff;'></span></button>
								</a>
		</td>
		<td align='center'><a href='controller/saleController.php?delete=y&id_sale=$id_sale' $can_delete>
								<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $first_name $last_name ? ');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button>
								</a>
		</td>
		</tr>";
		$i++; $n++;
	}		
		echo"
</table>
</div>
</div>
	";
}

	?>
</div>
<script>
function submit_add(){
	var em_id = $("#id_employee").val();
	var group_id = $("#id_group").val();
	if(em_id ==""){
		$("#em_error").html("คุณยังไม่ได้เลือกพนักงาน");
		$("#group_error").html("");
		$("#id_employee").focus();
	}else if(group_id ==""){
		$("#group_error").html("คุณยังไม่ได้กำหนดเขตการขาย");
		$("#em_error").html("");
		$("#id_group").focus();
	}else{
		$("#sale_form").submit();
	}
}
function submit_edit(){
	var sale_id = $("#id_sale").val();
	var group_id = $("#id_group").val();
	if(sale_id ==""){
		$("#em_error").html("คุณยังไม่ได้เลือกพนักงาน");
	}else if(group_id ==""){
		$("#group_error").html("คุณยังไม่ได้กำหนดเขตการขาย");
		$("#id_group").focus();
	}else{
		$("#sale_form").submit();
	}
}
</script>