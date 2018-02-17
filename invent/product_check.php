<?php
	$page_menu = "invent_product_check";
	$page_name = "ตรวจนับสินค้า";
	$id_tab = 10;
	$id_profile = $_COOKIE['profile_id'];
   $pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
	$btn = "";
	if( isset( $_GET['id_zone'] ) )
	{
		if( isset($_GET['add'] ) )
		{
			$btn .= "<button type='button' class='btn btn-warning btn-sm' onclick='go_back()'><i class='fa fa-arrow-left'></i>&nbsp; กลับ</button>";
		}
		if( $add ){ $btn .= "<button type='button' class='btn btn-primary btn-sm' onclick='add()'><i class='fa fa-plus'></i>&nbsp; เพิ่มสินค้า</button>"; }
		if( $edit ){ $btn .= "<button type='button' class='btn btn-success btn-sm' onclick=\"save_edit()\"><i class='fa fa-save'></i>&nbsp; บันทึก</button>"; }
	}
	function get_diff($id_product_attribute, $id_zone)
	{
		$diff = 0;
		$qs = dbQuery("SELECT qty_add, qty_minus FROM tbl_diff WHERE id_product_attribute = ".$id_product_attribute." AND id_zone = ".$id_zone." AND status_diff = 0");
		if(dbNumRows($qs) > 0 )
		{
			list($qty_add, $qty_minus) = dbFetchArray($qs);
			if($qty_add == 0 && $qty_minus > 0)
			{
				$diff = $qty_minus;
			}
			else if($qty_add > 0 && $qty_minus == 0)
			{
				$diff = $qty_add;
			}
		}
		return $diff;
	}
	?>
<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-sm-6" style="margin-top:10px;"><h4 class="title"><i class="fa fa-check-square-o"></i>&nbsp; <?php echo $page_name; ?></h4></div>
    <div class="col-sm-6"><p class="pull-right"><?php echo $btn; ?></p></div>
</div>
<hr style="border-color:#CCC; margin-top: 10px; margin-bottom:15px;" />
<!-- End page place holder -->
<?php if( isset( $_GET['add'] ) ) : ?>

<?php elseif( isset( $_GET['id_zone'] ) ) : ?>
<div class="row">
	<div class="col-lg-3 col-lg-offset-4">
    	<input type="text" class="form-control input-sm" id="txt_zone" placeholder="ระบุ ชื่อโซน หรือ ยิงบาร์โค้ดโซน" style="text-align:center;" autofocus />
    </div>
    <div class="col-lg-1">
    	<button type="button" class="btn btn-primary btn-sm" id="btn_ok" onclick="get_zone()"><i class="fa fa-check-square-o"></i>&nbsp; ตรวจนับ</button>
    </div>
</div>
<hr style="border-color:#CCC; margin-top: 10px; margin-bottom:15px;" />
<div class="row">
<form method="post" id="stock_form" name="edit_qty" action="controller/productcheckController.php?editqty=y"   >
	<div class="col-lg-12">
		<table class="table table-striped">
        <thead>
        <tr>
		<th colspan="5" style="text-align:center">โซน <?php echo get_zone($_GET['id_zone']);?> </th>
		</tr>
		<tr>
		<th style="text-align: left">ชื่อสินค้า</th>
        <th width="15%"  style="text-align: center">จำนวนก่อนนับ</th>
        <th width="10%" style="text-align: center">จำนวนที่นับจริง</th>
        <th width="3%">&nbsp;</th>
        <th width="10%" style="text-align: center">ยอดต่าง</th>
        <th width="10%">&nbsp;</th>
		</tr>
		</thead>
   <?php if(isset($_GET['saved']) ): ?>
   		<input type="hidden" id="saved" />
   <?php endif; ?>
   <tbody id="item">
	<?php $qs = dbQuery("SELECT * FROM tbl_stock WHERE id_zone = ".$_GET['id_zone']); ?>
	<?php if(dbNumRows($qs) > 0 ) : ?>
    <?php 	while($rs = dbFetchArray($qs) ) : ?>
    <?php 		$diff = get_diff($rs['id_product_attribute'], $_GET['id_zone']); ?>
    <?php 		$id = $rs['id_product_attribute']; ?>
    <?php		$count = $rs['qty'] + $diff; ?>
    	<tr>
        	<td><?php echo get_product_reference($rs['id_product_attribute']); ?></td>
            <td align="center"><?php echo number_format($rs['qty']); ?><input type="hidden" id="qty_<?php echo $id; ?>" name="qty[<?php echo $id; ?>]" value="<?php echo $rs['qty']; ?>" /></td>
            <td align="center">
				<?php if($edit) : ?>
            	<input type="text" class="form-control input-sm" style="text-align:center;" id="qty_check_<?php echo $id; ?>" name="qty_check[<?php echo $id; ?>]" onkeyup="check_number($(this))" value="<?php echo $count; ?>" />
                <?php endif; ?>
           </td>
           <td align="center">
           		<?php if(isset($_GET['saved'])) : ?>
                <i class="fa fa-check" id="checked_<?php echo $id; ?>" style="color:green;"></i>
                <?php else : ?>
           		<i class="fa fa-check" id="checked_<?php echo $id; ?>" style="color:green; display:none;"></i>
                <?php endif; ?>
           </td>
           <td align="center"><span id="diff_<?php echo $id; ?>"><?php echo $diff; ?></span></td>
           <td align="center">
           		<?php if( $edit ) : ?>
                <button type="button" class="btn btn-info btn-sm btn-block" onclick="save_checked(<?php echo $id; ?>, <?php echo $_GET['id_zone']; ?>)"><i class="fa fa-save"></i>&nbsp; บันทึก</button>
                <?php endif; ?>
           </td>
        </tr>
    <?php	endwhile; ?>
    <?php else : ?>
    	<tr><td colspan="6" style="text-align:center;"><h4>ไม่มีสินค้าในโซนนี้</h4></td></tr>
    <?php endif; ?>
    	</tbody>
    </table>
    <input type="hidden" name="id_zone" id="id_zone" value="<?php echo $_GET['id_zone']; ?>" />
    </div>
    </form>
</div>

    <div class="modal fade" id="add_modal" aria-hidden="true" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog"  style="width:600px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">เพิ่มรายการสินค้าในโซน</h4>
                </div>
                <div class="modal-body">
                    <div class="col-lg-4">
                    	<label>จำนวนที่นับได้</label>
                        <input type="text" id="qty_add" class="form-control input-sm" onkeydown="check_number($(this))" />
                    </div>
                    <div class="col-lg-6">
                    	<label>บาร์โค้ดสินค้า</label>
                        <input type="text" id="barcode_item" class="form-control input-sm" />
                    </div>
                    <div class="col-lg-2">
                    	<label style="visibility:hidden">ok</label>
                        <button type="button" id="btn_add" class="btn btn-success btn-sm" onclick="add_product()"><i class="fa fa-plus"></i>&nbsp; เพิ่ม</button>
                    </div>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>

</div>
<?php else : ?>
<div class="row">
	<div class="col-lg-3 col-lg-offset-4">
    	<input type="text" class="form-control input-sm" id="txt_zone" placeholder="ระบุ ชื่อโซน หรือ ยิงบาร์โค้ดโซน" style="text-align:center;" autofocus />
    </div>
    <div class="col-lg-1">
    	<button type="button" class="btn btn-primary btn-sm" id="btn_ok" onclick="get_zone()"><i class="fa fa-check-square-o"></i>&nbsp; ตรวจนับ</button>
    </div>
</div>
<?php endif; ?>
</div>
<script id="template" type="text/x-handlebars-template">
	<tr>
		<td>{{ product }}</td>
        <td align="center">{{ qty }} <input type="hidden" name="qty_{{ id }}" id="qty[{{ id }}" value="{{ qty }}" /></td>
        <td align="center"><input type="text" class="form-control input-sm" style="text-align:center;" id="qty_check_{{ id }}" name="qty_check[{{ id }}]" onkeyup="check_number($(this))" value="{{ sum }}" /></td>
        <td align="center"> <i class="fa fa-check" id="checked_{{ id }}" style="color:green;"></i></td>
        <td align="center"><span id="diff_{{ id }}">{{ diff }}</span></td>
         <td align="center"><button type="button" class="btn btn-info btn-sm btn-block" onclick="save_checked({{ id }}, {{ id_zone }})"><i class="fa fa-save"></i>&nbsp; บันทึก</button></td>
	</tr>
</script>
<script>
$(document).ready(function(e) {
    if($("#saved").length > 0 )
	{
		swal({ title: "เรียบร้อย", text : "บันทึกยอดต่างเรียบร้อยแล้ว", timer: 1000, type: "success"}, function(){ $("#txt_zone").focus();});
	}
});





function add_product()
{
	var qty = $("#qty_add").val();
	var barcode = $("#barcode_item").val();
	var id_zone = $("#id_zone").val();
	if(qty != "" && barcode != "")
	{
		$("#add_modal").modal("hide");
		load_in();
		$.ajax({
			url:"controller/productcheckController.php?add",
			type:"POST", cache:"false", data:{ "qty" : qty, "barcode" : barcode, "id_zone" : id_zone },
			success: function(rs)
			{
				var rs = $.trim(rs);
				if(rs == "duplicated")
				{
					load_out();
					swal("มีสินค้านี้อยู่ในโซนแล้ว");
				}else if( rs == "noproduct"){
					load_out();
					swal("ไม่มีสินค้านี้ในระบบ", "ไม่พบข้อมูลของบาร์โค้ดที่ระบุ กรุณาตรวจสอบบาร์โค้ด หรือ ตรวจสอบภาษาให้แน่ใจว่าเป็นภาษาอังกฤษ", "error");
				}else if( rs == "fail"){
					load_out();
					swal("เพิ่มรายการสินค้าไม่สำเร็จ");
				}else{
					var data		= $.parseJSON(rs);
					var source 	= $("#template").html();
					var row 		= Handlebars.compile(source);
					var html 		= row(data);
					$("#item").prepend(html);
					$("#qty_add").val("");
					$("#barcode_item").val("");
					load_out();
				}
			}
		});
	}
}


function get_zone()
{
	var zone = $("#txt_zone").val();
	if(zone != "" )
	{
		load_in();
		$.ajax({
			url:"controller/productcheckController.php?get_zone",
			type: "POST", cache: "false", data: { "zone" : zone },
			success: function(rs)
			{
				var rs = $.trim(rs);
				var id = parseInt(rs);
				if(!isNaN(id))
				{
					window.location.href = "index.php?content=ProductCheck&id_zone="+id;
					load_out();
				}
				else
				{
					load_out();
					swal("ไม่พบโซนที่ระบุ กรุณาตรวจสอบ");
				}
			}
		});
	}
}

function save_checked(id, id_zone)
{
	var qty = $("#qty_check_"+id).val();
	var c_qty = $("#qty_"+id).val();
	if(qty == '')
	{
		swal("ยอดตรวจนับต้องไม่ใช่ค่าว่าง");
		return false;
	}else{
		load_in();
		$.ajax({
			url:"controller/productcheckController.php?save_checked",
			type: "POST", cache: "false", data: { "id_product_attribute" : id, "qty" : c_qty, "qty_check" : qty, "id_zone" : id_zone },
			success: function(rs)
			{
				var rs = $.trim(rs);
				if(!isNaN(rs))
				{
					$("#diff_"+id).html(rs);
					$("#checked_"+id).css("display", "block");
					load_out();
					swal({ title: "เรียบร้อย", text : "บันทึกยอดต่างเรียบร้อยแล้ว", timer: 1000, type : "success" });
					$("#txt_zone").focus();
				}else{
					load_out();
					swal("บันทึกไม่สำเร็จ");
				}
			}
		});
	}
}

function add()
{
	$("#add_modal").modal("show");
}

function save_edit()
{
	$("#stock_form").submit();
}
function check_number(el)
{
	var qty = el.val();
	if( qty !='' && isNaN(parseInt(qty)) )
	{
		swal("ระบุเป็นตัวเลขเท่านั้น");
		el.val('');
	}
}
$("#qty_add").keyup(function(e) {
    if(e.keyCode == 13)
	{
		if($(this).val() != "")
		{
			$("#barcode_item").focus();
		}
	}
});

$("#barcode_item").keyup(function(e) {
    if(e.keyCode == 13)
	{
		if($(this).val() != "" && $("#qty_add") != "")
		{
			$("#btn_add").click();
		}
	}
});
$("#txt_zone").keyup(function(e) {
    if(e.keyCode == 13)
	{
		if($(this).val() != "")
		{
			$("#btn_ok").click();
		}
	}
});

function setFocus(el, time){
		setTimeout(function(){ if( ! $('.form-control').is(':focus') ){ el.focus();}},time);
}

$("#txt_zone").focusout(function(){
	setFocus($("#txt_zone"),1000);
});


</script>
