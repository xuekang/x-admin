<?php
declare (strict_types = 1);

namespace app\elem\logic;

use app\BaseLogic as Base;
use app\model\SysElemTable;
use app\model\SysElemTableEle;
use app\model\SysAuth;
use app\model\SysElemBtn;

use app\elem\logic\FormGeneratorLogic;

class TableGeneratorLogic extends Base
{
	/** 获取表格配置
     * @return boolean
     * @author xk
     */
	public function getTableConf($param)
	{
		$auth_id = $param['auth_id'];
		$conf = SysElemTable::getOne([['etbl_rele_auth','=',$auth_id]]);
		my_throw_if(!$conf,'未找到相关配置');

		$ele_list  = SysElemTableEle::alias('te')->where('etbe_rele_etbl',$conf['id'])
		->join('sys_element se','se.elem_name = te.etbe_elem_name')
		->order('te.order_value asc,te.id asc')->select()->toArray();
		
		// dump($ele_list);
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

				$columns[] = $this->makeColumn($elem_item);
			}

			if($elem_item['etbe_is_search']){
				if($elem_item['etbe_search_form_type']){
					$elem_item['elem_form_type'] = $elem_item['etbe_search_form_type'];
				}
				$searchForm[] = $this->makeSearchFormField($elem_item);
			}
		}

		$this->makeTableButtonConf($auth_id);

		$data = [
			'columns'=>$columns,
			'searchForm'=>$searchForm,
			'tableName'=>$conf['etbl_name'],
			'tableDesc'=>$conf['etbl_desc'],
			'url'=>$conf['etbl_get_url'],
		];

		if($conf['etbl_param']) $data['extraParams'] = json_decode($conf['etbl_param'],true);
		if($conf['etbl_extra_prop']) $data['tableAttrs'] = json_decode($conf['etbl_extra_prop'],true);

		return $data;

	}

	/** 生成栏目
     * @param array $elem_item
	 * @return array
     * @author xk
     */
	public function makeColumn($elem_item){
		$data = [];

		$data['prop'] = $elem_item['etbe_elem_name'];
		$data['label'] = $elem_item['elem_cname'];

		return $data;
	}

	/** 生成查询条件
     * @param array $elem_item
	 * @return array
     * @author xk
     */
	public function makeSearchFormField($elem_item){
		$data = [];
		$FormGeneratorLogic = new FormGeneratorLogic();
		$data = $FormGeneratorLogic->makeField($elem_item);
		$data['__config__']['span'] = 6;

		return $data;
	}


	/** 生成查询条件
     * @param array $elem_item
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

		foreach ($btn_list as $btn_item) {
			# code...
		}
		dump($btn_auth);

	}

	/** 生成查询条件
     * @param array $elem_item
	 * @return array
     * @author xk
     */
	public function makeTableButtonItem($btn_item){
		$data = [
			'type'=>$btn_item['ebtn_style_type'],
			'text'=>$btn_item['ebtn_name'],
			'actionType'=>$btn_item['ebtn_action_type'],
		];

		return $data;
	}
}

