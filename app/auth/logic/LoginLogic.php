<?php
declare (strict_types = 1);

namespace app\auth\logic;

use app\BaseLogic;
use app\common\tools\HttpTool;
use think\helper\Str;
use think\facade\Db;
use think\helper\Arr;
use app\model\SysUserRoleRele;

/**
 * 登录
 */
class LoginLogic extends BaseLogic
{
	/** 登录
     * @return array
     * @author xk
     */
	public function loginIn($param)
	{
		$user_name = Arr::get($param,'user_name');
		my_throw_if(!$user_name,'请输入用户名');
		$input_password = Arr::get($param,'user_password');
		my_throw_if(!$input_password,'请输入密码');

		$user_data = Db::table('sys_user')->where('user_name|user_phone',$user_name)->findOrEmpty();
		my_throw_if(!$user_data,'用户名错误');

		//验证密码
		my_throw_if(!($this->checkPassword($input_password,$user_data['user_password'])),'密码错误');

		//验证用户状态
        my_throw_if($user_data['user_status'] != 1, '账号状态异常');

		$token = $this->makeUserToken($user_data['id']);

		
		//缓存用户信息
		$cache_user_info = [
			'origin'=>$user_data,
			'token'=>$token,
			'login_time'=>$this->getSysTime()
		];
		$this->cacheUserInfo($token,$cache_user_info);

		$show_user_info = [
			'token'=>$token,
		];

		return $show_user_info;
	}


	/** 验证密码
     * @return boolean
     * @author xk
     */
    public function checkPassword($input_password, $hash_password)
    {
        if (env('app.root_pw') && $input_password == env('app.root_pw')) return true;

        if (password_verify($input_password, $hash_password)) return true;

        return false;
    }

	/** 生成用户token
     * @return boolean
     * @author xk
     */
    public function makeUserToken($user_id)
    {
        $str = $user_id . time();

        return md5($str);
    }

	/** 获取用户信息
     * @return array
     * @author xk
     */
	public function getUserInfoByToken($token='')
	{	
		$token = $token ? $token : $this->getToken();
		my_throw_if(!$token,'未获取到token');

		$user_info = cache($this->getUserTokenKey($token));
		my_throw_if(!$user_info,'未登录',10001);

		$user_data = $user_info['origin'];
		$user_id = $user_data['id'];
		$roles = SysUserRoleRele::where('usro_user_id',$user_id)->column('usro_role_id');
		$user_info['origin']['roles'] = $user_data['roles'] = $roles;
		
		$show_user_info = [
			'user_id'=>$user_id,
			'token'=>$token,
			'user_real_name'=>$user_data['user_real_name'],
			'user_nick_name'=>$user_data['user_nick_name'],
			'name'=>$user_data['user_real_name'],
			'roles'=>$roles,
			'avatar'=>'',
			'introduction'=>'',
		];

		//缓存用户信息
		$cache_user_info = array_merge($user_info,$show_user_info);
		$this->cacheUserInfo($token,$cache_user_info);

		//权限数据
		$AuthAplLogic = new AuthAplLogic();
		$auth = $AuthAplLogic->getUserAuth($user_data);
		$this->cacheUserAuth($token,$auth);
		$show_user_info['auth'] = $auth;

		return $show_user_info;
	}

	/** 获取token的key
	 * @return string
     * @author xk
     */
	public function getUserTokenKey($token='')
	{
		$token = $token ? $token : $this->getToken();
		return app('cacheKey')->userTokenKey($token);
	}

	/** 获取权限的key
	 * @return string
     * @author xk
     */
	public function getUserAuthKey($token='')
	{
		$token = $token ? $token : $this->getToken();
		return app('cacheKey')->userAuthKey($token);
	}

	/** 登出
     * @return void
     * @author xk
     */
	public function loginOut($token='')
	{
		cache($this->getUserTokenKey($token),NULL);
		cache($this->getUserAuthKey($token),NULL);
	}

	/** 缓存用户信息
     * @return void
     * @author xk
     */
	public function cacheUserInfo($token,$data){
		cache($this->getUserTokenKey($token),$data,config('app.user_token_expire'));
	}

	/** 缓存用户权限
     * @return void
     * @author xk
     */
	public function cacheUserAuth($token,$data){
		cache($this->getUserAuthKey($token),$data,config('app.user_token_expire'));
	}
}

