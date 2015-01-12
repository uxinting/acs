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
    <div style="width: 400px; margin: 50px auto">
        <form class="bs-example bs-example-form" role="form">
            <div class="input-group" style="width: 100%">
                根据邮箱，找回密码&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style="float: right"> <a href="index.php">返回CTest首页</a> </div>
            </div>
            <br>
            <br>
            <div class="input-group">
                <span class="input-group-addon">帐号</span>
                <input type="text" name="userNo" class="form-control" placeholder="例如：U201501019999">
            </div>
            <br>
            <div class="input-group">
                <span class="input-group-addon">邮箱</span>
                <input type="text" name="userEmail" class="form-control" placeholder="例如：123456@163.com">
            </div>
            <br>
            <div id="result" style="width: 400px; margin: 0 auto 5px auto; font-size: 12px; color: green;"></div>
            <div class="input-group" style="width: 400px; margin: 0 auto">
                <input type="button" id="submit" onclick="FindPassword()" class="form-control" value="找回密码" />
            </div>
        </form>
    </div>
    <script>
        function FindPassword(){
            var uNo = $('input[name="userNo"]').val();
            var uEmail = $('input[name="userEmail"]').val();
            if(uNo=='' || uEmail==''){
                alert("帐号或邮箱输入不能为空!")
                return false;
            }
            $.ajax({
                type: "POST",
                url: "api/doUser.php",
                data: { type: 'findPsw', uNo: uNo, uEmail:uEmail } ,
                beforeSend: function(XMLHttpRequest){
                    $('#submit').val("正在提交...");
                    $('#submit').attr("disabled","disabled");
                },
                success: function(data){
                    $('#submit').val("已申请过了");
                    $('#result').text(data.split(':')[1]);
                },
                error: function(){//请求出错处理
                    $('#submit').val("重新提交");
                    $('#submit').removeAttr("disabled","");
                }
            });
            return false;
        }
    </script>
    </body>
</html>