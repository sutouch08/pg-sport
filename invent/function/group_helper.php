<?php
function countMember($id_group)
	{
		$sc = 0;
		$qs = dbQuery("SELECT COUNT(*) FROM tbl_customer WHERE id_default_group = ".$id_group);
		list( $rs ) = dbFetchArray($qs);
		if( ! is_null( $rs ) )
		{
			$sc = $rs;
		}
		return $sc;
	}

function selectGroup($se = '' )
{
	$sc = '';
	$qs = dbQuery("SELECT * FROM tbl_group");
	while( $rs = dbFetchArray($qs) )
	{
		$sc .= '<option value="'.$rs['id_group'].'" '.isSelected($se, $rs['id_group']).' >'.$rs['group_name'].'</option>';
	}
	return $sc;
}

function productGroupName($id)
{
	$sc = "";
	$qs = dbQuery("SELECT name FROM tbl_product_group WHERE id = ".$id);
	if( dbNumRows($qs) == 1 )
	{
		list( $sc ) = dbFetchArray($qs);
	}
	return $sc;
}
?>