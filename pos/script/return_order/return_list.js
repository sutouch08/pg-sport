function cancleReturn(id, code){
  swal({
    title:'คุณแน่ใจหรือ ?',
    text:'ต้องการยกเลิกเอกสาร '+code+' หรือไม่ ?',
    showCancelButton:true,
    confirmButtonColor:'#FA5858',
    confirmButtonText:'ใช่ ฉันต้องการ',
    cancelButtonText:'ไม่ต้องการ',
    closeOnConfirm:false
  }, function(){

    $.ajax({
      url:'controller/returnOrderController.php?cancleReturn',
      type:'POST',
      cache:'false',
      data:{
        'id_return_order' : id
      },
      success:function(rs){
        rs = $.trim(rs);
        if(rs == 'success'){
          swal({
            title:'Deleted',
            type:'success',
            timer:1000
          });

          $('#td-'+id).text('CN');
          $('#btn-del-'+id).remove();
        }else{
          swal('Error!', rs, 'error');
        }
      }
    });
  });
}
