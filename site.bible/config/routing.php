<?php
class RoutingConfigs{
    //路徑的前綴名稱
    static $maps=array(); //正查表 app->path，系統自動產生，請勿移除
    static $r_maps=array(); //反查表 path->app，系統自動產生，請勿移除
    static $parents=array(); //母app對照表，系統自動產生，請勿移除
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
                
                'songs/sns'=>array('name'=>'songs-sns', 'parents'=>'songs'),
                'songs/langs'=>array('name'=>'songs-langs', 'parents'=>'songs'),
                'songs/books'=>array('name'=>'songs-books', 'parents'=>'songs'),
                'songs'=>array('name'=>'songs'),
                
                'bible/books'=>array('name'=>'bible-books', 'parents'=>'bible'),
                'bible/verses'=>array('name'=>'bible-verses', 'parents'=>'bible'),
                'bible'=>array('name'=>'bible'),
                
                'albums/*/photos'=>array('name'=>'album-photos', 'parents'=>'albums'),
                'albums'=>array('name'=>'albums'),
                
                'managers/groups'=>array('name'=>'groups', 'parents'=>'managers'),
                'managers'=>array('name'=>'managers'),
                
                'syslog'=>array('name'=>'syslog'),
                
                '__default__'=>array('name'=>'main'),
            ),
            'main'=>array(
                'javascript'=>array('name'=>'javascript'),
                '__default__'=>array('name'=>'main'),
            ),
        );

}
?>