<?php
/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 17/3/27
 * Time: 上午12:17
 */
/**
 * @param int    $code
 * @param string $msg
 * @param null   $data
 *
 * @return array
 */
function result($code = 200, $msg = '', $data = null){
    return array('code' => $code, 'msg' => $msg, 'data' => $data);
}

/**
 * 解决file_get_contents获取为空时，采用curl获取
 *
 * @param $url
 *
 * @return bool|mixed|string
 */
function file_get_content($url){
    if (function_exists('file_get_contents')){
        $file_contents = @file_get_contents($url);
    }
    if ($file_contents == ”){
        $ch = curl_init();
        $timeout = 30;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        curl_close($ch);
    }
    return $file_contents;
}