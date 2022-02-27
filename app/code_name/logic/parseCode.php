<?php
declare (strict_types = 1);

namespace app\code_name\logic;

use app\BaseLogic as Base;
use app\common\tools\HttpTool;
use app\api\logic\BaiduTranslate;
use app\api\logic\SearchCode;
use think\helper\Str;

class parseCode extends Base
{
	
	public function parse($query,$lan=[])
	{
		$vals = [];
		$variables = [];

		if($this->_isZH($query)){
			$BaiduTranslate = new BaiduTranslate();
			$keyword = $BaiduTranslate->translate($query);
		}else{
			$keyword = $query;
		}
		
		//转小写
		$keyword = strtolower($keyword);

		// 过滤英文翻译中的名称修饰词
		preg_replace('/(a|an|the)\s{1,}/i',' ',$keyword);

		$keywords = explode(' ',$keyword);
		$keyword_patterns = [];
		foreach($keywords as $v){
			$keyword_patterns[] = $this->getKeyWordPattern($v);
		}
		

		$SearchCode = new SearchCode();
		$search_res = $SearchCode->search($keyword,0,100,$lan);
		foreach($search_res as $item){
			$search_code_str = $this->getSearchCodeStr($item['lines']);

			foreach($keyword_patterns as $keyword_pattern){
				preg_match_all($keyword_pattern,$search_code_str,$match_res);
				foreach($match_res[0] as $match_item){
					//remove "-" and "/" from the start and the end
					preg_replace('/^(\-|_|\\|\/)*/','',$match_item);
					preg_replace('/(\-|_|\\|\/)*$/','',$match_item);
					if(
						!$this->_isLink($match_item)
						&& strlen($match_item) < 64
						&& !in_array($match_item,$vals)
						&& !in_array(strtolower($match_item),$vals)
						&& !in_array(strtoupper($match_item),$vals)
					){
						$vals[] = $match_item;
						$variables[] = [
							'keyword'=> $match_item,
							'repoLink'=> $item['repo'],
							'repoLang'=> $item['language'],
							'color'=>$this->randomLabelColor()
						];
					}
				}
			}
			
		}

		return $variables;
	}

	private function _isZH($str)
	{
		return preg_match('/[\x4e00-\x9fa5]/',$str) ? true : false;
	}

	private function _isLink($str)
	{
		return preg_match('/\//',$str) ? true : false;
	}

	private function getKeyWordPattern($keyword)
	{
		return '/([\\-_\\w\\d\\/\\$]{0,}){0,1}' . $keyword . '([\\-_\\w\\d\\$]{0,}){0,1}/i';
	}

	private function getSearchCodeStr($lines)
	{
		$search_code_str = '';
		$code_str_item = is_array($lines) ? implode('',$lines) : $lines;
		preg_replace('/\r\n/i',' ',$code_str_item);
		$search_code_str .= $code_str_item;
		return $search_code_str;
	}

	private function randomLabelColor()
	{
		$colors = [
			'red',
			'orange',
			'yellow',
			'olive',
			'green',
			'teal',
			'blue',
			'violet',
			'purple',
			'pink',
			'brown',
		];
		$colors = ['','success','info','warning','danger'];
		$key = array_rand($colors);
		return $colors[$key];
	}

}

