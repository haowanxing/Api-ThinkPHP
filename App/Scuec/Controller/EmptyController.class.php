<?php
namespace Scuec\Controller;
use Think\Controller;
class EmptyController extends Controller {
    public function _empty(){
        $this->error('你是怎么找到我的!');
    }
}