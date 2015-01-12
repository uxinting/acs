<?php

/*
* 客户端信息类
*/
class ClientInfo {

    /* 获取用户Ip */
    public function  GetIp(){
        if (@$_SERVER['HTTP_X_FORWARDED_FOR'])
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (@$_SERVER['HTTP_CLIENT_IP'])
            return $_SERVER['HTTP_CLIENT_IP'];
        else if (@$_SERVER['REMOTE_ADDR'])
            return $_SERVER['REMOTE_ADDR'];
        else if (@getenv('HTTP_X_FORWARDED_FOR'))
            return getenv('HTTP_X_FORWARDED_FOR');
        else if (@getenv('HTTP_CLIENT_IP'))
            return getenv('HTTP_CLIENT_IP');
        else if (@getenv('REMOTE_ADDR'))
            return getenv('REMOTE_ADDR');
        else
            return '0.0.0.0';
    }

}