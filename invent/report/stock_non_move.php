<?php
	$page_menu = "invent_stock_non_move";
	$page_name = "รายงานสินค้าไม่เคลื่อนไหว";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder -->
<form name='report_form' id='report_form' action='index.php?content=non_move&stock_report=y' method='post'>
<div class="row" style="height:30px;">
	<div class="col-sm-8" style="margin-top:10px;"><h4 class="title"><i class="fa fa-bar-chart"></i>&nbsp; <?php echo $page_name; ?></h4></div>
    <div class="col-sm-4">
    	<p class="pull-right" style="margin-bottom:0px;">
        	<button type="button" class="btn btn-success btn-sm" onClick="get_report()"><i class="fa fa-list"></i>&nbsp; รายงาน</button>
            <button type="button" class="btn btn-info btn-sm" onClick="export_report()"><i class="fa fa-file-excel-o"></i>&nbsp; ส่งออก</button>
         </p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<!-- End page place holder -->
<div class='row'>
   <div class='col-lg-2 col-lg-offset-4'>
   		<div class='input-group'>
        	<span class='input-group-addon'>จาก</span>
        	<input type='text' name='from_date' id='from_date' class='form-control input-sm' />
        </div>
    </div>
	<div class='col-lg-2'>
   		<div class='input-group'>
            <span class='input-group-addon'>ถึง</span>
            <input type='text' name='to_date' id='to_date' class='form-control input-sm' />
        </div>
    </div>
    </form>
</div>
<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
<div class='row'>
	<div class='col-lg-12' id='result'></div>
    </div>
</div>  
<script id="template" type="text/x-handlebars-template">
<table class="table table-striped">
{{#each this}}
	{{#if title}}
    	<tr><th colspan="8" style="text-align:center; font-size:16px;"><strong>รายงานสินค้าไม่เคลื่อนไหว ตั้งแต่วันที่ {{ from }} ถึง {{ to }}  : <?php echo COMPANY; ?></strong></th></tr>
    {{else}}
    	{{#if thead}}
        	<tr style="font-size:14px;">
            	<th style="width:5%; text-align:center;">ลำดับ</th>
                <th style="width: 15%;">บาร์โค้ด</th>
                <th style="width: 15%;">รหัสสินค้า</th>
                <th style="width: 20%;">ชื่อสินค้า</th>
                <th style="width: 10%; text-align:right;">ทุน</th>
                <th style="width: 10%; text-align:right;">คงเหลือ</th>
                <th style="width: 10%; text-align:right;">มูลค่า</th>
                <th style="width: 15%; text-align:center;">เคลื่อนไหวล่าสุด</th>
            </tr>
        {{else}}
			{{#if nocontent }}
			<tr><td colspan="8" align="center">----- ไม่พบรายการสินค้าไม่เคลื่อนไหว  -----</td></tr>
			{{else}}
        	<tr style="font-size:12px;">
            	<td align="center">{{ no }}</td>
                <td>{{ barcode }}</td>
                <td>{{ reference }}</td>
                <td>{{ product }}</td>
                <td align="right">{{ cost }}</td>
                <td align="right">{{ qty }}</td>
                <td align="right">{{ amount }}</td>
                <td align="center">{{ last_move }}</td>
            </tr>
			{{/if}}
        {{/if}}
    {{/if}}
{{/each}}    
</table>
</script>
   
<script>
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


function export_report()
{
	var from_date 		= $("#from_date").val();
	var to_date 			= $("#to_date").val();
	if( !isDate(from_date) || !isDate(to_date) )
	{
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}
	var token = new Date().getTime();
	get_download(token);
	window.location.href = "controller/reportController.php?stock_non_move&export&from_date="+from_date+"&to_date="+to_date+"&token="+token;
}

function get_report()
{
	var from_date 		= $("#from_date").val();
	var to_date 			= $("#to_date").val();
	if( !isDate(from_date) || !isDate(to_date) )
	{
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}
	load_in();
	$.ajax({
		url:"controller/reportController.php?stock_non_move&report",
		type:"POST", cache:"false", data: {"from_date" : from_date, "to_date" : to_date },
		success: function(rs){
			load_out();
			var source	= $("#template").html();
			var data 		= $.parseJSON(rs);
			var output	= $("#result");
			render(source, data, output);
		}
	});
		
}
</script>