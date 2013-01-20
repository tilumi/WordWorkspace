<?php
class SubjectsWtypes{
    static $useTable='subjects_wtypes';
    
    function pagelist( $submits, $pageID, $pageRows=PAGEROWS ){
        $sql ="SELECT * FROM ".self::$useTable." WHERE 1<>2";
        
        $key="key";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND `".$key."` LIKE ".Model::quote( '%'.$submits[$key].'%' , 'text');
        }
        $key="name";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND ".$key." LIKE ".Model::quote( '%'.$submits[$key].'%' , 'text');
        }
        $key="is_active";
        if( in_array( $submits[$key], array('0','1') ) ){
            $sql.=" AND ".$key." = ".Model::quote($submits[$key], 'text');
        }
        $sql.=" ORDER BY sort";
        $totalItems = Model::fetchOne( preg_replace('/^SELECT .* FROM/','SELECT COUNT(*) FROM', $sql) );
        $sql.=" LIMIT ".Model::getOffsetStart( $pageID, $pageRows ).", ".$pageRows;
        $rows = Model::fetchAll( $sql );
        
        return array($rows, $totalItems);
    }
    function findById( $id ){
        if( is_array($id) ){
            $id_list=array();
            foreach( $id as $r ){
                $id_list[]=Model::quote($r, 'text');
            }
            $sql = "SELECT * FROM ".self::$useTable." WHERE id IN (".implode(',', $id_list).")";
            $data = Model::fetchAll( $sql );
            return $data;
        }
        $sql = "SELECT * FROM ".self::$useTable." WHERE id=".Model::quote($id, 'text');
        $data = Model::fetchRow( $sql );
        return $data;
    }
    function add( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
       	$data['key']=$data['id'];
       	$data['created']=date('Y-m-d H:i:s');
        
        return Model::insert($data, self::$useTable);
    }
    function quickAdd( $name ){
        //先檢查是否有同名的類型，有則直接返回其ID
        $sql ="SELECT id FROM ".self::useTable;
        $sql.=" WHERE name=".Model::quote($name, 'text');
        $result=Model::fetchRow($sql);
        if( count($result['id'])>0 ){
            return $result['id'];
        }
        
        $data['name']=$name;
        $data['id']=uniqid('WType');
        $data['sort']='99';
        $id=$data['id'];
        
        if( Model::insert($data) ){
            return $id;
        }
        return false;
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
            $data['ids']=array( $data['id'] );
        }
        //when $data is id list
        $id=$data['ids'];
        $id_list=array();
        foreach( $id as $r ){
            $id_list[]=Model::quote($r, 'text');
        }
        $sql ="DELETE FROM ".self::$useTable;
        $sql.=" WHERE id IN (".implode(',', $id_list).')';
        return Model::exec($sql);
    }
    function setActive( $id ){
        if( is_string($id) ){
            $fields=array();
            $fields['id']=$id;
           	$fields['updated']=date('Y-m-d H:i:s');
            $fields['is_active']='1';
            
            return Model::update($fields, 'id', self::$useTable);
        }
        //when $id is array
        $ids=$id;
        foreach( $ids as $key=>$id ){
            $ids[$key]=Model::quote($id, 'text');
        }
        $sql ="UPDATE ".self::$useTable." SET is_active='1', updated=".Model::quote(date('Y-m-d H:i:s'), 'text');
        $sql.=" WHERE id IN (".implode(',', $ids).')';
        return Model::exec($sql);
    }
    function setInactive( $id ){
        if( is_string($id) ){
            $fields=array();
            $fields['id']=$id;
           	$fields['updated']=date('Y-m-d H:i:s');
            $fields['is_active']='0';
            
            return Model::update($fields, 'id', self::$useTable);
        }
        //when $id is array
        $ids=$id;
        foreach( $ids as $key=>$id ){
            $ids[$key]=Model::quote($id, 'text');
        }
        $sql ="UPDATE ".self::$useTable." SET is_active='0', updated=".Model::quote(date('Y-m-d H:i:s'), 'text');
        $sql.=" WHERE id IN (".implode(',', $ids).')';
        return Model::exec($sql);
    }
    function getList(){
        $sql ="SELECT * FROM ".self::$useTable;
        $sql.=" WHERE is_active='1'";
        $sql.=" ORDER BY sort";
        $rows=Model::fetchAll($sql);
        
        $result=array();
        foreach( $rows as $r ){
            $result[$r['id']]=$r['name'];
        }
        return $result;
    }
}
?>