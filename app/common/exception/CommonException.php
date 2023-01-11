<?php
declare (strict_types=1);

namespace app\common\exception;

use app\common\tools\JsonService;
use think\exception\HttpException;
use think\exception\Handle;
use think\Request;
use think\Response;
use think\facade\Db;
use Throwable;
/**
 * 通用异常处理
 * @author xk
 */
class CommonException extends Handle
{
    private $httpStatus = 400;

    private $error_code_map = [
        0=>'请求成功',
        1=>'请求失败,请稍后重试',
        10001=>'未登录',
        10002=>'该未找到该数据接口',
        10003=>'该数据接口权限未授权',
        20001=>'请求参数错误',
        20002=>'请求地址错误'
    ];


    /**
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 如果处于调试模式
        $app_debug = input('app_debug',env('app_debug'));
        if ($app_debug) {
            app()->debug(true);
            return parent::render($request, $e);
            //return JsonService::fail($e->getMessage());
        }

        if ($e instanceof HttpException) {
            $this->httpStatus = $e->getStatusCode();
        }

        // 回滚事务
        // if($e->getCode() != 200){
        //     Db::rollback();
        // }

        // 添加自定义异常处理机制
        $message = $e->getMessage();
        $code = $e->getCode();

        //将code的默认值置为1
        $code = $code == 0 ? 1 : $code;

        //转换code映射提示信息
        if(empty($message) && isset($this->error_code_map[$code])){
            $message = $this->error_code_map[$code];
        }

        //抛出异常信息有中文时，返回异常，无中文则直接提示
        $error = preg_match('/[\x{4000}-\x{9fa5}]/u', $message) > 0 ? $message : "请求异常，请重试[{$message}]";
        
        ###todo日志记录错误
        // $this->reportException($request,$e);
        // echo $this->httpStatus;
        // echo $code;

        return json(JsonService::fail($error, [], $code));
    }

    //记录exception到日志
    private function reportException($request, Throwable $e):void 
    {
        $errorStr = "url:".$request->host().$request->url()."\n";
        $errorStr .= "code:".$e->getCode()."\n";
        $errorStr .= "file:".$e->getFile()."\n";
        $errorStr .= "line:".$e->getLine()."\n";
        $errorStr .= "message:".$e->getMessage()."\n";
        $errorStr .=  $e->getTraceAsString();

        trace($errorStr, 'error');
    }
}