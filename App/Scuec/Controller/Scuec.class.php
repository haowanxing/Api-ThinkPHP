<?php
namespace Scuec\Controller;
use Think\Controller;
class Scuec{
    private $idsusrl    =   "http://ids.scuec.edu.cn/amserver/UI/Login?goto=http://ssfw.scuec.edu.cn/ssfw/j_spring_ids_security_check";
    private $student    =   null;
    private $password   =   null;
    private $targeturl  =   "http://ssfw.scuec.edu.cn/ssfw/zhcx/cjxx.do";
    private $varifyurl  =   "http://ssfw.scuec.edu.cn/ssfw/j_spring_ids_security_check";
    private $options    =   null;
    private $data   =   null;
    private $cookies    =   null;
    private $content;

    /**
     * 带账号密码的对象构造器
     * @param $username
     * @param $password
     */
    public function __construct($username,$password){
        $this->setStudent($username);
        $this->setPassword($password);
    }

    /**
     * 初始化进程,使得Curl信息为最新,若使用默认数据可直接初始化
     * @throws Exception 若没有初始化过账号密码则会报错
     */
    public function init(){
        if($this->getCookies() == null){
            $this->setCookies($this->getStudent()==null?"":$this->getStudent()."-cookies");
        }
        if(!isset($this->data)){
            if($this->getStudent() == null || $this->getPassword() == null){
                throw new Exception("暂未配置账号密码!");
            }
            $this->setData(array("IDToken0"=>"",
                "IDToken1"=>$this->getStudent(),
                "IDToken2"=>$this->getPassword(),
                "IDButton"=>"Submit",
                "goto"=>"aHR0cDovL3NzZncuc2N1ZWMuZWR1LmNuL3NzZncval9zcHJpbmdfaWRzX3NlY3VyaXR5X2NoZWNr",
                "encoded"=>"true",
                "gx_charset"=>"UTF-8"));
        }
        $this->setData(http_build_query($this->getData()));
        if(!isset($options)){
            $this->options=array(CURLOPT_URL=>$this->getIdsusrl(),
                CURLOPT_HEADER=>false,
                CURLOPT_RETURNTRANSFER=>true,
                CURLOPT_TIMEOUT=>10,
                CURLOPT_POST=>1,
                CURLOPT_POSTFIELDS=>$this->getData(),
                CURLOPT_COOKIEFILE=>$this->getCookies(),
                CURLOPT_COOKIEJAR=>$this->getCookies()
            );
        }
        $this->setOptions($this->options);
    }

    /**
     *  执行进程,完毕后结果储存在Content(数组)属性中
     */
    public function run(){
        //初步登陆获取cookies
        $ch = curl_init();
        curl_setopt_array($ch,$this->getOptions());
        curl_exec($ch);
        curl_close($ch);
        //二次验证 进入教务系统
        $ch = curl_init();;
        curl_setopt_array($ch,array(CURLOPT_URL=>$this->getVarifyurl(),
            CURLOPT_HEADER=>false,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_TIMEOUT=>10,
            CURLOPT_COOKIEFILE=>$this->getCookies(),
            CURLOPT_COOKIEJAR=>$this->getCookies()
        ));
        curl_exec($ch);
        curl_close($ch);
        //带Cookie访问教务系统页面并输出
        $ch = curl_init();
        curl_setopt_array($ch,array(CURLOPT_URL=>$this->getTargeturl(),
            CURLOPT_HEADER=>false,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_TIMEOUT=>10,
            CURLOPT_COOKIEFILE=>$this->getCookies(),
            CURLOPT_COOKIEJAR=>$this->getCookies()
        ));
        $output = curl_exec($ch);
        curl_close($ch);
        //print_r($output);
        $this->content = $output;
        if(file_exists($this->getCookies())){
            @unlink($this->getCookies());
        }
    }

    /**
     * @param $options
     * @throws Exception
     */
    public function setOptions($options)
    {
        if(gettype($options)!='array'){
            throw new Exception ("The Parameter 'Options' must be Array");
        }
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getVarifyurl()
    {
        return $this->varifyurl;
    }

    /**
     * @param string $varifyurl
     */
    public function setVarifyurl($varifyurl)
    {
        $this->varifyurl = $varifyurl;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function getTargeturl()
    {
        return $this->targeturl;
    }

    /**
     * @param string $targeturl
     */
    public function setTargeturl($targeturl)
    {
        $this->targeturl = $targeturl;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param null $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return null
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * @param null $student
     */
    public function setStudent($student)
    {
        $this->student = $student;
    }

    /**
     * @return string
     */
    public function getIdsusrl()
    {
        return $this->idsusrl;
    }

    /**
     * @param string $idsusrl
     */
    public function setIdsusrl($idsusrl)
    {
        $this->idsusrl = $idsusrl;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param null $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @param string $cookies
     */
    public function setCookies($cookies)
    {
        for($i=0;$i<5;$i++){
            $cookies .= rand(0,9);
        }
        $this->cookies = $cookies.".txt";
    }

    /**
     * 返回结果(数组)
     * @return mixed
     */
    public function getContent()
    {
        $code = 200;
        if(empty($this->content)){
            $code = 404;
        }
        $content = array("code"=>$code,"content"=>$this->content);
        return $content;
    }
}