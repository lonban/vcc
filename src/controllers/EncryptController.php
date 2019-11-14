<?php

namespace Lonban\Vcc\Controllers;

use Illuminate\Http\Request;
use Lonban\Vcc\Classes\VccEncryptPHP;
use Lonban\Vcc\Classes\VccEncrypt;

class EncryptController extends CommonController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($dir=null,$file=null)
    {
        /*$path = base_path('vendor\lonban\\'.$dir.'\src\classes\\'.$file);
        if(is_file($path)){
            return CodeClass::newFile($path);
        }
        return 0;*/
    }

    public function showPhpInput()
    {
        return view('vcc::encrypt/php');
    }

    public function getPhpString(Request $request)
    {
        return view('vcc::encrypt/php')->with('data',VccEncryptPHP::newString($request['string']));
    }

    public function showJsInput()
    {
        return view('vcc::encrypt/js');
    }

    /*加密成计算机code获取js*/
    public function getJsString(Request $request)
    {
        $text = preg_replace('/\/\*(\s|.)*?\*\//', '', $request['string']);
        $text = '/*'.(time()*883256).'*/'.$text;
        $text = VccEncrypt::encodeJS($text);
        return view('vcc::encrypt/js')->with('data',VccEncrypt::encodeJS($text));
    }
}
