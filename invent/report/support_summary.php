<?php 
	$page_name = "รายงานสรุป ยอดเบิกอภินันทนาการ(พนักงาน)";
	$id_profile = $_COOKIE['profile_id'];
	
	function support_year_select($year = "")
	{
		$option = "";
		if($year == "" ){ $year = date("Y"); }
		$i = 2;
		while($i > -5){
			$text = $year+$i;
			$value = $year + $i;
			if($value == $year){ $se = "selected"; }else{ $se = ""; }
			$option .= "<option value='$value' ".$se.">".$text."</option>";  	
			$i--;
		}
		return $option;
	}
	?>
<div class="container">
<!-- page place holder --><form name='report_form' id='report_form' action='' method='post'>
<div class="row">
	<div class="col-sm-8"><h3 class="title"><i class="fa fa-list"></i>&nbsp;<?php echo $page_name; ?></h3></div>
    <div class="col-sm-4">
    	<p class="pull-right">
        	<button type="button" class="btn btn-primary" id="report"><i class="fa fa-file-text"></i>&nbsp;รายงาน</button>
            <button type="button" class="btn btn-success" id="gogo"><i class="fa fa-file-excel-o"></i>&nbsp;ส่งออก</button>
        </p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<!-- End page place holder -->

<!-- ++++++++++++++++++++++++++++  ผู้รับ +++++++++++++++++++++++++++++-->    
<div class="col-lg-4" style="padding-left:5px; padding-right:5px;">
        <fieldset style="border: 1px solid #DDD; margin:5px; padding-bottom:15px; height: 150px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:0px; border:0px;">ผู้รับ</legend>
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

<!-- ++++++++++++++++++++++++++++  ปีงบประมาณ +++++++++++++++++++++++++++++-->    
<div class="col-lg-4" style="padding-left:5px; padding-right:5px;">
        <fieldset style="border: 1px solid #DDD; margin:5px; padding-bottom:15px; height: 150px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:0px; border:0px;">ปีงบประมาณ</legend>
        	<div class="col-lg-12">&nbsp;</div>
        	<div class="col-lg-12" >
            	
            </div>        
            <div class="col-lg-12">&nbsp;</div>   
            <div class="col-lg-12">
            		<div class="input-group input-group-sm">
                		<span class="input-group-addon">
                        
                      	งบประมาณปี
                        </span>
                		<select name="year" id="year" class="form-control"><?php echo support_year_select(); ?></select>
                	</div>
             </div>
        </fieldset>
</div>
<!-- ++++++++++++++++++++++++++++   end ปีงบประมาณ  +++++++++++++++++++++++++++++--> 

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


</form>
<div class="row">
	<div class="col-lg-12">
    <table class="table table-striped table-hover">
    <thead>
    	<th style="width:5%; text-align:center">ลำดับ</th>
        <th style="width:30%; text-align:center">ผู้รับการสนับสนุน</th>
        <th style="width:12%; text-align:right">งบประมาณ</th>
        <th style="width:12%; text-align:right; color:blue;">ใช้ไป</th>
        <th style="width:12%; text-align:right; color:red;">ใช้ไปทั้งหมด</th>
        <th style="width:12%; text-align:right; clolor:green;">คงเหลือ</th>
        <th style="text-align:right">รายละเอียด</th>
    </thead>
    <tbody  id="result">
    
    </tbody>    
    </table>
    </div>
</div><!-- End row -->
<script id="template" type="text/x-handlebars-template">
 	{{#each this}}
	{{#if nocontent}}
	<tr><td colspan="7" align="center"><h4>{{ nocontent }}</h4></td></tr>
	{{else}}
		{{#if @last}}
		<tr>
			<td colspan="2" align="right"><strong>รวม</strong></td>
			<td align="right"><strong>{{ budget }}</strong></td>
			<td align="right" style="color:blue;"><strong>{{ used }}</strong></td>
			<td align="right" style="color:red;"><strong>{{ all_used }}</strong></td>
			<td align="right" style="color:green;"><strong>{{ balance }}</strong></td>
			<td align="right">&nbsp;</td>
		</tr>
		{{else}}
		<tr>
			<td align="center">{{ n }}</td>
			<td>{{ employee }}</td>
			<td align="right">{{ budget }}</td>
			<td align="right" style="color:blue;">{{ used }}</td>
			<td align="right" style="color:red;">{{ all_used }}</td>
			<td align="right" style="color:green;">{{ balance }} </td>
			<td align="right"><button type="button" class="btn btn-primary" onclick="get_detail( {{detail_btn}}, '{{employee}}')" ><i class='fa fa-search'></i>&nbsp; รายละเอียด</button></td>
		</tr>
		{{/if}}
	{{/if}}
	{{/each}}	 
</script>	
<script id="modal" type="text/x-handlebars-template">
	{{#each this}}
		{{#if nocontent}}
			<tr><td colspan="5" align="center"><h4></h4></td></tr>
		{{else}}
			{{#if @last}}
            	<tr>
				<td colspan="2" align="right"><strong>รวม</strong></td>
				<td align="right" style="color:blue"><strong>{{ qty }}</strong></td>
				<td align="right" style="color:green"><strong>{{ amount }}</strong></td>
				<td>&nbsp;</td><td>&nbsp;</td></tr>
            {{else}}
            	<tr>
				<td>{{ date }}</td><td>{{ reference }}</td>
				<td align="right" style="color:blue;">{{ qty }}</td>
				<td align="right" style="color: green">{{ amount }}</td>
				<td>&nbsp;</td><td>{{ remark }}</td></tr>
            {{/if}}
		{{/if}}
	{{/each}}
</script>
<button data-toggle='modal' data-target='#order_grid' id='btn_toggle' style='display:none;'>toggle</button>
<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
								  <div class='modal-dialog' id='modal' style="width:800px;">
									<div class='modal-content'>
									  <div class='modal-header'>
										<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
										<center><h4 class='modal-title' id='modal_title'>title</h4></center>
									  </div>
									  <div class='modal-body' id='modal_body'>
                                      <table class='table table-striped'>
                                        <thead>
                                        <th style='width:10%'>วันที่</th>
                                        <th style='width:10%;'>เลขที่เอกสาร</th>
                                        <th style='width:10%; text-align:right;'>จำนวน</th>
                                        <th style='width:15%; text-align:right;'>มูลค่า</th>
                                         <th style='width:2%; text-align:right;'></th>
                                        <th style='text-align:center'>หมายเหตุ</th>
                                        </thead>
                                        <tbody id="modal_result">
                                        
                                        </tbody>
                                        </table>
                                      </div>
									  <div class='modal-footer'>
										<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
									  </div>
									</div>
								  </div>
								</div>
</div><!-- Container -->
<?php	$qs = dbQuery("SELECT tbl_support.id_employee, first_name, last_name FROM tbl_support JOIN tbl_employee ON tbl_support.id_employee = tbl_employee.id_employee"); ?>
<?php	if(dbNumRows($qs) > 0 ) : ?>
<script>
		var EmployeeList = [
<?php		while($rs = dbFetchArray($qs) ) : ?>
				"<?php echo $rs['id_employee']; ?> : <?php echo $rs['first_name']." ".$rs['last_name']; ?>",						
<?php		endwhile; ?>
			];
</script>
<?php 	endif;  ?>
<script id='detail' type="text/x-handlebars-template">
{{#each this}}
	{{#if nocontent}}
		<tr><td colspan="6"><center><strong>{{ nocontent }}</strong></center></td></tr>
	{{else}}
		{{#if @last}}
			<tr><td colspan="2" align="right"><strong>รวม</strong></td><td align="right" style="color:blue;"><strong>{{ total_qty }}</strong></td><td align="right" style="color:green"><strong>{{ total_amount }}</strong></td><td></td><td></td></tr>
		{{else}}
			<tr><td>{{ date_upd }}</td><td> {{ reference }}</td><td align="right">{{ qty }}</td><td align="right">{{ amount }}</td><td></td><td>{{ remark }}</td></tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>

<script>

/************************  year  **********************/
    var year = $("#year").val();
	var min_date = "01-01-"+year;
    
$("#year").change(function(e) {
	var year = $(this).val();
	var min_date = "01-01-"+year;
    $( "#from_date" ).datepicker( "option", "minDate", min_date );
});
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
      dateFormat: 'dd-mm-yy', minDate: min_date, onClose: function( selectedDate ) {
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
	source : EmployeeList, //"controller/autoComplete.php?get_support_id",
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
	var year = $("#year").val();
	var rank = $("input[name='date']:checked").val();
	var emp_id = $("#employee_selected").val();
	var from = $("#from_date").val();
	var to = $("#to_date").val();
	if( rank == 1 )
	{
		if( !isDate(from) || !isDate(to)){
		swal("วันที่ไม่ชัดเจน", "กรุณาระบุวันที่ให้ครบถ้วน","error");
		return false;
		}
	}
	if(emp == 1 && emp_id ==""){
		swal("ผู้รับไม่ถูกต้อง","กรุณาระบุผู้รับหากไม่ระบุให้เลือกตัวเลือกทั้งหมด","error");	
		return false;
	}
    load_in();
	$.ajax({
		url: "report/reportController/supportReportController.php?support_summary&employee_rank="+emp+"&employee_id="+emp_id+"&year="+year+"&rank="+rank+"&from_date="+from+"&to_date="+to,
		type:"GET", cache:false,
		success: function(data){
			var source = $("#template").html();
			var data = $.parseJSON(data);
			var output = $("#result");
			render(source, data, output);
			load_out();
		}
		});
});

$("#gogo").click(function(e) {
	var emp 		= $("input[name='employee']:checked").val();
	var year 	= $("#year").val();
	var rank 		= $("input[name='date']:checked").val();
	var emp_id 	= $("#employee_selected").val();
	var from 		= $("#from_date").val();
	var to 		= $("#to_date").val();
	if( rank == 1 )
	{
		if( !isDate(from) || !isDate(to)){
		swal("วันที่ไม่ชัดเจน", "กรุณาระบุวันที่ให้ครบถ้วน","error");
		return false;
		}
	}
	if(emp == 1 && emp_id ==""){
		swal("ผู้รับไม่ถูกต้อง","กรุณาระบุผู้รับหากไม่ระบุให้เลือกตัวเลือกทั้งหมด","error");	
		return false;
	}
	var token = new Date().getTime();
	var target = "report/reportController/supportReportController.php?export_support_summary&employee_rank="+emp+"&employee_id="+emp_id+"&year="+year+"&rank="+rank+"&from_date="+from+"&to_date="+to+"&token="+token;
	get_download(token);
	window.location.href = target;

});


function get_detail(id_employee, name){
	var year = $("#year").val();
	var rank = $("input[name='date']:checked").val();
	var from = $("#from_date").val();
	var to = $("#to_date").val();
	load_in();
	$.ajax({
		url: "report/reportController/supportReportController.php?get_support_detail",
		type:"GET", cache:false,
		data:{"id_employee" : id_employee, "rank" : rank, "from_date" : from, "to_date" : to, "year" : year},
		success: function(data){
			var source = $("#modal").html();
			var data = $.parseJSON(data);
			var output = $("#modal_result");
			render(source, data, output);
			$("#modal_title").html(name);
			load_out();
			$("#btn_toggle").click();	
		}
	});	
}
</script>