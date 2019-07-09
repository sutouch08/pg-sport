$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
});


function getSearch(){
  var sCode     = $('#sCode').val();
  var sInvoice  = $('#sInvoice').val();
  var sPo       = $('#sPo').val();
  var sSup      = $('#sSup').val();
  var fromDate  = $('#fromDate').val();
  var toDate    = $('#toDate').val();

  if(sCode.length || sInvoice.length || sPo.length || sSup.length || (isDate(fromDate) && isDate(toDate)) ){
    $('#searchForm').submit();
  }

  return;
}


function goAdd(){
  window.location.href = "index.php?content=receive_product&add";
}


function viewDetail(id){
  window.location.href = "index.php?content=receive_product&view_detail&id_receive_product="+id;
}



function goEdit(id, id_zone){
  if(id_zone === undefined){
    window.location.href = "index.php?content=receive_product&edit=Y&id_receive_product="+id;
  }else{
    window.location.href = "index.php?content=receive_product&edit=Y&id_receive_product="+id+"&id_zone="+id_zone;
  }

}


function getDelete(id, reference){
  swal({
    title:'Are You Sure ?',
    text: 'ต้องการลบเอกสาร '+reference+'หรือไม่?',
    type:'warning',
    showCancelButton:true,
    confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่ ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: true
  }, function(){
    load_in();
    $.ajax({
      url:'controller/receiveProductController.php?delete_doc&id_receive_product='+id,
      type:'GET',
      cache:false,
      success:function(rs){
        var rs = $.trim(rs);
				if( rs == 'success' ){
          load_out();
					swal({
            title:'Deleted',
            text: 'ลบเอกสารเรียบร้อยแล้ว',
            type: 'success',
            timer: 1000
          });

					$("#row-"+id).remove();
				}else{
					swal("ข้อผิดพลาด", rs, "error");
				}
      }
    })
  });// swal
}


function goBack(){
  window.location.href = "index.php?content=receive_product";
}


function review(){
  var id = $('#id_receive_product').val();
  var id_zone = $('#id_zone').val();
  window.location.href = "index.php?content=receive_product&review=Y&id_receive_product="+id+"&id_zone="+id_zone;
}


function deleteRow(id){
  $('#row-'+id).remove();
  updateNo();
}


function printReceived(id)
{
  //--- properties for print
  var center    = ($(document).width() - 800)/2;
  var prop 			= "width=800, height=900. left="+center+", scrollbars=yes";
  var target = 'controller/receiveProductController.php?print&id_receive_product='+id;
	window.open(target, "_blank", prop);
}
