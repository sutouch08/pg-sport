<?php
require "../../library/config.php";
require "../../library/functions.php";
///////////////////  AutoComplete //////////////////////
if(isset($_REQUEST['term'])){
	$qstring = "SELECT email FROM tbl_customer WHERE email LIKE '%".$_REQUEST['term']."%'";
	$result = dbQuery($qstring);//query the database for entries containing the term
if ($result->num_rows>0)
	{
		$data= array();
	while($row = $result->fetch_array())//loop through the retrieved values
		{
				$data[] = $row['email'];
		}
		echo json_encode($data);//format the array into json data
	}else {
		echo "error";
	}

}
?>