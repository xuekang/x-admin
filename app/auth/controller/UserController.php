<?php

declare (strict_types = 1);

namespace app\auth\Controller;

use app\BaseController;
use app\auth\logic\UserLogic;

class UserController extends BaseController
{
	/**
     * @Apidoc\Title("获取列表")
     * @Apidoc\Desc("")
     * @Apidoc\Url("/auth/User/list")
     * @Apidoc\Method("POST")
     * @Apidoc\Tag("通用")
	 * @Apidoc\Param("current_page", type="int",default="1",desc="当前页"),
     * @Apidoc\Param("page_size", type="int",default="10",desc="页面大小"),
     * @Apidoc\Param("condition", type="array",default="[]",desc="查询条件"),
     * @Apidoc\Param("field", type="array",default="[]",desc="显示表头"),
     * @Apidoc\Param("order", type="array",default="[]",desc="排序"),
     * @Apidoc\Returned("total", type="int", desc="总数"),
     * @Apidoc\Returned("list", type="array", desc="订单列表"),
     * @Apidoc\Author("xk")
     */
	public function list()
	{
		$param = input();
		$data = (new UserLogic())->list($param);
		return $this->success($data);
	}

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
