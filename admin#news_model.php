<?php
class News{
    static $useTable='news';
    
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
        $sql.=" AND deleted='0'";
        $sql.=" ORDER BY published DESC";
        $totalItems = Model::fetchOne( str_replace('SELECT *','SELECT COUNT(*)', $sql) );
        $sql.=" LIMIT ".Model::getOffsetStart( $pageID, $pageRows ).", ".$pageRows;
        $rows = Model::fetchAll( $sql );
        
        return array($rows, $totalItems);
    }
    function add( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
       	$data['id']=uniqid('News');
       	$data['urn']=$data['name'];
        $date=$data['published'];
        $timestamp=mktime( $date['H'],$date['i'],0,$date['m'],$date['d'],$date['Y'] );
    	$data['published']=date('Y-m-d H:i:s', $timestamp);
       	
       	$data['author']=$_SESSION['admin']['userid'];
       	$data['author_id']=$_SESSION['admin']['id'];
       	$data['created']=date('Y-m-d H:i:s');
        
        return Model::insert($data, self::$useTable);
    }
    function edit( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
        $date=$data['published'];
        $timestamp=mktime( $date['H'],$date['i'],0,$date['m'],$date['d'],$date['Y'] );
    	$data['published']=date('Y-m-d H:i:s', $timestamp);
       	$data['updated']=date('Y-m-d H:i:s');
        
        return Model::update($data, 'id', self::$useTable);
    }
}
?>