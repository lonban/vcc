<?php

namespace Lonban\Vcc\Redis;

use Illuminate\Support\Facades\Redis;

class VccCacheG
{
    public static $redis = null;
    public static $model = null;

    public function __construct($name=1)
    {
        self::$model = 'vcc_cache_group_'.$name.'_';
        self::$redis = Redis::connection('cache');/*连接*/
    }

    public static function newSelf()
    {
        if(!isset(self::$redis) || !isset(self::$model)){
            new self();
        }
    }

    /*改*/
    public static function update($name,$key,$val)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->lset($model.$name, $key, $val);
    }

    /*增*/
    public static function create($name,$val)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        $redis->sadd($model.'name', $model.$name);
        return $redis->rpush($model.$name, $val);
    }

    /*查*/
    public static function get($name,$key,$key2=null)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        if($key2){
            return $redis->lrange($model.$name, $key, $key2);
        }
        return $redis->lindex($model.$name, $key);
    }
    public static function all($name,$type=null)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        if(!$type){
            return $redis->lrange($model.$name, 0, -1);
        }
        if($type=='len'){
            return $redis->llen($model.$name);
        }
        if($type=='all'){
            return ['data'=>$redis->lrange($model.$name, 0, -1),'len'=>$redis->llen($model.$name)];
        }
    }
    public static function allX()
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        $all = [];
        foreach($redis->smembers($model.'name') as $v){
            $all[$v] = $redis->lrange($v, 0, -1);
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