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
    include "lib/cfg.inc.php";
    include "lib/class/CTestFile.class.php";

    $jId = @$_GET['id'];
    if( $jId==''){
        die('0:非法请求！');
    }

    /* 从数据库中查询job的相关信息 */
    $sql_job = 'select jJob_Title, JURI_Prob, JTime_Start, JTime_End, JIsPub, tId from job where JId='.$jId;
    $res_job = $Conn->query($sql_job);
    $row_job = $res_job->fetch_array();
    if( @$row_job['tId']!=$_COOKIE['uId'] ){
        die('您没有权限查看！');
    }
    $date_archive = $row_job['JURI_Prob'];
    $ctestFile = new CTestFile();
    ?>
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div id="job" class="panel panel-default">
                        <div class="panel-heading"><?php echo $row_job['jJob_Title']; ?><span class="pull-right">#Job<?php echo $jId; ?> &nbsp;&nbsp;&nbsp;
                                您正在查看自己发布的作业，<a href="teacher.php">返回教师页面</a>
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
                            <?php
                                $outSample = htmlentities($ctestFile->readReferencedCode($date_archive, $jId));
                                echo '<pre style="height: 100%; width: 100%; border: 0;">'.$outSample.'</pre>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>