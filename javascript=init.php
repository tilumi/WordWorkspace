<?php
list( $startIndex ) = APP::$appBuffer;
?>
$(document).ready( function(){
    $("a[rel='bible-book']").each( function(){
        var url = encodeURI( '<?php echo url('/');?>' + this.name + '/chapters.html' );
        $(this).colorbox({
            loop: false,
            top: "90px",
            /*fixed: true,*/
            transition: "none",
            opacity: 0.1,
            width:"960px",
            height:"80%",
            href: url
        });
    });
    
    if( $('#menu .active a').length > 0 ){
        var offsetLeft = ( $('#menu .active a')[0].offsetLeft - 320 );
    }else{
        var offsetLeft = ( $('#menu a')[39].offsetLeft - 500+83/2 );
    }
    var maxLeft = $('#menu')[0].offsetWidth - $('#menu-container')[0].offsetWidth ;
    if( offsetLeft < 0 ) offsetLeft = 0;
    if( offsetLeft > maxLeft ) offsetLeft = maxLeft;
    $('#menu').css('marginLeft', '-'+ offsetLeft +'px' );
});

$( function(){
	//scrollpane parts
	var scrollPane = $( "#menu-container" ),
		scrollContent = $( "#menu" );

	//build slider
	var scrollbar = $( ".scroll-bar" ).slider({
		slide: function( event, ui ) {
			if ( scrollContent.width() > scrollPane.width() ) {
				scrollContent.css( "margin-left", Math.round(
					ui.value / 100 * ( scrollPane.width() - scrollContent.width() )
				) + "px" );
			} else {
				scrollContent.css( "margin-left", 0 );
			}
		}
	});
	
	//append icon to handle
	var handleHelper = scrollbar.find( ".ui-slider-handle" )
    	.mousedown(function() {
    		scrollbar.width( handleHelper.width() );
    	})
    	.mouseup(function() {
    		scrollbar.width( "100%" );
    	})
    	.append( "<span class='ui-icon ui-icon-grip-dotted-vertical'></span>" )
    	.wrap( "<div class='ui-handle-helper-parent'></div>" ).parent();
	 
	//change overflow to hidden now that slider handles the scrolling
	scrollPane.css( "overflow", "hidden" );
	
	//size scrollbar and handle proportionally to scroll distance
	function sizeScrollbar() {
        var warpSize = $('.scroll-bar-wrap').width();
		var handleSize = 1;//warpSize * ( scrollPane.width() / scrollContent.width() );
		
		var leftVal = scrollContent.css( "margin-left" ) === "auto" ? 0 :
			parseInt( scrollContent.css( "margin-left" ) );
		var handleLeft = warpSize * ( (-leftVal) / ( scrollContent.width()-scrollPane.width() ) ) - handleSize;
		
		scrollbar.find( ".ui-slider-handle" ).css({
			width: handleSize,
			"margin-left": -handleSize / 2,
			"left": handleLeft+'px'
		});
		
		handleHelper.width( scrollbar.width() - handleSize );
	}
	
	//reset slider value based on scroll content position
	function resetValue() {
		var remainder = scrollPane.width() - scrollContent.width();
		var leftVal = scrollContent.css( "margin-left" ) === "auto" ? 0 :
			parseInt( scrollContent.css( "margin-left" ) );
		var percentage = Math.round( leftVal / remainder * 100 );
		scrollbar.slider( "value", percentage );
	}
	
	//if the slider is 100% and window gets larger, reveal content
	function reflowContent() {
			var showing = scrollContent.width() + parseInt( scrollContent.css( "margin-left" ), 10 );
			var gap = scrollPane.width() - showing;
			if ( gap > 0 ) {
				scrollContent.css( "margin-left", parseInt( scrollContent.css( "margin-left" ), 10 ) + gap );
			}
	}
	
	//change handle position on window resize
	$( window ).resize(function() {
		resetValue();
		sizeScrollbar();
		reflowContent();
	});
	//init scrollbar size
	setTimeout( sizeScrollbar, 10 );//safari wants a timeout
} );