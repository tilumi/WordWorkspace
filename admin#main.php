<?php
if( APP::$doctype != 'html' ){
    require('error/404.php');die;
}

$action = pos( APP::$params );
$registedAction = array(
    'index',
    'login',
    'logout',
    'passwd',
    'userinfo',
    'settings',
);
if( in_array( $action, $registedAction ) ){
    $action = array_shift(APP::$params);
}
APP::setAction($action);

$modelPath = APP::$handler.'_model.php';
if( file_exists($modelPath) ){ include( $modelPath ); }

if( in_array( $action , $registedAction ) ){
    //設定action的別名轉換
    switch( $action ){
        case 'passwd':
            $action='passwd';
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
    //View::setHeader( 'sitename', 'The Bible 線上聖經 - 最美最舒適的線上讀經網' );
    $user_id=$_SESSION['admin']['userid'];
    $sql="SELECT * FROM syslog WHERE type='login' AND userid=".Model::quote($user_id, 'text')." ORDER BY created DESC LIMIT 10";
    $logs=Model::fetchAll($sql);
    
    APP::$appBuffer = array($logs);
}
function login(){
    View::setTitle('登入');
    View::setHeader('has_layout', false);
    
    $SESSION = &$_SESSION['admin'];
    $SESSION['is_auth']==true;
    if( $SESSION['is_auth']===true ){
        redirect('/' , '你已經登入過囉！' , 'info' );
    }
    
    APP::load('vendor', 'auth.component');
    $form=AuthComponent::getLoginForm( '會員登入' );
    
    if( $form->validate() ){
        $submits = $form->getSubmitValues();
        $userid = $submits['userid'];
        $password = $submits['password'];
        $auto_login = $submits['remember'];
        
        if( AuthComponent::login( $userid , $password ) ){
            $userdata=AuthComponent::getAuthData();
            $encryptPassword=AuthComponent::getEncryptPassword();
            
            $SESSION=$userdata;
            $SESSION['is_auth'] = true;
            $SESSION['privileges'] = AuthComponent::getPrivileges( $userdata['id'] );
        	$longterm_login=false;
            if( $auto_login == 'auto' ){
                setcookie('admin_autologin[user]', $userdata['id'], time()+60*60*24*14 );
                setcookie('admin_autologin[login]', $encryptPassword, time()+60*60*24*14 );
                $longterm_login=true;
            }
        	$goto='/';
        	if( isset($_SESSION['PageBeforeLogin']) && !empty($_SESSION['PageBeforeLogin']) ){
                $goto = '_/'.$_SESSION['PageBeforeLogin'];
                unset($_SESSION['PageBeforeLogin']);
            }
            $logmsg=$SESSION['userid'].' 登入成功';
            if( $longterm_login ){
                $logmsg.='，並已設定長期登入';
            }

            APP::syslog($logmsg, APP::$prior['info'], 'login');
        	redirect( $goto , '親愛的 '.$SESSION['username'].', 歡迎回來！' , 'success' );
        }
        $sql="SELECT * FROM managers WHERE userid=".Model::quote($userid, 'text');
        $rows=Model::fetchAll($sql);
        $custom_userid='';
        if( count($rows)>0 ){
            $r=pos($rows);
            $custom_userid=$r['userid'];
        }
        APP::syslog( '某人使用帳號 '.$userid.' 嘗試登入失敗', APP::$prior['warning'], 'login', $custom_userid);
        redirect( '_/'.APP::$routing['ME'] , '你的帳號密碼有誤，請再試一次' , 'error' );
    }
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array($form);
}
function logout(){
    $SESSION = &$_SESSION['admin'];
    $userid=$SESSION['userid'];
    APP::syslog( $userid.' 登出', APP::$prior['info'], 'login');
    
    unset($_SESSION['admin'], $_SESSION['Redirect'], $_SESSION['PageBeforeLogin']); //清除登入用的轉頁標記
    setcookie('admin_autologin[user]', '', time()-3600 );
    setcookie('admin_autologin[login]', '', time()-3600 );
	redirect( '/login.html' , '登出成功！拜拜，歡迎再次回來' , 'success' );
}

function passwd(){
    View::setTitle('變更密碼');
    APP::load('vendor', 'auth.component');
    
    $form=AuthComponent::getChangePasswordForm( '變更密碼' );
    $SESSION = & $_SESSION['admin'];
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = $form->process( array( 'AuthComponent' , 'changePassword') ); 
            $userid=$SESSION['userid'];
            if( $errmsg === true ){
                APP::syslog( $userid.' 已變更密碼', APP::$prior['notice'], 'login');
                redirect( '.' , '密碼已變更成功' , 'success' );
            }
            APP::syslog( $userid.' 嘗試變更密碼失敗。失敗訊息: '.$errmsg , APP::$prior['error'], 'login');
            redirect( '' , $errmsg , 'error' );
        }
    }
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array($form);
}
function userinfo(){
    View::setTitle('設定帳戶資訊');
    
    $form=Form::create('frmUserInfo', 'post', APP::$ME );
    
    $form->addElement('header', '', '帳戶資訊' );
    $form->addElement('hidden', 'id');
    $form->addElement('text', 'username', '用戶名稱', array('class'=>'input-short'));
    $buttons=Form::buttons();
    $form->addGroup($buttons, null, null, '&nbsp;');
    $form->addRule('username', '用戶名稱 必填', 'required', null, 'client');
    
    $id=$_SESSION['admin']['id'];
    $data=Main::getUserinfo( $id );
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = $form->process( array( 'Main' , 'userinfo' ) ); 
            $userid=$_SESSION['admin']['userid'];
            if( $errmsg === true ){
                APP::syslog( $userid.' 更新了帳戶資訊', APP::$prior['notice'], 'login');
                redirect( '.' , '帳戶資訊已更新成功' , 'success' );
            }
            APP::syslog( $userid.' 嘗試更新帳戶失敗。失敗訊息: '.$errmsg , APP::$prior['error'], 'login');
            redirect( '' , $errmsg , 'error' );
        }
    }
    //$form->setDefaults($data);
    $form->setDefaults( $data );
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array($form);
}
function settings(){
    View::setTitle('快速設定');
    
    $form=Form::create('frmUserInfo', 'post', APP::$ME );
    
    $form->addElement('header', '', '快速設定帳戶' );
    $form->addElement('hidden', 'id');
    $form->addElement('text', 'username', '用戶名稱', array('class'=>'input-short'));
    
    $form->addElement('password', 'password', '請輸入原密碼', array('class'=>'input-medium'));
    $form->addElement('password', 'password1', '密碼', array('class'=>'input-medium password'));
    $form->addElement('password', 'password2', '再輸入一次', array('class'=>'input-medium'));
    
    $form->addRule('id','目標帳戶不可留空', 'required', '', 'client');
    $form->addRule('password','您必須輸入原密碼', 'required', '', 'client');
    $form->addRule('password1','您必須輸入新密碼', 'required', '', 'client');
    $form->addRule('password1','密碼必須為6位以上字母或數字', 'rangelength', array(6,64), 'client');
    $form->addRule(array('password1','password2'), '兩次密碼輸入不相符', 'compare', '', 'client');

    $form->addRule('username', '用戶名稱 必填', 'required', null, 'client');
    
    $buttons=Form::buttons();
    $form->addGroup($buttons, null, null, '&nbsp;');
    
    $id=$_SESSION['admin']['id'];
    $data=Main::getUserinfo( $id );
    $data=array(
        'id'=>$data['id'],
        'username'=>$data['username'],
    );
    
    $submits = $form->getSubmitValues();
    if( count($submits)>0 ){
        if( ! isset($submits['commit']) ){
            redirect( '.' , '使用者取消' , 'info' );
        }
        if( $form->validate() ){
            $errmsg = $form->process( array( 'Main' , 'userinfo' ) ); 
            $userid=$_SESSION['admin']['userid'];
            if( $errmsg === true ){
                APP::syslog( $userid.' 更新了帳戶資訊', APP::$prior['notice'], 'login');
                redirect( '.' , '帳戶資訊已更新成功' , 'success' );
            }
            APP::syslog( $userid.' 嘗試更新帳戶失敗。失敗訊息: '.$errmsg , APP::$prior['error'], 'login');
            redirect( '' , $errmsg , 'error' );
        }
    }
    //$form->setDefaults($data);
    $form->setDefaults( $data );
    
    $form=Form::getHtml($form);
    
    APP::$appBuffer = array($form);
}

?>