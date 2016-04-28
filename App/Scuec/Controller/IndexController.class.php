<?php
namespace Scuec\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
//        $this->assign('url',u('Index/index'));
//        $this->display();
    $this->success("恭喜你!找到我了,但是你能找到实用点的页面吗?我带你去吧","../info/index",5);
    }
    public function _empty(){
    $this->error('你是怎么找到我的!');
}
}