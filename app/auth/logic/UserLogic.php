<?php
declare (strict_types = 1);

namespace app\auth\logic;

use app\BaseLogic as Base;
use app\common\tools\HttpTool;
use app\model\SysUser;

class UserLogic extends Base
{

	public function add($data)
	{
		$data['user_password'] = password_hash($data['user_password'], PASSWORD_BCRYPT);

		SysUser::myCreate($data);
	}

	public function edit($data)
	{
		SysUser::mySave($data);
	}


}

