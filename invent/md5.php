<?php 
if(isset($_POST['input'])){
	$text = md5($_POST['input']);
	header("location: input_md5.php?md5=$text");
}
?>