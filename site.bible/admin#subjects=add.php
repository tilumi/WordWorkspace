<?php
include('layout_admin/tpl_header.php');
include('layout_admin/helper.blocks.php');
list( $form , $years , $wtypes , $data ) = APP::$appBuffer;
$mainTitle = APP::$mainTitle;
$mainName = APP::$mainName;

$worshiped=strtotime($data['worshiped']);
$year=date('Y', $worshiped);
$month=date('n', $worshiped);
$day=date('j', $worshiped);
$wday=date('w', $worshiped);
$numdays=date('t', $worshiped);
$wdays=array(
    '日',
    '一',
    '二',
    '三',
    '四',
    '五',
    '六',
);

?>
            <!-- Form elements -->    
            <div class="grid_12">
<p>
<?php echo View::anchor('/', '管理首頁'); ?>
 »
<?php echo View::anchor('.', $mainTitle); ?>
 »
<?php echo APP::$pageTitle; ?>
</p>

<?php echo redirect_message(); ?>

                <?php echo Blocks::mainTitle( APP::$mainTitle ); ?>
            </div>


<script>
var wdays=new Array('日','一','二','三','四','五','六');
function renewWDays(){ //更新星期幾的標示
    var year=$('#worshiped_year').get(0).value;
    var month=$('#worshiped_month').get(0).value;
    var day=$('#worshiped_day').get(0).value;
    var wday= new Date(year, month-1, day).getDay();
    wday=parseInt(wday);
    $('#worshiped_wday')[0].innerHTML = '星期' + wdays[ wday ];
    $('#worshiped_wday')[0].style.backgroundColor='';
    $('#worshiped_wday')[0].style.color='';
    $('#worshiped_wtype_id').val('other');
    if( wday==0 ){
        $('#worshiped_wday')[0].style.backgroundColor='#F62217';
        $('#worshiped_wday')[0].style.color='white';
        $('#worshiped_wtype_id').val('LordDay');
    }
    if( wday==3 ){
        $('#worshiped_wday')[0].style.backgroundColor='#52D017';
        $('#worshiped_wday')[0].style.color='white';
        $('#worshiped_wtype_id').val('Wednesday');
    }
    showWtypeName();
}
function renewMDays(){ //更新一個月中的天數
    var year=$('#worshiped_year').get(0).value;
    var month=$('#worshiped_month').get(0).value;
    var mdays=getMonthDays(year,month);
    $('#worshiped_day').html('');
    var checked_selected=false; //一律顯示第一個星期日
    for( var i=1;i<=mdays;i++ ){
        wday= new Date(year, month-1, i).getDay();
        style=''; selected='';
        if( wday==0 ){
            style+='background-color:#F62217;';
            style+='color:white;';
            if( ! checked_selected ){ selected='selected'; checked_selected=true; }
        }
        if( wday==3 ){
            style+='background-color:#52D017;';
            style+='color:white;';
        }
        html='<option value="'+i+'" style="'+style+'" '+selected+'>'+i+'日('+wdays[ wday ]+')</option>';
        
        $('#worshiped_day').html( $('#worshiped_day').html()+ html );
    }
    //因為一律顯示第一個星期日 ...
    $('#worshiped_wday')[0].innerHTML = '星期日';
    $('#worshiped_wday')[0].style.backgroundColor='#F62217';
    $('#worshiped_wday')[0].style.color='white';
    $('#worshiped_wtype_id').val('LordDay');
    showWtypeName();
}
function showWtypeName(){
    if( $('#worshiped_wtype_id').val() == 'other' ){
        $('#worshiped_wtype_name')[0].style.display='inline';
    }else{
        $('#worshiped_wtype_name')[0].style.display='none';
    }
}
function setDefault(){
    year=$('#worshiped_year').attr('value','<?php echo $year; ?>');
    month=$('#worshiped_month').attr('value','<?php echo $month; ?>');
    renewMDays();
    $('#worshiped_day').attr('value','<?php echo $day; ?>');
    renewWDays();
}
function getMonthDays(year,month){ //取得指定月份中的天數
    var date= new Date(year,month,0);
    return date.getDate();
}
</script>
<style>
#worshiped_wday { display:inline-block;width:70px;font-size:12px;text-align:center; }
</style>

        <form method="post" action="<?php echo APP::$ME; ?>">

            <div class="grid_6">
                <div class="module">
                    <h2><span>禮拜主題</span></h2>
                    <div class="module-body">
                        <p>
                            日期<br>
                            <select name="worshiped[year]" id="worshiped_year" class="input-medium" onchange="javascript: renewMDays();">
<?php                       foreach( $years as $key=>$value ){ ?>
                                <option value="<?php echo $key; ?>"<?php echo ( $key==$year )?' selected':'';?>><?php echo $value; ?></option>
<?php                       } ?>
                            </select>
                            <input type="button" class="submit-gray" value="日期重設" onclick="javascript: setDefault();" />
                            <div>
                            月份
                            <select name="worshiped[month]" id="worshiped_month" class="input" onchange="javascript: renewMDays();">
<?php                       for( $i=1;$i<=12;$i++ ){ ?>
                                <option value="<?php echo $i; ?>"<?php echo ( $i==$month )?' selected':'';?>><?php echo $i; ?>月</option>
<?php                       } ?>
                            </select>
                            日數
                            <select name="worshiped[day]" id="worshiped_day" class="input" onchange="javascript: renewWDays();">
<?php                       
                            for( $i=1;$i<=$numdays;$i++ ){
                                $tmp_wday=date('w', mktime(0,0,0,$month,$i,$year));
                                $style='';
                                if( $tmp_wday==0 ){ $style='color:white;background-color:#F62217;'; }
                                if( $tmp_wday==3 ){ $style='color:white;background-color:#52D017;'; }
?>
                                <option value="<?php echo $i; ?>"<?php echo ( $i==$day )?' selected':'';?> style="<?php echo $style; ?>"><?php echo $i; ?>日(<?php echo $wdays[ $tmp_wday ]; ?>)</option>
<?php                       } ?>
                            </select>
<?php
                            $style='';
                            if( $wday==0 ){ $style='color:white;background-color:#F62217;'; }
                            if( $wday==3 ){ $style='color:white;background-color:#52D017;'; }
?>
                            <b><span id="worshiped_wday" style="<?php echo $style; ?>">星期<?php echo $wdays[$wday];?></span></b>
                        </p> 
                        <p>
                            禮拜類型<br>
                            <select name="wtype_id" id="worshiped_wtype_id" class="input" onchange="javascript: showWtypeName();">
<?php                       foreach( $wtypes as $wtype_id=>$wtype_name ){ ?>
                                <option value="<?php echo $wtype_id; ?>"<?php echo ( $wtype_id==$data['wtype_id'] )?' selected':'';?>><?php echo $wtype_name; ?></option>
<?php                       } ?>
                                <option value="other">其他</option>
                            </select>
                            <input type="text" name="wtype_name" id="worshiped_wtype_name" class="input" style="<?php echo ($r['wtype_id']=='other')?'display:inline;':'display:none;'; ?>" />
                            </div>
                        </p> 
                        <p>
                            中文主題(橫式)<br>
                            <input class="input-long" name="name_zh" type="text" value="<?php echo $r['name_zh'];?>" onkeyup="<?php if( empty($r['name_zh']) ){ echo "javascript: $('#name_zh_unfold').val( this.value )"; } ?>" />
                        </p> 
                        <p>
                            中文主題(展開)<br>
                            <textarea class="input-long" style="height:70px;" name="name_zh_unfold" id="name_zh_unfold"><?php echo $r['name_zh_unfold'];?></textarea>
                        </p> 
                        <p>
                            韓文主題(橫式)<br>
                            <input class="input-long" name="name_kr" type="text" value="<?php echo $r['name_kr'];?>" onkeyup="<?php if( empty($r['name_kr']) ){ echo "javascript: $('#name_kr_unfold').val( this.value )"; } ?>" />
                        </p> 
                        <p>
                            韓文主題(展開)<br>
                            <textarea class="input-long" style="height:70px;" name="name_kr_unfold" id="name_kr_unfold"><?php echo $r['name_kr_unfold'];?></textarea>
                        </p> 
                        <p>
                            <br>
                            <input type="hidden" name="" value="cancel" class="hidden-cancel">
                            <input class="submit-green" name="commit" value="送出" type="submit" />&nbsp;<input class="submit-gray" name="" value="重設" type="reset" />&nbsp;<input class="submit-gray" onclick="$('.hidden-cancel').attr('name', 'cancel');this.form.submit();" name="" value="取消" type="button" />
                        </p> 

                        <div style="clear: both;"></div>
                    </div> <!-- End .module-body -->
                </div> <!-- End .module -->
            </div>
            <div class="grid_6">
                <div class="module">
                    <h2><span>引用經文</span></h2>
                    <div class="module-body">
                    
                        <div style="clear: both;"></div>
                    </div> <!-- End .module-body -->
                </div> <!-- End .module -->

                <div class="module">
                    <h2><span>全體讚美</span></h2>
                    <div class="module-body">
                    
                        <div style="clear: both;"></div>
                    </div> <!-- End .module-body -->
                </div> <!-- End .module -->

                <div class="module">
                    <h2><span>其他資訊</span></h2>
                    <div class="module-body">
                        <p>
                            傳講地點<br>
                            <input type="text" name="location" class="input-long" value="<?php echo $r['location']; ?>" />
                        </p>
                        <p>
                            其他說明<br>
                            <textarea class="input-long" style="height:70px;" name="info" id="info"><?php echo $r['info'];?></textarea>
                        </p> 
                        <div style="clear: both;"></div>
                    </div> <!-- End .module-body -->
                </div> <!-- End .module -->
            </div>

        </form>

        		<div style="clear: both;"></div>
                
<?php
include('layout_admin/tpl_footer.php');
?>