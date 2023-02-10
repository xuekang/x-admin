<?php

declare (strict_types = 1);

namespace app\elem\Controller;

use app\BaseController;
use app\elem\logic\FormGeneratorLogic;
use app\elem\logic\TableGeneratorLogic;

class IndexController extends BaseController
{
     /**
     * @Apidoc\Title("获取表格配置")
     * @Apidoc\Desc("")
     * @Apidoc\Url("elem/Index/getTableConf")
     * @Apidoc\Method("POST")
     * @Apidoc\Tag("通用")
     * @Apidoc\Param("auth_id", type="int",require=true, desc="权限id" )
     * @Apidoc\Returned("", type="array", desc="选项数据数组",
     *      @Apidoc\Returned ("label",type="string",desc="选项名称"),
     *      @Apidoc\Returned ("value",type="string",desc="选项值")
     * )
     */
	public function getTableConf()
	{
		$param = input();
		$data = (new TableGeneratorLogic())->getTableConf($param['auth_id']);
		return $this->success($data);
	}

	/**
     * @Apidoc\Title("获取表单配置")
     * @Apidoc\Desc("")
     * @Apidoc\Url("elem/Index/getFormConf")
     * @Apidoc\Method("POST")
     * @Apidoc\Tag("通用")
     * @Apidoc\Param("sele_code", type="mix",require=true, desc="选项编码" )
     * @Apidoc\Returned("", type="array", desc="选项数据数组",
     *      @Apidoc\Returned ("fields",type="array",desc="表单字段数据"),
     * )
     */
	public function getFormConf()
	{
		$param = input();
		$data = (new FormGeneratorLogic())->getFormConf($param);
		return $this->success($data);
	}
}
