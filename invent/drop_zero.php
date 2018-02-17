<div class="container">

<div class="row">
	<div class="col-sm-6"><h3 style="margin-top:0px; margin-bottom:10px;">เคลียร์ยอดสต็อกที่เป็นศูนย์</h3></div>
    <div class="col-sm-6">
    		<p class="pull-right">
            <button type='submit' class='btn btn-warning' id='clear' style="display:none"><i class="fa fa-eraser"></i>&nbsp;เคลียร์</button>
       		<button type='submit' class='btn btn-success' id='check'><i class="fa fa-search"></i>&nbsp;ตรวจสอบ</button>
            </p>
    </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<div class="row">
	<div class="col-lg-12" id="result">
    </div>
    <div id="loader" style="position:absolute; padding: 15px 25px 15px 25px; background-color:#fff; opacity:0.0; box-shadow: 0px 0px 25px #CCC; top:-20px; display:none;">
        <center><i class="fa fa-spinner fa-5x fa-spin blue"></i></center><center>Searching</center></div>
</div>
</div>
<script>
function check(){
	$("#result").html("");
	loadin();
	setTimeout(function(){
	$.ajax({
		url:"controller/reportController.php?zero_stock=y",type:"GET",cache:false,
		success: function(rx){
			var rs = rx.trim();
			if(rs !="x"){
				loadout();
				$("#result").html(rs);
				$("#clear").css("display","");
			}else if(rs =="x"){
				loadout();
				$("#result").html("<h3 style='text-align:center'>------ ไม่มีรายการที่เป็นศูนย์  ---------</h3>");	
			}
		}
	}) }, 500);
}

function clear(){
	$("#result").html("");
	loadin();
	setTimeout(function(){
	 $.ajax({
		url: "controller/reportController.php?clear_zero=y", type:"GET", cache:false,
		success: function(rx){
			var rs = rx.trim();
			if(rs =="success"){
				swal("Success", "เคลียร์ยอดที่เป็นศูนย์ออกแล้ว", "success");
				check();
			}else if(rs =="x"){
				swal("Fail", "เคลียร์ยอดที่เป็นศูนย์ออกไม่สำเร็จ", "error");
				check();
			}
		}
	})}, 500);
}
function loadin(){
	var x = ($(document).innerWidth()/2)-50;
	$("#loader").css("display","");
	$("#loader").css("left",x);
	$("#loader").animate({opacity:0.8, top:100},300);	
}

function loadout(){
	$("#loader").animate({opacity:0.1, top:-20},300);
	$("#loader").css("display","none");
}

$("#check").click(function(e) {
	check();	
});

$("#clear").click(function(e) {
	clear();
});
</script>