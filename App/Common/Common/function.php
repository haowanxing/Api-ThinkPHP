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
function result($code=200,$msg='',$data=null){
    return array('code'=>$code,'msg'=>$msg,'data'=>$data);
}