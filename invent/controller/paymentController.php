<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
require '../function/order_helper.php';
require '../function/bank_helper.php';
require '../function/payment_helper.php';
require '../function/date_helper.php';


//------------------ ยืนยันการชำระเงิน  -----------------------//
if( isset( $_GET['confirmPayment'] ) )
{
	$sc			= 'success';
	$id_order	= $_POST['id_order'];
	$id_emp		= getCookie('user_id');
	$state		= 2;
	startTransection();
	$rs			= validPayment($id_order);
	$rd			= validOrder($id_order);
	$ra			= insertOrderState($id_order, 2, $id_emp);
	$rc			= changeToPrepare($id_order, $id_emp);
	if( $rs === TRUE && $rd === TRUE && $ra === TRUE && $rc === TRUE )
	{
		commitTransection();
	}
	else
	{
		dbRollback();
		$sc = 'fail';
	}
	echo $sc;
}

if( isset( $_GET['removePayment'] ) )
{
	$sc = 'success';
	$id_order = $_POST['id_order'];
	$qs = dbQuery("DELETE FROM tbl_payment WHERE id_order = ".$id_order." AND valid = 0");
	if( ! $qs ){ $sc = 'fail'; }
	echo $sc;
}

//------------------  ตารางรายการรอตรวจสอบยอดชำระ  ------------//
if( isset( $_GET['getOrderTable'] ) )
{
	$sc = 'fail';
	$qs = dbQuery("SELECT * FROM tbl_payment WHERE valid = 0");
	if( dbNumRows($qs) > 0 )
	{
		$ds = array();
		while( $rs = dbFetchArray($qs) )
		{
			$id 			= $rs['id_order'];
			$order 		= new order($id);
			$bank	 		= getBankAccount($rs['id_account']);
			$amount 		= orderAmount($id);
			$billDisc		= bill_discount($id);
			$amount		-= $billDisc;
			$shipFee		= getDeliveryFee($id);
			$servFee	= getServiceFee($id);

			$netAmount	=  $amount + $shipFee + $servFee;
			$arr			= array(
									"id"						=> $id,
									"reference"			=> $order->reference,
									"customer"			=> onlineCustomerName($id),
									"orderAmount"		=> number_format($amount, 2), //--- ค่าสินค้า
									"deliveryAmount"	=> number_format($shipFee, 2), //--- ค่าจัดส่ง
									"serviceAmount"	=> number_format($servFee, 2), //--- ค่าบริการ
									"totalAmount"		=> number_format($netAmount, 2), //--- ยอดที่ต้องชำระ
									"payAmount"			=> number_format($rs['pay_amount'], 2),   //--- ยอดโอน
									"bankName"			=> $bank['bank_name'],
									"accNo"				=> $bank['acc_no'],
									"payDate"			=> thaiDateFormat($rs['paydate'], TRUE, '/')
									);
			array_push($ds, $arr);
		}
		$sc = json_encode($ds);
	}
	echo $sc;
}


//--------------- ข้อมูลการชำระเงินเพื่อตรวจสอบ  ------------//
if( isset( $_GET['getPaymentDetail'] ) )
{
	$sc 			= 'fail';
	$id_order 	= $_POST['id_order'];
	$qs 			= dbQuery("SELECT * FROM tbl_payment WHERE id_order = ".$id_order." AND valid = 0 ");
	if( dbNumRows($qs) == 1 )
	{
		$rs		= dbFetchArray($qs);
		$order	= new order($id_order);
		$bank	 	= getBankAccount($rs['id_account']);
		$img		= imageUrl($order->reference);

		$ds 	= array(
						"id"			=> $id_order,
						"orderAmount"	=> number_format($rs['order_amount'], 2),
						"payAmount"		=> number_format($rs['pay_amount'], 2),
						"payDate"		=> thaiDateFormat($rs['paydate'], TRUE, '/'),
						"bankName"		=> $bank['bank_name'],
						"branch"			=> $bank['branch'],
						"accNo"			=> $bank['acc_no'],
						"accName"		=> $bank['acc_name'],
						"date_add"	=> thaiDateTime($rs['date_add']),
						"imageUrl"		=> $img === FALSE ? '' : $img
						);
		$sc = json_encode($ds);
	}
	echo $sc;
}

//--------------- ข้อมูลการชำระเงินเพื่อตรวจสอบ  ------------//
if( isset( $_GET['viewPaymentDetail'] ) )
{
	$sc 			= 'fail';
	$id_order 	= $_POST['id_order'];
	$qs 			= dbQuery("SELECT * FROM tbl_payment WHERE id_order = ".$id_order);
	if( dbNumRows($qs) == 1 )
	{
		$rs		= dbFetchArray($qs);
		$order	= new order($id_order);
		$bank	 	= getBankAccount($rs['id_account']);
		$img		= imageUrl($order->reference);

		$ds 	= array(
						"id"			=> $id_order,
						"orderAmount"	=> number_format($rs['order_amount'], 2),
						"payAmount"		=> number_format($rs['pay_amount'], 2),
						"payDate"		=> thaiDateFormat($rs['paydate'], TRUE, '/'),
						"bankName"		=> $bank['bank_name'],
						"branch"			=> $bank['branch'],
						"accNo"			=> $bank['acc_no'],
						"accName"		=> $bank['acc_name'],
						"date_add"	=> $rs['date_add'] == '0000-00-00 00:00:00' ? '' : thaiDateTime($rs['date_add']),
						"imageUrl"		=> $img === FALSE ? '' : $img
						);
		$sc = json_encode($ds);
	}
	echo $sc;
}


?>
