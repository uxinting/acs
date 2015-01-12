<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>Welcome</title> 
        <link rel="stylesheet" type="text/css" href="ex/52framework/css/reset.css" media="screen"/>
        <link rel="stylesheet" type="text/css" href="ex/52framework/css/css3.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="ex/52framework/css/general.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="ex/52framework/css/grid.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="ex/52framework/css/forms.css" media="screen" />
        <link href="css/index.css" rel="stylesheet"/> 
        <script src="ex/52framework/js/jquery.js"></script>
        <script src="ex/52framework/js/modernizr-1.7.min.js"></script>
        <script src="js/jquery.md5.js"></script>
    </head>
    <body>
    <?php
        $u = @$_COOKIE['u'];
        if($u=='0'){
            die( '<script>window.location="student.php";</script>');
        }else if($u=='1'){
            die( '<script>window.location="teacher.php";</script>');
        }else if($u=='2'){
            die( '<script>window.location="admin.php";</script>');
        }
    ?>
        <section class="row">
            <div id="logo" class="col_9 col">
                <div class="information">
                     <h4>最新通知</h4>
                    <a>C语言作业自动验收和提交系统拟在2014年1月11日前完成系统原型，现有功能应都能正确运行，
                    满足学生作业提交、教师布置作业、自动批阅作业、自动将作业打包发给指定教师。最后截止日期2014年12月31。</a>
                </div>
            </div>
            <div class="col_1 col">&nbsp;</div>
            <div class="col_6 col">
                <h1>C语言作业提交系统</h1>
                <div class="row">
                    <form class="col col_6">
                        <fieldset class="s_column">
                            <div id="type">
                                <input  id="stu" type="radio" checked="true" name="type" value="1"/> 学生
                                <input type="radio" name="type" value="2"/> 教师
                                <input type="radio" name="type" value="3"/> 管理员
                            </div>
                            <div>
                                <label>用户名</label>
                                <input type="text" name="userName" required="required" class="box_shadow"/>
                            </div>    
                            <div>
                                <label>密码</label>
                                <input type="password" name="password" required="required" class="box_shadow"/>
                            </div>
                            <input name="submit" type="button" onclick="Login()" value="登录 &rarr;" /><div id="result"></div>
                        </fieldset>
                    </form>
                </div>
                <a href="forget.php">忘记密码？</a>
            </div>
        </section>

        <footer class="row">
            <div class="col_8 col"> &copy;2006-2014 华中科技大学C语言作业提交&nbsp;湖北省武汉市洪山区珞喻路1037号</div>
            <div class="col_8 col">邮政编码：430074 电话：027-87540101  信箱：hubsupport@mail.hust.edu.cn</div>
        </footer>
    </body>
</html>
<script>
function Login(){
    var type='';
    var radios=document.getElementsByName("type");
    for(var i=0; i<radios.length; i++){
        if(radios[i].checked){
            type=radios[i].value;
            break;
        }
    }
    var uName=$('input[name="userName"]').val();
    var uPsw=$('input[name="password"]').val();
    if(uName=='' || uPsw==''){
        $('#result').html("请输入用户名和密码!");
        return false;
    }
    //使用ajax发送用户名和密码
    uPsw=$.md5(uPsw);
    $.ajax({
        type: "POST",
        url: "api/doAuthor.php",
        data: {xtype: type, xName: uName, xPsw:uPsw} ,
        beforeSend: function(XMLHttpRequest){
           $('input[name="submit"]').val("正在登录...");
           $('input[name="submit"]').attr("disabled","disabled");
        },
        success: function(data){
            //$('#result').html(data);
            var str=data.toString();
            if(str.indexOf("0")>=0){//登录失败
                $('#result').html(str.split(':')[1]);
            }else{ //登录成功\
               switch(str.substring(0,1)){
                case '1': window.location='student.php?type=1';break;
                case '2': window.location='teacher.php?type=2';break;
                case '3': window.location='admin.php?type=3';break;
               }
            }
            $('input[name="password"]').val('');
            $('input[name="submit"]').val("重新登录");
            $('input[name="submit"]').removeAttr("disabled");
        },
        error: function(){//请求出错处理
           $('input[name="submit"]').val("重新登录");
           $('input[name="submit"]').removeAttr("disabled");
           $('#result').html("系统错误，请联系管理员!");
        }
    });
    return false;
}
</script>