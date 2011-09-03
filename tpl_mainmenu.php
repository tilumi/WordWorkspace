<?php include('cache/bible_info.php'); ?>

            <div class="midnav">
    			<div id="menu-container">
    				<div id="menu">
    					<ul>
<?php
foreach( $bibleFull as $id=>$name ){
    $style='';
    if( $id <= 5              ){ $style.='border-color:#efb3b3;color:#efb3b3;'; } //摩西五經
    if( $id > 5 && $id <= 17  ){ $style.='border-color:#b3efc8;color:#b3efc8;'; } //歷史書
    if( $id > 17 && $id <= 22 ){ $style.='border-color:#efe9b3;color:#efe9b3;'; } //詩歌智慧書
    if( $id > 22 && $id <= 27 ){ $style.='border-color:#efb3ec;color:#efb3ec;'; } //大先知書
    if( $id > 27 && $id <= 39 ){ $style.='border-color:#ddd;color:#ddd;'; } //小先知書
    if( $id > 39 && $id <= 43 ){ $style.='border-color:#e8b4ce;color:#e8b4ce;'; } //四福音書
    if( $id > 43 && $id <= 44 ){ $style.='border-color:#b3efb6;color:#b3efb6;'; } //使徒行傳
    if( $id > 44 && $id <= 57 ){ $style.='border-color:#efe5b3;color:#efe5b3;'; } //保羅書信
    if( $id > 57 && $id <= 65 ){ $style.='border-color:#e5b3ef;color:#e5b3ef;'; } //大公書信
    if( $id > 65 && $id <= 66 ){ $style.='border-color:#c2aee2;color:#c2aee2;'; } //啟示錄
    $style='style="'.$style.'"';
    
    $active='';
    if( $tab_id == $id ){
        $active='class="active"';
    }
    $max = $bibleMaxChapter[ $id-1 ];
?>
    						<li <?php echo $active; ?>>
                                <a href="<?php echo url( '/'.$name.'.html' ); ?>" rel="bible-book" accesskey="<?php echo $id; ?>" name="<?php echo $name; ?>">
                                    <em <?php echo $style; ?>><?php echo $name; ?></em>
                                    <span class="digit"><?php echo $max; ?></span>
                                </a>
                            </li>
<?php
}
?>
    					</ul>
    				</div>
    			</div>
    			<div class="scroll-bar-wrap ui-widget-content ui-corner-bottom"> 
                    <div class="next"></div>
                    <div class="prev"></div>
                    <div class="scroll-bar"></div> 
                </div>
			</div>
