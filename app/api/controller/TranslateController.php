<?php

declare (strict_types = 1);

namespace app\api\Controller;

use app\BaseController as Base;
use app\api\logic\BaiduTranslate;

class TranslateController extends Base
{

	public function translate()
	{
		$data = input();
		$L = new BaiduTranslate($data);
		$data = $L->translate($data);
		return $this->success('成功',$data);
	}
}
