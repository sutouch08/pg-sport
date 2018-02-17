<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Fav and touch icons -->
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="ico/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="assets/ico/favicon.ico">
<title><?php echo $pageTitle ; ?></title>
<!-- Bootstrap core CSS -->
<link href="assets/bootstrap/css/bootstrap.css" rel="stylesheet">
<!-- Custom styles for this template -->
 <link href="assets/css/bootflat.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
<!-- css3 animation effect for this template -->
<link href="assets/css/animate.min.css" rel="stylesheet">
<!-- styles needed by carousel slider -->
<link href="assets/css/owl.carousel.css" rel="stylesheet">
<link href="assets/css/owl.theme.css" rel="stylesheet">
<link href="<?php echo WEB_ROOT; ?>library/css/paginator.css" rel="stylesheet">
<!-- styles needed by checkRadio -->
<link href="assets/css/ion.checkRadio.css" rel="stylesheet">
<link href="assets/css/ion.checkRadio.cloudy.css" rel="stylesheet">
<!-- styles needed by mCustomScrollbar -->
<link href="assets/css/jquery.mCustomScrollbar.css" rel="stylesheet">
<link href='assets/css/jquery.minimalect.min.css' rel='stylesheet'>
<link rel="stylesheet" href="<?php  echo WEB_ROOT;?>library/css/jquery-ui-1.10.4.custom.min.css" />
<!-- Just for debugging purposes. -->
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
<script type="text/javascript" src="assets/js/jquery/1.8.3/jquery.js"></script>     
 <script src="<?php  echo WEB_ROOT;?>library/js/jquery-ui-1.10.4.custom.min.js"></script>
 <script src="<?php echo WEB_ROOT; ?>library/js/sweet-alert.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo WEB_ROOT; ?>library/css/sweet-alert.css">

<!-- include pace script for automatic web page progress bar  -->

<script>
    paceOptions = {
      elements: true
    };
</script>
<script src="assets/js/pace.min.js"></script>
</head>

<body>
<?php 

if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	echo "<input type='hidden' id='error' value='$error_message' />";
}

if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo "<input type='hidden' id='success' value='$message' />";
}
if(isset($_GET['warning'])){
	$message = $_GET['warning'];
	echo "<input type='hidden' id='warning' value='$message' />";
}
?>

<?php  include "top_menu.php"; ?>
<div class='container headerOffset' >
  <?php include $content;	?>
</div>
<div id="loader" style="position:absolute; padding: 15px 25px 15px 25px; background-color:#fff; opacity:0.0; box-shadow: 0px 0px 25px #CCC; top:-20px; display:none;">
        <center><i class="fa fa-spinner fa-5x fa-spin blue"></i></center><center>กำลังทำงาน....</center>
</div> 
    <!-- Core Scripts - Include with every page -->
<script>
function load_in(){
	var x = ($(document).innerWidth()/2)-50;
	$("#loader").css("display","");
	$("#loader").css("left",x);
	$("#loader").animate({opacity:0.8, top:300},300);		
}
function load_out(){
	$("#loader").animate({opacity:0, top:-20},300, function(){ $("#loader").css("display","none");});
}   

function isDate(txtDate){
	  var currVal = txtDate;
	  if(currVal == '')
	    return false;  
	  //Declare Regex 
	  var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
	  var dtArray = currVal.match(rxDatePattern); // is format OK?
	  if (dtArray == null){
		     return false;
	  }
	  //Checks for mm/dd/yyyy format.	  
	  dtDay= dtArray[1];
	  dtMonth = dtArray[3];
	  dtYear = dtArray[5];
	  if (dtMonth < 1 || dtMonth > 12){
	      return false;
	  }else if (dtDay < 1 || dtDay> 31){
	      return false;
	  }else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31){
	      return false;
	  }else if (dtMonth == 2){
	     var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
	     if (dtDay> 29 || (dtDay ==29 && !isleap)){
	          return false;
		 }
	  }
	  return true;
	}
	
	
	function confirm_delete(title, text, url, confirm_text, cancle_text)
{
	var confirm_text = typeof confirm_text !== 'undefined' ? confirm_text : "ใช่";
	var cancle_text = typeof cancle_text !== 'undefined' ? cancle_text : "ไม่ใช่";
	swal({
	  title: title,
	  text: text,
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: confirm_text,
	  cancelButtonText: cancle_text,
	  closeOnConfirm: false},
	  function(isConfirm){
	  if (isConfirm) {
		window.location.href = url;
	  } 
	});
}
checkerror();
function checkerror(){
	
    if($("#error").length){
		var mess = $("#error").val();
		swal({ title: "เกิดข้อผิดพลาด!", text: mess, timer: 3000, type: "error"});
	}else if($("#success").length){
		var mess = $("#success").val();
		swal({ title: "สำเร็จ", text: mess, timer: 1000, type: "success"});
	}else if($("#warning").length){
		var mess = $("#warning").val();
		swal({ title: "คำเตือน", text: mess, timer: 2000, type: "warning"});
	}
}
//**************  Handlebars.js  **********************//
function render(source, data, output){
	var template = Handlebars.compile(source);
	var html = template(data);
	output.html(html);
}
</script>
<!-- Le javascript
================================================== --> 

<!-- Placed at the end of the document so the pages load faster --> 
<script src="assets/bootstrap/js/bootstrap.min.js"></script> 
<!-- include jqueryCycle plugin --> 
<script src="assets/js/jquery.cycle2.min.js"></script> 
<!-- include easing plugin --> 
<script src="assets/js/jquery.easing.1.3.js"></script> 
<!-- include  parallax plugin --> 
<script type="text/javascript"  src="assets/js/jquery.parallax-1.1.js"></script> 
<!-- optionally include helper plugins --> 
<script type="text/javascript"  src="assets/js/helper-plugins/jquery.mousewheel.min.js"></script> 
<!-- include mCustomScrollbar plugin //Custom Scrollbar  --> 
<script type="text/javascript" src="assets/js/jquery.mCustomScrollbar.js"></script> 
<!-- include checkRadio plugin //Custom check & Radio  --> 

<!-- include grid.js // for equal Div height  --> 
<script src="assets/js/grids.js"></script> 
<!-- include carousel slider plugin  --> 
<script src="assets/js/owl.carousel.min.js"></script> 
<!-- jQuery minimalect // custom select   --> 
<script src="assets/js/jquery.minimalect.min.js"></script> 
<!-- include touchspin.js // touch friendly input spinner component   --> 
<script src="assets/js/bootstrap.touchspin.js"></script> 
<!-- include custom script for site  --> 
<script src="assets/js/script.js"></script>

</body>
</html>


