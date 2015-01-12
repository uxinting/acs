<?php
class DbHelper {
    private $tb_start = '';
    private $mysqli = null;
    public function __construct($host, $user, $psw, $db) {
        $this->mysqli = new mysqli($host, $user, $psw, $db);
        if($this->mysqli->connect_error) {
            die("系统出错，请联系管理员#0");
        }
        $this->mysqli -> query("set names utf8");
 	}

    /* 设置表前缀 */
    public function set_tb_pre($str="") {
		if($str<>"") $this -> tb_start = $str;
	}

    /* 查询数据 */
    public function query($sql) {
		return $this->mysqli->query( str_replace('-table-', $this -> tb_start, $sql) );
	}

    /* 扩展查询数据 */
    public function queryEx($table, $arr, $condition) {
        $sql = '';
        $i = 0;
        foreach($arr as $key => $value) {
            if( $i == 0 ){
                $sql .= " `$key` '$value'";
                $i = 1;
            }else{
                $sql .= " , `$key` '$value'";
            }
        }
        $sql = 'SELECT '.$sql.' FROM '.$table.' WHERE '. $condition;
        return $this->mysqli->query( $sql );
    }

    /* 插入数据 */
    public function insert($table, $arr) {
        $sql = "INSERT INTO `$table` (`".implode('`,`', array_keys($arr))."`) VALUES('".implode("','", $arr)."')";
        return $this -> query($sql);
	}

    /* 获取上次自增Id，每个连接线程都会自动保存独立的自增Id */
    public function geLastId(){
        $res = $this->query('SELECT LAST_INSERT_ID() id');
        $row = $res->fetch_array();
        if( $row!='' ){
            return $row['id'];
        }else{
            return 0;
        }
    }

    /* 修改记录 */
    public function update($table, $arr, $condition = '1=1') {
		$sql = '';
        $i = 0;
		foreach($arr as $key => $value) {
            if( $i == 0 ){
                $sql .= " `$key`='$value'";
                $i = 1;
            }else{
                $sql .= " , `$key`='$value'";
            }
		}
		$sql = 'UPDATE '.$table.' SET '.$sql.' WHERE '. $condition;
        //echo '['.$sql.']';
        return $this -> query($sql);
	}

    /* 删除数据 */
    public function delete($table, $condition) {
        $sql = 'DELETE FROM '.$table.' where '.$condition;
        return $this -> query($sql);
    }

    /*释放资源*/
    public function free($res){
        $res->free();
    }

    /* 关闭数据库 */
    public function close() {
        return $this->mysqli->close();;
    }
}
?>