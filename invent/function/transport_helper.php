<?php
function sender_name($id_sender)
{
	$name = '';
	$qs = dbQuery("SELECT name FROM tbl_sender WHERE id_sender = ".$id_sender);
	if( dbNumRows($qs) == 1 )
	{
		list($name) = dbFetchArray($qs);
	}
	return $name;
}

function getSender($id)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT * FROM tbl_sender WHERE id_sender = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		$sc = dbFetchArray($qs);
	}
	return $sc;
}


function sender_in($txt) /// return id_sender LIKE IN( *** return value ***)
{
	$qs = dbQuery("SELECT id_sender FROM tbl_sender WHERE name LIKE '%".$txt."%'");
	$row = dbNumRows($qs);
	if( $row > 0 )
	{
		$in = '';
		$i = 1;
		while($rs = dbFetchArray($qs))
		{
			$in .= $rs['id_sender'];
			if( $i != $row ){ $in .= ', '; }
			$i++;	
		}
	}
	else
	{
		$in = false; 
	}
	return $in;
}

function countAddress($id_customer)
{
	$qs = dbQuery("SELECT id_address FROM tbl_address WHERE id_customer = ".$id_customer);
	return dbNumRows($qs);	
}

function countSender($id_customer)
{
	$sd = 0;
	$qs = dbQuery("SELECT * FROM tbl_transport WHERE id_customer = ".$id_customer);
	if( dbNumRows($qs) == 1 )
	{
		$rs = dbFetchArray($qs);
		$sd += 1;
		$sc = $rs['second_sender'] == 0 ? 0 : 1;
		$thd	= $rs['third_sender'] == 0 ? 0 : 1;
		$sd = $sd + $sc + $thd;
	}
	return $sd;
}

function countBoxes($id_order)
{
	$qs = dbQuery("SELECT id_box FROM tbl_box WHERE id_order = ".$id_order);
	return dbNumRows($qs);	
}

function getAllCustomerAddress($id_customer)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT * FROM tbl_address WHERE id_customer = ".$id_customer);
	if( dbNumRows($qs) > 0 )
	{
		$sc = $qs; 	
	}
	return $sc;
}

function getAllSender($id_customer)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT * FROM tbl_transport WHERE id_customer = ".$id_customer);
	if( dbNumRows($qs) > 0 )
	{
		$sc = dbFetchArray($qs);
	}
	return $sc;
}
function getMainSender($id_customer)
{
	$sc = 0;
	$qs = dbQuery("SELECT main_sender FROM tbl_transport WHERE id_customer = ".$id_customer);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}

function getidAddress($id_customer)
{
	$sc = 0;
	$qs = dbQuery("SELECT id_address FROM tbl_address WHERE id_customer = ".$id_customer." LIMIT 1 ");
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);	
	}
	return $sc;
}

function getAddress($id)
{
	$sc = FALSE;
	$qs = dbQuery("SELECT * FROM tbl_address WHERE id_address = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		$sc = dbFetchArray($qs);	
	}
	return $sc;
}


?>