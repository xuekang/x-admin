<?php
declare (strict_types=1);

namespace app;

use app\common\traits\BaseLogicToolTrait;

/**
 * 逻辑类基类
 * @author xk
 */
class BaseLogic
{
    use BaseLogicToolTrait;

    static protected $request_url;//请求地址
    static protected $token;
    static protected $sys_time;//系统时间 int
    static protected $client;//客户端


    public function __construct()
    {

        
    }

    
    /**
     * 获取当前系统请求地址
     * @return string
     */
    public function getRequestUrl()
    {
        if(self::$request_url){
            return self::$request_url;
        }

        $request_url = strtolower(request()->root() . '/' . request()->pathinfo());
        $request_url = implode('/',array_filter(explode('/',$request_url)));
        return self::$request_url = $request_url;
    }

    /**
     * 获取当前系统用户token
     * @return string
     */
    public function getToken()
    {
        if(self::$token){
            return self::$token;
        }
        return self::$token = request()->header('token', '');
    }

    /**
     * 获取当前系统时间
     * @return int
     */
    public function getSysTime()
    {
        if(self::$sys_time){
            return self::$sys_time;
        }
        return self::$sys_time = request()->sys_time ? request()->sys_time : time();
    }

    /**
     * 获取当前系统客户端
     * @return string
     */
    public function getClient()
    {
        if(self::$client){
            return self::$client;
        }
        return self::$client = request()->header('client', DEFAULT_CLIENT);
    }
}