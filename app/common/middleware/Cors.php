<?php
declare (strict_types = 1);

namespace app\common\middleware;

use think\Config;
use think\middleware\AllowCrossDomain;

class Cors extends AllowCrossDomain
{

    /**
     * HandleCors constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $methods = $config->get('cors.allowedMethods');
        $headers = $config->get('cors.allowedHeaders');
        $this->header = [
            'Access-Control-Allow-Credentials' => $config->get('cors.supportsCredentials'),
            'Access-Control-Max-Age' => $config->get('cors.maxAge'),
            'Access-Control-Allow-Methods' => is_array($methods) ? join(',',$methods) : $methods,
            'Access-Control-Allow-Headers' => is_array($headers) ? join(',',$headers) : $headers
        ];
        parent::__construct($config);
    }
}