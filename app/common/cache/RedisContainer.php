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

    public function __call($name,$args)
    {
        return $this->redis->$name(...$args);
    }


    /** 获取指定key的值
     * string
     * @param string $key
     * @return string|boolean 失败返回false
     * @author xk
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }

    /** 设置指定key的值
     * string
     * @param string $key
     * @param string $value
     * @return boolean 成功返回true
     * @author xk
     */
    public function set($key,$value)
    {
        return $this->redis->set($key,$value);
    }




    /** 获取指定key的指定字段的值
     * hash
     * @param string $key
     * @param string $field
     * @return string
     * @author xk
     */
    public function getValue($key, $field)
    {
        return $this->redis->hGet($key, $field);
    }

    /** 获取指定key的全部值
     * hash
     * @param string $key
     * @return array
     * @author xk
     */
    public function getAllValue($key)
    {
        return $this->redis->hGetAll($key);
    }

    /** 设置指定key的值
     * hash
     * @param string $key
     * @param array $value 例['y'=>'是','n'=>'否']
     * @return void
     * @author xk
     */
    public function setValue($key, $value)
    {
        return $this->redis->hMset($key, $value);
    }





    /** 获取指定key的全部key
     * @param string $key 例，oa:api:*
     * @return array
     * @author xk
     */
    public function getAllKey($key)
    {
        return $this->redis->keys($key);
    }
 
    /** 判断值是否在指定key值集合中
     * @param string $key
     * @param string $value
     * @return boolean
     * @author xk
     */
    public function isInSet($key,$value)
    {
        return $this->redis->sIsMember($key,$value);
    }

    /** 删除指定的key
     * @param string $key
     * @return int
     * @author xk
     */
    public function del($key)
    {
        return $this->redis->unlink($key);
    }

}