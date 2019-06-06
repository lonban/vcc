<?php

namespace Lonban\Vcc\Redis;

trait Monitor
{
    //注意,必须以 boot 开头
    public static function bootMonitor()
    {
        foreach(static::getModelEvents() as $event) {
            static::$event(function ($model){
                $model->setRemind(static::class);
            });
        }
    }

    public static function getModelEvents()
    {
        if(isset(static::$recordEvents)){
            return static::$recordEvents;
        }
        return ['saved','deleted'];
    }

    public function setRemind($model)
    {
        $redis = new Redis($model);
        $redis->deleteAll();
    }
}