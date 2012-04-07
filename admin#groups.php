<?php
if( APP::$doctype != 'html' ){
    require('error/404.php');die;
}

APP::$pageTitle = '管理員群組';
APP::$mainTitle='管理員群組 Managers Groups';
APP::$mainName='群組';

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
    $form=Form::create('frmSearch', 'get', APP::$ME );
    
    $form->addElement('header', '', '內容檢索' );
    
    $form->addElement('text', 'name', APP::$mainName.'名稱', array('class'=>'input-long'));
    
    if( ACL::checkAuth('active') ){
        $options = array(
            ''=>'--- 選擇狀態 ---',
            '1'=>'顯示',
            '0'=>'隱藏',
        );
        $form->addElement('select', 'is_active', '顯示狀態', $options, array('class'=>'input'));
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
        if( $key=='name' ){ $searchInfo[]=APP::$mainName.'名稱含"<span>'.$value.'</span>"'; }
        if( $key=='is_active' ){ $_=array(0=>'隱藏',1=>'直接顯示'); $searchInfo[]='顯示狀態為"<span>'.$_[$value].'</span>"'; }
    }
    
    list($rows, $totalItems) = Groups::pagelist($submits, $pageID, $pageRows);
    
    APP::$appBuffer = array($rows,$totalItems,$pageID,$pageRows,$form,$searchInfo,$dignities);
}
function add(){
    $form=Form::create('frmInsert', 'post', APP::$ME );
    
    $form->addElement('header', '', '新增'.APP::$mainName );
    
    $form->addElement('text', 'name', '身分名稱', array('class'=>'input-short'));
    $form->addElement('text', 'info', '身分說明', array('class'=>'input-medium'));
    $form->addElement('text', 'sort', '排序', array('class'=>'input', 'value'=>1 ));
    
    if( ACL::checkAuth( array('action'=>'active') ) ){
        $options = array(
            '1'=>'直接顯示',
            '0'=>'暫時隱藏',
        );
        $form->addElement('select', 'is_active', '顯示狀態', $options, array('class'=>'input-short'));
    }
    
    $buttons=Form::buttons();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    $form->addRule('name', '名稱 必填', 'required', null, 'client');
    $form->addRule('name', '身分名稱 應 2~32 個字元', 'rangelength', array( 2,32 ), 'client');
    $form->addRule('info', '身分說明 必填', 'required', null, 'client');
    $form->addRule('sort', '排序 必填', 'required', null, 'client');
    $form->addRule('sort', '排序 必須是數字', 'numeric', null, 'client');

    $form->applyFilter('name', 'trim');
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = Groups::add($submits); 
            if( $errmsg === true ){
                $userid=$_SESSION['admin']['userid'];
                APP::syslog($userid.' 新增'.APP::$mainName.': '.$submits['userid'].' 成功', APP::$prior['info'], 'managers' );
                redirect( '.' , APP::$mainName.'已新增成功' , 'success' );
            }
            $userid=$_SESSION['admin']['userid'];
            APP::syslog($userid.' 新增'.APP::$mainName.': '.$submits['userid'].' 發生錯誤。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
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
    $data = Groups::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    $form=Form::create('frmUpdate', 'post', APP::$ME );
    
    $form->addElement('header', '', '編輯'.APP::$mainName );
    
    $form->addElement('hidden', 'id');
    $form->addElement('text', 'name', '身分名稱', array('class'=>'input-short'));
    $form->addElement('text', 'info', '身分說明', array('class'=>'input-medium'));
    $form->addElement('text', 'sort', '排序', array('class'=>'input'));
    
    if( ACL::checkAuth( array('action'=>'active') ) ){
        $options = array(
            '1'=>'直接顯示',
            '0'=>'暫時隱藏',
        );
        $form->addElement('select', 'is_active', '顯示狀態', $options, array('class'=>'input-short'));
    }
    
    $buttons=Form::buttons();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    $form->addRule('name', '名稱 必填', 'required', null, 'client');
    $form->addRule('name', '身分名稱 應 2~32 個字元', 'rangelength', array( 2,32 ), 'client');
    $form->addRule('info', '身分說明 必填', 'required', null, 'client');
    $form->addRule('sort', '排序 必填', 'required', null, 'client');
    $form->addRule('sort', '排序 必須是數字', 'numeric', null, 'client');

    $form->applyFilter('name', 'trim');
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = Groups::edit($submits); 
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
function privileges(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = Groups::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    if( $data['is_super_user']=='1' ){
        redirect( '.' , '不能變更這個使用者的權限' , 'attention' );
    }
    
    $priv=sfYaml::load( dirname(__FILE__).'/configs/privileges.yml' );
    
    $form=Form::get( 'privileges' ,  '設定'.APP::$mainName.'權限: &nbsp; '.$data['username'].' ('.$data['name'].')', $data, $priv );
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = Groups::setPrivileges($submits); 
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
    $priv=Groups::loadPrivileges($data['id']);
    $default=array('userid'=>$data['id']) + $priv;
    $form->setDefaults( $default ); 
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array($form);
}
function delete(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = Groups::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    $form=Form::create('frmDelete', 'post', APP::$ME );
    
    $form->addElement('header', '', '您確定要刪除'.APP::$mainName.' '.$data['userid'].' ?' );
    
    $form->addElement('hidden', 'id');
    $form->addElement('static', 'name', '名稱', array('class'=>'input-medium'));
            
    $buttons=Form::buttonsNoReset();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    $form->applyFilter('name', 'trim');
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = Groups::delete($submits); 
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
            $errmsg = Groups::setActive($items);
            break;
        case 'inactive':
            $errmsg = Groups::setInactive($items);
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
        $errmsg = Groups::delete($submits); 
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
    $form=Form::get( 'm_delete' , '以下'.APP::$mainName.'都將被刪除，是否確認：', $rows );
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array($form);
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
            $errmsg = Groups::setNormalUser($items);
            break;
        case 'super_user':
            $errmsg = Groups::setSuperUser($items);
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
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = Groups::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    if( $errmsg = Groups::setActive($id) ){
        $userid=$_SESSION['admin']['userid'];
        APP::syslog($userid.' '.APP::$mainName.' '.$data['name'].' 已設定為直接顯示', APP::$prior['info'], 'managers' );
        redirect( '.' , APP::$mainName.' '.$data['name'].' 已設定為直接顯示' , 'success' );
    }
    $userid=$_SESSION['admin']['userid'];
    APP::syslog($userid.' '.APP::$mainName.' '.$data['name'].' 設定為直接顯示失敗。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
    redirect( '.' , $errmsg , 'error' );
}
function inactive(){
    $id = pos(APP::$params);
    if( empty($id) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    $data = Groups::findById($id);
    if( !(is_array($data) && count($data)>0) ){
        redirect( '.' , '指定的'.APP::$mainName.'不存在' , 'attention' );
    }
    
    $errmsg = Groups::setInactive($id);
    if( $errmsg === true ){
        $userid=$_SESSION['admin']['userid'];
        APP::syslog($userid.' '.APP::$mainName.' '.$data['name'].' 已設定為暫時隱藏', APP::$prior['info'], 'managers' );
        redirect( '.' , APP::$mainName.' '.$data['name'].' 已設定為暫時隱藏' , 'success' );
    }
    $userid=$_SESSION['admin']['userid'];
    APP::syslog($userid.' '.APP::$mainName.' '.$data['name'].' 設定為暫時隱藏失敗。錯誤訊息: '.$errmsg, APP::$prior['error'], 'managers' );
    redirect( '.' , $errmsg , 'error' );
}

?>