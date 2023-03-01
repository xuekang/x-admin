<?php
declare (strict_types = 1);

namespace app\common\cache;

use app\BaseLogic as Base;

/**
 * 缓存键规则
 */
class RedisContainer extends Base
{
    public $redis = null;//redis实例化时静态变量

    use RedisKey;

    public function __construct($opts=[])
    {
        //连接redis
        $redis      =   new \Redis();
        $host       =   config('cache.stores.redis.host');
        $port       =   config('cache.stores.redis.port');
        $auth       =   config('cache.stores.redis.password');
        $select     =   config('cache.stores.redis.select');
        $port = intval($port);
        $select = intval($select);
        // dump($host,$port);
        if (!$redis->isConnected()){
            $redis->pconnect($host,$port);
            $redis->auth($auth);
            $redis->select($select);
        }
        $this->redis = $redis;
    }



}