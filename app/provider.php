<?php

use app\common\cache\cacheKey;
use app\ExceptionHandle;
use app\Request;
use app\common\exception\CommonException;

// 容器Provider定义文件
return [
    'think\Request'          => Request::class,
    // 'think\exception\Handle' => ExceptionHandle::class,
    'think\exception\Handle' => CommonException::class,
    'cacheKey' => cacheKey::class,
];
