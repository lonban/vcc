<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix'=>'vcc','namespace'=>'Lonban\Vcc\Controllers'],function(){
    Route::resource('/','IndexController');
});

Route::group(['prefix'=>'vcc/encrypt','namespace'=>'Lonban\Vcc\Controllers'],function(){
    Route::get('/{dir}/{file}','EncryptController@index'); //文件加密
    Route::get('/php','EncryptController@showPhpInput'); //加密字符串的页面显示
    Route::post('/php','EncryptController@getPhpString'); //加密字符串的提交
    Route::get('/js','EncryptController@showJsInput'); //加密字符串的页面显示
    Route::post('/js','EncryptController@getJsString'); //加密字符串的提交
});