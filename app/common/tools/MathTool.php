<?php

namespace app\common\lib;

/**
 * 数学公式相关工具类
 */
class MathTool
{
    /**
     * 千位取整-向下
     * @param int|float $x
     * @return int
     * @author xk
     */
    public static function qwqzFloor($x)
    {
        return floor($x / 1000) * 1000;
    }

    /**
     * 千位取整-向上
     * @param int $x
     * @return int
     * @author xk
     */
    public static function qwqzCeil($x)
    {
        return ceil($x / 1000) * 1000;
    }


    /**
     * 千位取整-四舍五入
     * @param int $x
     * @return int
     * @author xk
     */
    public static function qwqzRound($x)
    {
        return round($x / 1000) * 1000;
    }


}