<?php
$doctype=APP::$routing['doctype'];
if( $doctype != 'html' ){
    require('error/404.php');die;
}
$action = pos( APP::$params );
//全都是中文字，表示為指定書卷或是章節
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
if( in_array($action, array('index') ) ){
    $action=array_shift(APP::$params);
}

$registerAction = array(
    'index',
);

include( APP::$handler.'_model.php' );

if( in_array( $action , $registerAction ) ){
    $action();
}else{
    require('error/404.php');die;
}



$viewTpl = APP::$handler.'='.$action.'.php';
if( file_exists($viewTpl) )
    include( $viewTpl );

/******************************************************************************/

function index(){
    $rows=Main::getSongs();
    View::setHeader( 'sitename', '主的愛& 線上歌本' );
    APP::$appBuffer = array($rows);
}
?>