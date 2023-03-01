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
        $func = SysData::getFunctionData($func_code);
        my_throw_if(!$func,"函数({$func_code})未配置");

        $func_cname = $func['func_cname'];
        $method = $func['func_name'];
        $class = $func['class_name'];
        $type = $func['func_type'];

        if($type == 1){
            try {
                $obj = new $class;
            } catch (\Throwable $th) {
                $err =$th->getMessage();
                my_throw("函数($func_cname:$method)在类($class)中定义错误:$err");
            }
            $data = call_user_func_array([$obj,$method],$args);
        }elseif($type == 2){
            $data = call_user_func_array("$class::$method",$args);
        }else{
            $data = $method(...$args);
        }

        return $data;
    }

    

    
}
