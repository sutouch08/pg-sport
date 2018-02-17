<div class="container">
<?php 
	if(isset($_GET['id_recieved_product'])){ $id_recieved_product = $_GET['id_recieved_product']; }else{ $id_recieved_product ="";}
	if(isset($_GET['date_add'])){ $date_add = dbDate($_GET['date_add']); }else{ $date_add = date('Y-m-d');}
	echo"
	  <form id='import_form' action='import_process.php?import=y&id_recieved_product=$id_recieved_product&date_add=$date_add'  method='post' enctype='multipart/form-data'>
        	<div class='row'><div class='col-xs-12'><h5>&nbsp;</h5></div></div>
            <div class='row'>
            	<div class='col-xs-3 col-xs-offset-4'>
                    	<input type='file' name='csv' id='csv' class='btn btn-success' />
                </div>
                <div class='col-xs-1'><button type='submit' id='import' class='btn btn-default'>ดำเนินการ</button></div>
               </div>
       
  </form>
  	<div class='row'><hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
 		<div class='col-xs-12' id='result'></div>
 	</div>
</div>";
?>

