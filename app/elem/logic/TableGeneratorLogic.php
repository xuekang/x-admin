<?php
declare (strict_types = 1);

namespace app\elem\logic;

use app\BaseLogic;
use app\common\tools\ArrayTool;
use app\common\tools\DataFormat;
use app\common\tools\StringTool;
use app\model\SysElemTable;
use app\model\SysElemTableEle;
use app\model\SysAuth;
use app\model\SysElemBtn;
use app\model\SysElemBtnFormEle;
use app\elem\logic\FormGeneratorLogic;

class TableGeneratorLogic extends BaseLogic
{
	/** 获取表格配置
     * @return boolean
     * @author xk
     */
	public function getTableConf($auth_id)
	{
		$conf = SysElemTable::getOne([['etbl_rele_auth','=',$auth_id]]);
		my_throw_if(!$conf,'未找到相关配置');

		list($columns,$searchForm) = $this->makeColumnsAndSearchForm($conf['id']);

		list($headerButtons,$columnButtons) = $this->makeTableButtonConf($auth_id);

		$data = [
			'tableName'=>$conf['etbl_name'],
			'tableDesc'=>$conf['etbl_desc'],
			'crudTableName'=>$conf['etbl_crud_table_name'],
			'url'=>$conf['etbl_get_url'],
			'columns'=>$columns,
			'searchForm'=>$searchForm,
			'headerButtons'=>$headerButtons,
			'columnButtons'=>$columnButtons
		];

		if($conf['etbl_params']) $data['tableParams'] = json_decode($conf['etbl_params'],true);
		if($conf['etbl_attrs']) $data['tableAttrs'] = json_decode($conf['etbl_attrs'],true);

		return $data;

	}

	/** 生成表格栏目和查询
     * @param int $table_id
	 * @return array
     * @author xk
     */
	public function makeColumnsAndSearchForm($table_id){
		$ele_list  = SysElemTableEle::alias('te')
		->where('etbe_rele_etbl',$table_id)
		->join('sys_element se','se.elem_name = te.etbe_elem_name')
		->order('te.order_value asc,te.id asc')->select()->toArray();
		$columns = [];
		$searchForm = [];
		
		foreach ($ele_list as $elem_item) {
			if($elem_item['etbe_extend']){
				$extend = json_decode($elem_item['etbl_extra_prop'],true);
				$elem_item = array_merge($elem_item,$extend);
			}
			
			
			if($elem_item['etbe_is_column']){
				if($elem_item['etbe_column_form_type']){
					$elem_item['elem_form_type'] = $elem_item['etbe_column_form_type'];
				}

				$columns[] = $this->makeColumnItem($elem_item);
			}

			if($elem_item['etbe_is_search']){
				if($elem_item['etbe_search_form_type']){
					$elem_item['elem_form_type'] = $elem_item['etbe_search_form_type'];
				}
				$searchForm[] = $this->makeSearchFormItem($elem_item);
			}
		}

		return [$columns,$searchForm];
	}



	/** 生成栏目
     * @param array $elem_item
	 * @return array
     * @author xk
     */
	public function makeColumnItem($elem_item){
		$data = [];

		$data['prop'] = $elem_item['etbe_elem_name'];
		$data['label'] = $elem_item['elem_cname'];
		$data['type'] = $elem_item['elem_show_form_type'] ? : $elem_item['elem_form_type'];

		$data = ArrayTool::deepMerge($data,DataFormat::getJsonValue($elem_item,'elem_table_column_attrs'));

		return $data;
	}

	/** 生成查询条件
     * @param array $elem_item
	 * @return array
     * @author xk
     */
	public function makeSearchFormItem($elem_item){
		$data = [];
		$FormGeneratorLogic = new FormGeneratorLogic();
		$data = $FormGeneratorLogic->makeField($elem_item);
		$data['__config__']['span'] = 6;

		return $data;
	}


	/** 生成按钮
     * @param int $auth_id
	 * @return array
     * @author xk
     */
	public function makeTableButtonConf($auth_id){
		$data = [];
		$btn_auth  = SysAuth::getColumn('id',null,[
			['auth_pid','=',$auth_id],
			['auth_type','=',3]
		]);
		if(!$btn_auth) return $data;

		$btn_list = SysElemBtn::getAll([
			['ebtn_rele_auth','in',$btn_auth]
		]);
		$btn_list = SysElemBtn::alias('b')
		->where('ebtn_rele_auth','in',$btn_auth)
		->join('sys_auth a','a.id = b.ebtn_rele_auth')
		->where('a.delete_time',0)
		->field('a.auth_route_name,b.*')
		->select()->toArray();
		

		$btn_form_ele_list = SysElemBtnFormEle::alias('bfe')
		->where('ebfe_rele_ebtn','in',array_column($btn_list,'id'))
		->join('sys_element se','se.elem_name = bfe.ebfe_elem_name')
		->order('bfe.order_value asc,bfe.id asc')->select()->toArray();
		
		$headerButtons = [];
		$columnButtons = [];
		foreach ($btn_list as $btn_item) {
			$data_item = $this->makeTableButtonItem($btn_item,$btn_form_ele_list);
			if($btn_item['ebtn_table_type'] == 2){
				$headerButtons[] = $data_item;
			}else{
				$columnButtons[] = $data_item;
			}
		}

		return [$headerButtons,$columnButtons];
	}

	/** 生成单个按钮
     * @param array $btn_item
	 * @param array $btn_form_ele_list
	 * @return array
     * @author xk
     */
	public function makeTableButtonItem($btn_item,$btn_form_ele_list){
		$FormGeneratorLogic = new FormGeneratorLogic();

		$data = [
			'renderKey'=>StringTool::createGuid(),
			'auth_id'=>$btn_item['ebtn_rele_auth'],
			'auth_route_name'=>$btn_item['auth_route_name'],
			'type'=>$btn_item['ebtn_style_type'],
			'text'=>$btn_item['ebtn_name'],
			'actionType'=>$btn_item['ebtn_action_type'],
			'msgboxMessage'=>$btn_item['ebtn_msgbox_message'],
			'submitUrl'=>$btn_item['ebtn_submit_url'],
			'getUrl'=>$btn_item['ebtn_get_url'],
			'dialogFormFields'=>$this->makeTableButtonItemForm($btn_item,$btn_form_ele_list),
			'title'=>$btn_item['ebtn_title'],
			'newPagePath'=>$btn_item['ebtn_new_page_path'],
		];
		if($btn_item['ebtn_params']) $data['params'] = DataFormat::getJsonValue($btn_item,'ebtn_params');
		if($btn_item['ebtn_msgbox_attrs']) $data['msgboxAttrs'] = DataFormat::getJsonValue($btn_item,'ebtn_msgbox_attrs');
		if($btn_item['ebtn_dialog_attrs']) $data['dialogAttrs'] = DataFormat::getJsonValue($btn_item,'ebtn_dialog_attrs');

		$data['defaultFormConf'] = $FormGeneratorLogic->getDefaultFormConf();
		$data['defaultFormConf']['formBtns'] = false;
		if($btn_item['ebtn_form_conf']) {
			$data['defaultFormConf'] = ArrayTool::deepMerge($data['defaultFormConf'],DataFormat::getJsonValue($btn_item,'ebtn_form_conf'));
		}

		$ebtn_attrs = DataFormat::getJsonValue($btn_item,'ebtn_attrs');
		$data = ArrayTool::deepMerge($data,$ebtn_attrs);

		return $data;
	}



	/** 生成单个按钮表单数据
     * @param array $btn_item
	 * @param array $btn_form_ele_list
	 * @return array
     * @author xk
     */
	public function makeTableButtonItemForm($btn_item,$btn_form_ele_list){
		$data = [];
		if(!$btn_form_ele_list) return $data;

		$FormGeneratorLogic = new FormGeneratorLogic();
		foreach ($btn_form_ele_list as $elem_item) {
			if($btn_item['id'] != $elem_item['ebfe_rele_ebtn']) continue;
			$ebfe_extend = DataFormat::getJsonValue($elem_item,'ebfe_extend');
			$elem_item = ArrayTool::deepMerge($elem_item,$ebfe_extend);

			$data_item = $FormGeneratorLogic->makeField($elem_item);
			if($elem_item['ebfe_is_required']){
				$data_item['__config__']['required'] = true;
			}
			$data[] = $data_item;
		}
		return $data;
	}



}

