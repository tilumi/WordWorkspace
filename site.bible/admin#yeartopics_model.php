<?php
class YearTopics{
    static $useTable='yeartopics';
    
    function pagelist( $submits, $pageID, $pageRows=PAGEROWS ){
        $sql ="SELECT * FROM ".self::$useTable." WHERE 1<>2";
        
        $key="name";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND ".$key." LIKE ".Model::quote( '%'.$submits[$key].'%' , 'text');
        }
        $key="name_kr";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND ".$key." LIKE ".Model::quote( '%'.$submits[$key].'%' , 'text');
        }
        $key="year_bigger_then";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND year >= ".Model::quote( $submits[$key] , 'text');
        }
        $key="year_smaller_then";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND year <= ".Model::quote( $submits[$key] , 'text');
        }
        $key="is_active";
        if( in_array( $submits[$key], array('0','1') ) ){
            $sql.=" AND ".$key." = ".Model::quote($submits[$key], 'text');
        }
        $sql.=" AND deleted='0'";
        $sql.=" ORDER BY id DESC";
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
       	$data['year']=$data['id'];
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
           	$fields['updated']=date('Y-m-d H:i:s');
            $fields['deleted']='1';
            return Model::update($fields, 'id', self::$useTable);
        }
        //when $data is id list
        $id=$data['ids'];
        $id_list=array();
        foreach( $id as $r ){
            $id_list[]=Model::quote($r, 'text');
        }
        $sql ="UPDATE ".self::$useTable." SET deleted='1', updated=".Model::quote(date('Y-m-d H:i:s'), 'text');
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
}
?>