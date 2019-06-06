<?php

namespace Lonban\Vcc\Redis;

use Illuminate\Support\Facades\Redis as Redis_Y;

class VccCache
{
    public static $redis = null;

    public function __construct()
    {
        self::$redis = Redis_Y::connection('cache');/*连接*/
    }

    public static function newSelf()
    {
        if(isset(self::$redis)){
            return self::$redis;
        }else{
            return self::$redis = Redis_Y::connection('cache');/*连接*/
        }
    }

    /*改*/
    public static function update($k,$v)
    {
        $redis = self::newSelf();
        return $redis->getset('vcc_:'.$k, $v);
    }
    /*自增*/
    /*public static function urlIncr($url,$key)
    {
        $redis = self::newSelf();
        return $redis->incr('vcc_:'.$url.'['.$key.']');
    }*/
    public static function incr($k)
    {
        $redis = self::newSelf();
        return $redis->incr('vcc_:'.$k);
    }

    /*增*/
    public static function create($k,$v)
    {
        $redis = self::newSelf();
        return $redis->set('vcc_:'.$k, $v);
    }

    /*查*/
    public static function get($k)
    {
        $redis = self::newSelf();
        return $redis->get('vcc_:'.$k);
    }
    public static function all()
    {
        $redis = self::newSelf();
        $data = [];
        foreach($redis->keys('vcc_:*') as $v){
            $data[] = $v.'::'.$redis->get($v);
        }
        return $data;
    }

    /*删*/
    public static function delete($k)
    {
        $redis = self::newSelf();
        return $redis->del('vcc_:'.$k);
    }
    public static function deleteAll()
    {
        $redis = self::newSelf();
        $i = 0;
        foreach($redis->keys('vcc_:*') as $v){
            $i = $i+$redis->del($v);
        }
        return $i;
    }
}
