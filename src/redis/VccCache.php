<?php

namespace Lonban\Vcc\Redis;

use Illuminate\Support\Facades\Redis as Redis_Y;

class VccCache
{
    public static $redis = null;
    public static $model = null;

    public function __construct($name=1)
    {
        self::$model = 'vcc_cache_'.$name.'_';
        self::$redis = Redis_Y::connection('cache');/*连接*/
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
    public static function incr($name,$max=100000)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        if(($i = $redis->incr($model.$name))>$max){
            $redis->del($model.$name);
            return false;
        }
        return $i;
    }
    public static function push($name,$val,$max=1000000)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        if($redis->strlen ($model.$name)>$max){
            $redis->del($model.$name);
            return false;
        }
        return $redis->append($model.$name,$val);
    }
    /*$time秒*/
    public static function create($name,$val,$time=null)
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
