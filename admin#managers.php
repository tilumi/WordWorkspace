<?php
if( APP::$doctype != 'html' ){
    require('error/404.php');die;
}

APP::$pageTitle = '系統管理員';
APP::$mainTitle='系統管理員 Managers';
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
    View::setHeader( 'title', APP::$pageTitle );
    
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
    $form->addRule('userid', '這個帳戶名稱已被使用，請再選一個', '_is_allowed_userid', null);
    
    $form->applyFilter('name', 'trim');
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = Managers::add($submits); 
            if( $errmsg === true ){
                $userid=$_SESSION['admin']['userid'];
                APP::syslog($userid.' 新增'.APP::$mainName.': '.$submits['userid'].' 成功', APP::$prior['info'], 'managers');
                redirect( '.' , APP::$mainName.'已新增成功' , 'success' );
            }
            $userid=$_SESSION['admin']['userid'];
            APP::syslog($userid.' 新增'.APP::$mainName.': '.$submits['userid'].' 發生錯誤。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers');
            redirect( '.' , $errmsg , 'error' );
        }
    }
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array($form);
}
function edit(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $sql = "SELECT * FROM managers WHERE id=".Model::quote($id, 'text');
    $data = Model::fetchRow( $sql );
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    View::setTitle('編輯'.APP::$mainName.': &nbsp; '.$data['username']);

    $form=Form::create('frmUpdate', 'post', APP::$ME );
    
    $form->addElement('header', '', '編輯'.APP::$mainName );
    
    $form->addElement('hidden', 'id');
    $form->addElement('text', 'userid', '帳號名稱 (不能編輯)', array('class'=>'input-short','disabled'));
    //$form->setDefaults( array('id'=>'admin' ) );
    
    $form->addElement('text', 'username', '人員名稱', array('class'=>'input-short'));
    
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
    $form->addElement('password', 'password1', '密碼 (如不變更請留空)', array('class'=>'input-short password'));
    $form->addElement('password', 'password2', '再輸入一次', array('class'=>'input-short'));
    
    
    $buttons=Form::buttons();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    $form->addRule('username', '人員名稱 必填', 'required', null, 'client');
    $form->addRule('password1','密碼必須為6位以上字母或數字', 'minlength',6, 'client');
    $form->addRule(array('password1','password2'), '兩次密碼輸入不相符', 'compare', '', 'client');
    
    $form->applyFilter('name', 'trim');

    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = Managers::edit($submits); 
            if( $errmsg === true ){
                $userid=$_SESSION['admin']['userid'];
                APP::syslog($userid.' 編輯'.APP::$mainName.': '.$submits['userid'].' 成功', APP::$prior['info'], 'managers' );
                redirect( '.' , APP::$mainName.'已編輯成功' , 'success' );
            }
            $userid=$_SESSION['admin']['userid'];
            APP::syslog($userid.' 編輯'.APP::$mainName.': '.$submits['userid'].' 失敗。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
            redirect( '' , $errmsg , 'error' );
        }
    } 
    $form->setDefaults($data);
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array($form);
}
function dignity(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $sql = "SELECT * FROM managers WHERE id=".Model::quote($id, 'text');
    $data = Model::fetchRow( $sql );
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    $form=Form::get( 'dignity', '設定'.APP::$mainName.'身分' );
    View::setTitle('設定'.APP::$mainName.'身分：'.$data['username']);
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = Model::call( 'dignity', $submits ); 
            if( $errmsg === true ){
                $userid=$_SESSION['admin']['userid'];
                APP::syslog($userid.' 設定'.APP::$mainName.'身分: '.$submits['userid'].' 成功', APP::$prior['info'], 'managers' );
                redirect( '.' , APP::$mainName.'已設定身分成功' , 'success' );
            }
            $userid=$_SESSION['admin']['userid'];
            APP::syslog($userid.' 設定'.APP::$mainName.'身分: '.$submits['userid'].' 失敗。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
            redirect( '' , $errmsg , 'error' );
        }
    } 
    $dignities=Model::call('getDignitiesByAdmin');
    $data['dignities']=$dignities[ $data['id'] ][0];
    $form->setDefaults($data);
    
    $form=Form::getHtml($form);
    
    View::set( compact('form') );
    View::render();
}
function privileges(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $sql = "SELECT * FROM managers WHERE id=".Model::quote($id, 'text');
    $data = Model::fetchRow( $sql );
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    if( $data['is_super_user']=='1' ){
        redirect( '.' , '不能變更這個使用者的權限' , 'attention' );
    }
    
    LoadVendors( 'Symfony'.DS.'yaml'.DS.'sfYaml' );
    $priv=sfYaml::load( dirname(__FILE__).'/config/privileges.yml' );
    
    $form=Form::get( 'privileges', '設定'.APP::$mainName.'權限: &nbsp; '.$data['username'].' ('.$data['userid'].')', $data, $priv );
    View::setTitle('設定'.APP::$mainName.'權限: &nbsp; '.$data['username']);
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = Model::call( 'setPrivileges', $submits ); 
            if( $errmsg === true ){
                $userid=$_SESSION['admin']['userid'];
                APP::syslog($userid.' 變更'.APP::$mainName.'權限: '.$data['userid'].' 成功', APP::$prior['info'], 'managers' );
                redirect( '.' , APP::$mainName.'權限已設定成功' , 'success' );
            }
            $userid=$_SESSION['admin']['userid'];
            APP::syslog($userid.' 變更'.APP::$mainName.'權限: '.$data['userid'].' 失敗。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
            redirect( '.' , $errmsg , 'error' );
        }
    }
    $priv=Model::call( 'loadPrivileges', $data['id'] );
    $default=array('userid'=>$data['id']) + $priv;
    $form->setDefaults( $default ); 
    
    $form=Form::getHtml($form, 'privileges');
    
    View::set( compact('form') );
    View::render();
}
function delete(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $sql = "SELECT * FROM managers WHERE id=".Model::quote($id, 'text');
    $data = Model::fetchRow( $sql );
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    View::setTitle('刪除'.APP::$mainName.': &nbsp; '.$data['username']);
    
    $form=Form::create('frmDelete', 'post', APP::$ME );
    
    $form->addElement('header', '', '您確定要刪除'.APP::$mainName.' '.$data['userid'].' ？' );
    
    $form->addElement('hidden', 'id');
    $form->addElement('hidden', 'userid');
    $form->addElement('static', 'userid', '帳號名稱', array('class'=>'input-short','disabled'));
    //$form->setDefaults( array('id'=>'admin' ) );
    
    $form->addElement('static', 'username', '人員名稱', array('class'=>'input-medium'));
    
    $buttons=Form::buttonsNoReset();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    $form->applyFilter('name', 'trim');
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = Managers::delete($submits); 
            if( $errmsg === true ){
                $userid=$_SESSION['admin']['userid'];
                APP::syslog($userid.' 刪除'.APP::$mainName.': '.$submits['userid'].' 成功', APP::$prior['info'], 'managers' );
                redirect( '.' , APP::$mainName.'已刪除' , 'success' );
            }
            $userid=$_SESSION['admin']['userid'];
            APP::syslog($userid.' 刪除'.APP::$mainName.': '.$submits['userid'].' 失敗。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
            redirect( '.' , $errmsg , 'error' );
        }
    } 
    $form->setDefaults($data);
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array($form);
}
function m_edit(){
    $form=Form::create('frmList', 'post', ME );
    $submits = $form->getSubmitValues();
    
    $type=$submits['mode'];
    
    $allowed_type=array(
        'active'=>'已啟用',
        'inactive'=>'已停用',
    );
    if( ! array_key_exists($type, $allowed_type) ){
        redirect( '.' , '不允許這樣的操作' , 'attention' );
    }
    
    $items=$submits['items'];
    if( count($items)<1 ){
        redirect('.', '尚未選擇執行目標，您必須先選擇執行的項目', 'error');
    }
    switch( $type ){
        case 'active':
            $errmsg = Model::call( 'setActive', $items );
            break;
        case 'inactive':
            $errmsg = Model::call( 'setInactive', $items );
            break;
    }
    if( $errmsg === true ){
        $userid=$_SESSION['admin']['userid'];
        APP::syslog($userid.' 指定的'.count($items).'位'.APP::$mainName.' '.$allowed_type[$type].' id:'.implode(',',$submits['items']), APP::$prior['info'], 'managers' );
        redirect('.', '指定的 '.count($items).' 位'.APP::$mainName.$allowed_type[$type] , 'success');
    }
    $userid=$_SESSION['admin']['userid'];
    APP::syslog($userid.' 指定的'.count($items).'位'.APP::$mainName.' '.$allowed_type[$type].'失敗 id:'.implode(',',$submits['items']).'。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
    redirect('.', $errmsg , 'error');
}
function m_delete(){
    $form=Form::create('frmMultiple', 'post', array('action'=>'m_delete') );
    $submits = $form->getSubmitValues();
    
    if( isset($submits['commit']) ){
        $num=count($submits['ids']);
        $errmsg = Model::call( 'delete', $submits ); 
        if( $errmsg === true ){
            $userid=$_SESSION['admin']['userid'];
            APP::syslog($userid.' 指定的'.count($items).'位'.APP::$mainName.' 已刪除 id:'.implode(',',$submits['items']), APP::$prior['info'], 'managers' );
            redirect( '.' , '指定的 '.$num.' 位'.APP::$mainName.'已刪除' , 'success' );
        }
        $userid=$_SESSION['admin']['userid'];
        APP::syslog($userid.' 指定的'.count($items).'位'.APP::$mainName.' 已刪除失敗 id:'.implode(',',$submits['items']).'。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
        redirect('.', $errmsg , 'error');
    }
    if( !isset($submits['items']) || count($submits['items'])<1 ){
        redirect('.', '尚未選擇執行目標，您必須先選擇執行的項目', 'error');
    }
    $items=$submits['items'];
    $rows=Model::fetchById( $items );
    $form=Form::get( 'm_delete', '以下'.APP::$mainName.'都將被刪除，是否確認：', $rows );
    $form=Form::getHtml($form);
    
    View::set( compact('form') );
    View::render();
}
function m_priv(){
    $form=Form::create('frmList', 'post', ME );
    $submits = $form->getSubmitValues();
    
    $type=$submits['mode'];
    
    $allowed_type=array(
        'normal_user'=>'已設定為一般管理員',
        'super_user'=>'已設定為超級管理員',
    );
    if( ! array_key_exists($type, $allowed_type) ){
        redirect( '.' , '不允許這樣的操作' , 'attention' );
    }
    
    $items=$submits['items'];
    if( count($items)<1 ){
        redirect('.', '尚未選擇執行目標，您必須先選擇執行的項目', 'error');
    }
    switch( $type ){
        case 'normal_user':
            $errmsg = Model::call( 'setNormalUser', $items );
            break;
        case 'super_user':
            $errmsg = Model::call( 'setSuperUser', $items );
            break;
    }
    if( $errmsg === true ){
        $userid=$_SESSION['admin']['userid'];
        APP::syslog($userid.' 指定的'.count($items).'位'.APP::$mainName.' '.$allowed_type[$type].' id:'.implode(',',$submits['items']), APP::$prior['info'], 'managers' );
        redirect('.', '指定的 '.count($items).' 位'.APP::$mainName.$allowed_type[$type] , 'success');
    }
    $userid=$_SESSION['admin']['userid'];
    APP::syslog($userid.' 指定的'.count($items).'位'.APP::$mainName.' '.$allowed_type[$type].'失敗 id:'.implode(',',$submits['items']).'。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
    redirect('.', $errmsg , 'error');
}
function active(){
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = Model::fetchById( $id );
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    if( $errmsg = Model::call( 'setActive', $id ) ){
        $userid=$_SESSION['admin']['userid'];
        APP::syslog($userid.' '.APP::$mainName.' '.$data['userid'].' ('.$data['username'].') 帳戶已設定為啟用', APP::$prior['info'], 'managers' );
        redirect( '.' , APP::$mainName.' '.$data['userid'].' ('.$data['username'].') 帳戶已設定為啟用' , 'success' );
    }
    $userid=$_SESSION['admin']['userid'];
    APP::syslog($userid.' '.APP::$mainName.' '.$data['userid'].' ('.$data['username'].') 帳戶設定為啟用失敗。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
    redirect( '.' , $errmsg , 'error' );
}
function inactive(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $sql = "SELECT * FROM managers WHERE id=".Model::quote($id, 'text');
    $data = Model::fetchRow( $sql );
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    $errmsg = Model::call( 'setInactive', $id );
    if( $errmsg === true ){
        $userid=$_SESSION['admin']['userid'];
        APP::syslog($userid.' '.APP::$mainName.' '.$data['userid'].' ('.$data['username'].') 帳戶已設定為停用', APP::$prior['info'], 'managers' );
        redirect( '.' , APP::$mainName.' '.$data['userid'].' ('.$data['username'].') 帳戶已設定為停用' , 'success' );
    }
    $userid=$_SESSION['admin']['userid'];
    APP::syslog($userid.' '.APP::$mainName.' '.$data['userid'].' ('.$data['username'].') 帳戶設定為停用失敗。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
    redirect( '.' , $errmsg , 'error' );
}
function normal_user(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $sql = "SELECT * FROM managers WHERE id=".Model::quote($id, 'text');
    $data = Model::fetchRow( $sql );
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    $errmsg = Model::call( 'setNormalUser', $id );
    if( $errmsg === true ){
        $userid=$_SESSION['admin']['userid'];
        APP::syslog($userid.' '.APP::$mainName.' '.$data['userid'].' ('.$data['username'].') 帳戶已設定為一般管理員', APP::$prior['info'], 'managers' );
        redirect( '.' , APP::$mainName.' '.$data['userid'].' ('.$data['username'].') 帳戶已設定為一般管理員' , 'success' );
    }
    $userid=$_SESSION['admin']['userid'];
    APP::syslog($userid.' '.APP::$mainName.' '.$data['userid'].' ('.$data['username'].') 帳戶設定為一般管理員失敗。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
    redirect( '.' , $errmsg , 'error' );
}
function super_user(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $sql = "SELECT * FROM managers WHERE id=".Model::quote($id, 'text');
    $data = Model::fetchRow( $sql );
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }

    if( $errmsg = Model::call( 'setSuperUser', $id ) ){
        $userid=$_SESSION['admin']['userid'];
        APP::syslog($userid.' '.APP::$mainName.' '.$data['userid'].' ('.$data['username'].') 帳戶已設定為超級管理員', APP::$prior['info'], 'managers' );
        redirect( '.' , APP::$mainName.' '.$data['userid'].' ('.$data['username'].') 帳戶已設定為超級管理員' , 'success' );
    }
    $userid=$_SESSION['admin']['userid'];
    APP::syslog($userid.' '.APP::$mainName.' '.$data['userid'].' ('.$data['username'].') 帳戶設定為超級管理員失敗。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
    redirect( '.' , $errmsg , 'error' );
}
function _is_allowed_userid($element_value){
    $sql ="SELECT count(userid) num FROM managers";
    $sql.=" WHERE deleted='0' AND userid=".Model::quote($element_value, 'text');
    $rownum=Model::fetchRow($sql);
    if( $rownum['num'] > 0 ){
        return false;
    }
    return true;
}

?>