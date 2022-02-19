<?php
declare (strict_types=1);

namespace app\common\middleware;

use think\Request;
use Closure;
use think\Response;
use think\helper\Str;

/**
 * 权限中间件
 * @author xk
 */
class Auth
{
    //地址白名单,类型:1-全匹配，2-以其开头，3-以其结尾
    private static $whiteList = [
        ['url'=>'apidoc/','type'=>2]
    ];

    /**
     * 权限中间件
     * @param Request $request
     * @param Closure $next
     * @return Response $reponse
     */
    public static function handle($request, Closure $next)
    {
        //获取数据
        $request_url = strtolower($request->root() . '/' . $request->pathinfo());
        $request_url = implode('/',array_filter(explode('/',$request_url)));
        
        //全局变量赋值
        $request->sys_time = time();


        //过滤options请求
        if(strtolower($request->method()) == 'options'){
            return $next($request);
        }

        //处理白名单
        if(self::checkWhiteList($request_url)){
            return $next($request);
        }

        //记录访问日志
        //$Auth->redis_log($request);
        // echo '执行前置中间件</br>';    
        $reponse = $next($request);//执行方法
        // echo '执行后置中间件</br>';
        return $reponse;
    }

    /** 验证白名单
     * @param string $request_url
     * @return boolean
     */
    private static function checkWhiteList($request_url)
    {
        foreach(self::$whiteList as $v){
            if($v['type'] == 2){
                if(Str::startsWith($request_url,$v['url'])){
                    return true;
                }
            }elseif($v['type'] == 3){
                if(Str::endsWith($request_url,$v['url'])){
                    return true;
                }
            }else{
                if($request_url == $v['url']){
                    return true;
                }
            }
        }
        return false;
    }

}
