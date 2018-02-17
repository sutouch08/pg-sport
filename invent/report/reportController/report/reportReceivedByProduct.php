<?php

//----- รายงานการรับสินค้าแยกตามสินค้า
	$sc 			= "nodata";
	$pRange		= $_GET['pRange'];
	$tRange		= $_GET['tRange'];
	$pFrom		= trim($_GET['pFrom']);
	$pTo			= trim($_GET['pTo']);
	$from			= $tRange == 2 ? date('Y-m-d', strtotime($_GET['from'])) : date('Y-01-01');
	$to			= $tRange == 2 ? date('Y-m-d', strtotime($_GET['to'])) : date('Y-12-31');
	$qp			= $pRange == 1 ? "AND product_code >= '".$pFrom."' AND product_code <= '".$pTo."' ": "";
	
	$qr	= "SELECT tbl_receive_product_detail.id_product, product_code, product_name, SUM(tbl_receive_product_detail.qty) AS qty FROM tbl_receive_product_detail ";
	$qr 	.= "JOIN tbl_receive_product ON tbl_receive_product_detail.id_receive_product = tbl_receive_product.id_receive_product ";
	$qr	.= "JOIN tbl_product ON tbl_receive_product_detail.id_product = tbl_product.id_product ";
	$qr 	.= "WHERE tbl_receive_product_detail.status = 1 ".$qp." AND tbl_receive_product.date_add >= '".$from."' AND tbl_receive_product.date_add <= '".$to."' ";
	$qr	.= "GROUP BY tbl_receive_product_detail.id_product ORDER BY tbl_product.product_code ASC";

	$qs 	= dbQuery($qr);
	if( dbNumRows($qs) > 0 )
	{
		$ds = array();
		$no	= 1;
		$totalQty	= 1;
		while( $rs = dbFetchObject($qs) )
		{
			$arr = array(
							'no'		=> $no,
							'pCode'	=> $rs->product_code,
							'pName'	=> $rs->product_name,
							'received'	=> number_format($rs->qty	)
							);
			array_push($ds, $arr);
			$no++;
			$totalQty += $rs->qty;
		}
		$arr = array('totalQty' => number_format($totalQty));
		array_push($ds, $arr);
		$sc = json_encode($ds);
	}
	echo $sc;

?>
