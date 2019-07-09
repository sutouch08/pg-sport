function clearFilter(){
  $.get('controller/receiveProductController.php?clear_filter', function(){
    goBack();
  });
}
