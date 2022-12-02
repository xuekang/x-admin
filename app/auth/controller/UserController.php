<?php

declare (strict_types = 1);

namespace app\auth\Controller;

use app\BaseController;
use app\auth\logic\UserLogic;

class UserController extends BaseController
{

	public function add()
	{
		$param = input();
		(new UserLogic())->add($param);
		return $this->success();
	}

	public function edit()
	{
		$param = input();
		(new UserLogic())->edit($param);
		return $this->success();
	}
}
