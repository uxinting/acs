<?php
require '../lib/cfg.inc.php';

$type=@$_POST['type'];
$aNo=@$_COOKIE['uNo'];
if($aNo==''){
    echo '0:非法操作!';
    return ;
}

switch($type){
    case 'newjob' : _NewJob($Conn, $tNo);break;
    case 'changepsw' : _ChangePsw($Conn, $aNo);break;
    case 'changeemail' : _ChangeEmail($Conn, $tNo);break;
    case 'info_student' : _InfoStudent($Conn);break;
    case 'info_teacher' : _InfoTeacher($Conn);break;
    case 'jobs_pub' : _JobAllList($Conn, 1);break;
    case 'jobs_notpub' : _JobAllList($Conn, 0);break;
    case 'jobs_del' : _JobAllList($Conn, -1);break;
    case 'del_student' : _OptStudent($Conn, 1);break;
    case 'ban_student' : _OptStudent($Conn, 2);break;
    case 'logout_student' : _OptStudent($Conn, 3);break;
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

/*获取学校信息列表*/
function _InfoStudent($Conn){
    $sql_student_info='select * from student';
    $res_student_info=$Conn->query($sql_student_info);
    while($row_student_info=$res_student_info->fetch_array()){
            //根据班级号查询班级名次
            $sql_classId='select className from class where classId="'.$row_student_info['sClassId'].'" limit 0,1 ';
            $res_classId=$Conn->query($sql_student_info);
            $row_classId=$res_classId->fetch_array();
        echo '<tr>
            <td>'.$row_student_info['sNo'].'</td>
            <td>'.$row_student_info['sName'].'</td>
            <td>暂无班级</td>
            <td>'.$row_student_info['sAddr'].'</td>
            <td><a href="api/doAdmin.php?type=del_student&id='.$row_student_info['sId'].'">删除</a>&nbsp;&nbsp;&nbsp;<a href="api/doAdmin.php?type=logout_student&id='.$row_student_info['sId'].'">下线</a>&nbsp;&nbsp;&nbsp;<a href="api/doAdmin.php?type=ban_student&id='.$row_student_info['sId'].'">禁用</a></td>
        </tr>';
    }
}

/*获取教师信息列表*/
function _InfoTeacher($Conn){
    $sql_teacher_info='select * from teacher';
    $res_teacher_info=$Conn->query($sql_teacher_info);
    while($row_teacher_info=$res_teacher_info->fetch_array()){
        echo '<tr>
            <td>'.$row_teacher_info['tNo'].'</td>
            <td>'.$row_teacher_info['tName'].'</td>
            <td>'.$row_teacher_info['tColleage'].'</td>
            <td><a>删除</a>&nbsp;&nbsp;&nbsp;<a>下线</a>&nbsp;&nbsp;&nbsp;<a>禁用</a></td>
        </tr>';
    }
}

/*查询所有教师的作业*/
function _JobAllList($Conn, $flag){
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
        case 0 : $sql_jobs=$sql_jobs.' and to_days(jTime_End)-to_days(now())<=7 ';break;
        case 1 : $sql_jobs=$sql_jobs.' and to_days(jTime_End)-to_days(now())<=30 ';break;
        default:break;
    }
    //$flag+1表示已发布作业，$flag=0表示待发布作业，$flag=-1表示已删除作业
    switch($flag){
        case 1 : $sql_jobs=$sql_jobs.' and jIsPub=1 ';break;
        case 0 : $sql_jobs=$sql_jobs.' and jIsPub=0 ';break;
        case -1 : $sql_jobs=$sql_jobs.' and jIsPub=-1 ';break;
        default : break;
    }
    
    $sql_jobs.=' order by jTime_End desc ';
    $res_jobs=$Conn->query($sql_jobs);
    $count=0;
    echo $sql_jobs.'===';
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
        <a href="job.php?t=1&id=1" class="btn btn-xs btn-default" target="_blank">详细</a>
        </p>
        </li>';
    }
}

/*操作学生1:删除；2:禁用*；3：下线*/
function _OptStudent($Conn, $flag){
    $user = new User($Conn);
    $sId=$_POST['id'];
    switch($flag){
        case 1 : $user->DeleteStudent($sId); break;
        case 2 : $user->BanStudent($sId); break;
        case 3 : $user->ForceStudentLogout($sId); break;
    }
    echo '操作成功';
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