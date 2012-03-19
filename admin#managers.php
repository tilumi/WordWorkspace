<?php
if( APP::$doctype != 'html' ){
    require('error/404.php');die;
}

APP::$pageTitle = '網站管理員';
APP::$mainTitle='網站管理員 Managers';
APP::$mainName='管理員';

$action = pos( APP::$params );
$registedAction = array(
    'index',
    'add',
    'edit',
    'delete',
    'privileges',
    'm_edit',
    'm_delete',
    'm_priv',
    'active',
    'inactive',
    'normaluser',
    'superuser',
);
if( in_array( $action, $registedAction ) ){
    $action = array_shift(APP::$params);
}

$modelPath = APP::$handler.'_model.php';
if( file_exists($modelPath) ){ include( $modelPath ); }

$modelPath = APP::$prefix.'#groups_model.php';
if( file_exists($modelPath) ){ include( $modelPath ); }

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
if( file_exists($viewTpl) ){ include( $viewTpl ); }

/******************************************************************************/

function index(){
    View::setHeader( 'title', APP::$mainTitle );
    
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
    
    $form->addElement('text', 'userid', '帳號', array('class'=>'input-long'));
    $form->addElement('text', 'username', '人員', array('class'=>'input-long'));
    
    if( ACL::checkAuth('active') ){
        $options = array(
            ''=>'--- 選擇狀態 ---',
            '1'=>'啟用',
            '0'=>'停用',
        );
        $form->addElement('select', 'is_active', '啟用狀態', $options, array('class'=>'input'));
    }
    if( ACL::checkAuth('super_user') ){
        $options = array(
            ''=>'--- 選擇等級 ---',
            '0'=>'管理員',
            '1'=>'開發者',
        );
        $form->addElement('select', 'is_super_user', '人員層級', $options, array('class'=>'input'));
    }
    
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
        if( $key=='name' ){ $searchInfo[]='標題含"<span>'.$value.'</span>"'; }
        if( $key=='author' ){ $searchInfo[]='作者含"<span>'.$value.'</span>"'; }
        if( $key=='is_active' ){ $_=array(0=>'隱藏',1=>'直接顯示'); $searchInfo[]='顯示狀態為"<span>'.$_[$value].'</span>"'; }
    }
    
    list($rows, $totalItems) = Managers::pagelist($submits, $pageID, $pageRows);
    //$dignities=Model::call('getDignitiesByAdmin');
    
    APP::$appBuffer = array($rows,$totalItems,$pageID,$pageRows,$form,$searchInfo,$dignities);
}

function add(){
    View::setHeader( 'title', '新增'.APP::$mainTitle );
    
    $form=Form::create('frmInsert', 'post', APP::$ME );
    
    $form->addElement('header', '', '新增'.APP::$mainName );
    
    $form->addElement('text', 'userid', '帳號名稱', array('class'=>'input-short'));
    
    $form->addElement('text', 'username', '人員名稱', array('class'=>'input-medium'));
    
    $form->addElement('password', 'password1', '密碼', array('class'=>'input-short password'));
    $form->addElement('password', 'password2', '再輸入一次', array('class'=>'input-short'));
    $form->setDefaults( array('username'=>'' ) );
    
    if( ACL::checkAuth('active') ){
        $options = array(
            '1'=>'帳號啟用',
            '0'=>'暫時停用',
        );
        $form->addElement('select', 'is_active', '啟用狀態', $options, array('class'=>'input-short'));
    }
    if( ACL::checkAuth('super_user') ){
        $options = array(
            '0'=>'管理員',
            '1'=>'開發者',
        );
        $form->addElement('select', 'is_super_user', '人員層級', $options, array('class'=>'input-short'));
    }
    
    $buttons=Form::buttons();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    $form->addRule( 'userid', '管理者名稱必填', 'required', '', 'client');
    $form->addRule( 'userid', '管理者名稱長度區間', 'rangelength', array( 2,32 ), 'client');
    //$form->addRule( 'userid', '管理者名稱只允許英文和數字', 'alphanumeric', '', 'client');
    //$form->addRule('userid', '管理者名稱必須是中文', 'regex', '/^[\x{4e00}-\x{9fff}]+$/u', '');
    $form->addRule('userid', '管理者名稱必須是中文或英文', 'regex', '/^[a-zA-Z\x{4e00}-\x{9fff}]+$/u', '');
    $form->addRule('username', '人員名稱 必填', 'required', null, 'client');
    $form->addRule('password1','您必須輸入密碼', 'required', '', 'client');
    $form->addRule('password1','密碼必須為6位以上字母或數字', 'rangelength', array(6,64), 'client');
    $form->addRule(array('password1','password2'), '兩次密碼輸入不相符', 'compare', '', 'client');
    /**** 自訂規則範例 (不支援客戶端) ****/
    $form->registerRule('is_allowed_userid', 'callback', 'is_allowed_userid' );
    $form->addRule('userid', '這個帳戶名稱已被使用，請再選一個', 'is_allowed_userid', null);
    
    $form->applyFilter('name', 'trim');
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = Managers::add($submits); 
            if( $errmsg === true ){
                $userid=$_SESSION['administrator']['userid'];
                APP::syslog($userid.' 新增'.APP::$mainName.': '.$submits['userid'].' 成功', APP::$prior['info'], 'managers');
                redirect( '.' , APP::$mainName.'已新增成功' , 'success' );
            }
            $userid=$_SESSION['administrator']['userid'];
            APP::syslog($userid.' 新增'.APP::$mainName.': '.$submits['userid'].' 發生錯誤。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers');
            redirect( '.' , $errmsg , 'error' );
        }
    }
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array($form);
}
function is_allowed_userid($element_value){
    $sql ="SELECT count(userid) num FROM managers";
    $sql.=" WHERE deleted='0' AND userid=".Model::quote($element_value, 'text');
    $rownum=Model::fetchRow($sql);
    if( $rownum['num'] > 0 ){
        return false;
    }
    return true;
}

?>