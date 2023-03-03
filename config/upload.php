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

// +----------------------------------------------------------------------
// | 上传配置设置
// +----------------------------------------------------------------------

return [
	//上传地址,
	'upload_url' => '/sys_data/Tool/upload',
	//文件存储根目录
	'upload_dir' => 'uploads/',
	//缩略图目录
	'thumb_dir' => 'thumbs/',
	//缩略图宽
	'thumb_width' => '150',
	//缩略图高
	'thumb_height' => '150',
	//文件保存格式
	'save_key' => '{year}{mon}{day}/{filemd5}{.suffix}',
	//文件字段名
	'field_name' => 'file',
	//最大可上传大小
	'max_size' => '10Mb',
	//最大可上传大小-音视频
	'video_max_size' => '40Mb',
	//最大可上传大小-文件
	'file_max_size' => '20Mb',
	//可上传的文件类型
	'mime_type' => '*',
];
