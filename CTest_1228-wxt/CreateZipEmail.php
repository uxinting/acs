<?php

    include 'lib/cfg.inc.php';
    $sql = 'select * from job where jIsPub=1 and jCount_Email<2';
    $res = $Conn->query($sql);
    while( $row = $res->fetch_array() ){
        if(  $this->ompareTime($row['JTime_End'])<0 ){
            // 结束考试后一个半小时左右自动打包发给老师（2次）
            include 'lib/class/Crontable.class.php';
            $sql_teacher = 'select * from teacher where tId='.$row['tId'];
            $res_teacher = $Conn->query($sql_teacher);
            $row_teacher = $res_teacher->fetch_array();
            Crontaber::ZipAndMail( $row['jURI_Prob'], $row['jId'], $row_teacher['tEmail'],
                'CTest测试题归档', '/var/www/data/'.$row['jId'].'.zip', $row['jId'].'.zip归档文件' );
        }
    }


    /* 一个半小时左右开始发送 */
    function compareTime($time){
        $a = strtotime($time)+5400 - time();
        if($a > 0){ // 大于当前时间
            return 1;
        }else if($a < 0){ // 小于当前时间
            return -1;
        }
        return 0;
    }
?>