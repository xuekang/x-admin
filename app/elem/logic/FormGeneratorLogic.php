<?php
declare (strict_types = 1);

namespace app\elem\logic;

use app\BaseLogic as Base;
use app\common\lib\DataFormat;
use app\common\tools\ArrayTool;
use app\model\SysElement;
use think\helper\Arr;

class FormGeneratorLogic extends Base
{
	/** 获取表单配置
     * @return array
     * @author xk
     */
	public function getFormConf()
	{
		$elem_list = SysElement::getAll();

		$fields = array_map(function ($elem_item){
			return $this->makeField($elem_item);
		},$elem_list);

		$data = [
			'fields'=>$fields,
			'submitFormDataUrl'=>'',
			'getFormDataUrl'=>''
		];

		return $data;

	}

	/** 生成字段
     * @param array $elem_item
	 * @return array
     * @author xk
     */
	public function makeField($elem_item){
		$data = [];

		$data['__config__'] = $this->makeFieldConfig($elem_item);
		$data['__slot__'] = $this->handleFormSlot($elem_item);
		$data['__vModel__'] = $elem_item['elem_name'];
		$data['placeholder'] = $elem_item['elem_placeholder'];
		$data['style'] = ['width'=>'100%'];
		$data['clearable'] = true;

		$extra_prop = DataFormat::getJsonValue($elem_item,'elem_extra_prop');
		$data = ArrayTool::deepMerge($data,$extra_prop);

		$data = $this->handleJsonField([
			'__config__.regList'=>null
		],$data);

		return $data;
	}

	/** 处理表单扩展属性
     * @param array $elem_item
	 * @return array
     * @author xk
     */
	public function handleFormExtraProp($elem_item){
		$extra_prop = $elem_item['elem_extra_prop'];
		$extra_prop = $extra_prop ? (is_array($extra_prop) ? $extra_prop :  json_decode($elem_item['elem_extra_prop'],true)) : [];
		return $extra_prop;
	}

	/** 生成字段配置
     * @param array $elem_item
	 * @return array
     * @author xk
     */
	public function makeFieldConfig($elem_item){
		$data = [];
		
		$data['label'] = $elem_item['elem_cname'];
		$data['labelWidth'] = null;
		$data['showLabel'] = true;
		$data['changeTag'] = true;
		$data = array_merge($data,$this->formTypeTranslate($elem_item));
		$data['required'] = false;
		$data['layout'] = 'colFormItem';
		$data['span'] = 24;
		$data['regList'] = $elem_item['elem_form_validate'];;

		return $data;
	}

	/** 表单类型转换
     * @param array $elem_item
	 * @return array
     * @author xk
     */
	public function formTypeTranslate($elem_item){
		$data = [];
		if($elem_item['elem_form_type'] == 'aaa'){

		}else{
			$data['tag'] = 'el-input';
			$data['tagIcon'] = 'input';
		}

		return $data;
	}

	/** 处理表单插槽
     * @param array $elem_item
	 * @return array
     * @author xk
     */
	public function handleFormSlot($elem_item){
		$data = ['prepend'=>$elem_item['elem_slot_prepend'],'append'=>$elem_item['elem_slot_append']];
		return $data;
	}

	/** 处理json字段
     * @param array $fields
	 * @param array $data
	 * @return array
     * @author xk
     */
	public function handleJsonField($fields,$data){
		foreach ($fields as $field_name => $default_value) {
			$data[$field_name] = DataFormat::getJsonValue($data,$field_name,$default_value);
		}
		return $data;
	}


	

}

