<?php 
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
$id_employee = $_COOKIE['user_id'];

if( isset( $_GET['get_zone']) && isset( $_POST['zone'] ) )
{
	$qs = dbQuery("SELECT id_zone FROM tbl_zone WHERE zone_name = '".$_POST['zone']."' OR barcode_zone = '".$_POST['zone']."' LIMIT 1 ");
	if(dbNumRows($qs) == 1 )
	{
		list($id_zone) = dbFetchArray($qs);
		echo $id_zone;	
	}
	else
	{
		echo "fail";
	}
}

if( isset($_GET['save_checked']) && isset($_POST['id_zone']) )
{
	$diff = $_POST['qty_check'] - $_POST['qty'] ;
	$rs = false;
	$qs = dbQuery("SELECT id_diff FROM tbl_diff WHERE id_zone = ".$_POST['id_zone']." AND id_product_attribute = ".$_POST['id_product_attribute']." AND status_diff = 0");
	$row = dbNumRows($qs);
	if($diff != 0 )
	{
		if($diff > 0 )
		{
			if($row == 0 )
			{
				$rs = dbQuery("INSERT INTO tbl_diff ( id_zone, id_product_attribute, qty_add, qty_minus, id_employee, status_diff ) VALUES (".$_POST['id_zone'].", ".$_POST['id_product_attribute'].", ".$diff.", 0, ".$id_employee.", 0 )");
			}
			else if( $row == 1 )
			{
				$rd = dbFetchArray($qs);
				$rs = dbQuery("UPDATE tbl_diff SET qty_add = ".$diff.", qty_minus = 0, id_employee = ".$id_employee." WHERE id_diff = ".$rd['id_diff']);
			}
			else
			{
				$qr = dbQuery("DELETE FROM tbl_diff WHERE id_zone = ".$_POST['id_zone']." AND id_product_attribute = ".$_POST['id_product_attribute']." AND status_diff = 0 ");
				if($qr)
				{
					$rs = dbQuery("INSERT INTO tbl_diff ( id_zone, id_product_attribute, qty_add, qty_minus, id_employee, status_diff ) VALUES (".$_POST['id_zone'].", ".$_POST['id_product_attribute'].", ".$diff.", 0, ".$id_employee.", 0 )");
				}
			}
		}
		else if($diff < 0 )
		{
			$diff = $diff * -1;  /// ทำให้เป็นจำนวนเต็มไม่ติดลบ
			if($row == 0 )
			{
				$rs = dbQuery("INSERT INTO tbl_diff ( id_zone, id_product_attribute, qty_add, qty_minus, id_employee, status_diff ) VALUES (".$_POST['id_zone'].", ".$_POST['id_product_attribute'].", 0, ".$diff.", ".$id_employee.", 0 )");
			}
			else if( $row == 1 )
			{
				$rd = dbFetchArray($qs);
				$rs = dbQuery("UPDATE tbl_diff SET qty_add = 0, qty_minus = ".$diff.", id_employee = ".$id_employee." WHERE id_diff = ".$rd['id_diff']);
			}
			else
			{
				$qr = dbQuery("DELETE FROM tbl_diff WHERE id_zone = ".$_POST['id_zone']." AND id_product_attribute = ".$_POST['id_product_attribute']." AND status_diff = 0 ");
				if($qr)
				{
					$rs = dbQuery("INSERT INTO tbl_diff ( id_zone, id_product_attribute, qty_add, qty_minus, id_employee, status_diff ) VALUES (".$_POST['id_zone'].", ".$_POST['id_product_attribute'].", 0, ".$diff.", ".$id_employee.", 0 )");
				}
			}
		}
	}
	else if($diff == 0 )
	{
		if($row > 0 )
		{
			$rs = dbQuery("DELETE FROM tbl_diff WHERE id_zone = ".$_POST['id_zone']." AND id_product_attribute = ".$_POST['id_product_attribute']." AND status_diff = 0 ");
		}else{
			$rs = true;
		}
	}
	if($rs)
	{
		echo $diff;
	}else{
		echo "fail";
	}
}


if( isset( $_GET['editqty'] ) && isset( $_POST['id_zone'] ) )
{
	$id_zone = $_POST['id_zone'];
	$qty = $_POST['qty'];
	$qty_check = $_POST['qty_check'];
	foreach( $qty_check as $id => $val )
	{
		$diff = $val - $qty[$id];
		$qs = dbQuery("SELECT id_diff FROM tbl_diff WHERE id_zone = ".$id_zone." AND id_product_attribute = ".$id." AND status_diff = 0");
		$row = dbFetchArray($qs);
		if($diff > 0)
		{
			if($row == 0 )
			{		
				dbQuery("INSERT INTO tbl_diff ( id_zone, id_product_attribute, qty_add, qty_minus, id_employee, status_diff ) VALUES (".$id_zone.", ".$id.", ".$diff.", 0, ".$id_employee.", 0)");
			}
			else if( $row == 1 )
			{
				$rs = dbFetchArray($qs);
				dbQuery("UPDATE tbl_diff SET qty_add = ".$diff.", qty_minus = 0, id_employee = ".$id_employee." WHERE id_diff = ".$rs['id_diff']);
			}
			else
			{
				$qr = dbQuery("DELETE FROM tbl_diff WHERE id_zone = ".$id_zone." AND id_product_attribute = ".$id." AND status_diff = 0");
				if($qr)
				{
					dbQuery("INSERT INTO tbl_diff ( id_zone, id_product_attribute, qty_add, qty_minus, id_employee, status_diff ) VALUES (".$id_zone.", ".$id.", ".$diff.", 0, ".$id_employee.", 0 )");
				}
			}
		}
		else if($diff < 0 )
		{
			$diff = $diff * -1;  /// ทำให้เป็นจำนวนเต็มไม่ติดลบ
			if($row == 0 )
			{
				dbQuery("INSERT INTO tbl_diff ( id_zone, id_product_attribute, qty_add, qty_minus, id_employee, status_diff ) VALUES (".$id_zone.", ".$id.", 0, ".$diff.", ".$id_employee.", 0)");
			}
			else if( $row == 1 )
			{
				$rs = dbFetchArray($qs);
				dbQuery("UPDATE tbl_diff SET qty_add = 0, qty_minus = ".$diff.", id_employee = ".$id_employee." WHERE id_diff = ".$rs['id_diff']);
			}
			else
			{
				$qr = dbQuery("DELETE FROM tbl_diff WHERE id_zone = ".$id_zone." AND id_product_attribute = ".$id." AND status_diff = 0");
				if($qr)
				{
					dbQuery("INSERT INTO tbl_diff ( id_zone, id_product_attribute, qty_add, qty_minus, id_employee, status_diff ) VALUES (".$id_zone.", ".$id.", 0, ".$diff.", ".$id_employee.", 0)");
				}
			}
		}
		else if( $diff == 0 )
		{
			if($row > 0 )
			{
				$qr = dbQuery("DELETE FROM tbl_diff WHERE id_zone = ".$id_zone." AND id_product_attribute = ".$id." AND status_diff = 0");
			}
		}
		$i++;
	}
	header("location: ../index.php?content=ProductCheck&id_zone=$id_zone&saved");
}


if( isset( $_GET['add'] ) && isset( $_POST['barcode'] ) )
{
	$product = new product();
	$arr 	= $product->check_barcode($_POST['barcode']); ///ดึง id_product_attribute และ จำนวน จากบาร์โค้ด คืนค่ามาเป็น array [id_product_attribute] และ [qty] ตามลำดับ
	$qty 	= $arr['qty'] * $_POST['qty'];
	$id		= $arr['id_product_attribute'];
	$id_zone = $_POST['id_zone'];
	if($id)
	{
		$qs = dbNumRows(dbQuery("SELECT * FROM tbl_stock WHERE id_product_attribute = ".$id." AND id_zone = ".$id_zone));
		if($qs)
		{
			echo "duplicated";
		}
		else
		{
			$qs = dbQuery("INSERT INTO tbl_stock (id_zone, id_product_attribute, qty) VALUES (".$id_zone.", ".$id.", 0 )");
			if($qs)
			{
				$qr = dbQuery("INSERT INTO tbl_diff ( id_zone, id_product_attribute, qty_add, qty_minus, id_employee, status_diff ) VALUES (".$id_zone.", ".$id.", 0, ".$qty.", ".$id_employee.", 0)");
				if($qr)
				{
					$data = array(
								"id" => $id,
								"product" => get_product_reference($id),
								"qty" => 0,
								"diff" => $qty,
								"sum" => $qty,
								"id_zone" => $id_zone
								);
					echo json_encode($data);
				}
				else
				{
					echo "fail";
				}
			}
		}
	}
	else
	{
		echo "noproduct";	
	}
}
	
?>