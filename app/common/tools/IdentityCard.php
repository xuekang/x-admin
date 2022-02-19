<?php

namespace app\common\lib;

use Nette\Utils\Strings;

//=============新的18位身份证号码各位的含义:=======================
//1-2位省、自治区、直辖市代码；11-65
//3-4位地级市、盟、自治州代码；
//5-6位县、县级市、区代码；
//7-14位出生年月日，比如19670401代表1967年4月1日；
//15-17位为顺序号，其中17位男为单数，女为双数；
//18位为校验码，0-9和X，由公式随机产生。
//举例：
//130503 19670401 0012这个身份证号的含义: 13为河北，05为邢台，03为桥西区，出生日期为1967年4月1日，顺序号为001，2为验证码
//===========15位身份证号码各位的含义:=======================
//1-2位省、自治区、直辖市代码；
//3-4位地级市、盟、自治州代码；
//5-6位县、县级市、区代码；
//7-12位出生年月日,比如670401代表1967年4月1日,这是和18位号码的第一个区别；
//13-15位为顺序号，其中15位男为单数，女为双数；
//与18位身份证号的第二个区别：没有最后一位的验证码。
//举例：
//130503 670401 001的含义; 13为河北，05为邢台，03为桥西区，出生日期为1967年4月1日，顺序号为001。

/**
 * 身份证号验证类
 * @author xk
 */
class IdentityCard
{
    /**
     * 校验身份证号是否合法
     * @param string $num 待校验的身份证号
     * @return bool
     */
    public static function isValid($num)
    {
        //老身份证长度15位，新身份证长度18位
        $length = strlen($num);
        if ($length == 15) { //如果是15位身份证

            //15位身份证没有字母
            if (!is_numeric($num)) {
                return false;
            }
            // 省市县（6位）
            $areaNum = substr($num, 0, 6);
            // 出生年月（6位）
            $dateNum = substr($num, 6, 6);

        } else if ($length == 18) { //如果是18位身份证

            //基本格式校验
            if (!preg_match('/^\d{17}[0-9xX]$/', $num)) {
                return false;
            }
            // 省市县（6位）
            $areaNum = substr($num, 0, 6);
            // 出生年月日（8位）
            $dateNum = substr($num, 6, 8);

        } else { //假身份证
            return false;
        }

        //验证地区
        if (!self::isAreaCodeValid($areaNum)) {
            return false;
        }

        //验证日期
        if (!self::isDateValid($dateNum)) {
            return false;
        }

        //验证最后一位
        if (!self::isVerifyCodeValid($num)) {
            return false;
        }

        return true;
    }

    /**
     * 省市自治区校验
     * @param string $area 省、直辖市代码
     * @return bool
     */
    private static function isAreaCodeValid(string $area) {
        $provinceCode = substr($area, 0, 2);

        // 根据GB/T2260—999，省市代码11到65
        if (11 <= $provinceCode && $provinceCode <= 65) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证出生日期合法性
     * @param string $date 日期
     * @return bool
     */
    private static function isDateValid(string $date) {
        if (strlen($date) == 6) { //15位身份证号没有年份，这里拼上年份
            $date = '19'.$date;
        }
        $year  = intval(substr($date, 0, 4));
        $month = intval(substr($date, 4, 2));
        $day   = intval(substr($date, 6, 2));

        //日期基本格式校验
        if (!checkdate($month, $day, $year)) {
            return false;
        }

        //日期格式正确，但是逻辑存在问题(如:年份大于当前年)
        $currYear = date('Y');
        if ($year > $currYear) {
            return false;
        }
        return true;
    }

    /**
     * 验证18位身份证最后一位
     * @param string $num 待校验的身份证号
     * @return bool
     */
    private static function isVerifyCodeValid(string $num)
    {
        if (strlen($num) == 18) {
            $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
            $tokens = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];

            $checkSum = 0;
            for ($i = 0; $i < 17; $i++) {
                $checkSum += intval($num[$i]) * $factor[$i];
            }

            $mod   = $checkSum % 11;
            $token = $tokens[$mod];

            $lastChar = strtoupper($num[17]);

            if ($lastChar != $token) {
                return false;
            }
        }
        return true;
    }
}