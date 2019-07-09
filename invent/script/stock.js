function addStock(){
  $('#add-modal').modal('show');
}


$('#add-modal').on('shown.bs.modal', function(){
  $('#pd-code').focus();
});



function addToStock(){
    var id_product = $('#id_product').val();
    var id_zone = $('#id_zone').val();
    var qty = $('#add-qty').val();
    var pd = $('#pd-code');
    var zone = $('#zone-code');
    var num = $('#add-qty');

    if(id_product == ''){
      $('#pd-code').addClass('has-error');
      $('#pd-error').removeClass('not-show');
      return false;
    }else{
      $('#pd-code').removeClass('has-error');
      $('#pd-error').addClass('not-show');
    }


    if(id_zone == ''){
      $('#zone-code').addClass('has-error');
      $('#zone-error').removeClass('not-show');
      return false;
    }else{
      $('#zone-code').removeClass('has-error');
      $('#zone-error').addClass('not-show');
    }

    if(qty == 0 || qty == ''){
      $('#add-qty').addClass('has-error');
      $('#qty-error').removeClass('not-show');
      return false;
    }else{
      $('#add-qty').removeClass('has-error');
      $('#qty-error').addClass('not-show');
    }

    $('#add-modal').modal('hide');
    load_in();
    $.ajax({
      url:'controller/stockController.php?addNewStock',
      type:'POST',
      cache:'false',
      data:{
        'id_product' : id_product,
        'id_zone' : id_zone,
        'qty' : qty
      },
      success:function(rs){
        load_out();
        rs = $.trim(rs);
        if(rs == 'success'){
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          $('#id_product').val('');
          $('#pd-code').val('');
          $('#add-qty').val('');
        }else{
          swal({
            title:'Error',
            text:rs,
            type:'error'
          });
        }
      }
    });
}




$('#pd-code').autocomplete({
  source:'controller/autoCompleteController.php?getItemCodeAndId',
  minLength:2,
  autoFocus:true,
  close:function(){
    rs = $(this).val();
    arr = rs.split(' | ');
    if(arr.length == 2){
      code = arr[0];
      id = arr[1];
      $(this).val(code);
      $('#id_product').val(id);
    }else{
      $('#id_product').val('');
    }
  }
});




$('#pd-code').keyup(function(e){
  if(e.keyCode == 13){
    $('#zone-code').focus();
  }
});





$('#pd-code').focusout(function(){
  id_product = $('#id_product').val();
  if(id_product == ''){
    $('#pd-code').addClass('has-error');
    $('#pd-error').removeClass('not-show');
  }else{
    $('#pd-code').removeClass('has-error');
    $('#pd-error').addClass('not-show');
  }
});





$('#zone-code').autocomplete({
  source:'controller/autoCompleteController.php?getZone',
  minLength:2,
  autoFocus:true,
  close:function(){
    rs = $(this).val();
    arr = rs.split(' | ');
    if(arr.length == 2){
      name = arr[0];
      id = arr[1];
      $(this).val(name);
      $('#id_zone').val(id);
    }else{
      $('#id_zone').val('');
    }
  }
});




$('#zone-code').keyup(function(e){
  if(e.keyCode == 13){
    $('#add-qty').focus();
  }
});





$('#zone-code').focusout(function(){
  var id_zone = $('#id_zone').val();
  if(id_zone == ''){
    $('#zone-code').addClass('has-error');
    $('#zone-error').removeClass('not-show');
  }else{
    $('#zone-code').removeClass('has-error');
    $('#zone-error').addClass('not-show');
  }
});






$('#add-qty').keyup(function(e){
  if(e.keyCode == 13){
    $('#add-btn').focus();
  }
});




$('#add-qty').focusout(function(){
  var qty = $(this).val();
  if(qty == 0 || qty == ''){
    $('#add-qty').addClass('has-error');
    $('#qty-error').removeClass('not-show');

  }else{
    $('#add-qty').removeClass('has-error');
    $('#qty-error').addClass('not-show');
  }
});





function goBack(){
  window.location.href = 'index.php?content=stock';
}





function getSearch(){
  $('#searchForm').submit();
}





function clearFilter(){
  $.get('controller/storeController.php?clearFilter&stock', function(){ goBack();});
}





$('.search-box').keyup(function(e){
  if(e.keyCode == 13){
    getSearch();
  }
});


function editStock(id_stock){
  $('#qty-'+id_stock).removeAttr('disabled');
  $('#btn-edit-'+id_stock).addClass('hide');
  $('#btn-update-'+id_stock).removeClass('hide');
  $('#qty-'+id_stock).focus();
}


function updateStock(id_stock){
  var qty = $('#qty-'+id_stock).val();
  if(!isNaN(parseInt(qty))){
    $.ajax({
      url:'controller/storeController.php?updateStock',
      type:'POST',
      cache:'false',
      data:{
        'id_stock' : id_stock,
        'qty' : qty
      },
      success:function(rs){
        var rs = $.trim(rs);
        if(rs == 'success'){
          $('#qty-'+id_stock).attr('disabled', 'disabled');
          $('#btn-update-'+id_stock).addClass('hide');
          $('#btn-edit-'+id_stock).removeClass('hide');
        }else{
          swal('Error', rs, 'error');
        }
      }
    });
  }
}


function deleteStock(id_stock){
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#FA5858",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
      $.ajax({
        url:'controller/storeController.php?deleteStock',
        type:'POST',
        cache:'false',
        data:{
          'id_stock' : id_stock
        },
        success:function(rs){
          var rs = $.trim(rs);
          if(rs == 'success'){
            swal({
              title:'Deleted',
              type:'success',
              timer:1000
            });

            $('#row-'+id_stock).remove();

          }else{
            swal('Error', rs, 'error');
          }
        }
      });

	});
}
