<?php
class SongsLangs{
    static $useTable='songs_langs';
    
    function pagelist( $submits ){
        $sql ="SELECT * FROM ".self::$useTable." WHERE 1<>2";
        
        $key="id";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND ".$key." LIKE ".Model::quote( $submits[$key].'%' , 'text');
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
        $rows = Model::fetchAll( $sql );
        
        return array($rows);
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
       	$data['id']=strtolower($data['id']);
       	$data['created']=date('Y-m-d H:i:s');
        
        return Model::insert($data, self::$useTable);
    }
    function edit( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
       	$data['id']=strtolower($data['id']);
       	$data['updated']=date('Y-m-d H:i:s');
        
        return Model::update($data, 'id', self::$useTable);
    }
    function delete( $data ){
        if( isset($data['id']) ){
            $sql="DELETE FROM ".self::$useTable." WHERE id=".Model::quote($data['id'], 'text');
            return Model::exec($sql);
        }
        //when $data is id list
        $id=$data['ids'];
        $id_list=array();
        foreach( $id as $r ){
            $id_list[]=Model::quote($r, 'text');
        }
        $sql="DELETE FROM ".self::$useTable." WHERE id IN (".implode(',', $id_list).')';
        return Model::exec($sql);
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
        $sql="UPDATE ".self::$useTable." SET is_active='1' WHERE id IN (".implode(',', $ids).')';
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
        $sql="UPDATE ".self::$useTable." SET is_active='0' WHERE id IN (".implode(',', $ids).')';
        return Model::exec($sql);
    }
}
?>