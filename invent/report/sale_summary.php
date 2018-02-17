<?php 
	$page_name = "รายงานสรุปยอดขาย แยกตามรุ่นสินค้า เปรียบเทียบรายเดือน";
	?>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-8"><h4 class="title"><i class="fa fa-bar-chart"></i>&nbsp; <?php echo $page_name; ?></h4></div>
    <div class="col-sm-4">
    	<p class="pull-right" style="margin-bottom:0px;">
        	<button type="button" class="btn btn-success btn-sm" onClick="report()"><i class="fa fa-list"></i>&nbsp; รายงาน</button>
            <button type="button" class="btn btn-info btn-sm" onClick="export_report()"><i class="fa fa-file-excel-o"></i>&nbsp; Export to Excel</button>
         </p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<!-- End page place holder -->
<div class='row'>
	<div class="col-lg-3">
    	<label style="display:block;">สินค้า</label>
        <div class="btn-group" style="width:100%;">
        	<button type="button" class="btn btn-primary btn-sm" style="width:50%;" id="product_1" onClick="select_product($(this), 2)" value="1">เลือกทั้งหมด</button>
            <button type="button" class="btn btn-sm" style="width:50%;" id="product_2" onClick="select_product($(this), 1)" value="2">เลือกเป็นช่วง</button>
		</div>
	</div>
	<div class="col-lg-3">
		<label>เลือกจาก</label>
		<input type="text" class="form-control input-sm" name="p_from" id="p_from" disabled />
	</div> 
 	<div class="col-lg-3">
		<label>ถึง</label>
		<input type="text" class="form-control input-sm" name="p_to" id="p_to" disabled />
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
  

<div style="width:95%; margin-left:auto; margin-right:auto;">
<div class="row">
	<div class="col-lg-12" id="rs">
    	
    </div>
</div>
</div>
<script id="template" type="text/x-handlebars-template">
<table class="table table-bordered table-striped">
        	<tr><th colspan="28" style="text-align:center;">========== รายงานยอดขายแยกตามสินค้าเปรียบเทียบแต่ละเดือน =============</th></tr>
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
<?php $qs = dbQuery("SELECT product_code FROM tbl_product ORDER BY product_code ASC"); ?>
var data = [
<?php while($rs = dbFetchArray($qs) ) : ?>
<?php		echo "'".$rs['product_code']."',"; ?>
<?php endwhile; ?>
];

function select_product(el, id)
{
	var i = el.val();
	$("#product").val(i);
	$("#product_"+id).removeClass("btn-primary");
	el.addClass("btn-primary");
	if(i == 2 )
	{
		$("#p_from").removeAttr("disabled");
		$("#p_to").removeAttr("disabled");
	}else if(i == 1 ){
		$("#p_from").attr("disabled", "disabled");
		$("#p_to").attr("disabled", "disabled");
	}
}


$(document).ready(function(e) {
   $("#p_from").autocomplete({
	   minLength: 2,
		source: data,
		autoFocus: true,
		close: function(event,ui){
			var from = $(this).val();
			var to		= $("#p_to").val();
			if(to != "")
			{
				if(to < from)
				{ 
					$("#p_from").val(to);
					$("#p_to").val(from);
				}
			}
		}
	});		
	
	$("#p_to").autocomplete({
		minLength: 2,
		source: data,
		autoFocus: true,
		close: function(event,ui){
			var to 	= $(this).val();
			var from	=	$("#p_from").val();
			if(from !="")
			{
				if(to < from)
				{
					$("#p_to").val(from);
					$("#p_from").val(to);	
				}
			}
		}
	});			
});
function report()
{
	var product 	= $("#product").val();
	var from			= $("#p_from").val();
	var to				= $("#p_to").val();
	var year			= $("#year").val();
	if(product == 2)
	{
		if( from == "" || to == "")
		{ 
			swal("เลือกช่วงสินค้า");
			return false;
		}
	}
	load_in();
	$.ajax({
		url:"report/reportController/saleReportController.php?sale_summary&report",
		type:"POST", cache: "false", data:{ "rank" : product, "from" : from, "to" : to, "year" : year },
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
	var from			= $("#p_from").val();
	var to				= $("#p_to").val();
	var year			= $("#year").val();
	if(product == 2)
	{
		if( from == "" || to == "")
		{ 
			swal("เลือกช่วงสินค้า");
			return false;
		}
	}
	var token = new Date().getTime();
	get_download(token);
	window.location.href="report/reportController/saleReportController.php?sale_summary&export&rank="+product+"&from="+from+"&to="+to+"&year="+year+"&token="+token;
}
</script>