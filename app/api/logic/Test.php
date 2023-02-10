<?php

declare (strict_types = 1);

namespace app\api\logic;

use app\BaseLogic as Base;
use app\common\tools\HttpTool;
use think\facade\Cache;
use think\facade\Db;
use app\api\logic\SearchCode;
use app\code_name\logic\parseCode;
use app\common\tools\DataFormat;
use app\common\tools\SnowFlake;
use app\sql_tool\logic\sqlParser;

class Test extends Base
{
	public function test()
	{
		$a = [
			['id'=>8617396742779830562,'name'=>'a','k'=>0.11],
			['id'=>13945967577402461,'name'=>'c','cccc'=>1312321],
		];
		$a  =array_column($a,null,'id');

		$b = DataFormat::bigIntToStr($a);
		dump($b);
		// 7024824711360022993
		// 7029854217791604981
		
		// 8617396742779830562
		// $a = ['a'=>8617396742779830562];
		// $b = json_encode($a);
		// $c = json_decode($b,true);
		// dump(gettype(9617396742779830562),gettype(17978812896666957068),is_long(9617396742779830562),gettype($b),gettype($c),$b,$c);

		// $a = [
		// 	'a'=>['a1'=>1,'a2'=>2],
		// 	'aa'=>['aa1'=>11,'aa2'=>22],
		// ];
		// $b = [
		// 	'a'=>['a1'=>111,'b2'=>222],
		// 	'aa'=>['aa1'=>1111,'bb2'=>2222],
		// ];

		// dump(array_merge($a,$b),array_merge_recursive($a,$b),merge($a,$b));

		// $this->testaa(null);
		// dump(make_id());
		// $L = new parseCode();
		// $data = $L->parseV2('员工入职');
		// halt($data);

		// $str = 'Customer information';

		// // preg_match_all('/o/',$str,$data);
		// // halt($data);

		// $parseCode = new parseCode();
		// $data = $parseCode->parse($str);
		// halt($data);
		// phpinfo();


		// $L = new sqlParser();
		// halt($L->parseV2());
		return true;
	}

	public function testaa($a=1,$b=2){
		dump($a,$b);
	}

	public function test1()
	{
		//Db
		// 	$data = Db::table('test')->where([['id','>',0],['name','>',0]])->field('id,name')->buildSql();
		$data = Db::table('test')->fetchSql()->insert([
			'name'=>'c'
		]);
		//redis
		// Cache::store('redis')->set('name','value',3600);
		// $data = Cache::store('redis')->get('name');
		
		halt(22,$data);
		return true;
	}

	public function makeId($num)
	{
		$num = $num > 0 ? $num : 1;
		for ($i=0; $i < $num; $i++) { 
			echo make_id() . '<br />';
		}
		die();
	}

}

