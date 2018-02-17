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

?>

<div class='row'>
	<div class='col-lg-4 col-md-4 col-sm-6 col-xs-12 col-lg-offset-4 col-md-offset-4 col-sm-offset-3' style='min-height:80vmax;'>
    	<h1 >&nbsp;</h1>
   		 <h1 style='text-align:center'><a href="index.php?content=order&new_request"><i class="fa fa-plus-circle"></i><br/>Make Request </a></h1> 
    </div>
</div>
<!-- <hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' /> -->
