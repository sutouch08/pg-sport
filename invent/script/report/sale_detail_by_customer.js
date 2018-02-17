function getReport(){
  var fromDate = $('#fromDate').val();
  var toDate   = $('#toDate').val();
  var customer = $('#customer').val();
  var id_customer = $('#id_customer').val();

  if(!isDate(fromDate) || ! isDate(toDate)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }


  if( customer.length == 0 || id_customer == ''){
    swal('ชื่อลูกค้าไม่ถูกต้อง');
    return false;
  }


  load_in();
  $.ajax({
    url:'report/reportController/saleReportController.php?sale_detail_by_customer&report',
    type:'GET',
    cache:'false',
    data: {
      "fromDate" : fromDate,
      "toDate" : toDate,
      "id_customer" : id_customer
    },
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(isJson(rs)){
        var source = $('#report-template').html();
        var data = $.parseJSON(rs);
        var output = $('#result');
        render(source, data, output);
      }
    }
  });

}




function doExport()
{
  var fromDate = $('#fromDate').val();
  var toDate   = $('#toDate').val();
  var customer = $('#customer').val();
  var id_customer = $('#id_customer').val();

  if(!isDate(fromDate) || ! isDate(toDate)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }


  if( customer.length == 0 || id_customer == ''){
    swal('ชื่อลูกค้าไม่ถูกต้อง');
    return false;
  }

	var token = new Date().getTime();
	get_download(token);
	window.location.href="report/reportController/saleReportController.php?sale_detail_by_customer&export&fromDate="+fromDate+"&toDate="+toDate+"&id_customer="+id_customer+"&token="+token;
}




$('#customer').autocomplete({
  source:'controller/autoComplete.php?get_customer',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 3){
      var name = arr[1];
      var id = arr[2];
      $('#id_customer').val(id);
      $('#customer').val(name);
    }else{
      $('#customer').val('');
      $('#id_customer').val('');
    }
  }
})

$(document).ready(function() {

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

});
