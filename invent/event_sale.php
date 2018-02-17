<?php 
	$page_menu = "invent_sale";
	$page_name = "พนักงานขายอีเว้นท์";
	$id_tab = 27;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
	$btn = "";
	$btn_back = "<button class='btn btn-warning btn-sm' onclick='go_back()'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button>";
	if( isset( $_GET['add'] ) )
	{
		$btn .= $btn_back;
		if($add)
		{
			$btn .= "<button class='btn btn-success btn-sm' onclick='check_add()'><i class='fa fa-save'></i>&nbsp; บันทึก</button>";
		}
	}
	else if( isset( $_GET['edit'] ) )
	{
		$btn .= $btn_back;
		if($edit)
		{
			$btn .= "<button class='btn btn-success btn-sm' onclick='check_edit()'><i class='fa fa-save'></i>&nbsp; บันทึก</button>";
		}
	}
	else
	{
		$btn .= "<button class='btn btn-success btn-sm' onclick='add_new()'><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button>";		
	}
	?>
<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-sm-6" style="margin-top:10px;"><h4 class="title"><i class="fa fa-user"></i>&nbsp;<?php echo $page_name; ?></h4>
	</div>
    <div class="col-sm-6">
       <p class="pull-right" style="margin-bottom:0px;">
      	<?php echo $btn; ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<!-- End page place holder -->
<?php if( isset($_GET['add']) ) : ?>
<div class="row">
	<div class="col-lg-4">	<span class="form-control input-sm label-left" style="border: 0px;">พนักงาน</span></div>
    <div class="col-lg-4"><input type="text" class="form-control input-sm" name="employeee" id="employee" placeholder="เลือกพนักงาน" /></div>
    <div class="col-lg-4">&nbsp;</div>
	<div class="col-lg-12">&nbsp;</div>
	<div class="col-lg-4">	<span class="form-control input-sm label-left" style="border: 0px;">โซน</span></div>
    <div class="col-lg-4"><input type="text" class="form-control input-sm" name="zone" id="zone" placeholder="เลือกโซนที่รับผิดชอบ" /></div>
	<div class="col-lg-4">&nbsp;</div>
	<div class="col-lg-12">&nbsp;</div>
    <div class="col-lg-4">	<span class="form-control input-sm label-left" style="border: 0px;">สถานะ</span></div>
    <div class="col-lg-4">
    	<div class="btn-group">
        	<button class="btn btn-success btn-sm" id="btn_enable" onclick="enable()" style="width:50%;"><i class="fa fa-check"></i>&nbsp; เปิดใช้งาน</button>
            <button class="btn btn-sm" id="btn_disable" onclick="disable()" style="width:50%;"><i class="fa fa-ban"></i>&nbsp; ปิดใช้งาน</button>
        </div>
    </div>
<input type="hidden" name="id_employee" id="id_employee" value="" />
<input type="hidden" name="id_zone" id="id_zone" value="" />
<input type="hidden" name="active" id="active" value="1" />
</div>


<?php elseif( isset($_GET['edit']) && isset($_GET['id_event_sale']) ) : ?>
	<?php $qs = dbQuery("SELECT * FROM tbl_event_sale WHERE id_event_sale = ".$_GET['id_event_sale']); ?>
    <?php $rs = dbFetchArray($qs); ?>
<div class="row">
	<div class="col-lg-4">	<span class="form-control input-sm label-left" style="border: 0px;">พนักงาน</span></div>
    <div class="col-lg-4"><input type="text" class="form-control input-sm" name="employeee" id="employee" placeholder="เลือกพนักงาน" value="<?php echo employee_name($rs['id_employee']); ?>" /></div>
    <div class="col-lg-4">&nbsp;</div>
	<div class="col-lg-12">&nbsp;</div>
	<div class="col-lg-4">	<span class="form-control input-sm label-left" style="border: 0px;">โซน</span></div>
    <div class="col-lg-4"><input type="text" class="form-control input-sm" name="zone" id="zone" placeholder="เลือกโซนที่รับผิดชอบ" value="<?php echo get_zone($rs['id_zone']); ?>" /></div>
	<div class="col-lg-4">&nbsp;</div>
	<div class="col-lg-12">&nbsp;</div>
    <div class="col-lg-4">	<span class="form-control input-sm label-left" style="border: 0px;">สถานะ</span></div>
    <div class="col-lg-4">
    	<div class="btn-group">
        	<button class="btn btn-sm<?php if($rs['active'] == 1 ){ echo " btn-success"; } ?>" id="btn_enable" onclick="enable()" style="width:50%;"><i class="fa fa-check"></i>&nbsp; เปิดใช้งาน</button>
            <button class="btn btn-sm<?php if($rs['active'] ==0 ){ echo " btn-danger"; } ?>" id="btn_disable" onclick="disable()" style="width:50%;"><i class="fa fa-ban"></i>&nbsp; ปิดใช้งาน</button>
        </div>
    </div>
<input type="hidden" name="id_event_sale" id="id_event_sale" value="<?php echo $rs['id_event_sale']; ?>"  />
<input type="hidden" name="id_employee" id="id_employee" value="<?php echo $rs['id_employee']; ?>" />
<input type="hidden" name="id_zone" id="id_zone" value="<?php echo $rs['id_zone']; ?>" />
<input type="hidden" name="active" id="active" value="<?php echo $rs['active']; ?>" />
</div>


<?php else : ?>
	<?php 
			if( isset($_POST['search_text']) )
			{
				$search_text = $_POST['search_text'];
				setcookie("event_sale_search_text", $search_text, time()+3600, "/");	
			}else if( isset($_COOKIE['event_sale_search_text']) ){
				$search_text = $_COOKIE['event_sale_search_text'];
			}else{
				$search_text = "";
			}
			
			if( $search_text != "" )
			{
				$where = " WHERE first_name LIKE '%".$search_text."%' OR last_name LIKE '%".$search_text."%' ";
			}else{
					$where = "";	
			}
			
		?>
			
<div class="row">
	<div class="col-lg-4 col-lg-offset-4">
    	<div class="input-group">
        	<input type="text" id="search_text" name="search_text" class="form-control input-sm" placeholder="ค้นหาชื่อพนักงาน"  />
            <span class="input-group-btn"><button type="button" class="btn btn-primary btn-sm" value="<?php echo $search_text; ?>" onclick="set_filter()"><i class="fa fa-search"></i> ค้นหา</button></span>
        </div>    
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
	<?php 	
			$paginator 	= new paginator();
			if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows']; $paginator->setcookie_rows($get_rows) ;}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
			$table = "tbl_event_sale JOIN tbl_employee ON tbl_event_sale.id_employee = tbl_employee.id_employee";
			$paginator->Per_Page($table, $where, $get_rows);
			$paginator->display($get_rows,"index.php?content=event_sale");
		?>
<div class="row">
<div class="col-lg-12">
	<table class="table table-striped">
    <thead>
    <tr>
    	<th style="width:5%; text-align:center;">ID</th>
        <th style="width:35%;">พนักงาน</th>
        <th style="width:35%;">โซน</th>
        <th style="width:5%; text-align:center;">สถานะ</th>
        <th style="text-align:right;">การกระทำ</th>
    </tr>
    </thead>
    <?php $field = "id_event_sale, first_name, last_name, id_zone, tbl_event_sale.active as active"; ?>
    <?php $qs = dbQuery("SELECT ".$field." FROM ".$table . $where." LIMIT ".$paginator->Page_Start.", ".$paginator->Per_Page); ?>
	<?php if(dbNumRows($qs) > 0 ) : ?>
	<?php 	while( $rs = dbFetchArray($qs) ) : 	?>
				<tr style="font-size:12px;" id="row_<?php echo $rs['id_event_sale']; ?>">
                	<td align="center"><?php echo $rs['id_event_sale']; ?></td>
                    <td><?php echo $rs['first_name']." ".$rs['last_name']; ?></td>
                    <td><?php echo get_zone($rs['id_zone']); ?></td>
                    <td align="center"><?php echo isActived($rs['active']); ?></td>
                    <td align="right">
                    <?php if( $edit ) : ?> 	<button type="button" class="btn btn-warning btn-xs" onclick="edit_row(<?php echo $rs['id_event_sale']; ?>)"><i class="fa fa-pencil"></i></button><?php endif; ?>
                    <?php if( $delete ) : ?><button type="button" class="btn btn-danger btn-xs" onclick="delete_row(<?php echo $rs['id_event_sale']; ?>, '<?php echo $rs['first_name']; ?>')"><i class="fa fa-trash"></i></button><?php endif; ?>
                    </td>
                </tr>		
	<?php	endwhile; 	?>
	<?php else : 			?>
			<tr><td colspan="4" align="center">---------- ไม่มีรายการ  -----------</td></tr>
	<?php endif; ?>
    </table>
</div>
</div>
        
<?php endif; ?>
<?php $sqr = dbQuery("SELECT id_employee, first_name, last_name FROM tbl_employee"); ?>
<?php if(dbNumRows($sqr) > 0 ) : ?>
        	<script>
			var EmployeeList = [
        	<?php while($rs = dbFetchArray($sqr)) : ?>
            	<?php echo "\"".$rs['id_employee']." | ".$rs['first_name']." ".$rs['last_name']."\","; ?>
            <?php endwhile; ?>
			];
			</script>
        <?php endif; ?>
<?php $sql = dbQuery("SELECT id_zone, zone_name FROM tbl_zone WHERE id_warehouse = 2"); ?>
<?php if(dbNumRows($sql) > 0 ) : ?>
		<script>
		var ZoneList = [
		<?php while($rs = dbFetchArray($sql)) : ?>
			<?php echo "\"".$rs['id_zone']." | ".$rs['zone_name']."\","; ?>
		<?php endwhile; ?>
		];
		</script>
<?php endif; ?>        
<script>

$("#employee").autocomplete({
	source : EmployeeList,
	autoFocus : true,
	close: function()
	{
		var em = $(this).val();
		var arr = em.split(" | ");
		$("#id_employee").val(arr[0]);
		$(this).val(arr[1]);
	}
});

$("#employee").keyup(function(e) {
    if($(this).val() == ""){
		$("#id_employee").val("");
	}
});

$("#zone").autocomplete({
	source : ZoneList,
	autoFocus: true,
	close: function()
	{
		var zn = $(this).val();
		var arr = zn.split(" | ");
		$("#id_zone").val(arr[0]);
		$(this).val(arr[1]);	
	}
});

$("#zone").keyup(function(e) {
    if($(this).val() == ""){
		$("#id_zone").val("");
	}
});
function enable()
{
	$("#active").val(1);
	$("#btn_disable").removeClass("btn-danger");
	$("#btn_enable").addClass("btn-success");	
}

function disable()
{
	$("#active").val(0);
	$("#btn_enable").removeClass("btn-success");
	$("#btn_disable").addClass("btn-danger");
}

function check_add()
{
	var id_employee	= $("#id_employee").val();
	var id_zone			= $("#id_zone").val();
	if(id_employee == "" )
	{
		swal("โปรดระบุพนักงาน");
		return false;
	}
	if(id_zone == "")
	{
		swal("โปรดระบุโซน");
		return false;
	}
	load_in();
	$.ajax({
		url:"controller/saleController.php?check_event_sale",
		type:"POST", cache:"false", data: { "id_employee" : id_employee, "id_zone" : id_zone },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs == "0")
			{
				load_out();
				save_add();
			}else{
				load_out();
				swal("โซนซ้ำ");
			}
		}
	});
}

function check_edit()
{
	var id_event_sale 	= $("#id_event_sale").val();
	var id_employee	= $("#id_employee").val();
	var id_zone			= $("#id_zone").val();
	if(id_employee == "" )
	{
		swal("โปรดระบุพนักงาน");
		return false;
	}
	if(id_zone == "")
	{
		swal("โปรดระบุโซน");
		return false;
	}
	load_in();
	$.ajax({
		url:"controller/saleController.php?check_event_sale",
		type:"POST", cache:"false", data: { "id_event_sale" : id_event_sale, "id_employee" : id_employee, "id_zone" : id_zone },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs == "0")
			{
				load_out();
				save_edit();
			}else{
				load_out();
				swal("โซนซ้ำ");
			}
		}
	});
}
function save_add()
{
	var id_employee	= $("#id_employee").val();
	var id_zone			= $("#id_zone").val();
	var active 			= $("#active").val();
	$.ajax({
		url:"controller/saleController.php?add_event_sale",
		type:"POST", cache:"false", data: { "id_employee" : id_employee, "id_zone" : id_zone, "active" : active },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs == "success")
			{
				load_out();
				swal({ title:"สำเร็จ", text:"เพิ่มพนักงานขายอีเว้นท์เรียบร้อยแล้ว", timer: 1000, type:"success"});
				go_back();
			}else{
				load_out();
				swal({title: "ไม่สำเร็จ", text:"เพิ่มพนักงานขายอีเว้นท์ไม่สำเร็จ", type: "error"});
			}
		}
	});
}

function save_edit()
{
	var id_event_sale 	= $("#id_event_sale").val();
	var id_employee	= $("#id_employee").val();
	var id_zone			= $("#id_zone").val();
	var active 			= $("#active").val();
	$.ajax({
		url:"controller/saleController.php?update_event_sale",
		type:"POST", cache:"false", data: { "id_event_sale" : id_event_sale, "id_employee" : id_employee, "id_zone" : id_zone, "active" : active },
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs == "success")
			{
				load_out();
				swal({ title:"สำเร็จ", text:"แก้ไขพนักงานขายอีเว้นท์เรียบร้อยแล้ว", timer: 1000, type:"success"});
				go_back();
			}else{
				load_out();
				swal({title: "ไม่สำเร็จ", text:"แก้ไขพนักงานขายอีเว้นท์ไม่สำเร็จ", type: "error"});
			}
		}
	});
}
function go_back()
{
	window.location.href="index.php?content=event_sale";	
}

function add_new()
{
	window.location.href = "index.php?content=event_sale&add";	
}

function edit_row(id)
{
	window.location.href = "index.php?content=event_sale&edit=y&id_event_sale="+id;	
}

function delete_row(id, name)
{
	swal({
		  title: "คุณแน่ใจ ?",
		  text: "ต้องการลบ "+name+" ออกจากพนักงานอีเว้นท์",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "ใช่ ลบเลย",
		  cancelButtonText: "ยกเลิก",
		  closeOnConfirm: false,
		  html: true
		}, function(){
			load_in();
			$.ajax({
				url:"controller/saleController.php?delete_event_sale&id_event_sale="+id,
				type:"GET", cache:"false",
				success: function(rs)
				{
					var rs = $.trim(rs);
					if(rs == "success")
					{
						load_out();
						$("#row_"+id).remove();
						swal({ title: "เรียบร้อย", text: "ลบเรียบร้อยแล้ว", timer: 1000, type: "success"});
					}else{
						load_out();
						swal("ไม่สำเร็จ", "ลบไม่สำเร็จ", "error");
					}
				}
			});						
	});
}

</script>