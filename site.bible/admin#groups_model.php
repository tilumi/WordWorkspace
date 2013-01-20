<?php
class Groups{
    static $useTable='groups';
    
    function pageList( $submits, $pageID, $pageRows=PAGEROWS ){
        $sql ="SELECT * FROM ".self::$useTable." WHERE 1<>2";
        
        $key="name";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND ".$key." LIKE ".Model::quote( '%'.$submits[$key].'%' , 'text');
        }
        $key="is_active";
        if( in_array( $submits[$key], array('0','1') ) ){
            $sql.=" AND ".$key." = ".Model::quote($submits[$key], 'text');
        }
        $sql.=" AND deleted='0'";
        $sql.=" ORDER BY sort";
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
        $data['id']=uniqid('Group');
       	$data['created']=date('Y-m-d H:i:s');
        
        return Model::insert($data, self::$useTable);
    }
    function edit( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
       	$data['updated']=date('Y-m-d H:i:s');
        
        return Model::update($data, 'id', self::$useTable);
    }
    function delete( $data ){
        if( isset($data['id']) ){
            $fields=array();
            $fields['id']=$data['id'];
            $fields['deleted']='1';
            return Model::update($fields, 'id', self::$useTable);
        }
        //when $data is id list
        $ids=$data['ids'];
        foreach( $ids as $key=>$id ){
            $ids[$key]=Model::quote($id, 'text');
        }
        
        $sql="UPDATE ".Model::$useTable." SET deleted='1' WHERE id IN (".implode(',', $ids).')';
        return Model::exec($sql);
    }
    function setPrivileges( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
        //pr($data);die;
        $request=$data['userid'];
        unset($data['userid']);
        
        //pr($data);die;
        Model::exec('START TRANSACTION');
        $sql="DELETE FROM privileges WHERE request=".Model::quote($request, 'text');
        Model::exec($sql);
        
        $rows=array();
        foreach( $data as $key=>$actions ){
            $fields=array();
            if( !preg_match( "/^priv/" , $key ) ){ continue; }
            foreach( $actions as $action=>$setting ){
                list(  , $app )=explode(':', $key);
                $access='';
                if( $setting==='allow' ) $access='allow';
                if( $setting==='deny' ) $access='deny';
                if( $setting==='neutral' ) $access='neutral';
                if( $access==='' ){ continue; }
                
                $fields['request']=$request;
                $fields['access']=$access;
                
                $actions = array( $action );
                if( isset($data['represent'][$app][$action]) ){
                    $actions = explode(',', $data['represent'][$app][$action]);
                }
                foreach( $actions as $a ){
                    $content = $app.'.'.$a;
                    $fields['content']=$content;
                    $rows[]=$fields;
                }
            }
        }
        if( Model::inserts($rows, 'privileges') === false ){
            Model::exec('ROLLBACK');
            return '歐喔！不明原因的失敗，請再試一次. Error Code 1';
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
    function loadFullACLs( $userid ){
        //取出實際的權限資料
        $priv_actived=self::loadPrivileges($userid);
        
        //取出帳戶資料
        $data=self::findById($userid);
        
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
                $acls[$key]['methods'][$priv_name]='neutral';
                if( isset($priv_actived['priv:'.$app][$action]) && $priv_actived['priv:'.$app][$action]==='allow' ){
                    $acls[$key]['methods'][$priv_name]='allow';
                }
                if( isset($priv_actived['priv:'.$app][$action]) && $priv_actived['priv:'.$app][$action]==='deny' ){
                    $acls[$key]['methods'][$priv_name]='deny';
                }
            }
        }
        return $acls;
    }
    function setActive( $id ){
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
        $sql="UPDATE ".Model::$useTable." SET is_active='1' WHERE id IN (".implode(',', $ids).')';
        return Model::exec($sql);
    }
    function setInactive( $id ){
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
        
        $sql="UPDATE ".Model::$useTable." SET is_active='0' WHERE id IN (".implode(',', $ids).')';
        return Model::exec($sql);
    }
    function getList(){
        $sql ="SELECT * FROM ".self::$useTable." WHERE 1<>2";
        $sql.=" AND deleted='0' AND is_active='1' ORDER BY sort";
        $rows=Model::fetchAll($sql);
        $list=array();
        foreach( $rows as $r){
            $list[ $r['id'] ]=$r['name'];
        }
        return $list;
    }
    function getInfo(){
        $sql ="SELECT * FROM ".self::$useTable." WHERE 1<>2";
        $sql.=" AND deleted='0' AND is_active='1' ORDER BY sort";
        $rows=Model::fetchAll($sql);
        $list=array();
        foreach( $rows as $r){
            $list[ $r['id'] ]=$r;
        }
        return $list;
    }
    function getByManagers(){
        $list=self::getInfo();
        
        $sql="SELECT * FROM groups_managers ORDER BY manager_id, sort";
        $rows=Model::fetchAll($sql);
        $managers=array();
        foreach( $rows as $r){
            $managers[ $r['manager_id'] ][]=$list[ $r['group_id'] ];
        }
        return $managers;
    }
}
?>