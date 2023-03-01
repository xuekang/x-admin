<?php

declare (strict_types = 1);

namespace app\sys_data\Controller;

use app\BaseController;
use app\sys_data\logic\SysFileLogic;

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

     /**
      * @Apidoc\Title("文件上传")
      * @Apidoc\Desc("")
      * @Apidoc\Url("sys_data/Tool/upload")
      * @Apidoc\Method("POST")
      * @Apidoc\Tag("通用")
      * @Apidoc\Param("file", type="object",require=true, desc="文件体" )
      * @Apidoc\Param("file_type", type="string",require=true, desc="文件类型:img|video|file" )
      * @Apidoc\Param("max_size", type="string",require=false, desc="文件限制大小 )
      * @Apidoc\Returned ("id",type="int",desc="文件id"),
      * @Apidoc\Returned ("url",type="string",desc="文件地址"),
      * @Apidoc\Returned ("thumb",type="string",desc="图片缩略图"),
      * @Apidoc\Returned ("remark",type="string",desc="文件备注")
      */
      public function upload(){
          $file = request()->file(config('upload.field_name'));
          $params = input();
          $SysFileLogic = new SysFileLogic();
          $data = $SysFileLogic->upload($file,$params);
          return $this->success($data);
     }
}
