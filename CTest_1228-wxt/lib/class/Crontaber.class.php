<?php

/*
* 定时任务类
*/
class Crontaber {

    /* 教师发布作业后，定时执行打包任务和发送邮件*/
    public static function  ZipAndMail( $data, $jId, $addrTo, $topicName, $attach='', $attachName=''){
        include 'CTestFile.class.php';
        $cTestFile = new CTestFile();
        $cTestFile->createZip($attach , $data, $jId); // 打包文件
        // 创建定时任务
        $cmd = 'uuencode '.$attach.' '.$attachName.' | mail -s "'.$topicName.'" '.$addrTo;
        exec($cmd, $res, $retval);
        return $retval;
    }

    // 在linux服务器下写一个定时任务，每15分钟去请求一次
    // 0,15,30,45 * * * *, wget http://localhost/CTest_1228/CreateTask.php

}