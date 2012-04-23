<?php
//Regions default layout config

APP::$layoutsConfigs = array(
    'default' => array(
        'http_metas' => array(
            'content-type'      => 'text/html; charset=utf-8',
            'content-language'  => 'zh-TW'
        )
    ),
    'main' => array(
        'sitename' => '主的愛&線上聖經',
        'metas' => array(
            'author'        => '',
            'description'   => '因主的愛，我們努力提供您最佳閱讀聖經環境，願主耶穌基督的愛與真理充滿全地，哈雷路亞',
            'keywords'      => '聖經, bible, 線上聖經',
            'robots'        => 'index, follow, noimageindex',
        ),
        'stylesheets' => array(
            //'default.css','colorbox.css'
        ),
        'javascripts' => array(
            //'jquery.min.js','jcarousel.lite.js','jquery.mousewheel.js','jquery.colorbox-min.js'
        ),
    ),
    'admin' => array(
        'sitename' => '網站管理系統',
        'metas' => array(
            'author'        => '',
            'description'   => '',
            'keywords'      => '',
            'robots'        => 'noindex, nofollow',
        ),
        'stylesheets' => array(
            'style-merge.css','colorbox.css'
            //'reset.css', 'grid.css', 'styles.css', 'jquery.css', 'tablesorter.css', 'thickbox.css', 'theme-blue.css'
        ),
        'javascripts' => array(
            'jquery-merge.js','jquery.colorbox-min.js','initialize.js','submenu.js','/vendors/ckeditor/ckeditor.js'
            //'jquery-1.js', 'jquery_003.js', 'jquery_004.js', 'jquery_002.js', 'jquery.js', 'initialize.js', 'submenu.js', '/vendors/ckeditor/ckeditor.js'
        ),
    ),
);
?>