<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;


// Route::miss('indexController/error'); //强制路由

Route::miss(function() {
    return '404 Not Found!';
}); //强制路由

//测试
// Route::group('test', function () {
//     Route::any('test', 'test');
//     Route::any('test1', 'test1');
// })->prefix('TestController/');

