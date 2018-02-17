<?php 
require "../../library/config.php";
require "../../library/functions.php";
		
if(isset($_GET['id_profile']))
{
	$id_profile 	= $_GET['id_profile'];
	$views 		= $_POST['view'];
	$adds 		= $_POST['add'];
	$edits 		= $_POST['edit'];
	$deletes 		= $_POST['delete'];
	$tab 			= $_POST['tab'];

	foreach($tab as $id_tab)
	{
		$view = (isset($views[$id_tab]) ? 1 : 0 ); 
		$add = (isset($adds[$id_tab]) ? 1 : 0 ); 
		$edit = (isset($edits[$id_tab]) ? 1 : 0 );
		$delete = (isset($deletes[$id_tab]) ? 1 : 0 );
		$rs = dbQuery("SELECT tbl_access.view FROM tbl_access WHERE id_tab = '$id_tab' AND id_profile = '$id_profile'");
		$row = dbNumRows($rs);
		if($row >0){
			dbQuery("UPDATE tbl_access SET `view` = ".$view.", `add` = ".$add.", `edit` = ".$edit.", `delete` = ".$delete." WHERE id_profile = ".$id_profile." AND id_tab = ".$id_tab);
		}else{
			dbQuery("INSERT INTO tbl_access (id_profile, id_tab, tbl_access.view, tbl_access.add, tbl_access.edit, tbl_access.delete) VALUES (".$id_profile.", ".$id_tab.", ".$view.", ".$add.", ".$edit.", ".$delete.")");
		}
		
	}
	$message = "แก้ไขการกำหนดสิทธิ์เรียบร้อยแล้ว";
	header("location: ../index.php?content=securable&edit_right&id_profile=".$id_profile."&message=".$message);
	
}

?>