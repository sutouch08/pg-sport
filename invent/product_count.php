<?php 
if(isset($_GET['view_stock_diff'])){
	$id_check = $_GET['id_check'];
	$checkstock = new checkstock();
	$checkstock->detail($id_check);
	$page_menu = "invent_sale";
	$page_name = $checkstock->name_check;
}else{
	$page_menu = "invent_sale";
	$page_name = "ตรวจสอบยอดสินค้าจากการตรวจนับ";
}
	$id_tab = 32;
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
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-folder-close"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
       <?php 
	  if(isset($_GET['view_stock_diff'])){
		  $id_employee = $_COOKIE['user_id'];
		   echo"
		   <li><a href='index.php?content=ProductCount' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>
			<li><a href='controller/reportController.php?report_check_stock=y&id_check=$id_check' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' id='gogo'><span class='fa fa-file-excel-o' style='color:#5cb85c; font-size:35px;'></span><br />ส่งออก</button></a></li>";
			if($checkstock->status == "2"){
			echo "<li><a href='controller/checkstockController.php?save_diff&id_check=$id_check&id_employee=$id_employee' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' id='gogo'><span class='fa fa-floppy-o' style='color:#5cb85c; font-size:35px;'></span><br />บันทึกยอดต่าง</button></a></li>";
			}
	   }else{
		   $value = dbNumRows(dbQuery("SELECT id_check FROM tbl_check WHERE status = 1"));
		   if($value > 0){
			//   echo"
		 //  <li $can_add><a href='index.php?content=OpenCheck&close=y' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-eye-close' style='color:#5cb85c; font-size:30px;'></span><br />ปิดการตรวจสินค้า</button></a></li>";
		   }else{
		  // echo"
		 //  <li $can_add><a href='index.php?content=OpenCheck&open=y' style='text-align:center; background-color:transparent; padding-bottom:0px;' ><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-eye-open' style='color:#5cb85c; font-size:30px;'></span><br />เปิดการตรวจสินค้า</button></a></li>";
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
if(isset($_GET['view_stock_diff'])){
	$id_check = $_GET['id_check'];
	echo "<div class='row'>
<div class='col-lg-12'><ul class='nav nav-tabs' role='tablist' style='background-color:#EEE'>
<li class='active'><a href='#all' role='tab' data-toggle='tab'>ทั้งหมด</a></li>";

	$sql = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = 0 AND level_depth = 1 ORDER BY position ASC");
				$row = dbNumRows($sql);
				$i=0;
				while($i<$row){
				list($id_category, $category_name) = dbFetchArray($sql);
				$sqr = dbQuery("SELECT id_category, category_name FROM tbl_category WHERE parent_id = $id_category ORDER BY position ASC");
				$rs = dbNumRows($sqr);
				$n=0;
				if($rs<1){
					echo"<li calss=''><a href='#cat-$id_category' role='tab' data-toggle='tab'>$category_name</a>";
				}else{				
				echo"<li class='dropdown'><a id='ul-$id_category' class='dropdown-toggle' data-toggle='dropdown' href='#'>$category_name<span class='caret'></span></a>";
				echo"<ul class='dropdown-menu' role='menu' aria-labelledby='ul-$id_category'>";
				echo"<li class=''><a href='#cat-$id_category' tabindex='-1' role='tab' data-toggle='tab'>$category_name</a></li>";     
				while($n<$rs){
				list($id_sub_category, $sub_category_name) = dbFetchArray($sqr);
				echo" <li class=''><a href='#cat-$id_sub_category' tabindex='-1' role='tab' data-toggle='tab'>$sub_category_name</a></li>";
				$n++;
				}
				echo"</ul></li>";
				}	
				echo "</li>";
				$i++;
				}
echo "</ul>
</div>
</div>
<div class='row'><div class='col-lg-12'>
<div class='tab-content'>
<div class='tab-pane active' id='all'>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />";
	list($total_qty) = dbFetchArray(dbQuery("SELECT SUM(qty_before) FROM tbl_stock_check WHERE id_check = $id_check"));
	list($total_qty_check) = dbFetchArray(dbQuery("SELECT SUM(qty_after) FROM tbl_stock_check WHERE id_check = $id_check"));
		echo "<h4>จำนวนสต็อกทั้งหมด &nbsp;&nbsp;<span style='color:red;'>".number_format($total_qty)." </span>&nbsp;&nbsp; หน่วย   จำนวนที่เช็คได้ &nbsp;&nbsp;<span style='color:blue;'>".number_format($total_qty_check)." </span>&nbsp;&nbsp; หน่วย ยอดต่าง &nbsp;&nbsp;<span style='color:blue;'>".number_format($total_qty_check-$total_qty)." </span>&nbsp;&nbsp; หน่วย</h4>";
	echo"<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />";
	$sql = dbQuery("SELECT id_product, product_cost FROM product_table ORDER BY product_code ASC");
	$row = dbNumRows($sql); 
	if($row>0){
		$i=0;
		while($i<$row){
			list($id_product, $product_cost) = dbFetchArray($sql);
			$product = new product();
			$product->product_detail($id_product);
			$qty = qty_check_product_before($id_check,$id_product);
			$total_qty_check_product = qty_check_product_after($id_check,$id_product);
			$table_w = "style='width:800px;'";
			echo"<div class='item2 col-lg-2 col-md-2 col-sm-3 col-xs-4'>			
			<div class='modal fade' id='".$product->id_product."' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
							<div class='modal-dialog' $table_w>
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
									  </div>
									  <div class='modal-body'>";
										 $checkstock->table_stock_diff($id_product,$id_check);
										echo "</div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
									  </div>
									</div>
							 </div>
								</div>
			<div class='product'>
			<div class='image' style='text-align:center;'><a href='#' data-toggle='modal' data-target='#".$product->id_product."'>".$product->getCoverImage($product->id_product,2,"")."</a></div>
			<div class='description'>
				<h5 align='center'><a href='#' data-toggle='modal' data-target='#".$product->id_product."'>".$product->product_code."</a></h5>	
			</div>
			<div class='price' style='text-align:center;'>".number_format($qty)." :  ".number_format($total_qty_check_product)." : <span style='color:red;'>".number_format($total_qty_check_product-$qty)." </span> </div>
			</div>";
			 echo "</div>";
			$i++;
		}
		//echo"</div>";
	}else{ 
		echo"<h4 style='align:center;'>ยังไม่มีรายการสินค้า</h4>";
	}
?>
</div>
<?php 
	$query = dbQuery("SELECT id_category, category_name FROM tbl_category");
	$rc = dbNumRows($query);
	while($c = dbFetchArray($query)){
		$id_category = $c['id_category'];
		$cate_name = $c['category_name'];
		echo"<div class='tab-pane' id='cat-$id_category'>";
	$total_qty = qty_check_category_before($id_check,$id_category);
	$total_qty_check_category = qty_check_category_after($id_check,$id_category);
	echo "<h4>จำนวน$cate_name &nbsp;&nbsp;<span style='color:red;'>".number_format($total_qty)." </span>&nbsp;&nbsp; หน่วย   จำนวนที่เช็คได้ &nbsp;&nbsp;<span style='color:blue;'>".number_format($total_qty_check_category)." </span>&nbsp;&nbsp; หน่วย ยอดต่าง &nbsp;&nbsp;<span style='color:blue;'>".number_format($total_qty_check_category-$total_qty)." </span>&nbsp;&nbsp; หน่วย</h4>";
	echo"<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />";
		$sql = dbQuery("SELECT tbl_category_product.id_product, product_cost FROM tbl_category_product LEFT JOIN product_table ON tbl_category_product.id_product = product_table.id_product WHERE tbl_category_product.id_category = $id_category ORDER BY product_code ASC");
		$row = dbNumRows($sql); 
		if($row>0){
			$i=0;
			while($i<$row){
				list($id_product, $product_cost) = dbFetchArray($sql);
				$product = new product();
				$product->product_detail($id_product);
				$qty = qty_check_product_before($id_check,$id_product);
				$total_qty_check_product = qty_check_product_after($id_check,$id_product);
				$table_w = "style='width:800px;'";
				echo"<div class='item2 col-lg-2 col-md-2 col-sm-3 col-xs-4'>			
				<div class='modal fade' id='".$product->id_product."$id_category' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								<div class='modal-dialog' $table_w>
										<div class='modal-content'>
										  <div class='modal-header'>
											<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
											
										  </div>
										  <div class='modal-body'>";  $checkstock->table_stock_diff($id_product,$id_check);
										   echo"</div>
										  <div class='modal-footer'>
											<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
										  </div>
										</div>
								 </div>
									</div>
				<div class='product'>
				<div class='image' style='text-align:center;'><a href='#' data-toggle='modal' data-target='#".$product->id_product."$id_category'>".$product->getCoverImage($product->id_product,2,"")."</a></div>
				<div class='description'>
					<h5 align='center'><a href='#' data-toggle='modal' data-target='#".$product->id_product."$id_category'>".$product->product_code."</a></h5>	
				</div>
				<div class='price' style='text-align:center;'>".number_format($qty)." :  ".number_format($total_qty_check_product)." : <span style='color:red;'>".number_format($total_qty_check_product-$qty)." </span> </div>
				</div></div>";
				$i++;
			}
			//echo"</div>";
		}else{ 
			echo"<br/><h4 style='text-align:center;'>ยังไม่มีรายการสินค้า</h4>";
		}
		echo "</div>";
	}
echo "</div></div>";

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
        </thead>";$view = "";
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
		$result = dbQuery("SELECT id_check,name_check,status,date_start,date_stop FROM tbl_check WHERE (date_start BETWEEN '$from' AND '$to') ORDER BY id_check DESC");
		$i=0;
		$n=1;
		$row = dbNumRows($result);
		while($i<$row){
			list($id_check, $name_check, $status, $date_start, $date_stop) = dbFetchArray($result);
			echo "<tr>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductCount&view_stock_diff=y&id_check=$id_check\">$id_check</td>
			<td style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductCount&view_stock_diff=y&id_check=$id_check'\">$name_check</td>
			<td style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductCount&view_stock_diff=y&id_check=$id_check'\">";if($status == "1"){echo "กำลังตรวจนับสินค้า";}else if($status == "3"){echo "บันทึกยอดต่างแแล้ว";}else{echo "ตรวจนับเสร็จสิน";}echo "</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductCount&view_stock_diff=y&id_check=$id_check'\">".showDate($date_start)."</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductCount&view_stock_diff=y&id_check=$id_check'\">";if($date_stop == "0000-00-00"){}else{echo showDate($date_stop);}echo "</td>
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