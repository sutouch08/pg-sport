<?php 
	$page_menu = "invent_order";
	$page_name = "ติดตามออเดอร์";
	$id_profile = $_COOKIE['profile_id'];
	if(sale_access($_COOKIE['user_id'])){  }else{ echo accessDeny(); exit; }
	?>
    
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6 col-xs-9"><h3 style='font-size:3.5vmin; padding-bottom:0px;'><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6 col-xs-3">
       <ul class="nav navbar-nav navbar-right" style='margin:0px;'>
       <?php 
	   if(isset($_GET['edit'])&&isset($_GET['id_order'])){
		   $id_order = $_GET['id_order'];
		   $order= new order($id_order);
		   if($order->valid==1 || $order->current_state !=1 && $order->current_state !=3){ $active = "style='display:none;'";}else{$active = ""; }
		    echo"
		   <li><a href='index.php?content=tracking' style='text-align:center; background-color:transparent;  padding-bottom:0px; padding-top:0px;'><button type='button' class='btn btn-link' style=' padding-bottom:0px; padding-top:0px;'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:5vmin;'></span></button></a></li>";
			}else if(isset($_GET['edit']) || isset($_GET['add'])){
		   echo"
		   <li><a href='index.php?content=tracking' style='text-align:center; background-color:transparent; padding-bottom:0px; padding-top:0px;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
	  		}
	   ?>
       </ul>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php 
if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div id='error' class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
}
if(isset($_GET['missing'])){
	$missing = $_GET['missing'];
	echo"<div id='error' class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$missing</div>";
}
function payment_method($selected="เครดิต"){
	$sql=dbQuery("SELECT * FROM tbl_payment");
	while($row = dbFetchArray($sql)){
		$name = $row['payment_name'];
		if($selected==$name){ $default ="selected='selected'";}else{ $default = "";}
		echo "<option value='$name' $default>$name</option>";
	}
}
//***************************** ข้อมูลลูกค้า ****************************//
echo"<button data-toggle='modal' data-target='#myModal' id='info' style='display:none;'>xxx</button>
<div class='modal fade' id='myModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
							<div class='modal-dialog' style='width:600px;'>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<h4 class='modal-title' id='myModalLabel'></h4>
									  </div>
									  <div class='modal-body' style='font-size:1.5vmin;'></div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>รับทราบ</button>
									  </div>
									</div>
							 </div>
								</div>";
 if(isset($_GET['edit'])&&isset($_GET['id_order'])){
//*********************************************** แก้ไขออเดอร์ **************************************************************//
	//echo"<form id='state_change' action='controller/orderController.php?edit&state_change' method='post'>";
	$id_employee = $_COOKIE['user_id'];
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	if($order->id_customer != "0"){
	$customer = new customer($order->id_customer);
	}
	$sale = new sale($order->id_employee);
	$state = $order->orderState();
	$role = $order->role;
	echo"		
        <div class='row'>
        	<div class='col-lg-12'><h4 style='font-size:2.5vmin;'>".$order->reference." - ";if($order->id_customer != "0"){echo $customer->full_name;}echo "<p class='pull-right'>พนักงาน : &nbsp;".$sale->full_name."</p></h4></div>
        </div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'>
		<div class='col-lg-12' style='font-size:2.5vmin;'>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:1.5vmin'>วันที่สั่ง : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:1.5vminx'>".thaiDate($order->date_add)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:1.5vmin'>สินค้า :&nbsp;</dt><dd style='float:left; margin:0px; padding-right:1.5vmin'>".number_format($order->total_product)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:1.5vmin'>จำนวน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:1.5vmin'>".number_format($order->total_qty)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:1.5vmin'>ยอดเงิน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:1.5vminx'>".number_format($order->total_amount,2)."&nbsp;฿</dd> </dt></dl>
		</div></div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'><form id='state_change' action='controller/orderController.php?edit&state_change' method='post'>
		<div class='col-lg-6' style='font-size:2.5vmin;'>
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'><tr><input type='hidden' name='id_order' value='".$order->id_order."' /><input type='hidden' name='id_employee' value='$id_employee' />
		<td colspan='3' style='width:25%; text-align:center; vertical-align:middle;'>สถานะ :&nbsp; </td></tr>";
		$row = dbNumRows($state);
		$i=0;
		if($row>0){
			while($i<$row){
				list($id_order_state, $state_name, $first_name, $last_name, $date_add)=dbFetchArray($state);
			echo"
			<tr  style='background-color:".state_color($id_order_state).";'><td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'>$state_name</td>
			<td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'>$first_name  $last_name</td>
			<td style='padding-top:10px; padding-bottom:10px; text-align:center; color:#FFF;'>".date('d-m-Y H:i:s', strtotime($date_add))."</td></tr>";
			$i++;
			}
		}else{
		echo"<tr><td style='padding-top:10px; padding-bottom:10px; text-align:center;'>".$order->currentState()."</td>
		<td style='padding-top:10px; padding-bottom:10px; text-align:right;'></td>
		<td style='padding-top:10px; padding-bottom:10px; text-align:center;'>".date('d-m-Y H:i:s', strtotime($order->date_upd))."</td></tr>";
		}
		echo"
		</table></div></form><div class='col-lg-6' style='font-size:2.5vmin;'>";
		if($order->id_customer != "0"){
			if($role == 4){
				echo"
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'>
		<tr><td colspan='3' >ข้อมูลสปอนเซอร์</td></tr>
		<tr><input type='hidden' id='id_customer' value='".$customer->id_customer."' />
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ชื่อ :&nbsp; ".$customer->full_name."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เลขที่เอกสาร : &nbsp;".$customer->sponsor_reference."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>อีเมล์ :&nbsp;".$customer->email."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วงเงิน :&nbsp;".number_format($customer->sponsor_amount,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>อายุ :&nbsp;"; if($customer->birthday !="0000-00-00"){ echo round(dateDiff($customer->birthday,date('Y-m-d'))/365) ." &nbsp;( ". thaiTextDate($customer->birthday).")" ;}else{echo "-";} echo"</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ใช้ไป :&nbsp;".number_format($customer->sponsor_used,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เพศ : &nbsp;"; if($customer->id_gender==1){ echo"ไม่ระบุ";}else if($customer->id_gender==2){echo"ชาย";}else{echo"หญิง";} echo"</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>คงเหลือ : &nbsp;".number_format($customer->sponsor_balance,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วันที่เป็นสมาชิก :&nbsp;".thaiTextDate($customer->date_add)."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>จำนวนครั้งที่เบิก : &nbsp;".$customer->total_sponsor_place." ครั้ง</td>
		</tr><tr>
		<td colspan='2' style='width:100%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ระยะสัญญา : &nbsp;".thaiTextDate($customer->sponsor_start)." ถึง ".thaiTextDate($customer->sponsor_end)."</td></tr>
		</table>";
			}else{
		echo "
		<table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'>
		<tr><td colspan='3' >ข้อมูลลูกค้า</td></tr>
		<tr><input type='hidden' id='id_customer' value='".$customer->id_customer."' />
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ชื่อ :&nbsp; ".$customer->full_name."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตเทอม : &nbsp;".$customer->credit_term."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>อีเมล์ :&nbsp;".$customer->email."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วงเงินเครดิต :&nbsp;".number_format($customer->credit_amount,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>อายุ :&nbsp;"; if($customer->birthday !="0000-00-00"){ echo round(dateDiff($customer->birthday,date('Y-m-d'))/365) ." &nbsp;( ". thaiTextDate($customer->birthday).")" ;}else{echo "-";} echo"</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตใช้ไป :&nbsp;".number_format($customer->credit_used,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เพศ : &nbsp;"; if($customer->id_gender==1){ echo"ไม่ระบุ";}else if($customer->id_gender==2){echo"ชาย";}else{echo"หญิง";} echo"</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตคงเหลือ : &nbsp;".number_format($customer->credit_balance,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วันที่เป็นสมาชิก :&nbsp;".thaiTextDate($customer->date_add)."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ยอดเงินตั้งแต่เป็นสมาชิก : &nbsp;".number_format($customer->total_spent,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>&nbsp;</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ออเดอร์ตั้งแต่เป็นสมาชิก : &nbsp;".$customer->total_order_place."</td></tr>
		</table>";
		}
		}
		echo "</div><!--col --></div><!--row-->
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />	
		<form id='edit_order_form' action='controller/orderController.php?edit_order&add_detail' method='post' autocomplete='off'>
		<div class='row'><div class='col-md-12' style='font-size:2vmin;'>"; echo $order->saleOrderProductTable(); echo "</div></div><div class='row'><div class='col-md-12 col-xs-12' style='font-size:2vmin;'><p><h4>ข้อความ :  "; if($order->comment ==""){ echo"ไม่มีข้อความ";}else{ echo $order->comment; }echo "</h4></p></div></div><h4></h4></form>";
//*********************************************** จบหน้าแก้ไข ****************************************************/

}else{ 
//************************************************ แสดงรายการ *************************************************//
$paginator = new paginator();
$id_employee = $_COOKIE['user_id'];
$employee = new employee($id_employee);
$id_sale = $employee->get_id_sale($id_employee);
		if(isset($_POST['from_date'])){	$from = date('Y-m-d',strtotime($_POST['from_date'])); }else{ $from = "";} if(isset($_POST['to_date'])){  $to =date('Y-m-d',strtotime($_POST['to_date'])); }else{ $to = "";}
		if($from==""){
			if($to==""){
				$view = getConfig("VIEW_ORDER_IN_DAYS");
			}
		}
		if($view !=""){
			$date = getLastDays($view);
			$from = $date['from'];
			$to = $date['to'];
		}
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		$table = "tbl_customer LEFT JOIN tbl_order ON tbl_customer.id_customer = tbl_order.id_customer";
		$paginator->Per_Page($table,"WHERE (tbl_order.date_upd BETWEEN '$from' AND '$to') AND role IN(1) AND id_sale = $id_sale ORDER BY id_order DESC",$get_rows);
		echo"<div class='row'><div class='col-md-2 col-xs-6'>";
		$paginator->display($get_rows,"index.php?content=tracking");
		echo"</div></div>";
echo "<div class='row'>
<div class='col-xs-12' style='font-size:1.5vmin;'>
	<table class='table'>
    	<thead style='color:#FFF; background-color:#48CFAD;'>
        	<th style='width:25%;'>เลขที่อ้างอิง</th><th style='width:40%;'>ลูกค้า</th>
            <th style='width:10%; text-align:center;'>ยอดเงิน</th>
			<th style='width:25%; text-align:center;'>สถานะ</th>
        </thead>";
		$result = getTrackOrderTable($view,$from, $to,$paginator->Page_Start,$paginator->Per_Page, 1, $id_sale );
		$i=0;
		$row = dbNumRows($result);
		if($row>0){
		while($i<$row){
			list($id_order, $reference, $id_customer, $current_state, $date_upd)=dbFetchArray($result);
			$customer = new customer($id_customer);
			$order = new order($id_order);
			$amount = $order->total_amount;
			$status = $order->current_state_name;
			
	echo"<tr style='color:#FFF; font-size:3vmin; background-color:".state_color($current_state).";'>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=tracking&edit=y&id_order=$id_order&view_detail=y'\">$reference</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=tracking&edit=y&id_order=$id_order&view_detail=y'\">".$customer->full_name."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=tracking&edit=y&id_order=$id_order&view_detail=y'\">"; echo number_format($amount)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=tracking&edit=y&id_order=$id_order&view_detail=y'\">$status</td>

			</tr>";
			$i++;
		}
		}else if($row==0){
			echo"<tr><td colspan='9' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;ไม่มีรายการในช่วงนี้</h3></td></tr>";
		}
		echo"</table>";
		echo $paginator->display_pages();
		echo "<br><br>";
}
?>
</div>
<script language="javascript">  
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

$(document).ready(function(e) {
    if($("#error").length){
		alert($("#error").text());
	}
});
$("#get_info").click(function(e) {
	var cus_name = $("#customer_name").val();
	var cus_id = $("#id_customer").val();
	var arr = cus_name.split(":");
	 if(cus_name == ""){
		alert("ยังไม่ได้เลือกลูกค้า");
	}else if(cus_id ==""){
		alert("ระบบไม่พบ Customer ID ไม่สามารถเพิ่มออเดอร์ได้กรุณาเลือกลูกค้าใหม่หรือติดต่อผู้ดูแลระบบ");
	}else{
		$.ajax({
			url:"controller/customerController.php?get_info&id_customer="+cus_id,
			type:"GET", cache:false, success: function(data){
				$(".modal-title").text("ข้อมูล : "+arr[1]);
				$(".modal-body").html(data);
				$("#info").click();
			}
		});
	}
});
function get_row(){
	$("#rows").submit();
}
</script>