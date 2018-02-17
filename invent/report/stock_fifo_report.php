<?php 
	$page_name = "รายงานความเคลื่อนไหวสินค้า";
	$id_profile = $_COOKIE['profile_id'];
	?>
   
<div class="container">
<!-- page place holder -->
<div class="row" style="height:35px;">
	<div class="col-lg-6 col-sm-12 col-xs-12" style="margin-top:10px;">
    	<h4 class="title"><i class="fa fa-bar-chart"></i>  <?php echo $page_name; ?></h4>
    </div>
    <div class="col-lg-6 col-sm-12 col-xs-12">
       <p class="pull-right" style="margin-bottom:0px;">
       		<button type="button" class="btn btn-success btn-sm" onclick="get_report()"><i class="fa fa-list"></i>  รายงาน</button>
            <button type="button" class="btn btn-info btn-sm" onclick="do_export()"><i class="fa fa-file-excel-o"></i>  ส่งออก</button>
		</p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:10px;' />
<!-- End page place holder -->
<div class='row'>
    <div class="col-lg-2">
    	<label>ช่วงสินค้าตั้งแต่</label>
        <input type="text" id="p_from" name="p_from" class="form-control input-sm" style="text-align:center;" placeholder="-----  จาก  -----"  />
    </div>
    <div class="col-lg-2">
    	<label >ถึง</label>
        <input type="text" id="p_to" name="p_to" class="form-control input-sm" style="text-align:center;" placeholder="-----  ถึง  -----"  />
    </div>
    <div class='col-lg-2'>
    	<label>คลัง</label>
        <select name='wh' id='wh' class='form-control input-sm' >
        	<option value="0">ทุกคลัง</option>
			<?php warehouseList(); ?>
        </select>
    </div>
    <div class='col-lg-2'>
     	<label>วันที่ตั้งแต่</label>
        <input type="text" class="form-control input-sm" id="from_date" name="from_date" style="text-align:center;" placeholder="---  เริ่มต้น  ---" />
    </div>
    <div class='col-lg-2'>
     	<label >ถึง</label>
        <input type="text" class="form-control input-sm" id="to_date" name="to_date" style="text-align:center;" placeholder="---  สิ้นสุด  ---" />
    </div>
</div>

<hr style='border-color:#CCC; margin-top: 15px; margin-bottom:15px;' />
<div class='row'>
	<div class='col-lg-12' id='result'></div>
    </div>
</div>     

<script id="template" type="text/x-handlebars-template">
<table class="table table-striped">
{{#each this}}
	{{#if nocontent}}
    <tr><td align="center"><h4>-----  ไม่มีรายการตามเงื่อนไขที่เลือก  -----</h4></td></tr>
    {{else}}
    	{{#if @first}}
        	<tr>
            <td colspan="6" align="center"><strong>{{ title }}</strong></td>
            </tr>
			<tr>
            <td colspan="6" align="center"><strong>{{ p_range }}</strong></td>
            </tr>
        {{else}}
        	{{#if sub_header}}
            	<tr>
					<td colspan="5">{{ sub_header }}</td>
					<td align="right"><button type="button" class="btn btn-info btn-xs btn-block" onclick="print_movement('{{ btn }}')"><i class="fa fa-print"></i> พิมพ์</button></td>
				</tr>
                <tr>
                	<td style="width:15%;">วันที่</td>
                    <td style="width:20%;">เลขที่เอกสาร</td>
                    <td style="width:20%;">คลัง</td>
                    <td style="width:15%; text-align:right;">เข้า</td>
                    <td style="width:15%; text-align:right;">ออก</td>
                    <td style="width:15%; text-align:right;">คงเหลือ</td>
                </tr>
            {{else}}
				{{#if nomovement}}
				<tr>
                	<td colspan="6" align="center"><strong> ----- ไม่มีรายการเคลื่อนไหว  -----</strong></td>
                </tr>
				{{else}}
					{{#if blank_line}}
						<tr>
							<td colspan="6" align="center" style="height:50px;"></td>
						</tr>
					{{else}}
						<tr>
							<td>{{ date }}</td>
							<td>{{ reference }}</td>
							<td>{{ wh }}</td>
							<td align="right">{{ in }}</td>
							<td align="right">{{ out }}</td>
							<td align="right">{{ balance }}</td>
						</tr>
					{{/if}}
				{{/if}}
            {{/if}}        
        {{/if}}    
    {{/if}}
{{/each}}
</table>
</script>
<script>

$("#p_from").autocomplete({
	source:"controller/orderController.php?product_attribute",
	autoFocus: true,
	close: function(event,ui){
		var data 		= $(this).val();
		var arr 		= data.split(':');
		var name 	= arr[0];
		$(this).val(name);
		switch_field();
	}
});			

$("#p_to").autocomplete({
	source:"controller/orderController.php?product_attribute",
	autoFocus: true,
	close: function(event,ui){
		var data = $(this).val();
		var arr = data.split(':');
		var name = arr[0];
		$(this).val(name);
		switch_field();
	}
});	

function switch_field()
{
	var from = $("#p_from").val();
	var to		= $("#p_to").val();
	if(from != "" && to != "")
	{
		if(to < from)
		{
			$("#p_from").val(to);
			$("#p_to").val(from);	
		}
	}
}


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


function get_report(){
	var p_from		= $("#p_from").val();
	var p_to			= $("#p_to").val();
	var id_wh		= $("#wh").val();
	var from_date	= $("#from_date").val();
	var to_date		= $("#to_date").val();
	if(!isDate(from_date) || !isDate(to_date)){ swal("วันที่ไม่ถูกต้อง"); return false; }
		
	load_in();
	
	$.ajax({
		url:"report/reportController/stockReportController.php?fifo_report&report", 
		type:"POST",cache: "false", data:{ "p_from" : p_from, "p_to" : p_to,"id_wh" : id_wh, "from_date" : from_date, "to_date" : to_date },
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if(rs != "fail")
			{
				var source = $("#template").html();
				var data 		= $.parseJSON(rs);
				var output	= $("#result");
				render(source, data, output);	
			}
			else
			{
				swal("Error !! ", "ไม่พบข้อมูลที่ต้องการค้นหา", "error");	
			}
		}
	});
		
}

function do_export()
{
	var p_from		= $("#p_from").val();
	var p_to			= $("#p_to").val();
	var id_wh		= $("#wh").val();
	var from_date	= $("#from_date").val();
	var to_date		= $("#to_date").val();
	if(!isDate(from_date) || !isDate(to_date)){ swal("วันที่ไม่ถูกต้อง"); return false; }
	var token = new Date().getTime();
	get_download(token);
	window.location.href="report/reportController/stockReportController.php?fifo_report&export&p_from="+p_from+"&p_to="+p_to+"&id_wh="+id_wh+"&from_date="+from_date+"&to_date="+to_date+"&token="+token;
}

function print_movement(url)
{
	window.open(url, "_blank", "width=800, height=1000, scrollbars=yes");	
}
</script>
