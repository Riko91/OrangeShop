$('.btns a.delete').click(function(e){
	var msg = $(this).attr('data-msg');
	var accept = confirm(msg);
	
	if(accept) {
		return true;
	} else {
		return false;
	}
});