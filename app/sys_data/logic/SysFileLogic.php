<?php
declare (strict_types = 1);

namespace app\sys_data\logic;

use app\BaseLogic as Base;
use app\model\SysFile;
use app\common\tools\DataFormat;
use app\common\tools\ArrayTool;
use app\common\tools\ServerTool;
use app\common\tools\ValidateTool;
use think\helper\Arr;
use think\facade\Filesystem;
use think\File;
use think\file\UploadedFile;
use think\Image;

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
        $upload_dir = config('upload.upload_dir');
        $thumb_dir = config('upload.thumb_dir');
        $cnd_url = $domain_name . $upload_dir;

        $data = [];
        foreach ($file_id_arr as $k => $v) {
            if(!isset($list[$v])) continue;
            $item = $list[$v];
            $id = $item['id'];
            $file_url = $item['file_url'];
            $file_type = empty($item['file_type']) ? 'file' : $item['file_type'];

            $item['value'] = $id;
            $item['name'] =  $file_url;
            
            $item['id'] = $id;
            $item['origin'] =  $file_url;
            $item['thumb'] = '';
            $item['remark'] = $item['remark'] ?? '';

            if(ValidateTool::isUrl($file_url)){
                $item['url'] = $file_url;
            }else{
                $item['url'] = $cnd_url . $file_url;
            }

            if($file_type == 'img'){
                $item['thumb'] = $cnd_url . $thumb_dir . $file_url;
            }

            $item['label'] =  $item['url'];
            $data[] = $item;
        }
        
        return $data;
    }


    /** 文件上传，前端文件上传，服务器保存文件，并入库
     * @param UploadedFile  $file 文件体
     * @return array $data 文件信息
     */
    public function upload($file,$params){
        my_throw_if(empty($file),'未上传文件或超出服务器上传限制');
        $file_type = Arr::get($params,'file_type','file');
        $max_size = Arr::get($params,'max_size',config('upload.max_size'));
        $this->checkFileSize($file,$max_size);
        $this->checkFileMime($file);

        // $file_info = $file->getFileInfo();
        // dump($file_info,$info);

        //保存文件
        $path_name = $file_type .'/'. date('Ymd');
        $file_url = Filesystem::disk('uploads')->putFile($path_name , $file, 'md5');

        //生成缩略图
        if($file_type == 'img') $this->makeThumb($file,$file_url);
        

        $data['file_url'] = $file_url;
        $data['file_type'] = $file_type;
        $data['file_origin_name'] = $file->getOriginalName();
        $data['file_size'] = $file->getSize();
        $data['file_origin_extension'] = $file->getOriginalExtension();
        return $this->saveData($data);
    }

    /** 验证文件大小
     * @param UploadedFile  $file 文件数据
     * @param string  $max_size 文件限制大小
     * @return void
     */
    public function checkFileSize($file,$max_size){
        preg_match('/(\d+)(\w+)/', $max_size, $matches);
        $type = strtolower($matches[2]);
        $type_dict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
        $upload_max_size = (int)$max_size * pow(1024, isset($type_dict[$type]) ? $type_dict[$type] : 0);
        validate(['image'=>'fileSize:'.$upload_max_size])->check([$file]);
        $file_size = $file->getSize();
        my_throw_if(!($file_size <= (int) $upload_max_size),'文件上传大小超出限制(文件限制大小：{$max_size})');
    }

    /** 验证文件MIME类型
     * @param UploadedFile  $file 文件数据
     * @return void
     */
    public function checkFileMime($file){
        $mime = config('upload.mime_type');
        if($mime === '*') return true;

        if (is_string($mime)) {
            $mime = explode(',', $mime);
        }
        my_throw_if(!in_array(strtolower($file->getOriginalMime()), $mime),"上传文件类型错误(允许的文件类型：{$mime})");
    }
    
    /**
     * 生成缩略图
     * @param  string $file_url
     * @return void
     */
    public function makeThumb($file,$file_url)
    {
        $upload_dir = config('upload.upload_dir');
        $thumb_dir = config('upload.thumb_dir');
        $thumb_file_name = public_path($upload_dir . $thumb_dir) . $file_url;
        $thumb_file_path_info = pathinfo($thumb_file_name);
        $thumb_file_path = $thumb_file_path_info['dirname'];
        $this->checkPath($thumb_file_path);
        Image::open($file)->thumb(config('upload.thumb_width'),config('upload.thumb_height'))->save($thumb_file_name);
    }

    /**
     * 检查目录是否可写(没有则创建目录)
     * @access protected
     * @param  string $path 目录
     * @return boolean
     */
    public function checkPath($path)
    {
        if (is_dir($path)) {
            return true;
        }

        if (mkdir($path, 0755, true)) {
            return true;
        }
        my_throw("目录创建失败：($path)");
    }

    /** 保存文件数据入库
     * @param array  $data 文件数据
     * @return array $data 文件显示信息
     */
    public function saveData($data){
        $data['id'] = make_id();
        $data['file_upload_user_id'] = $this->getUserId();
        SysFile::addOne($data);
        return $this->getShowData([$data]);
    }
}

