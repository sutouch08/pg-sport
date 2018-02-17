<!-- styles needed by minimalect -->
<link href='assets/css/jquery.minimalect.min.css' rel='stylesheet'>
<div class='container'>
<?php
if(isset($_COOKIE['id_customer'])){ $id_customer = $_COOKIE['id_customer']; }else{ $id_customer = 0;}
if(!isset($_COOKIE['id_customer'])){
	
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
		}else{ echo" คุณยังไม่ได้เลือกลูกค้า ต้องการสั่งสินค้ากรุณา <a href='#' data-toggle='modal' data-target='#customer_change'>	<button type='button' class='btn btn-success'><span class='fa fa-user'></span>เลือกลูกค้า</button> </a>";
		} echo"</p>
		 </div></div>
		</form>
		<div class='row'><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' /></div>
	";
}
?>  
  <div class='row'>
    
    <div class='col-lg-6'>
      <h4 ><a href='index.php?content=order'><i class='fa fa-chevron-left'></i>&nbsp;เลือกสินค้าต่อ </a></h4>
    </div>
  </div><!--/.row-->
  <?php 
  if(isset($_COOKIE['id_request_order'])){
		  if(isset($_COOKIE['id_customer'])){
			  $id_customer = $_COOKIE['id_customer'];
		  }else{
			  $id_customer =0;
		  }
  ?>
<div class='row'>
<div class='col-lg-12'>
<?php 
		if(isset($_GET['error'])){
			$error_message = $_GET['error'];
			 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
		}
echo "<div id='sumary_table'>";
echo $cart_mini->request_sumary($id_request_order);  
echo "</div>";
echo "<h1>&nbsp;</h1>"; // เพิ่มช่องว่างด้านล่าง
?>
</div>
</div>

  <?php
   }else{
	  if(isset($_GET['finish'])){
	 echo "<div id='cartfull'><div class='alert alert-success'>ดำเนินการเรียบร้อยแล้ว </div><div style='text-align:center'><h3><a class='btn btn-lg btn-success' href='../index.php'><i class='fa fa-home'></i>กลับหน้าหลัก</a></h3></div></div>";

	  }else{
		 echo "<div id='cartfull'><div class='alert alert-warning'>ไม่มีสินค้าในตะกร้าของคุณ</div></div>"; 
	  }
  }
	  ?>
</div>
<!-- /.main-container-->
<script>
function update_sumary(id_request_order){
	$.ajax({
		url:"../controller/orderController.php?update_sumary&id_request_order="+id_request_order,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				$("#sumary_table").html(dataset);	
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
				update_sumary(id_request_order);
			}else{
				alert("ลบไม่สำเร็จ");
			}		
		}
	});	
}
function increase_qty(id_request_order, id_product_attribute, n){
	var qty_box = $("#quantity"+n);
	$.ajax({
		url:"../controller/orderController.php?increase_qty&id_request_order="+id_request_order+"&id_product_attribute="+id_product_attribute,
		type:"GET", cache:false, 
		success: function(dataset){
			data = dataset.trim();
			arr = data.split(":");
			var status = arr[0];
			if(status =="ok"){
				qty_box.val(arr[1]);
				$("#total").html("<br>รวมทั้งหมด&nbsp;&nbsp; "+arr[2]+" &nbsp;&nbsp;รายการ<br>&nbsp;");
				update_request_total_mobile(id_request_order);
				update_request_cart_mini_mobile(id_request_order);	
				update_request_cart_mini(id_request_order);
			}else{
				alert("เพิ่มจำนวนไม่สำเร็จ");
			}
		}
	});
}
function decrease_qty(id_request_order, id_product_attribute, n){
	var qty_box = $("#quantity"+n);
	$.ajax({
		url:"../controller/orderController.php?decrease_qty&id_request_order="+id_request_order+"&id_product_attribute="+id_product_attribute,
		type:"GET", cache:false, 
		success: function(dataset){
			data = dataset.trim();
			arr = data.split(":");
			var status = arr[0];
			if(status =="ok"){
				if(arr[1]>0){
				qty_box.val(arr[1]);
				$("#total").html("<br>รวมทั้งหมด&nbsp;&nbsp; "+arr[2]+" &nbsp;&nbsp;รายการ<br>&nbsp;");
				update_request_total_mobile(id_request_order);
				update_request_cart_mini_mobile(id_request_order);	
				update_request_cart_mini(id_request_order);
				}else{
				update_sumary(id_request_order);	
				}
			}else{
				alert("ลดจำนวนไม่สำเร็จ");
			}
		}
	});
}
function update_qty(id_request_order, id_product_attribute, n){
	var qty_box = $("#quantity"+n);
	var qty = qty_box.val();
	$.ajax({
		url:"../controller/orderController.php?update_qty&id_request_order="+id_request_order+"&id_product_attribute="+id_product_attribute+"&qty="+qty,
		type:"GET", cache:false, 
		success: function(dataset){
			data = dataset.trim();
			arr = data.split(":");
			var status = arr[0];
			if(status =="ok"){
				if(arr[1]>0){
				qty_box.val(arr[1]);
				$("#total").html("<br>รวมทั้งหมด&nbsp;&nbsp; "+arr[2]+" &nbsp;&nbsp;รายการ<br>&nbsp;");
				update_request_total_mobile(id_request_order);
				update_request_cart_mini_mobile(id_request_order);	
				update_request_cart_mini(id_request_order);
				}else{
				update_sumary(id_request_order);
				update_request_total_mobile(id_request_order);
				update_request_cart_mini_mobile(id_request_order);	
				update_request_cart_mini(id_request_order);
				}
			}else{
				alert("ลดจำนวนไม่สำเร็จ");
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
</script>


