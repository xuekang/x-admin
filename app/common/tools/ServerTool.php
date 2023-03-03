<?php

namespace app\common\tools;

/**
 * 服务器信息工具类
 */
class ServerTool
{
    /**
     * 获取服务器信息
     * @return string|array
     */
    public static function getServer($name = '') {
		if($name){
            if(isset($_SERVER[$name])) {
                return $_SERVER[$name];
            }elseif($name === 'REQUEST_SCHEME'){
                return request()->scheme();
            }elseif($name === 'SERVER_NAME'){
                return request()->host();
            }else{
                return request()->server($name);
            }
        }else{
            $data = array_merge($_SERVER,request()->server());

            if(!isset($data['REQUEST_SCHEME'])) $data['REQUEST_SCHEME'] = request()->scheme();

            if(!isset($data['SERVER_NAME'])) $data['SERVER_NAME'] = request()->host();

            return $data;
        }
	}

    /**
     * 获取ip地址
     * @return string
     */
    public static function getIp()
    {
        $request_ip = request()->ip();

        $realip = '';
        $unknown = 'unknown';
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($arr as $ip) {
                    $ip = trim($ip);
                    if ($ip != 'unknown') {
                        $realip = $ip;
                        break;
                    }
                }
            } else if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown)) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else if (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
                $realip = $_SERVER['REMOTE_ADDR'];
            } else {
                $realip = $unknown;
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)) {
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)) {
                $realip = getenv("REMOTE_ADDR");
            } else {
                $realip = $unknown;
            }
        }
        $realip = preg_match("/[\d\.]{7,15}/", $realip, $matches) ? $matches[0] : $unknown;

        // dump($request_ip,$realip);
        return $realip;
    }

    /**
     * 根据IP获取地址详情
     * @param string $ip
     * @return bool|mixed
     */
    public static function getIpInfo($ip='')
    {
        if (empty($ip)) {
            $ip = self::getIp();
        }
        $res = @file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip);
        if (empty($res)) {
            return false;
        }
        $json = json_decode($res, true);
        return $json;
    }

    /**
     * 获取域名信息
     * @param string $name
     * @return string
     */
    public static function getDomainName($name='main_domain_name')
    {
        $app_config = config('app');
        $domian_name = '';
        if($name && isset($app_config[$name])) $domian_name = $app_config[$name];

        if(!$domian_name) $domian_name = $app_config['main_domain_name'];

        if(!$domian_name) $domian_name = self::getServer('HTTP_HOST');

        $domian_name   = strtolower($domian_name); //首先转成小写
        $hosts = parse_url($domian_name);
        $host = $hosts['host'];
        if(isset($hosts['port'])){
            $host .= ':' . $hosts['port'];
        }
        $scheme = ValidateTool::isHttps() ? 'https://' : 'http://';
        return $scheme . $host . '/';
    }
}