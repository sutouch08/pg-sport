<?php
	$page_menu = "sale_dashboard";
	$page_name = "SALE DASHBOARD";
	$id_profile = $_COOKIE['profile_id'];
	$id_user= $_COOKIE['user_id'];
	$today = date('Y-m-d');
	$this_month = date("m",strtotime("this month"));
	$last_month = date("m",strtotime("-1 month"));
	$last_year = date("Y",strtotime("-1 year"))+543;
	$rang = getMonth();
	$from = $rang['from'];
	$to = $rang['to'];
	$employee = new employee($id_user);
	$id_sale = $employee->get_id_sale($id_user);
	$sale = new sale($id_sale);
	function posColor($n){
		$i = 10-$n;
		switch($i){
			case 9 :
			$class = "#4FC1E9";
			break;
			case 8 :
			$class = "#48CFAD";
			break;
			case 7 :
			$class = "#A0D468";
			break;
			case 6 :
			$class = "#FFCE54";
			break;
			case 5 :
			$class = "#FC6E51";
			break;
			default :
			$class = "#DA4453";
			break;
		}
		return $class;
	}

	?>

<div class="container">
<!-- page place holder -->
<div class="row">
	<div class="col-sm-6"><h3 style="margin-top:15px; margin-bottom:0px;"><span class="glyphicon glyphicon-home"></span>&nbsp;&nbsp;<?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
     		<li><a href='index.php?content=order' style='color:black; text-align:center; background-color:transparent; padding-top:10px; padding-bottom:10px;'><span class='glyphicon glyphicon-plus-sign' style='color:#5cb85c; font-size:30px;'></span><br />New Order</a></li>
       </ul>
    </div>
</div>
<hr style="border-color:#CCC; margin-top: 0px; margin-bottom:5px;" />
<!-- End page place holder -->
<div class='row'>
	<div class='col-sm-3'>
    	<div class="panel panel-primary">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ยอดขายวันนี้</h4>
              </div>
             <div class="panel-body" style="background-color:#4FC1E9;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php  echo $sale->sale_amount("today",$id_sale); ?></h3>
              </div>
           </div>
    </div>
	<div class='col-sm-3'>
    	<div class="panel panel-primary">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ยอดขายเมื่อวาน</h4>
              </div>
             <div class="panel-body" style="background-color:#4FC1E9;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php  echo $sale->sale_amount("yesterday",$id_sale); ?></h3>
              </div>
           </div>
    </div>
    <div class='col-sm-3'>
    	<div class="panel panel-success">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ยอดขายสัปดาห์นี้</h4>
              </div>
             <div class="panel-body" style="background-color:#A0D468;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php echo $sale->sale_amount("this_week",$id_sale); ?></h3>
              </div>
           </div>
    </div>
     <div class='col-sm-3'>
    	<div class="panel panel-success">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ยอดขายสัปดาห์ที่แล้ว</h4>
              </div>
             <div class="panel-body" style="background-color:#A0D468;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php echo $sale->sale_amount("last_week",$id_sale); ?></h3>
              </div>
           </div>
    </div>
    <div class='col-sm-3'>
    	<div class="panel panel-info">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ยอดขายเดือนนี้</h4>
              </div>
             <div class="panel-body" style="background-color:#48CFAD;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php echo $sale->sale_amount("this_month",$id_sale); ?></h3>
              </div>
           </div>
    </div>
 <div class='col-sm-3'>
    	<div class="panel panel-info">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ยอดขายเดือนนที่แล้ว</h4>
              </div>
             <div class="panel-body" style="background-color:#48CFAD;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php echo $sale->sale_amount("last_month",$id_sale); ?></h3>
              </div>
           </div>
    </div>
     <div class='col-sm-3'>
    	<div class="panel panel-warning">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>เดือนนี้ ปีที่แล้ว</h4>
              </div>
             <div class="panel-body" style="background-color:#FFCE54;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php  echo $sale->sale_amount("this_month_last_year", $id_sale); ?></h3>
              </div>
           </div>
    </div>
    <div class='col-sm-3'>
    	<div class="panel panel-warning">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ปีนี้</h4>
              </div>
             <div class="panel-body" style="background-color:#FFCE54;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php  echo $sale->sale_amount("this_year",$id_sale); ?></h3>
              </div>
           </div>
    </div>
</div>


 </div>
