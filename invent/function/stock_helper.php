<?php
function stockInZone($id_pa, $id_zone)
{
	$sc = 0;
	$qs = dbQuery("SELECT qty FROM tbl_stock WHERE id_product_attribute = ".$id_pa." AND id_zone = ".$id_zone);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);	
	}
	return $sc;
}

?>