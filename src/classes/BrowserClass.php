<?php

namespace Lonban\Vcc\Classes;

class BrowserClass
{
    public static function curl_get_file_contents($URL)
    {
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($c, CURLOPT_HEADER, 1);//输出远程服务器的header信息
        curl_setopt($c, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727;http://www.baidu.com)');
        curl_setopt($c, CURLOPT_URL, $URL);
        $contents = curl_exec($c);
        curl_close($c);
        if($contents){
            dd($contents);
        }else{
            return FALSE;
        }
    }
}