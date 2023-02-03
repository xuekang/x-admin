<?php

return [
    //雪花算法数据中心ID
    'data_center_id'=>env('snowflake.data_center_id',0),
    //雪花算法机器ID
    'worker_id' => env('snowflake.worker_id',0),
    //开始时间截 (2020-01-01 00:00:00)
    'start_time_stamp' => env('snowflake.start_time_stamp',1577808000000),
];