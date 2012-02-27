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
                        <span id="text-invitation">親愛的 <?php echo $_SESSION['administrator']['username'];?>, 歡迎回來<?php if( $has_message ){ ?>, 你有<?php } ?></span>
                        <?php if( $has_message ){ ?>
                        <!-- Messages displayed through the thickbox -->
                        <a href="<?php echo WEBLAYOUT.LAYOUT; ?>/messages.html" title="Inbox" class="thickbox" id="message-notification"><span>37</span> 則未讀訊息</a>
                        <?php } ?>
                    </div>
                    <div class="grid_6 topmenu">
                        <a href="<?php echo url( array('plugin'=>'main','action'=>'logout') );?>" id="logout">登出</a>
                        <a href="<?php echo url( array('plugin'=>'main','action'=>'change_password') );?>">變更密碼</a>
<?php if( method_exists('Region','checkAuth') ){ if( Region::checkAuth( array('plugin'=>'managers') ) ){ ?>
                        <a href="<?php echo url( array('plugin'=>'managers') );?>">系統管理員</a>
<?php } } ?>
                        <a href="<?php echo url( array('plugin'=>'main') );?>">主控面版</a>
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
