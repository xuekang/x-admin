<?php
declare (strict_types=1);

namespace app\common\tools;
use \Godruoyi\Snowflake\Snowflake;
use Godruoyi\Snowflake\RedisSequenceResolver;

Class SnowFlakeTool
{
    /**
     * 生成ID
     * @return int ID号，17-19位数字
     */
    public static function makeId(){
        $conf = config('snowflake');
        //intval($conf['start_time_stamp'])     strtotime('1958-1-1') * 1000
        $snowflake = new Snowflake(intval($conf['data_center_id']), intval($conf['worker_id']));
        
        // $redis = cache();
        // config('cache.stores.redis');
        //连接redis
        $RedisSequenceResolver  = new RedisSequenceResolver(app('Redis')->redis);
        $snowflake->setStartTimeStamp(intval($conf['start_time_stamp']))->setSequenceResolver($RedisSequenceResolver);
        return $snowflake->id();
    }
}