<?php

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 默认缓存驱动
    'default' => env('cache.driver', 'file'),

    // 缓存连接方式配置
    'stores'  => [
        'file' => [
            // 驱动方式
            'type'       => 'File',
            // 缓存保存目录
            'path'       => '',
            // 缓存前缀
            'prefix'     => '',
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],

        ],
        // Reids 配置
        'redis'    =>    [
            'type'     => env('redis.type', 'redis'),
            'host'     => env('redis.host', '127.0.0.1'),
            'port'     => env('redis.port', 6999),
            'password' => env('redis.password', ''),
            'select'   => env('redis.select', 0),
            'expire'   => env('redis.expire', 0),
            'prefix'   => env('redis.prefix', ''),
            'timeout'  => env('redis.timeout', 0),
        ],
    ],
];
