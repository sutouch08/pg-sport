<?php 
	$page_menu = "invent_product_adjust";
	$page_name = "ปรับปรุงยอดสินค้า";
	$id_tab = 11;
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
	<div class="col-sm-6"><h3><span class="glyphicon glyphicon-tasks"></span>&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
      <?php 
	  if(isset($_GET['view_detail'])){
		   echo"
       		<li><a href='index.php?content=ProductAdjust' style='text-align:center; background-color:transparent;' ><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
			echo"
       	<li $can_add><a href='index.php?content=ProductAdjust&add=y' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />เพิ่ม</button></a></li>";
	  }else if(isset($_GET['add'])){
		  if(isset($_GET['id_adjust'])){
			  $id_adjust = $_GET['id_adjust'];
			  echo"
       		<li><a href='index.php?content=ProductAdjust' style='text-align:center; background-color:transparent;' ><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
			  echo"
       	<li><a href='index.php?content=ProductAdjust&add=y&add_dif=y&id=$id_adjust' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-download' style='color:#5cb85c; font-size:30px;'></span><br />โหลด</button></a></li>";
			  echo"
       		<li><a href='controller/productAdjustController.php?adjust=y&id_adjust=$id_adjust' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' ><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />ปรับยอด</button></a></li>";
		  }else if(isset($_GET['id'])){
			  $id_adjust = $_GET['id'];
			  echo"
       		<li><a href='index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
			  echo"
       	<li><a href='#' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='document.loaddiff.submit();' ><span class='glyphicon glyphicon-download' style='color:#5cb85c; font-size:30px;'></span><br />โหลด</button></a></li>";
		  }else{
			   echo"
       		<li><a href='index.php?content=ProductAdjust' style='text-align:center; background-color:transparent;' ><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
		   echo"
       		<li><a href='#' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link' onclick='document.add.submit();'><span class='glyphicon glyphicon-floppy-disk' style='color:#5cb85c; font-size:30px;'></span><br />บันทึก</button></a></li>";
		  }
	  }else if(isset($_GET['ListDiff'])){
		   echo"
       		<li><a href='index.php?content=ProductAdjust' style='text-align:center; background-color:transparent;' ><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-arrow-left' style='color:#5cb85c; font-size:30px;'></span><br />กลับ</button></a></li>";
	  }else{
	  echo"
       	<li $can_add><a href='index.php?content=ProductAdjust&add=y' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />เพิ่ม</button></a></li>";
		echo"
       	<li $can_add><a href='index.php?content=ProductAdjust&ListDiff=y' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-list-alt' style='color:#5cb85c; font-size:30px;'></span><br />ListDiff</button></a></li>";
	  }
		?>
      
       </ul>
    </div>
</div>
<hr style="border-color:#CCC; margin-top: 0px; margin-bottom:8px;" />
<!-- End page place holder -->

<div class='col-sm-12'>
<?php
		//---------------------------------------------view detail-------------------------------------------------------
	if(isset($_GET['view_detail'])){
		if(isset($_GET['id_adjust'])){
			$id_adjust = $_GET['id_adjust'];
			list($adjust_no,$adjust_reference,$adjust_note,$adjust_date) = dbFetchArray(dbQuery("SELECT adjust_no,adjust_reference,adjust_note,adjust_date FROM tbl_adjust where id_adjust = '$id_adjust'"));
		}else{
			$id_adjust = "";
		}
		echo"
		<div class='row'>
			<div class='col-sm-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เลขที่เอกสาร</span>
					<input type='text' class='form-control' name='adjust_no' id='adjust_no'  value='";if($id_adjust != ""){ echo "$adjust_no'";}else{echo "".newAdjustNO()."'";} echo " disabled='disabled'/>
				</div>		
			</div>	
			<div class='col-sm-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เลขที่อ้างอิง</span>
				 <input type='text' class='form-control'  name='adjust_reference' id='adjust_reference' value='";if($id_adjust != ""){echo "$adjust_reference'";echo "disabled='disabled'";}else{echo "' autofocus";}echo "/>
				</div>
			</div>
			<div class='col-sm-2'>
				<div class='input-group'>
					<span class='input-group-addon'>วันที่</span>";
				echo"
				 <input type='text' class='form-control'  name='adjust_date' id='adjust_date' value='";if($id_adjust != ""){echo "".showDate($adjust_date)."'";echo "disabled='disabled'";}else{ echo "".date('d-m-Y')."'";} echo" />";
				 echo"
				</div>
			</div>
			<div class='col-sm-1'>
				
			</div>	
         </div>
		 <br>
		 <div class='row'>
		 <div class='col-sm-6'>
				<div class='input-group'>
					<span class='input-group-addon'>หมายเหตุ</span>
				 <input type='text' class='form-control'  name='note' id='note' value='";if($id_adjust != ""){echo $adjust_note;echo "' disabled='disabled'";}else{echo "'";}echo "/>
				</div>
		 </div>
		 </div>
				";
				echo "<hr style='border-color:#CCC; margin-top: 8px; margin-bottom:8px;' />
				<table class='table table-striped table-hover'>
					<thead>
						<th width='5%' style='text-align:center;'>ลำดับ</th><th width='15%' style='text-align:center;'>บาร์โค้ด</th><th width='25%' style='text-align:left;'>สินค้า</th>
						<th width='15%' style='text-align:left;'>คลัง</th><th width='10%' style='text-align:left;'>โซน</th>
						<th width='10%' style='text-align:right;'>จำนวนเพิ่ม</th><th width='10%' style='text-align:right;'>จำนวนลด</th>
					</thead>";	
						$result = dbQuery("SELECT id_adjust_detail,id_adjust,id_product_attribute,barcode,reference,id_warehouse,warehouse_name,id_zone,barcode_zone,zone_name,adjust_qty_add,adjust_qty_minus FROM adjust_datail_table where id_adjust = '$id_adjust' ORDER BY id_adjust_detail DESC");
						$i=0;
						$n=1;
						$row = dbNumRows($result);
						while($i<$row){
							list($id_adjust_detail, $id_adjust, $id_product_attribute, $barcode, $reference, $id_warehouse, $warehouse_name, $id_zone ,$barcode_zone ,$zone_name ,$adjust_qty_add ,$adjust_qty_minus) = dbFetchArray($result);
							echo "<tr>
							<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">$n</td>
							<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">$barcode</td>
							<td style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">$reference</td>
							<td align='left' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">$warehouse_name</td>
							<td align='left' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">$zone_name</td>
							<td align='right' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\" >$adjust_qty_add</td>
							<td align='right' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\" >$adjust_qty_minus</td>
							</tr>";
							$i++;
							$n++;
						}
					echo"</table>";
	}else if(isset($_GET['add'])){
		if(isset($_GET['id_adjust'])){
			$id_adjust = $_GET['id_adjust'];
			list($adjust_no,$adjust_reference,$adjust_note,$adjust_date) = dbFetchArray(dbQuery("SELECT adjust_no,adjust_reference,adjust_note,adjust_date FROM tbl_adjust where id_adjust = '$id_adjust'"));
		}else if(isset($_GET['id'])){
			$id_adjust = $_GET['id'];
			list($adjust_no,$adjust_reference,$adjust_note,$adjust_date) = dbFetchArray(dbQuery("SELECT adjust_no,adjust_reference,adjust_note,adjust_date FROM tbl_adjust where id_adjust = '$id_adjust'"));
		}else{
			$id_adjust = "";
		}
		echo"	<form method='post' name='add' action='controller/productAdjustController.php?add=y' >
		<div class='row'>
			<div class='col-sm-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เลขที่เอกสาร</span>
					<input type='text' class='form-control' name='adjust_no' id='adjust_no'  value='";if($id_adjust != ""){ echo "$adjust_no'";}else{echo "".newAdjustNO()."'";} echo " disabled='disabled'/>
				</div>		
			</div>	
			<div class='col-sm-3'>
				<div class='input-group'>
					<span class='input-group-addon'>เลขที่อ้างอิง</span>
				 <input type='text' class='form-control'  name='adjust_reference' id='adjust_reference' value='";if($id_adjust != ""){echo "$adjust_reference'";echo "disabled='disabled'";}else{echo "' autofocus";}echo "/>
				</div>
			</div>
			<div class='col-sm-2'>
				<div class='input-group'>
					<span class='input-group-addon'>วันที่</span>";
				echo"
				 <input type='text' class='form-control'  name='adjust_date' id='adjust_date' value='";if($id_adjust != ""){echo "".showDate($adjust_date)."'";echo "disabled='disabled'";}else{ echo "".date('d-m-Y')."'";} echo" />";
				 echo"
				</div>
			</div>
			<div class='col-sm-1'>
				
			</div>	
         </div>
		 <br>
		 <div class='row'>
		 <div class='col-sm-6'>
				<div class='input-group'>
					<span class='input-group-addon'>หมายเหตุ</span>
				 <input type='text' class='form-control'  name='note' id='note' value='";if($id_adjust != ""){echo $adjust_note;echo "' disabled='disabled'";}else{echo "'";}echo "/>
				</div>
		 </div>
		 </div>
				</form>";
			if(isset($_GET['id_adjust'])){
				echo "<hr style='border-color:#CCC; margin-top: 8px; margin-bottom:8px;' />";
				if(isset($_GET['edit'])){
					$id_adjust_detail_edit = $_GET['id_adjust_detail'];
					list($barcode,$barcode_zone,$zone_name,$adjust_qty_add,$adjust_qty_minus,$warehouse) = dbFetchArray(dbQuery("SELECT barcode,barcode_zone,zone_name,adjust_qty_add,adjust_qty_minus,id_warehouse FROM adjust_datail_table where id_adjust_detail = '$id_adjust_detail_edit'"));
					echo "<form id='detail_form' action='controller/productAdjustController.php?edit_detail=y&id_adjust=$id_adjust' method='post'>";
				}else{
					echo "<form id='detail_form' action='controller/productAdjustController.php?add_detail=y&id_adjust=$id_adjust' method='post'>";
				}
					echo "<div class='row'>
					<div class='col-sm-4 col-sm-offset-4'>
					<div class='input-group'>
					<span class='input-group-addon'>คลัง</span>";
				 if(isset($_GET['edit'])){	$id_warehouse = $warehouse; }else{ $id_warehouse ="";}
					echo"
					<select class='form-control' name='id_warehouse' >"; warehouseList($id_warehouse); echo"</select>
					</div>
					</div></div>
					<hr style='border-color:#CCC; margin-top: 8px; margin-bottom:8px;' />
					<div class='row'><div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
					<div class='col-sm-3' style='padding: 5px 5px;'><div class='input-group'>
					<span class='input-group-addon' style='font-size:1vmin'>บาร์โค้ดสินค้า</span>
					<input type='hidden' name='id_adjust' id='id_adjust' value='$id_adjust' />
					<input type='hidden' name='adjust_no' id='adjust_no' value='$adjust_no' />";
					if(isset($_GET['edit'])){
						echo "<input type='hidden' name='id_adjust_detail' id='id_adjust_detail' value='$id_adjust_detail_edit' />";
					}
					echo "<input type='text' class='form-control' name='barcode' id='barcode' value='";if(isset($_GET['edit'])){ echo $barcode;} echo "'onkeypress='checknumber()'  />
					</div></div>
					<div class='col-sm-3' style='padding: 5px 5px;'>
					<div class='input-group'>
					<span class='input-group-addon' style='font-size:1vmin'>บาร์โค้ดโซน</span>
					<input type='text' class='form-control' name='barcode_zone' id='barcode_zone' value='";if(isset($_GET['edit'])){ echo $barcode_zone;} echo "'onkeypress='checknumber();keybarcode()' />
					</div></div>
					<div class='col-sm-2' style='padding: 5px 5px;'><div class='input-group'>
					<span class='input-group-addon' style='font-size:1vmin'>ชื่อโซน</span>
					<input type='text' class='form-control' name='zone_name' id='zone_name' value='";if(isset($_GET['edit'])){ echo $zone_name;} echo "' onkeypress='keyzone()' />
					</div></div>
					<div class='col-sm-1' style='padding: 5px 5px; width:12.5%;'><div class='input-group'>
					<span class='input-group-addon' style='font-size:1vmin'>เพิ่ม</span>
					<input type='text' class='form-control' name='adjust_qty_add' id='adjust_qty_add' value='";if(isset($_GET['edit'])){ echo $adjust_qty_add;} echo "'onkeypress='checknumber()' />
					</div></div>
					<div class='col-sm-1' style='padding: 5px 5px; width:12.5%;'><div class='input-group'>
					<span class='input-group-addon' style='font-size:1vmin'>ลด</span>
					<input type='text' class='form-control' name='adjust_qty_minus' id='adjust_qty_minus' value='";if(isset($_GET['edit'])){ echo $adjust_qty_minus;} echo "'onkeypress='checknumber()' />
					</div></div>
					<div class='col-sm-1' style='padding: 5px 5px;'>
					<button type='button' id='ok' class='btn btn-default btn-block' onclick='submit_detail()'>OK</button>
					</div></div>
					</div></form>";
						if(isset($_GET['message'])){
							$message = $_GET['message'];
							echo"<div class='alert alert-success' align='center'>$message</div>";
						}
				echo "<hr style='border-color:#CCC; margin-top: 8px; margin-bottom:8px;' />
				<table class='table table-striped table-hover'>
					<thead>
						<th width='5%' style='text-align:center;'>ลำดับ</th><th width='15%' style='text-align:center;'>บาร์โค้ด</th><th width='25%' style='text-align:left;'>สินค้า</th>
						<th width='15%' style='text-align:left;'>คลัง</th><th width='15%' style='text-align:left;'>โซน</th>
						<th width='8%' style='text-align:right;'>จำนวนเพิ่ม</th><th width='8%' style='text-align:right;'>จำนวนลด</th><th style='text-align:center;'></th>
					</thead>";	
						$result = dbQuery("SELECT id_adjust_detail,id_adjust,id_product_attribute,barcode,reference,id_warehouse,warehouse_name,id_zone,barcode_zone,zone_name,adjust_qty_add,adjust_qty_minus,status_up FROM adjust_datail_table where id_adjust = '$id_adjust' ORDER BY id_adjust_detail DESC");
						$i=0;
						$row = dbNumRows($result);
						$n=$row;
						while($i<$row){
							list($id_adjust_detail, $id_adjust, $id_product_attribute, $barcode, $reference, $id_warehouse, $warehouse_name, $id_zone ,$barcode_zone ,$zone_name ,$adjust_qty_add ,$adjust_qty_minus,$status_up) = dbFetchArray($result);
							echo "<tr>
							<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">$n</td>
							<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">$barcode</td>
							<td style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">$reference</td>
							<td align='left' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">$warehouse_name</td>
							<td align='left' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">$zone_name</td>
							<td align='right' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\" >$adjust_qty_add</td>
							<td align='right' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\" >$adjust_qty_minus</td>
							<td align='right' ><a href='index.php?content=ProductAdjust&add=y&edit=y&id_adjust=$id_adjust&id_adjust_detail=$id_adjust_detail' style='text-align:center;";if($status_up == "1"){ echo "display:none;";}echo "background-color:transparent;'><span class='glyphicon glyphicon-pencil' style='color:#5cb85c; font-size:16px;'></span></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='controller/productAdjustController.php?drop_adjust=y&id_adjust=$id_adjust&id_adjust_detail=$id_adjust_detail'";if($status_up == "1"){echo "$can_delete";}echo " style='text-align:center;  background-color:transparent;'><span class='glyphicon glyphicon-trash' style='color:#F40622; font-size:15px;'></span></a></td>
							</tr>";
							$i++;
							$n--;
						}
					echo"</table>";
			}else if(isset($_GET['id'])){
				echo "<hr style='border-color:#CCC; margin-top: 8px; margin-bottom:8px;' />
				<form name='loaddiff' action='controller/productAdjustController.php?loaddiff=y&id_adjust=$id_adjust' method='post'>
				<table class='table table-striped table-hover'>
					<thead>
						<th width='5%' style='text-align:center;'>ลำดับ</th><th width='15%' style='text-align:center;'>สินค้า</th><th width='25%' style='text-align:left;'>โซน</th>
						<th width='15%' style='text-align:left;'>จำนวนเพิ่ม</th><th width='10%' style='text-align:left;'>จำนวนลด</th>
						<th width='10%' style='text-align:right;'>ชื่อ</th><th width='10%' style='text-align:right;'><input name='CheckAll' type='checkbox' id='CheckAll' value='Y' onClick='ClickCheckAll(this)'></th>
					</thead>";	
						$result = dbQuery("SELECT id_diff,id_zone,zone_name,id_product_attribute,reference,qty_add,qty_minus,first_name FROM diff_table where status_diff = '0'");
						$i=0;
						$n=1;
						$row = dbNumRows($result);
						while($i<$row){
							list($id_diff, $id_zone, $zone_name, $id_product_attribute, $reference, $qty_add ,$qty_minus ,$first_name) = dbFetchArray($result);
							echo "<tr>
							<td align='center'>$n</td>
							<td align='center'>$reference</td>
							<td>$zone_name</td>
							<td align='left'>$qty_add</td>
							<td align='left'>$qty_minus</td>
							<td align='right'>$first_name</td>
							<td align='right'><input type='checkbox' name='chkDel$n' id='chkDel$n' value='$id_diff'></td>
							</tr>";
							$i++;
							$n++;
						}
					echo"</table><input type='hidden' name='hdnCount' value='$i'>
						<input type='hidden' name='id_adjust' value='$id_adjust'></form>";
			}
	}else if(isset($_GET['ListDiff'])){
		echo "<table class='table table-striped table-hover'>
					<thead>
						<th width='5%' style='text-align:center;'>ลำดับ</th><th width='15%' style='text-align:center;'>สินค้า</th><th width='25%' style='text-align:left;'>โซน</th>
						<th width='15%' style='text-align:left;'>จำนวนเพิ่ม</th><th width='10%' style='text-align:left;'>จำนวนลด</th>
						<th width='10%' style='text-align:right;'>ผู้เช็ค</th>
					</thead>";	
						$result = dbQuery("SELECT id_diff,id_zone,zone_name,id_product_attribute,reference,qty_add,qty_minus,first_name FROM diff_table where status_diff = '0'");
						$i=0;
						$n=1;
						$row = dbNumRows($result);
						while($i<$row){
							list($id_diff, $id_zone, $zone_name, $id_product_attribute, $reference, $qty_add ,$qty_minus ,$first_name) = dbFetchArray($result);
							echo "<tr>
							<td align='center'>$n</td>
							<td align='center'>$reference</td>
							<td>$zone_name</td>
							<td align='left'>$qty_add</td>
							<td align='left'>$qty_minus</td>
							<td align='right'>$first_name</td>
							</tr>";
							$i++;
							$n++;
						}
					echo "</table>";
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
        	<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:12%;'>เลขที่</th><th style='width:12%;'>อ้างอิง</th>
            <th style='width:13%; text-align:center;'>วันที่</th><th style='width:13%; text-align:center;'>พนักงาน</th><th style='width:20%; text-align:left;'>หมายเหตุ</th><th style='width:10%; text-align:center;'>สถานะ</th><th style='width:15%; text-align:left;'></th>
        </thead>";
		$result = dbQuery("SELECT * FROM adjust_table where adjust_date BETWEEN '$from' AND '$to'");
		$i=0;
		$n=1;
		$row = dbNumRows($result);
		while($i<$row){
			list($id_adjust, $adjust_no, $adjust_reference, $adjust_date, $adjust_note, $employee,$adjust_status) = dbFetchArray($result);
			echo "<tr>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">$n</td>
			<td style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">$adjust_no</td>
			<td style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">$adjust_reference</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">"; echo thaiDate($adjust_date); echo"</td>
			<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">$employee</td>
			<td align='left' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\" >$adjust_note</td>";
			if($adjust_status == "0"){
				echo
			"<td align='center' style='cursor:pointer; color: red;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\">ยังไม่ได้บันทึก</td>";
			}else{
				echo
			"<td align='center' style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductAdjust&view_detail=y&id_adjust=$id_adjust'\" >บันทึกแล้ว</td>";
			}
			echo "
			<td align='center'>
				<a href='index.php?content=ProductAdjust&add=y&id_adjust=$id_adjust' $can_edit>
					<button class='btn btn-warning btn-sx'>
						<span class='glyphicon glyphicon-pencil' style='color: #fff;'></span>
					</button>
				</a>&nbsp;
				<a href='controller/productAdjustController.php?drop=y&id_adjust=$id_adjust' $can_delete>
					<button class='btn btn-danger btn-sx' onclick=\"return confirm('คุณแน่ใจว่าต้องการลบ $adjust_no ? ');\">
						<span class='glyphicon glyphicon-trash' style='color: #fff;'></span>
					</button>
				</a>
			</td>
			</tr>";
			$i++;
			$n++;
		}
		if($row == "0"){
			echo "<td align='left' colspan='8'><div class='alert alert-info'  align='center'>ไม่มีรายการที่ปรับยอดในช่วงเวลานี้</div></td>";
		}
		echo"       
    </table>
</div> </div>";
	}
?>
</div>

<div class="row">
<div class="col-sm-12">
<br />
	

</div>
</div></div>
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
	$(function() {
    $("#adjust_date").datepicker({
      dateFormat: 'dd-mm-yy'
    });
  });
  });
  $(function() {
    $("#date").datepicker({
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
$("#barcode").focus();
///************  บาร์โค้ดสินค้า **********************//
$("#barcode").bind("enterKey",function(){
	if($("#barcode").val() != ""){
	$("#barcode_zone").focus();
	}
});
$("#barcode").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }
});
///***************** รหัสโซน *********************///
$("#barcode_zone").bind("enterKey",function(){
	if($("#barcode_zone").val() != ""){
	$("#adjust_qty_add").focus();
	}else{
		$("#zone_name").focus();
	}
});
$("#barcode_zone").keyup(function(e){
	if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
	}else if(e == ""){
		$(this).trigger("enterKey");
	}
});
///********************* จำนวนสินค้า ***********************///
$("#zone_name").bind("enterKey",function(){
	if($("#zone_name").val() != ""){
	$("#adjust_qty_add").focus();
	}else{
		alert("ต้องระบุบาร์โค้ดโซน หรือ ชื่อโซน อย่างน้อย 1 อย่าง"); 
		$("#barcode_zone").focus();
	}
});
$("#zone_name").keyup(function(e){
	if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
	}else if(e == ""){
		$(this).trigger("enterKey");
	}
});
///********************* จำนวนสินค้า ***********************///
$("#adjust_qty_add").bind("enterKey",function(){
	if($("#adjust_qty_add").val() != ""){
	$("#adjust_qty_minus").focus();
	}else{
		document.getElementById('adjust_qty_add').value=0;
		$("#adjust_qty_minus").focus();
	}
});
$("#adjust_qty_add").keyup(function(e){
	if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
	}else if(e == ""){
		$(this).trigger("enterKey");
	}
});
///********************* รหัสสินค้า ***********************///
	
$("#adjust_qty_minus").bind("enterKey",function(){
	var adjust_qty_add = $("#adjust_qty_add").val();
	var adjust_qty_minus = $("#adjust_qty_minus").val();
	var sumqty = parseInt(adjust_qty_add) + parseInt(adjust_qty_minus);
	if($("#adjust_qty_minus").val() != ""){
		if(sumqty > "0"){
			$("#ok").click();
		}else{
			alert("ยังไม่ได้ใส่จำนวน"); 
			$("#adjust_qty_add").focus();
		} 
	}else{
		document.getElementById('adjust_qty_minus').value=0;
		$(this).trigger("enterKey");
	}
});
$("#adjust_qty_minus").keyup(function(e){
    if(e.keyCode == 13)
    {
        $(this).trigger("enterKey");
    }else if(e == ""){
		$(this).trigger("enterKey");
	}
});
//****************ตรวจสอบรายการ*****************************//
function submit_detail(){
	var barcode_zone = $("#barcode_zone").val();
	var zone_name = $("#zone_name").val();
	var adjust_qty_add = $("#adjust_qty_add").val();
	var adjust_qty_minus = $("#adjust_qty_minus").val();
	var barcode = $("#barcode").val();
	if(barcode == ""){
		alert("ยังไม่ได้ใส่บาร์โค้ดสินค้า");
		$("#barcode").focus();
	}else if(barcode_zone ==""){
		if(zone_name==""){  
			alert("ต้องระบุบาร์โค้ดโซน หรือ ชื่อโซน อย่างน้อย 1 อย่าง"); 
			$("#barcode_zone").focus();
		}else if(adjust_qty_add == ""){
			alert("ยังไม่ได้ใส่จำนวนเพิ่มถ้าไม่มีกรุณาใส่ '0'");
			$("#adjust_qty_add").focus();
		}else if(parseInt(adjust_qty_add)<0){
			alert("จำนวนเพิ่มต้องเป็นค่ามากกว่าหรือเท่ากับ 0");
			$("#adjust_qty_add").focus();
		}else if(adjust_qty_minus ==""){
			alert("ยังไม่ได้ใส่จำนวนลดถ้าไม่มีกรุณาใส่ '0'");
			$("#adjust_qty_minus").focus();
		}else if(parseInt(adjust_qty_minus)<0){
			alert("จำนวนลดต้องมีค่ามากกว่าหรือเท่ากับ 0 ");
			$("#adjust_qty_minus").focus();
		}else{
			$("#detail_form").submit();
		}
	}else if(adjust_qty_add == ""){
		alert("ยังไม่ได้ใส่จำนวนเพิ่มถ้าไม่มีกรุณาใส่ '0'");
		$("#adjust_qty_add").focus();
	}else if(parseInt(adjust_qty_add)<0){
		alert("จำนวนเพิ่มต้องเป็นค่ามากกว่าหรือเท่ากับ 0");
		$("#adjust_qty_add").focus();
	}else if(adjust_qty_minus ==""){
		alert("ยังไม่ได้ใส่จำนวนลดถ้าไม่มีกรุณาใส่ '0'");
		$("#adjust_qty_minus").focus();
	}else if(parseInt(adjust_qty_minus)<0){
		alert("จำนวนลดต้องมีค่ามากกว่าหรือเท่ากับ 0 ");
		$("#adjust_qty_minus").focus();
	}else{
		$("#detail_form").submit();
	}
}
function ClickCheckAll(vol)
	{
		var i=1;
		for(i=1;i<=document.loaddiff.hdnCount.value;i++){
			if(vol.checked == true){
				eval("document.loaddiff.chkDel"+i+".checked=true");
			}else{
				eval("document.loaddiff.chkDel"+i+".checked=false");
			}
		}
	}
function keybarcode(){
	document.getElementById('zone_name').value="";
}
function keyzone(){
	document.getElementById('barcode_zone').value="";
}
</script>