<?php
declare (strict_types = 1);

namespace app\auth\logic;

use app\BaseLogic;
use app\model\SysUser;
use think\helper\Arr;

class UserMngLogic extends BaseLogic
{
	public function handlePassword($param)
	{
		$param['user_password'] = password_hash($param['user_password'], PASSWORD_BCRYPT);
		return $param;
	}
}

