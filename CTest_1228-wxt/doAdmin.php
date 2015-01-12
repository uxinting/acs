<?php
require '../lib/cfg.inc.php';

$type=$_POST['type'];
$aNo=$_COOKIE['uNo'];
if($aNo==''){
    echo '0:非法操作!';
    return ;
}

switch($type){
    case 'newjob' : _NewJob($Conn, $tNo);break;
    case 'changepsw' : _ChangePsw($Conn, $aNo);break;
    case 'changeemail' : _ChangeEmail($Conn, $tNo);break;
}

/*
教师布置新作业
*/
function _NewJob( $conn, $tNo){
    if($tNo==''){
        echo '0非法操作!';
        return ;
    }
    $jobName = $_POST['jobName'];
    $jobdesc = $_POST['jobdesc'];
    $jobin = $_POST['jobin'];
    $jobout = $_POST['jobout'];
    $jobinex = $_POST['jobinex'];
    $joboutex = $_POST['joboutex'];
    $time_start = $_POST['time_start'];
    $time_end = $_POST['time_end'];
    if($jobName=='' || $jobdesc=='' || $jobin=='' || $jobout=='' || $jobinex=='' || $time_end=='' || $joboutex=='' || $time_start==''){
        echo '0信息填写不完整!';
    }
    $sql_job='insert into job("jChapNo", "jAssNo", "tId", "cId", "jJob_Title", "jURL_Prob", "jIp_pub", "jTime_Start", "jTime_End", "jTime_Pub") 
    values("","","","","","","","","","","","","","","","")';
    $res_job=$conn->query($sql_job);
    if($res_job){
        echo '1布置作业成功!';
    }else{
        echo '0布置作业失败!';
    }
}

/*
修改教师登录密码
*/
function _ChangePsw($Conn, $aNo){
    if($aNo==''){
        echo '0:非法操作!';
        return ;
    }
    $oldpsw = $_POST['oldpsw'];
    $newpsw = $_POST['newpsw'];
    $sql_changepsw='select count(*) c from admin where alogName ="'.$aNo.'" and aPsw="'.$oldpsw.'"';
    $res_changepsw=$Conn->query($sql_changepsw);
    $row_changepsw=$res_changepsw->fetch_array();
    if(intval($row_changepsw['c'])==1){
        $sql_changepsw='update admin set aPsw="'.$newpsw.'" where alogName="'.$aNo.'"';
        $res_changepsw=$Conn->query($sql_changepsw);
        if($res_changepsw){
            echo '1修改密码成功!';
            return ;
        }else{
            echo '0修改密码失败!';
            return ;
        }
    }else{
        echo '0修改密码失败!';
        return ;
    }
}

/*
修改教师邮箱
*/
function _ChangeEmail($Conn, $tNo){
    if($tNo==''){
        echo'0非法操作!';
        return ;
    }
    $tEmail = $_POST['Email'];
    $sql_changeemail='update teacher set tEmail = "'.$tEmail.'" where tNo="'.$tNo.'" ';
    $res_changeemail=$Conn->query($sql_changeemail);
    if($res_changeemail){
        echo '1修改邮箱成功!';
        return ;
    }else{
        echo '0修改邮箱失败!';
        return ;
    }
}
?>