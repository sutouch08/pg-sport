<?php 
	$page_menu = "repay";
	$page_name = "ตัดหนี้";
	$id_tab = 33;
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
	
	?>
    
<div class="container"><audio id="sound1"><source src="../library/beep.mp3" type="audio/mpeg"></audio>
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-usd"></span>&nbsp;<?php echo $page_name; ?></h3>
  </div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	 if(isset($_GET['view_detail'])&&isset($_GET['id_order'])){
		   echo"
		   <li><a href='index.php?content=repay' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
	   }
		   
	   ?>
       </ul>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php 
if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div id='error' class='alert alert-danger alert-dismissible' role='alert' >
	 
	 <b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
}
///************************** แสดงรายละเอียด ****************************//
if(isset($_GET['view_detail'])&&isset($_GET['id_order'])){
	$id_employee = $_COOKIE['user_id'];
	$id_order = $_GET['id_order'];
	$order = new order($id_order);
	$customer = new customer($order->id_customer);
	$sale = new sale($order->id_sale);
	if($order->current_state != 9){ $active = "style='display:none;'";}else{ $active = ""; }
	if($order->current_state == 10 ){ $confirm = ""; }else{ $confirm = "style='display:none;'";}
	echo "
        <div class='row'>
        	<div class='col-lg-12'><h4>".$order->reference." - ";if($order->id_customer != "0"){echo $customer->full_name;}echo "<p class='pull-right'>พนักงานขาย : &nbsp;".$sale->full_name."</p></h4></div>
        </div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
		<div class='row'>
		<div class='col-lg-12'>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>วันที่สั่ง : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".thaiDate($order->date_add)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>สินค้า :&nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_product)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>จำนวน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_qty)."</dd>  |</dt></dl>
		<dl style='float:left; margin-left:10px;'><dt style='float:left; margin:0px; padding-right:10px'>ยอดเงิน : &nbsp;</dt><dd style='float:left; margin:0px; padding-right:10px'>".number_format($order->total_amount,2)."&nbsp;฿</dd> </dt></dl>
	
		<p class='pull-right' $active >
			<a href='controller/billController.php?print_order&id_order=$id_order' >
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-primary'><span class='glyphicon glyphicon-print' style='color:#FFF; font-size:30px;'></span></button>
			</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</p><form action='controller/billController.php?repay&id_order=$id_order' method='post'><p class='pull-right' $active >&nbsp;&nbsp;&nbsp;&nbsp;<button class='btn btn-default' type='submit' $can_edit>ตกลง</button></p><p class='pull-right' $active ><select name='order_valid' class='form-control input-sm' $can_edit><option>----สถานะ----</option><option value='1'>ชำระเงินแล้ว</option></select> </p></form>
		</div></div>
		<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />";
		echo "	
		<div class='row'><div class='col-lg-12'>"; echo $order->order_detail_qc_table(); echo "</div></div><br><br>";
	
}else{
/// ***************************  แสดงรายการรอตรวจ ****************************////
echo "<form action='' method='post' >
	<div class='row'>
    	<div class='col-lg-6 col-lg-offset-3'>
    		<div class='input-group'>
            	<span class='input-group-addon'>&nbsp;&nbsp;เลขที่อ้างอิง &nbsp;&nbsp;</span>
            	<input type='text' name='search' id='search' class='form-control' value='";if(isset($_POST['search'])){echo $_POST['search'];}echo "' />
                <span class='input-group-btn'>
                <button type='button' class='btn btn-default'>&nbsp;&nbsp;<span class='glyphicon glyphicon-search'></span>&nbsp;&nbsp;</button>
                </span>
            </div>
        </div>
    </div>
	</form>";
if(isset($_POST['search'])){
	$search = $_POST['search'];
}else{
	$search = "";
}
$paginator = new paginator();
if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
$paginator->Per_Page("tbl_order","WHERE current_state = 9 AND role IN(1,4,5) AND valid = 0 AND role = 1 AND reference LIKE '%$search%'",$get_rows);
		$paginator->display($get_rows,"index.php?content=repay");
		$Page_Start = $paginator->Page_Start;
$Per_Page = $paginator->Per_Page;
echo"
<div class='row'>
<div class='col-sm-12'>
	<table class='table table-striped'>
    	<thead style='color:#FFF; background-color:#48CFAD;'>
        	<th style='width:5%; text-align:center;'>ลำดับ</th>
			<th style='width:10%;'>เลขที่อ้างอิง</th><th style='width:20%;'>ลูกค้า</th>
            <th style='width:10%; text-align:center;'>ยอดเงิน</th>
			<th style='width:15%; text-align:center;'>เงื่อนไข</th>
			<th style='width:10%; text-align:center;'>สถานะ</th>
			<th style='width:10%;'>พนักงาน</th>
			<th style='width:10%; text-align:center;'>วันที่เพิ่ม</th>
			<th style='width:10%; text-align:center;'>วันที่ปรับปรุง</th>
        </thead>";
		$result = dbQuery("SELECT id_order,id_customer,id_employee,reference,payment,current_state,date_add,date_upd FROM tbl_order WHERE current_state = 9 AND role IN(1,4,5) AND valid = 0 AND role = 1  AND reference LIKE '%$search%' ORDER BY id_order DESC LIMIT $Page_Start , $Per_Page");
		$i=0;
		$n = 1;
		$row = dbNumRows($result);
		if($row>0){
		while($i<$row){
			list($id_order,$id_customer,$id_employee,$reference,$payment,$current_state,$date_add,$date_upd) = dbFetchArray($result);
			list($first_name,$last_name) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_customer WHERE id_customer = '$id_customer'"));
			list($amount) = dbFetchArray(dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail WHERE id_order = '$id_order'"));
			list($first,$last) = dbFetchArray(dbQuery("SELECT first_name,last_name FROM tbl_employee WHERE id_employee = '$id_employee'"));
			list($state_name) = dbFetchArray(dbQuery("SELECT state_name FROM tbl_order_state WHERE id_order_state = '$current_state'"));
			$customer_name = "$first_name $last_name";
			$employee_name = "$first $last";	
	echo"<tr>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=repay&id_order=$id_order&view_detail=y'\">$n</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=repay&id_order=$id_order&view_detail=y'\">$reference</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=repay&id_order=$id_order&view_detail=y'\">$customer_name</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=repay&id_order=$id_order&view_detail=y'\">"; echo number_format($amount)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=repay&id_order=$id_order&view_detail=y'\">$payment</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=repay&id_order=$id_order&view_detail=y'\">$state_name</td>
				<td style='cursor:pointer;' onclick=\"document.location='index.php?content=repay&id_order=$id_order&view_detail=y'\">$employee_name</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=repay&id_order=$id_order&view_detail=y'\">"; echo thaiDate($date_add)."</td>
				<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=repay&id_order=$id_order&view_detail=y'\">"; echo thaiDate($date_upd)."</td>
			</tr>";
			$i++; $n++;
		}
		}else if($row==0){
			echo"<tr><td colspan='9' align='center'><h3><span class='glyphicon glyphicon-exclamation-sign'></span>&nbsp;ไม่มีรายการในช่วงนี้</h3></td></tr>";
		}
		echo"</table>";
		echo $paginator->display_pages();
		echo "<br><br>";
}
?>
<script>
function get_row(){
	$("#rows").submit();
}
</script>