<?php 
	$page_menu = "sponser";
	$page_name = "รายงานยอดสปอนเซอร์";
	$id_profile = $_COOKIE['profile_id'];
	?>
<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-8"><h3><span class="glyphicon glyphicon-list"></span>&nbsp;<?php echo $page_name; ?></h3></div>
    <div class="col-sm-4">
       <ul class="nav navbar-nav navbar-right">
			<li>
                <a style='text-align:center; background-color:transparent; padding-bottom:0px;'>
                	<button type='submit' class='btn btn-link' id='report'><span class='fa fa-file-text-o' style='color:#5cb85c; font-size:35px;'></span><br />รายงาน</button>
                </a>
            </li>
			
        </ul>
     </div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />
<!-- End page place holder -->
<?php 
	if(isset($_POST['year'])){
		$year = $_POST['year'];
	}else if(isset($_GET['year'])){
		$year = $_GET['year'];
	}else{
		$year = "";
	}

echo "<form method='post' id='report_form' action='index.php?content=sponser_report&view' > 
		<div class='col-xs-3 col-xs-offset-4'>
   		<div class='input-group'><span class='input-group-addon'>งบประมาณปี : </span> <select class='form-control' name='year' id='year' >"; selectYear($year); echo"</select></div>
    </div></from><br><br><hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' />";
if(isset($_GET['detail'])){
	//$year = $_GET['year'];
	$id_sponsor = $_GET['id_sponsor'];
	list($id_customer, $start, $end) = dbFetchArray(dbQuery("SELECT id_customer, start, end FROM tbl_sponsor WHERE id_sponsor = $id_sponsor"));
	echo "<div class='row'>
            	<div class='col-lg-12'>
							   <table class='table table-striped table-hover'>
                                    <thead>
                                        <tr> <th style='width:10%; text-align: center;'>ลำดับ</th><th style='width:50%;'>สินค้า</th><th style='width:20%; text-align: right;'>จำนวน</th><th style='width:20%; text-align: right;'>มูลค่า</th></tr>
                                    </thead>";
								$sql = dbQuery("SELECT id_product, SUM(sold_qty),SUM(total_amount) FROM tbl_order_detail_sold WHERE id_role = 4 AND id_customer = $id_customer AND (date_upd BETWEEN '$start 00:00:00' AND '$end 23:59:59') GROUP BY id_product");
								$row = dbNumRows($sql);
								if($row>0){
								$i =0;
								$n =1;
								$total_qty = 0;
								$total_amount =0;
								while($i<$row){
									list($id_product, $qty, $amount) = dbFetchArray($sql);
									$product = new product();
									$product->product_detail($id_product);
									$product_name = $product->product_code." : ".$product->product_name;
									echo "<tr><td align='center'>$n</td><td>$product_name</td><td align='right'>".number_format($qty)."</td><td align='right'>".number_format($amount,2)."</td></tr>";
									$total_amount += $amount;
									$total_qty += $qty;
									$n++;
									$i++;
								}
								echo"<tr><td colspan='2' align='right'><h4>รวม</h4></td><td align='right'><h4>".number_format($total_qty)."</h4></td><td align='right'><h4>".number_format($total_amount,2)."</h4></td></tr>";
								}else{
									echo"<tr><td colspan='4' align='center'><h4>ยังไม่มีรายการ</h4></td></tr>";
								}
								echo"<tr><td colspan='4' align='right'><h4>&nbsp;</h4></td></tr>";
							echo "</table>
                            <!-- /.table-responsive -->
                        
                </div>
                <!-- /.col-lg-6 -->
            </div>";
}else if(isset($_GET['view'])){
	
	$num_sponser = dbNumRows(dbQuery("SELECT id_sponsor FROM tbl_sponsor WHERE year = '$year' and active = 1"));
	list($suml_imit) = dbFetchArray(dbQuery("SELECT SUM(limit_amount) FROM tbl_sponsor WHERE year = '$year' and active = 1"));
	$sql = dbQuery("SELECT id_customer,start,end FROM tbl_sponsor WHERE year = '$year' and active = 1");
	$row = dbNumRows($sql);
	$i=0;
	$sumtotal = "0";
	while($i<$row){
	list($id_customer,$start,$end) = dbFetchArray($sql);
	list($sum_total) = dbFetchArray(dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail_sold WHERE (date_upd BETWEEN '$start 00:00:00' AND '$end 23:59:59') AND id_customer = '$id_customer' AND id_role = '4'"));
	$sumtotal = $sumtotal + $sum_total;
	$i++;
	}
	echo "
<div class='row'>
<div align='center'><h4>รายงานภาพรวมสปอนเซอร์ปี ".($year+543) ." </h4></div>
</div>
 <div class='row'>
                <div class='col-xs-3'>
                    <div class='panel panel-primary'>
                        <div class='panel-heading'>
                            <div class='row'>
                                 <div class='col-xs-12'>
                                    <div class='huge' align='center'><h4>". number_format($num_sponser)."</h4></div>
                                  </div>
                            </div>
                        </div>
                            <div class='panel-footer'>
                                <span class='pull-left'>จำนวนสปอนเซอร์</span>
                                <div class='clearfix'></div>
                            </div>
                    </div>
                </div>
                <div class='col-xs-3'>
                    <div class='panel panel-yellow'>
                        <div class='panel-heading'>
                            <div class='row'>
                                <div class='col-xs-12'>
                                    <div class='huge' align='center'><h4>".number_format($suml_imit)." บาท</h4> </div>
                                </div>
                            </div>
                        </div>
                            <div class='panel-footer'>
                                <span class='pull-left'>วงเงิน</span>
                                <div class='clearfix'></div>
                            </div>
                    </div>
                </div>
                <div class='col-xs-3'>
                    <div class='panel panel-red'>
                        <div class='panel-heading'>
                            <div class='row'>
                              <div class='col-xs-12'>
                                    <div class='huge' align='center'><h4>".number_format($sumtotal)." บาท</h4> </div>
                                </div>
                            </div>
                        </div>
                            <div class='panel-footer'>
                                <span class='pull-left'>ใช้ไปแล้ว</span>
                                <div class='clearfix'></div>
                            </div>
                    </div>
                </div>
                <div class='col-xs-3'>
                    <div class='panel panel-green'>
                        <div class='panel-heading'>
                            <div class='row'>
                                <div class='col-xs-12'>
                                    <div class='huge' align='center'><h4>".number_format($suml_imit-$sumtotal)." บาท</h4> </div>
                                </div>
                            </div>
                        </div>
                            <div class='panel-footer'>
                                <span class='pull-left' >คงเหลือ</span>
                                <div class='clearfix'></div>
                            </div>
                    </div>
                </div>
            </div>
            <div class='row'>
            	<div class='col-lg-12'>";
                    
                                
                                   
									$sql = dbQuery("SELECT id_sponsor,reference,id_customer,limit_amount,start,end,remark FROM tbl_sponsor WHERE year = '$year' and active = 1");
											$row = dbNumRows($sql);
											if($row>0){
												echo "<table class='table table-striped table-hover'>
                                    					<thead><th style='width:5%; text-align:center'>ลำดับ</th> <th style='width:25%;'>ผู้รับ</th><th style='width:10%;'>เอกสาร</th>
														<th style='width:20%; text-align:center'>ระยะสัญญา</th><th style='width:12%; text-align: right'>วงเงิน</th><th style='width:12%; text-align: right'>ใช้ไป</th>
														<th style='width:12%; text-align: right'>คงเหลือ</th><th></th></thead>";
											$i=0;
											$sumtotal = "0";
											$n = 1;
											while($i<$row){
											list($id_sponsor,$reference,$id_customer,$limit_amount,$start,$end,$remark) = dbFetchArray($sql);
											list($sum_totald) = dbFetchArray(dbQuery("SELECT SUM(total_amount) FROM tbl_order_detail_sold  WHERE (date_upd BETWEEN '$start 00:00:00' AND '$end 23:59:59') AND id_customer = '$id_customer' AND id_role = '4'"));
											$customer = new customer($id_customer);
											$period_time = thaiTextDate($start)." - ".thaiTextDate($end);
											if($sum_totald == ""){$sum_totald = "0";}
											$balance = $limit_amount - $sum_totald;
											echo "<tr>
                                            <td align='center'>$n</td>
                                            <td>".$customer->full_name."</td>
                                            <td>$reference</td>
                                            <td align='center'>$period_time</td>
                                            <td align='right'>".number_format($limit_amount,2)."</td>
                                            <td align='right'>".number_format($sum_totald,2)."</td>
                                            <td align='right'>".number_format($balance,2)."</td>
                                            <td align='center'><a href='index.php?content=sponser_report&detail&year=$year&id_sponsor=$id_sponsor'><i class='fa fa-align-justify'></i></a></td>
                                        </tr>";
										$n++;
										$i++;
										}
										echo"</table><h4>&nbsp;</h4></div></div>";
											}else{
												echo "<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:15px;' /><br/><h3 align='center'>ไม่มีรายการ</h3>";
											}

	}
		
	
?>
</div>     
<script>
$(document).ready(function(e) {
    $("#report").click(function(e) {
		var year = $("#year").val();
		if(year != "0000"){
       	 	$("#report_form").submit();
		}else{
			alert("ยังไม่ได้เลือกปีงบประมาณ");
		}
    });
});

</script>