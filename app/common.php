<?php
declare (strict_types = 1);

// 应用公共文件

//全局常量
require_once __DIR__ . '/constant.php';

/** 自定义抛出错误
 */
if (!function_exists('my_throw')) {
	function my_throw($err) {
		throw new RuntimeException($err);
	}
}

/** 自定义按条件抛出错误
 */
if (!function_exists('my_throw_if')) {
	function my_throw_if($condition,$err) {
		throw_if($condition,new RuntimeException($err));
	}
}

/** 生成系统主键编码
 */
if (!function_exists('make_id_code')) {
	function make_id_code() {
		return (new \app\common\tools\SnowFlake(SNOW_FLAKE_WORKER_ID))->nextId();
	}
}