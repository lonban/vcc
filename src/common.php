<?php

use Illuminate\Http\Request;
use Lonban\Vcc\Classes\JsClass;
use Lonban\Vcc\Classes\PathClass;
use Lonban\Vcc\Classes\FileClass;

class Vcc
{
    //获取vcc的资源
    public static function vccSrc($path=null)
    {
        return JsClass::getSrc($path);
    }

    //获取相对资源路径
    public static function getSrc($str=null)
    {
        return PathClass::getSrc($str);
    }

    //获取相对文件路径
    public static function gitPath($str=null)
    {
        return PathClass::gitPath($str);
    }

    public static function getName($int)
    {
        return PathClass::getName($int);
    }

    //文件上传
    public static function putFileAjax(Request $data,$input_name,$file_name=null)
    {
        return FileClass::putFileAjax($data,$input_name,$file_name);
    }

    //写入文件
    public static function putFile($data,$name=null,$input_name=null)
    {
        return FileClass::putFile($data,$name,$input_name);
    }

    //获取文件
    public static function getFile($file)
    {
        return FileClass::getFile($file);
    }

    //获取文件Url
    public static function getFileUrl($file)
    {
        return FileClass::getFileUrl($file);
    }
}