<?php
	$page_menu = "invent_product_in";
	$page_name = "โอนคลัง";
	$id_tab = 43;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
  	if($add==1){ $can_add = "";}else{ $can_add = "style='display:none;'"; }
	if($edit==1){ $can_edit = "";}else{ $can_edit = "style='display:none;'"; }
	if($delete==1){ $can_delete = "";}else{ $can_delete ="style='display:none;'"; }
	$btn = "";
	if( isset($_GET['edit']) && isset($_GET['id_received_product']) ) :
		$btn = "<a href='index.php?content=product_in'><button type='button' class='btn btn-warning btn-sm'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		$btn .= "<button type='button' class='btn btn-success btn-sm' style='margin-left:10px;' onclick='edit_stock()'><i class='fa fa-save'></i>&nbsp; บันทึก</button>";
	elseif( isset($_GET['edit']) || isset($_GET['add']) ) :
		$btn = "<a href='index.php?content=tranfer'><button type='button' class='btn btn-warning btn-sm'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
		if( isset($_GET['id_tranfer']) ) :
			$id_tranfer = $_GET['id_tranfer'];
			$btn .= "<a href='controller/tranferController.php?print=y&id_tranfer=".$id_tranfer."' style='text-decoration:none; margin-left:10px;'><button type='button' class='btn btn-success btn-sm'><i class='fa fa-print'></i>&nbsp;พิมพ์</button></a>";
		endif;
	elseif( isset($_GET['view_detail']) ) :
		$btn = "<a href='index.php?content=tranfer'><button type='button' class='btn btn-warning btn-sm'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
	else :
		if($add) :
			$btn = "<a href='index.php?content=tranfer&add=y'><button type='button' class='btn btn-success btn-sm' ><i class='fa fa-plus'></i>&nbsp;เพิ่มใหม่</button></a>";
		endif;
	endif;
	?>

<div class="container">
<!-- page place holder -->

<div class="row" style="height:35px;">
	<div class="col-xs-6" style="margin-top:10px;">
    	<h4 class="title"><i class="fa fa-upload"></i>&nbsp;<?php echo $page_name; ?></h4>
	</div>
    <div class="col-xs-6">
      	<p class="pull-right" style="margin-bottom:0px;">
        	<?php echo $btn; ?>
        </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php

if(isset($_GET['add'])){
		if(!isset($_GET['id_tranfer'])){
			$reference = get_max_role_reference_tranfer("PREFIX_TRANFER");
			$user_id = $_COOKIE['user_id'];
			$active = "";
			$customer_name = "";
			$comment = "";
			$warehouse_from = "";
			$warehouse_to = "";
		}else{
			$id_tranfer = $_GET['id_tranfer'];
			list($reference,$warehouse_from,$warehouse_to,$date_add,$comment) = dbFetchArray(dbQuery("SELECT reference,warehouse_from,warehouse_to,date_add,comment FROM tbl_tranfer WHERE id_tranfer = $id_tranfer"));
			$active = "disabled='disabled'";
		}
function select_zone_consign($selected=""){
	echo"<option value='0'>-------เลือกพื้นที่--------</option>";
	$sql = dbQuery("SELECT * FROM tbl_zone WHERE id_warehouse = 2");
	while($rs = dbFetchArray($sql)){
		$id_zone = $rs['id_zone'];
		$zone_name = $rs['zone_name'];
		if($selected==$id_zone){ $select = "selected='selected'";}else{ $select = "";}
		echo"<option value='$id_zone' $select>$zone_name</option>";
	}
}
	/////////  เพิ่มออเดอร์ ID ใหม่
echo "<form id='add_order_form' action='controller/tranferController.php?add=y' method='post'>
	<div class='row'><input type='hidden' name='id_employee' value='$user_id' />
	<div class='col-xs-3'><div class='input-group'><span class='input-group-addon'>เลขที่เอกสาร</span><input type='text' id='doc_id' class='form-control' value='$reference' disabled='disabled'/></div> </div>
	<div class='col-xs-2'><div class='input-group'><span class='input-group-addon'>วันที่</span><input type='text' id='doc_date' name='doc_date' class='form-control' value='".date('d-m-Y')."' $active/></div> </div>

	<div class='col-xs-3'><div class='input-group'>
						<span class='input-group-addon'>ย้ายจาก</span>";
						echo"
						<select class='form-control' name='warehouse_from' $active>"; warehouseList($warehouse_from); echo"</select>
						</div></div>
	<div class='col-xs-3'><div class='input-group'>
						<span class='input-group-addon'>ไปที่</span>";
						echo"
						<select class='form-control' name='warehouse_to' $active>"; warehouseList($warehouse_to); echo"</select>
						</div></div></div>
						<div class='row' style='margin-top:15px;'>
	<div class='col-xs-6'><div class='input-group'><span class='input-group-addon'>หมายเหตุ</span><input type='text' id='comment' name='comment' class='form-control' value='$comment' autocomplete='off' $active/></div> </div>
	<div class='col-xs-2'><button class='btn btn-default' type='submit' id='add_order' $active>&nbsp&nbsp;เพิ่ม&nbsp;&nbsp</button></div>
	</div></form>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />";
	if(isset($id_tranfer)){
	echo "<div class='row'><div class='col-xs-10'><div id='from_zone' style='display:none;'>
		<table border='0' width='80%' align='center' id='from_con'>
			<tr>
				<td width='65%'>
					<div class='input-group'>
						<span class='input-group-addon'>ชื่อโซน,บาร์โค้ดโซน ที่ออก</span>
						<input type='text' name='zone' id='zone' class='form-control' placeholder='' required autofocus >
					</div>
				</td>
				<td width='15%' align='center'><div class='input-group' id='load'><input type='submit' id='submit' class='btn btn-primary' value='ตกลง' onclick='check_zone()' /></div></td>
				<td width='20%' align='right'></td>
			</tr>
		</table>
	</div>
	<div id='from_move_all' style='display:none;'>
		<table border='0' width='80%' align='center' id='from_con'>
			<tr>
				<td width='65%'>
					<div class='input-group'>
						<span class='input-group-addon'>ชื่อโซน,บาร์โค้ดโซน ที่เข้า</span>
						<input type='text' name='zone_in_all' id='zone_in_all' class='form-control' placeholder='' required autofocus >
					</div>
				</td>
				<td width='15%' align='center'><div class='input-group' id='load4'><input type='submit' id='submit' class='btn btn-primary' value='ตกลง' onclick='move_all()' /></div></td>
				<td width='20%' align='right'></td>
			</tr>
		</table>
	</div>
	<div id='from_move_in' style='display:none;'>
		<table border='0' width='100%' align='center' id='from_con'>
			<tr>
				<td width='35%'>
					<div class='input-group'>
						<span class='input-group-addon'>สินค้า</span>
						<input type='text' id='name_item' class='form-control' placeholder='' disabled='disabled' >
					</div>
					</td>
					<td width='5%' align='center'></td>
				<td width='50%' align='center'><div class='input-group'>
						<span class='input-group-addon'>เข้าโซน ชื่อโซน,บาร์โค้ดโซน</span>
						<input type='text' name='zone' id='zone_in' class='form-control' placeholder='' required autofocus >
					</div></td>
				<td width='15%' align='right'><div class='input-group' id='load2'><input type='submit' id='submit' class='btn btn-primary' value='ตกลง' onclick='move_in()' /></div></td>
			</tr>
		</table>
	</div>

	<div id='from_out' style='display:none;' >
		<table border='0' width='100%' align='center' id='from_con'>
			<tr>
			<td width='15%' align='center'>
			<div class='input-group'><input type='checkbox' id='checkboxes' onclick='check_import()' /><label for='auto_zone' style='margin-left:10px;'>ย้ายทั้งหมด</label></div>
			</td>
			<td width='75%' align='center'>
			<div id='move_out1'><table border='0' width='100%' align='center' id='from_con'>
			<tr>
			<td width='20%'>
				<div class='input-group'>
					<span class='input-group-addon'>จำนวน</span>
					<input type='text' name='qty' id='qty' class='form-control' placeholder='' value='1'  >

				</div>
			</td>
			<td width='10%' style='padding-left: 15px;'><label><input type='checkbox' id='check_zero' style='margin-right: 5px;' />ติดลบได้</label><input type='hidden' id='allow_under_zero' value='0' /></td>
			<td width='35%'><div class='input-group'><span class='input-group-addon'>บาร์โค้ด</span><input type='text' name='barcode_item' id='barcode_item' class='form-control' ></div></td>
			<td width='10%' align='center'><div id='load1'><input type='submit' class='btn btn-primary' onclick='moveout()' value='ย้าย' /></div></td>
			</tr>
			</table>
			</div>

			<div id='move_out2' style='display:none;' ><table border='0' width='100%' align='center' id='from_con'>
			<tr><td width='20%'><div id='load3'><input type='submit' class='btn btn-primary' onclick='moveout_all()' value='ดำเนินการย้าย' /></div></td><td width='5%'></td><td width='40%'></td><td width='10%' align='center'></td></tr></table></div>
			</td>
			<td width='10%' align='right'><input type='submit' id='change_zone' class='btn btn-default' onclick='new_zone()' value='โซนไหม่ (F2)' /></td></tr>
		</table>
	</div>
	<div id='menu_main' style='' >
		<input type='submit' id='item_move' class='btn btn-default' onclick='menu_main()' value='ย้ายสินค้าออก' />&nbsp;&nbsp;<input type='submit' id='item_move' class='btn btn-default' onclick='click_move_all()' value='ย้ายสินค้าเข้าทั้งหมด' />
	</div>
	</div>
	<div class='col-xs-2'>
		<input type='submit' id='item_move' class='btn btn-default' onclick='item_move()' value='สินค้าที่ย้าย' />
	</div></div>
	<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
	<input type='hidden' id='id_zone'  ><input type='hidden' id='id_tranfer' value='$id_tranfer' ><input type='hidden' id='id_product_attribute'  ><input type='hidden' id='qty_in'  >
	<div id='tables'>
	<table class='table table-bordered'>
	<thead id='head'>
		<th style='width:5%;' >ลำดับ</th>
		<th style='width:15%;'>บาร์โค้ด</th>
		<th style='width:30%;'>รหัสสินค้า</th>
		<th style='width:10%;text-align:center;'>ย้ายจาก</th>
		<th style='width:10%;text-align:center;'>ไปที่</th>
		<th style='width:10%;text-align:center;'>จำนวน</th>
		<th  style='width:10%; text-align:center;'>การกระทำ</th>
	</thead>";
				$sql = dbQuery("SELECT id_tranfer_detail,id_product_attribute,id_zone_from,id_zone_to,tranfer_qty,valid FROM tbl_tranfer_detail WHERE id_tranfer = '$id_tranfer' ORDER BY id_product_attribute ASC");
				$row = dbNumRows($sql);
				$n = 1;
				$i = 0;
				if($row>0){
				while($i<$row){
					list($id_tranfer_detail,$id_product_attribute,$id_zone_from,$id_zone_to,$tranfer_qty,$valid)= dbFetchArray($sql);
					$product = new product();
					$product->product_attribute_detail($id_product_attribute);
					$reference = $product->reference;
					$barcode = $product->barcode;
					list($name_zone_from) = dbFetchArray(dbQuery("SELECT zone_name FROM tbl_zone WHERE id_zone = $id_zone_from"));
					if($valid == "0"){
						$name_zone_to = "<button type='button' class='btn btn-link' onclick=\"click_move_in($id_product_attribute,'$reference',$tranfer_qty)\"><span class='glyphicon glyphicon-log-in' style='color:#5cb85c; font-size:16px;'></span></button>";
					}else{
						list($name_zone_to) = dbFetchArray(dbQuery("SELECT zone_name FROM tbl_zone WHERE id_zone = $id_zone_to"));
					}
					echo"<tr id='row$id_product_attribute'><td style='text-align:center; vertical-align:middle;'>$n</td><td style='vertical-align:middle;'>$barcode</td><td style='vertical-align:middle;'>$reference</td><td style='text-align:center; vertical-align:middle;'>$name_zone_from</td><td style='text-align:center; vertical-align:middle;'>$name_zone_to</td><td style='text-align:center; vertical-align:middle;'>$tranfer_qty</td>

			<td align='center'>
					<button class='btn btn-danger btn-xs' onclick='delete_detail($id_tranfer_detail)'>
						<span class='glyphicon glyphicon-trash' style='color: #fff;'></span>
					</button>
			</td></tr>";
					$i++;
					$n++;	}
				}else{
					echo"<tr id='row'><td style='text-align:center; vertical-align:middle;' colspan='7' align='center'><h4>ไม่มีรายการสินค้าที่ย้าย</h4></td></tr>";
				}echo "<div></div>";
	}
}else{

	$paginator = new paginator();
	$get_rows = isset( $_POST['get_rows'] ) ? $_POST['get_rows'] : ( getCookie('get_rows') ? getCookie('get_rows') : 50);

	$where = "WHERE id_tranfer != 0 ORDER BY id_tranfer DESC";
	$page  = isset($_GET['Page']) ? $_GET['Page'] : 1;
	

?>

<div class='row'>
<div class='col-xs-12'>

<?php $paginator->Per_Page("tbl_tranfer",$where,$get_rows); ?>
<?php $paginator->display($get_rows,"index.php?content=tranfer"); ?>

	<table class='table table-striped table-hover'>
    	<thead style='background-color:#48CFAD;'>
        	<th style='width:5%; text-align:center;'>ลำดับ</th>
			<th style='width:15%;'>อ้างอิง</th>
          	<th style='width:10%; text-align:center;'>ย้ายจาก</th>
			<th style='width:10%; text-align:center;'>ไปที่</th>
			<th style='width:10%; text-align:center;'>พนักงาน</th>
			<th style='width:10%; text-align:center;'>วันที่</th>
			<th style='width:10%; text-align:center;'>สถานะ</th>
			<th style='width:10%; text-align:center;'>การกระทำ</th>
        </thead>
<?php 	$qs = dbQuery("SELECT * FROM tbl_tranfer ". $where ." LIMIT ".$paginator->Page_Start.", ".$paginator->Per_Page); 	?>
<?php 	if( dbNumRows($qs) > 0 ) : ?>
<?php 		$n = ($page -1) * $paginator->Per_Page + 1; ?>
<?php 		while($rs = dbFetchObject($qs)) : ?>
		<tr>
			<td align='center' style='cursor:pointer;' onclick="getEdit(<?php echo $rs->id_tranfer; ?>)"> <?php echo $n; ?></td>
			<td style='cursor:pointer;' onclick="getEdit(<?php echo $rs->id_tranfer; ?>)"><?php echo $rs->reference; ?></td>
			<td align='center' style='cursor:pointer;' onclick="getEdit(<?php echo $rs->id_tranfer; ?>)"><?php echo get_warehouse_name_by_id($rs->warehouse_from); ?></td>
			<td align='center' style='cursor:pointer;' onclick="getEdit(<?php echo $rs->id_tranfer; ?>)"><?php echo get_warehouse_name_by_id($rs->warehouse_to); ?></td>
			<td align='center' style='cursor:pointer;' onclick="getEdit(<?php echo $rs->id_tranfer; ?>)"><?php echo employee_name($rs->id_employee); ?></td>
			<td align='center' style='cursor:pointer;' onclick="getEdit(<?php echo $rs->id_tranfer; ?>)"><?php echo thaiDate($rs->date_add); ?></td>
			<td align='center' style='cursor:pointer;' onclick="getEdit(<?php echo $rs->id_tranfer; ?>)">
				<?php $rows = dbNumRows(dbQuery("SELECT id_product_attribute FROM tbl_tranfer_detail WHERE id_tranfer = ".$rs->id_tranfer." AND valid = 0")); ?>
				<?php  if($rows > 0) : ?>
					<span style='color:red;'>ไม่สมบูรณ์</span>
				<?php  endif; ?>
			</td>
			<td align='center'>
			<?php if($edit) : ?>
				<a href='index.php?content=tranfer&add=y&id_tranfer=<?php echo $rs->id_tranfer; ?>'><button class='btn btn-warning btn-sx'><i class='fa fa-pencil'></i></button></a>
			<?php endif; ?>
			<?php if($delete) : ?>
				<button class='btn btn-danger btn-sx' onclick='delete_tranfer(<?php echo $rs->id_tranfer; ?>)'><i class='fa fa-trash'></i>	</button>
			<?php endif; ?>
			</td>
		</tr>
		<?php $n++;	 ?>
<?php 		endwhile; ?>	
<?php 	endif; ?>		
    </table>
</div> 
</div>
<?php 
}

?>
</div>
<script language="javascript">

function getEdit(id)
{
	window.location.href = "index.php?content=tranfer&add=y&id_tranfer="+id;
}
$(function() {
    $("#from_date").datepicker({
      dateFormat: 'dd-mm-yy', onClose: function( selectedDate ) {
        $( "#to_date" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to_date" ).datepicker({
      dateFormat: 'dd-mm-yy',   onClose: function( selectedDate ) {
        $( "#from_date" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
  $(function() {
    $("#doc_date").datepicker({
      dateFormat: 'dd-mm-yy'
    });
  });
   function validate() {
	var from_date = $("#from_date").val();
	var to_date = $("#to_date").val();
	if(from_date =="เลือกวัน"){
		alert("คุณยังไม่ได้เลือกช่วงเวลา");
		}else if(to_date ==""){
		alert("คุณยังไม่ได้เลือกวันสุดท้าย");
	}else{
		$("#form").submit();
	}
}
//// เมื่อยิงบาร์โค้ด หรือ ใส่รหัสด้วยมือแล้ว enter////
///************  บาร์โค้ดโซน **********************//
$("#zone").bind("enterKey",function(){
	if($("#zone").val() != ""){
		check_zone();
	}else{
		alert("ยังไม่ได้ใส่ชื่อหรือบาร์โค้ดโซน");
	}
});
$("#zone").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
function check_zone(){
	var zone = $("#zone").val();
	var id_tranfer = $("#id_tranfer").val();
	$("#submit").focus();
	$("#load").html("<img src='../img/ajax-loader.gif' width='32' height='32' />");
	$.ajax({
		url:"controller/tranferController.php?check_zone&zone="+zone+"&id_tranfer="+id_tranfer,
		type:"GET", cache:false,
		success: function(data){
			var data = $.trim(data);
			var arr = data.split('!');
			var status = arr[0];
			if(status == "ok"){
				var id_zone = arr[1];
				var table = arr[2];
				///alert(table);
				$("#tables").html(table);
				$("#id_zone").val(id_zone);
				$("#zone").val("");
				$("#from_zone").css("display","none");
				$("#from_out").css("display","");
				$("#menu_main").css("display","none");
				$("#from_move_in").css("display","none");
				$("#barcode_item").focus();
			}else{
				var mes = arr[1];
				$("#tables").html("");
				alert(mes);

			}
			$("#load").html("<button class='btn btn-default' type='button' id='add_detail' onclick='check_zone()'>&nbsp&nbsp;ตกลง&nbsp;&nbsp</button>");
		}
	});
}
///***************** รหัสโซน *********************///
$("#barcode_item").bind("enterKey",function(){
	if($("#barcode_item").val() != ""){
	moveout();
	}
});
$("#barcode_item").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
///********************* จำนวนสินค้า ***********************///
$("#qty").bind("enterKey",function(){
	if($("#qty").val() != ""){
	$("#barcode_item").focus();
	}
});
$("#qty").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
function moveout(){
	var barcode_item 	= $("#barcode_item").val();
	var qty 				= $("#qty").val();
	var id_zone 		= $("#id_zone").val();
	var id_tranfer 		= $("#id_tranfer").val();
	var under_zero 	= $("#allow_under_zero").val();
	if(qty != ""){
		if(barcode_item != ""){
			$("#submit").focus();
			$("#load1").html("<img src='../img/ajax-loader.gif' width='32' height='32' />");
			$.ajax({
				url:"controller/tranferController.php?moveout&id_zone="+id_zone+"&id_tranfer="+id_tranfer+"&barcode_item="+barcode_item+"&qty="+qty+"&under_zero="+under_zero,
				type:"GET", cache:false,
				success: function(data){
					var arr = data.split('!');
					var status = arr[0];
					if(status == "ok"){
						var id_zone = arr[1];
						var table = arr[2];
						$("#tables").html(table);
						$("#barcode_item").val("");
						$("#check_zero").attr("checked", false);
						$("#allow_under_zero").val(0);
						$("#qty").val(1);
						$("#barcode_item").focus();
					}else{
						var mess = arr[1];
						alert(mess);
					}
					$("#load1").html("<input type='submit' class='btn btn-primary' onclick='moveout()' value='ย้าย' />");
				}
			});
		}else{
			alert("ยังไม่ได้ใส่บาร์โค้ด");
		}
	}else{
		alert("ยังไม่ได้ใส่จำนวน");
	}
}
//// เปลี่ยนโซน///
$(document).bind("F2",function(){
	$("#change_zone").click();
});
$(document).keyup(function(e){
	if(e.keyCode == 113)
	{
		$(this).trigger("F2");
	}
});
function new_zone(){
	$("#from_zone").css("display","");
	$("#from_out").css("display","none");
	$("#from_move_in").css("display","none");
	$("#tables").html("");
	$("#zone").focus();
}
function item_move(){
	var id_tranfer = $("#id_tranfer").val();
	$.ajax({
		url:"controller/tranferController.php?item_move&id_tranfer="+id_tranfer,
		type:"GET", cache:false,
		success: function(table){
			$("#tables").html(table);
			$("#from_zone").css("display","none");
			$("#from_out").css("display","none");
			$("#menu_main").css("display","");
			$("#from_move_in").css("display","none");
			$("#from_move_all").css("display","none");
		}
	});
}
function menu_main(){
	$("#tables").html("");
	$("#from_zone").css("display","");
	$("#from_out").css("display","none");
	$("#menu_main").css("display","none");
	$("#from_move_all").css("display","none");
	$("#zone").focus();
}
function click_move_in(id_product_attribute,reference,qty){
	$("#id_product_attribute").val(id_product_attribute);
	$("#name_item").val(reference);
	$("#qty_in").val(qty);
	$("#from_zone").css("display","none");
	$("#from_out").css("display","none");
	$("#menu_main").css("display","none");
	$("#from_move_in").css("display","");
	$("#from_move_all").css("display","none");
	$("#zone_in").focus();
}
$("#zone_in").bind("enterKey",function(){
	if($("#zone_in").val() != ""){
		move_in();
	}else{
		alert("ยังไม่ได้ใส่บาร์โค้ดโซน");
	}
});
$("#zone_in").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
function move_in(){
	var id_product_attribute = $("#id_product_attribute").val();
	var zone_in = $("#zone_in").val();
	var id_tranfer = $("#id_tranfer").val();
	var qty_in = $("#qty_in").val();
	if(zone_in == ""){
		alert("ยังไม่ได้ใส่ชื่อหรือบาร์โค้ดโซน");
	}else{
		$("#load2").html("<img src='../img/ajax-loader.gif' width='32' height='32' />");
		$.ajax({
			url:"controller/tranferController.php?move_in&id_tranfer="+id_tranfer+"&zone_in="+zone_in+"&id_product_attribute="+id_product_attribute+"&qty_in="+qty_in,
			type:"GET", cache:false,
			success: function(data){
				var arr = data.split('!');
				var status = arr[0];
				//alert(status);
				if(status == "ok"){
					var table = arr[1];
					$("#tables").html(table);
					$("#id_product_attribute").val("");
					$("#name_item").val("");
					$("#qty_in").val("");
					$("#from_zone").css("display","none");
					$("#from_out").css("display","none");
					$("#menu_main").css("display","");
					$("#from_move_in").css("display","none");
					$("#from_move_all").css("display","none");
				}else{
					var mess = arr[1];
					alert(mess);
					$("#zone_in").val("");
					$("#zone_in").focus();
				}
				$("#load2").html("<input type='submit' id='submit' class='btn btn-primary' value='ตกลง' onclick='move_in()' />");
			}
		});
	}
}
$("#zone_in_all").bind("enterKey",function(){
	if($("#zone_in_all").val() != ""){
		move_all();
	}else{
		alert("ยังไม่ได้ใส่บาร์โค้ดโซน");
	}
});
$("#zone_in_all").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
function move_all(){
	var id_tranfer = $("#id_tranfer").val();
	var zone_in_all = $("#zone_in_all").val();
	if(zone_in_all == ""){
		alert("ยังไม่ได้ใส่ชื่อหรือบาร์โค้ดโซน");
	}else{
		$("#load4").html("<img src='../img/ajax-loader.gif' width='32' height='32' />");
		$.ajax({
			url:"controller/tranferController.php?move_in_all&id_tranfer="+id_tranfer+"&zone_in_all="+zone_in_all,
			type:"GET", cache:false,
			success: function(data){
				var arr = data.split('!');
				var status = arr[0];
				if(status == "ok"){
					var table = arr[1];
					$("#tables").html(table);
					$("#id_product_attribute").val("");
					$("#name_item").val("");
					$("#qty_in").val("");
					$("#from_zone").css("display","none");
					$("#from_out").css("display","none");
					$("#menu_main").css("display","");
					$("#from_move_in").css("display","none");
					$("#from_move_all").css("display","none");
				}else{
					var mess = arr[1];
					alert(mess);
					$("#zone_in").val("");
					$("#zone_in").focus();
				}
				$("#load4").html("<input type='submit' id='submit' class='btn btn-primary' value='ตกลง' onclick='move_in()' />");
			}
		});
	}
}
function click_move_all(){
	$("#menu_main").css("display","none");
	$("#from_move_all").css("display","");
	$("#zone_in_all").focus();
}
function check_import(){
	if(checkboxes.checked){
		$("#move_out1").css("display","none");
		$("#move_out2").css("display","");
	}else{
		$("#move_out2").css("display","none");
		$("#move_out1").css("display","");
	}
}
function moveout_all(){
	var id_tranfer = $("#id_tranfer").val();
	var id_zone = $("#id_zone").val();
	$("#load3").html("<img src='../img/ajax-loader.gif' width='32' height='32' />");
			$.ajax({
				url:"controller/tranferController.php?moveout_all&id_zone="+id_zone+"&id_tranfer="+id_tranfer,
				type:"GET", cache:false,
				success: function(data){
					var arr = data.split('!');
					var status = arr[0];
					if(status == "ok"){
						var table = arr[1];
						$("#tables").html(table);
					}else{
						var mess = arr[1];
						alert(mess);
					}
					$("#load3").html("<input type='submit' class='btn btn-primary' onclick='moveout_all()' value='ดำเนินการย้าย' />");
				}
			});
}
function delete_detail(id_tranfer_detail){
	var id_tranfer = $("#id_tranfer").val();
	if(confirm('ต้องการลบหรือไม่') == true){
	$.ajax({
				url:"controller/tranferController.php?delete&id_tranfer_detail="+id_tranfer_detail+"&id_tranfer="+id_tranfer,
				type:"GET", cache:false,
				success: function(data){
					var arr = data.split('!');
					var status = arr[0];
					if(status == "ok"){
						var table = arr[1];
						$("#tables").html(table);
					}else{
						var mess = arr[1];
						alert(mess);
					}
				}
		});
	}
}
function delete_tranfer(id_tranfer){
	if(confirm('ต้องการลบหรือไม่') == true){
	$.ajax({
				url:"controller/tranferController.php?delete_tranfer&id_tranfer="+id_tranfer,
				type:"GET", cache:false,
				success: function(data){
					var arr = data.split('!');
					var status = arr[0];
					if(status == "ok"){
						location.reload();
					}else{
						var mess = arr[1];
						alert(mess);
					}
				}
		});
	}
}

/*
$(document).ready(function(e) {
    $("#zone_name").autocomplete({
		source:"controller/tranferController.php?autozone&zone_name="+zone_name,
		autoFocus: true,
		close: function(event,ui){
			var data = $("#zone_name").val();
		}
	});
});
*/

$("#check_zero").change(function(e) {
    if($(this).is(':checked') )
	{
		$("#allow_under_zero").val(1);
	}else{
		$("#allow_under_zero").val(0);
	}
});
      </script>
