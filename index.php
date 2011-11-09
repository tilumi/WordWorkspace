<?php
require('lib/marktime.php');

marktime('Core', 'Start');
marktime('SystemUser', 'Start');

class Routing{
    static $appRegister=array(
            'javascript',
        );
}
//phpinfo();
/*******************************************************************\
*** Routing     ****************************************************
\*******************************************************************/

//過濾不安全的url輸入
//$path=$_GET['p'];
//$path=filter_var( $path, FILTER_SANITIZE_URL); //for php version > 5.2
//$p=$path;

//直接從系統環境取得REDIRECT_URL
$base=dirname( getenv('SCRIPT_NAME') );
$p=filter_var( getenv('REQUEST_URI'), FILTER_SANITIZE_URL);
$p=urldecode( $p );
$p=substr( $p, strlen($base) );
if( substr($p,0,1)=='/' ) $p=substr($p,1);

$routing_args=routing( $p );

$app = $routing_args['app'].'.php';
if( $routing_args['prefix']!='main' ){
    $app = $routing_args['prefix'].'#'.$app;
}

if( ! file_exists($app) ){
    require('error/404.php');
    die;
}

marktime('Core', 'Routing');

/*******************************************************************\
*** 執行程式     ****************************************************
\*******************************************************************/

//呼叫初始化程式
require( 'app.php' );


marktime('Core', 'App Executed');

/*******************************************************************\
*** 垃圾回收     ****************************************************
\*******************************************************************/

marktime( 'Core' , 'Garbage Collection');
marktime( 'SystemUser', 'User');

if( DEBUG==0 ) exit;
if( APP::$systemConfigs['Debug']==0 ) exit; //提供APP於執行期決定是否關閉訊息

echo '<b>Cookies:</b>';
pr($_COOKIE);
echo '<b>PageBeforeLogin:</b>';
pr($_SESSION['PageBeforeLogin']);

//pr(Dispatch::$params);
//pr($pageConfig);
markquery_report();
marktime_report();

/*******************************************************************\
*** 路由函數     ****************************************************
\*******************************************************************/

function routing( $p ){
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
    if( pos($nodes) == 'administrator' ){
        $prefix='admin';
        array_shift($nodes);
    }
    
    //排除prefix之後，只判斷第一層級，如有註冊，就指定為app
    //其他自動保留為參數
    $app='main';
    $arg1 = pos($nodes);
    if( in_array( $arg1 , Routing::$appRegister ) ){
        $app = array_shift($nodes);
    }
    
    return array(
        'prefix'=>$prefix,
        'app'=>$app,
        'params'=>$nodes,
        'doctype'=>$ext
    );
    
}


?>