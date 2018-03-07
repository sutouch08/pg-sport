<?php
require '../../library/config.php';
require '../../library/functions.php';
require '../../invent/function/tools.php';

if(isset($_GET['saleSummary']))
{
  include 'report/sale/sale_summary.php';
}


if(isset($_GET['saleByItem']) && isset($_GET['report']))
{
  include 'report/sale/sale_by_item.php';
}

if(isset($_GET['saleByItem']) && isset($_GET['export']))
{
  include 'report/sale/export_sale_by_item.php';
}


?>
