<?php

function removeVAT($amount, $vat=7)
{
	if( $vat != 0 )
	{
		$re_vat	= ($vat + 100) / 100;
		return $amount/$re_vat;
	}
	else
	{
		return $amount;
	}
}






function addVAT($amount, $vat = 7)
{
	if( $vat != 0 )
	{
		$re_vat = $vat * 0.01;
		$sc = ($amount * $re_vat) + $amount;
	}
	else
	{
		$sc = $amount;
	}
	return $sc;
}






function getVatAmount($amount, $VAT, $exVAT = TRUE)
{
	$sc = 0;
	if( $VAT != 0 )
	{
		if( $exVAT === TRUE ) //--- กรณีแยกนอก
		{
			$sc = $amount * ( $VAT * 0.01 );
		}
		else
		{
			$sc = $amount - ( $amount / ( ( 100 + $VAT ) * 0.01 ) );
		}
	}
	return $sc;
}



?>
