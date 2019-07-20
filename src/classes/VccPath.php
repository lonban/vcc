<?php

namespace Lonban\Vcc\Classes;

class VccPath
{
    static $path = __DIR__; //项目路径，继承的子项目加上这一句就是子项目的路径，不加默认为vcc的项目路径
    /*
        定义局外路径，可以在继承项目里添加多个PathClass然后分别对它们定义根目录如'swagger-api\swagger-ui\dist'
        $path与$set_path二选一
        实例：必须在\common.php运行如VshopPath::setPath();
        也可以直接这样用：SwaggerApiPath::setPath()->getSrc('swagger-ui.css');
    */
    static $set_path = '/lonban/vcc/';
    static $set_path_str_end = null; //注意斜杠，意思是可以定义src的前面几级如\lonban\\结果："vendor"，如在vcc项目定义为vcc结果："vendor\lonban"
    static $src_splice = '/'; //可以定义为/aaa/，意思是src后面拼接如src/aaa/
    static $assets_splice = 'src/resources/views/assets/'; //可以定义为/aaa/，意思是/resources/views后面拼接如/resources/views/aaa/

    /*给出$set_path_str_end,与$set_path一样*/
    public static function setPath($str=null)
    {
        if(!$str){
            $str = static::$set_path;
        }
        $str = preg_replace("/\\\/",'/',$str);
        $str = str_replace("//",'/',$str);
        $str = trim($str,'/');
        $start = stripos(__DIR__,'vendor');
        $end = strrpos($str,'/');
        if(strpos($str,'.')){
            $str = substr($str,0,$end);
        }
        $end = strrpos($str,'/');
        static::$path = substr(__DIR__,0,$start+6).'/'.$str;
        static::$set_path_str_end = substr($str,$end+1);
        return new static();
    }
    /*获取的是命名空间："vendor\lonban\vshop"*/
    public static function getName()
    {
        $str = static::$path;
        $start = stripos($str,'vendor');
        if(static::$set_path_str_end){
            $end = static::$set_path_str_end;
        }else{
            $end = 'src';
        }

        $end = stripos($str,$end);
        return substr($str,$start,$end-$start);
    }
    /*获取的是链接src下的$path："http://vshop.a/vendor/lonban/vshop/src/{$path}"*/
    public static function getSrc($path=null)
    {
        if(static::$set_path_str_end){
            $end = static::$set_path_str_end;
        }else{
            $end = 'src';
        }
        $str = static::$path;
        $start = stripos($str,'vendor');
        $str = substr($str,$start,stripos($str,$end)-$start+strlen($end)).static::$src_splice.$path;
        $str = preg_replace("/\\\/",'/',$str);
        $str = str_replace("//",'/',$str);
        return url($str);
    }
    /*获取的是路径src下的$path：D:\www\vshop\vendor\lonban\vshop\src\{$path}*/
    public static function getPath($path=null)
    {
        if(static::$set_path_str_end){
            $end = static::$set_path_str_end;
        }else{
            $end = 'src';
        }
        $str = static::$path;
        $str = substr($str,0,stripos($str,$end)+strlen($end)).static::$src_splice.$path;
        $str = preg_replace('/\//','\\',$str);
        $str = str_replace('\\\\','\\',$str);
        return $str;
    }
    /*获取的是链接src/resources/views下的$path：http://vshop.a/vendor/lonban/vshop/src/resources/views/assets/{$path}*/
    public static function getAssets($path=null)
    {
        $str = static::$path;
        $start = stripos($str,'vendor');
        if(static::$set_path_str_end){
            $end = static::$set_path_str_end;
        }else{
            $end = 'src';
        }
        $end = stripos($str,$end);
        $str = substr($str,$start,$end-$start).static::$src_splice.static::$assets_splice.$path;
        $str = preg_replace("/\\\/",'/',$str);
        $str = str_replace("//",'/',$str);
        return url($str);
    }
}