$('#p-disc').keyup(function(e){
  if(e.keyCode == 13){
    $('#barcode-item').focus();
  }
});





$('#p-disc').focusout(function(){
  disc = parseFloat($(this).val());
  disc = isNaN(disc) ? 0 : disc;
  if(disc > 100){
    $(this).val(100);
  }

  if(disc < 0 ){
    $(this).val(0);
  }
});





$('#a-disc').keyup(function(e){
  if(e.keyCode == 13){
    $('#barcode-item').focus();
  }
});



$('#a-disc').focusout(function(){
  disc = parseFloat($(this).val());
  disc = isNaN(disc) ? 0 : disc;
  if(disc < 0){
    $(this).val(0);
  }
});


$('#price').keyup(function(e){
  if(e.keyCode == 13){
    $('#barcode-item').focus();
  }
});


function decreaseQty(){
  qty = parseInt($('#qty').val());
  qty = isNaN(qty) ? 0 : qty;
  if( (qty - 1) > 0)
  {
    qty--;
    $('#qty').val(qty);
  }
  $('#barcode-item').focus();
}



function increaseQty(){
  qty = parseInt($('#qty').val());
  qty = isNaN(qty) ? 0 : qty;
  qty++;
  $('#qty').val(qty);
  $('#barcode-item').focus();
}


$('#qty').keyup(function(e){
  if(e.keyCode == 13){
    $('#barcode-item').focus();
  }
});


$('#qty').focusout(function(){
  qty = parseInt($(this).val());
  qty = isNaN(qty) ? 1 : qty;
  if(qty < 1){
    $(this).val(1);
  }
});




$('#barcode-item').keyup(function(e){
  if(e.keyCode == 13){
    addToOrder();
  }

  //--- space bar
  if(e.keyCode == 32){
    $(this).val('');
    $('#txt-received-money').focus();
  }
});


function deleteRow(id, pdCode){
  var id_order = $('#id_order').val();
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '"+pdCode+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#FA5858",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
			$.ajax({
				url:"controller/posController.php?deleteDetail",
				type:"POST",
        cache:"false",
        data:{
          'id_order_pos' : id_order,
          "id_order_pos_detail" : id
        },
				success: function(rs){
					var rs = $.trim(rs);
					if( rs == 'success' ){
						swal({
              title: 'Deleted',
              type: 'success',
              timer: 1000
            });

            setTimeout(function(){
              $('#barcode-item').focus();
            }, 1200);

						$("#row-"+id).remove();

            reOrder();
            reCal();
					}else{
						swal('Error!', rs, 'error');
					}
				}
			});
	});
}



function disableControl(){
  $('.control').attr('disabled', 'disabled');
}

function disabledPayment(){
  $('.payment').attr('disabled', 'disabled');
}



function showPauseBill(){
  $.ajax({
    url:'controller/posController.php?getPauseList',
    type:'GET',
    cache:'false',
    success:function(rs){
      var source = $('#pause-list-template').html();
      var data = $.parseJSON(rs);
      var output = $('#modal_body');
      render(source, data, output);
      $('#pause-bill-modal').modal('show');
    }
  });
}
