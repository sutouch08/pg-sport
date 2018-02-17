<?php 
	$page_menu = "invent_sale";
	$page_name = "เปิด / ปิดการตรวจนับ";
	$id_tab = 31;
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
	if(isset($_POST['from_date'])){
		$from_date = $_POST['from_date'];
		$to_date = $_POST['to_date'];
		 $from= date('Y-m-d',strtotime($from_date));
		 $to = date('Y-m-d',strtotime($to_date));
	}else{
		$month = getMonth();
		$from = $month['from'];
		$to = $month['to'];
	}
	?>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-eye-close"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['open']) || isset($_GET['close'])){
		   echo"
		   <li><a href='index.php?content=OpenCheck' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
	   }else if(isset($_GET['view_detail'])){
		   echo"
		   <li><a href='index.php?content=OpenCheck' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
	   }else{
		   $value = dbNumRows(dbQuery("SELECT id_check FROM tbl_check WHERE status = 1"));
		   if($value > 0){
			   echo"
		   <li $can_add><a href='index.php?content=OpenCheck&close=y' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-eye-close' style='color:#5cb85c; font-size:30px;'></span><br />ปิดการตรวจสินค้า</button></a></li>";
		   }else{
		   echo"
		   <li $can_add><a href='index.php?content=OpenCheck&open=y' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-eye-open' style='color:#5cb85c; font-size:30px;'></span><br />เปิดการตรวจสินค้า</button></a></li>";
		   }
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
	 echo"<div class='alert alert-danger'><b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
}
if(isset($_GET['open'])){
	$id_employee = $_COOKIE['user_id'];
	echo"	<form method='post' name='add' action='controller/openCheckController.php?open=y' >
		<input type='hidden' name='id_employee' value='$id_employee' >
		<div class='row'>
			<div class='col-sm-4'>
				<div class='input-group'>
					<span class='input-group-addon'>หัวข้อ</span>
					<input type='text' class='form-control' name='name_check' id='name_check'  value='' />
				</div>		
			</div>	
			<div class='col-sm-4'>
				<div class='input-group'>
					<span class='input-group-addon'>เลือกคลัง</span><select name='id_warehouse' id='id_warehouse' class='form-control'>"; warehouseList(); echo"</select></div>		
			</div>	
			<div class='col-sm-3'>
				<div class='input-group'>
					<span class='input-group-addon'>วันที่เริ่ม</span>
				 <input type='text' class='form-control'  name='adjust_reference' id='adjust_reference' disabled='disabled' value='".date('d-m-Y')."'/>
				</div>
			</div>
			<div class='col-sm-1'>
				<button type='submit' class='btn btn-default'>เริ่มการตรวจนับ</button>
			</div>
         </div>
		 <br>
		 <div class='row'>
		 <div class='col-sm-6'>
				
		 </div>
		 </div>
				</form>";
	
	
}else if(isset($_GET['close'])){
	$id_employee = $_COOKIE['user_id'];
	list($id_check,$name_check,$date_start) = dbFetchArray(dbQuery("SELECT id_check,name_check,date_start FROM tbl_check WHERE status = 1"));
	echo"	<form method='post' name='add' action='controller/openCheckController.php?close=y' >
	<input type='hidden' name='id_employee' value='$id_employee' >
	<input type='hidden' name='id_check' value='$id_check' >
		<div class='row'>
			<div class='col-sm-5'>
				<div class='input-group'>
					<span class='input-group-addon'>หัวข้อ</span>
					<input type='text' class='form-control' name='name_check' id='name_check'  value='$name_check' disabled='disabled' />
				</div>		
			</div>	
			<div class='col-sm-3'>
				<div class='input-group'>
					<span class='input-group-addon'>วันที่เริ่ม</span>
				 <input type='text' class='form-control'  name='adjust_reference' id='adjust_reference' disabled='disabled' value='".showDate($date_start)."'/>
				</div>
			</div>
			<div class='col-sm-2'>
				<button type='submit' class='btn btn-default'>ปิดการตรวจนับ</button>
			</div>
			<div class='col-sm-1'>
				
			</div>	
         </div>
		 <br>
		 <div class='row'>
		 <div class='col-sm-6'>
				
		 </div>
		 </div>
				</form>";
}else if(isset($_GET['view_detail'])){
	echo"</form>";
	$id_check = $_GET['id_check'];
	 $checkstock = new Checkstock();
	 $checkstock->detail($id_check);
	 $employee_open = new employee($checkstock->id_employee_open);
	 if($checkstock->id_employee_close == "0"){
		 $id_employee_close = "";
	 }else{
		 $id_employee_close = $checkstock->id_employee_close;
	 }
	 $employee_close = new employee($id_employee_close);
	echo"
	<table width='100%' border='0'>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>หัวข้อ : &nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>".$checkstock->name_check."</td><td style='padding-left:15px; vertical-align:text-top;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>คลัง : &nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>".$checkstock->warehouse_name."</td><td style='padding-left:15px; vertical-align:text-top;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ผู้เปิดการตรวจนับ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>".$employee_open->full_name."</td>
		<td style='padding-left:15px; vertical-align:text-top;'></td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>ผู้ปิดการตรวจนับ :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>".$employee_close->full_name."</td><td style='padding-left:15px;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>วันที่เปิด :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>".showDate($checkstock->date_start)."</td>
		<td style='padding-left:15px; vertical-align:text-top;'>&nbsp;</td>
	</tr>
	<tr>
		<td width='20%' align='right' style='padding-bottom:10px; vertical-align:text-top;'>วันที่ปิด :&nbsp;</td>
		<td width='40%' align='left' style='padding-bottom:10px;'>";if($checkstock->date_stop == "0000-00-00"){}else{echo showDate($checkstock->date_stop);}echo "</td>
		<td style='padding-left:15px; vertical-align:text-top;'></td>
	</tr>
	</table>";
	
}else{
echo"<form  method='post' id='form'>
		<div class='row'>
			<div class='col-sm-2 col-sm-offset-4'>
				<div class='input-group'>
					<span class='input-group-addon'> จาก :</span>
					<input type='text' class='form-control' name='from_date' id='from_date'  value='";
					 if(isset($_POST['from_date']) && $_POST['to_date'] && $_POST['from_date'] && $_POST['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($from_date));} elseif(isset($_GET['from_date']) && $_GET['to_date'] && $_GET['from_date'] && $_GET['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($from_date));} else { echo date('d-m-Y',strtotime($from));} 
					 echo "'/>
				</div>		
			</div>	
			<div class='col-sm-2 '>
				<div class='input-group'>
					<span class='input-group-addon'>ถึง :</span>
				 <input type='test' class='form-control'  name='to_date' id='to_date' value='";
				  if(isset($_POST['from_date']) && $_POST['to_date'] && $_POST['from_date'] && $_POST['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($to_date));} elseif(isset($_GET['from_date']) && $_GET['to_date'] && $_GET['from_date'] && $_GET['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($to_date));} else { echo date('d-m-Y',strtotime($to));}  echo"' />
				</div>
			</div>
			<div class='col-sm-1'>
					<button type='button' class='btn btn-default' onclick='validate()'>แสดง</button>
			</div>	
         </div>
				</form>
				<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />
<div class='row'>
<div class='col-sm-12'>
	<table class='table table-striped table-hover'>
    	<thead style='background-color:#48CFAD;'>
        	<th style='width:5%; text-align:center;'>ID</th><th style='width:50%;'>หัวข้อการตรวจนับ</th><th style='width:15%;'>สถานะ</th>
            <th style='width:15%; text-align:center;'>วันที่เริ่ม</th><th style='width:15%; text-align:center;'>วันที่จบ</th>
        </thead>";
		$view = "";
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
		$result = dbQuery("SELECT id_check,name_check,status,date_start,date_stop FROM tbl_check WHERE (date_start BETWEEN '$from' AND '$to')  ORDER BY id_check DESC");
		$i=0;
		$n=1;
		$row = dbNumRows($result);
		while($i<$row){
			list($id_check, $name_check, $status, $date_start, $date_stop) = dbFetchArray($result);
			echo "<tr>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=OpenCheck&view_detail=y&id_check=$id_check\">$id_check</td>
			<td style='cursor:pointer;' onclick=\"document.location='index.php?content=OpenCheck&view_detail=y&id_check=$id_check'\">$name_check</td>
			<td style='cursor:pointer;' onclick=\"document.location='index.php?content=OpenCheck&view_detail=y&id_check=$id_check'\">";if($status == "1"){echo "กำลังตรวจนับสินค้า";}else if($status == "3"){echo "บันทึกยอดต่างแแล้ว";}else{echo "ตรวจนับเสร็จสิน";}echo "</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=OpenCheck&view_detail=y&id_check=$id_check'\">".showDate($date_start)."</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=OpenCheck&view_detail=y&id_check=$id_check'\">";if($date_stop == "0000-00-00"){}else{echo showDate($date_stop);}echo "</td>
			</tr>";
			$i++;
			$n++;
		}
		if($row == "0"){
			echo "<td align='left' colspan='5'><div class='alert alert-info'  align='center'>ยังไม่มีการตรวจนับสินค้า</div></td>";
		}
		echo"       
    </table>
</div> </div>";
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
  </script>