<?php
list( $r , $chaps , $position ) = APP::$appBuffer;
$tab_id = $r['id'];
include('tpl_header-style2.php');

$category_image = url('/cabinets/bible/bible-'.$r['category_id'].'.png');

$posnav='';
$first = pos($position);
$last = end($position);
if( $first['id'] != 1 ){ $posnav.=' &hellip; &nbsp;'; }
foreach( $position as $pos ){
    if( $pos['id'] == $r['id'] ){
        $posnav.='<strong>'.$pos['name'].'</strong> &nbsp; ';
        continue;
    }
    $url = url( '/'.$pos['name'].'.html' );
    $posnav.='<a href="'.$url.'" title="'.$pos['name'].'">'.$pos['name'].'</a>';
    $posnav.=' &nbsp; ';
}
if( $last['id'] != 66 ){ $posnav.=' &hellip; '; }

$posnav='<div style="text-align:center;border-top:1px solid #ccc;border-bottom:1px solid #ccc;padding:15px 0;">'.$posnav.'</div>';
?>
			<div id="page" class="container">
				<div id="content" style="min-height:0px;">
					<div id="box5" class="box-style">

                        <div class="clearfix">
                            <h2 class="title"><?php echo $r['name']; ?>(<?php echo $r['name_en']; ?>)簡介</h2>
                            <div style="float:left;padding-right:25px;padding-bottom:10px;">
        						<img src="<?php echo $category_image; ?>" alt="<?php echo $r['category_name']; ?>" />
        						<div style="width:187px;font-size:12px;font-weight:bold;padding-top:10px;text-align:center;color:#233c52;">本書屬於「<?php echo $r['category_name']; ?>」</div>
    						</div>
                            <?php echo $r['info_html']; ?>
                        </div>
                        
                        
                    </div>
				</div>
				<div id="sidebar">
                    <div class="box-style clearfix">
                        <h2 class="title"><?php echo $r['name']; ?>(<?php echo $r['name_en']; ?>)綱要</h2>
                        <?php echo $r['summary_html']; ?>
                    </div>
<!--
					<div class="box-style">
						<h2 class="title">本書卷屬於「<?php echo $r['category_name']; ?>」</h2>
						<p><strong>Sit tempor aliquam lorem</strong> nunc nisl velit, Fusce tor pharetra elit volutpat nunc tempor. <a href="#">More &#8230;</a></p>
					</div>
					<div id="box3" class="box-style">
						<h2 class="title">Pharetra elit nullam</h2>
						<ul class="style1">
							<li><span>05.25.10</span><a href="#">Aliquam sit tempor lorem nunc nisl velit fusce pharetra elit blandit</a></li>
							<li><span>05.24.10</span><a href="#">Lorem ipsum dolor sit amet nullam lorem sit amet tempus fusce</a></li>
							<li><span>05.21.10</span><a href="#">Aptent litoria faucibus orci primis ipsum lorem et tempor nulla</a></li>
						</ul>
						<p><a href="#" class="link2">More News &#8230;</a></p>
					</div>
					<div id="box4" class="box-style">
						<h2 class="title">Fusce blandit lacus</h2>
						<p><img src="images/homepage11.jpg" width="80" height="80" alt="" class="alignleft" /><strong>Blandit sit nullam</strong> lorem velit, Vivamus volutpat quam et dui sed vestibulum ultricies. Fusce blandit pharetra elit in volutpat. Nunc et tempor. Praesent vehicula eros a lacus fringilla sed  lorem ipsum amet faucibus orci luctus et ultrices posuere cubilia lorem ipsum sit veroeros consequat nullam.</p>
					</div>
-->
				</div>
			</div>

			<div class="single container clearfix">
				<div id="content" class="">
					<div id="box5" class="box-style">
                        <div class="clearfix" style="margin-bottom:30px;">
                            <?php echo $posnav; ?>
                        </div>
                        

                        <div class="clearfix" style="margin-bottom:30px;">
                            <h2 class="title"><?php echo $r['name']; ?>(<?php echo $r['name_en']; ?>)目錄</h2>
                            
<?php
    $max=$r['max_chapter'];
    $blockChaps=10;
    
    $loops = ceil($max/$blockChaps); //將要產生的區塊數，20章1區
    $blocks = array();
    for( $i=1;$i<=$loops;$i++ ){
        $html='';
        $html.='<ul>'."\n";
        $items=0;
        $chap=pos($chaps);
        $ch_start=(($i-1)*$blockChaps+1);
        $ch_end=( $i*$blockChaps < $max ) ? ($i*$blockChaps) : $max;
        $html.='<h2 class="title">'.$ch_start.' ~ '.$ch_end.'</h2>';
        while( $chap && $items < $blockChaps ){
            $name='';
            if( ! empty($chap['name']) ){ /*$name = ' &nbsp; '.$chap['name'];*/ }
            $unit='章';
            if( $chap['book_id']==19 ){ $unit='篇'; }
            $url=url('/'.$r['name'].'/'.$chap['chapter_id'].'.html');
            
            $html.='    <li><a href="'.$url.'">第 '.$chap['chapter_id'].' '.$unit.$name.'</a></li>'."\n";
            
            $items+=1;
            $chap=next($chaps);
        }
        $html.='</ul>'."\n";
        $width='20%';
        //一個區塊的寬度預設都是一半的空間，但若是最後一個且靠左（單數），可以使用全部空間（100%）
        //if( $i==$loops && $i%3==1 ){ $width='99%'; }
        //if( $i==$loops && $i%3==2 ){ $width='66%'; }
/*        $width='50%';
        //一個區塊的寬度預設都是一半的空間，但若是最後一個且靠左（單數），可以使用全部空間（100%）
        if( $i==$loops && $i%2==1 ){ $width='100%'; } */
        echo '<div style="float:left;width:'.$width.';">'."\n";
        echo $html;
        echo '</div>'."\n";
    }
?>
                        </div>

                        <div class="clearfix" style="margin-bottom:70px;">
                            <?php echo $posnav; ?>
                        </div>

    				</div>
				</div>
			</div>


<?php
include('tpl_footer.php');
?>