<?php
$doctype=APP::$routing['doctype'];
if( $doctype != 'html' ){
    require('error/404.php');die;
}

$action = pos( APP::$params );
if( empty($action) ){ $action='index'; }
if( $action == 'init' ){ array_shift(APP::$params); }

//全部都是中文字，表示已指定書卷或是章節
if( preg_match( '/^([\x{4e00}-\x{9fff}]+)$/u', $action) ){
    switch( count(APP::$params) ){
        case 1: //只有指定書卷的時候，進入書卷介紹頁
            $action='book';
            break;
        default:
            if( count(APP::$params)==2 && empty(APP::$params[1]) ){ //搜尋書卷的根目錄，自動重導至書卷.html
                $book=pos(APP::$params);
                redirect( '/'.$book.'.html' );
                break;
            }
            $action='chapter';
            if( APP::$params[1]=='chapters' ){
                $action='chapters';
            }
    }
}
$registerAction = array(
    'index',
    'book',
    'chapters', //顯示書卷章節表
    'chapter', //閱讀經文
    'catalog',
    'init',
);

include( APP::$routing['app'].'_model.php' );

if( in_array( $action , $registerAction ) ){
    $action();
}else{
    pr(APP::$routing);
    require('error/404.php');die;
}



$viewTpl = APP::$routing['app'].'='.$action.'.php';
if( file_exists($viewTpl) )
    include( $viewTpl );

/******************************************************************************/

function index(){
    $rows=Main::getBooks();
    View::setHeader( 'sitename', 'The Bible 線上聖經 - 最美最舒適的線上讀經網站' );
    APP::$appBuffer = array($rows);
}
function catalog(){

}
function book(){
    require("cache/bible_info.php");
/*    $cats=array('摩西五經','歷史書','詩歌智慧書','大先知書','小先知書','四福音書','宗徒大事錄','保羅書信','大公書信','啟示錄');
    for( $i=66;$i<=66;$i++ ){
        $data['id']=$i;
        $cat_id=10;
        $data['category_id']=$cat_id;
        $data['category_name']=$cats[ $cat_id-1 ];
        Model::update($data, 'id', 'bible_books');
    }
    for( $i=1;$i<=66;$i++ ){
        $sql="SELECT max_chapter FROM bible_books WHERE id='$i'";
        $max=Model::fetchOne($sql);
        $chaps = chapters_info($i);
        $data=array();
        for( $id=1;$id<=$max;$id++ ){
            $name=$chaps[$id];
            $data[$id]['id']=sprintf( '%02d' ,$i ).':'.sprintf( '%03d' ,$id );
            $data[$id]['book_id']=$i;
            $data[$id]['chapter_id']=$id;
            $data[$id]['name']=$name;
        }
        Model::inserts($data, 'bible_chapters');
    }
*/
    $book = pos(APP::$params);
    if( ! in_array( $book , $bibleFull ) ){
        require('error/404.php');die;
    }
    $book_id = array_search( $book , $bibleFull );
    
    list($data, $chaps, $position) = Main::getBookInfo( $book_id );
    
    View::setTitle( $book."(".$data['name_en'].")" );
    View::setHeader( 'metas.description', mb_substr( str_replace( "\r\n", '', $data['info']) , 0 , 70 ).' ...' );
    View::setHeader( 'metas.keywords', $data['name'].', '.$data['name_en'].', '.$data['name_kr'] );
    
    APP::$appBuffer = array($data, $chaps, $position);
}
function chapters(){
    require("cache/bible_info.php");

    $book = pos(APP::$params);
    if( ! in_array( $book , $bibleFull ) ){
        require('error/404.php');die;
    }
    $book_id = array_search( $book , $bibleFull );
    
    list($data, $chaps, $position) = Main::getBookInfo( $book_id );
    
    View::setTitle( $book."(".$data['name_en'].")" );
    
    APP::$appBuffer = array($data, $chaps, $position);
}


function chapter(){
    require("cache/bible_info.php");
    
    $book = pos(APP::$params);
    if( ! in_array( $book , $bibleFull ) ){
        require('error/404.php');die;
    }

    $book_id = array_search( $book , $bibleFull );
    $chapter_str = str_replace( 'Chapter-', '', next(APP::$params) );
    
    $region = Main::parseVerseRegion($chapter_str);
    $chapter_id = $region['chapter_id'];
    
    $chap_key = $book_id.'-'.$chapter_id;
    if( ! in_array( $chap_key , $bibleChapter ) ){
        require('error/404.php');die;
    }
    
    list($rows, $navbar, $pageTitle) = Main::getChapterVerses( $book_id, $region );
    View::setTitle( $pageTitle );
    $got_desc=false; $i=0;
    $description = '';
    while( ! $got_desc ){
        if( $rows[$i]['stype_id']=='g' ){
            $got_desc = true;
            $description = $rows[$i]['name'];
        }
        $i+=1;
    }
    
    View::setHeader( 'metas.description', strip_tags($description . ' ...') );
    View::setHeader( 'metas.keywords', $navbar['testament'].', '.$navbar['book_name'].', '.$navbar['chapter_name'] );
    
    APP::$appBuffer = array($rows, $navbar);
}
?>