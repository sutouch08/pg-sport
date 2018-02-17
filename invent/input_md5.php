<html>
<head>
<title>md5</title>
</head>
<body>
<form action="md5.php" method="post">
<input type="text" name="input" /><input type="submit" value="submit" />
</form>
<?php 
if(isset($_GET['md5'])){
	echo $_GET['md5'];
}
?>

</html>