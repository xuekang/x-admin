<?php
declare (strict_types = 1);

namespace app\common\cache;


/**
 * 缓存键规则
 */
trait RedisKey
{
    /** 
     * 用户token的key
	 * redis类型：hash
     * 例: pn_pc:token:asdfghjklasdfghj
     * @param string $token
     * @return string 
     * @author xk
     */
    public function userTokenKey($token)
    {
        return $this->getClient() . ':token:' . $token;
    }

    /** 
     * 用户权限的key
	 * redis类型：hash
     * 例: pn_pc:token:asdfghjklasdfghj:auth
     * @param string $token
     * @return string 
     * @author xk
     */
    public function userAuthKey($token)
    {
        return $this->userTokenKey($token) . ':auth';
    }

    /** 选项code的key
     * hash
     * 例: select:code:boolean
     * @param string $sele_code
     * @return string
     * @author xk
     */
    public function selectKey($sele_code)
    {
        return 'select:code:' . $sele_code;
    }
}