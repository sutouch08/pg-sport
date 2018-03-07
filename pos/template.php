<?php

if (!defined('WEB_ROOT')) {
	exit;
}



$self = WEB_ROOT . 'index.php';


if( isset( $_POST['get_rows'] ) )
{
	createCookie('get_rows', $_POST['get_rows'], 3600*24*60);
}



function get_rows()
{
	$get_rows 	= isset( $_POST['get_rows'] ) ? $_POST['get_rows'] : ( getCookie('get_rows') ? getCookie('get_rows') : 50);
	return $get_rows;
}



function get_page()
{
	$page	= isset( $_GET['Page'] ) ? $_GET['Page'] : 1;
	return $page;
}



function row_no()
{
	$no	= (get_rows() * (get_page() -1)) + 1 ;
	return $no;
}

?>
<!DOCTYPE HTML>
<html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="../favicon.ico" />
    <title><?php echo $pageTitle ?></title>

    <!-- Core CSS - Include with every page -->
    <link href="<?php echo WEB_ROOT; ?>library/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/paginator.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/bootflat.css" rel="stylesheet">
     <link rel="stylesheet" href="<?php  echo WEB_ROOT;?>library/css/jquery-ui-1.10.4.custom.min.css" />
     <script src="<?php echo WEB_ROOT; ?>library/js/jquery.min.js"></script>
     <script src="<?php echo WEB_ROOT; ?>library/js/handlebars-v3.js"></script>
  	<script src="<?php  echo WEB_ROOT;?>library/js/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="<?php echo WEB_ROOT; ?>library/js/bootstrap.min.js"></script>



    <!-- SB Admin CSS - Include with every page -->
    <link href="<?php echo WEB_ROOT; ?>library/css/sb-admin.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/color.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/template.css" rel="stylesheet">
		<link href="<?php echo WEB_ROOT; ?>library/css/pos.css" rel="stylesheet">
   <script src="<?php echo WEB_ROOT; ?>library/js/sweet-alert.js"></script>
   <script src="<?php echo WEB_ROOT; ?>library/js/jquery.cookie.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo WEB_ROOT; ?>library/css/sweet-alert.css">
    <style>
	.ui-autocomplete { 	height: 400px; overflow-y: scroll; overflow-x: hidden; }
	</style>

</head>

<body style='padding-top:0px;' onLoad="checkerror();">
<input type="hidden" name="id_user" id="id_user" value="<?php echo $_COOKIE['user_id']; ?>" />

    <div id="wrapper">
    <?php if(!isset($_GET['nomenu'])) : ?>
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style='position:relative;'>
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php"><?php echo COMPANY; ?></a>
            </div>
            <!-- /.navbar-header -->
            <div class="navbar-collapse collapse">

            <?php include "../pos_menu.php"; ?>
          	<?php include "../user_menu.php"; ?>
           </div>
        </nav>
        <?php else : ?>
        <div style="width:100%; height:10px;">&nbsp;</div>
        <?php endif; ?>
   </div>
    <!-- /#wrapper -->
    <!--/.nav-collapse -->


<div class="starter-template">
  <?php
	if($id_tab != 0)
	{
		$pm = checkAccess($id_profile, $id_tab);
		$view = $pm['view'];
		$add = $pm['add'];
		$edit = $pm['edit'];
		$delete = $pm['delete'];
		accessDeny($view);
	}
			
		include $content;
		?>
</div>
<div class='modal fade' id='xloader' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop="static">
    <div class='modal-dialog' style='width:150px; background-color:transparent;' >
        <div class='modal-content'>
            <div class='modal-body'>
            	<div style="width:100%; height:150px; padding-top:25px;">
                	<div style="width:100%;  text-align:center; margin-bottom:10px;">
            			<i class="fa fa-spinner fa-4x fa-pulse" style="color:#069; display:block;"></i>
                    </div>
                    <div style="width:100%; height:10px; background-color:#CCC;"></div>
                    <div id="preloader" style="margin-top:-10px; height:10px; width:1%; background-color:#09F;"></div>


                    <div style="width:100%;  text-align:center; margin-top:15px; font-size:12px;">
                		<span><strong>Loading....</strong></span>
 					</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="loader" style="position:absolute; padding: 15px 25px 15px 25px; background-color:#fff; opacity:0.0; box-shadow: 0px 0px 25px #CCC; top:-20px; display:none;">
        <center><i class="fa fa-spinner fa-5x fa-spin blue"></i></center>
        <center>กำลังโหลด....</center>
</div>

<script src="../library/js/template.js"></script>

</body>

</html>
