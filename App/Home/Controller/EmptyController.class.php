<?php
namespace Home\Controller;

use Think\Controller;

class EmptyController extends Controller
{
    public function index(){
        $this->error("不可以胡乱访问哦!现在带你回去.");
    }
    public function _empty(){
        $this->error("你确定你没有胡乱访问?");
    }
}