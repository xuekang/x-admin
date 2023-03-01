<?php

namespace app\common\tools;

use think\helper\Str;
/**
 * 日期相关工具类
 */
class ValidateTool
{

	/**
	 * 布尔判断
	 * @param mixed 字符串
	 * 真：1、"1"、true、"true"、TRUE、"TRUE"、"on"、"ON"、"yes"、"YES"、"y"  、"Y" ;
	 * 假：0、"0"、false、"false"、FALSE、"FALSE"、"off"、"OFF"、"no"、"no"、"n"  、"n" 等
	 * @return boolean
	 */
	public static function isMyBoolean($data)
	{
		if(!empty($data)){
			if(filter_var($data, FILTER_VALIDATE_BOOLEAN) || $data === "Y" || $data === "y"){
				return true;
			}
		}
	    return false;
	}

	/**
     * 判断某个值是否有效
     * @param mix $value
     * @return boolean
     */
    public static function isValidValue($value){
        return ($value || $value === 0 || $value === '0') && $value !== 'null';
    }
    


	/**
	 * 邮箱检测
	 * @param $str 字符串
	 * @return boolean
	 */
	public static function isEmail($str)
	{
	    if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
	        return true;
	    } else {
	        return false;
	    }
	}

	/**
	 * URL访问地址检测
	 * @param  $str 字符串
	 * @return boolean
	 */
	public static function isUrl($str)
	{
	    if (filter_var($str, FILTER_VALIDATE_URL)) {
	        return true;
	    } else {
	        return false;
	    }
	}

	/**
	 * 手机号检测
	 * @param string|string $str 字符串
	 * @return boolean 
	 */
	public static function isMobilePhone($str)
	{
	    $search = '/^(1[3-9])\\d{9}$/';
	    if (preg_match($search, $str)) {
	        return true;
	    } else {
	        return false;
	    }
	}

	/**
     * 判断是否为微信访问
     * @return boolean
     */
    public static function isWxVisit(){

        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;

    }

    /**
     * 判断是否为手机访问
     * @return  boolean
     */
    public static function isMobileVisit() {

        $sp_is_mobile = false;

        if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
            $sp_is_mobile = false;
        } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
            $sp_is_mobile = true;
        } else {
            $sp_is_mobile = false;
        }

        return $sp_is_mobile;

    }

    /**
     * 当前请求是否是https
     * @return boolean
     */
    public static function isHttps()
    {
        return request()->isSsl();
    }


}