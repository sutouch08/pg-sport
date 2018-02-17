<?php
require "../../../library/config.php";
require "../../../library/functions.php";
require "../../function/tools.php";
require "../../function/report_helper.php";

//--- Report Received Product Summary
if( isset( $_GET['receivedByProduct'] ) && isset( $_GET['report'] ) )
{
	include 'report/reportReceivedByProduct.php';
}

//----- Export Received Product Summary
if( isset( $_GET['receivedByProduct'] ) && isset( $_GET['export'] ) )
{
	include 'export/exportReceivedByProduct.php';
}

?>