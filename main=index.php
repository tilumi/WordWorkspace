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
    //$('.bookNav').append('<div id="chapter-guide"></div>');
    $("#bible-container").jCarouselLite({
        //auto: 6000,
        speed: 500,
        circular: false,
        //hoverPause: true,
        visible: 5,
        scroll: 5,
        btnNext: '#bible-new',
        btnPrev: '#bible-old'
    });
});
</script>
<?php } ?>


<style>
.sky { float:left;font-family: Georgia, "蘋果儷黑體", "微軟正黑體", "新細明體", "Times New Roman", Times, serif; }
.cloud a{ text-decoration:none; }
.cloud a:hover{ text-decoration:underline; }
.chapter_digit{ color:#000;font-size:13px;font-weight:bold;font-family:Arial; }
</style>
			<div id="page" class="single container">
				<div id="content">
				    <div class="cloud sky clearfix" style="border-bottom:1px solid #CCC;margin-bottom:40px;">
    				    <div class="clearfix">
    				        <div id="bible-old" class="bible-button ui-corner-all"><h2 class="title">舊約 Old Testament</h2></div>
    				        <div id="bible-new" class="bible-button ui-corner-all"><h2 class="title">新約 New Testament</h2></div>
				        </div>
				        <div id="bible-container">
				            <ul>
				                <li>
				                    <div>
                                    <img src="<?php echo url('/cabinets/bible/bible-1.png'); ?>">
<a href="創世記.html" style="font-size:16px;" rel="bible-index" name="創世記">創世記</a> <span class="chapter_digit">50</span> &nbsp;<a href="創世記.html" style="font-size:12px;color:#999;">概要</a><br>
<a href="出埃及記.html" style="font-size:16px;" rel="bible-index" name="出埃及記">出埃及記</a> <span class="chapter_digit">40</span> &nbsp;<a href="出埃及記.html" style="font-size:12px;color:#999;">概要</a><br>
<a href="利未記.html" style="font-size:16px;" rel="bible-index" name="利未記">利未記</a> <span class="chapter_digit">27</span> &nbsp;<a href="利未記.html" style="font-size:12px;color:#999;">概要</a><br>
<a href="民數記.html" style="font-size:16px;" rel="bible-index" name="民數記">民數記</a> <span class="chapter_digit">36</span> &nbsp;<a href="民數記.html" style="font-size:12px;color:#999;">概要</a><br>
<a href="申命記.html" style="font-size:16px;" rel="bible-index" name="申命記">申命記</a> <span class="chapter_digit">34</span> &nbsp;<a href="申命記.html" style="font-size:12px;color:#999;">概要</a><br>
                                    </div>
                                </li>
				                <li>
                                    <img src="<?php echo url('/cabinets/bible/bible-2.png'); ?>">
<span style="font-size:11px;color:#ccc;font-family:Arial;">6 </span> <a href="約書亞記.html" style="font-size:16px;" rel="bible-index" name="約書亞記">約書亞記</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">24</span> &nbsp;<a href="約書亞記.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">7 </span> <a href="士師記.html" style="font-size:16px;" rel="bible-index" name="士師記">士師記</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">21</span> &nbsp;<a href="士師記.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">8 </span> <a href="路得記.html" style="font-size:16px;" rel="bible-index" name="路得記">路得記</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">4</span> &nbsp;<a href="路得記.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">9 </span> <a href="撒母耳記上.html" style="font-size:16px;" rel="bible-index" name="撒母耳記上">撒母耳記上</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">31</span> &nbsp;<a href="撒母耳記上.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">10 </span> <a href="撒母耳記下.html" style="font-size:16px;" rel="bible-index" name="撒母耳記下">撒母耳記下</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">24</span> &nbsp;<a href="撒母耳記下.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">11 </span> <a href="列王紀上.html" style="font-size:16px;" rel="bible-index" name="列王紀上">列王紀上</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">22</span> &nbsp;<a href="列王紀上.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">12 </span> <a href="列王紀下.html" style="font-size:16px;" rel="bible-index" name="列王紀下">列王紀下</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">25</span> &nbsp;<a href="列王紀下.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">13 </span> <a href="歷代志上.html" style="font-size:16px;" rel="bible-index" name="歷代志上">歷代志上</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">29</span> &nbsp;<a href="歷代志上.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">14 </span> <a href="歷代志下.html" style="font-size:16px;" rel="bible-index" name="歷代志下">歷代志下</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">36</span> &nbsp;<a href="歷代志下.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">15 </span> <a href="以斯拉記.html" style="font-size:16px;" rel="bible-index" name="以斯拉記">以斯拉記</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">10</span> &nbsp;<a href="以斯拉記.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">16 </span> <a href="尼希米記.html" style="font-size:16px;" rel="bible-index" name="尼希米記">尼希米記</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">13</span> &nbsp;<a href="尼希米記.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">17 </span> <a href="以斯帖記.html" style="font-size:16px;" rel="bible-index" name="以斯帖記">以斯帖記</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">10</span> &nbsp;<a href="以斯帖記.html" style="font-size:12px;color:#999;">概要</a><br>
                                </li>
				                <li>
                                    <img src="<?php echo url('/cabinets/bible/bible-3.png'); ?>">
<span style="font-size:11px;color:#ccc;font-family:Arial;">18 </span> <a href="約伯記.html" style="font-size:16px;" rel="bible-index" name="約伯記">約伯記</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">42</span> &nbsp;<a href="約伯記.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">19 </span> <a href="詩篇.html" style="font-size:16px;" rel="bible-index" name="詩篇">詩篇</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">150</span> &nbsp;<a href="詩篇.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">20 </span> <a href="箴言.html" style="font-size:16px;" rel="bible-index" name="箴言">箴言</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">31</span> &nbsp;<a href="箴言.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">21 </span> <a href="傳道書.html" style="font-size:16px;" rel="bible-index" name="傳道書">傳道書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">12</span> &nbsp;<a href="傳道書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">22 </span> <a href="雅歌.html" style="font-size:16px;" rel="bible-index" name="雅歌">雅歌</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">8</span> &nbsp;<a href="雅歌.html" style="font-size:12px;color:#999;">概要</a><br>
                                </li>
				                <li>
                                    <img src="<?php echo url('/cabinets/bible/bible-4.png'); ?>">
<span style="font-size:11px;color:#ccc;font-family:Arial;">23 </span> <a href="以賽亞書.html" style="font-size:16px;" rel="bible-index" name="以賽亞書">以賽亞書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">66</span> &nbsp;<a href="以賽亞書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">24 </span> <a href="耶利米書.html" style="font-size:16px;" rel="bible-index" name="耶利米書">耶利米書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">52</span> &nbsp;<a href="耶利米書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">25 </span> <a href="耶利米哀歌.html" style="font-size:16px;" rel="bible-index" name="耶利米哀歌">耶利米哀歌</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">5</span> &nbsp;<a href="耶利米哀歌.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">26 </span> <a href="以西結書.html" style="font-size:16px;" rel="bible-index" name="以西結書">以西結書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">48</span> &nbsp;<a href="以西結書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">27 </span> <a href="但以理書.html" style="font-size:16px;" rel="bible-index" name="但以理書">但以理書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">12</span> &nbsp;<a href="但以理書.html" style="font-size:12px;color:#999;">概要</a><br>
                                </li>
				                <li>
                                    <img src="<?php echo url('/cabinets/bible/bible-5.png'); ?>">
<span style="font-size:11px;color:#ccc;font-family:Arial;">28 </span> <a href="何西阿書.html" style="font-size:16px;" rel="bible-index" name="何西阿書">何西阿書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">14</span> &nbsp;<a href="何西阿書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">29 </span> <a href="約珥書.html" style="font-size:16px;" rel="bible-index" name="約珥書">約珥書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">3</span> &nbsp;<a href="約珥書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">30 </span> <a href="阿摩司書.html" style="font-size:16px;" rel="bible-index" name="阿摩司書">阿摩司書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">9</span> &nbsp;<a href="阿摩司書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">31 </span> <a href="俄巴底亞書.html" style="font-size:16px;" rel="bible-index" name="俄巴底亞書">俄巴底亞書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">1</span> &nbsp;<a href="俄巴底亞書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">32 </span> <a href="約拿書.html" style="font-size:16px;" rel="bible-index" name="約拿書">約拿書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">4</span> &nbsp;<a href="約拿書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">33 </span> <a href="彌迦書.html" style="font-size:16px;" rel="bible-index" name="彌迦書">彌迦書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">7</span> &nbsp;<a href="彌迦書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">34 </span> <a href="那鴻書.html" style="font-size:16px;" rel="bible-index" name="那鴻書">那鴻書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">3</span> &nbsp;<a href="那鴻書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">35 </span> <a href="哈巴谷書.html" style="font-size:16px;" rel="bible-index" name="哈巴谷書">哈巴谷書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">3</span> &nbsp;<a href="哈巴谷書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">36 </span> <a href="西番雅書.html" style="font-size:16px;" rel="bible-index" name="西番雅書">西番雅書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">3</span> &nbsp;<a href="西番雅書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">37 </span> <a href="哈該書.html" style="font-size:16px;" rel="bible-index" name="哈該書">哈該書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">2</span> &nbsp;<a href="哈該書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">38 </span> <a href="撒迦利亞書.html" style="font-size:16px;" rel="bible-index" name="撒迦利亞書">撒迦利亞書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">14</span> &nbsp;<a href="撒迦利亞書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">39 </span> <a href="瑪拉基書.html" style="font-size:16px;" rel="bible-index" name="瑪拉基書">瑪拉基書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">4</span> &nbsp;<a href="瑪拉基書.html" style="font-size:12px;color:#999;">概要</a><br>
                                </li>
				                <li>
                                    <img src="<?php echo url('/cabinets/bible/bible-6.png'); ?>">
<span style="font-size:11px;color:#ccc;font-family:Arial;">40 </span> <a href="馬太福音.html" style="font-size:16px;" rel="bible-index" name="馬太福音">馬太福音</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">28</span> &nbsp;<a href="馬太福音.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">41 </span> <a href="馬可福音.html" style="font-size:16px;" rel="bible-index" name="馬可福音">馬可福音</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">16</span> &nbsp;<a href="馬可福音.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">42 </span> <a href="路加福音.html" style="font-size:16px;" rel="bible-index" name="路加福音">路加福音</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">24</span> &nbsp;<a href="路加福音.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">43 </span> <a href="約翰福音.html" style="font-size:16px;" rel="bible-index" name="約翰福音">約翰福音</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">21</span> &nbsp;<a href="約翰福音.html" style="font-size:12px;color:#999;">概要</a><br>
                                </li>
				                <li>
                                    <img src="<?php echo url('/cabinets/bible/bible-7.png'); ?>">
<span style="font-size:11px;color:#ccc;font-family:Arial;">44 </span> <a href="使徒行傳.html" style="font-size:16px;" rel="bible-index" name="使徒行傳">使徒行傳</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">28</span> &nbsp;<a href="使徒行傳.html" style="font-size:12px;color:#999;">概要</a><br>
                                </li>
				                <li>
                                    <img src="<?php echo url('/cabinets/bible/bible-8.png'); ?>">
<span style="font-size:11px;color:#ccc;font-family:Arial;">45 </span> <a href="羅馬書.html" style="font-size:16px;" rel="bible-index" name="羅馬書">羅馬書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">16</span> &nbsp;<a href="羅馬書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">46 </span> <a href="哥林多前書.html" style="font-size:16px;" rel="bible-index" name="哥林多前書">哥林多前書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">16</span> &nbsp;<a href="哥林多前書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">47 </span> <a href="哥林多後書.html" style="font-size:16px;" rel="bible-index" name="哥林多後書">哥林多後書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">13</span> &nbsp;<a href="哥林多後書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">48 </span> <a href="加拉太書.html" style="font-size:16px;" rel="bible-index" name="加拉太書">加拉太書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">6</span> &nbsp;<a href="加拉太書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">49 </span> <a href="以弗所書.html" style="font-size:16px;" rel="bible-index" name="以弗所書">以弗所書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">6</span> &nbsp;<a href="以弗所書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">50 </span> <a href="腓立比書.html" style="font-size:16px;" rel="bible-index" name="腓立比書">腓立比書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">4</span> &nbsp;<a href="腓立比書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">51 </span> <a href="歌羅西書.html" style="font-size:16px;" rel="bible-index" name="歌羅西書">歌羅西書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">4</span> &nbsp;<a href="歌羅西書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">52 </span> <a href="帖撒羅尼迦前書.html" style="font-size:16px;" rel="bible-index" name="帖撒羅尼迦前書">帖撒羅尼迦前書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">5</span> &nbsp;<a href="帖撒羅尼迦前書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">53 </span> <a href="帖撒羅尼迦後書.html" style="font-size:16px;" rel="bible-index" name="帖撒羅尼迦後書">帖撒羅尼迦後書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">3</span> &nbsp;<a href="帖撒羅尼迦後書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">54 </span> <a href="提摩太前書.html" style="font-size:16px;" rel="bible-index" name="提摩太前書">提摩太前書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">6</span> &nbsp;<a href="提摩太前書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">55 </span> <a href="提摩太後書.html" style="font-size:16px;" rel="bible-index" name="提摩太後書">提摩太後書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">4</span> &nbsp;<a href="提摩太後書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">56 </span> <a href="提多書.html" style="font-size:16px;" rel="bible-index" name="提多書">提多書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">3</span> &nbsp;<a href="提多書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">57 </span> <a href="腓利門書.html" style="font-size:16px;" rel="bible-index" name="腓利門書">腓利門書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">1</span> &nbsp;<a href="腓利門書.html" style="font-size:12px;color:#999;">概要</a><br>
                                </li>
				                <li>
                                    <img src="<?php echo url('/cabinets/bible/bible-9.png'); ?>">
<span style="font-size:11px;color:#ccc;font-family:Arial;">58 </span> <a href="希伯來書.html" style="font-size:16px;" rel="bible-index" name="希伯來書">希伯來書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">13</span> &nbsp;<a href="希伯來書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">59 </span> <a href="雅各書.html" style="font-size:16px;" rel="bible-index" name="雅各書">雅各書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">5</span> &nbsp;<a href="雅各書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">60 </span> <a href="彼得前書.html" style="font-size:16px;" rel="bible-index" name="彼得前書">彼得前書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">5</span> &nbsp;<a href="彼得前書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">61 </span> <a href="彼得後書.html" style="font-size:16px;" rel="bible-index" name="彼得後書">彼得後書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">3</span> &nbsp;<a href="彼得後書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">62 </span> <a href="約翰一書.html" style="font-size:16px;" rel="bible-index" name="約翰一書">約翰一書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">5</span> &nbsp;<a href="約翰一書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">63 </span> <a href="約翰二書.html" style="font-size:16px;" rel="bible-index" name="約翰二書">約翰二書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">1</span> &nbsp;<a href="約翰二書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">64 </span> <a href="約翰三書.html" style="font-size:16px;" rel="bible-index" name="約翰三書">約翰三書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">1</span> &nbsp;<a href="約翰三書.html" style="font-size:12px;color:#999;">概要</a><br>
<span style="font-size:11px;color:#ccc;font-family:Arial;">65 </span> <a href="猶大書.html" style="font-size:16px;" rel="bible-index" name="猶大書">猶大書</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">1</span> &nbsp;<a href="猶大書.html" style="font-size:12px;color:#999;">概要</a><br>
                                </li>
				                <li>
                                    <img src="<?php echo url('/cabinets/bible/bible-10.png'); ?>">
<span style="font-size:11px;color:#ccc;font-family:Arial;">66 </span> <a href="啟示錄.html" style="font-size:16px;" rel="bible-index" name="啟示錄">啟示錄</a> <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">22</span> &nbsp;<a href="啟示錄.html" style="font-size:12px;color:#999;">概要</a><br>
                                </li>
				            </ul>
				        </div>
				    </div>
				    <div class="clearfix" style="border-bottom:1px solid #CCC;margin-bottom:40px;">
    					<div id="box5" class="box-style sky" style="width:550px;">
    					    <h2 class="title">舊約 Old Testament</h2>
    					    <div class="cloud" style="line-height:24px;">
<?php
    $max=39;
    $blockChaps=13;
    
    $loops = ceil($max/$blockChaps); //將要產生的區塊數，20章1區
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
            
            $html.='<span style="font-size:11px;color:#ccc;font-family:Arial;">'.$pointer.' ';
            $html.='</span> ';
            $html.='<a href="'.$r['name'].'.html" style="font-size:16px;" rel="bible-index" name="'.$r['name'].'">'.$r['name'].'</a>';
            $html.=' <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">'.$r['max_chapter'].'</span>';
            $html.=' &nbsp;<a href="'.$r['name'].'.html" style="font-size:12px;color:#999;">概要</a>';
            $html.='<br>'."\n";
            
            $r=next($rows);
        }
        $width='33%';
/*        $width='50%';
        //一個區塊的寬度預設都是一半的空間，但若是最後一個且靠左（單數），可以使用全部空間（100%）
        if( $i==$loops && $i%2==1 ){ $width='100%'; } */
        echo '<div style="float:left;width:'.$width.';">'."\n";
        echo $html;
        echo '</div>'."\n";
    }
?>


                            </div>
    					</div>
    					<div id="box5" class="box-style sky" style="width:350px;margin-left:0px;">
    					    <h2 class="title">新約 New Testament</h2>
    					    <div class="cloud" style="line-height:24px;">
<?php
    $max=66;
    $blockChaps=14;
    
    $loops = ceil($max/$blockChaps); //將要產生的區塊數，20章1區
    $blocks = array();
    for( $i=1;$i<=$loops;$i++ ){
        $html='';
        $items=0;
        $r=pos($rows);
        //$html.='<h2 class="title">'.$ch_start.' ~ '.$ch_end.'</h2>';
        while( $r && $pointer < $max && $items < $blockChaps ){
            $items+=1;
            $pointer+=1;
            
            $html.='<span style="font-size:11px;color:#ccc;font-family:Arial;">'.$pointer.' ';
            $html.='</span> ';
            $html.='<a href="'.$r['name'].'.html" style="font-size:16px;" rel="bible-index" name="'.$r['name'].'">'.$r['name'].'</a>';
            $html.=' <span style="color:#000;font-size:13px;font-weight:bold;font-family:Arial;">'.$r['max_chapter'].'</span>';
            $html.=' &nbsp;<a href="'.$r['name'].'.html" style="font-size:12px;color:#999;">概要</a>';
            $html.='<br>'."\n";
            
            $r=next($rows);
        }
        $width='50%';
/*        $width='50%';
        //一個區塊的寬度預設都是一半的空間，但若是最後一個且靠左（單數），可以使用全部空間（100%）
        if( $i==$loops && $i%2==1 ){ $width='100%'; } */
        echo '<div style="float:left;width:'.$width.';">'."\n";
        echo $html;
        echo '</div>'."\n";
    }
?>

        					</div>
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
                            </p>

<p>或分享給你的朋友喔 ...</p>
<p>
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