<?php 
	$page_menu = "invent_add_sponsor";
	$page_name = "สปอนเซอร์ สโมสร";
	$id_tab = 24;
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
<?php 
	if( isset($_GET['add']) ){
		$btn = can_do($add, "<a href='index.php?content=add_sponsor' ><button type='button' id='btn_back' class='btn btn-warning' onclick='goback()'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>");
		$btn .= can_do($add, "&nbsp;<button type='button' id='btn_save' class='btn btn-success' onclick='save()' ".$can_add."><i class='fa fa-save'></i>&nbsp; บันทึก</button>");
	}else if( isset($_GET['edit']) ){
		$btn = can_do($edit, "<a href='index.php?content=add_sponsor' ><button type='button' id='btn_back' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>");
		$btn .= can_do($add, "&nbsp;<button type='button' id='btn_new_budget' class='btn btn-success' ><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button>");
	}else if(isset($_GET['view_detail']) ){
		$btn = "<a href='index.php?content=add_sponsor' ><button type='button' id='btn_back' class='btn btn-warning' onclick='goback()'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button></a>";
	}else{
		$btn = can_do($add, "<a href='index.php?content=add_sponsor&add' ><button type='button' id='btn_add' class='btn btn-success'><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button></a>");
	}
	
	function sponsor_year_select($year = "")
	{
		$option = "";
		if($year == "" ){ $year = date("Y"); }
		$i = 2;
		while($i > -5){
			$text = $year+$i;
			$value = $year + $i;
			if($value == $year){ $se = "selected"; }else{ $se = ""; }
			$option .= "<option value='$value' ".$se.">".$text."</option>";  	
			$i--;
		}
		return $option;
	}
?> 
<div class="container">
<!-- page place holder -->
<div class="row">
<div class="col-lg-6"><h3 class="title" style="margin-bottom:0px;"><i class="fa fa-shield"></i>&nbsp; เพิ่ม/แก้ไข สปอนเซอร์สโมสร</h3></div>
<div class="col-lg-6"><p class="pull-right"><?php echo $btn; ?></p></div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:5px;' />
<div class="row">
<?php 
if(isset($_GET['error'])){
	$error_message = $_GET['error'];
	 echo"<div id='error' class='alert alert-danger' >
	 <b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
} 
if(isset($_GET['message'])){
	$message = $_GET['message'];
	echo"<div class='alert alert-success'>$message</div>";
}
if(isset($_GET['message1'])){
	$message1 = $_GET['message1'];
echo "<div class='alert alert-warning' role='alert'>$message1</div>";
}
?>
<!-- ++++++++++++++++++++++++++++++++++++++++++++   Add   +++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<?php if( isset($_GET['add']) ) : ?>
	<form action="controller/sponsorController.php?add_member" method="post">
	<div class="col-lg-5"><hr /></div><div class="col-lg-2"><h4  style="margin-top:5px; color:#999;">เพิ่มผู้รับสปอนเซอร์</h4></div><div class="col-lg-5"><hr /></div>
    <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
    
	<div class="col-lg-4"><span class="form-control label-left">ผู้รับ</span></div>
    <div class="col-lg-4"><input type="text" name="customer" id="customer" class="form-control" placeholder="กำหนดผู้รับ โดยเลือกจากลูกค้า" required="required" autocomplete="off" /><input type="hidden" name="id_customer" id="id_customer" /></div>
    <div class="col-lg-4">
    	<span style="color:red">* &nbsp; &nbsp;</span><span style="color:#999; margin-right:15px;">-- หรือ  -- </span> 
        <a href="index.php?content=customer&add=y" target="_blank"><button type="button" class="btn btn-warning"><i class="fa fa-plus"></i>&nbsp; เพิ่มลูกค้า</button></a>
    </div>
    
    <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
    
    <div class="col-lg-4"><span class="form-control label-left">อ้างอิง</span></div>
    <div class="col-lg-4"><input type="text" name="reference" id="reference" class="form-control" placeholder="เลขที่เอกสารอ้างอิง/สัญญา/อื่นๆ" /></div>
    <div class="col-lg-4">&nbsp;</div>
    
     <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
     
     <div class="col-lg-4"><span class="form-control label-left">งบประมาณ</span></div>
     <div class="col-lg-4"><input type="text" name="budget" id="budget" class="form-control" placeholder="กำหนดงบประมาณสำหรับการให้การสนับสนุน" required="required" autocomplete="off" onkeyup="valid_value($(this))" /></div>
     <div class="col-lg-4"><span style="color:red">* &nbsp; &nbsp;</span></div>
    
     <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
     
     <div class="col-lg-4"><span class="form-control label-left">ระยะเวลา</span></div>
     <div class="col-lg-2"><input type="text" name="from_date" id="from_date" class="form-control" placeholder="วันเริ่มการสนับสนุน" required="required" autocomplete="off" /></div>
     <div class="col-lg-2"><input type="text" name="to_date" id="to_date" class="form-control" placeholder="วันสิ้นสุดการสนับสนุน" required="required" autocomplete="off" /></div>
     <div class="col-lg-4"><span style="color:red">* &nbsp; &nbsp;</span></div>
     
     <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
     
     <div class="col-lg-4"><span class="form-control label-left">ปีงบประมาณ</span></div>
     <div class="col-lg-2"><select name="year" id="year" class="form-control"><?php echo sponsor_year_select(); ?></select></div>
     <div class="col-lg-2"><span style="color:red">* &nbsp; &nbsp;</span></div>
     <div class="col-lg-4"></div>
     
     <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
     
     <div class="col-lg-4"><span class="form-control label-left">สถานะ</span></div>
     <div class="col-lg-1"><input type="radio" name="active" id="yes" value="1" checked /><label style="padding-left:15px;" for="yes" ><i class="fa fa-check fa-2x" style="color:green;"></i></label></div>
     <div class="col-lg-3"><input type="radio" name="active" id="no" value="1"  /><label style="padding-left:15px;" for="no" ><i class="fa fa-remove fa-2x" style="color:red;"></i></label></div>
     <div class="col-lg-4"></div>
     
      <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
      
     <div class="col-lg-4"><span class="form-control label-left">หมายเหตุ</span></div>
     <div class="col-lg-4"><textarea class="form-control" rows="5" name="remark" placeholder="หมายเหตุ/เงื่อนไข/อื่นๆ" ></textarea></div>
     <div class="col-lg-4"><button id="btn_submit" type="button" style="display:none;">submit</button></div>
    
    <div class="col-lg-12"><h4>&nbsp;</h4> </div>
    </form>
<!-- ++++++++++++++++++++++++++++++++++++++++++++   end Add  +++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->



<!-- ++++++++++++++++++++++++++++++++++++++++++++   Edit   +++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<?php elseif( isset($_GET['edit']) && isset($_GET['id_sponsor']) ) : ?>
<?php 
	$id_sponsor = $_GET['id_sponsor'];
	$sql = dbQuery("SELECT id_customer, year FROM tbl_sponsor WHERE id_sponsor = ".$id_sponsor);
	list($id_customer, $year) = dbFetchArray($sql);
	$customer = new customer($id_customer);
	list($reference, $budget, $from, $to, $active, $b_year ) = dbFetchArray(dbQuery("SELECT reference, limit_amount, start, end, active, year FROM tbl_sponsor_budget WHERE id_sponsor = ".$id_sponsor." AND year = '".$year."' "));
	$qr = dbQuery("SELECT * FROM tbl_sponsor_budget WHERE id_sponsor = ".$id_sponsor);
?>


	<!-- <div class="col-lg-4"><hr /></div><div class="col-lg-4"><center><h4  style="margin-top:5px; color:#999;">แก้ไขผู้รับสปอนเซอร์</h4></center></div><div class="col-lg-4"><hr /></div> -->
    
    
    <div class="col-lg-12">
    <fieldset style="border: 1px solid #DDD; margin:0px; padding-bottom:15px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:10px; border:0px;">ข้อมูลปัจจุบัน</legend>
            <div class="col-sm-6">
            	<strong>ผู้รับ : <span id="sponsor_name"><?php echo $customer->full_name; ?></span></strong>&nbsp;&nbsp;
            	<?php echo can_do($edit, "<button type=\"button\" class=\"btn btn-warning btn-xs\" data-toggle='modal' data-target='#member_modal' >เปลี่ยนแปลงผู้ร้บ</button>"); ?>
            </div>
            <div class="col-sm-6">
            	<strong>ใช้งบประมาณปี : <span id="current_year"><?php echo $year; ?></span></strong>&nbsp;&nbsp;
                <?php echo can_do($edit, "<button type=\"button\" class=\"btn btn-warning btn-xs\" data-toggle='modal' data-target='#year_modal'>เปลี่ยนแปลงการใช้งบประมาณ</button>"); ?>
            </div>
            
             <div class="col-sm-12">&nbsp;</div>
            <div class="col-sm-6"><strong>งบประมาณ : <span id="current_budget"><?php echo number_format($budget,2); ?></span></strong></div>
            <div class="col-sm-6"><strong>ระยะเวลา : <span id="current_time"><?php echo thaiDate($from,"/"); ?> - <?php echo thaiDate($to,"/"); ?></span></strong></div>
    </fieldset>        
	</div>
   
 <!--*********************************  Modal  เปลียนแปลงปีงบประมาณ  **********************************-->             
	<div class='modal fade' id='year_modal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' style='width:600px;'>
		<div class='modal-content'>
			<div class='modal-header'>										
            	<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
				<h4 class='modal-title'><center>เปลี่ยนแปลงการใช้งบ</center></h4>
			</div>
			<div class='modal-body'>
                <div class="row">
                    <div class="col-lg-4"><span class="form-control label-left">ปีงบประมาณ :</span></div>
                    <div class="col-lg-6"><select name="year" id="set_year" class="form-control"><?php echo sponsor_year_select($year); ?></select></div>
                    <div class="col-lg-2"></div> 
                </div>
			</div>
			<div class='modal-footer'>
				<button type='button' id="btn_year_cancle" class='btn btn-warning' data-dismiss='modal'><i class="fa fa-remove"></i>&nbsp; ยกเลิก</button>
                <button type="button" onclick="update_year(<?php echo $id_sponsor; ?>)" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp; บันทึก</button>
			</div>
		</div>
	</div>
</div> 
<!--*********************************  End Modal เปลียนแปลงปีงบประมาณ  **********************************-->     
               
<!--*********************************  Modal  เปลี่ยนแปลงผู้รับการสนับสนุน  **********************************-->             
	<div class='modal fade' id='member_modal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' style='width:600px;'>
		<div class='modal-content'>
			<div class='modal-header'>										
            	<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
				<h4 class='modal-title'><center>เปลี่ยนแปลงผู้รับการสนับสนุน</center></h4>
			</div>
			<div class='modal-body'>
                <div class="row">
                    <div class="col-lg-2"><span class="form-control label-left">ผู้รับ</span></div>
                    <div class="col-lg-8"><input type="text" name="customer" id="customer" class="form-control"  /></div>
                    <div class="col-lg-2"><input type="hidden" name="id_customer" id="id_customer" /></div> 
                </div>
			</div>
			<div class='modal-footer'>
				<button type='button' id="btn_cancle" class='btn btn-warning' data-dismiss='modal'><i class="fa fa-remove"></i>&nbsp; ยกเลิก</button>
                <button type='button' class='btn btn-success' onclick="change_sponsor(<?php echo $id_sponsor; ?>)"><i class="fa fa-save"></i>&nbsp; บันทึก</button>
			</div>
		</div>
	</div>
</div> 
<!--*********************************  End Modal  เปลี่ยนแปลงผู้รับการสนับสนุน  **********************************-->            
    <!-- +++++++++++++++++++++++++  Budget List  ++++++++++++++++++++++++++++ -->
  
    <div class="col-lg-12">
    <fieldset style="border: 1px solid #DDD; margin:0px; padding-bottom:15px; padding-left:15px; padding-right:15px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:10px; border:0px;">รายการงบประมาณ</legend>
    <table class="table table-striped">
    	<thead>
        	<th style="width:15%">เลขที่เอกสาร/อ้างอิง</th>
            <th style="width:15%; text-align:right">งบประมาณ</th>
            <th style="width:10%; text-align:center">เริ่มต้น</th>
            <th style="width:10%; text-align:center">สิ้นสุด</th>
            <th style="width:10%; text-align:center">ปีงบประมาณ</th>
            <th style="width:10%; text-align:center">สถานะ</th>
            <th style="width:15%;">หมายเหตุ</th>
            <th style="width:15%; text-align:right">การกระทำ</th>
        </thead>
<?php while($rs = dbFetchArray($qr) ) : ?>
		<tr>
        	<td><?php echo $rs['reference']; ?></td>
            <td align="right"><?php echo number_format($rs['limit_amount'],2); ?></td>
            <td align="center"><?php echo thaiDate($rs['start']); ?></td>
            <td align="center"><?php echo thaiDate($rs['end']); ?></td>
            <td align="center"><?php echo $rs['year']; ?></td>
            <td align="center"><?php echo isActived($rs['active']); ?></td>
            <td ><?php echo $rs['remark']; ?></td>
            <td align="right">
            	<?php echo can_do($edit, "<button type='button' class='btn btn-warning' onclick='get_data(".$id_sponsor.", ".$rs['id_sponsor_budget'].")'><i class='fa fa-pencil'></i>&nbsp; แก้ไข</button>"); ?>
                <?php echo can_do($delete, "<button type='button' class='btn btn-danger' onclick=\"confirm_delete('คุณแน่ใจว่าต้องการลบรายการนี้','การกระทำนี้ไม่สามารถกู้คืนได้','controller/sponsorController.php?delete_budget&id_sponsor=".$id_sponsor."&id_sponsor_budget=".$rs['id_sponsor_budget']."', 'ใช่ ต้องการลบ','ยกเลิก'); \"><i class='fa fa-trash'></i>&nbsp; ลบ</button>"); ?>
           </td>
        </tr>
<?php endwhile; ?>		        
    </table>
    </fieldset>
    </div>
    
     <!-- +++++++++++++++++++++++++  End Budget List  ++++++++++++++++++++++++++++ -->
  <!-- +++++++++++++++++++++++++  MODAL Edit Budget  ++++++++++++++++++++++++++++ -->
   <button data-toggle='modal' data-target='#myModal' id='info' style="display:none" >xxx</button>
<div class='modal fade' id='myModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' style='width:800px;'>
		<div class='modal-content'>
			<div class='modal-header'>										
            	<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
				<h4 class='modal-title' id='header_title'><center>แก้ไขงบประมาณ</center></h4>
			</div>
			<div class='modal-body'>
            <div class="row">
            	<form action="controller/sponsorController.php?edit_budget" method="post">
                <div class="col-lg-2"><span class="form-control label-left">ผู้รับ</span></div>
                <div class="col-lg-8"><input type="text" class="form-control"  disabled="disabled" value="<?php echo $customer->full_name; ?>" /></div>
                <div class="col-lg-2"><input type="hidden" name="id_sponsor" id="id_sponsor" /><input type="hidden" name="id_sponsor_budget" id="id_sponsor_budget" /></div>
                
                <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
                
                <div class="col-lg-2"><span class="form-control label-left">อ้างอิง</span></div>
                <div class="col-lg-8"><input type="text" name="reference" id="reference" class="form-control" placeholder="เลขที่เอกสารอ้างอิง/สัญญา/อื่นๆ"  /></div>
                <div class="col-lg-2">&nbsp;</div>
                
                 <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
                 
                 <div class="col-lg-2"><span class="form-control label-left">งบประมาณ</span></div>
                 <div class="col-lg-8"><input type="text" name="budget" id="budget" class="form-control" placeholder="กำหนดงบประมาณสำหรับการให้การสนับสนุน"  required="required" autocomplete="off" onkeyup="valid_value($(this))" /></div>
                 <div class="col-lg-2"><span style="color:red">* &nbsp; &nbsp;</span></div>
                
                 <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
                 
                 <div class="col-lg-2"><span class="form-control label-left">ระยะเวลา</span></div>
                 <div class="col-lg-4"><input type="text" name="from_date" id="from_date" class="form-control" placeholder="วันเริ่มการสนับสนุน" required="required" autocomplete="off"  /></div>
                 <div class="col-lg-4"><input type="text" name="to_date" id="to_date" class="form-control" placeholder="วันสิ้นสุดการสนับสนุน" required="required" autocomplete="off"  /></div>
                 <div class="col-lg-2"><span style="color:red">* &nbsp; &nbsp;</span></div>
                 
                 <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
                 
                 <div class="col-lg-2"><span class="form-control label-left">ปีงบประมาณ</span></div>
                 <div class="col-lg-4"><select name="year" id="year" class="form-control"><?php echo sponsor_year_select(); ?></select></div>
                 <div class="col-lg-4"><span style="color:red">* &nbsp; &nbsp;</span></div>
                 <div class="col-lg-2"></div>
                 
                 <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
                 
                 <div class="col-lg-2"><span class="form-control label-left">สถานะ</span></div>
                 <div class="col-lg-2"><input type="radio" name="active" id="yes" value="1"  /><label style="padding-left:15px;" for="yes" ><i class="fa fa-check fa-2x" style="color:green;"></i></label></div>
                 <div class="col-lg-2"><input type="radio" name="active" id="no" value="0"  /><label style="padding-left:15px;" for="no" ><i class="fa fa-remove fa-2x" style="color:red;"></i></label></div>
                 <div class="col-lg-6"></div>
                 
                  <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
                  
                 <div class="col-lg-2"><span class="form-control label-left">หมายเหตุ</span></div>
                 <div class="col-lg-8"><textarea id="remark" class="form-control" rows="5" name="remark" placeholder="หมายเหตุ/เงื่อนไข/อื่นๆ" ></textarea></div>
                 <div class="col-lg-2"><button id="btn_submit" type="button" style="display:none;">submit</button></div>
                 
                </form></div>
			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-warning' data-dismiss='modal'><i class="fa fa-remove"></i>&nbsp; ยกเลิก</button>
                <button type='button' class='btn btn-success' onclick="save()"><i class="fa fa-save"></i>&nbsp; บันทึก</button>
			</div>
		</div>
	</div>
</div>

<!-- +++++++++++++++++++++++++  MODAL New Budget  ++++++++++++++++++++++++++++ -->
   <button data-toggle='modal' data-target='#add_budget_modal' id='new_info' style="display:none" >xxx</button>
<div class='modal fade' id='add_budget_modal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' style='width:800px;'>
		<div class='modal-content'>
			<div class='modal-header'>										
            	<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
				<h4 class='modal-title' id='header_title'><center>เพิ่มงบประมาณใหม่</center></h4>
			</div>
			<div class='modal-body'>
            <div class="row">
            	<form action="controller/sponsorController.php?add_budget&id_sponsor=<?php echo $id_sponsor; ?>" method="post">
                <div class="col-lg-2"><span class="form-control label-left">ผู้รับ</span></div>
                <div class="col-lg-8"><input type="text" class="form-control"  disabled="disabled" value="<?php echo $customer->full_name; ?>" /></div>
                <div class="col-lg-2"></div>
                
                <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
                
                <div class="col-lg-2"><span class="form-control label-left">อ้างอิง</span></div>
                <div class="col-lg-8"><input type="text" name="reference" id="new_reference" class="form-control" placeholder="เลขที่เอกสารอ้างอิง/สัญญา/อื่นๆ"  /></div>
                <div class="col-lg-2">&nbsp;</div>
                
                 <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
                 
                 <div class="col-lg-2"><span class="form-control label-left">งบประมาณ</span></div>
                 <div class="col-lg-8"><input type="text" name="budget" id="new_budget" class="form-control" placeholder="กำหนดงบประมาณสำหรับการให้การสนับสนุน"  required="required" autocomplete="off" onkeyup="valid_value($(this))" /></div>
                 <div class="col-lg-2"><span style="color:red">* &nbsp; &nbsp;</span></div>
                
                 <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
                 
                 <div class="col-lg-2"><span class="form-control label-left">ระยะเวลา</span></div>
                 <div class="col-lg-4"><input type="text" name="from_date" id="new_from_date" class="form-control" placeholder="วันเริ่มการสนับสนุน" required="required" autocomplete="off"  /></div>
                 <div class="col-lg-4"><input type="text" name="to_date" id="new_to_date" class="form-control" placeholder="วันสิ้นสุดการสนับสนุน" required="required" autocomplete="off"  /></div>
                 <div class="col-lg-2"><span style="color:red">* &nbsp; &nbsp;</span></div>
                 
                 <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
                 
                 <div class="col-lg-2"><span class="form-control label-left">ปีงบประมาณ</span></div>
                 <div class="col-lg-4"><select name="year" id="new_year" class="form-control"><?php echo sponsor_year_select(); ?></select></div>
                 <div class="col-lg-4"><span style="color:red">* &nbsp; &nbsp;</span></div>
                 <div class="col-lg-2"></div>
                 
                 <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
                 
                 <div class="col-lg-2"><span class="form-control label-left">สถานะ</span></div>
                 <div class="col-lg-2"><input type="radio" name="active" id="new_yes" value="1" checked  /><label style="padding-left:15px;" for="new_yes" ><i class="fa fa-check fa-2x" style="color:green;"></i></label></div>
                 <div class="col-lg-2"><input type="radio" name="active" id="new_no" value="0"  /><label style="padding-left:15px;" for="no" ><i class="fa fa-remove fa-2x" style="color:red;"></i></label></div>
                 <div class="col-lg-6"></div>
                 
                  <div class="col-lg-12"><!----- Divider ------>&nbsp; </div>
                  
                 <div class="col-lg-2"><span class="form-control label-left">หมายเหตุ</span></div>
                 <div class="col-lg-8"><textarea id="new_remark" class="form-control" rows="5" name="remark" placeholder="หมายเหตุ/เงื่อนไข/อื่นๆ" ></textarea></div>
                 <div class="col-lg-2"><button id="btn_new_submit" type="button" style="display:none;">submit</button></div>
                 
                </form></div>
			</div>
			<div class='modal-footer'>
				<button type='button' class='btn btn-warning' data-dismiss='modal'><i class="fa fa-remove"></i>&nbsp; ยกเลิก</button>
                <button type='button' class='btn btn-success' onclick="add(<?php echo $id_sponsor; ?>)"><i class="fa fa-save"></i>&nbsp; บันทึก</button>
			</div>
		</div>
	</div>
</div>    

<!-- ++++++++++++++++++++++++++++++++++++++++++++   end Edit   +++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->



<!-- ++++++++++++++++++++++++++++++++++++++++++++   View Detail   +++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<?php elseif( isset($_GET['view_detail']) && isset($_GET['id_sponsor']) ) : ?>
<?php 
	$id_sponsor = $_GET['id_sponsor'];
	$sql = dbQuery("SELECT id_customer, year FROM tbl_sponsor WHERE id_sponsor = ".$id_sponsor);
	list($id_customer, $year) = dbFetchArray($sql);
	$customer = new customer($id_customer);
	list($reference, $budget, $from, $to, $active, $b_year ) = dbFetchArray(dbQuery("SELECT reference, limit_amount, start, end, active, year FROM tbl_sponsor_budget WHERE id_sponsor = ".$id_sponsor." AND year = '".$year."' "));
	$qr = dbQuery("SELECT * FROM tbl_sponsor_budget WHERE id_sponsor = ".$id_sponsor);
?>


	<!-- <div class="col-lg-4"><hr /></div><div class="col-lg-4"><center><h4  style="margin-top:5px; color:#999;">แก้ไขผู้รับสปอนเซอร์</h4></center></div><div class="col-lg-4"><hr /></div> -->
    
    
    <div class="col-lg-12">
    <fieldset style="border: 1px solid #DDD; margin:0px; padding-bottom:15px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:10px; border:0px;">ข้อมูลปัจจุบัน</legend>
            <div class="col-sm-6">
            	<strong>ผู้รับ : <span id="sponsor_name"><?php echo $customer->full_name; ?></span></strong>&nbsp;&nbsp;
            </div>
            <div class="col-sm-6">
            	<strong>ใช้งบประมาณปี : <span id="current_year"><?php echo $year; ?></span></strong>&nbsp;&nbsp; 
            </div>
            
            <div class="col-sm-12">&nbsp;</div>
            <div class="col-sm-6"><strong>งบประมาณ : <span id="current_budget"><?php echo number_format($budget,2); ?></span></strong></div>
            <div class="col-sm-6"><strong>ระยะเวลา : <span id="current_time"><?php echo thaiDate($from,"/"); ?> - <?php echo thaiDate($to,"/"); ?></span></strong></div>
    </fieldset>        
	</div>
           
    <!-- +++++++++++++++++++++++++  Budget List  ++++++++++++++++++++++++++++ -->
  
    <div class="col-lg-12">
    <fieldset style="border: 1px solid #DDD; margin:0px; padding-bottom:15px; padding-left:15px; padding-right:15px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:10px; border:0px;">รายการงบประมาณ</legend>
    <table class="table table-striped">
    	<thead>
        	<th style="width:15%">เลขที่เอกสาร/อ้างอิง</th>
            <th style="width:15%; text-align:right">งบประมาณ</th>
            <th style="width:10%; text-align:center">เริ่มต้น</th>
            <th style="width:10%; text-align:center">สิ้นสุด</th>
            <th style="width:10%; text-align:center">ปีงบประมาณ</th>
            <th style="width:10%; text-align:center">สถานะ</th>
            <th style="text-align:center">หมายเหตุ</th>
        </thead>
<?php while($rs = dbFetchArray($qr) ) : ?>
		<tr>
        	<td><?php echo $rs['reference']; ?></td>
            <td align="right"><?php echo number_format($rs['limit_amount'],2); ?></td>
            <td align="center"><?php echo thaiDate($rs['start'],"/"); ?></td>
            <td align="center"><?php echo thaiDate($rs['end'],"/"); ?></td>
            <td align="center"><?php echo $rs['year']; ?></td>
            <td align="center"><?php echo isActived($rs['active']); ?></td>
            <td ><?php echo $rs['remark']; ?></td>  
        </tr>
<?php endwhile; ?>		        
    </table>
    </fieldset>
    </div>
    
     <!-- +++++++++++++++++++++++++  End Budget List  ++++++++++++++++++++++++++++ -->


<!-- ++++++++++++++++++++++++++++++++++++++++++++   End View Detail   +++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->



<!-- ++++++++++++++++++++++++++++++++++++++++++++   List Table  +++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<?php else : ?>
	<div class="col-lg-12">
    <table class="table table-striped">
    <thead>
    	<th style="width:5%; text-align:center">ลำดับ</th>
        <th style="width:30%;">ผู้รับ (สโมสร)</th>
        <th style="width:15%; text-align:center">ปีงบประมาณ</th>
        <th style="width:15%; text-align:center">สถานะ</th>
        <th style="text-align:center">การกระทำ</th>
	</thead>
<?php
	$sql = dbQuery("SELECT * FROM tbl_sponsor");
	if(dbNumRows($sql) > 0) :
		$n = 1;
?>
	
<?php	
		while($rs = dbFetchArray($sql) ) : 
			$id_sponsor = $rs['id_sponsor'];
			$customer = new customer($rs['id_customer']);
			$customer_name = $customer->full_name;
			$v_year = $rs['year'];
			$year = $rs['year'];
			$active = isActived($rs['active']);		
		?>
				<tr>
                	<td align="center"><?php echo $n; ?></td>
                    <td><?php echo $customer_name; ?></td>
                    <td align="center"><?php echo $year; ?></td>
                    <td align="center"><?php echo $active; ?></td>
                    <td align="right">
                    	<a href="index.php?content=add_sponsor&view_detail&id_sponsor=<?php echo $id_sponsor; ?>">
                        	<button type="button" class="btn btn-default"><i class="fa fa-search"></i>&nbsp; รายละเอียด</button>
                       </a>
                       <?php if($edit == 1 ) : ?>
                       <a href="index.php?content=add_sponsor&edit=y&id_sponsor=<?php echo $id_sponsor; ?>">
                       		<button type="button" class="btn btn-warning"><i class="fa fa-pencil"></i>&nbsp; แก้ไข</button>
                        </a>
                        <?php endif; ?>
                        <?php if($delete == 1) : ?>
                        <a href="controller/sponsorController.php?delete_member&id_sponsor=<?php echo $id_sponsor; ?>">
                        	<button type="button" class="btn btn-danger"><i class="fa fa-trash"></i>&nbsp; ลบ</button>
                        </a>
                        <?php endif; ?>
                    </td>
				</tr>
                <?php $n++; ?>
<?php  	endwhile; ?>
<?php 	else : ?>
			<tr><td colspan="8"><center><h4>----------  ยังไม่มีรายการ  ----------</h4></center></td></tr>
<?php 	endif;  ?>	
	</table>
	</div>
<?php endif; ?>    
</div>

</div><!-- container -->
<script>
function get_data(id_sponsor, id_budget)
{
	$.ajax({
		url:"controller/sponsorController.php?get_budget&id_sponsor="+id_sponsor+"&id_sponsor_budget="+id_budget,
		type:"GET", cache:false,success: function(rs){
			if(rs !=""){
				var arr = rs.split(" : ");
				var id_sponsor = arr[1];
				var id_sponsor_budget = arr[0];
				var reference = arr[2];
				var budget = arr[3];
				var start = arr[4];
				var end = arr[5];
				var remark = arr[6];
				var active = arr[7];
				var year = arr[8];
				$("#id_sponsor").val(id_sponsor);
				$("#id_sponsor_budget").val(id_sponsor_budget);
				$("#reference").val(reference);
				$("#budget").val(budget);
				$("#from_date").val(start);
				$("#to_date").val(end);
				$("#remark").val(remark);
				if(active == 1){ $("#yes").attr("checked","checked"); }else{ $("#no").attr("checked", "checked"); }
				$("#year").val(year);
				$("#info").click();				
			}
		}
	});	
}

//****************************  อัพเดตปีงบประมาณปัจจุบัน  เช่น มี งบหลายปี แต่ถ้าเลือกปีงบประมาณเป็นปีไหนก็จะใช้ งบของปีนั้น  ******************//
function update_year(id)
{
	var year = $("#set_year").val();
	$.ajax({
		url: "controller/sponsorController.php?set_year&id_sponsor="+id+"&year="+year,
		type:"GET", cache:false,
		success: function(rs){
			$("#btn_year_cancle").click();
			if(rs != "noyear" && rs !=""){
				arr = rs.split(" : ");
				var budget = arr[0];
				var time = arr[1];
				var year = arr[2];
				$("#current_year").html(year);
				$("#current_budget").html(budget);
				$("#current_time").html(time);
				swal("สำเร็จ","ปรับปรุงข้อมูลเรียบร้อย","success");
			}else if(rs == "noyear"){
				swal("ผิดพลาด","ไม่มีงบประมาณสำหรับปีที่เลือกมา","error");
			}else{
				swal("ผิดพลาด","ปรับปรุงข้อมูลไม่สำเร็จ","error");
			}
		}
	});
}


function goback(){
	window.history.back();
}
/*************************  เลือกชื่อลูกค้า  **************************/
$("#customer").autocomplete({
	source : "controller/autoComplete.php?get_customer_id",
	autoFocus: true,
	close: function(event,ui){
		var data = $(this).val();
		var arr = data.split(' : ');
		var id = arr[0];
		var name = arr[1];
		$("#id_customer").val(id);
		$(this).val(name);
		valid_duplicate(id);
		}
});

/******************************************  เลือกวันที่  **************************************/
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
    $("#new_from_date").datepicker({
      dateFormat: 'dd-mm-yy', onClose: function( selectedDate ) {
        $( "#new_to_date" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#new_to_date" ).datepicker({
      dateFormat: 'dd-mm-yy',   onClose: function( selectedDate ) {
        $( "#new_from_date" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
  
  
/******************************************  ตรวจสอบงบประมาณ ต้องเป็นตัวเลขเท่านั้น  ************************/  
function valid_value(el)
{
	var amount = el.val();
	if(isNaN(amount))
	{
		swal("ชนิดข้อมูลผิด", "กรุณาใส่ตัวเลขเท่านั้น", "error");
		el.val('');
		el.focus();
		return false;
	}
}

/*****************************************  ตรวจสอบการเพิ่ม ผู้รับสปอนเซอร์ ต้องไม่ซ้ำ  **********************/
function valid_duplicate(id){
	$.ajax({
		url : "controller/sponsorController.php?valid_duplicate&id_customer="+id,
		type: "GET",
		cache:false,
		success: function(rs){
			var i = rs.trim();	
			if(i == 1)
			{
				swal("ผู้รับซ้ำ", "มีผู้รับรายนี้อยู่ในรายการแล้ว ไม่อนุญาติให้เพิ่มซ้ำ", "error");
				$("#customer").val("");
				$("#id_customer").val("");
			}
		}
	});
}
//*************************************  บันทึก เพิ่มผู้รับสปอนเซอร์  *******************************//
function save(){
	var from = isDate($("#from_date").val());
	var to = isDate($("#to_date").val());
	if( from && to ){
		$("#btn_submit").attr("type","submit");
		$("#btn_submit").click();
	}else{
		swal("วันที่ไม่ถูกต้อง", "กรุณาตรวจสอบค่าใชช่องวันที่ว่าถูกต้องหรือไม่", "error");
	}
}

function add(id){
	var from = isDate($("#new_from_date").val());
	var to = isDate($("#new_to_date").val());
	var year = $("#new_year").val();
	if( from && to ){
		$.ajax({
			url: "controller/sponsorController.php?check_valid_year&id_sponsor="+id+"&year="+year,
			type:"GET", cache:false,
			success: function(rs){
				if(rs == 1){
					swal("ปีงบประมาณซ้ำ", "ไม่สามารถมีงบประมาณ 2 งบในปีเดียวกันได้","error");
					return false;
				}else{
				$("#btn_new_submit").attr("type","submit");
				$("#btn_new_submit").click();	
				}
			}
		});	
	}else{
		swal("วันที่ไม่ถูกต้อง", "กรุณาตรวจสอบค่าใชช่องวันที่ว่าถูกต้องหรือไม่", "error");
	}
}

function change_sponsor(id_sponsor)
{
	var id_customer = $("#id_customer").val();
	if(id_customer !="")
	{
		$.ajax({
			url: "controller/sponsorController.php?edit_member&id_sponsor="+id_sponsor+"&id_customer="+id_customer,
			type:"GET", cache:false,
			success: function(rs){
				$("#btn_cancle").click();
				if(rs != "false"){
					$("#sponsor_name").html(rs);
					swal("เรียบร้อย","เปลี่ยนผู้รับการสนับสนุนเรียบร้อยแล้ว","success");
				}else{
					swal("ผิดพลาด", "เปลี่ยนแปลงผู้รับการสนับสนุนไม่สำเร็จ","error");
				}
			}
		});
	}else{
		swal("ไม่ได้ระบุผู้รับ", "กรุณาระบุผู้รับ","error");
	}
}

$("#btn_new_budget").click(function(e){
	$("#new_info").click();
});
</script>