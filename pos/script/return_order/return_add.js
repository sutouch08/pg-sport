$('#date_add').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(){
    $('#txt-bill').focus();
  }
});


$('#txt-bill').autocomplete({
  source:'controller/returnOrderController.php?searchValidBill',
  autoFocus:true,
  close:function(){
    rs = $(this).val();
    if(rs == 'ไม่พบข้อมูล'){
      $(this).val('');
      $('#txt-customer').val('');
      $('#id_customer').val('');
      $('#txt-emp').val('');
      $('#txt-payment-method').val('');
      $('#result').html('');
    }else{
      getBillDetail();
    }
  }
});



function getBillDetail(){
  var reference = $('#txt-bill').val();
  load_in();
  $.ajax({
    url:'controller/returnOrderController.php?getBillDetail',
    type:'GET',
    cache:'false',
    data:{
      'reference' : reference
    },
    success:function(rs){
      load_out();
      if(isJson(rs)){
        ds = $.parseJSON(rs);
        $('#txt-customer').val(ds.customerName);
        $('#id_customer').val(ds.id_customer);
        $('#txt-emp').val(ds.empName);
        $('#txt-payment-method').val(ds.payment);
        source = $('#bill-template').html();
        data = ds.data;
        output = $('#result');
        render(source, data, output);
        $('#btn-save').removeAttr('disabled');
        inputInit();
      }else{
        swal('Error!', rs, 'error');
      }
    }
  });
}


function inputInit(){
  $('.qty').keyup(function(e){
    ids = $(this).attr('id').split('-');
    id = ids[1];
    limit = parse_int($('#qty-'+id).text());
    qty = parse_int($(this).val());

    if(qty > limit || qty < 0){
      $(this).val('');
    }

    reCal();
  });
}



function reCal(){
  console.log('recal');
  var sumQty = 0;
  var sumAmount = 0;
  $('.qty').each(function(index, el) {
    ids = $(this).attr('id').split('-');
    id  = ids[1];
    qty = parseInt($(this).val());
    qty = isNaN(qty) ? 0 : qty;
    price = parseFloat($('#price-'+id).text());
    price = isNaN(price) ? 0 : price;

    sumQty += qty;
    sumAmount += (qty * price);
    $('#cnAmount-'+id).text(addCommas(qty * price));
  });

  $('#sumQty').text(addCommas(sumQty));
  $('#sumAmount').text(addCommas(sumAmount));
}



function save(){
  swal({
    title:'ยืนยันการคืนสินค้า',
    text:'ต้องการยืนยันการคืนสินค้าหรือไม่ ?',
    showCancelButton:true,
    confirmButtonText:'ยืนยัน',
    closeOnConfirm:false
  }, function(){
    saveReturn();
  });
}



function saveReturn(){
  var billCode = $('#txt-bill').val();
  var date_add = $('#date_add').val();
  var id_customer = $('#id_customer').val();
  var remark = $('#remark').val();
  var count = 0;
  if(billCode == ''){
    swal('เลขที่บิลไม่ถูกต้อง');
    return false;
  }

  if(!isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  ds = [
    {'name' : 'billCode', 'value' : billCode},
    {'name' : 'date_add', 'value' : date_add},
    {'name' : 'id_customer', 'value' : id_customer},
    {'name' : 'remark' , 'value' : remark}
  ];

  $('.qty').each(function(index, el) {
    ids = $(this).attr('id').split('-');
    id = ids[1];
    qty = parse_int($(this).val());
    if(qty > 0){
      name = 'qty['+id+']';
      ds.push({'name' : name, 'value' : qty});
      count++;
    }

  });

  if(count == 0){
    swal('ไม่พบรายการรับคืน');
    return false;
  }

  $.ajax({
    url:'controller/returnOrderController.php?addNew',
    type:'POST',
    cache:'false',
    data: ds,
    success:function(rs){
      //--- rs = id_return_order
      var rs = parseInt($.trim(rs));
      if(! isNaN(rs)){
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          viewDetail(rs);
        }, 1200);
      }else{
        swal('Error!', rs, 'error');
      }
    }
  });
}
