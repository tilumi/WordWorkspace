<?php
require('lib/marktime.php');
require('lib/routing.php');

marktime('Core', 'Start');
marktime('SystemUser', 'Start');

//phpinfo();
/*******************************************************************\
*** Routing     ****************************************************
\*******************************************************************/

//過濾不安全的url輸入
//$path=$_GET['p'];
//$path=filter_var( $path, FILTER_SANITIZE_URL); //for php version > 5.2
//$p=$path;

//清除$_GET全域陣列中的 p （rewrite所引入的路徑資料）
unset($_GET['p']);
//直接從系統環境取得REDIRECT_URL
$base=dirname( getenv('SCRIPT_NAME') );
$p=filter_var( getenv('REQUEST_URI'), FILTER_SANITIZE_URL);
$p=urldecode( $p );
$p=substr( $p, strlen($base) );
if( substr($p,0,1)=='/' ) $p=substr($p,1);

$routing_args=Routing::parse( $p );

/*echo '<pre>';
print_r($routing_args);
echo '</pre>';
echo '<pre>';
print_r($_GET);
echo '</pre>';
die;*/

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



?>