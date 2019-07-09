function changeZone(){
  $('#id_zone').val('');
  $('#zone-code').val('');
  $('#zone-box').val('');
  $('#zone-code').removeAttr('disabled');
  $('#zone-box').removeAttr('disabled');
  $('#btn-save').attr('disabled', 'disabled');
  $('#btn-change-zone').attr('disabled', 'disabled');
  $('#zone-code').focus();
}


$('#zone-box').autocomplete({
  source:'controller/autoComplete.php?get_zone_name',
  autoFocus:true,
  close:function(event, ui){
    let data = $(this).val();
    let arr = data.split(' : ');
    if(arr.length === 2){
      $('#id_zone').val(arr[0]);
      $(this).val(arr[1]);
      $(this).attr('disabled', 'disabled');
      $('#zone-code').attr('disabled', 'disabled');
      $('#btn-save').removeAttr('disabled');
      $('#btn-change-zone').removeAttr('disabled');
    }
  }
});


$('#zone-code').keyup(function(e){
  if(e.keyCode == 13){
    let barcode = $(this).val();
    if(barcode.length > 0){
      getZone(barcode);
    }else{
      $('#zone-box').focus();
    }
  }
});



function getZone(barcode){
  $.ajax({

    url: 'controller/receiveProductController.php?get_zone',
    type:"POST",
    cache:false,
    data:{ "barcode" : barcode },
    success:function(rs){
      rs = $.trim(rs);
      if(rs === 'fail'){
        swal("ไม่พบโซนที่ระบุ", "โปรดตรวจสอบความถูกต้องของบาร์โค้ด หรือ ภาษาที่ใช้เป็นภาษาอังกฤษ", "error");
        return false;
      }else{
        let arr = rs.split(' : ');
        if(!isNaN(parseInt(arr[0]))){
          $('#id_zone').val(arr[0]);
          $('#zone-box').val(arr[1]);
          $('#zone-code').attr('disabled','disabled');
          $('#zone-box').attr('disabled', 'disabled');
          $('#btn-save').removeAttr('disabled');
          $('#btn-change-zone').removeAttr('disabled');
        }
      }
    }
  });
}
