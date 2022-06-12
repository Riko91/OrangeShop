$('.btns a.delete').click(function(e){
	var msg = $(this).attr('data-msg');
	var accept = confirm(msg);
	
	if(accept) {
		return true;
	} else {
		return false;
	}
});


var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/62a5e7c0b0d10b6f3e76ec91/1g5c12id6';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();