<?php
require '../lib/cfg.inc.php';

$type=@$_POST['type'];
$sNo=@$_COOKIE['uNo'];
if($sNo==''){
    echo '0:非法操作!';
    return ;
}

//根据学生的sNo获取sId
$sql_student_id = 'select * from student where sNo="'.$sNo.'"';
$res_student_id = $Conn->query($sql_student_id);
$row_student_id = $res_student_id->fetch_array();

switch($type){
    case 'changepsw' : _ChangePsw($Conn, $sNo);break;
    case 'changeinfo' : _ChangeInfo($Conn, $sNo);break;
    case 'jobs_all' : _JobAllList($Conn, $sNo, $row_student_id['sId']);break;
    case 'jobs_pub' : _JobMyList($Conn, $sNo, 1);break;
    case 'jobs_notpub' : _JobMyList($Conn, $sNo, -1);break;
    default : die('0:非法操作!');
}



/*
修改教师登录密码
*/
function _ChangePsw($Conn, $sNo){
    if($sNo==''){
        echo '0:非法操作!';
        return ;
    }
    $oldpsw = $_POST['oldpsw'];
    $newpsw = $_POST['newpsw'];
    $sql_changepsw='select count(*) c from student where sNo="'.$sNo.'" and sPsw="'.$oldpsw.'"';
    $res_changepsw=$Conn->query($sql_changepsw);
    $row_changepsw=$res_changepsw->fetch_array();
    if(intval($row_changepsw['c'])==1){
        $sql_changepsw='update student set sPsw="'.$newpsw.'" where sNo="'.$sNo.'"';
        $res_changepsw=$Conn->query($sql_changepsw);
        if($res_changepsw){
            echo '1:修改密码成功!';
            return ;
        }else{
            echo '0"修改密码失败!';
            return ;
        }
    }else{
        echo '0:修改密码失败!';
        return ;
    }
}

/*
修改学生信息
*/
function _ChangeInfo($Conn, $sNo){
    if($sNo==''){
        echo'0:非法操作!';
        return ;
    }
    $sName = $_POST['Name'];
    $sSex = $_POST['Sex'];
    $sEmail = $_POST['Email'];
    $sAddr = $_POST['Addr'];
    $sTel = $_POST['Tel'];
    $sQQ = $_POST['QQ'];
    if($sEmail =='' || $sAddr=='' || $sTel=='' || $sQQ==''){
        echo '0:信息填写不完整!';
    }
    $sql_changeemail='update student set sName="'.$sName.'", sSex="'.$sSex.'", sEmail = "'.$sEmail.'", sAddr="'.$sAddr.'", sTel="'.$sTel.'", sQQ="'.$sQQ.'"  where sNo="'.$sNo.'" ';
    $res_changeemail=$Conn->query($sql_changeemail);
    if($res_changeemail){
        echo '1:修改信息成功!';
        return ;
    }else{
        echo '0:修改信息失败!';
        return ;
    }
}


/*获取当前登录学生已经提交的作业列表*/
function _JobMyList($Conn, $sNo, $flag){
    $fltstudentId= $_POST['fltstudentId'];
    $flttime=$_POST['flttimeId'];
    $fltstudent=$_POST['fltstudentId'];
    $fltcourse=$_POST['fltcourseId'];
    //$fltstudentName=$_POST['fltstudentName'];
    $fltcourseName=$_POST['fltcourseName'];
    $now = date("Y-m-d H:i:s");
    $sql_jobs='select * from job_submit_pass where sId="'.$fltstudentId.'"';
    //筛选课程，$fltcourse表示课程的cid
    /*if($fltcourse!=-1){
        $sql_jobs.=' and cid='.$fltcourse;
    }*/
    //筛选时间,$flttime=-1表示全部时间，$flttime=0表示一周内，$flttime=1表示一月内
    switch($flttime){
        case -1 : ;break;
        case 0 : $sql_jobs=$sql_jobs.' and to_days(now())-to_days(jTime_Sub)<=7 ';break;
        case 1 : $sql_jobs=$sql_jobs.' and to_days(now())-to_days(jTime_Sub)<=30 ';break;
    }
    /*$flag+1表示已发布作业，$flag=0表示待发布作业，$flag=-1表示已删除作业
    switch($flag){
        case 1 : $sql_jobs=$sql_jobs.' and jResult=1 ';break;
        case -1 : $sql_jobs=$sql_jobs.' and jResult=-1 ';break;
    }*/
    $sql_jobs.=' order by jTime_Sub desc ';
    $res_jobs=$Conn->query($sql_jobs);
    $count=0;
    while($row_jobs=$res_jobs->fetch_array()){
        //根据jobid查找job信息
        $sql_job='select * from job where jId="'.$row_jobs['jId'].'"';
        $res_job=$Conn->query($sql_job);
        $row_job=$res_job->fetch_array();
        
        //查找课程名称和教师名称
        $sql_teacher_name='select tName, tId from teacher where tId='.$row_job['tId'];
        $res_teacher_name=$Conn->query($sql_teacher_name);
        $row_teacher_name=$res_teacher_name->fetch_array();
        
        
        $sql_course_name='select cName from course where cid='.$row_job['cid'];
        $res_course_name=$Conn->query($sql_course_name);
        $row_course_name=$res_course_name->fetch_array();
        
        echo '<li class="list-group-item">
        <h4>作业'.++$count.'：'.$row_job['jJob_Title'].'</h4>
        <p><em>'.$row_teacher_name['tName'].'</em>&nbsp;&nbsp;&nbsp;&nbsp;<small>'.$row_course_name['cName'].'</small></p>
        <p><span>提交时间:&nbsp;'.$row_jobs['jTime_Sub'].'&nbsp;&nbsp;&nbsp;得分<span class="badge">'.$row_jobs['jScore'].'</span>
        <a href="job.php?id=1" class="btn btn-xs btn-default" target="_blank">详细</a>
        </p>
        </li>';
    }
}

/*获取当前登录学生的作业列表*/
function _JobAllList($Conn, $sNo, $sId){
    $flttime=$_POST['flttimeId'];
    //$fltstudent=$_POST['fltstudentId'];
    $fltcourse=$_POST['fltcourseId'];
    $fltstudentName=$_POST['fltstudentName'];
    $fltcourseName=$_POST['fltcourseName'];
    $now = date("Y-m-d H:i:s");
    
    $sql_jobs='select * from job where 1=1 ';
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
    
    $sql_jobs.=' order by jTime_End desc  ';
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
        
        //根据题目id和学生id查找该学生是否已经提交该题答案并通过
        $sql_ispass='select count(*) c from job_submit_pass where jId="'.$row_jobs['jId'].'" and sId="'.$sId.'" order by jTime_Sub desc limit 0, 1';
        $res_ispass=$Conn->query($sql_ispass);
        $row_ispass=$res_ispass->fetch_array();
        echo '<li class="list-group-item">
        <h4>作业'.++$count.'：'.$row_jobs['jJob_Title'].'<mark style="color:#ff44ff">';
        if($res_ispass && $row_ispass['c']==1){//该题已经提交并通过
            echo '已通过';
        }else{//该题没有提交或者提交未通过
            echo '未通过';
        }
        echo '</mark></h4>
        <p><em>'.$row_teacher_name['tName'].'</em>&nbsp;&nbsp;&nbsp;&nbsp;<small>'.$row_course_name['cName'].'</small></p>
        <p>剩余<span class="badge">'.DiffDays($row_jobs['jTime_End'],$now).'</span>天&nbsp;&nbsp;<span>'.$row_jobs['jTime_Start'].'</span>&nbsp;~&nbsp;'.$row_jobs['jTime_End']
        .'<a href="job.php?id=1" class="btn btn-xs btn-default" target="_blank">详细</a>
        </p>
        </li>';
    }
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