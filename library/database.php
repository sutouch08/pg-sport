<?php
require_once 'config.php';

$dbConn = mysqli_connect ($dbHost, $dbUser, $dbPass, $dbName) or die ('MySQL connect failed. ' . mysql_error());
mysqli_query($dbConn,'SET NAMES utf8');
date_default_timezone_set('Asia/Bangkok');

function dbQuery($sql)
{
	global $dbConn;
	$result = mysqli_query($dbConn,$sql);
	return $result;
}

function dbAffectedRows()
{
	global $dbConn;
	
	return mysqli_affected_rows($dbConn);
}

function dbFetchArray($result) {
	return mysqli_fetch_array($result);
}

function dbFetchAssoc($result)
{
	return mysqli_fetch_assoc($result);
}

function dbFetchRow($result) 
{
	return mysqli_fetch_row($result);
}

function dbFreeResult($result)
{
	return mysqli_free_result($result);
}

function dbFetchObject($result)
{
	return mysqli_fetch_object($result);	
}

function dbNumRows($result)
{
	return mysqli_num_rows($result);
}

function dbSelect($dbName)
{
	return mysqli_select_db($dbName);
}

function dbInsertId()
{
	global $dbConn;
	return mysqli_insert_id($dbConn);
}
function startTransection()
{
	global $dbConn;
	return mysqli_autocommit($dbConn, FALSE);
}

function endTransection()
{
	global $dbConn;
	return mysqli_autocommit($dbConn, TRUE);	
}

function commitTransection()
{
	global $dbConn;
	return mysqli_commit($dbConn);
}

function dbRollback()
{
	global $dbConn;
	return mysqli_rollback($dbConn);
}
?>