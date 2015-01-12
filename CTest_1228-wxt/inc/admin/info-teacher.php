<div><a class="btn btn-default" href="download.html?f=1.zip" target="_blank">导出教师</a>
    <div class="import btn btn-default">
        导入教师(.xls)
        <input type="file" >
    </div>
</div>

<table class="table table-striped" id="info_teacher">
    <thead>
    <tr>
        <th>工号</th>
        <th>姓名</th>
        <th>学院</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<script>
function GetTeacherInfo(){
    $.ajax({
        type: "Post",
        url: "api/doAdmin.php",
        data: {type: 'info_teacher'},
        beforeSend: function(XMLHttpRequest){
            $('table#info_teacher tbody').html('正在加载...');
        },
        success: function(data){
            $('table#info_teacher tbody').html(data);
            //alert(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
            $('table#info_teacher tbody').html(data);
            alert('连接出错');
        }
    });
} 

GetTeacherInfo();
</script>