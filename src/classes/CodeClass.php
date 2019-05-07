<?php

namespace Lonban\Vcc\Classes;

class CodeClass
{
    /*$key = '12sdfsdafsdafafsdfsdfsdfsadfsdfsadfsdfasdfasdfdsafsdfsdfsdafsdfasdfsdfsdfafsdfsdfasdfsdfsadf3';
    $iv = substr($key, 0, 16);*/
    /**
     * 加密字符串
     * @param string $text 字符串
     * @param string $key 加密key
     * @param string $iv 加密向量
     * @return string
     */
    public static function encodeSSL($text, $key, $iv='')
    {
        return base64_encode(openssl_encrypt($text, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv));
    }

    /**
     * 解密字符串
     * @param string $text 字符串
     * @param string $key 加密key
     * @param string $iv 加密向量
     * @return object
     */
    public static function decodeSSL($text, $key, $iv='')
    {
        return openssl_decrypt(base64_decode($text), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    }

    /*对js进行加密，其实就是把代码转化为十六进制*/
    public static function encodeJS($text)
    {
        $text = iconv('UTF-8', 'UCS-2', $text);
        $len = strlen($text);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2)
        {
            $c = $text[$i];
            $c2 = $text[$i + 1];
            if (ord($c) > 0){
                // 两个字节的文字
                $str .= '\u'.base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16);
            }else{
                $str .= $c2;
            }
        }
        $str=bin2hex($str);
        $res='';
        for($i=0;$i<strlen($str)-1;$i+=2){
            $tmp='\x'.$str[$i].$str[$i+1];
            $res.=$tmp;
        }
        return 'eval(\''.$res.'\')';
    }
}