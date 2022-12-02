<?php
declare (strict_types=1);

namespace app;

use think\helper\Str;
use think\helper\Arr;
use app\common\tools\StringTool;

/**
 * 逻辑类基类
 * @author xk
 */
class BaseLogic
{

    static protected $request_url;//请求地址
    static protected $token;
    static protected $sys_time;//系统时间 int


    public function __construct()
    {

        
    }

    
    /**
     * 获取当前系统请求地址
     * @return string
     */
    public function getRequestUrl()
    {
        if($this->request_url){
            return $this->request_url;
        }

        $request_url = strtolower(request()->root() . '/' . request()->pathinfo());
        $request_url = implode('/',array_filter(explode('/',$request_url)));
        return $this->request_url = $request_url;
    }

    /**
     * 获取当前系统用户token
     * @return string
     */
    public function getToken()
    {
        if($this->token){
            return $this->token;
        }
        return $this->token = request()->header('token', '');
    }

    /**
     * 获取当前系统时间
     * @return int
     */
    public function getSysTime()
    {
        if($this->sys_time){
            return $this->sys_time;
        }
        return $this->sys_time = request()->sys_time ? request()->sys_time : time();
    }

    /**
     * 获取当前系统客户端
     * @return string
     */
    public function getClient()
    {
        if($this->client){
            return $this->client;
        }
        return $this->client = request()->header('client', DEFAULT_CLIENT);
    }

    /**
     * 生成全局唯一id
     * @param string $prefix 前缀
     * @param int $length 长度
     * @return string
     * @author xk
     */
    public function createGuid($prefix='',$length=0){
        return StringTool::createGuid($prefix,$length);
    }
}