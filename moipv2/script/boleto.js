(function($) {

BoletoWidget = function(){

	$(".btnPrint").printPage({
	  url: $(".btnPrint").attr('href'),
	  attr: "href",
	  message:''
	})
	
};
update_iframe = function(){
	$(".progress-striped").fadeOut('active');
	$("#moip_page").fadeIn('active');
	
}
})(jQuery);
