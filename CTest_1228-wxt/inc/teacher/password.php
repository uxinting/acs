<form role="form" class="form-horizontal col-sm-offset-2">
    <div class="form-group">
        <label for="oldpsw" class="col-sm-2 control-label">旧密码</label>
        <div class="col-sm-5">
            <input type="password" class="form-control" id="oldpsw" placeholder="Old Password" required="true"/>
        </div>
    </div>
    <div class="form-group">
        <label for="newpsw1" class="col-sm-2 control-label">新密码</label>
        <div class="col-sm-5">
            <input type="password" class="form-control" id="newpsw1" placeholder="New Password" required="true"/>
        </div>
    </div>
    <div class="form-group">
        <label for="newpsw2" class="control-label col-sm-2">新密码</label>
        <div class="col-sm-5">
            <input type="password" class="form-control" id="newpsw2" placeholder="New Password" required="true"/>
        </div>
        <span id="errorMsg" class="help-block"></span>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-5">
            <button type="button"  class="btn btn-primary" onclick="ChangePsw()">确定</button>
            <!--<button type="submit" class="btn btn-primary" onclick="ChangePsw();">确定</button>-->
        </div>
    </div>
</form>
<script>
function ChangePsw(){
    var oldpsw = $('#oldpsw').val();
    var newpsw1 = $('#newpsw1').val();
    var newpsw2 = $('#newpsw2').val();
    if(oldpsw=='' || newpsw1=='' || newpsw2==''){
        alert('信息填写不完整!');
    }
    
    //验证两次密码是否一致
    if(newpsw1!=newpsw2){
        $('#newpsw1').parents('.form-group').addClass('has-error');
        $('#newpsw2').parents('.form-group').addClass('has-error');
        $('#errorMsg').text('两次密码不一致!');
        $('#newpsw1').val('');
        $('#newpsw2').val('');
        return ;
    }else{
        $('#newpsw1').parents('.form-group').removeClass('has-error');
        $('#newpsw2').parents('.form-group').removeClass('has-error');
    }
    //密码用md5加密
    var oldpsw = $.md5(oldpsw);
    var newpsw = $.md5(newpsw1);
    $.ajax({
            type: "POST",
            url: "api/doTeacher.php",
            data: {type: 'changepsw', oldpsw:oldpsw, newpsw:newpsw} ,
            beforeSend: function(XMLHttpRequest){
                $('.btn-primary').text('正在提交...');
                $('.btn-primary').attr('disabled','disabled');
            },
            success: function(data){
                var str=data.toString();
                $('#oldpsw').val('');
                $('#newpsw1').val('');
                $('#newpsw2').val('');
                $('.btn-primary').text('确定');
                $('.btn-primary').attr('disabled','');
                alert(str.substring(1));
                return false;
            },
            error: function(){//请求出错处理
                $('#oldpsw').val('');
                $('#newpsw1').val('');
                $('#newpsw2').val('');
                $('.btn-primary').text('确定');
                $('.btn-primary').attr('disabled','');
                alert('系统故障，请联系管理员!');
                return false;
            }
    });
}
</script>