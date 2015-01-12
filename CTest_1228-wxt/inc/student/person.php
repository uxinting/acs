<?php
    require_once '../../lib/cfg.inc.php';
    @$uNo =$_COOKIE['uNo'];
    //获取该学生的基本信息
    $sql_student_info='select * from student where sNo="'.$uNo.'" limit 0,1 ';
    $res_student_info=$Conn->query($sql_student_info);
    $row_student_info=$res_student_info->fetch_array();
?>
<form role="form" class="form-horizontal col-sm-offset-2">
    <div class="form-group">
        <label for="recvname" class="col-sm-2 control-label">名字</label>
        <div class="col-sm-5">
            <input type="name" class="form-control" id="recvname" placeholder="name" required="true" value="<?php echo $row_student_info['sName']; ?>" disabled="disabled">
        </div>
    </div>
    <div class="form-group">
        <label for="recvsex" class="col-sm-2 control-label">性别</label>
        <div class="col-sm-5">
            <input type="sex" class="form-control" id="recvsex" placeholder="sex" required="true" value="<?php if(intval($row_student_info['sSex'])==1) echo '男'; else echo '女'; ?>" disabled="disabled">
        </div>
    </div>
    <div class="form-group">
        <label for="recvemail" class="col-sm-2 control-label">邮箱</label>
        <div class="col-sm-5">
            <input type="email" class="form-control" id="recvemail" placeholder="Email" required="true" value="<?php echo $row_student_info['sEmail']; ?>" disabled="disabled">
        </div>
    </div>
    <div class="form-group">
        <label for="recvaddr" class="col-sm-2 control-label">地址</label>
        <div class="col-sm-5">
            <input type="addr" class="form-control" id="recvaddr" placeholder="addr" required="true" value="<?php echo $row_student_info['sAddr']; ?>" disabled="disabled">
        </div>
    </div>
    <div class="form-group">
        <label for="recvtel" class="col-sm-2 control-label">电话</label>
        <div class="col-sm-5">
            <input type="tel" class="form-control" id="recvtel" placeholder="tel" required="true" value="<?php echo $row_student_info['sTel']; ?>" disabled="disabled">
        </div>
    </div>
    <div class="form-group">
        <label for="recvqq" class="col-sm-2 control-label">QQ</label>
        <div class="col-sm-5">
            <input type="qq" class="form-control" id="recvqq" placeholder="qq" required="true" value="<?php echo $row_student_info['sQQ']; ?>" disabled="disabled">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-5">
            <button type="button" id="btn_person" onclick="ChangeEmail()" class="btn btn-primary">修改</button>
        </div>
    </div>
</form>
<script>
function ChangeEmail(){
    var btntext=$('#btn_person').text();
    if(btntext=='修改'){
        $('#btn_person').text('提交');
        $('#recvname').removeAttr('disabled');
        $('#recvsex').removeAttr('disabled');
        $('#recvemail').removeAttr('disabled');
        $('#recvaddr').removeAttr('disabled');
        $('#recvtel').removeAttr('disabled');
        $('#recvqq').removeAttr('disabled');
        return false;
    }else{
        $('#btn_person').text('修改');
        $('#recvname').attr('disabled','disabled');
        $('#recvsex').attr('disabled','disabled');
        $('#recvemail').attr('disabled','disabled');
        $('#recvaddr').attr('disabled','disabled');
        $('#recvtel').attr('disabled','disabled');
        $('#recvqq').attr('disabled','disabled');
    }
    var Name=$('#recvname').val();
    var Sex=$('#recvsex').val();
    var Email=$('#recvemail').val();
    var Addr=$('#recvaddr').val();
    var Tel=$('#recvtel').val();
    var QQ=$('#recvqq').val();
    if(Sex=='女'){
        Sex=0;
    }else{
        Sex=1;
    }
    $.ajax({
            type: "POST",
            url: "api/doStudent.php",
            data: {type: 'changeinfo', Name:Name, Sex:Sex, Email:Email, Addr:Addr, Tel:Tel, QQ:QQ} ,
            beforeSend: function(XMLHttpRequest){
            },
            success: function(data){  
                var str=data.toString();
                alert(str.split(':')[1]);
            },
            error: function(){//请求出错处理
                alert('系统故障，请联系管理员!');
            }
    });
}
</script>