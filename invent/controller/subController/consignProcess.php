<?php
//----- หา id_zone ฝากขาย จาก tbl_order_consignment
$toZone		= getConsignmentIdZone( $id_order ); 
if( $toZone !== FALSE )
{	
	if( dbNumRows($qs) > 0 )
	{
		startTransection();
	
		while( $rs = dbFetchObject($qs) )
		{
			set_time_limit(60);
			$id_pa		= $rs->id_pa;
			$qty 			= $rs->qty;
			$dateUpd	= dbDate($order->date_add, TRUE);
			$newQty		= $qty * (-1);
			$fromZone 	= $rs->id_zone;
			$idWH 		= $rs->id_warehouse;
			$pd			= new product();
			$id_pd 		= $pd->getProductId($id_pa);
			$tmpStatus	= 4;				 //----- สถานะของ temp = 4  คือ เปิดบิลแล้ว
			$curStatus	= 3;				//----- Current status in temp  3 =  รอเปิดบิล
			
			//--- เปลี่ยนสถานนะใน temp
			$sc 	= updateTemp($tmpStatus, $curStatus, $id_order, $id_pa, $qty);  
			//----- update buffer
			$sc 	= $sc === TRUE ? update_buffer_zone( $newQty, $id_pd, $id_pa, $id_order, $fromZone, $idWH, $id_employee) : FALSE;
			//------ Update Stock
			$sc 	= $sc === TRUE ? update_stock_zone( $qty, $toZone, $id_pa) : FALSE;
			//------ movement out
			$sc 	= $sc === TRUE ? stock_movement("out", 2, $id_pa, $idWH, $qty, $order->reference, $dateUpd, $fromZone) : FALSE;
			//------ movement in
			$sc	= $sc === TRUE ? stock_movement("in", 1, $id_pa, 2, $qty, $order->reference, $dateUpd, $toZone) : FALSE;
		}
		
		if( $sc === TRUE )
		{
			/// เคลียร์ buffer กรณีจัดขาดจัดเกินหรือแก้ไขออเดอร์ที่จัดแล้วเหลือสินค้าที่จัดแล้วค้างอยู่ที่ Buffer ให้ย้ายไปอยู่ใน cancle แทน
			clear_buffer($id_order); 
			commitTransection();
			endTransection();
		}
		else
		{
			dbRollback();
			endTransection();
			$message = "ทำรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง";	
		}
		
	}
	else
	{
		$sc = FALSE;
		$message = "ทำรายการไม่สำเร็จ เนื่องจากไม่พบข้อมูลการตรวจสินค้า";
	}
}
else
{
	$sc = FALSE;
	$message = "ทำรายการไม่สำเร็จ เนื่องจากไม่พบโซนปลายทาง";
}


?>