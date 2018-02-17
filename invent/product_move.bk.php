<?php 
	$page_menu = "invent_product_move";
	$page_name = "ย้ายพื้นที่จัดเก็บ";
	$id_tab = 9;
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
	if(isset($_POST['zone'])){
		$zone = $_POST['zone'];
		list($id_zone,$zone_name) = dbFetchArray(dbQuery("select id_zone,zone_name from tbl_zone where barcode_zone = '$zone' or zone_name = '$zone'"));
		$check = "1";
	}else if(isset($_GET['id_zone'])){
		$id_zone = $_GET['id_zone'];
		list($zone_name) = dbFetchArray(dbQuery("select zone_name from tbl_zone where id_zone = '$id_zone'"));
		$check = "1";
	}else{
		$check = "";
	}
	?>

<div class="container">
<!-- page place holder -->
<div class="row" style="height:35px;">
	<div class="col-sm-6" style="margin-top:10px;"><h4 class="title"><i class="fa fa-exchange"></i>&nbsp;<?php echo $page_name; ?></h4>
	</div>
    <div class="col-sm-6">
    	<p class="pull-right" style="margin-bottom:0px;">
        <?php if(isset($_GET['cancle_zone'])) : ?>
        <a href='index.php?content=ProductMove' style='text-align:center; background-color:transparent;' ><button type='button' class='btn btn-warning  btn-sm'><i class="fa fa-arrow-left"></i>&nbsp;กลับ</button></a>
        <button type="button"  id="btn_move" class="btn btn-success btn-sm" onclick="move_cancle_out()"><i class="fa fa-cloud-upload"></i>&nbsp;ย้ายออก</button>
    	<?php elseif(isset($_POST['zone'])) : ?>		
       	<a href='index.php?content=ProductMove' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-success  btn-sm'><i class="fa fa-star"></i>&nbsp;โซนใหม่</button></a>
       	<a href='index.php?content=ProductMove&productMove=y' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-primary  btn-sm'><i class="fa fa-cloud"></i>&nbsp;สินค้าที่ย้าย</button></a>
		<?php elseif(isset($_GET['id_zone'])) : ?>
       	<a href='index.php?content=ProductMove' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-success  btn-sm'><i class="fa fa-star"></i>&nbsp;โซนใหม่</button></a>
		<a href='index.php?content=ProductMove&productMove=y' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-primary  btn-sm'><i class="fa fa-cloud"></i>&nbsp;สินค้าที่ย้าย</button></a>
        <?php elseif(isset($_GET['productMove'])) : ?>
		<a href='index.php?content=ProductMove' style='text-align:center; background-color:transparent;' ><button type='button' class='btn btn-success  btn-sm'><i class="fa fa-arrow-left"></i>&nbsp;กลับ</button></a>
		<?php else : ?>
       	<a href='index.php?content=ProductMove&productMove=y' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-primary  btn-sm'><i class="fa fa-cloud"></i>&nbsp;สินค้าที่ย้าย</button></a>
		<?php endif; ?>
		</p>
    </div>
</div>
<hr style="border-color:#CCC; margin-top: 5px; margin-bottom:15px;" />
<!-- End page place holder -->

<div class='col-sm-12'>
<?php if(isset($id_zone) || isset($_GET['id_zone']) ) : ?>
	<form method='post' name='zone' action='controller/productmoveController.php?moveout=y'>
	<table border='0' width='80%' align='center'>
    	<tr>
			<td width='20%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>จำนวน</span>
                 	<input type='text' name='qty' class='form-control' placeholder='' value='1' required  >
					<input type='hidden' name='id_zone' class='form-control' placeholder='' value='<?php echo $id_zone; ?>' >
				</div>
			</td>
			<td width='5%'>
			</td>
            <td width='45%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>บาร์โค้ด</span>
                 	<input type='text' name='barcode' class='form-control' placeholder='' required autofocus >
				</div>
			</td>
            <td width='10%' align='center'><input type='submit' class='btn btn-primary' value='ย้าย' /></td>
            <td width='20%' align='right'></td>
        </tr>
    </table>
    </form>
    
    
    
    
<?php   elseif(isset($_GET['cancle_zone'])) : ?> <!--
	<form method='post' name='zone' action='controller/productmoveController.php?move_cancle_out'>
	<table border='0' width='80%' align='center'>
    	<tr>
			<td width='20%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>จำนวน</span>
                 	<input type='text' name='qty' class='form-control' placeholder='' value='1' required  >
				</div>
			</td>
			<td width='5%'>
			</td>
            <td width='45%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>บาร์โค้ด</span>
                 	<input type='text' name='barcode' class='form-control' placeholder='' required autofocus >
				</div>
			</td>
            <td width='10%' align='center'><input type='submit' class='btn btn-primary' value='ย้าย' /></td>
            <td width='20%' align='right'></td>
        </tr>
    </table>
    </form> -->
    
  
    
<?php elseif(isset($_GET['productMove'])) : ?>
	<?php 
		if(isset($_GET['in'])) :
		$id = $_GET['id'];
		list($id_product_attribute,$reference,$qty_move) = dbFetchArray(dbQuery("SELECT tbl_move.id_product_attribute,reference,qty_move FROM tbl_move LEFT JOIN tbl_product_attribute ON tbl_move.id_product_attribute = tbl_product_attribute.id_product_attribute where id_move = '$id'"));
		?>
		
	<form method='post' name='zone' action='controller/productmoveController.php?movein=y'>
	<table border='0' width='80%' align='center'>
		<tr>
			<td width='60%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>ชื่อสินค้า</span>
                 	<input type='text' name='reference' class='form-control' placeholder='' value='<?php echo $reference; ?>' disabled='disabled' >
					<input type='hidden' name='id_move' class='form-control' placeholder='' value='<?php echo $id; ?>' >
					<input type='hidden' name='id_product_attribute' class='form-control' placeholder='' value='<?php echo $id_product_attribute; ?>' >
				</div>
			</td>
            <td width='40%' align='right'></td>
        </tr>
		 </table>
		 <table border='0' width='80%' align='center' height='50px'>
    	<tr>
			<td width='20%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>จำนวน</span>
                 	<input type='text' name='qty_move' class='form-control' placeholder='' value='<?php echo $qty_move; ?>' required  >
					<input type='hidden' name='qty' class='form-control' placeholder='' value='<?php echo $qty_move; ?>'  >
				</div>
			</td>
			<td width='5%'>
			</td>
            <td width='45%'>
            	<div class='input-group'>
            		<span class='input-group-addon'>บาร์โค้ด,ชื่อ</span>
                 	<input type='text' name='zone' class='form-control' placeholder='' required autofocus >
				</div>
			</td>
            <td width='10%' align='center'><input type='submit' class='btn btn-primary' value='ย้าย' /></td>
            <td width='20%' align='right'></td>
        </tr>
    </table>
    </form>
    <?php endif; ?>



<?php else : ?>
	<form method='post' name='zone' action='index.php?content=ProductMove'>
    <div class="col-lg-4 col-lg-offset-4">
    	<div class='input-group'>
        	<span class='input-group-addon'>ชื่อโซน,บาร์โค้ดโซน</span>
               <input type='text' name='zone' class='form-control' placeholder='' required autofocus >
		</div>
     </div>
     <div class="col-lg-1">
			<input type='submit' class='btn btn-primary' value='ตกลง' /></td>
     </div>
     <div class="col-lg-3">
     	<p class="pull-right"><a href="index.php?content=ProductMove&cancle_zone"><button type="button" class="btn btn-info">ย้ายสินค้าจากโซนยกเลิก</button></a></p>
    </div>
    </form>

<?php endif; ?>

</div>

<div class="row">
<div class="col-sm-12">
<br />
<?php  //****************************************************************  สำหรับย้ายออกจาก cancle zone  *****************************************// ?>
<?php if( isset($_GET['cancle_zone']) ) :  ?>
<center><h4>สินค้า โซนยกเลิก </h4></center><hr />
<form id="cancle_move_form" action="controller/productmoveController.php?move_cancle_out" method="post">
<table class="table table-striped">
<thead><th style="width: 40%">สินค้า</th><th style="width:10%; text-align:right;">จำนวน</th><th style="width:15%; text-align:center">จากโซน</th><th style="width:15%; text-align:center">ออเดอร์</th><th colspan="2" style="text-align:center">จำนวนที่ย้าย</th></thead>
<?php 	$qr = dbQuery("SELECT * FROM tbl_cancle");  /// ดึงรายการทั้งหมดที่อยู่ใน cancle zone  ?>
<?php		$row = dbNumRows($qr); ?>
<?php		if($row > 0 ) :  ?>

<?php			while($rs = dbFetchArray($qr) ) : ?>
					<tr>
                    	<td style="vertical-align:middle"><?php echo get_product_reference($rs['id_product_attribute']); ?></td>
                        <td align="right" style="vertical-align:middle"><?php echo number_format($rs['qty']); ?></td>
                        <td align="center" style="vertical-align:middle"><?php echo get_zone($rs['id_zone']); ?></td>
                        <td align="center" style="vertical-align:middle"><?php echo get_order_reference($rs['id_order']); ?></td>
                        <td style="vertical-align:middle"><input type="text" class="form-control move_qty" name="move_qty[<?php echo $rs['id_cancle']; ?>]" id="move_qty_<?php echo $rs['id_cancle']; ?>"  /></td>
                        <td style="vertical-align:middle">
                            <button type="button" onclick="add_qty(<?php echo $rs['id_cancle']; ?>, <?php echo $rs['qty']; ?>)" class="btn btn-default"><i class="fa fa-angle-double-left"></i>&nbsp; ย้ายทั้งหมด</button>
                        </td>
                       </tr>						
<?php			endwhile; ?>

	<?php else : ?>
    <tr><td colspan="6"><center><h4>----------  ไม่มีสินค้าในโซนยกเลิก  ----------</h4></center></td></tr>
<?php 		endif; ?>
</table>
</form>



<?php else : ?>           
 <?php        
 
	if(isset($_GET['productMove'])){
		if(isset($_GET['message'])){
				$message = $_GET['message'];
				echo"<div class='alert alert-success' align='center'>$message</div>";
		}
		if(isset($_GET['error'])){
			$error_message = $_GET['error'];
			echo"<div id='error' class='alert alert-danger' >
			<b>มีบางอย่างผิดพลาด&nbsp;</b>$error_message</div>";
		} 
		?>
		<table class="table table-striped">
        <thead>
        <tr><th colspan="3" style="text-align:center">สินค้าที่กำลังย้าย</th></tr><tr><th width="55%" style="text-align: left">ชื่อสินค้า</th><th width="15%"  style="text-align: right">จำนวน</th><th width="15%"  style="text-align: center">เข้าโซน</th></tr>
		</thead>
		<?php 
		$sql = dbQuery("SELECT * FROM tbl_move LEFT JOIN tbl_product_attribute ON tbl_move.id_product_attribute = tbl_product_attribute.id_product_attribute");
		$row = dbNumRows($sql);
		$i = 0;
		while($i<$row){
			$result = dbFetchArray($sql);
			$id_move = $result['id_move'];
			$reference = $result['reference'];
			$qty_move = $result['qty_move'];
			$id_product_attribute = $result['id_product_attribute'];
			echo "<tr style='cursor:pointer;' onclick=\"document.location='index.php?content=ProductMove&productMove=y&in=in&id=$id_move'\">
					<td align='left'>$reference</td><td align='right'>$qty_move</td><td align='center'><a href='index.php?content=ProductMove&productMove=y&in=in&id=$id_move' style='text-align:center; background-color:transparent;'><button type='button' class='btn btn-link'><span class='glyphicon glyphicon-log-in' style='color:#5cb85c; font-size:16px;'></span></button></a></td>
				</tr>";
		$i++;
		}
		if($row == "0"){
			echo "<td align='left' colspan='3'><div class='alert alert-info'  align='center'>ไม่มีสินค้าที่กำลังย้าย</div></td>";
		}
	?>
	</table>
    <?php
	}
	if($check == "1"){
		if($id_zone == ""){
			echo "<div class='alert alert-danger' align='center'>ไม่มีโซนนี้กรุณาตรวจสอบ</div>";
		}else{
			if(isset($_GET['message'])){
				$message = $_GET['message'];
				echo"<div class='alert alert-success' align='center'>$message</div>";
			}
	?>
		<table class="table table-striped">
        <thead>
        <tr><th colspan="3" style="text-align:center">โซน <?php echo $zone_name;?></th></tr><tr><th width="70%" style="text-align: left">ชื่อสินค้า</th><th width="15%"  style="text-align: right">จำนวน</th></tr>
		</thead>
		<?php 
		$sql = dbQuery("SELECT * FROM stock where id_zone = '$id_zone'");
		$row = dbNumRows($sql);
		$i = 0;
		while($i<$row){
			$result = dbFetchArray($sql);
			$zone = $result['Zone'];
			$product = $result['Product'];
			$qty = $result['qty'];
			$id_product_attribute = $result['id_product_attribute'];
			list($qty_add,$qty_minus) = dbFetchArray(dbQuery("SELECT qty_add,qty_minus FROM tbl_diff WHERE id_product_attribute = '$id_product_attribute' and id_zone = '$id_zone' and status_diff = '0'"));
			$sumqty = $qty +($qty_add - $qty_minus);
			echo "<tr>
					<td align='left'>$product</td><td align='right'>$sumqty</td>
				</tr>";
		$i++;
		}
		if($row == "0"){
			echo "<td align='left' colspan='3'><div class='alert alert-info'  align='center'>ไม่มีสินค้าในโซนนี้</div></td>";
		}
	?>
	</table>
	<?php 
	}
	}
	?>
    <?php endif; ?>
</div>
</div></div>
<script>
function add_qty(id_cancle, qty){
	$("#move_qty_"+id_cancle).val(qty);
}

function move_cancle_out(){
	if($(".move_qty").length){
		var i = 0;
		$(".move_qty").each(function(index, element) {
            i += $(this).val();
        });
		if(i != 0 ){
			$("#cancle_move_form").submit();	
		}
	}
}
</script>