<?php 
require "../../library/config.php";
if(isset($_GET['add'])&& $_POST['category_name']){
	$category_name = $_POST['category_name'];
	$active = $_POST['active'];
	$max = 4;
	$parent_category = $_POST['root_category'];
	list($level) = dbFetchArray(dbQuery("SELECT level_depth FROM tbl_category WHERE id_category = $parent_category"));
	if($level < $max){ $depth = $level+1; }else{ $depth = $level; }
	$description = $_POST['description'];
	$qr = dbFetchArray(dbQuery("select max(position) as max from tbl_category"));
	$position = $qr['max']+1;
	$sql = "INSERT INTO tbl_category (category_name, description, parent_id, level_depth, position, date_add, active) VALUES ('$category_name', '$description', $parent_category,$depth, $position, now(), $active)";
	dbQuery($sql); 
	if(isset($_POST['groupcheck'])){
		$checked = $_POST['groupcheck'];
		$qs = dbFetchArray(dbQuery("SELECT max(id_category) as max FROM tbl_category"));
		$id_category = $qs['max'];
		foreach($checked as $id_group){
			$sqr="INSERT INTO tbl_category_group (id_category, id_group) VALUES ($id_category, $id_group)";
			dbQuery($sqr);
		}
	}
	header("location: ../index.php?content=category");
	
}
if(isset($_GET['edit'])&& $_POST['id_category']){
	$id_category = $_POST['id_category'];
	$max = 4;
	$category_name = $_POST['category_name'];
	$active = $_POST['active'];
	$parent_category = $_POST['root_category'];
	$description = $_POST['description'];
	$position = $_POST['position'];
	list($level) = dbFetchArray(dbQuery("SELECT level_depth FROM tbl_category WHERE id_category = $parent_category"));
	if($level < $max){ $depth = $level+1; }else{ $depth = $level; }
	$sql = "UPDATE tbl_category SET category_name='$category_name' , description='$description', parent_id=$parent_category, level_depth = $depth, position=$position, active=$active WHERE id_category=$id_category";
	dbQuery($sql); 
	if(isset($_POST['groupcheck'])){
		dbQuery("DELETE FROM tbl_category_group WHERE id_category = $id_category");
		$checked = $_POST['groupcheck'];
		foreach ($checked as $id_group) {
		dbQuery("INSERT INTO tbl_category_group (id_category, id_group) VALUES ($id_category, $id_group)");
		}
	}
	header("location: ../index.php?content=category");
}
if(isset($_GET['delete'])){
	$id_category = $_GET['id_category'];
	dbQuery("DELETE FROM tbl_category WHERE id_category = $id_category");
	dbQuery("DELETE FROM tbl_category_group WHERE id_category = $id_category");
	header("location: ../index.php?content=category");
}
	

?>