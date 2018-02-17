<?php

	$p_rank 	= $_POST['po'];
	$s_rank 	= $_POST['sup'];
	$t_rank	= $_POST['rank'];
	
	//---  PO Query
	$pQuery	= $p_rank == 2 ? "AND po_reference = '" . trim($_POST['reference']) . "' " : "";
	
	//---- Supplier Query
	$sQuery	= $s_rank == 2 ? "AND id_supplier = " . $_POST['id_sup'] . " " : "";
	
	//---- Time Query
	$from		= $t_rank == 2 ? fromDate($_POST['from_date']) : date('Y-01-01 00:00:00');
	$to		= $t_rank == 2 ? toDate($_POST['to_date']) : date('Y-12-31 23:59:59');
	
	$sc 		= 'fail';
	
	$qr = "SELECT tbl_receive_product.date_add AS date_add, tbl_receive_product_detail.id_product_attribute AS id_pa, tbl_product_attribute.reference AS item, ";
	$qr .= "tbl_receive_product.reference AS reference, tbl_receive_product.invoice, tbl_po.id_po, po_reference AS po, tbl_po.id_supplier, SUM( tbl_receive_product_detail.qty ) AS qty ";
	$qr .= "FROM tbl_receive_product_detail ";
	$qr .= "JOIN tbl_product_attribute ON tbl_receive_product_detail.id_product_attribute = tbl_product_attribute.id_product_attribute ";
	$qr .= "JOIN tbl_receive_product ON tbl_receive_product_detail.id_receive_product = tbl_receive_product.id_receive_product ";
	$qr .= "JOIN tbl_po ON tbl_receive_product.id_po = tbl_po.id_po ";
	$qr .= "WHERE tbl_receive_product.date_add >= '".$from."' AND tbl_receive_product.date_add <= '".$to."' AND tbl_receive_product_detail.status = 1 ";
	$qr .= $pQuery . $sQuery;
	$qr .= "GROUP BY tbl_receive_product_detail.id_product_attribute, tbl_receive_product.reference";
	$qs = dbQuery($qr);
	//echo $qr;
	
	if( dbNumRows($qs) > 0 )
	{
		$ds			= array();
		$no			= 1;
		$totalQty 		= 0;
		$totalAmount	= 0;
		while( $rs = dbFetchObject($qs) )
		{
			$cost 	= getPoPriceItem($rs->id_po, $rs->id_pa);
			$amount	= $cost * $rs->qty;
			$arr 		= array(
									"no" 				=> $no,
									"date_add" 		=> thaiDate($rs->date_add,"/"),
									"product_code"	=> $rs->item,							
									"reference" 		=> $rs->reference,
									"po_reference" => $rs->po,
									"supplier"			=> supplier_name($rs->id_supplier),
									"invoice"			=> $rs->invoice,
									"qty" 				=> number_format($rs->qty),
									"cost"				=> number_format($cost,2),
									"amount"			=> number_format($amount, 2)
								);
								
			array_push($ds, $arr);
			$totalQty 			+= $rs->qty;
			$totalAmount		+= $amount;
			$no++;
		}//----- end while
		$arr 	= array(
							'qty'	=> number_format($totalQty),
							'amount'	=> number_format($totalAmount, 2)
							);
		array_push($ds, $arr);
		$sc = json_encode($ds);
	}
	
	echo $sc;


?>