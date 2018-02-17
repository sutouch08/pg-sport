<?php
require_once '../library/config.php';
require_once '../library/functions.php';
if (isset($_POST['txtUserName'])) {
	$result = saleLogin();
}
if(isset($_GET['error'])){
	$error_message = $_GET['error'];
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="favicon.png">

    <title>Sign in</title>

    <!-- core CSS -->
  <link href="../library/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../library/css/signin.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../docs-assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">
      <form method="post" class="form-signin" role="form" >
        <h2 class="form-signin-heading" align="center"><?php // echo COMPANY ; ?></h2>
        <input type="text" class="form-control" style='margin-bottom:10px;' name="txtUserName" placeholder="Email OR User Name" required autofocus>
        <input type="password" class="form-control" name="txtPassword" placeholder="Password" required>
        <input type='checkbox' name='remember' id='remember' /><label for="remember" style='margin-left:10px;'>Remember me</label>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button><br/>
        <p class="pull-right"><a href="#">Forgot Password? </a></p>
      </form>
    <?php 	if(isset($error_message)){ echo"<div class='row'><div class='col-lg-6 col-lg-offset-3'><div class='alert alert-danger'>$error_message</div></div></div>"; } ?>
    </div> 
  </body>
</html>
