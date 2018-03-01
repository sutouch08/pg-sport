function goBack(){
  window.location.href = 'index.php?content=return_order';
}





function goAdd(id){
  window.location.href = 'index.php?content=return_order&add=Y';
}



function leave(){
  swal({
    title:'ยกเลิกข้อมูลนี้ ?',
    type:'warning',
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
  },function(){
    goBack();
  });
}



function viewDetail(id){
  window.location.href = 'index.php?content=return_order&view_detail&id_return_order='+id;
}
