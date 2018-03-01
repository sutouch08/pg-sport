var load_time;
function load_in(){
	$("#xloader").modal("show");
	var time = 0;
	load_time = window.setInterval(function(){
		if(time < 90)
		{
			time++;
		}else{
			time += 0.01;
		}
		$("#preloader").css("width", time+"%");
	}, 1000);
}





function load_out(){
	$("#xloader").modal("hide");
	window.clearInterval(load_time);
	$("#preloader").css("width", "0%");
}






function removeCommas(str) {
  while(str.search(",") >= 0) {
      str = (str + "").replace(',', '');
  }
  return str;
}




function addCommas(number){
	return (
	 	input.toString()).replace(/^([-+]?)(0?)(\d+)(.?)(\d+)$/g, function(match, sign, zeros, before, decimal, after) {
	 		var reverseString = function(string) { return string.split('').reverse().join(''); };
	 		var insertCommas  = function(string) {
					var reversed   = reverseString(string);
					var reversedWithCommas = reversed.match(/.{1,3}/g).join(',');
					return reverseString(reversedWithCommas);
					};
				return sign + (decimal ? insertCommas(before) + decimal + after : insertCommas(before + after));
				});
}





function isDate(txtDate){
	  var currVal = txtDate;
	  if(currVal == '')
	    return false;
	  //Declare Regex
	  var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
	  var dtArray = currVal.match(rxDatePattern); // is format OK?
	  if (dtArray == null){
		     return false;
	  }
	  //Checks for mm/dd/yyyy format.
	  dtDay= dtArray[1];
	  dtMonth = dtArray[3];
	  dtYear = dtArray[5];
	  if (dtMonth < 1 || dtMonth > 12){
	      return false;
	  }else if (dtDay < 1 || dtDay> 31){
	      return false;
	  }else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31){
	      return false;
	  }else if (dtMonth == 2){
	     var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
	     if (dtDay> 29 || (dtDay ==29 && !isleap)){
	          return false;
		 }
	  }
	  return true;
	}





function validEmail(v) {
    var r = new RegExp("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?");
    return (v.match(r) == null) ? false : true;
}





jQuery.fn.numberOnly = function()
						{
							return this.each(function()
							{
								$(this).keydown(function(e)
								{
									var key = e.charCode || e.keyCode || 0;
									// allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
									// home, end, period, and numpad decimal
									return (
										key == 8 ||
										key == 9 ||
										key == 13 ||
										key == 46 ||
										key == 110 ||
										key == 190 ||
										(key >= 35 && key <= 40) ||
										(key >= 48 && key <= 57) ||
										(key >= 96 && key <= 105));
								});
							});
						};

						addCommas = function(input){
						  // If the regex doesn't match, `replace` returns the string unmodified
						  return (input.toString()).replace(
							// Each parentheses group (or 'capture') in this regex becomes an argument
							// to the function; in this case, every argument after 'match'
							/^([-+]?)(0?)(\d+)(.?)(\d+)$/g, function(match, sign, zeros, before, decimal, after) {

							  // Less obtrusive than adding 'reverse' method on all strings
							  var reverseString = function(string) { return string.split('').reverse().join(''); };

							  // Insert commas every three characters from the right
							  var insertCommas  = function(string) {

								// Reverse, because it's easier to do things from the left
								var reversed           = reverseString(string);

								// Add commas every three characters
								var reversedWithCommas = reversed.match(/.{1,3}/g).join(',');

								// Reverse again (back to normal)
								return reverseString(reversedWithCommas);
							  };

							  // If there was no decimal, the last capture grabs the final digit, so
							  // we have to put it back together with the 'before' substring
							  return sign + (decimal ? insertCommas(before) + decimal + after : insertCommas(before + after));
							}
						  );
						};

						$.fn.addCommas = function() {
							  $(this).each(function(){
								$(this).val(addCommas($(this).val()));
							  });
							};




function confirm_delete(title, text, url, confirm_text, cancle_text)
{
	var confirm_text = typeof confirm_text !== 'undefined' ? confirm_text : "ใช่";
	var cancle_text = typeof cancle_text !== 'undefined' ? cancle_text : "ไม่ใช่";
	swal({
	  title: title,
	  text: text,
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: confirm_text,
	  cancelButtonText: cancle_text,
	  closeOnConfirm: false},
	  function(isConfirm){
	  if (isConfirm) {
		window.location.href = url;
	  }
	});
}



function checkerror(){
    if($("#error").length){
		var mess = $("#error").val();
		swal({ title: "เกิดข้อผิดพลาด!", text: mess, type: "error"});
	}else if($("#success").length){
		var mess = $("#success").val();
		swal({ title: "สำเร็จ", text: mess, timer: 1000, type: "success"});
	}else if($("#warning").length){
		var mess = $("#warning").val();
		swal({ title: "คำเตือน", text: mess, timer: 2000, type: "warning"});
	}
}



//**************  Handlebars.js  **********************//
function render(source, data, output){
	var template = Handlebars.compile(source);
	var html = template(data);
	output.html(html);
}



function render_append(source, data, output)
{
	var template 	= Handlebars.compile(source);
	var html 			= template(data);
	output.append(html);
}



function render_prepend(source, data, output)
{
	var template		= Handlebars.compile(source);
	var html			= template(data);
	output.prepend(html);
}




var downloadTimer;
function get_download(token)
{
	load_in();
	downloadTimer = window.setInterval(function(){
		var cookie = $.cookie("file_download_token");
		if(cookie == token)
		{
			finished_download();
		}
	}, 1000);
}




function finished_download()
{
	window.clearInterval(downloadTimer);
	$.removeCookie("file_down_load_token");
	load_out();
}





function get_rows(){
	$("#rows").submit();
}





$("#get_rows").change(function(e) {
    get_rows();
});




function parse_int(number){
  return isNaN(parseInt(number)) ? 0 : parseInt(number);
}


function parse_float(number){
  return isNaN(parseFloat(number)) ? 0 : parseFloat(number);
}


function isJson(str){
	try{
		JSON.parse(str);
	}catch(e){
		return false;
	}
	return true;
}




function printOut(url)
{
	var center = ($(document).width() - 800) /2;
	window.open(url, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
}
