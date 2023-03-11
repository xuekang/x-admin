<?php

declare (strict_types = 1);

namespace app\api\logic;

use app\BaseLogic;
use app\common\tools\HttpTool;
use think\facade\Cache;
use think\facade\Db;
use app\api\logic\SearchCode;
use app\code_name\logic\parseCode;
use app\common\tools\ServerTool;
use app\common\tools\ArrayTool;
use app\common\tools\DataFormat;
use app\common\tools\SnowFlake;
use app\common\tools\StringTool;
use app\sql_tool\logic\sqlParser;
use app\model\SysElemBtn;
use app\Request;
use app\sys_data\logic\SysData;
use ReflectionObject;

class Test1 extends BaseLogic
{
	public static $request = null;
	public function __construct(Request $request)
    {
		parent::__construct();
        self::$request = $request;
    }

	public function test(){
		dump('test',self::$request);
	}

	public static function test2(){
		dump('test2',self::$request);
	}
	

}

