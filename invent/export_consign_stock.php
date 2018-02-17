<?php 
	$page_name = "ส่งออกไฟล์ตรวจนับตั้งต้น";
	include("function/report_helper.php");
	?>
<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-sm-8" style="margin-top:10px;"><h4 class="title"><i class="fa fa-upload"></i>&nbsp; <?php echo $page_name; ?></h4></div> 
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-4 col-lg-offset-4"><input type="text" class="form-control input-sm" name="zone" id="zone" placeholder="เลือกโซนเพื่อส่งออก" /></div>
    <div class="col-lg-1"><button type="button" class="btn btn-primary btn-sm" onclick="get_search()"><i class="fa fa-search" ></i> ดูข้อมูล</button></div>
    <div class="col-lg-1"><button type="button" class="btn btn-info btn-sm" onclick="export_zone()"><i class="fa fa-file-excel-o"></i> ส่งออก</button></div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-12" id="rs"></div>
</div>
<input type="hidden" id="id_zone" name="id_zone" />
<script id="template" type="text/x-handlebars-template">
<table class="table table-bordered table-striped">
{{#each this}}
{{#if @first}}
        	<tr><th colspan="7" style="text-align:center;">========== สินค้าคงเหลือ โซน {{ zone }} =============</th></tr>
        	<tr style="font-size:12px;">
            	<th style="width:5%; text-align:center; vertical-align:middle;">ลำดับ</th>
				<th style="width: 10%; vertical-align:middle;">บาร์โค้ด</th>
                <th style="width: 20%; vertical-align:middle;">รหัส</th>
				<th style="width: 10%; text-align:right; vertical-align:middle;">ทุน</th>
				<th style="width: 10%; text-align:right; vertical-align:middle;">คงเหลือ</th>
				<th style="width: 20%; text-align:right; vertical-align:middle;">มูลค่า</th>
            </tr>
{{else}}			
	{{#if nodata}}
		<tr>
			<td colspan="7" align="center"><h4>-----  ไม่พบสินค้าคงเหลือในโซนนี้  -----</h4></td>				
		</tr>
	{{else}}
		{{#if @last}}
			<tr style="font-size:10px;">
				<td align="right" colspan="4"><strong>รวม</strong></td>
				<td align="right">{{ total_qty }}</td>
				<td align="right" >{{ total_amount }}</td>
			</tr>
		{{else}}
			<tr style="font-size:10px;">
				<td align="center">{{no}}</td>
				<td>{{ barcode }}</td>
				<td>{{ reference }}</td>
				<td align="right">{{ cost }}</td>
				<td align="right">{{ qty }}</td>
				<td align="right" >{{ amount }}</td>
			</tr>
		{{/if}}
	{{/if}}
{{/if}}
{{/each}}
			
        </table>
</script>
</div><!------- container ------>  

<script>
function get_search()
{
	var id_zone = $("#id_zone").val();
	var zone 	= $("#zone").val();
	if(zone == ""){ swal("กรุณาเลือกโซน"); return false; }
	if(id_zone !="" && zone != "")
	{
		load_in();
		$.ajax({
			url:"report/reportController/stockReportController.php?stock_in_zone&report",
			type:"POST", cache:"false", data:{ "id_zone" : id_zone },
			success: function(rs)
			{
				load_out();
				var rs = $.trim(rs);
				if(rs == "fail" || rs == "")
				{
					swal("ไม่พบโซนที่เลือก");	
				}
				else
				{
					var source	= $("#template").html();
					var data		= $.parseJSON(rs);
					var output	= $("#rs");
					render(source, data, output);	
				}
			}
		});
	}
}

function export_zone()
{
	var id_zone = $("#id_zone").val();
	var zone 	= $("#zone").val();
	if(zone == ""){ swal("กรุณาเลือกโซน"); return false; }
	var token		= new Date().getTime();
	get_download(token);
	window.location.href = "controller/exportController.php?stock_in_zone&export&id_zone="+id_zone+"&token="+token;
}

$("#zone").autocomplete({
	source: "controller/autoComplete.php?get_zone_name",
	autoFocus: true, close: function(){
		var rs = $(this).val();
		var arr = rs.split(" : ");
		$("#id_zone").val(arr[0]);
		$(this).val(arr[1]);
		}});
</script>   
