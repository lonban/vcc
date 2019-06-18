<?php

namespace Lonban\Vcc\Redis;

use Illuminate\Support\Facades\Redis as Redis_Y;

class VccCache
{
    public static $redis = null;
    public static $model = null;

    public function __construct($model='vcc_')
    {
        self::$redis = Redis_Y::connection('cache');/*连接*/
        self::$model = $model;
    }

    public static function newSelf()
    {
        if(!isset(self::$redis) || !isset(self::$model)){
            new self();
        }
    }

    /*改*/
    public static function update($k,$v)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->getset($model.':'.$k, $v);
    }
    public static function push($k,$v)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->append($model.':'.$k,$v);
    }
    /*自增*/
    /*public static function urlIncr($url,$key)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->incr($model.':'.$url.'['.$key.']');
    }*/
    public static function incr($k)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->incr($model.':'.$k);
    }
    public static function create($k,$v)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->set($model.':'.$k, $v);
    }

    /*查*/
    public static function get($k)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->get($model.':'.$k);
    }
    public static function all()
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        $data = [];
        foreach($redis->keys($model.':*') as $v){
            $data[] = $v.'::'.$redis->get($v);
        }
        return $data;
    }

    /*删*/
    public static function delete($k)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->del($model.':'.$k);
    }
    public static function deleteAll()
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        $i = 0;
        foreach($redis->keys($model.':*') as $v){
            $i = $i+$redis->del($v);
        }
        return $i;
    }
}
