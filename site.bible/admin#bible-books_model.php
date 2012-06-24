<?php
class BibleBooks{
    static $useTable='bible_books';
    
    function pagelist( $submits, $pageID, $pageRows=PAGEROWS ){
        $sql ="SELECT * FROM ".self::$useTable." WHERE 1<>2";
        
        $key="name";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND ".$key." LIKE ".Model::quote( '%'.$submits[$key].'%' , 'text');
        }
        $key="author";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND ".$key." LIKE ".Model::quote( '%'.$submits[$key].'%' , 'text');
        }
        $key="is_active";
        if( in_array( $submits[$key], array('0','1') ) ){
            $sql.=" AND ".$key." = ".Model::quote($submits[$key], 'text');
        }
        //$sql.=" AND deleted='0'";
        //$sql.=" ORDER BY published DESC";
        $totalItems = Model::fetchOne( str_replace('SELECT *','SELECT COUNT(*)', $sql) );
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
        $sql = "SELECT * FROM ".self::$useTable." WHERE id=".Model::quote($id, 'text');
        $data = Model::fetchRow( $sql );
        return $data;
    }
    function findByUrn( $urn ){
        if( is_array($urn) ){
            $urn_list=array();
            foreach( $urn as $r ){
                $urn_list[]=Model::quote($r, 'text');
            }
            $sql = "SELECT * FROM ".self::$useTable." WHERE urn IN (".implode(',', $urn_list).") AND deleted='0'";
            $data = Model::fetchAll( $sql );
            return $data;
        }
        $sql = "SELECT * FROM ".self::$useTable." WHERE urn=".Model::quote($urn, 'text');
        $data = Model::fetchRow( $sql );
        return $data;
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
        
        $data['urn']=slug($data['name_en']);
        
        $info=$data['info'];
        $info_str=preg_replace("/(\r|\n)+/",'<br>',$info);
        $info_arr=explode('<br>',$info_str);
        $info_tmp='';
        foreach($info_arr as $ia){
            $info_tmp.='<p>'.$ia.'</p>'."\n";
        }
        $info=$info_tmp;
        $data['info_html']=$info;

        $summary=$data['summary'];
        $summary_str=preg_replace("/(\r|\n)+/",'<br>',$summary);
        $summary_arr=explode('<br>',$summary_str);
        $summary_tmp='';
        foreach($summary_arr as $ia){
            $summary_tmp.='<li>'.$ia.'</li>'."\n";
        }
        $summary='<ul class="style1">'."\n".$summary_tmp.'</ul>'."\n";
        $data['summary_html']=$summary;

       	$data['updated']=date('Y-m-d H:i:s');
        
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