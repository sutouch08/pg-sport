<?php
require '../library/config.php';
require '../library/functions.php';
require "function/tools.php";
require "function/sponsor_helper.php";
require "function/support_helper.php";
require "function/lend_helper.php";
?>

<!DOCTYPE HTML>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="../favicon.ico" />
    <title>ทดสอบระบบ</title>

    <!-- Core CSS - Include with every page -->
    <link href="<?php echo WEB_ROOT; ?>library/css/bootstrap.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/paginator.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo WEB_ROOT; ?>library/css/bootflat.min.css" rel="stylesheet">
     <link rel="stylesheet" href="<?php  echo WEB_ROOT;?>library/css/jquery-ui-1.10.4.custom.min.css" />
     <script src="<?php echo WEB_ROOT; ?>library/js/jquery.min.js"></script>
    
  	<script src="<?php  echo WEB_ROOT;?>library/js/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="<?php echo WEB_ROOT; ?>library/js/bootstrap.min.js"></script>
     


<body>
<div class="container">
<form method="post">
<table width="500px;">
<tr>
	<td style="width:20%; text-align:center;"></td>
    <td align="center">A</td>
    <td align="center">B</td>
    <td align="center">C</td>
</tr>
<tr>
	<td align="center">M</td>
    <td align="center"><input type="text" name="qty[A][1]" class="form-control" /></td>
    <td align="center"><input type="text" name="qty[B][2]" class="form-control" /></td>
    <td align="center"><input type="text" name="qty[C][3]" class="form-control" /></td>
</tr>
<tr>
	<td align="center">M</td>
    <td align="center"><input type="text" name="qty[A][4]" class="form-control" /></td>
    <td align="center"><input type="text" name="qty[B][5]" class="form-control" /></td>
    <td align="center"><input type="text" name="qty[C][6]" class="form-control" /></td>
</tr>
<tr>
	<td align="center">M</td>
    <td align="center"><input type="text" name="qty[A][7]" class="form-control" /></td>
    <td align="center"><input type="text" name="qty[B][8]" class="form-control" /></td>
    <td align="center"><input type="text" name="qty[C][9]" class="form-control" /></td>
</tr>
</table>
<button type="submit">Sumit</button>
</form>
<?php
$pd = $_POST['qty'];

echo '<br/> ------------------- Unsorted -------------<br/>';
echo '<pre>'; 
print_r($pd);
echo '</pre>';		
echo '<br/> ------------------- Sorted -------------<br/>';


foreach( $pd as $ps)
{
	foreach( $ps as $key => $val)
	{
		echo $key .' => '.$val.'<br/>';	
	}
}
foreach( $pd as $ps )
{
	print_r($ps);	
}
?>


</div>

</body>

</html>
