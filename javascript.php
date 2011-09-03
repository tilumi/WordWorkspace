<?php
$doctype=APP::$routing['doctype'];
if( $doctype != 'js' ){
    require('error/404.php');die;
}

header('Content-type: text/javascript; charset=utf-8');

$action = pos( APP::$params );
if( empty($action) ){ $action='index'; }
if( $action == 'init' ){ array_shift(APP::$params); }

$registerAction = array(
    'init',
);

if( in_array( $action , $registerAction ) ){
    $action();
}else{
    require('error/404.php');die;
}

$viewTpl = APP::$routing['app'].'='.$action.'.php';
if( file_exists($viewTpl) )
    include( $viewTpl );

/******************************************************************************/

function init(){
    //這是仿js的程式
    $start=(int) pos(APP::$params);
    $start-=5;
    if( $start < 0 ){ $start+=66; }
    APP::$appBuffer = array($start);
}

?>