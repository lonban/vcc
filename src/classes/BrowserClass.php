<?php

namespace Lonban\Vcc\Classes;

class BrowserClass
{
    public static $data = null;

    //不同环境下获取真实的IP
    public static function getIp()
    {
        $data = self::$data;
        if(!empty($_GET['ip'])){
            return $_GET['ip'];
        }
        if(!empty($data['real_ip'])){
            return $data['real_ip'];
        }
        //判断服务器是否允许$_SERVER,不允许就使用getenv获取
        if(isset($_SERVER) && defined('HTTP_X_FORWARDED_FOR')){
            if(isset($_SERVER[HTTP_X_FORWARDED_FOR])){
                $data['real_ip'] = $_SERVER[HTTP_X_FORWARDED_FOR];
            }elseif(isset($_SERVER[HTTP_CLIENT_IP])) {
                $data['real_ip'] = $_SERVER[HTTP_CLIENT_IP];
            }else{
                $data['real_ip'] = $_SERVER[REMOTE_ADDR];
            }
        }else{
            if(getenv("HTTP_X_FORWARDED_FOR")){
                $data['real_ip'] = getenv( "HTTP_X_FORWARDED_FOR");
            }elseif(getenv("HTTP_CLIENT_IP")) {
                $data['real_ip'] = getenv("HTTP_CLIENT_IP");
            }else{
                $data['real_ip'] = getenv("REMOTE_ADDR");
            }
        }
        return self::$data['real_ip'] = preg_match ( '/[\d\.]{7,15}/', $data['real_ip'], $matches ) ? $matches [0] : '';
    }
}