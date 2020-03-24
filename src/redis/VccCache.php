<?php

namespace Lonban\Vcc\Redis;

use Illuminate\Support\Facades\Redis;

class VccCache
{
    public static $redis = null;
    public static $model = null;
    private static $f = '|;|';

    public function __construct($name=1)
    {
        self::$model = 'vcc_cache_'.$name.'_';
        self::$redis = Redis::connection('cache');/*连接*/
    }

    public static function newSelf()
    {
        if(!isset(self::$redis) || !isset(self::$model)){
            new self();
        }
    }

    /*改*/
    public static function update($name,$val)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->getset($model.$name,$val);
    }
    /*自增计数，当计数值大于最大值$count2进一,$get2为真时返回*/
    public static function incr($name,$max=100000,$get2=0)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        $count = $redis->incr($model.$name);
        if($count>=$max){
            $count = 0;
            $redis->set($model.$name,0);
            $count2 = $redis->incr($model.$name.'count2');
            if($count2>=$max){
                $redis->set($model.$name.'count2',0);
            }
        }
        if($get2){
            $count2 = (int)$redis->get($model.$name.'count2');
            return [$count,$count2];
        }
        return $count;
    }
    /*$time秒，默认365天(365*24*60*60)*/
    public static function create($name,$val,$time=31536000)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        $redis->sadd($model.'name', $model.$name);
        if($time){
            return $redis->setex($model.$name, $time, $val);
        }
        return $redis->set($model.$name, $val);
    }
    /*在原来的字符串后面拼接，每拼接一段加上分隔符|;| 如超过最大存储长度，将前面一半的字符串删除，返回数组*/
    public static function push($name,$val,$max=100000000)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        $f = self::$f;
        //$redis->del($model.$name);
        if($redis->strlen ($model.$name)>$max){//判断字符串长度
            $str = substr($redis->get($model.$name),$max/2);
            $str = substr($str,strpos($str,$f)+strlen($f));
            self::create($name,$str);
        }else{
            if($redis->strlen ($model.$name)>0){//如果原来有数据就进行追加，否则创建
                $val = $f.$val;
                $redis->append($model.$name,$val);
            }else{
                self::create($name,$val);
            }
        }
        //return self::gets($name);
        return ['state'=>1];
    }
    /*查拼接字符串返回数组*/
    public static function gets($name)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        $f = self::$f;
        $data = $redis->get($model.$name);
        if(strpos($data,$f) === false){
            return [$data];
        }
        return explode($f,$data);
    }
    /*对拼接的字符串进行修改*/
    public static function updates($name,$key,$val=null)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        $f = self::$f;
        $data = $redis->get($model.$name);
        if(strpos($data,$f) === false){
            if($key==0){
                $data = $val;
            }
        }else{
            $data = explode($f,$data);
            if($val){
                $data[$key] = $val;
            }else{
                unset($data[$key]);
            }
            $data = implode($f,$data);
        }
        if($data){
            return self::create($name,$data);
        }else{
            return self::delete($name);
        }
    }
    /*查*/
    public static function get($name)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->get($model.$name);
    }
    public static function all()
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        $all = [];
        foreach($redis->smembers($model.'name') as $v){
            $all[$v] = $redis->get($v);
        }
        return $all;
    }

    /*删*/
    public static function delete($name)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        $redis->srem($model.'name',  $model.$name);
        return $redis->del($model.$name);
    }
    public static function deleteAll()
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        $all = 0;
        foreach($redis->smembers($model.'name') as $v){
            $all = $all+$redis->del($v);
        }
        $all = $all+$redis->del($model.'name');
        return $all;
    }
}
