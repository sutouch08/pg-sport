<?php 
function thaiDate($date, $time = false, $sep ="-")
{
	if($date != "" && $date != "0000-00-00")
	{
		$y 	= date("Y", strtotime($date));
		if($y < 2200){ $y += 543; }
		if($time)
		{
			$date = date("d".$sep."m".$sep.$y." H:i:s", strtotime($date));
		}
		else
		{
			$date = date("d".$sep."m".$sep.$y, strtotime($date));
		}
	}
	return $date;
}

function dbDate($date)
{
	if($date != "" && $date != "0000-00-00")
	{
		$date = str_replace("/", "-", $date);
		$year = explode("-", $date);
		if(strlen($year[0]) == 4 )
		{
			$y = $year[0]; $m = $year[1]; $d = $year[2];
		}else if( strlen($year[2]) == 4 ){
			$y = $year[2]; $m = $year[1]; $d = $year[0];
		}	
		if($y > 2100){ $y -= 543; }
		$date = date("$y-$m-$d", strtotime($date));	
	}
	return $date;
}

function fromDate($date)
{
	return dbDate($date)." 00:00:00";
}

function toDate($date)
{
	return dbDate($date)." 23:59:59";	
}

function NOW()
{
	date_default_timezone_set('Asia/Bangkok');
	return date("Y-m-d H:i:s");	
}

function beforeDate($days = 0, $date = '')
{
	if( $date === ''){ $date = NOW(); }
	return date('Y-m-d H:i:s', strtotime("-$days day $date"));	
}

function dateDiff($from, $to)
{
	$fdate = strtotime($from);
	$tdate = strtotime($to);	
	$diff = ($tdate - $fdate)/(3600*24);
	return round($diff);
}
?>