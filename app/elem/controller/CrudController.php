<?php

declare (strict_types = 1);

namespace app\elem\Controller;

use app\BaseController;
use app\elem\logic\CrudLogic;

class CrudController extends BaseController
{


	/**
	 * @Apidoc\Title("获取列表")
	 * @Apidoc\Desc("")
	 * @Apidoc\Url("/elem/Test/list")
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
        $crud_table_name = input(TABLE_FORM_CRUD_TABLE_NAME);
		$data = (new CrudLogic($crud_table_name))->list($param);
		return $this->success($data);
	}

	public function add()
	{
		$formData = input(TABLE_FORM_FORM_DATA);
		(new CrudLogic())->add($formData);
		return $this->success();
	}

	public function edit()
	{
		$formData = input(TABLE_FORM_FORM_DATA);
		(new CrudLogic())->edit($formData);
		return $this->success();
	}

	public function del()
	{
		$formData = input(TABLE_FORM_FORM_DATA);
		(new CrudLogic())->del($formData);
		return $this->success();
	}

	public function get()
	{
		$formData = input(TABLE_FORM_FORM_DATA);
		$data = (new CrudLogic())->get($formData);
		return $this->success($data);
	}
}
