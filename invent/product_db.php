<?php 
	$page_name = "ฐานข้อมูลสินค้า";
	$id_profile = $_COOKIE['profile_id'];
?>

<div class="container">
<!-- page place holder -->
<div class="row" style="height:30px;">
	<div class="col-sm-6" style="margin-top:10px;"><h4 class="title"><i class="fa fa-database"></i>&nbsp;<?php echo $page_name; ?></h4>
	</div>
    <div class="col-sm-6">
      <p class="pull-right" style="margin-bottom:0px;">
		<button type="button" class="btn btn-sm btn-success" onclick="getReport()"><i class="fa fa-bar-chart"></i> รายงาน</button>
        <button type="button" class="btn btn-sm btn-info" onclick="doExport()"><i class="fa fa-file-excel-o"></i> &nbsp;ส่งออก Excel</button>
       </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:15px;' />
<!-- End page place holder -->
<div class="row">
	<div class="col-sm-3 col-sm-offset-3">
    	<select id="catSelect" class="form-control input-sm">
        	<?php echo category_list(); ?> 
        </select>
    </div>
    <div class="col-sm-3">
    	<input type="checkbox" id="cost" value="1" checked />
        <label for="cost" class="label-left">แสดงราคาทุน</label>
        <input type="checkbox" id="price" value="1" checked />
        <label for="price" class="label-left">แสดงราคาขาย</label>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 5px; margin-bottom:15px;' />
<div class="row">
	<div class="col-sm-12" id="res"></div>
</div>

<script id="template" type="text/x-handlebars-template">
<table class="table table-striped">
	<thead>
    	<tr>
        	<th style="width:5%; text-align:center;">ลำดับ</th>
            <th style="width:15%;">บาร์โค้ด</th>
            <th style="width:25%;">รหัสสินค้า</th>
            <th style="width:35%;">ชื่อสินค้า</th>
            <th style="width:10%; text-align:right;">ทุน</th>
            <th style="width:10%; text-align:right;">ราคาขาย</th>
        </tr>
    </thead>
    <tbody>
    	{{#each this}}
        	<tr>
            	<td align="center">{{ no }}</td>
                <td>{{ barcode }}</td>
                <td>{{ pCode }}</td>
                <td>{{ pName }}</td>
                <td align="right">{{ cost }}</td>
                <td align="right">{{ price }}</td>
            </tr>
        {{/each}}
    </tbody>
</table>
</script>

</div><!-- container -->

<script>
function getReport()
{
	var cate = $("#catSelect").val();
	var cost = $("#cost").is(':checked');
	var price = $("#price").is(':checked');
	if( cost == true ){
		cost = 1;
	}else{
		cost = 0;
	}
	if( price == true ){
		price = 1;
	}else{
		price = 0;
	}
	
	$.ajax({
		url:"controller/productController.php?getProductDB&report",
		type:"GET", cache:false, data: { "id_category" : cate, "showCost" : cost, "showPrice" : price },
		success: function(rs){
			var rs = $.trim(rs);
			if( rs == 'nodata' )
			{
				var html = '<div class="callout callout-primary"><h4>ไม่มีสินค้าในหมวดหมู่ที่เลือก</h4></div>';
				$("#res").html(html);
			}
			else
			{
				var source 	= $("#template").html();
				var data 		= $.parseJSON(rs);
				var output 	= $("#res");
				render(source, data, output);	
			}
		}
	});
}

function doExport()
{
	var cate = $("#catSelect").val();
	var cost = $("#cost").is(':checked');
	var price = $("#price").is(':checked');
	if( cost == true ){
		cost = 1;
	}else{
		cost = 0;
	}
	if( price == true ){
		price = 1;
	}else{
		price = 0;
	}
	var token	= new Date().getTime();
	var url		= "controller/productController.php?getProductDB&export&id_category="+cate+"&showCost="+cost+"&showPrice="+price+"&token="+token;
	get_download(token);
	window.location.href = url;
}
</script>