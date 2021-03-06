<?php
namespace Home\Controller;

use Think\Controller;
use Think\Think;

class IndexController extends Controller
{
    public function index()
    {
        header("Location:/index.php/Home/index/qrcode.html");
//        $this->error("现在返回");
//        $this->show("hello world");
//        $this->assign("img",u('index/verifycode'));
//        $this->display();
//        if(!empty(I('get.code'))){
//            echo $this->check_verify(I('get.code'))?'pass':'nopass';
//        }
    }

    public function phpinfo(){
        echo phpinfo();
    }

    public function verifycode()
    {
        $Verify = new \Think\Verify();
//        $Verify->fontttf = '5.ttf';
        $Verify->useZh = true;
//        $Verify->useImgBg = true;
        $Verify->entry();
    }

    protected function check_verify($code, $id = '')
    {
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }

    public function getnav()
    {
        $this->display("./Public/Tpl/head.html");
        $this->display("./Public/Tpl/nav.html");
        $this->display("./Public/Tpl/footer.html");
    }

    public function translate()
    {
        $keyfrom = "appfandouzi";    //申请APIKEY 时所填表的网站名称的内容
        $apikey = "1494784999";  //从有道申请的APIKEY

        if (!empty(I("get.word"))) {
            $word = I("get.word");
//有道翻译-xml格式
            $url_youdao = 'http://fanyi.youdao.com/fanyiapi.do?keyfrom=' . $keyfrom . '&key=' . $apikey . '&type=data&doctype=xml&version=1.1&q=' . $word;
            $xmlStyle = simplexml_load_file($url_youdao);
            $errorCode = $xmlStyle->errorCode;
            $paras = $xmlStyle->translation->paragraph;
            if (!empty(I("get.qq"))) {
                if (!is_numeric(I("get.qq")) || strlen(I("get.qq")) < 4 || strlen(I("get.qq")) > 10)
                    $this->ajaxReturn(array('code'=>400,'msg'=>"QQ号:".I("get.qq")."不合法"));
                else
                    echo $errorCode == 0 ? $paras : "无法进行有效的翻译";
            } else {
                $this->assign("result", $errorCode == 0 ? $paras : "无法进行有效的翻译");
                $this->display();
            }
        } else {
            $this->display();
        }
    }

    public function weather()
    {
        if (!empty(I("get.city"))) {
            $name = urlencode(I("get.city"));
            $weatherurl = "http://api.map.baidu.com/telematics/v2/weather?location=" . $name . "&ak=E248fd1674c8bab757f521e4f459353f";
            $apistr = file_get_contents($weatherurl);
            $apiobj = simplexml_load_string($apistr);
            $placeobj = $apiobj->currentCity;

            $weather = Array();
            if (!empty($placeobj)) {
                $results = $apiobj->results->result;
                if (empty($results))
                    $weather[0] = "读取数据出错,请联系管理员";
                else {
                    foreach ($results as $item) {
                        $weather[] = array($item->date . '', $item->weather . '', $item->wind . '', $item->temperature . '');
                    }
                }
            } else {
                $weather[0] = "找不到!";
            }
            if (!empty(I("get.qq"))) {
                if (!is_numeric(I("get.qq")) || strlen(I("get.qq")) < 4 || strlen(I("get.qq")) > 10)
                    $this->ajaxReturn(array('code'=>400,'msg'=>"QQ号:".I("get.qq")."不合法"));
                else
                    $this->showapi($weather);
//                $this->ajaxreturn($weather,'json');
            } else {
                $this->assign("tips", I("get.city"));
                $this->assign("result", $weather[0][0] . " " . $weather[0][1] . " " . $weather[0][2] . " " . $weather[0][3] . "<br>" . $weather[1][0] . " " . $weather[1][1] . " " . $weather[1][2] . " " . $weather[1][3] . "<br>" . $weather[2][0] . " " . $weather[2][1] . " " . $weather[2][2] . " " . $weather[2][3] . "<br>" . $weather[3][0] . " " . $weather[3][1] . " " . $weather[3][2] . " " . $weather[3][3]);
                $this->display();
            }
        } else {
            $this->display();
        }
    }

    public function qrcode()
    {
        $this->display();
    }

    protected function showapi($data)
    {
//        $this->ajaxreturn($data,__EXT__);
        if ("xml" == __EXT__) {
            $this->ajaxreturn($data, 'xml');
        } else {
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
    }

    public function iplocation()
    {
        import("ORG.Net.IpLocation");
        $Ip = new \Org\Net\IpLocation('UTFWry.dat');
        $area = $Ip->getlocation(I("get.ip", ''));
        $area = eval('return ' . iconv('gbk', 'utf-8', var_export($area, true)) . ';');
        if (!empty(I("get.qq"))) {
            if (!is_numeric(I("get.qq")) || strlen(I("get.qq")) < 4 || strlen(I("get.qq")) > 10)
                $this->ajaxReturn(array('code'=>400,'msg'=>"QQ号:".I("get.qq")."不合法"));
            else
                $this->showapi($area);
        } else {
            $this->assign("location", $area);
            $this->display();
        }
    }

    public function short_url()
    {
        $requesturl = "https://api.weibo.com/2/short_url/shorten.json?access_token=2.00lbGq6CptiSTDc1e1fe2df6fs4ngD&url_long=";
        if (I("post.type") == "2")
            $requesturl = "https://api.weibo.com/2/short_url/expand.json?access_token=2.00lbGq6CptiSTDc1e1fe2df6fs4ngD&url_short=";
        if (!empty(I("post.url"))) {
            $requesturl .= I("post.url");
        } else {
            $requesturl .= "http://www.fantwo.com";
            if (I("post.type") == "2")
                $requesturl .= "http://t.cn/RGCemd0";
        }
        $content = file_get_content($requesturl);
        $response = json_decode($content, true);
        if (!empty($response)) {
            if (I('get.qq'))
                if (!is_numeric(I("get.qq")) || strlen(I("get.qq")) < 4 || strlen(I("get.qq")) > 10) {
                    $this->ajaxReturn(array('code'=>400,'msg'=>"QQ号:".I("get.qq")."不合法"));
                } else {
                    $this->ajaxReturn($response, 'json');
                }
            if ($response['urls'][0]['result']) {
                $this->assign("result", $response['urls'][0]);
            } else {
                $this->assign("tips", "不符合规则");
//                $this->assign("result", $response['urls']);
            }
        } else {
            $this->assign("tips", "不符合规则,请求出错,{$content}");
        }
        $this->display();
    }

    public function cheapFlight(){
        I('get.from')?$userMsg['from'] = I('get.from'):$userMsg['from'] = "北京";
        I('get.to')?$userMsg['to'] = I('get.to'):$userMsg['to'] = "上海";
        import("ORG.Curl.Curl");
        $curl = new \Org\Curl\Curl();
        $apiurl = "http://tg.m.kuxun.cn/plane-speciallist-json.html";
        $data = array("json"=>1,"from"=>$userMsg['from'],"to"=>$userMsg['to']);
        $retJson = $curl->post($apiurl,$data);
        if (I('get.qq')) {
            if (!is_numeric(I("get.qq")) || strlen(I("get.qq")) < 4 || strlen(I("get.qq")) > 10) {
                $this->ajaxReturn(array('code'=>400,'msg'=>"QQ号:".I("get.qq")."不合法"));
            } else {
                $this->ajaxReturn($retJson, 'json');
            }
        }else{
            $retJson = json_decode(json_encode($retJson),true);
            if($retJson['apicode'] !== "10000"){
                $this->assign("tips", "请求出错");
                $this->assign("result", $retJson);
            }else{
                $this->assign("tips", $userMsg['from']." -> ".$userMsg['to']."");
                $this->assign("result", $retJson['data']);
            }
            $this->display();
        }
    }

    public function flightCalendar(){
        I('get.from')?$userMsg['from'] = I('get.from'):$userMsg['from'] = "北京";
        I('get.to')?$userMsg['to'] = I('get.to'):$userMsg['to'] = "上海";
        import("ORG.Curl.Curl");
        $curl = new \Org\Curl\Curl();
        $curl->setJsonDecoder(json_decode);
        $apiUrl = "http://tg.m.kuxun.cn/plane-datepicker-json.html";
        $data = array("json"=>1,"depart"=>$userMsg['from'],"arrive"=>$userMsg['to']);
        $retJson = $curl->get($apiUrl,$data);
        if (I('get.qq')) {
            if (!is_numeric(I("get.qq")) || strlen(I("get.qq")) < 4 || strlen(I("get.qq")) > 10) {
                $this->ajaxReturn(array('code'=>400,'msg'=>"QQ号:".I("get.qq")."不合法"));
            } else {
                $this->ajaxReturn($retJson, 'json');
            }
        }else{
            $retJson = json_decode(json_encode($retJson),true);
            if(empty($retJson['go'])){
                $this->assign("tips", "请求出错");
                $this->assign("result", $retJson);
            }else{
                $this->assign("tips", $userMsg['from']." -> ".$userMsg['to']."");
                $this->assign("result", $retJson);
            }
            $this->display();
        }
    }

    public function idWater(){
        $imgtext = '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';
        if(IS_POST){
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize   =     3145728 ;// 设置附件上传大小
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
            $upload->savePath  =     ''; // 设置附件上传（子）目录
            // 上传文件
            $info   =   $upload->upload();
            if(!$info) {// 上传错误提示错误信息
//                $this->error($upload->getError());
                $this->assign('tips',$upload->getError());
            }else{// 上传成功
//                var_dump($info);
                $uploadFilePath = './Uploads/'.$info['file']['savepath'].$info['file']['savename'];
                $text = I('post.text')?I('post.text'):'该证件仅用于某某认证';
                $color = I('post.color')?I('post.color'):'#888888';
                $fontsize = I('post.fontsize')?I('post.fontsize'):20;
                $font = THINK_PATH.'Library/Think/Verify/zhttfs/2.ttf';
                $image = new \Think\Image();
                $fileDriver = new \Think\Storage\Driver\File();
                $image->open($uploadFilePath)->thumb(560, 390,\Think\Image::IMAGE_THUMB_FILLED)->text($text,$font,$fontsize,$color,\Think\Image::IMAGE_WATER_CENTER,0,25)->save($uploadFilePath);
                $image->open($uploadFilePath)->text($text,$font,$fontsize,$color,\Think\Image::IMAGE_WATER_CENTER,100,25)->save($uploadFilePath);
                $image->open($uploadFilePath)->text($text,$font,$fontsize,$color,\Think\Image::IMAGE_WATER_CENTER,-100,25)->save($uploadFilePath);
                $imgtext = base64_encode($fileDriver->read($uploadFilePath));
                $fileDriver->unlink($uploadFilePath);
            }
        }
        $this->assign('pic',$imgtext);
        $this->display();
    }

    public function daypay(){
        $work_day = I("work_day",21.75,'float');
        $real_work_day = I("real_work_day",21.75,'float');
        $lunch_pay_per_day = I("lunch_pay_per_day",10,'float');
        $phone_pay_per_month = I("phone_pay_per_month",100,'float');
        $trafic_pay_per_month = I("trafic_pay_per_month",100,'float');
        $day_pay = I("day_pay",150,'float');

        $base_salary = $day_pay*$real_work_day;
        $lunch_salary = $lunch_pay_per_day*$real_work_day;
        $phone_salary = $phone_pay_per_month*$real_work_day/$work_day;
        $trafic_salary = $trafic_pay_per_month*$real_work_day/$work_day;

        $total = $base_salary+$lunch_salary+$phone_salary+$trafic_salary;

        $data=array(
            'work_day'=>$work_day,
            'real_work_day'=>$real_work_day,
            'lunch_pay_per_day'=>$lunch_pay_per_day,
            'phone_pay_per_month'=>$phone_pay_per_month,
            'trafic_pay_per_month'=>$trafic_pay_per_month,
            'day_pay'=>$day_pay,
            'base_salary'=>$base_salary,
            'lunch_salary'=>$lunch_salary,
            'phone_salary'=>$phone_salary,
            'trafic_salary'=>$trafic_salary,
            'total'=>$total
        );

        if(IS_GET){
            $this->assign('result',$data);
            $this->display();
        }else{
            if($work_day<$real_work_day)
                $this->ajaxReturn(result(400,'实际工作天数不能大于当月所需工作天数'));
            $this->ajaxReturn(result(200,'ok',$data));
        }
    }
}