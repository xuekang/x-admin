<?php
declare (strict_types = 1);

namespace app\elem\logic;

use app\BaseLogic;
use think\helper\Arr;
use think\helper\Str;
use app\elem\logic\EleUtility;
use app\sys_data\logic\SysData;
use app\common\tools\DataFormat;
use app\common\tools\StringTool;
use app\common\tools\ArrayTool;

class ListUtility extends BaseLogic
{
	protected $origin_list = [];
    protected $list = [];
    protected $ele_map = [];
    protected $select_show_map = [];
    protected $file_show_map = [];

    /**
     * 架构函数
     * @return void
     */
    public function __construct($list,$ele_map=[])
    {
        parent::__construct();
		$this->list = $this->origin_list = $list;
        if($list){
            if(!$ele_map){
                $ele_names = array_keys(current($list));
                $ele_map = SysData::getEleData($ele_names);
            }
            $this->ele_map = $ele_map;
        }

    }

    /**
     * 通用列表格式化
     * @return array
     * @author xk
     */
    public function format()
    {
        $list = $this->list;
        $ele_map = $this->ele_map;

        //预处理显示用map数据
        $this->handleSelectShowMap();
        $this->handleFileShowMap();

        foreach($list as $k=>$data){
            $list[$k]['origin'] = $data;
            foreach ($data as $ele_name => $value) {
                if(!isset($ele_map[$ele_name])) continue;

                $elem_item = $ele_map[$ele_name];
                $show_value = $this->formatShowValue($value,$elem_item);
                $list[$k][$ele_name] = $show_value;
            }
        }

        return $list;
    }

    /**
     * 处理选项类map数据
     * @return void
     * @author xk
     */
    public function handleSelectShowMap()
    {
        $list = $this->list;
        $ele_map = $this->ele_map;

        $sele_code_map = [];
        $Select = app('Select');
        foreach($list as $k=>$data){
            foreach ($data as $ele_name => $value) {
                if(empty($ele_map[$ele_name])) continue;

                $elem_item = $ele_map[$ele_name];
                $ele_name = $elem_item['elem_name'];
                $type = $elem_item['elem_show_form_type'] ? : $elem_item['elem_form_type'];

                //选项类
                $sele_code = $elem_item['elem_show_sele_code'] ? : $elem_item['elem_sele_code'];
                if(!$sele_code) continue;

                if(isset($sele_code_map[$sele_code])){
                    $sele_code_map[$sele_code]['condition'][] = $value;
                }else{
                    $sele_code_map[$sele_code] = [
                        'code'=>$sele_code,
                        'type'=>'select',
                        'condition'=>[$value]
                    ];
                }
            }
        }

        $select_show_map = [];
        $sele_codes = array_keys($sele_code_map);
        if($sele_codes){
            $select_data_map = SysData::getSelectData($sele_codes);
            foreach ($select_data_map as $sele_code => $v) {
                $select_item = $sele_code_map[$sele_code];
                $query_field = $Select->getSelectQueryKey($v);
                $query_value = $this->getQueryValue($select_item['condition']);
                
                if($query_value){
                    $sele_code_map[$sele_code]['condition'] = [[$query_field,'in',$query_value]];
                }
            }
            $select_show_map = $Select->getSelectMap($sele_code_map);
        }
    
        $this->select_show_map = $select_show_map;

    }

    /**
     * 处理文件类map数据
     * @return void
     * @author xk
     */
    public function handleFileShowMap()
    {
        $list = $this->list;
        $ele_map = $this->ele_map;

        $file_map = [
            'code'=>'file',
            'type'=>'file',
            'param'=>[]
        ];
        $Select = app('Select');
        foreach($list as $k=>$data){
            foreach ($data as $ele_name => $value) {
                if(!isset($ele_map[$ele_name])) continue;

                $elem_item = $ele_map[$ele_name];
                $ele_name = $elem_item['elem_name'];
                $type = $elem_item['elem_show_form_type'] ? : $elem_item['elem_form_type'];

                //文件类
                if(EleUtility::isFile($type)){
                    $file_map['param'][] = $value;
                }
            }
        }

        $file_show_map = [];
        $query_value = $this->getQueryValue($file_map['param']);
        if($query_value){
            $file_map['param'] = $query_value;
            $file_show_map = current($Select->getSelectMap(['file'=>$file_map],'file'));
        }

        $this->file_show_map = $file_show_map;

    }

    /**
     * 获取查询值
     * @return array
     * @author xk
     */
    public function getQueryValue($value)
    {
        $value = implode(',',$value);
        $value = explode(',',$value);
        foreach ($value as $k => $v) {
            if(!EleUtility::isValidValue($v,true)){
                unset($value[$k]);
            }
        }
        $value = array_unique($value);
        return $value;
    }

    /**
     * 格式化显示值
     * @param mixed $value
     * @param array $elem_item 一组单个元素属性数据
     * @return mixed
     */
    protected function formatShowValue($value,$elem_item)
    {
        $ele_name = $elem_item['elem_name'];
        $type = $elem_item['elem_show_form_type'] ? : $elem_item['elem_form_type'];
        $sele_code = $elem_item['elem_show_sele_code'] ? : $elem_item['elem_sele_code'];
        $elem_is_valid_zero = $elem_item['elem_is_valid_zero'];

        if(!EleUtility::isValidValue($value,$elem_is_valid_zero)){
            return '';
        }
        $show_value = $value;
        
        //分类处理
        if(EleUtility::isDate($type)){
            //日期
            if(in_array($type,[FORM_TYPE_DATE,FORM_TYPE_DATETIME])){
                $show_value = EleUtility::toDateStr($value,'Y-m-d');
            }elseif(in_array($type,[FORM_TYPE_DATE_RANGE,FORM_TYPE_DATETIME_RANGE])){
                $show_value = EleUtility::toDateStr($value);
            }
        }elseif(EleUtility::isFile($type)) {
            $show_value = [];
            $value = is_array($value) ? $value : explode(',',strval($value));
            foreach ($value as $k => $v) {
                if(!empty($this->file_show_map[$v])){
                    $show_value[] = $this->file_show_map[$v];
                }
            }
        }elseif(EleUtility::isSelect($type)){
            // $show_value = [];
            // $value = is_array($value) ? $value : explode(',',strval($value));
            // foreach ($value as $k => $v) {
                
            //     dump($this->select_show_map[$sele_code],$v);
            //     if(isset($this->select_show_map[$sele_code][$v])){
            //         $show_value[] = $this->select_show_map[$sele_code][$v];
            //     }
            // }
            // $show_value = implode(',',$show_value);
            $show_value = DataFormat::translateCode($value,$this->select_show_map[$sele_code],$elem_is_valid_zero);
        }

        return $show_value;
    }
	

    /**
     * 判断某个值是否有效 (列表数据,全部为逗号)
     * @param mixed $value
     * @param array $elem_item
     * @return boolean
     */
    protected static function isValidListShowValue($value,$elem_item)
    {
        $elem_is_valid_zero = $elem_item['elem_is_valid_zero'];
        if(is_string($value) && Str::contains($value,',')){
            $flag = false;
            $value = explode(',',$value);
            foreach ($value as $k => $v) {
                $v = EleUtility::isValidValue($v,$elem_is_valid_zero) ? $v : '';
                if($v !== ''){
                    $flag = true;
                    break;
                }
            }
        }else{
            $flag = true;
        }

        return $flag;
    }

}

