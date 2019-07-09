<?php

function getWarehouseIn($txt)
{
	$sc = '1234567890';
	$qs = dbQuery("SELECT id_warehouse FROM tbl_warehouse WHERE warehouse_name LIKE'%".$txt."%'");
	if(dbNumRows($qs) > 0)
	{
		$sc = '';
		$i = 1;
		while($rs = dbFetchObject($qs))
		{
			$sc .= $i == 1? "'".$rs->id_warehouse."'" : ", '".$rs->id_warehouse."'";
			$i++;
		}
	}

	return $sc;
}


 ?>
