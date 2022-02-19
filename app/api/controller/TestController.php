<?php

declare (strict_types = 1);

namespace app\api\Controller;

use app\BaseController as Base;
use app\api\logic\Test;
use app\api\logic\BaiduTranslate;

class TestController extends Base
{
	
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
		$L = new BaiduTranslate($data);
		$data = $L->translate($data);
		return $this->success($data);
	}
}
