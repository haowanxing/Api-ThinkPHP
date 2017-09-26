<?php
/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 17/9/25
 * Time: 下午12:58
 */

namespace Home\Controller;


use Think\Controller;

class RentroomController extends Controller{

    private $danke_token = "2xzFXLa7uwWQ4OnRBIuqhTPIKDLe7NK2LhzdfBDK";

    public function index(){
        $this->display();
    }

    public function Danke(){
        $this->assign("token",$this->danke_token);
        $this->display();
    }

    public function DankeOuter(){
        $url = "http://www.dankegongyu.com/map/search-by-params";

        $params['_token'] = I('get._token',$this->danke_token);
        $params['page'] = I('get.page',1);
        $params['size'] = I('get.size',10);
        $params['price'] = I('get.price','');
        $params['bedroomNum'] = I('get.bedroomNum','');
        $params['rentType'] = I('get.rentType','');
        $params['area'] = I('get.area','');
        $params['faceTo'] = I('get.faceTo','南');
        $params['hasToilet'] = I('get.hasToilet','')?'有':'';
        $params['hasBalcony'] = I('get.hasBalcony','')?'有':'';
        $params['hasShower'] = I('get.hasShower','')?'有':'';
        $params['xiaoquId'] = I('get.xiaoquId','');
        $params['left_bottom_lng'] = I('get.left_bottom_lng','');
        $params['left_bottom_lat'] = I('get.left_bottom_lat','');
        $params['right_top_lng'] = I('get.right_top_lng','');
        $params['right_top_lat'] = I('get.right_top_lat','');

        $parameter = http_build_query($params);
        $url .= "?".$parameter;
        $content = file_get_content($url);
        $rs = json_decode($content);
        $ret_msg = result(200,'ok',$rs);
        $this->ajaxReturn($ret_msg);

    }

    public function DankeGeo(){
        $url = "http://www.dankegongyu.com/map/search-geo-encode";

        $params['_token'] = I('get._token',$this->danke_token);
        $params['keywords'] = I('get.keywords');

        $parameter = http_build_query($params);
        $url .= "?".$parameter;
        $content = file_get_content($url);
        $rs = json_decode($content);
        $ret_msg = result(200,'ok',$rs);
        $this->ajaxReturn($ret_msg);

    }

}