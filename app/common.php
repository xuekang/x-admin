<?php
declare (strict_types = 1);

// 应用公共文件

//全局常量
require_once __DIR__ . '/constant.php';

/** 自定义抛出错误
 */
if (!function_exists('my_throw')) {
	function my_throw($message,$code=0) {
		throw new RuntimeException($message,$code);
	}
}

/** 自定义按条件抛出错误
 */
if (!function_exists('my_throw_if')) {
	function my_throw_if($condition,$message,$code=0) {
		$condition && my_throw($message,$code);
	}
}

/** 生成系统主键id
 */
if (!function_exists('make_id')) {
	function make_id() {
		return \app\common\tools\SnowFlakeTool::makeId();
	}
}