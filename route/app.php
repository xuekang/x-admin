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
use app\common\tools\JsonService;

Route::miss(function() {
    return json(JsonService::fail('该未找到该数据接口', [], 10002));
}); //强制路由

Route::get('think', function () {
    return 'hello,ThinkPHP6!';
});

Route::get('hello/:name', 'index/hello');

// Route::group('code_name', function () {
//     Route::group('test', function () {
//         Route::any('test', '@test');
//     })->prefix('\app\code_name\controller\TestController::class');
// });

// Route::group('code_name', function () {
//     Route::group('test', function () {
//         Route::any('test', 'code_name.Test/test');
//     });
// });


// Route::any('code_name/test/test1', '\app\code_name\controller\TestController@test1');