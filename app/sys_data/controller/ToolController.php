<?php

declare (strict_types = 1);

namespace app\sys_data\Controller;

use app\BaseController;

class ToolController extends BaseController
{
     /**
      * @Apidoc\Title("获取选项")
      * @Apidoc\Desc("")
      * @Apidoc\Url("sys_data/Tool/getSelect")
      * @Apidoc\Method("POST")
      * @Apidoc\Tag("通用")
      * @Apidoc\Param("sele_code", type="mix",require=true, desc="选项编码" )
      * @Apidoc\Returned("", type="array", desc="选项数据数组",
      *      @Apidoc\Returned ("label",type="string",desc="选项名称"),
      *      @Apidoc\Returned ("value",type="string",desc="选项值")
      * )
      */
     public function getSelect(){
          $data = app('Select')->getSelect(input('sele_code'));
          return $this->success($data);
     }
}
