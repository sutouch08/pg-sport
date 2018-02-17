<!-- page place holder -->
<?php
	$pop_on = "sale";
	$sql = dbQuery("SELECT delay, start, end, content, width, height FROM tbl_popup WHERE pop_on = '$pop_on' AND active =1");
	$row = dbNumRows($sql);
	if($row>0){
		list($delay, $start, $end, $content, $width, $height ) = dbFetchArray($sql);
		$popup_content ="<div class='row' ><div class='col-lg-12'>$content</div></div>";
		include "../library/popup.php";
		$today = date('Y-m-d H:i:s');
		if($start<=$today &&$end>=$today){  
			if(!isset($_COOKIE['pop_sale'])){
				setcookie("pop_sale", $pop_on, time()+$delay);
				echo" <script> $(document).ready(function(e) {  $('#modal_popup').modal('show'); }); </script>";
			}
		}
	}
		
?>
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
/*
if(!isset($_COOKIE['id_customer'])){
	
	echo"<form action ='controller/orderController.php?new=y' method='post'>
        <div class='col-lg-4 col-md-4 col-sm-8 col-xs-12 col-lg-offset-4 col-md-offset-4 col-sm-offset-2'>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'><h3>เลือกลูกค้า</h3></div>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><div class='form-group'>
			<select class='form-control' name='id_customer' id='id_customer' >"; customerList(getSaleId($_COOKIE['user_id'])); echo "</select> </div></div>
		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center;'>&nbsp; </div>
		 <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><button class='form-control btn-success' type='submit' >ตกลง</button></div>
		</div>
		</form>
	"; 
}else{*/

	$customer =new customer($id_customer); 
	if(isset($_COOKIE['id_cart'])){ $id_cart = $_COOKIE['id_cart']; }else{ $id_cart="";}
	if(isset($_GET['id_category'])){ $id_cate = $_GET['id_category']; } 
	echo"<div class='modal fade' id='customer_change' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' style='width:300px;'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='myModalLabel'>--- เลือกลูกค้า ---</h4>
									  </div>
									  <div class='modal-body'>
									  <form action ='controller/orderController.php?new=y' method='post'>
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
				<div class='col-lg-3 col-lg-offset-4 col-md-3 col-md-offset-4 col-sm-5 col-sm-offset-3 col-xs-9'>
            		<input type='text' name='search-text' id='search-text' class='form-control input-xs'  /><input type='hidden' id='id_customer' value='$id_customer' />
				</div>
				<div class='col-lg-1 col-md-1 col-sm-2 col-xs-3'>
                <button type='button' class='btn btn-success btn-block' id='search-btn'><span class='glyphicon glyphicon-search'></span></button>
        		</div>
				</div>
		<div class='row'><hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' /></div>
	<form action ='index.php?content=order&new=y' method='post'>
        <div class='row'><div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align:center; font-size:1.5vmin;'><p class='pull-right'>";
		if($id_customer !=0){ echo"
		คุณกำลังสั่งสินค้าให้กับลูกค้า &nbsp; <span class='glyphicon glyphicon-arrow-right' ></span>&nbsp;<b>&nbsp;".$customer->full_name."</b>&nbsp;หากไม่ใช่กรุณา &nbsp;
		<a href='#' data-toggle='modal' data-target='#customer_change'>	
		<button type='button' class='btn btn-warning btn-xs'><span class='glyphicon glyphicon-pencil'></span>แก้ไข</button> </a>
		หรือ <a href='controller/orderController.php?cancle=true&id_cart=$id_cart&id_customer=$id_customer'><button type='button' class='btn btn-danger btn-xs'><span class='glyphicon glyphicon-trash'></span>ยกเลิก</button></a>";
		}else{ echo" คุณยังไม่ได้เลือกลูกค้า ต้องการสั่งสินค้ากรุณา <a href='#' data-toggle='modal' data-target='#customer_change'>	<button type='button' class='btn btn-success btn-xs'><span class='fa fa-user'></span>เลือกลูกค้า</button> </a>";
		} echo"
		</p>
		 </div></div>
		</form>
		<div class='row'><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' /></div>
		
		
		<div class='row xsResponse' ><div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' id='product_grid'>
	";
	if(isset($id_cate)){ product_grid($id_cate, $customer->id_customer); }else{ newProduct(getConfig("NEW_PRODUCT_QTY"),$customer->id_customer ); }
	echo"</div></div>";
?>
<?php  /*<div class="morePost row featuredPostContainer style2 globalPaddingTop " >
    <h3 class="section-title style2 text-center"><span>FEATURES PRODUCT</span></h3>
    <div class="container">
      
	  if(isset($id_customer)){ $id_cus = $id_customer; }else{ $id_cus = 0; }
	   featureProduct(getConfig("FEATURES_PRODUCT"),$id_cus); 
	
    </div>
  </div>  */ ?>
<script>
	$("#search-text").bind("enterKey",function(){
	if($("#search-text").val() != ""){
		$("#search-btn").click();
	}
});
$("#search-text").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$("#search-btn").click(function(e) {
    var query_text = $("#search-text").val();
	var id_customer = $("#id_customer").val();
	if(query_text !=""){
	$.ajax({
		url:"controller/orderController.php?text="+query_text+"&id_customer="+id_customer , type: "GET", cache:false,
		success: function(result){
			$("#product_grid").html(result);
		}
	});
	}
});
function getData(id_product){
	var id_cus = $("#id_customer").val();
	$("#id_product").val(id_product);
	$.ajax({
		url:"../invent/controller/orderController.php?getData&id_product="+id_product+"&id_customer="+id_cus,
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
	function submit_product(){
		var id_product = $("#id_product").val();
		checknewcart(id_product);
	}
</script>
