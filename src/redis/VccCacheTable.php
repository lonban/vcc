<?php

namespace Lonban\Vcc\Redis;

use Illuminate\Support\Facades\Redis as Redis_Y;

class VccCacheTable
{
    public static $model = null;
    public static $redis = null;

    public function __construct($model)
    {
        self::$model = $model;
        self::$redis = Redis_Y::connection('default');/*连接*/
    }

    /*改*/
    public static function update($id)
    {
        self::$redis->zremrangebyscore(self::$model, $id,$id);/*删除*/
        return self::$redis->zadd(self::$model,$id,self::find($id));/*添加*/
    }
    /*增*/
    public static function create()
    {
        self::$redis->del(self::$model);/*删除*/
        foreach(self::$model::all() as $v){
            self::$redis->zadd(self::$model,$v['id'],$v);
        }
        return self::$redis->zcard(self::$model);/*共n条*/
    }
    public static function push($id)
    {
        return self::$redis->zadd(self::$model,$id,self::find($id));
    }

    /*查*/
    public static function get($id)
    {
        return self::$redis->zrange(self::$model,$id,$id);
    }
    /*如果有数据直接返回，没有就创建后返回*/
    public static function all()
    {
        if(!self::$redis->exists(self::$model)){
            foreach(self::$model::all() as $v){
                self::$redis->zadd(self::$model,$v['id'],$v);
            }
        }
        foreach(self::$redis->zrange(self::$model,0,-1) as $v){
            $data[] = json_decode($v,true);
        }
        return $data;
    }
    /*分页*/
    public static function getPaginate()
    {
        //
    }

    /*删*/
    public static function delete($id)
    {
        return self::$redis->zremrangebyscore(self::$model, $id,$id);
    }
    public static function deleteAll()
    {
        return self::$redis->del(self::$model);
    }
}
