<?php
declare (strict_types = 1);

namespace app\auth\logic;

use app\BaseLogic as Base;
use app\common\tools\HttpTool;
use app\model\SysUser;
use think\helper\Arr;

class UserLogic extends Base
{
	public function list($param)
	{
		$data = SysUser::listO($param);
		return $data;
	}

	public function add($param)
	{
		my_throw_if(empty($param['user_name']),'用户名为必须项');
		my_throw_if(empty($param['user_password']),'用户密码为必须项');
		$param['user_password'] = password_hash($param['user_password'], PASSWORD_BCRYPT);

		SysUser::myCreate($param);
	}

	public function edit($param)
	{
		SysUser::mySave($param);
	}

	public function del($param)
	{
		SysUser::mySoftDel($param[ID]);
	}

	public function get($param)
	{
		$data = SysUser::findOrEmpty($param[ID])->toArray();
		return $data;
	}
}

