<!-- page place holder -->
<?php 
if(isset($_COOKIE['id_customer'])){ $id_customer = $_COOKIE['id_customer']; }else{ $id_customer = 0;}
if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
}

if(!isset($_COOKIE['id_customer'])){
	echo"<form action ='../controller/orderController.php?new_request=y' method='post'>
		<div class='col-xs-12'><h1>&nbsp;</h1></div>
        <div class='col-lg-4 col-md-4 col-sm-8 col-xs-12 col-lg-offset-4 col-md-offset-4 col-sm-offset-2'>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'><h3>เลือกลูกค้า</h3></div>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><div class='form-group'>
			<select class='form-control' name='id_customer' id='id_customer' >"; customerList(getSaleId($_COOKIE['user_id'])); echo "</select> </div></div>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>&nbsp; </div>
		 <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><button class='form-control btn-success' type='submit' >ตกลง</button></div>
		</div>
		</form>
	"; 
}else{

	$customer =new customer($id_customer); 
	if(isset($_COOKIE['id_request_order'])){ $id_request_order = $_COOKIE['id_request_order']; }else{ $id_request_order="";}
	echo"<div class='modal fade' id='customer_change' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' style='width:300px;'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='myModalLabel'>--- เลือกลูกค้า ---</h4>
									  </div>
									  <div class='modal-body'>
									  <form action ='../controller/orderController.php?new_request=y' method='post'>
									<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><br /><select name='id_customer' id='id_customer' class='form-control input-sm input-sx'>"; 
									customerList(getSaleId($_COOKIE['user_id'])); echo "</select> </div>
									<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>&nbsp; </div>
									  </div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
										<button type='submit' class='btn btn-primary'>ตกลง</button></form>
									  </div>
									</div>
								  </div>
								</div>";
	echo "  <div class='row' style='margin-top:10px;'>
				<input type='hidden' id='id_customer' value='$id_customer' />	
		<div class='row'><hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' /></div>
	<form action ='../controller/orderController.php?new_request=y' method='post'>
        <div class='row'><div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center; font-size:1.5vmin;'><p class='pull-right'>";
		if($id_customer !=0){ echo"
		คุณกำลังอ้างถึงลูกค้า &nbsp; <span class='glyphicon glyphicon-arrow-right' ></span>&nbsp;<b>&nbsp;".$customer->full_name."</b>&nbsp;หากไม่ใช่กรุณา &nbsp;
		<a href='#' data-toggle='modal' data-target='#customer_change'>	
		<button type='button' class='btn btn-warning btn-xs'><span class='glyphicon glyphicon-pencil'></span>แก้ไข</button> </a>
		หรือ <a href='../controller/orderController.php?cancle_request=true&id_request_order=$id_request_order&id_customer=$id_customer'><button type='button' class='btn btn-danger btn-xs'><span class='glyphicon glyphicon-trash'></span>ยกเลิก</button></a>";
		}else{ echo" คุณยังไม่ได้เลือกลูกค้า ต้องการสั่งสินค้ากรุณา <a href='#' data-toggle='modal' data-target='#customer_change'>	<button type='button' class='btn btn-success btn-xs'><span class='fa fa-user'></span>เลือกลูกค้า</button> </a>";
		} echo"
		</p>
		 </div></div>
		</form>
		<div class='row'><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' /></div>";
		echo "<ul class='nav nav-tabs'>";
		
		$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = 0 AND level_depth = 1 ORDER BY position ASC");
				$row = dbNumRows($sql);
				$i=0;
				$a = 0;
				while($i<$row){
				list($id_category, $category_name) = dbFetchArray($sql);
				$sqr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category ORDER BY position ASC");
				$rs = dbNumRows($sqr);
				if($a == 0){ $ac = "active"; }else{ $ac =""; }
				$n=0;
				if($rs<1){
					echo"<li calss='$ac'><a href='#cat-$id_category' role='tab' data-toggle='tab'>$category_name</a>";
					$a++;
				}else{				
				echo"<li class='dropdown'><a id='ul-$id_category' class='dropdown-toggle' data-toggle='dropdown' href='#'>$category_name<span class='caret'></span></a>";
				echo"<ul class='dropdown-menu' role='menu' aria-labelledby='ul-$id_category'>";
				echo"<li class='$ac'><a href='#cat-$id_category' tabindex='-1' role='tab' data-toggle='tab'>$category_name</a></li>";     
				$a++;
				while($n<$rs){
				list($id_sub_category, $sub_category_name) = dbFetchArray($sqr);
				echo" <li class='$ac'><a href='#cat-$id_sub_category' tabindex='-1' role='tab' data-toggle='tab'>$sub_category_name</a></li>";
				$a++;
				$n++;
				}
				echo"</ul></li>";
				}	
				echo "</li>";
				$i++;
				}
				echo "</ul>";
	
echo"
<div class='row'><div class='col-lg-12 col-md-12 col-sm-12 col-sx-12'>
<div class='tab-content'>";	
$query = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE id_category !=0");
	$rc = dbNumRows($query);
	$r =0;
	while($c = dbFetchArray($query)){
		$id_category = $c['id_category'];
		$cate_name = $c['category_name'];
		echo"<div class='tab-pane"; if($r==0){ echo" active";} echo"' id='cat-$id_category'>";	
		$sql = dbQuery("SELECT tbl_category_product.id_product FROM tbl_category_product LEFT JOIN tbl_product ON tbl_category_product.id_product = tbl_product.id_product WHERE id_category = $id_category AND tbl_product.active = 1 ORDER BY product_code ASC");
		$row = dbNumRows($sql); 
		if($row>0){
			$i=0;
			while($i<$row){
				list($id_product) = dbFetchArray($sql);
				$product = new product();
				$product->product_detail($id_product);
				
		 echo"<div class='col-lg-1 col-md-1 col-sm-3 col-xs-4' style='text-align:center;'>
			<div  style='padding:5px;'>
			<div class='image'><a href='#' onclick='getData(".$product->id_product.")'>".$product->getCoverImage($product->id_product,1,"img-responsive")."</a></div>
			<div class='description' style='font-size:10px; min-height:50px;'><a href='#'  onclick='getData(".$product->id_product.")'>".$product->product_code."</a></div>
			  </div></div>";
				$i++;
				$r++;
			}
		}else{ 
			echo"<br/><h4 style='text-align:center;'>ยังไม่มีรายการสินค้า</h4>";
		}
		echo "</div>";
	}	
	echo"</div> <button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button>
</div></div>";	
//************************************ จบ ORDER GRID **********************************************//		
//*********************************** modal attribute grid ***********************//
echo"			
	<form action='../controller/orderController.php?add_to_order' method='post'>
	<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' id='modal'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='modal_title'>title</h4><input type='hidden' name='id_request_order' value='$id_request_order'/><input type='hidden' name='id_customer' value='$id_customer'/>
									  </div>
									  <div class='modal-body' id='modal_body'></div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
										<button type='submit' class='btn btn-primary'>เพิ่มในรายการ</button>
									  </div>
									</div>
								  </div>
								</div></form>";
	//***************************************   End modal  ****************************************//
	//echo"</div></div>"; 
}
	?>

<script>
	function getData(id_product){
	$.ajax({
		url:"../controller/orderController.php?getData&id_product="+id_product,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				$("#modal").css("width",table_w+"px");
				$("#modal_title").html(title);
				$("#modal_body").html(data);
				$("#btn_toggle").click();
			}else{
				alert("NO DATA");
			}		
		}
	});
}
function update_request_cart_mini(id_request_order){
	$.ajax({
		url:"../controller/orderController.php?update_request_detail&id_request_order="+id_request_order,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				//$("#cart_mini_for_mobile").html(dataset);	
				$("#mini_cart").html(dataset);	
			}else{
				alert("ลบไม่สำเร็จ");
			}		
		}
	});	
}
function update_request_cart_mini_mobile(id_request_order){
	$.ajax({
		url:"../controller/orderController.php?update_request_detail_mobile&id_request_order="+id_request_order,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				$("#mini_cart_for_mobile").html(dataset);	
				//$("#txtHint").html(dataset);	
			}else{
				alert("ลบไม่สำเร็จ");
			}		
		}
	});	
}

function update_request_total_mobile(id_request_order){
	$.ajax({
		url:"../controller/orderController.php?update_request_total_mobile&id_request_order="+id_request_order,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				$("#sumtotalcart_for_mobile").html(dataset);	
				$("#sumtotalcart").html(dataset);
				//$("#txtHint").html(dataset);	
			}else{
				alert("ลบไม่สำเร็จ");
			}		
		}
	});	
}
function drop_request_detail(id_request_order, id_product_attribute){
	$.ajax({
		url:"../controller/orderController.php?drop_request_detail&id_request_order="+id_request_order+"&id_product_attribute="+id_product_attribute,
		type:"GET", cache:false, 
		success: function(data){
			var status = data.trim();
			if(status=="deleted"){
				update_request_cart_mini(id_request_order);
				update_request_cart_mini_mobile(id_request_order);
				update_request_total_mobile(id_request_order);
			}else{
				alert("ลบไม่สำเร็จ");
			}		
		}
	});	
}
</script>
