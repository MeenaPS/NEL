jQuery( document ).ready(function($) {

  // Logo Slider
  $( '.wpls-logo-slider' ).each(function( index ) {
    
    var slider_id   = $(this).attr('id');
    var logo_conf   = $.parseJSON( $(this).closest('.wpls-logo-showcase-slider-wrp').find('.wpls-logo-showacse-slider-conf').attr('data-conf') );

    if( typeof(slider_id) != 'undefined' && slider_id != '' ) {
        jQuery('#'+slider_id).slick({
            centerMode  		: (logo_conf.center_mode) == "true" ? true : false,
            dots        		: (logo_conf.dots) == "true" ? true : false,
            arrows      		: (logo_conf.arrows) == "true" ? true : false,
            infinite      		: (logo_conf.loop) == "true" ? true : false,
            speed       		: parseInt(logo_conf.speed),
            autoplay      		: (logo_conf.autoplay) == "true" ? true : false,
            slidesToShow    	: parseInt(logo_conf.slides_column),
            slidesToScroll    	: parseInt(logo_conf.slides_scroll),
            autoplaySpeed   	: parseInt(logo_conf.autoplay_interval),
    		pauseOnFocus        : false,
    		centerPadding		: '0px',
            rtl               	: (logo_conf.rtl) == "true" ? true : false,
            mobileFirst       	: (Wpls.is_mobile == 1) ? true : false,
            responsive: [{
              breakpoint: 1023,
              settings: {
                slidesToShow  : (parseInt(logo_conf.slides_column) > 3) ? 3 : parseInt(logo_conf.slides_column),
                slidesToScroll  : 1
              }
            },{
              breakpoint: 640,
              settings: {
                slidesToShow  : (parseInt(logo_conf.slides_column) > 2) ? 2 : parseInt(logo_conf.slides_column),
                slidesToScroll  : 1
              }
            },{
              breakpoint: 479,
              settings: {
                slidesToShow  : 1,
                slidesToScroll  : 1
              }
            },{
              breakpoint: 319,
              settings: {
                slidesToShow: 1,
                slidesToScroll: 1
              }
            }]
      });
    }
  });
});