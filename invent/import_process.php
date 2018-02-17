<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>import stock</title>
    <!-- Bootstrap core CSS -->
   <!-- <link href="library/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
  
  </head>
  <body>
 <?php
	require "../library/config.php";
	require "../library/functions.php";
	require "function/tools.php";
	ini_set('max_execution_time', 300); //300 seconds = 5 minutes
	if(isset($_GET['import'])){
		if(isset($_GET['id_recieved_product'])){ $id_recieved_product = $_GET['id_recieved_product']; }else{ $id_recieved_product = 0 ; }
		if(isset($_GET['date_add'])){ $date_add = dbDate($_GET['date_add']); }else{ $date_add = "NOW()"; }
	$objCSV = fopen($_FILES["csv"]["tmp_name"], "r");
	$i =0;
	$n=0;
	while (($field = fgetcsv($objCSV, 10000, ",")) !== FALSE) {
		list($id_zone, $id_warehouse) = dbFetchArray(dbQuery("SELECT id_zone, id_warehouse FROM tbl_zone WHERE barcode_zone ='".$field[0]."'"));
		list($id_product_attribute) = dbFetchArray(dbQuery("SELECT id_product_attribute FROM tbl_product_attribute WHERE barcode = '".$field[1]."'"));
		if($id_zone !="" && $id_product_attribute != ""){
			dbQuery("INSERT INTO tbl_recieved_detail (id_recieved_product, id_product_attribute, qty, id_warehouse, id_zone, date, status) VALUES ($id_recieved_product, $id_product_attribute,".$field[2].", $id_warehouse, $id_zone, $date_add, 0)");
			echo" <p>เพิ่ม ".$field[0]." : ".$field[1]." : ".$field[2]." - สำเร็จ. </p>";
			$i++;
		}else{
			echo"<p>****************************************************** เพิ่ม ".$field[0]." : ".$field[1]." : ".$field[2]." - ไม่สำเร็จ. </p>";
			$n++;
		}
}
echo "สำเร็จ $i รายการ ไม่สำเร็จ $n รายการ";
fclose($objCSV);
echo"<p><a href='index.php?content=product_in&edit=y&id_recieved_product=$id_recieved_product&date_add=$date_add'><button type='button' class='btn btn-success'>&nbsp;&nbsp;นำเข้าเรียบร้อย&nbsp;&nbsp;</button></a></p>";
	//header("location:index.php?content=set&completed=Y");
}
?>
  </body>
</html>
