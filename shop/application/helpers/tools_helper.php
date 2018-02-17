<?php
function getConfig($config_name = '')
{
	$value = '';
	if($config_name !== '')
	{
		$qs = get_instance()->db->select('value')->where('config_name', $config_name)->get('tbl_config');
		if( $qs->num_rows() == 1 )
		{
			$value = $qs->row()->value;
		}
	}
	return $value;
}

function getCurrency()
{
	return getConfig('CURRENCY');
}

function getDiscountSymbol($type = 'percent')
{
	$symbol = '%';
	if( $type !== 'percent' )
	{
		$symbol = getCurrency();
	}
	return $symbol;
}



?>