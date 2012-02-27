<?php
$doctype=APP::$routing['doctype'];
if( $doctype != 'html' ){
    require('error/404.php');die;
}

$action = pos( APP::$params );
$registerAction = array(
    'index',
);
if( in_array( $action, $registerAction ) ){
    $action = array_shift(APP::$params);
}

include( APP::$handler.'_model.php' );

if( in_array( $action , $registerAction ) ){
    $action();
}else{
    require('error/404.php');die;
}



$viewTpl = APP::$routing['handler'].'='.$action.'.php';
if( file_exists($viewTpl) )
    include( $viewTpl );

/******************************************************************************/

function index(){
    //View::setHeader( 'sitename', 'The Bible 線上聖經 - 最美最舒適的線上讀經網' );
    APP::$appBuffer = array($rows);
}
?>