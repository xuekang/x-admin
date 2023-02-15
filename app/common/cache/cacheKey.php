<?php
declare (strict_types = 1);

namespace app\common\cache;

use app\BaseLogic as Base;

/**
 * 缓存键规则
 */
class cacheKey extends Base
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

}