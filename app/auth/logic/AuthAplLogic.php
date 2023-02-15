<?php
declare (strict_types = 1);

namespace app\auth\logic;

use app\BaseLogic as Base;
use app\common\tools\ArrayTool;
use app\common\tools\DataFormat;
use app\common\tools\HttpTool;
use app\model\SysUserRoleRele;
use app\model\SysRole;
use app\model\SysRoleAuthRele;
use think\helper\Arr;
use app\model\SysAuth;

/**
 * 权限应用
 */
class AuthAplLogic extends Base
{
	protected $valid_auth = [];

	/** 生成单个按钮表单数据
	 * @param array 
	 * @return array
     * @author xk
     */
	public function getUserAuth($user_data){
		$auth_list = $this->getValidAuth($user_data);
		list($menu_list,$button_list) = $this->splitMenuAndButton($auth_list);
		$menu = $this->makeMenu($menu_list);
		$button = $this->makeButton($button_list);
		// dump($menu,$button);
		return compact('menu','button');
	}


	/** 获取有效权限数据
     * @param array 
	 * @return array
     * @author xk
     */
	public function getValidAuth($user_data){
		$roles = Arr::get($user_data,'roles',[]);
		$is_super_mg = Arr::get($user_data,'user_is_super_mg',0);

		
		if($is_super_mg){
			$data =  SysAuth::getAll();
		}else{
			if(!$roles) return [];

			$data = SysRoleAuthRele::alias('r')
			->where('roau_role_id','in',$roles)
			->join('sys_auth a','a.id = r.roau_auth_id')
			->where('a.delete_time',0)
			->select()->toArray();
		}
		
		
		return $data;
	}

	/** 拆分菜单和按钮
     * @param array
	 * @return array
     * @author xk
     */
	public function splitMenuAndButton($auth_list){
		$menu = [];
		$button = [];
		foreach ($auth_list as $k => $v) {
			if($v['auth_type'] == AUTH_TYPE_BUTTON){
				$button[] = $v;
			}else{
				$menu[] = $v;
			}
		}

		return [$menu,$button];
	}


	/** 生成菜单
     * @param array
	 * @return array
     * @author xk
     */
	public function makeMenu($menu_list){
		$menu_data = [];
		foreach ($menu_list as $k => $v) {
			$item = [
				'id'=>$v['id'],
				'pid'=>$v['auth_pid'],
				'name'=>$v['auth_route_name'],
				'path'=>$v['auth_route_path'],
				'component'=>$v['auth_route_component'],
				'hidden'=>$v['auth_route_hidden'] ? true : false,
				'alwaysShow'=>$v['auth_type'] == AUTH_TYPE_MENU ? true : false,
				'meta'=>[
					'auth_id'=>$v['id'],
					'title'=>$v['auth_route_title'],
					'icon'=>$v['auth_route_icon'],
					'noCache'=>$v['auth_route_nocache'] ? true : false,
				]
			];
			$attrs = DataFormat::getJsonValue($v,'auth_route_attrs');
			$item = ArrayTool::deepMerge($item,$attrs);
			$menu_data[] = $item;
		}
		$data = DataFormat::getTree($menu_data);
		$data = ArrayTool::filterKey($data,'id,pid');

		return $data;
	}


	/** 生成按钮
     * @param array
	 * @return array
     * @author xk
     */
	public function makeButton($button_list){
		$data = array_column($button_list,'auth_route_name');
		// foreach ($button_list as $k => $v) {
		// 	$key = $v['auth_pid'];
		// 	if(isset($data[$key])){
		// 		$data[$key][] = $v['id'];
		// 	}else{
		// 		$data[$key] = [$v['id']];
		// 	}
		// }
		return $data;
	}

}



