<?php 
	$page_menu = "invent_sale";
	$page_name = "เพิ่ม/แก้ไข รายชื่อผู้ขาย";
	$id_tab = 45;
	$id_profile = $_COOKIE['profile_id'];
    $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
	$btn = "";
	if( isset($_GET['add']) || isset($_GET['edit']) )
	{
		$btn .= "<button type='button' onclick='go_back()' class='btn btn-warning'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button>";
	}else{
		if( $add ){ $btn .= "<button type='button' id='btn_save' onclick='add_new()' class='btn btn-success btn-sm'><i class='fa fa-plus'></i>&nbsp; เพิ่มใหม่</button>"; }	
	}
	?>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6" style="vertical-align:text-bottom;"><h4 class="title"><i class="fa fa-user"></i>&nbsp;<?php echo $page_name; ?></h4>
	</div>
    <div class="col-sm-6">
     <p class="pull-right">
       <?php 
	   	echo $btn;
	   ?>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php if( isset( $_GET['add'] ) ) : ?>
<!-------------  Add  --------------->
	<div class="row"    >
    	<div class="col-lg-2">
        	<label>รหัส</label>
            <input type="text" id="code" class="form-control" placeholder="รหัสผู้ขาย (จำเป็น)" autofocus />
        </div>
        <div class="col-lg-4">
        	<label>ชื่อผู้ขาย/บริษัท</label>
            <input type="text" id="name" class="form-control" placeholder="ชื่อผู้ขาย/บริษัท (จำเป็น)" />
        </div>
        <div class="col-lg-2">
        	<label>เครดิตเทอม</label>
            <input type="text" id="credit_term" class="form-control" placeholder="เครดิตเทอม" value="0" />
        </div>
        <div class="col-lg-2">
        	<label>สถานะ</label>
          	<select id="active" class="form-control"><option value="1">เปิดใช้งาน</option><option value="0">ปิดใช้งาน</option></select>
        </div>
        <div class="col-lg-2">
        	<label style="visibility:hidden">รหัส</label>
            <button type="button" id="btn_save" class="btn btn-success btn-block" onclick="save_add()"><i class="fa fa-plus"></i>&nbsp; เพิ่ม</button>
        </div>
    </div>
    <hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
    <div class="row">
    	<div class="col-lg-12">
        	<table class="table table-striped">
            <thead>
            	<th style="width:5%; text-align:center">ID</th>
                <th style="width:15%;">รหัส</th>
                <th>ชื่อผู้ขาย/บริษัท</th>
                <th style="width:10%; text-align:center;">เครดิตเทอม</th>
                <th style="width:15%; text-align:center">สถานะ</th>
            </thead>
            <tbody id="result">
            
            </tbody>
            </table>
        </div>
    </div>
	<script id="template" type="text/x-handlebars-template">
		<tr>
            	<td style="width:5%; text-align:center;">{{ id }}</td>
                <td style="width:15%;">{{ code }}</td>
                <td>{{ name }}</td>
				<td align="center">{{ credit_term }}</td>
                <td style="width: 15%; text-align:center">{{ status }}</td>
            </tr>
	</script>
<!-------------  Add  --------------->
<?php elseif( isset( $_GET['edit'] ) && isset($_GET['id']) ) : ?>
<!-------------  Edit  --------------->
	<?php $qs = dbQuery("SELECT * FROM tbl_supplier WHERE id = ".$_GET['id']." LIMIT 1"); ?>
    <?php if( dbNumRows($qs) == 1 ) : ?>
    <?php 	$rs = dbFetchArray($qs); ?>
	<div class="row"    >
    	<div class="col-lg-2">
        	<label>รหัส</label>
            <input type="text" id="code" class="form-control" placeholder="รหัสผู้ขาย (จำเป็น)" value="<?php echo $rs['code']; ?>" autofocus />
        </div>
        <div class="col-lg-4">
        	<label>ชื่อผู้ขาย/บริษัท</label>
            <input type="text" id="name" class="form-control" placeholder="ชื่อผู้ขาย/บริษัท (จำเป็น)" value="<?php echo $rs['name']; ?>" />
        </div>
        <div class="col-lg-2">
        	<label>เครดิตเทอม</label>
            <input type="text" id="credit_term" class="form-control" placeholder="เครดิตเทอม" value="<?php echo $rs['credit_term']; ?>" />
        </div>
        <div class="col-lg-2">
        	<label>สถานะ</label>
          	<select id="active" class="form-control"><option value="1" <?php echo isSelected($rs['active'],1); ?>>เปิดใช้งาน</option><option value="0" <?php echo isSelected($rs['active'],0); ?>>ปิดใช้งาน</option></select>
        </div>
        <div class="col-lg-2">
        	<label style="visibility:hidden">รหัส</label>
            <button type="button" id="btn_save" class="btn btn-success btn-block" onclick="save_edit(<?php echo $rs['id']; ?>)"><i class="fa fa-save"></i>&nbsp; บันทึก</button>
        </div>
    </div>
    <?php else : ?>
    <div class="row"><div class="col-lg-12"><center><h4>-----  ไม่พบข้อมูล  -----</h4></center></div></div>
    <?php endif; ?>
<!-------------  Edit  --------------->
<?php else : ?>
<!-------------  List  --------------->
	<?php 	
		if( isset($_POST['search_text']) ) : 
			$search_text = $_POST['search_text']; 
			setcookie("supplier_search_text", $search_text, time() + 3600, "/");
		elseif( isset($_COOKIE['supplier_search_text'])) :
			$search_text = $_COOKIE['supplier_search_text'];
		else :
			$search_text = '';
		endif;
	?>		
<form id="search_form" method="post" action="index.php?content=supplier">
<div class="row">
	<div class="col-lg-4 col-lg-offset-3">
    	<input type="text" class="form-control" id="search_text" name="search_text" value="<?php echo $search_text; ?>" placeholder="ค้นหาผู้ขาย พิมพ์ชื่อแล้วกด Eenter หรือ คลิกค้นหา" autofocus="autofocus" style="text-align:center" />
    </div>
    <div class="col-lg-2"><button type="button" id="btn_search" class="btn btn-primary btn-block"><i class="fa fa-search"></i>&nbsp; ค้นหา</button></div>
    <div class="col-lg-2"><a href="controller/supplierController.php?clear_filter"><button type="button" class="btn btn-warning"><i class="fa fa-refresh"></i>&nbsp; เคลียร์ฟิลเตอร์</button></a></div>
</div>
</form>

<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:15px;' />

	<?php
		$paginator = new paginator();
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
        if($search_text !="") : 
            $where = "WHERE code LIKE '%".$search_text."%' OR name LIKE '%".$search_text."%'";
        else :
            $where = "WHERE id != 0";
        endif;
        ?>
        <?php     
		$paginator->Per_Page("tbl_supplier",$where,$get_rows);
		$paginator->display($get_rows,"index.php?content=supplier");
		$qs 				= dbQuery("SELECT * FROM tbl_supplier ".$where." LIMIT ".$paginator->Page_Start.", ".$paginator->Per_Page);
?>	
        
<div class="row">
	<div class="col-lg-12">
    <table class="table table-striped">
    <thead style="font-size:14px;">
    	<th style="width:5%; text-align:center">ID</th>
        <th style="width: 15%;">รหัส</th>
        <th>ชื่อ/บริษัท</th>
        <th style="width:10%; text-align:center">เครดิตเทอม</th>
        <th style="width:10%; text-align:center">สถานะ</th>
        <th style="width:15%; text-align:right">การกระทำ</th>
    </thead>
    <tbody>
    <?php if( dbNumRows($qs) > 0 ) : ?>
		<?php while( $rs = dbFetchArray($qs) ) : ?>
		<tr id="row_<?php echo $rs['id']; ?>">
        	<td align="center"><?php echo $rs['id']; ?></td>
            <td><?php echo $rs['code']; ?></td>
            <td><?php echo $rs['name']; ?></td>
            <td align="center"><?php echo $rs['credit_term']; ?></td>
            <td align="center"><?php echo isActived($rs['active']); ?></td>
            <td align="right">
                <button type="button" class="btn btn-warning" onclick="edit(<?php echo $rs['id']; ?>)"><i class="fa fa-pencil"></i></button>
                <button type="button" class="btn btn-danger" onclick="delete_row(<?php echo $rs['id']; ?>, '<?php echo $rs['code']; ?>', '<?php echo $rs['name']; ?>')"><i class="fa fa-trash"></i></button>
            </td>
        </tr>		
		<?php endwhile;  ?>
	<?php else : ?>
    	<tr><td colspan="6"><center><h4>-----  ไม่มีรายการ  -----</h4></center></td></tr>
    <?php endif; ?>       
    </tbody>
    </table>
    </div>
</div>
<!-------------  List  --------------->
<?php endif; ?>
</div><!-- end container --->
<script>
function save_add()
{
	load_in();
	var code 	= $("#code").val();
	var name 	= $("#name").val();
	var status 	= $("#active").val();
	var credit	= $("#credit_term").val();
	if( code == "" )
	{
		load_out();
		swal("จำเป็นต้องกำหนดรหัสผู้ขาย");
		return false;
	}else if( name == "" ){
		load_out();
		swal("จำเป็นต้องระบุชื่อผู้ขาย");
		return false;
	}else{
		if( credit == '' ){ credit = 0; }
		$.ajax({
			url:"controller/supplierController.php?check_duplicate",
			type:"POST", cache:false, data:{ "code" : code},
			success: function(rs)
			{
				var rs = $.trim(rs);
				if( rs != "fail" )
				{
					$.ajax({
						url : "controller/supplierController.php?add_new",
						type:"POST", cache:false, data:{ "code" : code, "name" : name, "credit_term" : credit, "status" : status },
						success: function(data)
						{
							var data = $.trim(data);
							if( data == "fail" )
							{
								load_out();
								swal("Error !!", "เพิ่มรายชื่อผู้ขายไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
							}else{
								var source 	= $("#template").html();
								var data 		= $.parseJSON(data);
								var row		= Handlebars.compile(source);
								var html		= row(data);
								$("#result").prepend(html);
								clear_field();
								load_out();
							}
						}
					});
				}else{
					load_out();
					swal("รหัสผู้ขายซ้ำ");
					return false;	
				}
			}
		});
	}
}

function save_edit(id)
{
	load_in();
	var code 	= $("#code").val();
	var name 	= $("#name").val();
	var status 	= $("#active").val();
	var credit	= $("#credit_term").val();
	
	if( code == "" )
	{
		load_out();
		swal("จำเป็นต้องกำหนดรหัสผู้ขาย");
		return false;
	}else if( name == "" ){
		load_out();
		swal("จำเป็นต้องระบุชื่อผู้ขาย");
		return false;
	}else{
		if( credit == ""){ credit = 0; }
		$.ajax({
			url:"controller/supplierController.php?check_code",
			type:"POST", cache:false, data:{ "code" : code, "id" : id},
			success: function(rs)
			{
				var rs = $.trim(rs);
				if( rs != "fail" )
				{
					$.ajax({
						url : "controller/supplierController.php?update",
						type:"POST", cache:false, data:{ "code" : code, "name" : name, "credit_term" : credit, "status" : status, "id" : id },
						success: function(data)
						{
							var data = $.trim(data);
							if( data == "fail" )
							{
								load_out();
								swal("Error !!", "เพิ่มรายชื่อผู้ขายไม่สำเร็จ กรุณาลองใหม่อีกครั้ง", "error");
							}else{
								window.location.href = "index.php?content=supplier";
								load_out();
							}
						}
					});
				}else{
					load_out();
					swal("รหัสผู้ขายซ้ำ");
					return false;	
				}
			}
		});
	}
}

function edit(id)
{
	window.location.href="index.php?content=supplier&edit=y&id="+id;	
}

function delete_row(id, code, name)
{
	swal({ 
			title: "แน่ใจนะ !!", 
			text: "คุณแน่ใจนะว่าต้องการลบ "+code+" : "+name, 
			type: "warning", 
			showCancelButton: true, 
			confirmButtonColor: "#DD6B55",
  			confirmButtonText: "ใช่ ฉันต้องการลบ",
		 }, 
		 function(isConfirm){ 
		 	if(isConfirm)
			{
				load_in();
				$.ajax({
					url:"controller/supplierController.php?delete",
					type: "POST", cache:false, data: { "id" : id },
					success: function(rs)
					{
						var rs = $.trim(rs);
						if( rs == "success" )
						{
							load_out();
							swal({ title: "เรียบร้อย", text: "ลบ "+name+" เรียบร้อยแล้ว", type: "success", timer: 1200});
							$("#row_"+id).remove();
						}else{
							load_out();
							swal({ title: "Error !!", text: "ลบ รายการไม่สำเร็จ", type: "error", timer: 3000 });
						}
					}
				});
			}
		});
}

function clear_field()
{
	$("#code").val("");
	$("#name").val("");
	$("#active").val(1);	
}
function add_new()
{
	window.location.href = "index.php?content=supplier&add=y";	
}

function go_back()
{
	window.location.href = "index.php?content=supplier";	
}

$("#btn_search").click(function(e) {
	if( $("#search_text").val() != "" )
	{
       	$("#search_form").submit();
	}
 });
</script>