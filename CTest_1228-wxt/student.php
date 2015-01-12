<!DOCTYPE HTML>

<html>
    <head>
        <meta charset=utf-8>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>Student</title>
        <script src="ex/52framework/js/jquery.min.js"></script>
        <script src="ex/dist/js/bootstrap.min.js"></script>
        <script src="js/jquery.md5.js"></script>
        <link href="ex/dist/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="css/student.css" rel="stylesheet"/>
        <style>
        </style>
    </head>
    <body class="container">
<?php
    require 'lib/cfg.inc.php';
    $uNo = @$_COOKIE['uNo'];
    $author = new Author($Conn);
    if(@$_GET['exit']==1 ){
        $author->StudentExit();
    }
    $author->StudentIsLogin();
?>
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
                <?php include('inc/student/control.php'); ?>
            </div>
            
            <div class="col-md-9 tab-content">

                <div id="jobs-all" class="jobs tab-pane fade in" role="tabpanel">
                </div>

                <div id="jobs-pass" class="jobs tab-pane fade in" role="tabpanel">
                </div>

                <div id="jobs-notpass" class="jobs tab-pane fade in" role="tabpanel">
                </div>
                
                <div id="password" class="tab-pane fade" role="tabpanel">
                </div>

                <div id="person" class="tab-pane fade" role="tabpanel">
                </div>

                <div id="log-job" class="tab-pane fade" role="tabpanel">
                </div>

                <div id="log-login" class="tab-pane fade" role="tabpanel">
                </div>

                <div id="msg" class="tab-pane fade" role="tabpanel">
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
            
            function updatePagination( page ) {
                var jobs = $('#job .list-group-item.choose');
                var pages = Math.ceil( jobs.length / 6 ); //Every page contain 6 jobs
                if ( pages <= 1 ) {
                    $('#job nav').hide();
                } else {
                    $('#job nav').show();
                    $('#job nav ul li').remove( 'li:not(:first-child):not(:last-child)' );
                    var right = $('#job nav ul li:last-child');
                    if ( pages <= 6 ) { //Between 1~6 pages
                        for ( var i = 1; i <= pages; i++ ) {
                            $('<li><a href="#">'+i+'</a></li>').insertBefore( right );
                        }
                    } else { //more than 6 pages
                        for ( var i = 1; i <= pages / 2; i++ ) {
                            $('<li><a href="#">'+i+'</a></li>').insertBefore( right );
                        }
                        $('<li><a href="#">...</a></li>').insertBefore( right );
                        for ( var i = Math.ceil( pages/2 ); i <= pages; i++ ) {
                            $('<li><a href="#">'+i+'</a></li>').insertBefore( right );
                        }
                    }
                }
                if ( page )
                    changePage( 1 );
            }
            
            function updateWithFilter() {
                var stime = $('#flttime').text();
                var time = 0;
                if ( stime == '一月内' ) {
                } else if ( stime == '一周内' ) {
                } else {};
                
                var teacher = $('#fltteacher').text();
                var course = $('#fltcourse').text();
                var content = $('#jobcontrol li.active a').text();
                if ( content == '' ) content = '全部';
                
                $('#job>.list-group li:first-child').text( stime + ' ' + teacher + ' ' + course );
                
                var jobs = $('#job .list-group-item');
                for ( var i = 1; i < jobs.length; i++ ) {
                    var jtime = $(jobs[i]).find( 'p>span' ).text();
                    var jtchr = $(jobs[i]).find( 'p>em' ).text();
                    var jcors = $(jobs[i]).find( 'p>small' ).text();
                    var jcontent = $(jobs[i]).find( 'h4 span' ).text();
                    
                    if ( content != '全部' && content != jcontent ||
                         teacher != '全部教师' && teacher != jtchr ||
                         course != '全部科目' && course != jcors ) {
                        $(jobs[i]).hide().removeClass( 'choose' );
                    } else {
                        $(jobs[i]).show().addClass( 'choose' );
                    }
                }
                updatePagination( 1 );
            }
            
            updateWithFilter();
            retrivePanel();
	    
            $('#job nav ul').click( function (e) {
                var spage = $('li:hover a').text();
                var curpage = parseInt( $('#tools nav li.active a').text() );
                if ( spage == '«' ) {
                    curpage--;
                } else if ( spage == '»' ) {
                    curpage++;
                } else {
                    curpage = parseInt( spage );
                }
                changePage( curpage );
            } )
            
            $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
                $('a[data-toggle="tab"]').parent().removeClass("active");
                savePanel( this );
            });
            //Close Last Collapse
            $('.panel-title').click( function(e) {
                $('.collapse.in').collapse( 'hide' );
            } );
	    
            $('#tools ul[role="menu"] li').click( function (e) {
                var text = $(this).find( 'a' ).text();
                $(this).parentsUntil( '.btn-group' ).siblings( 'button' ).text( text );
                
                updateWithFilter();
            } )
            
            $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
                $('a[data-toggle="tab"]').parent().removeClass("active");
            }).on('shown.bs.tab', function (e) {
                updateWithFilter();
            } )
            
            $('#job div[role="tabpanel"]').on('shown.bs.collapse', function (e) {
                var id = $(e.target).attr( 'id' );
                $('a[href="#' + id + '"]').children('span').removeClass('glyphicon-collapse-down').addClass('glyphicon-collapse-up');
            })
            .on('hidden.bs.collapse', function (e) {
                var id = $(e.target).attr( 'id' );
                $('a[href="#' + id + '"]').children('span').removeClass('glyphicon-collapse-up').addClass('glyphicon-collapse-down');
            })
            
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
                        return false;
                    }
                } else {
                    $('#passwd1').parents('.form-group').addClass('has-error');
                    $('#passwd2').parents('.form-group').addClass('has-error');
                    $('#errorMsg').text('两次密码不一致');
                    return false;
                }
            })

        </script>
    </body>
</html>