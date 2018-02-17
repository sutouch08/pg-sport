<?php 
	$page_menu = "invent_zone";
	$page_name = "โซนสินค้า";
	$id_tab = 12;
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
<script src="<?php echo WEB_ROOT; ?>library/js/jquery.min.js"></script>
<div class="container">
<?php if(isset($_GET['edit'])){
	echo"<form id='zone_form' action='controller/zoneController.php?edit=y' method='post'>";
}else if(isset($_GET['add'])){
	echo"<form id='zone_form' action='controller/zoneController.php?add=y' method='post'>";
}
?>
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-map-marker"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php if(isset($_GET['edit']) || isset($_GET['add'])){
		   echo"
       		<li><a href='index.php?content=zone' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
	   }else{
		   echo"
       		<li $can_add><a href='index.php?content=zone&add=y' style='text-align:center; background-color:transparent;'><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:large;'></span><br />เพิ่มโซน</a></li>";
	   }
			?>
       </ul>
    </div>
</div>
<hr style="border-color:#CCC; margin-top: 0px; margin-bottom:5px;" />
<!-- End page place holder -->
<?php if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
	if(isset($_GET['edit'])&&isset($_GET['id_zone'])){
	$id_zone = $_GET['id_zone'];
	$zone = dbFetchArray(dbQuery("SELECT * FROM tbl_zone WHERE id_zone = $id_zone"));
	$zone_name = $zone['zone_name'];
	$barcode_zone = $zone['barcode_zone'];
	$id_warehouse = $zone['id_warehouse'];
	///**********************************************  หน้าแก้ไขคลังสินค้า  *******************************************///
		echo"
<div class='col-sm-12'>
<table width='100%'  border='0' align='center'>
	<tr><input type='hidden' name='id_zone' id='id_zone' value='$id_zone' /><input type='hidden' id='valid_name' value='$zone_name'/>
    	<td style='width: 15%; text-align:right;'>ชื่อโซน : </td>
		<td style='width:30%; text-align:left; padding-left:15px;'><input type='text' class='form-control input-sm' name='zone_name' id='zone_name' value='$zone_name' required='required' /></td>
            	<td id='validate_name' style='color:#AAA;'>&nbsp;&nbsp;*เช่น A101, โซน2 เป็นต้น (จำเป็นต้องใส่)</td>
            </tr>
            <tr>
            	<td style='width: 15%; text-align:right; padding-top:15px;'>บาร์โค้ดโซน : </td><input type='hidden' id='valid_code' value='$barcode_zone'/>
                <td style='width:30%; text-align:left; padding-left:15px; padding-top:15px;'><input type='text' class='form-control input-sm' name='barcode_zone' id='barcode_zone' value='$barcode_zone' required='required' /></td>
                <td id='validate_code' style='color:#AAA;'></td>
			</tr>
			<tr>
            	<td style='width: 15%; text-align:right; padding-top:15px;'>คลัง : </td>
                <td style='width:30%; text-align:left; padding-left:15px; padding-top:15px;'><select name='id_warehouse' id='id_warehouse' class='form-control input-sm'>"; warehouseList($id_warehouse); echo"</select></td>
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
	if(isset($_GET['id_warehouse'])){ $id_warehouse = $_GET['id_warehouse'];}else{$id_warehouse= null;}
	echo"
<div class='col-sm-12'>
<table width='100%'  border='0' align='center'>
<tr>
            	<td style='width: 15%; text-align:right; padding-top:15px;'>คลัง : </td>
                <td style='width:30%; text-align:left; padding-left:15px; padding-top:15px;'><select name='id_warehouse' id='id_warehouse' class='form-control input-sm'>"; warehouseList($id_warehouse); echo"</select></td>
                <td style='color:#AAA;'></td>
</tr>
<tr><td colspan='3'>&nbsp;</td></tr>
</table>		<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:5px;' />
<table width='100%'  border='0' align='center'>
	<tr><input type='hidden' name='id_zone' id='id_zone' value='$id_zone' /><input type='hidden' id='valid_name' /><input type='hidden' id='valid_code' value='$barcode_zone'/>
    	<td style='width: 15%; text-align:right;'>ชื่อโซน : </td>
		<td style='width:30%; text-align:left; padding-left:15px;'><input type='text' class='form-control input-sm' name='zone_name' id='zone_name' required='required' /></td>
            	<td id='validate_name' style='color:#AAA;'>&nbsp;&nbsp;*เช่น A101, โซน2 เป็นต้น (จำเป็นต้องใส่)</td>
            </tr>
            <tr>
            	<td style='width: 15%; text-align:right; padding-top:15px;'>บาร์โค้ดโซน : </td>
                <td style='width:30%; text-align:left; padding-left:15px; padding-top:15px;'><input type='text' class='form-control input-sm' name='barcode_zone' id='barcode_zone' required='required' /></td>
                <td id='validate_code' style='color:#AAA;'></td>
			</tr>
			
			<tr>
		<td colspan='2' align='right' style='padding-top: 10px;'><button type='button' class='btn btn-default' onclick='validate()' $can_add>เพิ่ม</button></td><td></td>
	</tr>
</table><hr style='border-color:#CCC; margin-top: 5px; margin-bottom:5px;' />
</div>
</form>
";
	}else{
		echo "<form action='' method='post' >
				<div class='col-lg-4 col-md-4 col-sm-8 col-xs-12 col-lg-offset-4 col-md-offset-4 col-sm-offset-2'>
				<div class='input-group'>
					<span class='input-group-addon'> ค้นหา</span>
						<input type='text' name='search' class='form-control' >
					</div>
				</div>
				</form>
				";	
	}
	?>
    
<div class="col-sm-12">

<?php 
$paginator = new paginator();
if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
if(isset($_POST['search'])){
	$search = $_POST['search'];
	$sql = dbQuery("SELECT id_zone, barcode_zone, zone_name, warehouse_name FROM tbl_zone LEFT JOIN tbl_warehouse ON tbl_zone.id_warehouse = tbl_warehouse.id_warehouse WHERE id_zone !=0 AND (barcode_zone LIKE '%$search%' OR zone_name LIKE '%$search%')");
}else{
	$paginator->Per_Page("tbl_zone LEFT JOIN tbl_warehouse ON tbl_zone.id_warehouse = tbl_warehouse.id_warehouse","WHERE id_zone !=0",$get_rows);
	$paginator->display($get_rows,"index.php?content=zone");
	$Page_Start = $paginator->Page_Start;
	$Per_Page = $paginator->Per_Page;
	$sql = dbQuery("SELECT id_zone, barcode_zone, zone_name, warehouse_name FROM tbl_zone LEFT JOIN tbl_warehouse ON tbl_zone.id_warehouse = tbl_warehouse.id_warehouse WHERE id_zone !=0 LIMIT $Page_Start , $Per_Page");
}
		?>
<table class="table table-striped">
<thead>
<tr>
<th width="5%" style="text-align:center">ID</th><th width="35%">บาร์โค้ดโซน</th><th width="35%">โซน</th>
<th width="15%">คลัง</th><th colspan="2" style="text-align:center">การกระทำ</th>
</tr>
</thead>
<?php 

		
		$row = dbNumRows($sql);
		$i = 0;
		while($i<$row){
			$result = dbFetchArray($sql);
			$id_zone = $result['id_zone'];
			$zone_name = $result['zone_name'];
			$barcode_zone = $result['barcode_zone'];
			$warehouse_name = $result['warehouse_name'];
			echo "<tr>
						<td align='center'>$id_zone</td><td>$barcode_zone</td><td>$zone_name</td><td>$warehouse_name</td>
						<td align='center'><a href='index.php?content=zone&edit=y&id_zone=$id_zone' $can_edit><button class='btn btn-warning btn-sx'><span class='glyphicon glyphicon-pencil' style='color: #fff;'></span></button></a></td>
						<td align='center'><a href='controller/zoneController.php?delete=y&id_zone=$id_zone' $can_delete><button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ  : $zone_name ');\"><span class='glyphicon glyphicon-trash' style='color: #fff;'></span></button></a></td>
					</tr>";
					$i++;
		}
?>
		</table>
        <?php
		echo $paginator->display_pages();
		echo "<br><br>";
		?>
</div>
</div><!--  end Container -->
<script>
$(document).ready(function() {
	$("#zone_name").keyup(function(){
		var zone_name = $("#zone_name").val();
		var warehouse = $("#id_warehouse").val()
		var id = $("#id_zone").val();
		$.ajax({
			type:"GET",
			url:"controller/zoneController.php",
			cache:"false", data:"zone_name="+zone_name+"&id_warehouse="+warehouse+"&id_zone="+id, 
			success: function(msg){
				if($("#zone_name").val().length >3){
					if(msg==1){
						$("#valid_name").val(msg);
						$("#validate_name").html("&nbsp;โซนซ้ำ ไม่อณุญาติให้ชื่อซ้ำกันภายในคลังเดียว");
					}else if(msg==0){
						$("#valid_name").val(msg);
					}
				}
			}
		});
	});
});
$(document).ready(function() {
	$("#barcode_zone").keyup(function(){
		var barcode_zone = $("#barcode_zone").val();
		var warehouse = $("#id_warehouse").val()
		var id = $("#id_zone").val();
		$.ajax({
			type:"GET",
			url:"controller/zoneController.php",
			cache:"false", data:"barcode_zone="+barcode_zone+"&id_warehouse="+warehouse+"&id_zone="+id, 
			success: function(msg){
				if($("#barcode_zone").val().length >3){
					if(msg==1){
						$("#valid_code").val(msg);
						$("#validate_code").html("&nbsp;บาร์โค้ดโซนซ้ำ ไม่อณุญาติให้ใช้บาร์โค้ดซ้ำกันภายในคลังเดียวกัน");
					}else if(msg==0){
						$("#valid_code").val(msg);
					}
				}
			}
		});
	});
});
function validate(){
	var checked_name = $("#valid_name").val();
	var checked_code = $("#valid_code").val();
	var checked_warehouse = $("#id_warehouse").val();
	var zone_name = $("#zone_name").val();
	var barcode_zone = $("#barcode_zone").val();
	if(zone_name==""){
		alert("คุณยังไม่ได้ระบุชื่อโซน");
		return false;
	}
	if(zone_name !="" && checked_name == 1 ){
			alert("ชื่อโซนซ้ำ ไม่อณุญาติให้ใช้ชื่อซ้ำกันภายในคลังเดียวกัน");
			return false;
	}
	if(checked_code == 1){
			alert("บาร์โค้ดโซนซ้ำ ไม่อณุญาติให้ใช้บาร์โค้ดซ้ำกันภายในคลังเดียวกัน");
			return false;
		}
		$("#zone_form").submit();
}

function get_row(){
	$("#rows").submit();
}
</script>