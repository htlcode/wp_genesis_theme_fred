(function($) {
	$(window).scroll(function() {
		if($(this).scrollTop() > 200){
			$("#scrollup").fadeIn()
		} else {
			$("#scrollup").fadeOut()
		}
	});

	$("#scrollup").click(function() {
	    return $("html, body").animate({
	        scrollTop: 0
	    }, 600), !1
	});

	if($('#genesis-sidebar-primary').hasClass('left-sidebar')){
		function defineLayout(){
			var win = $(this);
			if (win.width() < 1024) {
				$('#genesis-sidebar-primary').removeClass("item-first");  
			} else {
				$('#genesis-sidebar-primary').addClass("item-first");  
			}
		}
		$(window).on('resize', function(){
			defineLayout();
		});
		defineLayout();
	}
	
})(jQuery);