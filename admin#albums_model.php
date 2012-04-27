<?php
class Albums{
    static $useTable='albums';
    static $upload_dir='/albums/';
    static $thumbs=array(
        '80x80'=>array('width'=>80,'height'=>80,'quality'=>70,'zoom_crop'=>1,'output_type'=>'jpg'),
        '120x90'=>array('width'=>120,'height'=>90,'quality'=>70,'zoom_crop'=>1,'output_type'=>'jpg'),
        '400'=>array('width'=>400,'height'=>400,'quality'=>70,'zoom_crop'=>2,'output_type'=>'jpg'),
        '800'=>array('width'=>800,'height'=>800,'quality'=>70,'zoom_crop'=>2,'output_type'=>'jpg'),
        '1200'=>array('width'=>1200,'height'=>1200,'quality'=>70,'zoom_crop'=>2,'output_type'=>'jpg'),
        '1600'=>array('width'=>1600,'height'=>1600,'quality'=>70,'zoom_crop'=>2,'output_type'=>'jpg'),
    );
    
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
        $sql.=" ORDER BY sort";
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
        $sql = "SELECT * FROM ".self::$useTable." WHERE id=".Model::quote($id, 'text')." AND deleted='0'";
        $data = Model::fetchRow( $sql );
        return $data;
    }
    function add( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
        APP::load('vendor', 'thumb');
        APP::load('pear', 'HTTP/Upload');
        
        $submits = $data;
        
        $data=array();
       	$data['id']=uniqid('Album');
       	$id=$data['id'];
       	$data['sort']=$submits['sort'];
       	$data['urn']=slug($submits['name']);
       	$data['name']=$submits['name'];
       	$data['is_active']=$submits['is_active'];
       	$data['info']=$submits['info'];
       	$data['creator']=$_SESSION['admin']['userid'];
       	$data['creator_id']=$_SESSION['admin']['id'];
       	$data['created']=date('Y-m-d H:i:s');
       	
       	$upload_dir = repos_path( self::$upload_dir );
       	$base_dir = $upload_dir.$id.'/cover/';
    	$thumbs=self::$thumbs;
    	
    	$upload=new HTTP_Upload();
    	$photo=$upload->getFiles('photo');
    	if($photo->isValid()){
    		if( !is_dir($base_dir) ){ mkdirs($base_dir); }
    		
    		$photo->setName('photo-origin.'.strtolower($photo->upload['ext']));
    		$file_name=$photo->moveTo($base_dir);
            
    		$src=$base_dir.'/'.$file_name;
    		foreach( $thumbs as $size=>$thumb ){
                $output_type=$thumb['output_type'];
            	$dst=$base_dir.'/photo-'.$size.'.'.$output_type;
                
            	$w=$thumb['width'];$h=$thumb['height'];
                $c=$thumb['zoom_crop'];$q=$thumb['quality'];
            	$v_name=thumb($src, $dst, $w, $h, $c, $q, $output_type);
            }
            $data['has_cover']='1';
    	}
        return Model::insert($data, self::$useTable);
    }
    function edit( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
        APP::load('vendor', 'thumb');
        APP::load('pear', 'HTTP/Upload');
        
        $submits = $data;
        
        $data=array();
       	$data['id']=$submits['id'];
       	$id=$data['id'];
       	$data['sort']=$submits['sort'];
       	$data['urn']=slug($submits['name']);
       	$data['name']=$submits['name'];
       	$data['is_active']=$submits['is_active'];
       	$data['info']=$submits['info'];
       	$data['created']=date('Y-m-d H:i:s');
       	
       	$upload_dir = repos_path( self::$upload_dir );
       	$base_dir = $upload_dir.$id.'/cover/';
    	$thumbs=self::$thumbs;
    	
    	$upload=new HTTP_Upload();
    	$photo=$upload->getFiles('photo');
    	if($photo->isValid()){
    		if( !is_dir($base_dir) ){ mkdirs($base_dir); }
    		
    		$photo->setName('photo-origin.'.strtolower($photo->upload['ext']));
    		$file_name=$photo->moveTo($base_dir);
            
    		$src=$base_dir.'/'.$file_name;
    		foreach( $thumbs as $size=>$thumb ){
                $output_type=$thumb['output_type'];
            	$dst=$base_dir.'/photo-'.$size.'.'.$output_type;
                
            	$w=$thumb['width'];$h=$thumb['height'];
                $c=$thumb['zoom_crop'];$q=$thumb['quality'];
            	$v_name=thumb($src, $dst, $w, $h, $c, $q, $output_type);
            }
            $data['has_cover']='1';
    	}
    	if( isset($submits['remove']) && $submits['remove']=='1' ){
            rmdirs($base_dir);
            $data['has_cover']='0';
        }
    	
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
    }
}
?>