<?php

namespace app;

use think\Collection;
use think\facade\Db;
use think\helper\Arr;
use think\Model;
use think\model\concern\SoftDelete;

/** 
 * 模型基础类
 * @author xk
 */
class BaseModel extends Model
{

    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
   
    /** 获取设置信息
     * @return array
     */
    public static function getSysOpts()
    {
        return  request()->sysOpts ?? [];
    }
    
    /** 获取系统当前用户id
     * @return int
     */
    public static function getUserId()
    {
        return self::getSysOpts()['user_id'] ?? DEFAULT_USER_ID;
    }

    /** 获取系统当前用户中文名
     * @return string
     */
    public static function getUserRealName()
    {
        return self::getSysOpts()['user_real_name'] ?? DEFAULT_USER_REAL_NAME;
    }

    /** 获取系统当前时间
     * @return int
     */
    public static function getSysTime()
    {
        return self::getSysOpts()['sys_time'] ?? time();
    }

    /** 通用查询
     * @param array $where 查询条件
     * @param string|array $field 查询字段
     * @return Model
     */
    public static function myQuery($where, $field = false, $order = [], $group = '')
    {
        $query =  $field ? self::where($where)->field($field) : self::where($where);
        if ($order) $query->order($order);
        if ($group) $query->group($group);
        return $query;
    }
    

    /** 获取单条记录
     * @param array $where 查询数组条件
     * @param  string|array $field 需要查询字段，默认为全部
     * @return array
     */
    public static function getOne($where, $field = '*', $order = [], $group = '')
    {
        return self::myQuery($where, $field, $order, $group)->findOrEmpty()->toArray();
    }

    /** 获取指定条件指定值
     * @param array $where 查询条件数组
     * @param string $field 字段
     * @return mix
     */
    public static function getOneValue($where,$field)
    {
        return self::where($where)->value($field);
    }

    /** 根据自定义条件获取多条记录
     * @param array $where 查询条件数组
     * @param string|array $fields 字符串，默认为全部字段，可以自由定制
     * @param string|array $order 排序
     * @return array
     */
    public static function getAll($where=[], $field = '*', $order = PAGE_ORDER, $group= '')
    {
        return self::myQuery($where, $field, $order, $group)
            ->select()
            ->toArray();
    }

    /** 根据自定义条件获取多条记录
     * @param string|array $column 栏目
     * @param string $index_key 栏目索引
     * @param array $where 查询条件数组
     * @param string|array $order 排序
     * @return array
     */
    public static function getColumn($column=null,$index_key=null,$where=[], $order = PAGE_ORDER, $group= '')
    {
        $column = $column ? $column : '*';
        $index_key = $index_key ? $index_key : '';
        return self::myQuery($where, $column, $order, $group)->column($column,$index_key);
    }


    /** 添加单条记录并获取其主键
     * @param array $data 数据
     * @return int
     */
    public static function addOne($data)
    {
        return self::myCreate($data)->getKey();
    }

    /** 通用单条数据新增
     * @param array $data
     * @return Model
     */
    public static function myCreate($data)
    {
        $real_name = self::getUserRealName();
        $time = self::getSysTime();
        $data['id'] = make_id();
        $data['modifier'] = $real_name;
        $data['creator'] = $real_name;
        $data['create_time'] = $time;
        $data['update_time'] = $time;
        return self::create($data);
    }

    /** 通用单表数据静态更新(推荐)
     * @description 如果不需要使用事件或者不查询直接更新，直接使用静态的Update方法进行条件更新
     * @param array $where 条件
     * @param array $data 数据
     * @return Model
     */
    public static function myUpdate($where, $data)
    {
        $data['modifier'] = self::getUserRealName();
        $data['update_time'] = self::getSysTime();
        return self::update($data, $where);
    }

	/** 通用数据删除
     * @param int|array $data 主键|条件
     * @return boolean
     */
    public static function mySoftDel($data)
    {
        if(is_array($data)){
            $where = $data;
        }else{
            $where = [['id','in',strval($data)]];
        }
        $flag = self::myUpdate($where,['delete_time'=>self::getSysTime()]);
        return $flag ? true : false;
    }

	/** 通用单表单条数据更新
     * @param array $where 条件
     * @param array $data 数据
     * @return Model
     */
    public static function mySave($data,$where=[])
    {
        $data['modifier'] = self::getUserRealName();
        $data['update_time'] = self::getSysTime();
        return self::where($where)->save($data);
    }


    /** 通用多条数据新增
     * @param array $data
     * @return integer
     */
    public static function myInsertAll($data)
    {
        $real_name = self::getUserRealName();
        $time = self::getSysTime();
        foreach ($data as $k => $v) {
            $data[$k]['id'] = make_id();
            $data[$k]['modifier'] = $real_name;
            $data[$k]['creator'] = $real_name;
            $data[$k]['create_time'] = $time;
            $data[$k]['update_time'] = $time;
        }
        return self::insertAll($data);
    }

    /** 通用多条数据新增(自动判断主键)
     * @param array $data
     * @return Collection
     */
    public function mySaveAll($data)
    {
        $real_name = self::getUserRealName();
        $time = self::getSysTime();
        foreach ($data as $k => $v) {
            if(!isset($v['id'])){
                $data[$k]['id'] = make_id();
                $data[$k]['creator'] = $real_name;
                $data[$k]['create_time'] = $time;
            }
            $data[$k]['modifier'] = $real_name;
            $data[$k]['update_time'] = $time;
        }
        return $this->saveAll($data)->toArray();
    }

    /**
     * 翻译condition数组
     * @param $condition
     * @return array
     */
    public static function filterCondition($condition)
    {
        if(!empty($condition)){
            foreach ($condition as &$v){
                if(!empty($v[1]) && $v[1] == 'exp' && !empty($v[2])) $v[2] = Db::raw($v[2]);
            }
            unset($v);
        }
        return $condition;
    }
    
    /** 通用条件查询分页方法
     * @param array $where 查询条件
     * @param int $start 起始位置
     * @param int $limit 返回列，为-1时返回全量数据
     * @param string|array $field 查询字段
     * @param string|array $order 排序规则
     * @param array $join 连表条件，数字成员：alias,join_array,join_array支持多次连表操作，二位数组传参
     * @param array $with
     * @return array
     */
    public static function list($condition=[], $start = PAGE_START, $limit = PAGE_LIMIT, $field = '*', $order = PAGE_ORDER, $join = [], $with = [])
    {
        $data = ['total' => 0, 'list' => []];

        $condition = self::filterCondition($condition);
        if(!empty($join)){
            $joinKey   = array_keys($join);
            $alias     = $joinKey[0];
            $joinArray = $join[$alias];
            $list      = self::alias($alias);
            //多join组装
            foreach ($joinArray as $v){
                $list->join($v[0], $v[1], $v[2]??'INNER');
            }
            $list  = $list->where($condition);
        }else{
            $list  = self::where($condition);
        }
        if (!empty($with)) $list->with($with);
        $count = clone $list;
        if($limit > 0){
            $list = $list->page($start, $limit);
        }
        $total = $count->count();
        if($total){
            $list = $list->field($field)->order($order)->select()->toArray();
        }else{
            $list = [];
        }

        return ['total' => $total, 'list' => $list];
    }

    public static function listO($param){
        $condition = Arr::get($param,'condition',[]);
		$start = Arr::get($param,'current_page',PAGE_START); 
		$limit = Arr::get($param,'page_size',PAGE_LIMIT);
        $field = Arr::get($param,'field','*');
        if($field && $field !== '*'){
            $field = is_array($field) ? $field : explode(',',$field);
            $field[] = ID;
            $field[] = ORDER_VALUE_FIELD;
            $field = array_unique(array_filter($field));
        }
        $field = empty($field) ? '*' : $field;
        $order = Arr::get($param,'order',PAGE_ORDER);
        $join = Arr::get($param,'join',[]);
        $with = Arr::get($param,'with',[]);
        return self::list($condition,$start,$limit,$field,$order,$join,$with);
    }
}