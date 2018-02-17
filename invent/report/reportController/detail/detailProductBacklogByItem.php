<?php
	$sc = 'nodata';
	$id_pa	= $_GET['id_pa'];
	$tRange	= $_GET['tRange']; //--- all = 1 range = 2
	$from		= $tRange == 2 ? fromDate($_GET['from']) : date('Y-01-01 00:00:00');
	$to		= $tRange == 2 ? toDate($_GET['to']) : date('Y-m-d 23:59:59');
	
	$qr = "SELECT tbl_product_attribute.reference AS item_code, tbl_po.reference AS reference, SUM(qty) AS qty, SUM(received) AS received FROM tbl_po_detail ";
	$qr .= "JOIN tbl_product_attribute ON tbl_po_detail.id_product_attribute = tbl_product_attribute.id_product_attribute ";
	$qr .= "LEFT JOIN tbl_color ON tbl_product_attribute.id_color = tbl_color.id_color ";
	$qr .= "LEFT JOIN tbl_size ON tbl_product_attribute.id_size = tbl_size.id_size ";
	$qr .= "LEFT JOIN tbl_attribute ON tbl_product_attribute.id_attribute = tbl_attribute.id_attribute ";
	$qr .= "JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po ";
	$qr .= "WHERE tbl_po_detail.id_product_attribute = ".$id_pa." AND ";
	$qr .= "tbl_po.valid = 0 AND tbl_po.date_add >= '".$from."' AND tbl_po.date_add <= '".$to."' ";
	$qr .= "GROUP BY tbl_po.reference, tbl_po_detail.id_product_attribute ";
	$qr .= "ORDER BY tbl_product_attribute.reference ASC, tbl_color.position ASC, tbl_size.position ASC, tbl_attribute.position";
	
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
							'itemCode'	=> $rs->item_code,
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