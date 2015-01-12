<?php
//获取学生信息
    $sNo=$_COOKIE["uNo"];
    $sql_student='select * from student where sNo="'.$sNo.'"';
    $res_student=$Conn->query($sql_student);
    $row_student=$res_student->fetch_array();
?>
<div class="self-info">
    <p>姓名：<b><?php echo $row_student['sName']; ?></b></p>
    <p>学号：<?php echo $row_student['sNo']; ?></p>
    <p>学院：计算机科学与技术学院</p>
    <p>专业：计算机应用技术</p>
    <p>班级：计算机硕1202</p>
</div>
<div id="menu" class="panel-group" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">

        <div class="panel-heading" role="tab" >
            <h4 class="panel-title">
                <a data-toggle="collapse" href="#jobcontrol" aria-controls="jobcontrol">
                    我的作业
                </a>
            </h4>
        </div>
        <div id="jobcontrol" class="panel-collapse collapse" role="tabpanel">
            <ul class="list-group">
                <li class="list-group-item">
                    <a data-toggle="tab" href="#jobs-all" aria-controls="jobs-all">所有作业</a>
                </li>
                <li class="list-group-item">
                    <a data-toggle="tab" href="#jobs-pass" aria-controls="jobs-pass">已通过</a>
                </li>
                <!--
                <li class="list-group-item">
                    <a data-toggle="tab" href="#jobs-notpass" aria-controls="jobs-notpass">未通过</a>
                </li>
                -->
            </ul>
        </div>

        <div class="panel-heading" role="tab" >
            <h4 class="panel-title">
                <a data-toggle="collapse" href="#profile" aria-expanded="false" aria-controls="profile">
                    个人设置
                </a>
            </h4>
        </div>
        <div id="profile" class="panel-collapse collapse" role="tabpanel">
            <ul class="list-group">
                <li class="list-group-item">
                    <a data-toggle="tab" href="#password" aria-controls="password">密码修改</a>
                </li>
                <li class="list-group-item">
                    <a data-toggle="tab" href="#person" aria-controls="person">信息修改</a>
                </li>
            </ul>
        </div>

        <div class="panel-heading" role="tab" >
            <h4 class="panel-title">
                <a data-toggle="collapse" href="#log" aria-expanded="false" aria-controls="profile">
                    历史记录
                </a>
            </h4>
        </div>

        <div id="log" class="panel-collapse collapse" role="tabpanel">
            <ul class="list-group">
                <li class="list-group-item">
                    <a data-toggle="tab" href="#log-job" aria-controls="log-job">作业日志</a>
                </li>
                <li class="list-group-item">
                    <a data-toggle="tab" href="#log-login" aria-controls="log-login">登录日志</a>
                </li>
            </ul>
        </div>

        <div class="panel-heading" role="tab" >
            <h4 class="panel-title">
                <a data-toggle="tab" href="#msg" aria-controls="msg">
                    通知<span class="badge">1</span>
                </a>
            </h4>
        </div>
        
    </div>
</div>

<script>

    /* 侧边栏切换 */

    $('#menu a[data-toggle="tab"]').attr('aria-expanded','true');
    $(function(){
        $('#menu a[data-toggle="tab"]').bind("click",function(){
            var id = $(this).attr('aria-controls');
            $('#menu a[data-toggle="tab"]').parent().removeClass('active');
            //$(this).parent().addClass('active');

            $.ajax({
                type: "GET",
                url: "inc/student/"+$(this).attr('aria-controls')+".php",
                beforeSend: function(XMLHttpRequest){
                    $('#AjaxBody div.tab-pane').html('');
                    $('#'+id).html("正在载入...");
                },
                success: function(data){
                    $('#'+id).html(data);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    $('#'+id).html("加载出错...");
                }
            });
        });
    });


    $('a[href="#msg"]').on('shown.bs.tab', function (e) {
        $(e.target).find('.badge').hide();
    })

    $('blockquote.bg-warning').click( function (e) {
        $(this).removeClass('bg-warning');
    } );
</script>