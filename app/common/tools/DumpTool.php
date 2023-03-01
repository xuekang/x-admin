<?php

namespace app\common\lib;

/**
 * 杂项工具类
 */
class DumpTool
{
    /**
     * 打印数据到文件
     * @param array $value [打印数据]
     * @param string $dir [输出文件地址]
     * @return void
     */
    public static function dumpTxt($value = [], $dir = '1.txt')
    {
        $msg = "\r\n" . date('Y-m-d H:i:s') . '|' . "\r\n";
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                if (is_array($v)) {
                    $msg .= json_encode($v);
                } else {
                    $msg .= strval($v);
                }
                $msg .= '|' . "\r\n";
            }
        } else {
            $msg .= strval($value) . "\r\n";
        }
        file_put_contents($dir, $msg, FILE_APPEND);
    }
    
}