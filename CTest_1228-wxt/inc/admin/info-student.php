<div>
    <a class="btn btn-default" href="download.html?f=1.zip" target="_blank">导出学生</a>
    <div class="import btn btn-default">
        导入学生(.xls)
        <input type="file" >
    </div>
</div>

<table class="table table-striped" id="info_student">
    <thead>
    <tr>
        <th>学号</th>
        <th>姓名</th>
        <th>班级</th>
        <th>学院</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<script>
function GetStudentInfo(){
    $.ajax({
        type: "Post",
        url: "api/doAdmin.php",
        data: {type: 'info_student'},
        beforeSend: function(XMLHttpRequest){
            $('table#info_student tbody').html('正在加载...');
        },
        success: function(data){
            $('table#info_student tbody').html(data);
            //alert(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            $('table#info_student tbody').html(data);
            alert('连接出错');
        }
    });
} 

GetStudentInfo();
</script>