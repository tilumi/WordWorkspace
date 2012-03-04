<?php
if( APP::$doctype != 'html' ){
    require('error/404.php');die;
}

$action = pos( APP::$params );
$registedAction = array(
    'index',
    'chpasswd',
);
if( in_array( $action, $registedAction ) ){
    $action = array_shift(APP::$params);
}

include( APP::$handler.'_model.php' );

if( in_array( $action , $registedAction ) ){
    //設定action的別名轉換
    switch( $action ){
        case 'chpasswd':
            $action='chpasswd';
            break;
    }
    //執行action
    $action();
}else{
    require('error/404.php');die;
}



$viewTpl = APP::$handler.'='.$action.'.php';
if( file_exists($viewTpl) )
    include( $viewTpl );

/******************************************************************************/

function index(){
    //View::setHeader( 'sitename', 'The Bible 線上聖經 - 最美最舒適的線上讀經網' );
    APP::$appBuffer = array($rows);
}

function chpasswd(){
    View::setTitle('變更密碼');
/*    $form=AuthComponent::getChangePasswordForm( '變更密碼' );
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = $form->process( array( &$this->Auth , 'changePassword') ); 
            $userid=$_SESSION['administrator']['userid'];
            if( $errmsg === true ){
                APP::syslog( $userid.' 已變更密碼', APP::$prior['notice'], 'login');
                redirect( '.' , '密碼已變更成功' , 'success' );
            }
            APP::syslog( $userid.' 嘗試變更密碼失敗。失敗訊息: '.$errmsg , APP::$prior['error'], 'login');
            redirect( '' , $errmsg , 'error' );
        }
    }
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array($form);*/
}
?>