<?php
$copyright="Website Administrator © 2012 MiKiDo Group. Powered by bride2 Framework.";

$SESSION = &$_SESSION['admin'];

/* 設定上方工具列 */
$topmenu=array(
    array('name'=>'Goto Website', 'link'=>'/' ),
    array('name'=>'Administrators', 'link'=>array('plugin'=>'managers') ),
    array('name'=>'Change Password', 'link'=>array('plugin'=>'main','action'=>'changepwd') ),
);

/* 設定主工具列 */
$mainmenu=array(
    array('name'=>'主控面板', 'link'=>'/', 'id'=>'main.index' ), 
    array('name'=>'新聞中心', 'link'=>'/news/', 'id'=>'news.index' ),
    array('name'=>'系統紀錄', 'link'=>'/syslog/', 'id'=>'syslog.index' ),
    array('name'=>'操作說明', 'link'=>'/doc/', 'id'=>'docs.index' ),
    /*
    array('name'=>'文章管理', 'link'=>array('plugin'=>'articles', 'controller'=>'main'),
        'submenu'=>array(
            array('name'=>'關鍵字', 'link'=>array('plugin'=>'articles', 'controller'=>'keywords') ),
            array('name'=>'類型', 'link'=>array('plugin'=>'articles', 'controller'=>'categories') ),
        )
    ),
    */
    //array('name'=>'分類管理', 'link'=>array('plugin'=>'subjects', 'controller'=>'yeartopics') ),
);
function parseMenuItem($item, $markup=false, $submenu_key=null){
    $tmp="";
    $hidden=false;
    if( is_array($item) ){
        if( $item['hidden']===true ){
            $hidden=true;
        }
        if( isset($item['id']) && !empty($item['id']) ){
            $attrs=array();
            if( is_numeric($submenu_key) ){
                $attrs=array('onmouseover'=>"javascript: submenu.show($submenu_key);");
            }
            $current='';
            if( $markup ){ $current=' id="current"'; }
            
            if( ! $hidden ){
                $tmp .= '<li'.$current.'>';
                $tmp .= View::anchor( $item['link'], $item['name'], $attrs );
                $tmp .= "</li>\n";
            }
        }else{
            if( $markup ){
                $tmp.='<li id="current">';
                $tmp.=$item['name'];
                $tmp.="</li>\n";
            }else{
                $tmp.=$item['name']."\n";
            }
        }
    }
    
    return $tmp;
}


/* 產生 Top Menu，存入$topmenu_for_layout */
$tmp="";
foreach($topmenu as $item){
    $tmp.=parseMenuItem($item)."\n";
}
$topmenu_for_layout=$tmp;

/* 產生 mainmenu 和 submenu，存入$mainmenu_for_layout, $submenu_for_layout */
$tmp="";$_="";
$i=1;$list=array();
foreach($mainmenu as $item){
    //依照權限設定顯示
    if( ! method_exists('ACL','checkAuth') ){
        continue;
    }
    if( ! ACL::checkAuth($item['id']) ){
        continue;
    }
    //開始產生MENU
    $active=false;
    $app_id = APP::$prefix.'.'.APP::$app;
    $full_id = APP::parseFullID($item['id']);
    $item_app_id = substr($full_id, 0, strlen(strrchr($full_id, "."))*(-1));
    if( isset($item['submenu']) && !empty($item['submenu']) ){
        $subtmp="";
        $first=true; //控制分隔線的顯示
        foreach($item['submenu'] as $subitem){
            //依照權限設定顯示: 子選單部分
            if( ! ACL::checkAuth($item['id']) ){
                continue;
            }
            //開始產生子選單
            $subactive=false;
            if( $app_id === $item_app_id ){
                $active=true;$subactive=true;
            }
            if( !$first ){
                //$subtmp.='<span>|</span>'."\n";
            }
            if( $first ){ $first=!$first; }
            $subtmp.=parseMenuItem($subitem, $subactive);
        }
        $_.='<ul id="submenu-'.$i.'" style="float:left">'."\n";
        $_.=$subtmp;
        $_.="</ul>"."\n";
        //$_.=($active)?'<script>$(document).ready(function(){ submenu.show('.$i.') });</script>':'';
    }else{
        $_.='<ul id="submenu-'.$i.'" style="float:left">'."\n";
        $_.="</ul>"."\n";
    }
    if( $app_id === $item_app_id ){
        $active=true;
    }
    $tmp.=parseMenuItem($item, $active, $i);
    $i=$i+1;
}
$mainmenu_for_layout=$tmp;
$submenu_for_layout=$_;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>

<?php View::include_http_metas(); ?>
<?php View::include_title(); ?>
<?php View::include_stylesheets(); ?>
<?php View::include_javascripts(); ?>
<?php View::include_extra_headers(); ?>

<!-- IE Hacks for the Fluid 960 Grid System -->
<!--[if IE 6]><link rel="stylesheet" type="text/css" href="<?php echo layout_url('admin', '/css/ie6.css');?>" media="screen" /><![endif]-->
<!--[if IE 7]><link rel="stylesheet" type="text/css" href="<?php echo layout_url('admin', '/css/ie.css');?>" media="screen" /><![endif]-->

<script charset="utf-8" id="injection_graph_func" src="<?php echo layout_url('admin', '/js/injection_graph_func.js');?>"></script>
<script id="_nameHighlight_injection"></script>
<link class="skype_name_highlight_style" href="<?php echo layout_url('admin', '/css/injection_nh_graph.css');?>" type="text/css" rel="stylesheet" charset="utf-8" id="_injection_graph_nh_css">
<link href="<?php echo layout_url('admin', '/css/skypeplugin_dropdownmenu.css');?>" type="text/css" rel="stylesheet" charset="utf-8" id="_skypeplugin_dropdownmenu_css">

<!-- IE6 hover & png fix -->
<style>
body{behavior:url(<?php echo layout_url('admin', '/js/ie6hover.htc');?>);}
</style>
<style type="text/css"> 
img, div, input  { behavior: url(<?php echo layout_url('admin', '/js/ie6/iepngfix.htc');?>); } 
</style>
<script language="javascript" type="text/javascript" src="<?php echo layout_url('admin', '/js/ie6/iepngfix_tilebg.js');?>"></script>

    </head>
    <body>
    	<!-- Header -->
        <div id="header">
            <!-- Header. Status part -->
            <?php $has_message = false; ?>
            <div id="header-status">
                <div class="container_12">
                    <div class="grid_6">
<?php if( method_exists('ACL','checkLogin') && ACL::checkLogin() ){ ?>
                        <span id="text-invitation">親愛的 <?php echo $SESSION['username'];?>, 歡迎回來<?php if( $has_message ){ ?>, 你有<?php } ?></span>
                        <?php if( $has_message ){ ?>
                        <!-- Messages displayed through the thickbox -->
                        <a href="<?php echo WEBLAYOUT.LAYOUT; ?>/messages.html" title="Inbox" class="thickbox" id="message-notification"><span>37</span> 則未讀訊息</a>
                        <?php } ?>
<?php }else{ echo '<span id="text-invitation">'.$copyright.'</span>'; } ?>
                    </div>
                    <div class="grid_6 topmenu">
<?php if( method_exists('ACL','checkLogin') && ACL::checkLogin() ){ ?>
                        <a href="<?php echo url( '/logout.html' );?>" id="logout">登出</a>
                        <a href="<?php echo url( '/passwd.html' );?>">變更密碼</a>
<?php       if( method_exists('ACL','checkAuth') ){ if( ACL::checkAuth( 'managers.index' ) ){ ?>
                        <a href="<?php echo url( '/managers/' );?>">系統管理員</a>
<?php       } } ?>
                        <a href="<?php echo url( '_/' );?>" target="_blank">網站前台</a>
                        <a href="<?php echo url( '/' );?>">主控面版</a>
<?php } ?>
                    </div>
                </div>
                <div style="clear: both;"></div>
            </div> <!-- End #header-status -->


            <!-- Header. Main part -->
            <div id="header-main"><div id='test'></div>
                <div class="container_12">
                    <div class="grid_12">
                        <div id="logo">
                            <ul id="nav">
                                <?php echo $mainmenu_for_layout; ?>
                            </ul>
                        </div><!-- End. #Logo -->
                    </div><!-- End. .grid_12-->
                    <div style="clear: both;"></div>
                </div><!-- End. .container_12 -->
            </div> <!-- End #header-main -->
            <div style="clear: both;"></div>
            <!-- Sub navigation -->
            <div id="subnav">
                <div class="container_12">
                    <div class="grid_12">
                        <?php echo $submenu_for_layout; ?>
                        
                    </div><!-- End. .grid_12-->
                </div><!-- End. .container_12 -->
                <div style="clear: both;"></div>
            </div> <!-- End #subnav -->
        </div> <!-- End #header -->
        
		<div class="container_12">
