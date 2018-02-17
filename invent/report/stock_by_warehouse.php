
<style>
fieldset {
	border:1px solid #CCC;
	margin:0px;
	padding:15px;
	height:100px;
} 
legend {
	width:auto; 
	margin-left:auto; 
	margin-right:auto; 
	padding-left:15px; 
	padding-right:15px; 
	margin-bottom:0px; 
	border:0px;
}
</style>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-8"><h3 style="margin-top:0px; margin-bottom:0px;"><i class="fa fa-list"></i>&nbsp; รายงานสินค้าคงเหลือเปรียบเทียบตามคลัง</h3></div>
    <div class="col-sm-4">
       <p class="pull-right">
       		<button type='button' class='btn btn-success' id='report'><i class="fa fa-file-text"></i>&nbsp; รายงาน</button>
			<button type='button' class='btn btn-success' id='gogo'><i class="fa fa-file-excel-o"></i>&nbsp;ส่งออก</button>
       </p>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' class='hidden-print' />
<form id="the_form" method="post" action="">
<fieldset>
<legend>สินค้า</legend>
	<div class="col-sm-2">
    	<select name="option" class="form-control" id="option"><option value="1" selected="selected">สินค้าทั้งหมด</option><option value="2">เลือกเฉพาะ</option></select>
    </div>
    <div class="col-sm-4">
    <input type="text" name="product_selected" class="form-control" id="product_selected" disabled="disabled" required="required" /> <input type="hidden" name="id_product" id="id_product" />
    </div>
    </fieldset>
    <button type="button" id="btn_submit" style="display:none" disabled="disabled"></button>
    </form>
    <hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' class='hidden-print' />
	<div class="row"    >
    <div class="col-sm-12" id="result"></div>
    </div>
    <div id="loader" style="position:absolute; padding: 15px 25px 15px 25px; background-color:#fff; opacity:0.0; box-shadow: 0px 0px 25px #CCC; top:-20px; display:none;">
        <center><i class="fa fa-spinner fa-5x fa-spin blue"></i></center><center>Loading...</center></div>

</div><!-- Container -->
<script>
$("#option").change(function(e) {
    var x = $(this).val();
	if(x == 1){
		$("#product_selected").attr("disabled","disabled");
	}else if(x == 2){
		$("#product_selected").removeAttr("disabled");
	}else{
		$("#product_selected").attr("disabled","disabled");
	}
});

$(document).ready(function(e) {
    $("#product_selected").autocomplete({
		source:"controller/orderController.php?product_name",
		autoFocus: true,
		close: function(event,ui){
			var data = $("#product_selected").val();
			var arr = data.split(':');
			var id = arr[0];
			var name = arr[1];
			$("#id_product").val(id);
			$(this).val(name);
		}
	});			
});


function load_in()
{
	var x = ($(document).innerWidth()/2)-50;
	$("#loader").css("display","");
	$("#loader").css("left",x);
	$("#loader").animate({opacity:0.8, top:200},300);
}
function load_out()
{
	$("#loader").animate({opacity:0.1, top:-100},200,function(){$("#loader").css("display","none")});
	
}

$("#report").click(function(e) {
	var x = $("#option").val();
	var n = $("#product_selected").val();
	var product = "";
	if(x == 2){
		if(n ==""){
			swal("ยังไม่ได้เลือกสินค้า","กรุณาเลือกสินค้าที่จะเปรียบเทียบยอด","error");
			return false;
		}
	}
	$("#result").html("");
    load_in();
	if(x == 2){
		product = $("#id_product").val();
	}
	$.ajax({
		url:"controller/reportController.php?stock_by_warehouse&product="+product,
		type:"GET",cache:false,
		success: function(rs){
			load_out();
			$("#result").html(rs);
		}
	});
		
});
$("#gogo").click(function(e) {
    var x = $("#option").val();
	var n = $("#product_selected").val();
	var product = "";
	if(x == 2){
		if(n ==""){
			swal("ยังไม่ได้เลือกสินค้า","กรุณาเลือกสินค้าที่จะเปรียบเทียบยอด","error");
			return false;
		}
	}
	if(x == 2){
		product = $("#id_product").val();
	}
	$("#the_form").attr("action","controller/reportController.php?export_stock_by_warehouse&product="+product);
	$("#the_form").submit();
});
</script>