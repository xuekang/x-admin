<?php

namespace app\common\lib;
use think\facade\Db;

/**
 * 数据库相关工具类
 */
class DbTool
{

	/**
     * 判断某个表是否存在
     * @param string $tabel_name 表名
     * @return boolean
     */
    public static function hasTable($tabel_name){
        return Db::query('SHOW TABLES LIKE '."'".$tabel_name."'");
    }

    /**
     * 判断某个值是否有效
     * @param mix $value
     * @return boolean
     */
    public static function isValidValue($value){
        return ($value || $value === 0 || $value === '0') && $value !== 'null';
    }
    
    /**
     * 获取有效表名称
     * @param string $tabel_name
     * @return string
     */
    public static function getValidTableName($tabel_name){
        return preg_replace('/_ass$/','',$tabel_name);
    }
}