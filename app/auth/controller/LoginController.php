<?php

declare (strict_types = 1);

namespace app\auth\Controller;

use app\BaseController;
use app\auth\logic\LoginLogic;

class LoginController extends BaseController
{

	public function loginIn()
	{
		$param = input();
		$data = (new LoginLogic())->loginIn($param);
		return $this->success($data);
	}

	public function getUserInfo()
	{
		$data = (new LoginLogic())->getUserInfo();
		return $this->success($data);
	}

	public function loginOut()
	{
		(new LoginLogic())->loginOut();
		return $this->success('登出成功');
	}
}
