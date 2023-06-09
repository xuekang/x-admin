<?php

declare (strict_types = 1);

namespace app\common\tools;

use app\common\select\Select;
use think\Exception;
use think\helper\Arr;
use think\helper\Str;

/**
 * 数组相关工具类
 */
class ArrayTool
{



    /**
     * 数组深度合并（递归合并，后者会覆盖前者）
     * @param array $array
     * @param array $mergeArray
     * @return array
     */
    public static function deepMerge(array &$array,array ...$mergeArray): array
    {
        foreach ($mergeArray as $item){
            self::mergeOne($array,$item); //对每个待合并数组执行合并函数
        }
        return $array;
    }
    //如果仅有两个数组需要合并，也可以直接使用此函数
    public static function mergeOne(&$array,$pushArray)
    {
        foreach ($pushArray as $key=>$item){ //通过键值循环
            if (is_array($item)){ //如果待合并元素同样为数组，进行深度合并
                if(isset($array[$key])&&is_array($array[$key])){ //如果原数组同键名对应元素同样为数组
                    self::mergeOne($array[$key],$item); //递归深度合并
                }else{//如果原数组同键名对应元素不是数组，直接覆盖
                    $array[$key]=$item;
                }
            }else{ //如果待合并元素非数组，直接通过键名赋值
                $array[$key]=$item;
            }
        }
    }

    /**
     * 过滤指定键，支持移除指定键和保留指定键，支持无限极
     * @param array $array
     * @param string|array $keys
     * @param string|callable $mode ,默认remove:删除；save:保留；可定义方法；
     * @param string $children_key
     * @return array
     */
    public static function filterKey(array &$array, $keys, $mode='remove',$children_key = 'children'){
        $keys = is_array($keys) ? $keys : explode(',',$keys);
        if(self::isIndex($array)){
            foreach ($array as $k => $v) {
                self::filterKey($array[$k], $keys,$mode,$children_key);
            }
        }else{
            foreach ($array as $k => $v) {
                if(is_callable($mode)){
                    if($mode($v,$k)){
                        unset($array[$k]);
                    }
                }elseif ($mode == 'save') {
                    if(!(in_array($k,$keys) && $k == $children_key)){
                        unset($array[$k]);
                    }
                }else{
                    if(in_array($k,$keys)){
                        unset($array[$k]);
                    }
                }
                
            }
            if(isset($array[$children_key])){
                self::filterKey( $array[$children_key], $keys,$mode,$children_key);
            }
        }
    
    
        return $array;
    }

    /**
     * 过滤树形结构指定值，（支持无限极数据，子集符合,则父级也保留）
     * @param array $data 传入数据,无限极数据，二维数据
     * @param string|callable $search_key ,搜索关键词；可定义方法；
     * @param array $opts 扩展属性
     * @param string $opts['id_key'] id字段名
     * @param string $opts['pid_key'] pid字段名
     * @param string $opts['children_key']  children字段
     * @param int|string $opts['root']  根节点id的值
     * @return array 
     */
    public static function filterTreeValue(array $data,$search_key,$opts=[])
    {
        if(!$search_key) return $data;
        $id_key = Arr::get($opts,'id_key',TREE_ID);
        $pid_key = Arr::get($opts,'pid_key',TREE_PID);
        $children_key = Arr::get($opts,'children_key',TREE_CHILDREN);
        $path_key = Arr::get($opts,'path_key',TREE_PATH);
        $search_key_field = Arr::get($opts,'search_key_field',SELECT_LABEL);

        $normal_data = DataFormat::getFromTree($data,$opts);

        $save_ids = [];
        foreach ($normal_data as $k => $v) {
            $flag = true;
            if(is_callable($search_key)){
                $flag = $search_key($v,$search_key);
            }else{
                $flag=  Str::contains($v[$search_key_field], $search_key);
            }

            if($flag && isset($v[$path_key])){
                $save_ids = array_merge($save_ids,explode('-',$v[$path_key]));
            }
        }
        $save_ids = array_filter($save_ids);
        $save_ids = array_unique($save_ids);

        $result_data = [];
        foreach ($normal_data as $k => $v) {
            if(in_array($v[$id_key],$save_ids)){
                $result_data[] = $v;
            }
        }

        $result_data = DataFormat::getTree($result_data,$opts);

        return $result_data;
    }

    /**
     * 是否为索引数组
     * @param array $array
     * @return boolean
     */
    public static function isIndex($array) {
        if(is_array($array)) {
            $keys = array_keys($array);
            return $keys === array_keys($keys);
        }
        return false;
    }


     /**
     * 判断数组是否为一维数组
     * @param array $data
     * @return boolean
     * @author xk
     */
    public static function isOneDimenArray(array $data)
    {
        $flag = false;
        foreach ($data as $v) {
            if (!is_array($v)) {
                $flag = true;
                break;
            }
        }
        //count($data) == count($data, 1) 
        return $flag;
    }



    /**
     *  二维数组去重
     * @param array $_2d_array [待去重二维数组]
     * @param string|array $unique_key [去重键名,非必须]
     * @return array             [description]
     */
    public static function uniqueTwoArray($_2d_array, $unique_key = null)
    {
        $temp_arr = [];//临时数组，保存唯一数据
        foreach ($_2d_array as $k => $v) {
            if (is_array($v)) {
                //当前去重数组，无去重键名则整个数据加入临时数组
                $unique_arr = [];
                if ($unique_key) {
                    $unique_key = is_array($unique_key) ? $unique_key : explode(',', $unique_key);
                    foreach ($unique_key as $kk => $vv) {
                        $unique_arr[] = $v[$vv];
                    }
                } else {
                    $unique_arr = $v;
                }
                //判断当前去重数组是否在临时数组中存在
                if (in_array($unique_arr, $temp_arr)) {
                    unset($_2d_array[$k]);
                } else {
                    $temp_arr[] = $unique_arr;
                }
            } else {
                unset($_2d_array[$k]);
            }
        }
        return $_2d_array;
    }


    /**
     * 获取数组中重复的元素
     * @param array $data
     * @return array
     * @author xk
     */
    public static function getRepeatEle(array $data)
    {
        $unique_arr = array_unique($data);
        $repeat_arr = array_diff_assoc($data, $unique_arr);
        $repeat_arr = array_unique($repeat_arr);
        return $repeat_arr;
    }

    /**
     * 将传参数组类型的数据转为逗号分隔的字符(适用一维数组或者二维数据)
     * @param array $data
     * @return array $data
     * @author 薛康
     */
    public static function writeTransData(array $data)
    {
        if (!is_array($data)) {
            return $data;
        }
        if (self::isOneDimenArray($data)) {
            foreach ($data as $k => $v) {
                if (is_array($v)) {
                    $data[$k] = implode(',', $v);
                }
            }
        } else {
            foreach ($data as $k => $v) {
                foreach ($v as $kk => $vv) {
                    if (is_array($vv)) {
                        $data[$k][$kk] = implode(',', $vv);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * 任意维度的数组转换成一维数组
     * @param array $data
     * @return array
     * @author xk
     */
    public static function getOneDimenArray(array $data)
    {
        $result = [];
        array_walk_recursive($data, function ($value) use (&$result) {
            array_push($result, $value);
        });
        return $result;
    }

    /**
     * 对数组进行分组聚合
     * @param array $array
     * @param array $keys
     * @return array $result
     */
    public static function arrayGroupBy(array $array, $keys)
    {
        if (!is_array($keys) || count($keys) == 1) {
            $key = is_array($keys) ? array_shift($keys) : $keys;

            return array_reduce($array, function ($tmp_result, $item) use ($key) {
                $tmp_result[$item[$key]][] = $item;

                return $tmp_result;
            });
        } else {
            $keys = array_values($keys);

            $result = self::arrayGroupBy($array, array_shift($keys));

            foreach ($result as $k => $value) {
                $result[$k] = self::arrayGroupBy($value, $keys);
            }

            return $result;
        }
    }

















    /**
     * 两个数组一对多关联（list1.key1 = list2.key2）组成多维数组
     * @param array $list1 [列表1]
     * @param array $list2 [列表2]
     * @param array $params [关联条件] ['key1' => '','key2' => '','name' => 'children']
     * @return array [格式化完成的数组]
     * @author 陈昌盛
     */
    public static function withMany($list1, $list2, $params, $is_select = false)
    {
        $new_arr = [];
        foreach ($list1 as $v1) {
            $v1[$params['name']] = [];
            foreach ($list2 as $v2) {
                if ($v1[$params['key1']] == $v2[$params['key2']]) {
                    $v1[$params['name']][] = $v2;
                }
            }
            //如果是级联筛选 则将list1(父级)的value置为0
            if ($is_select) $v1['value'] = 0;
            $new_arr[] = $v1;
        }
        return $new_arr;
    }

    /**
     * 筛选二维数组字段
     * @param array $list [列表]
     * @param array $fields [字段列表]
     * @param array $type [0=>默认跳过不存在的字段 1=>不存在的字段置为空]
     * @return array            [格式化完成的数组]
     * @author 陈昌盛
     */
    public static function arrayColumns($list, $fileds, $type = 0)
    {
        $new_arr = [];
        foreach ($list as $k => $v) {
            foreach ($fileds as $field) {
                if (!isset($v[$field])) {
                    if ($type == 1) $new_arr[$k][$field] = '';
                    continue;
                }
                $new_arr[$k][$field] = $v[$field];
            }
        }
        return $new_arr;
    }

    /**
     * 筛选多维数组字段（递归筛选，适用于无限级TreeList）
     * @param array $list [表格列表数组(支持一维数组和多维数组)]
     * @param array $fields [表格字段数组(原字段名=>新字段名，如['ori_field'=>'new_field'])]
     * @param boolean $replace 是否只保留指定字段
     * @return array [格式化完成的数组]
     * @author 陈昌盛
     */
    public static function arrayFields($list, $fields, $replace = true)
    {
        $new_arr = $replace ? [] : $list;
        foreach ($list as $k => $v) {
            if (is_array($v)) {
                $new_arr[$k] = self::arrayFields($v, $fields, $replace);
            } else {
                foreach ($fields as $ori_field => $new_field) {
                    if (isset($list[$ori_field])) {
                        $new_arr[$new_field] = $list[$ori_field];
                    }
                    //unset($new_arr[$ori_field]);
                }
            }
        }
        return $new_arr;
    }

    /**
     * 两个数组进行in(not in)运算
     * @param array $list1 [列表1]
     * @param array $list2 [列表2]
     * @param int $type ['in'是交集 'not in'是差集]
     * @param array $fields [指定比较字段]
     * @return array         [数组]
     * @author 陈昌盛
     */
    public static function arrayIntersect($list1, $list2, $type = 'in', $fields = [])
    {
        if ($fields) {
            $list1 = self::arrayColumns($list1, $fields);
            $list2 = self::arrayColumns($list2, $fields);
        }
        return array_filter($list1, function ($item) use ($list2, $type) {
            if (in_array($item, $list2)) return $type == 'in' ? true : false;
            return $type == 'in' ? false : true;
        });
    }

    /**
     * 两个数组进行合并，相同字段用逗号拼接他们的值
     * @param array $list1 [列表1]
     * @param array $list2 [列表2]
     * @param array $fields [列表2指定字段]
     * @return array         [数组]
     * @author 陈昌盛
     */
    public static function arrayMerge($list1, $list2, $fields = [])
    {
        $list2 = Arr::only($list2, $fields);
        foreach ($list2 as $k => $v) {
            if (!empty($list1[$k])) $list1[$k] .= ',' . $v;
            else $list1[$k] = $v;
        }
        return $list1;
    }

    /**
     * 将字符串变成关联数组(场景：tp order字符串分割成数组)
     * @param string $str [字符串]
     * @param array $sep_array [分割数组]
     * @param array $sep_key_value [分割key value]
     * @return array         [数组]
     * @author 陈昌盛
     */
    public static function strToArray($str, $sep_array = ',', $sep_key_value = ' ')
    {
        $rs = [];
        $arr = explode($sep_array, $str);
        foreach ($arr as $v) {
            $key_value = explode($sep_key_value, $v);
            if (count($key_value) == 2) {
                $rs[$key_value[0]] = $key_value[1];
            }
        }
        return $rs;
    }


    /**
     * 找到第一个满足条件的数组元素
     * @param array $list [列表]
     * @param array $condition [条件数组]
     * @return array            [格式化完成的数组]
     * @author 陈昌盛
     */
    public static function arrayFind($list, $condition, $fields = [])
    {
        if ($fields) {
            $list = self::arrayColumns($list, $fields);
            $condition = Arr::only($condition, $fields);
        }
        $key = array_search($condition, $list);
        if (!$key) return [];
        return $list[$key];
    }

    
}