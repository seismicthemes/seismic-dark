jq2 = jQuery.noConflict();
jq2(function( $ ) {
	$(window).scroll(function(e){
		$el = $('#access');
		if ( $(window).width() > 600) {
			if ($(this).scrollTop() > 150 && $el.css('position') != 'fixed'){
				$('#access').addClass("fixedMenu");
			}
			else if ($(this).scrollTop() < 150 && $el.css('position') == 'fixed'){
				$('#access').removeClass("fixedMenu");
			}
		}
		else {
			$('#access').removeClass("fixedMenu");
		}
	});
});