<?php
function imageUrl($reference)
{
	$link	= WEB_ROOT.'img/payment/'.$reference.'.jpg';
	$file 	= realpath(DOC_ROOT.$link);
	if( ! file_exists($file) )
	{
		$link = FALSE;
	}
	return $link;
}

function validPayment($id_order)
{
	return dbQuery("UPDATE tbl_payment SET valid = 1 WHERE id_order = ".$id_order);
}


?>