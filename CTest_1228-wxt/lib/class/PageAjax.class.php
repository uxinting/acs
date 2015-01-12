<?php
/**
 *-------------------------翻页类----------------------*
 */
class MyPage_Ajax {
    private $count; // 页面总数
    private $size; // 每页显示数目
    private $now; // 当前页面
    private $url; // 页面Url, 形如 xxx.xx?page=
    private $fun; // 点击触发函数

    /* 构造函数 */
	function __construct($now=1, $count=0, $size=20, $url='', $fun='AjaxGet(this)' ){
		$this->count = $count;
		$this->now  = $now;
        $this->size = $size;
		$this->url  = $url.'&page=';
        $this->fun = $fun;
	}

    /* 获取首页 */
    private function getFirst(){
        return '<li class="previous"><a href="javascript:void(0);" onclick="'.$this->fun.'" rel="'.$this->url.'0">首页</a></li>';
    }

    /* 获取上一页 */
    private function getPrev(){
        if( $this->now>1 ){
            return '<li><a href="javascript:void(0);" onclick="'.$this->fun.'" rel="'.$this->url.($this->now-1).'">上一页</a></li>';
        }
    }

    /* 获取页面各个编号 */
    private function getPages(){
        $str = '';
        $start = ($this->now-6)>0 ? ($this->now-6) : 1 ;
        $end = 12 + $start;
        for( $i=$start; $i<=$end && $i<=$this->count; $i++ ){
            if( $this->now == $i ){
                $str .= '<li class="active disabled"><a>'.$i.'</a></li>';
            } else {
                $str .= '<li><a href="javascript:void(0);" onclick="'.$this->fun.'" rel="'.$this->url.$i.'">'.$i.'</a></li>';
            }
        }
        return $str;
    }

    /* 获取下一页 */
    private function getNext(){
        if( $this->now < $this->count ){
            return '<li><a href="javascript:void(0);" onclick="'.$this->fun.'" rel="'.$this->url.($this->now+1).'">下一页</a></li>';
        }
    }

    /* 获取下拉页面选择 */
    private function getSelectPages(){
        if( $this->count > 12 ){
            $str = '<li class="select-no-radius-both" style="float: left;" data-toggle="tooltip" data-placement="top" title="选择跳至">
                    <select class="selectpicker" data-live-search="true" data-width="80px">';
            for( $i=1; $i<=$this->count; $i++ ){
                $str .= '<option>'.$i.'</option>';
            }
            $str .= '</select></li>';
            return $str;
        }
    }

    /* 获取末页 */
    private function getLast(){
        return '<li class="next"><a href="javascript:void(0);" onclick="'.$this->fun.'" rel="'.$this->url.($this->count).'"
            data-toggle="tooltip" data-placement="top" title="共'.$this->count.'页，每页'.$this->size.'条">末页</a></li>';
    }

	function PageWrite() {
		$str = '<ul class="pagination-my pagination">';
        $str .= $this->getFirst();
        $str .= $this->getPrev();
        $str .= $this->getPages();
        //$str .= $this->getSelectPages();
        $str .= $this->getNext();
        $str .= $this->getLast();
        $str .= '</ul>';
        return $str;
	}
}
?>