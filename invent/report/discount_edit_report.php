<?php 

	$page_menu = "invent_stock_non_move";
	$page_name = "รายงานการแก้ไขส่วนลด";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder --><form name='report_form' id='report_form' action='index.php?content=discount_edit&edit_discount_report=y' method='post'>
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-list"></span>&nbsp;<?php echo $page_name; ?></h3></div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
			<li>
                <a style='text-align:center; background-color:transparent;'>
                	<button type='submit' class='btn btn-link' id='report'><span class='fa fa-file-text-o' style='color:#5cb85c; font-size:35px;'></span><br />รายงาน</button>
                </a>
            </li>
        </ul>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php
if(isset($_POST['customer_name'])){ $customer_name = $_POST['customer_name']; }else{ $customer_name = ""; }
if(isset($_POST['id_customer'])){ $id_customer = $_POST['id_customer'];}else{ $id_customer = "";}
if(isset($_POST['reference'])){ $reference = $_POST['reference'];}else{$reference = "";}
if(isset($_POST['from_date'])){ $from_date = $_POST['from_date']; }else{ $from_date = "เลือกวัน"; }
if(isset($_POST['to_date'])){ $to_date = $_POST['to_date']; }else{ $to_date = "เลือกวัน"; }
$today = date("Y-m-d");
if(isset($_POST['from_date'])){
if($_POST['from_date'] == "เลือกวัน"){
	$date = getMonth($today);
	$from_date = $date['from'];
	$to_date = $date['to'];
}
}
echo"
<div class='row'>
 <div class='col-xs-3'>
 	<div class='input-group'><span class='input-group-addon'>เลขที่ </span><input type='text' name='reference' id='reference' class='form-control' value='' /></div>
 </div>
 <div class='col-xs-3'>
 <div class='input-group'><span class='input-group-addon'>ชื่อลูกค้า</span><input type='text' id='customer_name' name='customer_name' class='form-control' value='$customer_name' autocomplete='off' /><input type='hidden' name='id_customer' id='id_customer' value='$id_customer' /></div> 
  </div>
   <div class='col-xs-2'>
   		<div class='input-group'><span class='input-group-addon'>จาก : </span><input type='text' name='from_date' id='from_date' class='form-control' value='$from_date' required /></div>
    </div>
	<div class='col-xs-2'>
   		<div class='input-group'><span class='input-group-addon'>ถึง : </span><input type='text' name='to_date' id='to_date' class='form-control' value='$to_date' required /></div>
    </div>
	<div class='col-xs-2'>
   		<button type='submit' id='btn_submit' style='display:none;' >ตกลง</button>
    </div>
    </form>
</div> ";
echo "<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />";
if(isset($_GET['edit_discount_report'])){
	//////////////    แสดงผลรายงาน ตามเงื่อนไขที่เลือกไป /////////////////////
	echo" 
	<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
	<table class='table table-bordered table-striped'>
	<thead>
		<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:15%; text-align:center;'>รหัสสินค้า</th><th style='widht:10%; text-align:center;'>เลขที่</th><th style='width:17%; text-align:center;'>ร้าน</th>
		<th style='width:17%; text-align:center;'>ผู้แก้ไข</th><th style='width:10%; text-align:center;'>ก่อนแก้ไข</th><th style='width:10%; text-align:center;'>แก้ไขเป็น</th><th style='width:16%; text-align:center;'>วัน/เวลา</th>
	</thead>";
		$qr = dbQuery("SELECT tbl_discount_edit.id_order,tbl_discount_edit.id_employee,tbl_discount_edit.date_upd,tbl_order.reference,dis_before,dis_after,tbl_product_attribute.reference,tbl_employee.first_name,tbl_employee.last_name,tbl_customer.first_name,tbl_customer.last_name FROM tbl_discount_edit LEFT JOIN tbl_discount_edit_detail ON tbl_discount_edit.id_discount_edit = tbl_discount_edit_detail.id_discount_edit LEFT JOIN tbl_order ON tbl_discount_edit.id_order = tbl_order.id_order LEFT JOIN tbl_employee ON tbl_discount_edit.id_employee = tbl_employee.id_employee LEFT JOIN tbl_order_detail ON tbl_discount_edit_detail.id_order_detail = tbl_order_detail.id_order_detail LEFT JOIN tbl_product_attribute ON tbl_order_detail.id_product_attribute = tbl_product_attribute.id_product_attribute LEFT JOIN tbl_customer ON tbl_order.id_customer = tbl_customer.id_customer WHERE (tbl_order.id_customer = '$id_customer' OR tbl_order.reference LIKE '%$reference%') AND tbl_discount_edit.date_upd between '".dbDate($from_date)."' AND '".dbDate($to_date)."' ORDER BY id_discount_edit_detail DESC");
		$row = dbNumRows($qr); 
		$i = 0;
		$n = 1;
		while($i<$row){
			list($id_order,$id_employee,$date_upd,$reference_order,$dis_before,$dis_after,$reference_product_attribute,$first_name_employee,$last_name_employee,$first_name_customer,$last_name_customer) = dbFetchArray($qr);
				echo "
				<tr><td align='center'>$n</td><td>$reference_product_attribute</td><td>$reference_order</td><td  align='left' style='padding-left:5px;'>$first_name_customer $last_name_customer</td><td  align='left' style='padding-left:5px;'>$first_name_employee $last_name_employee</td><td align='center' >$dis_before</td><td align='center'>$dis_after</td><td align='center'>$date_upd</td></tr>";
			$i++;
			$n++;
		}
		echo"</table>"; 
}

	
?>   
</div>     
<script>
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
$(document).ready(function(e) {
    $("#gogo").click(function(){
		var action = $("#export").val();
		$("#report_form").attr("action", action );
		$(this).attr("type", "submit");
	});
});
$(document).ready(function(e) {
    $("#report").click(function(e) {
        $("#report_form").attr("action", "index.php?content=discount_edit&edit_discount_report=y");
		$("#btn_submit").click();
    });
});
$(document).ready(function(e) {
    $("#customer_name").autocomplete({
		source:"controller/orderController.php?customer_name",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#customer_name").val();
			var arr = data.split(':');
			var id = arr[0];
			var name = arr[1];
			$("#id_customer").val(id);
			$(this).val(name);
		}
	});			
});
</script>