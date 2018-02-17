<?php 
	$page_name = "รายงานสรุปยอดความเคลื่อนไหวสินค้า เปรียบเทียบยอด เข้า - ออก";
	$id_profile = $_COOKIE['profile_id'];

	?>
<div class="container">
<!-- page place holder -->
<div class="row" style="height:35px;">
	<div class="col-lg-8 col-sm-12 col-xs-12" style="margin-top:10px;">
    	<h4 class="title"><i class="fa fa-bar-chart"></i>  <?php echo $page_name; ?></h4>
    </div>
    <div class="col-lg-4 col-sm-12 col-xs-12">
       <p class="pull-right" style="margin-bottom:0px;">
       		<button type="button" class="btn btn-success btn-sm" onclick="get_report()"><i class="fa fa-list"></i>  รายงาน</button>
            <button type="button" class="btn btn-info btn-sm" onclick="do_export()"><i class="fa fa-file-excel-o"></i>  ส่งออก</button>
		</p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:10px;' />
<!-- End page place holder -->
<div class="row">
	<div class="col-lg-3 col-lg-offset-3">
    	<div class="input-group">
        	<span class="input-group-addon">คลังสินค้า</span>
            <select class="form-control input-sm" id="wh" name="wh">
            	<option value="0">ทุกคลัง</option>
                <?php warehouseList(); ?>
            </select>
        </div>
    </div>

<?php $last_month = getLastMonth();  ?>
<div class="col-lg-2">
	<div class="input-group">
    	<span class="input-group-addon">ตั้งแต่</span>
        <input type="text" name="from_date" id="from_date" class="form-control input-sm" value="<?php echo thaiDate($last_month['start']); ?>" />
    </div>
</div>
<div class="col-lg-2">
	<div class="input-group">
    	<span class="input-group-addon">ถึง</span>
        <input type="text" name="to_date" id="to_date" class="form-control input-sm" value="<?php echo thaiDate($last_month['end']); ?>"  />
    </div>
</div>
</div><!-- End row -->

<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-12" id="result">
  
    </div>
</div><!-- End row -->
</div><!-- Container -->
<script id="template" type="text/x-handlebars-template">
<table class="table table-striped">
{{#each this}}
	{{#if @first}}
    	<tr style="font-size:12px;"><th colspan="7" align="center">ความเคลื่อนไหวสินค้า วันที่  {{ from }}  ถึง  {{ to }}   {{ wh }}  :  <?php echo COMPANY; ?></tr>
    {{else}}
    	{{#if header}}
        	<tr style="font-size:12px;">
            	<th style="width: 10%;">วันที่</th>
                <th style="width: 15%; text-align:right;">สินค้าเข้า(จำนวน)</th>
                <th style="width: 15%; text-align:right;">มูลค่าเข้า(ทุน)</th>
                <th style="width: 15%; text-align:right;">สินค้าออก(จำนวน)</th>
                <th style="width: 15%; text-align:right;">มูลค่าออก(ทุน)</th>
                <th style="width: 15%; text-align:right;">คงเหลือ</th>
                <th style="width: 15%; text-align:right;">มูลค่าคงเหลือ(ทุน)</th>
            </tr>
        {{else}}
                <tr style="font-size:12px;">
                    <td>{{ date }}</td>
                    <td align="right" style="color:red;">{{ move_in }}</td>
                    <td align="right" style="color:red;">{{ in_amount }}</td>
                    <td align="right" style="color:blue;">{{ move_out }}</td>
                    <td align="right" style="color:blue;">{{ out_amount }}</td>
                    <td align="right" style="color:green;">{{ balance }}</td>
                    <td align="right" style="color:green;">{{ amount }}</td>
                </tr>
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

function get_report()
{
	var wh 	= $("#wh").val();
	var from	= $("#from_date").val();
	var to		= $("#to_date").val();
	if( !isDate(from) || !isDate(to) )
	{
		swal("วันที่ไม่ถูกต้อง");
		return false;
	}
	load_in();
	$.ajax({
		url:"controller/reportController.php?movement_summary&report",
		type: "POST", cache: "false", data: { "id_warehouse" : wh, "from_date" : from, "to_date" : to },
		success: function(rs)
		{
			load_out();
			var source	= $("#template").html();
			var data 		= $.parseJSON(rs);
			var output	= $("#result");
			render(source, data, output);
		}
	});
}

/********************************  Export to Excel  ****************************/
function do_export()
{
	var wh 	= $("#wh").val();
	var from	= $("#from_date").val();
	var to 	= $("#to_date").val();
	if( !isDate(from) || !isDate(to) ){ swal("วันที่ไม่ถูกต้อง"); return false; }
	var token	= new Date().getTime();
	get_download(token);
	window.location.href = "controller/reportController.php?movement_summary&export&id_warehouse="+wh+"&from_date="+from+"&to_date="+to+"&token="+token;
}

$("#gogo").click(function(e) {
	var warehouse = $("input[name='warehouse']:checked").val();
	var rank = $("input[name='date']:checked").val();
	var from = $("#from_date").val();
	var to = $("#to_date").val();
	if( rank == 1 )
	{
		if( !isDate(from) || !isDate(to))
		{
			swal("วันที่ไม่ชัดเจน", "กรุณาระบุวันที่ให้ครบถ้วน","error");
			return false;
		}
	}/// end if;
	$("#report_form").attr("action", "controller/reportController.php?export_movement_summary");
	$("#report_form").submit();
});
</script>