<?php
/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 17/3/27
 * Time: 上午12:06
 */

namespace Home\Controller;


use Think\Controller;

class LotteryController extends Controller{
    public function ssq(){
        $this->display();
    }

    public function rangeouter(){
        if(I('type')=='ssq'){
            $rs = $this->doublecolor(I("red",6),I("blue",1));
        }
        $ret_msg = result(200,'ok',$rs);
        $this->ajaxReturn($ret_msg);
    }

    private function doublecolor($red_num=6,$blue_num=1){
        if($red_num<1)  $red_num = 6;
        if($blue_num<1)  $blue_num = 1;
        /*设置球球*/
        $red_balls=range(1,33);
        $blue_balls=range(1,16);

        /*模拟出球*/
        $reds = array();
        $blues = array();
        for ($i=0; $i < $red_num; $i++) {
            $key = array_rand($red_balls);
            $num = $red_balls[$key];
            while (in_array($num,$reds)) {
                $key = array_rand($red_balls);
                $num = $red_balls[$key];
            }
            array_push($reds,$num);
        }
        for ($i=0;$i<$blue_num;$i++){
            $key = array_rand($blue_balls);
            $num = $blue_balls[$key];
            while (in_array($num,$blues)) {
                $key = array_rand($blue_balls);
                $num = $blue_balls[$key];
            }
            array_push($blues,$num);
        }
        sort($reds);
        sort($blues);
        return array('red'=>$reds,'blue'=>$blues);
    }
}