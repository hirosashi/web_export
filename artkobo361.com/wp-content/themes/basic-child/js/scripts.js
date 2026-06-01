/*
	scripts.js
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
	Copyright: (c) 2015 Mamekko, http://welcustom.net
*/

jQuery(function($){  
	$('.nav-button').click(function(){
		$('.menu-wrap').slideToggle();
	});
	$('#header li[class*="has_children"], #header li[class*="has-children"]').hover(function(){
		$('>ul',this).slideToggle();
	});
	var pagetop = $('.page-top');
	$(window).scroll(function(){
		if($(this).scrollTop() > 200) {
			pagetop.fadeIn('slow');
		} else {
			pagetop.fadeOut('slow');
		}
	});

	//$('a[href^=#]').click(function(){
	//	var speed = 500;
	//	var href= $(this).attr('href');
	//	var target = $(href == '#' || href == "" ? 'html' : href);
	//	var position = target.offset().top;
	//	$('html, body').animate({scrollTop:position}, speed, 'swing');
	//	return false;
	//});
	
	/* For Welcart e-commerce plugin*/
	$('#searchsubmit').attr('value','').addClass('submit button postfix black font-awesome');
});