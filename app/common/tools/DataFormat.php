<?php

namespace app\common\tools;

use app\common\select\Select;
use think\Exception;
use think\helper\Arr;
use think\helper\Str;
use app\common\tools\StringTool;
use RuntimeException;
use app\common\tools\ArrayTool;

/**
 * 数据格式化类
 */
class DataFormat
{
    
	/**
     * 生成树结构数据(支持无限极)
     * @param array $list 传入数据,二维数组
     * @param array $opts 扩展属性
     * @param string $opts['id_key'] id字段名
     * @param string $opts['pid_key'] pid字段名
     * @param string $opts['children_key']  children字段
     * @param int|string $opts['root']  根节点id的值
     * @param array $opts['map_field']  映射字段，默认空数组，['新字段'=>'原字段']
     * @param array|string $opts['save_field']  指定保留字段，默认保留全部
     * @return array 
     */
    public static function getTree(array $list,$opts=[])
    {
        $id_key = Arr::get($opts,'id_key',TREE_ID);
        $pid_key = Arr::get($opts,'pid_key',TREE_PID);
        $children_key = Arr::get($opts,'children_key',TREE_CHILDREN);
        $root = Arr::get($opts,'root',0);
        $map_field = Arr::get($opts,'map_field',[]);
        $save_field = Arr::get($opts,'save_field',[]);

        //处理map_field
        if($map_field){
            foreach ($map_field as $new_field => $old_filed) {
                foreach($list as $k=>$v){
                    $list[$k][$new_field] = $v[$old_filed];
                }
            }
        }

        $tree = [];
        $packData = [];
        //将所有的分类id作为数组key
        foreach ($list as $k => $v) {
            $packData[$v[$id_key]] = $v;
        }
        //利用引用，将每个分类添加到父类child数组中，这样一次遍历即可形成树形结构。
        foreach ($packData as $key => $val) {
            if($root){
                if($val[$pid_key] == $root){
                    $tree[] = &$packData[$key];
                }else{
                    //找到其父类
                    $packData[$val[$pid_key]][$children_key][] = &$packData[$key];
                }
            }else{
                if($val[$pid_key] === 0 || $val[$pid_key] === '0' || $val[$pid_key] === ''){
                    $tree[] = &$packData[$key];
                }else{
                    //找到其父类
                    $packData[$val[$pid_key]][$children_key][] = &$packData[$key];
                }
            }
        }

        //save_field
        if($save_field){
            ArrayTool::filterKey($tree,$save_field,'save');
        }

        return $tree;
    }

    /**
     * 从无限极获取二维数组数据
     * @param array $list 传入数据,二维数组
     * @param array $opts 扩展属性
     * @param string $opts['children_key']  children字段
     * @return array 
     */
    public static function getFromTree(array $tree,$opts=[],&$data=[],$path=[])
    {
        $id_key = Arr::get($opts,'id_key',TREE_ID);
        $pid_key = Arr::get($opts,'pid_key',TREE_PID);
        $children_key = Arr::get($opts,'children_key',TREE_CHILDREN);
        $path_key = Arr::get($opts,'path_key',TREE_PATH);
        $data = [];
        foreach ($tree as $k => $v) {
            $item = $v;
            unset($item[$children_key]);
            $path[] = $item[$id_key];
            $item[$path_key] = implode('-',$path);
            
            $data[] = $item;
            if(isset($v[$children_key])){
                self::getFromTree($v[$children_key],$opts,$data,$path);
            }
        }

        return $data;
    }

   

    /** 翻译code值
     * @param string|int|array $code [初始code值，如用户id集合，'1,2,3'或者[1,2,3]]
     * @param array $select_map [选项映射数据，一维数组,如[1=>'张三',2=>'李四',3=>'王五']]
     * @param bool valid_zero 0是否有意义
     * @param string format_type 指定格式类型，可选值：select
     * @return string|array 显示值      [中文字符串(逗号分隔)，如'张三,李四,王五']
     * @author xk
     */
    public static function translateCode($code, $select_map ,$valid_zero = true,$format_type='')
    {
        $data = $format_type == 'select' ? [] : '';
        
        if($code === 0 || $code === '0'){
            if(!$valid_zero){
                return $data;
            }
        }else{
            if(!$code){
                return $data;
            }
        }
        $data_arr_old = is_array($code) ? $code : explode(',', strval($code));
        $data_arr_new = [];
        foreach ($data_arr_old as $k => $v) {
            if($format_type == 'select'){
                if (array_key_exists($v, $select_map)) {
                    $data_arr_new[$v] = $select_map[$v];
                } else {
                    $data_arr_new[$v] = $v;
                }
            }else{
                if (array_key_exists($v, $select_map)) {
                    $data_arr_new[] = $select_map[$v];
                } else {
                    $data_arr_new[] = $v;
                }
            }
            
        }
        if($format_type == 'select'){
            $data = Select::makeSelectArr($data_arr_new);
        }else{
            $data = implode(',', $data_arr_new);
        }
        
        return $data;
    }


    /** 翻译列表中的code值
     * @param  array $list_arr  [表格列表数组(支持一维和一维以上数组)]
     * @param  array $field_map [表格字段数组(字段=>选项code，如['is_valid'=>'boolean']  或者使用别名['is_valid|my_is_valid'=>'boolean'])]
     * @param  array $select_map_arr   [选项数据映射数组,二维数组['boolean'=>[1=>'是',0=>'否']]]
     * @param  array $extend 扩展属性
     *      valid_zero:boolean,0是否有意义,默认true
     *      format:array，指定数据格式，默认空数组，可选值select,例["is_valid"=>"select"]
     * @return array            [格式化完成的数组]
     * @author xk
     */
    public static function translateList($list_arr, $field_map, $select_map_arr,$extend=[])
    {
        
        $valid_zero = $extend['valid_zero'] ?? true;
        $format = $extend['format'] ?? [];
        
        $new_arr = $list_arr;
        foreach($list_arr as $k => $v){
            if (is_array($v)){
                $new_arr[$k] = self::translateList($v, $field_map, $select_map_arr,$extend);
            } else {
                foreach ($field_map as $kk => $vv) {
                    $key_arr = explode('|',$kk);
                    $key_real = $key_arr[0];
                    $key_alias = empty($key_arr[1]) ? $key_arr[0] : $key_arr[1];
                    if(!isset($list_arr[$key_real])) continue;
                    $format_type = isset($format[$key_real]) ? $format[$key_real] : '';
                    $sele_code = $vv['code'] ?? $vv;
                    $value = self::translateCode($list_arr[$key_real], $select_map_arr[$sele_code], $valid_zero,$format_type);
                    $new_arr[$key_alias] = $value;
                }
            }
        }
        return $new_arr;
    }



    /**
     * 写入转换(元素相关数据写入数据库)
     * @param mix $value
     * @param array $ele_item 元素配置数据
     * @return mix
     */
    public static  function eleWrite($value, $ele_item)
    {
        $type = $ele_item['type'];

        //时间类型
        switch ($type) {
            case FORM_TYPE_DATE:
                $value = self::fortmatDateToTimestamp($value);
                break;
            case FORM_TYPE_DATETIME:
                $value = self::fortmatDateToTimestamp($value);
                break;
            case FORM_TYPE_DATARANGE:
                $value = self::fortmatDateToTimestamp($value);
                break;
            case FORM_TYPE_DATETIMERANGE:
                $value = self::fortmatDateToTimestamp($value);
                break;
            default:
        }

        return $value;
    }

    /**
     * 写入转换(元素相关数据写入数据库)(批量)
     * @param array $data
     * @param array $ele_map 元素配置数据
     * @return array
     */
    public static  function eleWriteBatch($data, $ele_map)
    {
        foreach($data as $ele_name=>$value){
            if(isset($ele_map[$ele_name])){
                $data[$ele_name] = self::eleWrite($value,$ele_map[$ele_name]);
            }
        }
        return $data;
    }


    /**
     * 读取转换(元素相关数据从数据库读取)
     * @param mix $value 
     * @param array $ele_item 元素配置数据
     * @return mix 
     */
    public static function eleRead($value, $ele_item)
    {
        $type = $ele_item['type'];

        //时间类型
        switch ($type) {
            case FORM_TYPE_DATE:
                $value = self::fortmatDateToStr($value,'Y-m-d',);
                break;
            case FORM_TYPE_DATETIME:
                $value = self::fortmatDateToStr($value,'Y-m-d H:i:s');
                break;
            default:
        }
        return $value;
    }

    /**
     * 读取转换(元素相关数据从数据库读取)(批量)
     * @param array $data 
     * @param array $ele_map 
     * @return array 
     */
    public static function eleReadBatch($data, $ele_map)
    {
        foreach($data as $ele_name=>$value){
            if(isset($ele_map[$ele_name])){
                $data[$ele_name] = self::eleRead($value,$ele_map[$ele_name]);
            }
        }
        return $data;
    }


    /**
     * 格式化日期为时间戳
     * @param string|int|array $value 
     * @return string 
     */
    public static function fortmatDateToTimestamp($value)
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
     * 格式化日期为字符串
     * @param string|int|array $value 
     * @param string $format 日期格式
     * @return string 
     */
    public static function fortmatDateToStr($value, $format)
    {
        $arr = is_array($value) ? $value : explode(',',strval($value));
        $val_result = [];
        foreach ($arr as $val_item) {
            if(!empty($val_item)){
                $val_item = is_numeric($val_item) ? date($format, $val_item) : $val_item;
            }else{
                $val_item = '';
            }
            $val_result[] = $val_item;
        }
        $value = implode(',',$val_result);
        return $value;
    }

    /**
     * 获取json类型数据
     * @param array $data 数据源
     * @param string $key 支持点符号
     * @param mix $default_value 默认值
     * @return array 
     */
    public static function getJsonValue($data, $key, $default_value=[])
    {
        $value = Arr::get($data,$key,$default_value);
        if($value && !is_array($value)){
            $value = json_decode($value,true);
        }
        $value = $value ? $value : $default_value;
        return $value;
    }


    /**
     * 将大数字转为字符串
     * @param array|int|string|mixed $data
     * @return array|int|string|mixed 
     */
    public static function bigIntToStr($data)
    {
        if(is_array($data)){
            foreach ($data as $k => $v) {
                $k = self::bigIntToStr($k);
                $v = self::bigIntToStr($v);
                $data[$k] = $v;
            }
        }elseif(is_int($data) && mb_strlen($data) > 16 ){
            $data = strval($data);
        }
        return $data;
    }
}