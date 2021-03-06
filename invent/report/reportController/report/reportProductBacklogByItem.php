<?php
	$pdRange	= isset( $_GET['pdRange'] ) ? $_GET['pdRange'] : 0;
	$pFrom	= isset( $_GET['pFrom'] ) ? trim($_GET['pFrom']) : FALSE;
	$pTo		= isset( $_GET['pTo'] ) ? trim($_GET['pTo']) : FALSE;
	$tRank	= isset( $_GET['tRange'] ) ? $_GET['tRange'] : 1;
	$from		= isset( $_GET['from'] ) && $tRank == 2 ? fromDate($_GET['from']) : date('Y-01-01 00:00:00');
	$to		= isset( $_GET['to'] ) && $tRank == 2 ? toDate($_GET['to']) : date('Y-12-31 23:59:59');
	$pQuery	= $pdRange == 0 ? "" : "product_code >= '".$pFrom."' AND product_code <= '".$pTo."' AND ";
	$sc 		= "nodata";
	
	$qr = "SELECT tbl_po_detail.id_product_attribute, tbl_product_attribute.reference, product_code, product_name, SUM(qty) AS qty, SUM(received) AS received FROM tbl_po_detail ";
	$qr .= "JOIN tbl_product_attribute ON tbl_po_detail.id_product_attribute = tbl_product_attribute.id_product_attribute ";
	$qr .= "LEFT JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color ";
	$qr .= "LEFT JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size ";
	$qr .= "LEFT JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute ";
	$qr .= "JOIN tbl_product ON tbl_po_detail.id_product = tbl_product.id_product ";
	$qr .= "JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po ";
	$qr .= "WHERE ".$pQuery;
	$qr .= "tbl_po.valid = 0 AND tbl_po.date_add >= '".$from."' AND tbl_po.date_add <= '".$to."' GROUP BY tbl_po_detail.id_product_attribute ";
	$qr .= "ORDER BY product_code ASC, tbl_color.position ASC, tbl_size.position ASC, tbl_attribute.position ASC";
	
	$qs = dbQuery($qr);
	if( dbNumRows( $qs ) > 0 )
	{
		$no = 1;
		$ds = array();
		$totalPo	= 0;
		$totalReceived = 0;
		$totalBalance = 0;
		while( $rs = dbFetchObject($qs) )
		{
			$balance = $rs->qty - $rs->received;
			$arr = array(
								'no'			=> $no,
								'id_pa'		=> $rs->id_product_attribute,
								'itemCode'	=> $rs->reference,
								'pCode'		=> $rs->product_code,
								'pName'		=> $rs->product_name,
								'poQty'		=> number_format($rs->qty),
								'receivedQty'	=>number_format($rs->received),
								'balanceQty'	=> number_format($balance)
								);
			array_push($ds, $arr);
			$totalPo	+= $rs->qty;
			$totalReceived	+= $rs->received;
			$totalBalance	+= $balance;
			$no++;
		}
		$arr = array(
							'totalPoQty'	=> number_format($totalPo),
							'totalReceivedQty'	=> number_format($totalReceived),
							'totalBalanceQty'	=> number_format($totalBalance)
							);
		array_push($ds, $arr);
		$sc = json_encode($ds);		 	
	}	
	
	echo $sc;
?>