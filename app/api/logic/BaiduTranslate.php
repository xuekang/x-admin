<?php
declare (strict_types = 1);

namespace app\api\logic;

use app\BaseLogic as Base;
use app\common\tools\HttpTool;

class BaiduTranslate extends Base
{
	const URL = "http://api.fanyi.baidu.com/api/trans/vip/translate";
	const APP_ID = "20220216001084920";
	const SEC_KEY = "vagtmBncD7hGQQemSxuz";


	/**
     * 翻译
     * @param string $query 请求翻译query
     * @param array $from 翻译源语言
	 * @param string $to 翻译目标语言
     * @return string 译文
     * @author xk
     */
	public function translate($query,$from='zh',$to='en')
	{
		$args = array(
			'q' => $query,
			'appid' => self::APP_ID,
			'salt' => rand(10000,99999),
			'from' => $from,
			'to' => $to,
		);
		$args['sign'] = $this->buildSign($query, self::APP_ID, $args['salt'], self::SEC_KEY);
		$ret = HttpTool::curlRequest(self::URL, $args);

		$data = $ret[2];
		$data = $data['trans_result'] ?? [];
		$data = current($data) ?? [];
		$data = $data['dst'] ?? '';

		return $data; 
	}


	//加密
	private function buildSign($query, $appID, $salt, $secKey)
	{
		$str = $appID . $query . $salt . $secKey;
		$ret = md5($str);
		return $ret;
	}
}

