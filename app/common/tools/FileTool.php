<?php

namespace app\common\lib;

/**
 * 文件相关工具类
 */
class FileTool
{

	/**
	 * 获取文件的后缀
	 * @param string $file_path 本地文件路径地址名称
	 * @return string
	 */
	public static function getExt($file_path)
	{
		return strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
	}

	/** 获取指定文件目录中的全部文件
	 * @param string $file_path 本地文件目录
	 * @return array 文件数组
	 */
	public static function getAllFile($file_path,&$data=[])
	{
		$files = is_dir($file_path) ? scandir($file_path) : [];
		foreach ($files as $file) {
			if($file === '.' || $file === '..' ){
				continue;
			}
			
			$route_path = $file_path . DIRECTORY_SEPARATOR . $file;
			if(is_dir($route_path)){
				self::getAllFile($route_path,$data);
			}else{
				if (is_file($route_path)) {
					$data[] = $route_path;
				}
			}
		}
		return $data;
	}
	
}