function saveReceived(){
  var id = $('#id_receive_product').val();
  if(id === '' || id == 0){
    swal('ไม่พบเลขที่เอกสาร');
    return false;
  }

  load_in();
  $.ajax({
    url:'controller/receiveProductController.php?save_add',
    type:'POST',
    cache:false,
    data:{'id_receive_product' : id},
    success:function(rs){
      load_out();
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          text:'รับเข้าเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          viewDetail(id);
        }, 1500);
      }else{
        swal({
          title:'Error!!',
          text:'บันทีกรับเข้าไม่สำเร็จ',
          type:'error'
        });
      }
    }
  });
}
