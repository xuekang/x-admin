<?php

namespace app\common\lib;

/**
 * 加密解密类
 * 该算法仅支持加密数字。适用于数据库中id字段的加密解密，以及根据数字显示url的加密。
 */
class XDecode
{
    //默认盐值
    public static $salt = 'pinnong';

    /**
     * id加密
     * @param int  $nums 加密id
     * @param string $salt 加密盐值
     * @return string 加密后字符串
     */
    public static function encode($nums, $salt = null): string
    {
        $salt = is_null($salt) ? self::$salt : $salt;
        if (!is_numeric($nums)) return false;
        $strbase = "Flpvf70CsakVjqgeWUPXQxSyJizmNH6B1u3b8cAEKwTd54nRtZOMDhoG2YLrI";
        $length = 9;
        $key = 1983.2083;
        $codelen = substr($strbase, 0, $length);
        $codenums = substr($strbase, $length, 10);
        $codeext = substr($strbase, $length + 10);
        $rtn = "";
        $numslen = strlen($nums);
        //密文第一位标记数字的长度
        $begin = substr($codelen, $numslen - 1, 1);
        //密文的扩展位
        $extlen = $length - $numslen - 1;
        $temp = str_replace('.', '', $nums / $key);
        $temp = substr($temp, -$extlen);
        $arrextTemp = str_split($codeext);
        $arrext = str_split($temp);
        foreach ($arrext as $v) {
            $rtn .= $arrextTemp[$v];
        }
        $arrnumsTemp = str_split($codenums);
        $arrnums = str_split($nums);
        foreach ($arrnums as $v) {
            $rtn .= $arrnumsTemp[$v];
        }
        return $begin . $rtn . base64_encode($salt);
    }

    /**
     * 解密算法
     * @param $code 需解密的字符串
     * @param string $salt 解密参数
     * @return bool|string
     * @author ai
     */
    public static function decode($code, $salt = null)
    {
        $salt = is_null($salt) ? self::$salt : $salt;
        $strbase = "Flpvf70CsakVjqgeWUPXQxSyJizmNH6B1u3b8cAEKwTd54nRtZOMDhoG2YLrI";
        $length = 9;
        $codelen = substr($strbase, 0, $length);
        $codenums = substr($strbase, $length, 10);
        if (!strpos($code, base64_encode($salt))) return false;
        $code = substr($code, 0, strpos($code, base64_encode($salt)));
        $begin = substr($code, 0, 1);
        $rtn = '';
        $len = strpos($codelen, $begin);
        if ($len !== false) {
            $len++;
            $arrnums = str_split(substr($code, -$len));
            foreach ($arrnums as $v) {
                $rtn .= strpos($codenums, $v);
            }
        }
        return $rtn;
    }


    /**
     * 简单自定义双向加密
     * @param string $str
     * @author ai
     */
    public static function myEnc($str)
    {
        return ~$str;
    }



    /**
     * md5加密
     * @param string $str
     * @param string $salt 加密盐值
     * @return bool|string
     */
    public static function myMD5($str,$salt = null)
    {
        $salt = is_null($salt) ? self::$salt : $salt;
        $str = md5($salt.$str);
        return $str;
    }
}