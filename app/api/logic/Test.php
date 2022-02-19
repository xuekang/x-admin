<?php

declare (strict_types = 1);

namespace app\api\logic;

use app\BaseLogic as Base;
use app\common\tools\HttpTool;
use think\facade\Cache;
use think\facade\Db;
use app\api\logic\SearchCode;

class Test extends Base
{
	public function test()
	{
		$L = new SearchCode();
		$data = $L->search('cust_info');
		halt($data);
		return true;
	}


	public function test1()
	{
		//Db
		$data = Db::table('test')->select()->toArray();

		//redis
		Cache::store('redis')->set('name','value',3600);
		$data = Cache::store('redis')->get('name');
		
		halt($data);
		return true;
	}
}

