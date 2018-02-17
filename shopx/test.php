<!-- Placed at the end of the document so the pages load faster --> 
<script type='text/javascript' src='assets/js/jquery/1.8.3/jquery.js'></script> 
<script src='assets/bootstrap/js/bootstrap.min.js'></script> 
<?php 
echo"
<table >

    <tr><td><input type ='text' name='number1' id='number1'/></td><input type='hidden' name='qty1' id='qty1' value='100' /></tr>

</table>";
?>

<script>
$(document).ready(function(){
									$('#number1').keyup(function(){
								var n_order = $('#number1').val();
								var n_stock = $('#qty1').val();
								if(n_order>n_stock){
									alert('มีสินค้าในสต็อกแค่'+ n_stock);
									$('#number1').val(n_stock);
									}
									});
								});
								</script>

<!-- Placed at the end of the document so the pages load faster --> 
<script type='text/javascript' src='assets/js/jquery/1.8.3/jquery.js'></script> 
<script src='assets/bootstrap/js/bootstrap.min.js'></script> 

<!-- include jqueryCycle plugin --> 
<script src='assets/js/jquery.cycle2.min.js'></script> 

<!-- include easing plugin --> 
<script src='assets/js/jquery.easing.1.3.js'></script> 

<!-- include  parallax plugin --> 
<script type='text/javascript'  src='assets/js/jquery.parallax-1.1.js'></script> 

<!-- optionally include helper plugins --> 
<script type='text/javascript'  src='assets/js/helper-plugins/jquery.mousewheel.min.js'></script> 

<!-- include mCustomScrollbar plugin //Custom Scrollbar  --> 

<script type='text/javascript' src='assets/js/jquery.mCustomScrollbar.js'></script> 
<!-- include checkRadio plugin //Custom check & Radio  --> 
<script type='text/javascript' src='assets/js/ion-checkRadio/ion.checkRadio.min.js'></script> 

<!-- include grid.js // for equal Div height  --> 
<script src='assets/js/grids.js'></script> 

<!-- include carousel slider plugin  --> 
<script src='assets/js/owl.carousel.min.js'></script> 

<!-- jQuery minimalect // custom select   --> 
<script src='assets/js/jquery.minimalect.min.js'></script> 

<!-- include touchspin.js // touch friendly input spinner component   --> 
<script src='assets/js/bootstrap.touchspin.js'></script> 

<!-- include custom script for site  --> 
<script src='assets/js/script.js'></script>