<?php if( $viewStockOnly === FALSE ) : ?>
<ul class="nav navbar-nav">
            <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cubes"></i>&nbsp; ระบบคลังสินค้า</a>
              <ul class="dropdown-menu">

                <li class="dropdown-submenu"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-download"></i>&nbsp; รับสินค้าเข้า</a>
                	<ul class="dropdown-menu">
                    	<li><a href="index.php?content=receive_product"><i class="fa fa-download"></i>&nbsp; รับสินค้า จากการซื้อ</a></li>
                        <li><a href="index.php?content=receive_tranform"><i class="fa fa-download"></i>&nbsp; รับสินค้า จากการแปรสภาพ</a></li>
                    </ul>
                </li>
                <li class="dropdown-submenu"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-download"></i>&nbsp; รับคืนสินค้า</a>
                	<ul class="dropdown-menu">
                    	<li><a href="index.php?content=order_return"><i class="fa fa-download"></i>&nbsp; รับคืนสินค้า จากการขาย(ปัจจุบัน)	</a>
                        <li><a href="index.php?content=order_return2"><i class="fa fa-download"></i>&nbsp; รับคืนสินค้า จากการขาย(อดีต)</a>
                        <li><a href="index.php?content=support_return"><i class="fa fa-download"></i>&nbsp; รับคืนสินค้า จากอภินันท์</a>
                        <li><a href="index.php?content=sponsor_return"><i class="fa fa-download"></i>&nbsp; รับคืนสินค้า จากสปอนเซอร์</a>
                    </ul>
                </li>
                 <li class="divider"></li>
                <li><a href="index.php?content=requisition">		<i class="fa fa-upload"></i>&nbsp; เบิกสินค้าเพื่อแปรรูป		</a></li>
                <!--<li><a href="index.php?content=order_transform"><i class="fa fa-upload"></i>&nbsp; เบิกสินค้าเพื่อแปรสภาพ</a></li> -->
                <li><a href="index.php?content=order_support"><i class="fa fa-upload"></i>&nbsp; เบิกอภินันทนาการ</a></li>
                <li><a href="index.php?content=lend"><i class="fa fa-upload"></i>&nbsp; ยืมสินค้า</a></li>
                <li class="divider"></li>
                <li><a href="index.php?content=tranfer"><i class="fa fa-recycle"></i>&nbsp; โอนสินค้าระหว่างคลัง</a></li>
                <li><a href="index.php?content=ProductMove"><i class="fa fa-recycle"></i>&nbsp; ย้ายพื้นที่จัดเก็บ</a></li>
                <li><a href="index.php?content=ProductCheck"><i class="fa fa-check-square-o"></i>&nbsp; ตรวจนับสินค้า</a></li>
                <li><a href="index.php?content=ProductAdjust"><span class="glyphicon glyphicon-tasks"></span>&nbsp; ปรับปรุงยอด</a></li>
                <li><a href="index.php?content=drop_zero"><span class="glyphicon glyphicon-tasks"></span>&nbsp; เคลียร์ยอดสต็อกที่เป็นศูนย์</a></li>
                <li><a href="index.php?content=buffer_zone"><span class="glyphicon glyphicon-tasks"></span>&nbsp; ตรวจสอบ BUFFER ZONE</a></li>
                <li><a href="index.php?content=cancle_zone"><span class="glyphicon glyphicon-tasks"></span>&nbsp; ตรวจสอบ CANCLE ZONE</a></li>
              </ul>

            </li>
          <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-shopping-bag"></i>&nbsp; ระบบขาย</a>
              <ul class="dropdown-menu">
                <li><a href="index.php?content=order"><i class="fa fa-shopping-bag"></i>&nbsp; ออเดอร์</a></li>
                <li><a href="index.php?content=order_sponsor"><i class="fa fa-ticket"></i>&nbsp; สปอนเซอร์ สโมสร</a></li>
                <li><a href="index.php?content=consignment"><i class="fa fa-cloud-upload"></i>&nbsp; ฝากขาย</a></li>
                <li><a href="index.php?content=order_online"><i class="fa fa-desktop"></i>&nbsp; ขายออนไลน์</a></li>
                 <li class="divider"></li>
                <li><a href="index.php?content=prepare"><i class="fa fa-shopping-basket"></i>&nbsp; จัดสินค้า</a></li>
                <li><a href="index.php?content=qc"><i class="fa fa-check-square-o"></i>&nbsp; ตรวจสินค้า</a></li>
                <?php if($fast_qc) : ?>
                 	<li><a href="index.php?content=qc2"><i class="fa fa-check-square-o"></i>&nbsp; ตรวจสินค้า จากข้างนอก</a></li>
                 <?php endif; ?>
                <li><a href="index.php?content=bill"><i class="fa fa-file-text-o"></i>&nbsp; ออเดอร์รอเปิดบิล</a></li>
                <li><a href="index.php?content=order_closed"><i class="fa fa-file-text-o"></i>&nbsp; ออเดอร์เปิดบิลแล้ว</a></li>
                 <li class="divider"></li>
                 <!-- <li><a href="index.php?content=request"><i class="fa fa-thumb-tack"></i>&nbsp; ร้องขอสินค้า</a></li> -->
                 <li><a href="index.php?content=order_monitor"><i class="fa fa-exclamation-triangle"></i>&nbsp; ตรวจสอบออเดอร์</a></li>
              </ul>
            </li>

             <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-credit-card"></i>&nbsp; บัญชี</a>
              <ul class="dropdown-menu">
                <!-- <li><a href="index.php?content=consign&consign_check=y"><i class="fa fa-check-square-o"></i>&nbsp; กระทบยอดสินค้า</a></li> -->
                <li><a href="index.php?content=consign_check"><i class="fa fa-check-square-o"></i>&nbsp; กระทบยอดสินค้าฝากขาย</a></li>
                <li><a href="index.php?content=consign"><i class="fa fa-check-square-o"></i>&nbsp; ตัดยอดฝากขาย</a></li>
                <li class="divider"></li>
                <li><a href="index.php?content=payment_order"><i class="fa fa-check-square-o"></i>&nbsp; ตรวจสอบยอดชำระเงิน</a></li>
                <li class="divider"></li>
                <li class="dropdown-submenu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-archive"></i>&nbsp; ตรวจนับสินค้า (ภายใน)</a>
                	<ul class="dropdown-menu">
                    	<li><a href="index.php?content=checkstock"><i class="fa fa-check-square-o"></i>&nbsp; ตรวจนับสินค้า</a></li>
                        <li><a href="index.php?content=OpenCheck"><i class="fa fa-toggle-on"></i>&nbsp; เปิด/ปิดการตรวจนับ</a></li>
                        <li><a href="index.php?content=check_stock_moniter"><i class='fa fa-desktop'></i>&nbsp; ภาพรวมการตรวจนับ</a></li>
                        <li><a href="index.php?content=ProductCount"><i class="fa fa-exclamation-triangle"></i>&nbsp; ตรวจสอบยอดสินค้าจากการตรวจนับ</a></li>
                    </ul>
                </li>
                <li class="divider"></li>
                <li class="dropdown-submenu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-archive"></i>&nbsp; ตรวจนับสินค้า (ฝากขาย)</a>
                	<ul class="dropdown-menu">
                    	<li><a href="index.php?content=export_consign_stock"><i class="fa fa-file-text-o"></i>&nbsp; ส่งออกยอดตั้งต้น</a></li>
                        <li><a href="#"><i class="fa fa-download"></i>&nbsp; นำเข้ายอดตรวจนับ(ห้ามใช้)</a></li>
                    </ul>
                </li>
              </ul>
            </li>

            <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-archive"></i>&nbsp; ระบบซื้อ</a>
            	<ul class="dropdown-menu">
                	<li><a href="index.php?content=po"><i class="fa fa-archive"></i>&nbsp; สั่งซื้อ(PO)</a></li>
                    <li><a href="index.php?content=po_role"><i class="fa fa-archive"></i>&nbsp; เพิ่ม/แก้ไข ประเภทการสั่งซื้อ</a></li>
                </ul>
            </li>


            <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน</a>
            	<ul class="dropdown-menu">
                	<li class="dropdown-submenu"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน ระบบคลังสินค้า</a>
                    	<ul class="dropdown-menu">
                        	 <li><a href="index.php?content=recieved_report">			<i class="fa fa-bar-chart"></i>&nbsp;รายงาน การรับสินค้าจากการซื้อ</a></li>
                             <li><a href="index.php?content=received_by_document">	<i class="fa fa-bar-chart"></i>&nbsp;รายงาน การรับสินค้าจากการซื้อ แยกตามเลขที่เอกสาร</a></li>
                             <li><a href="index.php?content=received_by_product">	<i class="fa fa-bar-chart"></i>&nbsp;รายงาน การรับสินค้าจากการซื้อ แยกตามรุ่นสินค้า</a></li>
                             <li><a href="index.php?content=recieved_tranform_report"><i class="fa fa-bar-chart"></i>&nbsp;รายงาน การรับสินค้าจากการแปรสภาพ</a></li>
                            <li><a href="index.php?content=transform_by_document">	<i class="fa fa-bar-chart"></i>&nbsp;รายงาน การรับสินค้าจากการแปรสภาพ แยกตามเลขที่เอกสาร</a></li>

                        	<li class="divider"></li>
                        	<li><a href="index.php?content=current_stock"><i class='fa fa-list'></i>&nbsp; รายงานสินค้าคงเหลือปัจจุบัน</a></li>
                            <li><a href="index.php?content=stock_report"><i class="fa fa-bar-chart"></i>&nbsp; รายงานสินค้าคงเหลือ</a></li>
                            <li><a href="index.php?content=stock_zone_report"><i class="fa fa-bar-chart"></i>&nbsp; รายงานสินค้าคงเหลือแยกตามโซน</a></li>
                            <li><a href="index.php?content=stock_by_warehouse"><i class="fa fa-bar-chart"></i>&nbsp; รายงานสินค้าคงเหลือเปรียบเทียบคลัง</a></li>
                            <li><a href="index.php?content=movement_summary">		<i class="fa fa-bar-chart"></i>&nbsp; รายงานสรุป ยอดความเคลื่อนไหวสินค้า เปรียบเทียบ เข้า - ออก</a></li>
                            <li><a href="index.php?content=fifo"><i class="fa fa-bar-chart"></i>&nbsp; รายงานความเคลื่อนไหวสินค้าแต่ละตัว</a></li>
                            <li><a href="index.php?content=total_fifo"><i class="fa fa-bar-chart"></i>&nbsp; รายงานยอดรวมสินค้าเข้า-ออก</a></li>
                            <li><a href="index.php?content=non_move"><i class="fa fa-bar-chart"></i>&nbsp; รายงานสินค้าไม่เคลื่อนไหว</a></li>
                            <li><a href="index.php?content=request_report"><i class="fa fa-bar-chart"></i>&nbsp; รายงานการร้องขอสินค้า</a></li>
                            <li><a href="index.php?content=request_by_customer"><i class="fa fa-bar-chart"></i>&nbsp; รายงานการร้องขอสินค้า แยกตามลูกค้า</a></li>
                            <li class="divider"></li>
                            <li><a href="index.php?content=stock_summary"><i class="fa fa-bar-chart"></i>&nbsp; รายงานสรุปสินค้าคงเหลือ แยกตามรุ่นสินค้า</a></li>
                            <li><a href="index.php?content=stock_summary_by_category"><i class="fa fa-bar-chart"></i>&nbsp; รายงานสรุปสินค้าคงเหลือ แยกตามหมวดหมู่สินค้า</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน ระบบขาย</a>
                    	<ul class="dropdown-menu">
                        	<li><a href="index.php?content=sale_summary"><i class="fa fa-bar-chart"></i>&nbsp; สรุปยอดขาย แยกตามรุ่นสินค้า เปรียบเทียบรายเดือน</a></li>
                            <li><a href="index.php?content=sale_summary_by_category"><i class="fa fa-bar-chart"></i>&nbsp; สรุปยอดขาย แยกตามหมวดหมู่สินค้า เปรียบเทียบรายเดือน</a></li>
                        	<li><a href="index.php?content=sale_report_zone"><i class="fa fa-bar-chart"></i>&nbsp; สรุปยอดขาย แยกตามพื้นที่การขาย</a></li>
                            <li><a href="index.php?content=sale_report_employee"><i class="fa fa-bar-chart"></i>&nbsp; สรุปยอดขาย แยกตามพนักงานขาย</a></li>
                            <li><a href="index.php?content=sale_report_customer"><i class="fa fa-bar-chart"></i>&nbsp; สรุปยอดขาย แยกตามลูกค้า</a></li>
                            <li><a href="index.php?content=sale_report_product"><i class="fa fa-bar-chart"></i>&nbsp; สรุปยอดขาย แยกตามรุ่นสินค้า(ห้ามใช้)</a></li>
                            <li class="divider"></li>
                            <li><a href="index.php?content=sale_detail_by_customer"><i class="fa fa-bar-chart"></i>&nbsp; รายงานสินค้า แยกตามลูกค้า</a></li>
                            <li class="divider"></li>
                            <li><a href="index.php?content=sale_by_document"><i class="fa fa-bar-chart"></i>&nbsp; รายงานยอดขาย แยกตามเลขที่เอกสาร</a></li>
                            <li><a href="index.php?content=sale_amount_detail"><i class="fa fa-bar-chart"></i>&nbsp; รายละเอียดการขาย แยกตามพนักงานขาย</a></li>
                            <li><a href="index.php?content=sale_amount_document"><i class="fa fa-bar-chart"></i>&nbsp; รายงานยอดขาย แยกตามพนักงานและเอกสาร</a></li>
                            <li class="divider"></li>
                            <li><a href="index.php?content=sale_by_attribute"><i class="fa fa-bar-chart"></i>&nbsp;รายงานจำนวนขาย แยกตามคุณลักษณะสินค้า</a></li>
                        </ul>
                    </li>
                     <li class="dropdown-submenu"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน ระบบซื้อ</a>
                    	<ul class="dropdown-menu">
                        	<li><a href="index.php?content=po_backlog"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน ใบสั่งซื้อค้างรับ แยกตามผู้ขาย</a></li>
                            <li><a href="index.php?content=product_backlog_by_supplier"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน สค้างรับแยกตามผู้ขาย แสดงรุ่นสินค้าและเลขที่เอกสาร</a></li>
                            <li><a href="index.php?content=product_backlog"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน สินค้าค้างรับ แบบละเอียด</a></li>
                            <li class="divider"></li>
                        	<li><a href="index.php?content=sale_summary"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน สรุปสินค้าค้างรับ แยกตามเลขที่เอกสาร(ห้ามใช้)</a></li>
                            <li><a href="index.php?content=product_summary_backlog_by_product"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน สรุปสินค้าค้างรับ แยกตามรุ่นสินค้า</a></li>
                            <li><a href="index.php?content=product_summary_backlog_by_item"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน สรุปสินค้าค้างรับ แยกตามรายการสินค้า</a></li>
                            <li><a href="index.php?content=product_summary_backlog_by_supplier"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน สรุปสินค้าค้างรับ แยกตามผู้ขาย</a></li>
                            <li class="divider"></li>
                            <li><a href="index.php?content=sale_summary"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน สรุปยอดการสั่งซื้อ แยกตามเลขที่เอกสาร(ห้ามใช้)</a></li>
                            <li><a href="index.php?content=sale_summary"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน สรุปยอดการสั่งซื้อ แยกตามรุ่นสินค้า(ห้ามใช้)</a></li>
                            <li><a href="index.php?content=po_by_product"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน สรุปยอดการสั่งซื้อ แยกตามรายการสินค้า</a></li>
                            <li><a href="index.php?content=sale_summary"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน สรุปยอดการสั่งซื้อ แยกตามผู้ขาย(ห้ามใช้)</a></li>
                            <li class="divider"></li>
                            <li><a href="index.php?content=sale_summary"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน สรุปยอดการสั่งซื้อ เปรียบเทียบรายเดือน(ห้ามใช้)</a></li>


                        </ul>
                    </li>
                    <li class="dropdown-submenu"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน ติดตาม</a>
                    	<ul class="dropdown-menu">
                        	<li><a href="index.php?content=stock_backlogs"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน สินค้าค้างส่ง</a></li>
                            <li><a href="index.php?content=order_backlogs"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน ออเดอร์ค้างส่ง</a></li>
                            <li class="divider"></li>
                        	<li><a href="index.php?content=sponsor_by_customer">	<i class="fa fa-bar-chart"></i>&nbsp; รายงาน ยอดสปอนเซอร์ (ให้บุคคลภายนอก)</a></li>
                             <li><a href="index.php?content=sponsor_summary">			<i class="fa fa-bar-chart"></i>&nbsp; รายงานสรุป ยอดสปอนเซอร์ (ให้บุคคลภายนอก)</a></li>
                            <li><a href="index.php?content=support_by_employee">		<i class="fa fa-bar-chart"></i>&nbsp; รายงาน ยอดเบิกอภินันทนาการ (สำหรับพนักงาน) </a></li>
                            <li><a href="index.php?content=support_summary">			<i class="fa fa-bar-chart"></i>&nbsp; รายงานสรุป ยอดเบิกอภินันทนาการ (สำหรับพนักงาน) </a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน ตรวจสอบ</a>
                    	<ul class="dropdown-menu">
                        	<li><a href="index.php?content=discount_edit"><i class="fa fa-bar-chart"></i>&nbsp;รายงานการแก้ไขส่วนลด</a></li>
                            <li><a href="index.php?content=sponsor_log"><i class="fa fa-bar-chart"></i>&nbsp;รายงาน การเพิ่ม/ลบ/แก้ไข สปอนเซอร์</a></li>
                 			<li><a href="index.php?content=support_log"><i class="fa fa-bar-chart"></i>&nbsp;รายงาน การเพิ่ม/ลบ/แก้ไข อภินันทนาการ</a></li>
                            <li><a href="index.php?content=delivery_fee"><i class="fa fa-bar-chart"></i>&nbsp;รายงาน ค่าจัดส่งสินค้า (ออนไลน์)</a></li>
                            <li class="divider"></li>
                             <li><a href="index.php?content=pdbcd"><i class="fa fa-bar-chart"></i>&nbsp;รายงานสินค้า แยกตามลูกค้า แสดงเลขที่เอกสาร</a></li>
                            <li class="divider"></li>
                             <li><a href="index.php?content=document_by_product_attribute"><i class="fa fa-bar-chart"></i>&nbsp;รายงานเอกสาร แยกตามรายการสินค้า</a></li>
                             <li><a href="index.php?content=document_by_customer"><i class="fa fa-bar-chart"></i>&nbsp;รายงานเอกสาร แยกตามลูกค้า</a></li>
                             <li><a href="index.php?content=consignment_by_customer"><i class="fa fa-bar-chart"></i>&nbsp;รายงานบิลส่งของไปฝากขาย แยกตามลูกค้า เรียงตามเลขที่เอกสาร </a></li>
                             <li><a href="index.php?content=consign_by_customer"><i class="fa fa-bar-chart"></i>&nbsp;รายงานยอดขายสินค้าฝากขาย แยกตามลูกค้า เรียงตามเลขที่เอกสารตัดยอดฝากขาย </a></li>
                             <li><a href="index.php?content=sale_consign_product_by_customer"><i class="fa fa-bar-chart"></i>&nbsp;รายงานยอดขายสินค้าฝากขาย แยกตามลูกค้า แสดงรายการสินค้า </a></li>
                             <li class="divider"></li>
                             <li><a href="index.php?content=lend_by_doc"><i class="fa fa-bar-chart"></i>&nbsp;รายงานใบยืมสินค้าเรียงตามเลขที่เอกสาร</a></li>
                             <li><a href="index.php?content=lend_not_return"><i class="fa fa-bar-chart"></i>&nbsp;รายงานใบยืมสินค้า ยังไม่คืน แสดงรายการสินค้า /เลขที่เอกสาร /ผู้ยืม</a></li>
                             <li><a href="index.php?content=lend_by_product"><i class="fa fa-bar-chart"></i>&nbsp;รายงานใบยืมสินค้า แยกตามสินค้า</a></li>
                        </ul>
                    </li>

                    <li class="dropdown-submenu"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน วิเคราะห์</a>
                    	<ul class="dropdown-menu">
                        	<li><a href="index.php?content=sale_product_deep_analyz"><i class="fa fa-bar-chart"></i>&nbsp;รายงานวิเคราะห์ขายแบบละเอียด</a></li>
                            <li><a href="index.php?content=sponsor_product_deep_analyz"><i class="fa fa-bar-chart"></i>&nbsp;รายงานวิเคราะห์สปอนเซอร์แบบละเอียด</a></li>
                            <li><a href="index.php?content=support_product_deep_analyz"><i class="fa fa-bar-chart"></i>&nbsp;รายงานวิเคราะห์อภินันท์แบบละเอียด</a></li>
                            <li><a href="index.php?content=stock_product_deep_analyz"><i class="fa fa-bar-chart"></i>&nbsp;รายงานวิเคราะห์สินค้าคงเหลือแบบละเอียด</a></li>
                            <li><a href="index.php?content=received_product_deep_analyz"><i class="fa fa-bar-chart"></i>&nbsp;รายงานวิเคราะห์การรับสินค้าเข้าแบบละเอียด</a></li>
                            <li class="divider"></li>
                            <li><a href="index.php?content=customer_by_product"><i class="fa fa-bar-chart"></i>&nbsp;รายงานลูกค้า แยกตามสินค้า</a></li>
                             <li><a href="index.php?content=customer_by_product_attribute"><i class="fa fa-bar-chart"></i>&nbsp;รายงานลูกค้า แยกตามรายการสินค้า</a></li>
                             <li><a href="index.php?content=product_by_customer"><i class="fa fa-bar-chart"></i>&nbsp;รายงานสินค้า แยกตามลูกค้า</a></li>
                             <li><a href="index.php?content=product_attribute_by_customer"><i class="fa fa-bar-chart"></i>&nbsp;รายงานรายการสินค้า แยกตามลูกค้า</a></li>

                             <li class="divider"></li>
                            <li><a href="index.php?content=chart_movement_report"><i class="fa fa-line-chart"></i>&nbsp;กราฟรายงานภาพรวมการขาย</a></li>
                            <li><a href="index.php?content=sale_chart_zone"><i class="fa fa-line-chart"></i>&nbsp;กราฟรายงานยอดขาย เปรียบเทียบพื้นที่การขาย</a></li>
                             <li><a href="index.php?content=chart_move_movement_report"><i class="fa fa-line-chart"></i>&nbsp;กราฟรายงานภาพรวมสินค้าเปรียบเทียบยอด เข้า / ออก</a></li>
                            <li><a href="index.php?content=attribute_chart_report"><i class="fa fa-bar-chart"></i>&nbsp;รายงานวิเคราะห์คุณลักษณะสินค้า</a></li>
                             <li><a href="index.php?content=stock_chart_zone_report"><i class="fa fa-bar-chart"></i>&nbsp;กราฟรายงานการเคลื่อนไหวสินค้า แยกตามพื้นที่การขาย</a></li>
                             <li><a href="index.php?content=attribute_analyz"><i class="fa fa-bar-chart"></i>&nbsp;รายงานวิเคราะห์จำนวนขาย แยกตามคุณลักษณะสินค้า</a></li>
                        </ul>
                    </li>

                     <li class="dropdown-submenu"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน อื่นๆ</a>
                    	<ul class="dropdown-menu">
                        	<li><a href="index.php?content=delivery_ticket"><i class="fa fa-ticket"></i> พิมพ์ตั๋วจัดส่ง</a></li>
                            <li class="divider"></li>
                            <li><a href="index.php?content=sale_amount_report"><i class="fa fa-dashboard"></i>&nbsp;สรุปยอดขาย รวม</a></li>
                            <li><a href="index.php?content=sale_leader_board"><i class="fa fa-dashboard"></i>&nbsp;สรุปยอดขาย แยกตามพนักงาน</a></li>
                            <li><a href="index.php?content=sale_leader_group"><i class="fa fa-dashboard"></i>&nbsp;สรุปยอดขาย แยกตามพื้นที่</a></li>
                            <li><a href="index.php?content=sale_calendar"><i class="fa fa-dashboard"></i>&nbsp;ปฏิทิน ยอดขาย</a></li>
                            <li class="divider"></li>
                            <li><a href="index.php?content=order_freq"><i class="fa fa-bar-chart"></i>&nbsp; รายงานความถี่ในการสั่งสินค้า แยกตามช่วงเวลา</a></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bar-chart"></i>&nbsp; รายงาน ผู้บริหาร</a>
                    	<ul class="dropdown-menu">
                           <li><a href="index.php?content=sale_profit_by_item"><i class="fa fa-bar-chart"></i>&nbsp; รายงานยอดขาย แยกตามรายการสินค้า แสดงกำไรขั้นต้น</a></li>
                           <li><a href="index.php?content=sale_profit_by_customer"><i class="fa fa-bar-chart"></i>&nbsp; รายงานยอดขาย แยกตามลูกค้า แสดงกำไรขั้นต้น</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

             <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cogs"></i>&nbsp; การตั้งค่า</a>
              <ul class="dropdown-menu">
              	<li><a href="index.php?content=config"><i class="fa fa-cogs"></i>&nbsp; การตั้งค่า</a></li>
                <li class="divider"></li>
                <li><a href="index.php?content=popup"><i class="fa fa-bullhorn"></i>&nbsp; การแจ้งข่าว</a></li>
                <li class="divider"></li>
                <li><a href="index.php?content=Profile"><i class="fa fa-folder"></i>&nbsp; เพิ่ม/แก้ไข โปรไฟล์</a></li>
                <li><a href="index.php?content=securable"><i class="fa fa-unlock-alt"></i>&nbsp; กำหนดสิทธิ์</a></li>
              </ul>
            </li>

            <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-database"></i>&nbsp; ฐานข้อมูล</a>
            	<ul class="dropdown-menu">
            		<li class="dropdown-submenu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-tags"></i>&nbsp; สินค้า</a>
                        <ul class="dropdown-menu">
                        <li><a href="index.php?content=product"><i class="fa fa-tags"></i>&nbsp; เพิ่ม/แก้ไข สินค้า</a></li>
                        <li><a href="index.php?content=category"><i class="fa fa-bookmark"></i>&nbsp; เพิ่ม/แก้ไข หมวดหมู่</a></li>
                        <li><a href="index.php?content=product_group"><i class="fa fa-archive"></i>&nbsp; เพิ่ม/แก้ไข กลุ่มสินค้า</a></li>
                        <li><a href="index.php?content=color"><i class="fa fa-tint"></i>&nbsp; เพิ่ม/แก้ไข สี</a></li>
                        <li><a href="index.php?content=size"><i class="fa fa-tag"></i>&nbsp; เพิ่ม/แก้ไข ไซด์</a></li>
                        <li><a href="index.php?content=attribute"><i class="fa fa-leaf"></i>&nbsp; เพิ่ม/แก้ไข คุณลักษณะ</a></li>
                      </ul>
            		</li>

                    <li class="dropdown-submenu"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-home"></i>&nbsp; คลังสินค้า</a>
                    	<ul class="dropdown-menu">
                        	<li><a href="index.php?content=zone"><i class="fa fa-map-marker"></i>&nbsp; เพิ่ม/แก้ไข โซนสินค้า</a></li>
                			<li><a href="index.php?content=warehouse"><i class="fa fa-home"></i>&nbsp; เพิ่ม/แก้ไข คลังสินค้า</a></li>
                        </ul>
                    </li>

                    <li class="dropdown-submenu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-users"></i>&nbsp; ลูกค้า</a>
                      <ul class="dropdown-menu">
                        <li><a href="index.php?content=customer"><i class="fa fa-user"></i>&nbsp; เพิ่ม/แก้ไข ลูกค้า</a></li>
                        <li><a href="index.php?content=address"><i class="fa fa-envelope"></i>&nbsp; เพิ่ม/แก้ไข ที่อยู่ลูกค้า</a></li>
                        <li><a href="index.php?content=group"><i class="fa fa-users"></i>&nbsp; เพิ่ม/แก้ไข กลุ่มลูกค้า</a></li>
                         <li><a href="index.php?content=customer_transfer"><i class="fa fa-retweet"></i>&nbsp; โอน/ย้าย ลูกค้า</a></li>
                         <li class="divider"></li>
                        <li><a href="index.php?content=add_sponsor"><i class="fa fa-user"></i>&nbsp; เพิ่ม/แก้ไข รายชื่อสปอนเซอร์</a></li>
                      </ul>
                    </li>

                    <li class="dropdown-submenu"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-users"></i>&nbsp; พนักงาน</a>
                      <ul class="dropdown-menu">
                        <li><a href="index.php?content=Employee"><i class="fa fa-user"></i>&nbsp; เพิ่ม/แก้ไข พนักงาน</a></li>
                        <li><a href="index.php?content=sale"><i class="fa fa-user"></i>&nbsp; เพิ่ม/แก้ไข พนักงานขาย</a></li>
                        <li><a href="index.php?content=support"><i class="fa fa-user"></i>&nbsp; เพิ่ม/แก้ไข  รายชื่ออภินันทนาการ</a></li>
                      </ul>
                    </li>
                    <li class="divider"></li>
                    <li><a href="index.php?content=supplier"><i class='fa fa-users'></i>&nbsp; เพิ่ม/แก้ไข รายชื่อผู้ขาย</a></li>
                    <li class="divider"></li>
                    <li><a href="index.php?content=sender"><i class="fa fa-truck"></i>&nbsp; เพิ่ม/แก้ไข รายชื่อผู้จัดส่ง</a></li>
                    <li><a href="index.php?content=transport"><i class="fa fa-truck"></i>&nbsp; เชื่อมโยงการจัดส่งของลูกค้า</a></li>
                    <li class="divider"></li>
                    <li><a href="index.php?content=bank_account"><i class="fa fa-bank"></i>&nbsp; เพิ่ม/แก้ไข บัญชีธนาคาร</a></li>
                    <li class="divider"></li>
                    <li><a href="index.php?content=product_db"><i class='fa fa-database'></i>&nbsp; รายงาน รายการสินค้า</a></li>
                    <li><a href="index.php?content=export_product_db"><i class='fa fa-database'></i>&nbsp; ส่งออกรายการสินค้า นำเข้า POS</a></li>
                    <li><a href="index.php?content=export_stock_zone"><i class='fa fa-database'></i>&nbsp; ส่งออกยอดคงเหลือยกไปปลายงวด</a></li>
                    <li><a href="index.php?content=import_stock_zone"><i class='fa fa-database'></i>&nbsp; นำเข้ายอดคงเหลือยกมาต้นงวด</a></li>
            	</ul>
            </li>

<!--            <li><a href="../doc/index.php" target="_blank"><i class="fa fa-book"></i>&nbsp; คู่มือการใช้งาน</a></li> -->
          </ul>
<?php endif; ?>
