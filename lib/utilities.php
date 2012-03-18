<?php
function pr($var){
    if( PRODUCTION==1 ) return ;
    
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}
function vd($var){
    if( PRODUCTION==1 ) return ;
    
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}
function ve($var){
    if( PRODUCTION==1 ) return ;
    
    echo '<pre>';
    var_export($var);
    echo '</pre>';
}
function mkdirs($dir, $mode = 0777){
    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE; 
    if (!mkdirs(dirname($dir), $mode)) return FALSE;
    return @mkdir($dir, $mode);
}
function rmdirs($dir){
	$handle = @opendir($dir);
	while (false!==($item = @readdir($handle))){
		if($item != '.' && $item != '..'){
			if(is_dir($dir.'/'.$item)) {
			    rmdirs($dir.'/'.$item);
            }else{
                @unlink($dir.'/'.$item);
            }
        }
    }
    @closedir($handle);
    if(@rmdir($dir)){
        $success = true;
    }
    return $success;
}
function str_match( $substr , $string ){
    if( strpos( $string , $substr )===false ){
        return false;
    }
    return true;
}
function uc2ul( $str ){
    return ucwords2underline( $str );
}
function ul2uc( $str ){
    return underline2ucwords( $str );
}
function ucwords2underline( $str ){
    $str=trim($str);
    $str=preg_replace('/([A-Z]{1})/', '_$1', $str);
    $str=preg_replace('/^\_/', '', $str);
    $str=strtolower($str);
    return $str;
}
function underline2ucwords( $str ){
    $str=trim($str);
    $str=str_replace(' ', '', $str);
    $strs=explode('_', $str);
    $res='';
    foreach( $strs as $s ){
        $res.=ucfirst($s);
    }
    return $res;
}
function errmsg( $errmsg='' ){
    if( PRODUCTION==1 ) return ;
    
    $backtrace=debug_backtrace();
    //pr($backtrace);
    $loc=$backtrace[0];
    $err=$backtrace[1];
    echo '<META CONTENT="text/html; charset=utf-8" HTTP-EQUIV="Content-Type">';
    $msg ='<p style="font-size:14px;color:black;font-weight:normal;"><b>'.$loc['file'].' Line '.$loc['line'].'</b></p>';
    $msg.='<p style="font-size:12px;color:red;font-weight:normal;"><b>'.$err['function'].'() Error';
    if( !empty($err['class']) ){
        $msg.='<p style="font-size:12px;color:red;font-weight:normal;"><b>'.$err['class'].'::'.$err['function'].'() Error';
    }
    $msg.='<p style="font-size:12px;color:black;font-weight:normal;"><b>Error Message:</b> '.$errmsg.'</p>';
    //pr($backtrace);
    $msg.=debugBacktrace();
    
    die($msg);
}
function debugBacktrace(){
    $backtrace=debug_backtrace();
    array_shift($backtrace);
    array_shift($backtrace);
    
    $msg='';
    $msg.='<table style="font-size:13px;">';
    $msg.='<tr style="background:#ccf;">';
    $msg.='<th>Position</th>';
    $msg.='<th>Function</th>';
    $msg.='</tr>';
    foreach( $backtrace as $b ){
        $msg.='<tr>';
        if( isset($b['file']) ){
            $msg.='<td style="font-weight:bold;">'.str_replace(DIRROOT, '', $b['file']).' Line: '.$b['line'].'</td>';
        }else{
            $msg.='<td></td>';
        }
        $msg.='<td>';
        $msg.=$b['class'].$b['type'].'<b>'.$b['function'].'</b>';
        $args=array();
        foreach( $b['args'] as $arg ){
            if( is_string($arg) ){ $args[]="'".$arg."'"; continue; }
            if( is_array($arg) ){ $args[]="Array[".count($arg)."]"; continue; }
            if( is_object($arg) ){ $args[]="Object"; continue; }
            if( is_numeric($arg) ){ $args[]=$arg; continue; }
        }
        $msg.='( '.implode(', ', $args).' )';
        $msg.='</td>';
        $msg.='</tr>';
    }
    $msg.="</table>";
    
    return $msg;
}

function errorHandler($errno, $errstr, $errfile, $errline){
    if ( ! (error_reporting() & $errno) ){
        //pr(debug_backtrace());
        return;
    }
    
    switch ($errno) {
    case E_USER_ERROR:
        echo "<p><b>ERROR</b> [$errno] $errstr</p>\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...<br />\n";
        exit(1);
        break;

    case E_USER_WARNING:
        echo "<p><b>WARNING</b> [$errno] $errstr</p>\n";
        break;

    case E_USER_NOTICE:
        echo "<p><b>NOTICE</b> [$errno] $errstr</p>\n";
        break;

    default:
        echo "<p><b>Unknown Error Type</b>: [$errno] $errstr</p>\n";
        break;
    }
    echo "<p><b>".$errfile.", on line ".$errline."</b></p>";
    echo debugBacktrace();
    /* Don't execute PHP internal error handler */
    return true;
}

/*****  Extended Functions  *****/

function arrayMerge($a, $b){
    /**
    * Merges two arrays
    *
    * Merges two array like the PHP function array_merge but recursively.
    * The main difference is that existing keys will not be renumbered
    * if they are integers.
    */
    foreach ($b as $k => $v) {
        if (is_array($v)) {
            if (isset($a[$k]) && !is_array($a[$k])) {
                $a[$k] = $v;
            } else {
                if (!isset($a[$k])) {
                    $a[$k] = array();
                }
                $a[$k] = arrayMerge($a[$k], $v);
            }
        } else {
            $a[$k] = $v;
        }
    }
    return $a;
} // end func arrayMerge
function recursiveFilter($filter, $value){
    /**
     * Recursively apply a filter function
     */
    if (is_array($value)) {
        $cleanValues = array();
        foreach ($value as $k => $v) {
            $cleanValues[$k] = recursiveFilter($filter, $v);
        }
        return $cleanValues;
    } else {
        return $filter($value);
    }
} // end func _recursiveFilter
?>