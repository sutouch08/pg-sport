<?php 
	$page_menu = "invent_order";
	$page_name = "เบิกสินค้าไปขายงานอีเว้นท์";
	$id_tab = 39;
	$id_profile = $_COOKIE['profile_id'];
	$pm = checkAccess($id_profile, $id_tab);
	$view = $pm['view'];
	$add = $pm['add'];
	$edit = $pm['edit'];
	$delete = $pm['delete'];
	accessDeny($view);
	include "function/event_helper.php";
	$btn_back = "<button type='button' class='btn btn-warning btn-sm' onclick='go_back()'><i class='fa fa-arrow-left'></i> กลับ</button>";
	$btn = "";
	if( isset($_GET['add']) )
	{
		$btn .= $btn_back;	
		if( isset($_GET['id_order_event']) )
		{
			$btn .= "<button type='button' class='btn btn-success btn-sm' onclick='save_order()'><i class='fa fa-save'></i> บันทึก</button>";	
		}
	}
	else if( isset($_GET['edit']) && isset($_GET['id_order_event']) )
	{
		
	}
	else
	{
		if( $add ){ $btn .= "<button class='btn btn-success btn-sm' onclick='new_add()'><i class='fa fa-plus'></i> เพิ่มใหม่</button>"; }
	}
	?>
    
<div class="container">
<style>
	.center {
		margin-left: auto;
		margin-right: auto;
	}
</style>
<!-- page place holder -->
<div class="row">
<div class="col-lg-8"><h3 class="title"><i class="fa fa-trophy"></i>&nbsp; <?php echo $page_name; ?></h3></div>
<div class="col-lg-4"><p class="pull-right"><?php echo $btn; ?></p></div>
</div>
<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:10px;' />
<?php if( isset( $_GET['add'] ) ) : ?>
    <?php if( !isset( $_GET['id_order_event']) ) : ?>
<!-----------------------------------------  ADD  ------------------------------------------->

<div class="row">

    <div class="col-lg-2">
    	<label>วันที่</label>
        <input type="text" class="form-control input-sm" value="<?php echo date("d-m-Y"); ?>" style="text-align:center;" disabled  />
    </div>
    <div class="col-lg-3">
    	<label>ผู้เบิก</label>
        <input type="text" class="form-control input-sm" style="text-align:center" value="<?php echo employee_name($_COOKIE['user_id']); ?>" disabled  />
    </div>
    <div class="col-lg-3">
    	<label>ชื่องาน</label>
        <input type="text" class="form-control input-sm" id="event_name" />
    </div>
    <div class="col-lg-3">
    	<label>หมายเหตุ</label>
        <input type="text" class="form-control input-sm" id="remark" />
    </div>
    <div class="col-lg-1">
    	<label style="visibility:hidden; display:block;">x</label>
    	<button type="button" class="btn btn-success btn-sm" onclick="add_new()"><i class="fa fa-plus"></i>&nbsp; เพิ่ม</button>
	</div> 
</div>  
<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' /> 
	<?php elseif( isset($_GET['id_order_event']) ) : ?>
    <?php 	$ev = new event($_GET['id_order_event']); ?>
    <?php 	$id_order = $ev->id_order; ?>
    <?php 	$order 		= new order($id_order); ?>
    <div class="row">
        <div class="col-lg-2">
            <label>เอกสาร</label>
            <input type="text" class="form-control input-sm" value="<?php echo $order->reference; ?>" style="text-align:center;" disabled  />
        </div>
        <div class="col-lg-2">
            <label>วันที่</label>
            <input type="text" class="form-control input-sm" value="<?php echo thaiDate($order->date_add); ?>" style="text-align:center;" disabled  />
        </div>
        <div class="col-lg-4">
            <label>ผู้เบิก</label>
            <input type="text" class="form-control input-sm" style="text-align:center" value="<?php echo employee_name($order->id_employee); ?>" disabled  />
        </div>
        <div class="col-lg-4">
            <label>ชื่องาน</label>
            <input type="text" class="form-control input-sm" id="event_name" value="<?php echo event_name($order->id_order); ?>" disabled />
        </div>
        <div class="col-lg-11">
            <label>หมายเหตุ</label>
            <input type="text" class="form-control input-sm" id="remark" value="<?php echo $order->comment; ?>" disabled />
        </div>
        <div class="col-lg-1">
            <label style="visibility:hidden; display:block;">x</label>
            <button type="button" class="btn btn-warning btn-sm" id="btn_edit" onclick="edit_order()"><i class="fa fa-pencil"></i>&nbsp; แก้ไข</button>
            <button type="button" class="btn btn-success btn-sm" id="btn_update" style="display:none;" onclick="update_order()"><i class="fa fa-save"></i>&nbsp; บันทึก</button>
        </div> 
        <input type="hidden" id="id_order" value="<?php echo $order->id_order; ?>"  />
        <input type="hidden" id="id_order_event" value="<?php echo $ev->id_order_event; ?>"  />
        <input type="hidden" id="id_zone" value="<?php echo $ev->id_zone; ?>"  />
	</div>  
	<hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
    <div class="row">
    <?php 
		$qs = "SELECT tbl_product.id_product FROM tbl_stock JOIN tbl_product_attribute ON tbl_stock.id_product_attribute = tbl_product_attribute.id_product_attribute ";
		$qs .= "JOIN tbl_product ON tbl_product_attribute.id_product = tbl_product.id_product ";
		$qs .= "WHERE tbl_stock.id_zone = ".$ev->id_zone." GROUP BY tbl_product.id_product ORDER BY tbl_product.product_code ASC";
		$qs = dbQuery($qs);	
	?>
    <?php if(dbNumRows($qs) > 0 ) : ?>
    <?php 	while($rs = dbFetchArray($qs) ) : ?>
    <?php 		$product = new product(); ?>
    <?php 		$product->product_detail($rs['id_product']); ?>
    	<div class="col-lg-1 col-md-1 col-sm-3 col-xs-4" style="text-align:center; padding:5px;"> <a href="#" onclick="getData(<?php echo $rs['id_product']; ?>, '<?php echo $product->product_code; ?>')">
			<div class="product">
				<div class="image">
                
                	<?php echo $product->getCoverImage($rs['id_product'],1,"img-responsive center"); ?>
   
                </div>
				<div class="description" style="font-size:10px; min-height:50px;">
               
				<?php echo $product->product_code. "<br/>".number_format($product->product_price); ?> : <span style='color:red'> <?php echo number_format(available_qty($rs['id_product'], $ev->id_zone, $order->id_order)); ?></span>
               </div>
			</div> </a>
     	</div>
    <?php endwhile; ?>
    <?php else : ?>
   	<div class="col-lg-12">
    	<h4><center>-----  ไม่มีสินค้าในโซน  -----</center></h4>
    </div>
    <?php endif; ?>
    </div>
    <!-------------------------------  Order grid  ----------------------------->
   <form id="order_form" method="post">
	<div class='modal fade' id='order_grid' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
		<div class='modal-dialog' id='modal'>
			<div class='modal-content'>
	  			<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
					<h4 class='modal-title' id='modal_title'>title</h4>
                    <input type='hidden' name='id_order' value='<?php echo $id_order; ?>'/>
				 </div>
				 <div class='modal-body' id='modal_body'></div>
				 <div class='modal-footer'>
					<button type='button' class='btn btn-default' data-dismiss='modal'>ปิด</button>
					<button type='button' class='btn btn-primary' onclick="add_to_order()">เพิ่มในรายการ</button>
				 </div>
			</div>
		</div>
	</div>
</form>
    <hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />
<div class="row">
<div class="col-lg-12">
	<table class="table table-striped">
    <thead style="font-size:14px;">
    	<th style="width: 5%; text-align:center;">ลำดับ</th>
        <th style="width: 10%;">บาร์โค้ด</th>
        <th style="width: 15%;">รหัสสินค้า</th>
        <th style="width: 25%;">ชื่อสินค้า</th>
        <th style="width: 10%; text-align:right;">ราคา</th>
        <th style="width: 10%; text-align:right;">จำนวน</th>     
        <th style="width: 15%; text-align:right;">มูลค่า</th>   
        <th style="width: 10%; text-align:right;">actions</th>
    </thead>
    <tbody id="result">
    <?php $qrs = $ev->get_detail_data($id_order);	?>
    <?php if(dbNumRows($qrs) > 0 ) : ?>
    <?php $n = 1; ?>
    <?php $total_qty = 0; $total_amount = 0; ?>
    <?php 	while($rs = dbFetchArray($qrs) ) : ?>
    <?php 		$id = $rs['id_order_detail']; ?>
    	<tr id="row_<?php echo $id; ?>" style="font-size:12px;">
        	<td align="center"><?php echo $n; ?></td>
            <td><?php echo $rs['barcode']; ?></td>
            <td><?php echo $rs['product_reference']; ?></td>
            <td><?php echo $rs['product_name']; ?></td>
            <td align="right"><?php echo number_format($rs['product_price'], 2); ?></td>
            <td align="right"><?php echo number_format($rs['product_qty']); ?></td>
            <td align="right"><?php echo number_format($rs['total_amount'], 2); ?></td>
            <td align="right">
            	<?php if($ev->status != 2 ) : ?>
            	<button type="button" class="btn btn-danger btn-xs" onclick="delete_row(<?php echo $id; ?>, '<?php echo $rs['product_reference']; ?>')" ><i class="fa fa-trash"></i></button>
                <?php endif; ?>
            </td>
        </tr>
	<?php $n++; $total_qty += $rs['product_qty']; $total_amount += $rs['total_amount']; ?>
    <?php 	endwhile; ?>
    <tr style="font-size: 16px;"><td align="right" colspan="5">รวม</td><td align="right"><?php echo number_format($total_qty); ?></td><td align="right"><?php echo number_format($total_amount, 2); ?></td><td></td></tr>
    <?php endif; ?>
    </tbody>
    </table>
</div><!-- row -->
    <?php endif; ?>
<!--------------------------------------- End Add ------------------------------------------>

<?php elseif( isset( $_GET['edit'] ) ) : ?>
<!-----------------------------------------  Edit  ------------------------------------------->

					
<!--------------------------------------- End Edit ------------------------------------------>

<?php elseif( isset( $_GET['view_detail'] ) && isset( $_GET['id_order'] ) ) : ?>
<!-----------------------------------------  Detail  ------------------------------------------->


<!--------------------------------------- End Detail ------------------------------------------>

<?php else : ?>
<!-----------------------------------------  List  ------------------------------------------->
		<!---------------------- Filter  -------------------------->
 <?php 
 	if(isset($_POST['from_date']) && $_POST['from_date'] != ""){ setcookie("event_from_date", dbDate($_POST['from_date']), time() +3600, "/"); }else{ setcookie("event_from_date", "", time()+3600, "/"); }
	if(isset($_POST['to_date']) && $_POST['to_date'] != ""){ setcookie("event_to_date", dbDate($_POST['to_date']), time()+3600, "/"); }else{ setcookie("event_to_date", "", time()+3600, "/"); }
	?>
<form  method='post' id='form'>
<div class="row">
	<div class="col-lg-2">
    	<label>เงื่อนไข</label>
        <select class="form-control input-sm" id="filter" name="filter">
        	<option value="event_name" <?php if(isset($_POST['filter'])){ echo isSelected($_POST['filter'], "event_name"); }else if(isset($_COOKIE['event_filter'])){ echo isSelected($_COOKIE['event_filter'], "event_name"); } ?> >ชื่องาน</option>
            <option value="event_sale" <?php if(isset($_POST['filter'])){ echo isSelected($_POST['filter'], "event_sale"); }else if(isset($_COOKIE['event_filter'])){ echo isSelected($_COOKIE['event_filter'], "event_sale"); } ?>>ชื่อผู้เบิก</option>
            <option value="reference" <?php if(isset($_POST['filter'])){ echo isSelected($_POST['filter'], "reference"); }else if(isset($_COOKIE['event_filter'])){ echo isSelected($_COOKIE['event_filter'], "reference"); } ?>>เลขที่เอกสาร</option>
        </select>
    </div>
    <div class="col-lg-3">
    	<label>คำค้น</label>
         <?php 
			$value = '' ; 
			if(isset($_POST['search_text'])) : 
				$value = $_POST['search_text']; 
			elseif(isset($_COOKIE['event_search_text'])) : 
				$value = $_COOKIE['event_search_text']; 
			endif; 
		?>
        <input type="text" id="search_text" name="search_text" class="form-control input-sm" placeholder="ระบุคำที่ต้องการค้นหา" value="<?php echo $value; ?>" />
    </div>
    <div class='col-lg-2'>
		<label>จากวันที่</label>
            <?php 
				$value = ""; 
				if(isset($_POST['from_date']) && $_POST['from_date'] != "") : 
					$value = date("d-m-Y", strtotime($_POST['from_date'])); 
				elseif( isset($_COOKIE['event_from_date'])) : 
					$value = date("d-m-Y", strtotime($_COOKIE['event_from_date'])); 
				endif; 
				?>
			<input type='text' class='form-control' name='from_date' id='from_date' placeholder="ระบุวันที่" style="text-align:center;"  value='<?php echo $value; ?>'/>
	</div>	
	<div class='col-lg-2'>
		<label>ถึงวันที่</label>
            <?php
				$value = "";
				if( isset($_POST['to_date']) && $_POST['to_date'] != "" ) :
				 	$value = date("d-m-Y", strtotime($_POST['to_date'])); 
				 elseif( isset($_COOKIE['event_to_date']) ) :
					$value = date("d-m-Y", strtotime($_COOKIE['event_to_date']));
				 endif;
			?>  
			<input type='test' class='form-control'  name='to_date' id='to_date' placeholder="ระบุวันที่" style="text-align:center" value='<?php echo $value; ?>' />
	</div>
	<div class='col-lg-2 col-md-2 col-sm-2 col-sx-2'>
    	<label style="visibility:hidden">show</label>
		<button class='btn btn-primary btn-block' id='search-btn' type='submit' onclick="load_in()" ><i class="fa fa-search"></i>&nbsp;ค้นหา</button>
	</div>	
	<div class='col-lg-1 col-md-1 col-sm-1 col-sx-1'>
    	<label style="visibility:hidden">show</label>
		<button type='button' class='btn btn-danger' onclick="clear_filter()"><i class='fa fa-refresh'></i>&nbsp;reset</button>
	</div>    
</div>
</form>
		<!---------------------- End Filter  -------------------------->
 <hr style='border-color:#CCC; margin-top: 10px; margin-bottom:10px;' />    
 <?php 
		if(isset($_POST['from_date']) && $_POST['from_date'] != ""){$from = date('Y-m-d',strtotime($_POST['from_date'])); }else if( isset($_COOKIE['event_from_date'])){ $from = date('Y-m-d',strtotime($_COOKIE['event_from_date'])); }else{ $from = "";} 
		if(isset($_POST['to_date']) && $_POST['to_date'] != ""){ $to =date('Y-m-d',strtotime($_POST['to_date']));  }else if(  isset($_COOKIE['event_to_date'])){  $to =date('Y-m-d',strtotime($_COOKIE['event_to_date'])); }else{ $to = "";}
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
 
 /****  เงื่อนไขการแสดงผล *****/
		if(isset($_POST['search_text'])/* && $_POST['search_text'] !="" */) :
			$text = $_POST['search_text'];
			$filter = $_POST['filter'];
			setcookie("event_search_text", $text, time() + 3600, "/");
			setcookie("event_filter",$filter, time() +3600,"/");
		elseif(isset($_COOKIE['event_search_text']) && isset($_COOKIE['event_filter'])) :
			$text = $_COOKIE['event_search_text'];
			$filter = $_COOKIE['event_filter'];
		else : 
			$text	= "";
			$filter	= "";
		endif;
		$where = "JOIN tbl_order_event ON tbl_order.id_order = tbl_order_event.id_order WHERE role = 8 AND order_status = 1 ";
		if( $text != "" ) :
			switch( $filter) :				
				case "event_name" :
					$where = "AND event_name LIKE'%".$text."%'";
				break;
				case "event_sale" :
					$in_cause	= "";
					$qs 			= dbQuery("SELECT id_event_sale FROM tbl_event_sale JOIN tbl_employee ON tbl_event_sale.id_employee = tbl_employee.id_employee WHERE first_name LIKE'%".$text."%' OR last_name LIKE'%".$text."%'");
					$rs 			= dbNumRows($qs);
					$i 				= 0;
					if($rs > 0 )
					{
						while($i < $rs)
						{
							list($in) 		= dbFetchArray($qs);
							$in_cause 	.="$in";
							$i++;
							if($i < $rs){ $in_cause .=","; 	}
						}
						$where = "AND id_event_sale IN(".$in_cause.")";	
					}
					else
					{
						$where = "AND id_event_sale = 0";	
					}
				break;
				case "reference" :
				$where .= "AND reference LIKE'%$text%'";
				break;
			endswitch;
			if($from != "" && $to != "" ) : 
				$where .= " AND (tbl_order.date_add BETWEEN '".$from." 00:00:00' AND '".$to." 23:59:59')";  
			endif;
		else :
			if($from != "" && $to != "" ) : 
				$where .= "AND (tbl_order.date_add BETWEEN '".$from." 00:00:00' AND '".$to." 23:59:59')";  
			endif;	
		endif;
		$where .= " ORDER BY tbl_order.date_add DESC";
		
?>		
<?php
	$paginator = new paginator();
		if(isset($_POST['get_rows'])){$get_rows = $_POST['get_rows'];$paginator->setcookie_rows($get_rows);}else if(isset($_COOKIE['get_rows'])){$get_rows = $_COOKIE['get_rows'];}else{$get_rows = 50;}
		$paginator->Per_Page("tbl_order", $where, $get_rows);
		$paginator->display($get_rows,"index.php?content=order_event");
?>		
<div class="row">
	<div class="col-lg-12">
    <table class='table'>
    	<thead style='color:#FFF; background-color:#48CFAD;'>
        	<th style='width:5%; text-align:center;'>ID</th>
            <th style='width:10%;'>เลขที่อ้างอิง</th>
			<th style='width:30%;'>ชื่องาน</th>
            <th style='width:15%;'>พนักงาน</th>
			<th style='width:10%; text-align:center;'>สถานะ</th>
			<th style='width:10%; text-align:center;'>วันที่เพิ่ม</th>
            <th style='width:10%; text-align:center;'>วันที่ปรับปรุง</th>
            <th style='width:10%; text-align:right;'>actions</th>
        </thead>
	<?php $qs = dbQuery("SELECT * FROM tbl_order ".$where." LIMIT ".$paginator->Page_Start." ,". $paginator->Per_Page);	?>
    <?php if( dbNumRows($qs) > 0 ) : ?>
    <?php 	while($rs = dbFetchArray($qs) ) : ?>
		<tr style="color:#FFF; font-size:12px; background-color: <?php echo state_color($rs['current_state']); ?>;"> 
        	<td align="center"><?php echo $rs['id_order']; ?></td>
            <td><?php echo $rs['reference']; ?></td>
            <td><?php echo $rs['event_name']; ?></td>
            <td><?php echo event_sale_name($rs['id_event_sale']); ?></td>
            <td align="center"><?php echo state_name($rs['current_state']); ?></td>
            <td align="center"><?php echo thaiDate($rs['date_add']); ?></td>
            <td align="center"><?php echo thaiDate($rs['date_upd']); ?></td>
            <td align="right">
            
            </td>
        </tr>
    <?php 	endwhile; ?>
    <?php else : ?>
    
    <?php endif; ?>
    </div>
</div>

<!--------------------------------------- End List ------------------------------------------>

<?php endif; ?>
<input type="hidden" id="id_employee" value="<?php echo $_COOKIE['user_id']; ?>"  />
</div><!-- container -->  

<script id="template" type="text/x-handlebars-template">
{{#each this}}
	{{#if @last}}
		<tr style="font-size:16px;">
        	<td colspan="5" align="right">รวม</td>
            <td align="right">{{ qty }}</td>
            <td align="right">{{ amount }}</td>
            <td align="right"></td>
     	</tr>
	{{else}}
		<tr id="row_{{ id }}" style="font-size:12px;">
				<td align="center">{{ no }}</td>
				<td>{{ barcode }}</td>
				<td>{{ reference }}</td>
				<td>{{ product_name }}</td>
				<td align="right">{{ price }}</td>
				<td align="right">{{ qty }}</td>
				<td align="right">{{ amount }}</td>
				<td align="right"><button type="button" class="btn btn-danger btn-xs" onclick="delete_row({{ id }}, '{{ reference }}')" ><i class="fa fa-trash"></i></button></td>
		 </tr>
	{{/if}}
{{/each}}		
</script>		
<script>

function save_order()
{
	var id_order 	= $("#id_order").val();
	if(id_order == ""){ swal("ไม่พบ Order ID "); return false; }
	load_in();
	$.ajax({
		url:"controller/eventController.php?save_order",
		type:"POST", cache:"false", data: { "id_order" : id_order },
		success: function(rs)
		{
			load_out();
			var rs = $.trim(rs);
			if(rs == "success")
			{
				window.location.href = "index.php?content=order_event";	
			}
			else
			{
				swal("บันทึกออเดอร์ไม่สำเร็จ");
			}
		}
	});
}

function delete_row(id, ref)
{
	swal({
		title : "ยืนยันการลบ",
		text : "คุณกำลังจะลบ "+ref+" ออกจากรายการ	ใช่หรือไม่ ?",
		type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "ใช่ ลบเลย",
		  cancelButtonText: "ยกเลิก",
		  closeOnConfirm: false,
		  html: true
		}, function(){
			$.ajax({
				url:"controller/eventController.php?delete_item",
				type:"POST", cache:"false", data: { "id_order_detail" : id },
				success: function(rs)
				{
					var rs = $.trim(rs);
					if(rs == "success")
					{
						$("#row_"+id).remove();
						swal({ title: "สำเร็จ", text: "ลบ "+ref+" ออกจากรายการเรียบร้อยแล้ว", timer: 1000, type: "success"});
					}else{
						swal("ไม่สำเร็จ", "ลบ "+ref+" ไม่สำเร็จ", "error");
					}
				}
			});						
	});	
}


function getData(id_product, product_code){
	var id_order 	= $("#id_order").val();
	var id_zone 	= $("#id_zone").val();
	$.ajax({
		url:"controller/eventController.php?getData",
		type:"POST", cache:false, data: { "id_product" : id_product, "id_zone" : id_zone, "id_order" : id_order, "product_code" : product_code},
		success: function(dataset){
			if(dataset !=""){
				var arr = dataset.split("|");
				var data = arr[0];
				var table_w = arr[1];
				var title = arr[2];
				$("#modal").css("width",table_w+"px");
				$("#modal_title").html(title);
				$("#modal_body").html(data);
				$("#order_grid").modal("show");
			}else{
				alert("NO DATA");
			}		
		}
	});
}

function add_to_order()
{
	var id_zone  = $("#id_zone").val();
	$("#order_grid").modal("hide");
	load_in();
	$.ajax({
		url:"controller/eventController.php?add_to_order&id_zone="+id_zone,
		type:"POST", cache: "false", data: $("#order_form").serialize(),
		success: function(rs)
		{
			load_out();
			var rs = $.trim(rs);
			if(rs != "fail")
			{
				var source 	= $("#template").html();
				var data		= $.parseJSON(rs);
				var output	= $("#result");
				render(source, data, output);
			}
			else
			{
				swal("Error!!", "ไม่สามารถเพิ่มรายการได้", "error");	
			}
		}
	});	
}

function edit_order()
{
	$("#event_name").removeAttr("disabled");
	$("#remark").removeAttr("disabled");
	$("#btn_edit").css("display" , "none");
	$("#btn_update").css("display", "");
}

function updated()
{
	$("#event_name").attr("disabled", "disabled");
	$("#remark").attr("disabled", "disabled");
	$("#btn_edit").css("display" , "");
	$("#btn_update").css("display", "none");
}

function update_order()
{
	var id_order = $("#id_order").val();
	var id_order_event = $("#id_order_event").val();
	var event_name = $("#event_name").val();
	var remark				= $("#remark").val();
	load_in();
	$.ajax({
		url:"controller/eventController.php?update_order",
		type: "POST", cache: "false", data: { "id_order" : id_order, "id_order_event" : id_order_event, "event_name" : event_name, "remark" : remark },
		success: function(rs)
		{
			load_out();
			var rs = $.trim(rs);
			if(rs == "ok")
			{
				updated();
				swal({title: "Success", text: "Updated successfully", timer: 1000, type: "success"});
			}
			else
			{
				swal("Fail !!", "Updated fail", "error");
			}
		}
	});
}

function go_back()
{
	window.location.href = "index.php?content=order_event";	
}

function add_new()  /// เพิ่มออเดอร์ใหม่
{
	var id_employee 	= $("#id_employee").val();
	var event_name 	= $("#event_name").val();
	var remark			= $("#remark").val();
	load_in();
	$.ajax({
		url:"controller/eventController.php?new_order",
		data: { "id_employee" : id_employee, "event_name" : event_name, "remark" : remark }, type: "POST", cache: "false",
		success: function(rs)
		{
			var rs = $.trim(rs);
			if(rs != "error")
			{
				window.location.href = "index.php?content=order_event&add=y&id_order_event="+rs;
				load_out();
			}else{
				load_out();
				swal("Error !!", "ไม่สามารถเพิ่มรายการได้", "error");
			}
		}
	});
}


function new_add()  /// ตรวจสอบเงื่อนไขก่อนอนุญาติให้เพิ่มออเดอร์ใหม่
{
	var id_employee = $("#id_employee").val();
	$.ajax({
		url:"controller/eventController.php?check_event_user",
		type: "POST", cache: "false", data: { "id_employee" : id_employee },
		success: function(rs){
			var rs = $.trim(rs);
			if(rs == "not_found")		// กรณีที่ไม่ได้เป็นพนักงานอีเว้นท์ไม่อนุญาติให้เบิก
			{
				swal("Error !!", "คุณไม่ได้รับอนุญาติให้เบิกสินค้าเพื่อไปขายงานอีเว้นท์", "error");
			}else if( rs == "not_clear"){		/// กรณีที่เบิกไปครั้งก่อนยังไม่ได้มาเคลียร์ไม่อนุญาติให้เบิกเพิ่ม
				swal("Error !!", "คุณยังไม่ได้เคลียร์รายการเบิกครั้งก่อน ไม่อนุญาติให้เบิกเพิ่ม", "error");
			}else if( rs == "ok"){
				window.location.href = "index.php?content=order_event&add=y";
			}
		}
	});
}

$("#from_date").datepicker({
	dateFormat: "dd-mm-yy",
	onClose: function(selectedDate)
	{
		$("#to_date").datepicker("option", "minDate", selectedDate);
	}
});
$("#to_date").datepicker({
	dateFormat: "dd-mm-yy",
	onClose: function(selectedDate)
	{
		$("#from_date").datepicker("option", "maxDate", selectedDate);
	}
});
</script>