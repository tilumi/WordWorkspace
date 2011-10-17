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
        visible: 1,
        scroll: 1,
        btnNext: '#bible-new',
        btnPrev: '#bible-old'
    });
    
    $(".book-cats").mouseover( function(){
        $(this).find(".book-mask").css('opacity', 0.5);
        //$(this).find(".book-info").css('background-color', 'white');
    } ).mouseout( function(){
        $(this).find(".book-mask").css('opacity', 0.9);
        //$(this).find(".book-info").css('background-color', 'auto');
    } );
    $(".book-info").mouseover( function(){
        $(this).css('background-color', 'white');
    } ).mouseout( function(){
        $(this).css('background-color', '');
    } );
});
</script>
<?php } ?>


<style>
.sky { float:left;font-family: Georgia, "蘋果儷黑體", "微軟正黑體", "新細明體", "Times New Roman", Times, serif; }
.cloud{ line-height:24px; }
.cloud a{ text-decoration:none; }
.cloud a:hover{ text-decoration:underline; }
.ts { font-family: Georgia, "蘋果儷黑體", "微軟正黑體", "新細明體", "Times New Roman", Times, serif;margin-bottom:20px; }
.book-cats{ float:left;position:relative;width:180px;height:235px; }
.book-cats img{ position:absolute;z-index:10; }
.book-cats .book-mask{ position:absolute;z-index:20;width:100%;height:100%;background:white;filter: alpha(opacity=90);-moz-opacity:.90;opacity:.90; }
.book-cats .book-list{ position:absolute;z-index:30;padding:40px 0 0 33px; }
.book-cats .book-info{  }
.ts .chapter_digit{ font-size:12px;color:#000;font-family:Arial;font-weight:bold; }
.ts .book-link{ font-size:12px;color:#777; }
</style>
			<div id="page" class="single container">
				<div id="content">
<!--
				    <div class="clearfix" style="border-bottom:1px solid #CCC;margin-bottom:40px;">
    					<div id="box5" class="box-style ts clearfix">
    					    <h2 class="title">舊約 Old Testament</h2>
    					    <div class="cloud clearfix">

			                    <div class="book-cats">
                                    <img src="<?php echo url('/cabinets/bible/bible-1.png'); ?>" style="float:left;margin-right:10px;">
                                    <div class="book-mask"></div>
                                    <div class="book-list">
                                        <div class="book-info"><a href="創世記.html" rel="bible-index" name="創世記">創世記</a> <span class="chapter_digit">50</span> &nbsp;<a class="book-link" href="創世記.html">概要</a></div>
                                        <div class="book-info"><a href="出埃及記.html" rel="bible-index" name="出埃及記">出埃及記</a> <span class="chapter_digit">40</span> &nbsp;<a class="book-link" href="出埃及記.html">概要</a></div>
                                        <div class="book-info"><a href="利未記.html" rel="bible-index" name="利未記">利未記</a> <span class="chapter_digit">27</span> &nbsp;<a class="book-link" href="利未記.html">概要</a></div>
                                        <div class="book-info"><a href="民數記.html" rel="bible-index" name="民數記">民數記</a> <span class="chapter_digit">36</span> &nbsp;<a class="book-link" href="民數記.html">概要</a></div>
                                        <div class="book-info"><a href="申命記.html" rel="bible-index" name="申命記">申命記</a> <span class="chapter_digit">34</span> &nbsp;<a class="book-link" href="申命記.html">概要</a></div>
                                    </div>
                                </div>
			                    <div class="book-cats">
                                    <img src="<?php echo url('/cabinets/bible/bible-2.png'); ?>" style="float:left;margin-right:10px;">
                                    <div class="book-mask"></div>
                                    <div class="book-list" style="line-height:15px;padding-top:15px;">
                                        <div class="book-info"><a href="約書亞記.html" rel="bible-index" name="約書亞記">約書亞記</a> <span class="chapter_digit">24</span> &nbsp;<a href="約書亞記.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="士師記.html" rel="bible-index" name="士師記">士師記</a> <span class="chapter_digit">21</span> &nbsp;<a href="士師記.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="路得記.html" rel="bible-index" name="路得記">路得記</a> <span class="chapter_digit">4</span> &nbsp;<a href="路得記.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="撒母耳記上.html" rel="bible-index" name="撒母耳記上">撒母耳記上</a> <span class="chapter_digit">31</span> &nbsp;<a href="撒母耳記上.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="撒母耳記下.html" rel="bible-index" name="撒母耳記下">撒母耳記下</a> <span class="chapter_digit">24</span> &nbsp;<a href="撒母耳記下.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="列王紀上.html" rel="bible-index" name="列王紀上">列王紀上</a> <span class="chapter_digit">22</span> &nbsp;<a href="列王紀上.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="列王紀下.html" rel="bible-index" name="列王紀下">列王紀下</a> <span class="chapter_digit">25</span> &nbsp;<a href="列王紀下.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="歷代志上.html" rel="bible-index" name="歷代志上">歷代志上</a> <span class="chapter_digit">29</span> &nbsp;<a href="歷代志上.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="歷代志下.html" rel="bible-index" name="歷代志下">歷代志下</a> <span class="chapter_digit">36</span> &nbsp;<a href="歷代志下.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="以斯拉記.html" rel="bible-index" name="以斯拉記">以斯拉記</a> <span class="chapter_digit">10</span> &nbsp;<a href="以斯拉記.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="尼希米記.html" rel="bible-index" name="尼希米記">尼希米記</a> <span class="chapter_digit">13</span> &nbsp;<a href="尼希米記.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="以斯帖記.html" rel="bible-index" name="以斯帖記">以斯帖記</a> <span class="chapter_digit">10</span> &nbsp;<a href="以斯帖記.html" class="book-link">概要</a></div>
                                    </div>
                                </div>
			                    <div class="book-cats">
                                    <img src="<?php echo url('/cabinets/bible/bible-3.png'); ?>" style="float:left;margin-right:10px;">
                                    <div class="book-mask"></div>
                                    <div class="book-list">
                                        <div class="book-info"><a href="約伯記.html" rel="bible-index" name="約伯記">約伯記</a> <span class="chapter_digit">42</span> &nbsp;<a href="約伯記.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="詩篇.html" rel="bible-index" name="詩篇">詩篇</a> <span class="chapter_digit">150</span> &nbsp;<a href="詩篇.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="箴言.html" rel="bible-index" name="箴言">箴言</a> <span class="chapter_digit">31</span> &nbsp;<a href="箴言.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="傳道書.html" rel="bible-index" name="傳道書">傳道書</a> <span class="chapter_digit">12</span> &nbsp;<a href="傳道書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="雅歌.html" rel="bible-index" name="雅歌">雅歌</a> <span class="chapter_digit">8</span> &nbsp;<a href="雅歌.html" class="book-link">概要</a></div>
                                    </div>
                                </div>
			                    <div class="book-cats">
                                    <img src="<?php echo url('/cabinets/bible/bible-4.png'); ?>" style="float:left;margin-right:10px;">
                                    <div class="book-mask"></div>
                                    <div class="book-list">
                                        <div class="book-info"><a href="以賽亞書.html" rel="bible-index" name="以賽亞書">以賽亞書</a> <span class="chapter_digit">66</span> &nbsp;<a href="以賽亞書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="耶利米書.html" rel="bible-index" name="耶利米書">耶利米書</a> <span class="chapter_digit">52</span> &nbsp;<a href="耶利米書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="耶利米哀歌.html" rel="bible-index" name="耶利米哀歌">耶利米哀歌</a> <span class="chapter_digit">5</span> &nbsp;<a href="耶利米哀歌.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="以西結書.html" rel="bible-index" name="以西結書">以西結書</a> <span class="chapter_digit">48</span> &nbsp;<a href="以西結書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="但以理書.html" rel="bible-index" name="但以理書">但以理書</a> <span class="chapter_digit">12</span> &nbsp;<a href="但以理書.html" class="book-link">概要</a></div>
                                    </div>
                                </div>
			                    <div class="book-cats">
                                    <img src="<?php echo url('/cabinets/bible/bible-5.png'); ?>">
                                    <div class="book-mask"></div>
                                    <div class="book-list" style="line-height:15px;padding-top:15px;">
                                        <div class="book-info"><a href="何西阿書.html" rel="bible-index" name="何西阿書">何西阿書</a> <span class="chapter_digit">14</span> &nbsp;<a href="何西阿書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="約珥書.html" rel="bible-index" name="約珥書">約珥書</a> <span class="chapter_digit">3</span> &nbsp;<a href="約珥書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="阿摩司書.html" rel="bible-index" name="阿摩司書">阿摩司書</a> <span class="chapter_digit">9</span> &nbsp;<a href="阿摩司書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="俄巴底亞書.html" rel="bible-index" name="俄巴底亞書">俄巴底亞書</a> <span class="chapter_digit">1</span> &nbsp;<a href="俄巴底亞書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="約拿書.html" rel="bible-index" name="約拿書">約拿書</a> <span class="chapter_digit">4</span> &nbsp;<a href="約拿書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="彌迦書.html" rel="bible-index" name="彌迦書">彌迦書</a> <span class="chapter_digit">7</span> &nbsp;<a href="彌迦書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="那鴻書.html" rel="bible-index" name="那鴻書">那鴻書</a> <span class="chapter_digit">3</span> &nbsp;<a href="那鴻書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="哈巴谷書.html" rel="bible-index" name="哈巴谷書">哈巴谷書</a> <span class="chapter_digit">3</span> &nbsp;<a href="哈巴谷書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="西番雅書.html" rel="bible-index" name="西番雅書">西番雅書</a> <span class="chapter_digit">3</span> &nbsp;<a href="西番雅書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="哈該書.html" rel="bible-index" name="哈該書">哈該書</a> <span class="chapter_digit">2</span> &nbsp;<a href="哈該書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="撒迦利亞書.html" rel="bible-index" name="撒迦利亞書">撒迦利亞書</a> <span class="chapter_digit">14</span> &nbsp;<a href="撒迦利亞書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="瑪拉基書.html" rel="bible-index" name="瑪拉基書">瑪拉基書</a> <span class="chapter_digit">4</span> &nbsp;<a href="瑪拉基書.html" class="book-link">概要</a></div>
                                    </div>
                                </div>

                            </div>
    					</div>
    					<div id="box5" class="box-style ts">
    					    <h2 class="title">新約 New Testament</h2>
    					    <div class="cloud clearfix">
				                <div class="book-cats">
                                    <img src="<?php echo url('/cabinets/bible/bible-6.png'); ?>">
                                    <div class="book-mask"></div>
                                    <div class="book-list">
                                        <div class="book-info"><a href="馬太福音.html" rel="bible-index" name="馬太福音">馬太福音</a> <span class="chapter_digit">28</span> &nbsp;<a href="馬太福音.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="馬可福音.html" rel="bible-index" name="馬可福音">馬可福音</a> <span class="chapter_digit">16</span> &nbsp;<a href="馬可福音.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="路加福音.html" rel="bible-index" name="路加福音">路加福音</a> <span class="chapter_digit">24</span> &nbsp;<a href="路加福音.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="約翰福音.html" rel="bible-index" name="約翰福音">約翰福音</a> <span class="chapter_digit">21</span> &nbsp;<a href="約翰福音.html" class="book-link">概要</a></div>
                                    </div>
                                </div>
				                <div class="book-cats">
                                    <img src="<?php echo url('/cabinets/bible/bible-7.png'); ?>">
                                    <div class="book-mask"></div>
                                    <div class="book-list">
                                        <div class="book-info"><a href="使徒行傳.html" rel="bible-index" name="使徒行傳">使徒行傳</a> <span class="chapter_digit">28</span> &nbsp;<a href="使徒行傳.html" class="book-link">概要</a></div>
                                    </div>
                                </div>
				                <div class="book-cats">
                                    <img src="<?php echo url('/cabinets/bible/bible-8.png'); ?>">
                                    <div class="book-mask"></div>
                                    <div class="book-list" style="line-height:14px;padding-top:20px;">
                                        <div class="book-info"><a href="羅馬書.html" rel="bible-index" name="羅馬書">羅馬書</a> <span class="chapter_digit">16</span> &nbsp;<a href="羅馬書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="哥林多前書.html" rel="bible-index" name="哥林多前書">哥林多前書</a> <span class="chapter_digit">16</span> &nbsp;<a href="哥林多前書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="哥林多後書.html" rel="bible-index" name="哥林多後書">哥林多後書</a> <span class="chapter_digit">13</span> &nbsp;<a href="哥林多後書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="加拉太書.html" rel="bible-index" name="加拉太書">加拉太書</a> <span class="chapter_digit">6</span> &nbsp;<a href="加拉太書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="以弗所書.html" rel="bible-index" name="以弗所書">以弗所書</a> <span class="chapter_digit">6</span> &nbsp;<a href="以弗所書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="腓立比書.html" rel="bible-index" name="腓立比書">腓立比書</a> <span class="chapter_digit">4</span> &nbsp;<a href="腓立比書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="歌羅西書.html" rel="bible-index" name="歌羅西書">歌羅西書</a> <span class="chapter_digit">4</span> &nbsp;<a href="歌羅西書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a style="letter-spacing:-1px;font-size:14px;" href="帖撒羅尼迦前書.html" rel="bible-index" name="帖撒羅尼迦前書">帖撒羅尼迦前書</a> <span class="chapter_digit">5</span> &nbsp;<a href="帖撒羅尼迦前書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a style="letter-spacing:-1px;font-size:14px;" href="帖撒羅尼迦後書.html" rel="bible-index" name="帖撒羅尼迦後書">帖撒羅尼迦後書</a> <span class="chapter_digit">3</span> &nbsp;<a href="帖撒羅尼迦後書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="提摩太前書.html" rel="bible-index" name="提摩太前書">提摩太前書</a> <span class="chapter_digit">6</span> &nbsp;<a href="提摩太前書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="提摩太後書.html" rel="bible-index" name="提摩太後書">提摩太後書</a> <span class="chapter_digit">4</span> &nbsp;<a href="提摩太後書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="提多書.html" rel="bible-index" name="提多書">提多書</a> <span class="chapter_digit">3</span> &nbsp;<a href="提多書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="腓利門書.html" rel="bible-index" name="腓利門書">腓利門書</a> <span class="chapter_digit">1</span> &nbsp;<a href="腓利門書.html" class="book-link">概要</a></div>
                                    </div>
                                </div>
				                <div class="book-cats">
                                    <img src="<?php echo url('/cabinets/bible/bible-9.png'); ?>">
                                    <div class="book-mask"></div>
                                    <div class="book-list" style="line-height:18px;padding-top:38px;">
                                        <div class="book-info"><a href="希伯來書.html" rel="bible-index" name="希伯來書">希伯來書</a> <span class="chapter_digit">13</span> &nbsp;<a href="希伯來書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="雅各書.html" rel="bible-index" name="雅各書">雅各書</a> <span class="chapter_digit">5</span> &nbsp;<a href="雅各書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="彼得前書.html" rel="bible-index" name="彼得前書">彼得前書</a> <span class="chapter_digit">5</span> &nbsp;<a href="彼得前書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="彼得後書.html" rel="bible-index" name="彼得後書">彼得後書</a> <span class="chapter_digit">3</span> &nbsp;<a href="彼得後書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="約翰一書.html" rel="bible-index" name="約翰一書">約翰一書</a> <span class="chapter_digit">5</span> &nbsp;<a href="約翰一書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="約翰二書.html" rel="bible-index" name="約翰二書">約翰二書</a> <span class="chapter_digit">1</span> &nbsp;<a href="約翰二書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="約翰三書.html" rel="bible-index" name="約翰三書">約翰三書</a> <span class="chapter_digit">1</span> &nbsp;<a href="約翰三書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="猶大書.html" rel="bible-index" name="猶大書">猶大書</a> <span class="chapter_digit">1</span> &nbsp;<a href="猶大書.html" class="book-link">概要</a></div>
                                    </div>
                                </div>
				                <div class="book-cats">
                                    <img src="<?php echo url('/cabinets/bible/bible-10.png'); ?>">
                                    <div class="book-mask"></div>
                                    <div class="book-list">
                                        <div class="book-info"><a href="啟示錄.html" rel="bible-index" name="啟示錄">啟示錄</a> <span class="chapter_digit">22</span> &nbsp;<a href="啟示錄.html" class="book-link">概要</a></div>
                                    </div>
                                </div>

        					</div>
    					</div>
					</div>
				    <div class="cloud sky clearfix" style="border-bottom:1px solid #CCC;margin-bottom:40px;">
    				    <div class="clearfix">
    				        <div id="bible-old" class="bible-button ui-corner-all"><h2 class="title">舊約 Old Testament</h2></div>
    				        <div id="bible-new" class="bible-button ui-corner-all"><h2 class="title">新約 New Testament</h2></div>
				        </div>
				        <div id="bible-container">
				            <ul>
				                <li>
				                    <div style="position:absolute;top:0px;left:0px;width:360px;height:235px;">
                                        <img src="<?php echo url('/cabinets/bible/bible-1.png'); ?>" style="float:left;margin-right:10px;">
                                        <div style="padding-top:20px;">
                                        <div class="book-info"><a href="創世記.html" rel="bible-index" name="創世記">創世記</a> <span class="chapter_digit">50</span> &nbsp;<a class="book-link" href="創世記.html">概要</a></div>
                                        <div class="book-info"><a href="出埃及記.html" rel="bible-index" name="出埃及記">出埃及記</a> <span class="chapter_digit">40</span> &nbsp;<a class="book-link" href="出埃及記.html">概要</a></div>
                                        <div class="book-info"><a href="利未記.html" rel="bible-index" name="利未記">利未記</a> <span class="chapter_digit">27</span> &nbsp;<a class="book-link" href="利未記.html">概要</a></div>
                                        <div class="book-info"><a href="民數記.html" rel="bible-index" name="民數記">民數記</a> <span class="chapter_digit">36</span> &nbsp;<a class="book-link" href="民數記.html">概要</a></div>
                                        <div class="book-info"><a href="申命記.html" rel="bible-index" name="申命記">申命記</a> <span class="chapter_digit">34</span> &nbsp;<a class="book-link" href="申命記.html">概要</a></div>
                                        </div>
                                    </div>
				                    <div style="position:absolute;top:255px;left:0px;width:360px;height:235px;">
                                        <img src="<?php echo url('/cabinets/bible/bible-2.png'); ?>" style="float:left;margin-right:10px;">
                                        <div style="padding-top:10px;">
                                        <div class="book-info"><a href="約書亞記.html" rel="bible-index" name="約書亞記">約書亞記</a> <span class="chapter_digit">24</span> &nbsp;<a href="約書亞記.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="士師記.html" rel="bible-index" name="士師記">士師記</a> <span class="chapter_digit">21</span> &nbsp;<a href="士師記.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="路得記.html" rel="bible-index" name="路得記">路得記</a> <span class="chapter_digit">4</span> &nbsp;<a href="路得記.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="撒母耳記上.html" rel="bible-index" name="撒母耳記上">撒母耳記上</a> <span class="chapter_digit">31</span> &nbsp;<a href="撒母耳記上.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="撒母耳記下.html" rel="bible-index" name="撒母耳記下">撒母耳記下</a> <span class="chapter_digit">24</span> &nbsp;<a href="撒母耳記下.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="列王紀上.html" rel="bible-index" name="列王紀上">列王紀上</a> <span class="chapter_digit">22</span> &nbsp;<a href="列王紀上.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="列王紀下.html" rel="bible-index" name="列王紀下">列王紀下</a> <span class="chapter_digit">25</span> &nbsp;<a href="列王紀下.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="歷代志上.html" rel="bible-index" name="歷代志上">歷代志上</a> <span class="chapter_digit">29</span> &nbsp;<a href="歷代志上.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="歷代志下.html" rel="bible-index" name="歷代志下">歷代志下</a> <span class="chapter_digit">36</span> &nbsp;<a href="歷代志下.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="以斯拉記.html" rel="bible-index" name="以斯拉記">以斯拉記</a> <span class="chapter_digit">10</span> &nbsp;<a href="以斯拉記.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="尼希米記.html" rel="bible-index" name="尼希米記">尼希米記</a> <span class="chapter_digit">13</span> &nbsp;<a href="尼希米記.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="以斯帖記.html" rel="bible-index" name="以斯帖記">以斯帖記</a> <span class="chapter_digit">10</span> &nbsp;<a href="以斯帖記.html" class="book-link">概要</a></div>
                                        </div>
                                    </div>
				                    <div style="position:absolute;top:0px;left:350px;width:360px;height:235px;">
                                        <img src="<?php echo url('/cabinets/bible/bible-3.png'); ?>" style="float:left;margin-right:10px;">
                                        <div style="padding-top:20px;">
                                        <div class="book-info"><a href="約伯記.html" rel="bible-index" name="約伯記">約伯記</a> <span class="chapter_digit">42</span> &nbsp;<a href="約伯記.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="詩篇.html" rel="bible-index" name="詩篇">詩篇</a> <span class="chapter_digit">150</span> &nbsp;<a href="詩篇.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="箴言.html" rel="bible-index" name="箴言">箴言</a> <span class="chapter_digit">31</span> &nbsp;<a href="箴言.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="傳道書.html" rel="bible-index" name="傳道書">傳道書</a> <span class="chapter_digit">12</span> &nbsp;<a href="傳道書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="雅歌.html" rel="bible-index" name="雅歌">雅歌</a> <span class="chapter_digit">8</span> &nbsp;<a href="雅歌.html" class="book-link">概要</a></div>
                                        </div>
                                    </div>
				                    <div style="position:absolute;top:255px;left:350px;width:360px;height:235px;">
                                        <img src="<?php echo url('/cabinets/bible/bible-4.png'); ?>" style="float:left;margin-right:10px;">
                                        <div style="padding-top:20px;">
                                        <div class="book-info"><a href="以賽亞書.html" rel="bible-index" name="以賽亞書">以賽亞書</a> <span class="chapter_digit">66</span> &nbsp;<a href="以賽亞書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="耶利米書.html" rel="bible-index" name="耶利米書">耶利米書</a> <span class="chapter_digit">52</span> &nbsp;<a href="耶利米書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="耶利米哀歌.html" rel="bible-index" name="耶利米哀歌">耶利米哀歌</a> <span class="chapter_digit">5</span> &nbsp;<a href="耶利米哀歌.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="以西結書.html" rel="bible-index" name="以西結書">以西結書</a> <span class="chapter_digit">48</span> &nbsp;<a href="以西結書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="但以理書.html" rel="bible-index" name="但以理書">但以理書</a> <span class="chapter_digit">12</span> &nbsp;<a href="但以理書.html" class="book-link">概要</a></div>
                                        </div>
                                    </div>
				                    <div style="position:absolute;top:0px;left:700px;width:360px;height:235px;">
                                        <img src="<?php echo url('/cabinets/bible/bible-5.png'); ?>">
                                        <div style="padding-top:40px;padding-left:20px;">
                                        <div class="book-info"><a href="何西阿書.html" rel="bible-index" name="何西阿書">何西阿書</a> <span class="chapter_digit">14</span> &nbsp;<a href="何西阿書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="約珥書.html" rel="bible-index" name="約珥書">約珥書</a> <span class="chapter_digit">3</span> &nbsp;<a href="約珥書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="阿摩司書.html" rel="bible-index" name="阿摩司書">阿摩司書</a> <span class="chapter_digit">9</span> &nbsp;<a href="阿摩司書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="俄巴底亞書.html" rel="bible-index" name="俄巴底亞書">俄巴底亞書</a> <span class="chapter_digit">1</span> &nbsp;<a href="俄巴底亞書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="約拿書.html" rel="bible-index" name="約拿書">約拿書</a> <span class="chapter_digit">4</span> &nbsp;<a href="約拿書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="彌迦書.html" rel="bible-index" name="彌迦書">彌迦書</a> <span class="chapter_digit">7</span> &nbsp;<a href="彌迦書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="那鴻書.html" rel="bible-index" name="那鴻書">那鴻書</a> <span class="chapter_digit">3</span> &nbsp;<a href="那鴻書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="哈巴谷書.html" rel="bible-index" name="哈巴谷書">哈巴谷書</a> <span class="chapter_digit">3</span> &nbsp;<a href="哈巴谷書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="西番雅書.html" rel="bible-index" name="西番雅書">西番雅書</a> <span class="chapter_digit">3</span> &nbsp;<a href="西番雅書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="哈該書.html" rel="bible-index" name="哈該書">哈該書</a> <span class="chapter_digit">2</span> &nbsp;<a href="哈該書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="撒迦利亞書.html" rel="bible-index" name="撒迦利亞書">撒迦利亞書</a> <span class="chapter_digit">14</span> &nbsp;<a href="撒迦利亞書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="瑪拉基書.html" rel="bible-index" name="瑪拉基書">瑪拉基書</a> <span class="chapter_digit">4</span> &nbsp;<a href="瑪拉基書.html" class="book-link">概要</a></div>
                                        </div>
                                    </div>
                                </li>
				                <li>
                                    <img src="<?php echo url('/cabinets/bible/bible-6.png'); ?>">
                                        <div class="book-info"><a href="馬太福音.html" rel="bible-index" name="馬太福音">馬太福音</a> <span class="chapter_digit">28</span> &nbsp;<a href="馬太福音.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="馬可福音.html" rel="bible-index" name="馬可福音">馬可福音</a> <span class="chapter_digit">16</span> &nbsp;<a href="馬可福音.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="路加福音.html" rel="bible-index" name="路加福音">路加福音</a> <span class="chapter_digit">24</span> &nbsp;<a href="路加福音.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="約翰福音.html" rel="bible-index" name="約翰福音">約翰福音</a> <span class="chapter_digit">21</span> &nbsp;<a href="約翰福音.html" class="book-link">概要</a></div>
                                </li>
				                <li>
                                    <img src="<?php echo url('/cabinets/bible/bible-7.png'); ?>">
                                        <div class="book-info"><a href="使徒行傳.html" rel="bible-index" name="使徒行傳">使徒行傳</a> <span class="chapter_digit">28</span> &nbsp;<a href="使徒行傳.html" class="book-link">概要</a></div>
                                </li>
				                <li>
                                    <img src="<?php echo url('/cabinets/bible/bible-8.png'); ?>">
                                        <div class="book-info"><a href="羅馬書.html" rel="bible-index" name="羅馬書">羅馬書</a> <span class="chapter_digit">16</span> &nbsp;<a href="羅馬書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="哥林多前書.html" rel="bible-index" name="哥林多前書">哥林多前書</a> <span class="chapter_digit">16</span> &nbsp;<a href="哥林多前書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="哥林多後書.html" rel="bible-index" name="哥林多後書">哥林多後書</a> <span class="chapter_digit">13</span> &nbsp;<a href="哥林多後書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="加拉太書.html" rel="bible-index" name="加拉太書">加拉太書</a> <span class="chapter_digit">6</span> &nbsp;<a href="加拉太書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="以弗所書.html" rel="bible-index" name="以弗所書">以弗所書</a> <span class="chapter_digit">6</span> &nbsp;<a href="以弗所書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="腓立比書.html" rel="bible-index" name="腓立比書">腓立比書</a> <span class="chapter_digit">4</span> &nbsp;<a href="腓立比書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="歌羅西書.html" rel="bible-index" name="歌羅西書">歌羅西書</a> <span class="chapter_digit">4</span> &nbsp;<a href="歌羅西書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="帖撒羅尼迦前書.html" rel="bible-index" name="帖撒羅尼迦前書">帖撒羅尼迦前書</a> <span class="chapter_digit">5</span> &nbsp;<a href="帖撒羅尼迦前書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="帖撒羅尼迦後書.html" rel="bible-index" name="帖撒羅尼迦後書">帖撒羅尼迦後書</a> <span class="chapter_digit">3</span> &nbsp;<a href="帖撒羅尼迦後書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="提摩太前書.html" rel="bible-index" name="提摩太前書">提摩太前書</a> <span class="chapter_digit">6</span> &nbsp;<a href="提摩太前書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="提摩太後書.html" rel="bible-index" name="提摩太後書">提摩太後書</a> <span class="chapter_digit">4</span> &nbsp;<a href="提摩太後書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="提多書.html" rel="bible-index" name="提多書">提多書</a> <span class="chapter_digit">3</span> &nbsp;<a href="提多書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="腓利門書.html" rel="bible-index" name="腓利門書">腓利門書</a> <span class="chapter_digit">1</span> &nbsp;<a href="腓利門書.html" class="book-link">概要</a></div>
                                </li>
				                <li>
                                    <img src="<?php echo url('/cabinets/bible/bible-9.png'); ?>">
                                        <div class="book-info"><a href="希伯來書.html" rel="bible-index" name="希伯來書">希伯來書</a> <span class="chapter_digit">13</span> &nbsp;<a href="希伯來書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="雅各書.html" rel="bible-index" name="雅各書">雅各書</a> <span class="chapter_digit">5</span> &nbsp;<a href="雅各書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="彼得前書.html" rel="bible-index" name="彼得前書">彼得前書</a> <span class="chapter_digit">5</span> &nbsp;<a href="彼得前書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="彼得後書.html" rel="bible-index" name="彼得後書">彼得後書</a> <span class="chapter_digit">3</span> &nbsp;<a href="彼得後書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="約翰一書.html" rel="bible-index" name="約翰一書">約翰一書</a> <span class="chapter_digit">5</span> &nbsp;<a href="約翰一書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="約翰二書.html" rel="bible-index" name="約翰二書">約翰二書</a> <span class="chapter_digit">1</span> &nbsp;<a href="約翰二書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="約翰三書.html" rel="bible-index" name="約翰三書">約翰三書</a> <span class="chapter_digit">1</span> &nbsp;<a href="約翰三書.html" class="book-link">概要</a></div>
                                        <div class="book-info"><a href="猶大書.html" rel="bible-index" name="猶大書">猶大書</a> <span class="chapter_digit">1</span> &nbsp;<a href="猶大書.html" class="book-link">概要</a></div>
                                </li>
				                <li>
                                    <img src="<?php echo url('/cabinets/bible/bible-10.png'); ?>">
                                        <div class="book-info"><a href="啟示錄.html" rel="bible-index" name="啟示錄">啟示錄</a> <span class="chapter_digit">22</span> &nbsp;<a href="啟示錄.html" class="book-link">概要</a></div>
                                </li>
				            </ul>
				        </div>
				    </div>
-->
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