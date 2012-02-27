//Initiate WYIWYG text area
$(function()
{
$('.wysiwyg').wysiwyg(
{
controls : {
separator01 : { visible : true },
separator03 : { visible : true },
separator04 : { visible : true },
separator00 : { visible : true },
separator07 : { visible : false },
separator02 : { visible : false },
separator08 : { visible : false },
insertOrderedList : { visible : true },
insertUnorderedList : { visible : true },
undo: { visible : true },
redo: { visible : true },
justifyLeft: { visible : true },
justifyCenter: { visible : true },
justifyRight: { visible : true },
justifyFull: { visible : true },
subscript: { visible : true },
superscript: { visible : true },
underline: { visible : true },
increaseFontSize : { visible : false },
decreaseFontSize : { visible : false }
}
} );
});

//Initiate tablesorter script
$(document).ready(function() { 
    $.tablesorter.addParser({
        id: 'indexes', 
        is: function(s) {
            // return false so this parser is not auto detected 
            return false; 
        },
        format: function(s) {
            // format your data for normalization
            return s.toLowerCase().replace(/\<input .*type\=\"checkbox\"\>/,'').replace(/\./,'');
        },
        // set type, either numeric or text 
        type: 'numeric'
    });
    $.tablesorter.addParser({
        id: 'rollcalls', 
        is: function(s) {
            // return false so this parser is not auto detected 
            return false; 
        },
        format: function(s) {
            // format your data for normalization
            var type1='<a href="javascript: void(0);" title="尚未開放，等待中">';
            type1+='<img src="/layouts/magicadmin/images/tick-on-white-gray.gif" alt="尚未開放，等待中" width="16" height="16"></a>';
            var type2='<a href="javascript: void(0);" title="開放中">';
            type2+='<img src="/layouts/magicadmin/images/tick-on-white.gif" alt="開放中" width="16" height="16"></a>';
            var type3='<a href="javascript: void(0);" title="已結束">';
            type3+='<img src="/layouts/magicadmin/images/notification-slash.gif" alt="已結束" width="16" height="16"></a>';
            return s.toLowerCase().replace(type1, 1).replace(type2, 2).replace(type3, 3);
        },
        // set type, either numeric or text 
        type: 'numeric'
    });
    $(".myTable").tablesorter({
    	// zebra coloring
    	widgets: ['zebra'],
    	// pass the headers argument and assing a object 
    	headers: {
            0:{ sorter:'indexes' }
    	}
    }).tablesorterPager({container: $("#pager"),size:10,positionFixed: false});
    
    $("a[rel='openSearch']").colorbox({
        transition:"none", width:"50%", inline:true, href:"#openSearch", opacity:0.2,
        onLoad:function(){ $('#openSearch').css('display','block'); },
        onCleanup:function(){ $('#openSearch').css('display','none'); },
    });
}); 

//Initiate password strength script
$(function() {
    //$('.password').pstrength();
    // 設定 verdicts 及 minCharMsg 等值
    $('.password').pstrength({
        verdicts: ["非常弱","弱","普通","強","很強"],
        minCharText: "密碼最少要 %d 位"
    });
    
    // 加上新規則，如果密碼中包含 admin 則扣 10 分
    /*$(".password").pstrength.addRule("isAdmin", function (word, score) {
        return word.indexOf("admin")>-1 && score;
    }, -10, true);
    */
});

/*
這是控制表單操作的js function集
本js需使用到jquery，使用前請確定jquery已經載入
*/
var former=new Object();
former.clear=function clear(container){
    $(container+' input').each(
        function(){
            if(this.type=='text') this.value='';
            if(this.type=='checkbox' || this.type=='radio') this.checked='';
        }
    );
    $(container+' select').each(function(){this.options[0].selected='selected';});
}

var checker={
    all:function(){ $('input[name=items\[\]]').each( function(){ this.checked=true; } ); },
    none:function(){ $('input[name=items\[\]]').each( function(){ this.checked=false; } ); },
    reverse:function(){ $('input[name=items\[\]]').each( function(){ this.checked=!this.checked; } ); }
}

//列表頁批次操作用
var batch={
    operation: function( val , options ){
            var is_checked=false;
            $('input[name=items\[\]]').each( function(){ if( this.checked ){ is_checked=true; } } );
            if( ! is_checked ){
                alert('請至少選擇一個項目');
                $(".table-apply select").attr("value",'');
                return false;
            }
            
            var actionRoute = options[val];
            
            var frmList = $('form[name=frmList]');
            frmList.attr('action', actionRoute );
            frmList.get(0).mode.value=val;
            frmList.submit();
        }
}
