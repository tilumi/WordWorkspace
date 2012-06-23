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
    array('name'=>'管理首頁', 'link'=>'/', 'menu_id'=>0, 'id'=>'main.index' ), 
    array('name'=>'新聞中心', 'link'=>'/news/', 'menu_id'=>1, 'id'=>'news' ),
/*    array('name'=>'相簿管理', 'link'=>'/albums/', 'id'=>'albums.index'),
    array('name'=>'相片管理', 'link'=>'', 'id'=>'albums.index', 'hidden'=>true),
    array('name'=>'系統紀錄', 'link'=>'/syslog/', 'id'=>'syslog.index' ),
    array('name'=>'操作說明', 'link'=>'/doc/', 'id'=>'docs.index' ),
    */
    array('name'=>'聖經維護', 'link'=>'/bible/', 'menu_id'=>2, 'id'=>'bible',
        'submenu'=>array(
            array('name'=>'書卷管理', 'link'=>'/bible/books/', 'id'=>'bible.books' ),
            array('name'=>'經文管理', 'link'=>'/bible/verses/', 'id'=>'bible.verses' ),
        )
    ),
    array('name'=>'話語資料', 'link'=>'/words/', 'menu_id'=>3, 'id'=>'words',
        'submenu'=>array(
            array('name'=>'分類', 'link'=>'/words/catalogs/', 'id'=>'words.catalogs' ),
            array('name'=>'證道者', 'link'=>'/words/speakers/', 'id'=>'words.speakers' ),
        )
    ),
    array('name'=>'禮拜資料', 'link'=>'/subjects/', 'menu_id'=>4, 'id'=>'subjects',
        'submenu'=>array(
            array('name'=>'年度標語', 'link'=>'/subjects/yeartopics/', 'id'=>'subjects.yeartopics.index' ),
        )
    ),
    array('name'=>'讚美歌曲', 'link'=>'/songs/', 'menu_id'=>5, 'id'=>'songs',
        'submenu'=>array(
            array('name'=>'分類', 'link'=>'/songs/categories/', 'id'=>'songs.categories' ),
            array('name'=>'編號系統', 'link'=>'/songs/sns/', 'id'=>'songs.sns' ),
            array('name'=>'歌本收錄', 'link'=>'/songs/songbooks/', 'id'=>'songs.songbooks' ),
        )
    ),
    //array('name'=>'分類管理', 'link'=>array('plugin'=>'subjects', 'controller'=>'yeartopics') ),
);
function parseMenuItem($item, $markup=false, $submenu_key=null){
    $tmp="";
    $hidden=false;
    if( is_array($item) ){
        if( isset($item['hidden']) && $item['hidden']===true ){
            $hidden=true;
        }
        if( $hidden ){ continue; }
        
        $attrs=array();
        if( is_numeric($submenu_key) ){
            $attrs=array('onmouseover'=>"javascript: submenu.show($submenu_key);");
        }
        if( $markup ){ $menu_class.='current'; }
        
        $tmp .= '<li id="'.$menu_class.'">';
        $tmp .= View::anchor( $item['link'], $item['name'], $attrs );
        $tmp .= "</li>\n";
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
    //$app_id = APP::$prefix.'.'.APP::$app;
    //$full_id = APP::parseFullID($item['id']);
    //$item_app_id = substr($full_id, 0, strlen(strrchr($full_id, "."))*(-1));
    
    $menu_id='';
    if( isset(APP::$appBuffer['menu_id']) ){
        $menu_id = APP::$appBuffer['menu_id'];
    }
    
    $menu_active=false;
    if( $menu_id === $item['menu_id'] ){ $menu_active=true; }
    
    $mainmenu_tmp.=parseMenuItem($item, $menu_active, $i);
    
    $submenu_id = "submenu-".$i;
    $subtmp='';
    if( isset($item['submenu']) && ! empty($item['submenu']) ){
        $subtmp="";
        foreach($item['submenu'] as $subitem){
            //依照權限設定顯示: 子選單部分
            if( ! ACL::checkAuth($item['id']) ){
                continue;
            }
            //開始產生子選單
            $subtmp.=parseMenuItem($subitem, $subactive);
        }
    }
    $submenu_class="submenu";
    if( $menu_active ){
        $submenu_class.=" current";
    }
    $submenu_tmp.='<ul id="'.$submenu_id.'" class="'.$submenu_class.'" style="float:left;left:-1000px;">'."\n";
    $submenu_tmp.=$subtmp;
    $submenu_tmp.="</ul>"."\n";
    
    $i=$i+1;
}
$mainmenu_for_layout=$mainmenu_tmp;
$submenu_for_layout=$submenu_tmp;

?>
<!DOCTYPE HTML>
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
                        <a href="<?php echo url( '/' );?>">管理首頁</a>
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
