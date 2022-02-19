<?php

declare (strict_types = 1);

namespace app\api\Controller;

use app\BaseController as Base;
use app\api\logic\BaiduTranslate;
use app\api\logic\SearchCode;

class CodeNameController extends Base
{

	public function translate()
	{
		$query = input('query');
		my_throw_if(!$query,'查询关键词(query)为空');
		$L = new BaiduTranslate();
		$data = $L->translate($query);
		return $this->success('成功',$data);
	}

	public function searchCode()
	{
		$query = input('query');
		my_throw_if(!$query,'查询关键词(query)为空');
		$L = new SearchCode();
		$data = $L->search($query);
		return $this->success('成功',$data);
	}
}
