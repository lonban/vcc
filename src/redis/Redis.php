<?php

namespace Lonban\Vcc\Redis;

use Illuminate\Support\Facades\Redis as Redis_Y;

class Redis
{
    public static $DB = null;
    public static $model = null;
    public static $redis = null;

    public function __construct($model)
    {
        self::$DB = $model;
        self::$model = preg_replace("/[\\\|\/]/",'_',$model);
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
    /*以key获取*/
    public static function get($key=null)
    {
        $redis = self::$redis;
        $model = self::$model;
        if(!$redis->exists($model)){
            self::create();
        }
        $value = $redis->get($model);
        $obj = @unserialize($value);/*反序列化*/
        if(is_array($obj)||is_object($obj)){
            return isset($key)?$obj[$key]:$obj;
        }
        return $value;
    }
    /*创建同数据模型里一样的数组*/
    public static function create()
    {
        $DB = self::$DB;
        $redis = self::$redis;
        $model = self::$model;
        $value = $DB::all()->toArray();
        if(is_object($value)||is_array($value)){
            $value = serialize($value);/*序列化*/
        }
        return $redis->set($model,$value);
    }

    /*以id获取*/
    public static function getId($id)
    {
        $redis = self::$redis;
        $model = self::$model.'_id';
        if(!$redis->exists($model)){
            self::createId();
        }
        $value = $redis->get($model);
        $obj = @unserialize($value);/*反序列化*/
        if(is_array($obj)||is_object($obj)){
            return $obj[$id];
        }
        return $value;
    }
    /*创建可以id获取的数组*/
    public static function createId()
    {
        $DB = self::$DB;
        $redis = self::$redis;
        $model = self::$model.'_id';
        $value = $DB::all()->toArray();
        if(is_object($value)||is_array($value)){
            $data = [];
            foreach ($value as $v){
                $data[$v['id']] = $v;
            }
            $value = serialize($data);/*序列化*/
        }
        return $redis->set($model,$value);
    }

    /*创建特殊数据在模型里完成，比如分类后的url*/
    public static function create2($name,$val,$time=null)
    {
        $redis = self::$redis;
        $model = self::$model;
        if(is_object($val)||is_array($val)){
            $val = serialize($val);/*序列化*/
        }
        $redis->sadd($model.'_name', $model.'_'.$name);
        if($time){
            return $redis->setex($model.'_'.$name,$time,$val);
        }
        return $redis->set($model.'_'.$name, $val);
    }
    /*获取特殊数据*/
    public static function get2($name)
    {
        $redis = self::$redis;
        $model = self::$model;
        $value = $redis->get($model.'_'.$name);
        $obj = @unserialize($value);/*反序列化*/
        if(is_array($obj)||is_object($obj)){
            return $obj;
        }
        return $value;
    }
    public static function all2()
    {
        $redis = self::$redis;
        $model = self::$model;
        $all = [];
        foreach($redis->smembers($model.'_name') as $v){
            $all[$v] = $redis->get($v);
        }
        return $all;
    }

    /*删除*/
    public static function deleteAll()
    {
        $redis = self::$redis;
        $model = self::$model;
        $all = 0;
        foreach($redis->smembers($model.'_name') as $v){
            $all = $all+$redis->del($v);
        }
        $all = $all+$redis->del($model);
        $all = $all+$redis->del($model.'_id');
        $all = $all+$redis->del($model.'_name');
        return $all;
    }
}