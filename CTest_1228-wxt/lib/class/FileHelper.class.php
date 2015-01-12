<?php

Class FileHelper
{
    /*
     *功能：删除单个文件
     *输入：文件路径
     *输出： 1 删除成功
     *      -1 文件不存在 或者 不是常规文件
     *      -2 删除失败
    */
    public function delFile($path)
    {
        if(!file_exists($path))
            return -1; //文件不存在
        if(!is_file($path))
            return -1; //不是常规文件
            
        if(unlink($path))
            return 1;  //删除成功
        else
            return -2; //删除失败
    }
    
    /*内部函数*/
    private function delAll($path)
    {
        //先递归删除目录下的所有文件
        $files = opendir($path); 
        while($file = readdir($files))
        {
            if($file != "." && $file != "..")
            {
                $fullpath = $path."/".$file;
                if(!is_dir($fullpath))    //是文件，则删除
                {
                    if(!unlink($fullpath))
                        return false;
                }
                else
                {
                    if(!self::delAll($fullpath))
                        return false;
                }
            }
        }
        
        //再删除当前目录
        closedir($files);
        if(rmdir($path))
            return true;
        else
            return false;
    }
    
    /*
     *功能：删除文件夹
     *输入：文件夹路径
     *输出： 1 删除成功
     *      -1 文件夹不存在
     *      -2 删除失败
    */
    public function delDir($path)
    {
        if(!file_exists($path))
            return -1; //文件不存在
        if(!is_dir($path))
            return -1; //不是文件夹
        if(self::delAll($path))
            return 1;  //删除成功
        else
            return -2; //删除失败
    }
    
    
    /*
     *功能：新建文件
     *输入：文件路径，覆盖标识：1覆盖，0不覆盖
     *输出：1  创建成功
     *      -1 创建失败
    */
    public function createFile($path,$isCover)
    {
        if(file_exists($path))
        {
            if($isCover)
            {
                unlink($path);
                $file = fopen($path,"x");
                fclose($file);
                return 1;
            }
            else
                return 1;
        }
        $dir = dirname($path);
        if(!is_readable($dir))
        {
            if(self::createDir($dir) == -1)
                return -1;
        }
        $file = fopen($path,"x");
        fclose($file);
        return 1;
 
    }
    
    /*
     *功能：新建文件夹
     *输入：文件夹路径
     *输出：1  创建成功
     *      -1 创建失败
     */
    public function createDir($path)
    {      
        if(!is_readable($path))
        {
            self::createDir(dirname($path));
            if(!is_file($path)) 
            {
                if(!mkdir($path,0777))
                    return -1;
            }
        }
        return 1;
    }
    
    
    /*
     *功能：创建文件并写入内容
     *输入：文件路径，文件内容，覆盖标志：1覆盖，0不覆盖
     *输出：1成功，-1失败
     *      
     */
     public function createFileWithContent($path,$content,$isCover)
     {
        if(file_exists($path)) //文件存在时
        {
            if($isCover)   //覆盖
            {
                if(!($fid = fopen($path,"w")))
                    return -1;
                if(!(fwrite($fid,$content)))
                    return -1; 
                return 1;
            }
            else
                return 1;
        }
        //文件不存在时，新建
        $dir = dirname($path);
        if(!is_readable($dir)) //文件夹不存在则创建
        {
            if(self::createDir($dir) == -1)
                return -1;
        }
        if(!($fid = fopen($path,"w")))  //新建文件并写入数据
            return -1;
        if(!(fwrite($fid,$content)))
            return -1; 
        fclose($fid);
        return 1;
     }
     
     public function appendContent($path,$content)
     {
         if(!file_exists($path)) //文件不存在
             return -1;
         if(!($fid = fopen($path,"a+")))  //写入数据
             return -1;
         if(!(fwrite($fid,$content)))
             return -1; 
         fclose($fid);
         return 1;
     }
}
?>