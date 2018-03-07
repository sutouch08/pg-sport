function justBalance(){
  var amount = parseFloat(removeCommas($('#sum-amount').text()));
  var sellAmount = parseFloat($('#sell-amount').val());
  if(amount == sellAmount){
    $('#txt-received-money').val(amount);
    $('#change').val(0);
    $('#btn-pay-order').removeAttr('disabled');
    $('#btn-pay-order').focus();
  }else{
    swal({
      title:'ยอดเงินไม่ถูกต้อง',
      text:'กรุณากด F5 แล้วดำเนินการชำระเงินใหม่อีกครั้ง',
      type:'error'
    });
  }

}



function payByCard(){
  $('#payment-method').val(2);
  $('#btn-pay-card').addClass('btn-primary');
  $('#btn-pay-cash').removeClass('btn-primary');
  justBalance();
}


function payByCash(){
  $('#payment-method').val(1);
  $('#btn-pay-cash').addClass('btn-primary');
  $('#btn-pay-card').removeClass('btn-primary');
  $('#txt-received-money').focus();
}


$('#txt-received-money').keyup(function(e){
  if(e.keyCode == 13){
    payOrder();

  }else if(e.keyCode == 32){
    justBalance();
  }else{
    amount = parseFloat($('#sell-amount').val());
    pay = parseFloat($(this).val());
    pay = isNaN(pay) ? 0 : pay;

    change = pay - amount;
    $('#change').val(change);

    if(pay >= amount){
      $('#btn-pay-order').removeAttr('disabled');
    }else{
      $('#btn-pay-order').attr('disabled', 'disabled');
    }
  }

});



function payOrder(){
  id_order = $('#id_order').val();
  payMethod = $('#payment-method').val();
  amount = parseFloat($('#sell-amount').val());
  payAmount = parseFloat($('#txt-received-money').val());

  if(payAmount < amount){
    swal({
      title:'รับเงินไม่ครบ',
      text:'ยอดที่ต้องชำระ '+addCommas(amount),
      type:'warning',
      timer:3000
    });
    return false;
  }

  $('#txt-received-money').attr('disabled', 'disabled');
  $('#btn-pay-order').attr('disabled', 'disabled');

  $.ajax({
    url:'controller/posController.php?payOrder',
    type:'POST',
    cache:'false',
    data:{
      'id_order' : id_order,
      'paymentMethod' : payMethod,
      'payAmount' : payAmount
    },
    success:function(rs){
      var rs = $.trim(rs);
      if(rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        $('#is_paid').val(1);
        $('.control').attr('disabled', 'disabled');
        $('.payment').attr('disabled', 'disabled');
        $('.del-btn').attr('disabled', 'disabled');
        $('#btn-pause-bill').attr('disabled', 'disabled');
        $('#btn-print-bill').removeAttr('disabled');
        printBill();
      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}


function printBill(){
  id = $('#id_order').val();
  //--- properties for print
  var prop 			= "width=600, height=600. left="+center+", scrollbars=yes";
  var center    = ($(document).width() - 600)/2;

  var target  = "print/pos_bill.php?id_order="+id;
  window.open(target, '_blank', prop);
}

$(document).ready(function() {
  var intv = setInterval(function(){
    if($('input:focus').length == 0){
      $('#barcode-item').focus();
    }
  }, 3000)
});
