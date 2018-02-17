<?php 
	$page_name = "รายงานวิเคราะห์จำนวนขาย แยกตามคุณลักษณะสินค้า";
	$id_profile = $_COOKIE['profile_id'];

	?>
<div class="container">
<!-- page place holder --><form name='report_form' id='report_form' action='' method='post'>
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

<!-- ++++++++++++++++++++++++++++  สินค้า +++++++++++++++++++++++++++++-->    
<div class="col-lg-6" style="padding-left:5px; padding-right:5px;">
        <fieldset style="border: 1px solid #DDD; margin:5px; padding-bottom:15px; height: 150px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:0px; border:0px;">สินค้า</legend>  	
        	<div class="col-lg-12">&nbsp;</div>
            <div class="col-lg-4" >
            	<div class="input-group input-group-sm">
                <span class="input-group-addon">
                <input type='radio' name='product' id='product0' value='0' checked />
                </span>
                <label for="product0" class="input-group-addon form-control">ทั้งหมด</label>
                </div>
            </div>  
            <div class="col-lg-4">
            	<div class="input-group input-group-sm">
                <span class="input-group-addon">
                <input type='radio' name='product' id='product1' value='1' />
                </span>
                <label for="product1" class="input-group-addon form-control">เลือกสินค้าบางรุ่น</label>
                </div>
            </div>
            <div class="col-lg-4">
            	<button type="button" class="btn btn-default btn-sm" id="btn_select_some" disabled ><i class="fa fa-check-square-o"></i>&nbsp;เลือกสินค้า</button>
              </div>
            <div class="col-lg-12">&nbsp;</div>
            <div class="col-lg-8" >
            	<div class="input-group input-group-sm">
                <span class="input-group-addon">
                <input type='radio' name='product' id='product2' value='2' />
                </span>
                <label for="product2" class="input-group-addon">เฉพาะรุ่น</label>
                <input type="text" class="form-control input-sm" id="product_selected" placeholder="พิมพ์รุ่นสินค้า" disabled  />
                <input type="hidden" name="id_product" id="id_product" />
                </div>
            </div>  
        </fieldset>
</div>
<!-- ++++++++++++++++++++++++++++   end คลัง  +++++++++++++++++++++++++++++--> 
<?php $last_month = getLastMonth();  ?>
<!-- ++++++++++++++++++++++++++++  Date rank+++++++++++++++++++++++++++++-->    
<div class="col-lg-4" style="padding-left:5px; padding-right:5px;">
        <fieldset style="border: 1px solid #DDD; margin:5px; padding-bottom:15px; height: 150px;">
        <legend style="width:auto; margin-left:15px; margin-right:auto; padding-left:15px; padding-right:15px; margin-bottom:0px; border:0px;">ช่วงวันที่</legend>
        	<div class="col-lg-12">&nbsp;</div>
        	<div class="col-lg-12" >
            	<div class="input-group input-group-sm">
                <span class="input-group-addon">
                <input type='radio' name='date' id='date1' value='0' />
                </span>
                <label for="date1" class="form-control" style="width:135px">ทั้งหมด</label>
                </div>
            </div>        
            <div class="col-lg-12">&nbsp;</div>   
            <div class="col-lg-6" style="padding-right:0px;">
            		<div class="input-group input-group-sm">
                		<span class="input-group-addon">
                        <input type='radio' name='date' id='date2' value='1' checked="checked" />
                        <label for="date2" style="padding-left:10px; margin-bottom:0px;">จาก</label>
                        </span>
                		<input type="text" name="from_date" id="from_date" class="form-control" value="<?php echo thaiDate($last_month['start']); ?>" />
                	</div>
            </div>
            <div class="col-lg-6">
                    <div class="input-group input-group-sm">
                		<span class="input-group-addon">
                        <label for="date2" style="padding-left:10px; margin-bottom:0px;">ถึง</label>
                        </span>
                		<input type="text" name="to_date" id="to_date" class="form-control" value="<?php echo thaiDate($last_month['end']); ?>"  />
                	</div>
             </div>
        </fieldset>
</div>
<!-- ++++++++++++++++++++++++++++   end Date rank  +++++++++++++++++++++++++++++--> 

</div><!-- End row -->
<button type="button" data-toggle='modal' data-target='#order_grid' id='btn_toggle' style="display:none" >toggle</button>
<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
	<div class='modal-dialog' id='modal' style="width:1000px;"> 
		<div class='modal-content'>
			<div class='modal-header'>
				<p class="pull-right"><button type='submit' class='btn btn-primary' data-dismiss='modal'>เพิ่มในรายการ</button></p> <!-- <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button> -->
				<h4 class='modal-title' id='modal_title'>เลือกสินค้า</h4>										
			</div>
			<div class='modal-body' id='modal_body' >  
            <div class="row">
            <?php $qr = dbQuery("SELECT id_category, category_name FROM tbl_category"); ?>
            <?php while($cs = dbFetchArray($qr) ) : ?>
            <div class="col-lg-3">
            	<input type="checkbox" id="<?php echo $cs['id_category']; ?>" onchange="checked_cate($(this),<?php echo $cs['id_category']; ?>)" />
                <label for="<?php echo $cs['id_category']; ?>" style="padding-left:10px;"><?php echo $cs['category_name']; ?></label>
            </div>
            <?php endwhile; ?>
            </div>
            <hr />
<?php    $qs = dbQuery("SELECT id_product, product_code, default_category_id AS cat_id FROM tbl_product"); ?>
<?php	while($rs = dbFetchArray($qs) ) :	?>
			<div class="col-lg-3" style="margin-bottom:10px;">
            	<input type="checkbox" class="<?php echo $rs['cat_id']; ?> all" name="p_checked[<?php echo $rs['id_product']; ?>]" id="p_checked_<?php echo $rs['id_product']; ?>" value="<?php echo $rs['id_product']; ?>" />
                <label for="p_checked_<?php echo $rs['id_product']; ?>" style="padding-left:10px;"><?php echo $rs['product_code']; ?></label>
            </div>				
<?php	endwhile;     ?>    
   			         
                                        
			</div>
			<div class='modal-footer'>
				<button type='submit' class='btn btn-primary' data-dismiss='modal'>เพิ่มในรายการ</button>
			</div>
		</div>
	</div>
</div>
</form>
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
	<div class="col-lg-12">
    <table id="result" class="table table-striped table-hover">
 	<thead>
    	<th style="width:5%; text-align:center">ลำดับ</th>
        <th style='width: 20%'>รุ่นสินค้า</th>
    	<th style='width: 35%'>รายการสินค้า</th>
		<th style='width:10%; text-align:center'>สี</th>
		<th style='width:10%; text-align:center'>ไซด์</th>
		<th style='width:10%; text-align:center;'>อื่นๆ</th>
		<th style='width:10%; text-align:right;'>จำนวน</th>
    </thead>
    <tbody id="rs">
 
	</tbody>
 </table>
 <script id="template" type="text/x-handlebars-template">
 	{{#each this}}
    <tr>
    	<td align="center">{{ n }}</td>
		<td>{{ product_code }}</td>
        <td>{{ reference }}</td>
        <td align="center">{{ color }}</td>
        <td align="center">{{ size }}</td>
        <td align="center">{{ attribute }} </td>
        <td align="right">{{ qty }}</td>
    </tr>
	{{/each}}	 
 </script>
    </div>
</div><!-- End row -->
</div><!-- Container -->
<script>
/***************************  product  **********************/

$("#product0").change(function(e) {
	$("#product_selected").attr("disabled", "disabled");
	$("#product_selected").val("");
	$("#btn_select_some").attr("disabled","disabled");
	$("#id_product").val("");
});

$("#product1").change(function(e) {
    $("#product_selected").attr("disabled", "disabled");
	$("#product_selected").val("");
	$("#btn_select_some").removeAttr("disabled");
	$("#id_product").val("");
});

$("#product2").change(function(e) {
    $("#product_selected").removeAttr("disabled");
	$("#product_selected").focus();
	$("#btn_select_some").attr("disabled","disabled");
});

$("#product_selected").autocomplete({
	source: "controller/autoComplete.php?get_product_id",
	autoFocus: true,
	close: function(event,ui){
		var data = $(this).val();
		var arr = data.split(" : ");
		var id = arr[2];
		var name = arr[0];
		$("#id_product").val(id);
		$(this).val(name);
	}
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

/*************************  Show Report by condition  **********************/
$("#report").click(function(e) {
	var product = $("input[name='product']:checked").val();
	var id_product = $("#id_product").val();
	var rank = $("input[name='date']:checked").val();
	var from = $("#from_date").val();
	var to = $("#to_date").val();
	if( product == 2 && id_product =="")
	{
		swal("กรุณาระบุสินค้าที่ต้องการออกรายงาน");
		return false;
	}
	if( rank == 1 )
	{
		if( !isDate(from) || !isDate(to)){
		swal("วันที่ไม่ชัดเจน", "กรุณาระบุวันที่ให้ครบถ้วน","error");
		return false;
		}
	}
	//$("#result").html("");
    load_in();
	$.ajax({
		url: "report/reportController/analyzController.php?attribute_analyz",
		type:"POST", cache:false,
		data: $("#report_form").serialize(),
		success: function(data){
			load_out();
			var source = $("#template").html();
			var data 		= $.parseJSON(data);
			var output	= $("#rs");
			render(source, data, output);
		}
		});
});


/********************************  Export to Excel  ****************************/
$("#gogo").click(function(e) {
	var product = $("input[name='product']:checked").val();
	var id_product = $("#id_product").val();
	var rank = $("input[name='date']:checked").val();
	var from = $("#from_date").val();
	var to = $("#to_date").val();
	if( product == 2 && id_product =="")
	{
		swal("กรุณาระบุสินค้าที่ต้องการออกรายงาน");
		return false;
	}
	if( rank == 1 )
	{
		if( !isDate(from) || !isDate(to))
		{
			swal("วันที่ไม่ชัดเจน", "กรุณาระบุวันที่ให้ครบถ้วน","error");
			return false;
		}
	}/// end if;
	$("#report_form").attr("action", "report/reportController/analyzController.php?export_attribute_analyz");
	$("#report_form").submit();
});
function checked_cate(el, id_tab){
	if(el.is(":checked")){
		$("."+id_tab).each(function(index, element) {
            $(this).prop("checked", true);
        });
	}else{
		$("."+id_tab).each(function(index, element) {
            $(this).prop("checked", false);
        });
	}
}

function clear_all_checked(){
	$(".all").each(function(index, element) {
            $(this).prop("checked", false);
        });
}

$("#btn_select_some").click(function(e){
	$("#btn_toggle").click();
});



</script>