<?php

namespace app\common\lib;

/**
 * 日志记录
 */
class LogTool
{
    /**
     *取得log需要的参数
     */    
    public static function getUserLogParams($request,$user_id)
    {
        return [
            'url'  => $request->url,
            'ip'   => $request->ip(),
            'mg_id'=> $user_id,
            'params'=>$request->param(),
            'time' => date('Y-m-d H:i:s',time()),
        ];
    }

}