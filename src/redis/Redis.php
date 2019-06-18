<?php

namespace Lonban\Vcc\Redis;

use Illuminate\Support\Facades\Redis as Redis_Y;

class Redis
{
    public static $model = null;
    public static $name = null;
    public static $redis = null;

    public function __construct($model)
    {
        self::$name = preg_replace("/[\\\|\/]/",'_',$model);
        self::$redis = Redis_Y::connection('default');/*连接*/
        self::$model = $model;
    }

    /*获取*/
    public static function all()
    {
        $redis = self::$redis;
        $name = self::$name;
        if(!$redis->exists($name)){
            self::create();
        }
        $value = $redis->get($name);
        $obj = @unserialize($value);/*反序列化*/
        if(is_array($obj)||is_object($obj)){
            return $obj;
        }
        return $value;
    }
    /*以key获取*/
    public static function get($key=null)
    {
        $data = self::all();
        return isset($key)?$data[$key]:$data;
    }
    /*创建同数据模型里一样的数组*/
    public static function create()
    {
        $redis = self::$redis;
        $name = self::$name;
        $model = self::$model;
        $value = $model::all()->toArray();
        if(is_object($value)||is_array($value)){
            $value = serialize($value);/*序列化*/
        }
        return $redis->set($name,$value);
    }

    /*以id获取*/
    public static function getId($id)
    {
        $redis = self::$redis;
        $name = self::$name.':id';
        if(!$redis->exists($name)){
            self::createId();
        }
        $value = $redis->get($name);
        $obj = @unserialize($value);/*反序列化*/
        if(is_array($obj)||is_object($obj)){
            return $obj[$id];
        }
        return $value;
    }
    /*创建可以id获取的数组*/
    public static function createId()
    {
        $redis = self::$redis;
        $name = self::$name.':id';
        $model = self::$model;
        $value = $model::all()->toArray();
        if(is_object($value)||is_array($value)){
            $data = [];
            foreach ($value as $v){
                $data[$v['id']] = $v;
            }
            $value = serialize($data);/*序列化*/
        }
        return $redis->set($name,$value);
    }

    /*创建特殊数据在模型里完成，比如分类后的url*/
    public static function create2($k,$v)
    {
        $redis = self::$redis;
        $name = self::$name;
        return $redis->set($name.':'.$k, serialize($v));
    }
    /*获取特殊数据*/
    public static function get2($k)
    {
        $redis = self::$redis;
        $name = self::$name;
        $value = $redis->get($name.':'.$k);
        $obj = @unserialize($value);/*反序列化*/
        if(is_array($obj)||is_object($obj)){
            return $obj;
        }
        return $value;
    }

    /*删除*/
    public static function deleteAll()
    {
        $redis = self::$redis;
        $name = self::$name;
        $state = 0;
        foreach($redis->keys($name.'*') as $v){
            $state = $state+$redis->del($v);
        }
        return $state;
    }
}
