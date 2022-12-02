<?php

namespace app\common\tools;
use think\facade\Db;

/**
 * 数据库相关工具类
 */
class DbTool
{
    /** 获取当前数据库名
     * @return string
     */
    public static function getCurrentDataBaseName()
    {
        return config('database.connections.mysql.database') ?? env('database.database');
    }

    /**
     * 获取当前库所有表名
     * @param string $condition
     * @param string $field
     * @return array
     */
    public static function getDataBaseAllTableName($condition = '',$field='TABLE_NAME,TABLE_COMMENT')
    {
        $database = self::getCurrentDataBaseName();
        return Db::table('INFORMATION_SCHEMA.TABLES')
            ->field($field)
            ->where('TABLE_SCHEMA',$database)
            ->where($condition)
            ->select()->toArray();
    }

    /**
     * 获取指定表的所有字段
     * @param string $table_name 表名
     * @return array
     */
    public static function getTableAllField($table_name)
    {
        $database = self::getCurrentDataBaseName();
        return Db::query("SELECT COLUMN_NAME,COLUMN_COMMENT,DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$database}' AND TABLE_NAME = '{$table_name}'");
	}

	/**
     * 判断某个表是否存在
     * @param string $tabel_name 表名
     * @return boolean
     */
    public static function hasTable($tabel_name){
        return Db::query('SHOW TABLES LIKE '."'".$tabel_name."'");
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