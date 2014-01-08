jQuery(document).ready(function (jQuery){
jQuery('.top_slider .items, #slider').cycle({
	fx: 'fade',
	speed: 600,
	timeout: 4000,
	prev :'#slider_prev',
	next :'#slider_next',
	pause: true,
	cleartype: true,
	cleartypeNoBg: true,
	pager: 'ul.small_thumbs',
	after: feature_after,
	before: onbefore,
	pagerAnchorBuilder: function(idx, slide) {
	return 'ul.small_thumbs li:eq(' + (idx) + ')';
	}});
		jQuery('ul.small_thumbs li').hover(function() {
		jQuery('.top_slider .items').cycle('pause');
	}, function () {
		jQuery('.top_slider .items').cycle('resume');
	});

	 //jQuery('.top_slider .items').bind('mousewheel',wheelMove);

	function feature_after() {
		jQuery('.slider .top_slider .thumb .readmore').stop().animate({opacity:1, margin: '0 -2px 0 0'},{queue:false,duration: 200 });
		jQuery('.slider #slider_next').stop().animate({opacity:1, bottom:'0px'},{queue:false,duration: 50 });
		jQuery('.slider #slider_prev').stop().animate({opacity:1, bottom:'0px'},{queue:false,duration: 150 });
	/*
      interval = setTimeout(function(){
        jQuery('.top_slider .items').bind('mousewheel', wheelMove);
      },1600);
  	*/
	}
	function onbefore() {
		jQuery('.slider .top_slider .thumb .readmore').stop().animate({opacity:1, margin: '0 -200px 0 0'},{queue:false,duration:200});
		jQuery('.slider #slider_next').stop().animate({opacity:1, bottom: '-50px'},{queue:false, duration: 50});
		jQuery('.slider #slider_prev').stop().animate({opacity:1, bottom: '-50px'},{queue:false, duration: 150});

	}
		jQuery('.small_thumbs li:not(.activeSlide) a').click(
	function () {
		jQuery('.small_thumbs li a').css('opacity', 1);
		jQuery(this).css('opacity', 1);
	});
		jQuery('.small_thumbs li:not(.activeSlide) a').hover(
	function () {
		jQuery(this).stop(true, true).animate({opacity: 0.7}, 300);
	}, function () {
		jQuery(this).stop(true, true).animate({opacity: 1}, 300);
		});

});

jQuery(document).ready(function($){
	jQuery('.slider .top_slider .thumb img').hover( function() {
		jQuery(this).stop().animate({opacity:0.8},{queue:false,duration:200});  
		}, function() {
		jQuery(this).stop().animate({opacity:1},{queue:false,duration:200});  
	});
})