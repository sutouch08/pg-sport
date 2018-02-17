<div class='container'>
<?php
//// step 1 เลือก สี / ไซด์ /  อื่นๆ /////
if(isset($_GET['id_product'])&&isset($_GET['step'])&&$_GET['step']==1){
	$id_product = $_GET['id_product'];
	$colors = dbQuery("SELECT id_color, color_code, color_name, position FROM tbl_color ORDER BY color_code ASC");		
	$sizes = dbQuery("SELECT id_size, size_name, position FROM tbl_size ORDER BY position ASC");
	$attributes = dbQuery("SELECT id_attribute, attribute_name FROM tbl_attribute ORDER BY attribute_name ASC");
	echo"<form method='post' action='index.php?content=attribute_gen&id_product=$id_product&step=2' >
	<div class='row' >
			<div class='col-lg-4'>
			<h4 style='text-align:center;'>เลือกสี</h4>
			<div class='multiselect'>";
			while($color = dbFetchArray($colors)){
				$id_color = $color['id_color'];
				$color_name = $color['color_code']." : ".$color['color_name'];
				echo"<label><input type='checkbox' name='color[]' value='$id_color' style='margin-right:10px;'/>$color_name</label>";
			}
			echo"    
			</div>
			<hr>
				 <h4><label><input type='radio' name='matching' value='color'  checked='checked'style='margin-right:10px;'/>  จับรูปคู่กับสี</label></h4>
			</div>
			<div class='col-lg-4'>
			<h4 style='text-align:center;'>เลือกไซด์</h4>
			<div class='multiselect'>";
			while($size = dbFetchArray($sizes)){
				$id_size = $size['id_size'];
				$size_name = $size['size_name'];
				echo"<label><input type='checkbox' name='size[]' value='$id_size' style='margin-right:10px;'/>$size_name</label>";
			}
			echo"    
			</div>
			<hr>
				 <h4><label><input type='radio' name='matching' value='size' style='margin-right:10px;'/>  จับรูปคู่กับไซต์</label></h4>
			</div>
			<div class='col-lg-4'>
			<h4 style='text-align:center;'>เลือกคุณลักษณะอื่นๆ</h4>
			<div class='multiselect'>";
			while($attr = dbFetchArray($attributes)){
				$id_attr = $attr['id_attribute'];
				$attr_name = $attr['attribute_name'];
				echo"<label><input type='checkbox' name='attribute[]' value='$id_attr' style='margin-right:10px;'/>$attr_name</label>";
			}
			echo"    
			</div>
			<hr>
				 <h4><label><input type='radio' name='matching' value='attribute'  style='margin-right:10px;'/>  จับรูปคู่กับคุณลักษณะอื่นๆ</label></h4>
			</div>
			</div>
			<h4>&nbsp;</h4>
			<div class='row'>
				<div class='col-lg-12'>
				<p class='pull-right'>
					<button type='button' class='btn btn-warning input-medium' onClick='goBack(".$id_product.")'>ยกเลิก</button>
					<button class='btn btn-info input-medium' type='submit'>ถัดไป<i class='fa fa-arrow-circle-right'></i></button>
				</p>
				</div>
			</div>
			</form>";	
}
/// step 2 ///
if(isset($_GET['id_product'])&&isset($_GET['step'])&&$_GET['step']==2){
		$id_product = $_GET['id_product'];
		echo"<form action='controller/productController.php?attribute_gen&id_product=$id_product' method='post' >
		<div class='row'><div class='col-lg-12'><h4>&nbsp;</h4></div>";
		if(isset($_POST['color'])){ $color = $_POST['color']; }
		if(isset($_POST['size'])){ $size = $_POST['size']; }
		if(isset($_POST['attribute'])){ $attr = $_POST['attribute']; }
		if(isset($_POST['matching'])){$matching = $_POST['matching'];}
		if(isset($color)){
				echo"
				<div class='col-lg-4'>
				<div class='panel panel-default'>
				<select name='set_color' id='set_color' class='form-control'  onchange='selected_color()'><option value='1' selected='selected'>1</option>";if(isset($color) && !isset($size) && !isset($attr)){}else{echo "<option value='2'>2</option>";if(isset($color) && isset($size) && isset($attr)){ echo "<option value='3'>3</option>";}}echo "</select>
				<input type='hidden' id='hid_color' value='1'>
				</div>
					<div class='panel panel-default'>
						<div class='panel-heading'><h3 class='panel-title'>สี</h3></div>
					<div class='panel-body'>";
						foreach($color as $colors){
							list($color_code, $color_name) = dbFetchArray(dbQuery("SELECT color_code, color_name FROM tbl_color WHERE id_color = $colors ORDER BY color_code ASC"));
							echo"<p><input type='hidden' name='color[]' value='$colors' />$color_code : $color_name</p>";
						}
				echo"</div>
					</div>
					</div>";
		}//จบ color
		if(isset($size)){
			echo"
				<div class='col-lg-4'>
				<div class='panel panel-default'>
				<select name='set_size' class='form-control' id='set_size' onchange='selected_size()'>";if(!isset($color) && isset($size) && !isset($attr)){echo "<option value='1'>1</option>";}else{if(!isset($color) && isset($size) && isset($attr)){ $nu = 1; echo "<option value='1' selected='selected'>1</option><option value='2' >2</option>";}else{$nu=2;echo "<option value='1'>1</option><option value='2' selected='selected'>2</option>";}if(isset($color) && isset($size) && isset($attr)){ echo "<option value='3'>3</option>";}}echo "</select>
				<input type='hidden' id='hid_size' value='$nu'>
				</div>
					<div class='panel panel-default'>
						<div class='panel-heading'><h3 class='panel-title'>ไซด์</h3></div>
					<div class='panel-body'>";
						foreach($size as $sizes){
							list($size_name) = dbFetchArray(dbQuery("SELECT size_name FROM tbl_size WHERE id_size = $sizes ORDER BY position ASC"));
							echo"<p><input type='hidden' name='size[]' value='$sizes' />$size_name</p>";
						}
				echo"</div>
					</div>
					</div>";
		}//จบ size
		if(isset($attr)){
			echo"
				<div class='col-lg-4'>
					<div class='panel panel-default'>
				<select name='set_attribute' id='set_attribute' class='form-control' onchange='selected_attribute()'><option value='1'>1</option>";if(!isset($color) && !isset($size) && isset($attr)){}else{if(isset($color) && isset($size) && isset($attr)){$num = 3; echo "<option value='2'>2</option><option value='3' selected='selected'>3</option>";}else{$num=2;echo "<option value='2' selected='selected'>2</option>";}}echo "</select>
				<input type='hidden' id='hid_attribute' value='$num'>
				</div>
					<div class='panel panel-default'>
						<div class='panel-heading'><h3 class='panel-title'>คุณลักษณะอื่นๆ</h3></div>
					<div class='panel-body'>";
						foreach($attr as $attribute){
							list($attribute_name) = dbFetchArray(dbQuery("SELECT attribute_name FROM tbl_attribute WHERE id_attribute = '$attribute' ORDER BY attribute_name ASC"));
							echo"<p><input type='hidden' name='attribute[]' value='$attribute' />$attribute_name</p>";
							
						}
				echo"</div>
					</div>
					</div>";
		}//จบ size
		echo"</div><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:5px;'/>";
		$sql = dbQuery("SELECT * FROM tbl_image WHERE id_product = $id_product");
		echo"<div class='row'>";
		if(isset($color) || isset($size) || isset($attr)){
			while($item = dbFetchArray($sql)){
				$product = new product();
				$id_image = $item['id_image'];
				echo"<div class='col-lg-3'>
				<p><img src='".$product->get_image_path($id_image, 2)."' /></p> 
				";
				if($matching == "color"){
						echo "<p><select name='image[]' ><option value='0' >เลือกรูปภาพให้ตรงกับสี</option>";
				foreach($color as $colors){
				
							list($color_code, $color_name) = dbFetchArray(dbQuery("SELECT color_code, color_name FROM tbl_color WHERE id_color = $colors ORDER BY color_code ASC"));
							echo"<option value='$id_image:$colors'>$color_code : $color_name</option>";
				}
				}else if($matching == "size"){
					echo "<p><select name='image[]' ><option value='0' >เลือกรูปภาพให้ตรงกับไซต์</option>";
				foreach($size as $sizes){
							list($size_name) = dbFetchArray(dbQuery("SELECT size_name FROM tbl_size WHERE id_size = '$sizes' ORDER BY size_name ASC"));
							echo"<option value='$id_image:$sizes'>$size_name</option>";
				}
				}else if($matching == "attribute"){
					echo "<p><select name='image[]' ><option value='0' >เลือกรูปภาพให้ตรงกับคุณลักษณะอื่นๆ</option>";
				foreach($attr as $attributes){
					
							list($attribute_name) = dbFetchArray(dbQuery("SELECT attribute_name FROM tbl_attribute WHERE id_attribute = '$attributes' ORDER BY attribute_name ASC"));
							echo"<option value='$id_image:$attributes'>$attribute_name</option>";
				}
				}
				echo"</select></p></div>";
			}
		}
			echo"</div>
			<input type='hidden' name='matching' value='$matching' >
			<h4>&nbsp;</h4>
			<div class='row'><div class='col-lg-2'><a href='javascript:history.back()'><button class='btn btn-info btn-block' type='button'><i class='fa fa-arrow-circle-left'></i> ย้อนกลับ</button></a></div><div class='col-lg-10'><p class='pull-right' style='width:150px;'><button class='btn btn-info btn-block' type='submit'>สร้างรายการ <i class='fa fa-arrow-circle-right'></i></button></p></div></div></form>";	
}

?>
</div>
<style>
.multiselect {
    width:100%;
    height:500px;
    border:solid 1px #c0c0c0;
    overflow:auto;
}
 
.multiselect label {
    display:block;
	margin-left:10px;
}
 
.multiselect-on {
    color:#ffffff;
    background-color:#000099;
}
</style>
<script>
$(function() {
    // $(".multiselect").multiselect();
});
function goBack(id)
{
	window.location.href = "index.php?content=product&edit&id_product="+id+"&tab=2";
}

function selected_color(){
	var color = $("#set_color").val();
	var size = $("#set_size").val();
	var attribute = $("#set_attribute").val();
	var hid_color = $("#hid_color").val();
	if(color == size){
		$("#set_size").val(hid_color);
		$("#hid_size").val(hid_color);
	}else if(color == attribute){
		$("#set_attribute").val(hid_color);
		$("#hid_attribute").val(hid_color);
	}
	$("#hid_color").val(color);
}
function selected_size(){
	var color = $("#set_color").val();
	var size = $("#set_size").val();
	var attribute = $("#set_attribute").val();
	var hid_size = $("#hid_size").val();
	if(size == color){
		$("#set_color").val(hid_size);
		$("#hid_color").val(hid_size);
	}else if(size == attribute){
		$("#set_attribute").val(hid_size);
		$("#hid_attribute").val(hid_size);
	}
	$("#hid_size").val(size);
}
function selected_attribute(){
	var color = $("#set_color").val();
	var size = $("#set_size").val();
	var attribute = $("#set_attribute").val();
	var hid_attribute = $("#hid_attribute").val();
	if(attribute == color){
		$("#set_color").val(hid_attribute);
		$("#hid_color").val(hid_attribute);
	}else if(attribute == size){
		$("#set_size").val(hid_attribute);
		$("#hid_size").val(hid_attribute);
	}
	$("#hid_attribute").val(attribute);
}
</script>