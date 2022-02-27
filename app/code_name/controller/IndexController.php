<?php

declare (strict_types = 1);

namespace app\code_name\Controller;

use app\BaseController as Base;
use app\api\logic\BaiduTranslate;
use app\api\logic\SearchCode;

class IndexController extends Base
{

	public function translate()
	{
		// $query = input('query');
		// my_throw_if(!$query,'查询关键词(query)为空');
		// $L = new BaiduTranslate();
		// $data = $L->translate($query);
		// return $this->success('成功',$data);
	}
}
