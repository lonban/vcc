<?php

namespace Lonban\Vcc\Classes;

use Lonban\Vcc\Redis\VccCache;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VccImg
{
    public static $name = 'Lonban_Vcc_Classes_VccImg';

    /**
     * 生成二维码，并转成64码存到缓存里
     */
    public static function getCodeImg($content)
    {
        if($code_img = VccCache::get(self::$name.'code_img'.$content)){
            return $code_img;
        }else{
            $code_img = 'data:image/png;base64,'.base64_encode(QrCode::format('png')->size(80)->color(66,66,0)->margin(0)->encoding('UTF-8')->generate($content));
            VccCache::create(self::$name.'code_img'.$content,$code_img);
        }
        return $code_img;
    }
}