<?php
if( APP::$doctype != 'html' ){
    require('error/404.php');die;
}

$action = pos( APP::$params );
$registedAction = array(
    'index',
);
if( in_array( $action, $registedAction ) ){
    $action = array_shift(APP::$params);
}

APP::$pageTitle = '系統紀錄';
APP::$mainTitle = '系統紀錄 Syslog';
APP::$mainName = '紀錄';

$modelPath = APP::$handler.'_model.php';
if( file_exists($modelPath) ){ include( $modelPath ); }

if( in_array( $action , $registedAction ) ){
    //設定action的別名轉換
    switch( $action ){
        case 'change-passwd':
            $action='changepwd';
            break;
    }
    //執行action
    $action();
}else{
    require('error/404.php');die;
}



$viewTpl = APP::$handler.'='.$action.'.php';
if( file_exists($viewTpl) ){ include( $viewTpl ); }

/******************************************************************************/

function index(){
    View::setTitle(APP::$mainTitle);
    
    //初始化
    $SESS = & $_SESSION['Pager'];
    if( ! isset($SESS[ APP::$prefix ][ APP::$app ]) ){
        $SESS[ APP::$prefix ][ APP::$app ] = array();
        $SESS[ APP::$prefix ][ APP::$app ]['pageRows'] = PAGEROWS;
        $SESS[ APP::$prefix ][ APP::$app ]['pageID'] = 1;
        $SESS[ APP::$prefix ][ APP::$app ]['search'] = array();
    }
    $session=& $SESS[ APP::$prefix ][ APP::$app ];
    
    //Pager
    $pageRows = $session['pageRows'];
    $pageID = $session['pageID'];
    if( isset($_GET['pageID']) && is_numeric($_GET['pageID']) ){
        $pageID=(int)$_GET['pageID'];
        $session['pageID']=$pageID;
    }
    
    //Search
    $form=Form::create('frmSearch', 'post', APP::$ME );
    $form->addElement('header', '', '內容檢索' );
    $form->addElement('text', 'name', '紀錄內容', array('class'=>'input-long'));
    $buttons=Form::buttonsSearchForm( false );
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    //紀錄搜尋資料
    $submits=$form->getSubmitValues();
    if( count($submits)>0 ){
        $session['search']=$submits;
        $session['pageID'] = 1;
        redirect('.'); //重導洗掉GET參數
    }
    
    $submits = $session['search'];
    $form->setDefaults($submits);
    $form=Form::getHtml($form);
    
    //Search Message
    $searchInfo=array();
    foreach($submits as $key=>$value){
        if( $value==='' ){ continue; }
        if( $key=='name' ){ $searchInfo[]='<u>系統紀錄</u> 含 "<span>'.$value.'</span>" '; }
    }
    
    list($rows, $totalItems) = Syslog::pagelist($submits, $pageID, $pageRows);
    APP::$appBuffer = array( $rows, $totalItems, $pageID, $pageRows, $form, $searchInfo );
}


?>