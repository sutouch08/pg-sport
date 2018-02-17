<script>
function dropproductcart(id_cart,id) {
		  $.ajax({
					type: "GET",
					url: "controller/cartController.php?dropproductcart=Y&id_cart="+id_cart+"&id_product_attribute="+id,
					success: function(row)	{
						$('#mini_cart').html(row);
						$('#mini_cart_for_mobile').html(row);
						showsumtotal(id_cart);
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

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie() {
	createCookie("id_cart","",-1);
}
			function checknewcart(id_product){
			if($('#id_cart').val() == ''){
				newcart(id_product);
				//addtocart();
				//ทำเรื่องตะกร้าสินค้าต่อ
			}else{
				var id_cart = $('#id_cart').val();
				addtocart(id_cart,id_product);
			}
		}
		function newcart(id_product){
			//
			$.ajax({
					type: 'GET',
					url: 'controller/cartController.php?newcart=Y',
					success: function(id)	{
						id_ca = id.trim();
						createCookie('id_cart',id_ca,1)
						$('#id_cart').val(id_ca);
						addtocart(id_ca,id_product);
					}
				});
				
		}
        function addtocart(id_cart, id_product)
		{
			//var loop = $('#loop'+id_product).val();
			//alert(id_product);
			var i = 0;
			while( i < $('#loop'+id_product).val()) {
				var m = i+1;
				var id_product_attribute = $('#id_product_attribute'+id_product+m).val();
				var number = $('#number'+id_product_attribute).val();
				var qty = $('#qty'+id_product_attribute).val();
				
				if(number != ''){
				var dataString = {number:number,id_product_attribute:id_product_attribute,id_cart:id_cart,qty:qty};
				$.ajax({
					type: 'POST',
					url: 'controller/cartController.php?addtocart=Y',
					data: dataString,
					success: function(row){
						if(parseInt(row) == "1"){
							alert("คุณมีอยู่เเล้วในปริมาณที่สูงสุดของสินค้านี้");
						}else{
							$('#mini_cart').html(row);
							$('#mini_cart_for_mobile').html(row);
							showsumtotal(id_cart);
							
						}
					}
				});
				$('#number'+id_product_attribute).val('');
				}
				i++
				
			} 
			$('#order_grid').modal('hide');
			$('body').removeClass('modal-open');
			$('.modal-backdrop').remove();
	}
		function showsumtotal(id_cart){
		$.ajax({
					type: "GET",
					url: "controller/cartController.php?checksumtotal=Y&id_cart="+id_cart,
					success: function(total)	{
						if(total != "null"){
							$('#sumtotalcartin').html("ราคารวม : "+total);
							$('#sumtotalcart').html("ตะกร้า("+total+")");
							$('#sumtotalcartin_for_mobile').html("ราคารวม : "+total);
							$('#sumtotalcart_for_mobile').html("ตะกร้า("+total+")");
						}else{
							$('#sumtotalcartin').html("ราคารวม : ");
							$('#sumtotalcart').html("ตะกร้า(ว่างเปล่า)");
							$('#sumtotalcartin_for_mobile').html("ราคารวม : "+total);
							$('#sumtotalcart_for_mobile').html("ตะกร้า("+total+")");
						}
					}
					
				});
						}		
        </script>
        
<?php
if(isset($_GET['id_cart'])){
	$id_cart = $_GET['id_cart'];
	require "../library/config.php";
	$product = new Product();
	$cart_mini = new cart();
	
} else if(isset($_COOKIE['id_cart'])){
	$id_cart = $_COOKIE['id_cart'];
	$product = new Product();
	$cart_mini = new cart();

}else{
	$product = new Product();
	$cart_mini = new cart();
	$id_cart = '';

}
?>