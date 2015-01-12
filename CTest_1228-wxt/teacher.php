<!DOCTYPE HTML>
<html>
    <head>
        <meta charset=utf-8>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Teacher</title>

        <link href="ex/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/teacher.css" rel="stylesheet">
        <script src="ex/52framework/js/jquery.min.js"></script>
        <script src="ex/dist/js/bootstrap.min.js"></script>
        <script src="js/jquery.md5.js"></script>
        <style></style>
    </head>
<?php
    require_once 'lib/cfg.inc.php';
    $uNo =@$_COOKIE['uNo'];
    $author = new Author($Conn);
/*if(@$_GET['exit']==1 ){
    $author->StudentExit();
}
$author->TeacherIsLogin(); */
?>
    <body class="container">
        <header class="row">
            <div class="col-md-10">
                <h1><span style="color: red; font: italic bold 55px arial;">C</span>语言作业提交系统</h1>
            </div>
            <div class="col-md-2">
                <a href="?exit=1" class="btn btn-default btn-lg log-out">
                    退出
                    <span class="glyphicon glyphicon-log-out"></span>
                </a>
            </div>
        </header>
        <div class="row">
            <div class="col-md-3">
                <?php include('inc/teacher/control.php'); ?>
            </div>
            
            <div class="col-md-9 tab-content" id="AjaxBody">

                <div id="jobs-all" class="jobs tab-pane fade in active" role="tabpanel">
                </div>

                <div id="jobs-del" class="jobs tab-pane fade in" role="tabpanel">
                </div>

                <div id="jobs-pub" class="jobs tab-pane fade in" role="tabpanel">
                </div>

                <div id="jobs-notpub" class="jobs tab-pane fade in" role="tabpanel">
                </div>

                <div id="newjob" class="tab-pane fade in" role="tabpanel">
                </div>

                <div id="password" class="tab-pane fade in" role="tabpanel">
                </div>

                <div id="person" class="tab-pane fade in" role="tabpanel">
                </div>

                <div id="log-job" class="tab-pane fade" role="tabpanel">
                </div>

                <div id="log-login" class="tab-pane fade in" role="tabpanel">
                </div>

                <div id="msg" class="tab-pane fade in" role="tabpanel">
                </div>

            </div>
            
        </div>
        
        <footer>
            <div class="container">
                <div class="col-md-6"> &copy;2006-2014 华中科技大学C语言作业提交&nbsp;湖北省武汉市洪山区珞喻路1037号</div>
                <div class="col-md-6">邮政编码：430074 电话：027-87540101  信箱：hubsupport@mail.hust.edu.cn</div>
            </div>
        </footer>

        <script>
            //Retrive Panel Status
            function retrivePanel() {
                var id = $.cookie( 'active' );
                if ( id == undefined ) id = '#job-all';
                $(id).addClass( 'active in' );
                $('a[href="'+id+'"]').parents('.collapse').collapse('show');
                
                var content = $.cookie( 'content' );
                if ( content != "" ) {
                    $('li>a[href="'+id+'"][data-content="'+content+'"]').parent().addClass('active');
                } else {
                    $('li>a[href="'+id+'"]').parent().addClass('active');
                }
            }
            
            //Save Panel Status
            function savePanel( ele ) {
                $.cookie("active", $(ele).attr( 'href' ));
                var content = $(ele).data( 'content' );
                if ( content == undefined )
                    $.cookie("content", "");
                else
                    $.cookie("content", content);
            }
            retrivePanel();
            $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
                $('a[data-toggle="tab"]').parent().removeClass("active");
                savePanel( this );
            });
            //Close Last Collapse
            $('.panel-title').click( function(e) {
                $('.collapse.in').collapse( 'hide' );
            } );
            $('#commit').on('show.bs.modal', function (e) {
                var id = $(e.relatedTarget).parents( '.list-group-item' ).find( '[role="tabpanel"]' ).attr( 'id' );
                $('#jobID').val( id );
            })

            $('a[href="#msg"]').on('shown.bs.tab', function (e) {
                $(e.target).find('.badge').hide();
            })

            $('blockquote.bg-warning').click( function (e) {
                $(this).removeClass('bg-warning');
            } )

            $('#password form').submit(function(e) {
                var p1 = $('#passwd1').val();
                var p2 = $('#passwd2').val();
                if ( p1 == p2 ) {
                    $('#passwd1').parents('.form-group').removeClass('has-error');
                    $('#passwd2').parents('.form-group').removeClass('has-error');
                    if ( p1.length < 8 ) {
                        $('#errorMsg').text('新密码小于8位');
                    }
                } else {
                    $('#passwd1').parents('.form-group').addClass('has-error');
                    $('#passwd2').parents('.form-group').addClass('has-error');
                    $('#errorMsg').text('两次密码不一致');
                }
            })
        </script>
    </body>
</html>