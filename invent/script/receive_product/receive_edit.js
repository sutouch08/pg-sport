function saveEdit(){
  var id = $('#id_receive_product').val();
  var id_po = $('#id_po').val();
  var id_zone = $('#id_zone').val();
  var max = $('.receive-box').length;

  if(isNaN(parseInt(id_zone))){
    swal("โซนไม่ถูกต้อง");
    return false;
  }

  if(max > 0){
    $('#btn-save').attr('disabled', 'disabled');
    $('#btn-change-zone').attr('disabled', 'disabled');
    load_in();
    $('.receive-box').each(function(index){
      let arr = $(this).attr('id').split('-');
      let id_pa = arr[1];
      if(receiveItem(id, id_po, id_zone, id_pa) === false){
        return false;
      }
    });
    load_out();

    review();
  }
}


function receiveItem(id, id_po, id_zone, id_pa){
  var qty = parseInt($('#receive-'+id_pa).val());
  var id_pd = $('#productId-'+id_pa).val();

  if(isNaN(qty)){
    swal("จำนวนไม่ถูกต้อง");
    return false;
  }

  if(qty !== 0){
    var data = [
      {"name" : "id_receive_product", "value" : id},
      {"name" : "id_po", "value" : id_po},
      {"name" : "id_product", "value" : id_pd},
      {"name" : "id_product_attribute", "value" : id_pa},
      {"name" : "qty", "value" : qty},
      {"name" : "id_zone", "value" : id_zone}
    ];

    $.ajax({
      url:'controller/receiveProductController.php?receiveItem',
      type:'POST',
      cache:false,
      data: data,
      success:function(rs){
        setReceived(id_pa, qty);
      }
    });
  }else{
    setReceived(id_pa, qty);
  }
}





function setReceived(id, qty){
  $('#label-'+id).text(qty);
  $('#receive-'+id).addClass('hide');
  $('#label-'+id).removeClass('hide');
  $('#btn-remove-'+id).addClass('hide');
}


function setUnReceived(id){
  $('#receive-'+id).removeClass('hide');
  $('#label-'+id).addClass('hide');
  $('#btn-receive-'+id).removeClass('hide');
  $('#btn-remove-'+id).addClass('hide');
}


$('.receive-box').keyup(function(){
  if(isNaN(parseInt($(this).val()))) {
    $(this).val(0);
  }
});


function deleteRow(id){
  $('#row-'+id).remove();
  updateNo();
}
