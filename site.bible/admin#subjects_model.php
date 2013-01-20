<?php
class Subjects{
    static $useTable='subjects';
    static $bibleCache=array();
    
    function pagelist( $submits, $pageID, $pageRows=PAGEROWS ){
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
        $sql.=" ORDER BY CAST(year AS UNSIGNED) DESC, CAST(week AS UNSIGNED) DESC, wday";
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
        
        //$data['verses']=json_decode($data['verses']);
        //$data['anthems']=json_decode($data['anthems']);
        
        return $data;
    }
    function findByYears( $id ){
        if( is_string($id) ){
            $id = array($id);
        }
        $id_list=array();
        foreach( $id as $r ){
            $id_list[]=Model::quote($r, 'text');
        }
        $sql ="SELECT * FROM ".self::$useTable;
        $sql.=" WHERE year IN (".implode(',', $id_list).") AND deleted='0'";
        $sql.=" ORDER BY CAST(year AS UNSIGNED), CAST(week AS UNSIGNED), wday";
        $data = Model::fetchAll( $sql );
        
        APP::load('model', 'weekly');
        
        $subjects=array();
        foreach( $data as $r ){
            if( ! isset($subjects[ $r['year'].'-'.$r['week'] ]['LordDay']) ){
                $subjects[ $r['year'].'-'.$r['week'] ]['LordDay']=array();
            }
            if( ! isset($subjects[ $r['year'].'-'.$r['week'] ]['others']) ){
                $subjects[ $r['year'].'-'.$r['week'] ]['others']=array();
            }
            if( $r['wtype_id']==='LordDay' ){
                $subjects[ $r['year'].'-'.$r['week'] ]['LordDay']=$r;
            }else{
                $subjects[ $r['year'].'-'.$r['week'] ]['others'][]=$r;
            }
        }
        
        return $subjects;
    }
    function add( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
        $data['id']=uniqid('Subject');
        $ts=mktime(0,0,0,$data['worshiped']['month'],$data['worshiped']['day'],$data['worshiped']['year']);
        $data['year']=$data['worshiped']['year'];
        $data['week']=Weekly::getWeek($ts);
        $data['wday']=date('w', $ts);
        $data['worshiped']=date('Y-m-d', $ts );
        if( $data['wtype_id']!='other'){
            $wtype=SubjectsWtypes::findById($data['wtype_id']);
            $data['wtype_name']=$wtype['name'];
        }
        if( $data['wtype_id']=='other' && !empty($data['wtype_name']) ){
            $data['wtype_id']=SubjectsWtypes::quickAdd($data['wtype_name']);
        }
        $data['name']=$data['name_zh'];
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
    function parseVerseKey( $key ){
        list($p1, $p2) = explode('#', $key);
        
        if( ! isset(self::$bibleCache['full']) ){
            require('cache/bible_info.php');
            self::$bibleCache['full']=$bibleFull;
            self::$bibleCache['short']=$bibleShort;
            
            //儲存書卷和章節鍵，可查該章是否存在，如1-1表創世記1章
            self::$bibleCache['chapters']=$bibleChapter;
            
            //儲存書卷的最大章數
            self::$bibleCache['max']=$bibleMaxChapter;
        }
        
        list($type, $book_id) = explode(':', $p1);
        list($chapter_id, $verse_id) = explode(':', $p2);
        
        $book_id = (int)$book_id;
        $chapter_id = (int)$chapter_id;
        
        return array(
            'type' => $type,
            'book_id' => $book_id,
            'book_name' => self::$bibleCache['full'][$book_id],
            'book_short' => self::$bibleCache['short'][$book_id],
            'chapter_id' => $chapter_id,
            'verse_id' => $verse_id,
            'part1' => $p1,
            'part2' => $p2,
        );
    }
}
?>