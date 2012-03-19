<?php
class Main{
    function userinfo($data){
        if( isset($data['commit']) ){
            unset($data['commit']);
        }
        $SESSION = &$_SESSION['admin'];
        
        $data['id']=$SESSION['id'];
        $username=$data['username'];
        
        if( Model::update($data, 'id', 'managers') ){
            $SESSION['username']=$username;
            return true;
        }
        return '更新失敗，請再試一次。如果持續發生，請洽管理者。';
    }
    function getUserinfo( $id ){
        $sql="SELECT * FROM managers WHERE id=".Model::quote($id, 'text');
        return Model::fetchRow( $sql );
    }
    
}
?>