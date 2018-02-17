<?php 
	$page_name = "รายงานสรุปยอดขาย แยกตามหมวดหมู่สินค้า เปรียบเทียบรายเดือน";
	?>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-8"><h4 class="title"><i class="fa fa-bar-chart"></i>&nbsp; <?php echo $page_name; ?></h4></div>
    <div class="col-sm-4">
    	<p class="pull-right" style="margin-bottom:0px;">
        	<button type="button" class="btn btn-success btn-sm" id="btn_report" onClick="report()"><i class="fa fa-list"></i>&nbsp; รายงาน</button>
            <button type="button" class="btn btn-info btn-sm" id="btn_export" onClick="export_report()"><i class="fa fa-file-excel-o"></i>&nbsp; Export to Excel</button>
         </p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<!-- End page place holder -->
<div class='row'>
	<div class="col-lg-3">
    	<label style="display:block;">หมวดหมู่สินค้า</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm" style="width:50%" id="product_1" onClick="select_product($(this), 2)" value="1">เลือกทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%" id="product_2" onClick="select_product($(this), 1)" value="2">เลือกบางรายการ</button>
		</div>
	</div>
 	<div class="col-lg-2">
		<label>เลือกปี</label>
        	<select name="year" id="year" class="form-control input-sm">
                	<?php echo select_year(); ?>
            </select>
		<input type="hidden" name="product" id="product" value="1" />
	</div>
</div>    
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
</div><!------- container ------>     

<!--------------------------  Modal Select list --------------------->
<div class='modal fade' id='select_list' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' style='width:600px;' >
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                <h4 class='modal-title' id='myModalLabel'></h4>
            </div>
            <div class='modal-body'>
            <?php $ql = dbQuery("SELECT * FROM tbl_category WHERE id_category != 0 "); ?>
            <?php if( dbNumRows($ql) > 0 ) : ?>
            <form id="selected_form" method="post">
            <?php	while($rs = dbFetchArray($ql) ) : ?>
            	<div class="col-sm-6"><label><input class="chk" type="checkbox" name="p_selected[<?php echo $rs['id_category']; ?>]" value="<?php echo $rs['id_category']; ?>"  />&nbsp;&nbsp; <?php echo $rs['category_name']; ?></label></div>
            <?php	endwhile; ?>
            </form>
            <?php else : ?>
            	<div class="col-sm-12"><center><h4>-----  ไม่มีหมวดหมู่  -----</h4></center></div>
            <?php endif; ?>
            </div>
            <div class='modal-footer'>
            	<button type='button' class='btn btn-default' data-dismiss='modal'>ตกลง</button>
            </div>
        </div>
    </div>
</div>  

<div style="width:95%; margin-left:auto; margin-right:auto;">
<div class="row">
	<div class="col-lg-12" id="rs">
    	
    </div>
</div>
</div>
<script id="template" type="text/x-handlebars-template">
<table class="table table-bordered table-striped">
        	<tr><th colspan="28" style="text-align:center;">========== รายงานยอดขายแยกตามสินค้าเรียงตามหมวดหมู่สินค้าเปรียบเทียบแต่ละเดือน =============</th></tr>
        	<tr style="font-size:12px;">
            	<th rowspan="2" style="width:3%; text-align:center; vertical-align:middle;">ลำดับ</th>
                <th rowspan="2" style="width:10%; text-align:center; vertical-align:middle;">รหัส</th>
                <th colspan="2" style="width:5%; text-align:center;">Jan</th>
                <th colspan="2" style="width:5%; text-align:center;">Feb</th>
                <th colspan="2" style="width:5%; text-align:center;">Mar</th>
                <th colspan="2" style="width:5%; text-align:center;">Apr</th>
                <th colspan="2" style="width:5%; text-align:center;">May</th>
                <th colspan="2" style="width:5%; text-align:center;">Jun</th>
                <th colspan="2" style="width:5%; text-align:center;">Jul</th>
                <th colspan="2" style="width:5%; text-align:center;">Aug</th>
                <th colspan="2" style="width:5%; text-align:center;">Sep</th>
                <th colspan="2" style="width:5%; text-align:center;">Oct</th>
                <th colspan="2" style="width:5%; text-align:center;">Nov</th>
                <th colspan="2" style="width:5%; text-align:center;">Dec</th>
                <th colspan="2" style="width:5%; text-align:center;">Total</th>
            </tr>
            <tr style="font-size:10px;">
            	<td align="center" style="color:blue;">Qty.</td><td align="center" style="color:#060;">Amount.</td>
                <td align="center" style="color:blue;">Qty.</td><td align="center" style="color:#060;">Amount.</td>
                <td align="center" style="color:blue;">Qty.</td><td align="center" style="color:#060;">Amount.</td>
                <td align="center" style="color:blue;">Qty.</td><td align="center" style="color:#060;">Amount.</td>
                <td align="center" style="color:blue;">Qty.</td><td align="center" style="color:#060;">Amount.</td>
                <td align="center" style="color:blue;">Qty.</td><td align="center" style="color:#060;">Amount.</td>
                <td align="center" style="color:blue;">Qty.</td><td align="center" style="color:#060;">Amount.</td>
                <td align="center" style="color:blue;">Qty.</td><td align="center" style="color:#060;">Amount.</td>
                <td align="center" style="color:blue;">Qty.</td><td align="center" style="color:#060;">Amount.</td>
                <td align="center" style="color:blue;">Qty.</td><td align="center" style="color:#060;">Amount.</td>
                <td align="center" style="color:blue;">Qty.</td><td align="center" style="color:#060;">Amount.</td>
                <td align="center" style="color:blue;">Qty.</td><td align="center" style="color:#060;">Amount.</td>
                <td align="center" style="color:blue;">Qty.</td><td align="center" style="color:#060;">Amount.</td>
            </tr>
            {{#each this}}
            <tr style="font-size:10px;">
            	<td align="center">{{no}}</td><td>{{ product }}</td>
            	<td align="right" style="color:blue;">{{ qty_1 }}</td><td align="right" style="color:#060;">{{ amount_1 }}</td>
                <td align="right" style="color:blue;">{{ qty_2 }}</td><td align="right" style="color:#060;">{{ amount_2 }}</td>
                <td align="right" style="color:blue;">{{ qty_3 }}</td><td align="right" style="color:#060;">{{ amount_3 }}</td>
                <td align="right" style="color:blue;">{{ qty_4 }}</td><td align="right" style="color:#060;">{{ amount_4 }}</td>
                <td align="right" style="color:blue;">{{ qty_5 }}</td><td align="right" style="color:#060;">{{ amount_5 }}</td>
                <td align="right" style="color:blue;">{{ qty_6 }}</td><td align="right" style="color:#060;">{{ amount_6 }}</td>
                <td align="right" style="color:blue;">{{ qty_7 }}</td><td align="right" style="color:#060;">{{ amount_7 }}</td>
                <td align="right" style="color:blue;">{{ qty_8 }}</td><td align="right" style="color:#060;">{{ amount_8 }}</td>
                <td align="right" style="color:blue;">{{ qty_9 }}</td><td align="right" style="color:#060;">{{ amount_9 }}</td>
                <td align="right" style="color:blue;">{{ qty_10 }}</td><td align="right" style="color:#060;">{{ amount_10 }}</td>
                <td align="right" style="color:blue;">{{ qty_11 }}</td><td align="right" style="color:#060;">{{ amount_11 }}</td>
                <td align="right" style="color:blue;">{{ qty_12 }}</td><td align="right" style="color:#060;">{{ amount_12 }}</td>
                <td align="right" style="color:blue;">{{ total_qty }}</td><td align="right" style="color:#060;">{{ total_amount }}</td>
            </tr>
            {{/each}}
        </table>
</script>
<script>

function select_product(el, id)
{
	var i = el.val();
	$("#product").val(i);
	$("#product_"+id).removeClass("btn-primary");
	el.addClass("btn-primary");
	if(i == 2 )
	{
		$("#select_list").modal("show");
	}else if(i == 1 ){
		$("#select_list").modal("hide");
	}
}

function check_value()
{
	var i = 0;
	$(".chk").each(function(index, element) {
        if($(this).is(":checked"))
		{
			i++;
		}
    });
	return i;
}

function report()
{
	var product 	= $("#product").val();
	var year			= $("#year").val();
	if(product == 2 )
	{
		q = check_value();
		if(q == 0 )
		{
			swal("คุณต้องเลือกอย่างน้อย 1 หมวดหมู่");
			return false;
		}
	}
	load_in();
	$.ajax({
		url:"report/reportController/saleReportController.php?sale_summary_by_category&report&rank="+product+"&year="+year,
		type:"POST", cache: "false", data: $("#selected_form").serialize(),
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs !="fail" || rs != "")
			{
				var data 	= $.parseJSON(rs);
				var source	= $("#template").html();
				var output	= $("#rs");
				render(source, data, output);
				load_out();
			}else{
				load_out();
				swal("Error!!", "เกิดความผิดพลาดระห่วงส่งข้อมูล", "error");
				return false;	
			}
		}
	});	
}

function export_report()
{
	var product 	= $("#product").val();
	var year			= $("#year").val();
	if(product == 2 )
	{
		q = check_value();
		if(q == 0 )
		{
			swal("คุณต้องเลือกอย่างน้อย 1 หมวดหมู่");
			return false;
		}
	}
	var token		= new Date().getTime();
	var el				= [$("#btn_report"), $("#btn_export")];
	$("#selected_form").attr("action", "report/reportController/saleReportController.php?sale_summary_by_category&export&rank="+product+"&year="+year+"&token="+token);
	get_download(token, el);
	$("#selected_form").submit();
}
</script>