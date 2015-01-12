ExcelInOut类

使用了第3方编写的PHPExcel工具组件：PHPExcel.php和PHPExcel文件夹

本类首先实现了内存与Excel表之间的基本导入导出函数：
    excelIn($filename)，利用工具类将filename命名的Excel表导入内存，然后对数据做简单处理放入
$data二维数组中，并将data返回
    excelOut($data,$filename)，将二维数组data中的数据导出到filename命名的Excel表中

随后根据系统的需求（教师、学生信息的导入导出），利用上述两个基本函数，实现了4个对外的功能函数
studentToDB ($filename)；   将学生信息从Excel表中导入到ctest数据student表中
studentToExcel ($filename)；将学生信息从ctest数据student表中导出到Excel表中
teacherToDB ($filename)；   将教师信息从Excel表中导入到ctest数据teacher表中
teacherToExcel ($filename)；将教师信息从ctest数据teacher表中导出到Excel表中