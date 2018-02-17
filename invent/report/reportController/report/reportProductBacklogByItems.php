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
	
	$pQuery		= $pdRange == 0 ? "" : "AND pd.product_code >= '".$pdFrom."' AND pd.product_code <= '".$pdTo."' ";
	$sQuery		= $supRange == 1 ? "" : "AND po.id_supplier = ".$id_sup." ";
	$poQuery	= $poOption == 2 ? "" : ($poOption == 0 ? "AND po.valid = 0 " : "AND po.valid = 1 ");
	$sc 			= "nodata";
	
	
	
	$qr = "SELECT pa.reference AS items, ";
	$qr .= "po.reference AS reference, sp.name, po.due_date, po.date_add, ";
	$qr .= "SUM( pod.qty) AS qty, SUM( pod.received) AS received, po.valid ";
	$qr .= "FROM tbl_po_detail AS pod ";
	$qr .= "JOIN tbl_product_attribute AS pa USING( id_product_attribute ) ";
	$qr .= "JOIN tbl_product AS pd ON pa.id_product = pd.id_product ";
	$qr .= "JOIN tbl_po AS po USING( id_po ) ";
	$qr .= "JOIN tbl_supplier AS sp ON po.id_supplier = sp.id ";
	$qr .= "WHERE pod.received < pod.qty ";
	$qr .= $pQuery;
	$qr .= $sQuery;
	$qr .= $poQuery;
	$qr .= "AND po.date_add >= '".$from."' AND po.date_add <= '".$to."' ";
	$qr .= "GROUP BY pod.id_product_attribute, pod.id_po";
	//echo $qr;
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
								'no'				=> $no,
								'date_add'		=> thaiDate($rs->date_add,'/'),
								'product'			=> $rs->items,
								'poReference'	=> $rs->reference,
								'supplier'			=> $rs->name,
								'due_date'		=> thaiDate($rs->due_date,'/'),
								'qty'				=> number_format($rs->qty),
								'received'		=>number_format($rs->received),
								'backlog'			=> ($balance < 0 OR $rs->valid == 1) ? 0 : number_format($balance),
								'closed'			=> $closed
								);
			array_push($ds, $arr);
			$totalPo	+= $rs->qty;
			$totalReceived	+= $rs->received;
			$totalBalance	+= ( ($balance < 0 OR $rs->valid == 1 )? 0 : $balance);
			$no++;
		}
		$arr = array(
							'totalPoQty'			=> number_format($totalPo),
							'totalReceivedQty'	=> number_format($totalReceived),
							'totalBalanceQty'	=> $totalBalance < 0 ? 0 : number_format($totalBalance)
							);
		array_push($ds, $arr);
		$sc = json_encode($ds);		 	
	}	
	
	echo $sc;
	
?>