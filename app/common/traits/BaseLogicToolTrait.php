<?php

namespace app\common\traits;

use app\common\tools\StringTool;
use app\common\tools\SnowFlake;

/**
 * 基类控制器常用工具函数
 */
trait BaseLogicToolTrait
{
    /**
     * 生成全局唯一编码
     * @param string $prefix 前缀
     * @param int $length 长度
     * @return string 默认返回16位长度字符串
     * @author xk
     */
    public function createGuid($prefix='',$length=0){
        return StringTool::createGuid($prefix,$length);
    }

    /**
     * 生成全局唯一编码
     * @return int 默认返回18位长度数字
     * @author xk
     */
    public function makeIdCode(){
        return (new SnowFlake(SNOW_FLAKE_WORKER_ID))->nextId();
    }
}