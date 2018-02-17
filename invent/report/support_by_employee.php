<?php 
	$page_name = "รายงาน รายการเบิกอภินันท์(พนักงาน)";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder -->
<form name='report_form' id='report_form' action='' method='post'>
<div class="row">
	<div class="col-sm-8"><h4 class="title"><?php echo $page_name; ?></h4></div>
    <div class="col-sm-4">
    	<p class="pull-right">
        	<button type="button" class="btn btn-primary" id="report"><i class="fa fa-file-text"></i>&nbsp;รายงาน</button>
            <button type="button" class="btn btn-success" id="gogo"><i class="fa fa-file-excel-o"></i>&nbsp;ส่งออก</button>
        </p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<!-- End page place holder -->
<div class="row">
<!-- ++++++++++++++++++++++++++++  ผู้รับ +++++++++++++++++++++++++++++-->    
<div class="col-lg-4" style="padding-left:5px; padding-right:5px;">
        <fieldset style="border: 1px solid #DDD; margin:5px; padding-bottom:15px; height: 150px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:0px; border:0px;">ผู้เบิก</legend>
        	<div class="col-lg-12">&nbsp;</div>
        	<div class="col-lg-12" >
            	<div class="input-group input-group-sm">
                <span class="input-group-addon">
                <input type='radio' name='employee' id='employee1' value='0' checked="checked"/>&nbsp;
                </span>
                <label for="employee1" class="form-control" style="width:135px">ทั้งหมด</label>
                </div>
            </div>        
            <div class="col-lg-12">&nbsp;</div>   
            <div class="col-lg-12">
            		<div class="input-group input-group-sm">
                		<span class="input-group-addon">
                        <input type='radio' name='employee' id='employee2' value='1'  />
                        <label for="employee2" style="padding-left:10px; margin-bottom:0px;">เฉพาะ</label>
                        </span>
                        <input type="hidden" name="employee_selected" id="employee_selected"  />
                		<input type="text" name="txt_employee" id="txt_employee" class="form-control" disabled="disabled" />
                	</div>
             </div>
        </fieldset>
</div>
<!-- ++++++++++++++++++++++++++++   end ผู้ยืม  +++++++++++++++++++++++++++++--> 

<!-- ++++++++++++++++++++++++++++  Date rank+++++++++++++++++++++++++++++-->    
<div class="col-lg-4" style="padding-left:5px; padding-right:5px;">
        <fieldset style="border: 1px solid #DDD; margin:5px; padding-bottom:15px; height: 150px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:0px; border:0px;">ช่วงวันที่</legend>
        	<div class="col-lg-12">&nbsp;</div>
        	<div class="col-lg-12" >
            	<div class="input-group input-group-sm">
                <span class="input-group-addon">
                <input type='radio' name='date' id='date1' value='0' checked="checked"/>&nbsp;
                </span>
                <label for="date1" class="form-control" style="width:135px">ทั้งหมด</label>
                </div>
            </div>        
            <div class="col-lg-12">&nbsp;</div>   
            <div class="col-lg-6" style="padding-right:0px;">
            		<div class="input-group input-group-sm">
                		<span class="input-group-addon">
                        <input type='radio' name='date' id='date2' value='1'  />
                        <label for="date2" style="padding-left:10px; margin-bottom:0px;">จาก</label>
                        </span>
                		<input type="text" name="from_date" id="from_date" class="form-control" disabled="disabled" />
                	</div>
            </div>
            <div class="col-lg-6">
                    <div class="input-group input-group-sm">
                		<span class="input-group-addon">
                        <label for="date2" style="padding-left:10px; margin-bottom:0px;">ถึง</label>
                        </span>
                		<input type="text" name="to_date" id="to_date" class="form-control" disabled="disabled" />
                	</div>
             </div>
        </fieldset>
</div>
<!-- ++++++++++++++++++++++++++++   end Date rank  +++++++++++++++++++++++++++++--> 

<!-- ++++++++++++++++++++++++++++  view +++++++++++++++++++++++++++++-->    
<div class="col-lg-4" style="padding-left:5px; padding-right:5px;">
        <fieldset style="border: 1px solid #DDD; margin:5px; padding-bottom:15px; height: 150px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:0px; border:0px;">แสดง</legend>
        	<div class="col-lg-12">&nbsp;</div>
        	<div class="col-lg-12" >
                <input type='radio' name='view' id='view1' value='0' checked="checked" style="display:inline"/>&nbsp;
                <label for="view1" >แยกตามเลขที่เอกสาร</label> 
            </div>        
            <div class="col-lg-12">&nbsp;</div>   
            <div class="col-lg-12">
            	<input type='radio' name='view' id='view2' value='1'  />&nbsp;
                <label for="view2" >แยกตามรายการสินค้า</label>
            </div>
        </fieldset>
</div>

</div><!-- End row -->
</form>
<div class="row">
	<div class="col-lg-12" id="result">
 
    </div>
</div><!-- End row -->
<!------------------------------------------------- Modal  ----------------------------------------------------------->
<button data-toggle='modal' data-target='#myModal' id='btn_toggle' style='display:none;'>xxx</button>
<div class='modal fade' id='myModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' style='width:800px;'>
		<div class='modal-content'>
		  <div class='modal-header'>
			<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
			<h4 class='modal-title' id='myModalLabel'></h4>
		  </div>
		  <div class='modal-body'> 
          <table class="table table-striped table-hover">
    	<thead>
        	<th style="width:10%;"></th>
            <th style="width:10%;">รหัส</th>
            <th style="width:40%">สินค้า</th>
            <th style="width:10%; text-align:right">ราคา</th>
            <th style="width:10%; text-align:right">จำนวน</th>
            <th style="width:10%; text-align:right">มูลค่า</th>
        </thead>
        	<tbody id="rs">  
        
        	</tbody>
    	</table>       
          </div>
		  <div class='modal-footer'>
			<button type="button" data-dismiss='modal' class="btn btn-success btn-sm"><i class="fa fa-remove"></i>&nbsp; ปิด</button>
		  </div>
		</div>
	</div>
</div>
<!------------------------------------------------- END Modal  ----------------------------------------------------------->
</div><!-- Container -->
<script id="template" type="text/x-handlebars-template">
	
        {{#each this}}
        	{{#if nocontent}}
            	<tr><td colspan="6"><center><h4> {{ nocontent }}</h4></center></td></tr>
            {{else}}
            	{{#if @last}}
                	<tr><td colspan="4" align="right"><strong>รวม</strong></td><td align="right"><strong>{{ total_qty }}</strong></td><td align="right"><strong>{{ total_amount }}</strong></td></tr>
                {{else}}
                	<tr><td><img src="{{ img }}" width="40px;" /></td> <td>{{ reference }}</td><td>{{ product_name }}</td><td align="right">{{ price }}</td><td align="right">{{ qty }}</td><td align="right">{{ amount }}</td></tr>
                
                {{/if}}                      
            {{/if}}  
        {{/each}}
        
</script>
<script id="report1" type="text/x-handlebars-template">
	<table class="table table-striped table-hover">
	<thead>
       <th style='width: 10%;'>วันที่</th>
	   <th style='width:15%;'>ผู้เบิก(พนักงาน)</th>
       <th style='width:15%;'>ผู้รับ</th>
       <th style='width:10%'>เลขที่เอกสาร</th>
       <th style='width:10%; text-align:right;'>จำนวน</th>
       <th style='width:10%; text-align:right;'>มูลค่า</th>
       <th style='text-align:center;'>หมายเหตุ</th>
    </thead>
    <tbody >
    
    	{{#each this}}
        	{{#if nocontent}}
            	<tr><td colspan="10"><center><h4>{{ nocontent }}</h4></center></td>
            {{else}}
            	{{#if @last}}
                	<tr><td colspan="4"> รวม</td><td align="right">{{ total_qty }}</td><td align="right">{{ total_amount }}</td><td></td></tr>
                {{else}}
                	<tr>
						<td>{{ date_upd }}</td>
						<td>{{ employee }}</td>
						<td>{{ customer }}</td>
						<td> <a href="javascript:void(0)" onclick="get_detail({{ id_order }})">{{ reference }}</a></td>
						<td align="right">{{ qty }}</td>
						<td align="right">{{ amount }}</td>
						<td>{{ remark }}</td></tr>
            	{{/if}}
            {{/if}}       
        {{/each}}  
 </tbody>
    </table>
</script>
<script id="report2" type="text/x-handlebars-template">
<table class='table table-striped table-hover'>
<thead>
<th style='width: 10%;'>รูปภาพ</th>
<th style='width:15%;'>รหัส</th>
<th>สินค้า</th>
<th style='width:10%; text-align:right;'>จำนวน</th>
<th style='width:15%; text-align:right;'>มูลค่า</th>
</thead>
<tbody>
{{#each this}}
	{{#if nocontent}}
    	<tr><td colspan="5"><center><h4>{{ nocontent }}</h4></center></td></tr>
    {{else}}
    	{{#if @last}}
        	<tr><td colspan="3" align="right"><strong>รวม</strong></td><td align="right"><strong>{{ total_qty }}</strong></td><td align="right"><strong>{{ total_amount }}</strong></td></tr>
        {{else}}
        	<tr><td><img src="{{ img }}" width="50px" /></td><td>{{ reference }}</td><td>{{ product_name }}</td><td align="right">{{ qty }}</td><td align="right">{{ amount }}</td></tr>
        {{/if}}
    {{/if}}
{{/each}}
</tbody>
</table>
</script>
<script>

/***************************  Date Select *************************/
$("#date1").change(function(e) {
    $("#from_date").attr("disabled", "disabled");
	$("#to_date").attr("disabled","disabled");
});
$("#date2").change(function(e) {
    $("#from_date").removeAttr("disabled");
	$("#to_date").removeAttr("disabled");
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

/**************************  End Date select  *****************/


/************************  employee  **********************/
$("#employee1").change(function(e){
	$("#txt_employee").attr("disabled","disabled");
});

$("#employee2").change(function(e) {
    $("#txt_employee").removeAttr("disabled");
});

$("#txt_employee").autocomplete({
	source : "controller/autoComplete.php?get_support_id",
	autoFocus: true,
	close: function(event,ui){
		var data = $(this).val();
		var arr = data.split(" : ");
		var name = arr[1];
		var id = arr[0];
		$("#employee_selected").val(id);
		$(this).val(name);
	}
});
/*********************  End employee  ********************/
$("#report").click(function(e) {
	var emp = $("input[name='employee']:checked").val();
	var rank = $("input[name='date']:checked").val();
	var emp_id = $("#employee_selected").val();
	var from = $("#from_date").val();
	var to = $("#to_date").val();
	var view = $('input[name="view"]:checked').val();
	if((rank == 1 && from == "") || (rank == 1 && to == ""))
	{
		swal("วันที่ไม่ชัดเจน", "กรุณาระบุวันที่ให้ครบถ้วน","error");
		return false;
	}
	if(emp == 1 && emp_id ==""){
		swal("ผู้รับไม่ถูกต้อง","กรุณาระบุผู้รับหากไม่ระบุให้เลือกตัวเลือกทั้งหมด","error");	
		return false;
	}
    load_in();
	$.ajax({
		url: "report/reportController/supportReportController.php?support_by_employee",
		type:"GET", cache:false,
		data:{ "employee_rank" : emp, "employee_id" : emp_id, "rank" : rank, "from_date" : from, "to_date" : to, "view" : view },
		success: function(data){
			if( view == 0 ){
				var source = $("#report1").html();
			}else{
				var source = $("#report2").html();
			}
			var data = $.parseJSON(data);
			var output = $("#result");
			render(source, data, output);			
			load_out();
		}
	});
});

$("#gogo").click(function(e) {
    var emp = $("input[name='employee']:checked").val();
	var rank = $("input[name='date']:checked").val();
	var emp_id = $("#employee_selected").val();
	var from = $("#from_date").val();
	var to = $("#to_date").val();
	var view = $('input[name="view"]:checked').val();
	if((rank == 1 && from == "") || (rank == 1 && to == ""))
	{
		swal("วันที่ไม่ชัดเจน", "กรุณาระบุวันที่ให้ครบถ้วน","error");
		return false;
	}
	if(emp == 1 && emp_id ==""){
		swal("ผู้รับไม่ถูกต้อง","กรุณาระบุผู้รับหากไม่ระบุให้เลือกตัวเลือกทั้งหมด","error");	
		return false;
	}
	$("#report_form").attr("action","report/reportController/supportReportController.php?export_support_by_employee&employee_rank="+emp+"&employee_id="+emp_id+"&rank="+rank+"&from_date="+from+"&to_date="+to+"&view="+view);
	$(this).attr("type", "submit");
});

function get_detail(id_order)
{
	load_in();
	$.ajax({
		url:"report/reportController/supportReportController.php?get_support_detail&id_order="+id_order,
		type:"GET", cache:false,
		success: function(data)
		{
			if(data != 'fail')
			{
				var source = $("#template").html();
				var data = $.parseJSON(data);
				var output = $("#rs");
				render(source, data, output);
				load_out();
				$("#btn_toggle").click();
			}else{
				swal("Error : ระบบไม่พบข้อมูลที่ร้องขอ");
			}
		}
	});	
}
</script>