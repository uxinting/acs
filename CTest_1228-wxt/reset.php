<!DOCTYPE HTML>
<html>
    <head>
        <meta charset=utf-8>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>根据邮箱，找回密码</title>
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
    $uNo = @$_GET['u'];
    $uEmail = @$_GET['e'];
    $ufCode = @$_GET['c'];
    if($uNo!='' && $uEmail!='' && $ufCode!=''){
        include('lib/cfg.inc.php');
        include('lib/class/User.class.php');
        $user = new User($Conn);
        $user->ResetPasswodByEmail( $uNo, $uEmail, $ufCode );
    }else{
        die('1:非法操作！');
    }
   ?>
    </body>
</html>