<?php

/*
* 日志信息类
*/
class Log {

    /* 将日志数据插入到数据库中
    *  $Conn 数据库连接
    *  $tbName 表名
    *  $datas 日志数据
    */
    public function  insertTable( $Conn, $tbName, $datas ){
        $Conn->insert($tbName, $datas);
    }

    /* 将日志数据插入到文件中
    *  $file  文件名
    *  $datas 日志数据
    */
    public function  insertFile( $file, $datas ){

    }
}