<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
require "../function/report_helper.php";


if(isset($_GET['exportStockZone']))
{
	$excel = new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle('stockZone');
	$excel->getActiveSheet()->setCellValue('A1', 'zone code');
	$excel->getActiveSheet()->setCellValue('B1', 'product code');
	$excel->getActiveSheet()->setCellValue('C1', 'qty');

	$qr  = "SELECT z.barcode_zone, p.reference, s.qty ";
	$qr .= "FROM tbl_stock AS s LEFT JOIN tbl_zone AS z ON s.id_zone = z.id_zone ";
	$qr .= "LEFT JOIN tbl_product_attribute AS p ON s.id_product_attribute = p.id_product_attribute";

	//echo $qr;

	$qs = dbQuery($qr);

	if(dbNumRows($qs) > 0)
	{
		$row = 2;
		while($rs = dbFetchObject($qs))
		{
			$excel->getActiveSheet()->setCellValue('A'.$row, $rs->barcode_zone);
			$excel->getActiveSheet()->setCellValue('B'.$row, $rs->reference);
			$excel->getActiveSheet()->setCellValue('C'.$row, $rs->qty);
			$row++;
		}
	}

	//print_r($excel);


	$file_name = "STOCK_FOR_IMPORT.xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');
	setToken($_GET['token']);

}



function category_name($id_cate)
{
	$sc = '';
	$qs = dbQuery("SELECT category_name FROM tbl_category WHERE id_category = ".$id_cate);
	if( dbNumRows($qs) == 1 )
	{
		list($sc) = dbFetchArray($qs);
	}
	return $sc;
}

if( isset( $_GET['export_all'] ) && isset( $_GET['token'] ) )
{
	$web_id 	= getConfig("ITEMS_GROUP");
	$web_id = getConfig("ITEMS_GROUP");
	$excel = new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle("items");
	$excel->getActiveSheet()->setCellValue('A1', 'barcode');
	$excel->getActiveSheet()->setCellValue('B1', 'item_code');
	$excel->getActiveSheet()->setCellValue('C1', 'item_name');
	$excel->getActiveSheet()->setCellValue('D1', 'style');
	$excel->getActiveSheet()->setCellValue('E1', 'cost');
	$excel->getActiveSheet()->setCellValue('F1', 'price');
	$excel->getActiveSheet()->setCellValue('G1', 'items_group');
	$excel->getActiveSheet()->setCellValue('H1', 'category');

	$qs 		= dbQuery("SELECT barcode, reference, product_name, product_code, cost, price, default_category_id FROM tbl_product_attribute JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ORDER BY tbl_product.id_product ASC");
	if( dbNumRows($qs) > 0 ) :
		$row = 2;
		while( $rs = dbFetchArray($qs) ) :
			$excel->getActiveSheet()->setCellValue('A'.$row, $rs['barcode']);
			$excel->getActiveSheet()->setCellValue('B'.$row, $rs['reference']);
			$excel->getActiveSheet()->setCellValue('C'.$row, $rs['product_name']);
			$excel->getActiveSheet()->setCellValue('D'.$row, $rs['product_code']);
			$excel->getActiveSheet()->setCellValue('E'.$row, $rs['cost']);
			$excel->getActiveSheet()->setCellValue('F'.$row, $rs['price']);
			$excel->getActiveSheet()->setCellValue('G'.$row, $web_id);
			$excel->getActiveSheet()->setCellValue('H'.$row, category_name($rs['default_category_id']));
			$row++;
		endwhile;
	endif;
	$file_name = "items-".$web_id.".xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');
	setToken($_GET['token']);
}

if( isset( $_GET['export_product'] ) && isset( $_POST['export'] ) )
{
 	$exp 	= $_POST['export'];
	$web_id = getConfig("ITEMS_GROUP");
	$excel = new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle("items");
	$excel->getActiveSheet()->setCellValue('A1', 'barcode');
	$excel->getActiveSheet()->setCellValue('B1', 'item_code');
	$excel->getActiveSheet()->setCellValue('C1', 'item_name');
	$excel->getActiveSheet()->setCellValue('D1', 'style');
	$excel->getActiveSheet()->setCellValue('E1', 'cost');
	$excel->getActiveSheet()->setCellValue('F1', 'price');
	$excel->getActiveSheet()->setCellValue('G1', 'items_group');
	$excel->getActiveSheet()->setCellValue('H1', 'category');
	$row = 2;
	foreach($exp as $id => $val)
	{

		$qr = "SELECT barcode, reference, product_name, product_code, cost, price, default_category_id FROM tbl_product_attribute JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
		$qr .= "WHERE tbl_product_attribute.id_product = ".$id." ORDER BY tbl_product.id_product ASC";
		$qs = dbQuery($qr);
		if( dbNumRows($qs) > 0 )
		{
			while($rs = dbFetchArray($qs) )
			{
				$excel->getActiveSheet()->setCellValue('A'.$row, $rs['barcode']);
				$excel->getActiveSheet()->setCellValue('B'.$row, $rs['reference']);
				$excel->getActiveSheet()->setCellValue('C'.$row, $rs['product_name']);
				$excel->getActiveSheet()->setCellValue('D'.$row, $rs['product_code']);
				$excel->getActiveSheet()->setCellValue('E'.$row, $rs['cost']);
				$excel->getActiveSheet()->setCellValue('F'.$row, $rs['price']);
				$excel->getActiveSheet()->setCellValue('G'.$row, $web_id);
				$excel->getActiveSheet()->setCellValue('H'.$row, category_name($rs['default_category_id']));
				$row++;
			}
		}
	}

	$file_name = "items-".$web_id.".xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');
}


if( isset( $_GET['stock_in_zone'] ) && isset( $_GET['export'] ) )
{
 	$id_zone 	= $_GET['id_zone'];
	$excel = new PHPExcel();
	$excel->setActiveSheetIndex(0);
	$excel->getActiveSheet()->setTitle("stock_in_zone");
	$excel->getActiveSheet()->setCellValue('A1', 'barcode');
	$excel->getActiveSheet()->setCellValue('B1', 'item_code');
	$excel->getActiveSheet()->setCellValue('C1', 'qty');

	$qs			= "SELECT barcode, reference, qty ";
	$qs 			.= "FROM tbl_stock JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute ";
	$qs			.= "WHERE tbl_stock.id_zone = ".$id_zone;
	$qs 			= dbQuery($qs);
	if( dbNumRows($qs) > 0 )
	{
		$row = 2;
		while($rs = dbFetchArray($qs))
		{
			$excel->getActiveSheet()->setCellValue('A'.$row, trim($rs['barcode']));
			$excel->getActiveSheet()->setCellValue('B'.$row, $rs['reference']);
			$excel->getActiveSheet()->setCellValue('C'.$row, $rs['qty']);
			$row++;
		}
	}
	$file_name = "stock_in_zone_".date("d")."_".date("m")."_".(date("Y")+543).".xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	$writer->save('php://output');
	setToken($_GET['token']);
}



function outputCSV($data) {
    $output = fopen("php://output", "w");
    foreach ($data as $row) {
        fputcsv($output, $row); // here you can change delimiter/enclosure
    }
    fclose($output);
}


if( isset( $_GET['clear_filter'] ) )
{
	setcookie("db_search_text", "", time()-3600, "/");
	setcookie("db_from_date", "", 0, "/");
	setcookie("db_to_date", "", 0, "/");
	echo "success";
}

?>
