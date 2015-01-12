<?php
require '../lib/cfg.inc.php';

$type=$_POST['type'];
$tNo=$_COOKIE['uNo'];
if($tNo==''){
    echo '0:非法操作!';
    return ;
}
switch($type){
    case 'changepsw' : _ChangePsw($Conn, $tNo);break;
    case 'changeinfo' : _ChangeInfo($Conn, $tNo);break;
    case 'jobs_all' : _JobAllList($Conn, $tNo);break;
    case 'jobs_pub' : _JobMyList($Conn, $tNo, 1);break;
    case 'jobs_notpub' : _JobMyList($Conn, $tNo, 0);break;
    case 'jobs_del' : _JobMyList($Conn, $tNo, -1);break;
    default : break;
}


/*
修改教师登录密码
*/
function _ChangePsw($Conn, $tNo){
    if($tNo==''){
        echo '0:非法操作!';
        return ;
    }
    $oldpsw = $_POST['oldpsw'];
    $newpsw = $_POST['newpsw'];
    $sql_changepsw='select count(*) c from teacher where tNo="'.$tNo.'" and tPsw="'.$oldpsw.'"';
    $res_changepsw=$Conn->query($sql_changepsw);
    $row_changepsw=$res_changepsw->fetch_array();
    if(intval($row_changepsw['c'])==1){
        $sql_changepsw='update teacher set tPsw="'.$newpsw.'" where tNo="'.$tNo.'"';
        $res_changepsw=$Conn->query($sql_changepsw);
        if($res_changepsw){
            echo '1:修改密码成功!';
            return ;
        }else{
            echo '0:修改密码失败!';
            return ;
        }
    }else{
        echo '0:修改密码失败!';
        return ;
    }
}

/*
修改教师信息
*/
function _ChangeInfo($Conn, $tNo){
    if($tNo==''){
        echo'0:非法操作!';
        return ;
    }
    $tEmail = $_POST['Email'];
    $tAddr = $_POST['Addr'];
    $tTel = $_POST['Tel'];
    $tQQ = $_POST['QQ'];
    if($tEmail =='' || $tAddr=='' || $tTel=='' || $tQQ==''){
        echo '0:信息填写不完整!';
    }
    $sql_changeemail='update teacher set tEmail = "'.$tEmail.'", tAddr="'.$tAddr.'", tTel="'.$tTel.'", tQQ="'.$tQQ.'" where tNo="'.$tNo.'" ';
    $res_changeemail=$Conn->query($sql_changeemail);
    if($res_changeemail){
        echo '1:修改信息成功!';
        return ;
    }else{
        echo '0:修改信息失败!';
        return ;
    }
}

/*获取当前登录教师的作业列表*/
function _JobMyList($Conn, $tNo, $flag){
    $flttime=$_POST['flttimeId'];
    $fltteacher=$_POST['fltteacherId'];
    $fltcourse=$_POST['fltcourseId'];
    $fltteacherName=$_POST['fltteacherName'];
    $fltcourseName=$_POST['fltcourseName'];
    $now = date("Y-m-d H:i:s");
    
    $sql_jobs='select * from job where tId="'.$fltteacher.'"';
    //筛选课程，$fltcourse表示课程的cid
    if($fltcourse!=-1){
        $sql_jobs.=' and cid='.$fltcourse;
    }
    //筛选时间,$flttime=-1表示左右时间，$flttime=0表示一周内，$flttime=1表示一月内
    switch($flttime){
        case -1 : ;break;
        case 0 : $sql_jobs=$sql_jobs.' and to_days(jTime_End)-to_days(now())<=7 ';break;
        case 1 : $sql_jobs=$sql_jobs.' and to_days(jTime_End)-to_days(now())<=30 ';break;
    }
    //$flag+1表示已发布作业，$flag=0表示待发布作业，$flag=-1表示已删除作业
    switch($flag){
        case 1 : $sql_jobs=$sql_jobs.' and jIsPub=1 ';break;
        case 0 : $sql_jobs=$sql_jobs.' and jIsPub=0 ';break;
        case -1 : $sql_jobs=$sql_jobs.' and jIsPub=-1 ';break;
    }
    $sql_jobs.=' order by jTime_End desc ';
    //echo $sql_jobs.'===';
    $res_jobs=$Conn->query($sql_jobs);
    $count=0;
    while($row_jobs=$res_jobs->fetch_array()){
        //根据jobid查找课程名称和教师名称
        $sql_teacher_name='select tName, tId from teacher where tId='.$row_jobs['tId'];
        $res_teacher_name=$Conn->query($sql_teacher_name);
        $row_teacher_name=$res_teacher_name->fetch_array();
        
        $sql_course_name='select cName from course where cid='.$row_jobs['cid'];
        $res_course_name=$Conn->query($sql_course_name);
        $row_course_name=$res_course_name->fetch_array();
        
        echo '<li class="list-group-item">
        <h4>作业'.++$count.'：'.$row_jobs['jJob_Title'].'
            <a data-toggle="collapse" aria-expanded="false" href="#job-pub-1" aria-controls="job-pub-1" role="tab">
                <span class="glyphicon glyphicon-collapse-down"></span>
            </a>
            <mark>30/40</mark>
        </h4>
        <div id="job-pub-1" role="tabpanel" aria-expanded="false" class="collapse">
            <pre>'.$row_jobs['jNote'].'</pre>
        </div>
        <p><em>'.$row_teacher_name['tName'].'</em>&nbsp;&nbsp;&nbsp;&nbsp;<small>'.$row_course_name['cName'].'</small></p>
        <p><span>'.$row_jobs['jTime_Start'].'</span>~'.$row_jobs['jTime_End'].'&nbsp;剩余<span class="badge">'.DiffDays($row_jobs['jTime_End'],$now).'</span>天';
        if($flag==1){//发布
            echo'<a href="javascript:AlterJob(this,\'del\', '.$row_jobs['jId'].')"  id="del" class="btn btn-xs btn-default">删除</a>
                <a href="job_t.php?id='.$row_jobs['jId'].'" id="detail" class="btn btn-xs btn-default" target="_blank">详细</a>
                <a href="javascript:AlterJob(this,\'pubNot\', '.$row_jobs['jId'].')" id="change" class="btn btn-xs btn-default">改待发布</a>';
        }else if($flag==0){//待发布
            echo'<a href="javascript:AlterJob(this,\'del\', '.$row_jobs['jId'].')" class="btn btn-xs btn-default">删除</a>
                <a href="job_t.php?id='.$row_jobs['jId'].'" class="btn btn-xs btn-default" target="_blank">详细</a>
                <a href="javascript:AlterJob(this,\'pubC\', '.$row_jobs['jId'].')" class="btn btn-xs btn-default">改发布</a>';
        }else if($flag==-1){//已删除
            echo'<a href="javascript:AlterJob(this,\'del\', '.$row_jobs['jId'].')" class="btn btn-xs btn-default">彻底删除</a>
                <a href="job_t.php?id='.$row_jobs['jId'].'" class="btn btn-xs btn-default" target="_blank">详细</a>
                <a href="javascript:AlterJob(this,\'pubNot\', '.$row_jobs['jId'].')" class="btn btn-xs btn-default">改待发布</a>';
        }
        echo '</p>
        </li>';
    }
}

/*查询所有教师已经发布的作业*/
function _JobAllList($Conn, $tNo){
    $flttime=$_POST['flttimeId'];
    $fltteacher=$_POST['fltteacherId'];
    $fltcourse=$_POST['fltcourseId'];
    $fltteacherName=$_POST['fltteacherName'];
    $fltcourseName=$_POST['fltcourseName'];
    $now = date("Y-m-d H:i:s");
    
    $sql_jobs='select * from job where 1=1';
    //筛选教师，$fltteacher表示教师tId
    if($fltteacher!=-1){
        $sql_jobs.=' and tId ='.$fltteacher;
    }
    //筛选课程，$fltcourse表示课程的cid
    if($fltcourse!=-1){
        $sql_jobs.=' and cid='.$fltcourse;
    }
    //筛选时间,$flttime=-1表示左右时间，$flttime=0表示一周内，$flttime=1表示一月内
    switch($flttime){
        case -1 : ;break;
        case 0 : $sql_jobs=$sql_jobs.' and to_days(jTime_End)-to_days(now())<=7 ';break;
        case 1 : $sql_jobs=$sql_jobs.' and to_days(jTime_End)-to_days(now())<=30 ';break;
    }
    $sql_jobs.=' order by jTime_End desc ';
    //echo $sql_jobs.'===';
    $res_jobs=$Conn->query($sql_jobs);
    $count=0;
    while($row_jobs=$res_jobs->fetch_array()){
        //根据jobid查找课程名称和教师名称
        $sql_teacher_name='select tName, tId from teacher where tId='.$row_jobs['tId'];
        $res_teacher_name=$Conn->query($sql_teacher_name);
        $row_teacher_name=$res_teacher_name->fetch_array();
        
        $sql_course_name='select cName from course where cid='.$row_jobs['cid'];
        $res_course_name=$Conn->query($sql_course_name);
        $row_course_name=$res_course_name->fetch_array();
        
        echo '<li class="list-group-item">
        <h4>作业'.++$count.'：'.$row_jobs['jJob_Title'].'
            <a data-toggle="collapse" aria-expanded="false" href="#job-pub-1" aria-controls="job-pub-1" role="tab">
                <span class="glyphicon glyphicon-collapse-down"></span>
            </a>
            <mark>30/40</mark>
        </h4>
        <div id="job-pub-1" role="tabpanel" aria-expanded="false" class="collapse">
            <pre>'.$row_jobs['jNote'].'</pre>
        </div>
        <p><em>'.$row_teacher_name['tName'].'</em>&nbsp;&nbsp;&nbsp;&nbsp;<small>'.$row_course_name['cName'].'</small></p>
        <p><span>'.$row_jobs['jTime_Start'].'</span>~'.$row_jobs['jTime_End'].'&nbsp;剩余<span class="badge">'.DiffDays($row_jobs['jTime_End'],$now).'</span>天
        <a href="job.php?id='.$row_jobs['jId'].'" class="btn btn-xs btn-default" target="_blank">详细</a>
        </p>
        </li>';
    }
}

/*转换时间格式：2014-12-12 00:00:00转换成2014.12.12*/
function __ChangeTimeFormat($time){
    $arrtime = explode(' ', $time);
    $arrtime = explode('-', $arrtime[0]);
    $formatTime=$arrtime[0].'.'.$arrtime[1].'.'.$arrtime[2];
    return $formatTime;   
}

/*计算两个时间的天数差*/
function DiffDays($dateStart, $dateEnd){
    $dateStart = explode(' ',$dateStart);
    $dateEnd = explode(' ',$dateEnd);
    $d1=strtotime($dateStart[0]);
    $d2=strtotime($dateEnd[0]);
    return intval(($d1-$d2)/3600/24);
}
?>