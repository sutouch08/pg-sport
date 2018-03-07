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


function toDay(date){
  $('#fromDate').val(date);
  $('#toDate').val(date);
}

function getReport(){
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();

  if(!isDate(fromDate) || ! isDate(toDate)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  load_in();
  $.ajax({
    url:'controller/saleReportController.php?saleByItem',
    type:'GET',
    cache:'false',
    data:{
      'fromDate' : fromDate,
      'toDate' : toDate,
      'report' : 'Y'
    },
    success:function(rs){
      load_out();
      if(isJson(rs)){
        var source = $('#template').html();
        var data = $.parseJSON(rs);
        var output = $('#result');
        render(source, data, output);
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}


function doExport(){
  var fromDate = $('#fromDate').val();
  var toDate = $('#toDate').val();
  
  if(!isDate(fromDate) || ! isDate(toDate)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  token = new Date().getTime();

  target = 'controller/saleReportController.php?saleByItem&export';
  target += '&fromDate='+fromDate;
  target += '&toDate='+toDate;
  target += '&token='+token;
  get_download(token);
  window.location.href = target;
}
