function goBack(){
  window.location.href = 'index.php?content=stock_movement';
}





function getSearch(){
  $('#searchForm').submit();
}





function clearFilter(){
  $.get('controller/storeController.php?clearFilter&stock_movement', function(){ goBack();});
}





$('.search-box').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


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
