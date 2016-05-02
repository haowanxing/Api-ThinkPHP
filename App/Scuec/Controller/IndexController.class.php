<?php
namespace Scuec\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
//        $this->assign('url',u('Index/index'));
//        $this->display();
        $this->success("恭喜你!找到我了,但是你能找到实用点的页面吗?我带你去吧", "../info/index", 5);
    }

    public function getMsgCxcy(){
        $url = "http://www.scuec.edu.cn/s/15/t/1074/p/12/list.htm";
        $content = file_get_contents($url);
        $pattern = "/class='columnStyle'><tr><td><a href='(.*?)' target='_blank' style=''><font color='.*?'>[<b>]*(.*?)[<\/b>]*<\/font><\/a><\/td><td align='center' width='14%' nowrap class='postTime'>(.*?)<\/td><\/tr><\/table>/s";
        preg_match_all($pattern,$content,$matchlist);       //$matchlist[1]为url [2]为标题 [3]为日期
        array_shift($matchlist);
        $result = array();
        foreach($matchlist[0] as $k=>$v){
            $result[$k]['title'] = $matchlist[1][$k];
            $result[$k]['date'] = $matchlist[2][$k];
            $result[$k]['url'] = 'http://www.scuec.edu.cn'.$matchlist[0][$k];
        }
        $this->ajaxReturn(array('code'=>200,'result'=>$result),'json');
    }
    public function getMsgJiKe(){
        $url = "http://www.scuec.edu.cn/s/48/t/57/p/9/list.htm";
        $content = file_get_contents($url);
        $pattern = "/class='columnStyle'><tr><td><a href='(.*?)' target='_blank' style=''><font color='.*?'>[<b>]*(.*?)[<\/b>]*<\/font><\/a><\/td><\/tr><\/table>/s";
        preg_match_all($pattern,$content,$matchlist);       //$matchlist[1]为url [2]为标题 [3]为日期
        array_shift($matchlist);
        $result = array();
        foreach($matchlist[0] as $k=>$v){
            $result[$k]['title'] = $matchlist[1][$k];
            $result[$k]['date'] = $matchlist[2][$k];
            $result[$k]['url'] = 'http://www.scuec.edu.cn'.$matchlist[0][$k];
        }
        $this->ajaxReturn(array('code'=>200,'result'=>$result),'json');
    }

    public function getMsgMinDa(){
        $url = "http://news.scuec.edu.cn/xww/?class-focusNews.htm";
        $content = file_get_contents($url);
        $pattern = '/<ul>[\t|\n|\r]*?<li.*?>[<a href="\.\/\?(.*?)".*?>(.*?)<\/a><em>\((.*?)\)<\/em><\/li><span>(.*?)<\/span>]*<\/ul>/s';
        preg_match_all($pattern,$content,$matchlist);
        $pattern = '/<li.*?><a href="\.\/\?(.*?)".*?>(.*?)<\/a><em>\((.*?)\)<\/em><\/li><span>(.*?)<\/span>/s';
        preg_match_all($pattern,$matchlist[0][0],$matchlist);
        array_shift($matchlist);           //$matchlist[0]为url [1]为标题 [2]为日期 [3]为简介
        $result = array();
        foreach($matchlist[0] as $k=>$v){
            $result[$k]['title'] = str_replace('&nbsp;',' ',$matchlist[1][$k]);
            $result[$k]['date'] = $matchlist[2][$k];
            $result[$k]['intro'] = $matchlist[3][$k];
            $result[$k]['url'] = 'http://news.scuec.edu.cn/xww/?'.$matchlist[0][$k];
        }
        $this->ajaxReturn(array('code'=>200,'result'=>$result),'json');
    }
    public function _empty()
    {
        $this->error('你是怎么找到我的!');
    }
}