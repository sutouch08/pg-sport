<?php
	$page_menu = "sale_dashboard";
	$page_name = "HOME";
	$id_profile = $_COOKIE['profile_id'];
	$id_user= $_COOKIE['user_id'];
   
	$sale = new sale($id_user);	
	?>
    <?php
	$pop_on = "sale";
	$sql = dbQuery("SELECT delay, start, end, content, width, height FROM tbl_popup WHERE pop_on = '$pop_on' AND active =1");
	$row = dbNumRows($sql);
	if($row>0){
		list($delay, $start, $end, $content, $width, $height ) = dbFetchArray($sql);
		$popup_content ="<div class='row' style='widht:$width; height:$height;'><div class='col-lg-12'>$content</div></div>";
		include "../library/popup.php";
		$today = date('Y-m-d H:i:s');
		if($start<$today &&$end<$today){  
			if(!isset($_COOKIE['pop_up'])){
				setcookie("pop_up", $pop_on, time()+$delay);
				echo" <script> $(document).ready(function(e) {  $('#modal_popup').modal('show'); }); </script>";
			}
		}
	}
		
?>
<div class="container">
<!-- page place holder -->
<div class="row">
	
    <div class="col-xs-6 col-xs-offset-6">
       <ul class="nav navbar-nav navbar-right">
     		<li><a href='index.php?content=order' style='color:black; text-align:center; background-color:transparent; padding-top:10px; padding-bottom:10px;'><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />New Order</a></li>
       </ul>
    </div>
</div>
<hr style="border-color:#CCC; margin-top: 0px; margin-bottom:5px;" />
<!-- End page place holder -->

	<div class="row" >
        <h4 class="section-title style2 text-center"><span>NEW ARRIVALS</span></h4>
        <?php newProduct(getConfig("NEW_PRODUCT_QTY")); ?>
    </div>
</div>
