<?php 
	$page_menu = "invent_stock_report";
	$page_name = "ส่งออกฐานข้อมูลสินค้า";
	$id_profile = $_COOKIE['profile_id'];
	function category_name($id_category)
	{
		$name = "";
		$qs = dbQuery("SELECT category_name FROM tbl_category WHERE id_category = ".$id_category);
		if(dbNumRows($qs) == 1 )
		{
			list($name) = dbFetchArray($qs);
		}
		return $name;
	}
	if( isset($_GET['clear_filter']) )
	{
		setcookie("db_search_text", "", time()-3600, "/");
	}
?>
<div class="container">

<div class="row" style="height:35px;">
	<div class="col-lg-8" style="padding-top:10px;"><h4 class="title"><i class="fa fa-file-text-o"></i> <?php echo $page_name; ?></h4></div>
    <div class="col-lg-4">
   		<p class="pull-right" style="margin-bottom:0px;">
        	<button class="btn btn-info btn-sm" type="button" onclick="do_export_all()"><i class="fa fa-file-text-o"></i> ส่งออกรายการทั้งหมด</button>
        	<button class="btn btn-success btn-sm" type="button" onclick="do_export()"><i class="fa fa-file-text-o"></i> ส่งออกรายการที่เลือก</button>     
        </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:10px;' />
<?php
		if( isset($_POST['search_text']) && $_POST['search_text'] != "" )
		{
			$text = $_POST['search_text'];
			setcookie("db_search_text", $text, time()+3600, "/");	
		}
		else if( isset($_COOKIE['db_search_text']) )
		{
			$text = $_COOKIE['db_search_text'];
		}
		else
		{
			$text = "";
		}		
		
		if( isset($_POST['from_date']) && $_POST['from_date'] != "")
		{
			$from_date = $_POST['from_date'];
			setcookie("db_from_date", $from_date, time()+3600, "/");
		}
		else if( isset($_COOKIE['db_from_date']) )
		{
			$from_date = $_COOKIE['db_from_date'];
		}
		else
		{
			$from_date = "";
		}
		if( isset( $_POST['to_date']) && $_POST['to_date'] != "")
		{
			$to_date = $_POST['to_date'];
			setcookie("db_to_date", $to_date, time()+3600, "/");
		}
		else if( isset( $_COOKIE['db_to_date']) )
		{
			$to_date = $_COOKIE['db_to_date'];
		}
		else
		{
			$to_date = "";
		}
		
?>		

<div class="row">
	<form id="search_form" method="post">
    <div class="col-lg-2">
        <input type="text" class="form-control input-sm" id="from_date" name="from_date" value="<?php echo $from_date; ?>" placeholder="วันที่เพิ่ม : จากวันที่" />
    </div>
    <div class="col-lg-2">
        <input type="text" class="form-control input-sm" id="to_date" name="to_date" value="<?php echo $to_date; ?>" placeholder="วันที่เพิ่ม : ถึงวันที่" />
    </div>
	<div class="col-lg-4">
        <input type="text" class="form-control input-sm" id="search_text" name="search_text" placeholder="ระบุรุ่นสินค้าที่ต้องการค้นหา" autofocus="autofocus" value="<?php echo $text; ?>" />
    </div>
    <div class="col-lg-2">
        <button class="btn btn-primary btn-sm btn-block"  id="btn_search" onclick="get_search()"><i class="fa fa-search"></i> ค้นหา</button>
    </div>
    </form>
    <div class="col-lg-2">
        <button type="button" class="btn btn-warning btn-sm btn-block"  id="btn_reset" onclick="clear_filter()"><i class="fa fa-refresh"></i> เคลียร์ตัวกรอง</button>
    </div>
</div>

<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:0px;' />
<div class="row">
<?php 
	$date = "";
	if( $from_date != "" && $to_date != "")
	{
		$date = " AND ( date_add BETWEEN '".fromDate($from_date)."' AND '".toDate($to_date)."') ";
	}
	if( $text != "" )
	{
		$where = " WHERE product_code LIKE '%".$text."%' OR product_name LIKE '%".$text."%'".$date." ORDER BY date_add DESC";
	}
	else
	{
		$where = " WHERE id_product != 0".$date." ORDER BY date_add DESC";
	}
	
	$paginator = new paginator();
	if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
	$paginator->Per_Page("tbl_product", $where, $get_rows);
	$paginator->display($get_rows, "index.php?content=export_product_db");
	$Page_Start = $paginator->Page_Start;
	$Per_Page = $paginator->Per_Page;
?>   
<style>
	.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td 
	{
		vertical-align:middle;
	}
</style>
<?php $qs = dbQuery("SELECT * FROM tbl_product ".$where." LIMIT ".$Page_Start.", ".$Per_Page); ?>
<?php if( dbNumRows($qs) > 0 ) : ?>
<form id="export_form" method="post" action="controller/exportController.php?export_product">
<div class="col-lg-12">
<table class="table table-striped">
	<thead style="font-size:14px;">
    	<th style="width: 5%; text-align:center;">ID</th>
        <th style="width: 15%;">รหัสสินค้า</th>
        <th style="width: 25%;">ชื่อสินค้า</th>
        <th style="width: 15%;">หมวดหมู่</th>
      	<th style='width: 10%; text-align:center;'>ราคาทุน</th>
        <th style='width: 10%; text-align:center;'>ราคาขาย</th>
        <th style='text-align:center;'><label><input type="checkbox" id="check_all" style="margin-right:5px;" />เลือกทั้งหมด</label></th>
    </thead>
    <tbody>
<?php while( $rs = dbFetchArray($qs) ) : 	?>
<?php 	$id = $rs['id_product']; 				?>	
		<tr id="row_<?php echo $id; ?>" style="font-size:12px;">
        	<td align="center"><?php echo $id; ?></td>
            <td><?php echo $rs['product_code']; ?></td>
            <td><?php echo $rs['product_name']; ?></td>
            <td><?php echo category_name($rs['default_category_id']); ?></td>
            <td align="center"><?php echo number_format($rs['product_cost'], 2); ?></td>
            <td align="center"><?php echo number_format($rs['product_price'], 2); ?></td>
            <td align="center">
            <label style="margin: 0px; padding: 2px; color:#693">
            <input type="checkbox" class="check_me" name="export[<?php echo $id; ?>]" id="export_<?php echo $id; ?>" value="<?php echo $id; ?>" />&nbsp; เลือก
            </label>
            </td>  
        </tr>
<?php endwhile; ?>
    </tbody>    
</table>
</div>
</form>
<?php else : ?>
	<div class="col-lg-12"><center><h4> ----- ไม่พบรายการสินค้า  -----</h4></center></div>
<?php endif; ?>
</div>
</div>   <!-- End container --> 
<script>
$("#check_all").change(function(e) {
    if($(this).is(":checked")){
		$("input[type='checkbox']").each(function(index, element) {
            $(this).prop("checked",true);
        });
	}else{
		$("input[type='checkbox']").each(function(index, element) {
            $(this).prop("checked",false);
        });
	}
});

function clear_filter()
{
	$.ajax({
		url:"controller/exportController.php?clear_filter",
		type: "GET", cache: "false", success: function(rs)
		{
			window.location.href = "index.php?content=export_product_db";
		}
	});
}
function do_export()
{
	$("#export_form").submit();	
}

function do_export_all()
{
	var token = new Date().getTime();
	get_download(token);
	window.location.href = "controller/exportController.php?export_all&token="+token;
}
$(document).ready(function(e) {
    $("#from_date").datepicker({
		dateFormat: "dd-mm-yy",
		onClose: function(selectedDate){
			$("#to_date").datepicker( "option", "minDate",selectedDate );
		}
	});
	$("#to_date").datepicker({
		dateFormat: "dd-mm-yy",
		onClose: function(selectedDate){
			$("#from_date").datepicker("option", "maxDate", selectedDate);
		}
	});
});

function get_search()
{
	var from = $("#from_date").val();
	var to 	= $("#to_date").val();
	if( from != "" && to == ""){ swal("วันที่ไม่ถูกต้อง"); return false; }
	if( from =="" && to != ""){ swal("วันที่ไม่ถูกต้อง"); return false; }
	$("#search_form").submit();	
}
</script>