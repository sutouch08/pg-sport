<?php
require "../../../library/config.php";
require "../../../library/functions.php";
require "../../../library/class/php-excel.class.php";
require "../../function/tools.php";
require "../../function/report_helper.php";
require "../../function/order_helper.php";
require "../../function/transport_helper.php";


if( isset( $_GET['getDeliveryTicket'] ) )
{
	$ORDER			= $_GET['order'];
	$count			= count($ORDER);
	$rows			= 7;
	$total_rows		= $count * $rows ;
	$p_rows			= 16;
	$total_page		= ($count/$rows) > 1 ? $count/$rows : 1 ;
	$Page 			= '<style>.table-bordered > tbody > tr > td { border : solid 1px #333 !important;  }</style>';

	$printer 			= new printer();
	$config 			= array("row" => $p_rows, "header_row" => 0, "footer_row" => 0, "sub_total_row" => 0);
	$printer->config($config);
	$i	= 1;
	foreach( $ORDER as $id_order )
	{
		$data[$i] 	= $id_order;
		$i++;
	}
	
	$Page .= $printer->doc_header();
	$i	= 1;
	while($total_page > 0 )
	{
		$Page .= $printer->page_start();
		$n = 1;
		while( $n <= $rows && $i <= $count )
		{
			$id_order 		= $data[$i];
			$order			= new order($id_order);
			$reference		= $order->reference;
			$id_customer	= $order->id_customer;
			$id_address	= getIdAddress($id_customer);
			$id_sender		= getMainSender($id_customer);
			$sd				= getSender($id_sender);
			$ad				= getAddress($id_address);
			$cusName		= $ad['company'] != '' ? $ad['company'] : ($ad['first_name'] != '' ? $ad['first_name'].' '.$ad['last_name'] : customer_name($id_customer) );
			$cName			= getConfig('COMPANY_FULL_NAME');
			$cAddress		= getConfig('COMPANY_ADDRESS');
			$cPhone			= getConfig('COMPANY_PHONE');
			$box				= countBox($id_order);
			$c_box			= $box == 0 ? ' ___ ' : $box;
			$s_address		= $sd['phone'] == '' ? $sd['address1'].' '.$sd['address2'] : $sd['address1'].' '.$sd['address2'].' ('.$sd['phone'].')';
			$Page .= '				
				<table class="table table-bordered" style="margin-bottom:5px;">
					<tr style="font-size:10px">
						<td style="width:8%;">ใบสั่งงาน</td>
						<td style="width:25%;"><input type="checkbox" style="margin-left:10px; margin-right:5px;"> รับ <input type="checkbox" checked style="margin-left:10px; margin-right:5px;"> ส่ง</td>
						<td style="width:27%;">วันที่ '.date("d/m/Y").' <input type="checkbox" style="margin-left:10px; margin-right:5px;">เช้า <input type="checkbox" style="margin-left:10px; margin-right:5px;"> บ่าย</td>
						<td style="width:20%;">จำนวน '.$c_box.' กล่อง</td>
						<td style="width:20%;">ออเดอร์ :  '.$reference.'</td>
					</tr>
					<tr style="font-size:10px;"><td>ขนส่ง</td><td>'.$sd['name'].'</td><td colspan="3">'.$s_address.'</td></tr>
					<tr style="font-size:10px;"><td>ผู้รับ</td><td>'.$cusName.'</td><td colspan="3">'.$ad['address1'].' '.$ad['address2'].' '.$ad['city'].' '.$ad['postcode'].'</td></tr>
					<tr style="font-size:10px;"><td>ผู้ติดต่อ</td><td>'.$ad['first_name'].'</td><td>โทร. '.$ad['phone'].'</td><td>ผู้สั่งงาน '.$_COOKIE['UserName'].'</td><td>โทร. </td></tr>
				</table>';
				$i++; $n++;
		}
		$Page .= $printer->page_end();
		$total_page--;	
	}
	$Page .= $printer->doc_footer();
	echo $Page;	
}



if( isset( $_GET['clearFilter'] ) )
{
	deleteCookie('s_ref');
	deleteCookie('s_cus');
	deleteCookie('s_emp');
	deleteCookie('from');
	deleteCookie('to');
	deleteCookie('range');
	echo 'success';	
}

?>