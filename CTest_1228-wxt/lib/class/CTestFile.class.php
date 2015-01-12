<?php

/*
 *    cTestFiel
 * @version 1.0
 * @author  zcc
 * @date    2014-12-20
 *
 *createJob($date,$id,$content);             //创建作业文件
 *createTestCase($date,$id,$content);        //创建测试用例文件
 *createTestAnswer($date,$id,$content);      //创建测试用例答案文件
 *createSampleCase($date,$id,$content);      //创建样例文件
 *createSampleAnswer($date,$id,$content);    //创建样例答案文件
 *createReferencedCode($date,$id,$content);  //创建参考代码文件
 *createSubmitCode($date,$id,$content);      //创建学生提交代码文件
 *createZip($targetName,$date,$id);          //打包         配置：extension=php_zip.dll
*/
class CTestFile
{
    
    static private $ROOT =  '/var/www/data/ctest/'; // 文件的绝对根目录（为保证安全性）
    // 后缀名
    static private $PROB = '.prob';  //作业
    static private $TEST = '.test';  //测试用例
    static private $ANS  = '.ans';   //测试用例答案
    static private $SAMPLE_TEST = '.sample.test';  //样例
    static private $SAMPLE_ANS  = '.sample.test.ans';   //样例答案
    static private $C    = '.c';      //程序
    
    
    //文件备份
    private function backup($path,$bakpath)
    {
        if(!file_exists($path))
            return true;
        $dir = dirname($bakpath);
        if(!is_readable($dir))        //备份文件夹不存在时，创建文件夹
        {
            require_once("FileHelper.class.php");
            if(FileHelper::createDir($dir) == -1)
                return false;
        }
        if(!copy($path,$bakpath))
            return false;
        return true;       
    }
    
    
    //创建文件
    private function create($path,$bakpath,$content='')
    {
        $path = self::$ROOT.$path;
        $bakpath = self::$ROOT.$bakpath;
        if(!self::backup($path,$bakpath))
            return -1;
        $dir = dirname($path);
        if(!is_readable($dir))
        {
            require_once("FileHelper.class.php");
            if(FileHelper::createDir($dir) == -1)
                return -1;
        }
        if(!($fid = fopen($path,"w")))  //新建文件并写入数据
            return -1;
        if($content!='' && !(fwrite($fid,$content)))    // 允许写入为空的内容
            return -1; 
        fclose($fid);
        return 1;
    }
    
    
    //检查是否可以创建文件，若可以则创建
    private function createAndCheck($path,$bakpath,$content)
    {
        $absolutePath = self::$ROOT.$path;
        $dir = dirname($absolutePath);
        if(!is_readable($dir))   //如果文件夹不存在，说明题目不存在，则不能创建测试用例、答案、代码等文件
            return -2;
        if(self::create($path,$bakpath,$content) == -1)
            return -1;
        return 1;
    }
    
    
    /*
     *功能：创建作业文件
     *输入：日期，题号，类容
     *输出：1成功，-1失败
     */
    public function createJob($date,$id,$content)
    {
        $path = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$id.self::$PROB;
        $bakpath = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.'backup'.DIRECTORY_SEPARATOR.$id.self::$PROB.'.'.time();
        if(self::create($path,$bakpath,$content) == -1)
            return -1;
        return 1;
    }
    
    
     /*
     *功能：创建测试用例文件
     *输入：日期，题号，类容
     *输出：1成功，-1失败，-2因作业不存在而失败
     */
    public function createTestCase($date,$id,$content)
    {
        $path = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$id.self::$TEST;
        $bakpath = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.'backup'.DIRECTORY_SEPARATOR.$id.self::$TEST.'.'.time();  
        return self::createAndCheck($path,$bakpath,$content);
    }
    
    
    /*
     *功能：创建测试用例答案文件
     *输入：日期，题号，类容
     *输出：1成功，-1失败，-2因作业不存在而失败
     */
    public function createTestAnswer($date,$id,$content)
    {
        $path = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$id.self::$ANS;
        $bakpath = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.'backup'.DIRECTORY_SEPARATOR.$id.self::$ANS.'.'.time();  
        return self::createAndCheck($path,$bakpath,$content);
    }
    
    
    /*
     *功能：创建样例文件
     *输入：日期，题号，类容
     *输出：1成功，-1失败，-2因作业不存在而失败
     */
    public function createSampleCase($date,$id,$content)
    {
        $path = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$id.self::$SAMPLE_TEST;
        $bakpath = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.'backup'.DIRECTORY_SEPARATOR.$id.self::$SAMPLE_TEST.'.'.time();  
        return self::createAndCheck($path,$bakpath,$content);
    }
    
    
    /*
     *功能：创建样例答案文件
     *输入：日期，题号，类容
     *输出：1成功，-1失败，-2因作业不存在而失败
     */
    public function createSampleAnswer($date,$id,$content)
    {
        $path = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$id.self::$SAMPLE_ANS;
        $bakpath = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.'backup'.DIRECTORY_SEPARATOR.$id.self::$SAMPLE_ANS.'.'.time();  
        return self::createAndCheck($path,$bakpath,$content);
    } 
    
    
    /*
     *功能：创建参考代码文件
     *输入：日期，题号，类容
     *输出：1成功，-1失败，-2因作业不存在而失败
     */
    public function createReferencedCode($date,$id,$content)
    {
        $path = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$id.self::$C;
        $bakpath = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.'backup'.DIRECTORY_SEPARATOR.$id.self::$C.'.'.time();  
        return self::createAndCheck($path,$bakpath,$content);
    }
    
    
    /*
     *功能：创建测试用例文件
     *输入：日期，题号，类容
     *输出：1成功，-1失败，-2因作业不存在而失败
     * $date为归档时间，$id为所提交的题目的Id,  $sId 为学生学号, $content为代码内容
     */
    public function createSubmitCode($date,$id,$sId,$content)
    {
        $path = $date.DIRECTORY_SEPARATOR.$id;
        if(!is_readable(CTestFile::$ROOT.$path))
            return -2;
        $path = $path.DIRECTORY_SEPARATOR.'s'.DIRECTORY_SEPARATOR.$sId.'-'.$id.self::$C;
        $bakpath = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.'backup'.DIRECTORY_SEPARATOR.'s'.DIRECTORY_SEPARATOR.$sId.'-'.$id.self::$C.'.'.time();
        if(self::create($path,$bakpath,$content) == -1)
            return -1;
        return 1;
    }
    
    
    private function addFileToZip($sourcePath,$zip,$forbidPath)
    {
        $files = opendir($sourcePath); 
        while($file = readdir($files))
        {
            if($file != "." && $file != "..")
            {
                $fullpath = $sourcePath.DIRECTORY_SEPARATOR.$file;
                if($fullpath == $forbidPath)
                    continue;
                if(is_dir($fullpath)) 
                {
                    self::addFileToZip($fullpath,$zip,$forbidPath);
                }
                else
                {
                    $zip->addFile($fullpath);
                }
            }
        }
    }
    
    
    /*
     *功能：打包
     *输入：包名，日期，题号
     *输出：1成功，-1失败
     */
    public function createZip($targetName,$date,$id)    
    {
        $sourcePath = self::$ROOT.$date.DIRECTORY_SEPARATOR.$id;
        $forbidPath = self::$ROOT.$date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.'backup';  //过滤掉备份文件
        $zip = new ZipArchive();
        if($zip->open($targetName,ZipArchive::OVERWRITE))
        {
            self::addFileToZip($sourcePath,$zip,$forbidPath);
            return 1;
        }
        return -1;
    }


    private function read($path)
    {
        $path = self::$ROOT.$path;
        $content = null;
        if(!file_exists($path))
            $content = '';
        else
            $content = @file_get_contents($path);
        return $content;
    }

    /*
     *功能：读作业文件
     *输入：日期，题号
     *输出：成功则返回文件内容，否则返回-1
     */
    public function readJob($date,$id)
    {
        $path = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$id.self::$PROB;
        return self::read($path);
    }


    /*
    *功能：读测试用例文件
    */
    public function readTestCase($date,$id)
    {
        $path = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$id.self::$TEST;
        return self::read($path);
    }


    /*
     *功能：读测试用例答案文件
     */
    public function readTestAnswer($date,$id)
    {
        $path = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$id.self::$ANS;
        return self::read($path);
    }


    /*
     *功能：读样例文件
     */
    public function readSampleCase($date,$id)
    {
        $path = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$id.self::$SAMPLE_TEST;
        return self::read($path);
    }


    /*
     *功能：读样例答案文件
     */
    public function readSampleAnswer($date,$id)
    {
        $path = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$id.self::$SAMPLE_ANS;
        //echo $path;
        return self::read($path);
    }


    /*
     *功能：读参考代码文件
     */
    public function readReferencedCode($date,$id)
    {
        $path = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.$id.self::$C;
        return self::read($path);
    }


    /*
     *功能：读学生代码文件
     */
    public function readSubmitCode($date,$id,$sId)
    {
        $path = $date.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR.'s'.DIRECTORY_SEPARATOR.$sId.'-'.$id.self::$C;
        return self::read($path);
    }
}

/*
$myFile = new cTestFile();
echo $myFile->createJob('20150101',1,'张传超Love韩梅梅1');
echo $myFile->createTestCase('20150101',1,'这是测试用例1');
echo $myFile->createTestAnswer('20150101',1,'这是测试用例答案1');
echo $myFile->createSampleCase('20150101',1,'这是样例1');
echo $myFile->createSampleAnswer('20150101',1,'这是样例答案1');
echo $myFile->createReferencedCode('20150101',1,'这是参考代码1');
echo $myFile->createSubmitCode('20150101',1,'M201472736','这是我的代码哦1');
echo $myFile->createZip('E:/data/test.zip','20150101',1);*/
?>    
   