<?php
class BibleVerses{
    static $useTable='cuv_chapters';
    
    function pagelist( $submits, $pageID, $pageRows=PAGEROWS ){
        $sql ="SELECT c.*, b.name as book_name FROM ".self::$useTable." c";
        $sql.=" JOIN bible_books b ON b.id=c.book_id";
        $sql.=" WHERE 1<>2";
        
        $key="book_name";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND b.name LIKE ".Model::quote( '%'.$submits[$key].'%' , 'text');
        }
        $key="chapter_id";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND c.".$key." = ".Model::quote( $submits[$key] , 'text');
        }
        $key="name";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND c.".$key." LIKE ".Model::quote( '%'.$submits[$key].'%' , 'text');
        }
        //$sql.=" AND deleted='0'";
        //$sql.=" ORDER BY published DESC";
        
        
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
            $sql = "SELECT c.*, b.name as book_name FROM ".self::$useTable." c JOIN bible_books b ON b.id=c.book_id WHERE c.id IN (".implode(',', $id_list).")";
            $data = Model::fetchAll( $sql );
            return $data;
        }
        $sql = "SELECT c.*, b.name as book_name FROM ".self::$useTable." c JOIN bible_books b ON b.id=c.book_id WHERE c.id=".Model::quote($id, 'text');
        $data = Model::fetchRow( $sql );
        return $data;
    }
    function findByUrn( $urn ){
        if( is_array($urn) ){
            $urn_list=array();
            foreach( $urn as $r ){
                $urn_list[]=Model::quote($r, 'text');
            }
            $sql = "SELECT * FROM ".self::$useTable." WHERE id IN (".implode(',', $urn_list).")";
            $data = Model::fetchAll( $sql );
            return $data;
        }
        $sql = "SELECT * FROM ".self::$useTable." WHERE id=".Model::quote($urn, 'text');
        $data = Model::fetchRow( $sql );
        return $data;
    }
    function getVerses( $id ){
        $sql ="SELECT c.*, s.name as stype_name, s.class as stype_class, s.info as stype_info FROM cuv c";
        $sql.=" JOIN bible_stypes s ON s.id=c.stype_id WHERE c.id LIKE ".Model::quote($id.'%', 'text');
        $rows = Model::fetchAll( $sql );
        
        return $rows;
    }
    function add( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
       	$data['id']=uniqid('News');
       	$data['urn']=$data['name'];
       	$data['created']=date('Y-m-d H:i:s');
        
        return Model::insert($data, self::$useTable);
    }
    function edit( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
        
        $verses=$data['verses'];
        unset($data['verses']);
        $relates=$data['relates'];
        unset($data['relates']);
        $data['updated']=date('Y-m-d H:i:s');
        
        //取出比對資料，有更新才寫入
        $rows=self::getVerses($data['id']);
        $_verses=array();
        $_relates=array();
        foreach( $rows as $r ){
            $_verses[ $r['id'] ]=$r['name'];
            if( ! in_array($r['stype_id'], array('g', 'h')) ){
                $_relates[ $r['id'] ]=$r['relate'];
            }
        }
        
        foreach( $verses as $key=>$name ){
            if( $name===$_verses[$key] && ( ! isset($relates[$key]) || $relates[$key]===$_relates[$key]) ){ continue; }
            $update=array();
            $update['id']=$key;
            if( $name!==$_verses[$key] ){
                $update['name']=$name;
            }
            if( isset($relates[$key]) ){
                if( $relates[$key]!==$_relates[$key] ){
                    $update['relate']=$relates[$key];
                }
            }
            $update['updated']=$data['updated'];
            Model::update( $update, 'id', 'cuv' );
        }
        
        return Model::update($data, 'id', self::$useTable);
    }
    function updateAllHTML(){
        $sql ="SELECT * FROM ".self::$useTable." WHERE 1<>2";
        
        $rows = Model::fetchAll( $sql );
        
        foreach( $rows as $data ){
            for($i=0;$i<19;$i++){ unset($data[$i]); }
            self::edit($data);
        }
        
        return true;
    }
/*    function delete( $data ){
        if( isset($data['id']) ){
            $fields=array();
            $fields['id']=$data['id'];
            $fields['deleted']='1';
            return Model::update($fields, 'id', self::$useTable);
        }
        //when $data is id list
        $id=$data['ids'];
        $id_list=array();
        foreach( $id as $r ){
            $id_list[]=Model::quote($r, 'text');
        }
        $sql="UPDATE ".self::$useTable." SET deleted='1' WHERE id IN (".implode(',', $id_list).')';
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
    }*/
}
?>