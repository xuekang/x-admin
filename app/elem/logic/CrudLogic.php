<?php
declare (strict_types = 1);

namespace app\elem\logic;

use app\BaseLogic;
use think\helper\Arr;
use think\helper\Str;
use app\BaseModel;

class CrudLogic extends BaseLogic
{
	/**
     * 基础对象
     * @var BaseModel
     */
	public static $Model = null;

	/**
     * 架构函数
     * @return void
     */
    public function __construct($table_name)
    {
        parent::__construct();
		$model_name = '\\app\\model\\' . Str::studly($table_name);
		self::$Model = new $model_name();
    }

	public function list($param)
	{
		$data = self::$Model::listO($param);
		return $data;
	}

	public function add($param)
	{
		self::$Model::myCreate($param);
	}

	public function edit($param)
	{
		self::$Model::mySave($param);
	}

	public function del($param)
	{
		self::$Model::mySoftDel($param[ID]);
	}

	public function get($param)
	{
		$data = self::$Model::findOrEmpty($param[ID])->toArray();
		return $data;
	}
}

