<?php
class Main{
    function getSongs(){
        $sql="SELECT * FROM songs";
        return Model::fetchAll($sql);
    }
}
?>