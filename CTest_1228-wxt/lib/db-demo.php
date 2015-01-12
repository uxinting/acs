<?php
    require_once('lib/cfg.inc.php');
?>

<!DOCTYPE HTML>
<html>
<meta charset="utf-8" />
<title>数据库测试</title>
<body>

<p>以下为测试部分：</p>
<hr />
<p>插入数据：</p>
<?php
    $arr = Array(
        'uId' => '1',
        'uName' => 'HeLei',
        'uPsw' => '1122'
    );
    $Conn->query('user', $arr);

    $arr = Array(
        'uId' => '2',
        'uName' => 'Zhangjianxiong',
        'uPsw' => '2233'
    );
    $Conn->insert('user', $arr);
?>
<hr />
<p>修改数据：</p>
<?php
    $arr = Array(
        'uName' => 'ZhangChuanXChao',
        'uPsw' => '1122'
    );
    $Conn->update('user', $arr, 'uId = 2');
?>
<hr />
<p>删除数据：</p>
<?php
    $Conn->delete('user', 'uId = 1');
?>
<hr />
<p>查询数据：</p>
<?php
    $res = $Conn->query('select * from user');
    while( $row = $res->fetch_array() ){
        echo $row['uId'].'<br />'.$row['uName'].'<br />'.$row['uPsw'];
    }
?>

</body>
</html>