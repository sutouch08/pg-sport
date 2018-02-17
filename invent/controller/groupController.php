<?php 
require "../../library/config.php";
require "../../library/functions.php";
include_once '../function/group_helper.php';


//--------------  ย้ายลูกค้าระหว่างกลุ่ม --------//
if( isset( $_GET['moveToGroup'] ) )
{
	$sc = 'success';
	$id_group	= $_GET['id_group'];
	$customer	= $_POST['customer'];
	$count		= count($customer);
	$i				= 1;
	$cus 			= '';
	foreach( $customer as $id => $val )
	{
		$cus .= $id;
		if( $i < $count ){ $cus .= ', '; }
		$i++;
	}
	$qs = dbQuery("UPDATE tbl_customer SET id_default_group = ".$id_group." WHERE id_customer IN(".$cus.")");
	if( ! $qs ){ $sc = 'fail'; }
	echo $sc;
}

if( isset( $_GET['addNewGroup'] ) )
{
	$sc 		= 'success';
	$name	= $_POST['group_name'];	
	$qs		= dbQuery("INSERT INTO tbl_group ( group_name, date_add, date_upd ) VALUES ('".$name."', NOW(), NOW())");
	if( ! $qs ){ $sc = 'fail'; }
	echo $sc;
}



if( isset( $_GET['updateGroupName'] ) )
{
	$sc		= 'success';
	$id 		= $_POST['id_group'];
	$name 	= $_POST['group_name'];	
	$qs 		= dbQuery("UPDATE tbl_group SET group_name = '".$name."' WHERE id_group = ".$id);
	if( ! $qs )
	{
		$sc = 'fail';
	}
	echo $sc;		
}


if( isset( $_GET['removeGroup'] ) )
{
	$sc 	= 'success';
	$id		= $_POST['id_group'];
	if( countMember($id) == 0 )
	{
		$qs = dbQuery("DELETE FROM tbl_group WHERE id_group = ".$id);
		if( ! $qs ){ $sc = 'ลบกลุ่มไม่สำเร็จ'; }
	}
	else
	{
		$sc = 'ไม่สามารถลบกลุ่มได้เนื่องจากมีสมาชิกอยู่ในกลุ่ม';
	}
	echo $sc;	
}

if( isset( $_GET['validGroupName'] ) )
{
	$sc = 0;
	$id 	= isset( $_POST['id_group'] ) ? $_POST['id_group'] : '';
	if( $id != '' )
	{
		$qs = dbQuery("SELECT * FROM tbl_group WHERE group_name = '".$_POST['group_name']."' AND id_group != ".$id);	
	}
	else
	{
		$qs = dbQuery("SELECT * FROM tbl_group WHERE group_name = '".$_POST['group_name']."'");	
	}
	if( dbNumRows($qs) > 0 ){ $sc = 1; }
	echo $sc;	
}

///////////////  ตรวจสอบชื่อกลุ่มซ้ำ //////////////////
if(isset($_GET['group_name'])){
	$group_name = $_GET['group_name'];
	$row = dbNumRows(dbQuery("SELECT group_name FROM tbl_group WHERE group_name = '$group_name'")); ///ตรวจสอบชื่อซ้ำ
	if($row>0){
		$message = 1;
	}else if($row<1){
		$message = 0;
	}
	echo $message;
}

///////////// เพิ่มกลุ่มใหม่ ////////////////////
if(isset($_GET['add'])&&isset($_POST['group_name'])){
	$group_name = $_POST['group_name'];
	$show_price = $_POST['show_price'];
	$date_add = date('Y-m-d');
	$date_upd = date("Y-m-d");
	$row = dbNumRows(dbQuery("SELECT group_name FROM tbl_group WHERE group_name = '$group_name'")); ///ตรวจสอบชื่อซ้ำ
	if($row<1){
		if(dbQuery("INSERT INTO tbl_group(group_name, show_price, date_add, date_upd) VALUES ('$group_name', $show_price, '$date_add', '$date_upd')")){
			$message = "เพิ่มกลุ่มเรียบร้อยแล้ว";
			header("location: ../index.php?content=group&message=$message");
		}else{
			$message = "เพิ่มกลุ่มไม่สำเร็จ";
			header("location: ../index.php?content=group&error=$message");
		}
	}else if($row>0){
		$message = "กลุ่มซ้ำ กรุณาใช้ชื่ออื่น";
		header("location: ../index.php?content=group&add=y&error=$message&group_name=$group_name&discount=$discount&show_price=$show_price");
	}
}
if(isset($_GET['edit'])&&isset($_POST['id_group'])){
	$id_group = $_POST['id_group'];
	$group_name = $_POST['group_name'];
	$show_price = $_POST['show_price'];
	$row = dbNumRows(dbQuery("SELECT group_name FROM tbl_group WHERE group_name = '$group_name' AND id_group != $id_group"));
	if($row < 1 ){
		if(dbQuery("UPDATE tbl_group SET group_name = '$group_name', show_price = $show_price WHERE id_group = $id_group")){
			$message = "แก้ไขกลุ่มเรียบร้อยแล้ว";
			header("location: ../index.php?content=group&message=$message");
		}else{
			$message = "แก้ไขกลุ่มไม่สำเร็จ";
			header("location: ../index.php?content=group&error=$message");
		}
	}else if($row > 0){
		$message = "ชื่อกลุ่มซ้ำ กรุณาใช้ชื่ออื่น";
		header("location: ../index.php?content=group&edit=y&id_group=$id_group&error=$message");
	}
}
/////////////////// ลบกลุ่ม  ////////////////////////////
if(isset($_GET['delete'])&&isset($_GET['id_group'])){
	$id_group = $_GET['id_group'];
	if(dbQuery("DELETE FROM tbl_group WHERE id_group = $id_group")){
		if(dbQuery("DELETE FROM tbl_customer_group WHERE id_group = $id_group")){
			if(dbQuery("UPDATE tbl_customer SET id_default_group = 1 WHERE id_default_group = $id_group")){
				$message = "ลบกลุ่มเรียบร้อยแล้ว";
				header("location: ../index.php?content=group&message=$message");
			}else{
				$message = "ลบกลุ่มและความเชื่อมโยงสำเร็จ แต่เปลี่ยนกลุ่มหลักในตาราง ลูกค้าไม่สำเร็จ";
				header("location: ../index.php?content=group&error=$message");
			}
		}else{
			$message = "ลบกลุ่มสำเร็จแต่ลบความสัมพันธ์ของกลุ่มไม่สำเร็จ";
			header("location: ../index.php?content=group&error=$message");
		}
	}else{
		$message = "ลบกลุ่มไม่สำเร็จ";
		header("location: ../index.php?content=group&error=$message");
	}
}

?>