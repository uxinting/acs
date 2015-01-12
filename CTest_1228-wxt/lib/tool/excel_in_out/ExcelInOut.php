<?php
class ExcelInOut
{
    private function excelIn($filename)
    {
        require_once 'PHPExcel.php';
        require_once 'PHPExcel/IOFactory.php';  
        require_once 'PHPExcel/Writer/Excel5.php'; 
        $resultPHPExcel = new PHPExcel(); 
        $objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format 
        $objPHPExcel = $objReader->load($filename); 
        $sheet = $objPHPExcel->getSheet(0); 
        $rows = $sheet->getHighestRow();          
        $column = $sheet->getHighestColumn();
        $columns = PHPExcel_Cell::columnIndexFromString($column);
        for($i = 1; $i <= $rows; $i++)  //row
        {
            for($j = 0; $j < $columns; $j++)  //column
            {    
                $data[$i - 1][$j] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j, $i)->getValue();

            } 
        }
        return $data;
    }

    private function excelOut($data,$filename)
    {
        require_once 'PHPExcel.php';
        require_once 'PHPExcel/IOFactory.php';  
        require_once 'PHPExcel/Writer/Excel5.php'; 
        $resultPHPExcel = new PHPExcel();
        $resultPHPExcel->setActiveSheetIndex(0);
        $writer = new PHPExcel_Writer_Excel5($resultPHPExcel);
        for($i=0;$i<count($data);$i++)
        {
            for($j=0;$j<count($data[$i]);$j++)
            {
                $column = chr($j+ord('A'));
                $resultPHPExcel->getActiveSheet()->setCellValue($column.($i+1),$data[$i][$j]);
                //echo $data[$i][$j]." ";
            }
        }
        $writer->save($filename);
    }
    

    private function connectDB()
    {
        $conn = @mysql_connect("localhost","root","123456") or die("数据库连接出错");
        @mysql_select_db('CTest',$conn);
        @mysql_query("set names utf8");
        return $conn;
    }
    
    
    private function dataToDB($sql,$data)
    {
        $conn = self::connectDB();
        
        $column = count($data[0]);
        for($i = 1; $i < count($data); $i++)
        {
            //密码生成,初始密码为学号
            $data[$i][0] = str_replace(" "," ",$data[$i][0]);
            $data[$i][$column] = md5(trim($data[$i][0]));
            //注册日期
            $data[$i][$column+1] = date('Y-m-d H:i:s');

            $values = '("';
            for($j = 0; $j < $column+2; $j++)
            {
                $data[$i][$j] = str_replace(" "," ",$data[$i][$j]);
                $values .= trim($data[$i][$j]);
                if($j != $column + 1)
                    $values .= '","';
                else
                    $values .= '")';
            }
            $fullSql = $sql . $values;
            echo $fullSql,"</br>","</br>";
            
            @mysql_query($fullSql,$conn);
            if(mysql_error())
                echo mysql_error();         
        }
        @mysql_close($conn);
    }
    
    
    /**
     *
     *将给定含有学生信息的excel表（文件名：filename）中的数据导入数据库
     *
     */
    public function studentToDB ($filename)
    {
        $studentData = self::excelIn($filename);
        $column = 8;
        if(count($studentData[0]) != $column )
        {
            echo '数据不合法，请仔细核对数据';
        }
        $sql = 'insert into student(`sNo`,`sName`,`sClassId`,`sEmail`,`sSex`,`sAddr`,`sTel`,`sQQ`,`sPsw`,`sTime_Reg`) values ';
        self::dataToDB($sql,$studentData);
    }
    
    
    /**
     *
     *将学生信息从数据库导出到excel表中（文件名：filename）
     *
     */
    public function studentToExcel ($filename)
    {
        $conn = self::connectDB();
        $result = @mysql_query("select * from student",$conn);
        
        $studentData;
        $studentData[0]=array('sId','sNo','sName','sClassId','sEmail','sSex','sAddr','sTel','sQQ','sPsw','sCookie','sIp_Login','sTime_Login','sTime_Reg','sflag');
        $i = 1;
        while($row = @mysql_fetch_array($result))
        {
            for($j=0; $j<count($studentData[0]);$j++)
                $studentData[$i][$j] = $row[$studentData[0][$j]];
            $i++;
        }
        @mysql_close($conn);
        self::excelOut($studentData,$filename);
    }
    
    
    /**
     *
     *将给定含有教师信息的excel表（文件名：filename）中的数据导入数据库
     *
     */
    public function teacherToDB ($filename)
    {
        $teacherData = self::excelIn($filename);
        $column = 8;
        if(count($teacherData[0]) != $column )
        {
            echo '数据不合法，请仔细核对数据';
        }
        $sql = 'insert into teacher(`tNo`,`tName`,`tColleage`,`tEmail`,`tSex`,`tAddr`,`tTel`,`tQQ`,`tPsw`,`tTime_Reg`) values ';
        self::dataToDB($sql,$teacherData);
    }
    
    
    /**
     *
     *将教师信息从数据库导出到excel表（文件名：filename）中
     *
     */
    public function teacherToExcel ($filename)
    {
        $conn = self::connectDB();
        $result = @mysql_query("select * from teacher",$conn);
        
        $teacherData;
        $teacherData[0]=array('tId','tNo','tName','tColleage','tEmail','tSex','tAddr','tTel','tQQ','tPsw','tCookie','tIp_Login','tTime_Login','tTime_Reg','tright','tflag');
        $i = 1;
        while($row = @mysql_fetch_array($result))
        {
            for($j=0; $j<count($teacherData[0]);$j++)
                $teacherData[$i][$j] = $row[$teacherData[0][$j]];
            $i++;
        }
        @mysql_close($conn);
        self::excelOut($teacherData,$filename);
    }
    
}

$test = new ExcelInOut();

$test->studentToDB ('student.xls');
$test->teacherToDB ('teacher.xls');
$test->studentToExcel('studentOut.xls');
$test->teacherToExcel('teacherOut.xls');


?>