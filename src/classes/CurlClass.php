<?php

namespace Lonban\Vcc\Classes;

class CurlClass
{
    /*获取头信息*/
    public static function getHead($url,$time=1)
    {
        $ch = curl_init();//初始化curl模块
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $time);//请求超时n秒
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//是否不显示返回的信息
        curl_setopt($ch, CURLOPT_USERAGENT, BrowserClass::getAgent());//模拟ua
        $content = curl_exec($ch);//执行cURL
        if(curl_errno($ch)){
            return curl_error($ch);
        }
        $headers = curl_getinfo($ch);//返回的头信息，包括302重定向的地址
        curl_close($ch);//关闭cURL资源，并且释放系统资源
        return $headers;
    }
    /*获取信息*/
    public static function getContent($url,$time=1)
    {
        $ch = curl_init();//初始化curl模块
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $time);//请求超时n秒
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//是否不显示返回的信息
        curl_setopt($ch, CURLOPT_USERAGENT, BrowserClass::getAgent());//模拟ua
        $content = curl_exec($ch);//执行cURL
        if(curl_errno($ch)){
            return curl_error($ch);
        }
        curl_close($ch);//关闭cURL资源，并且释放系统资源
        return $content;
    }
}