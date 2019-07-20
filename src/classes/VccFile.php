<?php

namespace Lonban\Vcc\Classes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Lonban\Vcc\Exceptions\BasicException;

class VccFile
{
    static $drive = null;
    static $path = null;
    static $type = null;

    //驱动
    public static function drive()
    {
        self::$drive = config('vcc.file.drive');
        self::$type = config('vcc.file.type');
        self::$path = config('vcc.file.path');
        if(empty(self::$path)){
            self::$path = VccPath::getName();
        }
        return Storage::disk(self::$drive);
    }

    //文件上传
    public static function putFileAjax(Request $data,$input_name,$file_name=null)
    {
        if(!$input_name){throw new BasicException(sprintf('(%s) - input的name没有指定', $input_name));}
        if($file_name){
            self::drive()->putFileAs(self::$path.'/md5-name/',$data->file($input_name),$file_name,self::$type);
        }else{
            self::drive()->putFile(self::$path.'/md5-name/',$data->file($input_name),self::$type);
        }
        return 'Success!';
    }

    //写入文件
    public static function putFile($data,$name=null,$input_name=null)
    {
        if(is_string($data)){
            /*如果是字符串直接存*/
            self::drive()->put(self::$path.'/date-name/'.($name?$name:date('YmdHis').'_'.mt_rand(1,999).'.txt'),$data,self::$type);
        }else if(isset($input_name)){
            /*是文件*/
            self::putFileAjax($data,$input_name,$name,self::$type);
        }else{
            /*是数组或数组对象转成字符串后存*/
            self::drive()->put(self::$path.'/date-name/'.($name?$name:date('YmdHis').'_'.mt_rand(1,999).'.txt'),json_encode($data),self::$type);
        }
        return 'Success!';
    }

    //获取文件
    public static function getFile($file)
    {
        if(!self::drive()->exists($file)){
            throw new BasicException(sprintf('(%s) - 文件不存在', $file));
        }
        return self::drive()->get($file);
    }

    //获取文件Url
    public static function getFileUrl($file)
    {
        if(!self::drive()->exists(self::$path.'/date-name/'.$file)){
            throw new BasicException(sprintf('(%s) - 文件不存在', self::$path.'/date-name/'.$file));
        }
        $str = self::drive()->url('app/'.self::$path.'/date-name/'.$file);
        $str = preg_replace("/\\\/",'/',$str);
        $str = str_replace("//",'/',$str);
        return url($str);
    }
}