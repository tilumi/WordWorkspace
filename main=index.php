<?php
list( $rows ) = APP::$appBuffer;
$selectIndex = 40;
$homeIndex = true;
include('tpl_header.php');
?>

<?php $using_ie6 = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.') !== false ); ?>
<?php if( ! $using_ie6 ){ //ie6的使用者，不啟用JS功能 ?>
<script>
$(document).ready( function(){
    $("a[rel='bible-index']").each( function(){
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
    $(".scrolls").mouseover( function(){
        $(this).find("img").css('opacity', 1);
    } ).mouseout( function(){
        $(this).find("img").css('opacity', 0.5);
    } );
});
</script>
<?php } ?>


<style>
.sky {
    height:450px;
    font-family: Georgia, "蘋果儷黑體", "微軟正黑體", "新細明體", "Times New Roman", Times, serif;
}
.ot{ width:330px; }
.nt{ width:320px; }
.blocks-ot{ float:left;width:120px; }
#block-ot-1{ width:113px; }
#block-ot-2{ width:105px; }
#block-ot-3{ width:112px; }
.blocks-nt{ float:left;width:150px; }
#block-nt-1{ width:150px; }
#block-nt-2{ width:160px; }

.cloud{ line-height:24px; }
.cloud a{ text-decoration:none;letter-spacing:-1px; }
.cloud a:hover{ text-decoration:underline; }
.cloud .chaps{ color:#000;font-size:13px;font-weight:bold;font-family:Arial; }
.cloud img{ width:12px;height:12px;filter: alpha(opacity=50);-moz-opacity:.50;opacity:.50; }
.cloud .brief{ font-size:12px;color:#999;margin-left:3px; }
.cloud .catalog{ font-size:16px; }

.ts { font-family: Georgia, "蘋果儷黑體", "微軟正黑體", "新細明體", "Times New Roman", Times, serif;margin-bottom:20px; }
.social-link img { height:30px; }

</style>
			<div id="page" class="single container">
				<div id="content">
				    <div class="clearfix" style="border-bottom:1px solid #CCC;margin-bottom:40px;">
    					<div id="box5" class="ot box-style sky" style="float:left;">
    					    <h2 class="title">舊約 Old Testament</h2>
    					    <div class="cloud" style="line-height:24px;">
<?php
    $max=39;
    $blockChaps=13;
    
    $loops = ceil($max/$blockChaps); //將要產生的區塊數
    $blocks = array();
    $pointer=0;
    for( $i=1;$i<=$loops;$i++ ){
        $html='';
        $items=0;
        $r=pos($rows);
        //$html.='<h2 class="title">'.$ch_start.' ~ '.$ch_end.'</h2>';
        while( $r && $pointer < $max && $items < $blockChaps ){
            $items+=1;
            $pointer+=1;
            
            $html.='<div class="scrolls">';
            $html.='<a class="brief" href="'.$r['name'].'.html" title="'.$r['name'].' 簡介與概要"><img src="'.url('/cabinets/book-icon.png').'" alt="概要"></a>';
            $html.=' <a class="catalog" href="'.$r['name'].'.html" rel="bible-index" name="'.$r['name'].'" title="'.$r['name'].' 目錄">'.$r['name'].'</a>';
            $html.=' <span class="chaps">'.$r['max_chapter'].'</span>';
            $html.='</div>'."\n";
            
            $r=next($rows);
        }
        echo '<div id="block-ot-'.$i.'" class="blocks-ot">'."\n";
        echo $html;
        echo '</div>'."\n";
    }
?>


                            </div>
    					</div>
    					<div id="box5" class="nt box-style sky" style="float:right;">
    					    <h2 class="title">新約 New Testament</h2>
    					    <div class="cloud" style="line-height:24px;">
<?php
    $max=66;
    $blockChaps=14;
    
    $loops = ceil($max/$blockChaps); //將要產生的區塊數
    $blocks = array();
    for( $i=1;$i<=$loops;$i++ ){
        $html='';
        $items=0;
        $r=pos($rows);
        //$html.='<h2 class="title">'.$ch_start.' ~ '.$ch_end.'</h2>';
        while( $r && $pointer < $max && $items < $blockChaps ){
            $items+=1;
            $pointer+=1;
            
            $html.='<div class="scrolls">';
            $html.='<a class="brief" href="'.$r['name'].'.html" title="'.$r['name'].' 簡介與概要"><img src="'.url('/cabinets/book-icon.png').'" alt="概要"></a>';
            $html.=' <a class="catalog" href="'.$r['name'].'.html" rel="bible-index" name="'.$r['name'].'" title="'.$r['name'].' 目錄">'.$r['name'].'</a>';
            $html.=' <span class="chaps">'.$r['max_chapter'].'</span>';
            $html.='</div>';
            
            $r=next($rows);
        }
        $width='50%';
/*        $width='50%';
        //一個區塊的寬度預設都是一半的空間，但若是最後一個且靠左（單數），可以使用全部空間（100%）
        if( $i==$loops && $i%2==1 ){ $width='100%'; } */
        echo '<div id="block-nt-'.$i.'" class="blocks-nt">'."\n";
        echo $html;
        echo '</div>'."\n";
    }
?>

        					</div>
    					</div>
    					<div id="middle-area" style="position:relative;width:200px;height:450px;background:url(<?php echo layout_url('main','/images/jesus.png'); ?>) center top;margin:0 auto;">
                        </div>
					</div>
					<div class="box5 box-style" style="width:65%;float:left;">
                        <div class="clearfix" style="padding-bottom:20px;margin-bottom:30px;">
    						<h2 class="title">分享這個網站</h2>
    						<p>喜歡這個網站嗎？請幫忙推薦我們 ...
                            <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
                            <g:plusone size="medium"></g:plusone>
                            
                            <span id="fb-root"></span>
                            <script src="http://connect.facebook.net/en_US/all.js#appId=256358261057256&amp;xfbml=1"></script>
                            <fb:like href="http://bible.jbride.cc" send="true" layout="button_count" width="100" show_faces="true" font=""></fb:like>
                            <br>
                            或分享給你的朋友喔 ...

<?php $url_encode=urlencode('http://bible.jbride.cc'); ?>
<?php $title_encode=urlencode('The Bible 線上聖經'); ?>
<?php $link="http://twitter.com/home?status=".$title_encode.$url;?>

<a class="social-link normal" title="Plurk" href="javascript: void(window.open('http://www.plurk.com/?qualifier=shares&status=' .concat(encodeURIComponent(document.title)) .concat(' '). concat(encodeURIComponent(location.href)) ));">
<img src="<?php echo layout_url('main', '/images/social_icons/1311129562_plurk.png');?>" /></a>
<a class="social-link normal" target="_blank" title="Twitter" href="http://twitter.com/home?status=<?php echo $title_encode; ?>%20-%20<?php echo $url_encode; ?>">
<img src="<?php echo layout_url('main', '/images/social_icons/1311129481_twitter.png');?>" /></a>
<a class="social-link normal" target="_blank" title="Facebook" href="http://www.facebook.com/share.php?u=<?php echo $url_encode; ?>&amp;t=<?php echo $title_encode; ?>">
<img src="<?php echo layout_url('main', '/images/social_icons/1311129446_facebook.png');?>" /></a>
<a class="social-link normal" target="_blank" title="del.icio.us" href="http://delicious.com/post?url=<?php echo $url_encode; ?>&amp;title=<?php echo $title_encode; ?>">
<img src="<?php echo layout_url('main', '/images/social_icons/1311129498_delicious.png');?>" /></a>
<a class="social-link normal" target="_blank" title="Digg" href="http://digg.com/submit?phase=2&amp;url=<?php echo $url_encode; ?>&amp;title=<?php echo $title_encode; ?>">
<img src="<?php echo layout_url('main', '/images/social_icons/1311129509_digg.png');?>" /></a>
<!--
<a class="social-link normal" target="_blank" title="Linkedin" href="http://www.linkedin.com/shareArticle?url=<?php echo $url_encode; ?>&amp;title=<?php echo $title_encode; ?>">
<img src="images/social_icons/1311129543_linkedin.png" /></a>
-->
                            </p>

                        </div>
                    </div>
					<div id="sidebar" style="padding-top:0px;">
    					<div class="box5 box-style" style="display:inline-block;vertical-align:top;font-family: Arial;margin-bottom:30px;">
    						<h2 class="title">串連貼紙</h2>
    						<p>如果你喜歡我們，歡迎把這個貼紙貼到你的網站或部落格上喔 ...</p>
    						<?php $sticker='<a href="http://bible.jbride.cc" target="_blank" title="線上聖經"><img src="http://bible.jbride.cc/layout_main/images/sticker.jpg" alt="The Bible 線上聖經"></a>'; ?>
    						<p><?php echo $sticker; ?></p>
    						<textarea style="width:90%;height:100px;"><?php echo htmlspecialchars($sticker); ?></textarea>
    					</div>
    				</div>
				</div>

			</div>

<?php
include('tpl_footer.php');
?>