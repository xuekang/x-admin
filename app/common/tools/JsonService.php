<?php
declare (strict_types = 1);

namespace app\common\tools;

/**
 * 返回信息工具类
 * @author xk
 */
class JsonService
{
    const SUCCESSFUL_DEFAULT_CODE = 0;//默认成功码
    const SUCCESSFUL_DEFAULT_MESSAGE = '请求成功';//默认成功信息

    const FAIL_DEFAULT_CODE = 1;//默认失败码
    const FAIL_DEFAULT_MESSAGE = '请求失败';//默认失败信息

    /**
     * 通用成功返回
     * @param string|array $message 提示信息
     * @param array $data 数据
     * @param int $code 错误码
     * @param int $time 时间戳
     */
    public static function success($message, $data = [], $code=null, $time = 0)
    {
        if (true == is_array($message) && empty($data)) {
            $data = $message;
            $message = self::SUCCESSFUL_DEFAULT_MESSAGE;
        }
        $code = is_null($code) ? self::SUCCESSFUL_DEFAULT_CODE : $code;
        return self::result($code, $message, $data, $time);
    }

    /**
     * 通用失败返回
     * @param string $message 提示信息
     * @param array $data 数据
     * @param int $code 错误码
     * @param int $time 时间戳
     */
    public static function fail($message, $data = [], $code = null, $time = 0)
    {
        if (true == is_array($message) && empty($data)) {
            $data = $message;
            $message = self::FAIL_DEFAULT_MESSAGE;
        }

        if (true == is_int($message) && is_null($code)) {
            $code = $message;
            $message = self::FAIL_DEFAULT_MESSAGE;
        }

        $code = is_null($code) ? self::FAIL_DEFAULT_CODE : $code;
        return self::result($code, $message, $data, $time);
    }

    /**
     * 自定义返回信息
     * @param int $code 错误码
     * @param string $message 提示信息
     * @param array $data 数据
     * @param int $time 时间戳
     */
    public static function result($code, $message = '', $data = [], $time = 0)
    {
        return self::returnData($code, $message, $data, $time);
    }

    /**
     * 设置返回数据
     * @param int $code 错误码
     * @param string $message 提示语
     * @param array $data 返回数据
     * @param int $time 时间戳
     * @return array
     * */
    public static function returnData($code, $message = '', $data = [], $time = 0)
    {
        $time = $time ? $time : time();
        return compact('code', 'message', 'data', 'time');
    }

}