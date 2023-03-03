<?php
declare (strict_types = 1);

namespace app\sql_tool\logic;

use app\BaseLogic;
use app\common\tools\HttpTool;
use think\helper\Str;
use PHPSQLParser\PHPSQLParser;
use PHPSQLParser\PHPSQLCreator;
use marcocesarato\sqlparser\LightSQLParser;

class sqlParser extends BaseLogic
{
	
	public function parse()
	{
		$parser = new PHPSQLParser();
		// $sql_str = "SELECT `id`,`name` FROM `test` WHERE  `id` > 0  AND `name` > '0'";
		$sql_str = "INSERT INTO `test` SET `name` = 'c'";
		$data1 = $parser->parse($sql_str);

		$creator = new PHPSQLCreator();
		$data2 = $creator->create($data1);

		$LS = new LightSQLParser();
		$LS->setQuery($sql_str);
		$data3 = [$LS->getFields(),$LS->getTable(),$LS->getMethod()];

		halt($data1,$data2,$data3);
	}

}

