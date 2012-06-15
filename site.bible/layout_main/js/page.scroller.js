$(document).keydown(function(e){
    var scrollActive = false;
    var scrollToTop = 0;
    var offset=getCurrentPageOffsets();
    var pageTop=offset.y;
    var browserHeight = $(window).height();
    var scrollUnit = browserHeight - 150;
    var scrollInterval = 100;
    switch( e.keyCode ){
        /* space */
        case 32:
        /* [Page Down] */
        case 34:
            scrollActive = true;
            scrollToTop = pageTop + scrollUnit;
            break;
        /* [Page Up] */
        case 33:
            scrollActive = true;
            scrollToTop = pageTop - scrollUnit;
            break;
        /* [End] */
        case 35:
            scrollActive = true;
            scrollToTop = $(document).height();
            break;
        /* [Home] */
        case 36:
            scrollActive = true;
            scrollToTop = 0;
            break;
    }
    if( scrollActive ){
        var $body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
        $body.stop().animate({
            scrollTop: scrollToTop
        }, scrollInterval);
        return false;
    }
});
function getCurrentPageOffsets(w) {
    // Use the specified window or the current window if no argument 
    w = w || window;
    // This works for all browsers except IE versions 8 and before
    if (w.pageXOffset != null) return {x: w.pageXOffset, y:w.pageYOffset};
    // For IE (or any browser) in Standards mode
    var d = w.document;
    if (document.compatMode == "CSS1Compat")
    return {x:d.documentElement.scrollLeft, y:d.documentElement.scrollTop};
    // For browsers in Quirks mode
    return { x: d.body.scrollLeft, y: d.body.scrollTop };
}
