<?php

namespace app\common\core;

use OSS\Core\OssUtil;
use app\common\lib\ValidateTool;

/**
 * OSS工具类
 */
class OSSTool
{
    /**
     * 获取oss文件网络访问地址
     * @param string $object  文件的OSS object 名称
     * @return string $url
     */
    public static function getObjectURL($object) {
        $oss_conf =config('oss');
        $url = $object;
        if(!ValidateTool::isUrl($object)){
            if(isset($_SERVER['REQUEST_SCHEME'])){
                $url = $_SERVER['REQUEST_SCHEME'] . '://' . $oss_conf['aliDomainName'] . ':' . $_SERVER['SERVER_PORT'] . '/' . $object;
            }else{
                $url = 'http://' . $oss_conf['aliDomainName'] . '/' . $object;
            }
        }
        return $url;
    }


     /**
     * 转换时间戳为ISO8601格式时间
     * @param int $time  时间戳
     * @return string
     */
    public static function gmtISO8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }

}