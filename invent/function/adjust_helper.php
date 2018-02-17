<?php
function getAdjustData($id)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT * FROM tbl_adjust WHERE id_adjust = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		$sc = dbFetchArray($qs);
	}
	return $sc;
}


function getDiffData($id)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT * FROM tbl_diff WHERE id_diff = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		$sc = dbFetchArray($qs);	
	}
	return $sc;
}

?>