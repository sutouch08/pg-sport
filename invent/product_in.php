<?php 
	$page_menu = "store";
	$page_name = "รับสินค้าเข้า";
	$id_tab = 6;
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
    
<div class="container">
<!-- page place holder -->
<?php if(isset($_GET['edit'])){
	echo"<form name='recieved_form' id='recieved_form' action='controller/storeController.php?edit=y' method='post'>";
}else if(isset($_GET['add'])){
	echo"<form name='recieved_form' id='recieved_form' action='controller/storeController.php?add=y' method='post'>";
}
?>
<div class="row">
	<div class="col-xs-6"><h3><span class="glyphicon glyphicon-import"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-xs-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	   if(isset($_GET['edit'])&&isset($_GET['id_recieved_product'])){
		    echo"
		   <li><a href='index.php?content=product_in' style='text-align:center; background-color:transparent; '><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a href='#' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='edit_stock()'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
			}else if(isset($_GET['edit']) || isset($_GET['add'])){
		   echo"
		   <li><a href='index.php?content=product_in' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
       		<li><a href='#' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='submit_stock()'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
	  		}else if(isset($_GET['view_detail'])){
		   echo"
		   <li><a href='index.php?content=product_in' style='text-align:center; background-color:transparent;'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</a></li>";
	   }else{
		   echo"
		   <li $can_add><a href='index.php?content=product_in&add=y' style='text-align:center; background-color:transparent; padding-top:0px; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />$page_name</button></a></li>";
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
if(isset($_GET['barcode_zone'])){ $barcode_zone = $_GET['barcode_zone']; }else{ $barcode_zone = "";}
if(isset($_GET['id_zone'])){ $id_zone = $_GET['id_zone']; }else{ $id_zone = "";}
if(isset($_GET['name_zone'])){ $name_zone = $_GET['name_zone'];  }else{ $name_zone = ""; }
	if($id_zone !=""){ 
			$active = "disabled=disabled"; 
			$actived = "";
			}else{ 
			$active = "";
			$actived = "disabled=disabled"; 
		}
if(isset($_GET['add'])){ 
//***************************** หน้ารับสินค้าเข้า *******************************//
$role = 1;
echo"	<div class='row'>
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เลขที่เอกสาร</span>";
					$new_ref = get_max_reference("PREFIX_RECIEVE","tbl_recieved_product", "recieved_product_no");
					if(isset($_GET['id_recieved_product'])){ $id_recieved_product = $_GET['id_recieved_product']; 
					$qr = dbQuery("SELECT role FROM tbl_recieved_product WHERE id_recieved_product = $id_recieved_product");
					list($role) = dbFetchArray($qr);
					echo"<input type='hidden' name='id_recieved_product' value='$id_recieved_product' />
					<input type='text' class='form-control' name='recieved_no' id='recieved_no'  value='"; echo getRecievedNO($id_recieved_product); echo"' disabled='disabled'/>";}else{
					echo" <input type='hidden' name='recieved_no' value='".$new_ref."' />
					<input type='text' class='form-control' name='recieved_no' id='recieved_no'  value='".$new_ref."' disabled='disabled'/>";}
					echo"
				</div>		
			</div>	
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เลขที่อ้างอิง</span>
				 <input type='text' class='form-control'  name='reference' id='reference'"; if(isset($_GET['id_recieved_product'])){
				 echo"value='"; echo getRecievedReference($id_recieved_product); echo"' disabled='disabled'"; } echo"'/>
				</div>
			</div>
			<div class='col-xs-2'>
				<div class='input-group'>
					<span class='input-group-addon'>วันที่</span>";
					if(isset($_GET['id_recieved_product'])){
						echo"<span class='form-control'> <input type='hidden' name='date' id='date' value='".getRecievedDate($id_recieved_product)."'/>".thaiDate(getRecievedDate($id_recieved_product))."</span>"; }else{echo"
				 <input type='text' class='form-control'  name='date' id='date' value='"; echo date('d-m-Y'); echo"' />";}
				 echo"
				</div>
			</div>
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>วัตถุประสงค์</span>
						<select name='role' id='role' class='form-control'"; if(isset($_GET['id_recieved_product'])){ echo "disabled='disabled'";} echo">
				 			<option value='1'"; if($role == 1){ echo"selected='selected'";} echo">รับเข้าปกติ</option>
							<option value='2'"; if($role == 2){ echo"selected='selected'";} echo">รับเข้าจากการแปรรูป</option>
						</select>
				</div>
			</div>
			<div class='col-xs-1'>
					<button type='submit' class='btn btn-default'"; if(isset($_GET['id_recieved_product'])){ echo" disabled='disabled'";} echo">รับสินค้า</button>
			</div>	
         </div>
				</form>
				<hr style='border-color:#CCC; margin-top: 15px; margin-bottom: 15px;' />";
			if(isset($_GET['id_recieved_product'])){
				$id_recieved_product = $_GET['id_recieved_product'];
				$date_add = $_GET['date_add'];
				if(isset($_GET['first'])){
					echo "<div class='row'><div class='col-lg-12' style='text-align:center;'><p>ใช้งานระบบเป็นครั้งแรก ? คุณสามารถนำเข้ายอดยกมาจากไฟล์ได้</p><p> <a href='index.php?content=import_stock&id_recieved_product=$id_recieved_product&date=$date_add'><button type='button' class='btn btn-success'>นำเข้ายอดสินค้า</button></a></p></div></div><div class='row'><hr style='border-color:#CCC; margin-top: 15px; margin-bottom: 15px;' /></div>"; }
			echo"<form id='detail_form' action='controller/storeController.php?add_detail=y&id_recieved_product=$id_recieved_product' method='post'>
					<div class='row'>
					<div class='col-xs-4 col-xs-offset-4'>
						<div class='input-group'>
						<span class='input-group-addon'>คลัง</span>";
					 if(isset($_GET['id_warehouse'])){	$id_warehouse = $_GET['id_warehouse']; }else{ $id_warehouse ="";}
						echo"
						<select class='form-control' name='id_warehouse' >"; warehouseList($id_warehouse); echo"</select>
						</div>
					</div><div class='col-xs-2'><button type='button' class='btn btn-default' id='change_zone' onclick='reset_zone()' $actived >เปลี่ยนโซน(F2)</button></div>
					</div>
					<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
					<div class='row'>
					<div class='col-xs-3'>
					<div class='input-group'>
					<span class='input-group-addon'>บาร์โค้ดโซน</span>
					<input type='text' class='form-control' name='barcode_zone' id='barcode_zone' $active value='$barcode_zone' />
					</div></div>
					<div class='col-xs-3'>
					<div class='input-group'>
					<span class='input-group-addon'>ชื่อโซน</span><input type='hidden' name='id_zone' id='id_zone' value='' />
					<input type='text' class='form-control' name='zone_name' id='zone_name' $active value='$name_zone'/>
					</div></div>
					<div class='col-xs-2'>
					<div class='input-group'><input type ='hidden' name='date_add' value='$date_add' />
					<span class='input-group-addon'>จำนวน</span>
					<input type='text' class='form-control' name='qty' id='qty' value='1' />
					</div></div>
					<div class='col-xs-3'>
					<div class='input-group'>
					<span class='input-group-addon'>บาร์โค้ดสินค้า</span>
					<input type='text' class='form-control' name='barcode_item' id='barcode_item' autofocus />
					</div></div>
					<div class='col-xs-1'>
					<button type='button' id='ok' class='btn btn-default' onclick='submit_detail()'>OK</button>
					</div>
					</div></form><hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
					<table class='table table-striped table-hover'>
					<thead>
						<th width='5%' style='text-align:center;'>ลำดับ</th><th width='30%' style='text-align:center;'>รายการ</th><th width='10%' style='text-align:center;''>จำนวน</th>
						<th width='15%' style='text-align:center;'>คลัง</th><th width='10%' style='text-align:center;'>โซน</th>
						<th width='10%' style='text-align:center;'>วันที่</th><th width='10%' style='text-align:center;'>พนักงาน</th><th width='10%' style='text-align:center;'>การกระทำ</th>
					</thead>";	
					recievedDetail($id_recieved_product);
					echo"</table>";
				}
				

//****************************** จบหน้ารับสินค้าเข้า *********************************//
}else if(isset($_GET['edit'])&&isset($_GET['id_recieved_product'])){
	//********************************************  หน้าแก้ไข  *********************************************//
	$id_recieved_product = $_GET['id_recieved_product']; 
	list($date_add, $role) = dbFetchArray(dbQuery("SELECT date, role FROM tbl_recieved_product WHERE id_recieved_product = $id_recieved_product"));
	echo"	</form>
	<form id='edit_recieved_form' action='controller/storeController.php?edit=y&id_recieved_product=$id_recieved_product' method='post'>
		<div class='row'>
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เลขที่เอกสาร</span>";
					echo"<input type='hidden' name='id_recieved_product' id='id_recieved_product' value='$id_recieved_product' />
					<input type='text' class='form-control' name='recieved_no' id='recieved_no'  value='"; echo getRecievedNO($id_recieved_product); echo"' disabled='disabled'/>
				</div>		
			</div>	
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>อ้างอิง</span>
				 <input type='text' class='form-control'  name='reference' id='reference' value='"; echo getRecievedReference($id_recieved_product); echo"' disabled='disabled'/>
				</div>
			</div>
			<div class='col-xs-2'>
				<div class='input-group'>
					<span class='input-group-addon'>วันที่</span>";
						if(isset($_GET['id_recieved_product'])){
						echo"<input type='hidden' name='date' id='date_value' value='".thaiDate(getRecievedDate($id_recieved_product))."' /><input type='text' class='form-control'  id='date' value='".thaiDate(getRecievedDate($id_recieved_product))."' disabled />"; }else{echo"
				 <input type='text' class='form-control'  name='date' id='date' value='"; echo date('d-m-Y'); echo"' />";}
				 echo"
				</div>
			</div>
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>วัตถุประสงค์</span>
						<select name='role' id='role' class='form-control' disabled='disabled'>
				 			<option value='1'"; if($role == 1){ echo"selected='selected'";} echo">รับเข้าปกติ</option>
							<option value='2'"; if($role == 2){ echo"selected='selected'";} echo">รับเข้าจากการแปรรูป</option>
						</select>
				</div>
			</div>
			<div class='col-xs-1'>
					<button type='button' id='edit_btn' class='btn btn-default'>แก้ไข</button><button type='button' class='btn btn-default' id='update_btn' style='display:none;'>Update</button>
			</div>
         </div>
		 </form>
				<hr style='border-color:#CCC; margin-top: 15px; margin-bottom: 15px;' />
				<form id='detail_form' action='controller/storeController.php?add_detail=y&id_recieved_product=$id_recieved_product' method='post'>
					<div class='row'>
					<div class='col-xs-4 col-xs-offset-4'>
					<div class='input-group'>
					<span class='input-group-addon'>คลัง</span>";
				 if(isset($_GET['id_warehouse'])){	$id_warehouse = $_GET['id_warehouse']; }else{ $id_warehouse ="";}
					echo"
					<select class='form-control' name='id_warehouse' >"; warehouseList($id_warehouse); echo"</select>
					</div>
					</div><div class='col-xs-2'><button type='button' class='btn btn-default' id='change_zone' onclick='reset_zone()' $actived >เปลี่ยนโซน(F2)</button></div>
					</div>
					<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
					<div class='row'>
					<div class='col-xs-3'>
					<div class='input-group'>
					<span class='input-group-addon'>บาร์โค้ดโซน</span><input type='hidden' name='id_zone' id='id_zone' value='$id_zone' />
					<input type='text' class='form-control' name='barcode_zone' id='barcode_zone' $active value='$barcode_zone' />
					</div></div>
					<div class='col-xs-3'>
					<div class='input-group'>
					<span class='input-group-addon'>ชื่อโซน</span>
					<input type='text' class='form-control' name='zone_name' id='zone_name' $active value='$name_zone' />
					</div></div>
					<div class='col-xs-2'>
					<div class='input-group'>
					<span class='input-group-addon'>จำนวน</span><input type ='hidden' name='date_add' value='$date_add' />
					<input type='text' class='form-control' name='qty' id='qty' value='1' />
					</div></div>
					<div class='col-xs-3'>
					<div class='input-group'>
					<span class='input-group-addon'>บาร์โค้ดสินค้า</span>
					<input type='text' class='form-control' name='barcode_item' id='barcode_item' autofocus />
					</div></div>
					<div class='col-xs-1'>
					<button type='button' id='ok' class='btn btn-default' onclick='submit_detail()'>OK</button>
					</div>
					</div>
					</form>
					<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
					<table class='table table-striped table-hover'>
					<thead>
						<th width='5%' style='text-align:center;'>ลำดับ</th><th width='30%' style='text-align:center;'>รายการ</th><th width='10%' style='text-align:center;''>จำนวน</th>
						<th width='15%' style='text-align:center;'>คลัง</th><th width='10%' style='text-align:center;'>โซน</th>
						<th width='10%' style='text-align:center;'>วันที่</th><th width='10%' style='text-align:center;'>พนักงาน</th><th width='10%' style='text-align:center;'>การกระทำ</th>
					</thead>";	
					recievedDetail($id_recieved_product);
					echo"</table>";
	//********************************************* จบหน้าแก้ไข ******************************************//
	
		}else if(isset($_GET['view_detail'])&&isset($_GET['id_recieved_product'])){
//******************************** หน้ารายละเอียดการรับสินค้าเข้า ***********************//
$id_recieved_product = $_GET['id_recieved_product']; 
echo"	<div class='row'>
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เลขที่เอกสาร</span>
					<input type='hidden' name='id_recieved_product' value='$id_recieved_product' />
					<input type='text' class='form-control' name='recieved_no' id='recieved_no'  value='"; echo getRecievedNO($id_recieved_product); echo"' disabled='disabled'/>
				</div>		
			</div>	
			<div class='col-xs-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เลขที่อ้างอิง</span>
				 <input type='text' class='form-control'  name='reference' id='reference' value='"; echo getRecievedReference($id_recieved_product); echo"' disabled='disabled'/>
				</div>
			</div>
			<div class='col-xs-2'>
				<div class='input-group'>
					<span class='input-group-addon'>วันที่</span>
						<input type='text' class='form-control'  name='date' id='date' value='"; echo date('d-m-Y',strtotime(getRecievedDate($id_recieved_product))); echo"' disabled='disabled' />
				</div>
			</div>
			<div class='col-xs-2 col-xs-offset-2'>
				<p class='pull-right'><a href='controller/storeController.php?print&id_recieved_product=$id_recieved_product' ><span class='glyphicon glyphicon-print' style='color:#5cb85c; font-size:30px;'></span></a></p>
			</div>
         </div>
				</form>
				<hr style='border-color:#CCC; margin-top: 15px; margin-bottom: 15px;' />
					<table class='table table-striped table-hover'>
					<thead>
						<th width='10%' style='text-align:center;'>ลำดับ</th><th width='30%' style='text-align:center;'>รายการ</th><th width='10%' style='text-align:center;''>จำนวน</th>
						<th width='15%' style='text-align:center;'>คลัง</th><th width='10%' style='text-align:center;'>โซน</th>
						<th width='10%' style='text-align:center;'>วันที่</th><th width='15%' style='text-align:center;'>พนักงาน</th>
					</thead>";	
					getRecievedDetail($id_recieved_product);
					echo"</table>";
			

//******************************** จบหน้ารายละเอียด ************************************//

}else{
	$paginator = new paginator();
		echo"<form  method='post' id='form'>
		<div class='row'>
			<div class='col-xs-2 col-xs-offset-4'>
				<div class='input-group'>
					<span class='input-group-addon'> จาก :</span>
					<input type='text' class='form-control' name='from_date' id='from_date'  value='";
					 if(isset($_POST['from_date']) && $_POST['to_date'] && $_POST['from_date'] && $_POST['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($_POST['from_date']));} else { echo "เลือกวัน";} 
					 echo "'/>
				</div>		
			</div>	
			<div class='col-xs-2 '>
				<div class='input-group'>
					<span class='input-group-addon'>ถึง :</span>
				 <input type='test' class='form-control'  name='to_date' id='to_date' value='";
				  if(isset($_POST['from_date']) && $_POST['to_date'] && $_POST['from_date'] && $_POST['to_date'] !="เลือกวัน"){ echo date('d-m-Y',strtotime($_POST['to_date']));} else{ echo "เลือกวัน";}  echo"' />
				</div>
			</div>
			<div class='col-xs-1'>
					<button type='button' class='btn btn-default' onclick='validate()'>แสดง</button>
			</div>
			<div class='col-xs-1'>
					<a href='index.php?content=product_in'><button type='button' class='btn btn-default'><i class='fa fa-refresh'></i></button></a>
			</div>	
         </div>
				</form>
				<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:0px;' />";
				$view = "";
		if(isset($_POST['from_date'])){	$from = date('Y-m-d',strtotime($_POST['from_date'])); }else{ $from = "1970-01-01";} if(isset($_POST['to_date'])){  $to =date('Y-m-d',strtotime($_POST['to_date'])); }else{ $to = "2150-12-31";}
		/*if($from==""){
			if($to==""){
				$view = getConfig("VIEW_ORDER_IN_DAYS");
			}
		}
		if($view !=""){
			$date = getLastDays($view);
			$from = $date['from'];
			$to = $date['to'];
		}*/
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		$paginator->Per_Page("tbl_recieved_product","WHERE (date BETWEEN '$from' AND '$to') ORDER BY date DESC",$get_rows);
	echo "<div class='row' id='result'><div class='col-lg-12 col-md-12 col-sm-12 col-sx-12' id='search-table'>";
	$paginator->display($get_rows,"index.php?content=product_in");
	echo"
	<table class='table table-striped table-hover'>
    	<thead style='background-color:#48CFAD;'>
        	<th style='width:10%; text-align:center;'>ลำดับ</th><th style='width:20%;'>รายการ</th><th style='width:20%;'>อ้างอิง</th>
            <th style='width:10%; text-align:center;'>จำนวน</th><th style='width:10%; text-align:center;'>วันที่</th>
			<th style='width:10%; text-align:center;'>พนักงาน</th><th style='width:10%; text-align:center;'>สถานะ</th>
			<th colspan='2' style='width:10%; text-align:center;'>การกระทำ</th>
        </thead>";
		$result = dbQuery("SELECT id_recieved_product, recieved_product_no, reference_no, date, id_employee, status FROM tbl_recieved_product WHERE (date BETWEEN '$from' AND '$to' ) ORDER BY date DESC LIMIT ".$paginator->Page_Start." , ".$paginator->Per_Page); 
		$i=0;
		$n=1;
		$row = dbNumRows($result);
		if($row<1){ echo"<tr><td colspan='8' align='center'><h3>ไม่มีรายการในช่วงนี้</h3></td></tr>";
		}else{
		while($i<$row){
			list($id, $recieved_no, $reference, $date, $id_employee, $status) = dbFetchArray($result);
			list($qty)= dbFetchArray(dbQuery("SELECT SUM(qty) FROM tbl_recieved_detail WHERE id_recieved_product = $id"));
			$employee = new employee($id_employee);
			echo "<tr>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=product_in&view_detail=y&id_recieved_product=$id'\">$n</td>
			<td style='cursor:pointer;' onclick=\"document.location='index.php?content=product_in&view_detail=y&id_recieved_product=$id'\">$recieved_no</td>
			<td style='cursor:pointer;' onclick=\"document.location='index.php?content=product_in&view_detail=y&id_recieved_product=$id'\">$reference</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=product_in&view_detail=y&id_recieved_product=$id'\">"; 
			if($qty==NULL){ echo"0";}else{ echo $qty; } echo"</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=product_in&view_detail=y&id_recieved_product=$id'\">"; echo thaiDate($date); echo"</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=product_in&view_detail=y&id_recieved_product=$id'\">".$employee->first_name."</td>";
			 if($status == 1){echo" 
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=product_in&view_detail=y&id_recieved_product=$id'\">บันทึกแล้ว</td>";
			}else{ echo"
			<td align='center' style='cursor:pointer; color: red;' onclick=\"document.location='index.php?content=product_in&view_detail=y&id_recieved_product=$id'\">ยังไม่บันทึก</td>";
			} echo"
			<td align='center'>
				<a href='index.php?content=product_in&edit=y&id_recieved_product=$id' $can_edit>
					<button class='btn btn-warning btn-sx'>
						<span class='glyphicon glyphicon-pencil' style='color: #fff;'></span>
					</button>
				</a>
			</td>
			<td align='center'>
				<a href='controller/storeController.php?delete=y&id_recieved_product=$id' $can_delete>
					<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $recieved_no ? ');\">
						<span class='glyphicon glyphicon-trash' style='color: #fff;'></span>
					</button>
				</a>
			</td>
			</tr>";
			$i++;
			$n++;
		}
		echo"       
    </table>";
	echo $paginator->display_pages();
		echo "<br><br>
</div> </div>";
}
}
?>	
</div>
<script language="javascript">  
$(document).load(function(e) {
  var ss = $("#barcode_zone").val();
	if(ss != ""){
		$("#barcode_item").focus();
	}  
});
	
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
    $("#date").datepicker({
      dateFormat: 'dd-mm-yy'
    });
  });
 $("#date").change(function(e) {
    var value = $("#date").val();
	$("#date_value").val(value);
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
$("#barcode_zone").focus(); /// โฟกัสที่ช่องบาร์โค้ดโซน
//// เมื่อยิงบาร์โค้ด หรือ ใส่รหัสด้วยมือแล้ว enter////
///************  บาร์โค้ดโซน **********************//
$("#barcode_zone").bind("enterKey",function(){
	if($("#barcode_zone").val() != ""){
	$("#barcode_item").focus();
	}
});
$("#barcode_zone").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
///***************** รหัสโซน *********************///
$("#zone_name").bind("enterKey",function(){
	if($("#zone_name").val() != ""){
	$("#barcode_item").focus();
	}
});
$("#zone_name").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
$("#zone_name").autocomplete({
	source:"controller/storeController.php?get_zone",
	autoFocus: true,
	close: function(event,ui){
			var data = $(this).val();
			var arr = data.split(':');
			var id = arr[0];
			var name = arr[1];
			$("#id_zone").val(id);
			$(this).val(name);
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
///********************* รหัสสินค้า ***********************///
$("#barcode_item").bind("enterKey",function(){
	if($("#barcode_item").val() != ""){
	$("#ok").click();
	}
});
$("#barcode_item").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
//****************ตรวจสอบรายการ*****************************//
function submit_detail(){
	var barcode_zone = $("#barcode_zone").val();
	var zone_name = $("#zone_name").val();
	var qty = $("#qty").val();
	var barcode_item = $("#barcode_item").val();
	if(barcode_zone ==""){
		if(zone_name==""){  
		alert("ต้องระบุบาร์โค้ดโซน หรือ ชื่อโซน อย่างน้อย 1 อย่าง"); 
		}else if(qty==""){
			alert("ยังไม่ได้ใส่จำนวน");
		}else if(qty<=0){
			alert("จำนวนที่จะรับเข้าต้องมากกว่า 0 ");
		}else if(barcode_item ==""){
			alert("อย่าลืมยิงบาร์โค้ดสินค้า");
		}else{
			$("#detail_form").submit();
		}
	}else if(qty==""){
			alert("ยังไม่ได้ใส่จำนวน");
		}else if(qty<=0){
			alert("จำนวนที่จะรับเข้าต้องมากกว่า 0 ");
		}else if(barcode_item ==""){
			alert("อย่าลืมยิงบาร์โค้ดสินค้า");
		}else{
			$("#detail_form").submit();
	}
}
function submit_stock(){
	$("#recieved_form").submit();
}
function edit_stock(){
	$("#edit_recieved_form").submit();
}
function reset_zone(){
	var barcode_val = "";
	var id_zone_val = "";
	var zone_name_val = "";
	$("#barcode_zone").val(barcode_val);
	$("#id_zone").val(id_zone_val);
	$("#zone_name").val(zone_name_val);
	$("#barcode_zone").removeAttr("disabled");
	$("#zone_name").removeAttr("disabled");
	$("#barcode_zone").focus();
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
$("#edit_btn").click(function(e) {
	$(this).css("display","none");
	$("#reference").removeAttr("disabled");
    $("#date").removeAttr("disabled");
	$("#role").removeAttr("disabled");
	$("#update_btn").css("display","");
});
$("#update_btn").click(function(e) {
    var date = $("#date").val();
	var reference = $("#reference").val();
	var role = $("#role").val();
	var id_recieved = $("#id_recieved_product").val();
	if(date ==""){
		alert("ยังไม่ได้ระบุวันที่");
	}else{
		$.ajax({
			url:"controller/storeController.php?edit_recieved&id_recieved_product="+id_recieved, 
			data:{ doc_date: date, ref: reference, id_role: role }, cache:false, type:"POST",
			success: function(dataset){
				if(dataset.trim() =="ok"){
				$("#reference").attr("disabled","disabled");
				$("#date").attr("disabled","disabled");
				$("#role").attr("disabled","disabled");
				$("#update_btn").css("display","none");
				$("#edit_btn").css("display","");
			}else{
				alert("ปรับปรุงรายการไม่สำเร็จ");
				}
			}
		});	
	}
});
function get_row(){
	$("#rows").submit();
}
      </script>
