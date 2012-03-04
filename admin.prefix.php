<?php
//處理登入判斷
if( ! isset($_SESSION['administrator']) || ! $_SESSION['administrator']['is_auth'] ){
    //處理自動登入
    if( isset($_COOKIE['admin_autologin']) && !empty($_COOKIE['admin_autologin']['login']) ){
        
        APP::load('component', 'Auth');
        $auth=new AuthComponent;
        
        $id=$_COOKIE['admin_autologin']['user'];
        $pwd=$_COOKIE['admin_autologin']['login'];
        
        $autologin=true;
        if( $auth->login( $id , $pwd , $autologin ) ){
            $_SESSION['administrator']=array();
            $SESSION = &$_SESSION['administrator'];
            
            $userdata=$auth->getAuthData();
            $encryptPassword=$auth->encryptPassword;
            
            $SESSION=$userdata;
            $SESSION['is_auth'] = true;
            $SESSION['privileges'] = $auth->getPrivileges( $userdata['id'] );
            
            APP::syslog($SESSION['userid'].' 自動登入成功', APP::$prior['info'], 'login');
        }
    }
}

//設定不需要登入保護的頁面
if( $dispatch['plugin']=='main' && $dispatch['controller']=='main' ){
    if( in_array($dispatch['action'], array('login', 'forgot_password') ) ){
        return true;
    }
}

if( ! isset($_SESSION['administrator']) || ! $_SESSION['administrator']['is_auth'] ){
    self::savePageBeforeLogin();
    redirect( array('plugin'=>'main', 'action'=>'login') );
}
//以下為登入核可後的檢查
$SESSION = &$_SESSION['administrator'];
if( $SESSION['is_super_user'] === '1' ){
    return true; //若是超級管理者，到這裡就檢查結束了
}
if( $dispatch['plugin']=='main' && $dispatch['controller']=='main' ){
    return true; //基本功能部分為當然權限，不需檢查
}
if( ! self::__privileges( $dispatch ) ){
    $logmsg = $SESSION['userid'].' 嘗試進入無權通行的區域 ';
    $logmsg.= '@ '.ME;
    APP::syslog($logmsg, APP::$prior['warning'], 'login');
    
    redirect( array('plugin'=>'main'), '抱歉！您沒有權限進入這個區域', 'error' );
}

/******************************************************************************/
function __privileges( $dispatch ){
    //檢查目前的頁面，用戶是否有權可用
    $privileges = &$_SESSION['administrator']['privileges'];
    extract($dispatch); //will get region, plugin, controller, action, doctype
    if( isset($privileges[ $plugin ][ $controller ][ $action ])
        && $privileges[ $plugin ][ $controller ][ $action ]=='allow' ){
        return true;
    }
    return false;
}
function savePageBeforeLogin(){
    global $dispatch;
    //logout動作不紀錄
    if( $dispatch['plugin']=='main' && $dispatch['controller']=='main' && $dispatch['action']=='logout' ){ return false; }
    //非html操作不記錄
    if( $dispatch['doctype']!='html' ){ return false; }
    $_SESSION['PageBeforeLogin']=ME;
    return true;
}
function checkSuperUser(){
    if( !isset($_SESSION['administrator']['is_super_user']) ){
        return false;
    }
    if( $_SESSION['administrator']['is_super_user']=='1' ){
        return true;
    }
    return false;
}
function checkAuth( $item=array() ){
    global $dispatch;
    //傳入特定項目，檢查是否有權可供操作
    $privs = &$_SESSION['administrator']['privileges'];
    if( $_SESSION['administrator']['is_super_user']=='1' ){
        return true;
    }
    
    $plugin='main';
    $controller='main';
    $action='index';
    $first_param='';
    if( isset($item['plugin']) && !empty($item['plugin']) ){
        $plugin=$item['plugin'];
        if( empty($first_param) ){ $first_param='plugin'; }
    }
    if( isset($item['controller']) && !empty($item['controller']) ){
        $controller=$item['controller'];
        if( empty($first_param) ){ $first_param='controller'; }
    }
    if( isset($item['action']) && !empty($item['action']) ){
        $action=$item['action'];
        if( empty($first_param) ){ $first_param='action'; }
    }
    if( $first_param=='plugin' ){ /*do nothing*/ }
    if( $first_param=='controller' ){ $plugin=$dispatch['plugin']; }
    if( $first_param=='action' ){ $plugin=$dispatch['plugin'];$controller=$dispatch['controller']; }
    if( empty($first_param) ){ return false; }
    
    if( isset($privs[$plugin][$controller][$action]) && $privs[$plugin][$controller][$action]=='allow' ){
        return true;
    }
    return false;
}

?>