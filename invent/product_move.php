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
	?>

<div class="container">
<!-- page place holder -->
<div class="row" style="height:35px;">
	<div class="col-sm-6" style="margin-top:10px;"><h4 class="title"><i class="fa fa-exchange"></i>&nbsp;<?php echo $page_name; ?></h4>
	</div>
    <div class="col-sm-6">
    	<p class="pull-right" style="margin-bottom:0px;">
<?php if( isset($_GET['view_move'] ) || isset( $_GET['view_cancle'] ) ) : ?>        
        	<button type="button" class="btn btn-warning btn-sm" onclick="go_back()"><i class="fa fa-arrow-left"></i> กลับ</button>
<?php	if( isset($_GET['view_cancle'])): ?>
			<button type="button" class="btn btn-primary btn-sm" onclick="move_cancle()"><i class="fa fa-cloud-upload"></i> ย้ายออก</button>
<?php	endif; ?>            
<?php else : ?>            
        	<button type="button" class="btn btn-success btn-sm" onclick="view_move()"><i class="fa fa-cloud"></i> สินค้าที่กำลังย้าย</button>
<?php endif; ?>            
		</p>
    </div>
</div>
<hr style="border-color:#CCC; margin-top: 5px; margin-bottom:15px;" />
<!-- End page place holder -->
<?php if( isset( $_GET['view_move'] ) ) : ?>
<div class="row">
	<div class="col-lg-12">
	<table class="table table-striped">
    	<tr><th colspan="6" style="text-align:center;">รายการสินค้ากำลังย้าย (รอย้ายเข้า)</th></tr>
        <tr>
            <th style="width: 30%;">สินค้า</th>
            <th style="width: 10%; text-align:center;">จำนวน</th>
            <th style="width: 20%;">จากโซน</th>
            <th>พนักงาน</th>
            <th style="width: 15%; text-align:center;">วันที่</th>
            <th style="width: 10%; text-align:center;">ย้ายเข้าโซน</th>
        </tr>
<?php $qs = dbQuery("SELECT * FROM tbl_move"); ?>
<?php if( dbNumRows($qs) > 0 ) : ?>
<?php 	while($rs = dbFetchArray($qs) ) : ?>
<?php 		$id = $rs['id_move']; ?>
<?php 		$reference = get_product_reference($rs['id_product_attribute']); ?>
<?php 		$id_wh 		= get_warehouse_by_zone($rs['id_zone']); ?>
		<tr style="font-size:12px;" id="row_<?php echo $id; ?>">
        	<td><?php echo $reference; ?></td>
            <td align="center"><span id="qty_<?php echo $id; ?>"><?php echo $rs['qty_move']; ?></span></td>
            <td><?php echo get_zone($rs['id_zone']); ?></td>
            <td><?php echo employee_name($rs['id_employee']); ?></td>
            <td align="center"><?php echo thaiDateTime($rs['time_move']); ?></td>
            <td align="center">
            	<button type="button" class="btn btn-primary btn-xs" onclick="move_to_zone(<?php echo $id; ?>, '<?php echo $reference; ?>', <?php echo $rs['qty_move']; ?>)">
                <i class="fa fa-download"></i> ย้ายเข้าโซน</button>
			</td>                
        </tr>
<?php  	endwhile; ?>    
  
<?php else : ?>
		<tr><td colspan="5" align="center">-----  ไม่พบรายการสินค้าที่กำลังย้าย  -----</td></tr>
<?php endif; ?>		
    </table>
    </div>
</div>
 
<div class="modal fade" id="modal_move" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 300px;">
    	<div class="modal-content">
        	<div class="modal-header">
            	<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
				<center style="margin-bottom:10px;"><h4 class='modal-title' id='modal_title'>title</h4></center>            
            </div>
            <div class="modal-body" id="modal_body">
            	<div class="col-lg-6">
                	<label>จำนวน</label>
                    <input type="text" class="form-control input-sm" style="text-align:center;" id="qty_in" />
                    <input type="hidden" id="c_qty" />
                </div>
                <div class="col-lg-12">&nbsp;</div>
                <div class="col-lg-12">
                	<label>บาร์โค้ดโซน</label>
                    <input type="text" class="form-control input-sm" id="destination_zone_barcode"  />
                </div>
                <div class="col-lg-12">&nbsp;</div>
                <div class="col-lg-12" style="margin-bottom:15px;">
                	<label>ชื่อโซน</label>
                    <input type="text" class="form-control input-sm" id="destination_zone_name" />
                </div>
                <div class="col-lg-12"><button type="button" class="btn btn-info btn-sm btn-block" onclick="move_in()">ย้าย</button></div>
            </div>
            <div class="modal-footer">
            	<input type="hidden" id="id_move" />
            	<input type="hidden" id="destination_id_zone" />
            </div>
        </div>
    </div>
</div>   

<?php elseif( isset( $_GET['view_cancle'] ) ) : ?>
<div class="row">
<div class="col-lg-12">
	<?php $qs = dbQuery("SELECT * FROM tbl_cancle"); ?>
    <?php if( dbNumRows($qs) > 0 ) : ?>
    <form id="move_cancle_form" action="controller/productmoveController.php?move_cancle_out" method="post">
    <input type="hidden" name="submit_gard" />
    <table class="table table-striped">
    <thead>
    <tr>    <th colspan="7" style="text-align:center;"><h4>สินค้าโซนยกเลิก</h4></th> </tr>
    <tr>
    	<th style="width: 20%;">สินค้า</th>
        <th style="width: 10%; text-align:center;">จำนวน</th>
        <th style="width: 20%; text-align:center;">จากโซน</th>
        <th style="width: 15%; text-align:center;">เลขที่เอกสาร</th>
        <th style="width: 15%; text-align:center;">วันที่</th>
        <th style="width: 10%; text-align:center;">จำนวนที่ย้าย</th>
        <th style="width: 10%; text-align:center;"></th>
    </tr>
    </thead>
    <?php while( $rs = dbFetchArray($qs) ) : ?>
    <?php 	$id = $rs['id_cancle']; 	?>
    <tr style="font-size:12px;" id="row_<?php echo $id; ?>">
    	<td><?php echo get_product_reference($rs['id_product_attribute']); ?></td>
        <td align="center"><?php echo number_format($rs['qty']); ?></td>
        <td align="center"><?php echo get_zone($rs['id_zone']); ?></td>
        <td align="center"><?php echo get_order_reference($rs['id_order']); ?></td>
        <td align="center"><?php echo thaiDateTime($rs['date_upd']); ?></td>
        <td align="center">
        	<input type="text" class="form-control input-sm move_qty" name="move_qty[<?php echo $id; ?>]" id="move_qty_<?php echo $id; ?>" style="text-align:center;" onkeyup="check_qty($(this), <?php echo $rs['qty']; ?>)" />
        </td>
        <td align="center"><button type="button" class="btn btn-default btn-sm" onclick="move_all(<?php echo $id; ?>, <?php echo $rs['qty']; ?>)"><i class="fa fa-angle-double-left"></i>&nbsp; ย้ายทั้งหมด</button></td>        
    </tr>
    <?php endwhile; ?>
    </table>
    </form>
    <?php else : ?>
    <center><h4>-----  ไม่พบรายการใดๆ  -----</h4></center>
    <?php endif; ?>
</div>
</div>

<?php else : ?>
<div class="row" id="zone">
	<div class="col-lg-3">
		<div class="input-group">
        	<span class="input-group-addon">บาร์โค้ดโซน</span>
            <input type="text" class="form-control input-sm" id="barcode_zone" name="barcode_zone" style="text-align:center;" placeholder="ยิงบาร์โค้ดโซน" />
        </div>
	</div>
    <div class="col-lg-1" style="text-align:center;">
		- OR -
	</div>
    <div class="col-lg-3">
		<div class="input-group">
        	<span class="input-group-addon">ชื่อโซน</span>
            <input type="text" class="form-control input-sm" id="zone_name" name="zone_name" style="text-align:center;" placeholder="ค้นหาชื่อโซน" />
        </div>
	</div>
    <div class="col-lg-1" style="text-align:center;">
		<button type="button" class="btn btn-default btn-sm" onclick="get_product_in_zone()">ค้นหา</button>
	</div>
    <div class="col-lg-1" style="text-align:center;">
		- OR -
	</div>
    <div class="col-lg-3">
		<button type="button" class="btn btn-info btn-sm btn-block" onclick="view_cancle()">ย้ายสินค้าจากโซนยกเลิก</button>
	</div>
    <input type="hidden" name="id_zone" id="id_zone" />
</div>

<div class="row" id="input-form" style="display:none;">
	<div class="col-lg-2 col-lg-offset-1">
    	<div class="input-group">
        	<span class="input-group-addon">จำนวน</span>
           	<input type="text" class="form-control input-sm" id="qty_out" name="qty_out" style="text-align:center;" onkeyup="valid_qty()" value="1"  />
           
        </div>
    </div>
    <div class="col-lg-3">
    	<div class="input-group">
        	<span class="input-group-addon">บาร์โค้ดสินค้า</span>
           	<input type="text" class="form-control input-sm" id="barcode_item" name="barcode_item" style="text-align:center;"  />
        </div>
    </div>
    <div class="col-lg-1">
    	<button type="button" class="btn btn-default btn-sm" id="btn_move_out" onclick="move_out()">ย้ายออก</button>
    </div>
    <div class="col-lg-1">
    	- OR -
    </div>
     <div class="col-lg-3">
    	<button type="button" class="btn btn-primary btn-sm btn-block" onclick="change_zone()">เปลี่ยนโซน</button>
    </div>
</div>

<hr style="border-color:#CCC; margin-top: 15px; margin-bottom:15px;" />

<div class="row">
	<div class="col-lg-12" id="result">
    
    </div>
</div>

<script id="template" type="text/x-handlebars-template">
{{#each this}}
	{{#if @first}}
	<table class="table table-striped">
    	<thead style="font-size:14px;">
        <tr><th colspan="3" style="text-align:center;"><strong>สินค้าในโซน {{ zone }}</strong></th></tr>
        <tr>
        	<th style="text-align:center; width:10%;">ลำดับ</th>
			<th style="width: 20%;">บาร์โค้ด</th>
            <th style="width: 30%;">ชื่อสินค้า</th>
            <th style="text-align:right">จำนวนในโซน</th>
        </tr>
        </thead>
	{{else}}       
        {{#if noproduct}}
        <tr>
        	<td colspan="3" align="center"><h4>----- ไม่มีสินค้าในโซนนี้  -----</h4></td>
        </tr>
        {{else}}
        <tr>
        	<td align="center">{{ no }}</td>
			<td>{{ barcode }}</td>
            <td>{{ product }}</td>
            <td align="right"><span id="qty_{{id}}">{{ qty }}</span></td>
        </tr>
        {{/if}}
	{{/if}}        
{{/each}}        
    </table>
</script>    
<?php endif; ?>
</div>

<!-----

<form id="cancle_move_form" action="controller/productmoveController.php?move_cancle_out" method="post">

-->
<script>
function move_in()
{
	var id_move 	= $("#id_move").val();
	var id_zone 	= $("#destination_id_zone").val();
	var barcode 	= $("#destination_zone_barcode").val();
	var zone_name	= $("#destination_zone_name").val();
	var qty 			= parseInt($("#qty_in").val());
	var c_qty 		= parseInt($("#c_qty").val());
	
	if( isNaN(qty) ){ swal("จำนวนสินค้าไม่ถูกต้อง"); }
	if(qty > c_qty){ swal("ผิดพลาด !", "ยอดที่จะย้าย ต้องไม่มากกว่ายอดที่มีอยู่ในรายการรอย้ายเข้าโซน", "warning");  return false; }
	if( (barcode != "" || zone_name != "" ) && !isNaN(parseInt(id_zone)) )
	{
		$("#modal_move").modal("hide");
		load_in();
		$.ajax({
			url:"controller/productmoveController.php?move_in",
			type: "POST", cache: "false", data: { "id_move" : id_move, "qty" : qty, "id_zone" : id_zone },
			success: function(rs)
			{
				load_out();
				var rs = $.trim(rs);
				if(rs == "success")
				{
					if(qty == c_qty)   /// ถ้ายอดย้ายออกกับยอดที่มีเท่ากัน ลบแถวได้เลย
					{
						$("#row_"+id_move).remove();
						clear_form();
					}
					else
					{
						var b_qty = c_qty - qty;
						$("#qty_"+id_move).text(b_qty)
					}
					swal({ title : "สำเร็จ", text: "ย้ายสินค้าเรียบร้อยแล้ว", timer: 1000, type: "success" });
				}
				else if(rs == "warehouse_missmatch")
				{
					swal("โซนไม่ถูกต้อง", "ไม่สามารถย้ายสินค้าข้ามคลังได้  กรุณาเลือกโซนใหม่ที่อยู่ในคลังเดียวกันกับตอนที่ย้ายออกมา", "error");
				}
				else
				{
					swal("ย้ายสินค้าไม่สำเร็จ", "ไม่สามารถย้ายสินค้าได้ กรุณาลองใหม่อีกครั้งภายหลัง", "error");
				}
			}
		});
	}
}

$("#modal_move").on("shown.bs.modal", function(event){ $("#destination_zone_barcode").focus(); });
function move_to_zone(id, reference, qty)
{
	$("#id_move").val(id);
	$("#c_qty").val(qty);
	$("#qty_in").val(qty);
	$("#modal_title").html(reference);
	$("#modal_move").modal("show");
}

function clear_form()
{
	$("#id_move").val("");
	$("#c_qty").val("");
	$("#qty_in").val("");
	$("#modal_title").html("");
}

function view_move()
{
	window.location.href = "index.php?content=ProductMove&view_move";	
}

function view_cancle()
{
	window.location.href = "index.php?content=ProductMove&view_cancle";	
}

function move_all(id, qty)
{
	$("#move_qty_"+id).val(qty);
}

function check_qty(el, c_qty)
{
	var qty 		= el.val();
	if(qty != "")
	{
		var qty 		= parseInt(qty);
		var c_qty 	= parseInt(c_qty);
		if( isNaN(qty) ){ swal("กรุณาระบุตัวเลขเท่านั้น"); el.val(''); }
		if( qty > c_qty){ swal("จำนวนที่ย้ายต้องไม่มากกว่าจำนวนที่มีในโซน"); el.val(''); }
	}
}

function move_cancle()
{
	var qty = 0;
	$(".move_qty").each(function(index, element) {
        if( $(this).val() != "")
		{
			qty++;	
		}
    });
	if(qty != 0 )
	{
		$("#move_cancle_form").submit();	
	}
}

function move_out()
{
	var barcode 	= $("#barcode_item").val();
	var qty			= $("#qty_out").val();
	var id_zone		= $("#id_zone").val();
	if(barcode != "")
	{
		if( isNaN( parseInt(qty) ) )	{ swal("จำนวนไม่ถูกต้อง"); return false; }
		if( isNaN( parseInt(id_zone) ) ){ swal("โซนไม่ถูกต้อง กรุณาเลือกโซนใหม่");  return false; }
		$.ajax({
			url:"controller/productmoveController.php?move_out",
			type:"POST", cache:"false", data:{ "barcode" : barcode, "id_zone" : id_zone, "qty" : qty },			
			success: function(rs)
			{
				var rs = $.trim(rs)
				if( rs == "no_product" )
				{
					swal("ผิดพลาด !!", "ไม่มีสินค้านี้ในฐานข้อมูล กรุณาตรวจสอบบาร์โค้ด", "error");
				}
				else if( rs == "not_in_zone" )
				{
					swal("ผิดพลาด !!", "ไม่มีสินค้านี้ในโซน", "error");
				}
				else if( rs == "qty_greater_than_stock" )
				{
					swal("ผิดพลาด !!", "จำนวนที่จะย้าย 'มากกว่า' จำนวนคงเหลือในโซน", "error");
				}
				else if( rs == "move_error" )
				{
					swal("ผิดพลาด !!", "ย้ายสินค้าไม่สำเร็จ", "error");
				}
				else
				{
					var arr = rs.split(" | ");
					var id  = arr[0];
					var qtyx = arr[1];
					moved(id, qtyx);						
				}
			}
		});
	}
}

function moved(id, qty)
{
	$("#barcode_item").val("");
	$("#qty_out").val(1);
	var c_qty = parseInt($("#qty_"+id).html());
	$("#qty_"+id).html(c_qty - qty);
	$("#barcode_item").focus();
}

$("#zone_name").autocomplete({
	source: "controller/autoComplete.php?get_zone_name",
	autoFocus: true,
	close: function()
	{
		var rs 		= $(this).val();
		var arr 		= rs.split(" : ");
		var id 		= arr[0];
		var name		= arr[1];
		$("#id_zone").val(id);
		$(this).val(name);
		get_items(id);	
	}
});

$("#destination_zone_name").autocomplete({
	source: "controller/autoComplete.php?get_zone_name",
	autoFocus: true,
	close: function()
	{
		var rs 		= $(this).val();
		var arr 		= rs.split(" : ");
		var id 		= arr[0];
		var name		= arr[1];
		$("#destination_id_zone").val(id);
		$(this).val(name);
	}
});

function get_destination_zone()
{
	var barcode 	= $("#destination_zone_barcode").val();
	if( barcode != "" )
	{
		$.ajax({
			url:"controller/productmoveController.php?get_zone"	,
			type:"POST", cache: "false", data:{ "barcode" : barcode },
			success: function(rs)
			{
				var rs 	= $.trim(rs);	
				if( rs == "fail" || rs == "")
				{
					swal("บาร์โค้ดโซนไม่ถูกต้อง หรือไม่มีโซนที่ระบุมา");	
				}
				else if( !isNaN(parseInt(rs)) )
				{
					$("#destination_id_zone").val(rs);
					move_in();
				}
				else
				{
					swal("ไม่พบโซนที่ระบุ");	
				}
			}
		});
	}
}
function get_zone()
{
	var barcode 	= $("#barcode_zone").val();
	if( barcode != "" )
	{
		$.ajax({
			url:"controller/productmoveController.php?get_zone"	,
			type:"POST", cache: "false", data:{ "barcode" : barcode },
			success: function(rs)
			{
				var rs 	= $.trim(rs);	
				if( rs == "fail" || rs == "")
				{
					swal("บาร์โค้ดโซนไม่ถูกต้อง หรือไม่มีโซนที่ระบุมา");	
				}
				else if( !isNaN(parseInt(rs)) )
				{
					$("#id_zone").val(rs);
					get_items(rs);
				}
				else
				{
					swal("ไม่พบโซนที่ระบุ");	
				}
			}
		});
	}
}

function get_items(id)
{
	$.ajax({
		url:"controller/productmoveController.php?get_items",
		type:"POST", cache: "false", data:{ "id_zone" : id },
		success: function(rs)
		{
			var rs 		= $.trim(rs);	
			var source	= $("#template").html();
			var data		= $.parseJSON(rs);
			var output	= $("#result");
			render(source, data, output);
			zone_page();
		}
	});
}

function change_zone()
{
	$("#id_zone").val("");
	$("#zone_name").val("");
	$("#barcode_zone").val("");
	$("#result").html("");
	$("#input-form").css("display", "none");
	$("#zone").css("display", "");	
	$("#barcode_zone").focus();
}

function zone_page()
{
	$("#qty_out").val(1);
	$("#barcode_item").val('');
	$("#zone").css("display", "none");
	$("#input-form").css("display", "");	
	$("#barcode_item").focus();
}

function valid_qty()
{
	var qty = $("#qty_out").val();
	if(qty != "")
	{
		if(isNaN(parseInt(qty))){ swal("ใส่ตัวเลขเท่านั้น"); $("#qty_out").focus();}	
	}
}
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

function go_back()
{
	window.location.href = "index.php?content=ProductMove";	
}

$("#barcode_zone").keyup(function(e){
	if(e.keyCode == 13)
	{
		get_zone();	
	}
});

$("#destination_zone_barcode").keyup(function(e) {
    if(e.keyCode == 13 )
	{
		get_destination_zone();	
	}
});

$("#barcode_item").keyup(function(e){
	if(e.keyCode == 13 )
	{
		move_out();
	}
});
</script>