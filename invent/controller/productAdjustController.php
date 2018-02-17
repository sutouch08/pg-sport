<?php 
require "../../library/config.php";
require "../../library/functions.php";
require "../function/tools.php";
require '../function/stock_helper.php';
require '../function/adjust_helper.php';
$id_employee = $_COOKIE['user_id'];
//----------------- NEW CODE -------------------//


//-------- Unsave adjust
if( isset( $_GET['unsaveAdjust'] ) )
{
	$id_adj	= $_POST['id_adjust'];
	$adj 		= new adjust($id_adj);	
	$qs = $adj->getAllDetail($id_adj);
	$err = 0;
	
	if( dbNumRows($qs) > 0 )
	{
		startTransection();
		while( $rs = dbFetchObject($qs) )
		{
			if( $rs->status_up == 1 )
			{
				$reference = $adj->adjust_no;
				$incQty 	= $rs->adjust_qty_add;
				$decQty	= $rs->adjust_qty_minus;
				$id_pa	= $rs->id_product_attribute;
				$id_zone = $rs->id_zone;
				$qty 		= ($incQty - $decQty) * -1;  //--- กลับยอดเพิ่มเป็นลด ลดเป็นเพิ่ม
				$stock 	= stockInZone($id_pa, $id_zone);
				$allowUnderZero	= allow_under_zero();
				//---- ตรวจสอบ 
				if( ( $stock + $qty ) < 0 && ! $allowUnderZero )
				{
					$err++;
				}
				else
				{
					//---- 1. เปลี่ยนสถานะ
					$ra = $adj->setDetailStatus($rs->id_adjust_detail, 0);
					
					//---- 2. เปลี่ยนสถานะ ยอดต่าง
					$rb = $rs->status_adjust != 0 ? $adj->setDiff($rs->status_adjust, 1) : TRUE;
					//---- 3. เพิ่ม/ลด ยอดสต็อกในโซน
					$rc = update_stock_zone($qty, $id_zone, $id_pa);
					//---- 4. ลบ movement
					$rd = delete_movement($reference, $id_pa, $id_zone);
					if( $ra === TRUE && $rb === TRUE && $rc === TRUE && $rd === TRUE )
					{
						commitTransection();
					}
					else
					{
						$err++;
						dbRollback();	
					}
				}
			}
		} //--- end while
		endTransection();
	}
	$adj->setStatus($id_adj, 0);
	$sc = $err == 0 ? 'success' : 'ยกเลิกไม่สำเร็จ '.$err.' รายการ';
	echo $sc;
}


//-------  Save adjust 
if( isset( $_POST['saveAdjust'] ) )
{
	$id_adj 	= $_POST['id_adjust'];	
	$adj 		= new adjust($id_adj);
	$qs 		= $adj->getAllDetail($id_adj);
	$err		= 0;
	
	if( dbNumRows($qs) > 0 )
	{
		startTransection();
		while( $rs = dbFetchObject($qs) )
		{
			$rd 	= TRUE;  //---- ไว้ตรวจสอบ
			if( $rs->status_up == 0 )
			{
				$qty = $rs->adjust_qty_add - $rs->adjust_qty_minus;
				$movement_qty = $qty > 0 ? $rs->adjust_qty_add : $rs->adjust_qty_minus;
				$type = $qty > 0 ? 'in' : 'out';
				$reason = $qty > 0 ? 7 : 8 ;
				$id_wh 	= get_warehouse_by_zone($rs->id_zone);
				//--- 1. update stock
				$ra = update_stock_zone($qty, $rs->id_zone, $rs->id_product_attribute);
				
				//--- 2. stock_movement
				$rb = stock_movement($type, $reason, $rs->id_product_attribute, $id_wh, $movement_qty, $adj->adjust_no, dbDate($adj->adjust_date, TRUE), $rs->id_zone);
				
				//--- 3. change status detail 
				$rc = $adj->setDetailStatus($rs->id_adjust_detail, 1);
				
				//--- 4. change diff 
				if( $rs->status_adjust != 0 )
				{
					$rd = $adj->setDiff($rs->status_adjust, 2) ? TRUE : FALSE;
				}			
				
				if( $ra && $rb && $rc && $rd )
				{
					commitTransection();	
				}
				else
				{
					dbRollback();	
					$err++;
				}	
			}
		}	
		endTransection();
	}
	
	if( $err == 0 )
	{ 
		$adj->setStatus($id_adj, 1);
	}
	$sc = $err > 0 ? 'ปรับยอดไม่สำเร็จ '. $err.' รายการ' : 'success';
	echo $sc;
}

//---------------- send detail table 
if( isset( $_GET['getAdjustTable'] ) )
{
	$sc = 'fail';
	$id_adj = $_GET['id_adjust'];
	$qs = dbQuery("SELECT * FROM tbl_adjust_detail WHERE id_adjust = ".$id_adj);	
	if( dbNumRows($qs) > 0 )
	{
		$no = 1;
		$ds = array();
		while( $rs = dbFetchObject($qs) )
		{
			$arr = array(
						'no'			=> $no,
						'id'				=> $rs->id_adjust_detail,
						'barcode'	=> get_barcode($rs->id_product_attribute),
						'product'		=> get_product_reference($rs->id_product_attribute),
						'id_pa'		=> $rs->id_product_attribute,
						'zone'			=> get_zone($rs->id_zone),
						'id_zone'		=> $rs->id_zone,
						'upQty'		=> $rs->adjust_qty_add,
						'downQty'		=> $rs->adjust_qty_minus,
						'edit'			=> $rs->status_up == 0 ? 1 : 0,
						);
			array_push($ds, $arr);
			$no++;					
		}
		$sc = json_encode($ds);
	}
	else
	{
		$sc = 'nodata';
	}
	echo $sc;
}


if( isset( $_GET['addNewAdjust'] ) )
{
	$sc		= 'fail';
	$date		= dbDate($_POST['date']);
	$adj		= new adjust();
	$adj_no 	= $adj->getNewReference($date);
	$ds 		= array(
						'adjust_no'				=> $adj_no,
						'adjust_reference'		=> $_POST['adj_ref'],
						'adjust_date'			=> $date,
						'adjust_note'				=> $_POST['remark'],
						'id_employee'			=> $_POST['id_employee'],
						'adjust_status'			=> 0
						);
	$rs 	= $adj->add($ds);
	if( $rs !== FALSE )
	{
		$sc = $rs;
	}
	echo $sc;
}


if( isset( $_GET['updateHeader'] ) )
{
	$sc = 'success';
	$id_adj = $_POST['id_adjust'];
	$adj = new adjust($id_adj);
	$ds = array(
						'adjust_reference'	=> $_POST['adjust_reference'],
						'adjust_date'		=> dbDate($_POST['date'], TRUE),
						'adjust_note'			=> $_POST['remark']
					);
	$rs = $adj->update($id_adj, $ds);		
	if( $rs )
	{
		dbQuery("UPDATE tbl_stock_movement SET date_upd = '".dbDate($_POST['date'], TRUE)."' WHERE reference = '".$adj->adjust_no."'");
	}
	else
	{
		$sc = 'fail';
	}
	
	echo $sc;
}


	//เพิ่มสินค้าที่จะปรับยอด
if( isset( $_GET['insertDetail'] ) )
{

	$sc 					= 'success';
	$id_adj				= $_POST['id_adjust'];
	$id_pa				= $_POST['id_pa'];
	$id_zone				= $_POST['id_zone'];
	$increase			= $_POST['increase'];
	$decrease			= $_POST['decrease'];
	$stock				= stockInZone($id_pa, $id_zone);
	$allowUnderZero	= allow_under_zero();
	$adj					= new adjust();
	$id_ad				= $adj->isExistsDetail($id_adj, $id_pa, $id_zone); //--- if exists will be return id_adjust_detail if not return FALSE;
	if( $id_ad !== FALSE )
	{
		$ds = $adj->getAdjustDetail($id_ad);
		$incQty	= $ds === FALSE ? 0 + $increase : $ds['adjust_qty_add'] + $increase;
		$decQty	= $ds === FALSE ? 0 + $decrease : $ds['adjust_qty_minus'] + $decrease;
		$LastQty	= $incQty - $decQty;
		if( $ds['status_up'] == 1 )
		{
			$sc = 'รายการซ้ำ ไม่อนุญาติให้ปรับยอดสินค้าซ้ำกันในโซนเดียวกันบนเอกสารเดียวกัน';
		}
		else if( ($ds['adjust_qty_add'] > 0 && $decrease > 0) OR ($ds['adjust_qty_minus'] > 0 && $increase > 0) )
		{
			$sc = 'ไม่สามารถลดยอดและเพิ่มยอดในรายการเดียวกันได้';
		}
		else if( ($stock + $LastQty) < 0 && ! $allowUnderZero)
		{
			$sc = 'ไม่อนุญาติให้สต็อกติดลบ จำนวนที่ปรับลงต้องไม่เกิน '.$stock;
		}
		else
		{
			$qs = $adj->updateDetail($id_ad, $increase, $decrease);
			if( ! $qs )
			{
				$sc = 'เพิ่มรายการไม่สำเร็จ';
			}
			else
			{
				$adj->setStatus($id_adj, 0); //----- Change status to unsaved	
			}
		}			
	}
	else //---- detail not exists insert new one
	{	
		$LastQty = $increase - $decrease;
		if( ($stock + $LastQty) < 0 && ! $allowUnderZero)
		{
			$sc = 'ไม่อนุญาติให้สต็อกติดลบ จำนวนที่ปรับลงต้องไม่เกิน '.$stock;	
		}
		else
		{
			$data = array(
							"id_adjust"		=> $id_adj,
							"id_product_attribute" 	=> $id_pa,
							"id_zone"			=> $id_zone,
							"adjust_qty_add"	=> $increase,
							"adjust_qty_minus"	=> $decrease
							);
			$qs = $adj->insertDetail($data);
			if( ! $qs )
			{
				$sc = 'เพิ่มรายการไม่สำเร็จ';
			}
			else
			{
				$adj->setStatus($id_adj, 0); //----- Change status to unsaved	
			}
		}
	}
	echo $sc;
}


if( isset( $_GET['loadDiff'] ) )
{
	$id_adj 	= $_POST['id_adjust'];	
	$diffs		= $_POST['diff'];
	$adjust 	= new adjust($id_adj);
	$err 		= 0;
	startTransection();
	foreach( $diffs as $id )
	{
		$ds = getDiffData($id);
		if( $ds !== FALSE )
		{
			$arr = array(
							'id_adjust'		=> $id_adj,
							'id_product_attribute'	=> $ds['id_product_attribute'],
							'id_zone'			=> $ds['id_zone'],
							'adjust_qty_add'	=> $ds['qty_add'],
							'adjust_qty_minus'	=> $ds['qty_minus'],
							'date_up'		=> date("Y-m-d H:i:s"),
							'status_adjust'	=> $ds['id_diff'],
							'status_up'	=> 0
						);	
			$rs = $adjust->insertDetail($arr);
			if( ! $rs )
			{
				 $err++; 
			}
			else
			{
				$adjust->setDiff($id, 1);	
			}
		}
	}
	if( $err == 0 )
	{
		commitTransection();
	}
	else
	{
		dbRollback();
	}
	endTransection();
	$sc = $err > 0 ? 'ไม่สามารถนำเข้ายอดต่างบางรายการได้ กรุณาชลองใหม่อีกครั้ง' : 'success';
	echo $sc;	
}

if( isset( $_GET['deleteDiff'] ) )
{
	$diffs = $_POST['diff'];
	$err 	= 0;
	$cp 	= 0;
	foreach($diffs as $id)
	{
		$qs = dbQuery("DELETE FROM tbl_diff WHERE id_diff = ".$id);
		if( ! $qs )
		{
			$err++;
		}
		else
		{
			$cp++;
		}
	}
	$sc = $err > 0 ? 'ลบบางรายการไม่สำเร็จ' : 'success';
	echo $sc;
}


if( isset( $_GET['updateDetail'] ) )
{
	
	$sc = 'success';
	$id_adjust_detail	= $_POST['id_adjust_detail'];
	$id_zone 			= $_POST['id_zone'];
	$id_pa				= $_POST['id_pa'];
	$incQty				= $_POST['increase'];
	$decQty				= $_POST['decrease'];
	$LastQty				= $incQty - $decQty;
	$stock				= stockInZone($id_pa, $id_zone);
	$allowUnderZero	= allow_under_zero();
	if( ($stock + $LastQty) < 0 && ! $allowUnderZero )
	{
		$sc = 'ไม่อนุญาติให้สต็อกติดลบ จำนวนที่ปรับลงต้องไม่เกิน '.$stock;	
	}
	else
	{
		$qs = dbQuery("UPDATE tbl_adjust_detail SET id_product_attribute = ".$id_pa.", id_zone = ".$id_zone.", adjust_qty_add = ".$incQty.", adjust_qty_minus = ".$decQty." WHERE id_adjust_detail = ".$id_adjust_detail);
		if( $qs === FALSE )
		{
			$sc = 'fail';	
		}
	}
	echo $sc;
}



//------ Delete Adjust Order
if( isset( $_GET['deleteAdjust'] ) )
{
	$id_adj = $_POST['id_adjust'];
	$adj = new adjust($id_adj);
	$qs = $adj->getAllDetail($id_adj);
	$err = 0;
	if( dbNumRows($qs) > 0 )
	{
		startTransection();
		while( $rs = dbFetchObject($qs) )
		{
			
			if( $rs->status_up == 0 ) //----- ถ้ายังไม่บันทึก
			{
				//--- ลบรายการออกได้เลย
				$ra = $adj->deleteDetail($rs->id_adjust_detail); 
				$rd = $rs->status_adjust != 0 ? $adj->setDiff($rs->status_adjust, 0) : TRUE;
				if( $ra && $rd )
				{
					commitTransection();	
				}
				else
				{
					$err++;
					dbRollback();
				}
			}
			//---- ถ้าบันทึกแล้ว 
			//-------- ต้องตรวจสอบ ว่าหากมีการลดหรือเพิ่มยอดแล้วสต็อกติดลบหรือไม่
			//----------- ถ้าตรวจสอบผ่านแล้ว  1. ลบรายการ(เปลี่ยนสถานะยอดต่างถ้ามี)   2. เพิ่ม/ลด ยอดสต็อกในโซน  3. ลบความเคลื่อนไหว
			if( $rs->status_up == 1 )
			{
				$reference = $adj->adjust_no;
				$incQty 	= $rs->adjust_qty_add;
				$decQty	= $rs->adjust_qty_minus;
				$id_pa	= $rs->id_product_attribute;
				$id_zone = $rs->id_zone;
				$qty 		= ($incQty - $decQty) * -1;  //--- กลับยอดเพิ่มเป็นลด ลดเป็นเพิ่ม
				$stock 	= stockInZone($id_pa, $id_zone);
				$allowUnderZero	= allow_under_zero();
				if( ( $stock + $qty ) < 0 && ! $allowUnderZero )
				{
					$err++;
				}
				else
				{
					//---- 1. ลบรายการ
					$ra = $adj->deleteDetail($rs->id_adjust_detail);	
					$rd = $rs->status_adjust != 0 ? $adj->setDiff($rs->status_adjust, 0) : TRUE;
					
					//---- 2. เพิ่ม/ลด ยอดสต็อกในโซน
					$rb = update_stock_zone($qty, $id_zone, $id_pa);
					$rc = delete_movement($reference, $id_pa, $id_zone);
					if( $ra && $rd && $rb && $rc )
					{
						commitTransection();
					}
					else
					{
						$err++;
						dbRollback();	
					}	
				}	
			}
		}
		endTransection();
	}
	if( $err == 0 )
	{
		dbQuery("DELETE FROM tbl_adjust WHERE id_adjust = ".$id_adj);
	}
	$sc = $err > 0 ? 'ลบรายการไม่สำเร็จบางรายการ กรุณาตรวจสอบรายการภายในเอกสาร' : 'success';
	echo $sc;
}

//-----  Delete Adjust Detail	
if( isset( $_POST['deleteDetail'] ) )
{
	$sc = 'success';
	$id_ad	= $_POST['id_adjust_detail'];
	$id_adj	= $_POST['id_adjust'];
	$adj 		= new adjust($id_adj);
	$ds 		= $adj->getAdjustDetail($id_ad);
	$rs		= FALSE;
	$rd 		= FALSE;
	$rc		= FALSE;
	
	//----- ถ้ายังไม่บันทึก
	if( $ds['status_up'] == 0  )
	{
		$rs = $adj->deleteDetail($id_ad);	//--- ลบรายการออกได้เลย
		if( $rs )
		{
			//--- ถ้าโหลดยอดมา เปลี่ยนสถานะยอดต่างกลับเป็น 0
			if( $ds['status_adjust'] != 0 )
			{
				$id_diff = $ds['status_adjust'];
				$rd = $adj->setDiff($id_diff, 0); //--- 0 = ยังไม่โหลด 1 = โหลดเข้าปรับยอดแล้วแต่ยังไม่ได้ปรับ, 2 = ปรับยอดแล้ว
				if( ! $rd )
				{
					$sc = 'ลบรายการเรียบร้อย แต่คืนยอดต่างไม่สำเร็จ';
				}
			}
		}
		else
		{
			$sc = 'ลบรายการไม่สำเร็จ';
		}
	}
	//---- ถ้าบันทึกแล้ว 
	//-------- ต้องตรวจสอบ ว่าหากมีการลดหรือเพิ่มยอดแล้วสต็อกติดลบหรือไม่
	//----------- ถ้าตรวจสอบผ่านแล้ว  1. ลบรายการ(เปลี่ยนสถานะยอดต่างถ้ามี)   2. เพิ่ม/ลด ยอดสต็อกในโซน  3. ลบความเคลื่อนไหว
	if( $ds['status_up'] == 1 )
	{
		$reference = $adj->adjust_no;
		$incQty 	= $ds['adjust_qty_add'];
		$decQty	= $ds['adjust_qty_minus'];
		$id_pa	= $ds['id_product_attribute'];
		$id_zone = $ds['id_zone'];
		$qty 		= ($incQty - $decQty) * -1;  //--- กลับยอดเพิ่มเป็นลด ลดเป็นเพิ่ม
		$stock 	= stockInZone($id_pa, $id_zone);
		$allowUnderZero	= allow_under_zero();
		//---- ตรวจสอบ 
		if( ( $stock + $qty ) < 0 && ! $allowUnderZero )
		{
			$sc = 'ไม่สามารถลบได้เนื่องจากสต็อกจะติดลบ';
		}
		else
		{
			startTransection();
			//---- 1. ลบรายการ
			$rs = $adj->deleteDetail($id_ad);	
			if( $rs )
			{
				//--- ถ้าโหลดยอดมา เปลี่ยนสถานะยอดต่างกลับเป็น 0
				if( $ds['status_adjust'] != 0 )
				{
					$id_diff = $ds['status_adjust'];
					$rd = $adj->setDiff($id_diff, 0); //--- 0 = ยังไม่โหลด 1 = โหลดเข้าปรับยอดแล้วแต่ยังไม่ได้ปรับ, 2 = ปรับยอดแล้ว
					if( ! $rd )
					{
						$sc = 'ลบรายการเรียบร้อย แต่คืนยอดต่างไม่สำเร็จ';
					}
				}
				//---- 2. เพิ่ม/ลด ยอดสต็อกในโซน
				$ra = update_stock_zone($qty, $id_zone, $id_pa);
				$rc = delete_movement($reference, $id_pa, $id_zone);
			}
			if( $rs && $ra && $rc )
			{
				commitTransection();
			}
			else
			{
				dbRollback();	
			}
			endTransection();
		}
	}
	echo $sc;			
}




if( isset( $_GET['clearFilter'] ) )
{
	$cookie = array('adj_no', 'adj_ref', 'adj_rm', 'adj_vt', 'from', 'to');
	foreach( $cookie as $name )
	{
		deleteCookie($name);
	}
	echo 'done';	
}

//------------------ END NEW CODE ---------------//

?>