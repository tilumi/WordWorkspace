<?php
list( $rows , $nav ) = APP::$appBuffer;
$tab_id = $nav['book_id'];
$book_on_top = $nav;
$fixing_topnav = true;
View::setHeader('javascripts', 'page.scroller.js');
include('layout_main/tpl_header-style2.php');

$prev_url='';
if( $nav['prev']!==array() )
    $prev_url=url('/'.$nav['prev']['book_name'].'/'.$nav['prev']['chapter_id'].'.html');
$next_url='';
if( $nav['next']!==array() )
    $next_url=url('/'.$nav['next']['book_name'].'/'.$nav['next']['chapter_id'].'.html');
?>
<script>
$(document).keydown(function(e){
    var e=window.event?window.event:e;
    /* left */
    if( e.keyCode == 37 <?php echo ($nav['prev']===array())?'&& false':''; ?> ){ location.href="<?php echo $prev_url;?>"; }
    /* right */
    if( e.keyCode == 39 <?php echo ($nav['next']===array())?'&& false':''; ?> ){ location.href="<?php echo $next_url;?>"; }
});
$(document).ready( function(){
    var highlight = $('a.highlight');
    highlight.removeAttr("name"); 
    if( highlight.length > 0 ){
        var $body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
        $body.animate({
            scrollTop: highlight.offset().top - 140
        }, 300);
    }
});
</script>

<style>
#content{
    font-size:16px;
    line-height:28px;
    color:black;
}
#content h1{
    font-size:28px;
}
#content h2{
    font-size:21px;
    margin:30px 0 15px 0;
}
#content .script .verse{
    margin-right:5px;
}
#content .script .words, #content .script .verse{
	/*font-family: Georgia, "蘋果儷黑體", "微軟正黑體", "新細明體", "Times New Roman", Times, serif;*/
	font-family: Arial, "蘋果儷黑 Pro", "新細明體", "Trebuchet MS", Tahoma, Verdana, Arial, Helvetica, sans-serif;
}
.even{
    background-color:#eaf4f9;
}
.chapNav span{ margin:0 5px; color:#ccc; }
.subject h1.title{ margin:0; }
.psalms_scroll h1.title{  }
.psalms_title h2.title{ margin:0; }
.psalms_note h3.title{ font-size:14px; }
.cunp{ display:none; }
</style>

<?php ob_start(); //建立上下章導引列 ?>
<div class="chapNav">
<?php if( count($nav['prev']) > 0 ){ ?>
    <span style="color:#999;font-size:12px;">上一章</span><a href="<?php echo $prev_url; ?>"><?php echo $nav['prev']['name']?></a>
<?php }else{ echo '已達卷首'; }?>
    
    <span>|</span>
    
<?php if( count($nav['next']) > 0 ){ ?>
    <span style="color:#999;font-size:12px;">下一章</span><a href="<?php echo $next_url; ?>"><?php echo $nav['next']['name']?></a>
<?php }else{ echo '已至卷尾'; }?>
    <span>|</span>
    <h1 style="display:inline;font-size:16px;" class="title"><?php echo $nav['name']; ?></h1>
    <span style="color:#999;font-size:12px;margin-left:20px;">
    快速鍵：
    上一章 <img src="<?php echo layout_url( 'main', '/images/keyboard_left.png'); ?>" width="20px" />
    下一章 <img src="<?php echo layout_url( 'main', '/images/keyboard_right.png'); ?>" width="20px" />
    </span>
</div>
<?php $chapNav=ob_get_contents(); ?>
<?php ob_end_clean(); ?>

			<div id="page" class="single container">
				<div id="content">
					<div class="box-style">


<?php echo $chapNav; ?>

<div style="height:40px;"></div><!-- 導覽列與標題的間距 --> 
                        

<?php
$prev_stype='';
$highlight_1st=false;
$prev_chapter=false;
foreach( $rows as $r ){
    //if( ! in_array($r['stype_id'], array('g','h')) ){ continue; } //不顯示所有小標題
    foreach($r as $key=>$value){$r[$key]=$value;}
    if( $prev_chapter && $prev_chapter <> $r['chapter_id'] ){ //跨章顯示時，章與章之間需要間隔
        echo '<div style="height:20px;"></div>'."\n";
?>
<div class="subject">
    <h2 class="title"><?php echo $nav['book_name'].' '.$r['chapter_id'].' '.$nav['unit']; ?></h2>
</div>

<?php
        echo '<div style="height:20px;"></div>'."\n";
    }
    if( isset( $r['highlight'] ) && ! $highlight_1st ){ //跨章顯示時，章與章之間需要間隔
        $highlight_1st = true;
        echo '<a name="highlight" class="highlight"></a>'."\n";
    }
    switch( $r['stype_id'] ){
        case 'a':
            //在這裡控制 標題 與 上一段經文 的間距
            if( in_array($prev_stype, array('g', 'h')) ){
                echo '<div style="height:30px;"></div>'."\n"; 
            }
?>
<div class="subject">
    <h2 class="title"><?php echo $r['name']; ?>
    <?php //if( ! empty($r['relate']) ){echo '（'.$r['relate'].'）';}?></h2>
</div>

<?php
            break;
        case 'b':
?>
<div class="songs_anthem">
    <h2 class="title" style="margin:15px 0 10px 0;"><?php echo $r['name']; ?> <?php if( ! empty($r['relate']) ){echo '（'.$r['relate'].'）';}?></h2>
</div> 

<?php
            break;
        case 'c':
?>
<div class="songs_role">
    <?php echo $r['name']; ?>
</div> 

<?php
            break;
        case 'd':
?>
<div class="psalms_scroll">
    <h1 class="title" style="margin:0 0 30px 0;"><?php echo $r['name']; ?> <?php if( ! empty($r['relate']) ){echo '（'.$r['relate'].'）';}?></h1>
</div> 

<?php
            break;
        case 'e':
            //在這裡控制 經文 與 下段標題 的間距
            if( in_array($prev_stype, array('g', 'h')) ){
                echo '<div style="height:30px;"></div>'."\n"; 
            }
?>
<div class="psalms_title">
    <h2 class="title" style="margin:0;"><?php echo $r['name']; ?>
    <?php if( ! empty($r['relate']) ){echo '（'.$r['relate'].'）';}?></h2>
</div> 

<?php
            break;
        case 'f':
?>
<div class="psalms_note">
    <h3 class="title" style="margin:0;"><?php echo $r['name']; ?></h3>
</div> 

<?php
            break;
        case 'g':
            //在這裡控制 標題 與 經文 的間距
            if( ! in_array($prev_stype, array('b', 'c', 'd', 'g', 'h')) ){
                echo '<div style="height:10px;"></div>'."\n"; 
            }
?>
<div class="script<?php
    echo ( $r['verse_id']%2 )?' odd':' even';
    ?>">
    <span class="verse"><?php echo $r['chapter_id'].':'.$r['verse_id']; ?></span>
    <span class="words<?php echo ( isset($r['highlight']) )?' highlight':''; ?>"><?php echo nl2br($r['name']);?></span>
</div>

<?php
            break;
    }
    $prev_stype = $r['stype_id'];
    $prev_chapter = $r['chapter_id'];
}
?>
<div style="height:50px;"></div>

<?php echo $chapNav; ?>
<div style="height:50px;"></div>
                    </div>
				</div>
			</div>


<?php
include('layout_main/tpl_footer.php');
?>