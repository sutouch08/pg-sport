<?php
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
include "../function/product_helper.php";
include "../function/transfer_helper.php";

if( isset( $_GET['getZone'] ) )
{
	$txt = $_GET['txt'];
	$id_wh = $_GET['id_warehouse'];
	$qs = dbQuery("SELECT * FROM tbl_zone WHERE id_warehouse = ".$id_wh." AND barcode_zone = '".$txt."'");
	if(dbNumRows($qs) == 1 )
	{
		$rs = dbFetchObject($qs);
		$sc = json_encode(array("id_zone" => $rs->id_zone, "zone_name" => $rs->zone_name));
	}
	else
	{
		$sc = "ไม่พบโซน";
	}
	echo $sc; 
		
}

if( isset( $_GET['deleteTranfer'] ) )
{
	$sc = 'success';
	$id_tranfer = $_POST['id_tranfer'];
	$cs 			= new transfer();
	$hasDetail	= $cs->hasDetail($id_tranfer);
	if( $hasDetail === TRUE )
	{
		$sc = 'ไม่สามารถลบได้เนื่องจากเอกสารไม่ว่างเปล่า';
		
	}
	else
	{
		if( $cs->delete($id_tranfer) === FALSE )
		{
			$sc = 'ลบรายการไม่สำเร็จ';
		}
	}
	
	echo $sc;
}

if( isset( $_GET['deleteTranferDetail'] ) )
{
	$sc = 'success';
	$result = TRUE;
	$id_tranfer_detail	= $_POST['id_tranfer_detail'];
	//----- ดึงรายการที่จะลบมาตรวจสอบก่อน
	$qs = dbQuery("SELECT * FROM tbl_tranfer_detail WHERE id_tranfer_detail = ".$id_tranfer_detail);
	if( dbNumRows($qs) == 1 )
	{
		startTransection();
		$rs = dbFetchObject($qs);
		if( $rs->valid == 1 OR $rs->id_zone_to != 0 )
		{
			$cs = new transfer($rs->id_tranfer);
			//------ ตรวจสอบยอดคงเหลือในโซนก่อนว่าพอที่จะย้ายกลับมั้ย
			$isEnough = isEnough($rs->id_zone_to, $rs->id_product_attribute, $rs->tranfer_qty);
			
			//----- ถ้าพอย้าย ดำเนินการย้าย
			if( $isEnough === TRUE )
			{
				//----- update_stock_zone ตัดยอดออกจากโซนปลายทาง
				$ra = update_stock_zone(($rs->tranfer_qty * -1), $rs->id_zone_to, $rs->id_product_attribute);
				if( $ra === FALSE ){ $sc = 'update stock fail for desination zone'; }
				
				//----- update stock_movement เอารายการที่ย้ายเข้า มาโซนปลายทาง ออก
				$rb = stock_movement('in', 1, $rs->id_product_attribute, get_warehouse_by_zone($rs->id_zone_to), ($rs->tranfer_qty * -1), $cs->reference, $cs->date_add, $rs->id_zone_to);
				if( $rb === FALSE ){ $sc = 'update stock movement fail for desination zone'; }
				
				//------ update stock zone คืนยอดให้โซนต้นทาง
				$rc = update_stock_zone($rs->tranfer_qty, $rs->id_zone_from, $rs->id_product_attribute);
				if( $rc === FALSE ){ $sc = 'update stock fail for source zone'; }
				
				//------ update stock_movement เอารายการที่ย้ายออกจากโซนต้นทาง ออก
				$rd = stock_movement('out', 2, $rs->id_product_attribute, get_warehouse_by_zone( $rs->id_zone_from), ($rs->tranfer_qty * -1 ), $cs->reference, $cs->date_add, $rs->id_zone_from);
				if( $rd === FALSE ){ $sc = 'update stock movement fail for source zone'; }
				
				//------- delete tranfer detail
				$re = $cs->deleteDetail($rs->id_tranfer_detail);
				if( $re === FALSE ){ $sc = 'delete transfer detail fail'; }
				
				if( $ra === FALSE || $rb === FALSE || $rc === FALSE || $rd === FALSE || $re === FALSE )
				{
					$result = FALSE;
					$sc = 'ทำรายการไม่สำเร็จ';
				}				
				
			}
			else
			{
				$result = FALSE;
				$sc = 'ยอดคงเหลือในโซนไม่พอให้ย้ายกลับ';
			}	
		}
		else /////---- if valid
		{
			//------- move stock in temp to original zone 
			//-------  get stock in temp
			$qr = dbQuery("SELECT * FROM tbl_tranfer_temp WHERE id_tranfer_detail = ".$id_tranfer_detail);
			if( dbNumRows($qr) == 1 )
			{
				$res = dbFetchObject($qr);
				$cs = new transfer();
				//------- move stock in to original zone
				$ra = update_stock_zone($res->qty, $res->id_zone, $res->id_product_attribute);
				if( $ra === FALSE ){ $sc = 'update stock fail'; }
				
				//----- delete tranfer temp
				$rb = $cs->deleteTransferTemp($res->id_tranfer_detail);
				if( $rb === FALSE ){ $sc = 'delete temp fail'; }
				
				//---- delete tranfer detail
				$rc = $cs->deleteDetail($res->id_tranfer_detail);
				if( $rc === FALSE ){ $sc = 'delete detail fail'; }
				
				if( $ra === FALSE || $rb === FALSE || $rc === FALSE )
				{
					$result = FALSE;
				}
				
			}//--- end if temp dbNumRows
			
		}// -- end if valid
		
		//---- delete stock movement where contain 0 move_in and 0 move_out
		drop_zero_movement();
		
		if( $result === TRUE )
		{
			commitTransection();
		}
		else if( $result === FALSE )
		{
			dbRollback();
		}
		endTransection();
	}
	else
	{
		$sc = 'ไม่พบโซนปลายทาง';	
	}//--- end if dbNumRows
	
	echo $sc;
}



if( isset( $_GET['moveAllToZone'] ) )
{
	$sc = TRUE;
	$id_tranfer	= $_GET['id_tranfer'];
	$id_zone_to	= $_GET['id_zone_to'];
	$cs 			= new transfer($id_tranfer);
	
	$qs = dbQuery("SELECT * FROM tbl_tranfer_temp WHERE id_tranfer = ".$id_tranfer);
	if( dbNumRows($qs) > 0 )
	{
		startTransection();
		while( $rs = dbFetchObject($qs) )
		{
			//---- move to zone
			$ra = update_stock_zone($rs->qty, $id_zone_to, $rs->id_product_attribute);
			//---- if move success
			if( $ra === TRUE )
			{
				//------ Insert stock_movement
				$rb = stock_movement('out', 2, $rs->id_product_attribute, get_warehouse_by_zone($rs->id_zone), $rs->qty, $cs->reference, $cs->date_add, $rs->id_zone);
				$rc = stock_movement('in', 1, $rs->id_product_attribute, get_warehouse_by_zone($id_zone_to), $rs->qty, $cs->reference, $cs->date_add, $id_zone_to);
				
				//------ if success remove temp
				if( $rb === TRUE && $rc === TRUE )
				{
					$rd = dbQuery("DELETE FROM tbl_tranfer_temp WHERE id_tranfer_detail = ".$rs->id_tranfer_detail);
					//---- if remove temp successful  do update tranfer_detail field
					if( $rd === TRUE )
					{
						//-----  Update desination zone and valid
						$re = dbQuery("UPDATE tbl_tranfer_detail SET id_zone_to = ".$id_zone_to.", valid = 1 WHERE id_tranfer_detail = ".$rs->id_tranfer_detail);
						if( $re === FALSE )
						{
							$sc = FALSE;
						}
					}
					else
					{
						$sc  = FALSE;
					}
				}
				else
				{
					$sc = FALSE;
				}
				
			}
			else
			{
				$sc = FALSE;
			}
			
			
			if( $sc === TRUE )
			{
				commitTransection();	
			}
			else
			{
				dbRollback();
			}
			
			
		}//---- end while
		
		endTransection();	
		
		
	}//--- end if dbNumRows
	else
	{
		$sc = FALSE;	
	}
	
	echo $sc === TRUE ? 'success' : 'ย้ายสินค้าเข้าโซนไม่สำเร็จ';
}

if( isset( $_GET['moveToZone'] ) )
{
	$sc = TRUE;
	$id_tranfer_detail 	= $_GET['id_tranfer_detail'];
	$id_tranfer 			= $_GET['id_tranfer'];
	$id_zone_to			= $_GET['id_zone_to'];
	$cs  					= new transfer($id_tranfer);
	$qs = dbQuery("SELECT * FROM tbl_tranfer_temp WHERE id_tranfer_detail = ".$id_tranfer_detail);
	if( dbNumRows($qs) == 1 )
	{
	
		$rs = dbFetchObject($qs);
		startTransection();
		//---- move to zone
		$ra = update_stock_zone($rs->qty, $id_zone_to, $rs->id_product_attribute);
		//---- if move success
		if( $ra === TRUE )
		{
			//------ Insert stock_movement
			$rb = stock_movement('out', 2, $rs->id_product_attribute, get_warehouse_by_zone($rs->id_zone), $rs->qty, $cs->reference, $cs->date_add, $rs->id_zone);
			$rc = stock_movement('in', 1, $rs->id_product_attribute, get_warehouse_by_zone($id_zone_to), $rs->qty, $cs->reference, $cs->date_add, $id_zone_to);
			
			//------ if success remove temp
			if( $rb === TRUE && $rc === TRUE )
			{
				$rd = dbQuery("DELETE FROM tbl_tranfer_temp WHERE id_tranfer_detail = ".$id_tranfer_detail);
				//---- if remove temp successful  do update tranfer_detail field
				if( $rd === TRUE )
				{
					//-----  Update desination zone and valid
					$re = dbQuery("UPDATE tbl_tranfer_detail SET id_zone_to = ".$id_zone_to.", valid = 1 WHERE id_tranfer_detail = ".$id_tranfer_detail);
					if( $re === FALSE )
					{
						$sc = FALSE;
					}
				}
				else
				{
					$sc  = FALSE;
				}
			}
			else
			{
				$sc = FALSE;
			}
			
		}
		else
		{
			$sc = FALSE;
		}
		
		
		if( $sc === TRUE )
		{
			commitTransection();	
		}
		else
		{
			dbRollback();
		}
		endTransection();		
		
		
	}
	else
	{
		$sc = FALSE;	
	}
	
	echo $sc === TRUE ? 'success' : 'ย้ายสินค้าเข้าโซนไม่สำเร็จ';
}





if( isset( $_GET['moveBarcodeToZone'] ) )
{
	$sc = TRUE;
	$id_tranfer_detail 	= $_POST['id_tranfer_detail'];
	$id_tranfer 			= $_POST['id_tranfer'];
	$id_zone_to			= $_POST['id_zone_to'];
	$qty					= $_POST['qty'];
	$barcode			= $_POST['barcode'];
	$id_pa			 	= get_id_product_attribute_by_barcode($barcode);
	if( $id_pa != 0 )
	{
		$cs  		= new transfer($id_tranfer);
		$qs = dbQuery("SELECT * FROM tbl_tranfer_temp WHERE id_tranfer_detail = ".$id_tranfer_detail." AND id_product_attribute = ".$id_pa);
		if( dbNumRows($qs) == 1 )
		{
		
			$rs = dbFetchObject($qs);
			if( $rs->qty >= $qty)
			{
				startTransection();
				//---- move to zone
				$ra = update_stock_zone($qty, $id_zone_to, $rs->id_product_attribute);
				
				//------ Insert stock_movement
				$rb = stock_movement('out', 2, $rs->id_product_attribute, get_warehouse_by_zone($rs->id_zone), $qty, $cs->reference, $cs->date_add, $rs->id_zone);
				$rc = stock_movement('in', 1, $rs->id_product_attribute, get_warehouse_by_zone($id_zone_to), $qty, $cs->reference, $cs->date_add, $id_zone_to);
				
				//------ update temp
				$rd = updateTransferTemp($id_tranfer_detail, ($qty * -1) );
				
				//-----  Update desination zone and valid
				$re = validTransferDetail($id_tranfer_detail, $id_zone_to);
					
				if( $ra === FALSE || $rb === FALSE || $rc === FALSE || $rd === FALSE || $re === FALSE )
				{
					$sc = FALSE;
				}
			}
			else
			{
				$sc = FALSE;
			}///---- if $rs->qty >= $qty
			
			
			if( $sc === TRUE )
			{
				commitTransection();	
			}
			else
			{
				dbRollback();
			}
			
			endTransection();		
			
			
		}
		else//--- endif dbNumRows == 1
		{
			$sc = FALSE;	
		}//--- endif dbNumRows == 1
		
	}
	else
	{
		$sc = FALSE;	
	}//-- end fi id_pa
	
	echo $sc === TRUE ? 'success' : 'ย้ายสินค้าเข้าโซนไม่สำเร็จ';
}


if( isset( $_GET['getTempTable'] ) )
{
	$id 	= $_GET['id_tranfer'];
	$ds 	= array();	
	$qs 	= dbQuery("SELECT * FROM tbl_tranfer_temp WHERE id_tranfer = ".$id);
	if( dbNumRows($qs) > 0 )
	{
		$no = 1;
		while($rs = dbFetchObject($qs) )
		{
			$barcode = get_barcode($rs->id_product_attribute);
			$pReference = get_product_reference($rs->id_product_attribute);
			$arr = array(
						"no"		=> $no,
						"id"			=> $rs->id_tranfer_detail,
						"barcode"	=> $barcode,
						"products"		=> $pReference,
						'id_zone_from'	=> $rs->id_zone,
						'fromZone'	=> get_zone($rs->id_zone),
						"qty"			=> $rs->qty
						);
			array_push($ds, $arr);
			$no++;									
		}
	}
	else
	{
		array_push($ds, array("nodata" => "nodata"));
	}
	echo json_encode($ds);
}



if( isset( $_GET['getTransferTable'] ) )
{
	$id			= $_GET['id_tranfer'];
	$canAdd	= $_GET['canAdd'];
	$canEdit	= $_GET['canEdit'];
	$ds = array();
	$cs = new transfer();
	$qs = $cs->getMoveList($id);
	if( dbNumRows($qs) > 0 )
	{
		$no = 1;
		while( $rs = dbFetchObject($qs) )
		{
			$pReference = get_product_reference($rs->id_product_attribute);
			$toZone	= $rs->id_zone_to == 0 ? '<button type="button" class="btn btn-xs btn-primary" onclick="move_in('.$rs->id_tranfer_detail.', '.$rs->id_zone_from.')">ย้ายเข้าโซน</button>' : get_zone($rs->id_zone_to);
			$btn_delete = ($canAdd == 1 OR $canEdit == 1 ) ? '<button type="button" class="btn btn-xs btn-danger" onclick="deleteMoveItem(' . $rs->id_tranfer_detail .' , \'' . $pReference.'\')"><i class="fa fa-trash"></i></button>' : '';
			$barcode = get_barcode($rs->id_product_attribute);
			$arr = array(
						'no'			=> $no,
						'id'				=> $rs->id_tranfer_detail,
						'barcode'	=> $barcode,
						'products'	=> $pReference,
						'id_zone_from'	=> $rs->id_zone_from,
						'fromZone'	=> get_zone($rs->id_zone_from),
						'toZone'		=> $toZone,
						'qty'			=> number_format($rs->tranfer_qty),
						'btn_delete'	=> $btn_delete,
						'valid'			=> ($rs->valid == 0 ? '<input type="hidden" id="qty-'.$barcode.'" value="'.$rs->tranfer_qty.'" />' : '')
						);
			array_push($ds, $arr);	
			$no++;					
		}
	}
	else
	{
		array_push($ds, array('nodata' => 'nodata'));	
	}
	echo json_encode($ds);
}



if( isset( $_GET['addToTransfer'] ) )
{
	$sc = TRUE;
	$id_tranfer 	= $_GET['id_tranfer'];
	$id_zone		= $_GET['id_zone'];
	$moveQty 	= $_POST['moveQty'];
	$pd			= $_POST['id_pa'];
	$udz			= isset( $_POST['underZero'] ) ? $_POST['underZero'] : array();
	$cs = new transfer();
	foreach( $moveQty as $name => $val)
	{
		startTransection();
		if( $val != '' && $val != 0 )
		{
			$id_pa	= $pd[$name];
			$qty		= $val;
			$arr = array( 
							"id_tranfer" => $id_tranfer,
							"id_product_attribute"	=> $id_pa,
							"id_zone_from"	=> $id_zone,
							"id_zone_to"		=> 0,
							"tranfer_qty"		=> $qty
							);	
			$rs = $cs->isExistsDetail($arr);
			if( $rs !== FALSE )
			{
				//----- if exists detail update 
				$id = $cs->updateDetail($rs, $arr);
				
			}
			else
			{
				//---- if not exists insert new row
				$id = $cs->addDetail($arr);
				
			}
			
			if( $id === FALSE )
			{
				//----- If insert or update tranfer detail fail
				$sc = FALSE;
			}
			else
			{
				//----- If insert or update tranfer detail successful  do insert or update tranfer temp
				$temp = array(
									"id_tranfer_detail"	=> $id,
									"id_tranfer"			=> $id_tranfer,
									"id_product_attribute"	=> $id_pa,
									"id_zone"		=> $id_zone,
									"qty"	=> $qty,
									"id_employee"	=> getCookie('user_id')
									);
				$ra = $rs == FALSE ? $cs->addTransferTemp($temp) : $cs->updateTransferTemp($temp);	
				if( $ra === TRUE )
				{
					//---- if insert or update tranfer temp success do update stock in zone
					$rd = $cs->updateStock($id_zone, $id_pa, ($qty * -1));
					if( $rd === FALSE )
					{
						//--- if update stock fail
						$sc = FALSE;
					}
				}
				else
				{
					//---- if insert or update tranfer temp fail
					$sc = FALSE;	
				}
			}
		}
	}
	
	if( $sc === TRUE )
	{
		commitTransection();
	}
	else
	{
		dbRollback();	
	}
	endTransection();
	
	echo $sc === TRUE ? 'success' : 'fail';
}






//------------ เพิ่มรายการโอนด้วยบาร์โค้ด
if( isset( $_GET['addBarcodeToTransfer'] ) )
{
	$sc = TRUE;
	$id_tranfer 	= $_POST['id_tranfer'];
	$id_zone		= $_POST['id_zone_from'];
	$qty		 	= $_POST['qty'];
	$barcode	= $_POST['barcode'];
	$udz			= $_POST['underZero'];
	$cs = new transfer();
	startTransection();

	$id_pa	= get_id_product_attribute_by_barcode($barcode);
	$arr = array( 
					"id_tranfer" => $id_tranfer,
					"id_product_attribute"	=> $id_pa,
					"id_zone_from"	=> $id_zone,
					"id_zone_to"		=> 0,
					"tranfer_qty"		=> $qty
					);	
	$rs = $cs->isExistsDetail($arr);
	if( $rs !== FALSE )
	{
		//----- if exists detail update 
		$id = $cs->updateDetail($rs, $arr);
		
	}
	else
	{
		//---- if not exists insert new row
		$id = $cs->addDetail($arr);
		
	}
		
	if( $id === FALSE )
	{
		//----- If insert or update tranfer detail fail
		$sc = FALSE;
	}
	else
	{
		//----- If insert or update tranfer detail successful  do insert or update tranfer temp
		$temp = array(
							"id_tranfer_detail"	=> $id,
							"id_tranfer"			=> $id_tranfer,
							"id_product_attribute"	=> $id_pa,
							"id_zone"		=> $id_zone,
							"qty"	=> $qty,
							"id_employee"	=> getCookie('user_id')
							);
		$ra = $rs == FALSE ? $cs->addTransferTemp($temp) : $cs->updateTransferTemp($temp);	
		if( $ra === TRUE )
		{
			//---- if insert or update tranfer temp success do update stock in zone
			$rd = $cs->updateStock($id_zone, $id_pa, ($qty * -1));
			if( $rd === FALSE )
			{
				//--- if update stock fail
				$sc = FALSE;
			}
		}
		else
		{
			//---- if insert or update tranfer temp fail
			$sc = FALSE;	
		}
	}
	
	if( $sc === TRUE )
	{
		commitTransection();
	}
	else
	{
		dbRollback();	
	}
	endTransection();

echo $sc === TRUE ? 'success' : 'fail';
}




//--------- เพิ่มสินค้าทั้งหมดในโซนเข้าเอกสาร แล้ว ย้ายสินค้าทั้งหมดในโซนเข้า temp
if( isset( $_GET['addAllToTransfer'] ) )
{
	$sc = TRUE;
	$id_tranfer 	= $_GET['id_tranfer'];
	$id_zone		= $_GET['id_zone'];
	$udz			= $_GET['allowUnderZero'];
	$cs = new transfer();
	
	//------  ดึงสินค้าทั้งหมดในโซน
	$qs = dbQuery("SELECT * FROM tbl_stock WHERE id_zone = ".$id_zone);
	
	if( dbNumRows($qs) > 0 )
	{
		startTransection();
		while( $rs = dbFetchObject($qs) )
		{
			if( $rs->qty != 0 && ( $rs->qty > 0 OR $udz == 1 ) )
			{
				$arr = array(
							"id_tranfer"				=> $id_tranfer,
							"id_product_attribute"	=> $rs->id_product_attribute,
							"id_zone_from"			=> $rs->id_zone,
							"id_zone_to"				=> 0,
							"tranfer_qty"				=> $rs->qty
							);
				//---- check is tranfer_detail exists or not
				$ra = $cs->isExistsDetail($arr);
				if( $ra !== FALSE )
				{
					//----- if exists detail update 
					$id = $cs->updateDetail($ra, $arr);	
				}
				else
				{
					//---- if not exists insert new row
					$id = $cs->addDetail($arr);
				}
				
				if( $id === FALSE )
				{
					//----- If insert or update tranfer detail fail
					$sc = FALSE;
				}
				else
				{
					//----- If insert or update tranfer detail successful  do insert or update tranfer temp
					$temp = array(
										"id_tranfer_detail"	=> $id,
										"id_tranfer"			=> $id_tranfer,
										"id_product_attribute"	=> $rs->id_product_attribute,
										"id_zone"		=> $id_zone,
										"qty"	=> $rs->qty,
										"id_employee"	=> getCookie('user_id')
										);
					$rb = $ra == FALSE ? $cs->addTransferTemp($temp) : $cs->updateTransferTemp($temp);	
					if( $rb === TRUE )
					{
						//---- if insert or update tranfer temp success do update stock in zone
						$rd = $cs->updateStock($id_zone, $rs->id_product_attribute, ($rs->qty * -1));
						if( $rd === FALSE )
						{
							//--- if update stock fail
							$sc = FALSE;
						}
					}
					else
					{
						//---- if insert or update tranfer temp fail
						$sc = FALSE;	
					}//---- end if $rb === TRUE
				}//--- end if $id === FALSE
			}//---- end if qty != 0
		}//--- endwhile
	}//--- end if dbNumRows
	
	if( $sc === TRUE )
	{
		commitTransection();
	}
	else
	{
		dbRollback();	
	}
	
	endTransection();
	
	//------ Delete stock zone where qty = 0
	$cs->clearStockZeroZone($id_zone);
	
	echo $sc === TRUE ? 'success' : 'fail';
	
}




//----- Add new transfer document
if( isset( $_GET['addNew'] ) )
{
	$cs = new transfer();
	$date	= dbDate($_POST['date_add'], TRUE);
	$arr = array(
				'reference'			=> $cs->getNewReference($date),
				'warehouse_from'	=> $_POST['fromWH'],
				'warehouse_to'		=> $_POST['toWH'],
				'id_employee'		=> getCookie('user_id'),
				'date_add'			=> $date,
				'comment'			=> $_POST['remark']
				);
	$id = $cs->add($arr);		
	if( $id !== FALSE )
	{
		$ds = json_encode(array("id" => $id));
	}
	else
	{
		$ds = "เพิ่มรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้งภายหลัง";
	}
	echo $ds;				
}

//------- Update document header
if( isset( $_GET['updateHeader'] ) )
{
	$sc = 'success';
	$id_tranfer	= $_POST['id_tranfer'];
	$date			= dbDate($_POST['date_add'], TRUE);
	$cs 			= new transfer();
	$arr = array(
				'warehouse_from'	=> $_POST['fromWH'],
				'warehouse_to'		=> $_POST['toWH'],
				'id_employee'		=> getCookie('user_id'),
				'date_add'			=> $date,
				'comment'			=> $_POST['remark']
				);
	$rs = $cs->update($id_tranfer, $arr);
	if( $rs === FALSE )
	{
		$sc = $cs->error;
	}
	echo $sc;
}


if( isset( $_GET['getProductInZone'] ) )
{
	$sc = array();
	$id_zone = $_GET['id_zone'];
	$qr = "SELECT s.id_stock, s.id_product_attribute, p.barcode, p.reference, s.qty ";
	$qr .= "FROM tbl_stock AS s ";
	$qr .= "JOIN tbl_product_attribute AS p ";
	$qr .= "USING(id_product_attribute) ";
	$qr .= "WHERE s.id_zone = ".$id_zone." AND qty != 0";
	$qs = dbQuery($qr);
	if( dbNumRows($qs) > 0 )
	{
		$no = 1;
		while( $rs = dbFetchObject($qs) )
		{
			$arr = array( 
						"no"			=> $no,
						"id_stock" 	=> $rs->id_stock, 
						"id_pa"		=> $rs->id_product_attribute,
						"barcode" 	=> $rs->barcode, 
						"products" 	=> $rs->reference, 
						"qty" 			=> $rs->qty
						);
			array_push($sc, $arr);
			$no++;
		}
	}
	else
	{
		array_push($sc, array("nodata" => "nodata"));	
	}
	echo json_encode($sc);
}




if( isset( $_GET['clearFilter'] ) )
{
	deleteCookie('sCode');
	deleteCookie('sEmp');
	deleteCookie('fromDate');
	deleteCookie('toDate');
	deleteCookie('sStatus');
	echo 'success';	
}
?>