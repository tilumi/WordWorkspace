<?php
list( $startIndex ) = APP::$appBuffer;
?>
$(document).ready( function(){
    $("#menu-container").jCarouselLite({
        visible: 9,
        scroll: 4,
        mouseWheel: true,
        start: <?php echo $startIndex; ?>,
        btnNext: ".bookNavNext",
        btnPrev: ".bookNavPrev"
    });
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
            href:url
        });
    });
    $('.bookNav').append('<div id="chapter-guide"></div>');
});

