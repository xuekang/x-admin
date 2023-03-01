<?php
declare (strict_types = 1);

namespace app\sys_data\logic;

use app\BaseLogic as Base;
use app\model\SysFile;
use app\common\tools\DataFormat;
use app\common\tools\ArrayTool;
use app\common\tools\ServerTool;
use app\common\tools\ValidateTool;

class SysFileLogic extends Base
{
	/** 获取文件信息(用于前端显示)
     * @param string|array  $file 文件ids或者文件信息列表(二维数组)
     * @return array $data 文件信息
     */
    public function getShowData($file)
    {
        if(is_array($file) && !ArrayTool::isOneDimenArray($file)){
            $file_id_arr = array_column($file,'id');
            $list = $file;
        }else{
            $file_id_arr = is_array($file) ? $file : explode(',',strval($file));
			$list = SysFile::getAll([['id', 'in', $file_id_arr]]);
        }
        if(!$list) return [];
        $list = array_column($list,null,'id');

		$domain_name =ServerTool::getDomainName('file_domain_name');

        $data = [];
        foreach ($file_id_arr as $k => $v) {
            if(!isset($list[$v])) continue;
            $item = $list[$v];
            $id = $item['id'];
            $file_url = $item['file_url'];
            $type = empty($item['file_type']) ? 'file' : $item['file_type'];

            $item['value'] = $id;
            $item['name'] =  $file_url;
            
            $item['id'] = $id;
            $item['origin'] =  $file_url;
            $item['thumb'] = '';
            $item['remark'] = $item['remark'] ?? '';

            if(ValidateTool::isUrl($file_url)){
                $item['url'] = $file_url;
            }else{
                $item['url'] = $domain_name . $file_url;
            }
            $item['label'] =  $item['url'];
            $data[] = $item;
        }
        
        return $data;
    }

}

