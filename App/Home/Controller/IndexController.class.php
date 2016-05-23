<?php
namespace Home\Controller;

use Think\Controller;
use Think\Think;

class IndexController extends Controller
{
    public function index()
    {
        header("Location:http://www.fantwo.com/api-list.html");
//        $this->error("现在返回");
//        $this->show("hello world");
//        $this->assign("img",u('index/verifycode'));
//        $this->display();
//        if(!empty(I('get.code'))){
//            echo $this->check_verify(I('get.code'))?'pass':'nopass';
//        }
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
        $content = file_get_contents($requesturl);
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
            $this->assign("tips", "不符合规则,请求出错");
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
}