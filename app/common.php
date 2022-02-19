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

/** 获取请求参数
 */
if (!function_exists('getallheaders')) {
	function getallheaders() {
		$headers = array();
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ',substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
}