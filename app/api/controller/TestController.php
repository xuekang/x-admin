<?php

declare (strict_types = 1);

namespace app\api\Controller;

use app\BaseController;
use app\api\logic\Test;
use app\api\logic\BaiduTranslate;

class TestController extends BaseController
{
	public function makeId()
	{
		$data = input('num');
		$L = new Test();
		$data = $L->makeId($data);
		return $this->success($data);
	}

	public function test()
	{
		$data = input();
		$L = new Test($data);
		$data = $L->test($data);
		return $this->success($data);
	}

	public function test1()
	{
		$data = input();
		$L = new Test($data);
		$data = $L->test1($data);
		return $this->success($data);
	}
}
