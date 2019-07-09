<?php
include "../../config.php";
include "../../functions.php";

$codeType = getConfig('BARCODE_TYPE');

$version = phpversion();

if($version > 6)
{
	$barcode_file = $codeType.'.php';
	include 'PHP7/code/'.$barcode_file;
}
else
{
	include 'barcode_v5.php';
}

?>
