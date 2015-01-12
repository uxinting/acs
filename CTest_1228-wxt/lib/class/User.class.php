<?php
    ob_start();
   

class User {
    
    private $Conn;
    public function __construct($Conn) {
        $this->Conn = $Conn;
 	}


    /* 导入学生信息 */
    public function ImportStudents($filename)
    {
        include_once("lib/tool/excel_in_out/ExcelInOut.php");
        $my = new ExcelInOut();
        $my->studentToDB($filename);
    }
    
    
    /*导出学生信息*/
    public function ExportStudents($filename)
    {
        include_once("lib/tool/excel_in_out/ExcelInOut.php");
        $my = new ExcelInOut();
        $my->studentToExcel($filename);
    }  
    
    
    /*删除学生*/
    public function DeleteStudent($sNo)
    {
        $sql = 'delete from Student where sNo = ' . $sNo;
        $res = $this->Conn->query($sql);
        if(!$res)
        {
            die('0:数据库操作异常！');
        }
    }
    
    
    /*强制学生下线COOkie*/
    public function ForceStudentLogout($sNo)
    {
        $sql = 'update Student set sCookie = "" where sNo = ' . $sNo;
        $res = $this->Conn->query($sql);
        if(!$res)
        {
            die('0:数据库操作异常！');
        }
    }
      
       
    /*禁止学生帐号*/
    public function BanStudent($sNo)
    {
        $sql = 'update Student set sflag = 0 where sNo = ' . $sNo;
        $res = $this->Conn->query($sql);
        if(!$res)
        {
            die('0:数据库操作异常！');
        }
    }
    
    
    /* 导入老师信息 */
    public function ImportTeachers($filename)
    {
        include_once("lib/tool/excel_in_out/ExcelInOut.php");
        $my = new ExcelInOut();
        $my->teacherToDB($filename);
    }
    
    
    /*导出老师信息*/
    public function ExportTeachers($filename)
    {
        include_once("lib/tool/excel_in_out/ExcelInOut.php");
        $my = new ExcelInOut();
        $my->teacherToExcel($filename);
    }  
    
    
    /*删除老师*/
    public function DeleteTeacher($tNo)
    {
        $sql = 'delete from Teacher where tNo = ' . $tNo;
        $res = $this->Conn->query($sql);
        if(!$res)
        {
            die('0:数据库操作异常！');
        }
    }      
    
    
    /*强制老师下线COOkie*/
    public function ForceTeacherLogout($tNo)
    {
        $sql = 'update Teacher set tCookie = "" where tNo = ' . $tNo;
        $res = $this->Conn->query($sql);
        if(!$res)
        {
            die('0:数据库操作异常！');
        }
    }
       
       
    /*禁止老师帐号*/
    public function BanTeacher($tNo)
    {
        $sql = 'update Teacher set tflag = 0 where tNo = ' . $tNo;
        $res = $this->Conn->query($sql);
        if(!$res)
        {
            die('0:数据库操作异常！');
        }
    }

    /* 找回密码 */
    public function FindPasswod(){
        $uNo = trim( @$_POST["uNo"] );
        $uEmail = trim( @$_POST["uEmail"] );
        if( $uNo!='' && $uEmail!='' ){
            // 如果既不是学生也不是教师，则认为该用户不合法
            if( !$this->isRightUser('student', 'sEmail', $uEmail, 'sNo', $uNo)
                && !$this->isRightUser('teacher', 'tEmail', $uEmail, 'tNo', $uNo) ){
                die('1:该用户不存在，不能申请！');
            }
        }else{
            die('1:必要输入不能为空');
        }
        $ufCode = null;
        for($i=0;$i<9;$i++){
            $ufCode .= mt_rand(1,9);
        }
        $now = date("Y-m-d H:i:s");
        $ip = new ClientInfo();
        $ip_login = $ip->GetIp();

        $datas_findPsw = array(
            'uNo'    =>  $uNo,
            'uEmail'    =>  $uEmail,
            'ufCode'    =>  $ufCode,
            'ufTime'    =>  $now,
            'ufIp'    =>  $ip_login,
        );
        if( $this->Conn->insert('user_findPsw', $datas_findPsw) ){
            // 发送邮件
            include("Mailer.class.php");
            $mailer = new Mailer();
            $href = 'http://115.156.155.115/CTest_1228/reset.php?u='.$uNo.'&e='.$uEmail.'&c='.$ufCode;
            $topicName = 'CTest重置密码';
            $content = 'CTest重置密码链接，本链接有效期仅10分钟，10分钟内可多次重置密码，请将该地址复制到浏览器地址栏，'.$href.'，为保证您帐号的安全性，请勿泄漏！';
            $mailer->send($uEmail, $topicName , $content);
            die('0:申请成功，我们已将邮件发送至您的邮箱，如1分钟内未收到，请再次访问本页面，重新申请！');
        }else{
            die('1:申请失败，请稍后再来重试...');
        }
    }

    /* 根据邮箱验证重置密码 */
    public function ResetPasswodByEmail($uNo, $uEmail, $ufCode){
        $sql = 'select uEmail, ufTime, ufCode from user_findPsw where uNo="'.$uNo.'" order by ufId desc';
        //echo $sql;
        $res = $this->Conn->query($sql);
        if($res){
            $row = $res->fetch_array();
            if( $row['uEmail']==$uEmail && $row['ufCode']==$ufCode ){
                $now = date("Y-m-d H:i:s");
                $second = floor((strtotime($now)-strtotime($row['ufTime']))%86400/60);
                if( $second>=10 ){
                    die('1:已过期，失效的链接');
                }
                // 根据是教师还是学生重置密码为123456
                if( $this->isRightUser('student', 'sEmail', $uEmail, 'sNo', $uNo) ){
                    $sql_update = 'update student set sPsw="e10adc3949ba59abbe56e057f20f883e" where sNo="'.$uNo.'"';
                    //echo ' '.$sql_update.' ';
                    if( $this->Conn->query($sql_update) ){
                        die('0:'.$uNo.'：已重置您的密码为123456，前往<a href="index.php">CTest</a>重新登录！');
                    }
                }else if( $this->isRightUser('teacher', 'tEmail', $uEmail, 'tNo', $uNo) ){
                    $sql_update = 'update teacher set tPsw="e10adc3949ba59abbe56e057f20f883e" where tNo="'.$uNo.'"';
                    if( $this->Conn->query($sql_update) ){
                        die('0:'.$uNo.'：已重置您的密码为123456');
                    }
                }
                die('1:系统故障或邮箱可能已被修改#2');
            }
            die('1:请勿非法操作#1');
        }else{
            die('1:系统故障#1');
        }
    }

    /* 判断是否是合法用户
    *  $tbName表名; $UNo字段名字; $uNo字段值
     */
    private function isUser( $tbName, $UNo, $uNo ){
        $sql = 'select * from '.$tbName.' where '.$UNo.'="'.$uNo.'" limit 0 ,1';
        $res = $this->Conn->query($sql);
        if( $res ){
            $row = $res->fetch_array();
            if( $row[$UNo]=='' ){
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }

    /* 判断是否是正确的用户（根据邮箱和帐号）
    *  $tbName表名; $UNo字段名字; $uNo字段值
     */
    private function isRightUser( $tbName, $UEmail, $uEmail, $UNo, $uNo ){
        $sql = 'select '.$UEmail.' email from '.$tbName.' where '.$UNo.'="'.$uNo.'" limit 0 ,1';
        // echo $sql;
        $res = $this->Conn->query($sql);
        if( $res ){
            $row = $res->fetch_array();
            if( $row['email']!=$uEmail ){
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }

}