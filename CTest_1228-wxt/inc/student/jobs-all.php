<?php
    require_once '../../lib/cfg.inc.php';
    //获取所有学生姓名及id
    $sql_student='select sName, sId from student order by sName';
    $res_student=$Conn->query($sql_student);
    $row_student=$res_student->fetch_array();
    //获取所有课程名称及id
    $sql_course='select cName, cId from course order by cName';
    $res_course=$Conn->query($sql_course);
?>
<ul class="list-group">
    <li class="list-group-item list-group-item-info">
        <div class="btn-group btn-group-sm">
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-default" tabindex="-1" id="flttime_job_all">时间排序</button>
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
                    <span class="caret"></span>
                    <span class="sr-only"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="javascript: OnChangeTimeSelect(-1,'全部时间');">全部时间</a></li>
                    <li><a href="javascript: OnChangeTimeSelect(0,'一周内');">一周内</a></li>
                    <li><a href="javascript: OnChangeTimeSelect(1,'一月内');">一月内</a></li>
                </ul>
            </div>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-default" tabindex="-1" id="fltcourse_job_all">选择科目</button>
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
                    <span class="caret"></span>
                    <span class="sr-only"></span>
                </button>
                <ul class="dropdown-menu">
                <li><a href="javascript: OnChangeCourseSelect(-1,'全部课程');">全部课程</a></li>
                <?php
                while($row_course=$res_course->fetch_array()){
                    echo '<li><a href="javascript: OnChangeCourseSelect('.$row_course['cId'].',\''.$row_course['cName'].'\');">'.$row_course['cName'].'</a></li>';
                }
                ?>
                </ul>
            </div>
            <div class="btn btn-default btn-sm" disabled>
                <span class="caret"></span>
            </div>
        </div>
        <div style="float: right">所有作业</div>
    </li>

    <div id="jobs-all-list">
    <li class="list-group-item">
        <h4>作业1：冒泡排序算法
            <a data-toggle="collapse" aria-expanded="false" href="#job-pub-1" aria-controls="job-pub-1" role="tab">
                <span class="glyphicon glyphicon-collapse-down"></span>
            </a>
            <mark>30/40</mark>
        </h4>
        <div id="job-pub-1" role="tabpanel" aria-expanded="false" class="collapse">
            <pre>实现冒泡排序算法</pre>
        </div>
        <p><em>甘早斌</em>&nbsp;&nbsp;&nbsp;&nbsp;<small>C语言程序设计</small></p>
        <p><span>2013.12.3</span>~2014.12.30&nbsp;剩余<span class="badge">28</span>天
            <a href="job.php?id=1" class="btn btn-xs btn-default" target="_blank">详细</a>
        </p>
    </li>
    </div>
</ul>
<nav>
    <ul class="pagination pagination-sm">
        <li><a href="#">&laquo;</a></li>
        <li><a href="#">1</a></li>
        <li><a href="#">2</a></li>
        <li><a href="#">3</a></li>
        <li><a href="#">4</a></li>
        <li><a href="#">5</a></li>
        <li><a href="#">&raquo;</a></li>
    </ul>
</nav>
<script>
window.flttimeId=-1;
window.flteaId=<?php echo $row_student['sId']; ?>;
window.fltcourseId=-1;
window.fltstudentName='<?php echo $row_student['sName']; ?>';
window.fltcourseName='';
GetJobs();
function OnChangeTimeSelect(value, content){
    $('#flttime_job_all').text(content);
    window.flttimeId = value;
    GetJobs();
}

function OnChangeCourseSelect(value, content){
    $('#fltcourse_job_all').text(content);
    window.fltcourseId = value;
    window.fltcourseName=content;
    GetJobs();
}

function GetJobs(){
    $.ajax({
        type: "Post",
        url: "api/doStudent.php",
        data: {type: 'jobs_all', flttimeId:window.flttimeId, fltstudentId:window.fltstudentId, fltstudentName:window.fltstudentName, 
            fltcourseId:window.fltcourseId, fltcourseName:window.fltcourseName},
        beforeSend: function(XMLHttpRequest){
            $('div#listjobs').html('正在加载...');
        },
        success: function(data){
            $('div#jobs-all-list').html(data);
            //alert(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            $('div#listjobs').html(data);
            alert('连接出错');
        }
    });
} 
        
</script>
