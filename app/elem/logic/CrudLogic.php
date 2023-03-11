<?php
declare (strict_types = 1);

namespace app\elem\logic;

use app\BaseLogic;
use think\helper\Arr;
use think\helper\Str;
use app\elem\logic\EleUtility;
use app\sys_data\logic\SysData;
use app\BaseModel;
use app\common\tools\DataFormat;
use app\model\SysElemTableEle;
use app\model\SysTableRule;
use app\sys_data\logic\FunctionAplLogic;


class CrudLogic extends BaseLogic
{
	/**
     * 基础对象
     * @var BaseModel
     */
	public static $Model = null;

	public $rule_list = [];

	/**
     * 架构函数
     * @return void
     */
    public function __construct($table_name)
    {
        parent::__construct();
		$model_name = '\\app\\model\\' . Str::studly($table_name);
		self::$Model = new $model_name();

		$this->rule_list = SysTableRule::where('tbrl_table_name','in',['',$table_name])->order(['order_value'=>'asc','id'=>'asc'])->select()->toArray();
		
	}

	public function list($param)
	{
		$param = $this->excuteRule($param);

		$param['condition'] = $this->parseCondition(Arr::get($param,'condition',[]));
		$data = self::$Model::listO($param);

		$list = $this->excuteRule(0,$param,$data['list']);

		$ListUtility = new ListUtility($list);
		$show_list = $ListUtility->format();

		$data['list'] = $this->excuteRule(1,$data['list'],$show_list);

		return $data;
	}

	public function add($param)
	{
		$param = $this->excuteRule($param);

		$data = EleUtility::eleWriteBatch($param);

		$data = $this->excuteRule($param,$data);
		// dump('add data',$param,$data);

		$result = self::$Model::addOne($data);

		$result = $this->excuteRule($param,$data,$result);

		return $result;
	}

	public function edit($param)
	{
		$param = $this->excuteRule($param);

		$data = EleUtility::eleWriteBatch($param);

		$data = $this->excuteRule($param,$data);

		$result = self::$Model::mySave($data);

		$result = $this->excuteRule($param,$data,$result);

		return $result;
	}

	public function del($param)
	{
		$param = $this->excuteRule($param);

		$param = $this->excuteRule($param,$param);

		$result = self::$Model::mySoftDel($param[ID]);

		$result = $this->excuteRule($param,$param,$result);

		return $result;
	}

	public function get($param)
	{
		$param = $this->excuteRule($param);

		$data = self::$Model::findOrEmpty($param[ID])->toArray();

		$data = $this->excuteRule($param,$data);

		$result = EleUtility::eleReadBatch($data);

		$result = $this->excuteRule($param,$data,$result);

		return $result;
	}

	public function excuteRule(...$args){
		$len = count($args);

		foreach ($this->rule_list as $k => $rule) {
			if(!($rule['tbrl_point'] == $len)) continue;

			$tbrl_url = $this->getUrl($rule['tbrl_url']);
			$request_url = $this->getUrl('',$rule['tbrl_url_match_mode']);
			if(!($tbrl_url === '' || ($tbrl_url && $tbrl_url == $request_url))) continue;
			
			$args[$len-1] = FunctionAplLogic::executeFunction($rule['tbrl_content'],...$args);
		}
		return $args[$len-1];
	}


	public function getUrl($url='',$mode=false)
	{
		$mode = $mode ? true : false;
		$url = $url ? $url : request()->url($mode);
		$url = trim($url);
		$url = strtolower($url);
		$url = implode('/',array_filter(explode('/',$url)));
		return $url;
	}

	public function parseCondition($condition)
	{
		if(!$condition) return $condition;

		$elem_names = array_keys($condition);

		$elem_map = SysData::getEleData($elem_names);

		$table_ele_map = SysElemTableEle::where('etbe_elem_name','in',$elem_names)->column('*','etbe_elem_name');
		foreach ($elem_map as $elem_name => $v) {
			$item = $v;

			$item = array_merge($item,DataFormat::getJsonValue($v,'elem_attrs'));

			$item['search_mode'] = '';
			$item['search_value'] = '';

			if(isset($table_ele_map[$elem_name]) && $table_ele_map[$elem_name]['etbe_extend']){
				$item = array_merge($item,DataFormat::getJsonValue($table_ele_map[$elem_name],'etbe_extend'));

				$item['search_mode'] = $table_ele_map[$elem_name]['etbe_search_mode'];
				$item['search_value'] = $table_ele_map[$elem_name]['etbe_search_value'];
			}
			
			
			$elem_map[$elem_name] = $item;
		}

		
		$data = [];
		foreach ($condition as $elem_name => $value) {
			if(isset($elem_map[$elem_name])){
				$elem_item = $elem_map[$elem_name];
				$type = $elem_item['elem_form_type'];
				$elem_is_valid_zero = $elem_item['elem_is_valid_zero'];
				if(!EleUtility::isValidValue($value,$elem_is_valid_zero)) continue;

				if(EleUtility::isSelect($type)){
					$value = is_array($value) ? $value : strval($value);
					$data[] = [$elem_name,'in',$value];
				}elseif(in_array($type,[FORM_TYPE_DATE_RANGE,FORM_TYPE_DATETIME_RANGE])){
					if($type == FORM_TYPE_DATE_RANGE){
						$value = is_array($value) ? $value : explode(',',$value);
						$value[0] .= ' 00:00:00';
						$value[1] .= ' 23:59:59';
					}
					$value = EleUtility::toTimestamp($value);
					$data[] = [$elem_name,'between',$value];
				}else{
					$search_mode = $elem_item['search_mode'] ? : '=';

					if($elem_item['search_value']){
						$search_value = $elem_item['search_value'];
						$search_value = "return \"" . $search_value . "\";";
						$value = $search_value ? eval($search_value) : '';
					}

					$data[] = [$elem_name,$search_mode,$value];
				}

			}else{
				if(!EleUtility::isValidValue($value)) continue;

				$data[] = is_array($value) ? $value : [$elem_name,'=',$value];
			}
		}
		// dump($data);
		return $data;
	}
}

