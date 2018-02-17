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
   <script src="<?php echo WEB_ROOT; ?>library/js/sweet-alert.js"></script>
   <script src="<?php echo WEB_ROOT; ?>library/js/jquery.cookie.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo WEB_ROOT; ?>library/css/sweet-alert.css">
    <style>
	.ui-autocomplete { 	height: 400px; overflow-y: scroll; overflow-x: hidden; }
	</style>

</head>

<body style='padding-top:0px;' onLoad="checkerror();">
<input type="hidden" name="id_user" id="id_user" value="<?php echo $_COOKIE['user_id']; ?>" />
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
checkError();

?>

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
            
            <?php include "../menu.php"; ?>
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

<script>
var load_time;
function load_in(){
	$("#xloader").modal("show");
	var time = 0;
	load_time = window.setInterval(function(){
		if(time < 90)
		{
			time++;
		}else{
			time += 0.01;
		}
		$("#preloader").css("width", time+"%");
	}, 1000);		
}
function load_out(){
	$("#xloader").modal("hide");
	window.clearInterval(load_time);
	$("#preloader").css("width", "0%");
}  
 
function removeCommas(str) {
    while (str.search(",") >= 0) {
        str = (str + "").replace(',', '');
    }
    return str;
};

function addCommas(number){
	 return (
	 	input.toString()).replace(/^([-+]?)(0?)(\d+)(.?)(\d+)$/g, function(match, sign, zeros, before, decimal, after) { 
	 		var reverseString = function(string) { return string.split('').reverse().join(''); };
	 		var insertCommas  = function(string) { 
					var reversed   = reverseString(string);
					var reversedWithCommas = reversed.match(/.{1,3}/g).join(',');
					return reverseString(reversedWithCommas);
					};
				return sign + (decimal ? insertCommas(before) + decimal + after : insertCommas(before + after));
				});
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

function validEmail(v) {
    var r = new RegExp("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?");
    return (v.match(r) == null) ? false : true;
}	

jQuery.fn.numberOnly = function()
						{
							return this.each(function()
							{
								$(this).keydown(function(e)
								{
									var key = e.charCode || e.keyCode || 0;
									// allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
									// home, end, period, and numpad decimal
									return (
										key == 8 || 
										key == 9 ||
										key == 13 ||
										key == 46 ||
										key == 110 ||
										key == 190 ||
										(key >= 35 && key <= 40) ||
										(key >= 48 && key <= 57) ||
										(key >= 96 && key <= 105));
								});
							});
						};
						
						addCommas = function(input){
						  // If the regex doesn't match, `replace` returns the string unmodified
						  return (input.toString()).replace(
							// Each parentheses group (or 'capture') in this regex becomes an argument 
							// to the function; in this case, every argument after 'match'
							/^([-+]?)(0?)(\d+)(.?)(\d+)$/g, function(match, sign, zeros, before, decimal, after) {
						
							  // Less obtrusive than adding 'reverse' method on all strings
							  var reverseString = function(string) { return string.split('').reverse().join(''); };
						
							  // Insert commas every three characters from the right
							  var insertCommas  = function(string) { 
						
								// Reverse, because it's easier to do things from the left
								var reversed           = reverseString(string);
						
								// Add commas every three characters
								var reversedWithCommas = reversed.match(/.{1,3}/g).join(',');
						
								// Reverse again (back to normal)
								return reverseString(reversedWithCommas);
							  };
						
							  // If there was no decimal, the last capture grabs the final digit, so
							  // we have to put it back together with the 'before' substring
							  return sign + (decimal ? insertCommas(before) + decimal + after : insertCommas(before + after));
							}
						  );
						};
						
						$.fn.addCommas = function() {
							  $(this).each(function(){
								$(this).val(addCommas($(this).val()));
							  });
							};	
	
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

function checkerror(){
    if($("#error").length){
		var mess = $("#error").val();
		swal({ title: "เกิดข้อผิดพลาด!", text: mess, type: "error"});
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

function render_append(source, data, output)
{
	var template 	= Handlebars.compile(source);
	var html 			= template(data);
	output.append(html);
}

function render_prepend(source, data, output)
{
	var template		= Handlebars.compile(source);
	var html			= template(data);
	output.prepend(html);	
}

var downloadTimer;
function get_download(token)
{
	load_in();
	downloadTimer = window.setInterval(function(){
		var cookie = $.cookie("file_download_token");
		if(cookie == token)
		{
			finished_download();
		}
	}, 1000);
}

function finished_download()
{
	window.clearInterval(downloadTimer);
	$.removeCookie("file_down_load_token");
	load_out();
}

function get_rows(){
	$("#rows").submit();
}

$("#get_rows").change(function(e) {
    get_rows();
});

function isJson(str){
	try{
		JSON.parse(str);	
	}catch(e){
		return false;
	}
	return true;
}

function printOut(url)
{
	var center = ($(document).width() - 800) /2;
	window.open(url, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}	

</script>

</body>

</html>
