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
        $totalItems = Model::numRows($sql);
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
    function dignity( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
        
        $dignities=$data['dignities'];
        $admin_id=$data['id'];
        if( is_string($dignities) ){
            $dignities=array( $dignities );
        }
        $dignities=array_unique($dignities);
        
        $fields=array();
        if( count($dignities)>0 ){
            Model::exec('START TRANSACTION');
            $sql ="DELETE FROM dignities_admins WHERE admin_id=".Model::quote( $admin_id , 'text' );
            Model::exec($sql);
            foreach($dignities as $dignity_id){
                if( empty($dignity_id) ){ continue; }
                if( ! Model::insert($fields, 'dignities_admins', false) ){
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
        $request=Model::quote($userid, 'text');
        unset($data['userid']);
        
        //pr($data);die;
        Model::exec('START TRANSACTION');
        $sql="DELETE FROM privileges WHERE request=".$request;
        Model::exec($sql);
        
        $fields=array();
        foreach( $data as $key=>$actions ){
            if( !preg_match( "/^priv/" , $key ) ){ continue; }
            foreach( $actions as $action=>$setting ){
                list(  , $app )=explode(':', $key);
                $access='deny';
                if( $setting=='allow' ) $access='allow';
                
                if( $access!='allow' ){ continue; }
                
                $access = Model::quote( $access, 'text' );
                
                $actions = array( $action );
                if( isset($data['represent'][$app][$action]) ){
                    $actions = explode(',', $data['represent'][$app][$action]);
                }
                foreach( $actions as $a ){
                    $content = Model::quote( $app.'.'.$a , 'text' );
                    $sql="INSERT INTO privileges (request, content, access) VALUES ( $request , $content , $access )";
                    if( Model::exec($sql) === false ){
                        Model::exec('ROLLBACK');
                        return '歐喔！不明原因的失敗，請再試一次. Error Code 1';
                    }
                }
            }
        }
        Model::exec('COMMIT');
        return true;
    }
    function loadPrivileges( $userid ){
        $sql="SELECT * FROM privileges WHERE request=".Model::quote( $userid , 'text');
        $rows=Model::fetchAll($sql);
        
        $priv=array();
        foreach( $rows as $row ){
            $content=$row['content'];
            $access=$row['access'];
            list($app,$action)=explode('.', $content);
            $priv['priv:'.$app][$action]=$access;
        }
        return $priv;
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
    function getDignitiesList(){
        return Dignities::getList();
    }
    function getDignitiesByAdmin(){
        return Dignities::getByAdmin();
    }
    
}
?>