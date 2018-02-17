<?php 
	require LIB_ROOT."class/category.php";
	$page_menu = "invent_discount";
	$page_name = "ส่วนลด";
	$id_tab = 10;
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
	echo"<form id='discount_form' action='controller/discountController.php?edit=y' method='post'>";
}else if(isset($_GET['add'])){
	echo"<form id='discount_form' action='controller/discountController.php?add=y' method='post'>";
}
?>
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-home"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['edit'])&&isset($_GET['id_customer_discount'])){
		    echo"
		   <li><a href='index.php?content=discount' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a style='text-align:center; background-color:transparent;'><button type='submit' class='btn btn-link'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
			}else if(isset($_GET['edit']) || isset($_GET['add'])){
		   echo"
		   <li><a href='index.php?content=discount' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='validate()'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	  		}else if(isset($_GET['view_detail'])){
		   echo"
		   <li><a href='index.php?content=discount' style='text-align:center; background-color:transparent;'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</a></li>";
	   }else{
		   echo"
		   <li $can_add><a href='index.php?content=discount&add=y' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />$page_name</button></a></li>";
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
//*********************************************** เพิ่ม********************************************************// 
if(isset($_GET['add'])){
	echo" 
	<table width='100%' border='0'>
	<tr><td colspan='3'>&nbsp;</td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px;'>ชื่อลูกค้า :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
		<input type='text' name='email' id='email' class='form-control input-sm' "; if(isset($_GET['customer_name'])){echo"value='".$_GET['customer_name']."'";} echo" />
		</td>
		<td style='padding-left:15px; vertical-align:text-top;'><sub style='color:red;'>*</sub></td>
	</tr>
	<tr><td colspan='3'><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' /></td></tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ส่วนลด :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>
		<div class='input-group'>
		<input type='text' name='discount_all' id='discount_all' class='form-control input-sm' style='text-align:right;' "; if(isset($_GET['discount_all'])){echo"value='".$_GET['discount_all']."'";} echo"  />
		<span class='input-group-addon'> % </span></div>
		<span class='help-block'>กำหนดส่วนลดในช่องนี้หากต้องการให้ส่วนลดนี้ในทุกรายการสินค้า</span>	</td><td style='padding-left:15px;'>&nbsp;</td>
	</tr>  
	<tr><td colspan='3'><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' /></td></tr>
	<tr><td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ส่วนลดตามหมวดหมู่ : &nbsp;</td><td colspan='2' align='left' style='padding-bottom:10px;'>หากกำหนดล่วนลดนี้จะยกเลิกการให้ส่วนลดด้านบน</td></tr>
	
	
	";
	$cate = new category;
	$list = $cate->categoryList();
	$row = dbNumRows($list);
	$i =0;
	while($i<$row){
		list($id_category, $category_name, $array) = dbFetchArray($list);
		echo"<tr><td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>$category_name : &nbsp;</td><td width='40%' align='left' style='padding-bottom:10px;'>
		<div class='input-group'>
		<input ty;e='text' class='form-control input-sm' name='category$i' />
		<span class='input-group-addon'> % </span></div>
		</td></tr>";
		$i++;
	}
	
	echo"
	</table></form>
	";
	
//**************************************** จบหน้าเพิ่มที่อยู่ **********************************************//
}else if(isset($_GET['edit'])&&isset($_GET['id_discount'])){
//******************************************  หน้าแก้ไขที่อยู่ *****************************************************//


//****************************************** จบหน้าแก้ไข ****************************************************//
}else if(isset($_GET['view_detail'])&&isset($_GET['id_discount'])){
//////////////////////////////////// แสดงรายละเอียด  //////////////////////////////////

	
///////////////////////////////////// จบหน้าแสดงรายละเอียด  //////////////////////////
}else{

///////////////////////////////// แสดงรายการ ////////////////////////////////

//////////////////////////////// จบแสดงรายการ //////////////////////////////////////////
}
?>
</div>
<script>
$(document).ready(function() {
	$("#email" ).autocomplete(
	{
		 source: 'controller/discountController.php'
	});
});
function validateEmail(email) 
{
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}
function validate(){
	var email = $("#email").val();
	var alias = $("#alias").val();
	var first_name = $("#first_name").val();
	var last_name = $("#last_name").val();
	var address1 = $("#address1").val();
	var city = $("#city").val();
	var phone = $("#phone").val();
	if(email ==""){
		alert("กรุณาใส่อีเมล์");
		$("#email").focus();
	}else if( validateEmail(email)==false){
		alert("อีเมล์ไม่ถูกต้อง");
		$("#email").focus();
	}else if(alias ==""){
		alert("กรุณาระบุชื่อสำหรับเรียกที่อยู่");
		$("#alias").focus();
	}else if(first_name == ""){
		alert("กรุณาใส่ชื่อ");
		$("#first_name").focus();
	}else if(last_name == ""){
		alert("กรุณาใส่นามสกุล");
		$("#last_name").focus();
	}else if( address1 == ""){
		alert("กรุณาใส่ที่อยู่");
		$("#address1").focus();
	}else if(city ==""){
		alert("กรุณาเลือกจังหวัด");
		$("#city").focus();
	}else if(phone ==""){
		alert("กรุณาใส่เบอร์โทรอย่างน้อย 1 เบอร์");
		$("#phone").focus();
	}else{
		$("#address_form").submit();
	}
}
 $("#email").focus();		
</script>