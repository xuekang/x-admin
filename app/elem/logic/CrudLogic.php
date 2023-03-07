<?php
declare (strict_types = 1);

namespace app\elem\logic;

use app\BaseLogic;
use think\helper\Arr;
use think\helper\Str;
use app\elem\logic\EleUtility;
use app\sys_data\logic\SysData;
use app\BaseModel;
use app\common\tools\DataFormat;


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
		$ListUtility = new ListUtility($data['list']);
		$data['list'] = $show_list = $ListUtility->format();
		return $data;
	}

	public function add($param)
	{
		$data = EleUtility::eleWriteBatch($param);
		// dump($param,$data);
		self::$Model::myCreate($data);
	}

	public function edit($param)
	{
		$data = EleUtility::eleWriteBatch($param);
		self::$Model::mySave($data);
	}

	public function del($param)
	{
		self::$Model::mySoftDel($param[ID]);
	}

	public function get($param)
	{
		$data = self::$Model::findOrEmpty($param[ID])->toArray();
		$data = EleUtility::eleReadBatch($data);
		return $data;
	}



}

