function viewOrder(id){
  window.location.href = 'index.php?content=order&id_order='+id;
}

function newBill(){
  if($('#id_order').length == 0 || $('#is_paid').val() == 1){
    $.ajax({
      url:'controller/posController.php?newBill',
      type:'GET',
      cache:'false',
      success:function(rs){
        var rs = $.trim(rs);
        if(!isNaN( parseInt(rs) )){
          window.location.href = 'index.php?content=order&id_order='+rs;
        }else{
          swal('Error', rs, 'error');
        }
      }
    });
  }else{
    return false;
  }
}



function pauseBill(){
  id_order = $('#id_order').val();
  if($('.item-row').length > 0){
    $.ajax({
      url:'controller/posController.php?pauseBill',
      type:'POST',
      cache:'false',
      data:{
        'id_order' : id_order
      },
      success:function(rs){
        rs = $.trim(rs);
        if(rs == 'success'){
          $.ajax({
            url:'controller/posController.php?newBill',
            type:'GET',
            cache:'false',
            success:function(id){
              var id = $.trim(id);
              if(!isNaN( parseInt(id) )){
                window.location.href = 'index.php?content=order&id_order='+id;
              }else{
                swal('Error', id, 'error');
              }
            }
          });
        }
      }
    });
  }
}



function addToOrder(){
  id_order = $('#id_order').val();
  barcode = $('#barcode-item').val();
  pdisc = $('#p-disc').val();
  adisc = $('#a-disc').val();
  qty = $('#qty').val();
  price = $('#price').val();

  if(id_order == ''){
    swal('ไม่พบเลขที่ออเดอร์');
    return false;
  }

  if(barcode.length == 0){
    return false;
  }

  $.ajax({
    url:'controller/posController.php?addToOrder',
    type:'POST',
    cache:'false',
    data:{
      'id_order' : id_order,
      'barcode' : barcode,
      'pdisc' : pdisc,
      'adisc' : adisc,
      'price' : price,
      'qty' : qty
    },
    success:function(rs){
      if(isJson(rs)){
        ds = $.parseJSON(rs);
        if(ds.result == 'update'){
          id = ds.data.id;
          source = $('#current-row-template').html();
          data = ds.data;
          output = $('#row-'+id);
          render(source, data, output);
        }else{
          source = $('#new-row-template').html();
          data = ds.data;
          output = $('#detail-table');
          render_append(source, data, output);
        }

        clearField();
        reOrder();
        reCal();
      }else{
        swal('Error!', rs, 'error');
      }
    }
  });


}


function reOrder(){
  no = 1;
  $('.no').each(function(index, el) {
    $(this).text(no);
    no++;
  });
}


function reCal(){
  var items = 0;
  var qtys = 0;
  var amounts = 0;
  $('.item-row').each(function(index, el) {
    ids = $(this).attr('id').split('-');
    id = ids[1];
    items++;

    qty = parseInt(removeCommas($('#qty-'+id).text()));
    qtys += qty;

    amount = parseFloat(removeCommas($('#amount-'+id).text()));
    amounts += amount;
  });

  $('#sum-items').text(items);
  $('#sum-qty').text(addCommas(qtys));
  $('#sum-amount').text(addCommas(amounts.toFixed(2)));
  $('#sell-amount').val(amounts);
  $('#barcode-item').focus();
}

function clearField(){
  $('#p-disc').val('');
  $('#a-disc').val('');
  $('#price').val('');
  $('#qty').val(1);
  $('#barcode-item').val('');
  $('#barcode-item').focus();
}


$(document).ready(function() {
  is_paid = $('#is_paid').val();
  if(is_paid == 1){
    $('.control').attr('disabled', 'disabled');
    $('.payment').attr('disabled', 'disabled');
    $('.del-btn').attr('disabled', 'disabled');
    $('#btn-pause-bill').attr('disabled', 'disabled');
    $('#btn-print-bill').removeAttr('disabled');
  }
});
