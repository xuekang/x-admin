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

// Route::miss('indexController/error'); //强制路由

Route::miss(function() {
    return json(JsonService::fail('该未找到该数据接口', [], 10002));
}); //强制路由

//通用
Route::group('Tool', function () {
    Route::any('getSelect', 'getSelect');
    Route::any('upload', 'upload');
})->prefix('ToolController/');



