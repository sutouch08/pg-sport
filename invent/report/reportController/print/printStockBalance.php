<?php
	$print = new printer();
	$sc	= $print->doc_header();

	$pdOption 	= $_GET['pdOption'];
	$whOption	= $_GET['whOption'];
	$dateOption	= $_GET['dateOption'];
	$pdFrom		= $pdOption == 1 ? $_GET['pdFrom'] : FALSE;
	$pdTo		= $pdOption == 1 ? $_GET['pdTo'] : FALSE;
	$wh			= $whOption == 1 ? $_GET['wh'] : FALSE;
	$date 		= $dateOption == 1 ? dbDate($_GET['date']) : date('Y-m-d');
	$whList		= $whOption == 1 ? warehouseIn($wh) : warehouseIn($wh, TRUE);
	
	$pdQuery 	= $pdOption == 1 ? "AND product_code >= '".$pdFrom."' AND product_code <= '".$pdTo."' " : "";
	$whQuery	= $whOption == 1 && $whList !== FALSE ? "AND id_warehouse IN(".$whList.") " : '';
	$sc 	.= '<table class="table">';
	$sc	.= '<tr style="font-size:14px;">';
	$sc 	.= '<td colspan="2" align="center">รายงานสินค้าคงเหลือ ณ วันที่ '.thaiDate($date, '/').'</td>';
	$sc	.= '</tr>';
	$sc	.= '<tr style="font-size:14px;">';
	$sc	.= '<td align="center" style="width:50%;">สินค้า | '. ($pdOption == 0 ? 'ทั้งหมด' : $pdFrom .' - '. $pdTo). '</td>';
	$sc	.= '<td align="center" style="width:50%;">คลังสินค้า | '.( $whOption == 0 ? 'ทุกคลัง' : warehouseNameList($whList) ).'</td>';
	$sc 	.= '</tr>';
	$sc	.= '</table>';

	$sc .= '<table class="table">';
	$sc .= '<thead>';
	$sc .= '<tr style="font-size:12px;">';
	$sc .= '<th style="width:5%; text-align:center;">ลำดับ</th>';
	$sc .= '<th style="width:15%;">บาร์โค้ด</th>';
	$sc .= '<th style="width:20%;">รหัสสินค้า</th>';
	$sc .= '<th style="width:30%;">ชื่อสินค้า</th>';
	$sc .= '<th style="width:10%; text-align:right;">ทุน</th>';
	$sc .= '<th style="width:10%; text-align:right;">คงเหลือ</th>';
	$sc .= '<th style="width:10%; text-align:right;">มูลค่า</th>';
	$sc .= '</tr>';
	$sc .= '</thead>';
	
	if( $pdOption == 0 )
	{
		$qr = "SELECT id_product_attribute, barcode, reference, product_name, cost FROM tbl_product_attribute ";
		$qr .= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
		$qr .= "ORDER BY tbl_product_attribute.id_product ASC";
	}
	else
	{
		$qr = "SELECT id_product_attribute, barcode, reference, product_name, cost FROM tbl_product_attribute ";
		$qr .= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
		$qr .= "WHERE tbl_product_attribute.id_product != 0 ";
		$qr .= $pdQuery;
		$qr .= "ORDER BY tbl_product_attribute.id_product ASC";
	}
	
	$qs = dbQuery($qr);
	$n = 1;
	$total_qty = 0;
	$total_amount = 0;
	
	if( dbNumRows($qs) > 0 )
	{
		$today	= date('Y-m-d');
		$product = new product();
		while( $rs = dbFetchObject($qs) )
		{
			$currentQty 	= $whOption == 0 ? $product->all_available_qty($rs->id_product_attribute) : $product->stock_qty_by_warehouse($rs->id_product_attribute, $whList);
			$moveQty 	= $product->move_qty_by_warehouse($rs->id_product_attribute, $whList);
			$cancleQty	= $product->cancle_qty_by_warehouse($rs->id_product_attribute, $whList);
			$bufferQty 	= $product->buffer_qty_by_warehouse($rs->id_product_attribute, $whList);
			$qty = $currentQty + $moveQty + $cancleQty + $bufferQty;
			if( $dateOption == 1 && $date < $today) //---- ถ้าดูย้อนหลัง
			{
				$today = date('Y-m-d H:i:s');
				$viewDate = fromDate(date("Y-m-d", strtotime("+1 day", strtotime($date) ) ) ); //---- วันที่คำนวน transection
				$transectionQty = getTransectionQty($rs->id_product_attribute, $whList, $viewDate, $today);
				$movement = ( $transectionQty['move_in'] - $transectionQty['move_out'] ) * (-1);  //---- (ยอดรับเข้า - ยอดจ่ายออก) * -1 เพื่อนำไปบวกกลับ
				$qty = $qty + $movement;
			}
			if( $qty != 0 )
			{
				$sc .= '<tr style="font-size:12px;">';
				$sc .= 	'<td align="center">'.$n.'</td>';
				$sc .= 	'<td>'.$rs->barcode.'</td>';
				$sc .= 	'<td>'.$rs->reference.'</td>';
				$sc .= 	'<td>'.$rs->product_name.'</td>';
				$sc .= 	'<td align="right">'. number_format($rs->cost, 2) .'</td>';
				$sc .= 	'<td align="right">'. number_format($qty) .'</td>';
				$sc .= 	'<td align="right">'. number_format($qty * $rs->cost, 2) .'</td>';
				$sc .= 	'</tr>';
				$total_qty += $qty;
				$total_amount += $qty * $rs->cost;
				$n++;	
			}
		}//--- end while
		
		$sc .= '<tr>';
		$sc .= '<td colspan="5" align="right">รวม</td>';
		$sc .= '<td align="right">'. number_format($total_qty) .'</td>';
		$sc .= '<td align="right">'. number_format($total_amount, 2) .'</td>';
		$sc .= '</tr>';
		$sc .= '</table>';
	}
	
	$sc .= $print->doc_footer();
	
	echo $sc;

?>