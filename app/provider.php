<?php

use app\common\cache\RedisContainer;
use app\ExceptionHandle;
use app\Request;
use app\common\exception\CommonException;
use app\common\select\Select;

// 容器Provider定义文件
return [
    'think\Request'          => Request::class,
    // 'think\exception\Handle' => ExceptionHandle::class,
    'think\exception\Handle' => CommonException::class,
    'Redis' => RedisContainer::class,
    'Select' => Select::class,
];
