<?php

namespace app\common\lib;

use think\Exception;
use app\common\lib\StringTool;
use RuntimeException;
use app\common\lib\ArrayTool;

/**
 * 数据格式化类
 */
class DataFormat
{
    
	/**
     * 获取前端所需级联选择数据(支持无限极)
     * @param array $list 传入数据,二维数组
     * @param string $id_name id字段名
     * @param string $pid_name pid字段名
     * @param int $root  根节点id
     * @param string $children_name  children字段名
     * @param array $extend 扩展属性
     *      format:array，指定数据格式，默认空数组,例["label"=>"auth_name","value"=>"auth_id"]
     * @return array [['label'=>'名称','value'=>'值','children'=>['label'=>'名称','value'=>'值']]]
     */
    public static function getCascadeSelect(array $list, $id_name='id', $pid_name='pid', $root = 0,$children_name='children',$extend=[])
    {
        //数据格式化
        $format = $extend['format'] ?? [];
        if($format){
            foreach($list as $k=>$v){
                $list[$k]['label'] = $v[$format['label']];
                $list[$k]['value'] = $v[$format['value']];
            }
        }

        $tree = [];
        $packData = [];
        //将所有的分类id作为数组key
        foreach ($list as $k => $v) {
            $packData[$v[$id_name]] = $v;
        }
        //利用引用，将每个分类添加到父类child数组中，这样一次遍历即可形成树形结构。
        foreach ($packData as $key => $val) {
            if($root){
                if($val[$pid_name] == $root){
                    $tree[] = &$packData[$key];
                }else{
                    //找到其父类
                    $packData[$val[$pid_name]][$children_name][] = &$packData[$key];
                }
            }else{
                if($val[$pid_name] === 0 || $val[$pid_name] === '0' || $val[$pid_name] === ''){
                    $tree[] = &$packData[$key];
                }else{
                    //找到其父类
                    $packData[$val[$pid_name]][$children_name][] = &$packData[$key];
                }
            }
        }
        return $tree;
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
            $data = ArrayTool::makeSelectArr($data_arr_new);
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

        //数组类型转字符串
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        //字符串过滤空格
        if (is_string($value)) {
            $value = trim($value);
        }

        //时间类型
        switch ($type) {
            case FORM_TYPE_DATE:
                $value = $value && !is_numeric($value) ? strtotime(strval($value)) : $value;
                break;
            case FORM_TYPE_DATETIME:
                $value = $value && !is_numeric($value) ? strtotime(strval($value)) : $value;
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
                if($value){
                    $value = is_numeric($value) ? date('Y-m-d', $value) : $value;
                }else{
                    $value = '';
                }
                break;
            case FORM_TYPE_DATETIME:
                if($value){
                    $value = is_numeric($value) ? date('Y-m-d H:i:s', $value) : $value;
                }else{
                    $value = '';
                }
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
}