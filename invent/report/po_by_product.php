<?php 
$page_name = "รายงานใบสั่งซื้อ แยกตามสินค้า";  	
function get_item_po_backlog($id_po, $id_product_attribute)
{
	$backlog = 0;
	$qs = dbQuery("SELECT qty, received FROM tbl_po_detail WHERE id_po = ".$id_po." AND id_product_attribute = ".$id_product_attribute." AND tbl_po_detail.valid = 0");
	if( dbNumRows($qs) > 0 )
	{
		while($rs = dbFetchArray($qs) )
		{
			if($rs['qty'] > $rs['received'])
			{
				$backlog += $rs['qty'] - $rs['received'];
			}
		}
	}
	return $backlog;	
}
?>    


<?php 
if( isset( $_GET['get_item_po'] ) ) :
?>

	<div class="container">
    
    <?php 	$id 	= $_GET['id_product_attribute'];  ?>
     <?php    $product_code 	= get_product_reference($id); ?>
	<?php	$from	= fromDate(date("01-01-Y"));		?>
	<?php	$to 	= toDate(date("d-m-Y"));			?>
	<?php	$qs = dbQuery("SELECT tbl_po_detail.id_po, reference, id_supplier, tbl_po.date_add, SUM(qty) AS qty, SUM(received) AS received FROM tbl_po_detail JOIN tbl_po ON tbl_po_detail.id_po = tbl_po.id_po WHERE id_product_attribute = ".$id." AND (tbl_po.date_add BETWEEN '".$from."' AND '".$to."') GROUP BY tbl_po.id_po ORDER BY tbl_po.date_add DESC");		?>
    <?php if( dbNumRows($qs) > 0 ) { ?>
    <?php 	$total_qty 			= 0;  	?>
    <?php	$total_received		= 0;	?>
    <?php 	$total_backlog		= 0;	?>
   
    <div class="row">
    <div class="col-lg-12"><h4 class="title"><?php echo $product_code; ?></h4></div>
    </div>
    <hr/>
    <div class="row">
    <div class="col-lg-12">
    <table class="table table-striped">
        <thead style="font-size:12px;">
            <th style="width:20%;">ใบสั่งซื้อ</th>
            <th style="width:15%;">วันที่</th>
            <th>ผู้ขาย</th>
            <th style="width:10%; text-align:center;">สั่งซื้อ</th>
            <th style="width:10%; text-align:center;">รับแล้ว</th>
            <th style="width:10%; text-align:center;">ค้ารับ</th>
        </thead>
    <?php 	while($rs = dbFetchArray($qs) ){ ?>
    <?php		$backlog		= get_item_po_backlog($rs['id_po'], $id);		?>
   		<tr style="font-size:12px;">
            <td><?php echo $rs['reference']; ?></td>
            <td><?php echo thaiDate($rs['date_add']); ?></td>
            <td><?php echo supplier_name($rs['id_supplier']); ?></td>
            <td align="center"><?php echo number_format($rs['qty']); ?></td>
            <td align="center"><?php echo number_format($rs['received']); ?></td>
            <td align="center"><?php echo number_format($backlog); ?></td>
        </tr>
   <?php 	$total_qty += $rs['qty']; $total_received += $rs['received']; $total_backlog += $backlog; ?>
   <?php		} ?>
   		 <tr style="font-size:14px; font-weight:bold;">
            <td colspan="3" align="right">รวม</td>
            <td align="center"><?php echo number_format($total_qty); ?></td>
            <td align="center"><?php echo number_format($total_received); ?></td>
            <td align="center"><?php echo number_format($total_backlog); ?></td>
        </tr>
   </table>
     </div></div>
    <?php 
		}
		else
		{ 
	?>
    	<div class="col-lg-12"><center><h4>-----  ไม่พบรายการใดๆ  -----</h4></center></div>
	<?php
		 } 
		 ?>
    </div>	
<?php else : ?>    
<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-sm-8" style="margin-top:10px;"><h4 class="title"><i class="fa fa-bar-chart"></i>&nbsp; <?php echo $page_name; ?></h4></div>
    <div class="col-sm-4">
    	<p class="pull-right" style="margin-bottom:0px;">
        	<button type="button" class="btn btn-success btn-sm" onClick="report()"><i class="fa fa-list"></i>&nbsp; รายงาน</button>
         </p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<!-- End page place holder -->
<div class='row'>
	<div class="col-sm-3">
    	<label>สินค้า</label>
        <input type="text" class="form-control input-sm" id="product" name="product" placeholder="ค้นหารุ่นสินค้า" />
    </div>
	<div class="col-sm-2">
    	<label style="display:block;">ผู้ขาย</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm" style="width:50%;" id="all_sup" onClick="all_sup()" value="1">เลือกทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%;" id="select_sup" onClick="select_sup()" value="2">เลือกเฉพาะ</button>
		</div>
	</div>
    <div class="col-sm-3">
		<label>&nbsp;</label>
		<input type="text" class="form-control input-sm" name="supplier" id="supplier" style="text-align:center;" placeholder="ค้นหาชื่อผู้ขาย" disabled />
	</div> 
	<div class="col-sm-2">
		<label>จากวันที่</label>
		<input type="text" class="form-control input-sm" name="from_date" id="from_date" style="text-align:center;"  />
	</div> 
 	<div class="col-sm-2">
		<label>ถึงวันที่</label>
		<input type="text" class="form-control input-sm" name="to_date" id="to_date" style="text-align:center;" />
	</div>   
    <div class="col-sm-1">
    	<input type="hidden" name="id_sup" id="id_sup" />
        <input type="hidden" name="sup_rank" id="sup_rank" value="1"  />
        <input type="hidden" name="from" id="from" />
        <input type="hidden" name="to" id="to" />
    </div>
</div>    
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-sm-12" id="rs">
    	
    </div>
</div>
</div>

<!------- container ------>
<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg">
    	<div class="modal-content">
        	<div class="modal-header">
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class="modal-title" style="text-align:center;">reference</h4>
            </div>
            <div class="modal-body" id="po_detail">
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<script id="po_template" type="text/x-handlebars-template">
<table class="table table-striped">
<thead style="font-size:12px;">
    <th style="width:20%;">ใบสั่งซื้อ</th>
    <th style="width:15%;">วันที่</th>
    <th>ผู้ขาย</th>
    <th style="width:10%; text-align:center;">สั่งซื้อ</th>
    <th style="width:10%; text-align:center;">รับแล้ว</th>
    <th style="width:10%; text-align:center;">ค้ารับ</th>
</thead>
{{#each this}}
    {{#if @last}}
        <tr style="font-size:14px; font-weight:bold;">
            <td colspan="3" align="right">รวม</td>
            <td align="center">{{ total_qty }}</td>
            <td align="center">{{ total_received }}</td>
            <td align="center">{{ total_backlog }}</td>
        </tr>
    {{else}}
        <tr style="font-size:12px;">
            <td>{{ reference }}</td>
            <td>{{ date_add }}</td>
            <td>{{ sup_name }}</td>
            <td align="center">{{ qty }}</td>
            <td align="center">{{ received }}</td>
            <td align="center">{{ backlog }}</td>
        </tr>
    {{/if}}
{{/each}}
</table>
</script>
<script id="list_template" type="text/x-handlebars-template">
<table class="table table-striped">
    <thead>
    <th style="width:5%; text-align:center;">ลำดับ</th>
    <th style="width:25%; text-align:center;">สินค้า</th>
    <th style="width:15%; text-align:center;">สั่งรวม</th>
    <th style="width:15%; text-align:center;">รับแล้ว</th>
    <th style="width:15%; text-align:center;">ค้างรับ</th>
    <th style="text-align:center;">เลขที่เอกสาร</th>
    </thead>
	{{#each this}}
    <tr style="font-size:12px;">
    	<td align="center">{{ no }}</td>
        <td id="{{id_product_attribute}}">{{ product }}</td>
        <td align="center">{{ qty }}</td>
        <td align="center">{{ received }}</td>
        <td align="center">{{ backlog }}</td>
        <td align="right">
		{{#if content}}
		<button class="btn btn-xs btn-info" onclick="view_po({{ id_product_attribute }}, {{ id_sup }})"><i class="fa fa-eye"></i> &nbsp; ดูใบสั่งซื้อ</button>
		{{/if}}
		</td>
    </tr>
	{{/each}}
</table>  
</script>  

<script>
function view_po(id, id_sup)
{
	var from = $("#from_date").val();
	var to		= $("#to_date").val();
	var pd	= $("#"+id).text();
	if(from == "" || to == ""){ from = $("#from").val(); to = $("#to").val(); }
	if( !isDate(from) || !isDate(to) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	$(".modal-title").text(pd);
	$.ajax({
		url:"report/reportController/poReportController.php?get_po_list",
		type: "POST", cache: "false", data:{"id_product_attribute" : id, "id_sup" : id_sup, "from" : from, "to" : to },
		success: function(rs)
		{
			var rs 	= $.trim(rs);
			var source 	= $("#po_template").html();
			var data		= $.parseJSON(rs);
			var output	= $("#po_detail");
			render(source, data, output);
			$("#detail_modal").modal("show");
		}
	});
}

function all_sup()
{
	$("#all_sup").addClass("btn-primary");
	$("#select_sup").removeClass("btn-primary");
	$("#sup_rank").val(1);
	$("#supplier").attr("disabled", "disabled");
}
function select_sup()
{
	$("#all_sup").removeClass("btn-primary");
	$("#select_sup").addClass("btn-primary");
	$("#sup_rank").val(2);
	$("#supplier").removeAttr("disabled");
}

$("#supplier").autocomplete({
	minLength: 1,
	source: "controller/autoComplete.php?get_supplier", 
	autoFocus: true,
	close: function(event, ui)
	{
		var rs	= $("#supplier").val();
		var arr = rs.split(" | ");
		if( arr[0] != "ไม่พบข้อมูล" )
		{
			$("#supplier").val(arr[1]);
			$("#id_sup").val(arr[2]);
		}
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

$("#product").autocomplete({
	minLength: 2,
	source: "controller/autoComplete.php?product_code",
	autoFocus: true
});

function report()
{
	var product 	= $("#product").val();
	var sup_rank 	= $("#sup_rank").val();
	var id_sup		= $("#id_sup").val();
	var sup_name	= $("#supplier").val();
	var from			= $("#from_date").val();
	var to				= $("#to_date").val();
	if( product == ""){ swal("กรุณาระบุรุ่นสินค้า"); return false; }
	if( sup_rank == 2 && (sup_name == "" || id_sup == "")){ swal("กรุณาระบุชื่อผู้ขาย หรือ เลือกทั้งหมด"); return false; 	}
	if( !isDate(from) || !isDate(to) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	$("#from").val(from);
	$("#to").val(to);
	$.ajax({
		url:"report/reportController/poReportController.php?check_product_code",
		type:"POST", cache:"false", data:{ "product_code" : product },
		success: function(ps)
		{
			var ps = $.trim(ps);
			if(ps != "0" && ps != "")
			{
				load_in();
				$.ajax({
					url:"report/reportController/poReportController.php?po_by_product&report",
					type:"POST", cache:"false", data:{ "id_product" : ps, "sup_rank" : sup_rank, "id_sup" : id_sup, "from" : from, "to" : to },
					success: function(rs)
					{
						load_out();
						var rs = $.trim(rs);
						var source = $("#list_template").html();
						var data 		= $.parseJSON(rs);
						var output 	= $("#rs");
						render(source, data, output);
					}
				});
			}
			else
			{
				swal("รหัสสินค้าไม่ถูกต้อง", "รหัสสินค้าที่ระบุไม่ถูกต้อง คุณต้องระบุรหัสรุ่นสินค้าเท่านั้น", "error");	
			}
		}
	});
}
</script>
