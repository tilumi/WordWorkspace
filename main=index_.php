<?php
list( $rows ) = APP::$appBuffer;
$selectIndex = 40;
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
    $('.bookNav').append('<div id="chapter-guide"></div>');
});
</script>
<?php } ?>


<style>
.sky { float:left;height:450px;font-family: Georgia, "蘋果儷黑體", "微軟正黑體", "新細明體", "Times New Roman", Times, serif; }
.cloud a{ text-decoration:none; }
.cloud a:hover{ text-decoration:underline; }
</style>
			<div id="page" class="single container">
				<div id="content">
				    <div class="clearfix" style="border-bottom:1px solid #CCC;height:380px;margin-bottom:40px;">
    					<div id="box5" class="box-style sky" style="width:550px;height:auto;">
    					    <h2 class="title">舊約 Old Testament</h2>
    					    <div class="cloud" style="line-height:42px;">
<?php for( $i=0;$i<39;$i++ ){ $r=$rows[$i]; $fontsize = ceil(12 + 38*($r['max_chapter']/150) ); ?>
                            <a href="<?php echo $r['name']; ?>.html" style="font-size:<?php echo $fontsize; ?>px;" rel="bible-index" name="<?php echo $r['name']; ?>"><?php echo $r['name']; ?></a>
                            &nbsp;
<?php } ?>
                            </div>
    					</div>
    					<div id="box5" class="box-style sky" style="width:300px;margin-left:50px;height:auto;">
    					    <h2 class="title">新約 New Testament</h2>
    					    <div class="cloud" style="line-height:34px;">
<?php for( $i=39;$i<66;$i++ ){ $r=$rows[$i]; $fontsize = ceil(12 + 14*($r['max_chapter']/28) ); ?>
                                <a href="<?php echo $r['name']; ?>.html" style="font-size:<?php echo $fontsize; ?>px;" rel="bible-index" name="<?php echo $r['name']; ?>"><?php echo $r['name']; ?></a>
                                &nbsp;
<?php } ?>
        					</div>
    					</div>
					</div>
					<div class="box5 box-style" style="width:65%;float:left;">
                        <div class="clearfix" style="padding-bottom:20px;">
    						<h2 class="title">分享這個網站</h2>
    						<p>喜歡這個網站嗎？請幫忙推薦我們 ...
                            <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
                            <g:plusone></g:plusone>
                            
                            <span id="fb-root"></span>
                            <script src="http://connect.facebook.net/en_US/all.js#appId=256358261057256&amp;xfbml=1"></script>
                            <fb:like href="http://bible.jbride.cc" send="true" layout="button_count" width="100" show_faces="true" font=""></fb:like>
                            </p>

<p>或分享給你的朋友喔 ...</p>
<p>
<?php $url_encode=urlencode('http://bible.jbride.cc'); ?>
<?php $title_encode=urlencode('The Bible 線上聖經'); ?>
<?php $link="http://twitter.com/home?status=".$title_encode.$url;?>

<a class="social-link normal" target="_blank" title="Plurk" href="javascript: void(window.open('http://www.plurk.com/?qualifier=shares&status=' .concat(encodeURIComponent(document.title)) .concat(' '). concat(encodeURIComponent(location.href)) ));">
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