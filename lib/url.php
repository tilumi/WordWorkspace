<?php
function redirect( $href , $message='' , $message_template='' ){
    $url=url($href);
    if( !empty($message) ){
        if( !empty($message_template) ){
            $template = 'notice_'.$message_template;
            $msg = RenderRedirectMSG( $message , $template );
            $_SESSION['Redirect'][$url]=array(
                'timeout' => strtotime('+1 min'),
                'message' => $msg,
            );
        }else{
            $_SESSION['Redirect'][$url]=array(
                'timeout' => strtotime('+1 min'),
                'message' => $message,
            );
        }
    }
    //pr(headers_list());die;
    if( count($_POST)>0 ){
        $delay=1;
        $waitimg=WEBLAYOUT.'main/loading.gif';
        $redirectMiddlePage=<<<EOF
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Refresh" content='{$delay}; url={$url}'>
</head>
<body>
<div style="width:100%;height:55%;background:url({$waitimg}) center bottom no-repeat;"></div>
</body>
</html>
EOF;
        echo $redirectMiddlePage;
        exit;
    }else{
    	$headers=headers_list();
    	$cookies=array();
    	foreach( $headers as $h ){
            if( preg_match('/^Set\-Cookie/i', $h) ){
                $cookies[]=$h;
            }
        }
    	
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '. gmdate ('D, d M Y H:i:s') . 'GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false );
		header('Pragma: no-cache');
		header('Location: ' . $url );
		foreach( $cookies as $cookie ){
            header( $cookie );
        }
        exit;
    }
}
function anchor( $name, $href, $options=array() ){
    $url=url($href);
    if( is_string($options) ){
        $options=array($options);
    }
    $attrs=array();
    foreach( $options as $key=>$value ){
        if( is_int($key) ){
            $attrs[]=$value;
            continue;
        }
        $attrs[]=$key.'="'.$value.'"';
    }
    $html='';
    $html.='<a href="'.$url.'" '.implode(' ', $attrs).'>'.$name.'</a>';
    return $html;
}
function url( $href ){
    //如果傳入的參數是字串，則以字串URL方式處理
    if( is_string($href) ){
        return txturl($href);
    }
    if( ! is_array($href) ){
        return false;
    }
    
    return false;
}
function layout_url( $layout, $href ){
    //如果傳入的參數是字串，則以字串URL方式處理
    if( is_string($href) ){
        return txturl('/layout_'.$layout.$href);
    }
    if( ! is_array($href) ){
        return false;
    }
    
    return false;
}
function txturl( $href ){
    $href=trim($href);
    $href_abs=$href;
    //for empty path: imply ME
    if( empty($href) ){ return ''; }
    //for absolute location
    if( $href=='/' ){ return WEBROOT; }
    if( preg_match( '/^http/i', $href) ){ return $href; }
    if( preg_match( '/^'.str_replace('/','\/',WEBROOT).'/i', $href) ){ return $href; }
    //if '..', get current position, use explode('/', $href), determin '..' , then count abs path
    if( substr($href, 0, 1)=='/' ){
        return WEBROOT.substr($href, 1);
    }
    return WEBROOT.$href;
}
?>