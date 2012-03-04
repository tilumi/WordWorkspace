<?php
class RoutingConfigs{
    //路徑的前綴名稱
    static $prefixs=array(
            'administrator'=>array(
                'name'=>'admin',
            ),
            '__default__'=>array(
                'name'=>'main',
            ),
        );
    //各前綴名稱註冊的路徑和指向的控制器
    static $apps=array(
            'admin'=>array(
                'news'=>array('name'=>'news'),
                'managers'=>array('name'=>'managers'),
                '__default__'=>array('name'=>'main'),
            ),
            'main'=>array(
                'javascript'=>array('name'=>'javascript'),
                '__default__'=>array('name'=>'main'),
            ),
        );

}
?>