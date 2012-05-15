$(document).ready( function(){
    $("a[rel=bible-book]").bind('click', function() {
        alert( this.innerHTML );
        return false;
    });
});
