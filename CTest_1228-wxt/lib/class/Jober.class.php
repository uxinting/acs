<?php

/*
* 作业控制类
* 1、
* 2、
*/
class Jober {

    private static $PATH = '/var/www/data/ctest/';
    private static $PATTERN = '\/var\/www\/data\/ctest\/';
    private static $MAXRUNTIME = 3;
    /*发布到待发布*/
    public static function  Pub($Conn){

        $uId = $_COOKIE['uId'];     // 用户Id
        $jobChapNo = Jober::inputFormat('jobChapNo');   // 作业章节编号
        $jobAssNo = Jober::inputFormat('jobAssNo');     // 作业章内编号
        $jobType = Jober::inputFormat('jobType');       // 作业类型：0作业，1考试
        $jobCourse = Jober::inputFormat('jobCourse');   // 作业s所属课程Id
        $jobTitle = Jober::inputFormat('jobTitle');     // 作业标题
        $jobProDesc = Jober::inputFormat('jobProDesc'); // 作业问题描述
        $jobAnsCode = Jober::inputFormat('jobAnsCode'); // 作业标准答案源代码
        $jobInSample = Jober::inputFormat('jobInSample');    // 作业输入样例的内容
        $jobInTest = Jober::inputFormat('jobInTest');   // 作业测试用例的内容
        $jobTime_Start = Jober::inputFormat('jobTime_Start');   // 作业开始时间
        $jobTime_End = Jober::inputFormat('jobTime_End');       // 作业结束时间
        $jobNote = Jober::inputFormat('jobNote');       // 作业备注
        if( $jobType==-1 || $jobCourse==-1 || $jobTitle=='' || $jobProDesc=='' || $jobAnsCode==''){
            die('0:必要输入不能为空');
        }
        //  自动化处理
        $date_archive = date("Ymd");// 归档时间
        $now = date("Y-m-d H:i:s");// 当前时间
        $clientInfo = new ClientInfo();
        $datas_job = array(
            'jChapNo' => $jobChapNo,
            'jAssNo' => $jobAssNo,
            'tId' => $uId, // 发布者教师的Id
            'cId' => $jobCourse,
            'jType' => $jobType,
            'jJob_Title' => $jobTitle,
            'jURI_Prob' => $date_archive,
            'jIp_pub' => $clientInfo->GetIp(),
            'jTime_Start' => $jobTime_Start,
            'jTime_End' => $jobTime_End,
            'jTime_Pub' => $now,
            'jIsPub' => -1,  // 默认程序程序编译有异常
            'jNote' => $jobNote
        );
        // 如果用户Cookie中nJob已经存在，则取出nJob中的数字，更新该Id的条目
        $id = $nJobId = @$_COOKIE['nJob'];
        if( $nJobId!='' && is_numeric($nJobId) ){
            Jober::alterJobState( $Conn, -1, $uId, $nJobId  );
        }else{
            // 插入数据库并返回Id
            if(  !$Conn->insert('job', $datas_job) ){
                die('0:数据库出错，请联系管理员#1');
            }
            $id = $Conn->geLastId();
            // 更新记录发布者题号的Cookie
            setcookie("nJob", $id, time()+7200, '/' );
        }

        // 1. 保存题目到文件
        if( CTestFile::createJob($date_archive,$id,$jobProDesc)!=1 ){
            die('0:系统保存题目时出错，请联系管理员#2！');
        }
        // 2. 保存标准源代码到文件
        if( CTestFile::createReferencedCode($date_archive,$id,$jobAnsCode) != 1){
            die('0:系统保存标准源代码时出错，请联系管理员#3！');
        }
        // 3. 保存输入样例到文件
        if( CTestFile::createSampleCase($date_archive,$id,$jobInSample) != 1 ){
            die('0:系统保存输入样例时出错，请联系管理员#4！');
        }
        // 4. 保存测试用例到文件
        if( CTestFile::createTestCase($date_archive,$id,$jobInTest) != 1 ) {
            die('0:系统保存测试用例时出错，请联系管理员#5！');
        }
        // 5. 编译标准代码
        $absolutePath =  Jober::$PATH.$date_archive.'/'.$id.'/';
        $patternPath =  Jober::$PATTERN.$date_archive.'\/'.$id.'\/'.$id.'.c';
        $pathId =  Jober::$PATH.$date_archive.'/'.$id.'/'.$id;
        $codeFile1 = $pathId.'.c';
        $testFile1 = $pathId.".sample.test";
        $outName1 = $testFile1;

        // 编译代码，根据输入样例生成输出样例
        // $codeChecker1 = new CodeChecker( $absolutePath, $patternPath, $pathId.".c", $pathId.".sample.test",'' );
         $codeChecker1 = new CodeChecker( $absolutePath, $patternPath, $codeFile1, $testFile1, $outName1, '' );
        if( $codeChecker1->Compile()!=0 ){
            die('0:编译失败，请检查标准答案源代码!');
        }
        $codeChecker1->Run(1,Jober::$MAXRUNTIME); // 运行后生成结果且保留可执行程序
        // 编译代码，根据测试用例生成标准答案
        // $codeChecker2 = new CodeChecker( $absolutePath, $patternPath, $pathId.".c", $pathId.".test",'' );
        $codeFile2 = $pathId.'.c';
        $testFile2 = $pathId.".test";
        $outName2 = $testFile2;
        $codeChecker2 = new CodeChecker( $absolutePath, $patternPath, $codeFile2, $testFile2, $outName2, '' );
        if( $codeChecker2->Compile()!=0 ){
            die('0:编译失败，请检查标准答案源代码!');
        }
        $codeChecker2->Run(1,Jober::$MAXRUNTIME); // 运行后仅生成结果且删除可执行程序
        // 发布成功清空记录发布者题号的Cookie
        setcookie("nJob", '', time()-3600, '/' );
        // 更新数据库中的题目状态字段(待正式发布，保存)
        Jober::alterJobState( $Conn, 0, $uId, $id  );
        die('1:编译正常，根据输入样例已生成输出样例，根据测试用例已生成测试答案，发布成功！');
    }


    
    /* 改成正式发布 */
    public static function  PubC($Conn){
        $uId = $_COOKIE['uId'];
        $uNo = $_COOKIE['uNo'];
        $jId = @$_POST['jobId'];
        Jober::alterJobState( $Conn, 1, $uId, $jId  );
    }

    /* 改成待发布 */
    public static function  PubNot($Conn){
        $uId = $_COOKIE['uId'];
        $uNo = $_COOKIE['uNo'];
        $jId = @$_POST['jobId'];
        Jober::alterJobState( $Conn, 0, $uId, $jId  );
    }

    /* 编辑作业x */
    public static function Edit($Conn){
        $uId = $_COOKIE['uId'];
        $uNo = $_COOKIE['uNo'];
        $jId = $_POST['jobId'];
    }

    /*  删除作业TOTEST */
    public static function Del($Conn){
        $uId = $_COOKIE['uId'];
        $uNo = $_COOKIE['uNo'];
        $jId = $_POST['jobId'];
        Jober::alterJobState( $Conn, -1, $uId, $jId  );
    }


    /* 完全删除作业TODO  重名20130101/2->2X */
    public static function DelC($Conn) {
        $uId = $_COOKIE['uId'];
        $uNo = $_COOKIE['uNo'];
        $jId = $_POST['jobId'];
        
        //如果进行该操作的既不是管理员，也不是该作业的作者
        $sql_admin = 'select * from Admin where alogName='.$uNo;
        $res_admin = $Conn->query($sql_admin);
        if(!$res_admin) {
             die('0:数据库出现故障!');        
        } else {
            $row_admin = $res_admin->fetch_array();
            if($row_admin['alogName'] != $uNo) {
                $sql_teacher = 'select tId, jURI_Prob from Job where jId='.$jId;
                $res_teacher = $Conn->query($sql_teacher);
                if(!$res_teacher) {
                    die('0:数据库出现故障!');
                }
                $row_teacher = $res_teacher->fetch_array();
                if($row_teacher['tId'] != $uId) {
                    die('0:编译失败，请检查标准答案源代码!');
                } 
            }
        }
        if(CTestFile::rename($row_teacher['jURI_Prob'],$jId) != 1) {
            die('0:删除失败，请联系管理员!');
        }
        die('1:删除成功!');
    }
    
    
    

    /* 学生提交作业 */
    public static function Sub( $Conn ) {
        $uId = $_COOKIE['uId'];
        $uNo = $_COOKIE['uNo'];
        $code = $_POST['code'];
        $jId = $_POST['jobId'];
        if( $code=='' ){
            die('0:输入不完整！');
        }

        /* 从数据库中查询job的相关信息 */
        $sql_job = 'select JURI_Prob, JTime_Start, JTime_End, JIsPub from job where JId='.$jId;
        $res_job = $Conn->query($sql_job);
        $row_job = $res_job->fetch_array();
        // 对各种时间进行校验
        if( $row_job['JTime_Start']=='' || $row_job['JIsPub']!=1){
            die('1:非法请求！');
        }
        if(  Jober::compareTime($row_job['JTime_Start'])>0 ){
            die('1:还未开始,不能提交！');
        }else if(  Jober::compareTime($row_job['JTime_End'])<0 ){
            die('1:已经结束,不能提交！');
        }
        $date_archive = $row_job['JURI_Prob']; // 记录了问题所在目录（归档时间）
        $clientInfo = new ClientInfo();
        $now = date("Y-m-d H:i:s");// 当前时间
        $data_job = array(
            'jId' => $jId,
            'sId' => $uId, // 提交者教师的Id
            'JURI_Response' => $date_archive,
            'jIp_Sub' => $clientInfo->GetIp(),
            'jTime_Sub' => $now,
            'jResult' => 0,  // 等待编译
            'jScore' => 0,   // 默认分数
            'jDesc' => @$_POST['jobDesc'],
            'jsNote' => @$_POST['jobNote']
        );
        // 插入数据库并返回Id
        if(  !$Conn->insert('job_submit', $data_job) ){
            die('1:数据库出错，请联系管理员#1');
        }
        $subId = $Conn->geLastId();

        // 1. 创建学生提交的代码到文件中
        if( CTestFile::createSubmitCode( $date_archive, $jId, $uNo, $code )!=1 ){
            die('1:系统处理代码时出错，请联系管理员#2！');
        }
        // 2. 编译
        $absolutePath =  Jober::$PATH.$date_archive.'/'.$jId.'/s/';
        $patternPath =  Jober::$PATTERN.$date_archive.'\/'.$jId.'\/s\/'.$uNo.'-'.$jId.'.c';
        $pathId =  Jober::$PATH.$date_archive.'/'.$jId.'/';
        $codeFile = $pathId."s/".$uNo.'-'.$jId.".c";
        $testFile = $pathId.$jId.".test";
        $outName = $pathId."s/".$uNo.'-'.$jId;
        $ansFile = $pathId.$jId.'.test.ans';
        $codeChecker1 = new CodeChecker( $absolutePath, $patternPath, $codeFile, $testFile, $outName ,$ansFile );
        if( $codeChecker1->Compile()!=0 ){
            die('0:编译失败，请检查源代码!');
        }
        $codeChecker1->Run(1,Jober::$MAXRUNTIME); // 运行后生成结果删除执行程序
        // 3. 比较
        if( $codeChecker1->Compare()==0 ){
            // 更新数据库(提交正确)
            if( $_COOKIE['u']==0 ){
                // 只记录学生提交通过的信息
                Jober::alterJobSubmitState( $Conn, 1, $uId, $subId  );
                $data_job['jResult'] = 1;
                $data_job['jScore'] = 100;
                // 插入数据库并返回Id
                if(  !$Conn->insert('job_submit_pass', $data_job) ){
                    die('1:数据库出错，请联系管理员#102');
                }
            }
            die('0:恭喜，本次提交正确，运行通过!');
        }else{
            if( $_COOKIE['u']==0 ){
                // 只记录学生提交未通过的信息
                Jober::alterJobSubmitState( $Conn, -1, $uId, $subId  );
            }
            die('1:呜呜，本次提交不正确，解题失败，您可以在结束之前再次提交代码!');
        }
    }

    /* 代码输入格式化 */
    private static function inputFormat($str){
        $str = @$_POST[$str];
        return str_replace( '\r\n','\n',trim($str));
    }

    /* 修改发布的作业状态信息(删除-2,有异常-1，保存0，正式发布1) */
    private static function alterJobState( $Conn, $value, $uId, $jId  ){
        $datas_job = array(
            'jIsPub' => $value,  // 是否发布/删除等
        );
        if(  !$Conn->update('job', $datas_job, ' tId='.$uId.' and  jId='.$jId ) ){
            die('0:数据库出错，请联系管理员#-1');
        }
    }

    /* 修改提交的作业状态信息(运行错误-1，待编译0，运行正确1) */
    private static function alterJobSubmitState( $Conn, $value, $id  ){
        $data_job = array(
            'jResult' => $value,  // 是否运行正确
        );
        if(  !$Conn->update('job_submit', $data_job, ' sId='.$id ) ){
            die('0:数据库出错，请联系管理员#-1');
        }
    }

    /* 比较时间 */
    private static  function compareTime($time){
        $a = strtotime($time) - time();
        if($a > 0){ // 大于当前时间
            return 1;
        }else if($a < 0){ // 小于当前时间
            return -1;
        }
        return 0;
    }

}