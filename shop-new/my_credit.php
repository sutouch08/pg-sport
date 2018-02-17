<link href='assets/css/jquery.minimalect.min.css' rel='stylesheet'>
<?php
require_once('../invent/function/tools.php');
$id_customer = $_COOKIE['id_customer'];
$customer = new customer($id_customer);
echo "
<div class='container main-container headerOffset'>
  <div class='row'>
    <div class='breadcrumbDiv col-lg-12'>
      <ul class='breadcrumb'>
        <li><a href='index.php'>Home</a> </li>
        <li><a href='index.php?content=account'>บัญชีของฉัน</a> </li>
        <li class='active'> เครดิต </li>
      </ul>
    </div>
  </div><!--/.row-->
  
  
  <div class='row'>
  
    <div class='col-lg-9 col-md-9 col-sm-7'>
      <h1 class='section-title-inner'><span><i class='fa fa-credit-card'></i> เครดิต </span></h1>
      
      
      <div class='row userInfo'>
      
        <div class='col-lg-12'>
         <table class='table' style='width:100%; padding:10px; border: 1px solid #ccc;'>
		<tr><input type='hidden' id='id_customer' value='".$customer->id_customer."' />
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ชื่อ :&nbsp; ".$customer->full_name."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตเทอม : &nbsp;".$customer->credit_term."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>อีเมล์ :&nbsp;".$customer->email."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วงเงินเครดิต :&nbsp;".number_format($customer->credit_amount,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>อายุ :&nbsp;"; if($customer->birthday !="0000-00-00"){ echo round(dateDiff($customer->birthday,date('Y-m-d'))/365) ." &nbsp;( ". thaiTextDate($customer->birthday).")" ;}else{echo "-";} echo"</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตใช้ไป :&nbsp;".number_format($customer->credit_used,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เพศ : &nbsp;"; if($customer->id_gender==1){ echo"ไม่ระบุ";}else if($customer->id_gender==2){echo"ชาย";}else{echo"หญิง";} echo"</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>เครดิตคงเหลือ : &nbsp;".number_format($customer->credit_balance,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>วันที่เป็นสมาชิก :&nbsp;".thaiTextDate($customer->date_add)."</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ยอดเงินตั้งแต่เป็นสมาชิก : &nbsp;".number_format($customer->total_spent,2)."</td>
		</tr><tr>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>&nbsp;</td>
		<td style='width:50%; padding-left:10px; padding-top:10px; padding-bottom:10px; vertical-align:middle;'>ออเดอร์ตั้งแต่เป็นสมาชิก : &nbsp;".$customer->total_order_place."</td></tr>
		</table>
		 
		  
        </div><!--/.w100-->      
        <div class='col-lg-12 clearfix'>
          <ul class='pager'>
            <li class='previous pull-right'><a href='index.php'> <i class='fa fa-home'></i> หน้าหลัก </a></li>
            <li class='next pull-left'><a href='index.php?content=account'>&larr; กลับไปที่บัญชีของฉัน</a></li>
          </ul>
        </div>
        
      </div> <!--/row end--> 
    </div>
    
    <div class='col-lg-3 col-md-3 col-sm-5'> </div>
    
  </div> <!--/row-->
  
  <div style='clear:both'></div>
</div> <!-- /.main-container -->";
