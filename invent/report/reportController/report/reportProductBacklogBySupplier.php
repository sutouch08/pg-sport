<?php
	$pdRange	= isset( $_POST['pdRange'] ) ? $_POST['pdRange'] : 0;
	$pdFrom		= isset( $_POST['pdFrom'] ) ? trim($_POST['pdFrom']) : FALSE;
	$pdTo		= isset( $_POST['pdTo'] ) ? trim($_POST['pdTo']) : FALSE;
	$supRange	= isset( $_POST['supRange'] ) ? $_POST['supRange'] : 0;
	$id_sup		= isset( $_POST['id_sup'] ) ? $_POST['id_sup'] : 0;
	$tRank		= isset( $_POST['timeRange'] ) ? $_POST['timeRange'] : 1;
	$from			= isset( $_POST['from'] ) && $tRank == 2 ? fromDate($_POST['from']) : date('Y-01-01 00:00:00');
	$to			= isset( $_POST['to'] ) && $tRank == 2 ? toDate($_POST['to']) : date('Y-12-31 23:59:59');
	$poOption	= isset( $_POST['poOption'] ) ? $_POST['poOption'] : 0 ;
	
	$pQuery		= $pdRange == 0 ? "" : "AND product_code >= '".$pdFrom."' AND product_code <= '".$pdTo."' ";
	$sQuery		= $supRange == 1 ? "" : "AND tbl_po.id_supplier = ".$id_sup." ";
	$poQuery	= $poOption == 2 ? "" : ($poOption == 0 ? "AND tbl_po.valid = 0 " : "AND tbl_po.valid = 1 ");
	$sc 			= "nodata";
	
	$qr = "SELECT tbl_po_detail.id_product, ";
	$qr .= "product_code, ";
	$qr .= "tbl_po.reference, ";
	$qr .= "tbl_supplier.name, ";
	$qr .= "tbl_po.due_date, ";
	$qr .= "tbl_po.date_add, ";
	$qr .= "SUM(qty) AS qty, ";
	$qr .= "SUM(received) AS received, ";
	$qr .= "tbl_po.valid ";
	$qr .= "FROM tbl_po_detail ";
	$qr .= "JOIN tbl_product ON tbl_po_detail.id_product = tbl_product.id_product ";
	$qr .= "JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po ";
	$qr .= "JOIN tbl_supplier ON tbl_po.id_supplier = tbl_supplier.id ";
	$qr .= "WHERE tbl_po.id_po != 0 ";
	$qr .= $pQuery;
	$qr .= $sQuery;
	$qr .= $poQuery;
	$qr .= "AND tbl_po.date_add >= '".$from."' AND tbl_po.date_add <= '".$to."' ";
	$qr .= "GROUP BY tbl_po_detail.id_product, tbl_po_detail.id_po";

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
			$closed = $rs->valid == 0 ? '' : 'closed';
			$arr = array(
								'no'			=> $no,
								'date_add'	=> thaiDate($rs->date_add,'/'),
								'id_product'	=> $rs->id_product,
								'product'		=> $rs->product_code,
								'poReference'		=> $rs->reference,
								'supplier'		=> $rs->name,
								'due_date'	=> thaiDate($rs->due_date,'/'),
								'qty'		=> number_format($rs->qty),
								'received'	=>number_format($rs->received),
								'backlog'	=> ($balance < 0 OR $rs->valid == 1) ? 0 : number_format($balance),
								'closed'	=> $closed
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
							'totalBalanceQty'	=> $totalBalance < 0 ? 0 : number_format($totalBalance)
							);
		array_push($ds, $arr);
		$sc = json_encode($ds);		 	
	}	
	
	echo $sc;
?>