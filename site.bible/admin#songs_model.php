<?php
class Songs{
    static $useTable='songs';
    
    function pagelist( $submits, $pageID, $pageRows=PAGEROWS ){
        $sql ="SELECT * FROM ".self::$useTable." WHERE 1<>2";
        
        $key="name";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND ".$key." LIKE ".Model::quote( '%'.$submits[$key].'%' , 'text');
        }
        $key="std_id";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND UPPER(".$key.") LIKE UPPER(".Model::quote( $submits[$key].'%' , 'text').")";
        }
        $key="mps_key";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND ".$key." LIKE ".Model::quote( $submits[$key].'%' , 'text');
        }
        $key="hanyu_key";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND ".$key." LIKE ".Model::quote( $submits[$key].'%' , 'text');
        }
        $key="play_key";
        if( ! empty( $submits[$key]) ){
            $sql.=" AND UPPER(".$key.") = UPPER(".Model::quote( $submits[$key] , 'text').")";
        }
        $sql.=" AND deleted='0'";
        $sql.=" ORDER BY std_id";
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
        
        $data['lyrics']=array();
        $sql = "SELECT y.*, l.name as lang_name FROM songs_langs l";
        $sql.=" LEFT JOIN songs_lyrics y ON y.lang_id=l.id AND y.song_id=".Model::quote($id, 'text');
        $sql.=" WHERE l.is_active='1'";
        $sql.=" ORDER BY l.sort";
        
        $rows = Model::fetchAll( $sql );
        foreach( $rows as $r ){
            $data['lyrics'][ $r['lang_id'] ]=$r;
        }
        
        return $data;
    }
    function getLangs(){
        $sql = "SELECT * FROM songs_langs";
        $sql.=" WHERE is_active='1'";
        $sql.=" ORDER BY sort";
        $rows = Model::fetchAll( $sql );
        foreach( $rows as $r ){
            $data[ $r['id'] ]=$r;
        }
        
        return $data;
    }
    function add( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
        pr($data);die;
       	$data['id']=uniqid('Songs');
       	
       	$articles=array();
       	if( isset($data['articles']) ){
            $articles=$data['articles'];
            unset($data['articles']);
        }
       	$articles=array();
       	if( isset($data['articles']) ){
            $articles=$data['articles'];
            unset($data['articles']);
        }
       	$lyrics_names=array();
       	if( isset($data['lyrics_names']) ){
            $articles=$data['lyrics_names'];
            unset($data['lyrics_names']);
        }
        
       	$data['created']=date('Y-m-d H:i:s');
       	
       	$langs = self::getLangs();
       	
        Model::exec('START TRANSACTION');
       	foreach( $langs as $lang_id=>$langdata ){
       	    $lyrics_name = ( isset($lyrics_names[ $lang_id ]) )?$lyrics_names[ $lang_id ]:'';
       	    $article = ( isset($articles[ $lang_id ]) )?$articles[ $lang_id ]:'';
            $data_2=array();
            $data_2['id']=uniqid('Lyric');
            $data_2['song_id']=$data['id'];
            $data_2['lang_id']=$lang_id;
            $data_2['std_id']=$data['std_id'];
            $data_2['name']=$lyrics_name;
            $data_2['article']=$article;
            
            if( ! Model::insert($data_2, 'songs_lyrics') ){
                Model::exec('ROLLBACK');
                return '寫入失敗，請再試一次。Error 1. @ '.$lang_id;
            }
        }
        
        if( ! Model::insert($data, self::$useTable) ){
            Model::exec('ROLLBACK');
            return '寫入失敗，請再試一次。Error 2.';
        }
        return Model::exec('COMMIT');
    }
    function edit( $data ){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
        
       	$articles=array();
       	if( isset($data['articles']) ){
            $articles=$data['articles'];
            unset($data['articles']);
        }
       	$lyrics_names=array();
       	if( isset($data['lyrics_names']) ){
            $lyrics_names=$data['lyrics_names'];
            unset($data['lyrics_names']);
        }
        
       	$data['updated']=date('Y-m-d H:i:s');
       	
       	$langs = self::getLangs();
       	$songdata = self::findById( $data['id'] );
       	
        Model::exec('START TRANSACTION');
       	foreach( $langs as $lang_id=>$langdata ){
       	    $lyrics_name = ( isset($lyrics_names[ $lang_id ]) )?$lyrics_names[ $lang_id ]:'';
       	    $article = ( isset($articles[ $lang_id ]) )?$articles[ $lang_id ]:'';
            $data_2=array();
            $data_2['song_id']=$data['id'];
            $data_2['lang_id']=$lang_id;
            $data_2['std_id']=$data['std_id'];
            $data_2['name']=$lyrics_name;
            $data_2['article']=$article;
            
            //如果這個語系檔存在，則(update)就好
            if( isset($songdata['lyrics'][ $lang_id ]) ){
                if( ! Model::update($data_2, array('song_id', 'lang_id'), 'songs_lyrics') ){
                    Model::exec('ROLLBACK');
                    return '寫入失敗，請再試一次。Error 1. @ '.$lang_id;
                }
            //若不存在，則需要寫入(insert)新語系檔
            }else{
                $data_2['id']=uniqid('Lyric');
                if( ! Model::insert($data_2, 'songs_lyrics') ){
                    Model::exec('ROLLBACK');
                    return '寫入失敗，請再試一次。Error 1-1. @ '.$lang_id;
                }
            }
            
        }
        
        if( ! Model::update($data, 'id', self::$useTable) ){
            Model::exec('ROLLBACK');
            return '寫入失敗，請再試一次。Error 2.';
        }
        return Model::exec('COMMIT');
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