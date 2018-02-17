<?php
if( dbNumRows($qs) > 0 )
{
	
	while( $rs = dbFetchObject($qs) )
	{
		set_time_limit(60);
		$id_pa		= $rs->id_pa;
		$qty			= $rs->qty;
		$newQty		= $qty * (-1);
		$dateUpd	= dbDate($order->date_add, TRUE);
		$fromZone	= $rs->id_zone;
		$idWH			= $rs->id_warehouse;
		$pd			= new product();
		$id_pd		= $pd->getProductId($id_pa);
		$tmpStatus	= 4;				 //----- สถานะของ temp = 4  คือ เปิดบิลแล้ว
		$curStatus	= 3;				//----- Current status in temp  3 =  รอเปิดบิล
		
		//--- เปลี่ยนสถานนะใน temp
		$sc 	= updateTemp($tmpStatus, $curStatus, $id_order, $id_pa, $qty);  
		//----- update buffer
		$sc 	= $sc === TRUE ? update_buffer_zone( $newQty, $id_pd, $id_pa, $id_order, $fromZone, $idWH, $id_employee) : FALSE;
		//------ movement out
		$sc 	= $sc === TRUE ? stock_movement("out", 3, $id_pa, $idWH, $qty, $order->reference, $dateUpd, $fromZone) : FALSE;
	}
	
	//---- บันทึกยืม
	$sc = $sc === TRUE ? order_sold($id_order) : FALSE;
	
	//----- update ยอดยืม
	$sc = $sc === TRUE ? update_lend_qty($id_order) : FALSE;
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
	
?>			