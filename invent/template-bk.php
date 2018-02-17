<?php

if (!defined('WEB_ROOT')) {
	exit;
}
$self = WEB_ROOT . 'index.php';

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
    
  	<script src="<?php  echo WEB_ROOT;?>library/js/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="<?php echo WEB_ROOT; ?>library/js/bootstrap.min.js"></script>
    <script src="<?php echo WEB_ROOT; ?>library/js/iCheck/icheck.js"></script>
     
    
    
    <!-- SB Admin CSS - Include with every page -->
    <link href="<?php echo WEB_ROOT; ?>library/css/sb-admin.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/template.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/js/iCheck/skins/all.css?v=1.0.2" rel="stylesheet">
   

</head>

<body style='padding-top:0px;'>
    <div id="wrapper">
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
            <ul class="nav navbar-nav">
            <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-folder-open"></span>&nbsp; สินค้า</a>
              <ul class="dropdown-menu">
                <li><a href="index.php?content=product"><span class="glyphicon glyphicon-tasks"></span>&nbsp;เพิ่ม/แก้ไข สินค้า</a></li>
                <li><a href="index.php?content=category"><span class="glyphicon glyphicon-bookmark"></span>&nbsp;เพิ่ม/แก้ไข หมวดหมู่</a></li>
                <li><a href="index.php?content=color"><span class="glyphicon glyphicon-tint"></span>&nbsp;เพิ่ม/แก้ไข สี</a></li>
                <li><a href="index.php?content=size"><span class="glyphicon glyphicon-tag"></span>&nbsp;เพิ่ม/แก้ไข ไซด์</a></li>
                <li><a href="index.php?content=attribute"><span class="glyphicon glyphicon-leaf"></span>&nbsp;เพิ่ม/แก้ไข คุณลักษณะ</a></li>
              </ul>
            </li>
            <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-home"></span>&nbsp;คลังสินค้า</a>
              <ul class="dropdown-menu">
                <li><a href="index.php?content=product_in"><span class="glyphicon glyphicon-import"></span>&nbsp;รับสินค้าเข้า</a></li>
                <li><a href="index.php?content=order_return"><span class="glyphicon glyphicon-import"></span>&nbsp;รับคืนสินค้า</a></li>
                 <li class="divider"></li>
                <li><a href="index.php?content=requisition"><span class="glyphicon glyphicon-export"></span>&nbsp;เบิกสินค้า</a></li>
                <li><a href="index.php?content=lend"><span class="glyphicon glyphicon-export"></span>&nbsp;ยืมสินค้า</a></li>
                <li class="divider"></li>
                <li><a href="index.php?content=tranfer"><span class="glyphicon glyphicon-export"></span>&nbsp;โอนคลัง</a></li>
                <li class="divider"></li>
                <li><a href="index.php?content=ProductMove"><span class="glyphicon glyphicon-transfer"></span>&nbsp;ย้ายพื้นที่จัดเก็บ</a></li>
                <li><a href="index.php?content=ProductCheck"><span class="glyphicon glyphicon-check"></span>&nbsp;ตรวจนับสินค้า</a></li>
                <li><a href="index.php?content=ProductAdjust"><span class="glyphicon glyphicon-tasks"></span>&nbsp;ปรับปรุงยอด</a></li>
                <li><a href="index.php?content=drop_zero"><span class="glyphicon glyphicon-tasks"></span>&nbsp;เคลียร์ยอดสต็อกที่เป็นศูนย์</a></li>
                <li class="divider"></li>
                 <li><a href="index.php?content=zone"><span class="glyphicon glyphicon-map-marker"></span>&nbsp;เพิ่ม/แก้ไข โซนสินค้า</a></li>
                <li><a href="index.php?content=warehouse"><span class="glyphicon glyphicon-home"></span>&nbsp;เพิ่ม/แก้ไข คลังสินค้า</a></li>
              </ul>
             
            </li>
          <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;ออเดอร์</a>
              <ul class="dropdown-menu">
                <li><a href="index.php?content=order"><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;ออเดอร์</a></li>
                 <li><a href="index.php?content=sponsor"><span class="glyphicon glyphicon-export"></span>&nbsp;สปอนเซอร์</a></li>
                <li><a href="index.php?content=consignment"><span class="glyphicon glyphicon-resize-small"></span>&nbsp;ฝากขาย</a></li>
                 <li class="divider"></li>
                <li><a href="index.php?content=prepare"><span class="glyphicon glyphicon-inbox"></span>&nbsp;จัดสินค้า</a></li>
                <li><a href="index.php?content=qc"><span class="glyphicon glyphicon-eye-open"></span>&nbsp;ตรวจสินค้า</a></li>
                <li><a href="index.php?content=bill"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;รายการเปิดบิล</a></li>
                <li><a href="index.php?content=order_closed"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;รายการเปิดบิลแล้ว</a></li>
                 <li class="divider"></li>
                 <li><a href="index.php?content=request"><i class="fa fa-thumb-tack"></i>&nbsp;ร้องขอสินค้า</a></li>
              </ul>
            </li>
             <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span>&nbsp;ลูกค้า</a>
              <ul class="dropdown-menu">
                <li><a href="index.php?content=customer"><span class="glyphicon glyphicon-user"></span>&nbsp;ลูกค้า</a></li>
                <li><a href="index.php?content=address"><span class="glyphicon glyphicon-home"></span>&nbsp;ที่อยู่ลูกค้า</a></li>
                <li><a href="index.php?content=group"><span class="glyphicon glyphicon-filter"></span>&nbsp;กลุ่มลูกค้า</a></li> 
                <li><a href="index.php?content=add_sponsor"><span class="glyphicon glyphicon-gift"></span>&nbsp;สปอนเซอร์</a></li>
              </ul>
            </li>
          
            <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span>&nbsp;กำหนดค่า</a>
              <ul class="dropdown-menu">
              	<li><a href="index.php?content=config&general"><span class="glyphicon glyphicon-cog"></span>&nbsp;ทั่วไป</a></li>
                <li><a href="index.php?content=config&product"><span class="glyphicon glyphicon-tasks"></span>&nbsp;สินค้า</a></li>
                <li><a href="index.php?content=config&document"><span class="glyphicon glyphicon-file"></span>&nbsp;เอกสาร</a></li>
                <li><a href="index.php?content=config&popup"><span class="glyphicon glyphicon-file"></span>&nbsp;แจ้งข่าว</a></li>
                <li><a href="index.php?content=Employee"><span class="glyphicon glyphicon-user"></span>&nbsp;พนักงาน</a></li>
                <li><a href="index.php?content=sale"><span class="glyphicon glyphicon-user"></span>&nbsp;พนักงานขาย</a></li>
                <li><a href="index.php?content=Profile"><span class="glyphicon glyphicon-folder-close"></span>&nbsp;โปรไฟล์</a></li>
                <li><a href="index.php?content=securable"><span class="glyphicon glyphicon-lock"></span>&nbsp;กำหนดสิทธิ์</a></li>
              </ul>
            </li>
             <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-check"></span>&nbsp;นับสินค้า</a>
              <ul class="dropdown-menu">
                <li><a href="index.php?content=checkstock"><span class="glyphicon glyphicon-check"></span>&nbsp;นับสินค้า</a></li>
                <li><a href="index.php?content=OpenCheck"><span class="glyphicon glyphicon-eye-close"></span>&nbsp;เปิด/ปิดการตรวจนับ</a></li>
                 <li><a href="index.php?content=check_stock_moniter"><i class='fa fa-th'></i>&nbsp;ภาพรวมการตรวจนับ</a></li> 
                <li><a href="index.php?content=ProductCount"><span class="glyphicon glyphicon-folder-close"></span>&nbsp;ตรวจสอบยอดสินค้าจากการตรวจนับ</a></li>
              </ul>
            </li>
             <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-usd"></span>&nbsp;บัญชี</a>
              <ul class="dropdown-menu">
                <li><a href="index.php?content=repay"><span class="glyphicon glyphicon-usd"></span>&nbsp;ตัดหนี้</a></li>
                <li><a href="index.php?content=consign"><span class="glyphicon glyphicon-resize-small"></span>&nbsp;ตัดยอดฝากขาย</a></li>
              </ul>
            </li>
             <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-stats"></span>&nbsp;รายงาน</a>
              <ul class="dropdown-menu">
                 <li><a href="index.php?content=current_stock"><i class='fa fa-th'></i>&nbsp;รายงานสินค้าคงเหลือปัจจุบัน</a></li>
                 <li><a href="index.php?content=stock_report"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานสินค้าคงเหลือ</a></li>
                <li><a href="index.php?content=stock_zone_report"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานสินค้าคงเหลือแยกตามโซน</a></li>
                <li><a href="index.php?content=stock_by_warehouse"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานสินค้าคงเหลือเปรียบเทียบคลัง</a></li>
                <li><a href="index.php?content=fifo"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานความเคลื่อนไหวสินค้าแต่ละตัว</a></li>
                <li><a href="index.php?content=total_fifo"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานยอดรวมสินค้าเข้า-ออก</a></li>
                <li><a href="index.php?content=non_move"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานสินค้าไม่เคลื่อนไหว</a></li>
                <li><a href="index.php?content=request_report"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานการร้องขอสินค้า</a></li>
                <li><a href="index.php?content=request_by_customer"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานการร้องขอสินค้า แยกตามลูกค้า</a></li>
                <li><a href="index.php?content=discount_edit"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานการแก้ไขส่วนลด</a></li>
                <li class="divider"></li>
                <li><a href="index.php?content=sale_report_zone"><span class="glyphicon glyphicon-list"></span>&nbsp;สรุปยอดขาย แยกตามพื้นที่การขาย</a></li>
                <li><a href="index.php?content=sale_report_employee"><span class="glyphicon glyphicon-list"></span>&nbsp;สรุปยอดขาย แยกตามพนักงานขาย</a></li>
                <li><a href="index.php?content=sale_report_customer"><span class="glyphicon glyphicon-list"></span>&nbsp;สรุปยอดขาย แยกตามลูกค้า</a></li>
                <li><a href="index.php?content=sale_report_product"><span class="glyphicon glyphicon-list"></span>&nbsp;สรุปยอดขาย แยกตามรุ่นสินค้า</a></li>
                <li><a href="index.php?content=sale_by_document"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานยอดขาย แยกตามเลขที่เอกสาร</a></li>
                <li><a href="index.php?content=sale_amount_detail"><span class="glyphicon glyphicon-list"></span>&nbsp;รายละเอียดการขาย แยกตามพนักงานขาย</a></li>
                <li><a href="index.php?content=sale_amount_document"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานยอดขาย แยกตามพนักงานและเอกสาร</a></li>
                <li><a href="index.php?content=sponser_report"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานยอดสปอนเซอร์</a></li>                 
              </ul>
            </li>
            <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-stats"></span>&nbsp;รายงาน2</a>
              <ul class="dropdown-menu">
                 <li><a href="index.php?content=report_stock_backlogs"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานสินค้า ค้างส่ง</a></li>
                 <li><a href="index.php?content=recieved_report"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานการรับสินค้า</a></li>
                 <li><a href="index.php?content=customer_by_product"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานลูกค้า แยกตามสินค้า</a></li>
                 <li><a href="index.php?content=customer_by_product_attribute"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานลูกค้า แยกตามรายการสินค้า</a></li>
                 <li><a href="index.php?content=product_by_customer"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานสินค้า แยกตามลูกค้า</a></li>
                 <li><a href="index.php?content=product_attribute_by_customer"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานรายการสินค้า แยกตามลูกค้า</a></li>
                 <li><a href="index.php?content=document_by_product_attribute"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานเอกสาร แยกตามรายการสินค้า</a></li>
                 <li><a href="index.php?content=document_by_customer"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานเอกสาร แยกตามลูกค้า</a></li>
                 <li><a href="index.php?content=consignment_by_customer"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานบิลส่งของไปฝากขาย แยกตามลูกค้า เรียงตามเลขที่เอกสาร </a></li>
                 <li><a href="index.php?content=consign_by_customer"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานยอดขายสินค้าฝากขาย แยกตามลูกค้า เรียงตามเลขที่เอกสารตัดยอดฝากขาย </a></li>
                 <li><a href="index.php?content=sale_consign_product_by_customer"><span class="glyphicon glyphicon-list"></span>&nbsp;รายงานยอดขายสินค้าฝากขาย แยกตามลูกค้า แสดงรายการสินค้า </a></li>
              </ul>
            </li>
            <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-stats"></span>&nbsp;รายงานวิเคราะห์</a>
              <ul class="dropdown-menu">
              	<li><a href="index.php?content=sale_amount_report"><i class="fa fa-dashboard"></i>&nbsp;สรุปยอดขาย รวม</a></li>
                <li><a href="index.php?content=sale_leader_board"><i class="fa fa-dashboard"></i>&nbsp;สรุปยอดขาย แยกตามพนักงาน</a></li>
                <li><a href="index.php?content=sale_leader_group"><i class="fa fa-dashboard"></i>&nbsp;สรุปยอดขาย แยกตามพื้นที่</a></li>
                <li><a href="index.php?content=sale_calendar"><i class="fa fa-dashboard"></i>&nbsp;ปฏิทิน ยอดขาย</a></li>
             	<li class="divider"></li>
                <li><a href="index.php?content=chart_movement_report"><i class="fa fa-line-chart"></i>&nbsp;กราฟรายงานภาพรวมการขาย</a></li>
                <li><a href="index.php?content=sale_chart_zone"><i class="fa fa-line-chart"></i>&nbsp;กราฟรายงานยอดขาย เปรียบเทียบพื้นที่การขาย</a></li>
                 <li><a href="index.php?content=chart_move_movement_report"><i class="fa fa-line-chart"></i>&nbsp;กราฟรายงานภาพรวมสินค้าเปรียบเทียบยอด เข้า / ออก</a></li>
                <li><a href="index.php?content=attribute_chart_report"><i class="fa fa-bar-chart"></i>&nbsp;รายงานวิเคราะห์คุณลักษณะสินค้า</a></li>
                 <li><a href="index.php?content=stock_chart_zone_report"><i class="fa fa-bar-chart"></i>&nbsp;กราฟรายงานการเคลื่อนไหวสินค้า แยกตามพื้นที่การขาย</a></li>
              </ul>
            </li>
            <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-database"></i>&nbsp;ฐานข้อมูล</a>
              <ul class="dropdown-menu">
                 <li><a href="index.php?content=product_db"><i class='fa fa-th'></i>&nbsp;รายการสินค้า</a></li>                
              </ul>
            </li>
             <li><a href="../doc/index.php" target="_blank"><i class="fa fa-book"></i>&nbsp; คู่มือการใช้งาน</a></li>
          </ul>
          <?php include "../user_menu.php"; ?>
           </div> 
        </nav>
   </div>
    <!-- /#wrapper -->
    <!--/.nav-collapse -->
    
<div class="starter-template">
  <?php   
			include $content;	 
		?>
</div>
 
    <!-- Core Scripts - Include with every page -->
    

</body>

</html>
