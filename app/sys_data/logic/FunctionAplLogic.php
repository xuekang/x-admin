<?php

namespace app\sys_data\logic;

use app\BaseLogic;
use RuntimeException;
use think\helper\Arr;
use app\common\tools\StringTool;

class FunctionAplLogic extends BaseLogic
{

    /**
     * 执行函数
     * @return mixed|array|string|int|boolean
     * @author xk
     */
    public static function executeFunction(...$args)
    {
        $func_code = array_shift($args);
        $func = SysData::getFunctionData($func_code)[$func_code];
        my_throw_if(!$func,"函数({$func_code})未配置");

        $method = $func['func_name'];
        $class = $func['func_class_name'];

        if($class){
            my_throw_if(!class_exists($class),"类($class)不存在");
            my_throw_if(!method_exists($class,$method),"类($class)中方法($method)不存在");

            $ReflectionMethod = new \ReflectionMethod($class,$method);
            $App = app();
            if($ReflectionMethod->isStatic()){
                return $App->invoke("$class::$method",$args);
            }else{
                return $App->invoke([$class,$method],$args);
            }
        }else{
            my_throw_if(!function_exists($method),"全局方法($method)不存在");
            return $method(...$args);
        }
    }

    

    
}
