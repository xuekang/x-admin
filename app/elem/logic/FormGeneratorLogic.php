<?php
declare (strict_types = 1);

namespace app\elem\logic;

use app\BaseLogic;
use app\common\tools\DataFormat;
use app\common\tools\ArrayTool;
use app\common\tools\ServerTool;
use app\common\tools\StringTool;
use app\model\SysElement;
use think\helper\Arr;

class FormGeneratorLogic extends BaseLogic
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

	/** 获取默认表单配置
     * @return array
     * @author xk
     */
	public function getDefaultFormConf()
	{
		$data = [
			'fields'=> [],
			'formRef'=> 'elForm',
			'formModel'=> 'formData',
			'size'=> 'small',
			'labelPosition'=> 'right',
			'labelWidth'=> 100,
			'formRules'=> 'rules',
			'span'=> 6,
			'gutter'=> 15,
			'disabled'=> false,
			'loading'=> false,
			'formBtns'=> true
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
		$data['placeholder'] = $this->getPlaceholder($elem_item);

		$data = $this->handleFormType($data,$elem_item);
		
		$elem_attrs = DataFormat::getJsonValue($elem_item,'elem_attrs');
		$data = ArrayTool::deepMerge($data,$elem_attrs);
		$data = $this->handleJsonField([
			'__config__.regList'=>[]
		],$data);

		return $data;
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
		$data['formType'] = $elem_item['elem_form_type'];
		$data['required'] = false;
		$data['layout'] = 'colFormItem';
		$data['span'] = 24;
		$data['regList'] = $elem_item['elem_form_validate'];
		$data['formId'] = $this->getSysTime();
		$data['renderKey'] = StringTool::createGuid();

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
			Arr::set($data,$field_name,DataFormat::getJsonValue($data,$field_name,$default_value));
		}
		return $data;
	}

	/** 获取placeholder
     * @param array $elem_item
	 * @return string
     * @author xk
     */
	public function getPlaceholder($elem_item){
		$data = $elem_item['elem_placeholder'];
		if(!$data) {
			$text = $elem_item['elem_sele_code'] ? '请选择' : '请输入';
			$data = $text . $elem_item['elem_cname'];
		}
		return $data;
	}


	/** 处理表单类型转换
     * @param array $data
	 * @param array $elem_item
	 * @return array
     * @author xk
     */
	public function handleFormType($data,$elem_item){
		$type = $elem_item['elem_form_type'];
		$sele_code = $elem_item['elem_sele_code'];
		$options = [];
		if($sele_code){
			$options = app('Select')->getSelect($sele_code);
			$options = $options ? $options[$sele_code] : [];
		}
		
		$multiple = $elem_item['elem_is_multiple'] ? true : false;

		
		switch ($type) {
			case 'textarea'://多行文本
				$data['__config__']['tag'] = 'el-input';
				$data['__config__']['tagIcon'] = 'textarea';
				$data['autosize'] = ['minRows'=>4,'maxRows'=>4];
				break;

			case 'password'://密码
				$data['__config__']['tag'] = 'el-input';
				$data['__config__']['tagIcon'] = 'password';
				$data['show-password'] = true;
				break;
	
			case 'number'://计数器
				$data['__config__']['tag'] = 'el-input-number';
				$data['__config__']['tagIcon'] = 'number';
				break;
				
			case 'rich_text'://富文本编辑器
				$data['__config__']['tag'] = 'tinymce';
				$data['__config__']['tagIcon'] = 'rich-text';
				$data['height'] = 300;
				$data['branding'] = false;
				break;
			
				

			case 'select'://下拉选择,支持单+多
				$data['__config__']['tag'] = 'el-select';
				$data['__config__']['tagIcon'] = 'select';
				$data['__slot__']['options'] = $options;
				$data['filterable'] = true;
				$data['multiple'] = $multiple;
				break;

			case 'cascader'://级联选择,支持单+多
				$data['__config__']['tag'] = 'el-cascader';
				$data['__config__']['tagIcon'] = 'cascader';
				$data['__config__']['defaultValue'] = [];
				$data['__config__']['dataType'] = 'dynamic';
				$data['options'] = $options;
				$data['props'] = [
					'props'=>[
						'multiple'=>$multiple,
						'label'=>SELECT_LABEL,
						'value'=>SELECT_VALUE,
						'children'=>TREE_CHILDREN
					]
				];
				$data['filterable'] = true;

				break;

			case 'radio'://单选框
				$data['__config__']['tag'] = 'el-radio-group';
				$data['__config__']['tagIcon'] = 'radio';
				$data['__config__']['optionType'] = 'default';
				$data['__slot__']['options'] = $options;

				break;

			case 'checkbox'://多选框
				$data['__config__']['tag'] = 'el-checkbox-group';
				$data['__config__']['tagIcon'] = 'checkbox';
				$data['__config__']['defaultValue'] = [];
				$data['__config__']['optionType'] = 'default';
				$data['__slot__']['options'] = $options;
				break;
			
			case 'switch'://开关
				$data['__config__']['tag'] = 'el-switch';
				$data['__config__']['tagIcon'] = 'switch';
				$data['__config__']['defaultValue'] = 0;
				$data['active-color'] = null;
				$data['inactive-color'] = null;
				$data['active-value'] = 1;
				$data['inactive-value'] = 0;
				break;

			case 'slider'://滑块
				$data['__config__']['tag'] = 'el-slider';
				$data['__config__']['tagIcon'] = 'slider';
				$data['__config__']['defaultValue'] = 0;
				break;



			case 'date'://日期选择
				$data['__config__']['tag'] = 'el-date-picker';
				$data['__config__']['tagIcon'] = 'date';
				$data['__config__']['defaultValue'] = null;
				$data['type'] = 'date';
				$data['format'] = 'yyyy-MM-dd';
				$data['value-format'] = 'yyyy-MM-dd';
				break;

			case 'date_range'://日期范围选择
				$data['__config__']['tag'] = 'el-date-picker';
				$data['__config__']['tagIcon'] = 'date-range';
				$data['__config__']['defaultValue'] = null;
				$data['type'] = 'daterange';
				$data['format'] = 'yyyy-MM-dd';
				$data['value-format'] = 'yyyy-MM-dd';
				$data['range-separator'] = '至';
				$data['start-placeholder'] = '开始日期';
				$data['end-placeholder'] = '结束日';
				break;
			
			case 'datetime'://日期时间选择
				$data['__config__']['tag'] = 'el-date-picker';
				$data['__config__']['tagIcon'] = 'date';
				$data['__config__']['defaultValue'] = null;
				$data['type'] = 'datetime';
				$data['format'] = 'yyyy-MM-dd HH:mm:ss';
				$data['value-format'] = 'yyyy-MM-dd HH:mm:ss';
				break;

			case 'datetime_range'://日期时间范围选择
				$data['__config__']['tag'] = 'el-date-picker';
				$data['__config__']['tagIcon'] = 'date-range';
				$data['__config__']['defaultValue'] = null;
				$data['type'] = 'datetimerange';
				$data['format'] = 'yyyy-MM-dd HH:mm:ss';
				$data['value-format'] = 'yyyy-MM-dd HH:mm:ss';
				$data['range-separator'] = '至';
				$data['start-placeholder'] = '开始日期';
				$data['end-placeholder'] = '结束日';
				break;

			case 'time'://时间选择
				$data['__config__']['tag'] = 'el-time-picker';
				$data['__config__']['tagIcon'] = 'time';
				$data['__config__']['defaultValue'] = null;
				$data['picker-options'] = ['selectableRange'=>'00:00:00-23:59:59'];
				$data['format'] = 'HH:mm:ss';
				$data['value-format'] = 'HH:mm:ss';
				break;

			case 'time_range'://时间范围选择
				$data['__config__']['tag'] = 'el-time-picker';
				$data['__config__']['tagIcon'] = 'time-range';
				$data['__config__']['defaultValue'] = null;
				$data['is-range'] = true;
				$data['format'] = 'HH:mm:ss';
				$data['value-format'] = 'HH:mm:ss';
				$data['range-separator'] = '至';
				$data['start-placeholder'] = '开始日期';
				$data['end-placeholder'] = '结束日';
				break;

			case 'img'://上传
				$data['__config__']['tag'] = 'el-upload';
				$data['__config__']['tagIcon'] = 'upload';
				$data['__config__']['defaultValue'] = null;
				$data['__config__']['showTip'] = false;
				$data['__config__']['buttonText'] = '点击上传';
				$data['__config__']['fileSize'] = intval(config('upload.max_size'));
				$data['__config__']['sizeUnit'] = 'MB';
				$data['__slot__']['list-type'] = true;
				$data['action'] = ServerTool::getDomainName('file_domain_name') . trim(config('upload.upload_url'),'/');
				$data['accept'] = 'image/*';
				$data['name'] = config('upload.field_name');
				$data['auto-upload'] = true;
				$data['list-type'] = 'picture-card';
				$data['multiple'] = $multiple;
				$data['data'] = ['file_type'=>'img'];
				break;

			case 'video'://上传
				$data['__config__']['tag'] = 'el-upload';
				$data['__config__']['tagIcon'] = 'upload';
				$data['__config__']['defaultValue'] = null;
				$data['__config__']['showTip'] = false;
				$data['__config__']['buttonText'] = '点击上传';
				$data['__config__']['fileSize'] = intval(config('upload.video_max_size'));
				$data['__config__']['sizeUnit'] = 'MB';
				$data['__slot__']['list-type'] = true;
				$data['action'] = ServerTool::getDomainName('file_domain_name') . trim(config('upload.upload_url'),'/');
				$data['accept'] = 'video/*,audio/*';
				$data['name'] = config('upload.field_name');
				$data['auto-upload'] = true;
				$data['list-type'] = 'text';
				$data['multiple'] = $multiple;
				$data['data'] = ['file_type'=>'video'];
				break;
			
			case 'file'://上传
				$data['__config__']['tag'] = 'el-upload';
				$data['__config__']['tagIcon'] = 'upload';
				$data['__config__']['defaultValue'] = null;
				$data['__config__']['showTip'] = false;
				$data['__config__']['buttonText'] = '点击上传';
				$data['__config__']['fileSize'] = intval(config('upload.file_max_size'));
				$data['__config__']['sizeUnit'] = 'MB';
				$data['__slot__']['list-type'] = true;
				$data['action'] = ServerTool::getDomainName('file_domain_name') . trim(config('upload.upload_url'),'/');
				$data['accept'] = '';
				$data['name'] = config('upload.field_name');
				$data['auto-upload'] = true;
				$data['list-type'] = 'text';
				$data['multiple'] = $multiple;
				$data['data'] = ['file_type'=>'file'];
				break;
			// case ''://
			// 	$data['__config__']['tag'] = '';
			// 	$data['__config__']['tagIcon'] = '';
			// 	break;

			// case '':
			// 	$data['__config__']['tag'] = '';
			// 	$data['__config__']['tagIcon'] = '';
			// 	break;

			// case ''://
			// 	$data['__config__']['tag'] = '';
			// 	$data['__config__']['tagIcon'] = '';
			// 	break;

			// case '':
			// 	$data['__config__']['tag'] = '';
			// 	$data['__config__']['tagIcon'] = '';
			// 	break;
			
			default://text
				$data['__config__']['tag'] = 'el-input';
				$data['__config__']['tagIcon'] = 'input';
				break;
		}

		return $data;
	}

	

}

