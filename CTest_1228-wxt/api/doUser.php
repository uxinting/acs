<?php
    ob_start();
    include '../lib/cfg.inc.php';
    include '../lib/class/User.class.php';
    //1:学生；2:教师；3:管理员
    $type=$_POST["type"];
    $user = new User($Conn);
    switch($type){
        case 'findPsw' : // 提交申请修改密码
            $user->FindPasswod();
        break;

    }
?>