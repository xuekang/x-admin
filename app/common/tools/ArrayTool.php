<?php

declare (strict_types = 1);

namespace app\common\tools;

use think\helper\Arr;

/**
 * 数组相关工具类
 */
class ArrayTool
{
    const SELECT_FIELD_VALUE = 'value';//selet选项值默认字段
    const SELECT_FIELD_LABEL = 'label';//selet选项显示值默认字段

    /**
     * 将键值对数组转为select选项数组
     * @param array $data 键值对数组 ['选项值1'=>'选项名称1','选项值2'=>'选项名称2']
     * @param string $select_field_value selet选项值字段
     * @param string $select_field_label selet选项显示值字段
     * @return array 将select选项数组 [['label'=>'选项名称1','value'=>'选项值1'],['label'=>'选项名称2','value'=>'选项值2']]
     */
    public static function makeSelectArr($data,$select_field_value=self::SELECT_FIELD_VALUE,$select_field_label=self::SELECT_FIELD_LABEL)
    {
        $result = [];
        foreach ($data as $key => $val) {
            $result[] = [$select_field_label => $val, $select_field_value => strval($key)];
        }
        return $result;
    }

    /**
     * 将select选项数组转为键值对数组
     * @param array $data 将select选项数组 [['label'=>'选项名称1','value'=>'选项值1'],['label'=>'选项名称2','value'=>'选项值2']]
     * @param string $select_field_value selet选项值字段
     * @param string $select_field_label selet选项显示值字段
     * @return array 键值对数组 ['选项值1'=>'选项名称1','选项值2'=>'选项名称2']
     */
    public static function unMakeSelectArr($data,$select_field_value=self::SELECT_FIELD_VALUE,$select_field_label=self::SELECT_FIELD_LABEL)
    {
        $result = [];
        foreach ($data as $k => $v) {
            $result[$v['value']] = $v['label'];
        }
        return $result;
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
     * 二维数组中指定子项元素之和
     */
    public static function getChildSum(array $data, $filed)
    {
        $sum = 0;
        foreach ($data as $item) {
            $sum += (float)$item[$filed];
        }
        return $sum;
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

    /**
     * 替换一维数组键名
     * @param $data
     * @param $keys
     * @return mixed
     */
    public static function replaceKeys($data, $keys)
    {
        foreach ($data as $k => $item) {
            if (isset($keys[$k])) {
                $data[$keys[$k]] = $item;
                unset($data[$k]);
            }
        }
        return $data;
    }

    /**
     * 替换二维数组键名
     * @param $data
     * @param $keys
     * @return mixed
     */
    public static function arrayReplaceKeys($data, $keys)
    {
        foreach ($data as $k => $value) {
            foreach ($value as $vk => $item) {
                if (isset($keys[$vk])) {
                    $data[$k][$keys[$vk]] = $item;
                    unset($data[$k][$vk]);
                }
            }
        }
        return $data;
    }

    /**
     * 无限极分类树
     * @param array $data
     * @param int $parent_id
     * @param int $level
     * @return array
     */
    public static function getTree($data = [], $filterKeys = [], $parent_id = "", $level = 0, $parent_field = "pid", $id_field = "id", $children_field = "children")
    {
        $tree = [];
        if ($data && is_array($data)) {
            foreach ($data as $v) {
                if ($v[$parent_field] == $parent_id) {
                    $children = self::getTree($data, $filterKeys, $v[$id_field], $level + 1, $parent_field, $id_field, $children_field);
                    $v[$children_field] = $children;
                    $tree[] = Arr::only($v, $filterKeys);
                }
            }
        }
        return $tree;
    }
}