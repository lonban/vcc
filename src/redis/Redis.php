<?php

namespace Lonban\Vcc\Redis;

use Illuminate\Support\Facades\Redis as Redis_Y;

class Redis
{
    public static $model = null;
    public static $redis = null;

    public function __construct($model)
    {
        self::$model = $model;
        self::$redis = Redis_Y::connection('default');/*连接*/
    }

    /*获取*/
    public static function all()
    {
        $redis = self::$redis;
        $model = self::$model;
        if(!$redis->exists($model)){
            self::create();
        }
        $value = $redis->get($model);
        $obj = @unserialize($value);/*反序列化*/
        if(is_array($obj)||is_object($obj)){
            return $obj;
        }
        return $value;
    }
    public static function get($key=null)
    {
        $data = self::all();
        return isset($key)?$data[$key]:$data;
    }

    /*修改*/
    public static function update(int $key,array $val)
    {
        $redis = self::$redis;
        $model = self::$model;
        $data = self::all();
        $data = array_merge($data[$key],$val);
        if(is_object($data)||is_array($data)){
            $data = serialize($data);/*序列化*/
        }
        return $redis->set($model,$data);
    }
    /*创建*/
    public static function create()
    {
        $redis = self::$redis;
        $model = self::$model;
        $value = $model::all()->toArray();
        if(is_object($value)||is_array($value)){
            $data = [];
            foreach ($value as $v){
                $data[$v['id']] = $v;
            }
            $value = serialize($data);/*序列化*/
        }
        return $redis->set($model,$value);
    }

    /*删除*/
    public static function deleteAll()
    {
        $redis = self::$redis;
        $model = self::$model;
        return $redis->del($model);
    }
}
