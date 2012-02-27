<?php
include( dirname(dirname(__FILE__)).'/config/routing.php');
class Routing{
    function parse( $p ){
        if( empty($p) ){
            return array(
                'prefix'=>'main',
                'app'=>'main',
                'params'=>array(),
                'doctype'=>'html'
            );
        }
        //取得副檔名
        $p=trim($p);
        $ext = strtolower( substr( strrchr($p, ".") ,1 ) );
        if( empty($ext) ) $ext='html';
        $p = preg_replace( "/\.".$ext."$/", '', $p ); //移除副檔名
        
        //拆解路徑
        $nodes = explode('/', $p);
        
        //判別第一個節點，取得所屬的prefix
        $prefix='main';
        $current=pos($nodes);
        $prefixMap = RoutingConfigs::$prefixs;
        if( ! empty($current) && array_key_exists( $current , $prefixMap ) ){
            $prefix=$prefixMap[ $current ]['name'];
            array_shift($nodes);
        }elseif( array_key_exists( '__default__' , $prefixMap ) ){
            $prefix=$prefixMap['__default__']['name'];
        }
        
        //排除prefix之後，只判斷第一層級，如有註冊，就指定為app
        //其他自動保留為參數
        $app='main';
        $arg_1 = pos($nodes);
        $p = implode('/', $nodes).'.'.$ext;
        
        //若prefix未在apps中設定，則視為找不到
        if( ! isset(RoutingConfigs::$apps[ $prefix ]) ){
            return array('error'=>'404');
        }
        
        $routingTable = RoutingConfigs::$apps[ $prefix ];
        if( isset($routingTable[ $arg_1 ]) ){
            $app = $routingTable[ $arg_1 ]['name'];
            array_shift($nodes);
        }elseif( isset($routingTable['__default__']) ){
            $app = $routingTable['__default__']['name'];
        }
        if( !empty($arg1) && is_array( RoutingConfigs::$apps[$prefix] ) && in_array( $arg1 , RoutingConfigs::$apps[$prefix] ) ){
            $app = array_shift($nodes);
        }
        
        return array(
            'prefix'=>$prefix,
            'app'=>$app,
            'params'=>$nodes,
            'doctype'=>$ext
        );
        
    }
}

?>