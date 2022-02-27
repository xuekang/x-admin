<?php
declare (strict_types = 1);

namespace app\api\logic;

use app\BaseLogic as Base;
use app\common\tools\HttpTool;

class SearchCode extends Base
{
	const URL = "https://searchcode.com/api/codesearch_I/";

	//热门编程语言编码
	const topProgramLanMap = [

	];
	// { id: '22,106', language: 'JavaScript, CoffeeScript' },
	// { id: '133,135', language: 'CSS' },
	// { id: '3,39', language: 'HTML' },
	// { id: 137, language: 'Swift' },
	// { id: 35, language: 'Objective-C' },
	// { id: 23, language: 'Java' },
	// { id: 19, language: 'Python' },
	// { id: 24, language: 'PHP' },
	// { id: 32, language: 'Ruby' },
	// { id: 28, language: 'C' },
	// { id: 16, language: 'C++' },
	// { id: 6, language: 'C#' },
	// { id: 55, language: 'Go' },
	// { id: 51, language: 'Perl' },
	// { id: '104,109', language: 'Clojure, ClojureScript' },
	// { id: 40, language: 'Haskell' },
	// { id: 54, language: 'Lua' },
	// { id: 20, language: 'Matlab' },
	// { id: 144, language: 'R' },
	// { id: 47, language: 'Scala' },
	// { id: '69,78,146', language: 'Shell' },
	// { id: 29, language: 'Lisp' },
	// { id: 42, language: 'ActionScript' }

	/**
     * 翻译
     * @param string $query 请求翻译query
     * @param array $from 翻译源语言
	 * @param string $to 翻译目标语言
     * @return array
     * @author xk
     */
	public function search($query,$page=0,$per_page=100,$lan=[])
	{
		$args = array(
			'q' => $query,
			'p' => $page,
			'per_page' => $per_page
		);
		$args = http_build_query($args);

		if($lan){
			$lan_arr = is_array($lan) ? $lan : explode(',',$lan);
			$lan_data = [];
			foreach($lan_arr as $lan_item){
				$lan_data[] = "lan={$lan_item}"; 
			}
			$lan = implode('&',$lan_data);
			$args .= "&{$lan}";
		}
		
		$ret = HttpTool::curlRequest(self::URL, $args,false);
		$data = $ret[2];

		// halt($data);
		$data = $data['results'] ?? [];
		return $data; 
	}
}

