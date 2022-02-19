<?php
declare (strict_types = 1);

namespace app\common\tools;


/**
 * http请求相关
 */
class HttpTool
{

     /**
     * curl发送http请求
     * @param string $url 请求的url
     * @param  array|string $data 请求参数
     * @param bool $isPost 是否为post请求
     * @param array $opts curl配置参数
     * @return mixed
     */
    public static function curlRequest(string $url, $data = [], bool $isPost = true, array $opts = []): array
    {
        //初始化curl
        $curl = curl_init();

        //如果curl版本，大于7.28.1，得是2才行 。 而7.0版本的php自带的curl版本为7.40.1.  使用php7以上的，就能确保没问题
        $ssl     = (strpos($url, 'https') !== false) ? 2 : 0;
        $options = [
            //设置url
            CURLOPT_URL            => $url,

            //将头文件的信息作为数据流输出
            CURLOPT_HEADER         => false,

            //请求结果以字符串返回,不直接输出
            CURLOPT_RETURNTRANSFER => true,

            //禁止 cURL 验证对等证书
            CURLOPT_SSL_VERIFYPEER => false,
            //检查服务器SSL证书中是否存在一个公用名
            CURLOPT_SSL_VERIFYHOST => $ssl,

            //identity", "deflate", "gzip“，三种编码方式，如果设置为空字符串，则表示支持三种编码方式。当出现乱码时，可设置此字符串
            CURLOPT_ENCODING       => '',

            //设置http版本。HTTP1.1是主流的http版本
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,

            //连接对方主机时的最长等待时间。设置为10秒时，如果对方服务器10秒内没有响应，则主动断开链接。为0则，不限制服务器响应时间
            CURLOPT_CONNECTTIMEOUT => 0,

            //整个cURL函数执行过程的最长等待时间，也就是说，这个时间是包含连接等待时间的，超时时间 单位秒，0则永不超时
            CURLOPT_TIMEOUT        => 10,

            //设置头信息说明：应这样格式设置请求头才生效 ['Authorization:0f5fc4730e21048eae936e2eb99de548']
            CURLOPT_HTTPHEADER     => []
        ];
        
        $isJson = isset($opts['isJson']) ? $opts['isJson'] : false; //是否为json请求，默认为Content-Type:application/x-www-form-urlencoded
        $header = isset($options[CURLOPT_HTTPHEADER]) ? $options[CURLOPT_HTTPHEADER] : [];

        $options = $opts + $options;

        //post和get特殊处理
        if ($isPost) {
            // 设置POST请求
            $options[CURLOPT_POST] = true;

            if ($isJson && $data) {
                //json处理
                $data   = is_array($data) ? json_encode($data) : $data;
                $header = array_merge($header, ['Content-Type: application/json']);
                //设置头信息
                $options[CURLOPT_HTTPHEADER] = $header;

                //如果是json字符串的方式，不能用http_build_query函数
                $options[CURLOPT_POSTFIELDS] = $data;
            } else {
                //x-www-form-urlencoded处理
                //如果是数组的方式,要加http_build_query，不加的话，遇到二维数组会报错。
                $options[CURLOPT_POSTFIELDS] = is_array($data) ? http_build_query($data) : $data;
            }
        } else {
            // GET
            $options[CURLOPT_CUSTOMREQUEST] = 'GET';

            //没有？且data不为空,将参数拼接到url中
            if (strpos($url, '?') === false && !empty($data)) {
                if(is_array($data)){
                    $params_arr = [];
                    foreach ($data as $k => $v) {
                        array_push($params_arr, $k . '=' . $v);
                    }
                    $params_string = implode('&', $params_arr);
                }else{
                    $params_string = $data;
                }
                
                $options[CURLOPT_URL] = $url . '?' . $params_string;
            }
        }
        
        //数组方式设置curl，比多次使用curl_setopt函数设置在速度上要快
        curl_setopt_array($curl, $options);
        // dump($curl, $options);
        // 执行请求
        $response = curl_exec($curl);
        // halt($response);
        //返回的CONTENT_TYPE类型
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

        //返回的http状态码
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // $result = ['code' => $httpCode, 'header' => $contentType];
        $code = curl_errno($curl);//没有错误时curl_errno返回0
        $msg = $code ? curl_error($curl) : '成功';;
        $data = [];
        if ($response) {
            if(!is_null(json_decode($response, true))){
                $data = json_decode($response, true);
            }else{
                $data = $response;
            }
            
            // if (!$data) {
            //     //不是json,则认为是xml数据
            //     // libxml_disable_entity_loader(true);//验证xml
            //     $data            = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);//解析xml
            // }
        }

        //关闭请求
        curl_close($curl);

        return [$code,$msg,$data];
    }


    // /**
    //  * 通用curl请求
    //  * @param string $url 访问的URL
    //  * @param array $post post数据(不填则为GET)
    //  * @param string  $cookies 提交的$cookies
    //  * @param string  $return_cookie 是否返回$cookies
    //  * @param array  $http_header 请求头
    //  * @return array [错误码,错误信息,数据]
    //  */
    // public static function curlRequest($url, $post = [], $cookie = '', $return_cookie = 0, $http_header = [])
    // {
    //     $curl = curl_init();
    //     curl_setopt($curl, CURLOPT_URL, $url);
    //     curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    //     curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    //     curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    //     if ($post) {
    //         curl_setopt($curl, CURLOPT_POST, 1);
    //         curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    //     }
    //     if ($cookie) {
    //         curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    //     }
    //     if (!empty($http_header)) {
    //         curl_setopt($curl, CURLOPT_HTTPHEADER, $http_header);
    //     }
    //     curl_setopt($curl, CURLOPT_HEADER, $return_cookie);
    //     curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    //     $data = curl_exec($curl);
    //     $code = curl_errno($curl);
    //     $msg = $code ? curl_error($curl) : '成功';

    //     curl_close($curl);

    //     if ($return_cookie) {
    //         list($header, $body) = explode("\r\n\r\n", $data, 2);
    //         preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
    //         $data = [];
    //         $data['cookie'] = substr($matches[1][0], 1);
    //         $data['content'] = $body;
    //     }

    //     return [$code,$msg,$data];
    // }
}