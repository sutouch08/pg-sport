<?php 
//------------  Transform helper -------//
function selectTransformRole($se = '')
{
	$sc = '<option value="0">เลือกวัตถุประสงค์</option>';
	$sc .= '<option value="2" '. isSelected($se, 2) .'>แปรสภาพเพื่อขาย</option>';
	$sc .= '<option value="6" '. isSelected($se, 6) .'>แปรสภาพเพื่อสปอนเซอร์</option>';
	return $sc;
}

function testx()
{
	return TRUE;	
}
	
?>