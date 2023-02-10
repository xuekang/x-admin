<?php

return [
    //允许发送 Cookie
    'supportsCredentials' => 'true',
    //预检请求有效期
    'maxAge' => 1800,
    //支持的请求方法
    'allowedMethods' => ['GET, POST, PATCH, PUT, DELETE, OPTIONS'],
    //支持的头信息字段
    //token,client,Accept,api-access-key,api-timestamp,api-echostr,api-signature,Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With
    'allowedHeaders' => ['token,client,Authorization, Content-Type,appdebug'],
];