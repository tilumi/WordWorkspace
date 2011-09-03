<?php
list( $r , $chaps , $position ) = APP::$appBuffer;
$tab_id = $r['id'];

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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

				<div id="content" class="single" style="width:860px; margin:0 20px;">
					<div id="box5" class="box-style">


                        <div class="clearfix">
                            <div class="clearfix">
                                <h2 class="title" style="margin:0 0 0px 0;"><?php echo $r['name']; ?>(<?php echo $r['name_en']; ?>)目錄</h2>
                                <a style="font-size:12px;margin:0 0 20px 3px;" href="<?php echo url('/'.$r['name'].'.html'); ?>">關於 <?php echo $r['name']; ?>(<?php echo $r['name_en']; ?>) ...</a>
                            </div>
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
        $html.='<h2 class="title" style="margin:0 0 15px 0;">'.$ch_start.' ~ '.$ch_end.'</h2>';
        while( $chap && $items < $blockChaps ){
            $name='';
            if( ! empty($chap['name']) ){ $name = ' &nbsp; '.$chap['name']; }
            $unit='章';
            if( $chap['book_id']==19 ){ $unit='篇'; }
            $url=url('/'.$r['name'].'/'.$chap['chapter_id'].'.html');
            
            $html.='    <li style="line-height:16px;"><a href="'.$url.'">第 '.$chap['chapter_id'].' '.$unit.$name.'</a></li>'."\n";
            
            $items+=1;
            $chap=next($chaps);
        }
        $html.='</ul>'."\n";
        $width='33%';
        //一個區塊的寬度預設都是一半的空間，但若是最後一個且靠左（單數），可以使用全部空間（100%）
        if( $i==$loops && $i%3==1 ){ $width='99%'; }
        if( $i==$loops && $i%3==2 ){ $width='66%'; }
        echo '<div style="float:left;width:'.$width.';">'."\n";
        echo $html;
        echo '</div>'."\n";
    }
?>
                        </div>
                    </div>
				</div>

