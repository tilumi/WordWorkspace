<?php
class RoutingConfigs{
    //路徑的前綴名稱
    static $maps=array(); //正查表 app->path，系統自動產生，請勿移除
    static $r_maps=array(); //反查表 path->app，系統自動產生，請勿移除
    static $parents=array(); //母app對照表，系統自動產生，請勿移除
    static $prefixs=array(
            'm'=>array(
                'name'=>'mobile',
            ),
            '__default__'=>array(
                'name'=>'main',
            ),
        );
    //各前綴名稱註冊的路徑和指向的控制器
    static $apps=array(
            'mobile'=>array(
                '__default__'=>array('name'=>'main'),
            ),
        );
}
?>