<?php
list( $startIndex ) = APP::$appBuffer;
?>
var initContentLeft=0, initHandleLeft=0; //記錄書卷捲軸的起始點
var stHolder;
var stStop=false;
var stSpeed=20;
var stTS=70;
function scrollMinus(){
    if( stStop ){ stStop=false;return; }
	var scrollPane = $( "#menu-container" ),
		scrollContent = $( "#menu" );
    var oLeft = scrollContent[0].offsetLeft;
    oLeft = parseInt(scrollContent[0].style.marginLeft);
    var ts = stTS;
    oLeft = oLeft + stSpeed;
    if( oLeft > 0 ){ oLeft=0; }
    scrollContent.css( 'margin-left', oLeft );
    
    var handleLeft = 100*Math.abs(oLeft) / (scrollContent.width()-scrollPane.width());
    $( ".scroll-bar .ui-slider-handle" ).css( 'left', handleLeft+'%' );
    
    stHolder = setTimeout('scrollMinus()', ts);
}
function scrollPlus(){
    if( stStop ){ stStop=false;return; }
	var scrollPane = $( "#menu-container" ),
		scrollContent = $( "#menu" );
    var oLeft = parseInt(scrollContent[0].style.marginLeft);
    //alert(oLeft);
    var ts = stTS;
    var max = -(scrollContent.width() - scrollPane.width());
    oLeft = oLeft - stSpeed;
    //alert(oLeft);
    if( oLeft <= max ){ oLeft=max; }
    //alert(oLeft);return;
    scrollContent.css( 'margin-left', oLeft );
    
    var handleLeft = 100*Math.abs(oLeft) / (scrollContent.width()-scrollPane.width());
    $( ".scroll-bar .ui-slider-handle" ).css( 'left', handleLeft+'%' );
    
    stHolder = setTimeout('scrollPlus()', ts);
}
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
    
    //自動產生捲軸刻度
    /*var key=0;
    $('#menu a').each( function(){
    	var scrollPane = $( "#menu-container" ),
		    scrollContent = $( "#menu" ),
		    scrollBar = $( ".scroll-bar" ),
            scrollBarWarp = $( ".scroll-bar-warp" );
        var oLeft=this.offsetLeft + ($(this).width() / 2);
        var name=this.name;
        var sh=name.substring( 0, name.indexOf('(') );
        var pos=Math.round( oLeft / scrollContent.width() * scrollBar.width() );
        var style1='background:#176ba7;';
        var pos1=pos;
        var style2='top:1px;color:#fff;line-height:12px;';
        var pos2=pos-5;
        
        nsh=sh;
        if( nsh.indexOf('記')>=0 && nsh.length>3 ){
            nsh = nsh.replace('記','');
        }
        if( nsh==='約伯記' ){
            nsh = nsh.replace('記','');
        }
        if( nsh.indexOf('書')>=0 && nsh.length>3 && nsh!=='約書亞' && nsh.indexOf('彼得')<0 ){
            nsh = nsh.replace('書','');
        }
        if( nsh==='約珥書' || nsh==='約拿書' || nsh==='彌迦書' || nsh==='那鴻書' || nsh==='哈該書' ){
            nsh = nsh.replace('書','');
        }
        if( nsh.indexOf('帖撒羅尼迦')>=0 ){
            nsh = nsh.replace('帖撒羅尼迦','帖撒羅');
        }
        if( nsh==='耶利米哀歌' ){
            nsh = '耶利米哀';
        }
        
        scrollBar.append('<div class="grad-tags" style="left:'+pos2+'px;'+style2+'">'+nsh+'</div>');
        key=key+1;
    });*/
    //自動產生捲軸刻度v1
    /*
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
        var style2='top:7px;color:#176ba7;';
        var pos2=pos-5;
        if( (key % 2) == 1 ){
            style1='height:20px;';
            style2='top:22px;';
            if( sh.length > 1 ){
                style2='top:20px;width:12px;';
                pos2=pos-5;
            }
        }
        scrollBar.append('<div class="grads" style="left:'+pos1+'px;'+style1+'"></div><div class="grad-tags" style="left:'+pos2+'px;'+style2+'">'+sh+'</div>');
        key=key+1;
    });
    */
    //設定書卷捲軸的顯示及隱藏
    var showWrap=false;
    var overHolder, outHolder;
    $(".midnav").mouseenter(function(){
        clearTimeout(outHolder);
        //if( $(".scroll-bar-wrap").css('top') > -10 ){ return; }
        //overHolder=setTimeout( function(){ $(".scroll-bar-wrap").stop().animate({'top': '8px'}, 100); }, 200);
        overHolder=setTimeout( function(){ $(".scroll-bar-wrap").stop().animate({'opacity': '1'}, 100); }, 200);
        //$('#middle-area').text( $('#middle-area').text() + '1 ' );
    }).mouseleave(function(){
        clearTimeout(overHolder);
        //if( $(".scroll-bar-wrap").css('top') < -40 ){ return; }
        //outHolder=setTimeout( function(){ $(".scroll-bar-wrap").stop().animate({'top': '-52px'}, 200); }, 1200);
        outHolder=setTimeout( function(){ $(".scroll-bar-wrap").stop().animate({'opacity': '0'}, 200); }, 1200);
        //$('#middle-area').text( $('#middle-area').text() + '0 ' );
    });
    //uiSlider.get(0).onmousedown=function(){return false;};
    //uiSlider.get(0).onclick=function(){return true};
    
    /* 設定捲軸控制鈕 */
    var scrollBtnActive=false;
    $('.scroll-restore').click(function(){
        if( $( ".scroll-bar .ui-slider-handle" ).css('left').indexOf('px') > 0 ){ return; }
    	var scrollPane = $( "#menu-container" ),
    		scrollContent = $( "#menu" );
        var handleHelper = $( ".scroll-bar .ui-handle-helper-parent" );
        scrollContent.stop().animate( { marginLeft: initContentLeft }, 300 );
        
        var handleLeft = 100*initHandleLeft / handleHelper.width();
        $( ".scroll-bar .ui-slider-handle" ).stop().animate( { left: handleLeft+'%' }, 300 );
    });
    $('.scroll-minus').mousedown(function(){
        scrollBtnActive=true;
        scrollMinus();
    });
    $(document).mouseup(function(){
        if( ! scrollBtnActive ){ return; }
        $( "#menu" ).stop();
        clearTimeout(stHolder);
        scrollBtnActive=false;
    });
    $('.scroll-plus').mousedown(function(){
        scrollBtnActive=true;
        scrollPlus();
    });
    $(document).mouseup(function(){
        if( ! scrollBtnActive ){ return; }
        $( "#menu" ).stop();
        clearTimeout(stHolder);
        scrollBtnActive=false;
    });
    
    
    /* 設定書卷Scroll Handler的起始位置 */
    if( $('#menu .active a').length > 0 ){
        var target = $('#menu .active a');
        //alert(target.eq(0).position().left+' =? '+target[0].offsetLeft);
        //var offsetLeft = ( target[0].offsetLeft - ( $("#menu-container").width()-target.width() )/2 );
        var offsetLeft = ( target.eq(0).position().left - ( $("#menu-container").width()-target.width() )/2 );
    }else{
        var target = $('#menu a');
        //alert(target.eq(39).position().left+' =? '+target[39].offsetLeft);
        //var offsetLeft = ( target[39].offsetLeft - ( $("#menu-container").width()-target.width() )/2 );
        var offsetLeft = ( target.eq(39).position().left - ( $("#menu-container").width()-target.width() )/2 );
    }
    //var maxLeft = $('#menu')[0].offsetWidth - $('#menu-container')[0].offsetWidth ;
    var maxLeft = $('#menu').width() - $('#menu-container').width() ;
    if( offsetLeft < 0 ) offsetLeft = 0;
    if( offsetLeft > maxLeft ) offsetLeft = maxLeft;
    $('#menu').css('margin-left', '-'+ offsetLeft +'px' );
        
    /* 顯示操作說明 */
    var showTooltip=false;
    $(".tooltip-button").mouseover( function(){
        if( showTooltip ) return;
        $(".tooltip").stop().css({'top':'60px', 'opacity':0, display:'block'}).animate({'top':'55px', 'opacity':1}, 200);
        //showTooltip=true;
    } ).mouseout( function(){
        if( showTooltip ) return;
        $(".tooltip").stop().animate({'top':'50px', 'opacity':0}, 200);
        //showTooltip=false;
    } ).click( function(){
        showTooltip = ! showTooltip;
        if( ! showTooltip ){
            $(".tooltip").stop().animate({'top':'50px', 'opacity':0}, 200);
            $(this).addClass('tooltip-button').removeClass('tooltip-button-active');
        }
        if( showTooltip ){
            $(this).addClass('tooltip-button-active').removeClass('tooltip-button');
        }
    } );
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
        .focus(function(){ return false; })
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
		initContentLeft = leftVal; //紀錄Content起始點
		initHandleLeft = handleLeft; //紀錄起始點
		
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