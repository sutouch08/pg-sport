<?php 

	/* Dash Board รายงานยอดขาย */
	
	$page_name = "สรุปยอดขาย แยกตามพนักงาน";
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
<!-- บรรทัดสอง -->
<div class='row'>
	<div class='col-sm-3'>
    	<div class='panel panel-primary'>
        	<div class='panel-heading'>
            	<h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>วันนี้</h4>
            </div>
            <div class='panel-body'>
           <div class='row' style='margin-top:-15px; margin-bottom:-15px;'>
                <table class='table' style='margin-bottom:0px;'>
                <?php
				$result = $sale->LeaderBoard("today");
				$n = 1;
				foreach($result as $data){
					echo"<tr style='background-color:".posColor($n)."; color:#FFF;'>
					<td align='center' style='border-top:0px;'>$n</td><td style='border-top:0px;'>".$data['first_name']."</td><td align='right' style='border-top:0px;'>".number_format($data['sale_amount'],2)."</td>
					</tr>";
					$n++;
				}
				?>
					</table>		
                    </div>	
            </div>
        </div>
    </div>
    <div class='col-sm-3'>
    	<div class='panel panel-primary'>
        	<div class='panel-heading'>
            	<h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>เมื่อวานนี้</h4>
            </div>
            <div class='panel-body'>
           <div class='row' style='margin-top:-15px; margin-bottom:-15px;'>
                <table class='table' style='margin-bottom:0px;'>
                <?php
				$result = $sale->LeaderBoard("yesterday");
				$n = 1;
				foreach($result as $data){
					echo"<tr style='background-color:".posColor($n)."; color:#FFF;'>
					<td align='center' style='border-top:0px;'>$n</td><td style='border-top:0px;'>".$data['first_name']."</td><td align='right' style='border-top:0px;'>".number_format($data['sale_amount'],2)."</td>
					</tr>";
					$n++;
				}
				?>
					</table>		
                    </div>	
            </div>
        </div>
    </div>
    <div class='col-sm-3'>
    	<div class='panel panel-primary'>
        	<div class='panel-heading'>
            	<h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>สัปดาห์นี้</h4>
            </div>
            <div class='panel-body'>
           <div class='row' style='margin-top:-15px; margin-bottom:-15px;'>
                <table class='table' style='margin-bottom:0px;'>
                <?php
				$result = $sale->LeaderBoard("this_week");
				$n = 1;
				foreach($result as $data){
					echo"<tr style='background-color:".posColor($n)."; color:#FFF;'>
					<td align='center' style='border-top:0px;'>$n</td><td style='border-top:0px;'>".$data['first_name']."</td><td align='right' style='border-top:0px;'>".number_format($data['sale_amount'],2)."</td>
					</tr>";
					$n++;
				}
				?>
					</table>		
                    </div>	
            </div>
        </div>
    </div>
    <div class='col-sm-3'>
    	<div class='panel panel-primary'>
        	<div class='panel-heading'>
            	<h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>สัปดาห์ที่แล้ว</h4>
            </div>
            <div class='panel-body'>
           <div class='row' style='margin-top:-15px; margin-bottom:-15px;'>
                <table class='table' style='margin-bottom:0px;'>
                <?php
				$result = $sale->LeaderBoard("last_week");
				$n = 1;
				foreach($result as $data){
					echo"<tr style='background-color:".posColor($n)."; color:#FFF;'>
					<td align='center' style='border-top:0px;'>$n</td><td style='border-top:0px;'>".$data['first_name']."</td><td align='right' style='border-top:0px;'>".number_format($data['sale_amount'],2)."</td>
					</tr>";
					$n++;
				}
				?>
					</table>		
                    </div>	
            </div>
        </div>
    </div>
	<div class='col-sm-3'>
    	<div class='panel panel-primary'>
        	<div class='panel-heading'>
            	<h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>เดือนนี้</h4>
            </div>
            <div class='panel-body'>
           <div class='row' style='margin-top:-15px; margin-bottom:-15px;'>
                <table class='table' style='margin-bottom:0px;'>
                <?php
				$result = $sale->LeaderBoard("this_month");
				$n = 1;
				foreach($result as $data){
					echo"<tr style='background-color:".posColor($n)."; color:#FFF;'>
					<td align='center' style='border-top:0px;'>$n</td><td style='border-top:0px;'>".$data['first_name']."</td><td align='right' style='border-top:0px;'>".number_format($data['sale_amount'],2)."</td>
					</tr>";
					$n++;
				}
				?>
					</table>		
                    </div>	
            </div>
        </div>
    </div>
    <div class='col-sm-3'>
    	<div class='panel panel-primary'>
        	<div class='panel-heading'>
            	<h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>เดือนที่แล้ว</h4>
            </div>
            <div class='panel-body'>
           <div class='row' style='margin-top:-15px; margin-bottom:-15px;'>
                <table class='table' style='margin-bottom:0px;'>
                <?php
				$result = $sale->LeaderBoard("last_month");
				$n = 1;
				foreach($result as $data){
					echo"<tr style='background-color:".posColor($n)."; color:#FFF;'>
					<td align='center' style='border-top:0px;'>$n</td><td style='border-top:0px;'>".$data['first_name']."</td><td align='right' style='border-top:0px;'>".number_format($data['sale_amount'],2)."</td>
					</tr>";
					$n++;
				}
				?>
					</table>		
                    </div>	
            </div>
        </div>
    </div>
    <div class='col-sm-3'>
    	<div class='panel panel-primary'>
        	<div class='panel-heading'>
            	<h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ปีนี้</h4>
            </div>
            <div class='panel-body'>
           <div class='row' style='margin-top:-15px; margin-bottom:-15px;'>
                <table class='table' style='margin-bottom:0px;'>
                <?php
				$result = $sale->LeaderBoard("this_year");
				$n = 1;
				foreach($result as $data){
					echo"<tr style='background-color:".posColor($n)."; color:#FFF;'>
					<td align='center' style='border-top:0px;'>$n</td><td style='border-top:0px;'>".$data['first_name']."</td><td align='right' style='border-top:0px;'>".number_format($data['sale_amount'],2)."</td>
					</tr>";
					$n++;
				}
				?>
					</table>		
                    </div>	
            </div>
        </div>
    </div>
    <div class='col-sm-3'>
    	<div class='panel panel-primary'>
        	<div class='panel-heading'>
            	<h4 style='color:#FFF; margin-top:5px; margin-bottom:0px; text-align:center;'>ปีที่แล้ว</h4>
            </div>
            <div class='panel-body'>
           <div class='row' style='margin-top:-15px; margin-bottom:-15px;'>
                <table class='table' style='margin-bottom:0px;'>
                <?php
				$result = $sale->LeaderBoard("last_year");
				$n = 1;
				foreach($result as $data){
					echo"<tr style='background-color:".posColor($n)."; color:#FFF;'>
					<td align='center' style='border-top:0px;'>$n</td><td style='border-top:0px;'>".$data['first_name']."</td><td align='right' style='border-top:0px;'>".number_format($data['sale_amount'],2)."</td>
					</tr>";
					$n++;
				}
				?>
					</table>		
                    </div>	
            </div>
        </div>
    </div>
</div>
 </div>

