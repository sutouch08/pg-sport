<?php
if (!defined('WEB_ROOT')) {
	exit;
}

$self = WEB_ROOT . 'index.php';
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../docs-assets/ico/favicon.png">

    <title><?php echo $pageTitle ?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo WEB_ROOT;?>library/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT;?>library/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo WEB_ROOT;?>library/css/navbar-static-top.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT;?>library/css/template.css" rel="stylesheet">
 
    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../docs-assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

     <!-- Static navbar -->
    <div class="navbar navbar-default navbar-static-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        <a class="navbar-brand" href="<?php echo WEB_ROOT.'index.php';?>">HOME</a>
        </div>
        <div class="navbar-collapse collapse">
        <ul class="nav  navbar-nav">
                   <li><a href="#">menu 1</a> </li>
                   <li><a href="#">menu 2</a> </li>
                </ul>
                 <p class="navbar-text navbar-right"><a href="<?php echo $self; ?>?logout" class="navbar-link">Sign Out</a></p>
         		 <p class="navbar-text navbar-right">Sign in as <?php echo $_COOKIE['UserName']; ?></p>          
         
        </div><!--/.nav-collapse -->
      </div>
    </div>
    
<div class="starter-template">
  <?php
			include $content;	 
		?>
        </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

    <script src="../dist/js/bootstrap.min.js"></script>
    <script src="../dist/js/bootstrap.js"></script>
  </body>
</html>
