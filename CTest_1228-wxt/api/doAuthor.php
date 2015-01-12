<?php
    ob_start();
    require_once '../lib/cfg.inc.php';
     
    //1:学生；2:教师；3:管理员
    $type=$_POST["xtype"];
    $uNo=$_POST["xName"];
    $uPsw=$_POST["xPsw"];
    
    //$tbName, $No, $Psw, $Email, $Name, $Flag, $Ip_Login, $Time_Login, $Cookie
    $author = new Author($Conn);
    $tbName='studetn';
    $Name='sName';
    $Id='sId';
    $Cookie='sCookie';
    $Email = 'sEmail';
    $No = 'sNo';
    $Psw= 'sPsw';
    $Flag = 'sflag';
    $Ip_Login = 'sIp_Login';
    $Time_Login = 'sTime_Login';
    switch($type){
        case '-1'://$tbName, $No, $Name, $Flag, $Ip_Login, $Time_Login;
        //$author->ExitLogin();
        break;
        case '1' :
            $tbName = 'student';
            $Name='sName';
            $Id='sId';
            $Cookie='sCookie';
            $Email = 'sEmail';
            $No = 'sNo';
            $Psw= 'sPsw';
            $Flag = 'sflag';
            $Ip_Login = 'sIp_Login';
            $Time_Login = 'sTime_Login';
        break;
        
        case '2' :
            $tbName = 'teacher';
            $Name='tName';
            $Id='tId';
            $Cookie='tCookie';
            $Email = 'tEmail';
            $No = 'tNo';
            $Psw= 'tPsw';
            $Flag = 'tflag';
            $Ip_Login = 'tIp_Login';
            $Time_Login = 'tTime_Login';
        break;
        
        case '3' :
            $tbName = 'admin';
            $Name='aLogName';
            $Id='aId';
            $Cookie='aCookie';
            $Email = 'aEmail';
            $No = 'alogName';
            $Psw= 'aPsw';
            $Flag = '1';
            $Ip_Login = 'aIp_Login';
            $Time_Login = 'aTime_Login';
            $Psw = 'aPsw';
         break;
    }
    //$tbName, $No, $Psw, $Email, $Name, $Flag, $Ip_Login, $Time_Login, $Cookie
    if($author->Login($tbName, $Id, $No, $Psw, $Email, $Name, $Flag, $Ip_Login, $Time_Login, $Cookie, $uNo, $uPsw ))
    {
        echo $type.':登录成功!';
    }else{
        echo '0:登录失败!';
    }

    ob_end_flush();
?>