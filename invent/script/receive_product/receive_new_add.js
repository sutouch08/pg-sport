function addNew(){
  var id_po = $('#id_po').val();
  var po = $('#po').val();
  var invoice = $('#invoice').val();
  var dateAdd = $('#date_add').val();
  var remark = $('#remark').val();

  if(id_po == "" || po.length == 0){
    swal('ใบสั่งซื้อไม่ถูกต้อง');
    return false;
  }

  if(!isDate(dateAdd)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  $.ajax({
    url:"controller/receiveProductController.php?add_new",
    type:"POST",
    cache:false,
    data:{
      "invoice" : invoice,
      "po_reference" : po,
      "id_po" : id_po,
      "date_add" : dateAdd,
      "remark" : remark
    },
    success: function(rs)
    {
      var rs = parseInt(rs);
      if(! isNaN(rs)){
        window.location.href = 'index.php?content=receive_product&add=y&id_receive_product='+rs;
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}


$('#date_add').datepicker({
  dateFormat:'dd-mm-yy'
});


$('#po').autocomplete({
  source:'controller/autoComplete.php?get_active_po',
  autoFocus:true,
  close:function(event, ui){
    var data = $(this).val();
    var arr = data.split(" | ");

    if(arr.length == 3){
      $('#id_po').val(arr[0]);
      $('#po').val(arr[1]);
    }else{
      $('#id_po').val('');
    }
  }
});


$('#po').keyup(function(e){
  if(e.keyCode == 13 && $(this).val().length){
    $('#invoice').focus();
  }
});



$('#invoice').keyup(function(e){
  if(e.keyCode == 13){
    $('#remark').focus();
  }
});


$('#remark').keyup(function(e){
  if(e.keyCode == 13){
    addNew();
  }
});
