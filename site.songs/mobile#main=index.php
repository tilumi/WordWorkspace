<?php
list( $rows ) = APP::$appBuffer;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>JBride& 歌本</title>
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css" />
        <link rel="stylesheet" href="my.css" />
        <style>
            /* App custom styles */
        </style>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js">
        </script>
        <script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js">
        </script>
    </head>
    <body>
        <div data-role="page" id="home">
            <div data-theme="a" data-role="header">
                <h5>
                    Jbride&amp; 歌本
                </h5>
            </div>
            <div data-role="content" style="padding:0;background-color:#3b3a3f;">
                <div data-inset="false">
                    <?php include('layout_mobile/tpl_keyboard.php'); ?>
                </div>
            </div>
            <div data-role="content" style="padding-top:0;">
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput1">
                        </label>
                        <input id="textinput1" placeholder="注音速查或漢語拼音" value="" type="text" />
                    </fieldset>
                </div>
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        TIPS: 使用注音鍵盤「不要」點輸入框，防止跳出系統鍵盤
                    </fieldset>
                </div>
                <ul data-role="listview" data-divider-theme="c" data-inset="false">
                    <li data-theme="c">
                        <a href="#lyrics" data-transition="slide">
                            A1-1 最大的書
                        </a>
                    </li>
                    <li data-theme="c">
                        <a href="#lyrics" data-transition="slide">
                            Button
                        </a>
                    </li>
                    <li data-theme="c">
                        <a href="#lyrics" data-transition="slide">
                            Button
                        </a>
                    </li>
                    <li data-theme="c">
                        <a href="#lyrics" data-transition="slide">
                            Button
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div data-role="page" id="lyrics">
            <div data-theme="a" data-role="header" style="position:fixed;width:100%;">
                <h3>
                    不論是昨日今日《加詞版》
                </h3>
                <a data-role="button" data-transition="slide" data-theme="c" href="#home" data-icon="home" data-iconpos="left">
                    首頁
                </a>
                <div data-role="navbar" data-iconpos="left">
                    <ul>
                        <li>
                            <a href="#lyrics" data-theme="" data-icon="star" class="ui-btn-active">
                                中文
                            </a>
                        </li>
                        <li>
                            <a href="#lyrics" data-theme="" data-icon="star">
                                韓文
                            </a>
                        </li>
                        <li>
                            <a href="#lyrics" data-theme="" data-icon="star">
                                中韓對照
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div data-role="content" style="margin-top:70px;">
                <div style="font-size:16px;">
                    <h3>A14 不論是昨日今日《加詞版》</h3>
                    <pre>
  不論是昨日今日

  耶和華啊 (我們愛祢)
  親愛聖靈 (我們愛祢)
  主耶穌啊 (我們愛祢)

1 不論是昨日今日 不論去到哪裡
  都唯有禱告與 讚美與主的話語
  感謝與忠誠與 恩惠與真～理
  聖靈的火熱的 那動工以及感動

2 不論是今日明日 不論是做什麼
  要把主放在首位 如此地來生活
  愛則是 唯-有 我主與我而已
  生活是隨時都 與主同行的生活

  耶和華啊 (我們愛祢)
  親愛聖靈 (我們愛祢)
  主耶穌啊 (我們愛祢)

3 要盡心盡性盡意 全力地付出真情
  愛著神愛著救主 愛著弟兄姊妹
  生＿命就是救援 要話語和管理
  相＿信就是實踐 神蹟～正在興起

4 哦主唷我現在 將世上一切都斷絕
  我唯有愛著我主 為了我主而生活
  我順從主的話語 我將會做好預備
  我愛你懇求主 一定要帶我走
  我愛你懇求主 一定要帶我走
  我愛你懇求主 一定要帶我走

  不論是昨日今日
                    </pre>
                    <a data-role="button" data-transition="slide" data-theme="c" href="#home" data-icon="home" data-iconpos="left">
                        返回首頁
                    </a>
                </div>
            </div>
        </div>
        <script>
            //App custom javascript
        </script>
    </body>
</html>