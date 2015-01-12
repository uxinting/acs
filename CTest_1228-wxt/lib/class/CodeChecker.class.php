<?php

/*
* 代码检查类
* 1.编译Compile()
* 2.执行Run()
* 3.并检查运行结果是否正确Compare()
*/
class CodeChecker {

    private $codeFile;  // 待编译的代码的文件名
    private $testFile;  // 测试输入的文件名
    private $ansFile;   // 标准答案的文件名
    private $outFile;   // 测试输出结果的文件名
    private $exeFile;   // 编译后的可执行程序的文件名
    private $absolutePath;  // 程序存放的绝对路径
    private $patternPath;  // 程序存放的绝对路径的转换（防止用户看到文件存放的路径）

    public function __construct( $absolutePath, $patternPath, $codeFile, $testFile, $outName, $ansFile='' ) {
        $this->absolutePath = $absolutePath;
        $this->patternPath = $patternPath;
        $this->codeFile = $codeFile;
        $this->testFile = $testFile;
        $this->ansFile = $ansFile;
        $this->exeFile = $outName.'.exe';  // 输出名字+exe
        $this->outFile = $outName.'.ans';  // 输出名字+ans
//        echo $testFile.'===<br />';
//        echo $codeFile.'===<br />';
//        echo $ansFile.'===<br />';
//        echo $outName.'===<br />';
    }

    /* 自动编译程序，错误时返回编译错误，正确将返回true*/
    public function  Compile( ){
        $cmd = 'chmod -R 777 '.$this->absolutePath;
        system($cmd);
        $res = null;
        $tmp=null;
        $cmd = 'gcc '.$this->codeFile.' -o '.$this->exeFile.' 2>&1 | sed "s/'.$this->patternPath.'/Your Code/g"';
//        echo $cmd.'<br />';
        if( exec($cmd, $res)=='' ){
            return 0;  // 说明编译正确
        }else{
            foreach( $res as $tmp)
                echo $tmp.'<br />';  // 打印全部错误
            return 1;  // 说明编译不正确
        }
    }

    /* 自动运行程序， 生成运行结果，并做超时控制（默认5s）*/
    public function  Run($isOnlyResult, $maxTime=5){
        $retval = 0;
        $cmd = $this->exeFile.' < '.$this->testFile.' >'.$this->outFile
            .' & { sleep '.$maxTime.'; eval "kill -9 $!" &> /dev/null; }';
//        echo $cmd.'<br />';
        system($cmd, $retval);
        if( $isOnlyResult==1 ){ // 仅仅生成结果，则删除可执行文件保留输出结果
            $cmd = 'rm -fr '.$this->exeFile;
            system($cmd);
        }
        $cmd = 'chmod -R 777 '.$this->absolutePath;
        system($cmd);
        return $retval;
    }

    /* 判断执行结果是否正确 */
    public function Compare(){

        // 检测文件是否存在
        if( !file_exists($this->ansFile) ){
            die('0:题目异常，请联系管理员#3001');
        }
        if( !file_exists($this->outFile) ){
            die('0:代码运行出错，请检查您的代码');
        }
        $retVal = 0;
        $cmd = 'diff -q '.$this->ansFile.' '.$this->outFile;
//        echo $cmd;
        if( exec($cmd)=='' ){
            $retVal = 0; // 相同为0
        }else{
            $retVal = 1;
        }
        // 删除编译出来的执行文件和结果文件
        $cmd = 'rm -fr '.$this->exeFile.' '.$this->outFile;
        system($cmd);
        return $retVal;
    }
}

/* 测试代码

    $codeChecker = new CodeChecker("1.c","1.test",'1.sample.ans');

    if( $codeChecker->Compile()==1 ){
        echo '<br />编译失败，请检查代码!';
    }else{
        $codeChecker->Run(); // 运行
        // 和标准答案比较运行结果
        if( $codeChecker->Compare()==1 ){
            echo '结果正确!';
        }else{
            echo '结果不正确!';
        }
    }

 * */