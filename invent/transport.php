<?php 
	$page_name = "การจัดส่งของลูกค้า";
	$id_tab = 56;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
	include 'function/transport_helper.php';
	?>

<style> 
label { margin-top: 10px; } 
.input-medium { 
	width: 300px;
}
.input-small {
	width: 150px;
}
hr { margin-bottom:5px; margin-top:5px; }
</style>    
<div class="container">
	<div class="row" style="height:30px;">
        <div class="col-lg-6" style="margin-top:10px;">
        	<h4 class="title"><i class="fa fa-truck"></i> <?php echo $page_name; ?></h4>
        </div>
        <div class="col-lg-6">
        	<p class="pull-right" style="margin-bottom:0px;">
        <?php if( isset( $_GET['add'] ) || isset( $_GET['edit'] ) ) : ?>
        	<button type="button" class="btn btn-warning btn-sm" onClick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
        <?php endif; ?>
		<?php if( isset( $_GET['add'] ) ) :	?>
        	<?php if( $add ) : ?>
        		<button type="button" class="btn btn-success btn-sm" onClick="save()"><i class="fa fa-save"></i> บันทึก</button>
			<?php endif; ?>             
        <?php endif; ?>   
        <?php if( isset( $_GET['edit'] ) && isset( $_GET['id_transport'] ) ) : ?>
            <?php if( $edit ) : ?>
            	<button type="button" class="btn btn-success btn-sm" onClick="saveEdit(<?php echo $_GET['id_transport']; ?>)"><i class="fa fa-save"></i> บันทึก</button>
            <?php endif; ?>
        <?php endif; ?> 
        <?php if( !isset( $_GET['add'] ) && !isset( $_GET['edit'] ) ) : ?>
        	<?php if( $add ) : ?>
            	<button type="button" class="btn btn-success btn-sm" onClick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
            <?php endif; ?>
        <?php endif; ?>            
            </p>
		</div>       
    </div>
    <hr style="margin-bottom:10px;"/>
<?php if( isset( $_GET['add'] ) ) : ?>    
	<div class="row">
	<form id="addFrom">
        <div class="col-lg-4 col-lg-offset-4">
        	<label>ลูกค้า</label>
            <input type="text" class="form-control input-sm" id="customer" name="customer" placeholder="ค้นหาลูกค้า (จำเป็นต้องระบุ)" />
            <input type="hidden" name="id_customer" id="id_customer" />
            <span style="color:red; display:inline; float:right; margin-right: -15px; margin-top:-25px;" >*</span>
        </div>
        <div class="col-lg-1"></div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label>ผู้จัดส่งหลัก</label>
            <input type="text" class="form-control input-sm" id="main_sender" name="main_sender" placeholder="กำหนดผู้จัดส่งหลักให้ลูกค้า (จำเป็นต้องระบุ)"/>
            <input type="hidden" name="id_main_sender" id="id_main_sender" />
            <span style="color:red; display:inline; float:right; margin-right: -15px; margin-top:-25px;" >*</span>
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label>ผู้จัดส่งสำรอง 1</label>
            <input type="text" class="form-control input-sm" id="second_sender" name="second_sender" placeholder="กำหนดผู้จัดส่งสำรองให้ลูกค้า"/>
            <input type="hidden" name="id_second_sender" id="id_second_sender" value="0" />
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label>ผู้จัดส่งสำรอง 2</label>
            <input type="text" class="form-control input-sm" id="third_sender" name="third_sender" placeholder="กำหนดผู้จัดส่งสำรองให้ลูกค้า" />
            <input type="hidden" name="id_third_sender" id="id_third_sender" value="0" />
        </div>
	</form>
	</div><!--/ Row -->
<?php elseif( isset( $_GET['edit'] ) ) : ?>
	<?php if( !isset( $_GET['id_transport'] ) ) : ?>
    <h3 style="width:100%; text-align:center;">--- เกิดข้อผิดพลาด กรุณาออกแล้วลองใหม่อีกครั้ง ---</h3>
    <?php else : ?>
    <?php $id = $_GET['id_transport']; ?>
    <?php $qs = dbQuery("SELECT * FROM tbl_transport WHERE id_transport = ".$id); ?>
    <?php if( dbNumRows($qs) == 1 ) : ?>
    <?php 	$rs = dbFetchArray($qs); ?>
	<div class="row">
	<form id="editFrom">
        <div class="col-lg-4 col-lg-offset-4">
        	<label>ลูกค้า</label>
            <span class="form-control input-sm disabled"><?php echo customer_name($rs['id_customer']); ?></span>
        </div>
        <div class="col-lg-1"></div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label>ผู้จัดส่งหลัก</label>
            <input type="text" class="form-control input-sm" id="main_sender" name="main_sender" placeholder="กำหนดผู้จัดส่งหลักให้ลูกค้า (จำเป็นต้องระบุ)" value="<?php echo sender_name($rs['main_sender']); ?>"/>
            <input type="hidden" name="id_main_sender" id="id_main_sender" value="<?php echo $rs['main_sender']; ?>" />
            <span style="color:red; display:inline; float:right; margin-right: -15px; margin-top:-25px;" >*</span>
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label>ผู้จัดส่งสำรอง 1</label>
            <input type="text" class="form-control input-sm" id="second_sender" name="second_sender" placeholder="กำหนดผู้จัดส่งสำรองให้ลูกค้า" value="<?php echo sender_name($rs['second_sender']); ?>"/>
            <input type="hidden" name="id_second_sender" id="id_second_sender" value="<?php echo $rs['second_sender']; ?>" />
        </div>
        <div class="col-lg-4 col-lg-offset-4">
        	<label>ผู้จัดส่งสำรอง 2</label>
            <input type="text" class="form-control input-sm" id="third_sender" name="third_sender" placeholder="กำหนดผู้จัดส่งสำรองให้ลูกค้า" value="<?php echo sender_name($rs['third_sender']); ?>" />
            <input type="hidden" name="id_third_sender" id="id_third_sender" value="<?php echo $rs['third_sender']; ?>" />
        </div>
	</form>
	</div><!--/ Row -->
    	<?php endif; ?>
    <?php endif; ?>
<?php else : ?>
<?php if( isset($_POST['cus_search'] ) ){ $cus_search = $_POST['cus_search']; }else if( isset($_COOKIE['cus_search']) ){ $cus_search = $_COOKIE['cus_search']; }else{ $cus_search = ''; } ?>
<?php if( isset( $_POST['sender'] ) ){ $sender = $_POST['sender']; }else if( isset( $_COOKIE['sender'] ) ){ $sender = $_COOKIE['sender']; }else{ $sender = ''; } ?>

<form id="searchForm" method="post">
<div class="row">
    <div class="col-lg-3">
    	<div class="input-group">
    		<span class="input-group-addon">ลูกค้า</span>
        	<input type="text" class="form-control input-sm" name="cus_search" value="<?php echo $cus_search; ?>" />
		</div>        
    </div>
    <div class="col-lg-3">
    	<div class="input-group">
        	<span class="input-group-addon">ผู้จัดส่ง</span>
	        <input type="text" class="form-control input-sm" name="sender" value="<?php echo $sender; ?>" />
        </div>
    </div>
    <div class="col-lg-2">
    	<button type="button" class="btn btn-primary btn-sm btn-block" onClick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
    </div>
    <div class="col-lg-2">
    	<button type="button" class="btn btn-warning btn-sm btn-block" onClick="clearFilter()"><i class="fa fa-retweet"></i> รีเซ็ต</button>
    </div>
</div>
</form>
<hr style="margin-top:10px; margin-bottom:10px;"/>
<?php 
	$qr = "WHERE id_transport != 0 ";
	if($cus_search != '')
	{
		setcookie('cus_search', $cus_search, time()+3600, '/');
		$in = customer_in($cus_search);
		if( $in !== false )
		{
			$qr .= "AND id_customer IN(".$in.") ";
		}
	}
	
	if( $sender != '')
	{
		setcookie('sender', $sender, time()+3600, '/');
		$sin = sender_in($sender);
		if( $sin !== false )
		{
			$qr .= "AND (main_sender IN(".$sin.") OR second_sender IN(".$sin.") OR third_sender IN(".$sin.") )";
		}
	}
	$qr .= "ORDER BY id_transport DESC";
	
	$paginator = new paginator();
	if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
	$paginator->Per_Page("tbl_transport",$qr,$get_rows);
	$Page_Start = $paginator->Page_Start;
	$Per_Page = $paginator->Per_Page;
?>

<?php $qs = dbQuery("SELECT * FROM tbl_transport ".$qr." LIMIT ".$Page_Start.", ".$Per_Page); ?>
<div class="row">
	<div class="col-lg-12">
    	<table class="table table-striped" style="border:solid 1px #CCC;">
        	<thead>
            	<tr style="font-size:12px;">
                	<th style="width:5%; text-align:center;">ลำดับ</th>
                    <th style="width:20%;">ลูกค้า</th>
                    <th style="width:20%">ผู้จัดส่งหลัก</th>
                    <th style="width:20%;">ผู้จัดส่งสำรอง 1</th>
                    <th style="width:20%;">ผู้จัดส่งสำรอง 2</th>
                    <th></th>
                </tr>
            </thead>
        <?php $n = 1; ?>
		<?php while( $rs = dbFetchArray($qs) ) : ?>
        <?php 	$id = $rs['id_transport']; ?>
        	<tr id="<?php echo $id; ?>" style="font-size:10px;">
            	<td align="center"><?php echo $n; ?></td>
                <td><?php echo customer_name($rs['id_customer']); ?></td>
                <td><a href="javascript:void(0)" onClick="showSender(<?php echo $rs['main_sender']; ?>)"><?php echo sender_name($rs['main_sender']); ?></a></td>
                <td><a href="javascript:void(0)" onClick="showSender(<?php echo $rs['second_sender']; ?>)"><?php echo sender_name($rs['second_sender']); ?></a></td>
                <td><a href="javascript:void(0)" onClick="showSender(<?php echo $rs['third_sender']; ?>)"><?php echo sender_name($rs['third_sender']); ?></a></td>
                <td align="right">
                	<?php if( $edit ) : ?>
                    	<button type="button" class="btn btn-warning btn-xs" onClick="goEdit(<?php echo $id; ?>)"><i class="fa fa-pencil"></i></button>
                    <?php endif; ?>
                    <?php if( $delete ) : ?>
                    	<button type="button" class="btn btn-danger btn-xs" onClick="deleteRow(<?php echo $id; ?>)"><i class="fa fa-trash"></i></button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php $n++; ?>
        <?php endwhile; ?>                 
        </table>
        <?php $paginator->display($get_rows,"index.php?content=transport"); ?>
    </div>
</div><!--/ row -->
<?php endif; ?>    
</div><!--/ Container -->

<div class='modal fade' id='senderInfo' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal_info' style="width:500px;">
		<div class='modal-content'>
  			<div class='modal-header'>	</div>
			 <div class='modal-body' id='info_body'></div>
			 <div class='modal-footer'>
             	<button type='button' class='btn btn-default' data-dismiss='modal' aria-hidden='true'>ปิด</button>
			 </div>
		</div>
	</div>
</div>

<script id='sender_info_template' type="text/x-handlebars-template">
<div class="row">
	<div class="col-lg-12">
    <table class="table table-bordered table-striped">
    	<tr><td colspan="2" align="center"><h4>{{ sender_name }}</h4></td></tr>
        <tr><td width="25%">ที่อยู่</td><td>{{ address }}</td></tr>
        <tr><td width="25%">เบอร์โทร</td><td>{{ phone }}</td></tr>
        <tr><td width="25%">เวลาทำการ</td><td>{{ opentime }}</td></tr>
        <tr><td width="25%">เงื่อนไข</td><td>{{ type }}</td></tr>
    </table>
    </div>
</div>
</script>
<script>
function showSender(id){
	$.ajax({
		url:"controller/addressController.php?getSenderInfo",
		type:"GET", cache:"false", data:{ "id_sender" : id },
		success: function(rs){
			var rs = $.trim(rs);
			var source = $("#sender_info_template").html();
			var data 		= $.parseJSON(rs);
			var output 	= $("#info_body");
			render(source, data, output);
			$("#senderInfo").modal('show');
		}
	});
}


function deleteRow(id)
{
	swal({
		  title: "ต้องการลบการเชื่อมโยง ?",
		  text: "โปรดจำไว้ว่า การกระทำนี้จะไม่สามารถกู้คืนได้ ",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "ใช่ ลบเลย",
		  cancelButtonText: "ยกเลิก",
		  closeOnConfirm: false,
		}, function(){
			load_in();
			$.ajax({
				url:"controller/addressController.php?deleteTransportCustomer&id_transport="+id,
				type:"GET", cache:"false",
				success: function(rs)
				{
					var rs = $.trim(rs);
					if(rs == "success")
					{
						load_out();
						$("#"+id).remove();
						swal({ title: "เรียบร้อย", text: "ลบรายการเรียบร้อยแล้ว", timer: 1000, type: "success"});
					}else{
						load_out();
						swal("ไม่สำเร็จ", "ลบรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
					}
				}
			});						
	});
}

function goEdit(id)
{
	window.location.href = 'index.php?content=transport&edit=y&id_transport='+id;	
}

function saveEdit(id)
{
	var main			= $("#main_sender").val();
	var id_main		= $("#id_main_sender").val();
	var second		= $("#second_sender").val();
	var id_sec		= $("#id_second_sender").val();
	var third			= $("#third_sender").val();
	var id_third		= $("#id_third_sender").val();
	if( main == '' || id_main == 0 ){ swal("ข้อผิดพลาด !!", "ผู้จัดส่งหลักไม่ถูกต้องกรุณาเลือกใหม่อีกครั้ง", "warning"); return false; }
	if( second != '' && id_sec == 0 ){ swal("ข้อผิดพลาด !!", "ผู้จัดส่งสำรอง 1 ไม่ถูกต้องกรุณาเลือกใหม่อีกครั้ง", "warning"); return false; }
	if( third != '' && id_third == 0 ){ swal("ข้อผิดพลาด !!", "ผู้จัดส่งสำรอง 2 ไม่ถูกต้องกรุณาเลือกใหม่อีกครั้ง", "warning"); return false; }
	$.ajax({
		url:"controller/addressController.php?updateTransportCustomer&id_transport="+id,
		type:"POST", cache:"false", data:{ "main_sender" : id_main, "second_sender" : id_sec, "third_sender" : id_third },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ title:"สำเร็จ", text: "ปรับปรุงข้อมูลเรียบร้อยแล้ว", timer: 1000, type: "success" });
				setTimeout(function(){ goBack(); }, 1500);
			}else{
				swal("ข้อผิดพลาด !!", "ปรับปรุงข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
			}
		}
	});
}

function save(){
	var customer 	= $("#customer").val();
	var id_cus		= $("#id_customer").val();
	var main			= $("#main_sender").val();
	var id_main		= $("#id_main_sender").val();
	var second		= $("#second_sender").val();
	var id_sec		= $("#id_second_sender").val();
	var third			= $("#third_sender").val();
	var id_third		= $("#id_third_sender").val();
	if( customer == '' || id_cus == 0 ){ swal("ข้อผิดพลาด !!", "ชื่อลูกค้าไม่ถูกต้องกรุณาเลือกลูกค้าใหม่อีกครั้ง", "warning"); return false; }
	if( main == '' || id_main == 0 ){ swal("ข้อผิดพลาด !!", "ผู้จัดส่งหลักไม่ถูกต้องกรุณาเลือกใหม่อีกครั้ง", "warning"); return false; }
	if( second != '' && id_sec == 0 ){ swal("ข้อผิดพลาด !!", "ผู้จัดส่งสำรอง 1 ไม่ถูกต้องกรุณาเลือกใหม่อีกครั้ง", "warning"); return false; }
	if( third != '' && id_third == 0 ){ swal("ข้อผิดพลาด !!", "ผู้จัดส่งสำรอง 2 ไม่ถูกต้องกรุณาเลือกใหม่อีกครั้ง", "warning"); return false; }
	// ตรวจสอบว่าลูกค้ามีการเชื่อมโยงกับขนส่งไว้ก่อนแล้วหรือยัง
	$.ajax({
		url:"controller/addressController.php?isTransportCustomerExists",
		type:"GET", cache:"false", data:{ "id_customer" : id_cus },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs != '0' ){
				swal("ข้อผิดพลาด !!", "ลูกค้ามีการเชื่อมโยงการจัดส่งไว้แล้ว ไม่สามารถเพิ่มใหม่ได้", "warning");	
			}else{
				insertTransportCustomer(id_cus, id_main, id_sec, id_third);
			}
		}
	});
}

function insertTransportCustomer(id_cus, id_main, id_sec, id_third){
	$.ajax({
		url:"controller/addressController.php?insertTransportCustomer"	,
		type:"POST", cache:"false", data:{ "id_customer" : id_cus, "main_sender" : id_main, "second_sender" : id_sec, "third_sender" : id_third },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'success' ){
				swal({ 
					title : 'สำเร็จ', 
					text : 'เชื่อมโยงการจัดส่งเรียบร้อยแล้ว ต้องการเชื่อมโยงลูกค้าคนอื่นต่อหรือไม่ ?', 
					type: 'success', 
					//confirmButtonColor: "#DD6B55",
					confirmButtonText: "ใช่ เพิ่มอีก",
					cancelButtonText: "ไม่ใช่",
					closeOnConfirm: true,
					showCancelButton: true 
					}, function(isConfirm){
						if( isConfirm ){
							window.location.href = 'index.php?content=transport&add';
						}else{
							window.location.href = 'index.php?content=transport';
						}
					});
				}else{
					swal("Error!", "เชื่อมโยงการจัดส่งกับลูกค้าไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
				}
		}
	});				
}


$("#customer").autocomplete({
	source : 'controller/autoComplete.php?get_customer',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 3 ){
			$("#customer").val(arr[1]);
			$("#id_customer").val(arr[2]);
		}else{
			$("#customer").val('');
			$("#id_customer").val(0);
		}
	}			
});

$("#main_sender").autocomplete({
	source : 'controller/autoComplete.php?get_sender',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			$("#main_sender").val(arr[1]);
			$("#id_main_sender").val(arr[0]);
		}else{
			$("#main_sender").val('');
			$("#id_main_sender").val(0);
		}
	}			
});

$("#second_sender").autocomplete({
	source : 'controller/autoComplete.php?get_sender',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			$("#second_sender").val(arr[1]);
			$("#id_second_sender").val(arr[0]);
		}else{
			$("#second_sender").val('');
			$("#id_second_sender").val(0);
		}
	}			
});

$("#third_sender").autocomplete({
	source : 'controller/autoComplete.php?get_sender',
	autoFocus: true,
	close: function(){
		var rs = $(this).val();
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			$("#third_sender").val(arr[1]);
			$("#id_third_sender").val(arr[0]);
		}else{
			$("#third_sender").val('');
			$("#id_third_sender").val(0);
		}
	}			
});

function getSearch()
{
	$("#searchForm").submit();	
}

function clearFilter()
{
	$.ajax({
		url:"controller/addressController.php?clearFilter",
		type:"GET", cache:"false", success: function(rs){
			goBack();
		}
	});
}

function addNew()
{
	window.location.href= 'index.php?content=transport&add=y';	
}
function goBack()
{
	window.location.href = 'index.php?content=transport';	
}
</script>