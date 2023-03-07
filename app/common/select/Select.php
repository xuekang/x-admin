<?php
declare (strict_types=1);

namespace app\common\select;

use app\BaseLogic;
use app\common\tools\DataFormat;
use app\common\tools\ArrayTool;
use app\common\tools\StringTool;
use app\common\tools\ValidateTool;
use think\helper\Str;
use think\helper\Arr;
use app\sys_data\logic\SysFileLogic;
use app\sys_data\logic\SysData;
use think\facade\Cache;
use think\facade\Db;
use app\sys_data\logic\FunctionAplLogic;
use think\console\command\make\Validate;

/**
 * 通用集成选项
 * @author 薛康
 */
class Select extends BaseLogic
{
    protected $selectConfigMap; //选项配置数组
    protected $selectDict; //文件配置类选项
    protected $selectCacheTag = 'select_cache_key'; //选项缓存tag
    protected $SysFileLogic = null;
    

    /**
     * 架构函数
     * @return void
     */
    public function __construct($args = [])
    {
        parent::__construct();

        //配置类选项
        $this->selectDict = require_once root_path() . '/app/common/select/SelectDict.php';

        $this->SysFileLogic = new SysFileLogic();

        if(isset($args['select_config_map'])) $this->selectConfigMap = $args['select_config_map'];
    }

    /**
     * 获取选项
     * @param array|string $code_arr [
     *        'boolean'=>
     *            [
     *                'type'=>'select',//string，类型，非必须，可能值:select,img,file,video,默认select
     *                'code'=>'',//string，选项code，非必须；可能值:自定义，不填默认为本键
     *                'param'=>'',//string，额外参数，非不必须；当type为img,file,video时为必须值，需要对应的文件id;当type其他类型时为搜索关键词；
     *                'condition'=>[],/查询条件，非必须；用于动态查询条件
     *            ]
     * ]
     * 简洁写法:['boolean'=>'boolean'] 或者 ['boolean'] 获取 'boolean'
     * @return array 前端用数据，如[
     *        'boolean'=>[['label'=>'是','value'=>'1'],['label'=>'否','value'=>'0']]
     * ]
     * @author xk
     */
    public function getSelect($code, $args = [])
    {
        if (!$code) return [];

        $code_arr = is_string($code) ? explode(',', $code) : $code;
        $data = [];
        
        foreach ($code_arr as $k => $v) {
            //解析选项code
            list($code,$type,$param,$condition,$page_param,$select_cache_key) = $this->parseSeletCode($k,$v);

            $data_item = [];

            //读取缓存
            $data_item = Cache::get($select_cache_key);
            if($data_item){
                $data[$code] = $data_item;
                continue;
            }
            if (in_array($type, ['img', 'video', 'file'])) {
                //文件类
                $data_item = array_values($this->SysFileLogic->getShowData($param));
            } else {
                //选项类
                $data_item = $this->getSelectValue($code, $param, $condition, $page_param);
            }

            //加入缓存
            $select_cache_expire = config('app.select_cache_expire');
            if($select_cache_expire){
                Cache::tag([$this->selectCacheTag,$this->selectCacheTag.':'.$code])->set($select_cache_key,$data_item,$select_cache_expire+mt_rand(0,15));
            }

            $data_item = array_values($data_item);
            $data[$code] = $data_item;
        }

        return $data;
    }

    /**
     * 获取选项映射数组
     * @param array|string $code 详情见getSelect
     * @return array 键值对 ，如['boolean'=>['1'=>'是','0'=>'否']]
     * @author xk
     */
    public function getSelectMap($code,$type='select')
    {
        $select_data = $this->getSelect($code);
        
        $data = [];
        
        foreach ($select_data as $k => $v) {
            $data_item = $this->getSelectMapItem($v,$type);
            $data[$k] = $data_item;
        }

        return $data;
    }


    /**
     * 清空全部选项的本地缓存数据
     * @return void
     * @author xk
     */
    public function clearSelectCache($code=''){
        // 清除tag标签的缓存数据
        $key = $code ? $this->selectCacheTag.':'.$code : $this->selectCacheTag;
        Cache::tag($key)->clear();
    }






    /**
     * 解析选项code
     * @param string|int $code_k 
     * @param string|array $code_v
     * @return array
     * @author xk
     */
    protected function parseSeletCode($code_k,$code_v)
    {
        //解析选项code
        if (is_array($code_v)) {
            $code = isset($code_v['code']) && $code_v['code'] ? $code_v['code'] : $code_k;
            $type = isset($code_v['type']) && $code_v['type'] ? $code_v['type'] : 'select';
            $param = isset($code_v['param']) ? $code_v['param'] : '';
            $param = is_string($param) ? trim($param) : $param;
            $condition = isset($code_v['condition']) && $code_v['condition'] ? $code_v['condition'] : [];
            $page_param = isset($code_v['page_param']) && $code_v['page_param'] ? $code_v['page_param'] : [];
        } else {
            $code = $code_v;
            $type = 'select';
            $param = '';
            $condition = [];
            $page_param = [];
        }

        $select_cache_key = compact('code','type','param','condition');
        $select_cache_key = json_encode($select_cache_key);
        $select_cache_key = md5($select_cache_key);

        return [$code,$type,$param,$condition,$page_param,$select_cache_key];
    }

    /**
     * 获取选项数据
     * @param string $code 选项code
     * @param string $param 搜索关键词
     * @param array $condition 选项额外查询条件
     * @param array $page_param 页面参数
     * @return array
     * @author xk
     */
    protected function getSelectValue($code, $param = '', $condition = [], $page_param=[])
    {
        $value = [];

        //从配置文件获取
        $value = $this->getSelectvalueFromFileDict($code,$param);
        if($value !== false) return $value;

        //从redis中获取
        $value = $this->getSelectvalueFromRedis($code,$param);
        if($value !== false) return $value;


        //数据库类选项定义
        if(isset($this->selectConfigMap[$code])){
            $select_item = $this->selectConfigMap[$code];
        }else{
            $select_item = current(SysData::getSelectData($code));
        }
        if ($select_item) {
            if($select_item['sele_func']){
                $value = FunctionAplLogic::executeFunction($select_item['sele_func'],$param,$condition,$page_param);
            }else{
                $value = $this->getSelectvalueFromTable($select_item, $param, $condition);
            }
            $value = $this->formatSelectValue($value);
        }else{
            $value = [];
        }

        return $value;
    }

    /**
     * 根据选择文件字典获取选项数据
     * @param array $select 选项定义数据
     * @param string $search_key 搜索关键词
     * @param array|boolean
     * @author xk
     */
    protected  function getSelectvalueFromFileDict($code, $search_key){
        if(!isset($this->selectDict[$code])) return false;
        $data = $this->selectDict[$code];
        $data = $this->formatSelectValue($data);
        $data = $this->filterSelectValue($data,$search_key);
        return $data;
    }

    /**
     * 根据redis获取选项数据
     * @param array $select 选项定义数据
     * @param string $search_key 搜索关键词
     * @param array $condition 动态查询条件
     * @param array|boolean
     * @author xk
     */
    public function getSelectvalueFromRedis($code, $search_key){
        $data = [];
        $key = app('Redis')->selectKey($code);
        $data = app('Redis')->get($key);
        if($data === false) return false;

        $data = json_decode($data['sele_data'],true);
        $data = $this->formatSelectValue($data);
        $data = $this->filterSelectValue($data,$search_key);
        return $data;
    }

    /**
     * 根据选项条件获取选项数据
     * @param array $select 选项定义数据
     * @param string $search_key 搜索关键词
     * @param array $condition 动态查询条件
     * @param array
     * @author xk
     */
    public function getSelectvalueFromTable($select, $search_key,$condition=[])
    {
        if (!$select) {
            return [];
        }

        //处理参数
        $sele_value = $select['sele_value'];
        $sele_label = $select['sele_label'];
        $sele_cascade_key = $select['sele_cascade_key'] ? : $sele_value;
        $sele_cascade_pkey = $select['sele_cascade_pkey'];
        $sele_cascade_path = $select['sele_cascade_path'];
        $sele_value_ass = $select['sele_value_ass'];

        //处理默认查询条件
        $sele_where = $select['sele_where'];
        if ($search_key) {
            $search_condi = "$sele_label like '%$search_key%' or $sele_value like '%$search_key%'";
            if($sele_value_ass){
                $search_condi .= " or $sele_value_ass like '%$search_key%'";
            }
            if ($sele_where) {
                $sele_where .= " and ($search_condi)";
            }else{
                $sele_where = $sele_where;
            }
        }

        //处理动态查询条件
        $dynamic_condi = [];
        if($condition){
            foreach ($condition as $key => $value) {
                $dynamic_condi[] = is_array($value) ? $value : [$key, '=', $value];
            }
        }
       
        //处理查询字段
        $field = ['id', $sele_value, $sele_label];
        foreach([$sele_cascade_pkey, $sele_cascade_path, $sele_value_ass] as $this_field){
            $this_field && array_push($field,$this_field);
        }


        if ($sele_cascade_pkey) {
            // $data = Db::table($select['sele_table'])->where($sele_where)->column("$sele_cascade_key as value,$sele_label as label,$sele_cascade_key as id,$sele_cascade_pkey as pid",$sele_cascade_key);
            $sele_cascade_path = $sele_cascade_path ? $sele_cascade_path : 'path';
            $field[] = $sele_cascade_path;
            $data_origin = Db::table($select['sele_table'])->where($sele_where)->where($dynamic_condi)->field($field)->order(PAGE_ORDER)->select()->toArray();

            $key_arr = [];
            foreach ($data_origin as $key => $value) {
                $key_arr = array_merge($key_arr, explode('-', strval($value[$sele_cascade_path])));
            }
            if ($key_arr) {
                $data_origin = array_merge($data_origin, Db::table($select['sele_table'])->where([[$sele_cascade_key, 'in', $key_arr]])->field($field)->order(PAGE_ORDER)->select()->toArray());
            }

            //组织数据
            $data = [];
            foreach ($data_origin as $k => $v) {
                $value = $v[$sele_cascade_key];
                $label = $v[$sele_label];
                if($sele_value_ass && $v[$sele_value_ass]){
                    $label .= "(" . $v[$sele_value_ass] . ")";
                }

                $item = [
                    'value' => $value,
                    'label' => $label,
                    'id' => $v[$sele_cascade_key],
                    'pid' => $v[$sele_cascade_pkey]
                ];
                $data[] = $item;
            }
        } else {
            $data_origin = Db::table($select['sele_table'])->where($sele_where)->where($dynamic_condi)->field($field)->order(PAGE_ORDER)->select()->toArray();
            
            //组织数据
            $data = [];
            foreach ($data_origin as $k => $v) {
                $value = $v[$sele_value];
                $label = $v[$sele_label];
                if($sele_value_ass && $v[$sele_value_ass]){
                    $label .= "(" . $v[$sele_value_ass] . ")";
                }
                $item = [
                    'value' => $value,
                    'label' => $label
                ];
                $data[] = $item;
            }
        }
        return $data;
    }


    /**
     * 过滤选项数据
     * @param array $data 
     * @param string $search_key 搜索关键词
     * @param array
     * @author xk
     */
    public function filterSelectValue($data,$search_key){
        if(!$search_key) return $data;
        if(self::isTree($data)){
            $data = ArrayTool::filterTreeValue($data,$search_key);
        }else{
            $data = array_filter($data,function ($item) use ($search_key){
                return Str::contains($item[SELECT_LABEL],$search_key);
            });
        }
        return $data;
    }

    /**
     * 格式化选项数据
     * @param array $select_value
     * @return array
     * @author xk
     */
    public function formatSelectValue($select_value)
    {
        if(isset(current($select_value)['pid'])) {
            if(self::isTree($select_value)){
                //已格式化则保留原数据
                $data = $select_value;
            }else{
                //格式化为树结构选项类数据：label和value和children
                $data = DataFormat::getTree($select_value);
            }
            
        }else{
            if (!ArrayTool::isOneDimenArray($select_value) && isset(current($select_value)[SELECT_LABEL]) && isset(current($select_value)[SELECT_VALUE]) ){
                //已格式化则保留原数据
                $data = $select_value;
            }else{
                $data = self::makeSelectArr($select_value);
            }
            
        }
        //将value 统一转换类型
        $data = $this->formatSelectValueToNumber($data);
        return $data;
    }
    

    /**
     * 获取单个选项映射数组
     * @param array $select_item 单个选项初始数组
     * @param string $type
     * @return array 键值对
     * @author xk
     */
    protected function getSelectMapItem($select_item,$type='select', &$data = [])
    {
        foreach ($select_item as $k => $v) {
            $data[$v[SELECT_VALUE]] = $type == 'file' ? $v : $v[SELECT_LABEL];
            if (isset($v[TREE_CHILDREN]) && $v[TREE_CHILDREN]) {
                $this->getSelectMapItem($v[TREE_CHILDREN], $type,$data);
            }
        }
        return $data;
    }

    /**
     * 将value 统一转为字符串
     * @return array 
     * @author xk
     */
    protected function formatSelectValueToString($data)
    {
        foreach($data as $k=>$v){
            $data[$k][SELECT_VALUE] = strval($data[$k][SELECT_VALUE]);
            if(isset($v[TREE_CHILDREN])){
                $data[$k][TREE_CHILDREN] = $this->formatSelectValueToString($data[$k][TREE_CHILDREN]);
            }
        }
    
        return $data;
    }


    /**
     * 将value 统一转为数字
     * @return array 
     * @author xk
     */
    protected function formatSelectValueToNumber($data)
    {
        foreach($data as $k=>$v){
            if(is_numeric($data[$k][SELECT_VALUE]))  $data[$k][SELECT_VALUE] = StringTool::toNumber($data[$k][SELECT_VALUE]);
           
            if(isset($v[TREE_CHILDREN])){
                $data[$k][TREE_CHILDREN] = $this->formatSelectValueToNumber($data[$k][TREE_CHILDREN]);
            }
        }
    
        return $data;
    }




    /**
     * 将键值对数组转为select选项数组
     * @param array $data 键值对数组 ['选项值1'=>'选项名称1','选项值2'=>'选项名称2']
     * @param string $value selet选项值字段
     * @param string $lable selet选项显示值字段
     * @return array 将select选项数组 [['label'=>'选项名称1','value'=>'选项值1'],['label'=>'选项名称2','value'=>'选项值2']]
     */
    public static function makeSelectArr($data,$value=SELECT_VALUE,$lable=SELECT_LABEL)
    {
        $result = [];
        foreach ($data as $key => $val) {
            $result[] = [$lable => $val, $value => $key];
        }
        return $result;
    }

    /**
     * 将select选项数组转为键值对数组
     * @param array $data 将select选项数组 [['label'=>'选项名称1','value'=>'选项值1'],['label'=>'选项名称2','value'=>'选项值2']]
     * @param string $value selet选项值字段
     * @param string $lable selet选项显示值字段
     * @return array 键值对数组 ['选项值1'=>'选项名称1','选项值2'=>'选项名称2']
     */
    public static function unMakeSelectArr($data,$value=SELECT_VALUE,$lable=SELECT_LABEL)
    {
        $result = [];
        foreach ($data as $k => $v) {
            $result[$v[$value]] = $v[$lable];
        }
        return $result;
    }

    /**
     * 是否为树结构数据
	 * @param array $data
	 * @param array $opts 扩展属性
     * @param string $opts['id_key'] id字段名
     * @param string $opts['pid_key'] pid字段名
     * @param string $opts['children_key']  children字段
     * @return boolean
     */
    public static function isTree(array $data,$opts=[])
    {
		$flag = false;
        $id_key = Arr::get($opts,'id_key',TREE_ID);
        $pid_key = Arr::get($opts,'pid_key',TREE_PID);
        $children_key = Arr::get($opts,'children_key',TREE_CHILDREN);

        foreach ($data as $k => $v) {
			if(is_array($v) && isset($v[$id_key]) && isset($v[$pid_key]) && isset($v[$children_key])){
				$flag = true;
				break;
			}
		}
		return $flag;
    }


    /**
     * 获取查询键
	 * @param array $select_item
     * @return string
     */
    public function getSelectQueryKey($select_item)
    {
        $query_field = ID;
        if($select_item){
            if($select_item['sele_cascade_key']){
                $query_field = $select_item['sele_cascade_key'];
            }elseif($select_item['sele_value']){
                $query_field = $select_item['sele_value'];
            }
        }
        return $query_field;
    }
}
