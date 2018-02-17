<?php
	$sc = 'nodata';
	$id_pd	= $_GET['id_product'];
	$tRange	= $_GET['tRange']; //--- all = 1 range = 2
	$from		= $tRange == 2 ? fromDate($_GET['from']) : date('Y-01-01 00:00:00');
	$to		= $tRange == 2 ? toDate($_GET['to']) : date('Y-m-d 23:59:59');
	
	$qr = "SELECT product_code, reference, SUM(qty) AS qty, SUM(received) AS received FROM tbl_po_detail ";
	$qr .= "JOIN tbl_product ON tbl_po_detail.id_product = tbl_product.id_product ";
	$qr .= "JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po ";
	$qr .= "WHERE tbl_po_detail.id_product = ".$id_pd." AND ";
	$qr .= "tbl_po.valid = 0 AND tbl_po.date_add >= '".$from."' AND tbl_po.date_add <= '".$to."' ";
	$qr .= "GROUP BY tbl_po.reference, tbl_po_detail.id_product";
	
	$qs = dbQuery($qr);
	if(dbNumRows($qs) > 0 )
	{
		$ds = array();
		$no = 1;
		$totalPO = 0;
		$totalReceived = 0;
		$totalBalance = 0;
		while( $rs = dbFetchObject($qs) )
		{
			$balance = $rs->qty - $rs->received;
			$arr = array(
							'no'		=> $no,
							'pCode'	=> $rs->product_code,
							'PO'		=> $rs->reference,
							'pQty'		=> number_format($rs->qty),
							'rQty'		=> number_format($rs->received),
							'bQty'		=> number_format($balance)
							);
			array_push($ds, $arr);
			$no++;
			$totalPO	+= $rs->qty;
			$totalReceived	+= $rs->received;
			$totalBalance	+= $balance;			
		}
		$arr = array(
					'totalPO'	=> number_format($totalPO),
					'totalReceived'	=> number_format($totalReceived),
					'totalBalance'	=> number_format($totalBalance)
					);
		array_push($ds, $arr);
		$sc = json_encode($ds);
	}
	echo $sc;

?>