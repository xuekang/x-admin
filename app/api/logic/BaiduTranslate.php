<?php
declare (strict_types = 1);

namespace app\api\logic;

use app\common\tools\HttpTool;

class BaiduTranslate
{
	const CURL_TIMEOUT = 10;
	const URL = "http://api.fanyi.baidu.com/api/trans/vip/translate";
	const APP_ID = "20220216001084920";
	const SEC_KEY = "vagtmBncD7hGQQemSxuz";


	//翻译入口
	public function translate($data)
	{
		$query = $data['query'] ?? '';
		$from = $data['from'] ?? 'zh';
		$to = $data['to'] ?? 'en';
		my_throw_if(!$query,'查询关键词(query)为空');

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
	public function buildSign($query, $appID, $salt, $secKey)
	{
		$str = $appID . $query . $salt . $secKey;
		$ret = md5($str);
		return $ret;
	}

	// //翻译入口
	// public function translateV1($data)
	// {
	// 	$query = $data['query'] ?? '';
	// 	$from = $data['from'] ?? 'zh';
	// 	$to = $data['to'] ?? 'en';
	// 	my_throw_if(!$query,'查询关键词(query)为空');

	// 	$args = array(
	// 		'q' => $query,
	// 		'appid' => self::APP_ID,
	// 		'salt' => rand(10000,99999),
	// 		'from' => $from,
	// 		'to' => $to,
	// 	);
	// 	$args['sign'] = $this->buildSign($query, self::APP_ID, $args['salt'], self::SEC_KEY);
	// 	$ret = $this->call(self::URL, $args);
	// 	// HttpTool::curlRequest(URL, $args);
	// 	return $ret; 
	// }

	// //加密
	// public function buildSign($query, $appID, $salt, $secKey)
	// {
	// 	$str = $appID . $query . $salt . $secKey;
	// 	$ret = md5($str);
	// 	return $ret;
	// }

	// //发起网络请求
	// public function call($url, $args=null, $method="post", $testflag = 0, $timeout = self::CURL_TIMEOUT, $headers=array())
	// {
	// 	$ret = false;
	// 	$i = 0; 
	// 	while($ret === false) 
	// 	{
	// 		if($i > 1)
	// 			break;
	// 		if($i > 0) 
	// 		{
	// 			sleep(1);
	// 		}
	// 		$ret = $this->callOnce($url, $args, $method, false, $timeout, $headers);
	// 		$i++;
	// 	}
	// 	return $ret;
	// }

	// public function callOnce($url, $args=null, $method="post", $withCookie = false, $timeout = self::CURL_TIMEOUT, $headers=array())
	// {
	// 	$ch = curl_init();
	// 	if($method == "post") 
	// 	{
	// 		$data = $this->convert($args);
	// 		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	// 		curl_setopt($ch, CURLOPT_POST, 1);
	// 	}
	// 	else 
	// 	{
	// 		$data = $this->convert($args);
	// 		if($data) 
	// 		{
	// 			if(stripos($url, "?") > 0) 
	// 			{
	// 				$url .= "&$data";
	// 			}
	// 			else 
	// 			{
	// 				$url .= "?$data";
	// 			}
	// 		}
	// 	}
	// 	curl_setopt($ch, CURLOPT_URL, $url);
	// 	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// 	if(!empty($headers)) 
	// 	{
	// 		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	// 	}
	// 	if($withCookie)
	// 	{
	// 		curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
	// 	}
	// 	halt($ch);
	// 	$r = curl_exec($ch);
	// 	curl_close($ch);
	// 	return $r;
	// }

	// //
	// public function convert(&$args)
	// {
	// 	$data = '';
	// 	if (is_array($args))
	// 	{
	// 		foreach ($args as $key=>$val)
	// 		{
	// 			if (is_array($val))
	// 			{
	// 				foreach ($val as $k=>$v)
	// 				{
	// 					$data .= $key.'['.$k.']='.rawurlencode($v).'&';
	// 				}
	// 			}
	// 			else
	// 			{
	// 				$data .="$key=".rawurlencode(strval($val))."&";
	// 			}
	// 		}
	// 		return trim($data, "&");
	// 	}
	// 	return $args;
	// }
}

