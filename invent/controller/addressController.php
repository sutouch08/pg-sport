<?php
require "../../library/config.php";
require "../../library/functions.php";
require '../function/tools.php';
require "../function/transport_helper.php";
require "../function/order_helper.php";
require "../function/date_helper.php";

/****** add new *****/
if( isset( $_GET['insertAddress'] ) && isset( $_POST['id_customer'] ) )
{
	$sc = 'fail';
	$data = array(
						'id_customer'	=> $_POST['id_customer'],
						'alias'				=> $_POST['alias'],
						'company'		=> $_POST['company'],
						'first_name'		=> $_POST['fname'],
						'last_name'		=> $_POST['lname'],
						'address1'		=> $_POST['address1'],
						'address2'		=> $_POST['address2'],
						'city'				=> $_POST['city'],
						'postcode'		=> $_POST['postcode'],
						'phone'			=> $_POST['phone'],
						'remark'			=> $_POST['remark']
						);
	$ad 	= new address();
	$rs = $ad->insertAddress($data);
	if( $rs )
	{
		$sc = 'success';	
	}
	echo $sc;
}

if( isset( $_GET['updateAddress'] ) && isset( $_GET['id_address'] ) )
{
	$sc = 'fail';
	$id = $_GET['id_address'];
	$data = array(
						'id_customer'	=> $_POST['id_customer'],
						'alias'				=> $_POST['alias'],
						'company'		=> $_POST['company'],
						'first_name'		=> $_POST['fname'],
						'last_name'		=> $_POST['lname'],
						'address1'		=> $_POST['address1'],
						'address2'		=> $_POST['address2'],
						'city'				=> $_POST['city'],
						'postcode'		=> $_POST['postcode'],
						'phone'			=> $_POST['phone'],
						'remark'			=> $_POST['remark']
						);
	$ad 	= new address();
	$rs = $ad->updateAddress($id, $data);
	if( $rs === TRUE )
	{
		$sc = 'success';	
	}
	echo $sc;
}

if( isset( $_GET['deleteAddress'] ) && isset( $_GET['id_address'] ) )
{
	$sc = 'fail';
	$ad = new address();
	$rs = $ad->deleteAddress($_GET['id_address']);
	if( $rs === TRUE )
	{
		$sc = 'success';
	}
	echo $sc;
}

if( isset( $_GET['getAddressInfo'] ) )
{
	$ad = new address($_GET['id_address']);
	$data = array(
					"alias" 		=> $ad->alias,
					"company"	=> $ad->company,
					"customer"	=> $ad->first_name.' '.$ad->last_name,
					"address"	=> $ad->address1.' '.$ad->address2,
					"city"			=> $ad->city,
					"postcode"	=> $ad->postcode,
					"phone"		=> $ad->phone,
					"remark"		=> $ad->remark
					);
	echo json_encode($data);					
}



if( isset( $_GET['check_sender'] ) && isset( $_GET['sender_name'] ) )
{
	$name = trim($_GET['sender_name']);
	if( isset( $_GET['id_sender'] ) )
	{
		$id = $_GET['id_sender'];
		$qs = dbQuery("SELECT name FROM tbl_sender WHERE id_sender != ".$id." AND name = '".$name."'");
	}
	else
	{
		$qs = dbQuery("SELECT name FROM tbl_sender WHERE name = '".$name."'");
	}
	echo dbNumRows($qs);
}

if( isset( $_GET['addNewSender'] ) && isset( $_POST['type'] ) )
{
	$name 		= $_POST['name'];
	$address1 	= $_POST['address1'];
	$address2 	= $_POST['address2'];
	$phone 		= $_POST['phone'];
	$open			= $_POST['open'];
	$close		= $_POST['close'];
	$type 		= $_POST['type'] == 'เก็บเงินปลายทาง' ? $_POST['type'] : 'เก็บเงินต้นทาง';
	$qs = dbQuery("INSERT INTO tbl_sender ( name, address1, address2, phone, open, close, type ) VALUES ( '".$name."', '".$address1."', '".$address2."', '".$phone."', '".$open."', '".$close."', '".$type."')");
	if( $qs )
	{
		echo 'success';
	}
	else
	{
		echo 'fail';
	}
}

if( isset( $_GET['updateSender'] ) && isset( $_GET['id_sender'] ) )
{
	$id 		 	= $_GET['id_sender'];
	$name 		= $_POST['name'];
	$address1 	= $_POST['address1'];
	$address2 	= $_POST['address2'];
	$phone 		= $_POST['phone'];
	$open			= $_POST['open'];
	$close		= $_POST['close'];
	$type 		= $_POST['type'] == 'เก็บเงินปลายทาง' ? $_POST['type'] : 'เก็บเงินต้นทาง';
	$qs = dbQuery("UPDATE tbl_sender SET name = '".$name."', address1 = '".$address1."', address2 = '".$address2."', phone = '".$phone."', open = '".$open."', close = '".$close."', type = '".$type."' WHERE id_sender = ".$id);
	if( $qs )
	{
		echo 'success';
	}
	else
	{
		echo 'fail';
	}
}

if( isset( $_GET['deleteSender'] ) && isset( $_GET['id_sender'] ) )
{
	$id = $_GET['id_sender'];
	$qs = dbQuery("DELETE FROM tbl_sender WHERE id_sender = ".$id);
	if( $qs )
	{
		echo 'success';
	}
	else
	{
		echo 'fail';
	}
}

if( isset( $_GET['deleteTransportCustomer'] ) && isset( $_GET['id_transport'] ) )
{
	$id = $_GET['id_transport'];
	$qs = dbQuery("DELETE FROM tbl_transport WHERE id_transport = ".$id);
	if( $qs )
	{
		echo 'success';
	}
	else
	{
		echo 'fail';
	}
}

if( isset( $_GET['isTransportCustomerExists'] ) && isset( $_GET['id_customer'] ) )
{
	$qs = dbQuery("SELECT id_customer FROM tbl_transport WHERE id_customer = ".$_GET['id_customer']);
	echo dbNumRows($qs);
}

if( isset( $_GET['insertTransportCustomer'] ) && isset( $_POST['id_customer'] ) )
{
	$id_customer 		= $_POST['id_customer'];
	$main_sender 		= $_POST['main_sender'];
	$second_sender	= $_POST['second_sender'] == '' ? 0 : $_POST['second_sender'];
	$third_sender 		= $_POST['third_sender'] == '' ? 0 : $_POST['third_sender'];
	$qs = dbQuery("INSERT INTO tbl_transport (id_customer, main_sender, second_sender, third_sender) VALUES ('".$id_customer."', '".$main_sender."', '".$second_sender."', '".$third_sender."')");
	if( $qs )
	{
		echo 'success';
	}
	else
	{
		echo 'fail';
	}
}

if( isset( $_GET['updateTransportCustomer'] ) && isset( $_GET['id_transport'] ) )
{
	$id = $_GET['id_transport'];
	$main = $_POST['main_sender'];
	$sec = $_POST['second_sender'] == '' ? 0 : $_POST['second_sender'];
	$third = $_POST['third_sender'] == '' ? 0 : $_POST['third_sender'];
	$qs = dbQuery("UPDATE tbl_transport SET main_sender = '".$main."', second_sender = '".$sec."', third_sender = '".$third."' WHERE id_transport = ".$id);
	if( $qs )
	{
		echo 'success';
	}
	else
	{
		echo 'fail';
	}	
}

if( isset( $_GET['getSenderInfo'] ) && isset( $_GET['id_sender'] ) )
{
	$id = $_GET['id_sender'];
	$data = array();
	$qs = dbQuery("SELECT * FROM tbl_sender WHERE id_sender = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$data = array(
							"sender_name"	=> $rs['name'], 
							"address" 		=> $rs['address1'].' '.$rs['address2'], 
							"phone" 			=> $rs['phone'], 
							"opentime" 		=> date("H:i", strtotime($rs['open'])).' - '.date("H:i", strtotime($rs['close'])), 
							"type" 			=> $rs['type']
							);		
	}
	echo json_encode($data);
}

if( isset( $_GET['countAddress'] ) )
{
	$adds = 0;
	if( isset( $_POST['id_customer'] ) )
	{
		$id_customer = $_POST['id_customer'];	
		$adds = countAddress($id_customer);	
	}
	echo $adds;
}

if( isset( $_GET['getAddressForm'] ) )
{
	$sc = 'no_address';
	if( isset( $_POST['id_customer'] ) )
	{
		$id_customer = $_POST['id_customer'];
		$adds 	= countAddress($id_customer); /// จำนวนที่อยู่
		$sds		= countSender($id_customer); /// จำนวนผู้จัดส่ง
		if($adds == 1 && $sds == 1 )
		{
			$sc = 1;	
		}else if( $adds >= 1 && $sds < 1 ){
			$sc  = 'no_sender';
		}else{
			if( $adds >= 1 )
			{
				$add = '<tr><td colspan="2"><strong>เลือกที่อยู่สำหรับจัดส่ง</strong></td><tr>';
				$ds	= getAllCustomerAddress($id_customer); // ได้ที่อยู่กลับมาเป็น array ถ้าไม่มีเป็น FALSE
				$n = 1;
				if( $ds !== FALSE )
				{
					while( $rs = dbFetchArray($ds) )
					{
						$se = $n == 1 ? 'checked' : '';
						$add .= '<tr>';
						$add .= '<td style="width:35%;"><label><input type="radio" name="id_address" style="margin-left:15px; margin-right:15px;" value="'.$rs['id_address'].'" '.$se.' />'.$rs['alias'].'</label></td>';
						$add .= '<td>'.$rs['address1'].' '.$rs['address2'].' จังหวัด '.$rs['city'].'</td>';
						$add .= '</tr>';
						$n++;
					}
				}
			}
			
			if( $sds >= 1 )
			{
				$dds = '<tr><td colspan="2"><strong>เลือกผู้ให้บริการจัดส่ง</strong></td><tr>';	
				$dd = getAllSender($id_customer);
				if( $dd !== FALSE )
				{
					$dds .= '<tr >';
					$dds .= '<td colspan="2"><label><input type="radio" name="id_sender" value="'.$dd['main_sender'].'" style="margin-left:15px; margin-right:15px;" checked />'.sender_name($dd['main_sender']).'</label></td><tr>';	
					if( $dd['second_sender'] != 0 )
					{
						$dds .= '<tr>';
						$dds .= '<td colspan="2"><label><input type="radio" name="id_sender" value="'.$dd['second_sender'].'" style="margin-left:15px; margin-right:15px;" />'.sender_name($dd['second_sender']).'</label></td><tr>';
					}
					if( $dd['third_sender'] != 0 )
					{
						$dds .= '<tr>';
						$dds .= '<td colspan="2"><label><input type="radio" name="id_sender" value="'.$dd['third_sender'].'" style="margin-left:15px; margin-right:15px;" />'.sender_name($dd['third_sender']).'</label></td><tr>';
					}
				}
			}
	
			if( $adds >= 1 && $sds >= 1 )
			{
				$sc = '<table class="table table-bordered">';
				$sc .= $add;
				$sc .= $dds;
				$sc .= '</table>';
			}
		}	
	}
	echo $sc;
}

if( isset( $_GET['printAddressSheet'] ) && isset( $_GET['id_customer'] ) )
{
	$id_customer 	= $_GET['id_customer'];
	$id_order		= $_GET['id_order'];	
	$reference		= get_order_reference($id_order);
	$id_address	= isset( $_GET['id_address'] ) ? $_GET['id_address'] : getIdAddress($id_customer);
	$id_sender		= isset( $_GET['id_sender'] )  ? $_GET['id_sender'] : getMainSender($id_customer);
	$sd				= getSender($id_sender);
	$ad				= getAddress($id_address);
	$cusName		= $ad['company'] == '' ? $ad['first_name'].' '.$ad['last_name'] : $ad['company'];
	$cName			= getConfig('COMPANY_FULL_NAME');
	$cAddress		= getConfig('COMPANY_ADDRESS');
	$cPhone			= getConfig('COMPANY_PHONE');
	/*********  Sender  ***********/
	$sender			= '<div class="col-lg-12" style="font-size:18px; padding-top:15px; padding-bottom:30px;">';
	$sender			.= '<span style="display:block; margin-bottom:10px;">'.$cName.'</span>';
	$sender			.= '<span style="width:70%; display:block;">'.$cAddress.' '.getConfig('COMPANY_POST_CODE').'</span>';
	$sender			.= '<span style="display:block"> โทร. '.$cPhone.'</span>';
	$sender			.= '</div>';
	/********* / Sender *************/
	
	/*********** Receiver  **********/
	$receiver		= '<div class="col-lg-12" style="font-size:18px; padding-left: 250px; padding-top:15px; padding-bottom:40px;">';
	$receiver		.= '<span style="display:block; margin-bottom:10px;">'.$cusName.'</span>';
	$receiver		.= '<span style="display:block;">'.$ad['address1'].'</span>';
	$receiver		.= '<span style="display:block;">'.$ad['address2'].'</span>';
	$receiver		.= '<span style="display:block;">จ. '.$ad['city'].' '.$ad['postcode'].'</span>';
	$receiver		.= $ad['phone'] == '' ? '' : '<span style="display:block;">โทร. '.$ad['phone'].'</span>';
	$receiver		.= '</div>';
	/********** / Receiver ***********/
	
	/********* Transport  ***********/
	$transport = '';
	if( $sd !== FALSE ) 
	{
		$transport	= '<table style="width:100%; border:0px; margin-left: 30px; position: relative; bottom:1px;">';
		$transport	.= '<tr style="font-18px;"><td>'. $sd['name'] .'</td></tr>';
		$transport	.= '<tr style="font-18px;"><td>'. $sd['address1'] .' '.$sd['address2'].'</td></tr>';
		$transport	.= '<tr style="font-18px;"><td>โทร. '. $sd['phone'] .' เวลาทำการ : '.date('H:i', strtotime($sd['open'])).' - '.date('H:i', strtotime($sd['close'])).' น. - ( '.$sd['type'].')</td></tr>';
		$transport 	.= '</table>';
	}
		
	/*********** / transport **********/
	
	$boxes 			= countBoxes($id_order);
	$total_page		= $boxes <= 1 ? 1 : ($boxes+1)/2;
	$Page = '';
	
	$printer = new printer();
	$config = array("row" => 16, "header_row" => 0, "footer_row" => 0, "sub_total_row" => 0);
	$printer->config($config);
	
	
	$Page .= $printer->doc_header();
	$n = 1;
	while($total_page > 0 )
	{
		$Page .= $printer->page_start();
		
		if( $n < ($boxes+1) )
		{
			$Page .= $printer->content_start();
			$Page .= '<table style="width:100%; border:0px;"><tr><td style="width:50%;">';
			$Page .= $sender;
			$Page .= '</td><td style=" vertical-align:text-top; text-align:right; font-size:18px; padding-top:25px; padding-right:15px;">'.$reference.' : กล่องที่ '.$n.' / '.$boxes.'</td></tr></table>';
			$Page .= $receiver;
			$Page .= $transport;
			$Page .= $printer->content_end();
			$n++;
		}
		if( $n < ($boxes+1) )
		{
			$Page .= $printer->content_start();
			$Page .= '<table style="width:100%; border:0px;"><tr><td style="width:50%;">';
			$Page .= $sender;
			$Page .= '</td><td style=" vertical-align:text-top; text-align:right; font-size:18px; padding-top:25px; padding-right:15px;">'.$reference.' : กล่องที่ '.$n.' / '.$boxes.'</td></tr></table>';
			$Page .= $receiver;
			$Page .= $transport;
			$Page .= $printer->content_end();
			$n++;
		}
		if( $n > $boxes ){
			if( $n > $boxes && ($n % 2) == 0 )
			{
				$Page .= '
				<style>.table-bordered > tbody > tr > td { border : solid 1px #333 !important;  }</style>
				<table class="table table-bordered" >
					<tr style="font-size:10px">
						<td style="width:8%;">ใบสั่งงาน</td>
						<td style="width:25%;"><input type="checkbox" style="margin-left:10px; margin-right:5px;"> รับ <input type="checkbox" checked style="margin-left:10px; margin-right:5px;"> ส่ง</td>
						<td style="width:27%;">วันที่ '.date("d/m/Y").' <input type="checkbox" style="margin-left:10px; margin-right:5px;">เช้า <input type="checkbox" style="margin-left:10px; margin-right:5px;"> บ่าย</td>
						<td style="width:20%;">จำนวน '.$boxes.' กล่อง</td>
						<td style="width:20%;">ออเดอร์ :  '.$reference.'</td>
					</tr>
					<tr style="font-size:10px;"><td>ขนส่ง</td><td>'.$sd['name'].'</td><td colspan="3">'.$sd['address1'].' '.$sd['address2'].' ('.$sd['phone'].')</td></tr>
					<tr style="font-size:10px;"><td>ผู้รับ</td><td>'.$cusName.'</td><td colspan="3">'.$ad['address1'].' '.$ad['address2'].' '.$ad['city'].' '.$ad['postcode'].'</td></tr>
					<tr style="font-size:10px;"><td>ผู้ติดต่อ</td><td>'.$ad['first_name'].'</td><td>โทร. '.$ad['phone'].'</td><td>ผู้สั่งงาน '.$_COOKIE['UserName'].'</td><td>โทร. </td></tr>
				</table>';
			}
			$n++;
		}
		$Page .= $printer->page_end();
		
		$total_page--;	
	}
	$Page .= $printer->doc_footer();
	echo $Page;	
}

//----------------  พิมพ์ใบปะหน้ากล่อง ในหน้า QC ---------------------//
if( isset( $_GET['printAddress'] ) && isset( $_GET['id_customer'] ) )
{
	$id_customer 	= $_GET['id_customer'];
	$id_order		= $_GET['id_order'];	
	$reference		= get_order_reference($id_order);
	$id_address	= isset( $_GET['id_address'] ) ? $_GET['id_address'] : getIdAddress($id_customer);
	$id_sender		= isset( $_GET['id_sender'] )  ? $_GET['id_sender'] : getMainSender($id_customer);
	$sd				= getSender($id_sender);
	$ad				= getAddress($id_address);
	$cusName		= $ad['company'] == '' ? $ad['first_name'].' '.$ad['last_name'] : $ad['company'];
	$cName			= getConfig('COMPANY_FULL_NAME');
	$cAddress		= getConfig('COMPANY_ADDRESS');
	$cPhone			= getConfig('COMPANY_PHONE');
	/*********  Sender  ***********/
	$sender			= '<div class="col-lg-12" style="font-size:18px; padding-top:15px; padding-bottom:30px;">';
	$sender			.= '<span style="display:block; margin-bottom:10px;">'.$cName.'</span>';
	$sender			.= '<span style="width:70%; display:block;">'.$cAddress.' '.getConfig('COMPANY_POST_CODE').'</span>';
	$sender			.= '<span style="display:block"> โทร. '.$cPhone.'</span>';
	$sender			.= '</div>';
	/********* / Sender *************/
	
	/*********** Receiver  **********/
	$receiver		= '<div class="col-lg-12" style="font-size:18px; padding-left: 250px; padding-top:15px; padding-bottom:40px;">';
	$receiver		.= '<span style="display:block; margin-bottom:10px;">'.$cusName.'</span>';
	$receiver		.= '<span style="display:block;">'.$ad['address1'].'</span>';
	$receiver		.= '<span style="display:block;">'.$ad['address2'].'</span>';
	$receiver		.= '<span style="display:block;">จ. '.$ad['city'].' '.$ad['postcode'].'</span>';
	$receiver		.= $ad['phone'] == '' ? '' : '<span style="display:block;">โทร. '.$ad['phone'].'</span>';
	$receiver		.= '</div>';
	/********** / Receiver ***********/
	
	/********* Transport  ***********/
	$transport = '';
	if( $sd !== FALSE ) 
	{
		$transport	= '<table style="width:100%; border:0px; margin-left: 30px; position: relative; bottom:1px;">';
		$transport	.= '<tr style="font-18px;"><td>'. $sd['name'] .'</td></tr>';
		$transport	.= '<tr style="font-18px;"><td>'. $sd['address1'] .' '.$sd['address2'].'</td></tr>';
		$transport	.= '<tr style="font-18px;"><td>โทร. '. $sd['phone'] .' เวลาทำการ : '.date('H:i', strtotime($sd['open'])).' - '.date('H:i', strtotime($sd['close'])).' น. - ( '.$sd['type'].')</td></tr>';
		$transport 	.= '</table>';
	}
		
	/*********** / transport **********/
	$Page = '';
	
	$printer = new printer();
	$config = array("row" => 16, "header_row" => 0, "footer_row" => 0, "sub_total_row" => 0);
	$printer->config($config);
	
	
	$Page .= $printer->doc_header();
	$Page .= $printer->page_start();
	$n	= 1;
	//-----------------  box 1 -------------//
	while( $n <= 2 )
	{
		$Page .= $printer->content_start();
			$Page .= '<table style="width:100%; border:0px;"><tr><td style="width:50%;">';
			$Page .= $sender;
			$Page .= '</td><td style=" vertical-align:text-top; text-align:right; font-size:18px; padding-top:25px; padding-right:15px;">'.$reference.' : กล่องที่ ........../...........</td></tr></table>';
			$Page .= $receiver;
			$Page .= $transport;
			$Page .= $printer->content_end();
			$n++;
	}
	$Page .= $printer->page_end();
	$Page .= $printer->doc_footer();
	echo $Page;	
}


//----------------  พิมพ์ใบปะหน้ากล่อง ขาย Online  ---------------------//
if( isset( $_GET['printOnlineAddressSheet'] ) && isset( $_GET['id_address'] ) )
{
	$id_order		= $_GET['id_order'];	
	$id_address	= $_GET['id_address'];   /// id_address FRom tbl_address_online
	$onlineCode		= getCustomerOnlineReference($id_order);
	$ad				= dbQuery("SELECT * FROM tbl_address_online WHERE id_address = ".$id_address);
	if( dbNumRows($ad) == 1 )
	{
		$rs 				= dbFetchArray($ad);
		$cusName		= $rs['first_name'].' '.$rs['last_name'];
		$cusAdr1			= $rs['address1'];
		$cusAdr2		= $rs['address2'];
		$cusProv			= $rs['province'];
		$cusPostCode	= $rs['postcode'];
		$cusPhone		= $rs['phone'];
		$cusCode		= $onlineCode == '' ? '' : '( '.$onlineCode.' )';
		$cName			= getConfig('COMPANY_FULL_NAME');
		$cAddress		= getConfig('COMPANY_ADDRESS');
		$cPhone			= getConfig('COMPANY_PHONE');
		$cPostCode		= getConfig("COMPANY_POST_CODE");
		
	}
	$link = WEB_ROOT.'img/company/logo.png';
	$file = realpath(DOC_ROOT.$link);
	if( ! file_exists($file) )
	{
		$link = FALSE;
	}
	$order	= new order($id_order);
	$paid		= $order->valid == 1 ? 'จ่ายแล้ว' : 'รอชำระเงิน';
	
	/*********  Sender  ***********/
	$sender			= '<div class="col-sm-12" style="font-size:14px; font-weight: bold; border:solid 2px #ccc; border-radius:10px; padding:10px;">';
	$sender			.= '<span style="display:block; font-size: 20px; font-weight:bold; padding-bottom:10px; border-bottom:solid 2px #ccc; margin-bottom:15px;">ผู้ส่ง</span>';
	$sender			.= '<span style="display:block;">'.$cName.'</span>';
	$sender			.= '<span style="width:70%; display:block;">'.$cAddress.' '.$cPostCode.'</span>';
	$sender			.= '</div>';
	/********* / Sender *************/
	
	/*********** Receiver  **********/
	$receiver		= '<div class="col-sm-12" style="font-size:24px; border:solid 2px #ccc; border-radius:10px; padding:10px;">';
	$receiver		.= '<span style="display:block; font-size: 20px; font-weight:bold; padding-bottom:10px; border-bottom:solid 2px #ccc; margin-bottom:15px;">ผู้รับ &nbsp; |  &nbsp; ';
	$receiver		.= '<span style="font-size:16px; font-weight:500">โทร. '.$cusPhone.'</span></span>';
	$receiver		.= '<span style="display:block;">'.$cusName.'</span>';
	$receiver		.= '<span style="display:block;">'.$cusAdr1.'</span>';
	$receiver		.= '<span style="display:block;">'.$cusAdr2.'</span>';
	$receiver		.= '<span style="display:block;">จ. '.$cusProv.'</span>';
	$receiver		.= '<span style="display:block; margin-top:15px;">รหัสไปรษณีย์  <span style="font-size:30px;">'.$cusPostCode.'</span></span>';
	$receiver		.= '</div>';
	/********** / Receiver ***********/
	
	//----------------------------  order detail ---------------------------//
	//--------- Left column -----------------//
	$leftCol	= '<div class="row">';
	$leftCol	.= 		'<div class="col-sm-12">';
	$leftCol	.= 			$link === FALSE ? '' : '<span style="display:block; margin-bottom:10px;"><img src="'.$link.'" width="50px;" /></span>';
	$leftCol	.= 			'<span style="font-size:12px; font-weight:bold; display:block;">'.$cName.'</span>';
	$leftCol	.= 			'<span style="font-size:12px; display:block;">'.$cAddress.' '.$cPostCode.'</span>';
	$leftCol	.= 		'</div>';
	$leftCol	.= 		'<div class="col-sm-12" style="margin-top:50px;">';
	$leftCol	.= 			'<span style="font-size:12px; font-weight:bold; display:block;">ชื่อ - ที่อยู่จัดส่งลูกค้า</span>';
	$leftCol 	.=			'<span style="font-size:12px; display:block;">'.$cusName.' '.$cusCode.'</span>';
	$leftCol	.=			'<span style="font-size:12px; display:bolck;">'.$cusAdr1.' '.$cusAdr2.' '.$cusProv.' '.$cusPostCode.'</span>';
	$leftCol	.= 		'</div>';	
	$leftCol	.= '</div>';
	
	//---------/ Left column --------------//
	//----------- Right column ------------//
	$rightCol	=	'<div class="row">';
	$rightCol	.= 		'<div class="col-sm-12">';
	$rightCol	.= 			'<p class="pull-right" style="font-size:16px;"><strong>ใบเสร็จ / ใบส่งของ</strong></p>';
	$rightCol	.=		'</div>';
	$rightCol	.= 		'<div class="col-sm-12" style="margin-top:30px; font-size:12px;">';
	$rightCol	.= 			'<p style="float:left; width:20%;">เลขที่บิล</p><p style="float:left; width:35%;">'.$order->reference.'</p>';
	$rightCol	.= 			'<p style="float:left; width:45%; text-align:right;">สถานะ <span style="padding-left:15px;">'.$paid.'</span></p>';
	$rightCol	.= 			'<p style="float:left; width:20%;">วันที่สั่งซื้อ</p><p style="float:left; width:35%;">'.thaiTextDateFormat($order->date_add, TRUE).'</p>';
	$rightCol	.= 			'<p style="float:left; width:45%; text-align:right;">จำนวน<span style="padding-left:10px; padding-right:10px;">'.$order->total_product.'</span>รายการ</p>';
	$rightCol	.=		'</div>';
	$rightCol	.= 		'<div class="col-sm-12" style="font-size:12px;">';	
	$rightCol	.= 		'<table class="table table-bordered">';
	$rightCol	.= 			'<tr style="font-size:12px">';
	$rightCol	.=				'<td align="center" width="10%">ลำดับ</td>';
	$rightCol	.=				'<td width="30%">สินค้า</td>';
	$rightCol	.=				'<td width="15%" align="center">ราคา</td>';
	$rightCol	.=				'<td width="15%" align="center">จำนวน</td>';
	$rightCol	.=				'<td width="20%" align="right">มูลค่า</td>';
	$rightCol	.=			'</tr>';
	$qs	= dbQuery("SELECT id_product_attribute, SUM( qty ) as qty FROM tbl_qc WHERE id_order = ".$id_order." AND valid = 1 GROUP BY id_product_attribute");
	$totalAmount 	= 0;
	$totalDisc		= 0;
	$deliFee			= getDeliveryFee($id_order);
	if( dbNumRows($qs) > 0 )
	{
		$n	= 1;
		while( $rs = dbFetchArray($qs) )
		{
			$order->order_product_detail($rs['id_product_attribute']);
			$p_reference	= $order->product_reference;
			$qty				= $rs['qty'];
			$price			= $order->product_price;
			$p_dis			= $order->reduction_percent;
			$a_dis			= $order->reduction_amount;
			$disc				= $p_dis > 0 ? $qty * ($price * ($p_dis * 0.01) ) : ( $a_dis > 0 ? $qty * $a_dis : 0 );
			$amount			= $qty * $price;
			$rightCol	.= 	'<tr style="font-size:10px;">';
			$rightCol	.= 		'<td align="center">'.$n.'</td>';
			$rightCol	.=		'<td>'.$p_reference.'</td>';
			$rightCol	.=		'<td align="center">'.number_format($price, 2).'</td>';
			$rightCol	.=		'<td align="center">'.number_format($qty).'</td>';
			$rightCol	.=		'<td align="right">'.number_format($amount, 2).'</td>';
			$rightCol	.=	'</tr>';
			$totalAmount		+= $amount;
			$totalDisc		+= $disc;
			$n++;
		}
	}	
	$rightCol	.=		'<tr style="font-size:10px;"><td colspan="3" rowspan="4"> หมายเหตุ : '.$order->comment.'</td><td align="right">สินค้า</td><td align="right">'.number_format($totalAmount, 2).'</td></tr>';
	$rightCol	.=		'<tr style="font-size:10px;"><td align="right">ส่วนลด</td><td align="right">'.number_format($totalDisc, 2).'</td></tr>';
	$rightCol	.=		'<tr style="font-size:10px;"><td align="right">ค่าจัดส่ง</td><td align="right">'.number_format($deliFee, 2).'</td></tr>';
	$rightCol	.=		'<tr style="font-size:10px;"><td align="right">รวมสุทธิ</td><td align="right">'.number_format(($totalAmount - $totalDisc) + $deliFee, 2).'</td></tr>';
	$rightCol	.=		'</table>';
	$rightCol	.=		'</div>';
	$rightCol	.=	'</div>';
	
	//------------/ Right column ----------------//
	//------------------------------/ order detail --------------------------//
	

	$Page = '';
	
	$printer = new printer();
	$config = array("row" => 13, "total_row" => 1, "header_row" => 0, "footer_row" => 0, "sub_total_row" => 0, "content_border" => 0);
	$printer->config($config);
	$barcode	= "<img src='".WEB_ROOT."library/class/barcode/barcode.php?text=".$order->reference."' style='height:15mm;' />";
	$Page .= $printer->doc_header();
	$Page .= $printer->page_start();
	$Page .= $printer->content_start();
	$Page .= '<table style="width:100%; border:0px;">';
	$Page .= 	'<tr>';
	$Page .= 		'<td valign="top" style="width:40%; padding:10px;">'.$sender.'</td>';
	$Page .=			'<td valign="top" style="padding:10px;">'.$receiver.'</td>';
	$Page .= 	'</tr>';
	$Page	 .= 	'<tr><td></td><td style="padding:10px;">'.$barcode.'</td></tr>';
	$Page .= '</table>';
	$Page .= '<hr style="border: 1px dashed #ccc;" />';
	$Page .= '<div class="row">';
	$Page	 .= 	'<table style="width:100%; border:0px;">';
	$Page .= 	'<tr>';
	$Page .=			'<td width="35%" style="vertical-align:text-top; padding:15px;">'.$leftCol.'</td>';
	$Page .= 		'<td width="65%" style="vertical-align:text-top; padding:15px;">'.$rightCol.'</td>';
	$Page	 .=		'</tr>';
	$Page	 .=		'</table>';
	$Page .= '</div>';
	$Page .= $printer->content_end();
	$Page .= $printer->page_end();
	$Page .= $printer->doc_footer();
	echo $Page;	
}



if( isset($_GET['clearFilter']) )
{
	setcookie('name_search', '', time()-3600, '/');
	setcookie('ad_search', '', time()-3600, '/');
	setcookie('phone_search', '', time()-3600, '/');
	setcookie('type_search', '', time()-3600, '/');
	setcookie('cus_search', '', time()-3600, '/');
	setcookie('city_search', '', time()-3600, '/');
	setcookie('sender', '', time()-3600, '/');
	echo 'success';
}



?>