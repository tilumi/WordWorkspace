<?php
class AuthComponent{
    static $pears=array();
    static $params=array(
        'dsn' => DSN,
        'table' => 'managers',
        'usernameCol' => 'userid',
        'passwordCol' => 'password',
        'cryptTypeCol' => 'algorithm',
        'saltCol' => 'salt',
        'isActiveCol' => 'is_active',
        'isActiveAllowed' => '1',
        'deletedCol' => 'deleted',
        'deletedAllowed' => '0',
        'plugin' => 'managers',
        'db_fields' => '*',
    );
    static $AuthData=array();
    static $encryptPassword='';
    
    function __call($name, $arguments) {
    }
    function login( $username , $password , $autologin=false ){
        if( is_string(self::$params['db_fields']) ){
            self::$params['db_fields']=array(self::$params['db_fields']);
        }
        
        $sql ="SELECT ".implode(',', self::$params['db_fields'])." FROM ".self::$params['table'];
        $sql.=" WHERE ".self::$params['isActiveCol']."=".APP::$mdb->quote(self::$params['isActiveAllowed'],'text');
        $sql.=" AND ".self::$params['deletedCol']."=".APP::$mdb->quote(self::$params['deletedAllowed'],'text');
        if( ! $autologin ){
            $sql.=" AND ".self::$params['usernameCol']."=".APP::$mdb->quote($username,'text');
        }else{
            $sql.=" AND id=".APP::$mdb->quote($username,'text');
        }
        $rows=Model::fetchAll($sql);
        //echo count($rows).'<br>';
        // Began Verify
        if( count($rows) != 1 ){
            return false;
        }
        $userdata=$rows[0];
        if( empty($userdata['algorithm']) || empty($userdata['salt']) || empty($userdata['password']) ){
            return false;
        }
        $userid=$userdata['id'];
        $algorithm=$userdata['algorithm'];
        $salt=$userdata['salt'];
        
        if( ! $autologin ){
            if( ! function_exists($algorithm) ){
                return false;
            }
            $encrypt = $algorithm( $salt.$password.$salt );
            if( $encrypt != $userdata[ self::$params['passwordCol'] ] ){
                return false;
            }
        }else{
            if( $password != $userdata[ self::$params['passwordCol'] ] ){
                return false;
            }
        }
        
        // When Verifying Passed, go throuth here.f
        self::$AuthData = $userdata;
        self::$encryptPassword = $encrypt;
        
        $sql ='UPDATE '.self::$params['table'];
        $sql.=' SET last_login='.APP::$mdb->quote( date('Y-m-d H:i:s'), 'date');
        $sql.=' , last_login_ip='.APP::$mdb->quote( self::getUserClientIP() , 'text');
        $sql.=' WHERE id = '.APP::$mdb->quote( $userdata['id'], 'text' );
        APP::$mdb->exec($sql);
        
        return true;
    }
    function getLoginForm( $header='' ){
        APP::load('pear', 'HTML/QuickForm');
        
        $form=Form::create('frmLogin', 'post', APP::$ME );
        
        $form->addElement('password', 'userid', '管理者');
        $form->addElement('password', 'password', '密碼');
        $form->addElement('advcheckbox', 'remember', '', '兩週內記得我的登入', '', array('no','auto'));
        $form->addElement('submit', '', '送出');
        
        $form->addRule( 'userid', '管理者名稱必填', 'required', '', 'client');
        //$form->addRule( 'userid', '管理者名稱長度區間', 'rangelength', array( 2,32 ), 'client');
        //$form->addRule( 'userid', '管理者名稱只允許英文和數字', 'alphanumeric', '', 'client');
        //$form->addRule('userid', '管理者名稱必須是中文', 'regex', '/^[\x{4e00}-\x{9fff}]+$/u', '');
        $form->addRule('userid', '管理者名稱必須是中文或英文', 'regex', '/^[a-zA-Z\x{4e00}-\x{9fff}]+$/u', '');
        $form->addRule( 'password', '密碼必填', 'required', '', 'client');
        //$form->addRule( 'password', '密碼長度區間', 'rangelength', array(6,64), 'client');
        
        $form->applyFilter('userid', 'trim');
        
        return $form;
    }
    function getChangePasswordForm( $header ){
        $form=Form::create('frmChangePassword', 'post', ME );
        
        $form->addElement('header', '', $header );
        
        $form->addElement('hidden', 'id', $_SESSION['admin']['id'] );
        $form->addElement('password', 'password', '請輸入原密碼', array('class'=>'input-medium'));
        $form->addElement('password', 'password1', '密碼', array('class'=>'input-medium password'));
        $form->addElement('password', 'password2', '再輸入一次', array('class'=>'input-medium'));
        
        $buttons=array();
        $buttons[] = &HTML_QuickForm::createElement('submit', 'commit', '送出', array('class'=>'submit-green'));
        $buttons[] = &HTML_QuickForm::createElement('reset' , '', '重設', array('class'=>'submit-gray'));
        $buttons[] = &HTML_QuickForm::createElement('button', '', '取消', array('class'=>'submit-gray','onclick'=>'this.form.submit();'));
        $form->addGroup($buttons, null, null, '&nbsp;');
        
        $form->addRule('id','目標帳戶不可留空', 'required', '', 'client');
        $form->addRule('password','您必須輸入原密碼', 'required', '', 'client');
        $form->addRule('password1','您必須輸入新密碼', 'required', '', 'client');
        $form->addRule('password1','密碼必須為6位以上字母或數字', 'rangelength', array(6,64), 'client');
        $form->addRule(array('password1','password2'), '兩次密碼輸入不相符', 'compare', '', 'client');
        
        return $form;
    }
    function changePassword( $data ){
        $rows=Model::fetchAll($sql);
        
        //checking password
        $row = Model::fetchById( $data['id'] , 'administrators' );
        $algorithm=$row['algorithm'];
        $check['salt']=$row['salt'];
        $check['password']=$algorithm( $check['salt'].$data['password'].$check['salt'] );
        if( $check['password'] != $row['password'] ){
            return '原密碼輸入錯誤';
        }
        
        //encrypt password
        $algorithm='sha1';
        $salt=$algorithm(uniqid());
        $data['algorithm']=$algorithm;
        $data['salt']=$salt;
        $data['password']=$algorithm( $salt.$data['password1'].$salt );
        unset($data['password1'],$data['password2'],$data['commit']);
        
        $integer_fields=array('is_active','is_super_user');
        foreach( $data as $field=>$value ){
            if( in_array( $field , $integer_fields ) ){
                $data[$field]=APP::$mdb->quote( $value , 'integer' );
                continue;
            }
            $data[$field]=APP::$mdb->quote( $value , 'text' );
        }
        
        if( Model::update( $data , 'id' , 'administrators' ) ){
            return true;
        }
        return '密碼變更失敗，請再試一次';
    }
    function getAuthData(){
        $authdata=self::$AuthData;
        if( count($authdata)<1 ){ return array(); }
        
        unset($authdata[ self::$params['cryptTypeCol'] ]);
        unset($authdata[ self::$params['saltCol'] ]);
        unset($authdata[ self::$params['passwordCol'] ]);
        unset($authdata[ self::$params['isActiveCol'] ]);
        unset($authdata[ self::$params['deletedCol'] ]);
        unset($authdata[ 'plugin' ]);
        
        return $authdata;
    }
    function getEncryptPassword(){
        return self::$encryptPassword;
    }
    function getPrivileges( $userid ){
        //個人層級權限設定
        $sql="SELECT * FROM privileges WHERE request=".APP::$mdb->quote( $userid , 'text');
        $res=APP::$mdb->query($sql);
        
        $personal=array();
        while( $row = APP::$mdb->fetchRow($res) ){
            $content=$row['content'];
            $access=$row['access'];
            list($plugin,$ctrler,$action)=explode(':', $content);
            $personal[$plugin][$ctrler][$action]=$access;
        }
        
        //檢查群組權限表是否存在，不存在就略過群組權限的處理
        $sql= "SHOW TABLES LIKE 'dignities_admins'";
        $res = APP::$mdb->query($sql);
        $count=APP::$mdb->numRows($res);
        $priv=array();
        if( $count>0 ){
            //群組層級(管理員身分)權限設定
            $sql="SELECT * FROM dignities_admins WHERE admin_id=".APP::$mdb->quote( $userid , 'text')." ORDER BY sort";
            $res=APP::$mdb->query($sql);
            
            $dignities=array();
            $dignities_quote=array();
            while( $row = APP::$mdb->fetchRow($res) ){
                $dignities[]=$row['dignity_id'];
                $dignities_quote[]=APP::$mdb->quote( $row['dignity_id'] , 'text');
            }
            
            if( count($dignities_quote)>0 ){
                $sql="SELECT * FROM privileges WHERE request IN (".implode(',', $dignities_quote).")";
                $res=APP::$mdb->query($sql);
                
                $groups=array();
                while( $row = APP::$mdb->fetchRow($res) ){
                    $request=$row['request'];
                    $content=$row['content'];
                    $access=$row['access'];
                    list($plugin,$ctrler,$action)=explode(':', $content);
                    $groups[$request][$plugin][$ctrler][$action]=$access;
                }
                
                foreach( $groups as $group ){
                    $priv=$priv+$group;
                }
            }
        }
        $priv=$priv+$personal;
        
        return $priv;
    }
    function getUserClientIP(){
        $ip=false;
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip){
                array_unshift($ips, $ip); $ip = FALSE;
            }
            $ips_levels=count($ips);
            for ($i = 0; $i<$ips_levels; $i++){
                if (!preg_match ('/^(10|172\.16|192\.168)\./', $ips[$i])){
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }
}
?>