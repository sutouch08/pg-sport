<?php
//-------------------------- เปลี่ยนสถานะออเดอร์ ----------------------------------//
function order_state_change($id_order, $state, $id_emp)
{
	$sc		= TRUE;
	$order 	= new order($id_order);
	$c_state	= $order->current_state;  //---  สถานะปัจจุบัน
	if($state == 2)
	{
		if( ! $order->validOrder($id_order)){ $sc = FALSE; }
	}
	else if($state == 1 OR $state == 3 )
	{
		if( $c_state == 9 )  //----- ถ้าเปิดบิลไปแล้ว ----//
		{
			if( ! rollback_order($id_order) ){ $sc = FALSE; }  ///-----  ย้อนกระบวนการ  ----///
		}
	}
	else if($state == 8 )
	{
		require_once 'sponsor_helper.php';
		require_once 'support_helper.php';
		require_once 'lend_helper.php'; 
		if($c_state == 9 OR $c_state == 7 OR $c_state == 6 )
		{
			//----  ถ้าสถานะเป็นเปิดบิลแล้ว ให้ยกเลิกออเดอร์  ---//
			if( ! cancle_order($id_order) ){ $sc = FALSE; }
		}
		else if($c_state == 11 || $c_state == 10 || $c_state == 5 || $c_state == 4 || $c_state == 3 || $c_state == 1)
		{  
			//---- ถ้าสถานะคือ สินค้าถูกจัดออกมาแล้ว แต่ยังไม่ได้เปิดบิล ดึงยอดจาก buffer เพิ่มเข้า cancle
			if( ! clear_buffer($id_order) ){ $sc = FALSE; }
			
			///****************  คืนยอดงบประมาณคงเหลือ  ****************///
			if( ! return_budget($id_order) ){ $sc = FALSE; }
			if($order->role == 7 )
			{ 
				if( ! update_order_support_amount($id_order, 0.00 ) ){ $sc = FALSE; }
				if( ! update_order_support_status($id_order, 2) ){ $sc = FALSE; } /// 0 = notvalid   1 = valid  2 = cancle
			}
			else if($order->role == 4 )
			{
				if( ! update_order_sponsor_amount($id_order, 0.00 ) ){ $sc = FALSE;	}
				if( ! update_order_sponsor_status($id_order, 2) ){ $sc = FALSE; }  /// 0 = notvalid   1 = valid  2 = cancle
			}
			else if($order->role == 3 )
			{
				$lend 		= new lend();
				$id_lend 	= $lend->get_id_lend_by_order($id_order);
				$lend->change_lend_status($id_lend, 3);  /// 0 = not save 1 = saved  2 = closed  3 = cancled
				$lend->change_all_lend_detail_valid($id_lend, 2);    /// 0 = not return or not all  1 = returned all   2 = cancled  ถ้าสถานะเป็น 0 จะตรวจสอบก่อนว่าคืนของครบแล้วหรือยัง ถ้าครบแล้วจะเปลี่ยนเป็น 1 
			}
			$ra = drop_temp_qc($id_order);
			$rb = drop_order_detail($id_order);
			if( ! $ra OR ! $rb ){ $sc = FALSE; }	
		}		
	}
	if( ! dbQuery("UPDATE tbl_order SET current_state = ".$state." WHERE id_order = ".$id_order) ){ $sc = FALSE; };
	if( ! dbQuery("INSERT INTO tbl_order_state_change ( id_order, id_order_state, id_employee ) VALUES (".$id_order.", ".$state.", ".$id_emp.")") ){ $sc = FALSE; }
	
	return $sc;
}



//---------------------------- เมื่อยกเลิกออเดอร์  -----------------------------//
function cancle_order($id_order)
{		
	//---- ลบยอดขาย ลบการ qc ลบtemp ลบ movement นำยอดสินค้าเพิ่มเข้า tbl_cancle
	
	$sc = TRUE;  //-----  ค่าสำหรับส่งกลับ ถ้าไม่มีอะไรผิดพลาด
	//---- ดึงยอดทีบันทึกยอดขายใน order_detail_sold กลับมา
	$sql = dbQuery("SELECT id_product, id_product_attribute, reference, total_amount FROM tbl_order_detail_sold WHERE id_order = ".$id_order);  
	$order = new order($id_order);
	if($order->role == 5 )
	{
		if( ! cancle_consign($id_order) ){ $sc = FALSE; }
	}
	else
	{
		
		if( ! dbQuery("DELETE FROM tbl_order_discount WHERE id_order = ".$id_order) ){ $sc = FALSE; }
		
		///---- ถ้ามีรายการในตาราง order_detail_sold แสดงว่ามียอดขาย
		if( dbNumRows($sql) > 0 )
		{ 
			while( $rs = dbFetchArray($sql) )
			{
				//----- ดึงรายการในตาราง qc 
				$qr = "SELECT SUM(tbl_qc.qty) AS qty, id_warehouse, id_zone, tbl_qc.id_employee FROM tbl_qc JOIN tbl_temp ON tbl_qc.id_temp = tbl_temp.id_temp ";
				$qr .= "WHERE tbl_qc.id_order = ".$id_order." AND tbl_qc.id_product_attribute = ".$rs['id_product_attribute']." AND tbl_qc.valid = 1  GROUP BY id_zone";
				$qr = dbQuery($qr); 
				if( dbNumRows($qr) > 0 )  
				{
					//----- ทำการเพิ่มรายการเข้า tbl_cancle 
					while($rm = dbFetchArray($qr) )
					{
						//----- เพิ่มรายการเข้าตาราง ยกเลิก
						$ra = cancle_product( $rm['qty'], $rs['id_product'], $rs['id_product_attribute'], $id_order, $rm['id_zone'], $rm['id_warehouse'], $rm['id_employee'] ); 
						
						//----- ลบ stock_movement
						$rb = delete_movement( $rs['reference'], $rs['id_product_attribute'], $rm['id_zone'] );  
						
						//----- ลบยอดขาย
						$rc = delete_detail_sold( $id_order, $rs['id_product_attribute'] );	
						
						if( ! $ra OR ! $rb OR ! $rc ){ $sc = FALSE; }							
					}
				}
			}
			//----------------  คืนยอดงบประมาณ
			if( ! return_budget($id_order) ){ $sc = FALSE; }
			
			if($order->role == 7 )
			{ 
				//----- ปรับปรุงยอดออเดอร์ใน order_support
				$ra =	update_order_support_amount($id_order, 0.00 );
				
				//----- อัพเดทสถานะ order_support   0 = notvalid  / 1 = valid / 2 = cancle				
				$rb = update_order_support_status($id_order, 2); 
				
				if( ! $ra OR ! $rb ){ $sc = FALSE; }
				
			}
			
			if($order->role == 4 )
			{
				//----- ปรับปรุงยอดออเดอร์ใน order_sponsor
				$ra = update_order_sponsor_amount($id_order, 0.00 );	
				
				//----- 0 = notvalid   1 = valid  2 = cancle				
				$rb = update_order_sponsor_status($id_order, 2); 		
				
				if( ! $ra OR ! $rb ){ $sc = FALSE; }						
			}
			
			if($order->role == 3 )
			{
				$lend 		= new lend();
				$id_lend 	= $lend->get_id_lend_by_order($id_order);
				
				//----- 0 = not save 1 = saved  2 = closed  3 = cancled
				$ra 		= $lend->change_lend_status($id_lend, 3);  
				
				//----- 2 = cancled
				$rb		= $lend->change_all_lend_detail_valid($id_lend, 2); 
				
				if( ! $ra OR ! $rb ){ $sc = FALSE; }	
			}
		}
	}
	//-----  ลบรายการใน temp และ qc
	$ra = drop_temp_qc($id_order);		
	
	//-----  ลบรายละเอียดออเดอร์	
	$rb = drop_order_detail($id_order);
	
	if( ! $ra OR ! $rb ){ $sc = FALSE; }	
	
	return $sc;
}




//------------------------------  เมื่อเปลี่ยนสถานะของออเดอร์ที่เปิดบิลไปแล้ว ( แต่ไม่ได้ยกเลิกออเดอร์)
function rollback_order($id_order)
{
	//-----  กำหนดค่าเริ่มต้นสำหรับส่งกลับกรณีที่ไม่มีข้อผิดพลาดใดๆ
	$sc 		= TRUE;  
	$order 	= new order($id_order);
	if($order->role == 5 )
	{
		rollback_consign($id_order);
	}
	else
	{
		//----- ดึงยอดทีบันทึกยอดขายใน order_detail_sold กลับมา
		$qs = dbQuery("SELECT id_product, id_product_attribute, reference FROM tbl_order_detail_sold WHERE id_order = ".$id_order);  
		//----- ถ้ามียอดขาย
		if( dbNumRows($qs) > 0 )
		{ 
			while( $rs = dbFetchArray($qs) )
			{
				//-----  ดึงยอดจาก qc เพื่อเตรียมเพิ่มกลับเข้าไปที่ Buffer
				$qr = "SELECT SUM(tbl_qc.qty) AS qty, id_warehouse, id_zone, tbl_qc.id_employee FROM tbl_qc JOIN tbl_temp ON tbl_qc.id_temp = tbl_temp.id_temp ";
				$qr .= "WHERE tbl_qc.id_order = ".$id_order." AND tbl_qc.id_product_attribute = ".$rs['id_product_attribute']." GROUP BY id_zone";
				$qr = dbQuery($qr);
				
				if( dbNumRows($qr) > 0 )
				{ 
					//----- ทำการเพิ่มรายการกลับเข้า buffer
					while( $rm = dbFetchArray($qr) )
					{
						//-----  เพิ่มสินค้าเข้า Buffer
						$ra = update_buffer_zone($rm['qty'], $rs['id_product'], $rs['id_product_attribute'], $id_order, $rm['id_zone'], $rm['id_warehouse'], $rm['id_employee']);
						
						//-----  ลบรายการใน stock_movement
						$rb = delete_movement($rs['reference'], $rs['id_product_attribute'], $rm['id_zone']);
						
						//-----  ลบรายการใน order_detail_sold (ยอดขาย)
						$rc = delete_detail_sold($id_order, $rs['id_product_attribute']);
						
						if( ! $ra OR ! $rb OR ! $rc ){ $sc = FALSE; }
					}
				}
			}
			
			//--------------------------------------  UPdate Budget  ------------------------------//
			//------------------  กรณีเบิกอภินันท์
			if($order->role == 7)
			{
				$order_amount	=  $order->getCurrentOrderAmount($id_order);		//----- ตรวจสอบยอดเงินสั่งซื้อ
				$qc_amount 	= $order->qc_amount($id_order);						//----- ตรวจสอบยอดเงินที่ qc ได้
				
				//----- ถ้าไม่เท่ากันให้ทำการปรับปรุงยอดงบประมาณคงเหลือ
				if( $order_amount != $qc_amount )
				{											
					$id_budget	= get_id_support_budget_by_order($id_order);
					
					//----- ยอดต่างระหว่างยอดเงินสั่งซื้อ กับ ยอดเงิน qc  แล้วทำให้ติดลบเพื่อทำการลดงบประมาณให้เป็นไปตามออเดอร์ เพราะเมื่อเปิดบิลอีกครั้งหากมียอดต่างจะบวกกลับให้
					$amount 		= ($order_amount - $qc_amount) * -1;	
					
					//----- ดึงยอดงบประมาณคงเหลือขึ้นม					
					$balance 	= get_support_balance($id_budget);	
					
					//----- บวกยอดต่างกลับเข้าไป กรณีที่ ยอดสั่งมากกว่ายอด qc ต้องคืนยอดต่างกลับเข้างบ					
					$balance 	+= $amount;													
					
					//-----  ปรับปรุงยอดงบประมาณคงเหลือ
					if( ! update_support_balance($id_budget, $balance) ){ $sc = FALSE; }					
				}
				
				//----- ปรับปรุงยอดออเดอร์ใน order_sponsor
				$ra = update_order_support_amount($id_order, 0.00 );						
				
				//----- อัพเดท สภานะของ order_support  0 = notvalid /  1 = valid / 2 = cancle
				$rb = update_order_support_status($id_order, 0); 							
				
				if( ! $ra OR ! $rb ){ $sc = FALSE; }
			}
			
			//------------------- กรณีเบิกสปอนเซอร์
			if($order->role == 4)
			{
				$order_amount 	=  $order->getCurrentOrderAmount($id_order); 	//----- ตรวจสอบยอดเงินสั่งซื้อ
				$qc_amount 		= $order->qc_amount($id_order);						//----- ตรวจสอบยอดเงินที่ qc ได้
				
				//----- ถ้าไม่เท่ากันให้ทำการปรับปรุงยอดงบประมาณคงเหลือ
				if($order_amount != $qc_amount)
				{											
					$id_budget 	= get_id_sponsor_budget_by_order($id_order);	
					
					//----- ยอดต่างระหว่างยอดเงินสั่งซื้อ กับ ยอดเงิน qc  แล้วทำให้ติดลบเพื่อทำการลดงบประมาณให้เป็นไปตามออเดอร์ เพราะเมื่อเปิดบิลอีกครั้งหากมียอดต่างจะบวกกลับให้
					$amount 		= ($order_amount - $qc_amount) * -1;						
					
					//----- ดึงยอดงบประมาณคงเหลือขึ้นมา
					$balance 	= get_sponsor_balance($id_budget);						
					
					//----- บวกยอดต่างกลับเข้าไป กรณีที่ ยอดสั่งมากกว่ายอด qc ต้องคืนยอดต่างกลับเข้างบ
					$balance 	+= $amount;													
					
					//-----  ปรับปรุงยอดงบประมาณคงเหลือ
					if( ! update_sponsor_balance($id_budget, $balance) ){ $sc = FALSE; }			
							
				}
				
				//----- ปรับปรุงยอดออเดอร์ใน order_sponsor
				$ra = update_order_sponsor_amount($id_order, 0.00 );			
				
				//-----  อัพเดทสถานะของ order_sponsor  0 = notvalid /  1 = valid / 2 = cancle			
				$rb = update_order_sponsor_status($id_order, 0); 			
				
				if( ! $ra OR ! $rb ){ $sc = FALSE; }				
			}
			//----------------------------------------  END Update Budget  --------------------------------//
		}	
	}
	
	return $sc;
}





//------------------------  เมื่อเปลี่ยนสถานะของออเดอร์ฝากขายที่เปิดบิลไปแล้ว ( แต่ไม่ได้ยกเลิกออเดอร์)  --------------------------//
function rollback_consign($id_order)
{
	$sc = TRUE;
	$reference = get_order_reference($id_order);
	$id_emp		= getCookie('user_id') == FALSE ? 0 : getCookie('user_id');
	$qs = dbQuery("SELECT * FROM tbl_stock_movement WHERE reference = '".$reference."'"); 
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$product 	= new product();
			$id_product = $product->getProductId($rs['id_product_attribute']);
			
			//-----  ถ้า move_out = 0 แสดงว่าเป็นปลายทาง ให้ตัดยอดออกจากโซน
			if( $rs['move_in'] > 0 && $rs['move_out'] == 0 )
			{
				if( ! update_stock_zone($rs['move_in']*-1, $rs['id_zone'], $rs['id_product_attribute']) ){ $sc = FALSE;  }
			}
			
			//-----  ถ้า move_in = 0 แสดงว่า เป็นต้นทาง ให้คืนยอดเข้า buffer
			if( $rs['move_out'] > 0 && $rs['move_in'] == 0 )
			{
				if( ! update_buffer_zone($rs['move_out'], $id_product, $rs['id_product_attribute'], $id_order, $rs['id_zone'], $rs['id_warehouse'], $id_emp) ){ $sc = FALSE; }
			}
			
			$rc =	dbQuery("DELETE FROM tbl_stock_movement WHERE id_stock_movement = ".$rs['id_stock_movement']);	
			
			if( ! $rc ){ $sc = FALSE; }
		}
	}
	return $sc;
}





//-----------------------------  เมื่อยกเลิกออเดอร์ฝากขาย
function cancle_consign($id_order)
{
	$sc 			= TRUE;
	$reference 	= get_order_reference($id_order);
	$id_emp		= getCookie('user_id') === FALSE ? 0 : getCookie('user_id');
	$qs 			= dbQuery("SELECT * FROM tbl_stock_movement WHERE reference = '".$reference."'"); 
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			$product = new product();
			$id_product = $product->getProductId($rs['id_product_attribute']);
			
			if( $rs['move_in'] > 0 && $rs['move_out'] == 0 )
			{
				//-----  ตัดยอดออกจากโซน
				if( ! update_stock_zone( $rs['move_in']*-1, $rs['id_zone'], $rs['id_product_attribute'] ) ){ $sc = FALSE;  }
			}
			if( $rs['move_out'] > 0 && $rs['move_in'] == 0 )
			{				
				//----- เพิ่มรายการเข้าตาราง ยกเลิก
				if( ! cancle_product( $rs['move_in'], $id_product, $rs['id_product_attribute'], $id_order, $rs['id_zone'], $rs['id_warehouse'], $id_emp ) ){ $sc = FALSE;  }
				
			}
			
			//-----  ลบรายการออกจาก movement
			$rc = dbQuery("DELETE FROM tbl_stock_movement WHERE id_stock_movement = ".$rs['id_stock_movement']);	
			
			if( ! $rc ){ $sc = FALSE; }
			
		}//--- End while
	}//--- End if
	
	return $sc;
}












//------------------------------------  ฺBack up Old code  ------------------------------------//
/*
function order_state_change($id_order, $id_order_state, $id_employee)
{
	$row_open = dbNumRows(dbQuery("SELECT tbl_order.id_order FROM tbl_order WHERE tbl_order.id_order = $id_order AND  tbl_order.current_state = 9"));
	if($id_order_state == 2)
	{
		$qs = dbQuery("UPDATE tbl_order SET valid = 1 WHERE id_order = $id_order");
	}
	else if($id_order_state == 1 || $id_order_state == 3 )
	{
		if($row_open > 0)
		{
			rollback_order($id_order);
		}
		else
		{
			list($c_state) = dbFetchArray(dbQuery("SELECT current_state FROM tbl_order WHERE id_order = ".$id_order));  //// ดึงสถานะปัจจุบันของออเดอร์
			if($c_state == 8 )
			{ //// ถ้ายกเลิกไปแล้วจะนำกลับมาใหม่
				dbQuery("UPDATE tbl_order_detail SET valid_detail = 0 WHERE id_order = ".$id_order);  /// เปลี่ยนสถานะรายการสั่งสินค้าให้กลับมารอจัดได้อีกครั้ง
				$order = new order($id_order);
				apply_budget($id_order);			
				if($order->role == 7 )
				{ 
					update_order_support_status($id_order, 0); /// 0 = ยังไม่เปิดบิล /  1 = เปิดบิลแล้ว / 2 = ยกเลิก
				}
				else if($order->role == 4 )
				{
					update_order_sponsor_status($id_order, 0); /// 0 = ยังไม่เปิดบิล /  1 = เปิดบิลแล้ว / 2 = ยกเลิก
				}
				else if( $order->role == 3 )
				{
					$lend 		= new lend();
					$id_lend 	= $lend->get_id_lend_by_order($id_order);
					$lend->change_lend_status($id_lend, 2);  /// 0 = not save 1 = saved  2 = closed  3 = cancled   ถ้าสถานะเป็น 2 จะตรวจสอบก่อนว่าคืนครบแล้วหรือยังถ้ายังไม่ครบจะปรับให้เป็น 1
					$lend->change_all_lend_detail_valid($id_lend, 0); /// 0 = not return or not all  1 = returned all   2 = cancled  ถ้าสถานะเป็น 0 จะตรวจสอบก่อนว่าคืนของครบแล้วหรือยัง ถ้าครบแล้วจะเปลี่ยนเป็น 1 
				}
			}
		}		
		$qs = dbQuery("UPDATE tbl_order SET current_state = $id_order_state, valid = 0 WHERE id_order = $id_order");
	}
	else if($id_order_state == 8 )
	{
		require_once 'sponsor_helper.php';
		require_once 'support_helper.php';
		require_once 'lend_helper.php'; 
		$order = new order($id_order);
		list($c_state) = dbFetchArray(dbQuery("SELECT current_state FROM tbl_order WHERE id_order = ".$id_order));  //// ดึงสถานะปัจจุบันของออเดอร์
		if($c_state == 9 || $c_state == 7 || $c_state == 6 )
		{ // ถ้าสถานะเป็นเปิดบิลแล้ว ให้ยกเลิกออเดอร์
			cancle_order($id_order);
		}
		else if($c_state == 11 || $c_state == 10 || $c_state == 5 || $c_state == 4 || $c_state == 3 || $c_state == 1)
		{  // ถ้าสถานะคือ สินค้าถูกจัดออกมาแล้ว แต่ยังไม่ได้เปิดบิล ดึงยอดจาก buffer เพิ่มเข้า cancle
			$qs = dbQuery("SELECT * FROM tbl_buffer WHERE id_order = ".$id_order);
			$ro = dbNumRows($qs);
			if($ro > 0)
			{ /// ทำการเพิ่มรายการเข้า tbl_cancle 
				while($rs = dbFetchArray($qs) )
				{
					cancle_product($rs['qty'], $rs['id_product'], $rs['id_product_attribute'], $id_order, $rs['id_zone'], $rs['id_warehouse'], $rs['id_employee']);
					update_buffer_zone($rs['qty']*-1, $rs['id_product'], $rs['id_product_attribute'], $id_order, $rs['id_zone'], $rs['id_warehouse'], $rs['id_employee']);
				}
			}
			//-----------  คืนยอดงบประมาณคงเหลือ ---------------//
			return_budget($id_order);
			if($order->role == 7 )
			{ 
				update_order_support_amount($id_order, 0.00 );	
				update_order_support_status($id_order, 2); /// 0 = notvalid   1 = valid  2 = cancle
			}
			else if($order->role == 4 )
			{
				update_order_sponsor_amount($id_order, 0.00 );	
				update_order_sponsor_status($id_order, 2); /// 0 = notvalid   1 = valid  2 = cancle
			}
			else if($order->role == 3 )
			{
				$lend 		= new lend();
				$id_lend 	= $lend->get_id_lend_by_order($id_order);
				$lend->change_lend_status($id_lend, 3);  /// 0 = not save 1 = saved  2 = closed  3 = cancled
				$lend->change_all_lend_detail_valid($id_lend, 2);    /// 0 = not return or not all  1 = returned all   2 = cancled  ถ้าสถานะเป็น 0 จะตรวจสอบก่อนว่าคืนของครบแล้วหรือยัง ถ้าครบแล้วจะเปลี่ยนเป็น 1 
			}
			drop_temp_qc($id_order);
			drop_order_detail($id_order);
		}		
		$qs = dbQuery("UPDATE tbl_order SET current_state = $id_order_state WHERE id_order = $id_order");

	}
	else
	{
		$qs = dbQuery("UPDATE tbl_order SET current_state = $id_order_state WHERE id_order = $id_order");
	}
	
	$qs = dbQuery("INSERT INTO tbl_order_state_change SET id_order = $id_order, id_order_state = $id_order_state, id_employee = $id_employee");
	
	return $qs;
}
*/



/*
//----------------------  เมื่อเปลี่ยนสถานะของออเดอร์ที่เปิดบิลไปแล้ว ( แต่ไม่ได้ยกเลิกออเดอร์)  ------------------------------------//
	function rollback_order($id_order){
		$sql = dbQuery("SELECT id_product, id_product_attribute, reference FROM tbl_order_detail_sold WHERE id_order = ".$id_order);  /// ดึงยอดทีบันทึกยอดขายใน order_detail_sold กลับมา
		$row = dbNumRows($sql);
		$order = new order($id_order);
		if($order->role == 5 ){
			rollback_consign($id_order);
		}else{
			if($row > 0 ){ /// ถ้ามียอดขาย
				while($rs = dbFetchArray($sql) ){
					$qr = dbQuery("SELECT SUM(tbl_qc.qty) AS qty, id_warehouse, id_zone, tbl_qc.id_employee FROM tbl_qc JOIN tbl_temp ON tbl_qc.id_temp = tbl_temp.id_temp WHERE tbl_qc.id_order = ".$id_order." AND tbl_qc.id_product_attribute = ".$rs['id_product_attribute']." GROUP BY id_zone");
					$ro = dbNumRows($qr);
					if($ro > 0){ /// ทำการเพิ่มรายการกลับเข้า buffer
						while($rm = dbFetchArray($qr) ){
							update_buffer_zone($rm['qty'], $rs['id_product'], $rs['id_product_attribute'], $id_order, $rm['id_zone'], $rm['id_warehouse'], $rm['id_employee']);
							delete_movement($rs['reference'], $rs['id_product_attribute'], $rm['id_zone']);
							delete_detail_sold($id_order, $rs['id_product_attribute']);
						}
					}
				}
				///----------------------  UPdate Budget  ---------------------///
				if($order->role == 7){
					$order_amount =  $order->getCurrentOrderAmount($id_order);		/// ตรวจสอบยอดเงินสั่งซื้อ
					$qc_amount = $order->qc_amount($id_order);							/// ตรวจสอบยอดเงินที่ qc ได้
					if($order_amount != $qc_amount){											/// ถ้าไม่เท่ากันให้ทำการปรับปรุงยอดงบประมาณคงเหลือ
						$id_budget = get_id_support_budget_by_order($id_order);
						$amount = ($order_amount - $qc_amount) * -1;						/// ยอดต่างระหว่างยอดเงินสั่งซื้อ กับ ยอดเงิน qc  แล้วทำให้ติดลบเพื่อทำการลดงบประมาณให้เป็นไปตามออเดอร์ เพราะเมื่อเปิดบิลอีกครั้งหากมียอดต่างจะบวกกลับให้
						$balance = get_support_balance($id_budget);						/// ดึงยอดงบประมาณคงเหลือขึ้นม
						$balance += $amount;													/// บวกยอดต่างกลับเข้าไป กรณีที่ ยอดสั่งมากกว่ายอด qc ต้องคืนยอดต่างกลับเข้างบ
						update_support_balance($id_budget, $balance);					///  ปรับปรุงยอดงบประมาณคงเหลือ
					}
					update_order_support_amount($id_order, 0.00 );						/// ปรับปรุงยอดออเดอร์ใน order_sponsor
					update_order_support_status($id_order, 0); 							/// อัพเดท สภานะของ order_support  0 = notvalid /  1 = valid / 2 = cancle
				}else if($order->role == 4){
						$order_amount =  $order->getCurrentOrderAmount($id_order); 	/// ตรวจสอบยอดเงินสั่งซื้อ
						$qc_amount = $order->qc_amount($id_order);							/// ตรวจสอบยอดเงินที่ qc ได้
						if($order_amount != $qc_amount){											/// ถ้าไม่เท่ากันให้ทำการปรับปรุงยอดงบประมาณคงเหลือ
							$id_budget = get_id_sponsor_budget_by_order($id_order);	
							$amount = ($order_amount - $qc_amount) * -1;						/// ยอดต่างระหว่างยอดเงินสั่งซื้อ กับ ยอดเงิน qc  แล้วทำให้ติดลบเพื่อทำการลดงบประมาณให้เป็นไปตามออเดอร์ เพราะเมื่อเปิดบิลอีกครั้งหากมียอดต่างจะบวกกลับให้
							$balance = get_sponsor_balance($id_budget);						/// ดึงยอดงบประมาณคงเหลือขึ้นมา
							$balance += $amount;													/// บวกยอดต่างกลับเข้าไป กรณีที่ ยอดสั่งมากกว่ายอด qc ต้องคืนยอดต่างกลับเข้างบ
							update_sponsor_balance($id_budget, $balance);					///  ปรับปรุงยอดงบประมาณคงเหลือ
						}
						update_order_sponsor_amount($id_order, 0.00 );						/// ปรับปรุงยอดออเดอร์ใน order_sponsor
						update_order_sponsor_status($id_order, 0); 							///  อัพเดทสถานะของ order_sponsor  0 = notvalid /  1 = valid / 2 = cancle
				}
				///-------------------------- END Update Budget  -----------------------------//
			}	
		}
	}

*/


	


//---------------------------- เมื่อยกเลิกออเดอร์  -----------------------------//


/*
function cancle_order($id_order)
{		
	// ลบยอดขาย ลบการ qc ลบtemp ลบ movement นำยอดสินค้าเพิ่มเข้า tbl_cancle
	$sql = dbQuery("SELECT id_product, id_product_attribute, reference, total_amount FROM tbl_order_detail_sold WHERE id_order = ".$id_order);  /// ดึงยอดทีบันทึกยอดขายใน order_detail_sold กลับมา
	$row = dbNumRows($sql);
	$order = new order($id_order);
	if($order->role == 5 ){
		cancle_consign($id_order);
	}else{
		$qs = dbQuery("DELETE FROM tbl_order_discount WHERE id_order = ".$id_order);
		if($row > 0 ){ /// ถ้ามีรายการในตาราง order_detail_sold แสดงว่ามียอดขาย
			while($rs = dbFetchArray($sql) ) :
				$qr = dbQuery("SELECT SUM(tbl_qc.qty) AS qty, id_warehouse, id_zone, tbl_qc.id_employee FROM tbl_qc JOIN tbl_temp ON tbl_qc.id_temp = tbl_temp.id_temp WHERE tbl_qc.id_order = ".$id_order." AND tbl_qc.id_product_attribute = ".$rs['id_product_attribute']." AND tbl_qc.valid = 1  GROUP BY id_zone"); /// ดึงรายการในตาราง qc 
					$ro = dbNumRows($qr);
					if($ro > 0) : /// ทำการเพิ่มรายการเข้า tbl_cancle 
						while($rm = dbFetchArray($qr) ) :
							cancle_product($rm['qty'], $rs['id_product'], $rs['id_product_attribute'], $id_order, $rm['id_zone'], $rm['id_warehouse'], $rm['id_employee']); /// เพิ่มรายการเข้าตาราง ยกเลิก
							delete_movement($rs['reference'], $rs['id_product_attribute'], $rm['id_zone']);  /// ลบ stock_movement
							delete_detail_sold($id_order, $rs['id_product_attribute']);								/// ลบยอดขาย
						endwhile;
					endif;				
				endwhile;
				return_budget($id_order);
				if($order->role == 7 ){ 
					update_order_support_amount($id_order, 0.00 );					/// ปรับปรุงยอดออเดอร์ใน order_support
					update_order_support_status($id_order, 2); 						/// อัพเดทสถานะ order_support   0 = notvalid  / 1 = valid / 2 = cancle
				}else if($order->role == 4 ){
					update_order_sponsor_amount($id_order, 0.00 );					/// ปรับปรุงยอดออเดอร์ใน order_sponsor
					update_order_sponsor_status($id_order, 2); 						/// 0 = notvalid   1 = valid  2 = cancle		
				}else if($order->role == 3 ){
					$lend 		= new lend();
					$id_lend 	= $lend->get_id_lend_by_order($id_order);
					$lend->change_lend_status($id_lend, 3);  /// 0 = not save 1 = saved  2 = closed  3 = cancled
					$lend->change_all_lend_detail_valid($id_lend, 2); /// 2 = cancled
				}
			}
		}
		drop_temp_qc($id_order);			//// ลบรายการใน qc และ temp
		drop_order_detail($id_order);
}
*/



/*
//------------------------  เมื่อเปลี่ยนสถานะของออเดอร์ฝากขายที่เปิดบิลไปแล้ว ( แต่ไม่ได้ยกเลิกออเดอร์)  --------------------------//
function rollback_consign($id_order)
{
	$reference = get_order_reference($id_order);
	$qs = dbQuery("SELECT * FROM tbl_stock_movement WHERE reference = '".$reference."'"); 
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) ) :
			$product = new product();
			$id_product = $product->getProductId($rs['id_product_attribute']);
			if( $rs['move_in'] == 0 )
			{
				$rd = update_buffer_zone($rs['move_out'], $id_product, $rs['id_product_attribute'], $id_order, $rs['id_zone'], $rs['id_warehouse'], $_COOKIE['user_id']);
			}else{
				$rd = update_stock_zone($rs['move_in']*-1, $rs['id_zone'], $rs['id_product_attribute']); 
			}
			if($rd)
			{
				dbQuery("DELETE FROM tbl_stock_movement WHERE id_stock_movement = ".$rs['id_stock_movement']);	
			}
		endwhile;
	}
}

*/



/*
function cancle_consign($id_order)
{
	$reference = get_order_reference($id_order);
	$qs = dbQuery("SELECT * FROM tbl_stock_movement WHERE reference = '".$reference."'"); 
	if(dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) ) :
			$product = new product();
			$id_product = $product->getProductId($rs['id_product_attribute']);
			if( $rs['move_in'] == 0 )
			{
				$rd = cancle_product($rs['move_out'], $id_product, $rs['id_product_attribute'], $id_order, $rs['id_zone'], $rs['id_warehouse'], $_COOKIE['user_id']);  /// เพิ่มรายการเข้าตาราง ยกเลิก
			}else{
				$rd = update_stock_zone($rs['move_in']*-1, $rs['id_zone'], $rs['id_product_attribute']); 
			}
			if($rd)
			{
				dbQuery("DELETE FROM tbl_stock_movement WHERE id_stock_movement = ".$rs['id_stock_movement']);	
			}
		endwhile;
	}
}
*/
?>