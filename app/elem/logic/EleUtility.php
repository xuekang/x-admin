<?php
declare (strict_types = 1);

namespace app\elem\logic;

use app\BaseLogic;
use think\helper\Arr;
use think\helper\Str;
use app\BaseModel;
use app\common\tools\DataFormat;
use app\common\tools\StringTool;
use app\common\tools\ArrayTool;
use app\sys_data\logic\SysData;

class EleUtility extends BaseLogic
{
	

	
	/**
     * 写入转换(元素相关数据写入数据库)(批量)
     * @param array $data
     * @param array $ele_map 元素配置数据
     * @return array
     */
    public static  function eleWriteBatch($data, $ele_map=[])
    {
        $ele_map = $ele_map ? $ele_map : SysData::getEleData(array_keys($data));
        foreach($data as $ele_name=>$value){
            if(isset($ele_map[$ele_name])){
                $data[$ele_name] = self::eleWrite($value,$ele_map[$ele_name]);
            }
        }
        return $data;
    }

    /**
     * 写入转换(元素相关数据写入数据库)
     * @param mixed $value
     * @param array $elem_item 元素配置数据
     * @return mixed
     */
    public static  function eleWrite($value, $elem_item)
    {
        $type = $elem_item['elem_form_type'];
        
        if($value === '' || is_null($value)) return '';
        
		if(self::isDate($type)) $value = self::toTimestamp($value);

		if(self::isFile($type)) $value = self::toFileStr($value);

        $value = self::toStr($value);

        return $value;
    }

	/**
     * 判断是否为日期类
     * @param string $type 
     * @return boolean 
     */
    public static function isDate($type){
		return in_array($type,[FORM_TYPE_DATE,FORM_TYPE_DATETIME,FORM_TYPE_DATE_RANGE,FORM_TYPE_DATETIME_RANGE]);
	}

    /**
     * 判断是否为日期范围类
     * @param string $type 
     * @return boolean 
     */
    public static function isDateRange($type){
		return in_array($type,[FORM_TYPE_DATE_RANGE,FORM_TYPE_DATETIME_RANGE,FORM_TYPE_TIME_RANGE]);
	}

	/**
     * 判断是否为文件类
     * @param string $type 
     * @return boolean 
     */
    public static function isFile($type){
		return in_array($type,[FORM_TYPE_IMG,FORM_TYPE_VIDEO,FORM_TYPE_FILE]);
	}

    /**
     * 判断是否为文件类
     * @param string $type 
     * @return boolean 
     */
    public static function isSelect($type){
		return in_array($type,[FORM_TYPE_SELECT,FORM_TYPE_CASCADER,FORM_TYPE_RADIO,FORM_TYPE_CHECKBOX,FORM_TYPE_SWITCH]);
	}




    /**
     * 格式化数组类表单值为字符串
     * @param mixed $value 
     * @return string 
     */
    public static function toStr($value,$separator=','){
        if(is_array($value)){
            if(ArrayTool::isOneDimenArray($value) && ArrayTool::isIndex($value)){
                $value = implode($separator,$value);
            }else{
                $value = json_encode($value,JSON_UNESCAPED_UNICODE);
            }
        }
        return $value;
    }

    /**
     * 格式化日期类表单值为时间戳
     * @param string|int|array $value 
     * @return int|string 
     */
    public static function toTimestamp($value)
    {
        $arr = is_array($value) ? $value : explode(',', strval($value));
        $val_result = [];
        foreach ($arr as $k => $v) {
            $v = trim($v);
            $v = $v && !is_numeric($v) ? strtotime(strval($v)) : $v;
            $val_result[] = $v;
        }
        $value = implode(',',$val_result);
        
        return $value;
    }

    /**
     * 格式化文件表单值为字符串
     * @param string|int|array $value 
     * @return int|string 
     */
    public static function toFileStr($value)
    {
        if(is_array($value)){
            if(!ArrayTool::isOneDimenArray($value)){
                $save_value = [];
                foreach ($value as $k => $v) {
                    $save_value[] = $v[ID];
                }
                $value = implode(',',$save_value);
            }else{
                $value = self::toStr($value);
            }
        }
        return $value;
    }



    /**
     * 读取转换(元素相关数据从数据库读取)(批量)
     * @param array $data 
     * @param array $ele_map 
     * @return array 
     */
    public static function eleReadBatch($data, $ele_map=[])
    {
        $ele_map = $ele_map ? $ele_map : SysData::getEleData(array_keys($data));
        foreach($data as $ele_name=>$value){
            if(isset($ele_map[$ele_name])){
                $data[$ele_name] = self::eleRead($value,$ele_map[$ele_name]);
            }
        }
        return $data;
    }

    /**
     * 读取转换(元素相关数据从数据库读取)
     * @param mixed $value 
     * @param array $elem_item 元素配置数据
     * @return mixed 
     */
    public static function eleRead($value, $elem_item)
    {
        $type = $elem_item['elem_form_type'];
        if($value === '' || is_null($value)) return '';

		//日期
		if(in_array($type,[FORM_TYPE_DATE,FORM_TYPE_DATETIME])){
			$value = self::toDateStr($value,'Y-m-d');
		}elseif(in_array($type,[FORM_TYPE_DATE_RANGE,FORM_TYPE_DATETIME_RANGE])){
			$value = self::toDateStr($value);
		}

		//文件
		if(self::isFile($type)) $value = self::toFileArray($value, $elem_item);

		//数组类
		if(self::isArrayFormType($type,$elem_item)) $value = self::toArray($value);
        
        return $value;
    }
    
	/**
     * 判断是否为数组类表单
     * @param string|int|array $value 
     * @param array $elem_item 
     * @return boolean 
     */
    public static function isArrayFormType($type,$elem_item){
		if($elem_item['elem_is_multiple']){
			return true;
		}

		if(in_array($type,[FORM_TYPE_CHECKBOX,FORM_TYPE_CASCADER,FORM_TYPE_DATE_RANGE,FORM_TYPE_TIME_RANGE,FORM_TYPE_DATETIME_RANGE])){
			return true;
		}
	}

	/**
     * 格式化为数组
     * @param string|int|array $value 
     * @return array 
     */
    public static function toArray($value){
		if(is_string($value)){
			$value = explode(',',$value);
			foreach ($value as $k => $v) {
				$value[$k] = StringTool::toNumber($v);
			}
		}
		return $value;
	}



    /**
     * 格式化为字符串日期
     * @param string|int|array $value 
     * @param string $format 日期格式
     * @return string|array
     */
    public static function toDateStr($value, $format='Y-m-d H:i:s')
    {
        $arr = is_array($value) ? $value : explode(',',strval($value));
        $val_result = [];
        foreach ($arr as $val_item) {
            if(!empty($val_item)){
                $val_item = is_numeric($val_item) ? date($format, intval($val_item)) : $val_item;
            }else{
                $val_item = '';
            }
            $val_result[] = $val_item;
        }
        $value = implode(',',$val_result);
        return $value;
    }

	/**
     * 格式化为文件数组
     * @param string|int|array $value 
     * @param array $elem_item
     * @return array 
     */
    public static function toFileArray($value,$elem_item)
    {
		if(!$value) return [];

        $sele_code_show = $elem_item['elem_name'];
		$sele_code_show_param = [
			$sele_code_show=>[
				'code'=>$sele_code_show,
				'type'=>'file',
                'param'=>$value
			]
		];
		$sele_show_map = app('Select')->getSelect($sele_code_show_param);

		$show_value = current($sele_show_map);
        return $show_value;
    }


    /**
     * 判断某个值是否有效
     * @param mixed $value
     * @param boolean|int $valid_zero
     * @return boolean
     */
    public static function isValidValue($value,$valid_zero=false)
    {
		$flag = true;
        if(is_numeric($value)){
            $value = StringTool::toNumber($value);
            if($value === 0){
                $flag = $valid_zero ? true : false;
            }
        }else{
            $flag = $value ? true : false; 
        }
        return $flag;
    }
}

