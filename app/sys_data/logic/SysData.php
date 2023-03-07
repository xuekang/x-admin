<?php

namespace app\sys_data\logic;

use app\BaseLogic;
use think\helper\Arr;
use think\helper\Str;
use app\model\SysElement;
use app\model\SysSelect;
use app\model\SysFunctionCate;
use app\BaseModel;
use app\model\SysFunction;

class SysData extends BaseLogic
{
    public static $dataMap = [];

    /** 获取选项数据
     * @return array
     * @author xk
     */
    public static function getEleData($elem_name=''){
        return self::getCacheData(SysElement::class,'elem_name',$elem_name);
    }

	/** 获取选项数据
     * @return array
     * @author xk
     */
    public static function getSelectData($sele_code=''){
        return self::getCacheData(SysSelect::class,'sele_code',$sele_code);
    }

    /**
     * 获取函数数据
     * @param string $func_code 函数编码
     * @return array
     * @author xk
     */
    public static  function getFunctionData($func_code='')
    {
        return self::getCacheData(SysFunction::class,'func_code',$func_code);
    }

    /** 获取缓存数据
     * @param BaseModel $Model
     * @param string $key
     * @param string $code
     * @return array
     * @author xk
     */
    public static function getCacheData($Model,$key,$code='')
    {
        if($code){
            $data = [];
            $codes = is_array($code) ? $code : explode(',',$code);
            $all_map = Arr::get(self::$dataMap,"{$key}.all_map",[]);
            $map = Arr::get(self::$dataMap,"{$key}.map",[]);
            $all_map = array_merge($all_map,$map);
            $need_codes = array_diff($codes,array_keys($all_map));
            if($need_codes){
                $all_map = array_merge($all_map,$Model::getColumn(null,$key,[[$key,'in',$need_codes]]));
            }
            Arr::set(self::$dataMap,"{$key}.map",$all_map);
            foreach ($codes as $k => $this_code) {
                if(isset($all_map[$this_code])){
                    $data[$this_code] = $all_map[$this_code];
                }
            }
            
            return $data;
        }else{
            $all_map = Arr::get(self::$dataMap,"{$key}.all_map",null);
            if(is_null($all_map)){
                $all_map = $Model::getColumn(null,$key);
                Arr::set(self::$dataMap,"{$key}.all_map",$all_map);
            }
            return $all_map;
        }
    }
    
}
