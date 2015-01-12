<?php
    ob_start();
    include_once('../lib/cfg.inc.php');
    include_once('../lib/class/Jober.class.php');
    include_once('../lib/class/CTestFile.class.php');
    include_once('../lib/class/CodeChecker.class.php');
    $type=$_POST["type"];

    // 检查用户是否登录
    $author = new Author($Conn);
    switch($type){
        case 'pub': // 发布到待发布
            $author->TeacherIsLogin();
            Jober::Pub($Conn);
            break;
        case 'pubNot': // 发布到待发布
            $author->TeacherIsLogin();
            Jober::PubNot($Conn);
            break;
        case 'pubC': // 正式发布
            $author->TeacherIsLogin();
            Jober::PubC($Conn);
            break;
        case 'edit': // 编辑作业
            $author->TeacherIsLogin();
            Jober::Edit($Conn);
            break;
        case 'del': // 删除作业
            $author->TeacherIsLogin();
            Jober::Del($Conn);
            break;
        case 'delC': // 完全删除作业
            $author->TeacherIsLogin();
            Jober::DelC($Conn);
            break;
        case 'sub': // 提交作业
            $author->StudentOrTeahcerIsLogin();
            Jober::Sub($Conn);
            break;
        default:
            die('0:非法请求');
    }


?>