<?php
require '../library/config.php';
require '../library/functions.php';
require "function/tools.php";
require "function/sponsor_helper.php";
require "function/support_helper.php";
?>

<!DOCTYPE HTML>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="../favicon.ico" />
    <title>ทดสอบระบบ</title>

    <!-- Core CSS - Include with every page -->
    <link href="<?php echo WEB_ROOT; ?>library/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/paginator.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/bootflat.min.css" rel="stylesheet">
     <link rel="stylesheet" href="<?php  echo WEB_ROOT;?>library/css/jquery-ui-1.10.4.custom.min.css" />
     <script src="<?php echo WEB_ROOT; ?>library/js/jquery.min.js"></script>
    
  	<script src="<?php  echo WEB_ROOT;?>library/js/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="<?php echo WEB_ROOT; ?>library/js/bootstrap.min.js"></script>
     
    
    
    <!-- SB Admin CSS - Include with every page 
    <link href="<?php echo WEB_ROOT; ?>library/css/sb-admin.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/template.css" rel="stylesheet">-->
</head>

<body>
<div class="container">
<div class="row">
    <div class="col-sm-3">
        <label>บาร์โค้ด</label>
        <input type="text" class="form-control" id="input-barcode" placeholder="ใส่รหัสบาร์โค้ดที่ต้องการพิมพ์"/>
    </div>
    <div class="col-sm-3">
        <label>Text</label>
        <input type="text" class="form-control" id="input-name" placeholder="ใส่ตัวอักษรที่ต้องการพิมพ์"/>
    </div>
    <div class="col-sm-2">
        <label style="display:block; visibility: hidden;">button</label>
        <button type="button" class="btn btn-primary" onclick="addToList()">เพิ่มในรายการ</button>
    </div>
</div>
<hr/>
<div class="row">
<div class="col-sm-12">
<table class="table">
    <tbody id="rs">
        <tr>
            <td id="field-1" style="width:33%; text-align:center;"></td>
            <td id="field-2" style="width:33%; text-align:center;"></td>
            <td id="field-3" style="width:33%; text-align:center;"></td>
            
        </tr>

    </tbody>
</table>

</div>
</div>


</div>
   
        
    <script>
        var td = 1;
        function addToList(){
            var barcode = $("#input-barcode").val();
            var name  = $("#input-name").val();
            if( barcode != '' && name != ''){
                if(td >3){
                    td = 1
                }
                
                var temp = '<img src="../library/class/barcode/barcode.php?text='+barcode+'" style="height:25mm;"><center>'+name+'</center>';
                $("#field-"+td).append(temp);
                td++;
            }
        }
    </script>

</body>

</html>
