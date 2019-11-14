<?php

namespace Lonban\Vcc\Classes;

use Lonban\Vcc\Redis\VccCache;

class VccArray
{
    public static $name = 'Lonban_Vcc_Classes_VccArray';

    /**
     * 递归出随机数据
     * @param $data array 数据，要从这个数组里随机挑出来一个
     * @param $min int 最小下标，从第几个开始
     * @param $exclude int 已经循环过的随机数，在第二次循环时排除掉的下标
     * @param $len int 数组的长度,递归次数
     * @return array|string|int 返回$data[n]的随机一个下标数据
     */
    public static function randData($data,$min=0,$exclude=null,$len=0)
    {
        $max = count($data)-1;
        $rand = $min;
        if($max>$min){
            $rand = rand($min,$max);
            if(isset($exclude)){
                static $exclude_arr;$exclude_arr[] = $exclude;
                if(in_array($rand,$exclude_arr)){
                    return self::randData($data,$min,$rand,$len);
                }
            }
        }
        if(isset($data[$rand])){
            return $data[$rand];
        }else{
            if($len<count($data)-1){
                return self::randData($data,$min,$rand,$len+1);
            }
        }
    }

    /**
     * 区别递归出有序asc数据
     * @param $data array 数据，要从这个数组里随机挑出来一个
     * @param $name string 储存的一个用于二级判断的名字
     * @return array|string|int 返回$data[n+1]的一个升序下标数据
     */
    public static function ascData($data,$name)
    {
        if(!isset($data)){return 0;}
        if(!is_array($data)){return 0;}
        $id = VccCache::get(self::$name.'asc_data'.$name);
        $max = count($data)-1;
        if(!isset($id)){
            $id = 0;
        }else{
            $id++;
        }
        if($id>$max){
            $id = 0;
        }
        VccCache::create(self::$name.'asc_data'.$name,$id);
        if(isset($data[$id])){
            return $data[$id];
        }else{
            return self::ascData($data,$name);
        }
    }
}