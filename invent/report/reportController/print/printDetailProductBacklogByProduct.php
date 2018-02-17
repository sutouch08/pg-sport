<?php
	$print		= new printer();

	$id_pd		= $_GET['id_product'];
	$tRange		= $_GET['tRange'];
	$from			= $tRange == 2 ? fromDate($_GET['from']) : date('Y-01-01 00:00:00');
	$to			= $tRange == 2 ? toDate($_GET['to']) : date('Y-m-d 23:59:59');
	
	$title			= 'รายงานสรุปสินค้าค้างรับแยกตามสินค้า';
	$pTitle		= get_product_code($id_pd);
	$tTitle			= thaiDate($from, '/') . ' - ' . thaiDate($to, '/');
	
	$sc		= $print->doc_header($title);
	
	$sc 	.= '<table class="table">';
	$sc	.= '<tr style="font-size:14px;">';
	$sc 	.= '<td colspan="2" align="center">'. $title.'</td>';
	$sc	.= '</tr>';
	$sc	.= '<tr style="font-size:14px; border-bottom: solid 1px #ccc;">';
	$sc	.= '<td align="center" style="width:50%;">สินค้า | '. $pTitle .'</td>';
	$sc	.= '<td align="center" style="width:50%;">วันที่ | '. $tTitle .'</td>';
	$sc 	.= '</tr>';
	$sc	.= '</table>';
	
	$sc .= '<table class="table" style="border: solid 1px #ccc">';
	$sc .= '<thead>';
	$sc .= '<tr style="font-size:12px;">';
	$sc .= '<th style="width:10%; text-align:center;">ลำดับ</th>';
	$sc .= '<th style="width:30%;">รหัสสินค้า</th>';
	$sc .= '<th style="width:30%;">ใบสั่งซื้อ</th>';
	$sc .= '<th style="width:10%; text-align:right;">สั่งซื้อ</th>';
	$sc .= '<th style="width:10%; text-align:right;">รับแล้ว</th>';
	$sc .= '<th style="width:10%; text-align:right;">ค้างรับ</th>';
	$sc .= '</tr>';
	$sc .= '</thead>';
	
	$qr = "SELECT product_code, reference, SUM(qty) AS qty, SUM(received) AS received FROM tbl_po_detail ";
	$qr .= "JOIN tbl_product ON tbl_po_detail.id_product = tbl_product.id_product ";
	$qr .= "JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po ";
	$qr .= "WHERE tbl_po_detail.id_product = ".$id_pd." AND ";
	$qr .= "tbl_po.valid = 0 AND tbl_po.date_add >= '".$from."' AND tbl_po.date_add <= '".$to."' ";
	$qr .= "GROUP BY tbl_po.reference, tbl_po_detail.id_product";
	
	$qs = dbQuery($qr);
	
	if( dbNumRows( $qs ) > 0 )
	{
		$no = 1;
		$totalPo	= 0;
		$totalReceived = 0;
		$totalBalance = 0;
		while( $rs = dbFetchObject($qs) )
		{
			$balance = $rs->qty - $rs->received;
			$sc .= '<tr style="font-size:12px;">';
			$sc .= 	'<td align="center">'.$no.'</td>';
			$sc .= 	'<td>'. $rs->product_code .'</td>';
			$sc .= 	'<td>'. $rs->reference .'</td>';
			$sc .= 	'<td align="right">'. number_format($rs->qty) .'</td>';
			$sc .= 	'<td align="right">'. number_format($rs->received) .'</td>';
			$sc .= 	'<td align="right">'. number_format($balance) .'</td>';
			$sc .= '</tr>';
			
			$no++;
			$totalPo			+= $rs->qty;
			$totalReceived	+= $rs->received;
			$totalBalance	+= $balance;
		}
		
		$sc .= '<tr style="font-size:14px;">';
		$sc .= 	'<td colspan="3" align="right">รวม</td>';
		$sc .= 	'<td align="right">'. number_format($totalPo) .'</td>';
		$sc .= 	'<td align="right">'. number_format($totalReceived) .'</td>';
		$sc .=	'<td align="right">'. number_format($totalBalance) .'</td>';
		$sc .= '</tr>'; 	
	}	
	else
	{
		$sc .= '<tr style="font-size:14px;">';
		$sc .= 	'<td colspan="7" align="center"><h3>ไม่พบรายการค้างรับตามเงื่อนไขที่กำหนด</h3></td>';
		$sc .= '</tr>'; 		
	}
	$sc .= '</table>';
	
	$sc .= $print->doc_footer();
	
	echo $sc;
	
	?>