<?php

namespace Lonban\Vcc\Redis;

use Illuminate\Support\Facades\Redis;

class VccCacheGXX
{
    public static $model = null;
    public static $redis = null;

    public function __construct($name=1)
    {
        self::$model = 'vcc_cache_group_x_'.$name.'_';
        self::$redis = Redis::connection('cache');/*连接*/
    }

    public static function newSelf()
    {
        if(!isset(self::$redis) || !isset(self::$model)){
            new self();
        }
    }

    /*改索引，$val要改索引的元素，把索引改成$key*/
    public static function update($name,$key,$val)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->zincrby($model.$name,$key,$val);
    }

    /*增*/
    public static function create($name,int $key,$val)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        $redis->sadd($model.'name', $model.$name);
        return $redis->zadd($model.$name,$key, $val);
    }

    /*查*/
    public static function get($name,$key,$key2=null)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->zrange($model.$name,  $key, $key2?$key2:$key);
    }
    public static function all($name)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->zrange($model.$name, 0, -1);
    }
    public static function allX()
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        $all = [];
        foreach($redis->smembers($model.'name') as $v){
            $all[$v] = $redis->zrange($v, 0, -1);
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
    public static function deleteX()
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
