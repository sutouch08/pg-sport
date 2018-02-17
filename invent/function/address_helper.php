<?php
function customerProvince($id_customer)
{
	$sc = "";
	$qs = dbQuery("SELECT city FROM tbl_address WHERE id_customer = ".$id_customer);
	if( dbNumRows($qs) > 0 )
	{
		list( $sc ) = dbFetchArray($qs);	
	}
	return $sc;
}


?>