<?php
list( $startIndex ) = APP::$appBuffer;
?>
$(document).ready( function(){
    $("a[rel='bible-book']").each( function(){
        var url = encodeURI( '<?php echo url('/');?>' + this.name.substring( 0, this.name.indexOf('(') ) + '/chapters.html' );
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
    
    var key=0;
    $('#menu a').each( function(){
    	var scrollPane = $( "#menu-container" ),
		    scrollContent = $( "#menu" ),
		    scrollBar = $( ".scroll-bar" ),
            scrollBarWarp = $( ".scroll-bar-warp" );
        var oLeft=this.offsetLeft + ($(this).width() / 2);
        var name=this.name;
        var sh=name.substring( name.indexOf('(')+1, name.indexOf(')') );
        var pos=Math.round( oLeft / scrollContent.width() * scrollBar.width() );
        var style1='background:#176ba7;';
        var pos1=pos;
        var style2='top:10px;color:#176ba7;';
        var pos2=pos-6;
        if( (key % 2) == 1 ){
            style1='height:20px;';
            style2='top:25px;';
            if( sh.length > 1 ){
                style2='top:23px;width:12px;';
                pos2=pos-6;
            }
        }
        scrollBar.append('<div class="grads" style="left:'+pos1+'px;'+style1+'"></div><div class="grad-tags" style="left:'+pos2+'px;'+style2+'">'+sh+'</div>');
        key=key+1;
    });
    
    /* 設定 books navigator 的起始位置 */
    if( $('#menu .active a').length > 0 ){
        var target = $('#menu .active a');
        var offsetLeft = ( target[0].offsetLeft - ( $("#menu-container").width()-target.width() )/2 );
    }else{
        var target = $('#menu a');
        var offsetLeft = ( target[39].offsetLeft - ( $("#menu-container").width()-target.width() )/2 );
    }
    var maxLeft = $('#menu')[0].offsetWidth - $('#menu-container')[0].offsetWidth ;
    if( offsetLeft < 0 ) offsetLeft = 0;
    if( offsetLeft > maxLeft ) offsetLeft = maxLeft;
    $('#menu').css('margin-left', '-'+ offsetLeft +'px' );
});

$( function(){
	//scrollpane parts
	var scrollPane = $( "#menu-container" ),
		scrollContent = $( "#menu" );

	//build slider
	var scrollbar = $( ".scroll-bar" ).slider({
		slide: function( event, ui ) {
			if ( scrollContent.width() > scrollPane.width() ) {
				//scrollContent.css( "margin-left", Math.round( ui.value / 100 * (scrollPane.width() - scrollContent.width()) ) + "px" );
				var pos = Math.round( ui.value / 100 * (scrollPane.width() - scrollContent.width()) ) + "px";
				scrollContent.stop().animate( { marginLeft: pos }, 300 );
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
    	.append( '<span class="ui-icon ui-icon-grip-dotted-vertical"></span>' )
    	.wrap( '<div class="ui-handle-helper-parent"></div>' ).parent();
	 
	//change overflow to hidden now that slider handles the scrolling
	scrollPane.css( "overflow", "hidden" );
	
	//size scrollbar and handle proportionally to scroll distance
	function sizeScrollbar() {
        var warpSize = $('.scroll-bar-wrap').width();
		var handleSize = warpSize * ( scrollPane.width() / scrollContent.width() );
		
		var leftVal = scrollContent.css( "margin-left" ) === "auto" ? 0 : parseInt( scrollContent.css( "margin-left" ) );
		//var handleLeft = warpSize * ( (-leftVal) / ( scrollContent.width()-scrollPane.width() ) ) - handleSize/2;
		var handleLeft = warpSize / scrollContent.width() * (-leftVal);
		if( handleLeft < 0 ) handleLeft = 0;
		
		scrollbar.find( ".ui-slider-handle" ).css({
			width: handleSize,
			//"margin-left": -handleSize / 2,
			//"left": handleLeft+'px'
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