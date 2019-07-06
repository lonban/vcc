<?php

namespace Lonban\Vcc\Classes;

use Lonban\Vcc\Redis\VccCache;

class StringClass
{
    public static function getRandomStr($length=null)
    {
        $length = $length?$length:mt_rand(2,12);
        $chars='abcefghijklmnopqrstuvwxyz0123456789.';
        $str = '';
        $strlen = strlen($chars);
        for ( $i = 0; $i < $length; $i++ ){
            $str .= $chars[ mt_rand(0, $strlen - 1) ]; //取字符数组 $chars 的任意元素
        }
        return trim($str,'.');
    }

    /*慢160倍*/
    public static function getRandomStr_R($length=null,$auto=null)
    {
        $str = VccCache::get('getRandomStr');
        if(!$str || VccCache::incr('getRandomStr_Views',1000)===false){
            $str = self::getRandomStr(32);
            VccCache::create('getRandomStr',$str);
        }
        if($auto){
            $length = mt_rand(2,$length);
        }else{
            $length = $length?$length:mt_rand(2,8);
        }
        $str = substr($str,mt_rand(1,23),$length);
        return trim($str,'.');
    }

    public static function getRandomStr2($length=null,$type=null,$convert=null,$move=null)
    {
        if(!isset($length)){
            $length = mt_rand(2,16);
        }else if(!isset($move)){
            $length = mt_rand(2,$length);
        }
        switch ($type){
            case 1:$chars='1234567890';
                break;
            case 2:$chars='abcdefghijklmnopqrstuvwxyz';
                break;
            case 3:$chars='abcdefghijklmnopqrstuvwxyz0123456789';
                break;
            case 4:$chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 5:$chars='abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
                break;
            case 6:$chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                break;
            case 7:$chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|\\';
                break;
            default:$chars='abcdefghijklmnopqrstuvwxyz0123456789.';
        }
        $password = '';
        for ( $i = 0; $i < $length; $i++ ){
            //$password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1); //使用 substr 截取$chars中的任意一位字符
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ]; //取字符数组 $chars 的任意元素
        }
        if(isset($convert)){
            $password = ($convert>0)?strtoupper($password):strtolower($password);
        }
        return trim($password,'.');
    }
}