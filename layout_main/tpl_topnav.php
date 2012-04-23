
			<div id="topnav" style="font-family: Arial;">
                <div class="float-left">
<?php if( isset( $homeIndex ) && $homeIndex ){ ?>
                    <em class="home">主的愛&線上聖經</em>
<?php } ?>
                </div>
                <div class="float-right">
<?php if( is_array( $book_on_top ) ){ ?>
                    第<?php echo $book_on_top['chapter_id']; ?><?php echo $book_on_top['unit']; ?>
                    <span>|</span>
                    <?php echo $book_on_top['title']; ?>
                    <span>|</span>
                    <a href="<?php echo url('/'.$book_on_top['book_name'].'.html'); ?>"><?php echo $book_on_top['book_name']; ?>(<?php echo $book_on_top['book_name_en']; ?>)</a>
                    <span>|</span>
<?php } ?>
                    繁體中文
                    <span>|</span>
                    和合本
                    <span>|</span>
                    神版
<!--
                    <span>|</span>
                    <a href="<?php echo url('/catalog.html'); ?>">聖經目錄</a>
-->
                </div>
			</div>
