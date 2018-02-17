<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo base_url(); ?>shop/assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo base_url(); ?>shop/assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo base_url(); ?>shop/assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">
    <link rel="shortcut icon" href="<?php echo base_url(); ?>shop/assets/ico/favicon.png">
    <title><?php if( isset( $title )){ echo $title; }else{ echo 'Welcome'; } ?></title>
    <!-- Bootstrap core CSS -->
     <link rel="stylesheet" href="<?php echo base_url(); ?>library/css/jquery-ui-1.10.4.custom.min.css" />
     <script src="<?php echo base_url(); ?>library/js/jquery.min.js"></script>
     <script src="<?php echo base_url(); ?>library/js/handlebars-v3.js"></script> 
  	<script src="<?php echo base_url(); ?>library/js/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="<?php echo base_url(); ?>library/js/sweet-alert.js"></script>
   <script src="<?php echo ROOT_PATH; ?>library/js/jquery.cookie.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>library/css/sweet-alert.css">
    <link href="<?php echo base_url(); ?>shop/assets/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo base_url(); ?>shop/assets/css/style.css" rel="stylesheet">
    <style>
	.ui-autocomplete { 	height: 400px; overflow-y: scroll; overflow-x: hidden; }
	</style>
    <meta property="og:url"	content="<?php echo current_url(); ?>" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?php echo $title; ?>" />
	<meta property="og:description" content="" />
	<meta property="og:image" content="<?php echo base_url(); ?>img/company/logo.png" />


    <!-- Just for debugging purposes. -->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <!-- include pace script for automatic web page progress bar  -->


    <script>
        paceOptions = {
            elements: true
        };
    </script>

    <script src="<?php echo base_url(); ?>shop/assets/js/pace.min.js"></script>
</head>

<body onLoad="checkError()">
<!-- Load Facebook SDK for JavaScript -->
<div id="fb-root"></div>
	<script>
	(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
    </script>
<?php if(!is_int($this->id_customer)){  $this->load->view("include/login"); } ?>
<input type="hidden" name="id_customer" id="id_customer" value="<?php echo $this->id_customer; ?>" />
<input type="hidden" name="id_cart" id="id_cart" value="<?php echo $this->id_cart; ?>" />




<!-- Fixed navbar start -->
<div class="navbar navbar-tshop navbar-fixed-top megamenu" role="navigation">

<?php $this->load->view("include/navbar_top.php"); ?>
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only"> Toggle navigation </span> 
                <span class="icon-bar"> </span> 
                <span class="icon-bar"> </span> 
                <span class="icon-bar"> </span>
            </button>
            <a href="<?php echo base_url(); ?>shop/main/cart/<?php echo $this->id_cart; ?>"> 
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-cart">
            	<i class="fa fa-shopping-basket fa-lg colorWhite"> </i> 
            <?php if( $this->cart_qty > 0 ) : ?>
            	<span id="cartMobileLabel" class="label labelRounded label-danger" style="position: relative; margin-left:-10px; top:-10px;"><?php echo number_format($this->cart_qty); ?> </span>
            <?php else : ?>
            	<span id="cartMobileLabel" class="label labelRounded label-danger" style="position: relative; margin-left:-10px; top:-10px; visibility:hidden;"><?php echo number_format($this->cart_qty); ?> </span>
            <?php endif; ?>
            </button>
            </a>
            <a class="navbar-brand " href="<?php echo $this->home; ?>"> <img src="<?php echo base_url(); ?>shop/images/logo.png" alt="TSHOP"> </a>

            <!-- this part for mobile -->
            <div class="search-box pull-right hidden-lg hidden-md hidden-sm">
                <div class="input-group">
                    <button class="btn btn-nobg getFullSearch" type="button"><i class="fa fa-search"> </i></button>
                </div>
                <!-- /input-group -->

            </div>
        </div>

       <?php //$this->load->view("include/mini_cart_mobile.php"); ?>

        <div class="navbar-collapse collapse">
            <?php $this->load->view("include/menu.php"); ?>

          <?php $this->load->view("include/mini_cart.php"); ?>
        </div>
        <!--/.nav-collapse -->

    </div>
    <!--/.container -->

    <div class="search-full text-right"><a class="pull-right search-close"> <i class=" fa fa-times-circle"> </i> </a>

        <div class="searchInputBox pull-right">
            <input type="search" data-searchurl="search?=" name="q" placeholder="start typing and hit enter to search"
                   class="search-input">
            <button class="btn-nobg search-btn" type="submit"><i class="fa fa-search"> </i></button>
        </div>
    </div>
    <!--/.search-full-->

</div>
<!-- /.Fixed navbar  -->
<!-- <div class="container main-container headerOffset"> -->
<div class="gap"></div>
<div class="gap hide visible-xs"></div>
