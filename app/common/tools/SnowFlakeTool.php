<?php
declare (strict_types=1);

namespace app\common\tools;
use \Godruoyi\Snowflake\Snowflake;

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
        $snowflake->setStartTimeStamp(intval($conf['start_time_stamp']))->setSequenceResolver(config('cache.stores.redis'));
        return $snowflake->id();
    }
}