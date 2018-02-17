<?php 
	$page_name = "รายงานใบยืมสินค้า เรียงตามเลขที่เอกสาร";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder --><form name='report_form' id='report_form' action='' method='post'>
<div class="row">
	<div class="col-sm-8"><h4 style="margin-top:0px;"><?php echo $page_name; ?></h4></div>
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
<!-- ++++++++++++++++++++++++++++  เลขที่เอกสาร +++++++++++++++++++++++++++++-->    
<div class="col-lg-4" style="padding-left:5px; padding-right:5px;">
        <fieldset style="border: 1px solid #DDD; margin:5px; padding-bottom:15px; height: 150px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:0px; border:0px;">เลขที่เอกสาร</legend>
        	<div class="col-lg-12">&nbsp;</div>
        	<div class="col-lg-12" >
            	<div class="input-group input-group-sm">
                <span class="input-group-addon">
                <input type='radio' name='doc' id='doc1' value='0' checked="checked"/>&nbsp;
                </span>
                <label for="doc1" class="form-control" style="width:135px">ทั้งหมด</label>
                </div>
            </div>        
            <div class="col-lg-12">&nbsp;</div>   
            <div class="col-lg-12">
            		<div class="input-group input-group-sm">
                		<span class="input-group-addon">
                        <input type='radio' name='doc' id='doc2' value='1'  />
                        <label for="doc2" style="padding-left:10px; margin-bottom:0px;">เฉพาะ</label>
                        </span>
                        <input type="hidden" name="doc_selected" id="doc_selected"  />
                		<input type="text" name="txt_doc" id="txt_doc" class="form-control" disabled="disabled" />
                	</div>
             </div>
        </fieldset>
</div>
<!-- ++++++++++++++++++++++++++++   end เอกสาร  +++++++++++++++++++++++++++++--> 

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

<!-- ++++++++++++++++++++++++++++ Sort Method +++++++++++++++++++++++++++++++-->
<div class="col-lg-4" style="padding-left:5px; padding-right:5px;">
        <fieldset style="border: 1px solid #DDD; margin:5px; padding-bottom:15px; height: 150px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:0px; border:0px;">การเรียงลำดับ</legend>
        	<div class="col-lg-12">&nbsp;</div>
        	<div class="col-lg-12" >
                <input type='radio' name='sort' id='by_doc' value='0' checked="checked"/>&nbsp;
                <label for="by_doc" >เรียงตามเลขที่เอกสาร</label>
            </div>        
            <div class="col-lg-12">&nbsp;</div>   
            <div class="col-lg-12" >
                <input type='radio' name='sort' id='by_date' value='1' />&nbsp;
                <label for="by_date" >เรียงตามวันที่</label>
            </div>        
        </fieldset>
</div>
<!-- ++++++++++++++++++++++++++++ End Sort Method +++++++++++++++++++++++++++++++-->
</div><!-- End row -->
</form>
<div class="row">
	<div class="col-lg-12" id="result">
  
    </div>
</div><!-- End row -->
</div><!-- Container -->
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


/************************  doc  **********************/
$("#doc1").change(function(e){
	$("#txt_doc").attr("disabled","disabled");
});

$("#doc2").change(function(e) {
    $("#txt_doc").removeAttr("disabled");
});

$("#txt_doc").autocomplete({
	source : "controller/autoComplete.php?get_lend_id",
	autoFocus: true,
	close: function(event,ui){
		var data = $(this).val();
		var arr = data.split(" : ");
		var name = arr[1];
		var id = arr[0];
		$("#doc_selected").val(id);
		$(this).val(name);
	}
});
/*********************  End doc  ********************/
$("#report").click(function(e) {
	$("#result").html("");
    load_in();
	var doc = $("input[name='doc']:checked").val();
	var rank = $("input[name='date']:checked").val();
	var doc_id = $("#doc_selected").val();
	var from = $("#from_date").val();
	var to = $("#to_date").val();
	var order_by = $("input[name='sort']:checked").val();
	$.ajax({
		url: "controller/reportController.php?lend_by_doc&doc_rank="+doc+"&doc_id="+doc_id+"&rank="+rank+"&from_date="+from+"&to_date="+to+"&sort="+order_by,
		type:"GET", cache:false,
		success: function(data){
			$("#result").html(data);
			load_out();
		}
		});
});

$("#gogo").click(function(e) {
    var doc = $("input[name='doc']:checked").val();
	var rank = $("input[name='date']:checked").val();
	var doc_id = $("#doc_selected").val();
	var from = $("#from_date").val();
	var to = $("#to_date").val();
	var order_by = $("input[name='sort']:checked").val();
	$("#report_form").attr("action","controller/reportController.php?export_lend_by_doc&doc_rank="+doc+"&doc_id="+doc_id+"&rank="+rank+"&from_date="+from+"&to_date="+to+"&sort="+order_by);
	$(this).attr("type", "submit");
});

</script>