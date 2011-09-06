
			<div id="topnav" style="font-family: Arial;">
                <div class="float-left">
                </div>
                <div class="float-right">
                    繁體中文
                    <span>|</span>
                    和合本
                    <span>|</span>
                    神版
<?php if( is_array( $book_on_top ) ){ ?>
                    <span>|</span>
                    <a href="<?php echo url('/'.$book_on_top['book_name'].'.html'); ?>" target="_blank">關於 <?php echo $book_on_top['book_name']; ?>(<?php echo $book_on_top['book_name_en']; ?>)</a>
<?php } ?>
<!--
                    <span>|</span>
                    <a href="<?php echo url('/catalog.html'); ?>">聖經目錄</a>
-->
                </div>
			</div>
