<!DOCTYPE HTML>
<html>
    <head>
        <meta charset=utf-8>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Job</title>
        <link href="ex/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="ex/wysibb/css/wbbtheme.css" rel="stylesheet">
        <script src="ex/52framework/js/jquery.min.js"></script>
        <script src="ex/wysibb/js/jquery.wysibb.min.js"></script>
        <script src="ex/dist/js/bootstrap.min.js"></script>
        <link href="css/job.css" rel="stylesheet">
        <style>
        </style>
    </head>
    <body>
    <?php
    $jId = @$_GET['id'];
    if( $jId=='' ){
        die('0:非法请求！');
    }
    include "lib/cfg.inc.php";
    include "lib/class/CTestFile.class.php";

    /* 从数据库中查询job的相关信息 */
    $sql_job = 'select jJob_Title, JURI_Prob, JTime_Start, JTime_End, JIsPub from job where JId='.$jId;
    $res_job = $Conn->query($sql_job);
    $row_job = $res_job->fetch_array();
    if( $row_job['JTime_Start']=='' || $row_job['JIsPub']!=1){
        die('0:非法请求！');
    }
    if( (strtotime($row_job['JTime_Start']) - time())>0 ){
        die('0:还未开始,还不能查看题目！');
    }
    $date_archive = $row_job['JURI_Prob'];
    $ctestFile = new CTestFile();
    ?>
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div id="job" class="panel panel-default">
                        <div class="panel-heading"><?php echo $row_job['jJob_Title']; ?><span class="pull-right">#Job<?php echo $jId; ?> &nbsp;&nbsp;&nbsp;
                                <?php
                                    if( @$_GET['t']==1 ){
                                        echo '<a href="teacher.php">返回教师页面</a> ';
                                    }else{
                                        echo '<a href="student.php">返回学生页面</a> ';
                                    }
                                ?>
                            </span></div>
                        <div class="panel-body">
                          <?php
                                $proDesc =  $ctestFile->readJob($date_archive, $jId);
                                echo '<h4>描述</h4>';
                                if($proDesc==''){
                                    echo '<p><pre>此作业没有描述</pre></p>';
                                }else{
                                    echo '<p><pre>'.$proDesc.'</pre></p>';
                                }
                                echo '<h4>样例输入</h4>';
                                $inSample = $ctestFile->readSampleCase($date_archive, $jId);
                                if($inSample==''){
                                    echo '<p><pre>提示：本作业不需要外部输入</pre></p>';
                                }else{
                                    echo '<p><pre>'.$inSample.'</pre></p>';
                                    $outSample = $ctestFile->readSampleAnswer($date_archive, $jId);
                                    echo '<h4>样例输出</h4><p><pre>'.$outSample.'</pre></p>';
                                }
                          ?>
                        </div>
                        <?php
                        echo '<div style="margin: 8px auto; text-align: center;">接受时间： '.$row_job['JTime_Start'].' ~ '. $row_job['JTime_End'].'</div>';
                        ?>
                    </div>
                    <div id="code" class="panel panel-default">
                        <div class="panel-heading" role="tablist" aria-multiselectable="true">
                            <div class="btn-group btn-group-sm">
                                <a class="btn btn-default btn-sm" data-toggle="tab" role="tab" href="#src" aria-controls="src">源代码</a>
                                <!--
                                <a class="btn btn-default btn-sm" data-toggle="tab" role="tab" href="#compile" aria-controls="compile">编译结果</a>
                                <a class="btn btn-default btn-sm" data-toggle="tab" role="tab" href="#run" aria-controls="run">运行结果</a>-->
                            </div>
                            <div style="float: right; margin-top: 6px; font-size: 12px; color: #ff0000;">（提交代码后，如果编译通过，系统将自动执行，并返回运行结果）</div>
                        </div>
                        <div class="panel-body tab-content">
                            <textarea id="SrcCodeTextarea" style="width: 100%; height: 500px;"></textarea>
                            <!--
                            <div id="src" role="tabpanel" class="tab-pane fade in active">
                                <textarea id="linenumber" readonly></textarea>
                                <div id="srccontent"><textarea></textarea></div>
                            </div>
                            <div id="compile" role="tabpanel" class="tab-pane fade">
                                <p class="bg-success">未编译</p>
                            </div>
                            <div id="run" role="tabpanel" class="tab-pane fade">
                                <p>未运行</p>
                            </div>
                            -->
                        </div>
                    </div>
                    <div id="result" style="margin: 8px 0; color: #ff0000;"></div>
                    <button id="submitCode" class="btn btn-primary pull-right" onclick="SubmitCode(this)" type="button" style="margin-bottom: 20px;" >提交代码</button>
                </div>
            </div>
        </div>

        <script>
            /*
            function LineNumber( lines ) {
                var lss = $('#linenumber').val()
                var ls = lss.split( '\n' );
                var ml = ls.length <= 1 ? 0 : parseInt( ls[ls.length-2] );
                if ( ml < lines ) {
                    var linens = '';
                    for ( var i = ml+1; i < lines+10; i++ ) {
                        linens += i + '\n';
                    }
                    $('#linenumber').val( lss + linens );
                }
            }*/

            function SubmitCode(obj){
                var id = <?php echo $jId; ?>;
                var code = $('#SrcCodeTextarea').val();
                //var code =  $($('.wysibb-text-editor').html().replace(new RegExp('<br>', 'g'), '\n')  ).text();
                if( code=='' ){
                    alert('请输入代码...');
                }
                //alert(code);
                $.ajax({
                    type: "Post",
                    url: "api/doJob.php",
                    data: {
                        type: 'sub', jobId: id, code:code
                    },
                    beforeSend: function(XMLHttpRequest){
                        $('#result').html('正在提交，请勿刷新，提交按钮需等待15s后才可以再次点击...');
                        $(obj).attr("disabled","disable");
                        setTimeout(function(){
                            $(obj).text('再次提交');
                            $(obj).removeAttr("disabled");
                        }, 15000)
                    },
                    success: function(data){
                        $('#result').html(data);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown){
                        alert('连接出错，请检查网络或者联系管理员');
                    }
                });
            }

            /*
            $(document).ready(function() {
                $('#linenumber').val('');
                LineNumber( 30 );
                var wbbOpt = {
                    buttons: "bold,italic,underline,|,img,link,|,code,quote"
                }
                $("#srccontent>textarea").wysibb(wbbOpt);
                
                $('.wysibb-text-editor').scroll( function (e) {
                    LineNumber( $('.wysibb-text-editor br').length );
                    $('#linenumber').scrollTop( $(e.target).scrollTop() );
                } )
            });*/


            // 初始状态清空内容
            $('#submitCode').removeAttr("disabled");
            $('#SrcCodeTextarea').val('');
            $('.wysibb-text-editor').html('');
        </script>
    </body>
</html>