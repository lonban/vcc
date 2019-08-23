<?php

namespace Lonban\Vcc\Classes;

use Lonban\Vcc\Redis\VccCache;

class VccString
{
    public static function getRandomStr($length=null,$type='a-z0-9')
    {
        if(!$length){
            $length = mt_rand(2,12);
        }else if(is_array($length)){
            $length = mt_rand($length[0],$length[1]);
        }
        switch($type){
            case 'a-z':$chars='abcdefghijklmnopqrstuvwxyz';
            break;
            case 'a-z0-9':$chars='abcdefghijklmnopqrstuvwxyz0123456789';
            break;
            case 'a-zA-z0-9':$chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            break;
            case 'a-zA-z0-9@':$chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|\\';
            break;
            default:$chars=$type;
        }
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

    public static function reMonth($int)
    {
        switch($int){
            case 1:$to = '一';
                break;
            case 2:$to = '二';
                break;
            case 3:$to = '三';
                break;
            case 4:$to = '四';
                break;
            case 5:$to = '五';
                break;
            case 6:$to = '六';
                break;
            case 7:$to = '七';
                break;
            case 8:$to = '八';
                break;
            case 9:$to = '九';
                break;
            case 10:$to = '十';
                break;
            case 11:$to = '十一';
                break;
            case 12:$to = '十二';
                break;
            default:$to = '¿';
        }
        return $to;
    }
}