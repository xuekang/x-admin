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

//用户管理
Route::group('User', function () {
    Route::any('add', 'add');
    Route::any('edit', 'edit');
})->prefix('UserController/');


//登录
Route::group('Login', function () {
    Route::any('loginIn', 'loginIn');
    Route::any('getUserInfo', 'getUserInfo');
    Route::any('loginOut', 'loginOut');
})->prefix('LoginController/');

