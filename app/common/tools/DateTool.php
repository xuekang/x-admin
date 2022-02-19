<?php

namespace app\common\lib;

use think\Exception;
use app\common\lib\StringTool;

/**
 * 日期相关工具类
 */
class DateTool
{
	/**
	 * 获取指定时间的起始字符串的函数
	 * @param string $type 要查询的时间字符串
	 * @return array     $time       返回查询时间起始时间戳的数组
	 */
	public static function getTime($type = 'today')
	{
	    switch ($type) {
	        case 'today';
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d"), date("Y"))));
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d"), date("Y"))));
	            break;
	        case 'yesterday';
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"))));
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d") - 1, date("Y"))));
	            break;
	        case 'thisweek';
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - date("w") + 1 - 1, date("Y"))));
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d") - date("w") + 7 - 1, date("Y"))));
	            break;
	        case 'lastweek';
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - date("w") - 6 - 1, date("Y"))));
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d") - date("w") - 1, date("Y"))));
	            break;
	        case 'thismonth';
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, date("Y"))));
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m") + 1, 0, date("Y"))));
	            break;
	        case 'lastmonth';
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - 1, 1, date("Y"))));
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), 0, date("Y"))));
	            break;
	        case 'thisseason';
	            $month = date("m");
	            $season = ceil($month / 3);
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, ($season - 1) * 3 + 1, 1, date("Y"))));
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, ($season - 1) * 3 + 4, 0, date("Y"))));
	            break;
	        case 'lastseason';
	            $month = date("m");
	            $season = ceil($month / 3);
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, ($season - 2) * 3 + 1, 1, date("Y"))));
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, ($season - 2) * 3 + 4, 0, date("Y"))));
	            break;
	        case 'thisyear';
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, date("Y"))));
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, 12, 31, date("Y"))));
	            break;
	        case 'lastyear';
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, date("Y") - 1)));
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, 12, 31, date("Y") - 1)));
	            break;
            case 'lastyear_thismonth';//同期；去年本月
                $time[] = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, (date("Y")-1))));
                $time[] = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m") + 1, 0, (date("Y")-1))));
                break;
            case 'lastyear_nearly_three_months';
                $month = date("m");
                $season = ceil($month / 3);
                $time[] = strtotime("-2month",strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, ($season - 1) * 3 + 1, 1, (date("Y")-1)))));
                $time[] = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, ($season - 1) * 3 + 4, 0, (date("Y")-1))));
                break;
            case 'nearly_three_months';
                $time[] = strtotime("-2month",strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, date("Y")))));
                $time[] = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m") + 1, 0, date("Y"))));
                break;
            case 'nearly_six_months';
                $time[] = strtotime("-5month",strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, date("Y")))));
                $time[] = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m") + 1, 0, date("Y"))));
                break;
	        default:
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d"), date("Y"))));
	            $time[] = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d"), date("Y"))));
	    }
	    return $time;
	}



	/**
	 * 求两个日期之间时间差
	 * @param string|int $time1
	 * @param string|int $time2
	 * @return array
	 */
	public static function diffBetweenTwoDays($time1, $time2)
	{
	    //计算时间间隔
	    $second1 = is_int($time1) ? $time1 : strtotime($time1);
	    $second2 = is_int($time2) ? $time2 : strtotime($time2);
	    if ($second1 < $second2) {
	        $tmp = $second2;
	        $second2 = $second1;
	        $second1 = $tmp;
	    }
	    $diff = ($second1 - $second2);
	    //相差天数/小时/分钟/秒
	    $data['day'] = $diff / 86400;
	    $data['hour'] = $diff / 3600;
	    $data['minute'] = $diff / 60;
	    $data['second'] = $diff;
	    return $data;
	}

	/**
	 * 求两个日期之间时间差文字说明
	 * @param string|int $time1
	 * @param string|int $time2
	 * @return string
	 */
	public static function getDateDiffStr($time1, $time2)
	{
	    $diff = self::diffBetweenTwoDays($time1, $time2);
	    $cur_status_str = '';
	    if (intval($diff['day']) > 0) {
	        $cur_status_str .= intval($diff['day']) . '天';
	    } elseif (intval($diff['hour']) > 0) {
	        $cur_status_str .= intval($diff['hour']) . '小时';
	    } elseif (intval($diff['minute']) > 0) {
	        $cur_status_str .= intval($diff['minute']) . '分钟';
	    } else {
	        $cur_status_str .= intval($diff['second']) . '秒';
	    }
	    return $cur_status_str;
	}


    /**
	 * 改变excel时间
	 * @param string|int $time
	 * @return string|int
	 */
	public static function changeExcelDate($time)
	{
	    $d = 25569;
	    $t = 24 * 60 * 60;
	    try {
	        return gmdate('Y-m-d H:i:s', ($time - $d) * $t);
	    } catch (Exception $e) {
	        return $time;
	    }
	}

	/**
     * 获取时间戳
     * @param string|int $time 可能值：字符串时间，字符串日期模式，数字时间戳
     * @param int $data 时间戳
     */
	public static function getTimeStamp($time='')
	{
		$timestamp = 0;
		if(!$time){
			return 0;
		}
		if(is_numeric($time)){
			$timestamp = intval($time);
		}else{
			$timestamp = strtotime($time);
		}
		return $timestamp;
	}

    /** 
     * 转换时间戳到指定精度的日期
     * @param int $timestamp 时间戳
     * @param string $precision 精度,默认到时秒
     * @return string $date
     * @author xk
     */
    public static function timeToDate($timestamp = 0,$precision='s')
    {
        $str = 'Y-m-d H:i:s';
        $precision = StringTool::getContentBySpecialStr($str,$precision);
        if ($timestamp > 0 && is_numeric($timestamp)) {
            $date = date($str, $timestamp);
        } else {
            $date = '';
        }
        return $date;
    }


}