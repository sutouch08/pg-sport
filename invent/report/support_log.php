<?php 
	$page_name = "รายงาน การเพิ่ม/ลบ/แก้ไข อภินันทนาการ";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder --><form name='report_form' id='report_form' action='' method='post'>
<div class="row">
	<div class="col-sm-12"><h4 class="title"><?php echo $page_name; ?></h4></div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:10px;' />
<div class="row">
<div class="col-lg-2">

    	<label >การกระทำ</label>
        <select name="action" id="action" class="form-control">
        	<option value="all">ทั้งหมด</option>
            <option value="add">เพิ่ม</option>
            <option value="edit">แก้ไข</option>
            <option value="delete">ลบ</option>
        </select>
</div>
<div class="col-lg-3">
	<label>คำค้นหา</label>
    <input type="text" class="form-control" name="search_text" id="search_text" />
</div>
<div class="col-lg-2">
	<label>จากวันที่</label>
	<input type="text" class="form-control" name="from_date" id="from_date" />
</div>
<div class="col-lg-2">
	<label>ถึงวันที่</label>
	<input type="text" class="form-control" name="to_date" id="to_date" />
</div>
<div class="col-lg-2">
	<label style="visibility:hidden">ค้นหา</label>
	<button type="button" class="btn btn-success" style="display:block" onclick="get_search()"><i class="fa fa-search"></i>&nbsp; ค้นหา</button>
</div>
</div>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
<div class="col-lg-12">
	<table class="table table-striped table-hover">
    <thead style="font-size:12px">
    	<th style="width:5%; text-align:center">ลำดับ</th>
        <th style="width:15%;">ผู้ทำรายการ</th>
        <th style="width:5%">รูปแบบ</th>
        <th >การกระทำ</th>
        <th style="width:15%;">ค่าเดิม</th>
        <th style="width:15%">ค่าใหม่</th>
        <th style="width:15%">เวลาที่เกิด</th>
    </thead>
    <tbody id="result">
    
    </tbody>
    </table>
</div>
</div>
<script id="template" type="text/x-handlebars-template">
 	{{#each this}}
	{{#if nocontent}}
	<tr><td colspan="7" align="center"><h4>{{ nocontent }}</h4></td></tr>
	{{else}}
		<tr style="font-size:12px;">
			<td align="center">{{ n }}</td>
			<td>{{ employee_name }}</td>
			<td>{{ action_type }}</td>
			<td>{{ action }}</td>
			<td>{{ from_value }}</td>
			<td>{{ to_value }} </td>
			<td>{{ date_upd }}</td>
		</tr>
	{{/if}}
	{{/each}}	 
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
  
  function get_search()
  {
	  load_in();
	  var action = $("#action").val();
	  var search_text = $("#search_text").val();
	  var from_date = $("#from_date").val();
	  var to_date = $("#to_date").val();
	  if( from_date !='' || to_date != ''){
		  if( !isDate(from_date) || !isDate(to_date) )
		  {
			  load_out();
			  swal("วันที่ไม่ถูกต้อง");
			  return false;
		  }
	  }
	  $.ajax({
		  url: "report/reportController/supportReportController.php?get_log",
		  type:"GET", cache:false,
		  data: { "search_text" : search_text, "action_type" : action, "from_date" : from_date, "to_date" : to_date },
		  success: function(data){
			var source = $("#template").html();
			var data = $.parseJSON(data);
			var output = $("#result");
			render(source, data, output);
			load_out();  	  
		  }
	  });
	  
  }
</script>