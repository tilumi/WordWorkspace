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

APP::$pageTitle = '新聞中心';
APP::$mainTitle = '新聞中心 News';
APP::$mainName = '公告';

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
    View::setTitle($mainTitle);
    
    //初始化
    $SESS = $_SESSION['settings'];
    if( ! isset($SESSION[ APP::$prefix ][ APP::$app ]) ){
        $SESS[ APP::$prefix ][ APP::$app ] = array();
        $session=& $SESS[ APP::$prefix ][ APP::$app ];
        $session['pageRows'] = PAGEROWS;
        $session['pageID'] = 1;
        $session['search'] = array();
    }
    $session=& $SESS[ APP::$prefix ][ APP::$app ];
    
    //Pager
    $pageRows = $session['pageRows'];
    $pageID = $session['pageID'];
    if( $dispatch['params']['page'] && is_numeric($dispatch['params']['page']) ){
        $pageID=(int)$dispatch['params']['page'];
        $session['pageID']=$pageID;
    }
    //使網址與頁數同步
    if( $pageID != 1 && ! isset($dispatch['params']['page'])){
        redirect( url( array('params'=>array('page'=>$pageID)) ) );
    }
    
    //Search
    $form=Form::create('frmSearch', 'get', APP::$ME );
    $form->addElement('header', '', '內容檢索' );
    $form->addElement('text', 'name', '標題', array('class'=>'input-long'));
    $form->addElement('text', 'author', '作者', array('class'=>'input-long'));
    
    $options = array(
        ''=>'--- 選擇狀態 ---',
        '1'=>'顯示',
        '0'=>'隱藏',
    );
    $form->addElement('select', 'is_active', '顯示狀態', $options, array('class'=>'input'));
    $buttons=Form::buttonsSearchForm( false );
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    $submits=$form->getSubmitValues();
    if( count($submits)>0 ){
        $session['search']=$submits;
        redirect( url( array('params'=>array('page'=>1)) ) );
    }
    $submits = $session['search'];
    $form->setDefaults($submits);
    $form=Form::getHtml($form);
    
    //Search Message
    $searchInfo=array();
    foreach($submits as $key=>$value){
        if( $value==='' ){ continue; }
        if( $key=='name' ){ $searchInfo[]='標題含"<span>'.$value.'</span>"'; }
        if( $key=='author' ){ $searchInfo[]='作者含"<span>'.$value.'</span>"'; }
        if( $key=='is_active' ){ $_=array(0=>'隱藏',1=>'直接顯示'); $searchInfo[]='顯示狀態為"<span>'.$_[$value].'</span>"'; }
    }
    
    list($rows, $totalItems) = News::pagelist($submits, $pageID, $pageRows);
    
    APP::$appBuffer = array( $rows, $totalItems, $pageID, $pageRows, $form, $searchInfo );
}


?>