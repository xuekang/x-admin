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
use app\sys_data\logic\SysData;

class Test extends BaseLogic
{
	public function test(){
		// $a = SysElemBtn::alias('b')
		// ->join('sys_auth a','a.id = b.ebtn_rele_auth')
		// ->field('b.ebtn_rele_auth as auth_id,b.*')
		// ->select()->toArray();
		// $a = cache('auth1');
		// dump(is_null($a));

		// $a = ServerTool::getDomainName('file_domain_name');
		// 	$url = 'https://shimo.im:22/docs';
		// 	$url   = strtolower($url); //首先转成小写
		// $hosts = parse_url($url);
		// $host  = $hosts['host'];

		// $l = Db::table('sys_auth')->select()->toArray();
		// foreach ($l as $k => $v) {
		// 	$item = [
		// 		'id'=>$v['id'],
		// 		'pid'=>$v['auth_pid'],
		// 		'label'=>$v['auth_route_title'],
		// 		'value'=>$v['id'],
		// 		'path'=>$v['auth_path'],
		// 	];
		// 	$menu_data[] = $item;
		// }
		// $a = DataFormat::getTree($menu_data);
		// $b = DataFormat::getFromTree($a);
		// $c = ArrayTool::filterTreeValue($a,'用户');
		// $redis = app('Redis');
		// $code = 'boolean';
		// $data = app('Select')->getSelect($code)[$code];
		// $key = app('Redis')->selectKey($code);
		// $a = app('Redis')->set($key,json_encode($data,JSON_UNESCAPED_UNICODE));
		// $b = app('Redis')->get($key);
		// $c = app('Redis')->get('aaaa');

		// $a = pathinfo('uploads/thumbs/20023/1.jpg');
		// function ttt($str)
		// {
		// 	$str = strval($str);
		// 	preg_match('/^[+-]?(0|([1-9]\d*))(\.\d+)?$/',$str,$number);
		// 	return $number;
		// }
		$data = app('Select',['select_config_map'=>1],true);

		dump($data);
	}


	public function test2()
	{
		// $redis = new \Redis();
		// $conf = config('cache.stores.redis');
		// $redis->pconnect($conf['host'],intval($conf['port']));
		// $redis->auth($conf['password']);
		// $redis->select(intval($conf['select']));
		// $redis->hMset('token',['a'=>'aaa']);
		// dump($redis,$conf);
		$a = [[
			'id'=>1,
			'code'=>1,
			'name'=>"a1",
			'children'=>[
				'id'=>11,
				'code'=>11,
				'name'=>"a11",
				'children'=>[
					'id'=>111,
					'code'=>111,
					'name'=>"a111"
				]
			]
		]];
		// function removeKey(&$array,$keys,  $childKey = 'children'){
		// 	$keys = is_array($keys) ? $keys : explode(',',$keys);
		// 	// if(isset($array[$key])){
		// 	// 	unset($array[$key]);
		// 	// }
		// 	foreach ($array as $k => $v) {
		// 		if(in_array($k,$keys)){
		// 			unset($array[$k]);
		// 		}
		// 	}
		// 	dump($array,$keys);
		// 	if(isset($array[$childKey])){
		// 		removeKey( $array[$childKey], $keys,$childKey);
		// 	}
		
		// 	return $array;
		// }
		// $a = Cache::getDefaultDriver();
		function my1($e,$i){
			
		}
		$b0 = [1,2,3];
		$b4 = [1,2,'c'=>3];
		$b1 = ['a'=>1,'b'=>2,'c'=>[3]];
		$b2 = ['a'=>1,'b'=>2,'c'=>['c'=>3]];
		$b3 = [['a'=>1,'b'=>2,'c'=>3],['a'=>1,'b'=>2,'c'=>3]];

		$a1 = ArrayTool::filterKey($a,'id');
		dump(1111111111,$a1,$a);

		// $b = app('Redis')->userTokenKey('aaa');
		// $a = [
		// 	['id'=>8617396742779830562,'name'=>'a','k'=>0.11],
		// 	['id'=>13945967577402461,'name'=>'c','cccc'=>1312321],
		// ];
		// $a  =array_column($a,null,'id');

		// $b = DataFormat::bigIntToStr($a);
		
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

