<?php
class Managers{
    static $useTable='managers';
    
    function pageList( $submits, $pageID, $pageRows=PAGEROWS ){
        $sql ="SELECT * FROM ".self::$useTable." WHERE 1<>2";
        
        $key="userid";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND ".$key." LIKE ".Model::quote( '%'.$submits[$key].'%' , 'text');
        }
        $key="username";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND ".$key." LIKE ".Model::quote( '%'.$submits[$key].'%' , 'text');
        }
        $key="is_active";
        if( in_array( $submits[$key], array('0','1') ) ){
            $sql.=" AND ".$key." = ".Model::quote($submits[$key], 'text');
        }
        $key="is_super_user";
        if( in_array( $submits[$key], array('0','1') ) ){
            $sql.=" AND ".$key." = ".Model::quote($submits[$key], 'text');
        }
        $sql.=" AND deleted='0'";
        //$sql.=" ORDER BY sort";
        $totalItems = Model::fetchOne( preg_replace('/^SELECT .* FROM/','SELECT COUNT(*) FROM', $sql) );
        $sql.=" LIMIT ".Model::getOffsetStart( $pageID, $pageRows ).", ".$pageRows;
        $rows = Model::fetchAll($sql);
        
        return array($rows, $totalItems);
    }
    function findById( $id ){
        if( is_array($id) ){
            $id_list=array();
            foreach( $id as $r ){
                $id_list[]=Model::quote($r, 'text');
            }
            $sql = "SELECT * FROM ".self::$useTable." WHERE id IN (".implode(',', $id_list).") AND deleted='0'";
            $data = Model::fetchAll( $sql );
            return $data;
        }
        $sql = "SELECT * FROM ".self::$useTable." WHERE id=".Model::quote($id, 'text')." AND deleted='0'";
        $data = Model::fetchRow( $sql );
        return $data;
    }
    function add( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
        $data['id']=uniqid('Manager');
        //將密碼進行加密，並儲存回傳的加密參數
        APP::load('vendor', 'auth.component');
        $encrypt=AuthComponent::encrypt( $data['password1'] );
        $data['algorithm']=$encrypt['algorithm'];
        $data['salt']=$encrypt['salt'];
        $data['password']=$encrypt['encrypt'];
        unset($data['password1'],$data['password2']);
       	$data['created']=date('Y-m-d H:i:s');
        
        return Model::insert($data, self::$useTable);
    }
    function edit( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
        $id=$data['id'];
        //是否越權操作
        if( self::is_above_permit_level($id) ){ return '不能設定這個使用者'; }
        
        //檢查不正常的屬性關閉動作
        $id=$data['id'];
        if( $data['is_active']=='0' ){
            if( self::is_last_superuser($id) ){ return '系統中不能沒有全域管理者'; }
            if( self::is_last_admin($id) ){ return '系統中不能沒有管理者'; }
        }
        if( $data['is_super_user']=='0' ){
            if( self::is_last_superuser($id) ){ return '系統中不能沒有全域管理者'; }
            if( self::is_last_admin($id) ){ return '系統中不能沒有管理者'; }
        }
        //encrypt password
        if( ! empty($data['password1']) ){
            if( $data['password1'] !== $data['password2'] ){
                return "兩次密碼輸入不同，沒有任何資料被修改";
            }
            //直接更新及儲存密碼
            APP::load('vendor', 'auth.component');
            AuthComponent::passwd( $id, $data['password1'] );
        }
       	$data['updated']=date('Y-m-d H:i:s');
        unset($data['password1'],$data['password2']);
        
        return Model::update($data, 'id', self::$useTable);
    }
    function setGroup( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
        
        $groups=$data['groups'];
        $manager_id=$data['id'];
        if( is_string($groups) ){
            $groups=array( $groups );
        }
        $groups=array_unique($groups);
        
        $fields=array();
        if( count($groups)>0 ){
            Model::exec('START TRANSACTION');
            $sql ="DELETE FROM groups_managers WHERE manager_id=".Model::quote( $manager_id , 'text' );
            Model::exec($sql);
            foreach($groups as $group_id){
                if( empty($group_id) ){ continue; }
                $fields=array(
                    'manager_id'=>$manager_id,
                    'group_id'=>$group_id,
                    'sort'=>0,
                );
                
                if( ! Model::insert($fields, 'groups_managers') ){
                    Model::exec('ROLLBACK');
                    return '更新失敗，請再試一次';
                }
            }
            Model::exec('COMMIT');
        }
        return true;
    }
    function delete( $data ){
        if( isset($data['id']) ){
            //檢查
            if( self::is_above_permit_level($data['id']) ){ return '不能設定這個使用者'; }
            if( self::is_last_superuser($data['id']) ){ return '系統中不能沒有全域管理者'; }
            if( self::is_last_admin($data['id']) ){ return '系統中不能沒有管理者'; }
            
            $fields=array();
            $fields['id']=$data['id'];
            $fields['deleted']='1';
            return Model::update($fields, 'id', self::$useTable);
        }
        //when $data is id list
        if( self::is_above_permit_level($data['ids']) ){ return '不能設定這個使用者'; }
        if( self::is_last_superuser($data['ids']) ){ return '系統中不能沒有全域管理者'; }
        if( self::is_last_admin($data['ids']) ){ return '系統中不能沒有管理者'; }
        $ids=$data['ids'];
        foreach( $ids as $key=>$id ){
            $ids[$key]=Model::quote($id, 'text');
        }
        
        $sql="UPDATE ".self::$useTable." SET deleted='1' WHERE id IN (".implode(',', $ids).')';
        return Model::exec($sql);
    }
    function setPrivileges( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
        //pr($data);die;
        $userid=$data['userid'];
        $request=$userid;
        unset($data['userid']);
        
        //取得群組設定
        $sql="SELECT group_id FROM groups_managers WHERE manager_id=".Model::quote($userid, 'text');
        $group_id=Model::fetchOne($sql);
        $group=Groups::loadPrivileges($group_id);
        
        //pr($data);die;
        Model::exec('START TRANSACTION');
        $sql="DELETE FROM privileges WHERE request=".Model::quote($request, 'text');
        Model::exec($sql);
        
        $fields=array();
        foreach( $data as $key=>$actions ){
            if( !preg_match( "/^priv/" , $key ) ){ continue; }
            foreach( $actions as $action=>$setting ){
                list(  , $app )=explode(':', $key);
                $access='';
                if( $setting==='allow' ) $access='allow';
                if( $setting==='deny' ) $access='deny';
                if( $setting==='deny-locked' ) continue; //拒絕鎖定的設定直接跳過，因為將直接繼承群組的設定
                if( $access==='' ){ continue; }
                
                $fields['request']=$request;
                $fields['access']=$access;
                
                $actions = array( $action );
                if( isset($data['represent'][$app][$action]) ){
                    $actions = explode(',', $data['represent'][$app][$action]);
                }
                foreach( $actions as $a ){
                    $write_in = true;
                    if( $access === $group['priv:'.$app][$a] ){
                        //與群組重複的設定就跳過
                        $write_in = false;
                    }
                    if( $access === 'deny' && $group['priv:'.$app][$a]==='neutral' ){
                        //群組沒給，自行設定也沒給的就跳過
                        $write_in = false;
                    }
                    if( ! $write_in ){ continue; }
                    $content = $app.'.'.$a;
                    $fields['content']=$content;
                    $rows[]=$fields;
                }
            }
        }
        if( count($rows)>0 && Model::inserts($rows, 'privileges') === false ){
            Model::exec('ROLLBACK');
            return '歐喔！不明原因的失敗，請再試一次. Error Code 1';
        }
        Model::exec('COMMIT');
        return true;
    }
    function loadPrivileges( $userid ){
        $sql="SELECT * FROM privileges WHERE request=".Model::quote( $userid , 'text');
        $rows=Model::fetchAll($sql);
        
        $personal=array();
        foreach( $rows as $row ){
            $content=$row['content'];
            $access=$row['access'];
            list($app,$action)=explode('.', $content);
            $personal['priv:'.$app][$action]=$access;
        }

        //檢查群組權限表是否存在，不存在就略過群組權限的處理
        $sql= "SHOW TABLES LIKE 'groups_managers'";
        $res = Model::query($sql);
        $count=Model::numRows($res);
        $priv=array();
        if( $count>0 ){
            //群組層級(管理員身分)權限設定
            $sql="SELECT * FROM groups_managers WHERE manager_id=".Model::quote( $userid , 'text')." ORDER BY sort";
            $res=Model::query($sql);
            
            $dignities=array();
            $dignities_quote=array();
            while( $row = Model::fetchRow($res) ){
                $dignities[]=$row['group_id'];
                $dignities_quote[]=Model::quote( $row['group_id'] , 'text');
            }
            
            if( count($dignities_quote)>0 ){
                $sql="SELECT * FROM privileges WHERE request IN (".implode(',', $dignities_quote).")";
                $res=Model::query($sql);
                
                $groups=array();
                while( $row = APP::$mdb->fetchRow($res) ){
                    $request=$row['request'];
                    $content=$row['content'];
                    $access=$row['access'];
                    list($app,$action)=explode('.', $content);
                    if( $access==='deny' ){ $access='deny-locked'; }
                    if( $access==='neutral' ){ $access='deny'; }
                    $groups[$request]['priv:'.$app][$action]=$access;
                }
                
                foreach( $groups as $group ){
                    $priv=$priv+$group;
                }
            }
        }
        //權限表最後結算
        $privs=array();
        foreach( $priv as $auth_app=>$actions ){
            foreach( $actions as $auth_action=>$auth_value ){
                $auth_u = $personal[$auth_app][$auth_action];
                $auth_g = $priv[$auth_app][$auth_action];
                if( $auth_g==='deny-locked' ){
                    $privs[$auth_app][$auth_action]=$auth_g;
                    continue;
                }
                if( $auth_g==='deny' && $auth_u==='allow' ){
                    $privs[$auth_app][$auth_action]='allow';
                    continue;
                }
                if( $auth_g==='allow' && $auth_u==='deny' ){
                    $privs[$auth_app][$auth_action]='deny';
                    continue;
                }
                $privs[$auth_app][$auth_action]=$auth_g;
            }
        }

        return $privs;
    }
    function loadFullACLs( $userid ){
        //取出實際的權限資料
        $priv_actived=self::loadPrivileges($userid);
        
        //取出帳戶資料
        $data=self::findById($userid);
        
        //判斷是否是全域管理者
        $super_user=false;
        if( $data['is_super_user']==='1' ) $super_user=true;
        
        //取出權限表
        APP::load( 'vendor', 'Symfony'.DS.'yaml'.DS.'sfYaml' );
        $priv_acls=sfYaml::load( dirname(__FILE__).DS.'config'.DS.'privileges.yml' );
        
        //計算完整的權限狀態
        $acls=array();
        foreach($priv_acls as $key=>$priv){
            $name=$priv['name'];
            if( isset($priv['type']) && !empty($priv['type']) ){
                $acls[$key]=$priv;
                continue;
            }
            
            $app='';
            if( isset($priv['app']) && !empty($priv['app']) ){
                $app=$priv['app'];
            }
            
            $acls[$key]=$priv;
            $methods = $acls[$key]['methods'];
            foreach( $methods as $priv_name=>$actions ){
                $action = pos($actions);
                if( $super_user ){
                    $acls[$key]['methods'][$priv_name]='allow';
                    continue;
                }
                $acls[$key]['methods'][$priv_name]='deny';
                if( isset($priv_actived['priv:'.$app][$action]) && $priv_actived['priv:'.$app][$action]==='allow' ){
                    $acls[$key]['methods'][$priv_name]='allow';
                }
            }
        }
        return $acls;
    }
    function setActive( $id ){
        //是否越權操作
        if( self::is_above_permit_level($id) ){ return '不能設定這個使用者'; }
        
        if( is_string($id) ){
            $fields=array();
            $fields['id']=$id;
            $fields['is_active']='1';
            
            return Model::update($fields, 'id', self::$useTable);
        }
        //when $id is array
        $ids=$id;
        foreach( $ids as $key=>$id ){
            $ids[$key]=Model::quote($id, 'text');
        }
        $sql="UPDATE ".self::$useTable." SET is_active='1' WHERE id IN (".implode(',', $ids).')';
        return Model::exec($sql);
    }
    function setInactive( $id ){
        //是否越權操作
        if( self::is_above_permit_level($id) ){ return '不能設定這個使用者'; }
        if( self::is_last_superuser($id) ){ return '系統中不能沒有全域管理者'; }
        if( self::is_last_admin($id) ){ return '系統中不能沒有管理者'; }
        
        if( is_string($id) ){
            $fields=array();
            $fields['id']=$id;
            $fields['is_active']='0';
            
            return Model::update($fields, 'id', self::$useTable);
        }
        //when $id is array
        $ids=$id;
        foreach( $ids as $key=>$id ){
            $ids[$key]=Model::quote($id, 'text');
        }
        
        $sql="UPDATE ".self::$useTable." SET is_active='0' WHERE id IN (".implode(',', $ids).')';
        return Model::exec($sql);
    }
    function setNormalUser( $id ){
        //是否越權操作
        if( self::is_above_permit_level($id) ){ return '不能設定這個使用者'; }
        if( self::is_last_superuser($id) ){ return '系統中不能沒有全域管理者'; }
        if( self::is_last_admin($id) ){ return '系統中不能沒有管理者'; }
        
        if( is_string($id) ){
            
            $fields=array();
            $fields['id']=$id;
            $fields['is_super_user']='0';
            
            return Model::update($fields, 'id', self::$useTable);
        }
        //when $id is array
        $ids=$id;
        foreach( $ids as $key=>$id ){
            $ids[$key]=Model::quote($id, 'text');
        }
        
        $sql="UPDATE ".self::$useTable." SET is_super_user='0' WHERE id IN (".implode(',', $ids).')';
        return Model::exec($sql);
    }
    function setSuperUser( $id ){
        //是否越權操作
        if( self::is_above_permit_level($id) ){ return '不能設定這個使用者'; }

        if( is_string($id) ){
            $fields=array();
            $fields['id']=$id;
            $fields['is_super_user']='1';
            
            return Model::update($fields, 'id', self::$useTable);
        }
        //when $id is array
        $ids=$id;
        foreach( $ids as $key=>$id ){
            $ids[$key]=Model::quote($id, 'text');
        }
        $sql="UPDATE ".self::$useTable." SET is_super_user='1' WHERE id IN (".implode(',', $ids).')';
        return Model::exec($sql);
    }
    function is_last_superuser( $target_id ){
        if( is_string($target_id) ){
            $target_id=array($target_id);
        }
        foreach( $target_id as $key=>$id ){
            $target_id[$key]=Model::quote($id, 'text');
        }
        $ids=implode(',', $target_id);
        
        $sql ="SELECT count(id) FROM ".self::$useTable;
        $sql.=" WHERE is_super_user='1' AND is_active='1' AND deleted='0'";
        $sql.=" AND id NOT IN (".$ids.")";
        $count=Model::fetchOne($sql);
        if( $count<1 ){ return true; }
        return false;
    }
    function is_last_admin( $target_id ){
        if( is_string($target_id) ){
            $target_id=array($target_id);
        }
        foreach( $target_id as $key=>$id ){
            $target_id[$key]=Model::quote($id, 'text');
        }
        $ids=implode(',', $target_id);
        
        $sql ="SELECT count(id) FROM ".self::$useTable;
        $sql.=" WHERE is_active='1' AND deleted='0'";
        $sql.=" AND id NOT IN (".$ids.")";
        $count=Model::fetchOne($sql);
        if( $count<1 ){ return true; }
        return false;
    }
    function is_above_permit_level( $target_id ){
        //是否越權操作
        if( ! ACL::checkSuperUser() ){
            if( is_string($target_id) ){
                $target_id=array($target_id);
            }
            foreach( $target_id as $key=>$id ){
                $target_id[$key]=Model::quote($id, 'text');
            }
            $ids=implode(',', $target_id);
            
            //一般管理者不能設定全域管理者
            $sql ="SELECT is_super_user FROM ".self::$useTable;
            $sql.=" WHERE id IN (".$ids.")";
            $rows=Model::fetchAll($sql);
            foreach( $rows as $r ){
                if( $r['is_super_user']=='1' ){ return true; }
            }
        }
        return false;
    }
    function getGroupsList(){
        return Groups::getList();
    }
    function getGroupsByManagers(){
        return Groups::getByManagers();
    }
    
}
?>