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



$('#btn-some').click(function(event) {
  $('#rangeModal').modal('show');
});



function toggleOption(id){
  if( id == 1){
    $('#btn-all').removeClass('btn-primary');
    $('#btn-some').addClass('btn-primary');
  }else{
    $('#btn-some').removeClass('btn-primary');
    $('#btn-all').addClass('btn-primary');
  }

  $('#option').val(id);
}
