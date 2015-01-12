<?php

/*
* 日志信息类
*/
class Mailer {

    /* 发送邮件并包含附件
    *  $addrTo 发送给哪个邮箱地址
    *  $content  发送的内容
    *  $attach  附件所在路径
    */
    public function  sendWithAttach( $addrTo, $topicName, $attach='', $attachName='' ){
        $cmd = 'uuencode '.$attach.' '.$attachName.' | mail -s "'.$topicName.'" '.$addrTo;
        system($cmd, $retval);
        //echo $cmd.'-----'.$retval;
        return $retval;
    }

    /* 发送邮件
    */
    public function  send( $addrTo, $topicName, $content ){
        $cmd = 'echo "'.$content.'" | mail -s "'.$topicName.'"  '.$addrTo;
        // echo $cmd;
        exec($cmd, $res, $retval);
        return $retval;
    }

}