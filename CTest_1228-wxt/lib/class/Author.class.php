<?php
    ob_start();
   
/*
* 应用程序类
* 1、基本信息说明
* 2、应用程序配置
*/
class Author {
    private $Conn;
    public function __construct($Conn) {
        $this->Conn = $Conn;
 	}

    /* 检查教师是否登录 */
    public function TeacherIsLogin(){
        $uNo = @$_COOKIE['uNo'];
        if( !$this->IsLogin('teacher','tName','tId','tCookie','tEmail','tNo',$uNo) ){
            $this->clearCookie();
            die('0:未登录或45分钟未进行任何操作，<a href="index.php">请重新登录</a>!');
        }
        setcookie("u",'1',time()+2700, '/');
    }

    /* 检查学生是否登录 */
    public function StudentIsLogin(){
        $uNo = @$_COOKIE['uNo'];
        if( !$this->IsLogin('student','sName','sId','sCookie','sEmail','sNo',$uNo) ){
            $this->clearCookie();
            die('0:未登录或45分钟未进行任何操作，<a href="index.php">请重新登录</a>!');
        }
        setcookie("u",'0',time()+2700, '/');
    }

    /* 检查教师或者学生是否登录 */
    public function StudentOrTeahcerIsLogin(){
        $uNo = @$_COOKIE['uNo'];
        if( $this->IsLogin('student','sName','sId','sCookie','sEmail','sNo',$uNo)
            || $this->IsLogin('teacher','tName','tId','tCookie','tEmail','tNo',$uNo)){    
        }else{
            $this->clearCookie();
            die('0:未登录或45分钟未进行任何操作，<a href="index.php">请重新登录</a>!');
        }
    }

    /* 检查管理员是否登录 */
    public function AdminIsLogin(){
        $uNo = @$_COOKIE['uNo'];
        if(!$this->IsLogin('admin','aName','aId','aCookie','aEmail','alogName',$uNo)){
            $this->clearCookie();
            die('0:未登录或45分钟未进行任何操作，<a href="index.php">请重新登录</a>!');
        }
        setcookie("u",'2',time()+2700, '/');
    }

    /* 学生退出 */
    public function StudentExit(){
        $this->clearCookie();
        die ('退出成功，正在跳转...<script>window.location="index.php";</script>');
    }

    /* 教师退出 */
    public function TeacherExit(){
        $this->clearCookie();
        die ('退出成功，正在跳转...<script>window.location="index.php";</script>');
    }

    /* 管理员退出 */
    public function AdminExit(){
        $this->clearCookie();
        die ('退出成功，正在跳转...<script>window.location="index.php";</script>');
    }
    private function clearCookie(){
        setcookie("uName","",time()-3600, '/');
        setcookie("uId","",time()-3600, '/');
        setcookie("uNo","",time()-3600, '/');
        setcookie("uEmail","",time()-3600, '/');
        setcookie("uCookie","",time()-3600, '/');
        setcookie("u",'',time()-3600, '/');
    }

    /*检查用户是否登录*/
    private function IsLogin( $tbName, $Name, $Id, $Cookie, $Email, $No, $uNo ){
        /*
        $datas_user = array(
            $No => 'uNo',
            $Name => 'uName',
            $Id => 'uId',
            $Cookie => 'uCookie',
            $Email =>'uEmail'
        );
        $res_user=$Conn->queryEx($tbName, $datas_user, $No.'='.$uNo.'  limit 0, 1' );
        */
        if($uNo==''){
            return false;
        }
        $sql_user='select '.$Name.' uName, '.$Id.' uId, '.$Cookie.' uCookie, '.$Email.' uEmail, '.$No.' uNo from '.$tbName.' where '.$No.'="'.$uNo.'"';
        $res_user=$this->Conn->query($sql_user);
        $row_user=$res_user->fetch_array();
        if($row_user['uCookie']!=@$_COOKIE["uCookie"]){
            return false;
        }
        setcookie("uNo",$row_user['uNo'],time()+2700, '/');
        setcookie("uId",$row_user['uId'],time()+2700, '/');
        setcookie("uEmail",$row_user['uEmail'], time()+2700, '/');
        setcookie("uName",$row_user['uName'],time()+2700, '/');
        return true;
    }
    
    /*用户登录*/
    public function Login($tbName, $Id, $No, $Psw, $Email, $Name, $Flag, $Ip_Login, $Time_Login, $Cookie, $uNo, $uPsw){
             
        $sql_user='select '.$Id.' uId, '.$No.' uNo, '.$Psw.' uPsw, '.$Email.' uEmail, '.$Name
                  .' uName, '.$Flag.' uflag from '.$tbName.' where '.$No.'="'.$uNo.'" limit 0, 1';  
        $res_user=$this->Conn->query($sql_user);
        $row_user=$res_user->fetch_array();
        if($row_user['uPsw']!=$uPsw){
            return false;
        }
        $uCookie=md5($row_user['uId'].$uNo.$row_user['uName'].$uPsw.rand(1000,9999).date("Ymd-His"));
        setcookie("uNo",$row_user['uNo'],time()+2700, '/');
        setcookie("uId",$row_user['uId'],time()+2700, '/');
        setcookie("uEmail",$row_user['uEmail'], time()+2700, '/');
        setcookie("uName",$row_user['uName'],time()+2700, '/');
        setcookie("uCookie",$uCookie, time()+2700, '/');

        //将登陆信息写入登陆成功日志表
        $now=date('Y-m-d H:i:s');
        $ip=new ClientInfo();
        $ip_login=$ip->GetIp();
        
        $datas_student = array(
            $Ip_Login => $ip_login, 
            $Time_Login => $now,
            $Cookie => $uCookie
        );
        
        if( !$this->Conn->update($tbName, $datas_student, $No.'="'.$uNo.'"' ) ){
            return false;
        }
        if(intval($Flag)==1){//如果是管理员，单独插入管理员登录日志表
           $sql_log='insert into admin_login_log(aId, aIp_Login, aTime_Login, aflag) values("'.$row_user['uId'].'","'.$ip_login.'","'.$now.'","1")';
           $this->Conn->query($sql_log);
        }else{
            $datas_log = array(
                $No => $uNo,
                $Name => $row_user['uName'],
                $Ip_Login => $ip_login,
                $Time_Login => $now,
                $Flag => 1
            );
            $log = new Log();
            $log->insertTable($this->Conn, $tbName.'_login_log', $datas_log);
        }
        return true;
    }
    
    /*退出登录*/
    private function ExitLogin($tbName, $No, $uNo, $Name, $Flag, $Ip_Login, $Time_Login){
        setcookie("uName","",time()-3600);
        setcookie("uId","",time()-3600);
        setcookie("uNo","",time()-3600);
        setcookie("uEmail","",time()-3600);
        setcookie("uCookie","",time()-3600);
        
        $now=date('Y-m-d H:i:s');
        $ip=new ClientInfo();
        $ip_login=$ip->GetIp();
        
        $datas_log = array(
            $No => $uNo,
            $Name => $Name,
            $Ip_Login => $ip_login,
            $Time_Login => $now,
            $Flag => 0
        );
        
        $log = new Log();
        $log->insertTable($this->Conn, $tbName.'_login_log', $datas_log);
        die ('退出成功，正在跳转...<script>window.location="../../../../../../../Users/C/Desktop/index.php";</script>');
    }

}