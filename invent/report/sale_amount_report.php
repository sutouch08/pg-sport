<?php 

	/* Dash Board รายงานยอดขาย */
	
	$page_name = "สรุปยอดขายรวม";
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
	$sale = new sale();
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
	<div class="col-sm-6"><h3 style="margin-top:15px; margin-bottom:0px;"><?php echo $page_name; ?></h3>
	</div>
    <div class="col-sm-6">
       <ul class="nav navbar-nav navbar-right">
     		<li></li>
       </ul>
    </div>
</div>
<hr style="border-color:#CCC; margin-top: 0px; margin-bottom:5px;" />
<!-- End page place holder -->
<div class='row'>
	<div class='col-sm-3'>
    	<div class="panel panel-primary">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ยอดขายรวมวันนี้</h4>
              </div>
             <div class="panel-body" style="background-color:#4FC1E9;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php  echo $sale->totalToday(); ?></h3>
              </div>
           </div>
    </div>
    <div class='col-sm-3'>
    	<div class="panel panel-primary">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ยอดขายเมื่อวาน</h4>
              </div>
             <div class="panel-body" style="background-color:#4FC1E9;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php  echo $sale->totalLastDay(); ?></h3>
              </div>
           </div>
    </div>
     <div class='col-sm-3'>
    	<div class="panel panel-warning">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ยอดขายรวมสัปดาห์นี้</h4>
              </div>
             <div class="panel-body" style="background-color:#FFCE54;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php  echo $sale->totalThisWeek(); ?></h3>
              </div>
           </div>
    </div>
    <div class='col-sm-3'>
    	<div class="panel panel-warning">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ยอดขายรวมสัปดาห์ที่แล้ว</h4>
              </div>
             <div class="panel-body" style="background-color:#FFCE54;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php  echo $sale->totalLastWeek(); ?></h3>
              </div>
           </div>
    </div>
	<div class='col-sm-3'>
    	<div class="panel panel-success">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ยอดขายรวมเดือนนี้</h4>
              </div>
             <div class="panel-body" style="background-color:#A0D468;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php  echo $sale->totalThisMonth(); ?></h3>
              </div>
           </div>
    </div>
    <div class='col-sm-3'>
    	<div class="panel panel-success">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ยอดขายรวมเดือนที่แล้ว</h4>
              </div>
             <div class="panel-body" style="background-color:#A0D468;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php echo $sale->totalLastMonth(); ?></h3>
              </div>
           </div>
    </div>
    <div class='col-sm-3'>
    	<div class="panel panel-info">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ยอดขายรวมปีนี้</h4>
              </div>
             <div class="panel-body" style="background-color:#48CFAD;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php echo $sale->totalThisYear(); ?></h3>
              </div>
           </div>
    </div>
     <div class='col-sm-3'>
    	<div class="panel panel-info">
  			<div class="panel-heading">
                <h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ยอดขายรวมปีที่แล้ว</h4>
              </div>
             <div class="panel-body" style="background-color:#48CFAD;">
                   <h3 style='color:#FFF; margin-top:5px; text-align:center;'><?php echo $sale->totalLastYear(); ?></h3>
              </div>
           </div>
    </div>
</div>

 </div>

