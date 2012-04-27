<?php

//檢查使用者的登入狀態
userLoginCheck();




function userLoginCheck(){
    //處理登入判斷
    if( ! isset($_SESSION[ APP::$prefix ]) || ! $_SESSION[ APP::$prefix ]['is_auth'] ){
        //處理自動登入
        if( isset($_COOKIE['admin_autologin']) && !empty($_COOKIE['admin_autologin']['login']) ){
            
            APP::load('vendor', 'auth.component');
            $auth=new AuthComponent;
            
            $id=$_COOKIE['admin_autologin']['user'];
            $pwd=$_COOKIE['admin_autologin']['login'];
            
            $autologin=true;
            if( $auth->login( $id , $pwd , $autologin ) ){
                $_SESSION['admin']=array();
                $SESSION = &$_SESSION['admin'];
                
                $userdata=$auth->getAuthData();
                $encryptPassword=$auth->encryptPassword;
                
                $SESSION=$userdata;
                $SESSION['is_auth'] = true;
                $SESSION['privileges'] = $auth->getPrivileges( $userdata['id'] );
                
                APP::syslog($SESSION['userid'].' 自動登入', APP::$prior['info'], 'login');
            }
        }
    }
    
    //設定不需要登入保護的頁面
    if( APP::$app=='main' ){
        $action = pos(APP::$params);
        if( in_array($action, array('login', 'forgot_password') ) ){
            return true;
        }
    }
    
    if( ! isset($_SESSION['admin']) || ! $_SESSION['admin']['is_auth'] ){
        Admin::savePageBeforeLogin();
        redirect( '/login.html' );
    }
    //以下為登入核可後的檢查
    $SESSION = &$_SESSION['admin'];
    if( $SESSION['is_super_user'] === '1' ){
        return true; //若是超級管理者，到這裡就檢查結束了
    }
    if( APP::$app=='main' ){
        return true; //基本功能部分為當然權限，不需檢查
    }
    if( ! ACL::checkAuth( pos(APP::$params) ) ){
        $logmsg = $SESSION['userid'].' 嘗試進入無通行權的區域 ';
        $logmsg.= '@ '.APP::$routing['ME'];
        APP::syslog($logmsg, APP::$prior['warning'], 'login');
        
        redirect( '/', '抱歉！您沒有權限進入這個區域', 'error' );
    }
}
/******************************************************************************/
function RenderRedirectMSG( $message , $layout_name ){
    
    $layoutpath = DIRROOT. 'layout_admin'.DS.$layout_name.'.html'.EXT;
    ob_start();
    require( $layoutpath );
    $contents=ob_get_contents();
    ob_end_clean();
    
    return $contents;
}
class Admin{
    function savePageBeforeLogin(){
        //logout動作不紀錄
        if( APP::$app=='main' && pos(APP::$params)=='logout' ){ return false; }
        //非html操作不記錄
        if( APP::$doctype!='html' ){ return false; }
        $_SESSION['PageBeforeLogin']=APP::$routing['p'];
        return true;
    }
}
class ACL{
    function checkAuth( $item_id='' ){
        //傳入特定項目，檢查是否有權可供操作
        //return true;
        $admininfo = &$_SESSION[ APP::$prefix ];
        if( $admininfo['is_super_user']=='1' ){
            return true;
        }
        
        $item_id = APP::parseFullID( $item_id );
        
        list($prefix, $app, $action)=explode('.', $item_id);
        
        $acl = $admininfo['privileges'];
        if( isset($acl[$app][$action]) && $acl[$app][$action]=='allow' ){
            return true;
        }
        return false;
    }
    function checkSuperUser(){
        if( !isset($_SESSION['admin']['is_super_user']) ){
            return false;
        }
        if( $_SESSION['admin']['is_super_user']=='1' ){
            return true;
        }
        return false;
    }
    function checkLogin(){
        if( !isset($_SESSION['admin']['is_auth']) ){
            return false;
        }
        if( $_SESSION['admin']['is_auth']===true ){
            return true;
        }
        return false;
    }
    function checkPageAuth( $item_id ){
        //用於頁面的權限確認，無權限時要自動重導至允許的頁面
        
        if( self::chechAuth($item_id) ){
            return true;
        }else{
            $item_levels = 0;
            if( ! empty($item_id) ){
                $items=explode('.', $item_id);
                $item_levels = count($items);
            }
            
            $prefix='main';
            $app='main';
            $href_redirect='/';
            switch( $item_levels ){
                case 1:
                    // 1 個參數的時候，item_id 表示 app
                    redirect( $href_redirect , '您的權限不足，無法進入此區域');
                    break;
                case 2:
                    // 2 個參數的時候，item_id 表示 app.action
                    if( self::checkAuth( $items[0].'.index' ) ){
                        $href_redirect = $items[0].'/index.html';
                    }
                    redirect( $href_redirect , '您的權限不足，無法進入此區域');
                    break;
                case 3:
                    // 3 個參數的時候，item_id 表示 prefix.app.action
                    if( self::checkAuth( $items[1].'.index' ) ){
                        $href_redirect = $items[1].'/index.html';
                        redirect( $href_redirect , '您的權限不足，無法進入此區域');
                    }
                    break;
                default:
                    // 0 個或超過 3 個參數的時候，錯誤
                    redirect('/', '錯誤的權限表格式，請洽程式設計人員');
            }
            
        }
    }
}

?>