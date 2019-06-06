<?php

namespace Lonban\Vcc\Redis;

use Illuminate\Support\Facades\Redis as Redis_Y;

class VccCacheGroup
{
    public static $redis = null;
    public static $model = null;

    public function __construct($name = 1)
    {
        self::$redis = Redis_Y::connection('cache');/*连接*/
        self::$model = 'vcc_group:'.$name;
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
        return $redis->lset($model, $k, $v);
    }

    /*增*/
    public static function create($v)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->rpush($model, $v);
    }

    /*查*/
    public static function get($k)
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->lindex($model, $k);
    }
    public static function all($type='all')
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        if($type=='all'){
            return ['data'=>$redis->lrange($model, 0, -1),'len'=>$redis->llen($model)];
        }else if($type=='len'){
            return $redis->llen($model);
        }else{
            return $redis->lrange($model, 0, -1);
        }
    }
    public static function allX()
    {
        self::newSelf();
        $redis = self::$redis;
        $i = [];
        foreach($redis->keys('vcc_group:*') as $v){
            $i[] = $redis->lrange($v, 0, -1);
        }
        return $i;
    }

    /*删*/
    public static function delete()
    {
        self::newSelf();
        $redis = self::$redis;
        $model = self::$model;
        return $redis->del($model);
    }
    public static function deleteX()
    {
        self::newSelf();
        $redis = self::$redis;
        $i = [];
        foreach($redis->keys('vcc_group:*') as $v){
            $i[] = $redis->del($v);
        }
        return $i;
    }
}
