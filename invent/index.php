<?php
require_once '../library/config.php';
require_once '../library/functions.php';
require_once "function/tools.php";
if( !getConfig("CLOSED") )
{
checkUser();
$user_id = $_COOKIE['user_id'];
$id_profile = getProfile($user_id);
$viewStockOnly = isViewStockOnly($id_profile);
$id_tab = "";
$fast_qc = getConfig("FAST_QC");
$content = 'main.php';
$page = (isset($_GET['content'])&& $_GET['content'] !='')?$_GET['content']:'';
switch($page){
		case "orderToComplete" :
			$content = "orderToComplete.php";
			$pageTitle = "จัดออเดอร์ย้อนหลัง";
			break;
//**********  ระบบคลังสินค้า  **********//
		case "receive_product":
			$content = "receive_product.php";
			$pageTitle = "รับสินค้าเข้า";
			break;
		case "receive_tranform" :
			$content 		= "receive_tranform.php";
			$pageTitle	= "รับเข้าจากการแปรสภาพ";
			break;
		case "order_return":
			$content = "order_return.php";
			$pageTitle = "รับคืนสินค้า(ปัจจุบัน)";
			break;
		case "order_return2":
			$content = "order_return2.php";
			$pageTitle = "รับคืนสินค้า(อดีต)";
			break;
		case "sponsor_return":
			$content = "sponsor_return.php";
			$pageTitle = "รับคืนสินค้าสปอนเซอร์";
			break;
		case "support_return":
			$content = "support_return.php";
			$pageTitle = "รับคืนสินค้าอภินันท์";
			break;
		case 'order_transform' :
			$content	= 'order_transform.php';
			$pageTitle	= 'เบิกแปรสภาพ';
			break;

		//-- ปิดการใช้งานไว้ก่อน ใช้ order_transform แทน
		case "requisition";
			$content = "requisition.php";
			$pageTitle = "เบิกสินค้า";
			break;

		case "lend";
			$content = "lend.php";
			$pageTitle = "ยืมสินค้า";
			break;
		case "ProductMove":
			$content = "product_move.php";
			$pageTitle = "ย้ายพื้นที่จัดเก็บ";
			break;
		case "ProductCheck":
			$content = "product_check.php";
			$pageTitle = "ตรวจนับสินค้า";
			break;
		case "ProductAdjust":
			$content = "product_adjust.php";
			$pageTitle = "ปรับยอดสินค้า";
			break;
		case 'diff' :
			$content = "diff.php";
			$pageTitle = "โหลดยอดต่าง";
			break;
		case 'tranfer':
			$content = 'tranfer.php';
			$pageTitle = "โอนคลัง";
			break;
		case "drop_zero" :
			$content = "drop_zero.php";
			$pageTitle = "เคลียยอดสต็อกที่เป็นศูนย์";
			break;
		case "buffer_zone" :
			$content = "buffer_zone.php";
			$pageTitle = "ตรวจสอบ BUFFER ZONE";
			break;
		case "cancle_zone" :
			$content = "cancle_zone.php";
			$pageTitle = "ตรวจสอบ CANCLE ZONE";
			break;

//**********  ระบบขาย  **********//
		case "order":
			$content = "order.php";
			$pageTitle= "ออเดอร์";
			break;
		case "order_online" :
			$content = "order_online.php";
			$pageTitle = "online Sale";
			break;
		case "sponsor";
			$content = "sponsor.php";
			$pageTitle = "สปอนเซอร์สโมสร";
			break;
		case "order_sponsor";
			$content = "order_sponsor.php";
			$pageTitle = "สปอนเซอร์สโมสร";
			break;
		case "order_support" :
			$content = "order_support.php";
			$pageTitle = "เบิกอภินันทนาการ";
			break;
		case "consignment";
			$content = "consignment.php";
			$pageTitle = "ฝากขาย";
			break;
		case "prepare":
			$content = "product_prepare.php";
			$pageTitle = "จัดสินค้า";
			break;
		case "qc":
			$content = "product_qc.php";
			$pageTitle = "ตรวจสินค้า";
			break;
		case "qc2":
			if($fast_qc){
				$content = "product_qc2.php";
				$pageTitle = "ตรวจสินค้า";
			}else{
				$content = "product_qc.php";
				$pageTitle = "ตรวจสินค้า";
			}
			break;
		case "bill":
			$content = "bill.php";
			$pageTitle = "รายการรอเปิดบิล";
			break;
		case "order_closed" :
			$content = "order_closed.php";
			$pageTitle = "รายการเปิดบิลแล้ว";
			break;
		case "order_monitor" :
			$content = "order_monitor.php";
			$pageTitle = "ตรวจสอบออเดอร์";
			break;
		case "request" :
			$content = "order_request.php";
			$pageTitle = "ร้องขอสินค้า";
			break;
//**********  ระบบบัญชี  **********//
		case "repay":
			$content = "repay.php";
			$pageTitle = "ตัดหนี้";
			break;
		case "consign":
			$content = "consign.php";
			$pageTitle = "ตัดยอดฝากขาย";
			break;
		case "consign_check" :
			$content = "consign_check.php";
			$pageTitle = "กระทบยอดสินค้าฝากขาย";
			break;
		case "payment_order" :
			$content 	= 'payment_order.php';
			$pageTitle	= 'ตรวจสอบยอดชำระ';
			break;
		case "checkstock" :
			$content = "check_stock.php";
			$pageTitle = "เช็คสต็อก";
			break;
		case "OpenCheck" :
			$content = "open_check.php";
			$pageTitle = "เปิดปิดการตรวจนับ";
			break;
		case "check_stock_moniter":
			$content = "check_stock_moniter.php";
			$pageTitle = "moniter";
			break;
		case "ProductCount" :
			$content = "product_count.php";
			$pageTitle = "ตรวจสอบยอดสินค้าจากการตรวจนับ";
			break;
		case "export_consign_stock" :
			$content = "export_consign_stock.php";
			$pageTitle = "ส่งออกไฟล์ตรวจนับตั้งต้น";
			break;

//**********  ระบบซื้อ  **********//
		case "po" :
			$content		= "po.php";
			$pageTitle	= "สั่งซื้อ";
			break;
		case "po_role" :
			$content		= "po_role.php";
			$pageTitle	= "เพิ่ม/แก้ไข ประเภทการสั่งซื้อ";
			break;

//**********  รายงาน  **********//
	//*****  รายงานระบบคลังสินค้า  *****//
		case "recieved_report":
			$content = "report/stock_product_receive.php";
			$pageTitle = "รายงานการรับสินค้าจากการซื้อ";
			break;
		case "recieved_tranform_report":
			$content = "report/stock_tranform_product_receive.php";
			$pageTitle = "รายงานการรับสินค้า";
			break;
		case "transform_by_document":
			$content = "report/transform_by_document.php";
			$pageTitle = "รายงานการรับสินค้าจากการแปรสภาพแยกตามเลขที่เอกสาร";
			break;
		case "stock_summary" :
				$content	= "report/stock_summary.php";
				$pageTitle = "รายงานสรุปสินค้าคงเหลือ แยกตามรุ่นสินค้า";
			break;
		case "stock_summary_by_category" :
				$content	= "report/stock_summary_by_category.php";
				$pageTitle = "รายงานสรุปสินค้าคงเหลือ แยกตามหมวดหมู่สินค้า";
			break;
		case "current_stock":
			$content = "report/current_stock.php";
			$pageTitle = "รายงานสินค้าคงเหลือปัจจุบัน";
			break;
		case "stock_report":
			$content = "report/stock_report.php";
			$pageTitle = "รายงานสินค้าคงเหลือ";
			break;
		case "stock_zone_report";
			$content = "report/stock_zone_report.php";
			$pageTitle = " รายงานสินค้าคงเหลือ ";
			break;
		case "stock_by_warehouse";
			$content = "report/stock_by_warehouse.php";
			$pageTitle = " รายงานสินค้าคงเหลือเปรียบเทียบคลัง ";
			break;
		case "fifo";
			$content = "report/stock_fifo_report.php";
			$pageTitle = " FIFO ";
			break;
		case "total_fifo" :
			$content = "report/stock_fifo_total.php";
			$pageTitle = "รายงานยอดรวมสินค้า เข้า - ออก";
			break;
		case "movement_summary":
			$content = "report/movement_summary.php";
			$pageTitle = "รายงานความเคลื่อนไหวสินค้า เปรียบเทียบยอด เข้า - ออก";
			break;
		case "non_move":
			$content = "report/stock_non_move.php";
			$pageTitle = "รายงานสินค้าไม่เคลื่อนไหว";
			break;
		case "request_report":
			$content = "report/request_report.php";
			$pageTitle = "รายงานการร้องขอสินค้า";
			break;
		case "request_by_customer":
			$content = "report/request_by_customer.php";
			$pageTitle = "รายงานการร้องขอสินค้าแยกตามลูกค้า";
			break;
		case "received_by_document" :
			$content	= "report/received_by_document.php";
			$pageTitle	= "รายงานการรับสินค้าแยกตามเลขที่เอกสาร";
			break;
		case "received_by_product" :
			$content = "report/received_by_product.php";
			$pageTitle = "รายงานการรับสินค้าแยกตามรุ่นสินค้า";
			break;


	//*****  รายงานระบบขาย  *****//
		case "sale_summary" :
			$content	= "report/sale_summary.php";
			$pageTitle = "รายงาน สรุปยอดขาย แยกตามรุ่นสินค้า เปรียบเทียบรายเดือน";
		break;
		case "sale_summary_by_category" :
			$content	= "report/sale_summary_by_category.php";
			$pageTitle = "รายงาน สรุปยอดขาย แยกตามหมวดหมู่สินค้า เปรียบเทียบรายเดือน";
		break;
		case "sale_report_zone":
			$content = "report/sale_report_zone.php";
			$pageTitle = "รายงานยอดขาย แยกตามพื้นที่การขาย";
			break;
		case "sale_report_employee":
			$content = "report/sale_report_employee.php";
			$pageTitle = "รายงานยอดขาย แยกตามพนักงานขาย";
			break;
		case "sale_amount_detail":
			$content = "report/sale_amount_detail.php";
			$pageTitle = "รายงานรายละเอียดการขาย แยกตามพนักงานขาย";
			break;
		case "sale_amount_document":
			$content = "report/sale_amount_document.php";
			$pageTitle = "รายงานยอดขาย แยกตามพนักงานและเอกสาร";
			break;
		case "sale_report_customer":
			$content = "report/sale_report_customer.php";
			$pageTitle = "รายงานยอดขาย แยกตามลูกค้า";
			break;
		case "sale_report_product":
			$content = "report/sale_report_product.php";
			$pageTitle = "รายงานยอดขาย แยกตามสินค้า";
			break;
		case "sale_by_document":
			$content = "report/sale_by_document.php";
			$pageTitle = "รายงานยอดขาย แยกตามสินค้า";
			break;
		case "sale_detail_by_customer":
			$content = "report/sale_detail_by_customer.php";
			$pageTitle = "รายงานสินค้า แยกตามลูกค้า";
			break;

		case "sale_by_attribute" :
			$content = "report/sale_by_attribute.php";
			$pageTitle = "รายงานจำนวนขาย แยกตามคุณลักษณะสินค้า";
			break;
	//*****  รายงานระบบซื้อ  *****//

		case "po_backlog" :
			$content = "report/po_backlog.php";
			$pageTitle = "รายงานใบสั่งซื้อค้างรับ";
			break;
		case "po_by_product" :
			$content = "report/po_by_product.php";
			$pageTitle = "ประวัติการสั่งซื้อค้นตามสินค้า";
			break;
		case "product_summary_backlog_by_product" :
			$content	= "report/product_summary_backlog_by_product.php";
			$pageTitle	= "รายงานสรุปสินค้าค้างรับแยกตามรุ่นสินค้า";
			break;
		case "product_summary_backlog_by_item" :
			$content = "report/product_summary_backlog_by_item.php";
			$pageTitle 	= "รายงานสินค้าค้างรับแยกตามรายการสินค้า";
			break;
		case "product_backlog_by_supplier" :
			$content = "report/product_backlog_by_supplier.php";
			$pageTitle = "รายงานสินค้าค้างรับ แยกตามผู้ขาย";
			break;
		case 'product_backlog' :
			$content = 'report/product_backlog.php';
			$pageTitle = 'รายงานสินค้าค้างรับแบบละเอียด';
			break;
	//*****  รายงานติดตาม  *****//
		case "stock_backlogs":
			$content = "report/stock_backlogs.php";
			$pageTitle = "รายงานสินค้าค้างส่ง";
			break;
		case "order_backlogs" :
			$content = 'report/order_backlogs.php';
			$pageTitle = 'รายงานออเดอร์ค้างส่ง';
			break;
		case "sponsor_by_customer";
			$content = "report/sponsor_by_customer.php";
			$pageTitle = "รายงาน ยอดสปอนเซอร์";
			break;
		case "sponsor_summary" :
			$content = "report/sponsor_summary.php";
			$pageTitle = "รายงานสรุป ยอดสปอนเซอร์";
			break;
		case "support_by_employee" :
			$content = "report/support_by_employee.php";
			$pageTitle = "รายงาน ยอดเบิกอภินันทนาการ";
			break;
		case "support_summary" :
			$content = "report/support_summary.php";
			$pageTitle = "รายงานสรุป ยอดเบิกอภินันทนาการ";
			break;

	//*****  รายงานตรวจสอบ  *****//
		case "discount_edit":
			$content = "report/discount_edit_report.php";
			$pageTitle = "รายงานการแก้ไขส่วนลด";
			break;
		case "sponsor_log" :
			$content = "report/sponsor_log.php";
			$pageTitle = "Sponsor Log";
			break;
		case "support_log" :
			$content = "report/support_log.php";
			$pageTitle = "Support Log";
			break;
		case 'delivery_fee' :
			$content = 'report/delivery_fee.php';
			$pageTitle = 'ค่าจัดส่ง';
			break;
		case "pdbcd" :  /// product_by_customer_show_document  รายงานสินค้าแยกตามลูกค้าแสดงเลขที่เอกสาร
			$content = "report/product_by_customer_show_document.php";
			$pageTitle = "รายงานสินค้าแยกตามลูกค้าแสดงเลขที่เอกสาร";
			break;
		case "document_by_customer":
			$content = "report/document_by_customer.php";
			$pageTitle = "รายงานเอกสาร แยกตามลูกค้า";
			break;
		case "document_by_product_attribute":
			$content = "report/document_by_product_attribute.php";
			$pageTitle = "รายงานเอกสาร แยกตามรายการสินค้า";
			break;
		case "consignment_by_customer" :
			$content = "report/consignment_by_customer.php";
			$pageTitle = "รายงานบิลส่งสินค้าไปฝากขายแยกตามลูกค้า";
			break;
		case "consign_by_customer" :
			$content = "report/consign_by_customer.php";
			$pageTitle = "รายงานสินค้าฝากขายแยกตามลูกค้า";
			break;
		case "sale_consign_product_by_customer" :
			$content = "report/sale_consign_product_by_customer.php";
			$pageTitle = "รายงานยอดฝากขายแยกตามโซนแสดงรายการสินค้า";
			break;
		case "lend_by_doc" :
			$content = "report/lend_by_doc.php";
			$pageTitle = "รายงานใบยืมสินค้า เรียงตามเลขที่เอกสาร";
			break;
		case "lend_not_return" :
			$content = "report/lend_not_return.php";
			$pageTitle = "รายงานใบยืมสินค้า ยังไม่คืน";
			break;
		case "lend_by_product" :
			$content = "report/lend_by_product.php";
			$pageTitle = "รายงานใบยืมสินค้า แยกตามสินค้า";
			break;


	//*****  รายงานวิเคราะห์  *****//
	case "customer_by_product":
			$content = "report/customer_by_product.php";
			$pageTitle = "รายงานลูกค้า แยกตามสินค้า";
			break;
		case "customer_by_product_attribute":
			$content = "report/customer_by_product_attribute.php";
			$pageTitle = "รายงานลูกค้า แยกตามสินค้า";
			break;
		case "product_by_customer":
			$content = "report/product_by_customer.php";
			$pageTitle = "รายงานสินค้า แยกตามลูกค้า";
			break;

		case "product_attribute_by_customer":
			$content = "report/product_attribute_by_customer.php";
			$pageTitle = "รายงานรายการสินค้า แยกตามลูกค้า";
			break;
			/*************** รายงานวิเคราะห์ ****************/
		case "chart_move_movement_report":
			$content = "report/stock_move_chart_report.php";
			$pageTitle = "รายงานภาพรวมสินค้าเปรียบเทียบยอด เข้า / ออก";
			break;
		case "chart_movement_report":
			$content = "report/stock_chart_report.php";
			$pageTitle = "รายงานความเคลื่อนไหวสินค้า";
			break;
		case "sale_chart_zone":
			$content = "report/sale_chart_zone.php";
			$pageTitle = "กราฟรายงานยอดขาย เปรียบเทียบพื้นที่การขาย";
			break;
		case "attribute_chart_report":
			$content = "report/attribute_chart_report.php";
			$pageTitle = "รายงานวิเคราะห์คุณลักษณะสินค้า";
			break;

		case "stock_chart_zone_report" :
			$content = "report/stock_chart_zone_report.php";
			$pageTitle = "กราฟรายงานการเคลื่อนไหวสินค้า แยกตามพื้นที่การขาย";
			break;
		case "sale_chart":
			$content="report/sale_chart.php";
			$pageTitle = "กราฟรายงานวิเคราะห์ยอดขาย";
			break;
		case "sale_table":
			$content="report/sale_table.php";
			$pageTitle = "ตารางรายงานวิเคราะห์ยอดขาย";
			break;
		case "attribute_analyz" :
			$content = "report/attribute_analyz.php";
			$pageTitle = "วิเคราะคุณลักษณะสินค้า";
			break;

		case 'sale_product_deep_analyz' :
			$content 		= 'report/sale_product_deep_analyz.php';
			$pageTitle	= 'รายงานวิเคราะห์ขายแบบละเอียด';
			break;
		case 'sponsor_product_deep_analyz' :
			$content 		= 'report/sponsor_product_deep_analyz.php';
			$pageTitle	= 'รายงานวิเคราะห์สปอนเซอร์แบบละเอียด';
			break;
		case 'stock_product_deep_analyz' :
			$content		= 'report/stock_product_deep_analyz.php';
			$pageTitle	= 'รายงานวิเคราะห์สินค้าคงเหลือแบบละเอียด';
			break;
		case "support_product_deep_analyz" :
			$content 		= "report/support_product_deep_analyz.php";
			$pageTitle 	= "รายงานวิเคราะห์อภินันท์แบบละเอียด";
			break;
		case 'received_product_deep_analyz' :
			$content 		= 'report/received_product_deep_analyz.php';
			$pageTitle	= 'รายงานการรับสินค้าเข้าแบบละเอียด';
			break;

	//*****  รายงานผู้บริหาร  *****//
		case "sale_profit_by_item" :
			$content		= "report/sale_profit_by_item.php";
			$pageTitle 	= "รายงานยอดขาย แยกตามราการสินค้า แสดงกำไรขั้นต้น";
			break;
		case "sale_profit_by_customer" :
			$content		= "report/sale_profit_by_customer.php";
			$pageTitle 	= "รายงานยอดขาย แยกตามลูกค้า แสดงกำไรขั้นต้น";
			break;

	//******  รายงานอื่นๆ  *****//
		case "order_freq":
			$content = "report/order_freq.php";
			$pageTitle = "ความถี่";
			break;
		case "sale_amount_report":
			$content = "report/sale_amount_report.php";
			$pageTitle = "สรุปยอดขายรวม";
			break;
		case "sale_leader_board" :
			$content = "report/sale_leader_board.php";
			$pageTitle = "สรุปยอดขายแยกตามพนักงาน";
			break;
		case "sale_leader_group" :
			$content = "report/sale_leader_group.php";
			$pageTitle = "สรุปยอดขายแยกตามพื้นที่";
			break;
		case "sale_calendar" :
			$content = "report/sale_calendar.php";
			$pageTitle = "ปฏิทินการขาย";
			break;
		case "delivery_ticket" :
			$content = "report/delivery_ticket.php";
			$pageTitle = "การจัดส่ง";
			break;

//**********  การตั้งค่า  **********//
		case "config";
			$content = "setting.php";
			$pageTitle = "การตั้งค่า";
			break;
		case "popup" :
			$content = "popup.php";
			$pageTitle = "การแจ้งเตือน";
			break;
		case "securable":
			$content = "securable.php";
			$pageTitle = "กำหนดสิทธิ์";
			break;

//**********  ฐานข้อมูล  **********//
	//*****  สินค้า  *****//
			case 'product':
			$content = "product.php";
			$pageTitle = "รายการสินค้า";
			$id_tab = 1;
			break;
		case 'category':
			$content = 'category.php';
			$pageTitle = 'หมวดหมู่สินค้า';
			break;
		case 'product_group' :
			$content = 'product_group.php';
			$pageTitle = 'เพิ่ม/แก้ไข กลุ่มสินค้า';
			break;
		case 'color':
			$content = 'color.php';
			$pageTitle = 'รายการสี';
			break;
		case 'color_group':
			$content = 'color_group.php';
			$pageTitle = 'กลุ่มสี';
			break;
		case 'size':
			$content = 'size.php';
			$pageTitle = 'รายการไซด์';
			break;
		case "attribute":
			$content = "attribute.php";
			$pageTitle = "คุณลักษณะ";
			break;
		case "attribute_gen":
			$content = "attribute_gen.php";
			$pageTitle = "สร้างรายการสินค้าอัตโนมัติ";
			break;



	//*****  คลังสินค้า  *****//
		case 'warehouse':
			$content = 'warehouse.php';
			$pageTitle = 'คลังสินค้า';
			break;
		case 'zone':
			$content = 'zone.php';
			$pageTitle = 'รายการโซน';
			break;
		case 'import_stock':
			$content = 'import_stock.php';
			$pageTitle = "นำเข้ารายการสินค้า";
			break;

	//*****  ลูกค้า  *****//
		case "customer";
			$content="customer.php";
			$pageTitle="ข้อมูลลูกค้า";
			break;
		case "customer_transfer" :
			$content = "customer_transfer.php";
			$pageTitle = "โอนย้ายลูกค้า";
			break;
		case "address":
			$content = "address.php";
			$pageTitle = "ที่อยู่";
			break;
		case "group":
			$content = "group.php";
			$pageTitle = "กลุ่มลูกค้า";
			break;
		case "add_sponsor" :
			$content = "add_sponsor.php";
			$pageTitle = "สปอนเซอร์";
			break;
	//*****  พนักงาน  *****//
		case "Employee":
			$content = "employee.php";
			$pageTitle = "พนักงาน";
			break;
		case "sale";
			$content = "sale.php";
			$pageTitle = "พนักงานขาย";
			break;
		case "Profile":
			$content = "profile.php";
			$pageTitle = "โปรไฟล์";
			break;
		case "support" :
			$content = "support.php";
			$pageTitle = "รายการอภนันทนาการ";
			break;

	//*****  อื่นๆ  *****//
		case 'supplier' :
			$content 		= "supplier.php";
			$pageTitle	= "ผู้ขาย";
		break;
		case "sender" :
			$content = "sender.php";
			$pageTitle = "ผู้ให้บริการจัดส่ง";
			break;
		case "transport" :
			$content = "transport.php";
			$pageTitle = "เพิ่มการจัดส่ง";
			break;
		case "bank_account" :
			$content = 'bank_account.php';
			$pageTitle = 'บัญชีธนาคาร';
			break;
		case "product_db":
			$content="product_db.php";
			$pageTitle = "พิมพ์ฐานข้อมูลสินค้า";
			break;
		case "export_product_db":
			$content="export_product_db.php";
			$pageTitle = "ส่งออกฐานข้อมูลสินค้า";
			break;
		case "export_stock_zone" :
			$content = "export_stock_db.php";
			$pageTitle = "ส่งออกยอดยกไปคงเหลือปลายงวด";
			break;
		case "import_stock_zone" :
			$content = "import_stock_db.php";
			$pageTitle = "นำเข้ายอดยกมาต้นงวด";
			break;
		default:
			$content = 'main.php';
			$pageTitle = 'Smart Inventory';
			break;
}

if( $viewStockOnly === TRUE )
{
	$content = "view_stock.php";
	$pageTitle = "View Stock";
}
require_once 'template.php';
}
else
{
	require_once 'maintenance.php';
}
?>
