<?php 
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
$id_employee = $_COOKIE['user_id'];
function isMoved($id_product_attribute, $id_zone)
{
	$id = 0;
	$qs = dbQuery("SELECT id_move FROM tbl_move WHERE id_product_attribute = ".$id_product_attribute." AND id_zone = ".$id_zone);
	if( dbNumRows($qs) > 0 )
	{
		list($id) = dbFetchArray($qs);
	}
	return $id;	
}

if( isset( $_GET['move_cancle_out'] ) && isset( $_POST['move_qty'] ) )
{
	$move_qty	= $_POST['move_qty'];
	foreach($move_qty as $id => $val)
	{
		if($val != "")
		{
			startTransection();
			$qs 			= dbQuery("SELECT id_product_attribute, qty, id_zone, id_warehouse FROM tbl_cancle WHERE id_cancle = ".$id);
			$rs = dbFetchArray($qs);
			$id_move 	= isMoved($rs['id_product_attribute'], $rs['id_zone']); /// ถ้ามีการย้ายอยู่แล้วสินค้ากับโซนตรงกัน จะได้ id_move กลับมา 
			if($rs['qty'] == $val)
			{
				$qa = dbQuery("DELETE FROM tbl_cancle WHERE id_cancle = ".$id);
			}
			else
			{
				$qa = dbQuery("UPDATE tbl_cancle SET qty = (qty - ".$val.") WHERE id_cancle = ".$id);
			}
			if($id_move)
			{
				$qb = dbQuery("UPDATE tbl_move SET qty_move = (qty_move + ".$val.") WHERE id_move = ".$id_move);
			}
			else
			{	
				$qb = dbQuery("INSERT INTO tbl_move (id_product_attribute, qty_move, id_employee, id_warehouse, id_zone) VALUES (".$rs['id_product_attribute'].", ".$val.", ".$id_employee.", ".$rs['id_warehouse'].", ".$rs['id_zone'].")");
			}		
			if( $qa && $qb )
			{
				commitTransection();
			}
			else
			{
				dbRollback();
			}
		}
	}
	
	header("location: ../index.php?content=ProductMove&view_cancle");
}


/// ย้ายสินค้าออกจากโซนเดิม มาเข้า MOVE Zone
if( isset( $_GET['move_out'] ) && isset( $_POST['barcode'] ) )
{
	$barcode 	= trim($_POST['barcode']);
	$id_zone		= $_POST['id_zone'];
	$id_wh		= get_warehouse_by_zone($id_zone);
	$product		= new product();
	$ar 			= $product->check_barcode($barcode);	
	$id 			= $ar['id_product_attribute'];
	$qty 			= $ar['qty'] == 0 ? $_POST['qty'] : $ar['qty'] * $_POST['qty'];
	if( $id )
	{
		$qs = dbQuery("SELECT id_stock, qty FROM tbl_stock WHERE id_product_attribute = ".$id." AND id_zone = ".$id_zone);
		if( dbNumRows($qs) > 0 )
		{
			list($id_stock, $qtyx) = dbFetchArray($qs);
			if($qty <= $qtyx)
			{
				startTransection();
				$qa 			= dbQuery("UPDATE tbl_stock SET qty = (qty - ".$qty.") WHERE id_stock = ".$id_stock);
				$id_move 	= isMoved($id, $id_zone); /// ถ้ามีการย้ายอยู่แล้วสินค้ากับโซนตรงกัน จะได้ id_move กลับมา 
				if($id_move)
				{
					$qb = dbQuery("UPDATE tbl_move SET qty_move = (qty_move + ".$qty.") WHERE id_move = ".$id_move);
				}
				else
				{
					$qb = dbQuery("INSERT INTO tbl_move (id_product_attribute, qty_move, id_employee, id_warehouse, id_zone) VALUES (".$id.", ".$qty.", ".$id_employee.", ".$id_wh.", ".$id_zone.")");	
				}						
				if( $qa && $qb )
				{
					commitTransection();
					echo $id." | ".$qty;
				}
				else
				{
					dbRollback();
					echo "move_error";	
				}
			}
			else
			{
				echo "qty_greater_than_stock";	
			}
		}
		else
		{
			echo "not_in_zone";
		}
	}
	else
	{
		echo "no_product";
	}
}


/// ย้ายสินค้าออกจาก MOVE Zone เข้าโซนปกติ
if( isset( $_GET['move_in'] ) && isset( $_POST['id_move'] ) && isset( $_POST['id_zone'] ) )
{
	$id_zone		= $_POST['id_zone'];
	$id_move 	= $_POST['id_move'];
	$qty			= $_POST['qty'];
	$qs = dbQuery("SELECT * FROM tbl_move WHERE id_move = ".$id_move);
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$dest = get_warehouse_by_zone($id_zone);
		if( $dest == $rs['id_warehouse'] )
		{
			startTransection();
			$qa = update_stock_zone($qty, $id_zone, $rs['id_product_attribute']);
			if( $rs['qty_move'] == $qty )
			{
				$qb = dbQuery("DELETE FROM tbl_move WHERE id_move = ".$id_move);	
			}
			else
			{
				$qb = dbQuery("UPDATE tbl_move SET qty_move = (qty_move - ".$qty.") WHERE id_move = ".$id_move);	
			}
			if($qa && $qb )
			{
				commitTransection();
				echo "success";	
			}
			else
			{
				dbRollback();
				echo "fail";	
			}
		}
		else
		{
			echo "warehouse_missmatch";
		}
	}
	else
	{
		echo "error";	
	}	
}

if( isset( $_GET['get_items'] ) && isset( $_POST['id_zone'] ) )
{
	$id_zone 	= $_POST['id_zone'];	
	$data 		= array();
	$arr	 		= array("zone" => get_zone($id_zone));
	array_push($data, $arr);
	$qs 	= dbQuery("SELECT tbl_stock.id_product_attribute, barcode, reference, qty FROM tbl_stock JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute WHERE tbl_stock.id_zone = ".$id_zone);
	if( dbNumRows($qs) > 0 )
	{
		$no = 1;
		while($rs = dbFetchArray($qs))
		{
			$arr 	= array("no" => $no, "id" => $rs['id_product_attribute'], "barcode" => $rs['barcode'], "product" => $rs['reference'], "qty" => number_format($rs['qty']) );
			array_push($data, $arr);
			$no++;	
		}
	}
	else
	{
		$arr = array("noproduct" => "noproduct");
		array_push($data, $arr);
	}
	echo json_encode($data);
}
	
if( isset( $_GET['get_zone'] ) && isset( $_POST['barcode'] ) )
{
	$barcode	= $_POST['barcode'];
	$rs 			= "fail";
	$qs 			= dbQuery("SELECT id_zone FROM tbl_zone WHERE barcode_zone = '".$barcode."'");
	if( dbNumRows($qs) == 1 )
	{
		list($rs) 	= dbFetchArray($qs);
	}
	echo $rs;
}
	
?>