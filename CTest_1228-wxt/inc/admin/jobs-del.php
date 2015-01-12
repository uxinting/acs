<?php
    require_once '../../lib/cfg.inc.php';
    //获取所有教师姓名及id
    $sql_teacher='select tName, tId from teacher order by tName';
    $res_teacher=$Conn->query($sql_teacher);
    //获取所有课程名称及id
    $sql_course='select cName, cId from course order by cName';
    $res_course=$Conn->query($sql_course);
?>
<ul class="list-group">
    <li class="list-group-item list-group-item-info">
        <div class="btn-group btn-group-sm">
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-default" tabindex="-1" id="flttime_pub">时间排序</button>
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
                <button type="button" class="btn btn-default" tabindex="-1" id="fltteacher_pub">选择教师</button>
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
                    <span class="caret"></span>
                    <span class="sr-only"></span>
                </button>
                <ul class="dropdown-menu">
                <li><a href="javascript: OnChangeTeacherSelect(-1,'全部教师');">全部教师</a></li>
                <?php
                while($row_teacher=$res_teacher->fetch_array()){
                    echo '<li><a href="javascript: OnChangeTeacherSelect('.$row_teacher['tId'].',\''.$row_teacher['tName'].'\');">'.$row_teacher['tName'].'</a></li>';
                }
                ?>
                </ul>
            </div>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-default" tabindex="-1" id="fltcourse_pub">选择科目</button>
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
                    <span class="caret"></span>
                    <span class="sr-only"></span>
                </button>
                <ul class="dropdown-menu">
                <li><a href="javascript: OnChangeCourseSelect(-1,'');">全部科目</a></li>
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
window.fltteacherId=-1;
window.fltcourseId=-1;
window.fltteacherName='';
window.fltcourseName='';
GetJobs();
function OnChangeTimeSelect(value, content){
    $('#flttime_pub').text(content);
    window.flttimeId = value;
    GetJobs();
}
        
function OnChangeTeacherSelect(value, content){
    $('#fltteacher_pub').text(content);
    window.fltteacherId = value;
    window.fltteacherName=content;
    GetJobs();
}

function OnChangeCourseSelect(value, content){
    $('#fltcourse_pub').text(content);
    window.fltcourseId = value;
    window.fltcourseName=content;
    GetJobs();
}

function GetJobs(){
    $.ajax({
        type: "Post",
        url: "api/doAdmin.php",
        data: {type: 'jobs_del', flttimeId:window.flttimeId, fltteacherId:window.fltteacherId, fltteacherName:window.fltteacherName, 
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

/*把作业改成发布*/
function ChangeToPub(){
    
}
        
        
</script>
