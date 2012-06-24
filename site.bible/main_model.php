<?php
class Main{
    function getBooks(){
        $sql="SELECT * FROM bible_books";
        return Model::fetchAll($sql);
    }
    function getBookInfo( $book_id ){
        require("cache/bible_info.php");
        
        $sql="SELECT * FROM bible_books WHERE id=".Model::quote($book_id, 'text');
        $data=Model::fetchRow($sql);
        $sql="SELECT * FROM cuv_chapters WHERE book_id=".Model::quote($book_id, 'text');
        $chaps=Model::fetchAll($sql);
        
        $book_id=$data['id'];
        $testament=($data['testament']=='OT')?'舊約':'新約';
        $data['ta']=$testament;
/*
        $info=$data['info'];
        $info_str=preg_replace("/(\r|\n)+/",'<br>',$info);
        $info_arr=explode('<br>',$info_str);
        $info_tmp='';
        foreach($info_arr as $ia){
            $info_tmp.='<p>'.$ia.'</p>'."\n";
        }
        $info=$info_tmp;
        $data['info_html']=$info;
        
        $summary=$data['summary'];
        $summary_str=preg_replace("/(\r|\n)+/",'<br>',$summary);
        $summary_arr=explode('<br>',$summary_str);
        $summary_tmp='';
        foreach($summary_arr as $ia){
            $summary_tmp.='<li>'.$ia.'</li>'."\n";
        }
        $summary='<ul class="style1">'."\n".$summary_tmp.'</ul>'."\n";
        $data['summary_html']=$summary;
*/
        $prev=(($book_id-1)>=1)?array('id'=>($book_id-1),'name'=>$bibleFull[$book_id-1]):array();
        $next=(($book_id+1)<=66)?array('id'=>($book_id+1),'name'=>$bibleFull[$book_id+1]):array();
        $data['prev']=$prev;
        $data['next']=$next;
        
        $start = $book_id-3;
        if( $start < 1 ){ $start = 1; }
        $stop = $book_id+3;
        if( $stop > 66 ){ $stop = 66; }
        $position = array();
        for( $i=$start; $i<=$stop; $i++ ){
            $position[]=array(
                'id' => $i,
                'name' => $bibleFull[$i],
            );
        }
        
        return array($data, $chaps, $position);
    }
    function parseVerseRegion( $chapter_str ){
        $region=array(
            'highlight'=>false,
            'error'=>false,
            'chapter_id'=>'',
            'start'=>array(),
            'end'=>array(),
        );
        //預設動作
        $chap_id = (int)$chapter_str;
        $region['start'] = array( $chap_id , 0 );
        $region['end'] = array( $chap_id , 0 );
        //包含指定節數
        if( strpos( $chapter_str , '-' )!==false ){ //有連字號，表包含經文區間
            $region['highlight'] = true;
            list( $ch_start, $ch_end ) = explode('-', $chapter_str);
            //判斷起始章節
            $start_type='verse';
            if( preg_match('/^[0-9]+\:[0-9]+$/', $ch_start) ){
                $region['start'] = explode(':', $ch_start);
            }else{ //如果沒有冒號，表示為章，節自動帶入 0（表示整章）
                $ch_start=(int)$ch_start;
                $region['start'] = array( $ch_start , 0 );
                $start_type='chapter';
            }
            //判斷結束章節
            $end_type='verse';
            if( preg_match('/^[0-9]+\:[0-9]+$/', $ch_end) ){
                $region['end'] = explode(':', $ch_end);
            }else{ //如果沒有冒號，表示為章，節自動帶入 0（表示整章）
                $ch_end=(int)$ch_end;
                if( $start_type == 'verse' ){
                    $ch_end = ( $ch_end < $region['start'][1] ) ? $region['start'][1] : $ch_end ; //結束節必須相等於起始節，或大於
                    $region['end'] = array( $region['start'][0], $ch_end );
                }
                if( $start_type == 'chapter' ){
                    $region['end'] = array( $ch_end , 0 );
                    $end_type='chapter';
                }
            }
            if( $start_type=='chapter' && $end_type=='chapter' ){
                $region['highlight'] = false;
            }
        }elseif( strpos( $chapter_str , ':' )!==false ){ //沒有連字號，卻有冒號，表為單一章節
            $region['highlight'] = true;
            $region['start'] = explode(':', $chapter_str);
            $region['end'] = $region['start'];
        }
        $region['chapter_id'] = $region['start'][0];
        
        return $region;
    }
    function getChapterVerses( $book_id, $region ){
        require("cache/bible_info.php");
        
        $chapter_id = $region['chapter_id'];
        
        $sql="SELECT * FROM cuv WHERE book_id=".Model::quote($book_id, 'integer')." AND chapter_id >= ".Model::quote($region['start'][0], 'integer')." AND chapter_id <= ".Model::quote($region['end'][0], 'integer');
        $rows=Model::fetchAll($sql);
        $sql="SELECT * FROM cuv_chapters WHERE book_id=".Model::quote($book_id, 'integer')." AND chapter_id=".Model::quote($chapter_id, 'integer');
        $chapter=Model::fetchRow($sql);
        $sql="SELECT * FROM bible_books WHERE id=".Model::quote($book_id, 'integer');
        $book=Model::fetchRow($sql);
    
        $unit='章'; if( $book_id==19 ){ $unit='篇'; }
        $pageName=$bibleFull[ $book_id ].' '.$chapter_id.' '.$unit;
        $pageUnit=$unit;
        if( $region['start'][0] <> $region['end'][0] ){
            $pageName=$bibleFull[ $book_id ].' '.$region['start'][0];
            $pageName.=' '.$unit;
            $pageName.=' ~ ';
            $pageName.=$region['end'][0];
            $pageName.=' '.$unit;
        }
        $pageTitle=$pageName.' :: '.$chapter['name'];
        $chapter_name='第'.$chapter_id.$unit;
        
        //設定經文標示
        if( $region['highlight'] ){
            $start_ch = $region['start'][0];
            $start_vr = $region['start'][1];
            $end_ch = $region['end'][0];
            $end_vr = $region['end'][1];
            foreach( $rows as $k=>$r ){
                if( ! ( $r['chapter_id'] <= $start_ch && $r['verse_id'] < $start_vr ) && ! ( $r['chapter_id'] >= $end_ch && $r['verse_id'] > $end_vr )
                    && $r['stype_id']=='g' ){
                    
                    $rows[$k]['highlight'] = 1;
                }
            }
        }
        
        $chap_key = $book_id.'-'.$region['start'][0];
        $chap_key_index=array_search($chap_key, $bibleChapter);
        
        $prev=array();
        if( isset($bibleChapter[ ($chap_key_index - 1) ]) ){
            list( $prev_book_id , $prev_chapter_id ) = explode("-", $bibleChapter[ ($chap_key_index - 1) ] );
            $unit='章';
            if( $prev_book_id==19 ){ $unit='篇'; }
            $prev = array(
                'book_id'=>$prev_book_id,
                'book_name' => $bibleFull[ $prev_book_id ],
                'chapter_id'=>$prev_chapter_id,
                'name'=>$bibleFull[ $prev_book_id ].' '.$prev_chapter_id.' '.$unit,
                'unit'=>$unit,
            );
        }
        
        $chap_key = $book_id.'-'.$region['end'][0];
        $chap_key_index=array_search($chap_key, $bibleChapter);
        
        $next=array();
        if( isset($bibleChapter[ ($chap_key_index + 1) ]) ){
            list( $next_book_id , $next_chapter_id ) = explode("-", $bibleChapter[ ($chap_key_index + 1) ] );
            $unit='章'; if( $next_book_id==19 ){ $unit='篇'; }
            $next = array(
                'book_id'=>$next_book_id,
                'book_name' => $bibleFull[ $next_book_id ],
                'chapter_id'=>$next_chapter_id,
                'name'=>$bibleFull[ $next_book_id ].' '.$next_chapter_id.' '.$unit,
                'unit'=>$unit,
            );
        }
        
        $navbar=array(
            'ts' => ($book_id < 40)?'OT':'NT',
            'testament' => ($book_id < 40)?'舊約':'新約',
            'name' => $pageName,
            'title' => $chapter['name'],
            'book_id' => $book_id,
            'book_name' => $bibleFull[ $book_id ],
            'book_name_en' => $book['name_en'],
            'chapter_id' => $chapter_id,
            'chapter_name' => $chapter_name,
            'unit'=>$pageUnit,
            'max_chapter' => $bibleMaxChapter[ ($book_id-1) ],
            'prev' => $prev,
            'next' => $next,
        );
        
        return array($rows, $navbar, $pageTitle);
    }
}
?>