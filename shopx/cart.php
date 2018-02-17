
<!-- styles needed by minimalect -->
<link href='assets/css/jquery.minimalect.min.css' rel='stylesheet'>
<script>
function dropproductcartfull(id_cart,id) {
		  $.ajax({
					type: 'GET',
					url: 'controller/cartController.php?dropproductcartfull=Y&id_cart='+id_cart+'&id_product_attribute='+id,
					success: function(row)	{
						$('#cartfull').html(row);
						dropproductcart(id_cart,id);
						showsumtotal(id_cart);
					}
				});
		
		
}
function updatecart(id_cart,id,n,qty_stock){
	var timer;
	 clearTimeout(timer);
       timer=setTimeout(function validate(){
				var qty = $('#quanity'+n).val();
				if(qty_stock >= qty){
	 			$.ajax({
					type: 'GET',
					url: 'controller/cartController.php?updateproductcartfull=Y&id_cart='+id_cart+'&id_product_attribute='+id+'&qty='+qty,
					success: function(row)	{
							$('#cartfull').html(row);
							dropproductcart(id_cart,'0');
					}
				});
				}else{
					
					$.ajax({
					type: 'GET',
					url: 'controller/cartController.php?updateproductcartfull=Y&id_cart='+id_cart+'&id_product_attribute='+id+'&qty='+qty,
					success: function(row)	{
							$('#cartfull').html(row);
							dropproductcart(id_cart,'0');
							alert("คุณมีอยู่เเล้วในปริมาณที่สูงสุดของสินค้านี้");
					}
					});
				}
				},2000);
		
}
function up(id_cart,id_product_attribute,n,qty_stock){
	var qty = $('#quanity'+n).val();
	qtyup = parseInt(qty)+1;
	if(qty_stock >= qtyup){
		$('#quanity'+n).val(qtyup);
		updatecart(id_cart,id_product_attribute,n,qty_stock);
	}else{
		alert("คุณมีอยู่เเล้วในปริมาณที่สูงสุดของสินค้านี้");
	}
}
function down(id_cart,id_product_attribute,n,qty_stock){
	var qty = $('#quanity'+n).val();
	qtyup = qty-1;
	$('#quanity'+n).val(qtyup);
	updatecart(id_cart,id_product_attribute,n,qty_stock);
}
function not(){	
		$.ajax({
					type: 'GET',
					url: 'controller/cartController.php?checkproductstock=Y',
					success: function(values){
						if(values.trim() >= "1"){
							//$('#confrimorder').submit();
							location.reload();
						}else{
						    confrimorder();
						}
					}
		});
		//return false;
		//return false;
}
function getcondition(){
	if(checkboxes.checked){
		$("#checkcondition").html("<button class='btn btn-primary btn-lg' id='check_condition' width='50%' type='button' onclick='not()'  ><i class='fa fa-arrow-right'></i>&nbsp;ดำเนินการสั่งซื้อ </button>");
	}else{
		$("#checkcondition").html("<button class='btn btn-primary btn-lg' id='check_condition' width='50%'disabled='disabled' ><i class='fa fa-arrow-right'></i>&nbsp; ดำเนินการสั่งซื้อ </button>");
	}
}
function confrimorder() {
	var id_cart = $("#id_cart").val();
	var id_customer = $("#id_customer").val();
	var payment = $("#payment:checked").val();
	var sumtotal_cart = $("#sumtotal_cart").val();
	var shipping = $("#shipping").val();
	var comment = $("#comment").val();
	//alert(payment);
	$("#result").html("<h1>&nbsp;</h1><table style='width: 100%; border:0px;'><tr><td align='center'><i class='fa fa-spinner fa-spin fa-5x'></i><br/><h4>กำลังดำเนินการสั่งซื้อ......</h4></td></tr></table>");
		  $.ajax({
					type: 'GET',
					url: 'controller/cartController.php?confirm=Y&id_cart='+id_cart+'&id_customer='+id_customer+'&payment='+payment+'&sumtotal_cart='+sumtotal_cart+'&shipping='+shipping+'&comment='+comment,
					success: function(data){
						arr = data.split("!");
						message = arr[0].trim();
						if(message == "not"){
							alert(arr[1]);
							$("#result").html("<div class='alert alert-success'>"+arr[1]+"</div>");
						}else{
							createCookie("id_cart","",-1);
							dropproductcart(0,'0');
							$("#result").html("<div class='alert alert-success'>ดำเนินการสั่งซื้อเรียบร้อยแล้ว</div>");
						}
					}
				});
}
function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}
</script>
<div class='container main-container headerOffset'>
  <div class='row'>
    <div class='breadcrumbDiv col-lg-12'>
      <ul class='breadcrumb'>
        <li><a href='index.php'>Home</a> </li>
        <li class='active'>Cart </li>
      </ul>
    </div>
  </div><!--/.row-->
  <div class='row'>
    <div class='col-lg-9 col-md-9 col-sm-7'>
      <h1 class='section-title-inner'><span><i class='glyphicon glyphicon-shopping-cart'></i> ตะกร้าสินค้า</span></h1>
    </div>
    <div class='col-lg-3 col-md-3 col-sm-5 rightSidebar'>
      <h4 class='caps'><a href='index.php'><i class='fa fa-chevron-left'></i>&nbsp;ซื้อสินค้าต่อ</a></h4>
    </div>
  </div><!--/.row-->
  <?php if(isset($_COOKIE['id_cart'])){
	  //echo $_COOKIE['id_customer'];
	  if(isset($_COOKIE['id_customer'])){
		  $id_customer = $_COOKIE['id_customer'];
	  }else{
		  $id_customer = "0";
	  }
  ?>
  <div id='result'>
<div id='cartfull'><?php if(isset($_GET['error'])){
				$error_message = $_GET['error'];
				 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
			} 
			
			echo $cart_mini->cartfull($id_cart,$id_customer); 
			?></div>
			
    <div style='clear:both'></div>
	
		<div id="showcon"><?php 
		
		$cart_mini->confirm($id_cart,$id_customer);?></div>
</div>
  <div style='clear:both'></div><?php }else{
	  if(isset($_GET['finish'])){
		  list($value) = dbFetchArray(dbQuery("select value from tbl_config where id_config = 13"));
		  $search = array("\n","\s");
		  $replace = array("<br>","&nbsp;");
	 	  echo "<div id='cartfull'><div class='alert alert-success'>ดำเนินการสั่งซื้อเรียบร้อยแล้ว</div></div><br>".str_replace($search,$replace,$value);
	  }else{
		 echo "<div id='cartfull'><div class='alert alert-warning'>ไม่มีสินค้าในตะกร้าของคุณ</div></div>"; 
	  }
  }

	  ?>
</div>
<!-- /.main-container-->
<div class='gap'></div>

