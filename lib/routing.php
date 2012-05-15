<?php
include( $my_base.'/config/routing.php');
class Routing{
    function parse( $p ){
        if( empty($p) ){
            return array(
                'prefix'=>'main',
                'prefixFull'=>'',
                'app'=>'main',
                'params'=>array('index'),
                'parents'=>array(),
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
        
        //建立路徑對應表
        $routingTable = RoutingConfigs::$apps[ $prefix ];
        foreach( $routingTable as $path=>$config ){
            RoutingConfigs::$maps[ $config['name'] ]=$path;
            RoutingConfigs::$r_maps[ $path ]=$config['name'];
            //設定各app的母親app
            RoutingConfigs::$parents[ $config['name'] ]='';
            if( isset($config['parents']) ){
                RoutingConfigs::$parents[ $config['name'] ]=$config['parents'];
            }
        }
        
        $p_app = $p;
        if( $prefix!=='main' ){
            $p_app = substr($p, strlen($prefixFull)+1 );
        }
        
        $app='main';
        $default = array('name'=>'main');
        if( isset($routingTable['__default__']) ){
            $default=$routingTable['__default__'];
        }
        
        //取出並比對Routing資料
        $match=false;
        $app='';
        $app_path='';
        $path_vars=array();
        foreach( $routingTable as $path=>$config ){
            //二級以上動態路由的判定
            if( strpos($path, '*') !== false ){
                $re_path='^'.preg_quote($path).'/';
                $re_path=str_replace('/', '\/', $re_path);
                $re_path=str_replace('\*', '([^\/]+?)', $re_path);
                $re_path='/'.$re_path.'/';
                //print($re_path).'<br>';
                if( preg_match_all( $re_path, $p_app, $matches ) ){
                    
                    $match=true;
                    $app = $config['name'];
                    $app_path = array_shift($matches);
                    $app_path = substr($app_path[0], 0, -1);
                    /*echo '<pre>';
                    print_r($matches).'</pre><br>';*/
                    //將比對出來的變數一一儲存下來
                    //動態路由內的變數，將依序插入APP::$params陣列的前方
                    foreach( $matches as $key=>$p_match ){
                        $path_vars[] = $p_match[0];
                    }
                    //更新自身和所有母親APP的路徑
                    $i=0;
                    $renew_app=$app;
                    do{
                        $renew_path=RoutingConfigs::$maps[ $renew_app ]; //原路徑（含星號的路徑）
                        $updated_path=vsprintf( str_replace('*', '%s', $renew_path), $path_vars ); //更新後的路徑
                        //更新正查app->path
                        RoutingConfigs::$maps[ $renew_app ]=$updated_path;
                        //更新反查path->app
                        RoutingConfigs::$r_maps[ $updated_path ]=$renew_app;
                        unset(RoutingConfigs::$r_maps[ $renew_path ]);
                        
                        //檢查是否有母APP
                        if( ! empty(RoutingConfigs::$parents[$renew_app]) ){
                            $renew_app=RoutingConfigs::$parents[$renew_app];
                        }else{
                            $renew_app='';
                        }
                        $i+=1;
                        if( $i>10 ){ break; }
                    }while( ! empty($renew_app) );
                    
                    break;
                }
                if( $match ) continue;
            }
            //一般路由
            if( $path.'/' === substr($p_app, 0, strlen($path)+1) ){
                $match=true;
                $app = $config['name'];
                $app_path = $path;
                break;
            }
        }
        /*echo '<pre>';
        print_r(RoutingConfigs::$maps).'</pre><br>';
        echo '<pre>';
        print_r(RoutingConfigs::$r_maps).'</pre><br>';
        echo '<pre>';
        print_r(RoutingConfigs::$parents).'</pre><br>';
        echo $i;*/
        
        //echo 'app_path: '.$app_path.'<br>';
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
        /*echo '<pre>';
        print_r($nodes).'</pre><br>';*/
        
        $handler = $app;
        if( $prefix!='main' ){
            $handler = $prefix.'#'.$handler;
        }
        
        return array(
            'prefix'=>$prefix,
            'prefixFull'=>$prefixFull,
            'app'=>$app,
            'params'=>$nodes,
            'parents'=>$path_vars,
            'doctype'=>$ext,
            'handler'=>$handler,
        );
        
    }
}

?>