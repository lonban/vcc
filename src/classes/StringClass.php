<?php

namespace Lonban\Vcc\Classes;

class StringClass
{
    public static function getRandomStr($length=0,$type=0,$convert=0,$move=0)
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
        if(!empty($convert)){
            $password = ($convert>0)?strtoupper($password):strtolower($password);
        }
        return trim($password,'.');
    }
}