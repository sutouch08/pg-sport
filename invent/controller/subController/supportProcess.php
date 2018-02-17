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
	
	$sc = $sc === TRUE ? order_sold($id_order) : FALSE;
	
	if( $sc === TRUE )
	{
		/// ตรวจสอบยอดเงินสั่งซื้อ
		$order_amount = $order->getCurrentOrderAmount($id_order);		
		
		/// ตรวจสอบยอดเงินที่ qc ได้
		$qc_amount 	= $order->qc_amount($id_order);		
		
		/// ถ้าไม่เท่ากันให้ทำการปรับปรุงยอดงบประมาณคงเหลือ					
		if($order_amount != $qc_amount)
		{											
			$id_budget 	= get_id_support_budget_by_order($id_order);
			
			/// ยอดต่างระหว่างยอดเงินสั่งซื้อ กับ ยอดเงิน qc 
			$amount 		= $order_amount - $qc_amount;							
			
			/// ดึงยอดงบประมาณคงเหลือขึ้นมา
			$balance 	= get_support_balance($id_budget);						
			
			/// บวกยอดต่างกลับเข้าไป กรณีที่ ยอดสั่งมากกว่ายอด qc ต้องคืนยอดต่างกลับเข้างบ
			$balance 	+= $amount;													
			
			///  ปรับปรุงยอดงบประมาณคงเหลือ
			update_support_balance($id_budget, $balance);				
			
		}		
		
		/// ปรับปรุงยอดออเดอร์ใน order_support
		update_order_support_amount($id_order, $qc_amount);
		
		/// อัพเดท สภานะของ order_support  0 = notvalid /  1 = valid / 2 = cancle
		update_order_support_status($id_order, 1);			
			
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