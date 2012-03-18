<?php
include( dirname(dirname(__FILE__)).'/config/routing.php');
class Routing{
    function parse( $p ){
        if( empty($p) ){
            return array(
                'prefix'=>'main',
                'prefixFull'=>'',
                'app'=>'main',
                'params'=>array('index'),
                'doctype'=>'html',
                'handler'=>'main',
            );
        }
        
        //過濾不必要的空白
        $p=trim($p);
        //去除GET string（路徑中"?"以後的字串）
        if( mb_strpos($p, '?')!==false ){
            //用拆解的方式，只取以"?"區隔的第一個元素
            $_ = explode('?', $p);
            $p=$_[0];
        }
        //若結尾是 "/" ，暗示使用預設 "index.html"，自動補上
        if( substr($p, -1)==='/' ){
            $p.="index.html";
        }
        //取得副檔名
        $ext = strtolower( substr( strrchr($p, ".") ,1 ) );
        if( empty($ext) ) $ext='html';
        $p = preg_replace( "/\.".$ext."$/", '', $p ); //移除副檔名
        
        //拆解路徑
        $nodes = explode('/', $p);
        
        //判別第一個節點，取得所屬的prefix
        $prefix='main';
        $prefixFull='main';
        $current=pos($nodes);
        $prefixMap = RoutingConfigs::$prefixs;
        if( ! empty($current) && array_key_exists( $current , $prefixMap ) ){
            $prefix=$prefixMap[ $current ]['name'];
            array_shift($nodes);
            
            //記錄路徑前綴的全名
            $prefixFull = $current;
            
        }elseif( array_key_exists( '__default__' , $prefixMap ) ){
            $prefix=$prefixMap['__default__']['name'];
            
            //記錄路徑前綴的全名
            $prefixFull = '';
        }
        
        //排除prefix之後，只判斷第一層級，如有註冊，就指定為app
        //其他自動保留為參數
        $arg_1 = pos($nodes);
        //若prefix未在apps中設定，則視為找不到
        if( ! isset(RoutingConfigs::$apps[ $prefix ]) ){
            return array('error'=>'404');
        }
        
        $p_app = $p;
        if( $prefix!=='main' ){
            $p_app = substr($p, strlen($prefixFull)+1 );
        }
        
        $app='main';
        $routingTable = RoutingConfigs::$apps[ $prefix ];
        $default = array('name'=>'main');
        if( isset($routingTable['__default__']) ){
            $default=$routingTable['__default__'];
        }
        
        $match=false;
        $app='';
        $app_path='';
        foreach( $routingTable as $path=>$config ){
            if( $path.'/' === substr($p_app, 0, strlen($path)+1) ){
                $match=true;
                $app = $config['name'];
                $app_path = $path;
                break;
            }
        }
        if( ! $match ){
            $app = $default['name'];
        }
        
        //移除屬於app的路徑
        $p_params = $p_app;
        if( $app!='main' )
            $p_params = substr($p_app, strlen($app_path)+1 );
        
        /*echo 'p: '.$p.'<br>';
        echo 'p_app: '.$p_app.'<br>';
        echo 'p_params: '.$p_params.'<br>';*/
        
        //更新屬於參數的路徑區域
        $nodes=explode('/', $p_params);
        
        $handler = $app;
        if( $prefix!='main' ){
            $handler = $prefix.'#'.$handler;
        }
        
        return array(
            'prefix'=>$prefix,
            'prefixFull'=>$prefixFull,
            'app'=>$app,
            'params'=>$nodes,
            'doctype'=>$ext,
            'handler'=>$handler,
        );
        
    }
}

?>