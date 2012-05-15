/*
這是提供網站主選單顯示副選單的簡易js
本js需使用到jquery，使用前請確定jquery已經載入
*/
var submenu = new Object();
submenu.prefix='submenu'; //ID命名的前置文字, ex: submenu-0, submenu-1, ...  此時prefix即應設置為submenu
submenu.items=null;
submenu.itemsWidth=null;
submenu.show=function(no){
    submenu.hideAll();
    if( !submenu.items ) return;
    if( no==0 ) return;
    i=parseInt(no)-1;

    if( submenu.items[i] ){
        submenu.items[i].style.display='inline';
        //動態將副選單移動到主選項下並置中
        //公式：
        //margin-left = 主選項left + (主選項width/2) - (副選單width/2) + 不完全置中修正值 (13/2=6)px
        // 0 <= margin-left <= (Browser width - 副選單width)
        var browser=parseInt( Browser.getWidth() );
        var left=parseInt( getPosition($('#nav li').eq(i)[0]).x );
        var width=parseInt( getSize($('#nav li').eq(i)[0]).w );
        var subwidth=submenu.itemsWidth[i];
        var fix=parseInt(10);
        
        var marginLeft=(left)+parseInt(width/2)-parseInt(subwidth/2)+fix;
        
        if( marginLeft<0 ) marginLeft=0;
        if( marginLeft>(browser-subwidth) ){
            $(submenu.items[i]).css('float','right');
            $(submenu.items[i]).css('margin-right','10px');
        }else{
            $(submenu.items[i]).css('position','absolute');
            $(submenu.items[i]).css('left',marginLeft+'px');
        }
        
        var debug=false;
        //debug=true;
        if( debug ){
            $('#test').text( $('#test').text()+(left)+'px (left)' );
            $('#test').text( $('#test').text()+' + '+parseInt(width/2)+'px (width/2)' );
            $('#test').text( $('#test').text()+' - '+parseInt(subwidth/2)+'px (subwidth/2)' );
            $('#test').text( $('#test').text()+' + '+fix+'px (fix)' );
            $('#test').text( $('#test').text()+' = '+marginLeft+'px' );
            $('#test').text( $('#test').text()+' ::: '+submenu.prefix+'-'+no+'' );
        }
    }
};
submenu.hideAll=function(){
    if( !submenu.items ) return;
    for(var i=0;i<submenu.items.length;i++){
        submenu.items[i].style.display='none';
    }
};
$(document).ready(function(){
    var items=new Array();
    var itemsWidth=new Array();
    var prefix=submenu.prefix;
    var no=1;
    var obj=document.getElementById(prefix+'-'+no);
    while(obj){
        i = parseInt(no)-1;
        items[ i ]=obj;
        itemsWidth[ i ]=parseInt( getSize( obj ).w );
        no=parseInt(no)+1;
        obj=document.getElementById(prefix+'-'+no);
    }
    submenu.items=items;
    submenu.itemsWidth=itemsWidth;
    submenu.hideAll();
    /*
    $('#test').html( $('#test').html()+'Width: '+Browser.getWidth()+'px<br>' );
    var id=4;
    var width=getSize($('#menu div').filter('.item').eq(id)[0]).w;
    var height=getSize($('#menu div').filter('.item').eq(id)[0]).h;
    var top=getPosition($('#menu div').filter('.item').eq(id)[0]).y;
    var left=getPosition($('#menu div').filter('.item').eq(id)[0]).x;
    var smwidth=getSize($('#submenu-'+id).eq(0)[0]).w;
    $('#test').html( $('#test').html()+$('#menu div').filter('.item').eq(id).text().substr(2)+'<br> top: '+top+'px' );
    $('#test').html( $('#test').html()+' left: '+left+'px<br>' );
    $('#test').html( $('#test').html()+' width: '+width+'px' );
    $('#test').html( $('#test').html()+' height: '+height+'px<br>' );
    $('#test').html( $('#test').html()+' #menu padding-left: '+$('#menu').eq(0).css('padding-left').replace('px','')+'px' );
    $('#test').html( $('#test').html()+' #submenu width: '+smwidth+'px' );
    */
});

var Browser = new Object();
//取得瀏覽器視窗高度
Browser.getHeight=function() {
    if ($.browser.msie) {
        return document.compatMode == "CSS1Compat" ? document.documentElement.clientHeight :
                 document.body.clientHeight;
    } else {
        return self.innerHeight;
    }
};

//取得瀏覽器視窗寬度
Browser.getWidth=function() {
    if ($.browser.msie) {
        return document.compatMode == "CSS1Compat" ? document.documentElement.clientWidth :
                 document.body.clientWidth;
    } else {
        return self.innerWidth;
    }
};
function getPosition(e){
    var left = 0;
    var top  = 0;

    while (e.offsetParent){
        left += e.offsetLeft;
        top  += e.offsetTop;
        e     = e.offsetParent;
    }
    left += e.offsetLeft;
    top  += e.offsetTop;
    return {x:left, y:top};
}
function getSize(e){
    var width = 0;
    var height = 0;
    
    width = e.offsetWidth;
    height = e.offsetHeight;
    
    return {w:width, h:height};
}
