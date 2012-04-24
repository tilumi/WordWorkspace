<?php
function redirect_message(){
    //檢查Redirect Message並取出
    $RedirectMSG='';
    
    $redirect_messages = &$_SESSION['Redirect'];
    $ME_alias=preg_replace('/index\.html$/','', APP::$ME );
    $RMSG='';
    if( isset($redirect_messages[ APP::$ME ]) ){ //取出
        $RMSG=$redirect_messages[ APP::$ME ];
        unset($redirect_messages[ APP::$ME ]);
    }
    if( APP::$ME!=$ME_alias && isset($redirect_messages[ $ME_alias ]) ){
        $RMSG=$redirect_messages[ $ME_alias ];
        unset($redirect_messages[ $ME_alias ]);
    }
    if( isset($RMSG['timeout']) && $RMSG['timeout'] < mktime() ){ //如果逾期就刪除
        unset($RMSG);
    }
    if( isset($RMSG) && is_array( $RMSG ) ){
        $RedirectMSG=$RMSG['message'];
    }
    return $RedirectMSG;
}
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
        $waitimg=layout_url('admin', 'loading.gif');
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
function get_parents_app( $app ){
    return RoutingConfigs::$parents[ $app ];
}
function get_app_path( $app ){
    if( isset( RoutingConfigs::$maps[ $app ] ) ){
        return RoutingConfigs::$maps[ $app ];
    }
    return '';
}
function url( $href ){
    //如果傳入的參數是字串，則以字串URL方式處理
    if( is_string($href) ){
        $status = 0; //記錄曾啟用過哪些特殊功能
        
        // "/" 時，表示為所在 prefix 下的絕對路徑
        if( substr($href, 0, 1)=='/' ){
            $href = substr($href, 1);
            $base = '';
            if( APP::$prefix !== 'main' ){
                $base = APP::$prefixFull.'/';
            }
            if( APP::$prefix === 'main' ){
                $base = '/';
            }
            $href = $base.$href;
            $status += 8;
        }
        // ".." 表示取得app 的母親app，若無則指 prefix 的根目錄，或是 main app
        if( $status==0 && substr($href, 0, 2)=='..' ){
            $href = preg_replace('/[\.]{2,}/', '..', $href);
            $base = '';
            $href = substr($href, 2);
            $app = APP::$app;
            $i=0;
            do{
                $parents_app=get_parents_app($app);
                if( empty( $parents_app ) ){ //空字串，表示沒有母親APP，指根目錄
                    if( APP::$prefix !== 'main' ){
                        $base .= APP::$prefixFull.'/';
                    }
                }else{
                    $base .= get_app_path($parents_app).'/';
                }
                
                $next_level=substr($href, 0, 3);
                if( $next_level==='/..' ){
                    $app=$parents_app;
                }else{
                    $parents_app='';
                }
                $i+=1;
                if( $i>=10 ){ break; }
            }while( ! empty($parents_app) );
            //如果 $base & $href 同時非空白，此時會多一個 "/" ，因此需要移除其中一個
            if( ! empty($base) && ! empty($href) ){
                $base = substr($base, 0, -1);
            }
            $href = $base.$href;
            $status += 1;
        }
        // "." 永遠表示 prefix 及 app 的根目錄
        if( $status==0 && substr($href, 0, 1)=='.' ){
            $base = '';
            $href = substr($href, 1);
            if( APP::$prefix != 'main' ){
                $base .= APP::$prefixFull.'/';
            }
            if( APP::$app != 'main' ){
                $base .= RoutingConfigs::$maps[ APP::$app ].'/';
            }
            //如果 $base & $href 同時非空白，此時會多一個 "/" ，因此需要移除其中一個
            if( ! empty($base) && ! empty($href) ){
                $base = substr($base, 0, -1);
            }
            $href = $base.$href;
            $status += 2;
        }
        // "_" 表示為系統內部的絕對路徑（一定要放在路徑開頭），則只需要補上WEBROOT即可
        if( $status==0 && substr($href, 0, 1)=='_' ){
            $href = substr($href, 1);
            $status += 4;
        }
        //如果不曾啟用過以上特殊功能，表示為相對路徑，自動補上 prefix 和 app
        if( $status == 0 ){
            $base = '';
            if( APP::$prefix != 'main' ){
                $base .= APP::$prefixFull.'/';
            }
            if( APP::$app != 'main' ){
                $base .= RoutingConfigs::$maps[ APP::$app ].'/';
            }
            $href = $base.$href;
        }
        
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
        if( substr($href, 0, 1)!='/' ){
            $href = '/'.$href;
        }
        return txturl('/layout_'.$layout.$href);
    }
    if( ! is_array($href) ){
        return false;
    }
    
    return false;
}
function repos_url( $href ){
    //如果傳入的參數是字串，則以字串URL方式處理
    if( is_string($href) ){
        if( substr($href, 0, 1)!='/' ){
            $href = '/'.$href;
        }
        return txturl('/cabinets'.$href);
    }
    if( ! is_array($href) ){
        return false;
    }
    
    return false;
}
function repos_path( $href ){
    //如果傳入的參數是字串，則以字串URL方式處理
    if( is_string($href) ){
        if( substr($href, 0, 1)!='/' ){
            $href = '/'.$href;
        }
        return './cabinets'.$href;
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
    //for 絕對路徑
    if( $href=='/' ){ return WEBROOT; }
    //開頭有http(不分大小寫)時，表示絕對路徑
    if( preg_match( '/^http/i', $href) ){ return $href; }
    
    
    if( substr($href, 0, 1)=='/' ){
        return WEBROOT.substr($href, 1);
    }
    return WEBROOT.$href;
}
?>