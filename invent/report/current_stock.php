<?php 
	$page_name = "รายงานสินค้าคงเหลือ";
	?>
<div class="container">
    <div class="row top-row">
    	<div class="col-sm-6 top-col"><h4 class="title"><i class="fa fa-tags"></i> <?php echo $page_name; ?></h4></div>
        <div class="col-sm-6">
        	<p class="pull-right top-p">
            	<button type="button" class="btn btn-sm btn-success" onclick="getReport('all')">แสดงทั้งหมด</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="getReport('instock')">เฉพาะที่มียอด</button>
                <button type="button" class="btn btn-sm btn-danger" onclick="getReport('nonstock')">เฉพาะที่ไม่มียอด</button>
            </p>
        </div>
    </div><!--/ row -->

<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:10px;' />
<div class="row" id="rs"></div>

</div><!--/ container -->

<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
    <div class='modal-dialog' id='modal'>
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                
                <center><h4 class='modal-title' id='modal_title'></h4></center>
            </div>
            <div class='modal-body' id='modal_body'></div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
            </div>
        </div>
    </div>
</div>
<script>

function viewCategory(id) {
	
    var output = $("#cat-" + id);
	$('.tab-pane').removeClass('active');
	$(".menu").removeClass("active");	
	output.addClass('active');
}

function getData(id_product){
	$.ajax({
		url:"controller/reportController.php?getData&id_product="+id_product,
		type:"GET", cache:false, 
		success: function(dataset){
			if(dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				$("#modal").css("width",table_w+"px");
				$("#modal_title").html(title);
				$("#modal_body").html(data);
				$("#order_grid").modal('show');
			}else{
				alert("NO DATA");
			}		
		}
	});
}


function getReport(option)
{
	load_in();
	$.ajax({
		url:"report/reportController/stockReportController.php?reportStockCurrent&report",
		type:"POST", cache:"false", data: { "option" : option },
		success: function(rs){
			load_out();	
			$("#rs").html(rs);
		}
	});
}

</script>
