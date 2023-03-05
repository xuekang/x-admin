<?php

namespace app\sys_data\logic;

use app\BaseLogic;
use think\helper\Arr;
use think\helper\Str;
use app\model\SysElement;
use app\model\SysSelect;
use app\model\SysFunctionCate;

class SysData extends BaseLogic
{
    public static $eleData = null;
    public static $selectMap = null;
    public static $functionMap = null;

    /** 获取选项数据
     * @return array
     * @author xk
     */
    public static function getEleData($elem_name=''){
        if(is_null(self::$eleData)){
            $condition = [];
            if($elem_name){
                $condition[] = ['elem_name','in',$elem_name];  
            }
            self::$eleData = SysElement::getColumn(null,'elem_name',$condition);
        }

        return self::$eleData;
    }

	/** 获取选项数据
     * @return array
     * @author xk
     */
    public static function getSelectData($sele_code=''){
        if(is_null(self::$selectMap)){
            self::$selectMap = SysSelect::getColumn(null,'sele_code');
        }

		if($sele_code && isset(self::$selectMap[$sele_code])){
			return self::$selectMap[$sele_code];
		}

        return self::$selectMap;
    }



    /**
     * 获取函数数据
     * @param string $func_code 函数编码
     * @return array
     * @author xk
     */
    public static  function getFunctionData($func_code='')
    {
        if(is_null(self::$functionMap)){
            self::$functionMap = SysFunctionCate::getColumn(null,'id');
        }

		if($func_code && isset(self::$functionMap[$func_code])){
			return self::$functionMap[$func_code];
		}

        return self::$functionMap;
    }
    
}
