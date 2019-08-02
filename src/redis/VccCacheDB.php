<?php

namespace Lonban\Vcc\Redis;

use Illuminate\Support\Facades\Redis;

class VccCacheDB
{
    public static $DB = null;
    public static $NAME = [];
    public static $REDIS = null;

    public function __construct($model)
    {
        self::$DB = $model;
        $model = preg_replace("/[\\\|\/]/",'_',$model);
        self::$NAME['db_1'] = $model.'_db_1';
        self::$NAME['db_2'] = $model.'_db_2';
        self::$NAME['db_2_all_name'] = $model.'_name';
        self::$NAME['db_id'] = $model.'_db_id';
        self::$REDIS = Redis::connection('default');/*连接*/
    }

    /*获取*/
    public static function all()
    {
        $db_name = self::$NAME['db_1'];
        if(!self::$REDIS->exists($db_name)){
            self::create();
        }
        $value = self::$REDIS->get($db_name);
        $obj = @unserialize($value);/*反序列化*/
        if(is_array($obj)||is_object($obj)){
            return $obj;
        }
        return $value;
    }
    /*以数字索引获取*/
    public static function get($key=null)
    {
        $db_name = self::$NAME['db_1'];
        if(!self::$REDIS->exists($db_name)){
            self::create();
        }
        $value = self::$REDIS->get($db_name);
        $obj = @unserialize($value);/*反序列化*/
        if(is_array($obj)||is_object($obj)){
            return isset($key)?$obj[$key]:$obj;
        }
        return $value;
    }
    /**
     * 按条件获取
     * $where在指定后$val等于$where，类似db:where('id','>',1)
     * @param $key int|string 要匹配的键
     * @param $v_w $val int|string 要匹配的值
     * @param $w_v $where string 按什么条件进行匹配><==
     * @return string|array|0;
     */
    public static function where($key,$v_w,$w_v=null)
    {
        $db_name = self::$NAME['db_1'];
        if(!self::$REDIS->exists($db_name)){
            self::create();
        }
        $value = self::$REDIS->get($db_name);
        $obj = @unserialize($value);/*反序列化*/
        $data = [];
        if($w_v){
            $val = $w_v;
            $where = $v_w;
            if($where == '='){
                $where = '==';
            }
        }else{
            $val = $v_w;
            $where = '==';
        }
        $where = 'if($v[$key]'.$where.'$val){$data[] = $v;}';
        if(is_array($obj)||is_object($obj)){
            foreach($obj as $v){
                if(isset($v[$key])){
                    eval($where);
                }
            }
        }
        return $data;
    }
    /*创建同数据模型里一样的数组*/
    public static function create()
    {
        $db_name = self::$NAME['db_1'];
        $value = self::$DB::all()->toArray();
        if(is_object($value)||is_array($value)){
            $value = serialize($value);/*序列化*/
        }
        return self::$REDIS->set($db_name,$value);
    }

    /*以id获取*/
    public static function getId($id)
    {
        $db_id = self::$NAME['db_id'];
        if(!self::$REDIS->exists($db_id)){
            self::createId();
        }
        $value = self::$REDIS->get($db_id);
        $obj = @unserialize($value);/*反序列化*/
        if(is_array($obj)||is_object($obj)){
            return $obj[$id];
        }
        return $value;
    }
    /*创建可以id获取的数组*/
    public static function createId()
    {
        $db_id = self::$NAME['db_id'];
        $value = self::$DB::all()->toArray();
        if(is_object($value)||is_array($value)){
            $data = [];
            foreach ($value as $v){
                $data[$v['id']] = $v;
            }
            $value = serialize($data);/*序列化*/
        }
        return self::$REDIS->set($db_id,$value);
    }
    /*仅对redis做修改，不动db库*/
    public static function updateId(int $id,array $arr_data)
    {
        $db_id = self::$NAME['db_id'];
        if(!self::$REDIS->exists($db_id)){
            self::createId();
        }
        $value = self::$REDIS->get($db_id);
        $obj = @unserialize($value);/*反序列化*/
        if(is_object($obj)||is_array($obj)){
            foreach($arr_data as $k=>$v){
                if(isset($obj[$id][$k])){
                    $obj[$id][$k] = $v;
                }
            }
            $value = serialize($obj);/*序列化*/
            self::$REDIS->set($db_id,$value);
            return $obj[$id];
        }
        return $value;
    }

    /*创建特殊数据在模型里完成，比如分类后的url*/
    public static function create2($name,$val,$time=null)
    {
        $db_name = self::$NAME['db_2'].$name;
        $db_2_all_name = self::$NAME['db_2_all_name'];
        if(is_object($val)||is_array($val)){
            $val = serialize($val);/*序列化*/
        }
        self::$REDIS->sadd($db_2_all_name,$db_name);
        if($time){
            return self::$REDIS->setex($db_name,$time,$val);
        }
        return self::$REDIS->set($db_name,$val);
    }
    /*获取特殊数据*/
    public static function get2($name,$key=null)
    {
        $db_name = self::$NAME['db_2'].$name;
        $value = self::$REDIS->get($db_name);
        $obj = @unserialize($value);/*反序列化*/
        if(is_array($obj)||is_object($obj)){
            if($key){
                if(isset($obj[$key])){
                    return $obj[$key];
                }else{
                    return null;
                }
            }else{
                return $obj;
            }
        }
        return $value;
    }
    /*仅对redis做修改，不动db库*/
    public static function update2($name,$val,$time=null)
    {
        $db_name = self::$NAME['db_2'].$name;
        $value = self::$REDIS->get($db_name);
        $obj = @unserialize($value);/*反序列化*/
        if(is_object($obj)||is_array($obj)){
            if(is_object($val)||is_array($val)){
                foreach($val as $k=>$v){
                    if(isset($obj[$k])){
                        $obj[$k] = $v;
                    }
                }
            }
            $value = serialize($obj);/*序列化*/
            if($time){
               self::$REDIS->setex($db_name,$time,$val);
            }else{
                self::$REDIS->set($db_name,$value);
            }
            return $obj;
        }
        return $value;
    }
    public static function all2()
    {
        $db_2_all_name = self::$NAME['db_2_all_name'];
        $all = [];
        foreach(self::$REDIS->smembers($db_2_all_name) as $v){
            $all[$v] = self::$REDIS->get($v);
        }
        return $all;
    }
    /*删除*/
    public static function delete2($name)
    {
        $db_name = self::$NAME['db_2'].$name;
        $db_2_all_name = self::$NAME['db_2_all_name'];
        self::$REDIS->srem($db_2_all_name,$db_name);
        return self::$REDIS->del($db_name);
    }

    /*删除*/
    public static function deleteAll()
    {
        $db_id = self::$NAME['db_id'];
        $db_name = self::$NAME['db_1'];
        $db_2_all_name = self::$NAME['db_2_all_name'];
        $all = 0;
        foreach(self::$REDIS->smembers($db_2_all_name) as $v){
            $all = $all+self::$REDIS->del($v);
        }
        $all = $all+self::$REDIS->del($db_id);
        $all = $all+self::$REDIS->del($db_name);
        $all = $all+self::$REDIS->del($db_2_all_name);
        return $all;
    }
}